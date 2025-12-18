<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
</head>

<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f5f5f5;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f5f5f5; padding: 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden;">
                    <tr>
                        <td style="padding: 30px; background-color: #dc2626;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 24px;">Password Reset Request</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 30px;">
                            <h2 style="margin: 0 0 20px 0; color: #333333; font-size: 20px;">Hello {{ $user->name }},</h2>
                            <p style="margin: 0 0 15px 0; color: #666666; font-size: 16px; line-height: 1.6;">You are receiving this email because we received a password reset request for your account.</p>
                            <p style="margin: 0 0 30px 0; color: #666666; font-size: 16px; line-height: 1.6;">Click the button below to reset your password:</p>
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="padding: 20px 0;">
                                        <a href="{{ $resetUrl }}" style="display: inline-block; padding: 12px 30px; background-color: #dc2626; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 16px;">Reset Password</a>
                                    </td>
                                </tr>
                            </table>
                            <p style="margin: 30px 0 15px 0; color: #666666; font-size: 14px; line-height: 1.6;">Or copy and paste this link into your browser:</p>
                            <p style="margin: 0 0 30px 0; padding: 15px; background-color: #f8f9fa; border-radius: 5px; word-break: break-all; color: #333333; font-size: 12px; font-family: monospace;">{{ $resetUrl }}</p>
                            <p style="margin: 0 0 15px 0; color: #999999; font-size: 14px;"><strong>Note:</strong> This password reset link will expire in 60 minutes.</p>
                            <p style="margin: 0 0 15px 0; color: #999999; font-size: 14px;">If you did not request a password reset, no further action is required.</p>
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