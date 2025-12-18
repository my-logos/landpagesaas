<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Wallet Top-up</title>
</head>

<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f5f5f5;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f5f5f5; padding: 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden;">
                    <tr>
                        <td style="padding: 30px; background-color: #ec4899;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 24px;">New Wallet Top-up</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 30px;">
                            <h2 style="margin: 0 0 20px 0; color: #333333; font-size: 20px;">Wallet Top-up Notification</h2>
                            <p style="margin: 0 0 15px 0; color: #666666; font-size: 16px; line-height: 1.6;">A new wallet top-up has been made by a user.</p>
                            <p style="margin: 0 0 30px 0; color: #666666; font-size: 16px; line-height: 1.6;">Please review the top-up details below:</p>
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin: 20px 0; background-color: #f8f9fa; border-radius: 5px;">
                                <tr>
                                    <td style="padding: 15px;">
                                        <p style="margin: 8px 0; color: #333333; font-size: 14px;"><strong>User:</strong> {{ $user->name }} ({{ $user->email }})</p>
                                        <p style="margin: 8px 0; color: #333333; font-size: 14px;"><strong>Amount:</strong> {{ number_format($payment->amount / 100, 2) }} {{ $payment->currency }}</p>
                                        <p style="margin: 8px 0; color: #333333; font-size: 14px;"><strong>Payment Method:</strong> {{ $payment->payment_method }}</p>
                                        <p style="margin: 8px 0; color: #333333; font-size: 14px;"><strong>Top-up Date:</strong> {{ $payment->paid_at ? $payment->paid_at->format('Y-m-d H:i') : 'N/A' }}</p>
                                        <p style="margin: 8px 0; color: #333333; font-size: 14px;"><strong>Current Balance:</strong> {{ number_format($user->wallet_balance / 100, 2) }} {{ $payment->currency }}</p>
                                    </td>
                                </tr>
                            </table>
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="padding: 20px 0;">
                                        <a href="{{ url('/admin/subscriptions') }}" style="display: inline-block; padding: 12px 30px; background-color: #5561ff; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 16px;">View Details</a>
                                    </td>
                                </tr>
                            </table>
                            <p style="margin: 30px 0 0 0; color: #999999; font-size: 14px;">This is an automated notification from {{ config('app.name') }}.</p>
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