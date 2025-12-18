<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseAdminController;
use App\Models\SupportTicket;
use App\Models\SupportFaq;
use App\Models\User;
use App\Mail\SupportTicketReplyNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Helpers\TranslationHelper;

class SupportController extends BaseAdminController
{
    /**
     * Display tickets list
     */
    public function index(Request $request)
    {
        $viewData = $this->getViewData();
        extract($viewData);

        $query = SupportTicket::with(['user', 'assignedAdmin', 'replies'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->input('priority'));
        }

        // Filter by assigned admin
        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->input('assigned_to'));
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $tickets = $query->paginate(20);

        // Get counts by status
        $openCount = SupportTicket::where('status', 'open')->count();
        $inProgressCount = SupportTicket::where('status', 'in_progress')->count();
        $resolvedCount = SupportTicket::where('status', 'resolved')->count();
        $closedCount = SupportTicket::where('status', 'closed')->count();

        // Get admin users for assignment
        $admins = User::where('role', 'admin')->get();

        return view('admin.support.index', array_merge($viewData, compact(
            'tickets',
            'openCount',
            'inProgressCount',
            'resolvedCount',
            'closedCount',
            'admins'
        )));
    }

    /**
     * Show ticket details
     */
    public function show(Request $request, SupportTicket $ticket)
    {
        $viewData = $this->getViewData();
        extract($viewData);

        $ticket->load(['user', 'assignedAdmin', 'replies.user']);

        // Mark ticket as read when admin views it
        if (!$ticket->admin_read_at) {
            $ticket->update(['admin_read_at' => now()]);
            $ticket->refresh();
        }

        // Get admin users for assignment
        $admins = User::where('role', 'admin')->get();

        return view('admin.support.show', array_merge($viewData, compact('ticket', 'admins')));
    }

    /**
     * Update ticket status/priority/assignment
     */
    public function update(Request $request, SupportTicket $ticket)
    {
        $validated = $request->validate([
            'status' => 'nullable|in:open,in_progress,resolved,closed',
            'priority' => 'nullable|in:low,medium,high,urgent',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        if (isset($validated['status'])) {
            $ticket->status = $validated['status'];
            if ($validated['status'] === 'resolved') {
                $ticket->resolved_at = now();
            } elseif ($ticket->resolved_at && $validated['status'] !== 'resolved') {
                $ticket->resolved_at = null;
            }
        }

        if (isset($validated['priority'])) {
            $ticket->priority = $validated['priority'];
        }

        if (isset($validated['assigned_to'])) {
            $ticket->assigned_to = $validated['assigned_to'];
        }

        $ticket->save();

        return redirect()->route('admin.support.show', $ticket)
            ->with('success', TranslationHelper::get('messages.ticket_updated', 'Ticket updated successfully'));
    }

    /**
     * Store reply to ticket
     */
    public function reply(Request $request, SupportTicket $ticket)
    {
        $user = $request->user();

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
            'is_admin_reply' => true,
        ]);

        // Update ticket status to in_progress if it was open
        if ($ticket->status === 'open') {
            $ticket->update(['status' => 'in_progress']);
        }

        // Mark ticket as read by admin (since admin is replying)
        $ticket->update(['admin_read_at' => now()]);

        // Send email to user
        try {
            Mail::to($ticket->user->email)->send(new SupportTicketReplyNotification($ticket, $reply, true));
        } catch (\Exception $e) {
            Log::error('Failed to send ticket reply notification email: ' . $e->getMessage());
        }

        return redirect()->route('admin.support.show', $ticket)
            ->with('success', TranslationHelper::get('messages.reply_sent', 'Reply sent successfully'));
    }

    /**
     * Manage FAQ
     */
    public function faq(Request $request)
    {
        $viewData = $this->getViewData();
        extract($viewData);

        $faqs = SupportFaq::ordered()->get();

        return view('admin.support.faq', array_merge($viewData, compact('faqs')));
    }

    /**
     * Store FAQ
     */
    public function storeFaq(Request $request)
    {
        $validated = $request->validate([
            'question_en' => 'required|string|max:500',
            'question_ar' => 'required|string|max:500',
            'answer_en' => 'required|string|max:5000',
            'answer_ar' => 'required|string|max:5000',
            'keywords_en' => 'nullable|string',
            'keywords_ar' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        // Parse keywords
        $keywordsEn = !empty($validated['keywords_en'])
            ? array_filter(array_map('trim', explode(',', $validated['keywords_en'])))
            : [];
        $keywordsAr = !empty($validated['keywords_ar'])
            ? array_filter(array_map('trim', explode(',', $validated['keywords_ar'])))
            : [];

        SupportFaq::create([
            'question_en' => $validated['question_en'],
            'question_ar' => $validated['question_ar'],
            'answer_en' => $validated['answer_en'],
            'answer_ar' => $validated['answer_ar'],
            'keywords_en' => $keywordsEn,
            'keywords_ar' => $keywordsAr,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => isset($validated['is_active']) ? (bool) $validated['is_active'] : true,
        ]);

        return redirect()->route('admin.support.faq')
            ->with('success', TranslationHelper::get('messages.faq_created', 'FAQ created successfully'));
    }

    /**
     * Update FAQ
     */
    public function updateFaq(Request $request, SupportFaq $faq)
    {
        $validated = $request->validate([
            'question_en' => 'required|string|max:500',
            'question_ar' => 'required|string|max:500',
            'answer_en' => 'required|string|max:5000',
            'answer_ar' => 'required|string|max:5000',
            'keywords_en' => 'nullable|string',
            'keywords_ar' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'sometimes|boolean',
        ]);

        // Parse keywords
        $keywordsEn = !empty($validated['keywords_en'])
            ? array_filter(array_map('trim', explode(',', $validated['keywords_en'])))
            : [];
        $keywordsAr = !empty($validated['keywords_ar'])
            ? array_filter(array_map('trim', explode(',', $validated['keywords_ar'])))
            : [];

        $updateData = [
            'question_en' => $validated['question_en'],
            'question_ar' => $validated['question_ar'],
            'answer_en' => $validated['answer_en'],
            'answer_ar' => $validated['answer_ar'],
            'keywords_en' => $keywordsEn,
            'keywords_ar' => $keywordsAr,
            'sort_order' => $validated['sort_order'] ?? $faq->sort_order,
        ];

        if (isset($validated['is_active'])) {
            $updateData['is_active'] = (bool) $validated['is_active'];
        }

        $faq->update($updateData);

        return redirect()->route('admin.support.faq')
            ->with('success', TranslationHelper::get('messages.faq_updated', 'FAQ updated successfully'));
    }

    /**
     * Delete FAQ
     */
    public function deleteFaq(SupportFaq $faq)
    {
        $faq->delete();

        return redirect()->route('admin.support.faq')
            ->with('success', TranslationHelper::get('messages.faq_deleted', 'FAQ deleted successfully'));
    }
}
