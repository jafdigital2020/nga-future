<?php

namespace App\Providers;

use App\Models\SettingsTheme;
use App\Models\SettingsCompany;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Http\ViewComposers\UserComposer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $companySettings = SettingsCompany::first();
        $themeSettings = SettingsTheme::first();

        view()->share([
            'companySettings' => $companySettings,
            'themeSettings' => $themeSettings,
        ]);
    }

}
