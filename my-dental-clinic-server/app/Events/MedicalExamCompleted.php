<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MedicalExamCompleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notification;

    public function __construct($notification)
    {
        $this->notification = $notification;
        Log::info('MedicalExamCompleted event constructed', [
            'notification' => $notification
        ]);
    }

    public function broadcastOn()
    {
        $channel = new PrivateChannel('employee.' . $this->notification->idEmployee);
        Log::info('Broadcasting on channel', [
            'channel' => 'employee.' . $this->notification->idEmployee
        ]);
        return $channel;
    }

    public function broadcastAs()
    {
        return 'MedicalExamCompleted';
    }

    public function broadcastWith()
    {
        $data = [
            'id' => $this->notification->id,
            'message' => $this->notification->message,
            'created_at' => $this->notification->created_at
        ];
        Log::info('Broadcasting data', $data);
        return $data;
    }
}