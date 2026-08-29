<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\Permission;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Support\MediaStorage;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'status' => UserStatus::class,
        ];
    }

    /**
     * Profile relationships
     */
    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    public function ownerProfile(): HasOne
    {
        return $this->hasOne(OwnerProfile::class);
    }

    public function managerAssignmentsAsOwner(): HasMany
    {
        return $this->hasMany(PropertyManagerAssignment::class, 'owner_id');
    }

    public function managerAssignmentsAsManager(): HasMany
    {
        return $this->hasMany(PropertyManagerAssignment::class, 'manager_id');
    }

    public function bookingRequests(): HasMany
    {
        return $this->hasMany(BookingRequest::class, 'tenant_id');
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function favoriteProperties(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Property::class, 'favorites');
    }

    public function rentals(): HasMany
    {
        return $this->hasMany(Rental::class, 'tenant_id');
    }

    public function rentalIssues(): HasMany
    {
        return $this->hasMany(RentalIssue::class, 'tenant_id');
    }

    /**
     * Determine if the user can access a given Filament panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        if ($this->status !== UserStatus::ACTIVE) {
            return false;
        }

        if ($panel->getId() === 'admin') {
            return $this->role?->canAccessAdminPanel() ?? false;
        }

        return true;
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === UserRole::SUPER_ADMIN;
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, [UserRole::SUPER_ADMIN, UserRole::ADMIN], true);
    }

    public function isOwner(): bool
    {
        return $this->role === UserRole::OWNER;
    }

    public function isPropertyManager(): bool
    {
        return $this->role === UserRole::PROPERTY_MANAGER;
    }

    public function isTenant(): bool
    {
        return $this->role === UserRole::TENANT;
    }

    public function isActive(): bool
    {
        return $this->status === UserStatus::ACTIVE;
    }

    /**
     * Check if user has a specific permission based on role.
     */
    public function hasPermission(Permission $permission): bool
    {
        if (! $this->isActive()) {
            return false;
        }

        if ($this->isSuperAdmin()) {
            return true;
        }

        return match ($this->role) {
            UserRole::ADMIN => in_array($permission, [
                Permission::ACCESS_ADMIN_PANEL,
                Permission::MANAGE_USERS,
                Permission::MANAGE_CMS,
                Permission::MANAGE_SETTINGS,
                Permission::MODERATE_PROPERTIES,
            ], true),

            UserRole::OWNER => in_array($permission, [
                Permission::MANAGE_PROPERTIES,
                Permission::MANAGE_UNITS,
                Permission::MANAGE_PRICING,
                Permission::REVIEW_BOOKINGS,
                Permission::ACCEPT_BOOKINGS,
                Permission::REJECT_BOOKINGS,
                Permission::VIEW_FINANCE,
                Permission::ASSIGN_MANAGERS,
                Permission::CREATE_REVIEW,
            ], true),

            UserRole::PROPERTY_MANAGER => in_array($permission, [
                Permission::MANAGE_ASSIGNED_UNITS,
                Permission::REVIEW_ASSIGNED_BOOKINGS,
                Permission::MANAGE_ASSIGNED_AVAILABILITY,
            ], true),

            UserRole::TENANT => in_array($permission, [
                Permission::REQUEST_BOOKING,
                Permission::CANCEL_OWN_BOOKING,
                Permission::VIEW_OWN_RENTALS,
                Permission::CREATE_REVIEW,
            ], true),

            default => false,
        };
    }

    /**
     * Check if this user can manage properties on behalf of an owner.
     */
    public function canManageOwnerProperty(int $ownerId, ?string $requiredPermission = null): bool
    {
        if (! $this->isActive()) {
            return false;
        }

        if ($this->id === $ownerId && $this->isOwner()) {
            return true;
        }

        if ($this->isAdmin()) {
            return true;
        }

        $assignment = $this->managerAssignmentsAsManager()
            ->where('owner_id', $ownerId)
            ->where('status', 'active')
            ->first();

        if (! $assignment) {
            return false;
        }

        if ($requiredPermission) {
            return $assignment->hasPermission($requiredPermission);
        }

        return true;
    }

    /**
     * Get avatar image URL helper.
     */
    public function getAvatarUrlAttribute(): string
    {
        return MediaStorage::publicUrl($this->profile?->avatar_path);
    }
}
