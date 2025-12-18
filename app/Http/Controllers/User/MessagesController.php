<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\HasLocaleAndTranslation;
use App\Http\Controllers\Concerns\ValidatesRecaptcha;
use App\Models\Message;
use App\Models\MessageReply;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Helpers\TranslationHelper;
use App\Mail\StoreReplyNotification;
use App\Mail\SupportMessageNotification;

class MessagesController extends Controller
{
    use HasLocaleAndTranslation, ValidatesRecaptcha;

    /**
     * Display messages list
     */
    public function index(Request $request)
    {
        $user = $request->user();
        ['locale' => $locale, 'dir' => $dir, 't' => $t] = $this->getLocaleData();

        $query = Message::where('user_id', $user->id)
            ->with(['order', 'replies' => function ($q) {
                $q->orderBy('created_at', 'asc');
            }])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                    ->orWhere('sender_name', 'like', "%{$search}%")
                    ->orWhere('sender_email', 'like', "%{$search}%")
                    ->orWhere('order_number', 'like', "%{$search}%");
            });
        }

        $messages = $query->paginate(20);

        // Count by status
        $newCount = Message::where('user_id', $user->id)->where('status', 'new')->count();
        $readCount = Message::where('user_id', $user->id)->where('status', 'read')->count();
        $repliedCount = Message::where('user_id', $user->id)->where('status', 'replied')->count();

        return view('user.messages.index', compact(
            'messages',
            'newCount',
            'readCount',
            'repliedCount',
            'locale',
            'dir',
            't'
        ));
    }

    /**
     * Show message details
     */
    public function show(Request $request, Message $message)
    {
        $user = $request->user();
        ['locale' => $locale, 'dir' => $dir, 't' => $t] = $this->getLocaleData();

        // Verify ownership
        if ($message->user_id !== $user->id) {
            abort(403);
        }

        // Mark as read
        $message->markAsRead();
        $message->load(['order', 'replies.user']);

        return view('user.messages.show', compact('message', 'locale', 'dir', 't'));
    }

    /**
     * Store reply to message
     */
    public function reply(Request $request, Message $message)
    {
        $user = $request->user();

        // Verify ownership
        if ($message->user_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'reply' => 'required|string|min:10|max:5000',
        ], [
            'reply.required' => TranslationHelper::get('messages.reply_required', 'Reply is required'),
            'reply.min' => TranslationHelper::get('messages.reply_min', 'Reply must be at least 10 characters'),
            'reply.max' => TranslationHelper::get('messages.reply_max', 'Reply must not exceed 5000 characters'),
        ]);

        $reply = MessageReply::create([
            'message_id' => $message->id,
            'user_id' => $user->id,
            'reply' => $validated['reply'],
            'is_customer_reply' => false,
        ]);

        // Update message status and read_at to mark that user has seen all replies up to now
        // This will exclude old customer replies from the count since user has replied
        $message->update([
            'status' => 'replied',
            'read_at' => now(), // Mark as read to exclude previous customer replies from count
        ]);

        // Send email notification to customer if they have email
        if ($message->sender_email) {
            try {
                Mail::to($message->sender_email)->send(new StoreReplyNotification($message, $reply));
            } catch (\Exception $e) {
                Log::error('Failed to send store reply notification email', [
                    'message_id' => $message->id,
                    'reply_id' => $reply->id,
                    'customer_email' => $message->sender_email,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return redirect()->route('user.messages.show', $message)
            ->with('success', TranslationHelper::get('messages.reply_sent', 'Reply sent successfully'));
    }

    /**
     * Mark message as read
     */
    public function markAsRead(Request $request, Message $message)
    {
        $user = $request->user();

        if ($message->user_id !== $user->id) {
            abort(403);
        }

        $message->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Update message status
     */
    public function updateStatus(Request $request, Message $message)
    {
        $user = $request->user();

        if ($message->user_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:read,replied,closed',
        ]);

        $message->update(['status' => $validated['status']]);

        return redirect()->route('user.messages.show', $message)
            ->with('success', TranslationHelper::get('messages.status_updated', 'Status updated successfully'));
    }

    /**
     * Get order details for modal
     */
    public function getOrderDetails(Request $request, $orderId)
    {
        $user = $request->user();

        $order = Order::where('id', $orderId)
            ->where('user_id', $user->id)
            ->with(['product', 'landingPage'])
            ->firstOrFail();

        $customerData = $order->customer_data ?? [];

        return response()->json([
            'success' => true,
            'order' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'product_name' => $order->product ? $order->product->name : '-',
                'quantity' => $order->quantity,
                'total' => number_format($order->total_cents / 100, 2),
                'currency' => $order->currency ?? 'EGP',
                'status' => $order->status,
                'shipping_status' => $order->shipping_status,
                'created_at' => $order->created_at->format('Y-m-d H:i'),
                'landing_page' => $order->landingPage ? $order->landingPage->title : null,
                'customer_data' => $customerData,
                'size' => $customerData['size'] ?? null,
                'color' => $customerData['color'] ?? null,
            ]
        ]);
    }

    /**
     * Send new message to customer (from user dashboard)
     */
    public function sendMessageToCustomer(Request $request, Message $message)
    {
        $user = $request->user();

        // Verify ownership
        if ($message->user_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'subject' => 'required|string|min:5|max:255',
            'message' => 'required|string|min:10|max:5000',
        ], [
            'subject.required' => TranslationHelper::get('messages.subject_required', 'Subject is required'),
            'subject.min' => TranslationHelper::get('messages.subject_min', 'Subject must be at least 5 characters'),
            'message.required' => TranslationHelper::get('messages.message_required', 'Message is required'),
            'message.min' => TranslationHelper::get('messages.message_min', 'Message must be at least 10 characters'),
        ]);

        // Create new message from user to customer
        $customerMessage = Message::create([
            'user_id' => $user->id,
            'sender_name' => $user->name,
            'sender_email' => $user->email,
            'sender_phone' => $user->phone,
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'order_number' => $message->order_number,
            'order_id' => $message->order_id,
            'status' => 'new',
        ]);

        // Add reply to link the messages
        MessageReply::create([
            'message_id' => $customerMessage->id,
            'user_id' => $user->id,
            'reply' => $validated['message'],
            'is_customer_reply' => false,
        ]);

        // Update original message read_at to mark that user has responded
        // This will exclude this message from the unread count
        if (!$message->read_at) {
            $message->update(['read_at' => now()]);
        }

        return redirect()->route('user.messages.show', $message)
            ->with('success', TranslationHelper::get('messages.message_sent_to_customer', 'Message sent to customer successfully'));
    }

    /**
     * Show form to create new message from dashboard
     */
    public function create(Request $request)
    {
        $user = $request->user();
        ['locale' => $locale, 'dir' => $dir, 't' => $t] = $this->getLocaleData();

        // Get all orders for this user to select from
        $orders = Order::where('user_id', $user->id)
            ->with('product')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get selected order if provided
        $selectedOrder = null;
        if ($request->has('order_id')) {
            $selectedOrder = Order::where('id', $request->input('order_id'))
                ->where('user_id', $user->id)
                ->with('product')
                ->first();
        }

        return view('user.messages.create', compact('orders', 'selectedOrder', 'locale', 'dir', 't'));
    }

    /**
     * Store new message from dashboard
     */
    public function storeNewMessage(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'subject' => 'required|string|min:5|max:255',
            'message' => 'required|string|min:10|max:5000',
        ], [
            'order_id.required' => TranslationHelper::get('messages.order_required', 'Order is required'),
            'order_id.exists' => TranslationHelper::get('messages.order_not_found', 'Order not found'),
            'subject.required' => TranslationHelper::get('messages.subject_required', 'Subject is required'),
            'subject.min' => TranslationHelper::get('messages.subject_min', 'Subject must be at least 5 characters'),
            'message.required' => TranslationHelper::get('messages.message_required', 'Message is required'),
            'message.min' => TranslationHelper::get('messages.message_min', 'Message must be at least 10 characters'),
        ]);

        // Get order and verify ownership
        $order = Order::where('id', $validated['order_id'])
            ->where('user_id', $user->id)
            ->with('product')
            ->firstOrFail();

        $customerData = $order->customer_data ?? [];
        $customerEmail = $customerData['email'] ?? null;
        $customerName = $customerData['name'] ?? 'Customer';

        // Create message
        $message = Message::create([
            'user_id' => $user->id,
            'sender_name' => $customerName,
            'sender_email' => $customerEmail,
            'sender_phone' => $customerData['phone'] ?? null,
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'order_number' => $order->order_number,
            'order_id' => $order->id,
            'status' => 'new',
        ]);

        // Send email notification to customer if email is available
        if ($customerEmail) {
            try {
                Mail::to($customerEmail)->send(new SupportMessageNotification($message, $order));
            } catch (\Exception $e) {
                Log::error('Failed to send support message notification email', [
                    'message_id' => $message->id,
                    'customer_email' => $customerEmail,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return redirect()->route('user.messages.index')
            ->with('success', TranslationHelper::get('messages.message_sent_successfully', 'Message sent successfully'));
    }
}
