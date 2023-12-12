<?php

require __DIR__ . '/vendor/autoload.php';

use Inilim\JSON\JSON;
use App\Client;
// ----------------------------------------------------------------
use AmoCRM\Filters\LeadsFilter;
use AmoCRM\Helpers\EntityTypesInterface;
use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Collections\ContactsCollection;
use AmoCRM\Collections\CustomFieldsValuesCollection;
use AmoCRM\Collections\Leads\LeadsCollection;
use AmoCRM\Collections\NotesCollection;
use AmoCRM\Collections\TagsCollection;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Models\ContactModel;
use AmoCRM\Models\CustomFieldsValues\MultitextCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\MultitextCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueModels\MultitextCustomFieldValueModel;
use AmoCRM\Models\LeadModel;
use AmoCRM\Models\TagModel;
use AmoCRM\Models\Unsorted\FormsMetadata;

try {
function json (): JSON{
    static $obj = null;

    if(is_null($obj)) return $obj = new JSON();
    return $obj;
}

function initClient(
    $client_id,
    string $client_secret,
    string $token_value
): AmoCRMApiClient {
    $client = new Client(
        $client_id,
        $client_secret,
    );

    $client->createAccessToken($token_value);

    return $client->getClient();
}


// ------------------------------------------------------------------
// ------------------------------------------------------------------
// ------------------------------------------------------------------
// ------------------------------------------------------------------
// ------------------------------------------------------------------


$data_crm_token = [
    'token_value'   => file_get_contents("token_value.json"),
    'token_value_2' => json_decode(file_get_contents("token_value2.json"), TRUE),
];

$client = initClient(
    $data_crm_token['token_value_2']['client_id'],
    $data_crm_token['token_value_2']['client_secret'],
    $data_crm_token['token_value']
);

$filter = new LeadsFilter();
$filter->setIds([26489981]);

$leads = $client->leads()->get($filter);
print_r($leads->toArray());
} catch (Throwable $e) {
    var_dump($e->getMessage());
    
}