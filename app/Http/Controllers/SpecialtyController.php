<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSpecialtyRequest;
use App\Http\Requests\UpdateSpecialtyRequest;
use App\Models\File;
use App\Models\Specialty;
use Exception;
use Illuminate\Support\Facades\Auth;

class SpecialtyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = request('q')
            ? Specialty::search(request('q'))->query(fn($q) => $q->orderByDesc('id'))
            : Specialty::query()->orderByDesc('id');

        return view('dashboard.specialties.index', [
            'specialties' => $query->paginate(20)->withQueryString(),
        ]);
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

            $validatedData['user_id'] = Auth::id();

            Specialty::create($validatedData);

            return redirect()
                ->route('dashboard.specialties.index')
                ->with('success', 'A especialidade foi cadastrada com sucesso!');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Ocorreu um erro ao cadastrar a especialidade: ' . $e->getMessage());
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

            return redirect()
                ->route('dashboard.specialties.index')
                ->with('success', 'A especialidade foi atualizada com sucesso!');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Ocorreu um erro ao atualizar a especialidade: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Specialty $specialty)
    {
        try {
            $specialty->delete();

            return redirect()
                ->route('dashboard.specialties.index')
                ->with('success', 'A especialidade foi apagada com sucesso!');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Ocorreu um erro ao apagar a especialidade: ' . $e->getMessage());
        }
    }
}
