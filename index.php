<?php

/**
 * Library Requirements
 *
 * 1. Install composer (https://getcomposer.org)
 * 2. On the command line, change to this directory (api-samples/php)
 * 3. Require the google/apiclient library
 *    $ composer require google/apiclient:~2.0
 */
if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
  throw new \Exception('please run "composer require google/apiclient:~2.0" in "' . __DIR__ .'"');
}

require_once __DIR__ . '/vendor/autoload.php';

use app\library\youtube\Auth;
use app\library\youtube\Channel;
use app\library\youtube\Live;

$auth = new Auth();
$embed_code = '';

// Check to ensure that the access token was successfully acquired.
if ($auth->authenticate()->getAccessToken()) {
  try {

    $youtube = $auth->getYoutube();

    $channel = new Channel($youtube);
    $chanel_id = $channel->findUserChanel()->getChannelId();

    $video = new Live($youtube);
    $embed_code = $video->findLiveVideo($chanel_id)->getEmbedVideo();

    if(empty($embed_code)){
      $embed_code = "<h1>nenhum video online</h1>";
    }



  } catch (Google_Service_Exception $e) {
    session_unset();
    $embed_code = sprintf('<p>A service error occurred: <code>%s</code></p>',
        htmlspecialchars($e->getMessage()));
  } catch (Google_Exception $e) {
    session_unset();
    $embed_code = sprintf('<p>An client error occurred: <code>%s</code></p>',
        htmlspecialchars($e->getMessage()));
  }

} else {
  $authUrl = $auth->createAuthUrl();
  $embed_code = <<<END
  <h3>Authorization Required</h3>
  <p>You need to <a href="$authUrl">authorize access</a> before proceeding.<p>
END;
}
?>

<!doctype html>
<html style="height: 100%; margin: 0; padding: 0;">
<head>
  <title>Bound Live Broadcast</title>
</head>
<body style="height: 100%; margin: 0; padding: 0;">
<?=$embed_code?>
</body>
</html>

