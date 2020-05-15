<?php

namespace App\Jobs;

use App\ClientImport;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteClientImportJobFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var ClientImport
     */
    private $clientImport;

    /**
     * Create a new job instance.
     *
     * @param ClientImport $clientImport
     */
    public function __construct(ClientImport $clientImport)
    {
        $this->clientImport = $clientImport;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        disk_s3()->delete($this->clientImport->csv);
//        $this->clientImport->delete();
    }
}
