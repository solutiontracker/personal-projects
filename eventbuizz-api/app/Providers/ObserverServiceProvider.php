<?php

namespace App\Providers;

use App\Models\HelpDesk;
use App\Models\QA;
use App\Observers\HelpDeskObserver;
use App\Observers\QAObserver;
use Illuminate\Support\ServiceProvider;

class ObserverServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        QA::observe(QAObserver::class);
        HelpDesk::observe(HelpDeskObserver::class);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
