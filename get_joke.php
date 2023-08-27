<?php
$apiUrl = "https://moppenbot.nl/api/random/";

$response = file_get_contents($apiUrl);
$jokeData = json_decode($response, true);

if ($jokeData['success']) {
    $joke = $jokeData['joke']['joke']; // Hier selecteren we het specifieke "joke" veld
    $isNSFW = $jokeData['joke']['nsfw'];

    $jokeResult = [
        'joke' => $joke,
        'nsfw' => $isNSFW
    ];

    echo json_encode($jokeResult);
} else {
    echo json_encode(['joke' => 'Kon geen mop vinden.', 'nsfw' => false]);
}
?>
