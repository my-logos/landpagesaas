<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ValidatesRecaptcha;
use App\Models\Message;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Helpers\TranslationHelper;
use App\Mail\NewCustomerMessage;
use App\Mail\NewCustomerReply;

class PublicMessageController extends Controller
{
    use ValidatesRecaptcha;

    /**
     * Show message form or existing messages
     */
    public function create(Request $request)
    {
        $orderNumber = $request->input('order_number');
        $messageId = $request->input('message_id');
        $order = null;
        $user = null;
        $existingMessages = null;
        $replyingToMessage = null;

        if ($orderNumber) {
            $order = Order::where('order_number', $orderNumber)
                ->with(['product', 'landingPage.user'])
                ->first();

            if ($order && $order->landingPage && $order->landingPage->user) {
                $user = $order->landingPage->user;
            }

            // If message_id is provided, this is a reply to an existing message
            if ($messageId) {
                $replyingToMessage = Message::where('id', $messageId)
                    ->where(function ($query) use ($orderNumber, $order) {
                        $query->where('order_number', $orderNumber);
                        if ($order) {
                            $query->orWhere('order_id', $order->id);
                        }
                    })
                    ->with(['replies' => function ($q) {
                        $q->orderBy('created_at', 'asc');
                    }])
                    ->first();

                if ($replyingToMessage) {
                    // Show only this message thread
                    $existingMessages = collect([$replyingToMessage]);
                }
            } else {
                // Get all messages for this order
                $existingMessages = Message::where(function ($query) use ($orderNumber, $order) {
                    $query->where('order_number', $orderNumber);
                    if ($order) {
                        $query->orWhere('order_id', $order->id);
                    }
                })
                    ->with(['replies' => function ($q) {
                        $q->orderBy('created_at', 'asc');
                    }])
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
        }

        $locale = app()->getLocale();
        $dir = $locale === 'ar' ? 'rtl' : 'ltr';

        return view('public.message.create', compact('order', 'user', 'orderNumber', 'locale', 'dir', 'existingMessages', 'messageId', 'replyingToMessage'));
    }

    /**
     * Store message from public form
     */
    public function store(Request $request)
    {
        // Validate reCAPTCHA if enabled
        $this->validateRecaptcha($request);

        $messageId = $request->input('message_id');

        // If message_id is provided, this is a reply to an existing message
        if ($messageId) {
            return $this->storeReply($request, $messageId);
        }

        $validated = $request->validate([
            'sender_name' => 'required|string|min:2|max:255',
            'sender_email' => 'nullable|email|max:255',
            'sender_phone' => 'nullable|string|max:50',
            'subject' => 'required|string|min:5|max:255',
            'message' => 'required|string|min:10|max:5000',
            'order_number' => 'nullable|string|exists:orders,order_number',
        ], [
            'sender_name.required' => TranslationHelper::get('messages.name_required', 'Name is required'),
            'sender_name.min' => TranslationHelper::get('messages.name_min', 'Name must be at least 2 characters'),
            'sender_email.email' => TranslationHelper::get('messages.email_invalid', 'Invalid email format'),
            'subject.required' => TranslationHelper::get('messages.subject_required', 'Subject is required'),
            'subject.min' => TranslationHelper::get('messages.subject_min', 'Subject must be at least 5 characters'),
            'message.required' => TranslationHelper::get('messages.message_required', 'Message is required'),
            'message.min' => TranslationHelper::get('messages.message_min', 'Message must be at least 10 characters'),
            'order_number.exists' => TranslationHelper::get('messages.order_not_found', 'Order not found'),
        ]);

        // Get order and user
        $order = null;
        $userId = null;

        if ($validated['order_number']) {
            $order = Order::where('order_number', $validated['order_number'])
                ->with('landingPage.user')
                ->first();

            if ($order && $order->landingPage && $order->landingPage->user) {
                $userId = $order->landingPage->user->id;
            }
        }

        // Create message
        $message = Message::create([
            'user_id' => $userId,
            'sender_name' => $validated['sender_name'],
            'sender_email' => $validated['sender_email'],
            'sender_phone' => $validated['sender_phone'],
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'order_number' => $validated['order_number'],
            'order_id' => $order ? $order->id : null,
            'status' => 'new',
        ]);

        // Send email notification to store owner
        if ($userId && $user = \App\Models\User::find($userId)) {
            try {
                Mail::to($user->email)->send(new NewCustomerMessage($message, $user));
            } catch (\Exception $e) {
                Log::error('Failed to send new customer message notification email', [
                    'user_id' => $userId,
                    'message_id' => $message->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $locale = app()->getLocale();
        $dir = $locale === 'ar' ? 'rtl' : 'ltr';

        return view('public.message.success', compact('message', 'locale', 'dir'));
    }

    /**
     * Store reply to existing message
     */
    private function storeReply(Request $request, $messageId)
    {
        $validated = $request->validate([
            'reply' => 'required|string|min:10|max:5000',
            'order_number' => 'nullable|string|exists:orders,order_number',
        ], [
            'reply.required' => TranslationHelper::get('messages.reply_required', 'Reply is required'),
            'reply.min' => TranslationHelper::get('messages.reply_min', 'Reply must be at least 10 characters'),
            'reply.max' => TranslationHelper::get('messages.reply_max', 'Reply must not exceed 5000 characters'),
            'order_number.exists' => TranslationHelper::get('messages.order_not_found', 'Order not found'),
        ]);

        // Get the message being replied to
        $message = Message::where('id', $messageId)->firstOrFail();

        // Verify order_number matches if provided
        if ($request->has('order_number') && $message->order_number !== $request->input('order_number')) {
            return back()->withErrors(['order_number' => TranslationHelper::get('messages.order_not_found', 'Order not found')])->withInput();
        }

        // Create reply
        $reply = \App\Models\MessageReply::create([
            'message_id' => $message->id,
            'user_id' => null, // Customer reply, no user_id
            'reply' => $validated['reply'],
            'is_customer_reply' => true,
        ]);

        // Reload message with user relationship
        $message->load('user');

        // Send email notification to store owner
        if ($message->user_id && $user = $message->user) {
            try {
                Mail::to($user->email)->send(new NewCustomerReply($message, $reply, $user));
            } catch (\Exception $e) {
                Log::error('Failed to send new customer reply notification email', [
                    'user_id' => $message->user_id,
                    'message_id' => $message->id,
                    'reply_id' => $reply->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Update message status
        if ($message->status === 'new') {
            $message->update(['status' => 'read']);
        }

        $locale = app()->getLocale();
        $dir = $locale === 'ar' ? 'rtl' : 'ltr';

        return view('public.message.success', compact('message', 'locale', 'dir'));
    }
}
