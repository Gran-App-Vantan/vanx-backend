<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\GameLoginSession;
use Carbon\Carbon;

class CleanupExpiredTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cleanup-expired-tokens';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '期限切れのトークンを削除します';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $deleted = GameLoginSession::where('expires_at', '<', Carbon::now())->delete();
        $this->info("{$deleted} 件の期限切れトークンを削除しました。");
        return self::SUCCESS; 
    }
}