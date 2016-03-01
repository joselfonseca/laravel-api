<?php

namespace App\Providers;

use App\Entities\Users\User;
use App\Observers\UuidObserver;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [

    ];

    /**
     * Register any other events for your application.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher $events
     * @return void
     */
    public function boot(DispatcherContract $events)
    {
        parent::boot($events);
        $this->registerObservers();
    }

    /**
     * @return $this
     */
    public function registerObservers()
    {
        User::observe(app(UuidObserver::class));
        return $this;
    }
}
