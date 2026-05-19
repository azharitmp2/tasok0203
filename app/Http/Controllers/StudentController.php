<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Models\Graduation;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Spatie\SimpleExcel\SimpleExcelReader;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Http\Requests\BulkStudentActionRequest;

class StudentController extends Controller implements HasMiddleware
{

// Add this method inside the StudentController class body:

    /**
     * Process batch administrative functions (payment verification / data pruning).
     */
    public function bulk(BulkStudentActionRequest $request, Graduation $graduation): RedirectResponse
    {
        $data = $request->validated();
        $ids = $data['ids'];
        $action = $data['action'];

        // Defensively scope execution arrays strictly onto THIS graduation instance relation
        $scope = $graduation->students()->whereIn('uuid', $ids);

        if ($action === 'verify') {
            // Duplicate query builders via cloning to process sub-metrics concurrently
            $toVerify = (clone $scope)->whereNotNull('payment_receipt')->get();
            $skipped = (clone $scope)->whereNull('payment_receipt')->count();

            foreach ($toVerify as $student) {
                $student->update(['verified_at' => now()]);
            }

            return redirect()
                ->route('graduations.show', $graduation)
                ->with('status', "Batch update processed: Verified {$toVerify->count()} records. Skipped {$skipped} entries missing receipts.");
        }

        // Processing batch data deletion branch
        $removed = (clone $scope)->count();
        $scope->delete();

        return redirect()
            ->route('graduations.show', $graduation)
            ->with('status', "Batch removal processed: Completely cleared {$removed} student records from this roster session.");
    }

/**
     * Define route-bound middleware directly within the controller class.
     */
    public static function middleware(): array
    {
        return [new Middleware('auth')];
    }

    /**
     * Show the form for creating a new student.
     */
    public function create(Graduation $graduation): View
    {
        $this->authorize('create', Student::class);

        return view('students.create', compact('graduation'));
    }

    /**
     * Store a newly created student in storage.
     */
    public function store(StoreStudentRequest $request, Graduation $graduation): RedirectResponse
    {
        // Automatically assigns the correct foreign key graduation_id behind the scenes
        $student = $graduation->students()->create($request->validated());

        return redirect()
            ->route('graduations.show', $graduation)
            ->with('status', "Added {$student->name} successfully.");
    }

    /**
     * Bulk import students from an uploaded CSV spreadsheet file roster.
     */
    public function import(Request $request, Graduation $graduation): RedirectResponse
    {
        $this->authorize('create', Student::class);

        $request->validate([
            'csv' => ['required', 'file', 'mimes:csv,txt', 'max:2048'], // Maximum file limit: 2MB
        ]);

        $imported = 0;
        $skipped = 0;

        // Streams rows on-the-fly to keep memory consumption low
        SimpleExcelReader::create($request->file('csv')->getRealPath(), 'csv')
            ->getRows()
            ->each(function (array $row) use ($graduation, &$imported, &$skipped) {
                // Execute individual cell validations per item array structure
                $validator = Validator::make($row, [
                    'name' => ['required', 'string', 'max:255'],
                    'ic' => ['required', 'string', 'size:12', 'unique:students,ic'], // 12-digit exact match
                    'email' => ['required', 'email', 'unique:students,email'],
                    'matric_card' => ['required', 'string', 'max:100'],
                    'phone' => ['required', 'string', 'max:20'],
                ]);

                if ($validator->fails()) {
                    $skipped++;
                    return; // Gracefully continue loop sequence for subsequent items
                }

                // Append item cleanly onto nested database relation collection
                $graduation->students()->create($validator->validated());
                $imported++;
            });

        return redirect()
            ->route('graduations.show', $graduation)
            ->with('status', "Spreadsheet data processed: Imported {$imported} records. Skipped {$skipped} invalid rows.");
    }

    /**
     * Stream a filtered CSV export of the student roster.
     */
    public function export(Request $request, Graduation $graduation): StreamedResponse
    {
        $this->authorize('viewAny', Student::class);

        // Build the query pipeline using the exact same filters as the index views
        $query = $graduation->students()
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = '%' . trim((string) $request->input('search')) . '%';
                
                $q->where(function ($inner) use ($term) {
                    $inner->where('name', 'like', $term)
                        ->orWhere('ic', 'like', $term)
                        ->orWhere('email', 'like', $term)
                        ->orWhere('matric_card', 'like', $term);
                });
            })
            ->when($request->status === 'verified',
                fn ($q) => $q->whereNotNull('verified_at'))
            ->when($request->status === 'pending',
                fn ($q) => $q->whereNotNull('paid_at')->whereNull('verified_at'))
            ->when($request->status === 'not_paid',
                fn ($q) => $q->whereNull('paid_at'));

        // Generate a descriptive, timestamped slug filename
        $filename = Str::slug($graduation->title) . '-students-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            
            // Set up document headers
            fputcsv($out, ['name', 'ic', 'email', 'matric_card', 'phone', 'paid_at', 'verified_at']);

            // Stream rows in chunks under a flat memory profile
            $query->lazy()->each(function (Student $student) use ($out) {
                fputcsv($out, [
                    $student->name,
                    $student->ic,
                    $student->email,
                    $student->matric_card,
                    $student->phone,
                    $student->paid_at?->toIso8601String(),
                    $student->verified_at?->toIso8601String(),
                ]);
            });

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Display the specified student profile.
     */
    public function show(Graduation $graduation, Student $student): View
    {
        $this->authorize('view', $student);

        return view('students.show', compact('graduation', 'student'));
    }

    /**
     * Show the form for editing the specified student profile.
     */
    public function edit(Graduation $graduation, Student $student): View
    {
        $this->authorize('update', $student);

        return view('students.edit', compact('graduation', 'student'));
    }

    /**
     * Update the specified student profile in storage.
     */
    public function update(
        UpdateStudentRequest $request,
        Graduation $graduation,
        Student $student
    ): RedirectResponse {
        $data = $request->validated();

        // Safe file management tracking
        if ($request->hasFile('payment_receipt')) {
            $data['payment_receipt'] = $request->file('payment_receipt')
                ->store('receipts', 'public');
            
            // Auto-stamp payment timestamp only when a fresh receipt is submitted
            $data['paid_at'] = now();
        }

        $student->update($data);

        return redirect()
            ->route('graduations.students.show', [$graduation, $student])
            ->with('status', 'Student details updated.');
    }

    /**
     * Remove the specified student from storage.
     */
    public function destroy(Graduation $graduation, Student $student): RedirectResponse
    {
        $this->authorize('delete', $student);

        $student->delete();

        return redirect()
            ->route('graduations.show', $graduation)
            ->with('status', 'Student record removed.');
    }

    /**
     * Custom Action Endpoint: Approves student registration payments.
     */
    public function verify(Graduation $graduation, Student $student): RedirectResponse
    {
        $this->authorize('verify', $student);

        $student->update(['verified_at' => now()]);

        return redirect()
            ->route('graduations.students.show', [$graduation, $student])
            ->with('status', 'Payment verified.');
    }
}