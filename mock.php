<?php

//Cross-origin permissions, access from remote
header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: ' . $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']);

//Starting session for cookie generation
session_start();

//Array with words for the game
$dict = array(
    "Auto",
    "Haus",
    "Kirche",
    "Vertrag",
    "Betriebssystem",
    "Lampenschirm",
    "Monitor",
    "Praktikum",
    "Wolke",
    "Führungskraft",
    "Schwimmbad",
    "Datenbank",
    "Präsentation",
    "Ernährung",
    "Ketogen",
    "Telefonkonferenz",
    "Mediengestalter",
    "Kantine",
    "Zimmermann",
    "Armbanduhr",
    "Software",
    "Hardware",
    "Entwicklungsumgebung",
    "Fenster",
    "Terminal",
    "Performanz",
    "Animation",
    "Ladebalken",
    "Klasse",
    "Meisterprüfung"
);

//Requestobject from client
$request = json_decode(file_get_contents('php://input'), true);

//Method for starting the game
if ($_GET['api'] === 'startgame') {
    $_SESSION['selectedWord'] = str_split(strtoupper($dict[rand(0, 29)]));
    $_SESSION['foundWord'] = array_fill(0, count($_SESSION['selectedWord']), null);
    $_SESSION['status'] = array(
        'word' => $_SESSION['foundWord'],
        'status' => 'PLAYING',
        'failedAttempts' => 0
    );
}

//Method for playing the game
if ($_GET['api'] === 'game') {
    $foundChar = false;
    for ($i = 0; $i < count($_SESSION['selectedWord']); $i++) {
        if ($_SESSION['selectedWord'][$i] === strtoupper($request)) {
            $_SESSION['status']['word'][$i] = $_SESSION['selectedWord'][$i];
            $foundChar = true;
        }
    }
    if (!$foundChar) {
        $_SESSION['status']['failedAttempts']++;
    }
    if ($_SESSION['status']['failedAttempts'] === 10) {
        $_SESSION['status']['word'] = $_SESSION['selectedWord'];
        $_SESSION['status']['status'] = 'LOSE';
    } else if (implode($_SESSION['status']['word']) === implode($_SESSION['selectedWord'])) {
        $_SESSION['status']['status'] = 'WIN';
    }
}

//Return of the JSON for playing
if (isset($_SESSION['status'])) {
    echo json_encode($_SESSION['status']);
    exit;
}
?>