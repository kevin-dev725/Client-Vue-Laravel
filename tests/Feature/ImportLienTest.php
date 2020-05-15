<?php

namespace Tests\Feature;

use AdrianMejias\States\States;
use App\LienImportedFile;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Storage;
use Tests\TestCase;
use Tests\Traits\WithAdmin;

class ImportLienTest extends TestCase
{
    use WithAdmin, WithFaker, DatabaseTransactions;

    public function testImportLienRecordXls()
    {
        $this->actingAs($this->admin);

        $file = new UploadedFile(Storage::disk('local')->path('testing/lien-records.xlsx'), 'lien-records.xlsx', null, null, null, true);

        $this->post('/web-api/lien/import', $payload = [
            'file' => $file,
            'state' => States::query()->where('country_code', 'US')->inRandomOrder()->first(['iso_3166_2'])->iso_3166_2,
            'county' => $this->faker->name,
        ])
            ->assertSuccessful();

        /**  Create a new Reader of the type defined in $inputFileType  **/
        $reader = IOFactory::createReader('Xlsx');
        /**  Advise the Reader that we only want to load cell data  **/
        $reader->setReadDataOnly(true);
        /**  Load $inputFileName to a Spreadsheet Object  **/
        $spreadsheet = $reader->load($file->path());

        foreach ($spreadsheet->getActiveSheet()->getRowIterator(3) as $item) {
            $row = [];
            foreach ($item->getCellIterator('A', 'D') as $cell) {
                $row[] = $cell->getValue();
            }
            $this->assertDatabaseHas('liens', [
                'legal' => $row[0],
                'lienor' => $row[1],
                'owner' => $row[2],
                'state' => $payload['state'],
                'county' => $payload['county'],
            ]);
            $files = explode(',', $row[3]);
            foreach ($files as $file_name) {
                $this->assertDatabaseHas('lien_files', [
                    'file_name' => trim($file_name)
                ]);
            }
        }
    }

    public function testImportLienFiles()
    {
        $this->actingAs($this->admin);

        $file = new UploadedFile(Storage::disk('local')->path('testing/lien-files.zip'), 'lien-files.zip', null, null, null, true);

        $this->post('/web-api/lien/import-files', [
            'zip' => $file,
        ])
            ->assertSuccessful();

        $file_names = [
            '2049182_2.png',
            '2049182_4.png',
            '2051197_1.png',
            '2052034_1.png',
            '2052044_1.png',
        ];

        foreach ($file_names as $file_name) {
            $this->assertDatabaseHas('lien_imported_files', [
                'file_name' => $file_name
            ]);
            /** @var LienImportedFile $imported_file */
            $imported_file = LienImportedFile::where('file_name', $file_name)
                ->first();
            $this->assertDatabaseHas('media', [
                'model_type' => LienImportedFile::class,
                'model_id' => $imported_file->id,
                'collection_name' => 'default',
                'file_name' => $file_name,
            ]);
        }
    }
}
