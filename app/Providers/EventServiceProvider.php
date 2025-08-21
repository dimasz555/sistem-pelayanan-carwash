<?php

namespace App\Providers;

use App\Events\TransactionCreated;
use App\Events\TransactionPaid;
use App\Events\VehicleDone;
use App\Listeners\SendWhatsAppTransaction;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        TransactionCreated::class => [
            SendWhatsAppTransaction::class . '@handleCreated',
        ],
        TransactionPaid::class => [
            SendWhatsAppTransaction::class . '@handlePaid',
        ],
        VehicleDone::class => [
            SendWhatsAppTransaction::class . '@handleVehicleDone',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
