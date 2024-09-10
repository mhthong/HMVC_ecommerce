<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Passport\Passport;

use Illuminate\Http\JsonResponse;



class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Xác thực người dùng
        $request->authenticate();

        // Tạo lại session để bảo mật
        $request->session()->regenerate();

        // Lấy người dùng hiện tại
        $user = $request->user();

        // Xóa tất cả token hiện tại của người dùng
        $user->tokens()->delete();

        // Tạo một token mới với Laravel Passport
        $tokenResult = $user->createToken($user->name . '-token');

        // Định nghĩa guard nếu cần thiết
        $guard = ($user->role === 'admin') ? 'admin' : 'web';


            // Store token and other data in session if needed
            $request->session()->put('token', $tokenResult->accessToken);
            $request->session()->put('token_id', $tokenResult->token->id);
            $request->session()->put('$guard ', $guard);



        // Trả về phản hồi JSON bao gồm token và thông tin khác

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    
    
    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
