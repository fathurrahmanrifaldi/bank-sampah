<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Verifikasi Email – Bank Sampah RW 042</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
<link rel="icon" type="image" href="{{ asset('images/logo.png') }}">
<style>
* { box-sizing: border-box; }
body {
  min-height: 100vh;
  background: linear-gradient(135deg, #0f172a 0%, #134e26 100%);
  display: flex; align-items: center; justify-content: center;
  font-family: 'Segoe UI', sans-serif;
  padding: 20px 0;
}
.verify-card {
  background: #fff;
  border-radius: 24px;
  padding: 48px 44px;
  width: 100%; max-width: 480px;
  box-shadow: 0 24px 64px rgba(0,0,0,.35);
}
.icon-wrap {
  width: 72px; height: 72px; border-radius: 20px;
  background: linear-gradient(135deg, #16a34a, #15803d);
  display: flex; align-items: center; justify-content: center;
  font-size: 32px; margin: 0 auto 24px;
  color: #fff;
}
.verify-card h4 {
  text-align: center; font-weight: 700;
  color: #0f172a; margin-bottom: 8px; font-size: 22px;
}
.verify-card .subtitle {
  text-align: center; color: #64748b;
  font-size: 13.5px; margin-bottom: 28px; line-height: 1.6;
}
.email-badge {
  background: #f0fdf4; border: 1.5px solid #bbf7d0;
  border-radius: 10px; padding: 10px 16px;
  text-align: center; font-weight: 600; color: #15803d;
  font-size: 14px; margin-bottom: 28px;
  word-break: break-all;
}
.alert {
  border-radius: 12px; font-size: 13px; border: none;
  padding: 12px 16px;
}
.alert-success { background: #f0fdf4; color: #166534; }
.alert-danger  { background: #fef2f2; color: #991b1b; }
.alert-warning { background: #fffbeb; color: #92400e; }
.btn-primary-green {
  background: linear-gradient(135deg, #16a34a, #15803d);
  color: #fff; border: none; border-radius: 12px;
  padding: 12px; font-weight: 600; font-size: 14px;
  width: 100%; transition: opacity .2s; cursor: pointer;
  display: flex; align-items: center; justify-content: center; gap: 8px;
}
.btn-primary-green:hover { opacity: .9; color: #fff; }
.btn-primary-green:disabled { opacity: .6; cursor: not-allowed; }
.divider {
  display: flex; align-items: center; gap: 12px;
  margin: 20px 0; color: #94a3b8; font-size: 12px;
}
.divider::before, .divider::after {
  content: ''; flex: 1; height: 1px; background: #e2e8f0;
}
.steps {
  background: #f8fafc; border-radius: 12px; padding: 16px 20px;
  margin-bottom: 24px;
}
.steps p { margin: 0; font-size: 12.5px; color: #64748b; font-weight: 600; margin-bottom: 10px; }
.step-item {
  display: flex; align-items: flex-start; gap: 10px;
  font-size: 13px; color: #475569; margin-bottom: 8px;
}
.step-item:last-child { margin-bottom: 0; }
.step-num {
  width: 20px; height: 20px; border-radius: 50%;
  background: #16a34a; color: #fff; font-size: 11px;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0; font-weight: 700; margin-top: 1px;
}
#countdown-wrap { font-size: 12px; color: #94a3b8; text-align: center; margin-top: 10px; }
.back-link {
  text-align: center; font-size: 13px; color: #64748b; margin-top: 20px;
}
.back-link a { color: #16a34a; font-weight: 600; text-decoration: none; }
.back-link a:hover { text-decoration: underline; }
</style>
</head>
<body>
<div class="verify-card">
  <div class="icon-wrap">
    <i class="bi bi-envelope-check-fill"></i>
  </div>
  <h4>Verifikasi Email Anda</h4>
  <p class="subtitle">
    Kami telah mengirim link verifikasi ke email berikut:
  </p>

  <div class="email-badge">
    <i class="bi bi-envelope me-2"></i>{{ auth()->user()->email }}
  </div>

  {{-- Alert sukses kirim ulang --}}
  @if (session('resent'))
    <div class="alert alert-success mb-4">
      <i class="bi bi-check-circle-fill me-2"></i>
      <strong>Email terkirim!</strong> Link verifikasi baru telah dikirim ke inbox Anda.
    </div>
  @endif

  {{-- Alert error rate limit --}}
  @if (session('resend_error'))
    <div class="alert alert-warning mb-4">
      <i class="bi bi-exclamation-triangle-fill me-2"></i>
      {{ session('resend_error') }}
    </div>
  @endif

  {{-- Langkah-langkah --}}
  <div class="steps">
    <p><i class="bi bi-list-check me-1"></i> Cara verifikasi:</p>
    <div class="step-item">
      <span class="step-num">1</span>
      <span>Buka aplikasi email Anda (Gmail, Outlook, dll.)</span>
    </div>
    <div class="step-item">
      <span class="step-num">2</span>
      <span>Cari email dari <strong>Bank Sampah 042</strong></span>
    </div>
    <div class="step-item">
      <span class="step-num">3</span>
      <span>Klik tombol <strong>"Verify Email Address"</strong> di dalam email</span>
    </div>
    <div class="step-item">
      <span class="step-num">4</span>
      <span>Anda akan langsung diarahkan ke dashboard</span>
    </div>
  </div>

  {{-- Tombol kirim ulang --}}
  <form method="POST" action="{{ route('verification.resend') }}" id="resend-form">
    @csrf
    <button type="submit" class="btn-primary-green" id="resend-btn">
      <i class="bi bi-send-fill"></i>
      <span id="btn-text">Kirim Ulang Email Verifikasi</span>
    </button>
  </form>

  <div id="countdown-wrap" class="d-none">
    <i class="bi bi-clock me-1"></i>
    Kirim ulang tersedia dalam <strong id="countdown">60</strong> detik
  </div>

  <div class="divider">atau</div>

  <div class="back-link">
    Salah email? <a href="{{ route('logout') }}"
      onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
      Keluar dan daftar ulang
    </a>
  </div>
  <form method="POST" action="{{ route('logout') }}" id="logout-form" class="d-none">@csrf</form>
</div>

<script>
// Cooldown timer setelah klik kirim ulang
const resendForm = document.getElementById('resend-form');
const resendBtn  = document.getElementById('resend-btn');
const btnText    = document.getElementById('btn-text');
const countdownWrap = document.getElementById('countdown-wrap');
const countdownEl   = document.getElementById('countdown');

@if(session('resent'))
  // Jika baru kirim, langsung mulai countdown
  startCooldown();
@endif

resendForm.addEventListener('submit', function() {
  resendBtn.disabled = true;
  btnText.textContent = 'Mengirim...';
});

function startCooldown() {
  resendBtn.disabled = true;
  countdownWrap.classList.remove('d-none');
  let secs = 60;
  countdownEl.textContent = secs;

  const timer = setInterval(() => {
    secs--;
    countdownEl.textContent = secs;
    if (secs <= 0) {
      clearInterval(timer);
      resendBtn.disabled = false;
      btnText.textContent = 'Kirim Ulang Email Verifikasi';
      countdownWrap.classList.add('d-none');
    }
  }, 1000);
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
