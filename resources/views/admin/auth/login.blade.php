<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <title>Login - Murids Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

  <div class="login-page">
    <div class="login-card">
      <div class="login-logo">
        <div class="logo-icon">
          <img src="assets/img/logo.png" alt="Murids" class="logo-img">
        </div>
        <h1>Murids Admin</h1>
        <p>Sign in to your account</p>
      </div>

      <form id="loginForm" novalidate>
        <div class="mb-3">
          <label for="email" class="form-label-custom">Email Address</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
            <input type="email" class="form-control" id="email" placeholder="admin@murids.com" required>
            <div class="invalid-feedback">Please enter a valid email.</div>
          </div>
        </div>

        <div class="mb-3">
          <label for="password" class="form-label-custom">Password</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <input type="password" class="form-control" id="password" placeholder="Enter your password" required minlength="6">
            <div class="invalid-feedback">Password must be at least 6 characters.</div>
          </div>
        </div>

        <div class="login-extras">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="remember">
            <label class="form-check-label" for="remember" style="font-size: 0.85rem;">Remember me</label>
          </div>
          <a href="#" class="text-decoration-none" style="font-size: 0.85rem; color: var(--accent-dark);">Forgot password?</a>
        </div>

        <button type="submit" class="btn btn-primary-custom">
          <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
        </button>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/main.js"></script>
</body>
</html>

