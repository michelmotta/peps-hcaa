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
        $selectedId = $request->input('selected');

        if ($searchTerm) {
            $ids = Specialty::search($searchTerm)
                ->where('parent_id', null)
                ->keys();

            $query = Specialty::with('file')->whereIn('id', $ids);
        } else {
            $query = Specialty::with('file')->whereNull('parent_id');
        }
        $specialties = $query->orderByDesc('id')->paginate(15)->withQueryString();

        $selectedSpecialty = null;
        if ($selectedId) {
            $selectedSpecialty = Specialty::with('children', 'file')->find($selectedId);
        } elseif ($specialties->isNotEmpty()) {
            if (is_null($selectedId) && is_null($searchTerm)) {
                return redirect()->route('dashboard.specialties.index', ['selected' => $specialties->first()->id]);
            }
        }

        return view('dashboard.specialties.index', compact('specialties', 'selectedSpecialty'));
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
            $dados = $request->validated();

            // Upload do arquivo, se houver
            if ($request->hasFile('file')) {
                $arquivo = File::uploadSingleFile($request->file('file'), Auth::id(), 'uploads/specialties');
                $dados['file_id'] = $arquivo->id;
            }

            $specialty = Specialty::create($dados);

            collect(json_decode($request->input('subspecialties', '[]'), true))
                ->pluck('value')
                ->filter()
                ->unique()
                ->each(fn($nome) => $specialty->children()->create(['name' => $nome]));

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
            $dados = $request->validated();

            // Atualiza ou substitui o arquivo
            if ($request->hasFile('file')) {
                $specialty->file?->delete();
                $arquivo = File::uploadSingleFile($request->file('file'), Auth::id(), 'uploads/specialties');
                $dados['file_id'] = $arquivo->id;
            }

            $specialty->update($dados);

            $novasTags = collect(json_decode($request->input('subspecialties', '[]'), true))
                ->pluck('value')
                ->filter()
                ->unique()
                ->values();

            $tagsAtuais = $specialty->children->pluck('name');

            $specialty->children()->whereIn('name', $tagsAtuais->diff($novasTags))->delete();

            $novasTags->diff($tagsAtuais)->each(function ($tag) use ($specialty) {
                $specialty->children()->create(['name' => $tag]);
            });

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
