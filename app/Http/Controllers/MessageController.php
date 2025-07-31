<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMessageRequest;
use App\Http\Requests\UpdateMessageRequest;
use App\Mail\NewLessonMessageMail;
use App\Models\Lesson;
use App\Models\Message;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Lesson $lesson)
    {
        $searchTerm = request('q');

        $query = Message::query()
            ->when(
                $searchTerm,
                fn($q) => Message::search($searchTerm)->query(fn($q) => $q->orderByDesc('id')),
                fn($q) => $q->orderByDesc('id')
            );

        $messages = $query->where('lesson_id', $lesson->id)
            ->paginate(20)
            ->withQueryString();

        return view('dashboard.messages.index', compact('lesson', 'messages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Lesson $lesson)
    {
        return view('dashboard.messages.create', ['lesson' => $lesson]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMessageRequest $request, Lesson $lesson)
    {
        try {
            $validatedData = $request->validated();

            $validatedData['lesson_id'] = $lesson->id;
            $validatedData['user_id'] = Auth::id();

            $message = Message::create($validatedData);

            $lesson->load(['subscriptions' => function ($query) {
                $query->where('finished', false);
            }]);

            foreach ($lesson->subscriptions as $student) {
                Mail::to($student->email)->send(new NewLessonMessageMail($student, $lesson, $message));
            }

            return redirect()
                ->route('dashboard.lessons.messages.index', $lesson)
                ->with('success', 'O comunicado foi cadastrado com sucesso!');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Ocorreu um erro ao cadastrar o comunicado: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function edit(Lesson $lesson, Message $message)
    {
        return view('dashboard.messages.edit', ['lesson' => $lesson, 'message' => $message]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMessageRequest $request, Lesson $lesson, Message $message)
    {
        try {
            $validatedData = $request->validated();

            $validatedData['lesson_id'] = $lesson->id;
            $validatedData['user_id'] = Auth::id();

            $message->update($validatedData);

            $lesson->load(['subscriptions' => function ($query) {
                $query->where('finished', false);
            }]);

            foreach ($lesson->subscriptions as $student) {
                Mail::to($student->email)->send(new NewLessonMessageMail($student, $lesson, $message));
            }

            return redirect()
                ->route('dashboard.lessons.messages.index', $lesson)
                ->with('success', 'O comunicado foi atualizado com sucesso!');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Ocorreu um erro ao cadastrar o comunicado: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lesson $lesson, Message $message)
    {
        try {
            $message->delete();

            return redirect()
                ->route('dashboard.lessons.messages.index', $lesson)
                ->with('success', 'O comunicado foi apagado com sucesso!');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Ocorreu um erro ao excluir o comunicado: ' . $e->getMessage());
        }
    }
}
