<?php

namespace Visualplus\SocialLogin;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;

class KakaoidProvider extends AbstractProvider implements ProviderInterface
{
    /**
     * XML -> array 형식 변환.
     * 
     * @param string
     *
     * @return array
     */
    private function parseXML($xml)
    {
        $simpleXml = simplexml_load_string($xml, null, LIBXML_NOCDATA);
        $json = json_encode($simpleXml);

        return json_decode($json, true);
    }

    /**
     * Get the authentication URL for the provider.
     *
     * @param string $state
     *
     * @return string
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase('https://kauth.kakao.com/oauth/authorize', $state);
    }

    /**
     * Get the token URL for the provider.
     *
     * @return string
     */
    protected function getTokenUrl()
    {
        return 'https://kauth.kakao.com/oauth/token';
    }

    /**
     * Get the access token for the given code.
     *
     * @param string $code
     *
     * @return string
     */
    public function getAccessToken($code)
    {
        $response = $this->getHttpClient()->request('POST', $this->getTokenUrl(), [
            'form_params'    => $this->getTokenFields($code),
        ]);

        return $this->parseAccessToken($response->getBody());
    }

    /**
     * Get the POST fields for the token request.
     *
     * @param string $code
     *
     * @return array
     */
    protected function getTokenFields($code)
    {
        return [
            'grant_type'       => 'authorization_code',
            'client_id'        => $this->clientId,
            'redirect_uri'     => $this->redirectUrl,
            'code'             => $code,
        ];
    }

    /**
     * Get the raw user for the given access token.
     *
     * @param string $token
     *
     * @return array
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->request('POST', 'https://kapi.kakao.com/v1/user/me', [
            'headers' => [
                'Authorization' => 'Bearer '.$token,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * Map the raw user array to a Socialite User instance.
     *
     * @param array $user
     *
     * @return \Laravel\Socialite\User
     */
    protected function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user)->map([
            'id'         => $user['id'],
            'properties' => [
                'nickname'             => $user['properties']['nickname'],
                'thumbnail_image'      => $user['properties']['thumbnail_image'],
                'profile_image'        => $user['properties']['profile_image'],
            ],
        ]);
    }

    /**
     * User logout.
     * 
     * @param string $user
     */
    public function logOut($user)
    {
        $response = $this->getHttpClient()->request('POST', 'https://kapi.kakao.com/v1/user/logout', [
            'headers' => [
                'Authorization' => 'Bearer '.$user->token,
            ],
        ]);
    }
}
