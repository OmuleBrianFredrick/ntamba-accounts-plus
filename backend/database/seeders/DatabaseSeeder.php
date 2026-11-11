<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Product;
use App\Models\PriceHistory;
use App\Models\Route;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * M1 Foundation demo data (plan: Setup before the first trip, steps 1-8).
 * Company: Ntamba, UGX, Africa/Kampala. Users: admin, accountant, production
 * manager, and agents Gloria/Winnie/Faridah with codes GLO/WIN/FAR.
 * Routes: Kampala, Eastern Route, Western Route. Products: sample from the
 * Products sheet categories (coffee, dried fruit, honey) until the workbook
 * is supplied.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::firstOrCreate(['name' => 'Ntamba'], [
            'currency'       => 'UGX',
            'timezone'       => 'Africa/Kampala',
            'invoice_prefix' => 'NTA',
            'settings'       => ['theme' => [
                'green'         => '#1B7A3D', // brand green
                'light_green'   => '#A8D5BA',
                'golden_yellow' => '#F0B429',
                'coffee_brown'  => '#6F4E37',
                'logo'          => '/placeholders/logo.svg', // placeholder until supplied
            ]],
        ]);

        // Staff (email + password sign-in)
        User::withoutGlobalScopes()->firstOrCreate(
            ['email' => 'admin@ntamba.test'],
            ['company_id' => $company->id, 'name' => 'Admin', 'role' => 'admin',
             'password' => Hash::make('ChangeMe!123')]
        );
        User::withoutGlobalScopes()->firstOrCreate(
            ['email' => 'accountant@ntamba.test'],
            ['company_id' => $company->id, 'name' => 'Accountant', 'role' => 'accountant',
             'password' => Hash::make('ChangeMe!123')]
        );
        User::withoutGlobalScopes()->firstOrCreate(
            ['email' => 'production@ntamba.test'],
            ['company_id' => $company->id, 'name' => 'Production Manager', 'role' => 'production_manager',
             'password' => Hash::make('ChangeMe!123')]
        );

        // Agents (phone + PIN sign-in). Demo PIN 1234; real ones set by admin.
        foreach ([['Gloria','0700000001','GLO'],['Winnie','0700000002','WIN'],['Faridah','0700000003','FAR']] as [$name,$phone,$code]) {
            User::withoutGlobalScopes()->firstOrCreate(
                ['phone' => $phone],
                ['company_id' => $company->id, 'name' => $name, 'role' => 'agent',
                 'agent_code' => $code, 'pin_hash' => Hash::make('1234')]
            );
        }

        // Routes (setup step 4)
        foreach ([['Kampala','kampala'],['Eastern Route','upcountry'],['Western Route','upcountry']] as [$n,$z]) {
            Route::firstOrCreate(['company_id' => $company->id, 'name' => $n], ['zone_type' => $z]);
        }

        // Sample products (setup step 3 — full 22-item list comes with the workbook)
        foreach ([['Coffee','500g','coffee',8000],['Honey','500g','honey',12000],['Dried Fruit','250g','dried fruit',6000]] as [$name,$size,$category,$price]) {
            $product = Product::firstOrCreate(
                ['company_id' => $company->id, 'name' => $name, 'size' => $size],
                ['category' => $category, 'unit_price' => $price, 'tracks_expiry' => false]
            );
            if (!PriceHistory::where('product_id', $product->id)->exists()) {
                PriceHistory::create([
                    'company_id' => $company->id, 'product_id' => $product->id,
                    'price' => $price, 'effective_from' => now()->toDateString(),
                ]);
            }
        }
    }
}
