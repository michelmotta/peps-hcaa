<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLessonUserRequest;
use App\Http\Requests\UpdateLessonUserRequest;
use App\Models\Lesson;
use App\Models\LessonUser;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LessonUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Lesson $lesson)
    {
        $searchTerm = $request->input('q');

        $query = LessonUser::with('user')
            ->where('lesson_id', $lesson->id)
            ->when($searchTerm, function ($q) use ($searchTerm) {
                $q->whereHas('user', function ($query) use ($searchTerm) {
                    $query->where('name', 'ilike', "%{$searchTerm}%")
                        ->orWhere('email', 'ilike', "%{$searchTerm}%");
                });
            })
            ->orderByDesc('id');

        $subscriptions = $query->paginate(20)->withQueryString();

        return view('dashboard.subscriptions.index', compact('lesson', 'subscriptions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Lesson $lesson)
    {
        return view('dashboard.subscriptions.create', ['lesson' => $lesson]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLessonUserRequest $request, Lesson $lesson)
    {
        try {
            $validatedData = $request->validated();

            LessonUser::create($validatedData);

            return redirect()
                ->route('dashboard.lessons.subscriptions.index', $lesson)
                ->with('success', 'A inscrição foi cadastrada com sucesso!');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Ocorreu um erro ao cadastrar a inscrição: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(LessonUser $lessonUser)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lesson $lesson, LessonUser $subscription)
    {
        return view('dashboard.subscriptions.edit', ['lesson' => $lesson, 'subscription' => $subscription]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLessonUserRequest $request, Lesson $lesson, LessonUser $subscription)
    {
        try {
            $validatedData = $request->validated();

            $subscription->update($validatedData);

            return redirect()
                ->route('dashboard.lessons.subscriptions.index', $lesson)
                ->with('success', 'A inscrição foi atualizada com sucesso!');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Ocorreu um erro ao atualizar a inscrição: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lesson $lesson, LessonUser $subscription)
    {
        try {
            $subscription->delete();

            return redirect()
                ->route('dashboard.lessons.subscriptions.index', $lesson)
                ->with('success', 'A inscrição foi apagada com sucesso!');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Ocorreu um erro ao apagar a inscrição: ' . $e->getMessage());
        }
    }

    public function subscribe(Request $request, Lesson $lesson)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user) {
            return redirect()->back()->with('error', 'Você precisa estar logado para se inscrever nessa aula!');
        }

        if (!$user->subscriptions()->where('lesson_id', $lesson->id)->exists()) {

            $user->subscriptions()->attach($lesson->id);

            return redirect()->back()->with('success', 'Inscrição realizada com sucesso!');
        }

        return redirect()->back()->with('error', 'Você já está inscrito nesta aula.');
    }
}
