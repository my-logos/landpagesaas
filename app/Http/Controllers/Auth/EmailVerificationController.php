<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class EmailVerificationController extends Controller
{
    /**
     * Verify user email
     */
    public function verify(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        // Verify hash matches user's email
        if (!hash_equals((string) $hash, sha1($user->email))) {
            abort(403, 'Invalid verification link');
        }

        // Check if URL signature is valid
        if (!URL::hasValidSignature($request)) {
            abort(403, 'Verification link has expired');
        }

        // Mark email as verified
        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return redirect()->route('login')
            ->with('success', 'تم التحقق من بريدك الإلكتروني بنجاح. يمكنك الآن تسجيل الدخول.');
    }
}
