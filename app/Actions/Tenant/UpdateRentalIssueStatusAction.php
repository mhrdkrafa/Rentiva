<?php

namespace App\Actions\Tenant;

use App\Enums\IssueStatus;
use App\Models\RentalIssue;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;

class UpdateRentalIssueStatusAction
{
    public function execute(
        User $user,
        RentalIssue $issue,
        string $status,
        ?string $ownerNotes = null
    ): RentalIssue {
        $ownerId = $issue->rental->unit->property->owner_id;

        // Either tenant can close issue, or owner/manager/admin can update status
        $isOwnerOrManager = $user->id === $ownerId || $user->canManageOwnerProperty($ownerId) || $user->isAdmin();
        $isTenant = $user->id === $issue->tenant_id;

        if (! $isOwnerOrManager && ! $isTenant) {
            throw new AuthorizationException('Akses tidak diizinkan untuk mengubah tiket keluhan ini.');
        }

        $newStatus = IssueStatus::tryFrom($status) ?? IssueStatus::REPORTED;

        $updates = ['status' => $newStatus];
        if ($ownerNotes !== null) {
            $updates['owner_notes'] = $ownerNotes;
        }

        if ($newStatus === IssueStatus::RESOLVED && ! $issue->resolved_at) {
            $updates['resolved_at'] = now();
        }

        $issue->update($updates);

        return $issue;
    }
}
