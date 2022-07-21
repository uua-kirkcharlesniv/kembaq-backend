<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Merchant;
use App\Models\Message;
use App\Models\Reward;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        Category::create([
            'name'=> 'Food',
        ]);
        Category::create([
            'name'=> 'Salon',
        ]);
        Category::create([
            'name'=> 'Vets',
        ]);
        Category::create([
            'name'=> 'Retail',
        ]);
        Category::create([
            'name'=> 'Clinic',
        ]);

        // $kcU = User::create([
        //     'first_name' => 'Kirk Charles',
        //     'last_name' => 'Niverba',
        //     'email' => 'kirkniverba@icloud.com',
        //     'password' => Hash::make('password'),
        // ]);

        // User::create([
        //     'first_name' => 'Tony Tan',
        //     'last_name' => 'Caktiong',
        //     'email' => 'tonytan.caktiong@jollibee.com',
        //     'password' => Hash::make('password'),
        //     'is_merchant' => true,
        // ]);

        // $mc = Merchant::create([
        //     'mobile_number' => '09451494339',
        //     'logo' => 'http://assets.stickpng.com/images/62306f7fa39b9e9c515e5925.png',
        //     'background_color' => 'ED202A',
        //     'button_color' => 'FA8003',
        //     'business_address' => 'Bayan-Bayanan Ave, corner Molave St, Marikina, Metro Manila',
        //     'lat' => 14.656098220476428,
        //     'long' => 121.11899409976772,
        //     'business_name' => 'Jollibee - Marikina',
        //     'category' => 1,
        // ]);

        // Message::create([
        //     'merchant_id' => 1,
        //     'title' => 'Welcome to Jollibee',
        //     'link' => 'https://www.jollibeedelivery.com/',
        //     'message' => 'Lorem ipsum dolor sit amet, consectadipiscing elit...'
        // ]);

        // DB::table('merchant_user')->insert([
        //     ['merchant_id' => 1, 'user_id' => 2]
        // ]);

        // $jdc = User::create([
        //     'first_name' => 'Juan',
        //     'last_name' => 'dela Cruz',
        //     'email' => 'juandelacruz@moveup.app',
        //     'password' => Hash::make('password'),
        // ]);

        // Reward::create([
        //     'merchant_id' => $mc->id,
        //     'title' => 'Sample reward',
        //     'description' => 'Lorem ipsum generic description',
        //     'value' => 50,
        //     'photo' => 'https://cdn.shopify.com/s/files/1/0580/3245/5858/files/CJ_Fam_Deals.jpg?v=1643830712&width=1080',
        //     'valid_until' => Carbon::now()->utc()->addDays(30),
        // ]);

        // Reward::create([
        //     'merchant_id' => $mc->id,
        //     'title' => 'Sample reward 2',
        //     'description' => 'Lorem ipsum generic description',
        //     'value' => 5,
        //     'photo' => 'https://cdn.shopify.com/s/files/1/0580/3245/5858/files/CJ_Fam_Deals.jpg?v=1643830712&width=1080',
        //     'valid_until' => Carbon::now()->utc()->addDays(15),
        // ]);
    }
}
