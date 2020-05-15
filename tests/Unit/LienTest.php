<?php

namespace Tests\Unit;

use App\Lien;
use App\LienFile;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LienTest extends TestCase
{
    use DatabaseTransactions;

    public function testGetFileUrlsAttribute()
    {
        /** @var Lien $lien */
        $lien = factory(Lien::class)
            ->create();

        /** @var LienFile[]|Collection $files */
        $files = factory(LienFile::class, 3)
            ->create([
                'lien_id' => $lien->id,
            ]);

        factory(LienFile::class, 2)
            ->create();

        $expected = [];
        $url = rtrim(config('filesystems.disks.s3.url'), '/');
        $folder = trim(config('lien.files_folder'), '/');
        foreach ($files as $file) {
            $expected[] = $url . '/' . $folder . '/' . $file->file_name;
        }
        $this->assertEquals($expected, $lien->file_urls);

        $this->assertArrayHasKey('file_urls', $lien->attributesToArray());
    }
}
