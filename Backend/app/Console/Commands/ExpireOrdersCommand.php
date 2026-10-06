<?php

namespace App\Console\Commands;

use App\Enums\OrderStatusEnum;
use App\Models\Order;
use Illuminate\Console\Command;

class ExpireOrdersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:expire-initiated-orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire orders that have passed orders.expired_at, but still have INITIATED order_status.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('app:expire-initiated-orders is starting.');

        Order::query()->where('created_at', '>', now()->subWeek())
            ->where('expired_at', '<', now())
            ->whereIn('id_order_status', [OrderStatusEnum::INITIATED->id(), OrderStatusEnum::PENDING->id()])
            ->chunkById(100, function ($orders) {
                foreach ($orders as $order) {
                    $order->update([
                        'id_order_status' => OrderStatusEnum::EXPIRE->id(),
                    ]);
                }
            });

        $this->info('app:expire-initiated-orders run successfully.');
    }
}
