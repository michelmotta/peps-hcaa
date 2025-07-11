<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGuidebookCategoryRequest;
use App\Http\Requests\UpdateGuidebookCategoryRequest;
use App\Models\GuidebookCategory;
use Exception;
use Illuminate\Http\Request;

class GuidebookCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $baseQuery = GuidebookCategory::query()
            ->withCount('guidebooks')
            ->latest();

        $query = $request->filled('q')
            ? GuidebookCategory::search($request->q)->query(fn($query) => $query->setQuery($baseQuery))
            : $baseQuery;

        $categories = $query->paginate(15)->withQueryString();

        return view('dashboard.guidebook-categories.index', [
            'categories' => $categories,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.guidebook-categories.create', [
            'category' => null,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGuidebookCategoryRequest $request)
    {
        try {
            GuidebookCategory::create($request->validated());

            return redirect()->route('dashboard.guidebook-categories.index')
                ->with('success', 'A categoria foi cadastrada com sucesso!');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Ocorreu um erro: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GuidebookCategory $guidebookCategory)
    {
        return view('dashboard.guidebook-categories.edit', [
            'category' => $guidebookCategory,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGuidebookCategoryRequest $request, GuidebookCategory $guidebookCategory)
    {
        try {
            $guidebookCategory->update($request->validated());

            return redirect()->route('dashboard.guidebook-categories.index')
                ->with('success', 'A categoria foi atualizada com sucesso!');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Ocorreu um erro: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GuidebookCategory $guidebookCategory)
    {
        try {
            if ($guidebookCategory->guidebooks()->count() > 0) {
                return redirect()->back()->with('error', 'Não é possível apagar uma categoria que contém manuais.');
            }

            $guidebookCategory->delete();

            return redirect()->route('dashboard.guidebook-categories.index')
                ->with('success', 'A categoria foi apagada com sucesso!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Ocorreu um erro: ' . $e->getMessage());
        }
    }
}
