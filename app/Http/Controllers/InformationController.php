<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInformationRequest;
use App\Http\Requests\UpdateInformationRequest;
use App\Models\Information;
use Exception;
use Illuminate\Support\Facades\Auth;

class InformationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = request('q')
            ? Information::search(request('q'))->query(fn($q) => $q->orderByDesc('id'))
            : Information::query()->orderByDesc('id');

        return view('dashboard.information.index', [
            'information' => $query->paginate(20)->withQueryString(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.information.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInformationRequest $request)
    {
        try {
            $validatedData = $request->validated();

            $validatedData['user_id'] = Auth::id();

            Information::create($validatedData);

            return redirect()->route('dashboard.information.index')
                ->with('success', 'A informação foi cadastrada com sucesso!');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Ocorreu um erro ao cadastrar a informação: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Information $information)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Information $information)
    {
        return view('dashboard.information.edit', ['information' => $information]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInformationRequest $request, Information $information)
    {
        try {
            $validatedData = $request->validated();

            $validatedData['user_id'] = Auth::id();

            $information->update($validatedData);

            return redirect()
                ->route('dashboard.information.index')
                ->with('success', 'A informação foi atualizada com sucesso!');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Ocorreu um erro ao atualizar a informação: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Information $information)
    {
        try {
            $information->delete();

            return redirect()
                ->route('dashboard.information.index')
                ->with('success', 'A informação foi apagada com sucesso!');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Ocorreu um erro ao apagar a informação: ' . $e->getMessage());
        }
    }
}
