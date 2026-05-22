@extends('layouts.admin')
@section('title','Persetujuan Nasabah')
@section('page-title','Persetujuan Nasabah')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <p class="text-muted mb-0" style="font-size:13px">
      Total {{ $nasabah->total() }} nasabah menunggu persetujuan
    </p>
  </div>
</div>

<div class="table-card">
  <div class="card-header">
    <h6>Daftar Nasabah Pending</h6>
  </div>
  <div class="table-responsive">
    <table class="table mb-0">
      <thead>
        <tr>
          <th>#</th>
          <th>Nama</th>
          <th>NIK</th>
          <th>No. HP</th>
          <th>Alamat</th>
          <th>Mendaftar Pada</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($nasabah as $n)
        <tr>
          <td style="color:#94a3b8;font-size:12px">{{ $loop->iteration }}</td>
          <td>
            <div style="font-weight:600;font-size:13px">{{ $n->user->name }}</div>
            <div style="font-size:11px;color:#94a3b8">{{ $n->user->email }}
                @if($n->user->google_id)
                <span class="badge bg-danger ms-1" style="font-size: 10px;">Google</span>
                @endif
            </div>
          </td>
          <td>
            <span class="badge" style="background:#f1f5f9;color:#374151;font-size:12px">
              {{ $n->nik }}
            </span>
          </td>
          <td style="font-size:13px">{{ $n->no_hp }}</td>
          <td style="font-size:13px">{{ $n->alamat }}</td>
          <td style="font-size:12px;color:#64748b">{{ $n->created_at->format('d M Y H:i') }}</td>
          <td>
            <div class="d-flex gap-1">
              <form method="POST" action="{{ route('admin.nasabah.approve', $n->id) }}" onsubmit="return confirm('Setujui nasabah {{ $n->user->name }}?')">
                @csrf
                <button type="submit" class="btn btn-sm" style="background:#dcfce7;color:#16a34a;border-radius:7px" title="Approve">
                  <i class="bi bi-check-lg"></i>
                </button>
              </form>
              <form method="POST" action="{{ route('admin.nasabah.reject', $n->id) }}" onsubmit="return confirm('Tolak dan hapus data pendaftaran {{ $n->user->name }}?')">
                @csrf
                <button type="submit" class="btn btn-sm" style="background:#fee2e2;color:#dc2626;border-radius:7px" title="Reject">
                  <i class="bi bi-x-lg"></i>
                </button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7" class="text-center text-muted py-4">
            Tidak ada pendaftaran nasabah yang menunggu persetujuan.
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($nasabah->hasPages())
  <div class="d-flex justify-content-end p-3">
    {{ $nasabah->links() }}
  </div>
  @endif
</div>
@endsection
