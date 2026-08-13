<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Continue with Google & Apple</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: radial-gradient(circle at top left, #1e1b4b, #0f172a, #020617);
            color: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
        }
        .login-wrapper {
            width: 100%;
            max-width: 440px;
        }
        .login-card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 36px 32px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
        }
        .brand-logo {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #6366f1, #a855f7);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px auto;
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.3);
        }
        .brand-logo svg {
            width: 24px;
            height: 24px;
            fill: #ffffff;
        }
        .login-header h2 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 6px;
            color: #ffffff;
        }
        .login-header p {
            color: #94a3b8;
            font-size: 0.9rem;
        }
        .form-control {
            background-color: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: #ffffff;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }
        .form-control:focus {
            background-color: rgba(15, 23, 42, 0.8);
            border-color: #818cf8;
            color: #ffffff;
            box-shadow: 0 0 0 4px rgba(129, 140, 248, 0.15);
        }
        .form-label {
            font-size: 0.85rem;
            font-weight: 500;
            color: #cbd5e1;
            margin-bottom: 6px;
        }
        .btn-primary-action {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            border: none;
            color: #ffffff;
            font-weight: 600;
            border-radius: 10px;
            padding: 12px;
            width: 100%;
            font-size: 0.95rem;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }
        .btn-primary-action:hover {
            background: linear-gradient(135deg, #4338ca, #6d28d9);
            color: #ffffff;
            box-shadow: 0 6px 16px rgba(79, 70, 229, 0.4);
        }
        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 24px 0;
            color: #64748b;
            font-size: 0.8rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .divider span {
            padding: 0 12px;
        }
        .btn-social {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            width: 100%;
            padding: 11px 16px;
            border-radius: 10px;
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.2s ease;
            cursor: pointer;
            text-decoration: none;
        }
        .btn-google {
            background-color: #ffffff;
            color: #1e293b;
            border: 1px solid #e2e8f0;
        }
        .btn-google:hover {
            background-color: #f8fafc;
            color: #0f172a;
            box-shadow: 0 4px 12px rgba(255, 255, 255, 0.1);
        }
        .btn-apple {
            background-color: #000000;
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .btn-apple:hover {
            background-color: #111111;
            color: #ffffff;
            border-color: rgba(255, 255, 255, 0.4);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }
        .social-icon {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
        }
        .response-card {
            background: rgba(15, 23, 42, 0.9);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 20px;
            margin-top: 20px;
        }
        pre {
            margin: 0;
            color: #38bdf8;
            font-size: 0.85rem;
            white-space: pre-wrap;
            word-break: break-all;
        }
        .status-badge {
            font-size: 0.75rem;
            padding: 4px 8px;
            border-radius: 6px;
            font-weight: 600;
        }
    </style>
</head>
<body>

<div class="login-wrapper">
    <div class="login-card">
        <div class="brand-logo">
            <svg viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14.5v-9l6 4.5-6 4.5z"/>
            </svg>
        </div>

        <div class="login-header text-center">
            <h2>Welcome Back</h2>
            <p>Please enter your details or sign in with social account</p>
        </div>

        <form id="emailLoginForm" onsubmit="event.preventDefault(); handleStandardLogin();" class="mt-4">
            <div class="mb-3">
                <label for="loginEmail" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="loginEmail" placeholder="name@example.com" value="user@example.com" required>
            </div>

            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label for="loginPassword" class="form-label mb-0">Password</label>
                    <a href="#" class="text-decoration-none" style="font-size: 0.8rem; color: #818cf8;">Forgot password?</a>
                </div>
                <input type="password" class="form-control" id="loginPassword" placeholder="••••••••" value="123456" required>
            </div>

            <button type="submit" class="btn btn-primary-action mt-2">Sign In</button>
        </form>

        <div class="divider">
            <span>Or continue with</span>
        </div>

        <div class="d-flex flex-column gap-2">
            <!-- Continue with Google Button -->
            <button type="button" class="btn btn-social btn-google" id="btnContinueGoogle">
                <svg class="social-icon" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                </svg>
                <span>Continue with Google</span>
            </button>

            <!-- Continue with Apple Button -->
            <button type="button" class="btn btn-social btn-apple" id="btnContinueApple">
                <svg class="social-icon" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M15.97 6.35c.64-.78 1.08-1.85.96-2.93-.93.04-2.06.62-2.72 1.4-.59.68-1.1 1.78-.96 2.84 1.05.08 2.1-.53 2.72-1.31z"/>
                </svg>
                <span>Continue with Apple</span>
            </button>
        </div>

        <p class="text-center text-secondary mt-4 mb-0" style="font-size: 0.85rem;">
            Don't have an account? <a href="#" class="text-decoration-none" style="color: #818cf8; font-weight: 600;">Sign up</a>
        </p>
    </div>

    <!-- Live API Response Preview Box -->
    <div class="response-card" id="apiResponseCard" style="display: none;">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="fw-bold text-white" style="font-size: 0.85rem;" id="responseTitle">API Response:</span>
            <span id="apiStatusBadge" class="status-badge bg-secondary text-white">Status: -</span>
        </div>
        <pre><code id="apiJsonResult">Response will appear here...</code></pre>
    </div>
</div>

<script>
    const baseUrl = "{{ url('') }}";

    function showResponse(title, statusText, isSuccess, data) {
        const card = document.getElementById('apiResponseCard');
        const titleEl = document.getElementById('responseTitle');
        const badge = document.getElementById('apiStatusBadge');
        const jsonEl = document.getElementById('apiJsonResult');

        card.style.display = 'block';
        titleEl.textContent = title;
        badge.textContent = statusText;
        badge.className = `status-badge ${isSuccess ? 'bg-success' : 'bg-danger'} text-white`;
        jsonEl.textContent = JSON.stringify(data, null, 2);

        card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    // Standard Email/Password Login
    async function handleStandardLogin() {
        const email = document.getElementById('loginEmail').value;
        const password = document.getElementById('loginPassword').value;

        try {
            const res = await fetch(`${baseUrl}/api/login`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ email, password })
            });

            const data = await res.json();
            showResponse('POST /api/login', `Status: ${res.status}`, res.ok, data);
        } catch (err) {
            showResponse('POST /api/login', 'Error', false, { error: err.message });
        }
    }

    // Continue with Google Redirect Handler
    document.getElementById('btnContinueGoogle').addEventListener('click', function() {
        window.location.href = `${baseUrl}/api/google/redirect`;
    });

    // Continue with Apple Redirect Handler
    document.getElementById('btnContinueApple').addEventListener('click', function() {
        window.location.href = `${baseUrl}/api/apple/redirect`;
    });
</script>

</body>
</html>
