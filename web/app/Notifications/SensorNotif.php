<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SensorNotif extends Notification
{
    use Queueable;
    protected $nodeId, $node, $sensor, $limit, $value, $unit;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($nodeId, $node, $sensor, $status, $value, $unit)
    {
        $this->nodeId = $nodeId;
        $this->node = $node;
        $this->sensor = $sensor;
        $this->status = $status;
        $this->value = $value;
        $this->unit = $unit;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'node_id' => $this->nodeId,
            'node' => $this->node,
            'sensor' => $this->sensor,
            'status' => $this->status,
            'value' => $this->value,
            'unit' => $this->unit
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
