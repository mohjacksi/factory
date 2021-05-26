<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DBNotification extends Notification
{
    use Queueable;

    private $data;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /*
     * @return array
     */

    public function toDatabase($notifiable)
    {
        return [
            'title' => $this->data['msg'],
            'msg' => $this->data['msg'],
            'priority' => 'high',
            'to' => $this->data['to'],
            'model_id' => array_key_exists('model_id', $this->data) ? $this->data['model_id'] : null,
            'model_type' => array_key_exists('model_type', $this->data) ? $this->data['model_type'] : null,];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
//            'msg' => $this->data['msg'],
//            'to' => explode(',', $this->data['to']),
        ];
    }
}
