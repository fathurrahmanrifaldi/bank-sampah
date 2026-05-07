@extends('layouts.admin')
@section('title','Transaksi Setoran')
@section('page-title','Transaksi Setoran Sampah')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <p class="text-muted mb-0" style="font-size:13px">Total {{ $transaksi->total() }} transaksi</p>
  <a href="{{ route('admin.transaksi.create') }}" class="btn btn-sm"
     style="background:#16a34a;color:#fff;border-radius:8px">
    <i class="bi bi-plus-lg me-1"></i>Catat Setoran Baru
  </a>
</div>

<div class="table-card">
  <div class="card-header">
    <h6>Daftar Transaksi</h6>
    <form class="d-flex gap-2">
      <input type="month" name="periode" value="{{ request('periode') }}"
             class="form-control form-control-sm" style="border-radius:8px;width:160px">
      <button class="btn btn-sm" style="background:#f1f5f9">Filter</button>
    </form>
  </div>
  <div class="table-responsive">
    <table class="table mb-0">
      <thead>
        <tr>
          <th>#</th><th>Nasabah</th><th>Tanggal</th>
          <th>Jml Jenis</th><th>Total Nilai</th><th>Dicatat oleh</th><th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($transaksi as $t)
        <tr>
          <td style="color:#94a3b8;font-size:12px">{{ $t->id }}</td>
          <td>
            <div style="font-weight:600;font-size:13px">{{ $t->nasabah->user->name }}</div>
            <div style="font-size:11px;color:#94a3b8">{{ $t->nasabah->no_rekening }}</div>
          </td>
          <td style="font-size:13px">{{ $t->tanggal->format('d M Y') }}</td>
          <td>
            <span class="badge" style="background:#f1f5f9;color:#374151;font-size:12px">
              {{ $t->detail->count() }} jenis
            </span>
          </td>
          <td style="font-weight:700;color:#16a34a;font-size:13px">
            Rp {{ number_format($t->total_nilai, 0, ',', '.') }}
          </td>
          <td style="font-size:12px;color:#64748b">{{ $t->admin->name }}</td>
          <td>
            <a href="{{ route('admin.transaksi.show', $t) }}"
               class="btn btn-sm" style="background:#dcfce7;color:#16a34a;border-radius:7px">
              <i class="bi bi-eye"></i>
            </a>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7" class="text-center text-muted py-4">Belum ada transaksi.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($transaksi->hasPages())
    <div class="d-flex justify-content-end p-3">{{ $transaksi->links() }}</div>
  @endif
</div>
@endsection