<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['teams'])) {
    echo json_encode(['success' => false, 'error' => 'No teams found']);
    exit();
}

echo json_encode([
    'success' => true,
    'teams' => $_SESSION['teams']
]);
exit();
?>