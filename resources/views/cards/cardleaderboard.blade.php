<x-layout title="'Leaderboard'">


<div class="container">
    <h2>Leaderboard top 10 visiem līmeņiem</h2>

    @if($playerName && $playerScore && $selectedLevel)
        <div class="current-player-score" style="margin-bottom:15px; font-weight:bold; font-size:1.2em; color:#6d1b1b;">
            Tavs rezultāts līmenī <strong>{{ $selectedLevel }}</strong>: {{ $playerScore }} punkti ({{ $playerName }})
        </div>
    @endif

    <div class="filters">
        <select id="levelSelect">
            <option value="all">Visi līmeņi</option>
            @foreach(['easy','medium','hard'] as $level)
                <option value="{{ $level }}">{{ ucfirst($level) }}</option>
            @endforeach
        </select>
    </div>

    <div id="leaderboardContainer">
        @foreach($resultsByLevel as $level => $results)
            <div class="leaderboard-level" data-level="{{ $level }}">
                <h3>Līmenis: {{ ucfirst($level) }}</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Vieta</th>
                            <th>Vārds</th>
                            <th>Punkti</th>
                            <th>Laiks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $index => $result)
                            <tr @if($playerName === $result->nickname) style="font-weight:bold;" @endif>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $result->nickname }}</td>
                                <td>{{ $result->score }}</td>
                                <td>{{ gmdate('i:s', $result->time_seconds) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    </div>
</div>

  <div style="margin-top:20px;">
        <a href="{{ url('/play') }}">
  </div>
 
<script src="script.js"></script>


</x-layout>
