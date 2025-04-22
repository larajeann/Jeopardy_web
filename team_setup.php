<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $num_teams = (int)$_POST['num_teams'];
    $teams = [];

    for ($i = 1; $i <= $num_teams; $i++) {
        $name = trim($_POST["team_$i"]) ?: "Team $i";
        $teams[$name] = 0;
    }

    $_SESSION['teams'] = $teams;
    $_SESSION['used_questions'] = [];
    
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Setup Teams | Jeopardy</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #060CE9;
            color: white;
            text-align: center;
            padding: 20px;
            margin: 0;
        }
        
        h2 {
            color: #FFCC00;
            font-size: 2.5em;
            text-shadow: 3px 3px 0px #000;
            margin-bottom: 30px;
        }
        
        .setup-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: rgba(255, 255, 255, 0.1);
            padding: 30px;
            border-radius: 10px;
            border: 3px solid #FFCC00;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
        }
        
        label {
            display: block;
            margin-bottom: 10px;
            font-size: 1.2em;
            color: #FFCC00;
        }
        
        input[type="number"],
        input[type="text"] {
            padding: 12px;
            font-size: 1.1em;
            border: 2px solid #FFCC00;
            border-radius: 5px;
            background-color: #060CE9;
            color: white;
            margin-bottom: 15px;
            width: 80%;
            max-width: 300px;
        }
        
        input:focus {
            outline: none;
            border-color: white;
            box-shadow: 0 0 10px #FFCC00;
        }
        
        button {
            background-color: #FFCC00;
            color: #060CE9;
            border: none;
            padding: 12px 25px;
            font-size: 1.2em;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
            transition: all 0.3s;
        }
        
        button:hover {
            background-color: white;
            transform: scale(1.05);
        }
        
        .team-input {
            margin-bottom: 15px;
            animation: fadeIn 0.5s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
    <script>
        function showInputs() {
            const count = document.getElementById('num_teams').value;
            let container = document.getElementById('team_inputs');
            container.innerHTML = '';
            
            for (let i = 1; i <= count; i++) {
                const div = document.createElement('div');
                div.className = 'team-input';
                div.innerHTML = `
                    <label for="team_${i}">Team ${i} Name:</label>
                    <input type="text" name="team_${i}" id="team_${i}" required>
                `;
                container.appendChild(div);
                
                // Add animation delay for each input
                div.style.animationDelay = `${i * 0.1}s`;
            }
        }
    </script>
</head>
<body>
    <div class="setup-container">
        <h2>Setup Teams</h2>
        <form method="POST">
            <label for="num_teams">Number of Teams (2-6):</label>
            <input type="number" name="num_teams" id="num_teams" 
                   min="2" max="6" required onchange="showInputs()">
            
            <div id="team_inputs"></div>
            
            <button type="submit">Start Game</button>
        </form>
    </div>
</body>
</html>