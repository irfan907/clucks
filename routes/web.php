<?php

use App\Livewire\Categories\Create as CategoryCreate;
use App\Livewire\Categories\Edit as CategoryEdit;
use App\Livewire\Categories\Index as CategoryIndex;
use App\Livewire\Deliveries\AllDeliveries;
use App\Livewire\Deliveries\Create as DeliveryCreate;
use App\Livewire\Deliveries\Edit as DeliveryEdit;
use App\Livewire\Deliveries\MyDeliveries;
use App\Livewire\Deliveries\Show as DeliveryShow;
use App\Livewire\Products\Create as ProductCreate;
use App\Livewire\Products\Edit as ProductEdit;
use App\Livewire\Products\Index as ProductIndex;
use App\Livewire\Receivings\Create as ReceivingCreate;
use App\Livewire\Receivings\ReceivingsList;
use App\Livewire\Receivings\SelectDelivery;
use App\Livewire\Receivings\Show as ReceivingShow;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use App\Livewire\Stock\Index as StockIndex;
use App\Livewire\Users\Create as UserCreate;
use App\Livewire\Users\Edit as UserEdit;
use App\Livewire\Users\Index as UserIndex;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::get('/', function () {
    // return view('welcome');
    return redirect()->route('dashboard');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// PIN Login (public routes)
Route::get('login/pin', [App\Http\Controllers\Auth\PinLoginController::class, 'show'])->name('login.pin');
Route::post('login/pin', [App\Http\Controllers\Auth\PinLoginController::class, 'store'])->name('login.pin.store');

Route::middleware(['auth'])->group(function () {
    // Settings
    Route::redirect('settings', 'settings/profile');
    Route::get('settings/profile', Profile::class)->name('profile.edit');
    Route::get('settings/password', Password::class)->name('user-password.edit');
    Route::get('settings/appearance', Appearance::class)->name('appearance.edit');
    Route::get('settings/two-factor', TwoFactor::class)
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');

    // Categories
    Route::get('categories', CategoryIndex::class)->name('categories.index');
    Route::get('categories/create', CategoryCreate::class)->name('categories.create');
    Route::get('categories/{category}/edit', CategoryEdit::class)->name('categories.edit');

    // Products
    Route::get('products', ProductIndex::class)->name('products.index');
    Route::get('products/create', ProductCreate::class)->name('products.create');
    Route::get('products/{product}/edit', ProductEdit::class)->name('products.edit');

    // Stock
    Route::get('stock', StockIndex::class)->name('stock.index');

    // Deliveries
    Route::get('deliveries/my', MyDeliveries::class)->name('deliveries.my');
    Route::get('deliveries/all', AllDeliveries::class)->name('deliveries.all');
    Route::get('deliveries/create', DeliveryCreate::class)->name('deliveries.create');
    Route::get('deliveries/{delivery}', DeliveryShow::class)->name('deliveries.show');
    Route::get('deliveries/{delivery}/edit', DeliveryEdit::class)->name('deliveries.edit');

    // Receivings
    Route::get('receivings/select-delivery', SelectDelivery::class)->name('receivings.select-delivery');
    Route::get('receivings/create/{delivery}', ReceivingCreate::class)->name('receivings.create');
    Route::get('receivings/{receiving}', ReceivingShow::class)->name('receivings.show');
    Route::get('deliveries/{delivery}/receivings', ReceivingsList::class)->name('receivings.list');

    // Users
    Route::get('users', UserIndex::class)->name('users.index');
    Route::get('users/create', UserCreate::class)->name('users.create');
    Route::get('users/{user}/edit', UserEdit::class)->name('users.edit');
});
