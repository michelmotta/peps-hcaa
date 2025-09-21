<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSectorRequest;
use App\Http\Requests\UpdateSectorRequest;
use App\Models\Sector;
use Exception;
use Illuminate\Http\Request;

class SectorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = request('q')
            ? Sector::search(request('q'))->query(fn($q) => $q->orderByDesc('id'))
            : Sector::query()->orderByDesc('id');

        return view('dashboard.sectors.index', [
            'sectors' => $query->paginate(20)->withQueryString(),
        ]);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.sectors.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSectorRequest $request)
    {
        try {
            $validatedData = $request->validated();

            Sector::create($validatedData);

            return redirect()
                ->route('dashboard.sectors.index')
                ->with('success', 'O setor foi cadastrado com sucesso!');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Ocorreu um erro ao cadastrar o setor: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Sector $sector)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sector $sector)
    {
        return view('dashboard.sectors.edit', ['sector' => $sector]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSectorRequest $request, Sector $sector)
    {
        try {
            $validatedData = $request->validated();

            $sector->update($validatedData);

            return redirect()
                ->route('dashboard.sectors.index')
                ->with('success', 'O setor foi atualizado com sucesso!');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Ocorreu um erro ao atualizar o setor: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sector $sector)
    {
        try {
            $sector->delete();

            return redirect()
                ->route('dashboard.sectors.index')
                ->with('success', 'O setor foi apagado com sucesso!');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Ocorreu um erro ao apagar o setor: ' . $e->getMessage());
        }
    }
}
