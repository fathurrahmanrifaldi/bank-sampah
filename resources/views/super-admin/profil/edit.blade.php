@extends('layouts.super-admin')
@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')

@section('content')

<div class="row justify-content-center">
  <div class="col-lg-7">

    {{-- ══ INFO PROFIL ═══════════════════════════════════════════════════════ --}}
    <div class="table-card mb-4">
      <div class="card-header">
        <h6><i class="bi bi-person-circle me-2" style="color:#4f46e5"></i>Informasi Profil</h6>
      </div>
      <div style="padding:24px">

        {{-- Avatar & Role Badge --}}
        <div class="d-flex align-items-center gap-4 mb-5">
          <div style="width:72px;height:72px;border-radius:50%;
                      background:linear-gradient(135deg,#4f46e5 0%,#7c3aed 100%);
                      display:flex;align-items:center;justify-content:center;
                      font-size:28px;font-weight:800;color:#fff;flex-shrink:0;
                      box-shadow:0 4px 16px rgba(79,70,229,.4)">
            {{ strtoupper(substr($user->name, 0, 1)) }}
          </div>
          <div>
            <div style="font-size:18px;font-weight:800;color:#0f172a">{{ $user->name }}</div>
            <div style="font-size:13px;color:#64748b;margin-top:2px">{{ $user->email }}</div>
            <span style="display:inline-flex;align-items:center;gap:4px;margin-top:6px;
                         background:linear-gradient(90deg,#4f46e5,#7c3aed);color:#fff;
                         font-size:10px;font-weight:700;padding:3px 10px;border-radius:20px;letter-spacing:.6px">
              <i class="bi bi-shield-fill-check" style="font-size:9px"></i> SUPER ADMIN
            </span>
          </div>
        </div>

        @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        @endif

        {{-- Form Edit Nama --}}
        <form method="POST" action="{{ route('super-admin.profil.update') }}">
          @csrf @method('PUT')
          <div class="mb-4">
            <label class="form-label" style="font-weight:600;font-size:13px;color:#374151">
              Nama Lengkap <span style="color:#ef4444">*</span>
            </label>
            <input type="text" name="name" id="name"
                   value="{{ old('name', $user->name) }}"
                   class="form-control @error('name') is-invalid @enderror"
                   style="border-radius:10px;border-color:#e2e8f0;font-size:13.5px;padding:10px 14px">
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="mb-4">
            <label class="form-label" style="font-weight:600;font-size:13px;color:#374151">Email</label>
            <input type="email" value="{{ $user->email }}" disabled
                   class="form-control"
                   style="border-radius:10px;background:#f8fafc;border-color:#e2e8f0;font-size:13.5px;padding:10px 14px;color:#64748b">
            <div style="font-size:11.5px;color:#94a3b8;margin-top:4px">
              <i class="bi bi-lock me-1"></i>Email tidak dapat diubah
            </div>
          </div>
          <button type="submit" class="btn"
                  style="background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;
                         font-weight:700;border:none;padding:10px 28px;border-radius:10px;
                         box-shadow:0 4px 12px rgba(79,70,229,.35)">
            <i class="bi bi-check2-circle me-2"></i>Simpan Nama
          </button>
        </form>
      </div>
    </div>

    {{-- ══ UBAH PASSWORD ══════════════════════════════════════════════════════ --}}
    <div class="table-card">
      <div class="card-header">
        <h6><i class="bi bi-key-fill me-2" style="color:#4f46e5"></i>Ubah Password</h6>
      </div>
      <div style="padding:24px">

        @if(session('success_password'))
          <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success_password') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        @endif

        @if($errors->has('current_password'))
          <div class="alert alert-danger mb-4">
            <i class="bi bi-exclamation-circle me-2"></i>{{ $errors->first('current_password') }}
          </div>
        @endif

        <form method="POST" action="{{ route('super-admin.profil.password') }}">
          @csrf @method('PUT')
          <div class="mb-4">
            <label class="form-label" style="font-weight:600;font-size:13px;color:#374151">
              Password Saat Ini <span style="color:#ef4444">*</span>
            </label>
            <div style="position:relative">
              <input type="password" name="current_password" id="currentPwd"
                     class="form-control @error('current_password') is-invalid @enderror"
                     placeholder="Masukkan password lama"
                     style="border-radius:10px;border-color:#e2e8f0;font-size:13.5px;padding:10px 40px 10px 14px">
              <button type="button" onclick="togglePwd('currentPwd','eyeC')"
                      style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:#94a3b8;cursor:pointer">
                <i class="bi bi-eye" id="eyeC"></i>
              </button>
            </div>
          </div>

          <div class="mb-4">
            <label class="form-label" style="font-weight:600;font-size:13px;color:#374151">
              Password Baru <span style="color:#ef4444">*</span>
            </label>
            <div style="position:relative">
              <input type="password" name="password" id="newPwd"
                     class="form-control @error('password') is-invalid @enderror"
                     placeholder="Minimal 8 karakter"
                     style="border-radius:10px;border-color:#e2e8f0;font-size:13.5px;padding:10px 40px 10px 14px"
                     oninput="checkStrength(this.value)">
              <button type="button" onclick="togglePwd('newPwd','eyeN')"
                      style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:#94a3b8;cursor:pointer">
                <i class="bi bi-eye" id="eyeN"></i>
              </button>
            </div>
            @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            {{-- Strength indicator --}}
            <div style="margin-top:8px">
              <div style="height:4px;border-radius:4px;background:#e2e8f0;overflow:hidden">
                <div id="strengthBar" style="height:100%;width:0;border-radius:4px;transition:all .3s"></div>
              </div>
              <div id="strengthText" style="font-size:11px;color:#94a3b8;margin-top:4px"></div>
            </div>
          </div>

          <div class="mb-5">
            <label class="form-label" style="font-weight:600;font-size:13px;color:#374151">
              Konfirmasi Password Baru <span style="color:#ef4444">*</span>
            </label>
            <div style="position:relative">
              <input type="password" name="password_confirmation" id="confirmPwd"
                     class="form-control"
                     placeholder="Ulangi password baru"
                     style="border-radius:10px;border-color:#e2e8f0;font-size:13.5px;padding:10px 40px 10px 14px">
              <button type="button" onclick="togglePwd('confirmPwd','eyeConf')"
                      style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:#94a3b8;cursor:pointer">
                <i class="bi bi-eye" id="eyeConf"></i>
              </button>
            </div>
          </div>

          <button type="submit" class="btn"
                  style="background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;
                         font-weight:700;border:none;padding:10px 28px;border-radius:10px;
                         box-shadow:0 4px 12px rgba(79,70,229,.35)">
            <i class="bi bi-shield-lock-fill me-2"></i>Ubah Password
          </button>
        </form>
      </div>
    </div>

  </div>
</div>

@endsection

@push('scripts')
<script>
function togglePwd(inputId, eyeId) {
  const input = document.getElementById(inputId);
  const eye   = document.getElementById(eyeId);
  if (input.type === 'password') {
    input.type = 'text';
    eye.className = 'bi bi-eye-slash';
  } else {
    input.type = 'password';
    eye.className = 'bi bi-eye';
  }
}

function checkStrength(val) {
  const bar  = document.getElementById('strengthBar');
  const text = document.getElementById('strengthText');
  let score = 0;
  if (val.length >= 8)  score++;
  if (/[A-Z]/.test(val)) score++;
  if (/[0-9]/.test(val)) score++;
  if (/[^A-Za-z0-9]/.test(val)) score++;

  const levels = [
    { w: '25%', color: '#ef4444', label: 'Sangat Lemah' },
    { w: '50%', color: '#f59e0b', label: 'Lemah' },
    { w: '75%', color: '#3b82f6', label: 'Cukup Kuat' },
    { w: '100%', color: '#16a34a', label: 'Kuat' },
  ];
  if (val.length === 0) { bar.style.width = '0'; text.textContent = ''; return; }
  const lvl = levels[Math.max(score - 1, 0)];
  bar.style.width = lvl.w;
  bar.style.background = lvl.color;
  text.textContent = lvl.label;
  text.style.color = lvl.color;
}
</script>
@endpush
