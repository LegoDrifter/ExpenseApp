<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Categories;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $user = new User();
        $user->username = 'user';
        $user->email = 'admin@admin.com';
        $user->password = Hash::make('password');
        $user->role = 'admin';
        $user->budget = 0;
        $user->balance = 0;
        $user->save();

        $category1 = new Category();
        $category1->title = 'Wants';
        $category1->status = 1;
        $category1->percentage = 20;
        $category1->parent_id = 0;
        $category1->user_id = 0;
        $category1->save();

        $category2 = new Category();
        $category2->title = 'Needs';
        $category2->status = 1;
        $category2->percentage = 50;
        $category2->parent_id = 0;
        $category1->user_id = 0;
        $category2->save();

        $category3 = new Category();
        $category3->title = 'Investment and Savings';
        $category3->status = 1;
        $category3->percentage = 30;
        $category3->parent_id = 0;
        $category1->user_id = 0;
        $category3->save();

        $category4 = new Category();
        $category4->title = 'Income';
        $category4->status = 1;
        $category4->percentage = 0;
        $category4->parent_id = 0;
        $category1->user_id = 0;
        $category4->save();


    }
}
