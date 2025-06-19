<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDoubtRequest;
use App\Http\Requests\UpdateDoubtRequest;
use App\Models\Doubt;
use App\Models\Lesson;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

class DoubtController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Lesson $lesson)
    {
        $searchTerm = request('q');

        $query = Doubt::query()
            ->when(
                $searchTerm,
                fn($q) => Doubt::search($searchTerm)->query(fn($q) => $q->orderByDesc('id')),
                fn($q) => $q->orderByDesc('id')
            );

        $doubts = $query->where('lesson_id', $lesson->id)
            ->paginate(20)
            ->withQueryString();

        return view('dashboard.doubts.index', compact('lesson', 'doubts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Lesson $lesson)
    {
        return view('dashboard.doubts.create', ['lesson' => $lesson]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDoubtRequest $request, Lesson $lesson)
    {
        try {
            $validatedData = $request->validated();

            $validatedData['lesson_id'] = $lesson->id;
            $validatedData['user_id'] = Auth::id();

            if (!empty(trim(strip_tags($validatedData['description'])))) {
                $validatedData['answered'] = true;
                $validatedData['answered_at'] = Carbon::now();
            } else {
                $validatedData['answered'] = false;
                $validatedData['answered_at'] = null;
            }

            Doubt::create($validatedData);

            return redirect()
                ->route('dashboard.lessons.doubts.index', $lesson)
                ->with('success', 'A dúvida foi cadastrada com sucesso!');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Ocorreu um erro ao cadastrar a dúvida: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Doubt $doubt)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lesson $lesson, Doubt $doubt)
    {
        return view('dashboard.doubts.edit', ['lesson' => $lesson, 'doubt' => $doubt]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDoubtRequest $request, Lesson $lesson, Doubt $doubt)
    {
        try {
            $validatedData = $request->validated();

            $validatedData['lesson_id'] = $lesson->id;
            $validatedData['user_id'] = Auth::id();

            if (!empty(trim(strip_tags($validatedData['description'])))) {
                $validatedData['answered'] = true;
                $validatedData['answered_at'] = Carbon::now();
            } else {
                $validatedData['answered'] = false;
                $validatedData['answered_at'] = null;
            }

            $doubt->update($validatedData);

            return redirect()
                ->route('dashboard.lessons.doubts.index', $lesson)
                ->with('success', 'A dúvida foi atualizada com sucesso!');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Ocorreu um erro ao cadastrar a dúvida: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lesson $lesson, Doubt $doubt)
    {
        try {
            $doubt->delete();

            return redirect()
                ->route('dashboard.lessons.doubts.index', $lesson)
                ->with('success', 'A dúvida foi apagada com sucesso!');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Ocorreu um erro ao excluir a dúvida: ' . $e->getMessage());
        }
    }


    public function doubtCreate(Request $request, Lesson $lesson)
    {
        $validatedData = $request->validate([
            'doubt' => 'required',
        ]);

        try {
            $doubtText = trim(strip_tags($validatedData['doubt']));

            $doubt = Doubt::create([
                'doubt' => $doubtText,
                'lesson_id' => $lesson->id,
                'user_id' => Auth::id(),
                'answered' => false,
                'answered_at' => null,
            ]);

            // Carrega a relação do usuário
            $doubt->load('user');

            return response()->json([
                'status' => 'success',
                'doubt' => [
                    'doubt' => $doubt->doubt,
                    'created_at_formatted' => $doubt->created_at_formatted,
                    'answered_at_formatted' => $doubt->answered_at_formatted,
                    'user' => [
                        'name' => $doubt->user->name,
                    ],
                ],
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
