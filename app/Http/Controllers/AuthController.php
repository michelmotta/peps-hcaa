<?php

namespace App\Http\Controllers;

use App\Enums\ProfileEnum;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StorePerfilRequest;
use App\Http\Requests\UpdatePerfilRequest;
use App\Mail\WelcomeMail;
use App\Models\File;
use App\Models\User;
use App\Models\UserLogin;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Handle user login.
     *
     * @param  \App\Http\Requests\LoginUserRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function login(LoginUserRequest $request): RedirectResponse
    {
        $credentials = $request->validated();

        $user = User::where('username', $credentials['username'])->first();

        if (!$user) {
            return back()->with('error', 'O usuário informado está incorreto.');
        }

        if (!$user->active) {
            return back()->with('error', 'O usuário informado está inativo. Entre em contato com um coordenador.');
        }

        if (!Auth::attempt($credentials)) {
            return back()->with('error', 'A senha informada está incorreta.');
        }

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

            Mail::to($user->email)->send(new WelcomeMail($user));

            return redirect()
                ->route('login')
                ->with('success', 'O usuário foi cadastrado com sucesso. Aguarde a liberação de acesso.');
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

    public function forgotPassword(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'email' => 'required|email|exists:users,email',
            ], [
                'email.required' => 'O campo de e-mail é obrigatório.',
                'email.email'    => 'Informe um e-mail válido.',
                'email.exists'   => 'Não encontramos um usuário com esse e-mail.',
            ]);

            $status = Password::sendResetLink($request->only('email'));

            return $status === Password::RESET_LINK_SENT
                ? back()->with(['status' => 'Enviamos um link de redefinição para seu e-mail!'])
                : back()->withErrors(['email' => 'Não foi possível enviar o link de redefinição.']);
        }

        return view('web.forgot_password');
    }

    public function resetPassword(Request $request, $token = null)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'token' => 'required',
                'email' => 'required|email',
                'password' => ['required', 'confirmed'],
            ], [
                'password.required' => 'O campo de nova senha é obrigatório.',
                'password.confirmed' => 'A confirmação da senha não corresponde.',
            ]);

            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) {
                    $user->forceFill([
                        'password' => Hash::make($password),
                        'remember_token' => Str::random(60),
                    ])->save();
                }
            );

            return $status === Password::PASSWORD_RESET
                ? redirect()->route('login')->with('success', 'Senha redefinida com sucesso. Você já pode fazer login.')
                : back()->withErrors(['email' => 'Este token de redefinição de senha é inválido ou já expirou.']);
        }

        return view('web.reset_password', [
            'token' => $token,
            'email' => $request->email
        ]);
    }
}
