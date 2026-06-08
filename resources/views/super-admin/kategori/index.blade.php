@extends('layouts.super-admin')
@section('title', 'Kategori Sampah')
@section('page-title', 'Kategori Sampah')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h5 style="font-weight:800;color:#0f172a;margin:0">Kategori Sampah</h5>
    <p style="color:#64748b;font-size:13px;margin:2px 0 0">Atur jenis dan harga sampah yang diterima</p>
  </div>
  <a href="{{ route('super-admin.kategori.create') }}"
     class="btn d-flex align-items-center gap-2"
     style="background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;font-weight:600;
            border:none;padding:9px 18px;border-radius:10px;box-shadow:0 4px 12px rgba(79,70,229,.35)">
    <i class="bi bi-plus-circle-fill"></i> Tambah Kategori
  </a>
</div>

<div class="table-card">
  <div class="card-header">
    <h6><i class="bi bi-tags me-2" style="color:#4f46e5"></i>Daftar Kategori ({{ $kategori->count() }})</h6>
  </div>
  <table class="table mb-0">
    <thead>
      <tr>
        <th>#</th>
        <th>Nama Kategori</th>
        <th>Jenis</th>
        <th class="text-end">Harga/kg</th>
        <th>Keterangan</th>
        <th class="text-center">Aksi</th>
      </tr>
    </thead>
    <tbody>
      @php
        $jenisMap = [
          'organik'            => ['label' => 'Organik',            'bg' => '#dcfce7', 'color' => '#15803d'],
          'anorganik'          => ['label' => 'Anorganik',          'bg' => '#dbeafe', 'color' => '#1d4ed8'],
          'minyak_bekas'       => ['label' => 'Minyak Bekas',       'bg' => '#fef9c3', 'color' => '#a16207'],
          'tidak_dapat_diolah' => ['label' => 'Tidak Diolah',       'bg' => '#f1f5f9', 'color' => '#475569'],
        ];
      @endphp
      @forelse($kategori as $i => $k)
      <tr>
        <td style="color:#94a3b8;font-size:13px">{{ $i + 1 }}</td>
        <td style="font-weight:700;font-size:13.5px;color:#0f172a">{{ $k->nama_kategori }}</td>
        <td>
          @php $j = $jenisMap[$k->jenis] ?? ['label' => $k->jenis, 'bg' => '#f1f5f9', 'color' => '#475569']; @endphp
          <span class="badge px-3 py-1" style="background:{{ $j['bg'] }};color:{{ $j['color'] }};font-size:11px;border-radius:20px">
            {{ $j['label'] }}
          </span>
        </td>
        <td class="text-end" style="font-weight:700;color:#4f46e5;font-size:13.5px">
          Rp {{ number_format($k->harga_per_kg, 0, ',', '.') }}
        </td>
        <td style="font-size:12px;color:#64748b;max-width:180px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
          {{ $k->keterangan ?: '-' }}
        </td>
        <td class="text-center">
          <div class="d-flex justify-content-center gap-2">
            <a href="{{ route('super-admin.kategori.edit', $k) }}"
               class="btn btn-sm" style="background:#e0e7ff;color:#4f46e5;font-weight:600;border:none;border-radius:8px">
              <i class="bi bi-pencil-fill"></i>
            </a>
            <form method="POST" action="{{ route('super-admin.kategori.destroy', $k) }}"
                  onsubmit="return confirm('Hapus kategori {{ addslashes($k->nama_kategori) }}?')">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-sm"
                      style="background:#fee2e2;color:#dc2626;font-weight:600;border:none;border-radius:8px">
                <i class="bi bi-trash-fill"></i>
              </button>
            </form>
          </div>
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="6" class="text-center py-5" style="color:#94a3b8">
          <i class="bi bi-tags" style="font-size:36px;display:block;margin-bottom:8px"></i>
          Belum ada kategori sampah
        </td>
      </tr>
      @endforelse
    </tbody>
  </table>
</div>

@endsection
