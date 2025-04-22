<?php
session_start();
include 'game_data.php';
header('Content-Type: application/json');

// FOR TESTING - Mark all questions as used
$_SESSION['used_questions'] = [];
foreach ($categories as $category) {
    for ($i = 1; $i <= 5; $i++) {
        $points = $i * 100;
        $_SESSION['used_questions'][] = "$category-$points";
    }
}
// END TESTING
$totalQuestions = count($categories) * 5;
$usedQuestions = isset($_SESSION['used_questions']) ? count($_SESSION['used_questions']) : 0;

echo json_encode([
    'allUsed' => $usedQuestions >= $totalQuestions,
    'message' => 'All questions have been marked as used for testing' //
]);
exit();
