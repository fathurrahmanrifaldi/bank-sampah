@extends('layouts.admin')
@section('title','Tambah Nasabah')
@section('page-title','Tambah Nasabah Baru')

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-7">
    <div class="table-card">
      <div class="card-header">
        <h6>Form Data Nasabah</h6>
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

        <form method="POST" action="{{ route('admin.nasabah.store') }}">
          @csrf
          <div class="mb-3">
            <label class="form-label fw-semibold" style="font-size:13px">Nama Lengkap</label>
            <input type="text" name="name" class="form-control"
                   value="{{ old('name') }}" placeholder="Nama lengkap nasabah" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold" style="font-size:13px">NIK</label>
            <input type="text" name="nik" class="form-control"
                   value="{{ old('nik') }}" placeholder="Nomor Induk Kependudukan (16 digit)" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold" style="font-size:13px">Email</label>
            <input type="email" name="email" class="form-control"
                   value="{{ old('email') }}" placeholder="email@contoh.com" required>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label fw-semibold" style="font-size:13px">No. HP</label>
              <input type="text" name="no_hp" class="form-control"
                     value="{{ old('no_hp') }}" placeholder="08xxxxxxxxxx" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label fw-semibold" style="font-size:13px">Password Awal</label>
              <input type="password" name="password" class="form-control"
                     placeholder="Min. 8 karakter" required>
            </div>
          </div>
          <div class="mb-4">
            <label class="form-label fw-semibold" style="font-size:13px">Alamat</label>
            <textarea name="alamat" class="form-control" rows="3"
                      placeholder="Alamat lengkap nasabah" required>{{ old('alamat') }}</textarea>
          </div>
          <div class="d-flex gap-2">
            <button type="submit" class="btn px-4"
                    style="background:#16a34a;color:#fff;border-radius:9px;font-size:14px">
              <i class="bi bi-check-lg me-1"></i>Simpan Nasabah
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