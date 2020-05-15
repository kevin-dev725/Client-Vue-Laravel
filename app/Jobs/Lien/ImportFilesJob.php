<?php

namespace App\Jobs\Lien;

use App\LienImportedFile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Storage;
use Zipper;

class ImportFilesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var string
     */
    private $disk;
    /**
     * @var string
     */
    private $path;

    /**
     * Create a new job instance.
     *
     * @param string $disk
     * @param string $path
     */
    public function __construct($disk, $path)
    {
        $this->disk = $disk;
        $this->path = $path;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        transaction(function () {
            $zip_path = temp_path();
            $contents = Storage::disk($this->disk)->get($this->path);
            file_put_contents($zip_path, $contents);
            $zipper = Zipper::make($zip_path);

            Storage::makeDirectory($files_dir = 'lien/' . uniqid());
            $zipper->extractTo(Storage::path($files_dir));

            $files = Storage::files($files_dir);
            foreach ($files as $file_path) {
                $file_name = basename($file_path);
                $imported_file = LienImportedFile::create([
                    'file_name' => $file_name,
                ]);
                $imported_file->addMedia(Storage::path($file_path))
                    ->toMediaCollection();
            }

            Storage::deleteDirectory($files_dir);
            Storage::disk($this->disk)->delete($this->path);
        });
    }
}
