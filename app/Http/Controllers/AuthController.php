<?php

namespace App\Http\Controllers;

use App\Enums\ProfileEnum;
use App\Http\Requests\StorePerfilRequest;
use App\Http\Requests\UpdatePerfilRequest;
use App\Models\File;
use App\Models\User;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Handle user login.
     *
     * @param  \App\Http\Requests\LoginUserRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        if (Auth::attempt(['username' => $credentials['username'], 'password' => $credentials['password'], 'active' => true])) {
            $request->session()->regenerate();

            $user = Auth::user()->load('profiles');

            session(['user_profiles' => $user->profiles->pluck('name')->toArray()]);

            if ($user->hasAnyProfile(['Coordenador', 'Professor'])) {
                return redirect()->intended('dashboard');
            }

            return redirect()->intended('minhas-aulas');
        }

        return back()->with('error', 'Usuário inativo ou as credenciais não conferem.');
    }

    /**
     * Handle user logout.
     *
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('web.index');
    }

    public function perfilCreate(StorePerfilRequest $request)
    {
        try {
            $validatedData = $request->validated();

            $file = null;
            if ($request->hasFile('file')) {
                $file = File::uploadSingleFile($request->file('file'), null, 'uploads/users');
                $validatedData['file_id'] = $file->id;
            }

            $user = User::create($validatedData);

            if ($file !== null) {
                $file->user_id = $user->id;
                $file->update();
            }

            return redirect()
                ->route('web.login')
                ->with('success', 'O usuário foi cadastrado com sucesso!');
        } catch (Exception $e) {
            return redirect()
                ->route('web.perfil')
                ->with('error', 'Ocorreu um erro ao cadastrar o usuário: ' . $e->getMessage());
        }
    }

    public function perfilUpdate(UpdatePerfilRequest $request, User $user)
    {
        try {
            $validatedData = $request->validated();

            if ($user->id !== Auth::id()) {
                return back()->withErrors(['error' => 'Você pode alterar apenas os seus próprios dados.']);
            }

            if ($request->hasFile('file')) {
                if ($user->file) {
                    $user->file->delete();
                }
                $file = File::uploadSingleFile($request->file('file'), Auth::id(), 'uploads/users');
                $validatedData['file_id'] = $file->id;
            }

            $updated = $user->update($validatedData);

            return redirect()
                ->route('web.perfil')
                ->with('success', 'O usuário foi atualizado com sucesso!');
        } catch (Exception $e) {
            return redirect()
                ->route('web.perfil')
                ->with('error', 'Ocorreu um erro ao atualizar o usuário: ' . $e->getMessage());
        }
    }
}
