<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Lupa Password – Bank Sampah RW 042</title>
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
  width: 100%; max-width: 440px;
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
  border-radius: 12px; border: 1.5px solid #e2e8f0;
  padding: 11px 14px; font-size: 14px;
}
.form-control:focus { border-color: #16a34a; box-shadow: 0 0 0 3px rgba(22,163,74,.12); outline: none; }
.form-control.is-invalid { border-color: #ef4444; }
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
.alert-success { background: #f0fdf4; color: #166534; }
.alert-danger  { background: #fef2f2; color: #991b1b; }
.back-link {
  text-align: center; font-size: 13px; color: #64748b; margin-top: 20px;
}
.back-link a { color: #16a34a; font-weight: 600; text-decoration: none; }
.back-link a:hover { text-decoration: underline; }
.invalid-feedback { font-size: 12px; }
</style>
</head>
<body>
<div class="card-box">
  <div class="icon-wrap">
    <i class="bi bi-key-fill"></i>
  </div>
  <h4>Lupa Password?</h4>
  <p class="subtitle">
    Masukkan alamat email yang terdaftar.<br>
    Kami akan mengirim link untuk reset password.
  </p>

  {{-- Status (berhasil atau gagal — pesan sama untuk keamanan) --}}
  @if (session('status'))
    <div class="alert alert-success mb-4">
      <i class="bi bi-check-circle-fill me-2"></i>
      {{ session('status') }}
    </div>
  @endif

  @if ($errors->any())
    <div class="alert alert-danger mb-4">
      <i class="bi bi-exclamation-circle-fill me-2"></i>
      {{ $errors->first() }}
    </div>
  @endif

  <form method="POST" action="{{ route('password.email') }}">
    @csrf

    <div class="mb-4">
      <label for="email" class="form-label">Alamat Email</label>
      <div class="input-group">
        <span class="input-group-text" style="border-radius: 12px 0 0 12px; border: 1.5px solid #e2e8f0; border-right: none; background: #f8fafc;">
          <i class="bi bi-envelope" style="color: #94a3b8;"></i>
        </span>
        <input
          id="email"
          type="email"
          name="email"
          class="form-control @error('email') is-invalid @enderror"
          value="{{ old('email') }}"
          placeholder="nama@gmail.com"
          required
          autocomplete="email"
          autofocus
          style="border-radius: 0 12px 12px 0; border-left: none;"
        >
        @error('email')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
    </div>

    <button type="submit" class="btn-green mb-3">
      <i class="bi bi-send-fill"></i>
      Kirim Link Reset Password
    </button>
  </form>

  <div class="back-link">
    <i class="bi bi-arrow-left me-1"></i>
    <a href="{{ route('login') }}">Kembali ke halaman Login</a>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
