<?php
session_start();
include 'game_data.php';

$cat = $_GET['cat'] ?? null;
$pts = intval($_GET['pts'] ?? 0);

if (!$cat || !in_array($cat, $categories) || !isset($questions[$cat][$pts])) {
    die("Invalid category or points.");
}

if (!isset($_SESSION['used_questions'])) {
    $_SESSION['used_questions'] = [];
}

if (!isset($_SESSION['shuffled_questions'])) {
    $_SESSION['shuffled_questions'] = [];
}

$key = $cat . '-' . $pts;

if (!isset($_SESSION['shuffled_questions'][$key])) {
    $qList = $questions[$cat][$pts];
    shuffle($qList);
    $_SESSION['shuffled_questions'][$key] = $qList;
}

$used = $_SESSION['used_questions'];
$availableQuestions = $_SESSION['shuffled_questions'][$key];

$unusedQuestions = array_filter($availableQuestions, function($q) use ($used, $key) {
    return !in_array($key . '-' . md5($q['q']), $used);
});

if (empty($unusedQuestions)) {
    $questionText = "No more questions available for " . htmlspecialchars($cat) . " at " . $pts . " points.";
    $answerText = "";
} else {
    $questionData = reset($unusedQuestions);
    $questionText = $questionData['q'];
    $answerText = $questionData['a'];
    $_SESSION['used_questions'][] = $key . '-' . md5($questionText);
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
    <h2><?= htmlspecialchars($cat) ?> </h2> (<?= $pts ?> points)
    <h3> <p><strong><?= htmlspecialchars($questionText) ?></strong></p></h3>
   
    <div id="answer" style="display:none; margin-top: 10px; font-weight: bold;">
        Answer: <?= htmlspecialchars($answerText) ?>
    </div>
    <button onclick="document.getElementById('answer').style.display='block';" style="margin-top: 8px;">Reveal Answer</button> <br>
    <button onclick="closeModal()" style="font-size: 1em; padding: 4px 8px; margin-top: 35px; background-color: #e74c3c; color: white; border: none; border-radius: 6px;">Close</button>
</div>

    
</body>
</html>
