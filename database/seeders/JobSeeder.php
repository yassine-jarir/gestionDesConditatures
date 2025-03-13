<?php

namespace Database\Seeders;

use App\Models\Job;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JobSeeder extends Seeder
{
    protected $model = Job::class; 
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Job::factory(12)->create();
    }
}
