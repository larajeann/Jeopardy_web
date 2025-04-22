
<?php
session_start();
header('Content-Type: application/json');

$team = isset($_GET['team']) ? urldecode($_GET['team']) : '';
$points = isset($_GET['points']) ? (int)$_GET['points'] : 0;

if (isset($_SESSION['teams'][$team])) {
    $_SESSION['teams'][$team] += $points;
    echo json_encode([
        "success" => true,
        "team" => $team,
        "newScore" => $_SESSION['teams'][$team]
    ]);
} else {
    echo json_encode(["success" => false]);
}
exit();