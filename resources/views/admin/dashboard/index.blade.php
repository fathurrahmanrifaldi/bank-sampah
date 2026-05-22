@extends('layouts.admin')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<!-- Stat Cards Row -->
<div class="row g-3 mb-4">
  <div class="col-sm-6 col-xl-3">
    <div class="stat-card">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <div class="stat-icon" style="background:#dcfce7">🧑‍🤝‍🧑</div>
        <span class="badge" style="background:#dcfce7;color:#16a34a;font-size:11px">Total</span>
      </div>
      <div class="stat-value">{{ $totalNasabah }}</div>
      <div class="stat-label">Nasabah Terdaftar</div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="stat-card">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <div class="stat-icon" style="background:#dbeafe">🔄</div>
        <span class="badge" style="background:#dbeafe;color:#1d4ed8;font-size:11px">Bulan ini</span>
      </div>
      <div class="stat-value">{{ $transaksiBuilan }}</div>
      <div class="stat-label">Transaksi Setoran</div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="stat-card">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <div class="stat-icon" style="background:#fef9c3">⚖️</div>
        <span class="badge" style="background:#fef9c3;color:#854d0e;font-size:11px">Bulan ini</span>
      </div>
      <div class="stat-value">{{ number_format($totalBeratBulan, 1) }} kg</div>
      <div class="stat-label">Total Berat Sampah</div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="stat-card">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <div class="stat-icon" style="background:#fce7f3">💰</div>
        <span class="badge" style="background:#fce7f3;color:#9d174d;font-size:11px">Bulan ini</span>
      </div>
      <div class="stat-value">Rp {{ number_format($nilaiBuilan, 0, ',', '.') }}</div>
      <div class="stat-label">Total Nilai Setoran</div>
    </div>
  </div>
</div>

<!-- Chart + Tabel Transaksi Terbaru -->
<div class="row g-3">
  <div class="col-xl-7">
    <div class="table-card p-0" style="border-radius:12px">
      <div class="card-header" style="padding:16px 20px">
        <h6>📊 Setoran 6 Bulan Terakhir</h6>
      </div>
      <div class="p-3">
        <canvas id="chartDashboard" height="110"></canvas>
      </div>
    </div>
  </div>
  <div class="col-xl-5">
    <div class="table-card">
      <div class="card-header">
        <h6>🕐 Transaksi Terbaru</h6>
        <a href="{{ route('admin.transaksi.index') }}" class="btn btn-sm"
           style="background:#f1f5f9;font-size:12px">Lihat semua</a>
      </div>
      <div class="table-responsive">
        <table class="table mb-0">
          <thead>
            <tr>
              <th>Nasabah</th>
              <th>Tanggal</th>
              <th>Nilai</th>
            </tr>
          </thead>
          <tbody>
            @forelse($transaksiTerbaru as $t)
            <tr>
              <td>
                <div style="font-size:13px;font-weight:600">{{ $t->nasabah->user->name }}</div>
                <div style="font-size:11px;color:#94a3b8">{{ $t->nasabah->nik }}</div>
              </td>
              <td style="font-size:13px">{{ $t->tanggal->format('d M Y') }}</td>
              <td>
                <span class="badge" style="background:#dcfce7;color:#15803d;font-size:12px">
                  Rp {{ number_format($t->total_nilai, 0, ',', '.') }}
                </span>
              </td>
            </tr>
            @empty
            <tr><td colspan="3" class="text-center text-muted py-3">Belum ada transaksi</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const labels = @json($chartLabels);
const dataNilai = @json($chartNilai);
const dataBerat = @json($chartBerat);

new Chart(document.getElementById('chartDashboard'), {
  data: {
    labels: labels,
    datasets: [
      {
        type: 'bar',
        label: 'Total Nilai (Rp)',
        data: dataNilai,
        backgroundColor: 'rgba(22,163,74,0.7)',
        borderRadius: 6,
        yAxisID: 'yNilai',
      },
      {
        type: 'line',
        label: 'Total Berat (kg)',
        data: dataBerat,
        borderColor: '#3b82f6',
        backgroundColor: 'rgba(59,130,246,0.1)',
        tension: 0.4,
        yAxisID: 'yBerat',
        pointRadius: 4,
      }
    ]
  },
  options: {
    responsive: true,
    interaction: { mode: 'index' },
    plugins: { legend: { position: 'bottom', labels: { font: { size: 11 } } } },
    scales: {
      yNilai: { position: 'left',  ticks: { font: { size: 10 }, callback: v => 'Rp'+v.toLocaleString('id') } },
      yBerat: { position: 'right', ticks: { font: { size: 10 }, callback: v => v+' kg' }, grid: { drawOnChartArea: false } }
    }
  }
});
</script>
@endpush