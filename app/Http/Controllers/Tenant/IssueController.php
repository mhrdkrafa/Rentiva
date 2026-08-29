<?php

namespace App\Http\Controllers\Tenant;

use App\Actions\Tenant\ReportRentalIssueAction;
use App\Http\Controllers\Controller;
use App\Models\Rental;
use App\Models\RentalIssue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IssueController extends Controller
{
    public function index(Request $request): View
    {
        $issues = $request->user()->rentalIssues()
            ->with(['rental.unit.property'])
            ->latest()
            ->paginate(10);

        return view('tenant.issues.index', compact('issues'));
    }

    public function create(Request $request): View
    {
        $rentals = $request->user()->rentals()->active()->with('unit.property')->get();

        $selectedRentalId = $request->query('rental_id');

        return view('tenant.issues.create', compact('rentals', 'selectedRentalId'));
    }

    public function store(Request $request, ReportRentalIssueAction $action): RedirectResponse
    {
        $validated = $request->validate([
            'rental_id' => ['required', 'exists:rentals,id'],
            'title' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string', 'max:1000'],
            'priority' => ['required', 'in:low,medium,high,urgent'],
        ]);

        $rental = Rental::findOrFail($validated['rental_id']);

        $issue = $action->execute(
            $request->user(),
            $rental,
            $validated['title'],
            $validated['description'],
            $validated['priority']
        );

        return redirect()->route('tenant.issues.show', $issue)
            ->with('success', 'Laporan keluhan Anda berhasil dikirim ke pemilik kost.');
    }

    public function show(RentalIssue $issue): View
    {
        if (auth()->id() !== $issue->tenant_id && ! auth()->user()->isAdmin()) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $issue->load(['rental.unit.property.owner', 'rental.unit.roomType']);

        return view('tenant.issues.show', compact('issue'));
    }
}
