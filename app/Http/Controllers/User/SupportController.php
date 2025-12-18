<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\HasLocaleAndTranslation;
use App\Http\Controllers\Concerns\HasSubscriptionHelper;
use App\Models\SupportTicket;
use App\Models\SupportFaq;
use App\Models\UserSupportTicketCount;
use App\Models\User;
use App\Services\SettingsService;
use App\Mail\NewSupportTicketNotification;
use App\Mail\SupportTicketReplyNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Helpers\TranslationHelper;

class SupportController extends Controller
{
    use HasLocaleAndTranslation, HasSubscriptionHelper;

    /**
     * Display support center page
     */
    public function index(Request $request)
    {
        $user = $request->user();
        ['locale' => $locale, 'dir' => $dir, 't' => $t] = $this->getLocaleData();

        // Get user's subscription and package
        $subscriptionData = $this->getSubscriptionData($user);
        $currentPackage = $subscriptionData['package'];

        // Get current month ticket count
        $currentMonthCount = UserSupportTicketCount::getCurrentMonthCount($user->id);

        // Get ticket limit from package
        $ticketLimit = $currentPackage ? ($currentPackage->monthly_support_tickets_limit ?? 0) : 0;
        $canCreateTicket = $ticketLimit === null || $currentMonthCount < $ticketLimit;

        // Get active FAQ
        $faqs = SupportFaq::active()->ordered()->get();

        // Get user's tickets
        $tickets = SupportTicket::where('user_id', $user->id)
            ->with(['replies.user'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get social media links from landing page settings
        $settingsService = app(SettingsService::class);
        $socialMediaLinksJson = $settingsService->get('social_media_links', '[]');
        $socialMediaLinks = json_decode($socialMediaLinksJson, true);

        if (!is_array($socialMediaLinks)) {
            $socialMediaLinks = [];
        }

        // Get phone and email from general settings
        $sitePhone = $settingsService->get('site_phone', null);
        $siteEmail = $settingsService->get('site_email', null);

        // Filter enabled links and format them
        $socialMedia = [];
        foreach ($socialMediaLinks as $link) {
            if (!empty($link['enabled']) && !empty($link['url']) && $link['url'] !== '#') {
                $platform = strtolower($link['platform'] ?? '');
                $url = $link['url'];

                // Extract display text from URL
                $displayText = $url;
                if (filter_var($url, FILTER_VALIDATE_URL)) {
                    $parsed = parse_url($url);
                    $displayText = $parsed['host'] ?? $url;
                    // Remove www. and common domain parts
                    $displayText = preg_replace('/^www\./', '', $displayText);
                } elseif (strpos($url, '@') !== false || strpos($url, 'mailto:') !== false) {
                    $displayText = str_replace('mailto:', '', $url);
                }

                // Ensure URL is clickable
                if (!preg_match('/^https?:\/\//', $url) && strpos($url, 'mailto:') === false) {
                    if (strpos($url, '@') !== false) {
                        $url = 'mailto:' . $url;
                    } else {
                        $url = 'https://' . $url;
                    }
                }

                $socialMedia[] = [
                    'platform' => $platform,
                    'url' => $url,
                    'display' => $displayText,
                    'icon' => $this->getSocialIcon($platform),
                ];
            }
        }

        // Check if WhatsApp already exists in social media links
        $hasWhatsApp = false;
        foreach ($socialMedia as $item) {
            if ($item['platform'] === 'whatsapp') {
                $hasWhatsApp = true;
                break;
            }
        }

        // Add phone number with WhatsApp link if available and not already in social media
        if (!empty($sitePhone) && !$hasWhatsApp) {
            // Clean phone number (remove spaces, dashes, parentheses)
            $cleanPhone = preg_replace('/[^0-9+]/', '', $sitePhone);
            // Ensure phone starts with + for WhatsApp
            if (strpos($cleanPhone, '+') !== 0) {
                $cleanPhone = '+' . ltrim($cleanPhone, '0');
            }
            $whatsappUrl = 'https://wa.me/' . preg_replace('/[^0-9]/', '', $cleanPhone);

            $socialMedia[] = [
                'platform' => 'whatsapp',
                'url' => $whatsappUrl,
                'display' => $sitePhone,
                'icon' => 'fa-brands fa-whatsapp',
            ];
        }

        // Check if email already exists in social media links
        $hasEmail = false;
        foreach ($socialMedia as $item) {
            if ($item['platform'] === 'email' || strpos($item['url'], 'mailto:') !== false) {
                $hasEmail = true;
                break;
            }
        }

        // Add email if not already in social media links
        if (!empty($siteEmail) && !$hasEmail) {
            $socialMedia[] = [
                'platform' => 'email',
                'url' => 'mailto:' . $siteEmail,
                'display' => $siteEmail,
                'icon' => 'fa-solid fa-envelope',
            ];
        }

        return view('user.support.index', compact(
            'faqs',
            'tickets',
            'currentMonthCount',
            'ticketLimit',
            'canCreateTicket',
            'socialMedia',
            'locale',
            'dir',
            't'
        ));
    }

    /**
     * Get social media icon class
     */
    private function getSocialIcon(string $platform): string
    {
        $icons = [
            'facebook' => 'fa-brands fa-facebook',
            'twitter' => 'fa-brands fa-twitter',
            'instagram' => 'fa-brands fa-instagram',
            'linkedin' => 'fa-brands fa-linkedin',
            'youtube' => 'fa-brands fa-youtube',
            'tiktok' => 'fa-brands fa-tiktok',
            'whatsapp' => 'fa-brands fa-whatsapp',
            'telegram' => 'fa-brands fa-telegram',
            'snapchat' => 'fa-brands fa-snapchat',
            'email' => 'fa-solid fa-envelope',
        ];

        return $icons[strtolower($platform)] ?? 'fa-solid fa-link';
    }

    /**
     * Search FAQ
     */
    public function searchFaq(Request $request)
    {
        $locale = app()->getLocale();
        $searchTerm = $request->input('q', '');

        if (empty($searchTerm)) {
            return response()->json(['results' => []]);
        }

        $faqs = SupportFaq::active()->ordered()->get();
        $results = [];

        if ($faqs->isEmpty()) {
            return response()->json(['results' => []]);
        }

        $searchTermLower = mb_strtolower(trim($searchTerm));

        foreach ($faqs as $faq) {
            $question = $locale === 'ar' ? $faq->question_ar : $faq->question_en;
            $answer = $locale === 'ar' ? $faq->answer_ar : $faq->answer_en;
            $keywords = $locale === 'ar' ? ($faq->keywords_ar ?? []) : ($faq->keywords_en ?? []);

            if (empty($question)) {
                continue;
            }

            $questionLower = mb_strtolower($question);
            $answerLower = mb_strtolower($answer ?? '');

            // Check if search term matches question, answer, or keywords
            $questionMatch = mb_strpos($questionLower, $searchTermLower) !== false;
            $answerMatch = !empty($answer) && mb_strpos($answerLower, $searchTermLower) !== false;
            $keywordMatch = false;

            if (is_array($keywords) && !empty($keywords)) {
                foreach ($keywords as $keyword) {
                    if (!empty($keyword) && mb_strpos(mb_strtolower($keyword), $searchTermLower) !== false) {
                        $keywordMatch = true;
                        break;
                    }
                }
            }

            if ($questionMatch || $answerMatch || $keywordMatch) {
                $results[] = [
                    'id' => $faq->id,
                    'question' => $question,
                    'answer' => $answer ?? '',
                ];
            }
        }

        return response()->json(['results' => $results]);
    }

    /**
     * Get FAQ answer
     */
    public function getFaqAnswer(Request $request, $id)
    {
        $locale = app()->getLocale();
        $faq = SupportFaq::active()->findOrFail($id);

        return response()->json([
            'success' => true,
            'question' => $locale === 'ar' ? $faq->question_ar : $faq->question_en,
            'answer' => $locale === 'ar' ? $faq->answer_ar : $faq->answer_en,
        ]);
    }

    /**
     * Show create ticket form
     */
    public function create(Request $request)
    {
        $user = $request->user();
        ['locale' => $locale, 'dir' => $dir, 't' => $t] = $this->getLocaleData();

        // Get user's subscription and package
        $subscriptionData = $this->getSubscriptionData($user);
        $currentPackage = $subscriptionData['package'];

        // Get current month ticket count
        $currentMonthCount = UserSupportTicketCount::getCurrentMonthCount($user->id);

        // Get ticket limit from package
        $ticketLimit = $currentPackage ? ($currentPackage->monthly_support_tickets_limit ?? null) : null;
        // If limit is null or 0, it means unlimited. Otherwise check count
        $canCreateTicket = $ticketLimit === null || $ticketLimit === 0 || $currentMonthCount < $ticketLimit;

        if (!$canCreateTicket) {
            return redirect()->route('user.support.index')
                ->with('error', TranslationHelper::get('messages.ticket_limit_reached', 'You have reached your monthly ticket limit'));
        }

        return view('user.support.create', compact('locale', 'dir', 't', 'currentMonthCount', 'ticketLimit'));
    }

    /**
     * Store new ticket
     */
    public function store(Request $request)
    {
        $user = $request->user();

        // Get user's subscription and package
        $subscriptionData = $this->getSubscriptionData($user);
        $currentPackage = $subscriptionData['package'];

        // Check ticket limit
        $currentMonthCount = UserSupportTicketCount::getCurrentMonthCount($user->id);
        $ticketLimit = $currentPackage ? ($currentPackage->monthly_support_tickets_limit ?? null) : null;

        // If limit is 0 or null means unlimited, but if it's a number, check it
        if ($ticketLimit !== null && $ticketLimit > 0 && $currentMonthCount >= $ticketLimit) {
            return redirect()->route('user.support.index')
                ->with('error', TranslationHelper::get('messages.ticket_limit_reached', 'You have reached your monthly ticket limit'));
        }

        $validated = $request->validate([
            'subject' => 'required|string|min:5|max:255',
            'message' => 'required|string|min:10|max:5000',
            'priority' => 'nullable|in:low,medium,high,urgent',
        ], [
            'subject.required' => TranslationHelper::get('messages.subject_required', 'Subject is required'),
            'subject.min' => TranslationHelper::get('messages.subject_min', 'Subject must be at least 5 characters'),
            'message.required' => TranslationHelper::get('messages.message_required', 'Message is required'),
            'message.min' => TranslationHelper::get('messages.message_min', 'Message must be at least 10 characters'),
        ]);

        // Create ticket
        $ticket = SupportTicket::create([
            'user_id' => $user->id,
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'priority' => $validated['priority'] ?? 'medium',
            'status' => 'open',
        ]);

        // Increment ticket count
        UserSupportTicketCount::incrementForUser($user->id);

        // Send email to all admins
        try {
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                Mail::to($admin->email)->send(new NewSupportTicketNotification($ticket));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send new ticket notification email: ' . $e->getMessage());
        }

        return redirect()->route('user.support.tickets.index')
            ->with('success', TranslationHelper::get('messages.ticket_created_successfully', 'Ticket created successfully'));
    }

    /**
     * Show user tickets list
     */
    public function tickets(Request $request)
    {
        $user = $request->user();
        ['locale' => $locale, 'dir' => $dir, 't' => $t] = $this->getLocaleData();

        $query = SupportTicket::where('user_id', $user->id)
            ->with(['replies.user'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $tickets = $query->paginate(20);

        return view('user.support.tickets', compact('tickets', 'locale', 'dir', 't'));
    }

    /**
     * Show ticket details
     */
    public function show(Request $request, SupportTicket $ticket)
    {
        $user = $request->user();
        ['locale' => $locale, 'dir' => $dir, 't' => $t] = $this->getLocaleData();

        // Verify ownership
        if ($ticket->user_id !== $user->id) {
            abort(403);
        }

        $ticket->load(['replies.user']);

        // Mark admin replies as read when user views the ticket
        $ticket->replies()
            ->where('is_admin_reply', true)
            ->whereNull('user_read_at')
            ->update(['user_read_at' => now()]);

        return view('user.support.show', compact('ticket', 'locale', 'dir', 't'));
    }

    /**
     * Store reply to ticket
     */
    public function reply(Request $request, SupportTicket $ticket)
    {
        $user = $request->user();

        // Verify ownership
        if ($ticket->user_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'reply' => 'required|string|min:10|max:5000',
        ], [
            'reply.required' => TranslationHelper::get('messages.reply_required', 'Reply is required'),
            'reply.min' => TranslationHelper::get('messages.reply_min', 'Reply must be at least 10 characters'),
            'reply.max' => TranslationHelper::get('messages.reply_max', 'Reply must not exceed 5000 characters'),
        ]);

        // Create reply
        $reply = $ticket->replies()->create([
            'user_id' => $user->id,
            'reply' => $validated['reply'],
            'is_admin_reply' => false,
        ]);

        // Update ticket status to open if it was resolved
        if ($ticket->status === 'resolved' || $ticket->status === 'closed') {
            $ticket->update(['status' => 'open']);
        }

        // Reset admin_read_at when user replies
        $ticket->update(['admin_read_at' => null]);

        // Send email to all admins
        try {
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                Mail::to($admin->email)->send(new SupportTicketReplyNotification($ticket, $reply, false));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send ticket reply notification email: ' . $e->getMessage());
        }

        return redirect()->route('user.support.tickets.show', $ticket)
            ->with('success', TranslationHelper::get('messages.reply_sent', 'Reply sent successfully'));
    }
}
