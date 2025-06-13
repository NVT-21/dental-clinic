<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PatientAssigned implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $patientName;
    public $doctorName;
    public $doctorId;
    public $isNew; // Thêm cờ để phân biệt tạo mới hoặc cập nhật

    /**
     * Create a new event instance.
     *
     * @param string $patientName
     * @param string $doctorName
     * @param int $doctorId
     * @param bool $isNew (mặc định là true nếu tạo mới)
     */
    public function __construct($patientName, $doctorName, $doctorId, $isNew = true)
    {
        $this->patientName = $patientName;
        $this->doctorName = $doctorName;
        $this->doctorId = $doctorId;
        $this->isNew = $isNew; // True nếu tạo mới, False nếu cập nhật
        
        Log::info('PatientAssigned event constructed', [
            'patientName' => $patientName,
            'doctorName' => $doctorName,
            'doctorId' => $doctorId,
            'isNew' => $isNew
        ]);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $channel = new PrivateChannel("doctor.{$this->doctorId}");
        Log::info('Broadcasting on channel', ['channel' => "doctor.{$this->doctorId}"]);
        return [$channel];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'PatientAssigned';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        try {
            // Xây dựng message dựa trên trạng thái tạo mới hoặc cập nhật
            $message = $this->isNew
            ? "Patient {$this->patientName} has been assigned to you, Dr. {$this->doctorName}, for a new examination!"
            : "Patient {$this->patientName} has been reassigned to you, Dr. {$this->doctorName}, to continue the examination!";

            Log::info("Broadcasting PatientAssigned event", [
                'patientName' => $this->patientName,
                'doctorName' => $this->doctorName,
                'doctorId' => $this->doctorId,
                'isNew' => $this->isNew,
                'channel' => "doctor.{$this->doctorId}",
            ]);

            $data = [
                'message' => $message,
                'patient_name' => $this->patientName,
                'doctor_name' => $this->doctorName,
                'created_at' => now()->toISOString(),
                'is_new' => $this->isNew, // Gửi cờ này để FE xử lý nếu cần
            ];

            Log::info("Broadcasting data", $data);
            return $data;
        } catch (\Exception $e) {
            Log::error("Error in broadcastWith: " . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * The name of the queue connection to use when broadcasting the event.
     *
     * @var string
     */
    public $connection = 'database'; // Hoặc queue connection bạn đang dùng

    /**
     * The name of the queue on which to place the broadcasting job.
     *
     * @var string
     */
    public $queue = 'default';
}