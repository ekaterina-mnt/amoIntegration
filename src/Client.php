<?php

namespace App;

use AmoCRM\Client\AmoCRMApiClient;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Token\AccessTokenInterface;
use Exception;

/**
 * инициализация клиент api amocrm
 */
class Client
{
    private AmoCRMApiClient $client;
    /**
     * id_site
     */
    // private int $entity_id;

    public function __construct(
        $client_id,
        string $client_secret
    ) {
        // $client_id и $client_secret берется из базы "token_value_2"
        $this->client = new AmoCRMApiClient(
            $client_id,
            $client_secret,
            'https://ekaterina-mntn.ru/amocrm/secret.php'
        );
    }

    public function getClient(): AmoCRMApiClient
    {
        return $this->client;
    }

    /**
     * @param string $token_value JSON
     */
    public function createAccessToken(string $token_value): void
    {
        // if (!json()->isJSON($token_value)) throw new Exception('token_value не JSON');

        $accessToken = $this->makeAccessToken($token_value);

        if (is_null($accessToken)) throw new Exception('Не удалось создать AccessToken');

        $this->checkAndSetToken($accessToken);
    }

    // ---------------------------------------------
    // ---------------------------------------------
    // ---------------------------------------------
    // private
    // ---------------------------------------------
    // ---------------------------------------------
    // ---------------------------------------------

    private function makeAccessToken(string $token_value)
    {

        $accessToken = json_decode($token_value, true);

        if (
            isset($accessToken)
            && isset($accessToken['accessToken'])
            && isset($accessToken['refreshToken'])
            && isset($accessToken['expires'])
            && isset($accessToken['baseDomain'])
        ) {
            return new AccessToken([
                'access_token' => $accessToken['accessToken'],
                'refresh_token' => $accessToken['refreshToken'],
                'expires' => $accessToken['expires'],
                'baseDomain' => $accessToken['baseDomain'],
            ]);
        }
        return null;
    }

    /**
     * проверка токена на актуальность и внедрение в клиент
     */
    private function checkAndSetToken(AccessToken $accessToken): void
    {
        $this->getClient()->setAccessToken($accessToken)
            ->setAccountBaseDomain($accessToken->getValues()['baseDomain'])
            ->onAccessTokenRefresh(
                function (AccessTokenInterface $accessToken, string $baseDomain) {
                    $this->refrashToken(
                        [
                            'accessToken' => $accessToken->getToken(),
                            'refreshToken' => $accessToken->getRefreshToken(),
                            'expires' => $accessToken->getExpires(),
                            'baseDomain' => $baseDomain,
                        ]
                    );
                }
            );
    }

    private function refrashToken(array $accessToken): void
    {
        if (
            isset($accessToken)
            && isset($accessToken['accessToken'])
            && isset($accessToken['refreshToken'])
            && isset($accessToken['expires'])
            && isset($accessToken['baseDomain'])
        ) {
            $data = [
                'accessToken' => $accessToken['accessToken'],
                'expires' => $accessToken['expires'],
                'refreshToken' => $accessToken['refreshToken'],
                'baseDomain' => $accessToken['baseDomain'],
            ];
            // MCRMToken::updateToken($token_id, json_encode($data));
            file_put_contents('token_value.json', json_encode($data));
            file_put_contents('check.json', time(), FILE_APPEND);
                        
        }
    }
}
