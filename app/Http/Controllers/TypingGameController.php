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
        // load text paragraphs from config (see config/typing_texts.php)
        $this->texts = config('typing_texts.paragraphs', []);
    }

    public function index(Request $request)
    {
        // difficulty options -> approximate word counts will be handled in JS
        $levels = [
            'easy' => 50,
            'medium' => 100,
            'hard' => 150,
            'hardcore' => 300,
        ];

        return view('typing_game.index', [
            'levels' => $levels,
            'paragraphs' => $this->texts,
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
            'time_seconds' => (int)$data['time_seconds'],
            'accuracy' => round($data['accuracy'], 2),
        ]);

        return response()->json(['status' => 'ok', 'entry' => $entry]);
    }

    public function leaderboard($level)
    {
        $level = Str::lower($level);
        if (!in_array($level, ['easy','medium','hard','hardcore'])) {
            abort(404);
        }

        $top = TypingLeaderboard::where('level', $level)
            ->orderByDesc('wpm')
            ->orderBy('time_seconds')
            ->limit(50)
            ->get();

        return response()->json($top);
    }
}
