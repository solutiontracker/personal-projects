<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \Fruitcake\Cors\HandleCors::class,
        \App\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        \App\Http\Middleware\TrustProxies::class,
        \App\Http\Middleware\NullToBlank::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            'throttle:600000,1',
            'bindings',
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'valid.event' => \App\Http\Middleware\ValidateEvent::class,
        'valid.app.event' => \App\Http\Middleware\ValidateAppEvent::class,
        'valid.app.event.id' => \App\Http\Middleware\ValidateEventById::class,
        'valid.langauge' => \App\Http\Middleware\ValidateLanguage::class,
        'valid.interface.language' => \App\Http\Middleware\ValidateInterfaceLanguage::class,
        'auth.organizer' => \App\Http\Middleware\Authenticate::class,
        'auth.organizer.token' => \App\Http\Middleware\AuthOrganizerToken::class,
        'request.log' => \App\Http\Middleware\RequestLog::class,
        'validate.attendee' => \App\Http\Middleware\ValidateAttendee::class,
        'virtual.app.json' => \App\Http\Middleware\VirtualAppJsonModified::class,
        'setEventTimezone' => \App\Http\Middleware\SetEventTimeZone::class,
        'api.auth' => \App\Http\Middleware\ApiAuth::class,
        'valid.registration.flow.event' => \App\Http\Middleware\ValidateRegistrationFlowEvent::class,
        'valid.api.event' => \App\Http\Middleware\ValidateApiEvent::class,
        'api.request.response' => \App\Http\Middleware\ApiRequestResponse::class,
        'valid.event.url' => \App\Http\Middleware\ValidateEventUrl::class,
        'valid-ip-address' => \App\Http\Middleware\BlockIpAddressMiddleware::class,
        'valid.lead-event' => \App\Http\Middleware\ValidateLeadEvent::class,
        'gzip' => \App\Http\Middleware\Gzip::class,
        'route-based-labels' => \App\Http\Middleware\RouteBasedLabels::class,
        'registration.flow.json.modified' => \App\Http\Middleware\RegistrationFlowJsonModified::class,
        'registration.validate.order' => \App\Http\Middleware\Orders\ValidateOrder::class,
        'horizon' => \App\Http\Middleware\Horizon::class,
        'auth.agent' => \App\Http\Middleware\Sales\AuthenticateAgent::class,
        'json.response' => \App\Http\Middleware\ForceJsonResponse::class,
    ];
}
