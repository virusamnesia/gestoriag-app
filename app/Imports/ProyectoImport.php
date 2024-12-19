<?php

namespace App\Imports;

use App\Models\Proyecto;
use Maatwebsite\Excel\Concerns\ToModel;

class ProyectoImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Proyecto([
            //
        ]);
    }
}
