<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>{{ app()->getLocale() === 'ar' ? 'تحديث حالة طلبك' : 'Your Order Status Update' }}</title>
</head>

<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f5f5f5;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f5f5f5; padding: 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden;">
                    <tr>
                        <td style="padding: 30px; background-color: #1f2b6b;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 24px;">{{ app()->getLocale() === 'ar' ? 'تحديث حالة طلبك' : 'Your Order Status Update' }}</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 30px;">
                            <h2 style="margin: 0 0 20px 0; color: #333333; font-size: 20px;">{{ app()->getLocale() === 'ar' ? 'تم تحديث حالة طلبك' : 'Your Order Status Has Been Updated' }}</h2>
                            <p style="margin: 0 0 15px 0; color: #666666; font-size: 16px; line-height: 1.6;">
                                {{ app()->getLocale() === 'ar' ? 'نود إعلامك بأن حالة طلبك قد تم تحديثها.' : 'We would like to inform you that your order status has been updated.' }}
                            </p>

                            @php
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
                            $locale = app()->getLocale();
                            $newStatusLabel = $statusLabels[$locale][$newStatus] ?? ucfirst($newStatus);
                            $oldStatusLabel = $statusLabels[$locale][$oldStatus] ?? ucfirst($oldStatus);
                            @endphp

                            <table width="100%" cellpadding="0" cellspacing="0" style="margin: 20px 0; background-color: #f8f9fa; border-radius: 5px;">
                                <tr>
                                    <td style="padding: 15px;">
                                        <p style="margin: 8px 0; color: #333333; font-size: 14px;"><strong>{{ app()->getLocale() === 'ar' ? 'رقم الطلب:' : 'Order Number:' }}</strong> #{{ $order->order_number }}</p>
                                        <p style="margin: 8px 0; color: #333333; font-size: 14px;"><strong>{{ app()->getLocale() === 'ar' ? 'المنتج:' : 'Product:' }}</strong> {{ $order->product ? $order->product->name : '-' }}</p>
                                        <p style="margin: 8px 0; color: #333333; font-size: 14px;"><strong>{{ app()->getLocale() === 'ar' ? 'الحالة السابقة:' : 'Previous Status:' }}</strong> {{ $oldStatusLabel }}</p>
                                        <p style="margin: 8px 0; color: #333333; font-size: 14px;"><strong>{{ app()->getLocale() === 'ar' ? 'الحالة الحالية:' : 'Current Status:' }}</strong> <span style="color: #1f2b6b; font-weight: bold;">{{ $newStatusLabel }}</span></p>
                                        <p style="margin: 8px 0; color: #333333; font-size: 14px;"><strong>{{ app()->getLocale() === 'ar' ? 'تاريخ التحديث:' : 'Update Date:' }}</strong> {{ now()->format('Y-m-d H:i') }}</p>
                                    </td>
                                </tr>
                            </table>

                            @if($newStatus === 'delivered')
                            <div style="margin: 20px 0; padding: 15px; background-color: #d1fae5; border: 1px solid #a7f3d0; border-radius: 5px;">
                                <p style="margin: 0; color: #065f46; font-size: 14px; line-height: 1.6;">
                                    <strong>{{ app()->getLocale() === 'ar' ? 'تهانينا!' : 'Congratulations!' }}</strong>
                                    {{ app()->getLocale() === 'ar' ? 'تم تسليم طلبك بنجاح. نأمل أن تكون راضياً عن مشترياتك!' : 'Your order has been successfully delivered. We hope you are satisfied with your purchase!' }}
                                </p>
                            </div>
                            @elseif($newStatus === 'shipped')
                            <div style="margin: 20px 0; padding: 15px; background-color: #dbeafe; border: 1px solid #93c5fd; border-radius: 5px;">
                                <p style="margin: 0; color: #1e40af; font-size: 14px; line-height: 1.6;">
                                    {{ app()->getLocale() === 'ar' ? 'تم شحن طلبك وهو في طريقه إليك. سنقوم بإشعارك عند وصوله.' : 'Your order has been shipped and is on its way to you. We will notify you when it arrives.' }}
                                </p>
                            </div>
                            @endif

                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="padding: 20px 0;">
                                        <a href="{{ route('order.track', $order->order_number) }}" style="display: inline-block; padding: 12px 30px; background-color: #1f2b6b; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 16px;">{{ app()->getLocale() === 'ar' ? 'تتبع طلبك' : 'Track Your Order' }}</a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 30px 0 0 0; color: #999999; font-size: 14px;">{{ app()->getLocale() === 'ar' ? 'هذا إشعار تلقائي من ' : 'This is an automated notification from ' }}{{ config('app.name') }}.</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 20px 30px; background-color: #f8f9fa; border-top: 1px solid #e0e0e0;">
                            <p style="margin: 0; color: #999999; font-size: 12px; text-align: center;">{{ app()->getLocale() === 'ar' ? 'تم إرسال هذا البريد الإلكتروني من ' : 'This email was sent from ' }}{{ config('app.name') }}</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>