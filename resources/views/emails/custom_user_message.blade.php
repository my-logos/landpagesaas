<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message from {{ config('app.name') }}</title>
</head>

<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f5f5f5;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f5f5f5; padding: 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden;">
                    <tr>
                        <td style="padding: 30px; background-color: #5561ff;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 24px;">Message from {{ config('app.name') }}</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 30px;">
                            <h2 style="margin: 0 0 20px 0; color: #333333; font-size: 20px;">Hello {{ $user->name }},</h2>
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin: 20px 0; background-color: #f8f9fa; border-radius: 5px; border: 1px solid #e0e0e0;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <div style="color: #333333; font-size: 16px; line-height: 1.8;">{!! nl2br(e($emailMessage ?? '')) !!}</div>
                                    </td>
                                </tr>
                            </table>
                            <p style="margin: 30px 0 0 0; color: #999999; font-size: 14px;">If you have any questions, please contact us.</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 20px 30px; background-color: #f8f9fa; border-top: 1px solid #e0e0e0;">
                            <p style="margin: 0; color: #999999; font-size: 12px; text-align: center;">This email was sent from {{ config('app.name') }}</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>