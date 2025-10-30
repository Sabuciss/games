<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Memory Card Game</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container">
    <h1>Memory Card Game</h1>
    <div class="game-info">
      <div class="timer">
        <p>Time: <span id="time">00:00</span></p>
      </div>
      <div class="score-board">
        <p>Score: <span id="score">0</span></p>
      </div>
    </div>
    <div class="game-grid">
      <!-- Cards will be generated here -->
    </div>
    <button id="restart">Restart Game</button>
  </div>
  <script src="script.js"></script>
</body>
</html>