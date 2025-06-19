<?php

namespace App\Http\Controllers;

use App\Enums\ProfileEnum;
use App\Models\Suggestion;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard.index', [
            'teachersCount' => User::whereHas('profiles', function ($q) { $q->where('profiles.id', ProfileEnum::PROFESSOR->value); })->with('profiles')->count(),
            'studentsCount' => User::all()->count(),
            'suggestionsCount' => Suggestion::all()->count(),
            'suggestions' => Suggestion::query()->orderByDesc('votes')->paginate(20)->withQueryString(),
            'totalVotes' => Suggestion::sum('votes')
        ]);
    }
}
