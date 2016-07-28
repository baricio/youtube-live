<?php

namespace app\library\youtube;

/**
 * Created by PhpStorm.
 * User: Fabricio
 * Date: 27/07/2016
 * Time: 10:12
 */
class Auth
{
    private static $OAUTH2_CLIENT_ID = '1050408444978-e2l9pn6lrmk1lh6p6rbs9k22bnh00qrl.apps.googleusercontent.com';
    private static $OAUTH2_CLIENT_SECRET = 'ab17N7n9bW1Bt2HqmQbYwFcF';
    private $redirect;
    private $client;
    private $youtube;
    private $tokenSessionKey;

    public function __construct(){
        session_start();
        $this->client = new \Google_Client();
        $this->client->setClientId(Auth::$OAUTH2_CLIENT_ID);
        $this->client->setClientSecret(Auth::$OAUTH2_CLIENT_SECRET);
        $this->client->setScopes('https://www.googleapis.com/auth/youtube');
        $this->redirect = filter_var('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'],
            FILTER_SANITIZE_URL);
        $this->client->setRedirectUri($this->redirect);
    }

    public function authenticate(){
        // Define an object that will be used to make all API requests.
        $this->youtube = new \Google_Service_YouTube($this->client);

        // Check if an auth token exists for the required scopes
        $this->tokenSessionKey = 'token-' . $this->client->prepareScopes();
        if (isset($_GET['code'])) {
            if (strval($_SESSION['state']) !== strval($_GET['state'])) {
                die('The session state did not match.');
            }

            $this->client->authenticate($_GET['code']);
            $_SESSION[$this->tokenSessionKey] = $this->client->getAccessToken();
            header('Location: ' . $this->redirect);
        }

        if (isset($_SESSION[$this->tokenSessionKey])) {
            $this->client->setAccessToken($_SESSION[$this->tokenSessionKey]);
        }

        return $this;
    }

    public function getAccessToken(){
        $token = $this->client->getAccessToken();
        $_SESSION[$this->tokenSessionKey] = $token;
        return $token;
    }
    
    public function createAuthUrl(){
    	$state = mt_rand();
        $this->client->setState($state);
        $_SESSION['state'] = $state;

        return $this->client->createAuthUrl();
    }

    public function getYoutube(){
        return $this->youtube;
    }

    public function getClient(){
        return $this->client;
    }

}