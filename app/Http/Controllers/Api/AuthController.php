<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LoginToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
class AuthController extends Controller
{
    /**
     * Request a magic login link.
     * Generates a token, stores it, and sends it via email (or logs it for MVP).
     */
    public function requestLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        // Delete old unused tokens for this user
        LoginToken::where('user_id', $user->id)->whereNull('used_at')->delete();

        // Generate new token
        $token = Str::random(64);
        LoginToken::create([
            'user_id' => $user->id,
            'token' => hash('sha256', $token),
            'expires_at' => now()->addMinutes(15),
        ]);

        // For MVP, we just log the link. In production, send via Mail.
        $loginLink = url("/api/auth/verify?token={$token}&email={$user->email}");
        Log::info("Magic Login Link for {$user->email}: {$loginLink}");
        
        return response()->json([
            'message' => 'Login link sent! Check your email (or logs).',
            'debug_link' => $loginLink // CHECK: Remove this in production!
        ]);
    }

    /**
     * Verify the magic link token and log the user in.
     */
   public function verify(Request $request)
{
    $request->validate([
        'email' => 'required|email|exists:users,email',
        'token' => 'required|string',
    ]);

    $user = User::where('email', $request->email)->first();
    $hashedToken = hash('sha256', $request->token);

    $loginToken = LoginToken::where('user_id', $user->id)
        ->where('token', $hashedToken)
        ->whereNull('used_at')
        ->first();

    if (!$loginToken || !$loginToken->isValid()) {
        return response()->json(['message' => 'Invalid or expired login link.'], 401);
    }

    $loginToken->markAsUsed();

    $user->update([
        'last_login_ip' => $request->ip(),
        'last_login_at' => now(),
    ]);

    // Log the user in
    auth()->login($user, remember: true);

    // Regenerate session ID for security
    session()->regenerate();

    return response()->json([
        'message' => 'Authenticated successfully.',
        'user' => $user->only(['id', 'name', 'email', 'avatar', 'gem_balance', 'is_premium', 'reading_streak']),
    ]);
}
    /**
     * Get the authenticated user.
     */
    public function user(Request $request)
    {
        return response()->json($request->user()->only([
            'id', 'name', 'email', 'avatar', 'gem_balance', 'is_premium', 'premium_expires_at', 'reading_streak'
        ]));
    }

    /**
     * Logout.
     */
    public function logout(Request $request)
    {
        auth()->guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logged out.']);
    }
}