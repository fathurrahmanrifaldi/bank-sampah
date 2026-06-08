@extends('layouts.super-admin')
@section('title', isset($kategori) ? 'Edit Kategori' : 'Tambah Kategori')
@section('page-title', isset($kategori) ? 'Edit Kategori Sampah' : 'Tambah Kategori Sampah')

@section('content')

<div class="row justify-content-center">
  <div class="col-lg-6">

    <div class="mb-4">
      <a href="{{ route('super-admin.kategori.index') }}"
         style="color:#64748b;text-decoration:none;font-size:13px">
        <i class="bi bi-arrow-left me-1"></i>Kembali
      </a>
    </div>

    <div class="table-card">
      <div class="card-header">
        <h6>
          <i class="bi bi-tags me-2" style="color:#4f46e5"></i>
          {{ isset($kategori) ? 'Edit Kategori: ' . $kategori->nama_kategori : 'Tambah Kategori Baru' }}
        </h6>
      </div>
      <div style="padding:24px">

        @if($errors->any())
          <div class="alert alert-danger mb-4">
            <ul class="mb-0" style="padding-left:16px">
              @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
          </div>
        @endif

        <form method="POST"
              action="{{ isset($kategori) ? route('super-admin.kategori.update', $kategori) : route('super-admin.kategori.store') }}">
          @csrf
          @if(isset($kategori)) @method('PUT') @endif

          <div class="mb-4">
            <label class="form-label" style="font-weight:600;font-size:13px;color:#374151">
              Nama Kategori <span style="color:#ef4444">*</span>
            </label>
            <input type="text" name="nama_kategori" id="nama_kategori"
                   value="{{ old('nama_kategori', $kategori->nama_kategori ?? '') }}"
                   class="form-control @error('nama_kategori') is-invalid @enderror"
                   placeholder="cth: Botol Plastik"
                   style="border-radius:10px;border-color:#e2e8f0;font-size:13.5px;padding:10px 14px">
            @error('nama_kategori')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="mb-4">
            <label class="form-label" style="font-weight:600;font-size:13px;color:#374151">
              Jenis Sampah <span style="color:#ef4444">*</span>
            </label>
            <select name="jenis" id="jenis"
                    class="form-select @error('jenis') is-invalid @enderror"
                    style="border-radius:10px;border-color:#e2e8f0;font-size:13.5px;padding:10px 14px">
              <option value="">-- Pilih Jenis --</option>
              @php
                $jenisOptions = [
                  'organik'            => 'Organik',
                  'anorganik'          => 'Anorganik',
                  'minyak_bekas'       => 'Minyak Bekas',
                  'tidak_dapat_diolah' => 'Tidak Dapat Diolah',
                ];
              @endphp
              @foreach($jenisOptions as $value => $label)
                <option value="{{ $value }}"
                  {{ old('jenis', $kategori->jenis ?? '') === $value ? 'selected' : '' }}>
                  {{ $label }}
                </option>
              @endforeach
            </select>
            @error('jenis')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="mb-4">
            <label class="form-label" style="font-weight:600;font-size:13px;color:#374151">
              Harga per kg (Rp) <span style="color:#ef4444">*</span>
            </label>
            <input type="number" name="harga_per_kg" id="harga_per_kg"
                   value="{{ old('harga_per_kg', $kategori->harga_per_kg ?? '') }}"
                   class="form-control @error('harga_per_kg') is-invalid @enderror"
                   min="0" step="100" placeholder="cth: 3000"
                   style="border-radius:10px;border-color:#e2e8f0;font-size:13.5px;padding:10px 14px">
            @error('harga_per_kg')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="mb-5">
            <label class="form-label" style="font-weight:600;font-size:13px;color:#374151">
              Keterangan <span style="color:#94a3b8">(opsional)</span>
            </label>
            <textarea name="keterangan" id="keterangan" rows="3"
                      class="form-control @error('keterangan') is-invalid @enderror"
                      placeholder="Deskripsi singkat kategori..."
                      style="border-radius:10px;border-color:#e2e8f0;font-size:13.5px;padding:10px 14px">{{ old('keterangan', $kategori->keterangan ?? '') }}</textarea>
            @error('keterangan')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="d-flex gap-2">
            <button type="submit" class="btn"
                    style="background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;
                           font-weight:700;border:none;padding:10px 28px;border-radius:10px;
                           box-shadow:0 4px 12px rgba(79,70,229,.35)">
              <i class="bi bi-check2-circle me-2"></i>{{ isset($kategori) ? 'Simpan' : 'Tambahkan' }}
            </button>
            <a href="{{ route('super-admin.kategori.index') }}"
               class="btn" style="background:#f1f5f9;color:#475569;font-weight:600;border:none;
                                  padding:10px 20px;border-radius:10px">
              Batal
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

@endsection
