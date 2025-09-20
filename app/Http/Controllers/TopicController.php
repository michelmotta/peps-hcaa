<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTopicRequest;
use App\Http\Requests\UpdateTopicRequest;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\Topic;
use App\Models\Video;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TopicController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Lesson $lesson)
    {
        $searchTerm = request('q');

        $query = Topic::query()
            ->when(
                $searchTerm,
                fn($q) => Topic::search($searchTerm)->query(fn($q) => $q->orderByDesc('id')),
                fn($q) => $q->orderByDesc('id')
            );

        $topics = $query->where('lesson_id', $lesson->id)
            ->paginate(20)
            ->withQueryString();

        return view('dashboard.topics.index', compact('lesson', 'topics'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Lesson $lesson)
    {
        return view('dashboard.topics.create', ['lesson' => $lesson, 'topic' => new Topic()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTopicRequest $request, Lesson $lesson)
    {
        try {
            $validatedData = $request->validated();

            $validatedData['lesson_id'] = $lesson->id;

            $validatedData['quiz'] = isset($validatedData['quiz']) ? json_decode($validatedData['quiz'], true) : [];
            $validatedData['attachments'] = isset($validatedData['attachments']) ? json_decode($validatedData['attachments'], true) : [];

            $topic = Topic::create($validatedData);

            if (!empty($validatedData['quiz'])) {
                $quizzes = collect($validatedData['quiz'])->map(function ($quizData) {
                    return new Quiz([
                        'question' => $quizData['question'] ?? null,
                        'options'  => $quizData['options'] ?? null,
                        'correct'  => $quizData['correct'] ?? null,
                    ]);
                });

                $topic->quizzes()->saveMany($quizzes);
            }

            return redirect()
                ->route('dashboard.lessons.topics.index', $lesson)
                ->with('success', 'O tópico foi cadastrado com sucesso!');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Ocorreu um erro ao cadastrar o tópico: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lesson $lesson, Topic $topic)
    {
        return view('dashboard.topics.edit', ['lesson' => $lesson, 'topic' => $topic]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTopicRequest $request, Lesson $lesson, Topic $topic)
    {
        try {
            $validatedData = $request->validated();

            $validatedData['attachments'] = isset($validatedData['attachments']) ? json_decode($validatedData['attachments'], true) : [];

            $topic->update($validatedData);

            if ($request->has('quiz')) {
                $topic->quizzes()->delete();

                $quizData = json_decode($request->input('quiz'), true) ?? [];
                if (!empty($quizData)) {
                    $quizzes = collect($quizData)->map(function ($data) {
                        return new Quiz([
                            'question' => $data['question'] ?? null,
                            'options'  => $data['options'] ?? null,
                            'correct'  => $data['correct'] ?? null,
                        ]);
                    });
                    $topic->quizzes()->saveMany($quizzes);
                }
            }

            return redirect()
                ->route('dashboard.lessons.topics.index', $lesson->id)
                ->with('success', 'O tópico foi atualizado com sucesso!');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Ocorreu um erro ao atualizar o tópico: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lesson $lesson, Topic $topic)
    {
        try {
            if ($topic->video) {
                Storage::disk('public')->delete($topic->video->path);
                if ($topic->video->thumbnail_path) {
                    Storage::disk('public')->delete($topic->video->thumbnail_path);
                }
                $topic->video->delete();
            }

            $topic->delete();

            return redirect()
                ->route('dashboard.lessons.topics.index', $lesson)
                ->with('success', 'O tópico foi removido com sucesso!');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Ocorreu um erro ao excluir o tópico: ' . $e->getMessage());
        }
    }


    public function attachmentsUpload(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('uploads/lessons/attachments', 'public');

            return response()->json([
                'date' => Carbon::now()->format('d/m/Y'),
                'name' => $file->getClientOriginalName(),
                'extension' => $file->extension(),
                'size' => $file->getSize(),
                'path' => $path,
            ]);
        }

        return response()->json(['message' => 'Ocorreu um erro com o upload do arquivo.'], 400);
    }

    public function attachmentsDelete(Request $request)
    {
        $path = $request->input('path');
        if ($path && Storage::exists($path)) {
            Storage::delete($path);
        }

        return response()->json(['message' => 'Arquivo apagado com sucesso.']);
    }
}
