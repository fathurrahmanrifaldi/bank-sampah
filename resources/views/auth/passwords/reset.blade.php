<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reset Password – Bank Sampah RW 042</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
<style>
* { box-sizing: border-box; }
body {
  min-height: 100vh;
  background: linear-gradient(135deg, #0f172a 0%, #134e26 100%);
  display: flex; align-items: center; justify-content: center;
  font-family: 'Segoe UI', sans-serif;
  padding: 20px 0;
}
.card-box {
  background: #fff;
  border-radius: 24px;
  padding: 48px 44px;
  width: 100%; max-width: 460px;
  box-shadow: 0 24px 64px rgba(0,0,0,.35);
}
.icon-wrap {
  width: 72px; height: 72px; border-radius: 20px;
  background: linear-gradient(135deg, #16a34a, #15803d);
  display: flex; align-items: center; justify-content: center;
  font-size: 32px; margin: 0 auto 24px; color: #fff;
}
h4 {
  text-align: center; font-weight: 700;
  color: #0f172a; margin-bottom: 6px; font-size: 22px;
}
.subtitle {
  text-align: center; color: #64748b;
  font-size: 13.5px; margin-bottom: 28px; line-height: 1.6;
}
.form-label { font-size: 13px; font-weight: 600; color: #374151; }
.form-control {
  border-radius: 10px; border: 1.5px solid #e2e8f0;
  padding: 11px 14px; font-size: 14px;
}
.form-control:focus { border-color: #16a34a; box-shadow: 0 0 0 3px rgba(22,163,74,.12); outline: none; }
.form-control.is-invalid { border-color: #ef4444; }
.input-group .form-control { border-radius: 10px 0 0 10px; }
.input-group .btn-toggle { border-radius: 0 10px 10px 0; border: 1.5px solid #e2e8f0; border-left: none; background: #f8fafc; }
.btn-green {
  background: linear-gradient(135deg, #16a34a, #15803d);
  color: #fff; border: none; border-radius: 12px;
  padding: 12px; font-weight: 600; font-size: 14px;
  width: 100%; transition: opacity .2s; cursor: pointer;
  display: flex; align-items: center; justify-content: center; gap: 8px;
}
.btn-green:hover { opacity: .9; color: #fff; }
.alert {
  border-radius: 12px; font-size: 13px; border: none;
  padding: 12px 16px;
}
.alert-danger { background: #fef2f2; color: #991b1b; }
.password-strength { height: 4px; border-radius: 4px; margin-top: 6px; transition: all .3s; }
.strength-label { font-size: 11px; margin-top: 4px; font-weight: 600; }
.invalid-feedback { font-size: 12px; }
.back-link {
  text-align: center; font-size: 13px; color: #64748b; margin-top: 20px;
}
.back-link a { color: #16a34a; font-weight: 600; text-decoration: none; }
.back-link a:hover { text-decoration: underline; }
</style>
</head>
<body>
<div class="card-box">
  <div class="icon-wrap">
    <i class="bi bi-shield-lock-fill"></i>
  </div>
  <h4>Buat Password Baru</h4>
  <p class="subtitle">
    Masukkan password baru untuk akun Anda.<br>
    Pastikan minimal 8 karakter.
  </p>

  @if ($errors->any())
    <div class="alert alert-danger mb-4">
      <i class="bi bi-exclamation-circle-fill me-2"></i>
      {{ $errors->first() }}
    </div>
  @endif

  <form method="POST" action="{{ route('password.update') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">

    {{-- Email (hidden, prefilled) --}}
    <div class="mb-3">
      <label for="email" class="form-label">Alamat Email</label>
      <input
        id="email"
        type="email"
        name="email"
        class="form-control @error('email') is-invalid @enderror"
        value="{{ $email ?? old('email') }}"
        required
        autocomplete="email"
        readonly
        style="background: #f8fafc; color: #64748b;"
      >
      @error('email')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    {{-- Password Baru --}}
    <div class="mb-3">
      <label for="password" class="form-label">Password Baru</label>
      <div class="input-group">
        <input
          id="password"
          type="password"
          name="password"
          class="form-control @error('password') is-invalid @enderror"
          placeholder="Minimal 8 karakter"
          required
          autocomplete="new-password"
          oninput="checkStrength(this.value)"
        >
        <button class="btn btn-toggle" type="button" onclick="togglePassword(this)">
          <i class="bi bi-eye"></i>
        </button>
        @error('password')
          <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
      </div>
      {{-- Password Strength Bar --}}
      <div class="password-strength bg-secondary" id="strength-bar" style="width: 0%;"></div>
      <div class="strength-label" id="strength-label" style="color: #94a3b8;"></div>
    </div>

    {{-- Konfirmasi Password --}}
    <div class="mb-4">
      <label for="password-confirm" class="form-label">Konfirmasi Password Baru</label>
      <div class="input-group">
        <input
          id="password-confirm"
          type="password"
          name="password_confirmation"
          class="form-control"
          placeholder="Ulangi password baru"
          required
          autocomplete="new-password"
        >
        <button class="btn btn-toggle" type="button" onclick="togglePassword(this)">
          <i class="bi bi-eye"></i>
        </button>
      </div>
    </div>

    <button type="submit" class="btn-green mb-3">
      <i class="bi bi-check-circle-fill"></i>
      Simpan Password Baru
    </button>
  </form>

  <div class="back-link">
    <i class="bi bi-arrow-left me-1"></i>
    <a href="{{ route('login') }}">Kembali ke halaman Login</a>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function togglePassword(btn) {
  const input = btn.previousElementSibling;
  const icon = btn.querySelector('i');
  if (input.type === 'password') {
    input.type = 'text';
    icon.classList.replace('bi-eye', 'bi-eye-slash');
  } else {
    input.type = 'password';
    icon.classList.replace('bi-eye-slash', 'bi-eye');
  }
}

function checkStrength(val) {
  const bar   = document.getElementById('strength-bar');
  const label = document.getElementById('strength-label');
  let score = 0;
  if (val.length >= 8)                        score++;
  if (/[A-Z]/.test(val))                      score++;
  if (/[0-9]/.test(val))                      score++;
  if (/[^A-Za-z0-9]/.test(val))               score++;

  const levels = [
    { color: '#ef4444', text: 'Sangat Lemah', width: '25%' },
    { color: '#f97316', text: 'Lemah',        width: '50%' },
    { color: '#eab308', text: 'Cukup',        width: '75%' },
    { color: '#22c55e', text: 'Kuat',         width: '100%' },
  ];

  if (val.length === 0) {
    bar.style.width = '0'; bar.style.background = '#e2e8f0';
    label.textContent = '';
    return;
  }

  const level = levels[Math.max(0, score - 1)];
  bar.style.width      = level.width;
  bar.style.background = level.color;
  label.textContent    = level.text;
  label.style.color    = level.color;
}
</script>
</body>
</html>
