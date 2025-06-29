<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLessonRequest;
use App\Http\Requests\UpdateLessonRequest;
use App\Models\File;
use App\Models\Lesson;
use App\Models\Specialty;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use PhpParser\Node\Expr\Cast\String_;

class LessonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = request('q')
            ? Lesson::search(request('q'))->query(fn($q) => $q->orderByDesc('id'))
            : Lesson::query()->orderByDesc('id');

        // If the user is a professor, filter to only their lessons
        if (!Gate::allows('isCoordenador')) {
            $query->where('user_id', Auth::id());
        }

        return view('dashboard.lessons.index', [
            'lessons' => $query->paginate(10)->withQueryString(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.lessons.create', ['specialties' => Specialty::get()]);
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

            Lesson::create($validatedData);

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
     * Display the specified resource.
     */
    public function show(Lesson $lesson)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lesson $lesson)
    {
        // Professores só podem editar suas próprias aulas
        if (!Gate::allows('isCoordenador') && $lesson->user_id !== Auth::id()) {
            return redirect()
                ->back()
                ->with('error', 'Atenção! Você só pode editar suas próprias aulas.');
        }

        return view('dashboard.lessons.edit', ['lesson' => $lesson, 'specialties' => Specialty::get()]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLessonRequest $request, Lesson $lesson)
    {
        try {
            $validatedData = $request->validated();

            if ($request->hasFile('file')) {
                if ($lesson->file) {
                    $lesson->file->delete();
                }
                $file = File::uploadSingleFile($request->file('file'), Auth::id(), 'uploads/lessons');
                $validatedData['file_id'] = $file->id;
            }

            $lesson->update($validatedData);

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
}
