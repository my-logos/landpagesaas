<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>500 - Server Error</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            color: #333;
        }

        .error-container {
            background: white;
            border-radius: 20px;
            padding: 60px 40px;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            width: 100%;
        }

        .error-code {
            font-size: 120px;
            font-weight: bold;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
            margin-bottom: 20px;
        }

        .error-title {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #2d3748;
        }

        .error-message {
            font-size: 16px;
            color: #718096;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .error-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-block;
            padding: 12px 30px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(240, 147, 251, 0.4);
        }

        .btn-secondary {
            background: #e2e8f0;
            color: #4a5568;
        }

        .btn-secondary:hover {
            background: #cbd5e0;
        }

        .icon {
            font-size: 80px;
            color: #fbb6ce;
            margin-bottom: 20px;
        }

        @media (max-width: 600px) {
            .error-code {
                font-size: 80px;
            }

            .error-title {
                font-size: 24px;
            }

            .error-container {
                padding: 40px 30px;
            }
        }
    </style>
</head>

<body>
    <div class="error-container">
        <div class="icon">
            <i class="fa-solid fa-server"></i>
        </div>
        <div class="error-code">500</div>
        <h1 class="error-title">{{ app()->getLocale() === 'ar' ? 'خطأ في الخادم' : 'Server Error' }}</h1>
        <p class="error-message">
            {{ app()->getLocale() === 'ar' 
                ? 'حدث خطأ داخلي في الخادم. يرجى المحاولة مرة أخرى لاحقاً.' 
                : 'An internal server error occurred. Please try again later.' }}
        </p>
        <div class="error-actions">
            <a href="{{ url('/') }}" class="btn btn-primary">
                <i class="fa-solid fa-home"></i> {{ app()->getLocale() === 'ar' ? 'الصفحة الرئيسية' : 'Home' }}
            </a>
            <a href="javascript:location.reload()" class="btn btn-secondary">
                <i class="fa-solid fa-rotate"></i> {{ app()->getLocale() === 'ar' ? 'إعادة تحميل' : 'Reload' }}
            </a>
        </div>
    </div>
</body>

</html>