<?php

namespace App\Imports;

use App\Models\Gereja;
use Maatwebsite\Excel\Concerns\ToModel;

class GerejaImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Gereja([
            //
        ]);
    }
}
