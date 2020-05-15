<?php

namespace App\Jobs\Lien;

use App\Imports\LiensImport;
use Excel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Storage;

class ImportLienJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var string
     */
    private $file_disk;
    /**
     * @var string
     */
    private $path;
    /**
     * @var string
     */
    private $state;
    /**
     * @var string
     */
    private $county;

    /**
     * Create a new job instance.
     *
     * @param string $file_disk
     * @param string $path
     * @param string $state
     * @param string $county
     */
    public function __construct($file_disk, $path, $state, $county)
    {
        $this->file_disk = $file_disk;
        $this->path = $path;
        $this->state = $state;
        $this->county = $county;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        transaction(function () {
            $import = new LiensImport($this->state, $this->county);
            Excel::import($import, $this->path, $this->file_disk);
            Storage::disk($this->file_disk)
                ->delete($this->path);
        });
    }
}
