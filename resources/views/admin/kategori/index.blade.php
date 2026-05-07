@extends('layouts.admin')
@section('title','Kategori Sampah')
@section('page-title','Kategori Sampah')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <p class="text-muted mb-0" style="font-size:13px">{{ $kategori->count() }} kategori terdaftar</p>
  <a href="{{ route('admin.kategori.create') }}" class="btn btn-sm"
     style="background:#16a34a;color:#fff;border-radius:8px">
    <i class="bi bi-plus-lg me-1"></i>Tambah Kategori
  </a>
</div>

<div class="table-card">
  <div class="card-header"><h6>Daftar Kategori Sampah & Harga</h6></div>
  <div class="table-responsive">
    <table class="table mb-0">
      <thead>
        <tr>
          <th>#</th><th>Nama Kategori</th><th>Jenis</th>
          <th>Harga / kg</th><th>Keterangan</th><th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @php
          $warna = [
            'organik'           => ['#dcfce7','#16a34a'],
            'anorganik'         => ['#dbeafe','#1d4ed8'],
            'minyak_bekas'      => ['#fef9c3','#854d0e'],
            'tidak_dapat_diolah'=> ['#fee2e2','#dc2626'],
          ];
        @endphp
        @forelse($kategori as $k)
        <tr>
          <td style="color:#94a3b8;font-size:12px">{{ $loop->iteration }}</td>
          <td style="font-weight:600;font-size:13px">{{ $k->nama_kategori }}</td>
          <td>
            @php [$bg,$cl] = $warna[$k->jenis] ?? ['#f1f5f9','#374151']; @endphp
            <span class="badge" style="background:{{ $bg }};color:{{ $cl }};font-size:12px">
              {{ str_replace('_', ' ', ucfirst($k->jenis)) }}
            </span>
          </td>
          <td style="font-weight:700;color:#16a34a">
            Rp {{ number_format($k->harga_per_kg, 0, ',', '.') }}
          </td>
          <td style="font-size:12px;color:#64748b">{{ $k->keterangan ?? '-' }}</td>
          <td>
            <div class="d-flex gap-1">
              <a href="{{ route('admin.kategori.edit', $k) }}"
                 class="btn btn-sm" style="background:#dbeafe;color:#1d4ed8;border-radius:7px">
                <i class="bi bi-pencil"></i>
              </a>
              <form method="POST" action="{{ route('admin.kategori.destroy', $k) }}"
                    onsubmit="return confirm('Hapus kategori ini?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm"
                        style="background:#fee2e2;color:#dc2626;border-radius:7px">
                  <i class="bi bi-trash"></i>
                </button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center text-muted py-4">Belum ada kategori sampah.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection