<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

// Facebook API
Loader::library("facebook/facebook", "nuevebit");

/**
 * Description of social
 *
 * @author emerino
 */
class NuevebitFacebook {

    private $config;
    private $facebook;
    private $authenticated = false;

    public function __construct($appId, $secret) {
        $this->config = array(
            "appId" => $appId,
            "secret" => $secret
        );

        $this->facebook = new Facebook($this->config);
        //        $user = $facebook->getUser();
    }

    /**
     * Returns $count latests posts by $username
     * 
     * @param type $username
     * @param type $count
     */
    public function findPosts($username = "me", $count = 20) {
        $posts = $this->facebook->api("/$username/posts?limit=$count");
        return $posts["data"];
    }

}

Loader::library("tmhOAuth/tmhOAuth", "nuevebit");

class NuevebitTwitter {

    private $connection;

    public function __construct($consumerKey, $consumerSecret, $userToken, $userSecret) {
        $this->connection = new tmhOAuth(array(
                    'consumer_key' => $consumerKey,
                    'consumer_secret' => $consumerSecret,
                    'user_token' => $userToken,
                    'user_secret' => $userSecret,
                ));
    }

    public function findTweets($screenName, $count = 20) {
        $code = $this->connection->request('GET', $this->connection->url('1.1/statuses/user_timeline'), array('screen_name' => $screenName, "count" => $count));

        if ($code == 200) {
            $tweets = json_decode($this->connection->response["response"]);
        } else {
            $tweets = null;
        }

        return $tweets;
    }

}

Loader::library("google-api-client/Google_Client", "nuevebit");
Loader::library("google-api-client/contrib/Google_PlusService", "nuevebit");

class NuevebitGooglePlus {

    private $client;
    private $service;

    public function __construct($developerKey, $clientId=NULL, $clientSecret=NULL, $redirectUri=NULL) {
        $this->client = new Google_Client();
//        $this->client->setApplicationName($appName);
// Visit https://code.google.com/apis/console to generate your
// oauth2_client_id, oauth2_client_secret, and to register your oauth2_redirect_uri.
//        $client->setClientId('.apps.googleusercontent.com');
//        $client->setClientSecret('');
//        $client->setRedirectUri('');
        $this->client->setDeveloperKey($developerKey);
        $this->service = new Google_PlusService($this->client);
    }

    // we only need developerKey for this to work
    public function findActivities($userId, $count=20, $scope = "public") {
        $activities = $this->service->activities->listActivities($userId, $scope, array("maxResults" => $count));

        return $activities;
    }

}

?>
