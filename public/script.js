document.addEventListener('DOMContentLoaded', () => {
    // --- Leaderboard filter (ja ir) ---
    const levelSelect = document.getElementById('levelSelect');
    const levels = document.querySelectorAll('.leaderboard-level');
    if (levelSelect && levels.length > 0) {
        levelSelect.addEventListener('change', () => {
            const sel = levelSelect.value;
            levels.forEach(l => {
                l.style.display = (sel === 'all' || l.dataset.level === sel) ? 'block' : 'none';
            });
        });
        levelSelect.value = 'all';
        levels.forEach(l => l.style.display = 'block');
    }

    // --- Game logic (tikai /play lapā, kur ir spēles elementi) ---
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
    const leaderboardContainer = document.getElementById('leaderboardContainer');

    // Ja nav spēles pamatelementu (piemēram, leaderboard lapā) – spēles loģiku neaktivizējam
    if (!grid || !scoreDisplay || !timeDisplay || !restartBtn || !nicknameForm || !startBtn || !nicknameInput || !levelSelection || levelButtons.length === 0 || !gameInfo) {
        return;
    }

    let playerName = nicknameInput?.value || '';
    let score = 0, time = 0, flippedCards = [], matchedCards = [], timer;
    let rows = 0, cols = 0, selectedLevel = null;

    const cardImages = ['img/1.jpg', 'img/2.png', 'img/3.png', 'img/4.png', 'img/5.jpg', 'img/6.jpg', 'img/7.jpg', 'img/8.jpg', 'img/9.png', 'img/10.png'];
    let cardArray = [];

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
        cardArray.forEach((src, index) => {
            const card = document.createElement('div');
            card.classList.add('card-game');
            card.dataset.id = index;
            const img = document.createElement('img');
            img.src = src;
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
            if (flippedCards.length === 2) setTimeout(checkMatch, 500);
        }
    }

    function checkMatch() {
        const [c1, c2] = flippedCards;
        if (c1.querySelector('img').src === c2.querySelector('img').src) {
            matchedCards.push(c1, c2);
            score += 10 + Math.max(0, 20 - time);
        } else {
            c1.classList.remove('flipped');
            c2.classList.remove('flipped');
        }
        flippedCards = [];
        scoreDisplay.textContent = score;
        if (matchedCards.length === cardArray.length) {
            clearInterval(timer);
            saveHighScore();
            alert(`Tu uzvarēji, ${playerName}! Tavs rezultāts: ${score} punkti.`);
            levelSelection.style.display = 'block';
            gameInfo.style.display = 'none';
            grid.style.display = 'none';
            restartBtn.style.display = 'none';
            if (leaderboardContainer) {
                leaderboardContainer.style.display = 'block';
            }
        }
    }

    function startTimer() {
        timer = setInterval(() => {
            time++;
            let m = Math.floor(time / 60), s = time % 60;
            timeDisplay.textContent = `${m < 10 ? '0' + m : m}:${s < 10 ? '0' + s : s}`;
        }, 1000);
    }

    function restartGame() {
        score = 0; time = 0; flippedCards = []; matchedCards = [];
        clearInterval(timer);
        scoreDisplay.textContent = score;
        timeDisplay.textContent = '00:00';
        createBoard();
        startTimer();
    }

    // Pirmā starta / turpināšanas poga
    startBtn.addEventListener('click', () => {
        const nickname = nicknameInput.value.trim();
        if (!nickname) { alert('Lūdzu ievadi nickname!'); return; }
        playerName = nickname;
        nicknameForm.style.display = 'none';
        levelSelection.style.display = 'block';
    });

    // Līmeņu izvēle
    levelButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            selectedLevel = btn.dataset.level;
            switch (selectedLevel) {
                case 'easy': rows = 2; cols = 2; break;
                case 'medium': rows = 3; cols = 4; break;
                case 'hard': rows = 4; cols = 5; break;
            }
            setupGrid();
            restartGame();
            levelSelection.style.display = 'none';
            gameInfo.style.display = 'flex';
            grid.style.display = 'grid';
            restartBtn.style.display = 'inline-block';
            if (leaderboardContainer) {
                leaderboardContainer.style.display = 'none';
            }
        });
    });

    // Restart poga
    restartBtn.addEventListener('click', () => {
        if (!selectedLevel) return;
        setupGrid();
        restartGame();
        if (leaderboardContainer) {
            leaderboardContainer.style.display = 'none';
        }
    });

    // Rezultāta saglabāšana
    function saveHighScore() {
        fetch('/memory/submit-result', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                nickname: playerName,
                level: selectedLevel,
                score: score,
                time_seconds: time
            })
        });
    }
});
