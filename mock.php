<?php
header('Access-Control-Allow-Origin: '.$_SERVER['HTTP_ORIGIN']);
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: '.$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']);

session_start();

$word = array('H', 'A', 'N', 'G', 'M', 'A', 'N');
$found = array(null, null, null, null, null, null, null);


$request = json_decode(file_get_contents('php://input'), true);

if ($_GET['api'] === 'startgame') {
    $_SESSION['status'] = array(
        'word' => $found,
        'status' => 'PLAYING',
        'failedAttempts' => 0
    );
}

if ($_GET['api'] === 'game') {
    $foundChar = false;
    for ($i = 0; $i < count($word); $i++) {
        if ($word[$i] === strtoupper($request)) {
            $_SESSION['status']['word'][$i] = $word[$i];
            $foundChar = true;
        }
    }
    if (!$foundChar) {
        $_SESSION['status']['failedAttempts']++;
    }
    if ($_SESSION['status']['failedAttempts'] === 10) {
        $_SESSION['status']['word'] = $word;
        $_SESSION['status']['status'] = 'LOSE';
    } else if (implode($_SESSION['status']['word']) === implode($word)) {
        $_SESSION['status']['status'] = 'WIN';
    }
}

if (isset($_SESSION['status'])) {
    echo json_encode($_SESSION['status']);
    exit;
}
?><pre>
No game status found. Use as follows:

    /mock.php?api=startgame   // startgame api
    /mock.php?api=game        // game api


