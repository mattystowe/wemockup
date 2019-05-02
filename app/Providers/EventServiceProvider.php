<?php

namespace App\Providers;

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
        'App\Events\OrderCreated' => [
            'App\Listeners\SendNewOrderEmail',
            'App\Listeners\CreateItemTrelloCards'
        ],
        'App\Events\ItemJobProgressUpdated' => [
            'App\Listeners\UpdateItemJobProgress',
            'App\Listeners\UpdateItemProgress'
        ],
        'App\Events\ItemProgressUpdated' => [
            'App\Listeners\SendItemProgressWebhooks'
        ],
        'App\Events\ItemJobCompleted' => [
            'App\Listeners\ProcessCompletedItemJob'
        ],
        'App\Events\ItemQueued' => [
          'App\Listeners\UpdateTrelloCard'
        ],
        'App\Events\ItemProcessing' => [
          'App\Listeners\UpdateTrelloCard'
        ],
        'App\Events\ItemCompleted' => [
          'App\Listeners\SendItemCompleteEmail',
          'App\Listeners\UpdateTrelloCard',
          'App\Listeners\SendItemWebhooks'
        ],
        'App\Events\ItemFailed' => [
          'App\Listeners\AbortOutstandingItemJobs',
          'App\Listeners\AbortOutstandingItempostprocs',
          'App\Listeners\SendItemFailedEmail',
          'App\Listeners\UpdateTrelloCard',
          'App\Listeners\SendItemWebhooks'
        ],
        'App\Events\ItemCancelled' => [
          'App\Listeners\AbortOutstandingItemJobs',
          'App\Listeners\AbortOutstandingItempostprocs',
          'App\Listeners\SendItemCancelledEmail',
          'App\Listeners\UpdateTrelloCard',
          'App\Listeners\SendItemWebhooks'
        ],
    ];

    /**
     * Register any other events for your application.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
     */
    public function boot(DispatcherContract $events)
    {
        parent::boot($events);

        //
    }
}
