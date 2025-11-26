<x-layout :title="'Memory Card Game'">
  <div class="container">

    {{-- Nickname forma --}}
    <div id="nickname-form" style="{{ $playerName ? 'display:none;' : '' }}">
      <label for="nickname">Ievadi savu nickname:</label>
      <input type="text" id="nickname" placeholder="Tavs vārds" value="{{ $playerName ?? '' }}" />
      <button id="startBtn">{{ $playerName ? 'Turpināt spēli' : 'Sākt spēli' }}</button>
    </div>

    {{-- Ja jau ir playerName – sveiciens + izrakstīšanās --}}
    @if($playerName)
      <div style="margin-top:10px;">
        Sveiks, <strong>{{ $playerName }}</strong>!
        <form method="POST" action="{{ route('memory.logout') }}" style="display:inline;">
          @csrf
          <button type="submit">Izrakstīties</button>
        </form>
      </div>
    @endif

    {{-- Līmeņu izvēle:
         - ja $playerName ir (piemēram, nāc no leaderboard), uzreiz rādam
         - ja nav, sākumā paslēpta, un JS to parādīs pēc Start klikšķa --}}
    <div class="level-selection" style="{{ $playerName ? 'display:block;' : 'display:none;' }}">
      <h2>Izvēlies spēles līmeni:</h2>
      <button data-level="easy">Easy (2x2)</button>
      <button data-level="medium">Medium (3x4)</button>
      <button data-level="hard">Hard (4x5)</button>
    </div>

    {{-- Laika + punktu info (parādās, kad izvēlēts līmenis) --}}
    <div class="game-info" style="display:none; margin-top:10px;">
      <div class="timer">
        <p>Time: <span id="time">00:00</span></p>
      </div>
      <div class="score-board">
        <p>Score: <span id="score">0</span></p>
      </div>
    </div>

    {{-- Spēles režģis --}}
    <div class="game-grid" style="display:none; margin-top:10px;"></div>

    {{-- Restart poga --}}
    <button id="restart" style="display:none; margin-top:10px;">
      Restart Game
    </button>

    {{-- Saite uz leaderboardu --}}
    <div style="margin-top:20px;">
      <a href="{{ route('cardleaderboard') }}" class="btn-leaderboard">Apskatīt leaderboard</a>
    </div>

  </div>

 <script src="script.js"></script>

</x-layout>
