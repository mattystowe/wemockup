<?php

namespace App\Events;

use App\Events\Event;
use App\ItemJob;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ItemJobProgressUpdated extends Event
{
    use SerializesModels;

    public $itemjob;
    public $progress;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(ItemJob $itemjob, $progress)
    {
        $this->itemjob = $itemjob;
        $this->progress = $progress;
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
