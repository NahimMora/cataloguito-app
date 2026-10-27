<?php

namespace App\Services;

use App\Mail\OrderRequested;
use App\Models\Order;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class OutboxProcessor
{
    public function process(int $limit = 20): int
    {
        $processed = 0;
        $started = microtime(true);
        for ($i = 0; $i < $limit; $i++) {
            if (microtime(true) - $started >= 35) {
                break;
            }
            $job = DB::transaction(function () {
                $row = DB::table('outbox')->whereIn('status', ['pending', 'processing'])->where('next_attempt_at', '<=', now())->where(fn ($q) => $q->whereNull('lease_until')->orWhere('lease_until', '<', now()))->orderBy('id')->lockForUpdate()->first();
                if (! $row) {
                    return null;
                }
                $token = (string) Str::uuid();
                DB::table('outbox')->where('id', $row->id)->update(['status' => 'processing', 'lease_token' => $token, 'lease_until' => now()->addMinutes(2), 'updated_at' => now()]);
                $row->lease_token = $token;

                return $row;
            });
            if (! $job) {
                break;
            }
            $query = fn () => DB::table('outbox')->where('id', $job->id)->where('lease_token', $job->lease_token);
            $hourKey = 'mail-shop-'.$job->shop_id.'-'.now()->format('YmdH');
            $dailyCount = DB::table('outbox')->where('shop_id', $job->shop_id)->where('status', 'sent')->where('sent_at', '>=', now()->startOfDay())->count();
            if (Cache::get($hourKey, 0) >= 30 || $dailyCount >= 100) {
                $query()->update(['status' => 'pending', 'next_attempt_at' => now()->addHour(), 'lease_until' => null]);

                continue;
            }
            try {
                $order = Order::with(['shop', 'items'])->where('shop_id', $job->shop_id)->findOrFail($job->order_id);
                Mail::to($job->recipient)->send(new OrderRequested($order));
                Cache::add($hourKey, 0, 3600);
                Cache::increment($hourKey);
                $query()->update(['status' => 'sent', 'sent_at' => now(), 'lease_until' => null, 'attempts' => $job->attempts + 1, 'error_code' => null, 'updated_at' => now()]);
                $processed++;
            } catch (\Throwable $e) {
                $attempt = $job->attempts + 1;
                $minutes = [1, 5, 15, 60, 180][min($attempt - 1, 4)];
                $query()->update(['status' => $attempt >= 5 ? 'failed' : 'pending', 'attempts' => $attempt, 'next_attempt_at' => now()->addMinutes($minutes), 'lease_until' => null, 'error_code' => class_basename($e), 'updated_at' => now()]);
                report(new \RuntimeException('Outbox delivery failed (job '.$job->id.', '.class_basename($e).')'));
            }
        }

        return $processed;
    }
}
