<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;

class OrderStatusUpdate extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $oldStatus;
    public $newStatus;

    public function __construct(Order $order, $oldStatus, $newStatus)
    {
        $this->order = $order;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    public function build()
    {
        $locale = app()->getLocale();
        $statusLabels = [
            'ar' => [
                'pending' => 'قيد الانتظار',
                'processing' => 'قيد المعالجة',
                'shipped' => 'تم الشحن',
                'delivered' => 'تم التسليم',
                'rejected' => 'مرفوض',
                'failed_delivery' => 'فشل التسليم',
                'postponed' => 'مؤجل',
            ],
            'en' => [
                'pending' => 'Pending',
                'processing' => 'Processing',
                'shipped' => 'Shipped',
                'delivered' => 'Delivered',
                'rejected' => 'Rejected',
                'failed_delivery' => 'Failed Delivery',
                'postponed' => 'Postponed',
            ],
        ];

        $newStatusLabel = $statusLabels[$locale][$this->newStatus] ?? ucfirst($this->newStatus);

        $subject = $locale === 'ar'
            ? 'تحديث حالة طلبك - ' . $newStatusLabel
            : 'Your Order Status Update - ' . $newStatusLabel;

        return $this->subject($subject)
            ->view('emails.order_status_update');
    }
}
