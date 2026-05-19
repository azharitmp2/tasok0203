<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreGraduationRequest;
use App\Http\Requests\UpdateGraduationRequest;
use App\Models\Graduation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class GraduationController extends Controller implements HasMiddleware
{
    // Defensively restrict queries to explicitly whitelisted columns
    private const SORTABLE_COLUMNS = ['name', 'ic', 'email', 'matric_card', 'created_at'];

    /**
     * Define route-bound middleware directly within the controller class.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('auth'),
            new Middleware('can:viewAny,App\\Models\\Graduation', only: ['index']),
        ];
    }

    /**
     * Display a listing of the graduations.
     */
    public function index(): View
    {
        $graduations = Graduation::query()
            ->withCount('students')
            ->latest()
            ->paginate(15);

        return view('graduations.index', compact('graduations'));
    }

    /**
     * Show the form for creating a new graduation.
     */
    public function create(): View
    {
        $this->authorize('create', Graduation::class);
        return view('graduations.create');
    }

    /**
     * Store a newly created graduation in storage.
     */
    public function store(StoreGraduationRequest $request): RedirectResponse
    {
        $graduation = Graduation::create($request->validated());

        return redirect()
            ->route('graduations.show', $graduation)
            ->with('status', 'Graduation created.');
    }

    /**
     * Display the specified graduation event specs along with a paginated, filtered, and sorted roster.
     */
    public function show(Graduation $graduation, Request $request): View
    {
        $this->authorize('view', $graduation);

        // Defensively assert column strings using strict type mapping boundaries
        $sort = in_array($request->input('sort'), self::SORTABLE_COLUMNS, true)
            ? $request->input('sort')
            : 'created_at';

        $direction = $request->input('direction') === 'asc' ? 'asc' : 'desc';

        // Compose relation filters concurrently
        $students = $graduation->students()
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
                fn ($q) => $q->whereNull('paid_at'))
            ->orderBy($sort, $direction) // Bound tightly behind Whitelist constraints
            ->paginate(15)
            ->withQueryString();

        return view('graduations.show', compact('graduation', 'students'));
    }

    /**
     * Show the form for editing the specified graduation.
     */
    public function edit(Graduation $graduation): View
    {
        $this->authorize('update', $graduation);
        return view('graduations.edit', compact('graduation'));
    }

    /**
     * Update the specified graduation in storage.
     */
    public function update(UpdateGraduationRequest $request, Graduation $graduation): RedirectResponse
    {
        $graduation->update($request->validated());

        return redirect()
            ->route('graduations.show', $graduation)
            ->with('status', 'Graduation updated.');
    }

    /**
     * Remove the specified graduation from storage.
     */
    public function destroy(Graduation $graduation): RedirectResponse
    {
        $this->authorize('delete', $graduation);
        $graduation->delete();

        return redirect()
            ->route('graduations.index')
            ->with('status', 'Graduation archived.');
    }
}