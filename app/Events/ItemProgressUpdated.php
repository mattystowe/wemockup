<?php

namespace App\Events;

use App\Events\Event;
use App\Item;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ItemProgressUpdated extends Event
{
    use SerializesModels;

    public $item;
    public $progress;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Item $item, $progress)
    {
        $this->item = $item;
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
