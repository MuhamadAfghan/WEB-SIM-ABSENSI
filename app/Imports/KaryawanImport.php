<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class KaryawanImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return User::updateOrCreate(
            ['nip' => $row['nip']],
            [
                'name' => $row['nama'],
                'password' => Hash::make($row['nip']),
                'email' => $row['email'],
                'telepon' => $row['no_telepon'],
                'divisi' => $row['nama'],
                'mapel' => $row['nama'],
            ]
        );
    }
}
