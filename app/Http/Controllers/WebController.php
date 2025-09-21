<?php

namespace App\Http\Controllers;

use App\Enums\CertificateTypeEnum;
use App\Enums\LessonStatusEnum;
use App\Enums\ProfileEnum;
use App\Models\Certificate;
use App\Models\Feedback;
use App\Models\Guidebook;
use App\Models\Lesson;
use App\Models\LessonUser;
use App\Models\Library;
use App\Models\Sector;
use App\Models\Specialty;
use App\Models\Suggestion;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $feedback = null;
        $lessonUserData = null;
        $averageScore = null;

        if ($user) {
            $feedback = Feedback::where('lesson_id', $lesson->id)
                ->where('user_id', $user->id)
                ->first();

            $lessonUserData = LessonUser::where('user_id', $user->id)
                ->where('lesson_id', $lesson->id)
                ->first();
        }

        $rawAverageScore = LessonUser::where('lesson_id', $lesson->id)
            ->where('finished', true)
            ->average('score');

        $averageScore = null;
        if ($rawAverageScore !== null) {
            $averageScore = $rawAverageScore / 10.0;
        }

        $watchedTopicIds = $user ? $user->histories()->pluck('topic_id')->toArray() : [];

        return view('web.class', [
            'lesson' => $lesson,
            'feedback' => $feedback,
            'watchedTopicIds' => $watchedTopicIds,
            'lessonUserData' => $lessonUserData,
            'averageScore' => $averageScore,
        ]);
    }

    public function teachers(Request $request)
    {
        $search = $request->query('q');

        $query = User::query()
            ->whereHas('profiles', function ($q) {
                $q->where('profiles.id', ProfileEnum::PROFESSOR->value);
            })
            ->withCount([
                'createdLessons',
                'studentSubscriptions'
            ]);

        if ($search) {
            $teacherIds = User::search($search)->keys();
            $query->whereIn('id', $teacherIds);
        }

        $teachers = $query->orderByDesc('id')->paginate(12)->withQueryString();

        return view('web.teachers', [
            'teachers' => $teachers,
        ]);
    }

    public function teacher(User $user)
    {
        if (!$user->hasProfile('Professor')) {
            if (!$user->hasProfile('Professor')) {
                return redirect()
                    ->back();
            }
        }

        $user->loadCount(['createdLessons', 'studentSubscriptions']);

        return view('web.teacher', [
            'teacher' => $user,
        ]);
    }


    public function informations(Request $request)
    {
        $search = $request->query('q');

        $query = Guidebook::query()
            ->with('Category')
            ->where('type', 'extern');

        if ($search) {
            $guidebookIds = Guidebook::search($search)
                ->where('type', 'extern')
                ->keys();

            $query->whereIn('id', $guidebookIds);
        }

        $information = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();

        return view('web.informations', [
            'information' => $information
        ]);
    }

    public function classes(Request $request)
    {
        $search = $request->query('q');
        $specialtyId = $request->query('specialty_id');
        $teacherId = $request->query('teacher_id');
        $sortBy = $request->query('sort_by', 'newest');

        $with = ['file', 'specialties', 'teacher.file'];
        if (Auth::check()) {
            $with['subscriptions'] = fn($q) => $q->where('user_id', Auth::id());
        }

        $query = Lesson::query()
            ->with($with)
            ->where('lesson_status', LessonStatusEnum::PUBLICADA->value);

        if ($specialtyId) {
            $query->whereHas('specialties', fn($q) => $q->where('specialties.id', $specialtyId));
        }

        if ($teacherId) {
            $query->where('user_id', $teacherId);
        }

        if ($search) {
            $lessonIds = Lesson::search($search)->take(100)->keys();
            $query->whereIn('id', $lessonIds);
        }

        $orderDirection = $sortBy === 'oldest' ? 'asc' : 'desc';
        $query->orderBy('id', $orderDirection);

        $lessons = $query->paginate(15)->withQueryString();

        $specialties = Specialty::whereNull('parent_id')->orderBy('name')->get();

        $teachers = User::whereHas('profiles', function ($query) {
            $query->whereIn('profiles.id', [
                ProfileEnum::PROFESSOR->value,
                ProfileEnum::COORDENADOR->value
            ]);
        })
            ->whereHas('createdLessons', function ($query) {
                $query->where('lesson_status', LessonStatusEnum::PUBLICADA->value);
            })
            ->orderBy('name')
            ->get();

        return view('web.classes', [
            'lessons' => $lessons,
            'specialties' => $specialties,
            'teachers' => $teachers,
        ]);
    }

    public function myClasses(Request $request)
    {
        $search = $request->query('q');
        $specialtyId = $request->query('specialty_id');
        $teacherId = $request->query('teacher_id');
        $sortBy = $request->query('sort_by', 'newest');

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $query = $user->subscriptions()
            ->with(['file', 'specialties', 'teacher.file'])
            ->where('lesson_status', LessonStatusEnum::PUBLICADA->value);

        if ($specialtyId) {
            $query->whereHas('specialties', function ($q) use ($specialtyId) {
                $q->where('specialties.id', $specialtyId);
            });
        }

        if ($teacherId) {
            $query->where('user_id', $teacherId);
        }

        if ($search) {
            $lessonIds = Lesson::search($search)->keys();
            $query->whereIn('lessons.id', $lessonIds);
        }

        if ($sortBy === 'oldest') {
            $query->orderBy('lessons.id', 'asc');
        } else {
            $query->orderBy('lessons.id', 'desc');
        }

        $lessons = $query->paginate(9)->withQueryString();

        $specialties = Specialty::whereNull('parent_id')->orderBy('name')->get();
        $teachers = User::whereHas('profiles', fn($q) => $q->where('profiles.id', ProfileEnum::PROFESSOR->value))
            ->orderBy('name')->get();

        return view('web.my_classes', [
            'lessons' => $lessons,
            'specialties' => $specialties,
            'teachers' => $teachers,
        ]);
    }

    public function suggestions(Request $request)
    {
        $search = $request->query('q');

        $topSuggestions = collect();
        $suggestionsQuery = Suggestion::query()
            ->with('user')
            ->orderByDesc('votes');

        if ($search) {
            $suggestionIds = Suggestion::search($search)->take(1000)->keys();
            $suggestionsQuery->whereIn('id', $suggestionIds);
        }

        $suggestions = $suggestionsQuery->paginate(9)->withQueryString();

        if (!$search) {
            $topSuggestionIds = Suggestion::query()
                ->orderByDesc('votes')
                ->limit(3)
                ->pluck('id');

            if ($topSuggestionIds->isNotEmpty()) {
                $caseStatement = collect($topSuggestionIds)->map(function ($id, $index) {
                    return "WHEN id = {$id} THEN {$index}";
                })->implode(' ');

                $topSuggestions = Suggestion::whereIn('id', $topSuggestionIds)
                    ->orderByRaw("CASE {$caseStatement} END")
                    ->get();
            }
        }

        return view('web.suggestions', [
            'topSuggestions' => $topSuggestions,
            'suggestions' => $suggestions,
        ]);
    }

    public function library(Request $request)
    {
        $searchTerm  = $request->input('q');
        $specialtyId = $request->query('specialty_id');
        $sortBy      = $request->query('sort_by', 'newest');

        $query = Library::with('file');

        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'ILIKE', "%{$searchTerm}%");
            });
        }

        if ($specialtyId) {
            $query->whereHas('specialties', fn($q) => $q->where('specialties.id', $specialtyId));
        }

        $orderDirection = $sortBy === 'oldest' ? 'asc' : 'desc';
        $query->orderBy('id', $orderDirection);

        $libraryItems = $query->paginate(12);

        $specialties = Specialty::whereNull('parent_id')
            ->orderBy('name')
            ->get();

        return view('web.library', [
            'libraryItems' => $libraryItems,
            'specialties'  => $specialties,
        ]);
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
        $sectors = Sector::orderByDesc('name')->get();

        if ($user) {
            return view('web.perfil', ['user' => $user, 'sectors' => $sectors]);
        }

        return view('web.perfil', ['sectors' => $sectors]);
    }

    public function userTerms()
    {
        return view('web.user_terms');
    }

    public function generateStudentCertificate(Lesson $lesson)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!Gate::allows('canGenerateStudentCertificate', $lesson)) {
            return redirect()
                ->back()
                ->with('error', 'Você não tem permissão para gerar este certificado.');
        }

        $lessonUserData = LessonUser::where('user_id', $user->id)
            ->where('lesson_id', $lesson->id)
            ->where('finished', true)
            ->firstOrFail();

        $certificate = Certificate::registerCertificate($lesson, $user, CertificateTypeEnum::STUDENT);

        $validationUrl = route('web.validate.certificate', ['uuid' => $certificate->uuid]);

        $qrCodeImage = QrCode::format('png')->size(150)->margin(1)->generate($validationUrl);
        $qrCodeBase64 = 'data:image/png;base64,' . base64_encode($qrCodeImage);

        $data = [
            'user' => $user,
            'lesson' => $lesson,
            'lessonUserData' => $lessonUserData,
            'date' => $lesson->created_at->translatedFormat('d \\d\\e F \\d\\e Y'),
            'qrCodeBase64' => $qrCodeBase64,
            'validationUrl' => $validationUrl,
            'validationCode' => $certificate->uuid,
            'certificateDate' => $certificate->created_at->translatedFormat('d \\d\\e F \\d\\e Y'),
        ];

        return Pdf::loadView('web.includes.certificate', $data)
            ->setPaper('a4', 'landscape')
            ->stream('certificado-' . $user->name . '.pdf');
    }


    public function validateCertificate(Request $request)
    {
        $certificate = null;
        $searchedUuid = $request->input('uuid');

        if ($searchedUuid) {
            if ($request->isMethod('post')) {
                $request->validate([
                    'uuid' => ['required', 'uuid', 'exists:certificates,uuid'],
                ], [
                    'uuid.required' => 'O código de validação é obrigatório.',
                    'uuid.uuid' => 'O código de validação informado não é válido.',
                    'uuid.exists' => 'Nenhum certificado encontrado com esse código.',
                ]);
            }

            $certificate = Certificate::where('uuid', $searchedUuid)
                ->with(['user', 'lesson'])
                ->first();
        }

        return view('web.certificate', [
            'certificate' => $certificate,
            'searchedUuid' => $searchedUuid,
        ]);
    }
}
