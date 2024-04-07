<?php

    namespace App\Http\Controllers\Auth\Salesforce;

    use App\Models\SalesforceToken;
    use Illuminate\Http\Request;
    use App\Http\Controllers\Controller;
    use Omniphx\Forrest\Exceptions\InvalidLoginCreditialsException;

    class LoginController extends Controller
    {
        public function login()
        {
            return \EBForrest::authenticate();
        }

        public function callback(Request $request)
        {
            try {
                \EBForrest::callback();
            } catch (InvalidLoginCreditialsException $e) {
                dump($e->getMessage());
                return redirect(env('CDN_PROTOCOL') . '://' . env('CDN_URL') . '/_admin/integrations?module=integrations');
            }

            // Also fetch token expiry in 'exp' key value pair of storage.
//            \EBForrest::getTokenExpiry();

//            $records = \EBForrest::query("SELECT Id FROM Account WHERE Name='bUTT KARAHI' LIMIT 1");
//            $records = \EBForrest::sobjects('Account/0012y00000A2rTgAAJ', ['method' => 'DELETE']);
//            $records = \EBForrest::sobjects('Account/0012y00000A2rTgAAJ',
//                [
//                    'method' => 'PATCH',
//                    'body'   => [
//                        'Name' => 'EB212'
//                    ]
//                ]);

            \EBForrest::saveUserToken(auth()->user()->id);

            return redirect(env('CDN_PROTOCOL') . '://' . env('CDN_URL') . '/_admin/integrations?module=integrations');
        }

    }
