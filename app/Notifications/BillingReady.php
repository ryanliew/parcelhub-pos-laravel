<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class BillingReady extends Notification implements ShouldQueue
{
    use Queueable;

    public $billing;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($billing)
    {
        $this->billing = $billing;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject($this->billing->vendor_name . "Invoice of " . $this->billing->billing_start->toDateString() . " - " . $this->billing->billing_end->toDateString())
                    ->greeting("Hi,")
                    ->line('Here is the ' . $this->billing->vendor_name . " invoice of " . $this->billing->billing_start->toDateString() . " - " . $this->billing->billing_end->toDateString())
                    ->line('Kindly check.')
                    ->attach(storage_path("app/public/billing/" . $this->billing->branch_id . "/" . $this->billing->file_name . ".pdf"), [
                        "as" => $this->billing->file_name . ".pdf",
                        "mime" => "application/pdf",
                    ])
                    ->attach(storage_path("app/public/billing/" . $this->billing->branch_id . "/" . $this->billing->file_name . ".xls"), [
                        "as" => $this->billing->file_name . ".xls",
                        "mime" => "application/vnd.ms-excel",
                    ]);
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
