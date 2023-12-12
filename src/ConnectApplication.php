<?php

namespace App;

use AmoCRM\Client\AmoCRMApiClient;
use Exception;
use \Throwable;
use App\Client;
use App\Functions;


class ConnectApplication
{
    /**
     * Данные которые нам отправляет amo client_id и client_secret
     */
    private array $sent_data = [];
    private AmoCRMApiClient $client_api;
    /**
     * id_site
     */
    private $code;
    private $referer;

    public function main()
    {
        try {
            $this->code      = $_GET['code'] ?? null;
            $this->referer   = $_GET['referer'] ?? null;
            $this->sent_data = $this->getSentDataAmo();

            if (is_null($this->code) || is_null($this->referer)) $this->primary();
            elseif (!is_null($this->code) && !is_null($this->referer)) $this->secondary();
            else throw new Exception();

            exit('good');
        } catch (Throwable $e) {
            $e = json_encode(Functions::CollectDataException($e));
            file_put_contents("logs.json", $e . PHP_EOL, FILE_APPEND);
            var_dump($e);
            exit('bad');
        }
    }

    // ---------------------------------------------
    // ---------------------------------------------
    // ---------------------------------------------
    // private
    // ---------------------------------------------
    // ---------------------------------------------
    // ---------------------------------------------

    private function getSentDataAmo(): array
    {
        $input = $this->getInput();
        $data = [];
        $data['client_id'] = $input['client_id'] ?? null;
        $data['client_secret'] = $input['client_secret'] ?? null;
        return $data;
    }

    private function checkPrimary()
    {
        if (!isset($this->sent_data['client_id']) || !isset($this->sent_data['client_secret'])) throw new Exception('client_id and client_secret not found');
    }

    function getInput(): array
    {
        $inputs = [];
        $value = $this->getRaw();
        if ($value === '') return [];
        if (Functions::json()->isJSON($value)) {
            $value = json_decode($value, true);
            if (!is_array($value)) $value = [$value];
            return $value;
        }
        parse_str($value, $inputs);
        $post_keys = array_keys($_POST);
        if (sizeof($post_keys)) {
            foreach ($inputs as $key => &$val) {
                if (in_array($key, $post_keys)) unset($inputs[$key]);
            }
        }
        return $inputs;
    }

    function getRaw(): string
    {
        $value = @file_get_contents('php://input');
        if ($value === false) return '';
        return $value;
    }

    /**
     * для secondary нужно 5 параметров. Из бд: (client_id, client_secret) их мы должны получить в primary и entity_id, referer, code
     * @return newer
     */
    private function secondary()
    {
        
        $data_token_2 = file_get_contents("token_value2.json");

        $keys = json_decode($data_token_2, true);
        $client = new Client(
            $keys['client_id'],
            $keys['client_secret']
        );
        $this->client_api = $client->getClient();

        $token_value = $this->createTokenValue($this->client_api, $this->referer, $this->code);
        $token_value = json_encode($token_value);
        file_put_contents("token_value.json", $token_value);

        // создаем AccessToken, после создания клиентом можно пользоватся
        $client->createAccessToken($token_value);
        $this->redirect('/amocrm/first_step.php');
    }

    function redirect(string $path)
    {
        header('Location: ' . $path);
        exit();
    }

    /**
     * для primary нужно 3 параметра (entity_id, client_id, client_secret)
     */
    private function primary()
    {
        $this->checkPrimary();

        file_put_contents('token_value2.json', json_encode($this->sent_data));
    }

    private function createTokenValue(AmoCRMApiClient $client_api, string $referer, string $code): array
    {
        $client_api->setAccountBaseDomain($referer);
        // ---------------------------------------
        $accessToken = $client_api->getOAuthClient()->getAccessTokenByCode($code);
        if (!$accessToken->hasExpired()) {
            return [
                'accessToken' => $accessToken->getToken(),
                'refreshToken' => $accessToken->getRefreshToken(),
                'expires' => $accessToken->getExpires(),
                'baseDomain' => $client_api->getAccountBaseDomain(),
            ];
        } else throw new Exception('$accessToken->hasExpired');
    }
}
