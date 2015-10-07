# Social Login Provider

### 1. COMPOSER

```
composer require visualplus/social-login
```

### 2. SERVICE PROVIDER

Remove Laravel\Socialite\SocialiteServiceProvider from your providers[] array in config\app.php if you have added it already.
Add SocialiteProviders\Manager\ServiceProvider to your providers[] array in config\app.php.

```
'providers' => [
    // a whole bunch of providers
    // remove 'Laravel\Socialite\SocialiteServiceProvider',
    'SocialiteProviders\Manager\ServiceProvider', // add
];
```

### 3. ADD THE EVENT AND LISTENERS

Add SocialiteProviders\Manager\SocialiteWasCalled event to your listen[] array in <app_name>/Providers/EventServiceProvider.
Add listener to the SocialiteProviders\Manager\SocialiteWasCalled[] that you just created.

```
protected $listen = [
	'SocialiteProviders\Manager\SocialiteWasCalled' => [
		'Visualplus\SocialLogin\SocialLoginExtendSocialite',
	],
];
```

### 4. ADD SOCIALITE FACADE

```
'aliases' => [
	...
	'Socialite'	=> Laravel\Socialite\Facades\Socialite::class,
	...
];
```

### 5. ADD KEY AND REDIRECT URL TO config/services.php

```
return [
	'naverid' => [
		'client_id'		=> 'your client id key',
		'client_secret'	=> 'your client secret',
		'redirect'		=> 'your redirect url',
	],
    
	'kakaoid' => [
		'client_id'		=> 'your client id key',
		'client_secret'	=> '', // kakaoid doesn't have client secret. just leave a blank.
		'redirect'		=> 'your redirect url', 
	],
];
```

to get Naver ID keys. see this page https://nid.naver.com/devcenter/main.nhn
to get Kakao ID keys. see this page https://developers.kakao.com/docs/restapi

### 6. USAGE

For naverid.

```
Route::get('naverid', function() {
	return Socialite::with('naverid')->redirect();
});

Route::get('your redirect url', function() {
	dd(Socialite::with('naverid')->user());
});
```

For kakaoid.

```
Route::get('kakaoid', function() {
	return Socialite::with('kakaoid')->redirect();
});

Route::get('your redirect url', function() {
	dd(Socialite::with('kakaoid')->user());
});
```

if authentication was failed, error will be occured at getting user code ( Socialite::with('')->user() ).
you could know throught return page's parameters. ( e.g. http://your redirect url?error=some reason&extra1=extra1... )
