<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Merchant;
use App\Models\Message;
use App\Models\User;
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

        $kcU = User::create([
            'first_name' => 'Kirk Charles',
            'last_name' => 'Niverba',
            'email' => 'kirkniverba@icloud.com',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'first_name' => 'Tony Tan',
            'last_name' => 'Caktiong',
            'email' => 'tonytan.caktiong@jollibee.com',
            'password' => Hash::make('password'),
        ]);

        $mc = Merchant::create([
            'mobile_number' => '09451494339',
            'logo' => 'http://assets.stickpng.com/images/62306f7fa39b9e9c515e5925.png',
            'background_color' => 'ED202A',
            'button_color' => 'FA8003',
            'business_address' => 'Bayan-Bayanan Ave, corner Molave St, Marikina, Metro Manila',
            'lat' => 14.656098220476428,
            'long' => 121.11899409976772,
            'business_name' => 'Jollibee - Marikina',
            'category' => 1,
        ]);

        Message::create([
            'merchant_id' => 1,
            'title' => 'Welcome to Jollibee',
            'link' => 'https://www.jollibeedelivery.com/',
            'message' => 'Lorem ipsum dolor sit amet, consectadipiscing elit...'
        ]);

        DB::table('merchant_user')->insert([
            ['merchant_id' => 1, 'user_id' => 2]
        ]);

        $mc->deposit(100000);

        $kcU->createWallet([
            'name' => $kcU->last_name . ' ' . $kcU->first_name . ' - ' . $mc->business_name,
            'slug' => $kcU->id.'-'.$mc->id,
        ]);

        $jdc = User::create([
            'first_name' => 'Juan',
            'last_name' => 'dela Cruz',
            'email' => 'juandelacruz@moveup.app',
            'password' => Hash::make('password'),
        ]);

        $jdc->createWallet([
            'name' => $jdc->last_name . ' ' . $jdc->first_name . ' - ' . $mc->business_name,
            'slug' => $jdc->id.'-'.$mc->id,
        ]);
    }
}
