@extends('layouts.super-admin')
@section('title', 'Edit Petugas: ' . $admin->name)
@section('page-title', 'Edit Petugas Lapangan')

@section('content')

<div class="row justify-content-center">
  <div class="col-lg-7">

    <div class="d-flex align-items-center gap-2 mb-4">
      <a href="{{ route('super-admin.kelola-admin.index') }}"
         style="color:#64748b;text-decoration:none;font-size:13px">
        <i class="bi bi-arrow-left me-1"></i>Kembali ke Daftar Petugas
      </a>
    </div>

    <div class="table-card">
      <div class="card-header">
        <h6><i class="bi bi-pencil-fill me-2" style="color:#4f46e5"></i>Edit Akun: {{ $admin->name }}</h6>
        @if($admin->status === 'nonaktif')
          <span class="badge badge-nonaktif px-3 py-1" style="font-size:11px;border-radius:20px">Nonaktif</span>
        @else
          <span class="badge badge-aktif px-3 py-1" style="font-size:11px;border-radius:20px">Aktif</span>
        @endif
      </div>
      <div style="padding:24px">

        @if($errors->any())
          <div class="alert alert-danger mb-4">
            <ul class="mb-0" style="padding-left:16px">
              @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
          </div>
        @endif

        <form method="POST" action="{{ route('super-admin.kelola-admin.update', $admin) }}">
          @csrf @method('PUT')

          <div class="mb-4">
            <label class="form-label" style="font-weight:600;font-size:13px;color:#374151">
              Nama Lengkap <span style="color:#ef4444">*</span>
            </label>
            <input type="text" name="name" id="name"
                   value="{{ old('name', $admin->name) }}"
                   class="form-control @error('name') is-invalid @enderror"
                   style="border-radius:10px;border-color:#e2e8f0;font-size:13.5px;padding:10px 14px">
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="mb-4">
            <label class="form-label" style="font-weight:600;font-size:13px;color:#374151">
              Email <span style="color:#ef4444">*</span>
            </label>
            <input type="email" name="email" id="email"
                   value="{{ old('email', $admin->email) }}"
                   class="form-control @error('email') is-invalid @enderror"
                   style="border-radius:10px;border-color:#e2e8f0;font-size:13.5px;padding:10px 14px">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <hr style="border-color:#f1f5f9;margin:20px 0">
          <p style="font-size:12.5px;color:#64748b;margin-bottom:16px">
            <i class="bi bi-key me-1"></i>Kosongkan field password jika tidak ingin mengubah password.
          </p>

          <div class="mb-4">
            <label class="form-label" style="font-weight:600;font-size:13px;color:#374151">
              Password Baru
            </label>
            <input type="password" name="password" id="password"
                   class="form-control @error('password') is-invalid @enderror"
                   placeholder="Isi jika ingin ganti password"
                   style="border-radius:10px;border-color:#e2e8f0;font-size:13.5px;padding:10px 14px">
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="mb-5">
            <label class="form-label" style="font-weight:600;font-size:13px;color:#374151">
              Konfirmasi Password Baru
            </label>
            <input type="password" name="password_confirmation" id="password_confirmation"
                   class="form-control"
                   placeholder="Ulangi password baru"
                   style="border-radius:10px;border-color:#e2e8f0;font-size:13.5px;padding:10px 14px">
          </div>

          <div class="d-flex gap-2">
            <button type="submit" class="btn"
                    style="background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;
                           font-weight:700;border:none;padding:10px 28px;border-radius:10px;
                           box-shadow:0 4px 12px rgba(79,70,229,.35)">
              <i class="bi bi-check2-circle me-2"></i>Simpan Perubahan
            </button>
            <a href="{{ route('super-admin.kelola-admin.index') }}"
               class="btn" style="background:#f1f5f9;color:#475569;font-weight:600;border:none;
                                  padding:10px 20px;border-radius:10px">
              Batal
            </a>
          </div>
        </form>
      </div>
    </div>

    {{-- Quick Actions --}}
    <div class="table-card mt-3">
      <div class="card-header">
        <h6 style="color:#64748b"><i class="bi bi-lightning me-2"></i>Aksi Cepat</h6>
      </div>
      <div class="p-3 d-flex gap-2 flex-wrap">
        {{-- Toggle Status --}}
        <form method="POST" action="{{ route('super-admin.kelola-admin.toggle-status', $admin) }}">
          @csrf
          @if($admin->status === 'nonaktif')
            <button type="submit" class="btn btn-sm"
                    style="background:#dcfce7;color:#15803d;font-weight:600;border:none;border-radius:8px;padding:8px 16px">
              <i class="bi bi-toggle-off me-1"></i>Aktifkan Akun
            </button>
          @else
            <button type="submit" class="btn btn-sm"
                    onclick="return confirm('Nonaktifkan akun {{ addslashes($admin->name) }}? Petugas tidak akan bisa login.')"
                    style="background:#fef3c7;color:#b45309;font-weight:600;border:none;border-radius:8px;padding:8px 16px">
              <i class="bi bi-toggle-on me-1"></i>Nonaktifkan Akun
            </button>
          @endif
        </form>

        {{-- Hapus Akun --}}
        <form method="POST" action="{{ route('super-admin.kelola-admin.destroy', $admin) }}"
              onsubmit="return confirm('HAPUS akun {{ addslashes($admin->name) }} secara permanen?')">
          @csrf @method('DELETE')
          <button type="submit" class="btn btn-sm"
                  style="background:#fee2e2;color:#dc2626;font-weight:600;border:none;border-radius:8px;padding:8px 16px">
            <i class="bi bi-trash-fill me-1"></i>Hapus Akun
          </button>
        </form>
      </div>
    </div>

  </div>
</div>

@endsection
