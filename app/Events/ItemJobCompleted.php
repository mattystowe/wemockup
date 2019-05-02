<?php

namespace App\Events;

use App\Events\Event;
use App\ItemJob;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ItemJobCompleted extends Event
{
    use SerializesModels;

    public $itemjob;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(ItemJob $itemjob)
    {
        $this->itemjob = $itemjob;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
