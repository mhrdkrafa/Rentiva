<?php

namespace App\Http\Controllers\Owner;

use App\Actions\Tenant\UpdateRentalIssueStatusAction;
use App\Http\Controllers\Controller;
use App\Models\RentalIssue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IssueController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $query = RentalIssue::whereHas('rental.unit.property', function ($p) use ($user) {
            if ($user->isAdmin()) {
                return;
            }
            $p->where('owner_id', $user->id);
        })->with(['rental.unit.property', 'tenant']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $issues = $query->latest()->paginate(10)->withQueryString();

        return view('owner.issues.index', compact('issues'));
    }

    public function show(RentalIssue $issue): View
    {
        $this->authorizeOwnerOrManager($issue);

        $issue->load(['rental.unit.property', 'rental.unit.roomType', 'tenant.profile']);

        return view('owner.issues.show', compact('issue'));
    }

    public function update(Request $request, RentalIssue $issue, UpdateRentalIssueStatusAction $action): RedirectResponse
    {
        $this->authorizeOwnerOrManager($issue);

        $validated = $request->validate([
            'status' => ['required', 'in:reported,in_review,in_progress,resolved,closed'],
            'owner_notes' => ['nullable', 'string', 'max:500'],
        ]);

        $action->execute($request->user(), $issue, $validated['status'], $validated['owner_notes'] ?? null);

        return back()->with('success', 'Status tiket keluhan berhasil diperbarui.');
    }

    protected function authorizeOwnerOrManager(RentalIssue $issue): void
    {
        $user = auth()->user();
        $ownerId = $issue->rental->unit->property->owner_id;

        if ($user && ($user->isAdmin() || $user->id === $ownerId || $user->canManageOwnerProperty($ownerId))) {
            return;
        }

        abort(403, 'Akses tidak diizinkan.');
    }
}
