<?php


    namespace App\Facades;


    use Illuminate\Support\Facades\Facade;

    class EBForrest extends Facade
    {
        protected static function getFacadeAccessor()
        {
            return 'eb.forrest';
        }
    }