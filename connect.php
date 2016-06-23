<?php

require "vendor/autoload.php";

use phpunionplatform\Service\UnionplatformService;

$unionService = new UnionplatformService('https://yourdomain.com', 9200);

// connect, shake hands and say hello
echo $unionService->sayHello();
exit;
/*

$httpService->buildHttpQuery('d', array(
    'data' => utf8_encode($upc)
);

function poll($sessionId)
{
    $int = 1;
    return send('mode=c&sid=' . $sessionId . '&rid='.$int);
}




$msg = send(build($handshake, 'd'));
$sessionId = $msg->U->L->A[1];

poll($sessionId);

send(build($message));*/