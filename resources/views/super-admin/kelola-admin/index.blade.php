@extends('layouts.super-admin')
@section('title', 'Kelola Petugas Lapangan')
@section('page-title', 'Kelola Petugas Lapangan')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h5 style="font-weight:800;color:#0f172a;margin:0">Petugas Lapangan</h5>
    <p style="color:#64748b;font-size:13px;margin:2px 0 0">Kelola akun petugas yang mencatat setoran harian</p>
  </div>
  <a href="{{ route('super-admin.kelola-admin.create') }}"
     class="btn d-flex align-items-center gap-2"
     style="background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;font-weight:600;border:none;padding:9px 18px;border-radius:10px;box-shadow:0 4px 12px rgba(79,70,229,.35)">
    <i class="bi bi-person-plus-fill"></i> Tambah Petugas
  </a>
</div>

<div class="table-card">
  <div class="card-header">
    <h6><i class="bi bi-people me-2" style="color:#4f46e5"></i>Daftar Petugas ({{ $admins->count() }} orang)</h6>
  </div>
  <table class="table mb-0">
    <thead>
      <tr>
        <th>#</th>
        <th>Nama Petugas</th>
        <th>Email</th>
        <th>Status</th>
        <th>Terdaftar</th>
        <th class="text-center">Aksi</th>
      </tr>
    </thead>
    <tbody>
      @forelse($admins as $i => $admin)
      <tr>
        <td style="color:#94a3b8;font-size:13px">{{ $i + 1 }}</td>
        <td>
          <div class="d-flex align-items-center gap-3">
            <div style="width:36px;height:36px;border-radius:50%;
                        background:linear-gradient(135deg,#4f46e5,#7c3aed);
                        display:flex;align-items:center;justify-content:center;
                        color:#fff;font-weight:700;font-size:13px;flex-shrink:0">
              {{ strtoupper(substr($admin->name,0,1)) }}
            </div>
            <div>
              <div style="font-weight:700;font-size:13.5px;color:#0f172a">{{ $admin->name }}</div>
            </div>
          </div>
        </td>
        <td style="font-size:13px;color:#475569">{{ $admin->email }}</td>
        <td>
          @if($admin->status === 'nonaktif')
            <span class="badge badge-nonaktif px-3 py-1" style="font-size:11px;border-radius:20px">
              <i class="bi bi-x-circle me-1"></i>Nonaktif
            </span>
          @else
            <span class="badge badge-aktif px-3 py-1" style="font-size:11px;border-radius:20px">
              <i class="bi bi-check-circle me-1"></i>Aktif
            </span>
          @endif
        </td>
        <td style="font-size:12px;color:#64748b">
          {{ $admin->created_at ? $admin->created_at->format('d M Y') : '-' }}
        </td>
        <td class="text-center">
          <div class="d-flex justify-content-center gap-2">
            {{-- Edit --}}
            <a href="{{ route('super-admin.kelola-admin.edit', $admin) }}"
               class="btn btn-sm" style="background:#e0e7ff;color:#4f46e5;font-weight:600;border:none;border-radius:8px"
               title="Edit">
              <i class="bi bi-pencil-fill"></i>
            </a>

            {{-- Toggle Status --}}
            <form method="POST" action="{{ route('super-admin.kelola-admin.toggle-status', $admin) }}">
              @csrf
              @if($admin->status === 'nonaktif')
                <button type="submit" class="btn btn-sm" title="Aktifkan"
                        style="background:#dcfce7;color:#15803d;font-weight:600;border:none;border-radius:8px">
                  <i class="bi bi-toggle-off"></i>
                </button>
              @else
                <button type="submit" class="btn btn-sm" title="Nonaktifkan"
                        style="background:#fef3c7;color:#b45309;font-weight:600;border:none;border-radius:8px">
                  <i class="bi bi-toggle-on"></i>
                </button>
              @endif
            </form>

            {{-- Hapus --}}
            <form method="POST" action="{{ route('super-admin.kelola-admin.destroy', $admin) }}"
                  onsubmit="return confirm('Hapus akun {{ addslashes($admin->name) }}? Tindakan ini tidak dapat dibatalkan.')">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-sm" title="Hapus"
                      style="background:#fee2e2;color:#dc2626;font-weight:600;border:none;border-radius:8px">
                <i class="bi bi-trash-fill"></i>
              </button>
            </form>
          </div>
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="6" class="text-center py-5">
          <i class="bi bi-person-x" style="font-size:40px;color:#cbd5e1;display:block;margin-bottom:10px"></i>
          <div style="color:#94a3b8;font-size:14px">Belum ada petugas lapangan terdaftar</div>
          <a href="{{ route('super-admin.kelola-admin.create') }}" class="btn btn-sm mt-3"
             style="background:#4f46e5;color:#fff;border-radius:8px">Tambah Sekarang</a>
        </td>
      </tr>
      @endforelse
    </tbody>
  </table>
</div>

@endsection
