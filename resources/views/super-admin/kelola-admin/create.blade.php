@extends('layouts.super-admin')
@section('title', 'Tambah Petugas Lapangan')
@section('page-title', 'Tambah Petugas Lapangan')

@section('content')

<div class="row justify-content-center">
  <div class="col-lg-7">

    <div class="d-flex align-items-center gap-2 mb-4">
      <a href="{{ route('super-admin.kelola-admin.index') }}"
         style="color:#64748b;text-decoration:none;font-size:13px">
        <i class="bi bi-arrow-left me-1"></i>Kembali
      </a>
    </div>

    <div class="table-card">
      <div class="card-header">
        <h6><i class="bi bi-person-plus-fill me-2" style="color:#4f46e5"></i>Buat Akun Petugas Baru</h6>
      </div>
      <div style="padding:24px">

        {{-- Error Bag --}}
        @if($errors->any())
          <div class="alert alert-danger mb-4">
            <ul class="mb-0" style="padding-left:16px">
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form method="POST" action="{{ route('super-admin.kelola-admin.store') }}">
          @csrf

          <div class="mb-4">
            <label class="form-label" style="font-weight:600;font-size:13px;color:#374151">
              Nama Lengkap <span style="color:#ef4444">*</span>
            </label>
            <input type="text" name="name" id="name"
                   value="{{ old('name') }}"
                   class="form-control @error('name') is-invalid @enderror"
                   placeholder="Masukkan nama lengkap petugas"
                   style="border-radius:10px;border-color:#e2e8f0;font-size:13.5px;padding:10px 14px">
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="mb-4">
            <label class="form-label" style="font-weight:600;font-size:13px;color:#374151">
              Email <span style="color:#ef4444">*</span>
            </label>
            <input type="email" name="email" id="email"
                   value="{{ old('email') }}"
                   class="form-control @error('email') is-invalid @enderror"
                   placeholder="contoh@banksampah.id"
                   style="border-radius:10px;border-color:#e2e8f0;font-size:13.5px;padding:10px 14px">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="mb-4">
            <label class="form-label" style="font-weight:600;font-size:13px;color:#374151">
              Password <span style="color:#ef4444">*</span>
            </label>
            <input type="password" name="password" id="password"
                   class="form-control @error('password') is-invalid @enderror"
                   placeholder="Minimal 8 karakter"
                   style="border-radius:10px;border-color:#e2e8f0;font-size:13.5px;padding:10px 14px">
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="mb-5">
            <label class="form-label" style="font-weight:600;font-size:13px;color:#374151">
              Konfirmasi Password <span style="color:#ef4444">*</span>
            </label>
            <input type="password" name="password_confirmation" id="password_confirmation"
                   class="form-control"
                   placeholder="Ulangi password"
                   style="border-radius:10px;border-color:#e2e8f0;font-size:13.5px;padding:10px 14px">
          </div>

          <div class="d-flex gap-2">
            <button type="submit" class="btn"
                    style="background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;
                           font-weight:700;border:none;padding:10px 28px;border-radius:10px;
                           box-shadow:0 4px 12px rgba(79,70,229,.35)">
              <i class="bi bi-person-check-fill me-2"></i>Buat Akun
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

    <div class="mt-3 p-3" style="background:#e0e7ff;border-radius:10px;font-size:12.5px;color:#3730a3">
      <i class="bi bi-info-circle me-2"></i>
      Akun petugas yang dibuat di sini akan langsung aktif dan dapat login tanpa verifikasi email.
    </div>
  </div>
</div>

@endsection
