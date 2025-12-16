<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\ValidatesRecaptcha;
use App\Models\User;
use App\Models\SubscriptionPackage;
use App\Services\SubscriptionService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\AccountActivated;
use App\Helpers\TranslationHelper;

class RegistrationController extends Controller
{
    use ValidatesRecaptcha;
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function step1Form()
    {
        return view('auth.register-step1');
    }

    public function step1Post(Request $request)
    {
        // Validate reCAPTCHA if enabled
        $this->validateRecaptcha($request);

        $data = $request->validate([
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
        ]);

        $request->session()->put('reg.email', $data['email']);

        return redirect()->route('register.step2');
    }

    public function step2Form(Request $request)
    {
        $email = $request->session()->get('reg.email');
        if (! $email) {
            return redirect()->route('register.step1');
        }

        return view('auth.register-step2', compact('email'));
    }

    public function step2Post(Request $request)
    {
        $email = $request->session()->get('reg.email');
        if (! $email) {
            return redirect()->route('register.step1');
        }

        // Validate reCAPTCHA if enabled
        $this->validateRecaptcha($request);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^\d{6,15}$/'],
            'country_code' => ['nullable', 'string', 'max:4', 'regex:/^[1-9]\d{0,3}$/'],
        ]);

        // Combine country code with phone if provided
        $phoneNumber = $this->formatPhoneNumber($data['phone'] ?? null, $data['country_code'] ?? null);

        $user = User::create([
            'name' => $data['name'],
            'email' => $email,
            'password' => Hash::make($data['password']),
            'phone' => $phoneNumber,
            'is_active' => true,
        ]);

        // Dispatch Registered event so AppServiceProvider listener handles role/subscription
        event(new Registered($user));

        // Refresh user to pick up any changes (is_active, subscription)
        $user->refresh();

        // If user is active (e.g., auto-subscribed to a free package) => login and send activation email
        if ($user->is_active) {
            return $this->handleActiveUserRegistration($user);
        }

        // Otherwise store user id in session and redirect to package selection (step3)
        $request->session()->put('reg.user_id', $user->id);
        return redirect()->route('register.step3');
    }

    public function step3Form(Request $request)
    {
        $userId = $request->session()->get('reg.user_id');
        if (! $userId) {
            return redirect()->route('register.step1');
        }

        $packages = SubscriptionPackage::where('is_free', false)->get();

        return view('auth.register-step3', compact('packages'));
    }

    public function step3Post(Request $request, SubscriptionService $service)
    {
        $userId = $request->session()->get('reg.user_id');
        if (! $userId) {
            return redirect()->route('register.step1');
        }

        $data = $request->validate([
            'package_id' => ['required', 'exists:subscription_packages,id'],
        ]);

        $user = User::findOrFail($userId);

        $service->subscribeUserToPackage($user, $data['package_id']);

        // user remains inactive until payment confirmed
        return redirect()->route('login')->with('success', TranslationHelper::get('messages.payment_pending'));
    }

    /**
     * Format phone number with country code
     */
    protected function formatPhoneNumber(?string $phone, ?string $countryCode): ?string
    {
        if (empty($phone)) {
            return null;
        }

        $countryCode = !empty($countryCode) ? '+' . $countryCode : '+20';
        return $countryCode . $phone;
    }

    /**
     * Handle active user registration (auto-subscribed to free package)
     */
    protected function handleActiveUserRegistration($user)
    {
        Auth::login($user);

        try {
            Mail::to($user->email)->send(new AccountActivated($user));
        } catch (\Throwable $e) {
            // swallow mail errors, but log if needed
        }

        return redirect()->route('user.products.create.step1')
            ->with('success', TranslationHelper::get('messages.registration_success'));
    }
}
