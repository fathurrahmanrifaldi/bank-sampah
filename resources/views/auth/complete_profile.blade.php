<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Lengkapi Profil – Bank Sampah RW 042</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
<link rel="icon" type="image" href="{{ asset('images/logo.png') }}">
<style>
body {
  min-height: 100vh;
  background: linear-gradient(135deg, #0f172a 0%, #134e26 100%);
  display: flex; align-items: center; justify-content: center;
  font-family: 'Segoe UI', sans-serif;
  padding: 20px 0;
}
.login-card {
  background: #fff;
  border-radius: 20px;
  padding: 44px 40px;
  width: 100%; max-width: 500px;
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
  <div class="login-logo">🌿</div>
  <h4>Lengkapi Data Diri</h4>
  <p>Selesaikan registrasi akun Google Anda</p>

  @if($errors->any())
    <div class="alert alert-danger mb-3">
      <ul class="mb-0 ps-3">
        @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('nasabah.complete-profile.store') }}">
    @csrf
    
    <div class="mb-3">
      <label class="form-label">Nama Lengkap (Dari Google)</label>
      <input type="text" class="form-control bg-light" value="{{ $googleData['name'] }}" disabled>
    </div>
    
    <div class="mb-3">
      <label class="form-label">Email (Dari Google)</label>
      <input type="email" class="form-control bg-light" value="{{ $googleData['email'] }}" disabled>
    </div>

    <div class="mb-3">
      <label class="form-label">NIK (16 Digit)</label>
          <input type="text" name="nik" class="form-control" value="{{ old('nik') }}" placeholder="Contoh: 3201..." maxlength="16" required autofocus oninput="validateNik(this)">
          <small class="text-danger d-none nik-warning">NIK harus tepat 16 digit angka.</small>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Buat Password</label>
          <div class="input-group">
            <input type="password" name="password" class="form-control" placeholder="Minimal 8 karakter" required>
            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword(this)" style="border-color: #e2e8f0;">
                <i class="bi bi-eye"></i>
            </button>
          </div>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Konfirmasi Password</label>
          <div class="input-group">
            <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi Password" required>
            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword(this)" style="border-color: #e2e8f0;">
                <i class="bi bi-eye"></i>
            </button>
          </div>
        </div>
    </div>



    <button type="submit" class="btn btn-login mb-3">
      <i class="bi bi-check2-circle me-2"></i>Simpan Profil
    </button>
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

function validateNik(input) {
    input.value = input.value.replace(/\D/g, ''); // hanya angka
    const warning = input.nextElementSibling;
    if (warning && warning.classList.contains('nik-warning')) {
        if (input.value.length > 0 && input.value.length < 16) {
            warning.classList.remove('d-none');
            input.classList.add('is-invalid');
        } else {
            warning.classList.add('d-none');
            input.classList.remove('is-invalid');
        }
    }
}
</script>
</body>
</html>
