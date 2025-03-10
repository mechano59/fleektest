<?php

namespace App\Providers;

// No need for specific event/listener imports here

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Broadcast; // Import Broadcast

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return true; // Enable automatic discovery
    }

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        Broadcast::routes(['middleware' => ['web','auth']]);
        // Channel authorization within EventServiceProvider:

        Broadcast::channel('user.{id}', function ($user, $id) {
            return (int) $user->id === (int) $id;  // This is correct.
        });
        
        Broadcast::channel('admin-channel', function ($user) {
            return $user->isAdmin(); // Make SURE isAdmin() returns a boolean.
        }); 
    }
}
