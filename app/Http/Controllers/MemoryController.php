<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CardResult;

class MemoryController extends Controller
{
    public function showPlay(Request $request)
    {
        return view('cards.play', [
            'playerName' => $request->session()->get('playerName'),
            'playerScore' => $request->session()->get('playerScore'),
            'selectedLevel' => $request->session()->get('selectedLevel'),
        ]);
    }

    public function submitResult(Request $request)
    {
        $validated = $request->validate([
            'nickname' => 'required|string|max:255',
            'level' => 'required|string|max:50',
            'score' => 'required|integer|min:0',
            'time_seconds' => 'required|integer|min:0',
        ]);

        $result = CardResult::create($validated);

        $request->session()->put('playerName', $validated['nickname']);
        $request->session()->put('playerScore', $validated['score']);
        $request->session()->put('selectedLevel', $validated['level']);

        return response()->json(['status' => 'ok', 'result' => $result]);
    }

    public function showLeaderboardWithPlayer(Request $request)
    {
        $playerName = $request->session()->get('playerName', null);
        $playerScore = $request->session()->get('playerScore', null);
        $selectedLevel = $request->session()->get('selectedLevel', null);

        $levels = ['easy', 'medium', 'hard'];
        $resultsByLevel = [];

        foreach ($levels as $level) {
            $resultsByLevel[$level] = CardResult::where('level', $level)
                ->orderByDesc('score')
                ->orderBy('time_seconds')
                ->limit(10)
                ->get();
        }

        return view('cards.cardleaderboard', compact('playerName', 'playerScore', 'selectedLevel', 'resultsByLevel'));
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['playerName', 'playerScore', 'selectedLevel']);
        return redirect()->route('memory.play');
    }
}
