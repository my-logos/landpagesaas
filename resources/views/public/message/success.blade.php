<!doctype html>
<html lang="{{ $locale }}" dir="{{ $dir }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ $locale === 'ar' ? 'تم إرسال الرسالة' : 'Message Sent' }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css">
    @if($locale === 'ar')
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;800&display=swap" rel="stylesheet">
    @endif
    <!-- Styles included in main.css -->
    <style>
        :root {
            --font-family: {
                    {
                    $locale ==='ar' ? "'Tajawal', sans-serif": "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif"
                }
            }

            ;
        }
    </style>
</head>

<body class="public-message-page" dir="{{ $dir }}">
    <div class="message-container">
        <div class="success-container">
            <div class="success-icon">
                <i class="fa-solid fa-check-circle"></i>
            </div>
            <h1>{{ $locale === 'ar' ? 'تم إرسال الرسالة بنجاح!' : 'Message Sent Successfully!' }}</h1>
            <p class="success-message">
                {{ $locale === 'ar' ? 'شكراً لك، تم إرسال رسالتك بنجاح. سنقوم بالرد عليك في أقرب وقت ممكن.' : 'Thank you, your message has been sent successfully. We will reply to you as soon as possible.' }}
            </p>

            @if($message->order_number)
            <div class="action-buttons">
                <a href="{{ route('order.track', $message->order_number) }}" class="btn btn-primary">
                    <i class="fa-solid fa-box"></i>
                    {{ $locale === 'ar' ? 'العودة لتتبع الطلب' : 'Back to Order Tracking' }}
                </a>
            </div>
            @endif
        </div>
    </div>
</body>

</html>