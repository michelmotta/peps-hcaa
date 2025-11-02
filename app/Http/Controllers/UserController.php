<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\File;
use App\Models\Profile;
use App\Models\Sector;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $relations = [
            'file',
            'profiles',
            'logins' => fn($q) => $q->latest()->limit(5),
        ];

        $searchTerm = $request->input('q');
        $status = $request->input('status');

        $query = $searchTerm
            ? User::search($searchTerm)
            ->query(fn($q) => $q->with($relations)
                ->orderBy('active', 'desc')
                ->orderByDesc('id'))
            : User::query()
            ->with($relations)
            ->orderBy('active', 'desc')
            ->orderByDesc('id');

        if ($status === 'active') {
            $query->where('active', true);
        } elseif ($status === 'inactive') {
            $query->where('active', false);
        }

        return view('dashboard.users.index', [
            'users' => $query->paginate(15)->withQueryString(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.users.create', ['profiles' => Profile::get(), 'user' => null, 'sectors' => Sector::orderByDesc('name')->get()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        try {
            $validatedData = $request->validated();

            if ($request->hasFile('file')) {
                $file = File::uploadSingleFile($request->file('file'), Auth::id(), 'uploads/users');
                $validatedData['file_id'] = $file->id;
            }

            $validatedData['active'] = true;

            $user = User::create(Arr::except($validatedData, ['profiles']));

            if ($request->filled('profiles')) {
                $user->profiles()->sync($request->input('profiles'));
            }

            return redirect()
                ->route('dashboard.users.index')
                ->with('success', 'O usuário foi cadastrado com sucesso!');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Ocorreu um erro ao cadastrar o usuário: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $user->load('profiles');
        $profiles = Profile::all();
        $sectors = Sector::orderByDesc('name')->get();

        return view('dashboard.users.edit', compact('user', 'profiles', 'sectors'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        try {
            $validatedData = $request->validated();

            if ($request->hasFile('file')) {
                if ($user->file) {
                    $user->file->delete();
                }
                $file = File::uploadSingleFile($request->file('file'), Auth::id(), 'uploads/users');
                $validatedData['file_id'] = $file->id;
            }

            $user->update(Arr::except($validatedData, ['profiles']));

            $user->profiles()->sync($request->input('profiles', []));

            return redirect()
                ->route('dashboard.users.index')
                ->with('success', 'O usuário foi atualizado com sucesso!');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Ocorreu um erro ao atualizar o usuário: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try {
            $user->delete();

            return redirect()
                ->route('dashboard.users.index')
                ->with('success', 'O usuário foi apagado com sucesso!');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Ocorreu um erro ao apagar o usuário: ' . $e->getMessage());
        }
    }

    public function toggleActiveUser(Request $request, User $user)
    {
        try {
            $user->active = !$user->active;
            $user->save();

            return redirect()
                ->back()
                ->with('success', 'O usuário foi alterado com sucesso!');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Ocorreu um erro ao alterar o usuário: ' . $e->getMessage());
        }
    }

    public function searchUser(Request $request)
    {
        $search = request('q');

        $users = User::search($search)
            ->take(10)
            ->get();

        return $users->map(fn($user) => [
            'value' => $user->id,
            'text'  => $user->name,
        ])->values();
    }

    public function searchProfessor(Request $request)
    {
        $search = request('q');

        $teachers = User::search($search)
            ->query(function ($eloquentBuilder) {
                $eloquentBuilder->whereHas('profiles', function ($query) {
                    $query->where('name', 'Professor');
                });
            })
            ->take(10)
            ->get();

        return $teachers->map(fn($teacher) => [
            'value' => $teacher->id,
            'text'  => $teacher->name,
        ])->values();
    }
}
