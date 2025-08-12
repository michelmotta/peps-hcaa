<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGuidebookRequest;
use App\Http\Requests\UpdateGuidebookRequest;
use App\Models\Guidebook;
use App\Models\GuidebookCategory;
use Exception;
use Illuminate\Http\Request;

class GuidebookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $categories = GuidebookCategory::orderBy('name')->get();

        if ($request->filled('q')) {
            $guidebooksQuery = Guidebook::search($request->q);
        } else {
            $guidebooksQuery = Guidebook::query()->latest();
        }

        if ($request->filled('category_id')) {
            $guidebooksQuery->where('guidebook_category_id', $request->category_id);
        }

        $guidebooks = $guidebooksQuery->paginate(10)->withQueryString();

        return view('dashboard.guidebooks.index', [
            'categories' => $categories,
            'guidebooks' => $guidebooks,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = GuidebookCategory::orderBy('name')->get();
        return view('dashboard.guidebooks.create', [
            'guidebook' => null,
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGuidebookRequest $request)
    {
        try {
            $validatedData = $request->validated();

            Guidebook::create($validatedData);

            return redirect()->route('dashboard.guidebooks.index')
                ->with('success', 'O manual foi cadastrado com sucesso!');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Ocorreu um erro ao cadastrar o manual: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Guidebook $guidebook)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Guidebook $guidebook)
    {
        $categories = GuidebookCategory::orderBy('name')->get();
        return view('dashboard.guidebooks.edit', [
            'guidebook' => $guidebook,
            'categories' => $categories,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGuidebookRequest $request, Guidebook $guidebook)
    {
        try {
            $validatedData = $request->validated();

            $guidebook->update($validatedData);

            return redirect()
                ->route('dashboard.guidebooks.index')
                ->with('success', 'O manual foi atualizado com sucesso!');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Ocorreu um erro ao atualizar o manual: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Guidebook $guidebook)
    {
        try {
            $guidebook->delete();

            return redirect()
                ->route('dashboard.guidebooks.index')
                ->with('success', 'O manual foi apagado com sucesso!');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Ocorreu um erro ao apagar o manual: ' . $e->getMessage());
        }
    }
}
