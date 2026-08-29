<?php

namespace App\Actions\Tenant;

use App\Enums\IssuePriority;
use App\Enums\IssueStatus;
use App\Models\Rental;
use App\Models\RentalIssue;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;

class ReportRentalIssueAction
{
    public function execute(
        User $tenant,
        Rental $rental,
        string $title,
        string $description,
        string $priority = 'medium',
        ?array $photos = null
    ): RentalIssue {
        if ($tenant->id !== $rental->tenant_id && ! $tenant->isAdmin()) {
            throw new AuthorizationException('Anda tidak berhak melaporkan keluhan pada sewa ini.');
        }

        return RentalIssue::create([
            'rental_id' => $rental->id,
            'tenant_id' => $tenant->id,
            'title' => $title,
            'description' => $description,
            'priority' => IssuePriority::tryFrom($priority) ?? IssuePriority::MEDIUM,
            'status' => IssueStatus::REPORTED,
            'photos' => $photos,
        ]);
    }
}
