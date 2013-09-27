<?php 

Loader::library("facebook/facebook", "nuevebit");

$config = array(
    "appId" => "361620847262492",
    "secret" => "d116e7a0ffe256ed20331c96e05970af",
    "cookie" => true
);

$facebook = new Facebook($config);

try {
    $facebook->destroySession();
} catch (FacebookRestClientException $e) {
    Log::addEntry($e->getMessage());
}

//header('Location: http://localhost:8080/~emerino/pruebas/facebook');
?>