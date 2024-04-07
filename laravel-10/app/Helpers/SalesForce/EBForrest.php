<?php


    namespace App\Helpers\SalesForce;


    use App\Models\SalesforceToken;
    use GuzzleHttp\ClientInterface;
    use Omniphx\Forrest\Authentications\WebServer;
    use Omniphx\Forrest\Interfaces\EncryptorInterface;
    use Omniphx\Forrest\Interfaces\EventInterface;
    use Omniphx\Forrest\Interfaces\FormatterInterface;
    use Omniphx\Forrest\Interfaces\InputInterface;
    use Omniphx\Forrest\Interfaces\RedirectInterface;
    use Omniphx\Forrest\Interfaces\RepositoryInterface;
    use Omniphx\Forrest\Interfaces\ResourceRepositoryInterface;

    class EBForrest extends WebServer
    {

        public function __construct(
            ClientInterface $httpClient,
            EncryptorInterface $encryptor,
            EventInterface $event,
            InputInterface $input,
            RedirectInterface $redirect,
            RepositoryInterface $instanceURLRepo,
            RepositoryInterface $refreshTokenRepo,
            ResourceRepositoryInterface $resourceRepo,
            RepositoryInterface $stateRepo,
            RepositoryInterface $tokenRepo,
            RepositoryInterface $versionRepo,
            FormatterInterface $formatter,
            $settings)
        {
            parent::__construct(
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
                $settings
            );
        }
        public function setUpInstance($token){
            $this->tokenRepo->put($token);
            $this->instanceURLRepo->put($token['instance_url']);
            if (isset($token['refresh_token'])) {
                $this->refreshTokenRepo->put($token['refresh_token']);
            }

            $this->storeVersion();
            $this->storeResources();

        }

        public function getToken(){
            return  $this->tokenRepo->get();
        }

        public function getRefreshToken(){
            return  $this->refreshTokenRepo->get();
        }


        public function setRefreshToken($refresh_token){
            $this->refreshTokenRepo->put($refresh_token);
        }

        public function setToken($token){
            $this->tokenRepo->put($token);
        }

        public function getTokenExpiry(){
            $url   = $this->instanceURLRepo->get();
            $token = $this->tokenRepo->get();

            $response = $this->httpClient->request('post', $url . '/services/oauth2/introspect', [
                'form_params' => [
                    'token'           => $token['access_token'],
                    'client_id'       => $this->credentials['consumerKey'],
                    'client_secret'   => $this->credentials['consumerSecret'],
                    'token_type_hint' => 'access_token',
                ]
            ]);

            $expiry = json_decode($response->getBody()->getContents(), true);

            $token_repo        = $this->tokenRepo->get();
            $token_repo['exp'] = $expiry['exp'];

            $this->tokenRepo->put($token_repo);
        }

        public function saveUserToken($user_id)
        {
            $this->getTokenExpiry();
            $credentials = \EBForrest::getToken();
            $data        = SalesforceToken::updateOrCreate(
                [
                    'user_id' => $user_id,
                ],
                [
                    'access_token'      => $credentials['access_token'],
                    'refresh_token'     => $credentials['refresh_token'],
                    'instance_base_url' => $credentials['instance_url'],
                    'expires'           => date('Y-m-d H:i:s', $credentials['exp']),
                    'issued_at'         => date('Y-m-d H:i:s', $credentials['issued_at'] / 1000),
                    'token_body'        => json_encode($credentials),
                ]);
            return $data;
        }
    }