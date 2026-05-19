<?php

declare(strict_types=1);

use App\Models\Graduation;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('redirects guests away from the index', function () {
    $this->get(route('graduations.index'))
        ->assertRedirect(route('login'));
});

it('lists graduations for authenticated users', function () {
    $user = User::factory()->create();
    Graduation::factory()->count(3)->create();

    $this->actingAs($user)
        ->get(route('graduations.index'))
        ->assertOk();
});

it('denies non-admin from creating a graduation', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('graduations.store'), [
            'title' => 'Test Convocation',
            'ceremony_date' => now()->addMonth()->format('Y-m-d'),
            'fee' => 250,
            'status' => 'open',
        ])
        ->assertForbidden();
});

it('admin can create a graduation', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post(route('graduations.store'), [
            'title' => 'Test Convocation',
            'ceremony_date' => now()->addMonth()->format('Y-m-d'),
            'fee' => 300.00,
            'status' => 'open',
        ])
        ->assertRedirect();

    expect(Graduation::where('title', 'Test Convocation')->exists())->toBeTrue();
});

it('resolves graduations by uuid in URLs', function () {
    Graduation::factory()->create();
    $grad = Graduation::first();

    expect(route('graduations.show', $grad))
        ->toContain($grad->uuid)
        ->not->toContain("/{$grad->id}");
});

it('admin can upload a receipt for any student', function () {
    Storage::fake('public');

    $admin = User::factory()->admin()->create();
    $grad = Graduation::factory()->create();
    $student = Student::factory()->for($grad)->create();

    $this->actingAs($admin)
        ->patch(route('graduations.students.update', [$grad, $student]), [
            'name' => $student->name,
            'ic' => $student->ic,
            'email' => $student->email,
            'matric_card' => $student->matric_card,
            'phone' => $student->phone,
            'payment_receipt' => UploadedFile::fake()->create('receipt.pdf', 100, 'application/pdf'),
        ])
        ->assertRedirect();

    expect($student->fresh()->payment_receipt)->not->toBeNull();
});

it('student can only update their own record', function () {
    $studentUser = User::factory()->create();
    $grad = Graduation::factory()->create();

    $mine = Student::factory()->for($grad)->create(['user_id' => $studentUser->id]);
    $other = Student::factory()->for($grad)->create();

    $this->actingAs($studentUser)
        ->get(route('graduations.students.edit', [$grad, $mine]))
        ->assertOk();

    $this->actingAs($studentUser)
        ->get(route('graduations.students.edit', [$grad, $other]))
        ->assertForbidden();
});

it('admin verifies a paid student', function () {
    $admin = User::factory()->admin()->create();
    $grad = Graduation::factory()->create();
    $student = Student::factory()->paidUnverified()->for($grad)->create();

    $this->actingAs($admin)
        ->patch(route('graduations.students.verify', [$grad, $student]))
        ->assertRedirect();

    expect($student->fresh()->verified_at)->not->toBeNull();
});

it('admin cannot verify a student with no receipt', function () {
    $admin = User::factory()->admin()->create();
    $grad = Graduation::factory()->create();
    $student = Student::factory()->for($grad)->create(); // No payment receipt attached

    $this->actingAs($admin)
        ->patch(route('graduations.students.verify', [$grad, $student]))
        ->assertForbidden();
});

it('admin can add a student to a graduation', function () {
    $admin = User::factory()->admin()->create();
    $grad = Graduation::factory()->create();

    $this->actingAs($admin)
        ->post(route('graduations.students.store', $grad), [
            'name' => 'Ali Bin Ahmad',
            'ic' => '900101011234',
            'email' => 'ali@example.test',
            'matric_card' => 'M123456',
            'phone' => '0123456789',
        ])
        ->assertRedirect();

    expect($grad->students()->where('ic', '900101011234')->exists())->toBeTrue();
});

it('non-admin cannot add a student', function () {
    $user = User::factory()->create();
    $grad = Graduation::factory()->create();

    $this->actingAs($user)
        ->post(route('graduations.students.store', $grad), [
            'name' => 'Foo', 'ic' => '900101011235', 'email' => 'foo@x.test',
            'matric_card' => 'M1', 'phone' => '012',
        ])
        ->assertForbidden();
});

it('admin can delete a student', function () {
    $admin = User::factory()->admin()->create();
    $grad = Graduation::factory()->create();
    $student = Student::factory()->for($grad)->create();

    $this->actingAs($admin)
        ->delete(route('graduations.students.destroy', [$grad, $student]))
        ->assertRedirect();

    expect(Student::find($student->id))->toBeNull();
});

it('non-admin cannot delete a student', function () {
    $user = User::factory()->create();
    $grad = Graduation::factory()->create();
    $student = Student::factory()->for($grad)->create();

    $this->actingAs($user)
        ->delete(route('graduations.students.destroy', [$grad, $student]))
        ->assertForbidden();
});

it('admin can bulk import students from CSV', function () {
    $admin = User::factory()->admin()->create();
    $grad = Graduation::factory()->create();

    $csv = "name,ic,email,matric_card,phone\n"
        . "Aiman,900101011111,aiman@x.test,M1,012\n"
        . "Siti,900101012222,siti@x.test,M2,013\n";

    $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('roster.csv', $csv);

    $this->actingAs($admin)
        ->post(route('graduations.students.import', $grad), ['csv' => $file])
        ->assertRedirect();

    expect($grad->students()->count())->toBe(2);
});

it('skips invalid rows and reports a count', function () {
    $admin = User::factory()->admin()->create();
    $grad = Graduation::factory()->create();

    // Seed a duplicate student profile to trigger unique constraint violation checks
    Student::factory()->for($grad)->create([
        'ic' => '900101019999',
        'email' => 'dup@x.test',
    ]);

    $csv = "name,ic,email,matric_card,phone\n"
        . "Good,900101011111,good@x.test,M1,012\n"
        . "BadIC,!!!,bad@x.test,M2,012\n" // Malformed IC input data
        . "Dup,900101019999,dup@x.test,M3,012\n"; // Clashing credential unique check

    $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('roster.csv', $csv);

    $this->actingAs($admin)
        ->post(route('graduations.students.import', $grad), ['csv' => $file])
        ->assertRedirect()
        ->assertSessionHas('status', 'Spreadsheet data processed: Imported 1 records. Skipped 2 invalid rows.');
});

it('non-admin cannot import', function () {
    $user = User::factory()->create();
    $grad = Graduation::factory()->create();

    $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('roster.csv', "name\nx\n");

    $this->actingAs($user)
        ->post(route('graduations.students.import', $grad), ['csv' => $file])
        ->assertForbidden();
});

it('filters students table by name', function () {
    $admin = User::factory()->admin()->create();
    $grad = Graduation::factory()->create();

    Student::factory()->for($grad)->create(['name' => 'Alice Tan']);
    Student::factory()->for($grad)->create(['name' => 'Bob Lim']);

    $this->actingAs($admin)
        ->get(route('graduations.show', ['graduation' => $grad, 'search' => 'Alice']))
        ->assertOk()
        ->assertSee('Alice Tan')
        ->assertDontSee('Bob Lim');
});

it('filters by matric or ic', function () {
    $admin = User::factory()->admin()->create();
    $grad = Graduation::factory()->create();

    Student::factory()->for($grad)->create(['matric_card' => 'M111111']);
    Student::factory()->for($grad)->create(['matric_card' => 'M222222']);

    $this->actingAs($admin)
        ->get(route('graduations.show', ['graduation' => $grad, 'search' => 'M11']))
        ->assertSee('M111111')
        ->assertDontSee('M222222');
});

it('paginates with more than 15 students', function () {
    $admin = User::factory()->admin()->create();
    $grad = Graduation::factory()->create();
    Student::factory()->count(20)->for($grad)->create();

    $this->actingAs($admin)
        ->get(route('graduations.show', $grad))
        ->assertOk()
        ->assertSee('page=2'); // Pagination control string target verified
});
it('filters by verified status', function () {
    $admin = User::factory()->admin()->create();
    $grad = Graduation::factory()->create();

    Student::factory()->for($grad)->verified()->create(['name' => 'Done Tan']);
    Student::factory()->for($grad)->paidUnverified()->create(['name' => 'Wait Lim']);
    Student::factory()->for($grad)->create(['name' => 'Empty Ng']);

    $this->actingAs($admin)
        ->get(route('graduations.show', ['graduation' => $grad, 'status' => 'verified']))
        ->assertSee('Done Tan')
        ->assertDontSee('Wait Lim')
        ->assertDontSee('Empty Ng');
});

it('filters by pending status', function () {
    $admin = User::factory()->admin()->create();
    $grad = Graduation::factory()->create();

    Student::factory()->for($grad)->verified()->create(['name' => 'Done']);
    Student::factory()->for($grad)->paidUnverified()->create(['name' => 'Wait']);
    Student::factory()->for($grad)->create(['name' => 'Empty']);

    $this->actingAs($admin)
        ->get(route('graduations.show', ['graduation' => $grad, 'status' => 'pending']))
        ->assertDontSee('Done')
        ->assertSee('Wait')
        ->assertDontSee('Empty');
});

it('filters by not_paid status', function () {
    $admin = User::factory()->admin()->create();
    $grad = Graduation::factory()->create();

    Student::factory()->for($grad)->verified()->create(['name' => 'Done']);
    Student::factory()->for($grad)->paidUnverified()->create(['name' => 'Wait']);
    Student::factory()->for($grad)->create(['name' => 'Empty']);

    $this->actingAs($admin)
        ->get(route('graduations.show', ['graduation' => $grad, 'status' => 'not_paid']))
        ->assertDontSee('Done')
        ->assertDontSee('Wait')
        ->assertSee('Empty');
});

it('sorts students by name ascending', function () {
    $admin = User::factory()->admin()->create();
    $grad = Graduation::factory()->create();

    Student::factory()->for($grad)->create(['name' => 'Zoe Wong']);
    Student::factory()->for($grad)->create(['name' => 'Alice Tan']);

    $resp = $this->actingAs($admin)
        ->get(route('graduations.show', ['graduation' => $grad, 'sort' => 'name', 'direction' => 'asc']))
        ->assertOk();

    expect(strpos($resp->getContent(), 'Alice Tan'))
        ->toBeLessThan(strpos($resp->getContent(), 'Zoe Wong'));
});

it('sorts by ic desc', function () {
    $admin = User::factory()->admin()->create();
    $grad = Graduation::factory()->create();

    Student::factory()->for($grad)->create(['ic' => '111111111111']);
    Student::factory()->for($grad)->create(['ic' => '999999999999']);

    $resp = $this->actingAs($admin)
        ->get(route('graduations.show', ['graduation' => $grad, 'sort' => 'ic', 'direction' => 'desc']));

    expect(strpos($resp->getContent(), '999999999999'))
        ->toBeLessThan(strpos($resp->getContent(), '111111111111'));
});

it('falls back to created_at for an unknown sort column', function () {
    $admin = User::factory()->admin()->create();
    $grad = Graduation::factory()->create();

    Student::factory()->count(3)->for($grad)->create();

    $this->actingAs($admin)
        ->get(route('graduations.show', ['graduation' => $grad, 'sort' => 'pwned;DROP--', 'direction' => 'asc']))
        ->assertOk();
});

it('admin can export students to CSV', function () {
    $admin = User::factory()->admin()->create();
    $grad = Graduation::factory()->create(['title' => 'June 2026 Convocation']);
    Student::factory()->count(2)->for($grad)->create();

    $resp = $this->actingAs($admin)
        ->get(route('graduations.students.export', $grad))
        ->assertOk();

    expect($resp->headers->get('Content-Type'))->toContain('text/csv');
    expect($resp->streamedContent())->toContain('name,ic,email,matric_card,phone,paid_at,verified_at');
});

it('export honours status filter', function () {
    $admin = User::factory()->admin()->create();
    $grad = Graduation::factory()->create();

    Student::factory()->for($grad)->verified()->create(['name' => 'Done Tan']);
    Student::factory()->for($grad)->create(['name' => 'Empty Ng']);

    $resp = $this->actingAs($admin)
        ->get(route('graduations.students.export', ['graduation' => $grad, 'status' => 'verified']))
        ->assertOk();

    $body = $resp->streamedContent();
    expect($body)->toContain('Done Tan')->not->toContain('Empty Ng');
});

it('non-admin cannot export', function () {
    $user = User::factory()->create();
    $grad = Graduation::factory()->create();

    $this->actingAs($user)
        ->get(route('graduations.students.export', $grad))
        ->assertForbidden();
});

it('admin can bulk verify students with receipts', function () {
    $admin = User::factory()->admin()->create();
    $grad = Graduation::factory()->create();

    $a = Student::factory()->for($grad)->paidUnverified()->create();
    $b = Student::factory()->for($grad)->paidUnverified()->create();

    $this->actingAs($admin)
        ->post(route('graduations.students.bulk', $grad), [
            'action' => 'verify',
            'ids' => [$a->uuid, $b->uuid],
        ])
        ->assertRedirect();

    expect($a->fresh()->verified_at)->not->toBeNull();
    expect($b->fresh()->verified_at)->not->toBeNull();
});

it('bulk verify skips students without a receipt', function () {
    $admin = User::factory()->admin()->create();
    $grad = Graduation::factory()->create();

    $paid = Student::factory()->for($grad)->paidUnverified()->create();
    $unpaid = Student::factory()->for($grad)->create(); // no receipt

    $this->actingAs($admin)
        ->post(route('graduations.students.bulk', $grad), [
            'action' => 'verify',
            'ids' => [$paid->uuid, $unpaid->uuid],
        ])
        ->assertRedirect()
        ->assertSessionHas('status', 'Batch update processed: Verified 1 records. Skipped 1 entries missing receipts.');

    expect($paid->fresh()->verified_at)->not->toBeNull();
    expect($unpaid->fresh()->verified_at)->toBeNull();
});

it('admin can bulk delete students', function () {
    $admin = User::factory()->admin()->create();
    $grad = Graduation::factory()->create();
    $students = Student::factory()->count(3)->for($grad)->create();

    $this->actingAs($admin)
        ->post(route('graduations.students.bulk', $grad), [
            'action' => 'delete',
            'ids' => $students->pluck('uuid')->all(),
        ])
        ->assertRedirect();

    expect($grad->students()->count())->toBe(0);
});

it('non-admin cannot bulk verify', function () {
    $user = User::factory()->create();
    $grad = Graduation::factory()->create();
    $student = Student::factory()->for($grad)->paidUnverified()->create();

    $this->actingAs($user)
        ->post(route('graduations.students.bulk', $grad), [
            'action' => 'verify',
            'ids' => [$student->uuid],
        ])
        ->assertForbidden();
});

it('bulk action does not leak across graduations', function () {
    $admin = User::factory()->admin()->create();
    $gradA = Graduation::factory()->create();
    $gradB = Graduation::factory()->create();

    $studentInB = Student::factory()->for($gradB)->create();

    // Try to delete a B-student via the A bulk endpoint
    $this->actingAs($admin)
        ->post(route('graduations.students.bulk', $gradA), [
            'action' => 'delete',
            'ids' => [$studentInB->uuid],
        ])
        ->assertRedirect();

    expect(Student::find($studentInB->id))->not->toBeNull(); // still there
});