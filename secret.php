<?php

require __DIR__ . '/vendor/autoload.php';

use App\ConnectApplication;
use App\Functions;

// $a = [
//     'POST' => $_POST,
//     'GET'  => $_GET,
//     'RAW'  => file_get_contents('php://input'),
// ];


// file_put_contents('secret.json', json_encode($a));


$connect = new ConnectApplication();
$connect->main();

try {
    $connect = new ConnectApplication();
$connect->main();
        } catch (Throwable $e) {
            // file_put_contents("../logs", $e->getMessage());
            file_put_contents("../logs.json", "here".PHP_EOL);
            file_put_contents("../logs.json", json_encode(Functions::CollectDataException($e)), FILE_APPEND);

            //  writeLog(self::class, CollectDataException($e));
            // на случай если произошла ошибка на этапе "secondary"
            //  if ($this->secondary) {
            //     $_SESSION['tmp.amocrm.error.message'] = MessageError::self()->getMessageByCode(999);
            //     redirect('/sites/' . $this->entity_id . '/settings/amo_crm');
            //  }

            //  exit('bad');
            var_dump($e);
            exit('bad');
        }