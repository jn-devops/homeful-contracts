<?php

namespace Database\Seeders;

use App\Models\Mapping;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MappingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'code' => 'first_name',
                'path' => 'contact.first_name',
                'source' => 'array',
                'title' => 'First Name',
                'type' => 'string',
                'default' => 'Christian',
                'category' => 'buyer',
                'transformer' => 'UpperCaseTransformer, ReverseStringTransformer'
            ],
            [
                'code' => 'last_name',
                'path' => 'contact.last_name',
                'source' => 'array',
                'title' => 'Last Name',
                'type' => 'string',
                'default' => 'Ramos',
                'category' => 'buyer',
                'transformer' => 'UpperCaseTransformer, ReverseStringTransformer'
            ]
        ];

        foreach ($data as $d) {
            Mapping::create($d);
        }
    }
}
