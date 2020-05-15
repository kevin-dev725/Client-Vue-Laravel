<?php

namespace App\Console\Commands\Lien;

use App\Lien;
use Illuminate\Console\Command;

class PruneOldLiens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clientDomain:prune-old-liens';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prune liens created 5 years ago.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $liens = Lien::where('created_at', '<=', now()->subYears(5)->toDateTimeString())
            ->cursor();
        foreach ($liens as $lien) {
            $lien->delete();
        }
    }
}
