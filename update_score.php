<?php
session_start();
header('Content-Type: application/json');

// Ensure teams are initialized
if (!isset($_SESSION['teams']) || empty($_SESSION['teams'])) {
    echo json_encode(["success" => false, "error" => "Teams not initialized"]);
    exit();
}

$team = isset($_GET['team']) ? urldecode($_GET['team']) : '';
$points = isset($_GET['points']) ? (int)$_GET['points'] : 0;

if (isset($_SESSION['teams'][$team])) {
    // Prevent score from going below zero
    $newScore = $_SESSION['teams'][$team] + $points;
    if ($newScore < 0) {
        $newScore = 0;
    }

    $_SESSION['teams'][$team] = $newScore;

    echo json_encode([
        "success" => true,
        "team" => $team,
        "newScore" => $newScore
    ]);
} else {
    echo json_encode(["success" => false, "error" => "Team not found"]);
}
exit();
?>
