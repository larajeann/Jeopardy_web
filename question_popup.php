<?php
session_start();
include 'game_data.php';

$cat = $_GET['cat'];
$pts = $_GET['pts'];
$key = "$cat-$pts";

if (!isset($_SESSION['used_questions'])) {
    $_SESSION['used_questions'] = [];
}

$_SESSION['used_questions'][] = $key;
$question = $questions[$key] ?? "No question found.";
?>
<!DOCTYPE html>
<html>
<head><title>Question</title></head>
<body>

    <h2><?= $cat ?> for <?= $pts ?> points</h2>
    <strong><p><?= $question ?></p></strong>
    <button onclick="closeModal()">Close</button>
</body>
</html>

