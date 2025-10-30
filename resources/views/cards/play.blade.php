<!DOCTYPE html>
<html lang="lv">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Memory Card Game</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>

  <div class="container">

@include('components.navbar')

    <h1>Memory Card Game</h1>

    <!-- Nickname ievade -->
    <div id="nickname-form">
      <label for="nickname">Ievadi savu nickname:</label>
      <input type="text" id="nickname" placeholder="Tavs vārds" />
      <button id="startBtn">Sākt spēli</button>
    </div>

    <!-- Līmeņu izvēle -->
    <div class="level-selection" style="display:none;">
      <h2>Izvēlies spēles līmeni:</h2>
      <button data-level="easy">Easy (2x2)</button>
      <button data-level="medium">Medium (3x4)</button>
      <button data-level="hard">Hard (4x5)</button>
    </div>

    <!-- Spēles info -->
    <div class="game-info" style="display:none;">
      <div class="timer">
        <p>Time: <span id="time">00:00</span></p>
      </div>
      <div class="score-board">
        <p>Score: <span id="score">0</span></p>
      </div>
    </div>

    <!-- Spēles laukums -->
    <div class="game-grid" style="display:none;"></div>

    <!-- Restart poga -->
    <button id="restart" style="display:none;">Restart Game</button>

    <!-- Leaderboard -->
    <div id="leaderboard" style="margin-top:20px;"></div>
  </div>

  <script src="script.js"></script>
</body>
</html>
