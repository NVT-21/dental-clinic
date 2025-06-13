<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DoctorShiftChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $managerId;
    public $date;
    public $examCount;
    public $doctorName;

    public function __construct($managerId, $date, $examCount, $doctorName)
    {
        $this->managerId = $managerId;
        $this->date = $date;
        $this->examCount = $examCount;
        $this->doctorName = $doctorName;

        Log::info('DoctorShiftChanged event constructed', [
            'managerId' => $managerId,
            'date' => $date,
            'examCount' => $examCount,
            'doctorName' => $doctorName,
        ]);
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel("manager.{$this->managerId}")];
    }

    public function broadcastAs(): string
    {
        return 'DoctorShiftChanged';
    }

    public function broadcastWith(): array
    {
        $message = "{$this->examCount} medical exam(s) for Dr. {$this->doctorName} on {$this->date} need reassignment due to their shift being marked as off.";
        return [
            'message' => $message,
            'date' => $this->date,
            'exam_count' => $this->examCount,
            'doctor_name' => $this->doctorName,
            'created_at' => now()->toISOString(),
        ];
    }

    public $connection = 'database';
    public $queue = 'default';
}