<x-layout :title="'Memory Card Game'">
  <div class="container">
    <div id="nickname-form" style="{{ $playerName ? 'display:none;' : '' }}">
      <label for="nickname">Ievadi savu nickname:</label>
      <input type="text" id="nickname" placeholder="Tavs vārds" value="{{ $playerName ?? '' }}" />
      <button id="startBtn">{{ $playerName ? 'Turpināt spēli' : 'Sākt spēli' }}</button>
    </div>

    @if($playerName)
      <div>
        Sveiks, <strong>{{ $playerName }}</strong>!
        <form method="POST" action="{{ route('memory.logout') }}" style="display:inline;">
          @csrf
          <button type="submit">Izrakstīties</button>
        </form>
      </div>
    @endif

    <div class="level-selection" style="display:none;">
      <h2>Izvēlies spēles līmeni:</h2>
      <button data-level="easy">Easy (2x2)</button>
      <button data-level="medium">Medium (3x4)</button>
      <button data-level="hard">Hard (4x5)</button>
    </div>

    <div class="game-info" style="display:none;">
      <div class="timer">
        <p>Time: <span id="time">00:00</span></p>
      </div>
      <div class="score-board">
        <p>Score: <span id="score">0</span></p>
      </div>
    </div>

    <div class="game-grid" style="display:none;"></div>

    <button id="restart" style="display:none;">Restart Game</button>

    <a href="{{ route('cardleaderboard') }}" class="btn-leaderboard">Apskatīt leaderboard</a>
  </div>

<script src="script.js"></script>

</x-layout>
