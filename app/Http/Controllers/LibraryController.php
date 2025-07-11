<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLibraryRequest;
use App\Http\Requests\UpdateLibraryRequest;
use App\Models\File;
use App\Models\Library;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LibraryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = request('q')
            ? Library::search(request('q'))->query(fn($q) => $q->orderByDesc('id'))
            : Library::query()->orderByDesc('id');

        return view('dashboard.libraries.index', [
            'libraries' => $query->paginate(20)->withQueryString(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
     public function create()
    {
        return view('dashboard.libraries.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLibraryRequest $request)
    {
        try {
            $validatedData = $request->validated();

            if ($request->hasFile('file')) {
                $file = File::uploadSingleFile($request->file('file'), Auth::id(), 'uploads/library', true);
                $validatedData['file_id'] = $file->id;
            }

            $validatedData['user_id'] = Auth::id();

            Library::create($validatedData);

            return redirect()
                ->route('dashboard.libraries.index')
                ->with('success', 'O arquivo foi cadastrado com sucesso!');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Ocorreu um erro ao cadastrar o arquivo: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Library $library)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Library $library)
    {
        return view('dashboard.libraries.edit', ['library' => $library]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLibraryRequest $request, Library $library)
    {
        try {
            $validatedData = $request->validated();

            if ($request->hasFile('file')) {
                if ($library->file) {
                    $library->file->delete();
                }
                $file = File::uploadSingleFile($request->file('file'), Auth::id(), 'uploads/library', true);
                $validatedData['file_id'] = $file->id;
            }

            $library->update($validatedData);

            return redirect()
                ->route('dashboard.libraries.index')
                ->with('success', 'O arquivo foi atualizado com sucesso!');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Ocorreu um erro ao atualizar o arquivo: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Library $library)
    {
        try {
            $library->delete();

            return redirect()
                ->route('dashboard.libraries.index')
                ->with('success', 'O arquivo foi apagado com sucesso!');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Ocorreu um erro ao apagar o arquivo: ' . $e->getMessage());
        }
    }
}
