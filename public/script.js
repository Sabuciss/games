document.addEventListener('DOMContentLoaded', () => {
  const grid = document.querySelector('.game-grid');
  const scoreDisplay = document.getElementById('score');
  const timeDisplay = document.getElementById('time');
  const restartBtn = document.getElementById('restart');

  const nicknameForm = document.getElementById('nickname-form');
  const startBtn = document.getElementById('startBtn');
  const nicknameInput = document.getElementById('nickname');

  const levelSelection = document.querySelector('.level-selection');
  const levelButtons = document.querySelectorAll('.level-selection button');

  const gameInfo = document.querySelector('.game-info');
  const leaderboardDiv = document.getElementById('leaderboard');

  let playerName = '';
  let score = 0, time = 0, flippedCards = [], matchedCards = [], timer;
  let rows = 0, cols = 0;
  let cardImages = [
    'img/1.jpg', 'img/2.png', 'img/3.png', 'img/4.png',
    'img/5.jpg', 'img/6.jpg', 'img/7.jpg', 'img/8.jpg',
    'img/9.png', 'img/10.png',
  ];
  let cardArray = [];
  let selectedLevel = null;

  function setupGrid() {
    grid.style.setProperty('--cols', cols);
    grid.style.setProperty('--rows', rows);
  }

  function shuffleCards() {
    cardArray.sort(() => 0.5 - Math.random());
  }

  function createBoard() {
    const totalCards = rows * cols;
    const neededPairs = totalCards / 2;
    cardArray = [...cardImages.slice(0, neededPairs), ...cardImages.slice(0, neededPairs)];
    shuffleCards();

    grid.innerHTML = '';
    cardArray.forEach((cardImage, index) => {
      const card = document.createElement('div');
      card.classList.add('card');
      card.setAttribute('data-id', index);

      const img = document.createElement('img');
      img.setAttribute('src', cardImage);
      card.appendChild(img);

      card.addEventListener('click', flipCard);
      grid.appendChild(card);
    });
  }

  function flipCard() {
    const selectedCard = this;
    if (flippedCards.length < 2 && !selectedCard.classList.contains('flipped')) {
      selectedCard.classList.add('flipped');
      flippedCards.push(selectedCard);

      if (flippedCards.length === 2) {
        setTimeout(checkMatch, 500);
      }
    }
  }

  function checkMatch() {
    const [card1, card2] = flippedCards;
    const img1 = card1.querySelector('img').src;
    const img2 = card2.querySelector('img').src;

    if (img1 === img2) {
      matchedCards.push(card1, card2);
      // Punktu aprēķins
      const basePoints = 10;
      const speedBonus = Math.max(0, 20 - time); // Līdz 20 sekundēm bonuss
      score += basePoints + speedBonus;
    } else {
      card1.classList.remove('flipped');
      card2.classList.remove('flipped');
    }

    flippedCards = [];
    scoreDisplay.textContent = score;

    if (matchedCards.length === cardArray.length) {
      clearInterval(timer);
      if (confirm(`Tu uzvarēji! Tavs rezultāts: ${score} punkti.\nVai vēlies apskatīt leaderboards?`)) {
        renderLeaderboard();
      }
      // Atgriezt līmeņu izvēlei
      levelSelection.style.display = 'block';
      gameInfo.style.display = 'none';
      grid.style.display = 'none';
      restartBtn.style.display = 'none';
    }
  }

  function startTimer() {
    timer = setInterval(() => {
      time++;
      let minutes = Math.floor(time / 60);
      let seconds = time % 60;
      timeDisplay.textContent = `${minutes < 10 ? '0' + minutes : minutes}:${seconds < 10 ? '0' + seconds : seconds}`;
    }, 1000);
  }

  function restartGame() {
    score = 0;
    time = 0;
    flippedCards = [];
    matchedCards = [];
    clearInterval(timer);
    scoreDisplay.textContent = score;
    timeDisplay.textContent = '00:00';
    createBoard();
    startTimer();
  }

  startBtn.addEventListener('click', () => {
    const nickname = nicknameInput.value.trim();
    if (!nickname) {
      alert('Lūdzu, ievadi savu nickname!');
      return;
    }
    playerName = nickname;
    nicknameForm.style.display = 'none';
    levelSelection.style.display = 'block';
    renderLeaderboard();
  });

  levelButtons.forEach(button => {
    button.addEventListener('click', () => {
      const level = button.getAttribute('data-level');
      switch (level) {
        case 'easy':
          rows = 2;
          cols = 2;
          break;
        case 'medium':
          rows = 3;
          cols = 4;
          break;
        case 'hard':
          rows = 4;
          cols = 5;
          break;
      }
      setupGrid();
      restartGame();
      levelSelection.style.display = 'none';
      gameInfo.style.display = 'flex';
      grid.style.display = 'grid';
      restartBtn.style.display = 'inline-block';
    });
  });

  restartBtn.addEventListener('click', () => {
    if (!selectedLevel) return;
    setupGrid();
    restartGame();
    renderLeaderboard();
  });

  function saveHighScore() {
    if (!selectedLevel) return;
    const scoresKey = `highscores_${selectedLevel}`;
    let scores = JSON.parse(localStorage.getItem(scoresKey)) || [];

    scores.push({ name: playerName, time: time, score: score });
    scores.sort((a, b) => b.score - a.score); // Kārto pēc punktiem, liels pirmais
    if (scores.length > 10) scores = scores.slice(0, 10);

    localStorage.setItem(scoresKey, JSON.stringify(scores));
    alert(`Jauns labākais rezultāts līmenim ${selectedLevel}!`);
  }

  function renderLeaderboard() {
    if (!selectedLevel) {
      leaderboardDiv.innerHTML = '<p>Spēlētājs ievadiet nickname un izvēlieties līmeni, lai redzētu leaderboardu.</p>';
      return;
    }
    const scoresKey = `highscores_${selectedLevel}`;
    const scores = JSON.parse(localStorage.getItem(scoresKey)) || [];

    let html = '<h2>Top 10 rezultāti - ' + selectedLevel + '</h2>';
    html += '<table><thead><tr><th>Vieta</th><th>Vārds</th><th>Punkti</th><th>Laiks</th></tr></thead><tbody>';

    scores.forEach((scoreEntry, index) => {
      const highlight = scoreEntry.name === playerName && scoreEntry.score === score ? 'style="font-weight:bold;"' : '';
      html += `<tr ${highlight}><td>${index + 1}</td><td>${scoreEntry.name}</td><td>${scoreEntry.score}</td><td>${formatTime(scoreEntry.time)}</td></tr>`;
    });

    html += '</tbody></table>';
    leaderboardDiv.innerHTML = html;
  }

  function formatTime(seconds) {
    let m = Math.floor(seconds / 60);
    let s = seconds % 60;
    return `${m < 10 ? '0' + m : m}:${s < 10 ? '0' + s : s}`;
  }
});
