<?php
namespace Visualplus\SocialLogin;

use SocialiteProviders\Manager\SocialiteWasCalled;

class SocialLoginExtendSocialite {
	public function handle(SocialiteWasCalled $socialiteWasCalled) {
		$socialiteWasCalled->extendSocialite('naverid', \Visualplus\SocialLogin\NaveridProvider::class);
		$socialiteWasCalled->extendSocialite('kakaoid', \Visualplus\SocialLogin\KakaoidProvider::class);
	}
}