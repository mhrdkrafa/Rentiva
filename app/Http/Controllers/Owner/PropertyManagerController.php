<?php

namespace App\Http\Controllers\Owner;

use App\Actions\Identity\AssignPropertyManagerAction;
use App\Actions\Identity\RevokePropertyManagerAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\AssignPropertyManagerRequest;
use App\Models\PropertyManagerAssignment;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PropertyManagerController extends Controller
{
    public function index(): View
    {
        $assignments = auth()->user()->managerAssignmentsAsOwner()
            ->with('manager')
            ->latest()
            ->get();

        return view('owner.managers', compact('assignments'));
    }

    public function store(AssignPropertyManagerRequest $request, AssignPropertyManagerAction $action): RedirectResponse
    {
        $action->execute(
            $request->user(),
            $request->input('manager_email'),
            $request->input('property_id'),
            $request->input('permissions', [])
        );

        return back()->with('success', 'Manajer properti berhasil ditugaskan.');
    }

    public function destroy(PropertyManagerAssignment $assignment, RevokePropertyManagerAction $action): RedirectResponse
    {
        $action->execute(auth()->user(), $assignment);

        return back()->with('success', 'Penugasan manajer properti berhasil dicabut.');
    }
}
