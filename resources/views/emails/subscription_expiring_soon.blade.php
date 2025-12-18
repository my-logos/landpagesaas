<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Expiring Soon</title>
</head>

<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f5f5f5;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f5f5f5; padding: 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden;">
                    <tr>
                        <td style="padding: 30px; background-color: #f59e0b;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 24px;">Important Alert</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 30px;">
                            <h2 style="margin: 0 0 20px 0; color: #333333; font-size: 20px;">Hello {{ $user->name }},</h2>
                            <p style="margin: 0 0 15px 0; color: #666666; font-size: 16px; line-height: 1.6;">We would like to inform you that your subscription to the package <strong>{{ $package->name }}</strong> will expire in <strong>{{ $daysRemaining }} days</strong>.</p>
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin: 20px 0; background-color: #fef3c7; border-radius: 5px; border: 1px solid #f59e0b;">
                                <tr>
                                    <td style="padding: 15px;">
                                        <p style="margin: 5px 0; color: #92400e; font-size: 14px;"><strong>Expiration Date:</strong> {{ $subscription->ends_at->format('Y-m-d') }}</p>
                                        <p style="margin: 5px 0; color: #92400e; font-size: 14px;"><strong>Days Remaining:</strong> {{ $daysRemaining }} days</p>
                                    </td>
                                </tr>
                            </table>
                            <p style="margin: 0 0 30px 0; color: #666666; font-size: 16px; line-height: 1.6;">Please renew your subscription to avoid service interruption.</p>
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="padding: 20px 0;">
                                        <a href="{{ url('/user/packages') }}" style="display: inline-block; padding: 12px 30px; background-color: #f59e0b; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 16px;">Renew Subscription Now</a>
                                    </td>
                                </tr>
                            </table>
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