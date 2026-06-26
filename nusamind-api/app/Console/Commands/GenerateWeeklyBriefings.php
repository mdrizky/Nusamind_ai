<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Models\User;
use App\Services\AiBriefingService;
use Illuminate\Console\Command;

class GenerateWeeklyBriefings extends Command
{
    protected $signature = 'briefing:generate';
    protected $description = 'Generate AI business briefing untuk semua user aktif setiap Senin pagi';

    public function handle(AiBriefingService $briefingService): int
    {
        $generated = 0;
        $processed = 0;

        User::where('role', 'user')
            ->where('status', 'active')
            ->whereHas('transactions', function ($q) {
                $q->where('transaction_date', '>=', now()->subDays(7));
            })
            ->chunkById(100, function ($users) use ($briefingService, &$generated, &$processed) {
                foreach ($users as $user) {
                    $processed++;
                    $insight = $briefingService->generateBriefing($user);

                    if ($insight) {
                        $generated++;
                        Notification::create([
                            'user_id' => $user->id,
                            'title' => 'Briefing mingguan baru',
                            'body' => 'Ringkasan bisnis minggu ini sudah siap, cek sekarang!',
                        ]);
                        $this->info("Briefing generated for user {$user->id} ({$user->name})");
                    } else {
                        $this->warn("No transactions for user {$user->id} ({$user->name}), skipped");
                    }
                }
            });

        if ($processed === 0) {
            $this->info('Tidak ada user dengan transaksi 7 hari terakhir');
            return Command::SUCCESS;
        }

        $this->info("Selesai! {$generated} briefing berhasil digenerate dari {$processed} user terproses");

        return Command::SUCCESS;
    }
}
