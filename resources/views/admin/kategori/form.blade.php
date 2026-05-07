@extends('layouts.admin')
@section('title', isset($kategori) ? 'Edit Kategori' : 'Tambah Kategori')
@section('page-title', isset($kategori) ? 'Edit Kategori Sampah' : 'Tambah Kategori Sampah')

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-6">
    <div class="table-card">
      <div class="card-header">
        <h6>{{ isset($kategori) ? 'Edit: '.$kategori->nama_kategori : 'Form Kategori Baru' }}</h6>
        <a href="{{ route('admin.kategori.index') }}" class="btn btn-sm"
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

        @if(isset($kategori))
          <form method="POST" action="{{ route('admin.kategori.update', $kategori) }}">
          @method('PUT')
        @else
          <form method="POST" action="{{ route('admin.kategori.store') }}">
        @endif
        @csrf

          <div class="mb-3">
            <label class="form-label fw-semibold" style="font-size:13px">Nama Kategori</label>
            <input type="text" name="nama_kategori" class="form-control"
                   value="{{ old('nama_kategori', $kategori->nama_kategori ?? '') }}"
                   placeholder="cth: Botol Plastik" required>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold" style="font-size:13px">Jenis Sampah</label>
            <select name="jenis" class="form-select" required>
              <option value="">-- Pilih Jenis --</option>
              @php
                $jenis = ['organik'=>'Organik','anorganik'=>'Anorganik',
                          'minyak_bekas'=>'Minyak Bekas','tidak_dapat_diolah'=>'Tidak Dapat Diolah'];
              @endphp
              @foreach($jenis as $val => $label)
                <option value="{{ $val }}"
                  {{ old('jenis', $kategori->jenis ?? '') == $val ? 'selected' : '' }}>
                  {{ $label }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold" style="font-size:13px">Harga per kg (Rp)</label>
            <div class="input-group">
              <span class="input-group-text" style="font-size:13px">Rp</span>
              <input type="number" name="harga_per_kg" class="form-control"
                     value="{{ old('harga_per_kg', $kategori->harga_per_kg ?? '') }}"
                     placeholder="0" min="0" step="100" required>
            </div>
          </div>

          <div class="mb-4">
            <label class="form-label fw-semibold" style="font-size:13px">
              Keterangan <small class="text-muted">(opsional)</small>
            </label>
            <textarea name="keterangan" class="form-control" rows="2"
                      placeholder="Deskripsi singkat kategori sampah ini">{{ old('keterangan', $kategori->keterangan ?? '') }}</textarea>
          </div>

          <div class="d-flex gap-2">
            <button type="submit" class="btn px-4"
                    style="background:#16a34a;color:#fff;border-radius:9px;font-size:14px">
              <i class="bi bi-check-lg me-1"></i>{{ isset($kategori) ? 'Perbarui' : 'Simpan' }}
            </button>
            <a href="{{ route('admin.kategori.index') }}"
               class="btn px-4" style="background:#f1f5f9;border-radius:9px;font-size:14px">Batal</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection