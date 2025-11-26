<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TypingLeaderboard;
use Illuminate\Support\Str;

class TypingGameController extends Controller
{
    protected $texts;

    public function __construct()
    {
        // Load text paragraphs from config (we’ll create it in a moment)
        $this->texts = config('typing_texts.paragraphs', []);
    }

    public function index(Request $request)
{
    $levels = [
        'easy' => 50,
        'medium' => 100,
        'hard' => 150,
        'hardcore' => 300,
    ];

    // send ALL paragraphs (pool) to JS so JS can build long texts
    $paragraphPool = $this->texts;

    // shuffle them so every refresh randomizes order
    shuffle($paragraphPool);

    return view('typing_game.index', [
        'levels' => $levels,
        'paragraphs' => $paragraphPool, // now RANDOM ORDER
    ]);
}



    public function submit(Request $request)
    {
        $data = $request->validate([
            'nickname' => 'required|string|max:40',
            'level' => 'required|in:easy,medium,hard,hardcore',
            'wpm' => 'required|numeric',
            'time_seconds' => 'required|numeric',
            'accuracy' => 'required|numeric',
        ]);

        $entry = TypingLeaderboard::create([
            'nickname' => $data['nickname'],
            'level' => $data['level'],
            'wpm' => round($data['wpm'], 2),
            'time_seconds' => (int) $data['time_seconds'],
            'accuracy' => round($data['accuracy'], 2),
        ]);

        return response()->json(['status' => 'ok', 'entry' => $entry]);
    }

    public function leaderboard($level)
    {
        $level = Str::lower($level);
        if (!in_array($level, ['easy', 'medium', 'hard', 'hardcore'])) {
            abort(404);
        }

        $top = TypingLeaderboard::where('level', $level)
            ->orderByDesc('accuracy')
            ->orderByDesc('wpm')
            ->orderBy('time_seconds')
            ->limit(50)
            ->get();

        return response()->json($top);
    }
}
