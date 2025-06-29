<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSpecialtyRequest;
use App\Http\Requests\UpdateSpecialtyRequest;
use App\Models\File;
use App\Models\Specialty;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SpecialtyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $searchTerm = $request->input('q');

        if ($searchTerm) {
            $searchResults = Specialty::search($searchTerm)->get();

            $parentIds = $searchResults->pluck('parent_id')->filter()->unique();
            $matchedIds = $searchResults->pluck('id');

            $finalParentIds = $matchedIds->merge($parentIds)->unique();

            $specialties = Specialty::with('children')
                ->whereIn('id', $finalParentIds)
                ->whereNull('parent_id')
                ->orderByDesc('id')
                ->paginate(20)
                ->withQueryString();
        } else {
            $specialties = Specialty::with('children')
                ->whereNull('parent_id')
                ->orderByDesc('id')
                ->paginate(20);
        }

        return view('dashboard.specialties.index', compact('specialties'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.specialties.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSpecialtyRequest $request)
    {
        try {
            $validatedData = $request->validated();

            if ($request->hasFile('file')) {
                $file = File::uploadSingleFile($request->file('file'), Auth::id(), 'uploads/specialties');
                $validatedData['file_id'] = $file->id;
            }

            $specialty = Specialty::create($validatedData);

            if ($request->filled('subspecialties')) {
                foreach ($request->input('subspecialties') as $sub) {
                    $specialty->children()->create(['name' => $sub]);
                }
            }

            return redirect()->route('dashboard.specialties.index')
                ->with('success', 'A especialidade foi cadastrada com sucesso!');
        } catch (Exception $e) {
            return back()->with('error', 'Erro ao cadastrar: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Specialty $specialty)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Specialty $specialty)
    {
        return view('dashboard.specialties.edit', ['specialty' => $specialty]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSpecialtyRequest $request, Specialty $specialty)
    {
        try {
            $validatedData = $request->validated();

            if ($request->hasFile('file')) {
                if ($specialty->file) {
                    $specialty->file->delete();
                }
                $file = File::uploadSingleFile($request->file('file'), Auth::id(), 'uploads/specialties');
                $validatedData['file_id'] = $file->id;
            }

            $specialty->update($validatedData);

            // Remover subespecialidades antigas e adicionar novas
            $specialty->children()->delete();

            if ($request->filled('subspecialties')) {
                foreach ($request->input('subspecialties') as $sub) {
                    $specialty->children()->create(['name' => $sub]);
                }
            }

            return redirect()->route('dashboard.specialties.index')
                ->with('success', 'A especialidade foi atualizada com sucesso!');
        } catch (Exception $e) {
            return back()->with('error', 'Erro ao atualizar: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Specialty $specialty)
    {
        try {
            $specialty->children()->delete();
            $specialty->delete();

            return redirect()
                ->route('dashboard.specialties.index')
                ->with('success', 'A especialidade e suas subespecialidades foram apagadas com sucesso!');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Ocorreu um erro ao apagar a especialidade: ' . $e->getMessage());
        }
    }
}
