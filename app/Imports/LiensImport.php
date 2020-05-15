<?php

namespace App\Imports;

use App\Lien;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Row;

class LiensImport implements WithStartRow, WithHeadingRow, OnEachRow, WithChunkReading, WithBatchInserts
{
    /**
     * @var string
     */
    private $state;
    /**
     * @var string
     */
    private $county;

    /**
     * LiensImport constructor.
     * @param string $state
     * @param string $county
     */
    public function __construct($state, $county)
    {
        $this->state = $state;
        $this->county = $county;
    }

    public function onRow(Row $row)
    {
        $row = $row->toArray();
        $lien = Lien::create([
            'state' => $this->state,
            'county' => $this->county,
            'legal' => $row['legal'],
            'lienor' => $row['lienor'],
            'owner' => $row['owner'],
        ]);
        $files = explode(',', $row['files']);
        foreach ($files as $file_name) {
            if (trim($file_name) === '') {
                continue;
            }
            $lien->files()
                ->create([
                    'file_name' => trim($file_name)
                ]);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function startRow(): int
    {
        return 3;
    }

    public function headingRow(): int
    {
        return 2;
    }

    /**
     * @return int
     */
    public function chunkSize(): int
    {
        return 250;
    }

    /**
     * @return int
     */
    public function batchSize(): int
    {
        return 250;
    }
}
