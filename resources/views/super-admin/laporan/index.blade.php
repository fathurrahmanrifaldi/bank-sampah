@extends('layouts.super-admin')
@section('title', 'Laporan Operasional')
@section('page-title', 'Laporan Operasional')

@push('head-styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('content')

{{-- ══ FILTER ════════════════════════════════════════════════════════════════ --}}
<form method="GET" action="{{ route('super-admin.laporan.index') }}" class="mb-4">
  <div class="table-card">
    <div style="padding:16px 20px">
      <div class="row g-3 align-items-end">
        <div class="col-auto">
          <label class="form-label" style="font-size:12px;font-weight:700;color:#475569;text-transform:uppercase">Bulan</label>
          <select name="bulan" class="form-select" style="border-radius:10px;border-color:#e2e8f0;font-size:13px;min-width:140px">
            @foreach(range(1,12) as $b)
              <option value="{{ $b }}" {{ $bulan == $b ? 'selected' : '' }}>
                {{ \Carbon\Carbon::create()->month($b)->translatedFormat('F') }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="col-auto">
          <label class="form-label" style="font-size:12px;font-weight:700;color:#475569;text-transform:uppercase">Tahun</label>
          <select name="tahun" class="form-select" style="border-radius:10px;border-color:#e2e8f0;font-size:13px;min-width:110px">
            @foreach(range(now()->year, 2024, -1) as $y)
              <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-auto">
          <button type="submit" class="btn"
                  style="background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;
                         font-weight:600;border:none;padding:9px 20px;border-radius:10px">
            <i class="bi bi-funnel-fill me-2"></i>Filter
          </button>
        </div>
      </div>
    </div>
  </div>
</form>

{{-- ══ RINGKASAN KEUANGAN ══════════════════════════════════════════════════ --}}
<div class="row g-3 mb-4">
  <div class="col-md-4">
    <div class="stat-card" style="border-top:3px solid #4f46e5">
      <div class="stat-icon mb-2" style="background:#e0e7ff"><i class="bi bi-collection" style="color:#4f46e5"></i></div>
      <div class="stat-value">Rp {{ number_format($totalSetoran, 0, ',', '.') }}</div>
      <div class="stat-label">Total Setoran Nasabah</div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="stat-card" style="border-top:3px solid #16a34a">
      <div class="stat-icon mb-2" style="background:#dcfce7"><i class="bi bi-cash-stack" style="color:#16a34a"></i></div>
      <div class="stat-value">Rp {{ number_format($totalDicairkan, 0, ',', '.') }}</div>
      <div class="stat-label">Dana Dicairkan</div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="stat-card" style="border-top:3px solid #f59e0b">
      <div class="stat-icon mb-2" style="background:#fef3c7"><i class="bi bi-shop" style="color:#f59e0b"></i></div>
      <div class="stat-value">Rp {{ number_format($totalPengepul, 0, ',', '.') }}</div>
      <div class="stat-label">Penjualan ke Pengepul</div>
    </div>
  </div>
</div>

{{-- ══ AKTIVITAS PER PETUGAS (khusus Super Admin) ════════════════════════ --}}
<div class="table-card mb-4">
  <div class="card-header">
    <h6><i class="bi bi-person-badge me-2" style="color:#4f46e5"></i>Aktivitas per Petugas Lapangan</h6>
    <span style="font-size:12px;color:#64748b">{{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }} {{ $tahun }}</span>
  </div>
  <table class="table mb-0">
    <thead>
      <tr>
        <th>Nama Petugas</th>
        <th class="text-center">Jumlah Transaksi</th>
        <th class="text-end">Total Nilai Diproses</th>
      </tr>
    </thead>
    <tbody>
      @forelse($aktivitasAdmin as $a)
      <tr>
        <td style="font-weight:600;font-size:13.5px">{{ $a->admin_name }}</td>
        <td class="text-center">
          <span class="badge px-3" style="background:#e0e7ff;color:#4f46e5;border-radius:20px">
            {{ $a->jumlah_transaksi }} transaksi
          </span>
        </td>
        <td class="text-end" style="font-weight:700;color:#4f46e5">
          Rp {{ number_format($a->total_nilai, 0, ',', '.') }}
        </td>
      </tr>
      @empty
      <tr><td colspan="3" class="text-center py-4" style="color:#94a3b8">Tidak ada aktivitas pada periode ini</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

{{-- ══ CHARTS ══════════════════════════════════════════════════════════════ --}}
<div class="row g-3 mb-4">
  <div class="col-md-8">
    <div class="table-card">
      <div class="card-header">
        <h6><i class="bi bi-bar-chart me-2" style="color:#4f46e5"></i>Tren Nilai Setoran {{ $tahun }}</h6>
      </div>
      <div style="padding:20px"><canvas id="chartSetoran" height="100"></canvas></div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="table-card">
      <div class="card-header">
        <h6><i class="bi bi-pie-chart me-2" style="color:#4f46e5"></i>Komposisi Sampah</h6>
      </div>
      <div style="padding:20px"><canvas id="chartKategori" height="170"></canvas></div>
    </div>
  </div>
</div>

{{-- ══ REKAP SETORAN NASABAH ══════════════════════════════════════════════ --}}
<div class="table-card mb-4">
  <div class="card-header">
    <h6><i class="bi bi-people me-2" style="color:#4f46e5"></i>Rekap Setoran Nasabah</h6>
  </div>
  <table class="table mb-0">
    <thead>
      <tr>
        <th>#</th><th>Nama Nasabah</th><th>NIK</th>
        <th class="text-center">Jml. Setor</th>
        <th class="text-end">Total Nilai</th>
      </tr>
    </thead>
    <tbody>
      @forelse($rekap as $i => $r)
      <tr>
        <td style="color:#94a3b8;font-size:13px">{{ $i+1 }}</td>
        <td style="font-weight:600;font-size:13px">{{ $r->name }}</td>
        <td style="font-size:12px;color:#64748b">{{ $r->nik }}</td>
        <td class="text-center"><span class="badge" style="background:#e0e7ff;color:#4f46e5;border-radius:20px">{{ $r->jumlah_setor }}×</span></td>
        <td class="text-end" style="font-weight:700;color:#4f46e5">Rp {{ number_format($r->total_nilai, 0, ',', '.') }}</td>
      </tr>
      @empty
      <tr><td colspan="5" class="text-center py-4" style="color:#94a3b8">Tidak ada data setoran</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

{{-- ══ REKAP PENARIKAN DANA ════════════════════════════════════════════════ --}}
<div class="table-card mb-4">
  <div class="card-header">
    <h6><i class="bi bi-cash-stack me-2" style="color:#4f46e5"></i>Rekap Penarikan Dana</h6>
    <div class="d-flex gap-3" style="font-size:12px">
      <span style="color:#15803d">✓ Cair: <strong>Rp {{ number_format($statPenarikan->total_disetujui,0,',','.') }}</strong></span>
      <span style="color:#d97706">⏳ Tunggu: <strong>Rp {{ number_format($statPenarikan->total_menunggu,0,',','.') }}</strong></span>
      <span style="color:#dc2626">✗ Tolak: <strong>Rp {{ number_format($statPenarikan->total_ditolak,0,',','.') }}</strong></span>
    </div>
  </div>
  <table class="table mb-0">
    <thead>
      <tr><th>Nasabah</th><th>NIK</th><th>Jenis</th><th class="text-end">Jumlah</th><th>Status</th><th>Tgl Diminta</th></tr>
    </thead>
    <tbody>
      @forelse($rekapPenarikan as $p)
      <tr>
        <td style="font-weight:600;font-size:13px">{{ $p->name }}</td>
        <td style="font-size:12px;color:#64748b">{{ $p->nik }}</td>
        <td><span class="badge" style="background:#f1f5f9;color:#475569;border-radius:20px;font-size:10px">{{ ucfirst($p->jenis) }}</span></td>
        <td class="text-end" style="font-weight:700;font-size:13px">Rp {{ number_format($p->jumlah,0,',','.') }}</td>
        <td>
          @if($p->status === 'disetujui') <span class="badge badge-aktif" style="border-radius:20px">Disetujui</span>
          @elseif($p->status === 'menunggu') <span class="badge" style="background:#fef3c7;color:#b45309;border-radius:20px">Menunggu</span>
          @else <span class="badge badge-nonaktif" style="border-radius:20px">Ditolak</span>
          @endif
        </td>
        <td style="font-size:12px;color:#64748b">{{ \Carbon\Carbon::parse($p->tanggal_diminta)->format('d M Y') }}</td>
      </tr>
      @empty
      <tr><td colspan="6" class="text-center py-4" style="color:#94a3b8">Tidak ada data penarikan</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

{{-- ══ REKAP PENJUALAN PENGEPUL ════════════════════════════════════════════ --}}
<div class="table-card">
  <div class="card-header">
    <h6><i class="bi bi-shop me-2" style="color:#4f46e5"></i>Penjualan ke Pengepul</h6>
    <span style="font-size:12px;color:#64748b">
      {{ $statPengepul->jumlah_transaksi }} transaksi •
      Total tahun ini: <strong>Rp {{ number_format($statPengepul->total_tahun_ini,0,',','.') }}</strong>
    </span>
  </div>
  <table class="table mb-0">
    <thead>
      <tr><th>Tanggal</th><th>Dicatat oleh</th><th class="text-end">Total</th><th>Catatan</th></tr>
    </thead>
    <tbody>
      @forelse($rekapPengepul as $pg)
      <tr>
        <td style="font-size:13px">{{ \Carbon\Carbon::parse($pg->tanggal_jual)->format('d M Y') }}</td>
        <td style="font-weight:600;font-size:13px">{{ $pg->admin_name }}</td>
        <td class="text-end" style="font-weight:700;color:#4f46e5">Rp {{ number_format($pg->total_uang,0,',','.') }}</td>
        <td style="font-size:12px;color:#64748b">{{ $pg->catatan ?: '-' }}</td>
      </tr>
      @empty
      <tr><td colspan="4" class="text-center py-4" style="color:#94a3b8">Tidak ada data penjualan</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

@endsection

@push('scripts')
<script>
// Chart Setoran
const bulanNames = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
const grafikData = @json($grafik);
const setoranLabels = bulanNames;
const setoranValues = setoranLabels.map((_, i) => {
  const row = grafikData.find(r => r.bulan == i + 1);
  return row ? parseFloat(row.total) : 0;
});

new Chart(document.getElementById('chartSetoran'), {
  type: 'line',
  data: {
    labels: setoranLabels,
    datasets: [{
      label: 'Nilai Setoran', data: setoranValues,
      borderColor: '#4f46e5', backgroundColor: 'rgba(79,70,229,.08)',
      borderWidth: 2.5, fill: true, tension: 0.4,
      pointBackgroundColor: '#4f46e5', pointRadius: 4
    }]
  },
  options: {
    responsive: true, plugins: { legend: { display: false } },
    scales: {
      y: { beginAtZero: true, grid: { color: '#f1f5f9' },
           ticks: { callback: v => 'Rp ' + (v/1000).toFixed(0) + 'K', font: { size: 11 } } },
      x: { grid: { display: false }, ticks: { font: { size: 11 } } }
    }
  }
});

// Chart Kategori (doughnut)
const katData = @json($grafikKategori);
if (katData.length > 0) {
  new Chart(document.getElementById('chartKategori'), {
    type: 'doughnut',
    data: {
      labels: katData.map(k => k.nama_kategori),
      datasets: [{
        data: katData.map(k => parseFloat(k.total_berat)),
        backgroundColor: ['#4f46e5','#7c3aed','#06b6d4','#10b981','#f59e0b','#ef4444'],
        borderWidth: 0, hoverOffset: 4
      }]
    },
    options: {
      responsive: true,
      plugins: { legend: { position: 'bottom', labels: { font: { size: 11 }, boxWidth: 12, padding: 12 } } },
      cutout: '65%'
    }
  });
}
</script>
@endpush
