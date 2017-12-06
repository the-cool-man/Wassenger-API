<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
   
   //
// Simple PHP script that sends text messages to different numbers
// by using the Wassenger API: https://docs.wassenger.com
//
// NOTE: you must have an account created in Wassenger.com and have at least one device created and online.
// You can sign up here: https://console.wassenger.com/register
// You can create a device here: https://console.wassenger.com/devices/create
// 
// Usage from the command-line:
//
//  php -f send-messages.php
//
//

// Wassenger API token
// Take it from: https://console.wassenger.com/apikeys


// List of messages to send
// TODO: customize it as needed, or load it from a CSV file
$messages = array(
    array(
        'phone' => '+917878787878', // <-- CHANGE ME
        'message' => 'Hello! This is a test message by Whatsapp API, sent via PHP code Ignore it.'
    ),
    //array(
//        'phone' => '+919429545260', // <-- CHANGE ME
//        'message' => 'Yo! This is a another test message by Whatsapp API, sent via PHP code Ignore it. :)'
//    ),
);

$media = array(
        'file' => "5c722ea17bdd0a00162ebaee",
		'filename' => "logo-screen.png",
        'message' => 'Media File'
);

$location = array(
        'address' => "Siddhivinayak Tower, SG Highway Ahmedabad",
);

function send_message ($phone, $message, $media, $location) {
	
	$token = '74dcecd1ac7ea88c21228ca880eef63badab16535d45f123f6c9668b9a04962415bdefcb2d4323bf7bf0e';
	
    echo "Sending message to number: $phone\n";

    // Send the message with the image
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.wassenger.com/v1/messages",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode(array(
            'phone' => $phone,
            'message' => $message,
			//'media' => $media,
			'location' => $location
        )),
        CURLOPT_HTTPHEADER => array(
            "content-type: application/json",
            "token: $token"
        ),
    ));

    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($curl);
    $response_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $err = curl_error($curl);

    curl_close($curl);
    if ($err) {
        throw new Exception("HTTP request error: $err");
    }

    if ($response_status >= 400) {
        throw new Exception("Invalid server response status: $response_status");
    }

    // Device JSON response and return message ID
    $message = json_decode($response, true);
    $message_id = $message['id'];

    return $message_id;
}

// Store message ID for further lookup/reference
$sent_messages = array();

// Iterate messages and send them
foreach ($messages as $message) {
    $message_id = send_message($message['phone'], $message['message'],  $media, $location);
    echo "Message queued with ID: $message_id <br/><br/>";
}

echo "All messages were sent :)<br/><br/>";
