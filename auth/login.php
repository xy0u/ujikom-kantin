<?php
session_start();
require '../core/database.php';
require '../core/helpers.php';

if (isLoggedIn()) redirect('/public/index.php');

$error   = '';
$success = getFlash();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid request. Coba lagi.';
    } else {
        $email    = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!$email || !$password) {
            $error = 'Email dan password wajib diisi.';
        } else {
            $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE email = ?");
            mysqli_stmt_bind_param($stmt, 's', $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $user   = mysqli_fetch_assoc($result);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name']    = $user['name'];
                $_SESSION['email']   = $user['email'];
                $_SESSION['role']    = $user['role'];

                $redirect = $_GET['redirect'] ?? ($user['role'] === 'admin' ? '/admin/dashboard.php' : '/public/index.php');
                redirect($redirect);
            } else {
                $error = 'Email atau password salah.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Kantin Digital</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/public/assets/css/auth.css">
</head>
<body class="auth-body">

<!-- Background Effects -->
<div class="auth-bg">
    <div class="auth-bg-grid"></div>
    <div class="auth-bg-glow auth-bg-glow--1"></div>
    <div class="auth-bg-glow auth-bg-glow--2"></div>
</div>

<div class="auth-container">

    <!-- Left Panel (decorative) -->
    <div class="auth-panel auth-panel--left">
        <div class="auth-panel-content">
            <a href="/public/index.php" class="auth-logo">
                <span>[</span>KD<span>]</span>
            </a>
            <h1 class="auth-panel-title">KANTIN<br>DIGITAL</h1>
            <p class="auth-panel-desc">Platform pemesanan makanan &amp; minuman digital untuk lingkungan sekolah yang modern.</p>

            <div class="auth-panel-features">
                <div class="auth-feature">
                    <div class="auth-feature-icon">⚡</div>
                    <div>
                        <div class="auth-feature-title">Order Cepat</div>
                        <div class="auth-feature-desc">Pesan dalam hitungan detik</div>
                    </div>
                </div>
                <div class="auth-feature">
                    <div class="auth-feature-icon">🔒</div>
                    <div>
                        <div class="auth-feature-title">Aman & Terpercaya</div>
                        <div class="auth-feature-desc">Data selalu terlindungi</div>
                    </div>
                </div>
                <div class="auth-feature">
                    <div class="auth-feature-icon">📱</div>
                    <div>
                        <div class="auth-feature-title">Mobile Friendly</div>
                        <div class="auth-feature-desc">Akses dari mana saja</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Panel (form) -->
    <div class="auth-panel auth-panel--right">
        <div class="auth-form-wrap">

            <!-- Mobile Logo -->
            <a href="/public/index.php" class="auth-logo-mobile">[KD]</a>

            <div class="auth-form-header">
                <h2 class="auth-form-title">Selamat Datang</h2>
                <p class="auth-form-subtitle">Masuk ke akun Kantin Digital Anda</p>
            </div>

            <?php if ($error): ?>
            <div class="auth-alert auth-alert--error">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <?= e($error) ?>
            </div>
            <?php endif; ?>

            <?php if ($success): ?>
            <div class="auth-alert auth-alert--success">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
                <?= e($success['message']) ?>
            </div>
            <?php endif; ?>

            <form method="POST" class="auth-form" id="loginForm" novalidate>
                <?= csrfField() ?>

                <!-- Email -->
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <div class="input-icon-wrap">
                        <svg class="input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                            <polyline points="22,6 12,13 2,6"/>
                        </svg>
                        <input
                            type="email"
                            name="email"
                            id="email"
                            class="input"
                            placeholder="nama@email.com"
                            value="<?= e($_POST['email'] ?? '') ?>"
                            required
                            autocomplete="email"
                        >
                    </div>
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password" class="form-label">
                        Password
                        <a href="/auth/forgot.php" class="form-label-link">Lupa password?</a>
                    </label>
                    <div class="input-icon-wrap input-password-wrap">
                        <svg class="input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                        <input
                            type="password"
                            name="password"
                            id="password"
                            class="input"
                            placeholder="Masukkan password"
                            required
                            autocomplete="current-password"
                        >
                        <button type="button" class="input-toggle-pw" id="togglePw" aria-label="Tampilkan password">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" id="eyeIcon">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="form-check">
                    <input type="checkbox" name="remember" id="remember" class="form-check-input">
                    <label for="remember" class="form-check-label">Ingat saya</label>
                </div>

                <button type="submit" class="btn btn--primary btn--full btn--lg" id="submitBtn">
                    <span class="btn-text">Masuk</span>
                    <span class="btn-loader" hidden>
                        <svg class="spin" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
                        </svg>
                    </span>
                </button>
            </form>

            <p class="auth-switch">
                Belum punya akun?
                <a href="/auth/register.php" class="auth-switch-link">Daftar sekarang</a>
            </p>

            <a href="/public/index.php" class="auth-back-link">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="15 18 9 12 15 6"/>
                </svg>
                Kembali ke Beranda
            </a>
        </div>
    </div>
</div>

<script>
// Password toggle
document.getElementById('togglePw')?.addEventListener('click', function() {
    const pw = document.getElementById('password');
    const isText = pw.type === 'text';
    pw.type = isText ? 'password' : 'text';
    document.getElementById('eyeIcon').innerHTML = isText
        ? '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>'
        : '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>';
});

// Loading state on submit
document.getElementById('loginForm')?.addEventListener('submit', function() {
    const btn  = document.getElementById('submitBtn');
    const text = btn.querySelector('.btn-text');
    const loader = btn.querySelector('.btn-loader');
    btn.disabled = true;
    text.hidden  = true;
    loader.hidden = false;
});
</script>
</body>
</html>