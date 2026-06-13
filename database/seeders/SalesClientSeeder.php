<?php

namespace Database\Seeders;

use App\Models\SalesClient;
use Illuminate\Database\Seeder;

class SalesClientSeeder extends Seeder
{
    public function run(): void
    {
        SalesClient::storeClient();
    }
}
