<?php

namespace App\Events;
use App\Models\Bid; 
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewBid implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $bid;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Bid $bid)
    {
        $this->bid = $bid;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        $event_id = $this->bid->event_id;
        return new Channel('event-'.$event_id);
    }

    
    public function broadcastAs(): string
    {
        return 'NewBid';
    }

    // public function broadcastWith()
    // {
    //     return [
    //         'id' => $this->bid->id,
    //         'product_id' => $this->bid->product_id,
    //         'user_id' => $this->bid->user_id,
    //         'bid' => $this->bid->bid,
    //     ];
    // }


}
