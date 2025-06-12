<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class PasienExport implements FromCollection
{
    protected Collection $pasiens;

    public function __construct(Collection $pasiens)
    {
        $this->pasiens = $pasiens;
    }

    /**
     * Return a collection of data to be exported.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection(): Collection
    {
        return $this->pasiens;
    }
}
