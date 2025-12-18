<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>{{ app()->getLocale() === 'ar' ? 'تم استلام طلبك بنجاح' : 'Your Order Has Been Received' }}</title>
</head>

<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f5f5f5;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f5f5f5; padding: 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden;">
                    <tr>
                        <td style="padding: 30px; background-color: #10b981;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 24px;">{{ app()->getLocale() === 'ar' ? 'تم استلام طلبك بنجاح!' : 'Your Order Has Been Received!' }}</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 30px;">
                            <h2 style="margin: 0 0 20px 0; color: #333333; font-size: 20px;">{{ app()->getLocale() === 'ar' ? 'شكراً لك على طلبك' : 'Thank You for Your Order' }}</h2>
                            <p style="margin: 0 0 15px 0; color: #666666; font-size: 16px; line-height: 1.6;">
                                {{ app()->getLocale() === 'ar' ? 'نود إعلامك بأننا قد استلمنا طلبك بنجاح وسنقوم بمعالجته في أقرب وقت ممكن.' : 'We are pleased to inform you that we have successfully received your order and will process it as soon as possible.' }}
                            </p>

                            <table width="100%" cellpadding="0" cellspacing="0" style="margin: 20px 0; background-color: #f8f9fa; border-radius: 5px;">
                                <tr>
                                    <td style="padding: 15px;">
                                        <p style="margin: 8px 0; color: #333333; font-size: 14px;"><strong>{{ app()->getLocale() === 'ar' ? 'رقم الطلب:' : 'Order Number:' }}</strong> #{{ $order->order_number }}</p>
                                        <p style="margin: 8px 0; color: #333333; font-size: 14px;"><strong>{{ app()->getLocale() === 'ar' ? 'المنتج:' : 'Product:' }}</strong> {{ $order->product ? $order->product->name : '-' }}</p>
                                        <p style="margin: 8px 0; color: #333333; font-size: 14px;"><strong>{{ app()->getLocale() === 'ar' ? 'الكمية:' : 'Quantity:' }}</strong> {{ $order->quantity }}</p>
                                        <p style="margin: 8px 0; color: #333333; font-size: 14px;"><strong>{{ app()->getLocale() === 'ar' ? 'الإجمالي:' : 'Total:' }}</strong> {{ number_format($order->total_cents / 100, 2) }} {{ $order->currency ?? 'EGP' }}</p>
                                        <p style="margin: 8px 0; color: #333333; font-size: 14px;"><strong>{{ app()->getLocale() === 'ar' ? 'تاريخ الطلب:' : 'Order Date:' }}</strong> {{ $order->created_at->format('Y-m-d H:i') }}</p>
                                        <p style="margin: 8px 0; color: #333333; font-size: 14px;"><strong>{{ app()->getLocale() === 'ar' ? 'حالة الطلب:' : 'Order Status:' }}</strong> {{ app()->getLocale() === 'ar' ? 'قيد المعالجة' : 'Processing' }}</p>
                                    </td>
                                </tr>
                            </table>

                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="padding: 20px 0;">
                                        <a href="{{ route('order.track', $order->order_number) }}" style="display: inline-block; padding: 12px 30px; background-color: #10b981; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 16px;">{{ app()->getLocale() === 'ar' ? 'تتبع طلبك' : 'Track Your Order' }}</a>
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