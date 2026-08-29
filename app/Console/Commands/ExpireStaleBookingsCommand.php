<?php

namespace App\Console\Commands;

use App\Actions\Booking\ExpireStaleBookingsAction;
use Illuminate\Console\Command;

class ExpireStaleBookingsCommand extends Command
{
    protected $signature = 'rentiva:expire-bookings';

    protected $description = 'Expire pending and approved booking requests whose deadline has passed';

    public function handle(ExpireStaleBookingsAction $action): int
    {
        $count = $action->execute();
        $this->info("Successfully expired {$count} stale booking requests.");

        return Command::SUCCESS;
    }
}
