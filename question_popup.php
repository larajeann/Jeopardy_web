<?php
session_start();
include 'game_data.php';

// Get category and points from query string
$cat = $_GET['cat'] ?? null;
$pts = intval($_GET['pts'] ?? 0);

// Validate inputs
if (!$cat || !in_array($cat, $categories) || !isset($questions[$cat][$pts])) {
    die("Invalid category or points.");
}

// Initialize used questions tracker in session
if (!isset($_SESSION['used_questions'])) {
    $_SESSION['used_questions'] = [];
}

// Initialize shuffled questions per category and points in session
if (!isset($_SESSION['shuffled_questions'])) {
    $_SESSION['shuffled_questions'] = [];
}

// Create a unique key for the category and points
$key = $cat . '-' . $pts;

// If this is the first time for this category and points, shuffle and store
if (!isset($_SESSION['shuffled_questions'][$key])) {
    $qList = $questions[$cat][$pts];
    shuffle($qList);
    $_SESSION['shuffled_questions'][$key] = $qList;
}

$used = $_SESSION['used_questions'];
$availableQuestions = $_SESSION['shuffled_questions'][$key];

// Filter out used questions for this key
$unusedQuestions = array_filter($availableQuestions, function($q) use ($used, $key) {
    return !in_array($key . '-' . md5($q), $used);
});

// Pick the first unused question or show message if none left
if (empty($unusedQuestions)) {
    $question = "No more questions available for " . htmlspecialchars($cat) . " at " . $pts . " points.";
} else {
    // Get the first unused question
    $question = reset($unusedQuestions);

    // Mark this question as used
    $_SESSION['used_questions'][] = $key . '-' . md5($question);
}
?>
    <script>
        function closeModal() {
            window.close();
        }
    </script>
</head>
<body>
    <div class="popup">
        <h2><?= htmlspecialchars($cat) ?> for <?= $pts ?> points</h2>
        <p><strong><?= htmlspecialchars($question) ?></strong></p>
        <button onclick="closeModal()">Close</button>
    </div>
</body>
</html>
