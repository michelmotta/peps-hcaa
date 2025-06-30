<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSuggestionRequest;
use App\Http\Requests\UpdateSuggestionRequest;
use App\Models\Suggestion;
use Exception;
use Illuminate\Support\Facades\Auth;

class SuggestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = request('q')
            ? Suggestion::search(request('q'))->query(fn($q) => $q->orderByDesc('votes'))
            : Suggestion::query()->orderByDesc('votes');

        return view('dashboard.suggestions.index', [
            'suggestions' => $query->paginate(20)->withQueryString(),
            'totalVotes' => Suggestion::sum('votes')
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.suggestions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSuggestionRequest $request)
    {
        try {
            $validatedData = $request->validated();

            $validatedData['user_id'] = Auth::id();

            Suggestion::create($validatedData);

            return redirect()->route('dashboard.suggestions.index')
                ->with('success', 'A sugestão foi cadastrada com sucesso!');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Ocorreu um erro ao cadastrar a sugestão: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Suggestion $suggestion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Suggestion $suggestion)
    {
        return view('dashboard.suggestions.edit', ['suggestion' => $suggestion]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSuggestionRequest $request, Suggestion $suggestion)
    {
        try {
            $validatedData = $request->validated();

            $validatedData['user_id'] = Auth::id();

            $suggestion->update($validatedData);

            return redirect()
                ->route('dashboard.suggestions.index')
                ->with('success', 'A sugestão foi atualizada com sucesso!');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Ocorreu um erro ao atualizar a sugestão: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Suggestion $suggestion)
    {
        try {
            $suggestion->delete();

            return redirect()
                ->route('dashboard.suggestions.index')
                ->with('success', 'A sugestão foi apagada com sucesso!');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Ocorreu um erro ao apagar a sugestão: ' . $e->getMessage());
        }
    }
}
