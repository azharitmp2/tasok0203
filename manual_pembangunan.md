Berikut adalah teks Markdown yang telah dibersihkan dan disusun semula ke dalam format `.md` yang kemas untuk **Manual Lengkap Pembangunan: Graduation Registration System**. Semua pecahan baris kod yang terputus akibat pengekstrakan fail PDF telah diperbaiki sepenuhnya.

Anda boleh menyalin keseluruhan teks dalam blok di bawah untuk terus disimpan sebagai fail `.md` (contohnya `DEVELOPMENT_MANUAL.md`) di GitHub:

---

```markdown
# Manual Lengkap Pembangunan: Graduation Registration System
### Panduan End-to-End Pembangunan Aplikasi CRUD Laravel (konvojtm) Berasaskan UUID & Tailwind CSS

Manual komprehensif ini menyediakan panduan langkah demi langkah untuk membina lapisan data, pengurusan pentadbir (Admin CRUD), portal urus diri pelajar, aliran muat naik resit, sehingga ke fasa ujian automasi menggunakan persekitaran Laravel terbaru.

---

## Fasa 1: Penyediaan Data Layer (Model, Migration, Factory & Seeder)

### Langkah 1.1: Penjanaan Komponen Serentak
Gunakan arahan Artisan dengan bendera (flag) `-mfs` untuk menjana fail Model, Migration, Factory, dan Seeder secara serentak bagi kedua-dua entiti utama:

```bash
php artisan make:model Graduation -mfs
php artisan make:model Student -mfs

```

### Langkah 1.2: Penyediaan Fail Migration

> ⚠️ **Nota Penting:** Fail penciptaan jadual `graduations` mestilah mempunyai tanda masa (*timestamp*) yang lebih awal berbanding fail jadual `students` bagi mengelakkan ralat kekangan kekunci asing (*foreign key constraint error*).

#### Fail 1: Jadual Graduations (Acara)

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('graduations', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Menggunakan UUID sebagai Primary Key
            $table->string('title'); // Contoh: 'Convocation 2025'
            $table->date('ceremony_date');
            $table->decimal('fee', 8, 2); // Kos pendaftaran dalam nilai RM
            $table->string('status')->default('active'); // active, pending, closed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('graduations');
    }
};

```

#### Fail 2: Jadual Students (Pendaftar)

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Menggunakan UUID
            // Hubungan Foreign Key berjenis UUID ke jadual graduations
            $table->foreignUuid('graduation_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('ic')->unique();
            $table->string('email')->unique();
            $table->string('matric_card')->unique();
            $table->string('phone');
            $table->string('payment_receipt')->nullable(); // Lokasi fail resit
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};

```

### Langkah 1.3: Konfigurasi Model (Trait HasUuids & Hubungan)

Aktifkan ciri penjanaan UUID automatik Laravel dengan menggunakan trait `HasUuids` di dalam model berkaitan.

#### Model: `app/Models/Graduation.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Graduation extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['title', 'ceremony_date', 'fee', 'status'];

    // Hubungan: Graduation HasMany Students
    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }
}

```

#### Model: `app/Models/Student.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'graduation_id', 'name', 'ic', 'email',
        'matric_card', 'phone', 'payment_receipt',
        'verified_at', 'paid_at'
    ];

    // Hubungan: Student Belongs To Graduation
    public function graduation(): BelongsTo
    {
        return $this->belongsTo(Graduation::class);
    }
}

```

### Langkah 1.4: Fail Penyediaan Data Olok-olok (Factories & Seeders)

Untuk memastikan data demo mempunyai identiti tempatan Malaysia, kemas kini pembolehubah persekitaran pada fail `.env`:

```env
FAKER_LOCALE=ms_MY

```

#### Factory: `database/factories/GraduationFactory.php`

```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class GraduationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => 'Convocation Sesi ' . $this->faker->randomElement(['I', 'II', 'III']),
            'ceremony_date' => $this->faker->dateTimeBetween('+1 month', '+1 year')->format('Y-m-d'),
            'fee' => $this->faker->randomElement([250.00, 300.00, 350.00, 400.00]),
            'status' => 'active',
        ];
    }
}

```

#### Factory: `database/factories/StudentFactory.php`

```php
<?php

namespace Database\Factories;

use App\Models\Graduation;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    public function definition(): array
    {
        // Format Kad Pengenalan Malaysia yang sah: YYMMDD-BP-NNNN
        $year = $this->faker->dateTimeBetween('-25 years', '-20 years')->format('ymd');
        $stateCode = $this->faker->randomElement(['01', '02', '10', '14', '08', '05']);
        $randomDigits = $this->faker->numerify('####');
        $icNumber = "{$year}-{$stateCode}-{$randomDigits}";

        $phonePrefix = $this->faker->randomElement(['+6011', '+6012', '+6013', '+6017', '+6019']);
        $phoneNumber = $phonePrefix . $this->faker->numerify('#######');

        return [
            'graduation_id' => Graduation::factory(),
            'name' => strtoupper($this->faker->name()),
            'ic' => $icNumber,
            'email' => $this->faker->unique()->safeEmail(),
            'matric_card' => 'BI' . $this->faker->unique()->numerify('#####'),
            'phone' => $phoneNumber,
            'payment_receipt' => 'receipts/rcpt_' . $this->faker->uuid() . '.pdf',
            'verified_at' => now(),
            'paid_at' => now()->subDays(2),
        ];
    }
}

```

#### Seeder Utama: `database/seeders/DatabaseSeeder.php`

```php
<?php

namespace Database\Seeders;

use App\Models\Graduation;
use App\Models\Student;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $convocation2025 = Graduation::create([
            'title' => 'Convocation 2025 (Sesi 1)',
            'ceremony_date' => '2025-11-15',
            'fee' => 350.00,
            'status' => 'active'
        ]);

        $convocation2026 = Graduation::create([
            'title' => 'Convocation 2026 (Sesi Perancangan)',
            'ceremony_date' => '2026-11-20',
            'fee' => 400.00,
            'status' => 'pending'
        ]);

        // Memasukkan pendaftaran demo pukal berciri Malaysia
        Student::factory()->count(150)->create(['graduation_id' => $convocation2025->id]);
        Student::factory()->count(50)->create(['graduation_id' => $convocation2026->id]);
    }
}

```

---

## Fasa 2: Pengurusan Acara (Admin CRUD)

### Langkah 2.1: Konfigurasi Laluan Utama (`routes/web.php`)

Daftarkan laluan sumber (*resource route*) untuk operasi pentadbir serta portal pendaftaran pelajar:

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GraduationController;
use App\Http\Controllers\StudentPortalController;
use App\Http\Controllers\AdminStudentController;

// Pengurusan Acara Graduasi (Admin)
Route::resource('graduations', GraduationController::class);
Route::patch('graduations/{graduation}/close', [GraduationController::class, 'close'])->name('graduations.close');

// Portal Pengurusan Kendiri Pelajar
Route::get('/portal/{student}', [StudentPortalController::class, 'edit'])->name('portal.edit');
Route::put('/portal/{student}', [StudentPortalController::class, 'update'])->name('portal.update');

// Semakan Dokumen Kewangan oleh Admin
Route::get('/admin/verifications', [AdminStudentController::class, 'index'])->name('admin.verifications.index');
Route::patch('/admin/students/{student}/verify', [AdminStudentController::class, 'verify'])->name('admin.students.verify');

```

### Langkah 2.2: Logik Validasi & Kawalan Admin

#### Form Request: `app/Http/Requests/GraduationRequest.php`

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GraduationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'ceremony_date' => 'required|date|after_or_equal:today',
            'fee' => 'required|numeric|min:0',
            'status' => 'required|in:active,pending,closed',
        ];
    }
}

```

#### Controller: `app/Http/Controllers/GraduationController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Graduation;
use App\Http\Requests\GraduationRequest;

class GraduationController extends Controller
{
    public function index()
    {
        $graduations = Graduation::withCount('students')->latest()->get();
        return view('graduations.index', compact('graduations'));
    }

    public function create()
    {
        return view('graduations.create');
    }

    public function store(GraduationRequest $request)
    {
        Graduation::create($request->validated());
        return redirect()->route('graduations.index')->with('success', 'Acara berjaya dicipta!');
    }

    public function show(Graduation $graduation)
    {
        $graduation->load('students');
        return view('graduations.show', compact('graduation'));
    }

    public function edit(Graduation $graduation)
    {
        return view('graduations.edit', compact('graduation'));
    }

    public function update(GraduationRequest $request, Graduation $graduation)
    {
        $graduation->update($request->validated());
        return redirect()->route('graduations.index')->with('success', 'Acara dikemas kini!');
    }

    public function destroy(Graduation $graduation)
    {
        $graduation->delete();
        return redirect()->route('graduations.index')->with('success', 'Acara dipadam!');
    }

    public function close(Graduation $graduation)
    {
        $graduation->update(['status' => 'closed']);
        return redirect()->route('graduations.index')->with('success', 'Pendaftaran ditutup!');
    }
}

```

### Langkah 2.3: Paparan Antaramuka CRUD (Tailwind CSS)

Struktur fail UI dibina berasaskan utiliti kelas Tailwind CSS tanpa memerlukan fail skrip luaran kompleks.

#### Index View: `resources/views/graduations/index.blade.php`

```html
@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Urus Acara Graduasi</h1>
    <a href="{{ route('graduations.create') }}" class="bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg shadow">+ Cipta Acara Baru</a>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-600">Nama Acara</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-600">Tarikh Majlis</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-600">Yuran</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-600">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 bg-white">
            @foreach($graduations as $event)
            <tr>
                <td class="px-6 py-4 font-medium text-gray-900">{{ $event->title }}</td>
                <td class="px-6 py-4 text-gray-500">{{ $event->ceremony_date }}</td>
                <td class="px-6 py-4 text-gray-500">RM {{ number_format($event->fee, 2) }}</td>
                <td class="px-6 py-4 text-gray-500">{{ $event->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

```

---

## Fasa 3: Portal Pelajar & Pengesahan Dokumen

### Langkah 3.1: Validasi Input Pelajar

#### Form Request: `app/Http/Requests/StudentPortalRequest.php`

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentPortalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $studentId = $this->route('student')->id;
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email,' . $studentId,
            'phone' => 'required|string|max:20',
            'payment_receipt' => $this->route('student')->payment_receipt
                ? 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'
                : 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ];
    }
}

```

### Langkah 3.2: Logik Pengawal Operasi Pelajar

#### Controller: `app/Http/Controllers/StudentPortalController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StudentPortalRequest;

class StudentPortalController extends Controller
{
    public function edit(Student $student)
    {
        return view('portal.edit', compact('student'));
    }

    public function update(StudentPortalRequest $request, Student $student)
    {
        $data = $request->validated();
        
        if ($request->hasFile('payment_receipt')) {
            if ($student->payment_receipt) {
                Storage::disk('public')->delete($student->payment_receipt);
            }
            $data['payment_receipt'] = $request->file('payment_receipt')->store('receipts', 'public');
        }
        
        $data['paid_at'] = now();
        $data['verified_at'] = null; // Set null kembali untuk kelulusan semula pentadbir
        
        $student->update($data);
        
        return redirect()->back()->with('success', 'Maklumat dan resit anda berjaya dikemas kini!');
    }
}

```

#### Controller: `app/Http/Controllers/AdminStudentController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Student;

class AdminStudentController extends Controller
{
    public function index()
    {
        $students = Student::with('graduation')
            ->whereNotNull('paid_at')
            ->whereNull('verified_at')
            ->latest()
            ->get();
            
        return view('admin.verifications.index', compact('students'));
    }

    public function verify(Student $student)
    {
        $student->update(['verified_at' => now()]);
        return redirect()->route('admin.verifications.index')->with('success', "Pembayaran disahkan!");
    }
}

```

---

## Fasa 4: Automasi Jaminan Kualiti (Pest Smoke Testing)

Gunakan pengujian berasaskan persekitaran Pest PHP bagi mengesahkan kod respons serta aliran integrasi aplikasi sentiasa berjalan lancar.

#### Fail Ujian: `tests/Feature/GraduationTest.php`

```php
<?php

use App\Models\Graduation;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can render the graduations index page and list events', function () {
    $graduation = Graduation::factory()->create([
        'title' => 'Sesi Konvokesyen Utama 2026',
    ]);

    $this->get(route('graduations.index'))
        ->assertStatus(200)
        ->assertSee('Sesi Konvokesyen Utama 2026');
});

it('fails validation when creating a graduation with missing fields', function () {
    $this->post(route('graduations.store'), [
        'title' => '',
        'ceremony_date' => '',
        'fee' => '',
    ])->assertSessionHasErrors(['title', 'ceremony_date', 'fee']);
});

it('can store a new graduation event successfully', function () {
    $data = [
        'title' => 'Convocation Test 2026',
        'ceremony_date' => now()->addMonths(2)->format('Y-m-d'),
        'fee' => 300.00,
        'status' => 'active',
    ];

    $this->post(route('graduations.store'), $data)
        ->assertRedirect(route('graduations.index'));

    $this->assertDatabaseHas('graduations', [
        'title' => 'Convocation Test 2026',
        'fee' => 300.00
    ]);
});

```

---

## Fasa 5: Pelancaran & Pengendalian Sistem

### Senarai Arahan Pelancaran Sistem:

* 📁 **Pautan Simpanan Fail:** `php artisan storage:link`
* 🔄 **Segarkan Pangkalan Data berserta Seeder:** `php artisan migrate:fresh --seed`
* 🧪 **Menjalankan Ujian Automasi:** `php artisan test --filter=GraduationTest`
* 🚀 **Menjalankan Pelayan Tempatan Sesi Demo:** `php artisan serve --host=0.0.0.0`

```
---

### 💡 Tips Tambahan untuk GitHub:
1. **README.md**: Jika anda namakan fail ini sebagai `README.md` dan letakkannya pada folder utama (*root*) repositori anda, GitHub akan memaparkan kandungan ini secara automatik pada halaman hadapan projek anda.
2. **Ulangi Kaedah Muat Naik**: Anda boleh menggunakan semula **Method 1 (Melalui web)** atau **Method 2 (Melalui Git CLI)** yang telah saya terangkan sebelum ini untuk memuat naik fail dokumentasi pembangunan ini.

```
