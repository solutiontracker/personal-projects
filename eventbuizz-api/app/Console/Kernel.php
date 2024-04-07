<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\Economic\Customer::class,
        Commands\Economic\Product::class,
        Commands\Economic\ProductGroup::class,
        Commands\Economic\Invoice::class,
        Commands\Eventcenter\EventAttendeeCount::class,
        Commands\Eventcenter\EventLabelCreate::class,
        // Commands\GoogleAnalytics\AddAnalyticsAccounts::class,
        // Commands\GoogleAnalytics\GenerateAnalyticsProperties::class,
        Commands\GoogleAnalyticsGA4\AddGA4AnalyticsAccounts::class,
        Commands\GoogleAnalyticsGA4\GenerateGA4AnalyticsProperties::class,
        Commands\Reporting\ReportingRevenue::class,
        Commands\Eventcenter\RegistrationFlow\DeleteDraftOrder::class,
        Commands\Eventcenter\RegistrationFlow\SendEanInvoice::class,
        Commands\Eventcenter\RegistrationFlow\SendInvoice::class,
        Commands\EventSiteTheme\EventThemeModuleVariations::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //E-economics cron jobs
        $schedule->command('fetch:customers')
            ->daily()
            ->onOneServer();

        $schedule->command('fetch:product_groups')
            ->daily()
            ->onOneServer();

        $schedule->command('fetch:products')
            ->daily()
            ->onOneServer();

        $schedule->command('fetch:invoices')
            ->daily()
            ->onOneServer();

        $schedule->command('fetch:departments')
            ->daily()
            ->onOneServer();

        //End e-economics cron jobs

        //Event center cron jobs
        $schedule->command('Update:EventAttendeesCount')
            ->dailyAt('02:00')
            ->onOneServer();


        $schedule->command('Update:EventDetectDuplicateOrder')
            ->when(function () {
                return \App::environment('production');
            })
            ->hourly()
            ->onOneServer();

        $schedule->command('reporting:update-event-revenue 133')
            ->hourly()
            ->onOneServer();

        /* $schedule->command('Create:Eventlabels')
            ->daily()
            ->onOneServer(); */


        $schedule->command('RegistrationFlow:DeleteDraftOrder')
            ->everyThirtyMinutes()
            ->onOneServer();
        
        $schedule->command('RegistrationFlow:SendEanInvoice')
            ->daily()
            ->onOneServer();

        $schedule->command('RegistrationFlow:SendInvoice')
            ->everyTwoMinutes()
            ->withoutOverlapping()
            ->onOneServer();

        //End event center cron jobs

        $schedule->command('salesforce:syncAttendee')
            ->daily()
            ->onOneServer();

        // fetch Analytics from agora console and sync them in database
        $schedule->command('send:ios_notifications')
            ->everyTwoMinutes()
            ->withoutOverlapping()
            ->onOneServer();

        $schedule->command('dynamics:syncAttendee')
            ->daily()
            ->onOneServer();

        // fetch Analytics from agora console and sync them in database
        $schedule->command('fetch:agora_analytics')
            ->daily()
            ->onOneServer();

        // Fetch Analytics Accounts and Add to Db
        $schedule->command('add:gA4AnalyticsAccounts')
            ->when(function () {
                return \App::environment('production');
            })
            ->dailyAt('02:00')
            ->onOneServer();
        
        // Fetch Analytics Accounts and Add to Db
        $schedule->command('generate:gA4AnalyticsProperties')
            ->when(function () {
                return \App::environment('production');
            })
            ->dailyAt('02:30')
            ->onOneServer();
        
        // $schedule->command('update:eventModuleVariations')
        // ->when(function () {
        //     return \App::environment('production');
        // })
        // ->everyTenMinutes()
        // ->onOneServer();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
