<?php

require "vendor/autoload.php";

use phpunionplatform\service\Unionplatform;
use phpunionplatform\library\client\HttpsClient;

$host = 'ipaddr';
$port = 80;
$domain = 'yourdomain.com';

$room = 'awesomeRoomName';

// create new http interface
$httpClient = new HttpsClient($host, $port, $domain);

// create
$unionplatformService = new Unionplatform(
    $httpClient
);

// connect, shake hands and say hello
$unionplatformService->sayHello();

// create a room
$unionplatformService->createRoom($room);

// join it
$unionplatformService->joinRoom($room);

// send a room message, if the userid is set it will send only to that user
// the last two parameters (userId, params[]) are optional
$unionplatformService->sendMessage($room, 'NOTIFY', '', array('hello my friend!'));
