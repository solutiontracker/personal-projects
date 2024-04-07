<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        //Default Routes
        Route::group([
            'middleware' => 'web',
            'namespace' => $this->namespace,
        ], function ($router) {
            require base_path('routes/web.php');
            require base_path('routes/web/salesforce/web.php');
            require base_path('routes/api/Auth/Zapier/api.php');
        });

        // Mobile (Mobile/Desktop) Routes
        Route::group([
            'prefix' => 'mobile',
            'namespace' => $this->namespace,
        ], function ($router) {
            require base_path('routes/web/Mobile/web.php');
        });
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        // App v1 API

        // PNP Routes
        Route::group([
            'middleware' => 'api',
            'namespace' => $this->namespace,
        ], function ($router) {
            //Organizer Auth
            require base_path('routes/api/Auth/Organizer/api.php');
            //PlugnPlay Routes
            require base_path('routes/api/Wizard/api.php');
        });

        //Zapier Routes
        Route::group([
            'middleware' => 'api',
            'namespace' => $this->namespace,
        ], function ($router) {
            require base_path('routes/api/Zapier/api.php');
        });

        // Zoom Routes
        Route::group([
            'middleware' => 'api',
            'namespace' => $this->namespace,
        ], function ($router) {
            require base_path('routes/api/Zoom/api.php');
        });

        // Mobile (Mobile/Desktop) Routes
        Route::group([
            'middleware' => 'api',
            'prefix' => 'mobile',
            'namespace' => $this->namespace,
        ], function ($router) {
            //Attendee Auth
            require base_path('routes/api/Auth/Attendee/api.php');
            //Logged
            require base_path('routes/api/Mobile/api.php');
        });

        // Api Routes
        Route::group([
            'middleware' => 'api',
            'prefix' => 'api',
            'namespace' => $this->namespace,
        ], function ($router) {
            require base_path('routes/api/Api/api.php');
        });

        // Registration flow Routes
        Route::group([
            'middleware' => 'api',
            'prefix' => 'registration',
            'namespace' => $this->namespace,
        ], function ($router) {
            require base_path('routes/api/RegistrationFlow/api.php');
        });

        // Common Routes
        Route::group([
            'middleware' => 'api',
            'prefix' => 'api',
            'namespace' => $this->namespace,
        ], function ($router) {
            require base_path('routes/api/api.php');
        });

        //Registration site Module
        Route::group([
            'middleware' => 'api',
            'namespace' => $this->namespace,
            'prefix' => 'api/v2',
        ], function ($router) {
            require base_path('routes/api/RegistrationSite/api.php');
        });

        //Super site Module
        Route::group([
            'middleware' => 'api',
            'namespace' => $this->namespace,
            'prefix' => 'api/v2',
        ], function ($router) {
            require base_path('routes/api/Super/api.php');
        });

        //Lead
        Route::group([
            'middleware' => 'api',
            'namespace' => $this->namespace,
            'prefix' => 'api/v2',
        ], function ($router) {
            require base_path('routes/api/Lead/api.php');

        });

        // Lead Auth Routes
        Route::group([
            'middleware' => 'api',
            'prefix' => 'api/v2',
            'namespace' => $this->namespace,
        ], function ($router) {
            require base_path('routes/api/Auth/Lead/api.php');
        });
        
        // Web App Routes
        Route::group([
            'middleware' => 'api',
            'prefix' => 'api/v2',
            'namespace' => $this->namespace,
        ], function ($router) {
            require base_path('routes/api/WebApp/api.php');
        });

        // Thirdparty Routes
        Route::group([
            'middleware' => 'api',
            'prefix' => 'api/v2',
            'namespace' => $this->namespace,
        ], function ($router) {
            require base_path('routes/api/Thirdparty/api.php');
        });

        // Organizer Routes (Event center)
        Route::group([
            'middleware' => 'api',
            'prefix' => 'organizer',
            'namespace' => $this->namespace,
        ], function ($router) {
            require base_path('routes/api/Organizer/api.php');
        });

        // Sales routes
        Route::group([
            'middleware' => 'api',
            'prefix' => 'api/v1',
            'namespace' => $this->namespace,
        ], function ($router) {
            require base_path('routes/api/Sale/api.php');
        }); 
        
        // Reporting Agent Auth Routes (Event center)
        Route::group([
            'middleware' => 'api',
            'prefix' => 'api/v2',
            'namespace' => $this->namespace,
        ], function ($router) {
            require base_path('routes/api/Auth/Reporting/api.php');
        });   

        // Reporting Agent Routes (Event center)
        Route::group([
            'middleware' => 'api',
            'prefix' => 'api/v2',
            'namespace' => $this->namespace,
        ], function ($router) {
            require base_path('routes/api/Reporting/api.php');
        });  
         
    }
}
