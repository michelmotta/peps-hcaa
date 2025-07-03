<?php

namespace App\Http\Controllers;

use App\Enums\ProfileEnum;
use App\Models\Doubt;
use App\Models\Lesson;
use App\Models\Suggestion;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        $suggestionsCount = Suggestion::count();
        $unansweredQuestionsCount = Doubt::where('answered', false)->count();

        if ($user->hasAnyProfile(['Coordenador'])) {
            $classesCount = Lesson::count();

            $studentsCount = User::whereHas('subscriptions')->distinct()->count();
        } elseif ($user->hasAnyProfile(['Professor'])) {
            $classesCount = $user->createdLessons()->count();

            $studentsCount = User::whereHas('subscriptions', function ($q) use ($user) {
                $q->where('lessons.user_id', $user->id);
            })->distinct()->count();
        } else {
            $classesCount = 0;
            $studentsCount = 0;
        }

        return view('dashboard.index', [
            'studentsCount' => $studentsCount,
            'suggestionsCount' => $suggestionsCount,
            'classesCount' => $classesCount,
            'unansweredQuestionsCount' => $unansweredQuestionsCount,
        ]);
    }
}
