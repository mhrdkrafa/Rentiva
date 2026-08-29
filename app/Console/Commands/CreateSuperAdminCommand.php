<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateSuperAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rentiva:super-admin {--name= : Name of the super admin} {--email= : Email address} {--password= : Password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create or update a Rentiva super administrator account';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = $this->option('name') ?: $this->ask('Full Name', 'Super Admin');
        $email = $this->option('email') ?: $this->ask('Email Address', 'admin@rentiva.test');
        $password = $this->option('password') ?: $this->secret('Password (leave empty for "password")');

        if (empty($password)) {
            $password = 'password';
        }

        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return self::FAILURE;
        }

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'role' => UserRole::SUPER_ADMIN,
                'status' => UserStatus::ACTIVE,
                'email_verified_at' => now(),
            ]
        );

        $this->info("Super Admin [{$user->email}] configured successfully.");

        return self::SUCCESS;
    }
}
