@extends('layouts.super-admin')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard Super Admin')

@push('head-styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('content')

{{-- ══ STAT CARDS ════════════════════════════════════════════════════════════ --}}
<div class="row g-3 mb-4">
  {{-- Total Nasabah --}}
  <div class="col-6 col-lg-3">
    <div class="stat-card">
      <div class="d-flex align-items-start justify-content-between">
        <div>
          <div class="stat-value">{{ number_format($totalNasabah) }}</div>
          <div class="stat-label">Total Nasabah</div>
        </div>
        <div class="stat-icon" style="background:#e0e7ff">
          <i class="bi bi-people-fill" style="color:#4f46e5"></i>
        </div>
      </div>
    </div>
  </div>

  {{-- Total Admin Aktif --}}
  <div class="col-6 col-lg-3">
    <div class="stat-card">
      <div class="d-flex align-items-start justify-content-between">
        <div>
          <div class="stat-value">{{ number_format($totalAdminAktif) }}</div>
          <div class="stat-label">Petugas Aktif</div>
        </div>
        <div class="stat-icon" style="background:#dcfce7">
          <i class="bi bi-person-badge-fill" style="color:#16a34a"></i>
        </div>
      </div>
    </div>
  </div>

  {{-- Total Transaksi --}}
  <div class="col-6 col-lg-3">
    <div class="stat-card">
      <div class="d-flex align-items-start justify-content-between">
        <div>
          <div class="stat-value">{{ number_format($totalTransaksi) }}</div>
          <div class="stat-label">Total Transaksi</div>
        </div>
        <div class="stat-icon" style="background:#fef9c3">
          <i class="bi bi-arrow-left-right" style="color:#ca8a04"></i>
        </div>
      </div>
    </div>
  </div>

  {{-- Total Nilai Setoran --}}
  <div class="col-6 col-lg-3">
    <div class="stat-card">
      <div class="d-flex align-items-start justify-content-between">
        <div>
          <div class="stat-value" style="font-size:18px">Rp {{ number_format($totalNilaiSetoran, 0, ',', '.') }}</div>
          <div class="stat-label">Total Nilai Setoran</div>
        </div>
        <div class="stat-icon" style="background:#fce7f3">
          <i class="bi bi-cash-coin" style="color:#db2777"></i>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Bulan ini + penarikan menunggu --}}
<div class="row g-3 mb-4">
  <div class="col-md-6">
    <div class="stat-card" style="border-left:4px solid #4f46e5">
      <div class="d-flex align-items-center gap-3">
        <div class="stat-icon" style="background:#e0e7ff;flex-shrink:0">
          <i class="bi bi-calendar-month" style="color:#4f46e5"></i>
        </div>
        <div>
          <div style="font-size:11px;color:#64748b;font-weight:600;text-transform:uppercase;letter-spacing:.5px">Setoran Bulan Ini</div>
          <div style="font-size:22px;font-weight:800;color:#0f172a">Rp {{ number_format($setoranBulanIni, 0, ',', '.') }}</div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="stat-card" style="border-left:4px solid #f59e0b">
      <div class="d-flex align-items-center gap-3">
        <div class="stat-icon" style="background:#fef3c7;flex-shrink:0">
          <i class="bi bi-hourglass-split" style="color:#d97706"></i>
        </div>
        <div>
          <div style="font-size:11px;color:#64748b;font-weight:600;text-transform:uppercase;letter-spacing:.5px">Penarikan Menunggu</div>
          <div style="font-size:22px;font-weight:800;color:#0f172a">{{ $penarikanMenunggu }} Pengajuan</div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ══ CHART + ADMIN TABLE ══════════════════════════════════════════════════ --}}
<div class="row g-3 mb-4">
  {{-- Chart 6 bulan --}}
  <div class="col-lg-8">
    <div class="table-card">
      <div class="card-header">
        <h6><i class="bi bi-bar-chart-line me-2" style="color:#4f46e5"></i>Nilai Setoran 6 Bulan Terakhir</h6>
      </div>
      <div style="padding:20px">
        <canvas id="chartSetoran" height="90"></canvas>
      </div>
    </div>
  </div>

  {{-- Daftar Petugas --}}
  <div class="col-lg-4">
    <div class="table-card h-100">
      <div class="card-header">
        <h6><i class="bi bi-person-badge me-2" style="color:#4f46e5"></i>Petugas Lapangan</h6>
        <a href="{{ route('super-admin.kelola-admin.index') }}"
           class="btn btn-sm" style="background:#e0e7ff;color:#4f46e5;font-size:12px;font-weight:600">Kelola</a>
      </div>
      <div style="padding:12px">
        @forelse($admins as $admin)
        <div class="d-flex align-items-center gap-3 py-2" style="border-bottom:1px solid #f1f5f9">
          <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#4f46e5,#7c3aed);
                      display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:12px;flex-shrink:0">
            {{ strtoupper(substr($admin->name,0,1)) }}
          </div>
          <div style="flex:1;min-width:0">
            <div style="font-size:13px;font-weight:600;color:#0f172a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
              {{ $admin->name }}
            </div>
            <div style="font-size:11px;color:#64748b">{{ $admin->email }}</div>
          </div>
          @if($admin->status === 'nonaktif')
            <span class="badge badge-nonaktif" style="font-size:9px;white-space:nowrap">Nonaktif</span>
          @else
            <span class="badge badge-aktif" style="font-size:9px">Aktif</span>
          @endif
        </div>
        @empty
        <div class="text-center py-4" style="color:#94a3b8;font-size:13px">
          <i class="bi bi-person-x" style="font-size:28px;display:block;margin-bottom:6px"></i>
          Belum ada petugas
        </div>
        @endforelse
      </div>
    </div>
  </div>
</div>

{{-- ══ TRANSAKSI TERBARU ══════════════════════════════════════════════════ --}}
<div class="table-card">
  <div class="card-header">
    <h6><i class="bi bi-clock-history me-2" style="color:#4f46e5"></i>Transaksi Terbaru</h6>
  </div>
  <table class="table mb-0">
    <thead>
      <tr>
        <th>Nasabah</th>
        <th>Tanggal</th>
        <th class="text-end">Nilai</th>
      </tr>
    </thead>
    <tbody>
      @forelse($transaksiTerbaru as $t)
      <tr>
        <td>
          <div style="font-weight:600;font-size:13px">{{ $t->nasabah->user->name ?? '-' }}</div>
        </td>
        <td style="font-size:13px;color:#64748b">{{ \Carbon\Carbon::parse($t->tanggal)->format('d M Y') }}</td>
        <td class="text-end" style="font-weight:700;font-size:13px;color:#4f46e5">
          Rp {{ number_format($t->total_nilai, 0, ',', '.') }}
        </td>
      </tr>
      @empty
      <tr><td colspan="3" class="text-center py-4" style="color:#94a3b8">Belum ada transaksi</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

@endsection

@push('scripts')
<script>
const ctx = document.getElementById('chartSetoran').getContext('2d');
new Chart(ctx, {
  type: 'bar',
  data: {
    labels: @json($chartLabels),
    datasets: [{
      label: 'Nilai Setoran (Rp)',
      data: @json($chartNilai),
      backgroundColor: 'rgba(79,70,229,.2)',
      borderColor: '#4f46e5',
      borderWidth: 2,
      borderRadius: 6,
      borderSkipped: false,
    }]
  },
  options: {
    responsive: true,
    plugins: { legend: { display: false } },
    scales: {
      y: {
        beginAtZero: true,
        grid: { color: '#f1f5f9' },
        ticks: {
          font: { size: 11 },
          callback: v => 'Rp ' + (v/1000).toFixed(0) + 'K'
        }
      },
      x: { grid: { display: false }, ticks: { font: { size: 11 } } }
    }
  }
});
</script>
@endpush
