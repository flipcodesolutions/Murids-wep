<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Login - Murids Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
</head>

<body>

    <div class="login-page">
        <div class="login-card">
            <div class="login-logo">
                <div class="logo-icon">
                    <img src="{{ asset('assets/img/logo.png') }}" alt="Murids" class="logo-img">
                </div>
                <h1>Murids Admin</h1>
                <p>Sign in to your account</p>
            </div>

            <div id="ajaxErrorBox" class="alert alert-danger mb-3 d-none"></div>

            <form id="loginForm" method="POST" action="{{ route('login.submit') }}" target="hiddenLoginFrame" novalidate>
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label-custom">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="admin@murids.com" required autofocus>
                    </div>
                    <div class="invalid-feedback d-block" id="emailError"></div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label-custom">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                    </div>
                    <div class="invalid-feedback d-block" id="passwordError"></div>
                </div>

                <div class="login-extras">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="remember" name="remember" value="1">
                        <label class="form-check-label" for="remember" style="font-size: 0.85rem;">
                            Remember me
                        </label>
                    </div>

                    <a href="#" class="text-decoration-none" style="font-size: 0.85rem; color: var(--accent-dark);">
                        Forgot password?
                    </a>
                </div>

                <button type="submit" class="btn btn-primary-custom" id="loginBtn">
                    <i class="bi bi-box-arrow-in-right me-2"></i>
                    <span id="loginBtnText">Sign In</span>
                </button>
            </form>

            <iframe name="hiddenLoginFrame" id="hiddenLoginFrame" style="display:none;"></iframe>
        </div>
    </div>

    <script>
        const loginForm = document.getElementById('loginForm');
        const loginBtn = document.getElementById('loginBtn');
        const loginBtnText = document.getElementById('loginBtnText');
        const iframe = document.getElementById('hiddenLoginFrame');

        const ajaxErrorBox = document.getElementById('ajaxErrorBox');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const emailError = document.getElementById('emailError');
        const passwordError = document.getElementById('passwordError');

        let submitted = false;

        function clearErrors() {
            ajaxErrorBox.classList.add('d-none');
            ajaxErrorBox.textContent = '';

            emailInput.classList.remove('is-invalid');
            passwordInput.classList.remove('is-invalid');

            emailError.textContent = '';
            passwordError.textContent = '';
        }

        function setLoading(status) {
            loginBtn.disabled = status;
            loginBtnText.innerHTML = status ?
                '<span class="spinner-border spinner-border-sm me-2"></span>Signing in...' :
                'Sign In';
        }

        function showError(message) {
            ajaxErrorBox.classList.remove('d-none');
            ajaxErrorBox.textContent = message;
        }

        loginForm.addEventListener('submit', function() {
            clearErrors();
            setLoading(true);
            submitted = true;
        });

        iframe.addEventListener('load', function() {
            if (!submitted) return;

            try {
                const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                const currentUrl = iframe.contentWindow.location.href;

                if (currentUrl && !currentUrl.includes('{{ route('login.submit') }}') && !currentUrl.includes('{{ route('login.submit') }}')) {
                    window.location.href = currentUrl;
                    return;
                }

                const emailInvalid = iframeDoc.querySelector('input[name="email"].is-invalid');
                const passwordInvalid = iframeDoc.querySelector('input[name="password"].is-invalid');
                const invalidFeedbacks = iframeDoc.querySelectorAll('.invalid-feedback');
                const alertDanger = iframeDoc.querySelector('.alert.alert-danger');

                if (emailInvalid && invalidFeedbacks[0]) {
                    emailInput.classList.add('is-invalid');
                    emailError.textContent = invalidFeedbacks[0].textContent.trim();
                }

                if (passwordInvalid && invalidFeedbacks[1]) {
                    passwordInput.classList.add('is-invalid');
                    passwordError.textContent = invalidFeedbacks[1].textContent.trim();
                }

                if (alertDanger) {
                    showError(alertDanger.textContent.trim());
                } else if (!emailError.textContent && !passwordError.textContent) {
                    showError('Invalid email or password.');
                }

            } catch (e) {
                showError('Something went wrong. Please try again.');
            } finally {
                setLoading(false);
                submitted = false;
            }
        });
    </script>
</body>

</html>
