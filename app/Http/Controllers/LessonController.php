<?php

namespace App\Http\Controllers;

use App\Enums\CertificateTypeEnum;
use App\Enums\LessonStatusEnum;
use App\Http\Requests\StoreLessonRequest;
use App\Http\Requests\UpdateLessonRequest;
use App\Models\Certificate;
use App\Models\Doubt;
use App\Models\File;
use App\Models\Lesson;
use App\Models\Specialty;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use PhpParser\Node\Expr\Cast\String_;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class LessonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $isCoordenador = Gate::allows('isCoordenador');
        $userId = Auth::id();

        $buildBaseQuery = function () use ($isCoordenador, $userId) {
            return Lesson::query()
                ->when(!$isCoordenador, fn($q) => $q->where('user_id', $userId));
        };

        $stats = [
            'published_count' => $buildBaseQuery()->where('lesson_status', LessonStatusEnum::PUBLICADA)->count(),
            'awaiting_count'  => $buildBaseQuery()->where('lesson_status', LessonStatusEnum::AGUARDANDO_PUBLICACAO)->count(),
            'draft_count'     => $buildBaseQuery()->where('lesson_status', LessonStatusEnum::RASCUNHO)->count(),
            'doubts_count'    => Doubt::whereIn('lesson_id', $buildBaseQuery()->pluck('id'))->where('answered', false)->count(),
        ];

        $specialties = Specialty::orderBy('name')->get();

        $lessons = $buildBaseQuery()
            ->when(request('status'), fn($q, $status) => $q->where('lesson_status', $status))
            ->when(request('specialty'), function ($q, $id) {
                $q->whereHas('specialties', fn($sub) => $sub->where('specialties.id', $id));
            })
            ->when(request('q'), fn($q, $term) => $q->where('name', 'like', "%{$term}%"))
            ->with(['file', 'specialties', 'teacher.file'])
            ->withCount(['topics', 'subscriptions', 'doubts'])
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('dashboard.lessons.index', compact('lessons', 'stats', 'specialties'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $specialties = Specialty::whereNull('parent_id')
            ->with('children')
            ->orderBy('name')
            ->get();

        return view('dashboard.lessons.create', ['specialties' => $specialties]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLessonRequest $request)
    {
        try {
            $validatedData = $request->validated();

            if ($request->hasFile('file')) {
                $file = File::uploadSingleFile($request->file('file'), Auth::id(), 'uploads/lessons');
                $validatedData['file_id'] = $file->id;
            }

            $validatedData['user_id'] = Auth::id();

            $lesson = Lesson::create($validatedData);

            if ($request->has('specialty_ids')) {
                $lesson->specialties()->attach($request->input('specialty_ids'));
            }

            return redirect()
                ->route('dashboard.lessons.index')
                ->with('success', 'A aula foi cadastrada com sucesso!');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Ocorreu um erro ao cadastrar a aula: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lesson $lesson)
    {
        if (!Gate::allows('isCoordenador') && $lesson->user_id !== Auth::id()) {
            return redirect()
                ->back()
                ->with('error', 'Atenção! Você só pode editar suas próprias aulas.');
        }

        $specialties = Specialty::whereNull('parent_id')
            ->with('children')
            ->orderBy('name')
            ->get();

        $selectedSpecialties = $lesson->specialties()->pluck('specialties.id')->toArray();

        return view('dashboard.lessons.edit', [
            'lesson' => $lesson,
            'edit' => true,
            'specialties' => $specialties,
            'selectedSpecialties' => $selectedSpecialties
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLessonRequest $request, Lesson $lesson)
    {
        try {
            $validatedData = $request->validated();
            $oldFile = null;

            if ($request->hasFile('file')) {
                if ($lesson->file) {
                    $oldFile = $lesson->file;
                }

                $newFile = File::uploadSingleFile($request->file('file'), Auth::id(), 'uploads/lessons');
                $validatedData['file_id'] = $newFile->id;
            }

            $lesson->update($validatedData);

            if ($oldFile) {
                $oldFile->delete();
            }

            $lesson->specialties()->sync($request->input('specialty_ids', []));

            return redirect()
                ->route('dashboard.lessons.index')
                ->with('success', 'A aula foi atualizada com sucesso!');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Ocorreu um erro ao atualizar a aula: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lesson $lesson)
    {
        // Professores só podem excluir suas próprias aulas
        if (!Gate::allows('isCoordenador') && $lesson->user_id !== Auth::id()) {
            return redirect()
                ->back()
                ->with('error', 'Atenção! Você só pode editar suas próprias aulas.');
        }

        try {
            $lesson->delete();

            return redirect()
                ->route('dashboard.lessons.index')
                ->with('success', 'A aula foi apagada com sucesso!');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Ocorreu um erro ao apagar a aula: ' . $e->getMessage());
        }
    }

    public function changeStatus(Request $request, Lesson $lesson)
    {
        try {

            $status = $request->validate([
                'status_id' => ['required'],
            ]);

            $lesson->lesson_status = $status['status_id'];
            $lesson->save();

            return redirect()
                ->back()
                ->with('success', 'O status da aula foi alterado com sucesso!');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Ocorreu um erro ao alterar o status da aula: ' . $e->getMessage());
        }
    }

    public function searchLesson(Request $request)
    {
        $search = request('q');

        $lessons = Lesson::search($search)
            ->take(10)
            ->get();

        return $lessons->map(fn($lesson) => [
            'value' => $lesson->id,
            'text'  => $lesson->name,
        ])->values();
    }

    public function generateTeacherCertificate(Lesson $lesson, User $user)
    {
        if (!Gate::allows('canGenerateTeacherCertificate', [$lesson, $user])) {
            return redirect()
                ->back()
                ->with('error', 'Você não tem permissão para gerar este certificado.');
        }

        $certificate = Certificate::registerCertificate($lesson, $user, CertificateTypeEnum::TEACHER);

        $validationUrl = route('web.validate.certificate', ['uuid' => $certificate->uuid]);

        $qrCodeImage = QrCode::format('png')->size(150)->margin(1)->generate($validationUrl);
        $qrCodeBase64 = 'data:image/png;base64,' . base64_encode($qrCodeImage);

        $data = [
            'user' => $user,
            'lesson' => $lesson,
            'date' => $lesson->created_at->translatedFormat('d \\d\\e F \\d\\e Y'),
            'qrCodeBase64' => $qrCodeBase64,
            'validationUrl' => $validationUrl,
            'validationCode' => $certificate->uuid,
            'certificateDate' => $certificate->created_at->translatedFormat('d \\d\\e F \\d\\e Y'),
        ];

        return Pdf::loadView('dashboard.lessons.certificate', $data)
            ->setPaper('a4', 'landscape')
            ->stream('certificado-' . $user->name . '.pdf');
    }
}
