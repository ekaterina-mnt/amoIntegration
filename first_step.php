<?php

function createUrl(): string
{
    $domain = "ekaterina-mntn.ru/amocrm/first_step.php";
    $url = 'https://www.amocrm.ru/oauth/?';
    $url .= '&mode=post_message';
    $url .= '&origin=' . urlencode('https://' . $domain);
    $url .= '&name=GSAnalytics';
    $url .= '&description=' . urlencode('Analytics for Vitagor');
    $url .= '&redirect_uri=' . urlencode('https://ekaterina-mntn.ru/amocrm/secret.php');
    $url .= '&secrets_uri=' . urlencode('https://ekaterina-mntn.ru/amocrm/secret.php');
    $url .= '&logo=';
    $url .= '&scopes[]=crm';
    $url .= '&scopes[]=notifications';
    return $url;
}
    
$url_amo = createUrl();
?>

<a class="" href="<?=$url_amo?>">Подключить</a>