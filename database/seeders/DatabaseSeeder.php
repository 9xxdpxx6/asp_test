<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        /* dev */
//        $this->call([
////            PostSeeder::class,
//            CategorySeeder::class,
//            StatusSeeder::class,
//            UserSeeder::class,
//            CallbackRequestSeeder::class,
//        ]);

        /* prod */
        $this->call([
            StatusSeeder::class,
            UserSeeder::class,
        ]);
    }

}
