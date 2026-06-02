<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login – Bank Sampah RW 042</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
<style>
body {
  min-height: 100vh;
  background: linear-gradient(135deg, #0f172a 0%, #134e26 100%);
  display: flex; align-items: center; justify-content: center;
  font-family: 'Segoe UI', sans-serif;
}
.login-card {
  background: #fff;
  border-radius: 20px;
  padding: 44px 40px;
  width: 100%; max-width: 400px;
  box-shadow: 0 24px 64px rgba(0,0,0,.35);
}
.login-logo {
  width: 56px; height: 56px; border-radius: 14px;
  background: linear-gradient(135deg, #16a34a, #15803d);
  display: flex; align-items: center; justify-content: center;
  font-size: 26px; margin: 0 auto 20px;
}
.login-card h4 {
  text-align: center; font-weight: 700;
  color: #0f172a; margin-bottom: 4px;
}
.login-card p {
  text-align: center; color: #64748b;
  font-size: 13px; margin-bottom: 28px;
}
.form-label { font-size: 13px; font-weight: 600; color: #374151; }
.form-control {
  border-radius: 10px; border: 1.5px solid #e2e8f0;
  padding: 10px 14px; font-size: 14px;
}
.form-control:focus { border-color: #16a34a; box-shadow: 0 0 0 3px rgba(22,163,74,.12); }
.btn-login {
  background: linear-gradient(135deg, #16a34a, #15803d);
  color: #fff; border: none; border-radius: 10px;
  padding: 11px; font-weight: 600; font-size: 14px;
  width: 100%; transition: opacity .2s;
}
.btn-login:hover { opacity: .9; color: #fff; }
.alert { border-radius: 10px; font-size: 13px; border: none; }
</style>
</head>
<body>
<div class="login-card">
  <img src="{{ asset('images/logo.png') }}" alt="Logo" class="login-logo">
  <h4>Bank Sampah RW 042</h4>
  <p>Kelurahan Bahagia · Kecamatan Babelan · Bekasi</p>

  @if(session('success'))
    <div class="alert alert-success mb-3">
      <i class="bi bi-check-circle me-1"></i>
      {{ session('success') }}
    </div>
  @endif

  @if(session('error'))
    <div class="alert alert-danger mb-3">
      <i class="bi bi-exclamation-circle me-1"></i>
      {{ session('error') }}
    </div>
  @endif

  @if($errors->any())
    <div class="alert alert-danger mb-3">
      <i class="bi bi-exclamation-circle me-1"></i>
      {{ $errors->first() }}
    </div>
  @endif

  <form method="POST" action="{{ route('login') }}">
    @csrf
    <div class="mb-3">
      <label class="form-label">Email</label>
      <input type="email" name="email" class="form-control"
             value="{{ old('email') }}" placeholder="nama@gmail.com" required autofocus>
    </div>
    <div class="mb-3">
      <label class="form-label">Password</label>
      <div class="input-group">
        <input type="password" name="password" class="form-control"
               placeholder="Masukan Password" required> 
        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword(this)" style="border-color: #e2e8f0;">
            <i class="bi bi-eye"></i>
        </button>
      </div>
    </div>
    <div class="form-check mb-4">
      <input class="form-check-input" type="checkbox" name="remember" id="remember">
      <label class="form-check-label" for="remember" style="font-size:13px;color:#64748b">
        Ingat saya
      </label>
    </div>
    <button type="submit" class="btn btn-login mb-3">
      <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
    </button>
    
    <p class="text-center mt-3" style="font-size: 13px; color: #64748b;">
        Belum punya akun? <a href="{{ route('register') }}" style="color: #16a34a; font-weight: 600; text-decoration: none;">Registrasi di sini</a>
    </p>
  </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function togglePassword(btn) {
    const input = btn.previousElementSibling;
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}
</script>
</body>
</html>