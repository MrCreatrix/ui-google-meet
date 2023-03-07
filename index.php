<?php

require_once __DIR__ . '/vendor/autoload.php';

$client = new Google_Client();
$client->setClientId('339482413692-qvbbesu9ijfp8c1vt53072rfp185fskp.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/php/ui-google-meet/');
$client->addScope('https://www.googleapis.com/auth/calendar.events');

// If an access token is available, set it on the client.
if (isset($_SESSION['access_token'])) {
  $client->setAccessToken($_SESSION['access_token']);
}

// If no access token is available, redirect the user to the OAuth 2.0 consent screen.
if (!$client->getAccessToken() && !isset($_GET['code'])) {
  $authUrl = $client->createAuthUrl();
  header('Location: ' . $authUrl);
  exit;
}

// If the user has granted access, exchange the authorization code for an access token.
if (isset($_GET['code'])) {
    $accessToken = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    $client->setAccessToken($accessToken);
  
    // Save the access token to the session.
    $_SESSION['access_token'] = $accessToken;
  }


  $service = new Google_Service_Calendar($client);

    // datetime formate   
  $date_string = '2023-03-08T09:00:00-07:00';
  $datetime = new DateTime($date_string);
  $formatted_date = $datetime->format('Y-m-d\TH:i:sO');

  //timezone

  $event = new Google_Service_Calendar_Event(array(
    'summary' => 'Test Meeting',
    'location' => 'Online',
    'description' => 'This is a test meeting',
    'start' => array(
      'dateTime' => $formatted_date,
      'timeZone' => 'Asia/Kolkata',
    ),
    'end' => array(
      'dateTime' => $formatted_date,
      'timeZone' => 'Asia/Kolkata',
    ),
    'conferenceData' => array(
      'createRequest' => array(
        'requestId' => 'random-request-id',
        'conferenceSolutionKey' => array(
          'type' => 'hangoutsMeet',
        ),
      ),
    ),
  ));
  
  $calendarId = 'primary';

  $event = $service->events->insert($calendarId, $event, array(
    'conferenceDataVersion' => 1,
  ));
  
//   printf('Event created: %s\n', $event->htmlLink); 
  $meeting_link = $event->conferenceData->entryPoints[0]->uri;
  ?>
<a href="<?php echo $meeting_link; ?>">
    Create Google Meet
</a>
