<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log; // Tambahkan ini

class SendOrderNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function handle(): void
    {
        // Simulasi pengiriman notifikasi dengan mencatatnya di log laravel
        Log::info("Notifikasi Order: Order dengan ID {$this->order->id} berhasil dibuat oleh User ID {$this->order->user_id} dengan total Rp" . $this->order->total_price);
    }
}
