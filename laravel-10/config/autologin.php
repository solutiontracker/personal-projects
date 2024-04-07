<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Token lifetime
    |--------------------------------------------------------------------------
    |
    | Here you may specifiy the number of minutes you wish the autologin token
    | to remain active.
    |
    */

    'lifetime' => 1440,

    /*
    |--------------------------------------------------------------------------
    | Token usage
    |--------------------------------------------------------------------------
    |
    | Indicate whether each time the token is used while it is valid, the count
    | on the column should be incremented. Be sure to disable this if you use
    | the next option, removing used tokens.
    |
    */

    'remove_expired' => true

];
