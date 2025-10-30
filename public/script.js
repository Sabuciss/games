document.addEventListener('DOMContentLoaded', () => {
    const grid = document.querySelector('.game-grid');
    const scoreDisplay = document.getElementById('score');
    const timeDisplay = document.getElementById('time');
    const restartBtn = document.getElementById('restart');
    let score = 0;
    let time = 0;
    let flippedCards = [];
    let matchedCards = [];
    let timer;

    const cardImages = [
        'apple.jpg', 'banana.jpg', 'black.jpg', 'grapes.jpg',
        'litchi.jpg', 'mango.jpg', 'orange.jpg', 'papaya.jpg'
    ];

    const cardArray = [...cardImages, ...cardImages];

    // Shuffle the cards
    function shuffleCards() {
        cardArray.sort(() => 0.5 - Math.random());
    }

    // Create the game board
    function createBoard() {
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

    // Flip the card
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

    // Check if the cards match
    function checkMatch() {
        const [card1, card2] = flippedCards;
        const img1 = card1.querySelector('img').src;
        const img2 = card2.querySelector('img').src;

        if (img1 === img2) {
            matchedCards.push(card1, card2);
            score += 10;
        } else {
            card1.classList.remove('flipped');
            card2.classList.remove('flipped');
        }

        flippedCards = [];
        scoreDisplay.textContent = score;

        if (matchedCards.length === cardArray.length) {
            clearInterval(timer);
            alert(`You won! Final Score: ${score}`);
        }
    }

    // Timer function
    function startTimer() {
        timer = setInterval(() => {
            time++;
            const minutes = Math.floor(time / 60);
            const seconds = time % 60;
            timeDisplay.textContent = `${minutes < 10 ? '0' + minutes : minutes}:${seconds < 10 ? '0' + seconds : seconds}`;
        }, 1000);
    }

    // Restart the game
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

    restartBtn.addEventListener('click', restartGame);

    createBoard();
    startTimer();
});