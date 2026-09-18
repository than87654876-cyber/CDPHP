<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\CustomerBackup;
use App\Models\CustomerBackupLog;
use App\Models\DailySchedule;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ServicePackage;
use App\Models\Subscription;
use App\Models\SubscriptionPause;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CustomerBackupService
{
    protected string $backupFolder = 'customer_backups';

    public function createBackup(?User $actor = null): CustomerBackup
    {
        if (!class_exists('ZipArchive')) {
            throw new \RuntimeException('PHP extension zip chưa được bật.');
        }

        $timestamp = now();
        $filename = 'Backup_Customers_' . $timestamp->format('Ymd_His') . '.zip';
        $storagePath = $this->backupFolder . '/' . $filename;

        $backup = CustomerBackup::create([
            'filename' => $filename,
            'storage_path' => $storagePath,
            'size' => 0,
            'status' => 'pending',
            'created_by' => $actor?->id,
        ]);

        try {
            $this->checkStorageSpace();

            $payload = $this->buildBackupPayload();
            $metadata = [
                'generated_at' => $timestamp->toDateTimeString(),
                'generated_by' => $actor?->email ?? 'system',
                'driver' => config('database.default'),
                'tables' => array_keys($payload),
            ];

            $tempPath = storage_path('app/' . $this->backupFolder . '/tmp_' . $timestamp->format('Ymd_His') . '.zip');
            if (!is_dir(dirname($tempPath))) {
                mkdir(dirname($tempPath), 0755, true);
            }

            $zip = new \ZipArchive();
            if ($zip->open($tempPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
                throw new \RuntimeException('Không thể tạo file zip sao lưu.');
            }

            $zip->addFromString('backup.json', json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            $zip->addFromString('metadata.json', json_encode($metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            $zip->close();

            $storageAbsolutePath = Storage::disk('local')->path($storagePath);
            if (!is_dir(dirname($storageAbsolutePath))) {
                mkdir(dirname($storageAbsolutePath), 0755, true);
            }

            if (!rename($tempPath, $storageAbsolutePath)) {
                throw new \RuntimeException('Không thể lưu file sao lưu vào hệ thống.');
            }

            $size = filesize($storageAbsolutePath) ?: 0;
            $backup->update(['size' => $size, 'status' => 'completed']);
            $this->log($backup, 'create', 'Backup created successfully.', $actor);

            return $backup;
        } catch (\Throwable $exception) {
            $backup->update(['status' => 'failed']);
            $this->log($backup, 'create_failed', $exception->getMessage(), $actor);
            if (isset($tempPath) && file_exists($tempPath)) {
                @unlink($tempPath);
            }
            throw $exception;
        }
    }

    public function restoreBackup(CustomerBackup $backup, ?User $actor = null): CustomerBackup
    {
        if (!Storage::disk('local')->exists($backup->storage_path)) {
            throw new \RuntimeException('File sao lưu không tồn tại.');
        }

        $backupAbsolutePath = Storage::disk('local')->path($backup->storage_path);
        $zip = new \ZipArchive();
        if ($zip->open($backupAbsolutePath) !== true) {
            throw new \RuntimeException('Không thể mở file sao lưu.');
        }

        $content = $zip->getFromName('backup.json');
        $zip->close();

        if ($content === false) {
            throw new \RuntimeException('File sao lưu không hợp lệ hoặc thiếu dữ liệu.');
        }

        $payload = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Dữ liệu sao lưu không thể giải mã: ' . json_last_error_msg());
        }

        $this->validateBackupPayload($payload);

        DB::beginTransaction();
        try {
            $this->disableForeignKeyChecks();
            $this->performRestore($payload);
            $this->enableForeignKeyChecks();
            DB::commit();

            $backup->update([
                'status' => 'restored',
                'restored_by' => $actor?->id,
                'restored_at' => now(),
            ]);
            $this->log($backup, 'restore', 'Restore completed successfully.', $actor);

            return $backup;
        } catch (\Throwable $exception) {
            DB::rollBack();
            $this->enableForeignKeyChecks();
            $this->log($backup, 'restore_failed', $exception->getMessage(), $actor);
            throw $exception;
        }
    }

    public function deleteBackup(CustomerBackup $backup, ?User $actor = null): void
    {
        if (Storage::disk('local')->exists($backup->storage_path)) {
            Storage::disk('local')->delete($backup->storage_path);
        }

        $this->log($backup, 'delete', 'Backup deleted.', $actor);
        $backup->delete();
    }

    protected function buildBackupPayload(): array
    {
        $users = User::where('role', 'customer')->get();
        $userIds = $users->pluck('id')->all();

        $orders = Order::whereIn('user_id', $userIds)->get();
        $orderIds = $orders->pluck('id')->all();

        $orderItems = OrderItem::whereIn('order_id', $orderIds)->get();
        $couponIds = $orders->pluck('coupon_id')->filter()->unique()->all();

        $subscriptions = Subscription::whereIn('user_id', $userIds)->get();
        $subscriptionIds = $subscriptions->pluck('id')->all();
        $packageIds = $subscriptions->pluck('package_id')->filter()->unique()->all();

        $servicePackages = ServicePackage::whereIn('id', $packageIds)->get();
        $dailySchedules = DailySchedule::whereIn('subscription_id', $subscriptionIds)->get();
        $subscriptionPauses = SubscriptionPause::whereIn('subscription_id', $subscriptionIds)->get();
        $coupons = Coupon::whereIn('id', $couponIds)->get();

        return [
            'users' => $users->map(function (User $user) {
                return $user->makeVisible(['password', 'remember_token'])->toArray();
            })->all(),
            'orders' => $orders->map(function (Order $order) {
                return $order->toArray();
            })->all(),
            'order_items' => $orderItems->map(function (OrderItem $item) {
                return $item->toArray();
            })->all(),
            'subscriptions' => $subscriptions->map(function (Subscription $subscription) {
                return $subscription->toArray();
            })->all(),
            'service_packages' => $servicePackages->map(function (ServicePackage $package) {
                return $package->toArray();
            })->all(),
            'daily_schedules' => $dailySchedules->map(function (DailySchedule $schedule) {
                return $schedule->toArray();
            })->all(),
            'subscription_pauses' => $subscriptionPauses->map(function (SubscriptionPause $pause) {
                return $pause->toArray();
            })->all(),
            'coupons' => $coupons->map(function (Coupon $coupon) {
                return $coupon->toArray();
            })->all(),
        ];
    }

    protected function validateBackupPayload(array $payload): void
    {
        $required = [
            'users',
            'orders',
            'order_items',
            'subscriptions',
            'service_packages',
            'daily_schedules',
            'subscription_pauses',
            'coupons',
        ];

        foreach ($required as $key) {
            if (!array_key_exists($key, $payload)) {
                throw new \RuntimeException('Tệp sao lưu thiếu phần dữ liệu: ' . $key);
            }
        }
    }

    protected function performRestore(array $payload): void
    {
        $userIds = collect($payload['users'])->pluck('id')->all();
        $subscriptionIds = collect($payload['subscriptions'])->pluck('id')->all();
        $orderIds = collect($payload['orders'])->pluck('id')->all();

        if (!empty($orderIds)) {
            DB::table('order_items')->whereIn('order_id', $orderIds)->delete();
            DB::table('orders')->whereIn('id', $orderIds)->delete();
        }

        if (!empty($subscriptionIds)) {
            DB::table('daily_schedules')->whereIn('subscription_id', $subscriptionIds)->delete();
            DB::table('subscription_pauses')->whereIn('subscription_id', $subscriptionIds)->delete();
            DB::table('subscriptions')->whereIn('id', $subscriptionIds)->delete();
        }

        if (!empty($userIds)) {
            DB::table('orders')->whereIn('user_id', $userIds)->delete();
            DB::table('subscriptions')->whereIn('user_id', $userIds)->delete();
            DB::table('users')->whereIn('id', $userIds)->delete();
        }

        if (!empty($payload['service_packages'])) {
            DB::table('service_packages')->upsert($payload['service_packages'], ['id']);
        }

        if (!empty($payload['coupons'])) {
            DB::table('coupons')->upsert($payload['coupons'], ['id']);
        }

        if (!empty($payload['users'])) {
            DB::table('users')->insert($payload['users']);
        }

        if (!empty($payload['subscriptions'])) {
            DB::table('subscriptions')->insert($payload['subscriptions']);
        }

        if (!empty($payload['daily_schedules'])) {
            DB::table('daily_schedules')->insert($payload['daily_schedules']);
        }

        if (!empty($payload['subscription_pauses'])) {
            DB::table('subscription_pauses')->insert($payload['subscription_pauses']);
        }

        if (!empty($payload['orders'])) {
            DB::table('orders')->insert($payload['orders']);
        }

        if (!empty($payload['order_items'])) {
            DB::table('order_items')->insert($payload['order_items']);
        }
    }

    protected function disableForeignKeyChecks(): void
    {
        $driver = DB::getDriverName();
        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        } elseif ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        } elseif ($driver === 'pgsql') {
            DB::statement('SET session_replication_role = replica;');
        }
    }

    protected function enableForeignKeyChecks(): void
    {
        $driver = DB::getDriverName();
        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        } elseif ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        } elseif ($driver === 'pgsql') {
            DB::statement('SET session_replication_role = DEFAULT;');
        }
    }

    protected function log(CustomerBackup $backup, string $action, string $message, ?User $actor = null): void
    {
        CustomerBackupLog::create([
            'backup_id' => $backup->id,
            'action' => $action,
            'message' => $message,
            'performed_by' => $actor?->id,
        ]);

        Log::info('[CustomerBackup][' . $action . '] ' . $message, [
            'backup_id' => $backup->id,
            'performed_by' => $actor?->id,
            'filename' => $backup->filename,
        ]);
    }

    protected function checkStorageSpace(): void
    {
        $path = storage_path('app');
        $free = @disk_free_space($path) ?: 0;
        if ($free < 20 * 1024 * 1024) {
            throw new \RuntimeException('Không đủ dung lượng lưu trữ để tạo bản sao lưu.');
        }
    }
}
