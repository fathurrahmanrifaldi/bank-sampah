@extends('layouts.admin')
@section('title','Edit Nasabah')
@section('page-title','Edit Data Nasabah')

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-7">
    <div class="table-card">
      <div class="card-header">
        <h6>Edit: {{ $nasabah->user->name }}</h6>
        <a href="{{ route('admin.nasabah.index') }}" class="btn btn-sm"
           style="background:#f1f5f9;font-size:12px">
          <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
      </div>
      <div class="p-4">
        @if($errors->any())
          <div class="alert alert-danger mb-3">
            <ul class="mb-0 ps-3">
              @foreach($errors->all() as $e)
                <li style="font-size:13px">{{ $e }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <div class="alert mb-4" style="background:#f0fdf4;border:1px solid #bbf7d0">
          <div style="font-size:12px;color:#15803d">
            <strong>NIK:</strong> {{ $nasabah->nik }} &nbsp;|&nbsp;
            <strong>Saldo:</strong> Rp {{ number_format($nasabah->saldo, 0, ',', '.') }}
          </div>
        </div>

        <form method="POST" action="{{ route('admin.nasabah.update', $nasabah) }}">
          @csrf @method('PUT')
          <div class="mb-3">
            <label class="form-label fw-semibold" style="font-size:13px">Nama Lengkap</label>
            <input type="text" name="name" class="form-control"
                   value="{{ old('name', $nasabah->user->name) }}" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold" style="font-size:13px">
              Email <small class="text-muted">(tidak bisa diubah)</small>
            </label>
            <input type="email" class="form-control" value="{{ $nasabah->user->email }}"
                   disabled style="background:#f8fafc">
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label fw-semibold" style="font-size:13px">No. HP</label>
              <input type="text" name="no_hp" class="form-control"
                     value="{{ old('no_hp', $nasabah->no_hp) }}" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label fw-semibold" style="font-size:13px">
                Password Baru <small class="text-muted">(kosongkan jika tidak diubah)</small>
              </label>
              <input type="password" name="password" class="form-control"
                     placeholder="Min. 6 karakter">
            </div>
          </div>
          <div class="mb-4">
            <label class="form-label fw-semibold" style="font-size:13px">Alamat</label>
            <textarea name="alamat" class="form-control" rows="3" required>{{ old('alamat', $nasabah->alamat) }}</textarea>
          </div>
          <div class="d-flex gap-2">
            <button type="submit" class="btn px-4"
                    style="background:#16a34a;color:#fff;border-radius:9px;font-size:14px">
              <i class="bi bi-check-lg me-1"></i>Perbarui Data
            </button>
            <a href="{{ route('admin.nasabah.index') }}"
               class="btn px-4" style="background:#f1f5f9;border-radius:9px;font-size:14px">
              Batal
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection