@extends('layouts.admin')
@section('title','Jual ke Pengepul')
@section('page-title','Penjualan Sampah ke Pengepul')

@section('content')

{{-- ── Stat Cards (all-time) ── --}}
<div class="row g-3 mb-4">
  {{-- Total uang masuk keseluruhan --}}
  <div class="col-md-4">
    <div class="stat-card">
      <div class="d-flex align-items-start justify-content-between">
        <div>
          <div class="stat-label">Total Uang Masuk</div>
          <div class="stat-value" style="font-size:22px;color:#16a34a">
            Rp {{ number_format($stat->total_uang ?? 0, 0, ',', '.') }}
          </div>
          <div class="stat-change text-muted">Keseluruhan penjualan</div>
        </div>
        <div class="stat-icon" style="background:#dcfce7;color:#16a34a">
          <i class="bi bi-cash-stack"></i>
        </div>
      </div>
    </div>
  </div>

  {{-- Jumlah transaksi jual keseluruhan --}}
  <div class="col-md-4">
    <div class="stat-card">
      <div class="d-flex align-items-start justify-content-between">
        <div>
          <div class="stat-label">Total Transaksi Jual</div>
          <div class="stat-value" style="font-size:22px">
            {{ $stat->jumlah_transaksi ?? 0 }} <span style="font-size:14px;color:#64748b">kali</span>
          </div>
          <div class="stat-change text-muted">Keseluruhan penjualan</div>
        </div>
        <div class="stat-icon" style="background:#f0fdf4;color:#16a34a">
          <i class="bi bi-receipt"></i>
        </div>
      </div>
    </div>
  </div>

  {{-- Rata-rata per transaksi --}}
  <div class="col-md-4">
    <div class="stat-card">
      <div class="d-flex align-items-start justify-content-between">
        <div>
          <div class="stat-label">Rata-rata per Penjualan</div>
          <div class="stat-value" style="font-size:22px;color:#0f172a">
            Rp {{ number_format($stat->rata_uang ?? 0, 0, ',', '.') }}
          </div>
          <div class="stat-change text-muted">Rata-rata keseluruhan</div>
        </div>
        <div class="stat-icon" style="background:#fefce8;color:#ca8a04">
          <i class="bi bi-graph-up-arrow"></i>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ── Grafik 6 Bulan Terakhir ── --}}
<div class="table-card mb-4">
  <div class="card-header">
    <h6><i class="bi bi-bar-chart-line me-2"></i>Uang Masuk 6 Bulan Terakhir</h6>
  </div>
  <div style="padding:20px">
    <canvas id="chartPenjualan" height="80"></canvas>
  </div>
</div>

{{-- ── Filter & Tabel ── --}}
<div class="d-flex justify-content-between align-items-center mb-3">
  <p class="text-muted mb-0" style="font-size:13px">Total {{ $penjualan->total() }} catatan penjualan</p>
  <a href="{{ route('admin.penjualan-pengepul.create') }}" class="btn btn-sm"
     style="background:#16a34a;color:#fff;border-radius:8px">
    <i class="bi bi-plus-lg me-1"></i>Catat Penjualan Baru
  </a>
</div>

<div class="table-card">
  <div class="card-header">
    <h6>Riwayat Penjualan ke Pengepul</h6>
  </div>
  <div class="table-responsive">
    <table class="table mb-0">
      <thead>
        <tr>
          <th>#</th>
          <th>Tanggal Jual</th>
          <th>Total Uang dari Pengepul</th>
          <th>Catatan</th>
          <th>Dicatat oleh</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($penjualan as $p)
        <tr>
          <td style="color:#94a3b8;font-size:12px">{{ $p->id }}</td>
          <td style="font-size:13px;font-weight:600">
            {{ $p->tanggal_jual->translatedFormat('d F Y') }}
          </td>
          <td style="font-weight:700;color:#16a34a;font-size:14px">
            Rp {{ number_format($p->total_uang, 0, ',', '.') }}
          </td>
          <td style="font-size:12px;color:#64748b;max-width:200px">
            {{ $p->catatan ?? '—' }}
          </td>
          <td style="font-size:12px;color:#64748b">{{ $p->admin->name }}</td>
          <td>
            <div class="d-flex gap-1">
              <a href="{{ route('admin.penjualan-pengepul.show', $p) }}"
                 class="btn btn-sm" style="background:#dcfce7;color:#16a34a;border-radius:7px" title="Lihat Detail">
                <i class="bi bi-eye"></i>
              </a>
              <a href="{{ route('admin.penjualan-pengepul.edit', $p) }}"
                 class="btn btn-sm" style="background:#fef9c3;color:#ca8a04;border-radius:7px" title="Edit">
                <i class="bi bi-pencil"></i>
              </a>
              <form method="POST" action="{{ route('admin.penjualan-pengepul.destroy', $p) }}"
                    onsubmit="return confirm('Hapus data penjualan tanggal {{ $p->tanggal_jual->translatedFormat('d F Y') }}?\nTindakan ini tidak dapat dibatalkan.')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm" style="background:#fee2e2;color:#dc2626;border-radius:7px" title="Hapus">
                  <i class="bi bi-trash"></i>
                </button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6" class="text-center text-muted py-4">
            <i class="bi bi-inbox" style="font-size:24px;display:block;margin-bottom:6px"></i>
            Belum ada data penjualan ke pengepul.
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($penjualan->hasPages())
    <div class="d-flex justify-content-end p-3">{{ $penjualan->links() }}</div>
  @endif
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const grafikData = @json($grafik);

new Chart(document.getElementById('chartPenjualan'), {
  type: 'bar',
  data: {
    labels: grafikData.map(d => d.label),
    datasets: [{
      label: 'Total Uang (Rp)',
      data: grafikData.map(d => d.total),
      backgroundColor: 'rgba(22,163,74,0.75)',
      borderRadius: 6,
      borderSkipped: false,
    }]
  },
  options: {
    responsive: true,
    plugins: {
      legend: { display: false },
      tooltip: {
        callbacks: {
          label: ctx => 'Rp ' + ctx.raw.toLocaleString('id-ID')
        }
      }
    },
    scales: {
      y: {
        ticks: {
          callback: val => 'Rp ' + val.toLocaleString('id-ID')
        },
        grid: { color: '#f1f5f9' }
      },
      x: { grid: { display: false } }
    }
  }
});
</script>
@endpush
