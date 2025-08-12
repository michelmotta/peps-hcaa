<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHistoryRequest;
use App\Http\Requests\UpdateHistoryRequest;
use App\Models\History;
use App\Models\Lesson;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HistoryController extends Controller
{
    public function saveHistory(Request $request, Lesson $lesson)
    {
        $request->validate([
            'topic_id' => 'required|exists:topics,id',
        ]);

        $history = History::firstOrCreate([
            'user_id' => Auth::id(),
            'topic_id' => $request->topic_id,
        ]);

        return response()->json(['message' => 'Histórico salvo com sucesso.']);
    }
}
