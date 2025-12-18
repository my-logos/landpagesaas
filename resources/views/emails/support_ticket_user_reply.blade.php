<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>{{ app()->getLocale() === 'ar' ? 'رد جديد على التذكرة' : 'New Reply on Ticket' }}</title>
</head>

<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f5f5f5;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f5f5f5; padding: 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden;">
                    <tr>
                        <td style="padding: 30px; background-color: #1f2b6b;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 24px;">{{ app()->getLocale() === 'ar' ? 'رد جديد على التذكرة' : 'New Reply on Ticket' }}</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 30px;">
                            <h2 style="margin: 0 0 20px 0; color: #333333; font-size: 20px;">{{ app()->getLocale() === 'ar' ? 'تم إضافة رد جديد على التذكرة' : 'A new reply has been added to the ticket' }}</h2>
                            <p style="margin: 0 0 15px 0; color: #666666; font-size: 16px; line-height: 1.6;">
                                {{ app()->getLocale() === 'ar' ? 'تم إضافة رد جديد من قبل المستخدم على التذكرة.' : 'A new reply has been added by the user to the ticket.' }}
                            </p>

                            <table width="100%" cellpadding="0" cellspacing="0" style="margin: 20px 0; background-color: #f8f9fa; border-radius: 5px;">
                                <tr>
                                    <td style="padding: 15px;">
                                        <p style="margin: 8px 0; color: #333333; font-size: 14px;"><strong>{{ app()->getLocale() === 'ar' ? 'رقم التذكرة:' : 'Ticket Number:' }}</strong> #{{ $ticket->ticket_number }}</p>
                                        <p style="margin: 8px 0; color: #333333; font-size: 14px;"><strong>{{ app()->getLocale() === 'ar' ? 'الموضوع:' : 'Subject:' }}</strong> {{ $ticket->subject }}</p>
                                        <p style="margin: 8px 0; color: #333333; font-size: 14px;"><strong>{{ app()->getLocale() === 'ar' ? 'المستخدم:' : 'User:' }}</strong> {{ $ticket->user->name }} ({{ $ticket->user->email }})</p>
                                        <p style="margin: 8px 0; color: #333333; font-size: 14px;"><strong>{{ app()->getLocale() === 'ar' ? 'الحالة:' : 'Status:' }}</strong> {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</p>
                                    </td>
                                </tr>
                            </table>

                            <h3 style="margin: 20px 0 10px 0; color: #333333; font-size: 18px;">{{ app()->getLocale() === 'ar' ? 'الرد:' : 'Reply:' }}</h3>
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin: 10px 0; background-color: #f8f9fa; border-radius: 5px;">
                                <tr>
                                    <td style="padding: 15px;">
                                        <p style="margin: 0; color: #666666; font-size: 14px; line-height: 1.6; white-space: pre-wrap;">{{ $reply->reply }}</p>
                                    </td>
                                </tr>
                            </table>

                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="padding: 20px 0;">
                                        <a href="{{ route('admin.support.show', $ticket) }}" style="display: inline-block; padding: 12px 30px; background-color: #1f2b6b; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 16px;">{{ app()->getLocale() === 'ar' ? 'عرض التذكرة' : 'View Ticket' }}</a>
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