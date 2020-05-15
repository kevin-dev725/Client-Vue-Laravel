<?php

namespace App\Jobs;

use App\QuickbooksImport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteQuickbooksImportJobFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var QuickbooksImport
     */
    private $import;

    /**
     * Create a new job instance.
     *
     * @param QuickbooksImport $import
     */
    public function __construct(QuickbooksImport $import)
    {
        $this->import = $import;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        disk_s3()->delete($this->import->path);
    }
}
