<?php

    namespace App\Providers;

    use App\Helpers\SalesForce\EBForrest;
    use GuzzleHttp\Client;
    use Illuminate\Support\ServiceProvider;
    use Omniphx\Forrest\Formatters\JSONFormatter;
    use Omniphx\Forrest\Providers\Laravel\LaravelCache;
    use Omniphx\Forrest\Providers\Laravel\LaravelEncryptor;
    use Omniphx\Forrest\Providers\Laravel\LaravelEvent;
    use Omniphx\Forrest\Providers\Laravel\LaravelInput;
    use Omniphx\Forrest\Providers\Laravel\LaravelRedirect;
    use Omniphx\Forrest\Providers\Laravel\LaravelSession;
    use Omniphx\Forrest\Providers\ObjectStorage;
    use Omniphx\Forrest\Repositories\InstanceURLRepository;
    use Omniphx\Forrest\Repositories\RefreshTokenRepository;
    use Omniphx\Forrest\Repositories\ResourceRepository;
    use Omniphx\Forrest\Repositories\StateRepository;
    use Omniphx\Forrest\Repositories\TokenRepository;
    use Omniphx\Forrest\Repositories\VersionRepository;

    class EBForrestServiceProvider extends ServiceProvider
    {
        /**
         * Returns the location of the package config file.
         *
         * @return string file location
         */
        protected function getConfigPath()
        {
            return config_path('forrest.php');
        }

        protected function getClient()
        {
            $client_config = app('config')->get('forrest.client', []);
            return new Client($client_config);
        }

        protected function getRedirect()
        {
            return new LaravelRedirect(app('redirect'));
        }

        protected function getStorage($storageType)
        {
            switch ($storageType) {
                case 'session':
                    return new LaravelSession(app('config'), app('request')->session());
                case 'cache':
                    return new LaravelCache(app('config'), app('cache')->store());
                case 'object':
                    return new ObjectStorage();
                default:
                    return new LaravelSession(app('config'), app('request')->session());
            }
        }

        /**
         * Register services.
         *
         * @return void
         */
        public function register()
        {
            $this->app->singleton('eb.forrest', function ($app) {

                // Config options
                $settings    = config('forrest');
                $storageType = config('forrest.storage.type');

                // Dependencies
                $httpClient = $this->getClient();
                $input      = new LaravelInput(app('request'));
                $event      = new LaravelEvent(app('events'));
                $encryptor  = new LaravelEncryptor(app('encrypter'));
                $redirect   = $this->getRedirect();
                $storage    = $this->getStorage($storageType);

                $refreshTokenRepo = new RefreshTokenRepository($encryptor, $storage);
                $tokenRepo        = new TokenRepository($encryptor, $storage);
                $resourceRepo     = new ResourceRepository($storage);
                $versionRepo      = new VersionRepository($storage);
                $instanceURLRepo  = new InstanceURLRepository($tokenRepo, $settings);
                $stateRepo        = new StateRepository($storage);

                $formatter = new JSONFormatter($tokenRepo, $settings);

                $forrest = new EBForrest(
                    $httpClient,
                    $encryptor,
                    $event,
                    $input,
                    $redirect,
                    $instanceURLRepo,
                    $refreshTokenRepo,
                    $resourceRepo,
                    $stateRepo,
                    $tokenRepo,
                    $versionRepo,
                    $formatter,
                    $settings);

                return $forrest;
            });
        }

        /**
         * Bootstrap services.
         *
         * @return void
         */
        public function boot()
        {
            //
        }
    }
