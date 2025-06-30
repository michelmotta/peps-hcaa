<?php

namespace App\Http\Controllers;

use App\Enums\LessonStatusEnum;
use App\Enums\ProfileEnum;
use App\Models\Feedback;
use App\Models\Information;
use App\Models\Lesson;
use App\Models\Specialty;
use App\Models\Suggestion;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WebController extends Controller
{
    public function index()
    {
        return view('web.index', [
            'specialties' => Specialty::with('file')
                ->whereNull('parent_id')
                ->orderBy('name')
                ->get(),
            'lessons' => Lesson::with('file')
                ->where('lesson_status', LessonStatusEnum::PUBLICADA->value)
                ->latest()
                ->take(4)
                ->get(),
            'teachersCount' => User::whereHas('profiles', function ($q) {
                $q->where('profiles.id', ProfileEnum::PROFESSOR->value);
            })->count(),
            'studentsCount' => User::count(),
            'lessonsCount' => Lesson::where('lesson_status', LessonStatusEnum::PUBLICADA->value)->count(),
        ]);
    }

    public function classes(Request $request)
    {
        $search = $request->query('q');
        $specialtyId = $request->query('specialty_id');

        $baseQuery = function ($query) use ($specialtyId) {
            $query->with('file')
                ->where('lesson_status', LessonStatusEnum::PUBLICADA->value)
                ->orderByDesc('id');

            if ($specialtyId) {
                $query->where('specialty_id', $specialtyId);
            }
        };

        $query = $search
            ? Lesson::search($search)->query($baseQuery)
            : Lesson::query()->tap($baseQuery);

        return view('web.classes', [
            'lessons' => $query->paginate(15)->withQueryString(),
        ]);
    }

    public function class(Lesson $lesson)
    {
        $user = Auth::user();
        $feedback = null;

        if ($user) {
            $feedback = Feedback::where('lesson_id', $lesson->id)
                ->where('user_id', $user->id)
                ->first();
        }

        return view('web.class', [
            'lesson' => $lesson,
            'feedback' => $feedback,
        ]);
    }

    public function teachers(Request $request)
    {
        $search = $request->query('q');

        $baseQuery = fn($query) => $query
            ->whereHas('profiles', function ($q) {
                $q->where('profiles.id', ProfileEnum::PROFESSOR->value);
            })
            ->with(['profiles', 'createdLessons.students'])
            ->orderByDesc('id');

        $query = $search
            ? User::search($search)->query($baseQuery)
            : User::query()->tap($baseQuery);

        return view('web.teachers', [
            'teachers' => $query->paginate(9)->withQueryString(),
        ]);
    }

    public function teacher(User $user)
    {
        if (!$user->hasProfile('Professor')) {
            return redirect()
                ->back();
        }

        $user->load(['createdLessons.students']);

        return view('web.teacher', [
            'teacher' => $user,
            'lessons' => Lesson::where('user_id', $user->id)->paginate(4)->withQueryString()
        ]);
    }

    public function informations(Request $request)
    {
        $search = $request->query('q');

        $baseQuery = fn($query) => $query
            ->with('user')
            ->where('published', true)
            ->orderByDesc('id');

        $query = $search
            ? Information::search($search)->query($baseQuery)
            : Information::query()->tap($baseQuery);

        return view('web.informations', [
            'information' => $query->paginate(9)->withQueryString(),
        ]);
    }

    public function myClasses(Request $request)
    {
        $search = $request->query('q');

        $baseQuery = fn($query) => $query
            ->whereHas('students', function ($q) {
                $q->where('users.id', Auth::id());
            })
            ->orderByDesc('id');

        $query = $search
            ? Lesson::search($search)->query($baseQuery)
            : Lesson::query()->tap($baseQuery);

        return view('web.my_classes', [
            'lessons' => $query->paginate(9)->withQueryString(),
        ]);
    }

    public function suggestions(Request $request)
    {
        $search = $request->query('q');

        $baseQuery = fn($query) => $query
            ->orderByDesc('votes');

        $query = $search
            ? Suggestion::search($search)->query($baseQuery)
            : Suggestion::query()->tap($baseQuery);

        return view('web.suggestions', [
            'suggestions' => $query->paginate(9)->withQueryString(),
        ]);
    }

    public function library()
    {
        return view('web.library');
    }

    public function suggestionCreate(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|max:255',
                'description' => 'nullable',
            ]);

            $validatedData['votes'] = 1;
            $validatedData['user_id'] = Auth::id();

            Suggestion::create($validatedData);

            return redirect()->route('web.suggestions')
                ->with('success', 'A sugestão foi cadastrada com sucesso!');
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Ocorreu um erro ao cadastrar a sugestão: ' . $e->getMessage());
        }
    }

    public function suggestionUpdate(Request $request, Suggestion $suggestion)
    {
        try {
            $suggestion->votes += 1;

            $suggestion->update();

            return redirect()->route('web.suggestions')
                ->with('success', 'O seu voto foi contabilizado com sucesso!');
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Ocorreu um erro ao contabilizar seu voto: ' . $e->getMessage());
        }
    }

    public function login()
    {
        return view('web.login');
    }

    public function perfil()
    {
        $user = Auth::user();

        if ($user) {
            return view('web.perfil', ['user' => $user]);
        }

        return view('web.perfil');
    }
}
