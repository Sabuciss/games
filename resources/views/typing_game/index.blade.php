<!doctype html>
<html lang="lv">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Typing Game</title>
  <link rel="stylesheet" href="{{ asset('css/typing-game.css') }}">

</head>
<body>
<!-- 
DB_CONNECTION=sqlite
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=database/database.sqlite
DB_USERNAME=root
DB_PASSWORD=
 -->
@include('components.navbar')
  <div id="game-container">
    <h1>Typing Game — Raksti ātrāk!</h1>
    <p>Izvēlies līmeni, spied START un sāc rakstīt.</p>

    <div id="settings">
      <div>
        <label>Līmenis</label>
        <select id="levelSelect">
          @foreach($levels as $k => $v)
            <option value="{{ $k }}">{{ ucfirst($k) }} — ~{{ $v }} vārdi</option>
          @endforeach
        </select>
      </div>

      <div>
        <label>Nick</label>
        <input id="nickname" placeholder="Tavs nik"/>
      </div>

      <div>
        <button id="startBtn">START</button>
      </div>
    </div>

    <div id="gameArea">
      <div id="textDisplay"></div>
      <div>
        <input id="typingInput" placeholder="Sāc rakstīt šeit..." disabled />
      </div>

      <div id="stats">
        <span>Laiks: <span id="timer">00:00</span></span>
        <span>WPM: <span id="wpm">0</span></span>
        <span>Accuracy: <span id="accuracy">0%</span></span>
      </div>

      <div id="actions">
        <button id="submitBtn" disabled>Submit</button>
        <button id="resetBtn">Reset</button>
      </div>
    </div>

    <div id="leaderboard-section">
      <h2>Leaderboard</h2>
      <div id="leaderboard"></div>
    </div>
  </div>

  <script>
    // Data from server
    const paragraphs = @json($paragraphs);
    const levelsMap = @json($levels);

    // State
    let words = []; // array of words to show
    let currentIndex = 0;
    let startTime = null;
    let timerInterval = null;
    let mistakes = 0;
    let typedCharsTotal = 0;
    let started = false;

    const levelSelect = document.getElementById('levelSelect');
    const nickname = document.getElementById('nickname');
    const startBtn = document.getElementById('startBtn');
    const typingInput = document.getElementById('typingInput');
    const textDisplay = document.getElementById('textDisplay');
    const timerEl = document.getElementById('timer');
    const wpmEl = document.getElementById('wpm');
    const accuracyEl = document.getElementById('accuracy');
    const submitBtn = document.getElementById('submitBtn');
    const resetBtn = document.getElementById('resetBtn');
    const leaderboardEl = document.getElementById('leaderboard');

    function pickTextForLevel(level) {
      const targetCount = levelsMap[level];
      // assemble paragraphs until we reach target word count (or slightly exceed)
      let text = '';
      let count = 0;
      // shuffle paragraphs to vary
      const pool = [...paragraphs].sort(() => Math.random() - 0.5);
      for (let p of pool) {
        text += (text ? ' ' : '') + p;
        count = text.trim().split(/\s+/).length;
        if (count >= targetCount) break;
      }
      // if still not enough, repeat random paragraphs
      while (text.trim().split(/\s+/).length < targetCount) {
        text += ' ' + paragraphs[Math.floor(Math.random()*paragraphs.length)];
      }
      return text.trim();
    }

    function renderText(txt) {
      words = txt.split(/\s+/);
      currentIndex = 0;
      textDisplay.innerHTML = words.map((w, i) => `<span data-index="${i}" class="word">${escapeHtml(w)}</span>`).join(' ');
      highlightCurrent();
    }

    function escapeHtml(str) {
      return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    function highlightCurrent() {
      const spans = textDisplay.querySelectorAll('.word');
      spans.forEach(s => s.classList.remove('bg-yellow-100','bg-green-100','bg-red-100','text-gray-400','underline'));
      const current = textDisplay.querySelector(`span[data-index="${currentIndex}"]`);
      if (current) current.classList.add('underline');
      // scroll into view if needed
      if (current) current.scrollIntoView({behavior: 'smooth', block: 'nearest'});
    }

    function startTimer() {
      if (started) return;
      started = true;
      startTime = Date.now();
      timerInterval = setInterval(() => {
        const secs = Math.floor((Date.now() - startTime)/1000);
        timerEl.textContent = formatTime(secs);
        updateWpmAndAccuracy(secs);
      }, 250);
    }

    function stopTimer() {
      if (timerInterval) {
        clearInterval(timerInterval);
        timerInterval = null;
      }
      started = false;
    }

    function formatTime(totalSeconds) {
      const s = totalSeconds % 60;
      const m = Math.floor(totalSeconds / 60);
      return `${m.toString().padStart(2,'0')}:${s.toString().padStart(2,'0')}`;
    }

    function updateWpmAndAccuracy(elapsedSeconds) {
      const minutes = Math.max( (elapsedSeconds)/60 , 1/60 ); // avoid div by zero
      const correctWords = Math.max(0, currentIndex); // words completed correctly
      const wpm = Math.round(correctWords / minutes);
      wpmEl.textContent = isFinite(wpm) ? wpm : 0;
      const accuracy = typedCharsTotal === 0 ? 100 : Math.max(0, ((typedCharsTotal - mistakes)/typedCharsTotal) * 100);
      accuracyEl.textContent = `${Math.round(accuracy)}%`;
    }

    // Real-time checking per word on space or enter or when user ends a word
    typingInput.addEventListener('keydown', (e) => {
      if (e.key === ' ' || e.key === 'Enter') {
        e.preventDefault();
        handleWordSubmit();
      } else {
        // on first keypress after START, start timer
        if (!started && !e.ctrlKey && !e.metaKey && !e.altKey) {
          startTimer();
        }
      }
    });

    typingInput.addEventListener('input', (e) => {
  const val = typingInput.value.trim();
  const target = words[currentIndex] || '';
  const span = textDisplay.querySelector(`span[data-index="${currentIndex}"]`);
  if (!span) return;

  // no krāsu stilu
  span.classList.remove('bg-red-200', 'bg-green-200', 'bg-green-50');

  if (val.length === 0) {
    // neko vēl nav ievadījis
    span.classList.remove('bg-red-200', 'bg-green-200', 'bg-green-50');
  } else if (target === val) {
    // pilnīgi pareizi
    span.classList.add('bg-green-200');
  } else if (target.startsWith(val)) {
    // daļēji pareizi
    span.classList.add('bg-green-50');
  } else {
    // kļūda
    span.classList.add('bg-red-200');
  }
});


    function handleWordSubmit() {
      const val = typingInput.value.trim();
      if (!val && typingInput.value === '') {
        // user pressed space without typing — ignore but count as mistake if they try to advance?
        return;
      }
      const target = words[currentIndex] || '';
      // update total typed chars and mistakes
      typedCharsTotal += val.length;
      if (val === target) {
        // correct
        const span = textDisplay.querySelector(`span[data-index="${currentIndex}"]`);
        if (span) {
          span.classList.remove('bg-yellow-100','bg-red-100');
          span.classList.add('bg-green-100');
        }
      } else {
        mistakes += Math.abs(target.length - val.length) + levenshtein(val, target); // rough penalty
        const span = textDisplay.querySelector(`span[data-index="${currentIndex}"]`);
        if (span) {
          span.classList.remove('bg-yellow-100');
          span.classList.add('bg-red-100');
        }
      }
      currentIndex++;
      typingInput.value = '';
      highlightCurrent();

      // If finished
      if (currentIndex >= words.length) {
        finishRun();
      }
    }

    function finishRun() {
      stopTimer();
      const secs = Math.floor((Date.now() - startTime)/1000);
      const minutes = Math.max(secs/60, 1/60);
      const correctWords = words.length; // we consider all as attempted; could refine
      const wpm = Math.round(correctWords / minutes);
      const accuracy = typedCharsTotal === 0 ? 100 : Math.max(0, ((typedCharsTotal - mistakes)/typedCharsTotal) * 100);
      // enable submit
      submitBtn.disabled = false;
      typingInput.disabled = true;
      // show final
      updateWpmAndAccuracy(secs);
      alert(`Pabeigts! WPM: ${wpm}, Time: ${formatTime(secs)}, Accuracy: ${Math.round(accuracy)}%`);
    }

    startBtn.addEventListener('click', () => {
      // prepare text
      const lvl = levelSelect.value;
      const txt = pickTextForLevel(lvl);
      renderText(txt);
      typingInput.disabled = false;
      typingInput.value = '';
      typingInput.focus();
      startTime = null;
      stopTimer();
      timerEl.textContent = '00:00';
      wpmEl.textContent = '0';
      accuracyEl.textContent = '0%';
      mistakes = 0;
      typedCharsTotal = 0;
      submitBtn.disabled = true;
      started = false;
    });

    submitBtn.addEventListener('click', async () => {
      const lvl = levelSelect.value;
      const nick = nickname.value.trim() || 'Anon';
      const secs = startTime ? Math.floor((Date.now() - startTime)/1000) : 0;
      const wpm = parseFloat(wpmEl.textContent) || 0;
      const acc = parseFloat(accuracyEl.textContent) || 0;

      try {
        const res = await fetch("{{ route('typing.submit') }}", {
          method: 'POST',
          headers: {
            'Content-Type':'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          body: JSON.stringify({
            nickname: nick,
            level: lvl,
            wpm: wpm,
            time_seconds: secs,
            accuracy: acc
          })
        });
        const json = await res.json();
        if (json.status === 'ok') {
          alert('Rezultāts saglabāts!');
          loadLeaderboard(lvl);
        } else {
          alert('Neizdevās saglabāt.');
        }
      } catch(e) {
        console.error(e);
        alert('Kļūda saglabājot rezultātu.');
      }
    });

    resetBtn.addEventListener('click', () => {
      stopTimer();
      typingInput.disabled = true;
      typingInput.value = '';
      textDisplay.innerHTML = '';
      timerEl.textContent = '00:00';
      wpmEl.textContent = '0';
      accuracyEl.textContent = '0%';
      submitBtn.disabled = true;
    });

    levelSelect.addEventListener('change', () => {
      loadLeaderboard(levelSelect.value);
    });

    async function loadLeaderboard(level = null) {
      level = level || levelSelect.value;
      try {
        const res = await fetch(`/typing-game/leaderboard/${level}`);
        const data = await res.json();
        if (!data || data.length === 0) {
          leaderboardEl.innerHTML = '<div class="text-sm text-gray-500">Nav ierakstu.</div>';
          return;
        }
        leaderboardEl.innerHTML = '<ol class="list-decimal pl-6">' + data.map((d) => {
          const t = new Date(d.created_at);
          const mins = Math.floor(d.time_seconds / 60);
          const secs = d.time_seconds % 60;
          const timeStr = `${mins}:${secs.toString().padStart(2,'0')}`;
          return `<li class="mb-1"><strong>${escapeHtml(d.nickname)}</strong> — WPM: ${d.wpm} — Time: ${timeStr} — Acc: ${d.accuracy}%</li>`;
        }).join('') + '</ol>';
      } catch(e) {
        leaderboardEl.innerHTML = '<div class="text-sm text-red-500">Neizdevās ielādēt leaderboard.</div>';
      }
    }

    // small Levenshtein distance for penalty
    function levenshtein(a,b){
      if(!a||!b) return Math.max(a.length,b.length);
      const m = a.length, n = b.length;
      const dp = Array.from({length:m+1}, ()=> new Array(n+1).fill(0));
      for(let i=0;i<=m;i++) dp[i][0]=i;
      for(let j=0;j<=n;j++) dp[0][j]=j;
      for(let i=1;i<=m;i++){
        for(let j=1;j<=n;j++){
          dp[i][j] = Math.min(
            dp[i-1][j]+1,
            dp[i][j-1]+1,
            dp[i-1][j-1] + (a[i-1]===b[j-1] ? 0 : 1)
          );
        }
      }
      return dp[m][n];
    }

    // init
    loadLeaderboard(levelSelect.value);
  </script>
</body>
</html>
