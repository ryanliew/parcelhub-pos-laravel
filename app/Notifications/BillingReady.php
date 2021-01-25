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
                    ->subject("Invoice #" . $this->billing->invoice_no)
                    ->greeting("Hello!")
                    ->line('The invoice for this month is ready and is attached in PDF and Excel format in the email. Please refer to the attachments.')
                    ->line('Thank you!')
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
