<?php
session_start();
include 'game_data.php';

if (!isset($_SESSION['teams'])) {
    header("Location: team_setup.php");
    exit();
}

if (!isset($_SESSION['used_questions'])) {
    $_SESSION['used_questions'] = [];
}
 
?>

<!DOCTYPE html>
<html>
<head>
    <title>Jeopardy Game</title>
    <link rel="stylesheet" href="assets/style.css">
    <script>
        
        function showQuestion(category, points) {
    const key = `${category}-${points}`;

    fetch(`question_popup.php?cat=${category}&pts=${points}`)
        .then(response => response.text())
        .then(html => {
            // Inject only the question content and the close button
            document.getElementById('modal-content').innerHTML = `
                ${html}
                <button class="close-modal" onclick="closeModal()">&times;</button>
            `;
            document.getElementById('question-modal').style.display = 'flex';

            // Style question buttons if needed
            const questionButtons = document.querySelectorAll('#modal-content button:not(.close-modal)');
            questionButtons.forEach(btn => {
                if (!btn.classList.contains('modal-btn')) {
                    btn.className = 'modal-btn modal-btn-primary';
                }
            });

            // Disable the board button
            const btn = document.querySelector(`button[data-key='${key}']`);
            if (btn) {
                btn.disabled = true;
                btn.classList.add('disabled');
                btn.innerHTML = 'X';
            }

            // Check if all questions are used
            checkAllQuestionsUsed();
        });
}


    function closeModal() {
        document.getElementById('question-modal').style.display = 'none';
    }


function updateScore(team, points, currentScore = null) {
    // Prevent score from going below zero for deductions
    if (points < 0 && currentScore !== null && currentScore + points < 0) {
        alert("Score cannot go below zero!");
        return false;
    }

    fetch(`update_score.php?team=${encodeURIComponent(team)}&points=${points}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Find all team score elements
                const teamElements = document.querySelectorAll('.team-score');
                
                teamElements.forEach(element => {
                    // Get the team name from the first div
                    const teamNameElement = element.querySelector('div:first-child');
                    const teamName = teamNameElement.textContent.trim();
                    
                    // Check for exact match
                    if (teamName === team) {
                        // Update the score display
                        const scoreElement = element.querySelector('div:nth-child(2)');
                        const newScore = data.newScore;
                        scoreElement.textContent = `Score: ${newScore}`;
                        
                        // Add visual feedback
                        element.classList.remove('score-up', 'score-down');
                        void element.offsetWidth; // Trigger reflow
                        element.classList.add(points > 0 ? 'score-up' : 'score-down');
                        
                        // Disable -100 button if score would go below zero
                        const minusBtn = element.querySelector('a[onclick*="-100"]');
                        if (minusBtn) {
                            minusBtn.style.opacity = newScore < 100 ? '0.5' : '1';
                            minusBtn.style.pointerEvents = newScore < 100 ? 'none' : 'auto';
                        }
                    }
                });
            } else {
                console.error('Failed to update score');
            }
        })
        .catch(error => console.error('Error:', error));
}

function checkAllQuestionsUsed() {
    fetch('check_questions.php')
        .then(response => response.json())
        .then(data => {
            if (data.allUsed) {
                const container = document.getElementById('finish-button-container');
                // Only add button if it doesn't already exist
                if (!container.querySelector('.finish-btn')) {
                    container.innerHTML = `
                        <button onclick="showWinner()" class="finish-btn">Finish Game 🎉</button>
                    `;
                }
            }
        })
        .catch(error => console.error('Error:', error));
}
function showWinner() {
    fetch('get_scores.php')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const teams = data.teams;
                const scores = Object.values(teams);
                const maxScore = Math.max(...scores);
                const winners = Object.keys(teams).filter(team => teams[team] === maxScore);

                let message = winners.length > 1
                    ? `🏆 It's a tie between: ${winners.join(', ')} with ${maxScore} points!`
                    : `🏆 Good Fun ${winners[0]}!<br> You won with ${maxScore} points!`;

                    document.getElementById('modal-content').innerHTML = `
                    <h2>Congratulations!</h2>
                    <p style="font-size: 1.4em; font-weight: bold;">${message}</p>
                    <div class="modal-buttons">
                        <button class="modal-btn modal-btn-primary" onclick="location.href='reset.php'">New Game</button>
                        <button class="modal-btn modal-btn-secondary" onclick="closeModal()">Close</button>
                    </div>
                    <button class="close-modal" onclick="closeModal()">&times;</button>
                `;
                document.getElementById('question-modal').style.display = 'flex';
            } else {
                console.error('Failed to get scores');
            }
        })
        .catch(error => console.error('Error:', error));
}
    </script>
</head>
<body>
    <h2>Let's Play!</h2>

    <div class="main-content">
    <?php
    $totalQuestions = count($categories) * 5;
    $usedQuestions = count($_SESSION['used_questions']);
    $allUsed = $usedQuestions >= $totalQuestions;
    ?>
    <table>
        <tr>
            <?php foreach ($categories as $category): ?>
                <th><?= $category ?></th>
            <?php endforeach; ?>
        </tr>
        <?php for ($i = 0; $i < 5; $i++): ?>
            <tr>
                <?php foreach ($categories as $category): 
                    $points = ($i + 1) * 100;
                    $key = "$category-$points";
                ?>
                    <td>
                    <?php
$isUsed = false;
foreach ($_SESSION['used_questions'] as $usedKey) {
    if (strpos($usedKey, $key . '-') === 0) {
        $isUsed = true;
        break;
    }
}
?>
<?php if (!$isUsed): ?>
    <button class="question-btn" onclick="showQuestion('<?= $category ?>', <?= $points ?>)" data-key="<?= $key ?>">
        <?= $points ?>
    </button>
<?php else: ?>
    <span class="disabled">X</span>
<?php endif; ?>

                    </td>
                <?php endforeach; ?>
            </tr>
        <?php endfor; ?>
    </table>
    <div id="question-modal">
        <div class="modal-content" id="modal-content">
            <!-- Question content will be loaded here -->
        </div>
    </div>
    </div>

    <div class="scores">
    <?php foreach ($_SESSION['teams'] as $team => $score): ?>
        <div class="team-score">
            <div><?= htmlspecialchars($team) ?></div>
            <div>Score: <?= $score ?></div>
            <div class="score-controls">
                <a href="#" onclick="updateScore('<?= htmlspecialchars($team) ?>', 100); return false;">+100</a>
                <a href="#" onclick="const current = parseInt(this.closest('.team-score').querySelector('div:nth-child(2)').textContent.replace(/\D/g, '')); updateScore('<?= htmlspecialchars($team) ?>', -100, current); return false;">-100</a>

            </div>
        </div>
    <?php endforeach; ?>
</div>

  
    <div id="finish-button-container">
        <?php if ($allUsed): ?>
            <button onclick="showWinner()" class="finish-btn">Finish Game 🎉</button>
        <?php endif; ?>
    </div>
    <a href="reset.php" class="reset-btn">Reset Game</a>
  

</body>
</html>