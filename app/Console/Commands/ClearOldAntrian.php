<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Antrian;
use Carbon\Carbon;

class ClearOldAntrian extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'antrian:clear-old';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete antrian records where status has not changed for more than 24 hours';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $threshold = Carbon::now()->subHours(24);

        $oldAntrians = Antrian::where('updated_at', '<=', $threshold)->get();

        $deletedCount = 0;
        foreach ($oldAntrians as $antrian) {
            // Assuming status unchanged means updated_at is old
            $antrian->delete();
            $deletedCount++;
        }

        $this->info("Deleted {$deletedCount} old antrian records.");

        return 0;
    }
}
