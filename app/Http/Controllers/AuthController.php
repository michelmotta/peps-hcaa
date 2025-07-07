<?php

namespace App\Http\Controllers;

use App\Enums\ProfileEnum;
use App\Http\Requests\StorePerfilRequest;
use App\Http\Requests\UpdatePerfilRequest;
use App\Models\File;
use App\Models\User;
use App\Models\UserLogin;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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

        $user = User::where('username', $credentials['username'])->first();

        if (!$user) {
            return back()->with('error', 'O usuário informado está incorreto.');
        }

        if (!$user->active) {
            return back()->with('error', 'O usuário informado está inativo. Entre em contato com o coordenador.');
        }

        if (!Hash::check($credentials['password'], $user->password)) {
            return back()->with('error', 'A senha informada está incorreta.');
        }

        Auth::login($user);
        $request->session()->regenerate();

        UserLogin::create([
            'user_id' => $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
        ]);

        $user->load('profiles');

        return $user->hasAnyProfile(['Coordenador', 'Professor'])
            ? redirect()->intended('dashboard')
            : redirect()->intended('minhas-aulas');
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
