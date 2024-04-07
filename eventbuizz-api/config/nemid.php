<?php
return [
	/*
	|--------------------------------------------------------------------------
	| Test
	|--------------------------------------------------------------------------
	| Default setting for test or production mode
	*/
	'test' => false,
	/*
	|--------------------------------------------------------------------------
	| Login settings for Nem-Id
	|--------------------------------------------------------------------------
	|
	*/
	'login' => [
		'settings'     => [
			'baseUrl'             => 'https://applet.danid.dk/', // Official url for production
			'uiMode'              => 'lmt', // lmt / std
			'origin'              => false, // Can be domain where iframe is hosted
			'showCancelBtn'       => true, // Show the cancel button in the iframe
			'privateKeyPassword'  => 'Buizz2014', // Password for the private key
			'privateKeyLocation'  => storage_path('app/certificates/production_privateKey.pem'), // Location for private key
			'certificateLocation' => storage_path('app/certificates/production_certificate.pem'), // Location for public certificate
		],
		'testSettings' => [
			'baseUrl'             => 'https://appletk.danid.dk/', // Official url for testing environment
			'uiMode'              => 'lmt', // lmt / std
			'origin'              => false, // Can be domain where iframe is hosted
			'showCancelBtn'       => true, // Show the cancel button in the iframe
			'privateKeyPassword'  => 'Buizz2014', // Password for the private key
			'privateKeyLocation'  => storage_path('app/certificates/dev_privateKey.pem'), // Location for private key
			'certificateLocation' => storage_path('app/certificates/dev_certificate.pem'), // Location for public certificate
		],

		// Check for certificate matching after login
		'certificationDigests' => [
            '0e2fd1fda36a4bf3995e28619704d60e3382c91e44a2b458ab891316380b1d50',
            '92d8092ee77bc9208f0897dc05271894e63ef27933ae537fb983eef0eae3eec8'
        ],
		'checkOcsp' => false, // The certificate can be validated through a external request
		'proxy'     => false, // Since you only have 10 ip whitelisted, it can be smart to proxy the ip calls

	],
	/*
	|--------------------------------------------------------------------------
	| Webservice settings Nem-Id
	|--------------------------------------------------------------------------
	|
	*/
	'webservice' => [
		'settings' => [
			'server'            => 'https://pidws.certifikat.dk/pid_serviceprovider_server/pidxml/', // Official url for production
			'certificateAndKey' => storage_path('app/certificates/production_certificateAndPrivateKey.pem'), // Location for certificateAndPrivateKey
			'password'          => 'Buizz2014', // Password for certificateAndPrivateKey
			'serviceId'         => '1078214103', // ServiceId also called SPID
			'proxy'             => false, // Since you only have 10 ip whitelisted, it can be smart to proxy the ip calls
		],
		'testSettings' => [
			'server'            => 'https://pidws.pp.certifikat.dk/pid_serviceprovider_server/pidxml/', // Official url for testing environment
			'certificateAndKey' => storage_path('app/certificates/dev_certificateAndPrivateKey.pem'), // Location for certificateAndPrivateKey
			'password'          => 'Buizz2014', // Password for certificateAndPrivateKey
			'serviceId'         => '336493200', // ServiceId also called SPID
			'proxy'             => false, // Since you only have 10 ip whitelisted, it can be smart to proxy the ip calls
		],
	],
];
