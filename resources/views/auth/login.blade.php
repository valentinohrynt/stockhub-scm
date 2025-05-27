<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Welcome Back</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #EFF6FF 0%, #DBEAFE 50%, #BFDBFE 100%);
            position: relative;
            overflow-x: hidden;
        }

        .bg-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }

        .particle {
            position: absolute;
            background: rgba(79, 70, 229, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .particle:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }

        .particle:nth-child(2) {
            width: 60px;
            height: 60px;
            top: 60%;
            left: 80%;
            animation-delay: 2s;
        }

        .particle:nth-child(3) {
            width: 40px;
            height: 40px;
            top: 80%;
            left: 20%;
            animation-delay: 4s;
        }

        .particle:nth-child(4) {
            width: 100px;
            height: 100px;
            top: 10%;
            right: 10%;
            animation-delay: 1s;
        }

        .particle:nth-child(5) {
            width: 50px;
            height: 50px;
            top: 70%;
            right: 30%;
            animation-delay: 3s;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px) rotate(0deg);
                opacity: 0.2;
            }

            50% {
                transform: translateY(-20px) rotate(180deg);
                opacity: 0.4;
            }
        }

        .container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            z-index: 2;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(15px);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 8px 32px 0 rgba(67, 56, 202, 0.15);
            border: 1px solid rgba(99, 102, 241, 0.2); 
            width: 100%;
            max-width: 420px;
            transform: translateY(50px);
            opacity: 0;
            animation: slideIn 0.8s ease-out forwards;
            position: relative;
            overflow: hidden;
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(99, 102, 241, 0.1), transparent);
            transition: left 0.5s;
        }

        .login-card:hover::before {
            left: 100%;
        }

        @keyframes slideIn {
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header h1 {
            color: #3730A3;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 0 2px 10px rgba(79, 70, 229, 0.1);
        }

        .login-header p {
            color: #4B5563;
            font-size: 1.1rem;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-input {
            width: 100%;
            padding: 15px 20px 15px 50px;
            border: none;
            border-radius: 15px;
            background: rgba(255, 255, 255, 0.8); 
            color: #1F2937; 
            font-size: 16px;
            transition: all 0.3s ease;
            backdrop-filter: blur(5px);
            border: 2px solid rgba(99, 102, 241, 0.2); 
        }

        .form-input:focus {
            outline: none;
            background: rgba(255, 255, 255, 1);
            border-color: #4F46E5;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(79, 70, 229, 0.2); 
        }

        .form-input::placeholder {
            color: #6B7280;
        }

        .input-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #6B7280; 
            font-size: 18px;
            transition: all 0.3s ease;
            z-index: 2;
        }

        .form-group:focus-within .input-icon {
            color: #4F46E5; 
            transform: translateY(-50%) scale(1.1);
        }

        @keyframes checkmark {
            0% {
                transform: rotate(45deg) scale(0);
            }

            100% {
                transform: rotate(45deg) scale(1);
            }
        }

        .remember-label {
            color: #374151; 
            font-size: 14px;
            cursor: pointer;
        }

        .forgot-password {
            color: #4F46E5; 
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .forgot-password:hover {
            color: #4338CA; 
            text-decoration: underline;
        }

        .login-btn {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 15px;
            background: linear-gradient(135deg, #4F46E5, #3730A3);
            color: white;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(79, 70, 229, 0.4); 
            background: linear-gradient(135deg, #6366F1, #4F46E5);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        .login-btn .bi {
            margin-right: 8px;
        }

        .login-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .login-btn:hover::before {
            left: 100%;
        }

        .error-message {
            background: rgba(220, 53, 69, 0.9);
            color: white;
            padding: 12px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
            border-left: 4px solid #dc3545;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-5px);
            }

            75% {
                transform: translateX(5px);
            }
        }

        .success-message {
            background: rgba(22, 163, 74, 0.9); 
            color: white;
            padding: 12px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
            border-left: 4px solid #16A34A;
            animation: slideInDown 0.5s ease;
        }

        @keyframes slideInDown {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        @media (max-width: 480px) {
            .login-card {
                padding: 30px 20px;
                margin: 20px;
            }

            .login-header h1 {
                font-size: 2rem;
            }
        }

        .loading {
            display: none;
            margin-left: 10px;
        }

        .spinner {
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>
    <div class="bg-animation">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <div class="container">
        <div class="login-card">
            <div class="login-header">
                <h1><i class="bi bi-box-seam"></i> StockHub</h1>
                <p>Sign in to your account</p>
            </div>

            @if ($errors->any())
                <div class="error-message" id="controllerErrorMessage">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            @if (session('success'))
                <div class="success-message" id="controllerSuccessMessage">
                    {{ session('success') }}
                </div>
            @endif


            <form id="loginForm" method="POST" action="{{ route('login') }}">
            @csrf
                <div class="form-group">
                    <i class="bi bi-envelope input-icon"></i>
                    <input type="email" name="email" id="email" class="form-input"
                        placeholder="Enter your email" value="{{ old('email') }}" required autofocus>
                </div>
                <div class="form-group">
                    <i class="bi bi-lock input-icon"></i>
                    <input type="password" name="password" id="password" class="form-input"
                        placeholder="Enter your password" required>
                </div>
                <button type="submit" class="login-btn" id="loginButton">
                    <i class="bi bi-box-arrow-in-right"></i>
                    Sign In
                    <div class="loading" id="loadingSpinner">
                        <div class="spinner"></div>
                    </div>
                </button>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            let isValid = true;

            if (!emailInput.value || !emailInput.value.includes('@')) {
                animateError(emailInput);
                isValid = false;
            }
            if (!passwordInput.value) {
                animateError(passwordInput);
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
                return;
            }

            const button = document.getElementById('loginButton');
            const spinner = document.getElementById('loadingSpinner');
            button.disabled = true;
            button.style.pointerEvents = 'none';
            spinner.style.display = 'inline-block';
        });

        function animateError(element) {
            element.style.borderColor = '#dc3545'; 
            element.classList.add('shake-animation');


            element.addEventListener('animationend', () => {
                element.classList.remove('shake-animation');
            });
        }

         const styleSheet = document.createElement("style");
        styleSheet.type = "text/css";
        styleSheet.innerText = `
            .shake-animation {
                animation: shake 0.5s ease-in-out;
            }
        `;
        document.head.appendChild(styleSheet);

        const controllerErrorMsg = document.getElementById('controllerErrorMessage');
        if (controllerErrorMsg) {
            setTimeout(() => {
                controllerErrorMsg.style.display = 'none';
            }, 7000);
        }

        const controllerSuccessMsg = document.getElementById('controllerSuccessMessage');
        if (controllerSuccessMsg) {
            setTimeout(() => {
                controllerSuccessMsg.style.display = 'none';
            }, 7000);
        }

        document.querySelectorAll('.form-input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
            });

            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });

        document.querySelector('.login-card').addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px) scale(1.02)';

            this.style.boxShadow = '0 20px 40px 0 rgba(79, 70, 229, 0.3)';
        });

        document.querySelector('.login-card').addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';

            this.style.boxShadow = '0 8px 32px 0 rgba(67, 56, 202, 0.15)';
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && (document.activeElement.tagName === 'INPUT' || document.activeElement.tagName === 'BUTTON')) {
                if (document.getElementById('loginForm').contains(document.activeElement)) {
                    const loginButton = document.getElementById('loginButton');
                    if (!loginButton.disabled) {
                         document.getElementById('loginForm').dispatchEvent(new Event('submit', { cancelable: true }));
                    }
                }
            }
        });
    </script>
</body>

</html>