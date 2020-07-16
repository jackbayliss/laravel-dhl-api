<?php

    namespace jackbayliss\DHLApi;

    use Illuminate\Support\ServiceProvider;
    use jackbayliss\DHLApi\Calls\{GetQuote,GetRouting,GetTracking};

    class DHLAPIProvider extends ServiceProvider
    {

        /**
         * Bootstrap the application services.
         *
         * @return Void
         */

        private $config = __DIR__.'/Config/Config.php';

        public function boot(): Void
        {

            $this->publishes([
                $this->config => config_path('Config.php'),
            ], 'dhlapiconfig');
    
        }

        /**
         * Register the application services.
         *
         * @return void
         */
        public function register(): Void
        {


        $this->mergeConfigFrom($this->config, 'dhlapiconfig');

        $this->app->singleton(GetQuote::class, function ($app) {
                return new GetQuote();
        });

        }

        public function provides(): Array
        {
            return [GetQuote::class,GetTracking::class,GetRouting::class];
        }
    }
