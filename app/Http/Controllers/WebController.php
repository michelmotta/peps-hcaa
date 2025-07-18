<?php

namespace App\Http\Controllers;

use App\Enums\LessonStatusEnum;
use App\Enums\ProfileEnum;
use App\Models\Certificate;
use App\Models\Feedback;
use App\Models\Information;
use App\Models\Lesson;
use App\Models\Specialty;
use App\Models\Suggestion;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class WebController extends Controller
{
    public function index()
    {
        $lessons = Lesson::with('file')
            ->where('lesson_status', LessonStatusEnum::PUBLICADA->value)
            ->latest()
            ->take(4)
            ->get();

        if (Auth::check()) {
            $lessons->load(['subscriptions' => function ($query) {
                $query->where('user_id', Auth::id());
            }]);
        }

        return view('web.index', [
            'specialties' => Specialty::with('file')
                ->whereNull('parent_id')
                ->orderBy('name')
                ->get(),
            'lessons' => $lessons,
            'teachersCount' => User::whereHas('profiles', function ($q) {
                $q->where('profiles.id', ProfileEnum::PROFESSOR->value);
            })->count(),
            'studentsCount' => User::count(),
            'lessonsCount' => Lesson::where('lesson_status', LessonStatusEnum::PUBLICADA->value)->count(),
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
            ->with(['profiles', 'createdLessons.subscriptions'])
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

    public function classes(Request $request)
    {
        $search = $request->query('q');
        $specialtyId = $request->query('specialty_id');

        $query = Lesson::query()
            ->with(['file', 'specialties', 'topics', 'teacher.file'])
            ->where('lesson_status', LessonStatusEnum::PUBLICADA->value)
            ->orderByDesc('id');

        if ($specialtyId) {
            $ids = Specialty::where('parent_id', $specialtyId)
                ->orWhere('id', $specialtyId)
                ->pluck('id');

            $query->whereHas('specialties', function ($q) use ($ids) {
                $q->whereIn('specialties.id', $ids);
            });
        }

        if ($search) {
            $lessonIds = Lesson::search($search)->keys();
            $query->whereIn('id', $lessonIds);
        }

        $lessons = $query->paginate(15)->withQueryString();

        if (Auth::check()) {
            $lessons->load(['subscriptions' => function ($query) {
                $query->where('user_id', Auth::id());
            }]);
        }

        return view('web.classes', [
            'lessons' => $lessons,
        ]);
    }

    public function myClasses(Request $request)
    {
        $search = $request->query('q');
        $userId = Auth::id();

        $baseQuery = function ($query) use ($userId) {
            $query->whereHas('subscriptions', function ($q) use ($userId) {
                $q->where('users.id', $userId);
            })
                ->with(['subscriptions' => function ($q) use ($userId) {
                    $q->where('users.id', $userId);
                }])
                ->orderByDesc('id');
        };

        $query = Lesson::query();

        if ($search) {
            $query = Lesson::search($search)->query($baseQuery);
        } else {
            $query->when(true, $baseQuery); // applies the baseQuery
        }

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

    public function generateCertificate(Lesson $lesson)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!Gate::allows('generateCertificate', $lesson)) {
            abort(403, 'Você não tem permissão para gerar este certificado.');
        }

        Certificate::registerCertificate($lesson, $user);

        $lessonData = $user->isTeacherOf($lesson)
            ? $lesson
            : $user->subscriptions()->where('lessons.id', $lesson->id)->firstOrFail();

        return Pdf::loadView('web.includes.certificate', [
            'user' => $user,
            'lesson' => $lessonData,
            'date' => now()->translatedFormat('d \\d\\e F \\d\\e Y'),
        ])->setPaper('a4', 'landscape')->download('certificado-' . $lesson->id . '.pdf');
    }
}
