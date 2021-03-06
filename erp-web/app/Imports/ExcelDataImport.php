<?php

namespace App\Imports;
use App\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
class ExcelDataImport implements ToModel
{
    /**
    * @param Collection $collection
    */
    public function model(array $row)
    {
        return new User([
           'name'     => $row[0],
           'email'    => $row[1],
           'password' => $row[2],
        ]);
    }
}
