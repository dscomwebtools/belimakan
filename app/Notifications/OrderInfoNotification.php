<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderInfoNotification extends Notification
{
    use Queueable;

    protected $order;

    /**
     * Buat instance baru.
     */
    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * Channel pengiriman notifikasi.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Isi email yang dikirim ke user.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $order = $this->order;

        return (new MailMessage)
            ->subject('Pesanan #' . $order->invoice . ' Telah Dikonfirmasi')
            ->greeting('Halo, ' . $notifiable->name)
            ->line('Pesanan Anda dengan nomor ' . $order->invoice . ' telah dikonfirmasi.')
            ->line('Silakan lanjutkan proses pembayaran untuk menyelesaikan pesanan Anda.')
            // ->line('Total yang harus dibayar: Rp' . number_format($order->total, 0, ',', '.'))
            ->action('Lihat Detail Pesanan', url('/profile?open=order&order_id=' . $order->id))
            ->line('Terima kasih telah berbelanja di ' . config('app.name') . '!')
            ->salutation('Hormat kami, ' . config('app.name'));
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
