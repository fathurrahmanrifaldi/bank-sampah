@extends('layouts.admin')
@section('title', 'Laporan Bank Sampah')

@section('content')
<div class="container-fluid">
  <h4 class="mb-4">Laporan Kegiatan Bank Sampah</h4>

  <!-- Filter bulan & tahun -->
  <form method="GET" class="row g-3 mb-4">
    <div class="col-auto">
      <select name="bulan" class="form-select">
        @for ($i = 1; $i <= 12; $i++)
          <option value="{{ $i }}"
            {{ $bulan == $i ? 'selected' : '' }}>
            {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
          </option>
        @endfor
      </select>
    </div>
    <div class="col-auto">
      <input type="number" name="tahun" value="{{ $tahun }}"
             class="form-control" min="2020" max="2099">
    </div>
    <div class="col-auto">
      <button class="btn btn-primary">Tampilkan</button>
    </div>
  </form>

  <!-- Grafik bar: total nilai per bulan -->
  <div class="row mb-4">
    <div class="col-md-7">
      <div class="card"><div class="card-body">
        <h6>Total Nilai per Bulan ({{ $tahun }})</h6>
        <canvas id="chartBulan" height="100"></canvas>
      </div></div>
    </div>
    <div class="col-md-5">
      <div class="card"><div class="card-body">
        <h6>Komposisi Sampah per Kategori</h6>
        <canvas id="chartKategori" height="140"></canvas>
      </div></div>
    </div>
  </div>

  <!-- Tabel rekapitulasi nasabah -->
  <div class="card">
    <div class="card-body">
      <h6>Rekap Setoran Nasabah – Bulan {{ $bulan }}/{{ $tahun }}</h6>
      <table class="table table-hover">
        <thead><tr>
          <th>No</th><th>Nama</th><th>No. Rekening</th>
          <th>Jml Setor</th><th>Total Nilai</th>
        </tr></thead>
        <tbody>
          @forelse($rekap as $i => $r)
          <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $r->name }}</td>
            <td>{{ $r->no_rekening }}</td>
            <td>{{ $r->jumlah_setor }} kali</td>
            <td>Rp {{ number_format($r->total_nilai, 0, ',', '.') }}</td>
          </tr>
          @empty
          <tr><td colspan="5" class="text-center text-muted">Belum ada data</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Data dari controller (di-encode ke JSON)
const grafikData = @json($grafik);
const kategoriData = @json($grafikKategori);
const bulanNames = ['Jan','Feb','Mar','Apr','Mei','Jun',
                    'Jul','Agt','Sep','Okt','Nov','Des'];

// Siapkan array 12 bulan, isi 0 dulu
const nilaiPerBulan = Array(12).fill(0);
grafikData.forEach(d => { nilaiPerBulan[d.bulan - 1] = parseFloat(d.total); });

// Chart 1: Bar chart total nilai per bulan
new Chart(document.getElementById('chartBulan'), {
  type: 'bar',
  data: {
    labels: bulanNames,
    datasets: [{
      label: 'Total Nilai (Rp)',
      data: nilaiPerBulan,
      backgroundColor: 'rgba(59,130,246,0.7)',
      borderRadius: 6,
    }]
  },
  options: { responsive: true, plugins: { legend: { display: false } } }
});

// Chart 2: Donut chart komposisi kategori
new Chart(document.getElementById('chartKategori'), {
  type: 'doughnut',
  data: {
    labels: kategoriData.map(d => d.nama_kategori),
    datasets: [{
      data: kategoriData.map(d => parseFloat(d.total_berat)),
      backgroundColor: ['#3b82f6','#10b981','#f59e0b','#ef4444'],
    }]
  },
  options: { responsive: true }
});
</script>
@endpush