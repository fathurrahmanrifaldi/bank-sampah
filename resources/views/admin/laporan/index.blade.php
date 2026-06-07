@extends('layouts.admin')
@section('title', 'Laporan Bank Sampah')
@section('page-title', 'Laporan Bank Sampah')

@push('head-styles')
<style>
  /* ── Tab Navigation ─────────────────────────────────────────── */
  .laporan-tabs {
    display: flex;
    gap: 4px;
    background: #f1f5f9;
    border-radius: 12px;
    padding: 4px;
    margin-bottom: 24px;
    overflow-x: auto;
  }
  .laporan-tab {
    flex: 1;
    min-width: 140px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 10px 16px;
    border-radius: 8px;
    border: none;
    background: transparent;
    color: #64748b;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all .2s;
    white-space: nowrap;
  }
  .laporan-tab:hover { background: #e2e8f0; color: #334155; }
  .laporan-tab.active { background: #fff; color: #0f172a; box-shadow: 0 1px 4px rgba(0,0,0,.1); }
  .laporan-tab i { font-size: 16px; }
  .tab-indicator {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 18px; height: 18px; border-radius: 9px;
    background: #16a34a; color: #fff; font-size: 10px; font-weight: 700;
  }

  /* ── Tab Panels ─────────────────────────────────────────────── */
  .tab-panel { display: none; }
  .tab-panel.active { display: block; }

  /* ── Summary Cards ──────────────────────────────────────────── */
  .summary-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px,1fr)); gap: 16px; margin-bottom: 20px; }
  .summary-card {
    background: #fff;
    border-radius: 12px;
    padding: 18px 20px;
    border: 1px solid #e2e8f0;
    display: flex; align-items: flex-start; gap: 14px;
  }
  .summary-icon {
    width: 42px; height: 42px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; flex-shrink: 0;
  }
  .summary-value { font-size: 20px; font-weight: 700; color: #0f172a; line-height: 1.2; }
  .summary-label { font-size: 11.5px; color: #64748b; margin-top: 2px; }

  /* ── Section Header ─────────────────────────────────────────── */
  .section-header {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 12px;
  }
  .section-title { font-size: 14px; font-weight: 700; color: #0f172a; margin: 0; }
  .section-sub { font-size: 12px; color: #64748b; }

  /* ── Charts Row ─────────────────────────────────────────────── */
  .charts-row { display: grid; grid-template-columns: 7fr 5fr; gap: 16px; margin-bottom: 20px; }
  .chart-card {
    background: #fff; border-radius: 12px; border: 1px solid #e2e8f0;
    padding: 18px 20px;
  }
  .chart-title { font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 12px; }
  @media (max-width: 768px) {
    .charts-row { grid-template-columns: 1fr; }
    .summary-grid { grid-template-columns: 1fr 1fr; }
  }

  /* ── Table Card ─────────────────────────────────────────────── */
  .report-card { background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; }
  .report-card-header {
    padding: 14px 20px;
    border-bottom: 1px solid #f1f5f9;
    display: flex; align-items: center; justify-content: space-between;
  }
  .report-card-header h6 { margin: 0; font-size: 13.5px; font-weight: 600; }

  /* ── Status Badge ───────────────────────────────────────────── */
  .status-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 3px 10px; border-radius: 20px; font-size: 11.5px; font-weight: 600;
  }
  .status-badge.menunggu  { background: #fef9c3; color: #854d0e; }
  .status-badge.disetujui { background: #dcfce7; color: #14532d; }
  .status-badge.ditolak   { background: #fee2e2; color: #7f1d1d; }

  /* ── Jenis Badge ────────────────────────────────────────────── */
  .jenis-badge {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;
  }
  .jenis-badge.segera     { background: #e0f2fe; color: #0369a1; }
  .jenis-badge.terjadwal  { background: #f3e8ff; color: #7e22ce; }

  /* ── Filter form ────────────────────────────────────────────── */
  .filter-card {
    background: #fff; border-radius: 12px; border: 1px solid #e2e8f0;
    padding: 14px 20px; margin-bottom: 20px;
    display: flex; align-items: center; gap: 12px; flex-wrap: wrap;
  }
  .filter-label { font-size: 12.5px; font-weight: 600; color: #374151; white-space: nowrap; }

  /* ── Keuangan Summary ───────────────────────────────────────── */
  .keuangan-card {
    background: linear-gradient(135deg, #0f172a, #1e293b);
    border-radius: 14px; padding: 22px 26px; color: #fff; margin-bottom: 20px;
  }
  .keuangan-title { font-size: 12px; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 16px; }
  .keuangan-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
  .keuangan-item {}
  .keuangan-item-label { font-size: 11px; color: #94a3b8; margin-bottom: 4px; }
  .keuangan-item-value { font-size: 18px; font-weight: 700; }
  .keuangan-divider { width: 1px; background: #334155; }
  @media (max-width: 600px) { .keuangan-grid { grid-template-columns: 1fr; } }

  /* ── Empty State ────────────────────────────────────────────── */
  .empty-state { text-align: center; padding: 40px 20px; }
  .empty-state i { font-size: 40px; color: #cbd5e1; margin-bottom: 10px; }
  .empty-state p { font-size: 13px; color: #94a3b8; margin: 0; }
</style>
@endpush

@section('content')
<div class="container-fluid">

  {{-- ═══ FILTER ══════════════════════════════════════════════════════════════ --}}
  <div class="filter-card">
    <span class="filter-label"><i class="bi bi-funnel me-1"></i>Filter Periode</span>
    <form method="GET" class="d-flex align-items-center gap-2 flex-wrap">
      <select name="bulan" class="form-select form-select-sm" style="width:auto">
        @for ($i = 1; $i <= 12; $i++)
          <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>
            {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
          </option>
        @endfor
      </select>
      <input type="number" name="tahun" value="{{ $tahun }}"
             class="form-control form-control-sm" style="width:90px" min="2020" max="2099">
      <button class="btn btn-primary btn-sm px-3">
        <i class="bi bi-search me-1"></i>Tampilkan
      </button>
      <span class="text-muted" style="font-size:12px">
        Menampilkan: <strong>{{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }} {{ $tahun }}</strong>
      </span>
    </form>
  </div>

  {{-- ═══ RINGKASAN KEUANGAN BULAN INI ════════════════════════════════════════ --}}
  <div class="keuangan-card">
    <div class="keuangan-title">
      <i class="bi bi-wallet2 me-1"></i>Ringkasan Keuangan –
      {{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }} {{ $tahun }}
    </div>
    <div class="keuangan-grid">
      <div class="keuangan-item">
        <div class="keuangan-item-label"><i class="bi bi-arrow-down-circle me-1"></i>Total Nilai Setoran</div>
        <div class="keuangan-item-value text-success">Rp {{ number_format($totalSetoran, 0, ',', '.') }}</div>
        <div style="font-size:11px;color:#64748b;margin-top:4px">{{ $rekap->count() }} nasabah setor</div>
      </div>
      <div class="keuangan-item" style="border-left:1px solid #334155;padding-left:20px">
        <div class="keuangan-item-label"><i class="bi bi-cash-stack me-1"></i>Dana Dicairkan</div>
        <div class="keuangan-item-value text-warning">Rp {{ number_format($totalDicairkan, 0, ',', '.') }}</div>
        <div style="font-size:11px;color:#64748b;margin-top:4px">{{ $statPenarikan->jumlah_pengajuan }} pengajuan</div>
      </div>
      <div class="keuangan-item" style="border-left:1px solid #334155;padding-left:20px">
        <div class="keuangan-item-label"><i class="bi bi-shop me-1"></i>Penjualan ke Pengepul</div>
        <div class="keuangan-item-value text-info">Rp {{ number_format($totalPengepul, 0, ',', '.') }}</div>
        <div style="font-size:11px;color:#64748b;margin-top:4px">{{ $statPengepul->jumlah_transaksi }} transaksi</div>
      </div>
    </div>
  </div>

  {{-- ═══ TAB NAVIGATION ═══════════════════════════════════════════════════════ --}}
  <div class="laporan-tabs">
    <button class="laporan-tab active" data-tab="setoran" id="tab-setoran">
      <i class="bi bi-recycle"></i> Setoran Nasabah
      @if($rekap->count() > 0)
        <span class="tab-indicator">{{ $rekap->count() }}</span>
      @endif
    </button>
    <button class="laporan-tab" data-tab="penarikan" id="tab-penarikan">
      <i class="bi bi-cash-stack"></i> Penarikan Dana
      @if($statPenarikan->jumlah_pengajuan > 0)
        <span class="tab-indicator">{{ $statPenarikan->jumlah_pengajuan }}</span>
      @endif
    </button>
    <button class="laporan-tab" data-tab="pengepul" id="tab-pengepul">
      <i class="bi bi-shop"></i> Jual ke Pengepul
      @if($statPengepul->jumlah_transaksi > 0)
        <span class="tab-indicator">{{ $statPengepul->jumlah_transaksi }}</span>
      @endif
    </button>
    <button class="laporan-tab" data-tab="overview" id="tab-overview">
      <i class="bi bi-bar-chart-line"></i> Grafik Tahunan
    </button>
  </div>

  {{-- ════════════════════════════════════════════════════════════════════════════
       TAB 1 – SETORAN NASABAH
  ════════════════════════════════════════════════════════════════════════════ --}}
  <div class="tab-panel active" id="panel-setoran">

    {{-- Grafik --}}
    <div class="charts-row">
      <div class="chart-card">
        <div class="chart-title"><i class="bi bi-bar-chart me-1 text-primary"></i>Total Nilai Setoran per Bulan ({{ $tahun }})</div>
        <canvas id="chartBulan" height="100"></canvas>
      </div>
      <div class="chart-card">
        <div class="chart-title"><i class="bi bi-pie-chart me-1 text-success"></i>Komposisi Sampah per Kategori</div>
        <canvas id="chartKategori" height="160"></canvas>
      </div>
    </div>

    {{-- Tabel rekap --}}
    <div class="report-card">
      <div class="report-card-header">
        <h6><i class="bi bi-table me-2 text-primary"></i>Rekap Setoran Nasabah –
          {{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }} {{ $tahun }}
        </h6>
        <span class="section-sub">{{ $rekap->count() }} nasabah</span>
      </div>
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead>
            <tr>
              <th>No</th>
              <th>Nama Nasabah</th>
              <th>NIK</th>
              <th class="text-center">Jml Setor</th>
              <th class="text-end">Total Nilai</th>
            </tr>
          </thead>
          <tbody>
            @forelse($rekap as $i => $r)
            <tr>
              <td class="text-muted">{{ $i + 1 }}</td>
              <td class="fw-semibold">{{ $r->name }}</td>
              <td class="text-muted font-monospace" style="font-size:12px">{{ $r->nik }}</td>
              <td class="text-center">
                <span class="badge bg-primary bg-opacity-10 text-primary fw-semibold">
                  {{ $r->jumlah_setor }}×
                </span>
              </td>
              <td class="text-end fw-bold text-success">Rp {{ number_format($r->total_nilai, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
              <td colspan="5">
                <div class="empty-state">
                  <i class="bi bi-inbox d-block"></i>
                  <p>Tidak ada setoran nasabah pada periode ini</p>
                </div>
              </td>
            </tr>
            @endforelse
          </tbody>
          @if($rekap->count() > 0)
          <tfoot>
            <tr class="table-light">
              <td colspan="3" class="fw-bold text-end text-muted" style="font-size:12px">TOTAL</td>
              <td class="text-center fw-bold">{{ $rekap->sum('jumlah_setor') }}×</td>
              <td class="text-end fw-bold text-success">
                Rp {{ number_format($rekap->sum('total_nilai'), 0, ',', '.') }}
              </td>
            </tr>
          </tfoot>
          @endif
        </table>
      </div>
    </div>
  </div>

  {{-- ════════════════════════════════════════════════════════════════════════════
       TAB 2 – PENARIKAN DANA
  ════════════════════════════════════════════════════════════════════════════ --}}
  <div class="tab-panel" id="panel-penarikan">

    {{-- Stat cards --}}
    <div class="summary-grid">
      <div class="summary-card">
        <div class="summary-icon" style="background:#fef9c3">
          <i class="bi bi-hourglass-split" style="color:#b45309"></i>
        </div>
        <div>
          <div class="summary-value text-warning">Rp {{ number_format($statPenarikan->total_menunggu, 0, ',', '.') }}</div>
          <div class="summary-label">Sedang Menunggu</div>
        </div>
      </div>
      <div class="summary-card">
        <div class="summary-icon" style="background:#dcfce7">
          <i class="bi bi-check-circle" style="color:#15803d"></i>
        </div>
        <div>
          <div class="summary-value text-success">Rp {{ number_format($statPenarikan->total_disetujui, 0, ',', '.') }}</div>
          <div class="summary-label">Berhasil Dicairkan</div>
        </div>
      </div>
      <div class="summary-card">
        <div class="summary-icon" style="background:#fee2e2">
          <i class="bi bi-x-circle" style="color:#dc2626"></i>
        </div>
        <div>
          <div class="summary-value text-danger">Rp {{ number_format($statPenarikan->total_ditolak, 0, ',', '.') }}</div>
          <div class="summary-label">Ditolak</div>
        </div>
      </div>
      <div class="summary-card">
        <div class="summary-icon" style="background:#e0f2fe">
          <i class="bi bi-list-check" style="color:#0284c7"></i>
        </div>
        <div>
          <div class="summary-value text-info">{{ $statPenarikan->jumlah_pengajuan }}</div>
          <div class="summary-label">Total Pengajuan</div>
        </div>
      </div>
    </div>

    {{-- Grafik penarikan --}}
    <div class="chart-card mb-4">
      <div class="chart-title"><i class="bi bi-bar-chart me-1 text-warning"></i>Total Dana Dicairkan per Bulan ({{ $tahun }})</div>
      <canvas id="chartPenarikan" height="80"></canvas>
    </div>

    {{-- Tabel penarikan --}}
    <div class="report-card">
      <div class="report-card-header">
        <h6><i class="bi bi-cash-stack me-2 text-warning"></i>Riwayat Pengajuan Penarikan –
          {{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }} {{ $tahun }}
        </h6>
        <span class="section-sub">{{ $rekapPenarikan->count() }} pengajuan</span>
      </div>
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead>
            <tr>
              <th>No</th>
              <th>Nama Nasabah</th>
              <th>NIK</th>
              <th>Jenis</th>
              <th>Tgl Diminta</th>
              <th>Tgl Cair</th>
              <th class="text-end">Jumlah</th>
              <th class="text-center">Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse($rekapPenarikan as $i => $p)
            <tr>
              <td class="text-muted">{{ $i + 1 }}</td>
              <td class="fw-semibold">{{ $p->name }}</td>
              <td class="text-muted font-monospace" style="font-size:12px">{{ $p->nik }}</td>
              <td>
                <span class="jenis-badge {{ $p->jenis }}">
                  @if($p->jenis === 'segera')
                    <i class="bi bi-lightning-charge"></i> Segera
                  @else
                    <i class="bi bi-calendar-event"></i> Terjadwal
                  @endif
                </span>
              </td>
              <td style="font-size:12.5px">
                {{ \Carbon\Carbon::parse($p->tanggal_diminta)->format('d M Y') }}
              </td>
              <td style="font-size:12.5px">
                {{ $p->tanggal_pencairan ? \Carbon\Carbon::parse($p->tanggal_pencairan)->format('d M Y') : '—' }}
              </td>
              <td class="text-end fw-bold">Rp {{ number_format($p->jumlah, 0, ',', '.') }}</td>
              <td class="text-center">
                <span class="status-badge {{ $p->status }}">
                  @if($p->status === 'menunggu')
                    <i class="bi bi-clock"></i> Menunggu
                  @elseif($p->status === 'disetujui')
                    <i class="bi bi-check"></i> Disetujui
                  @else
                    <i class="bi bi-x"></i> Ditolak
                  @endif
                </span>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="8">
                <div class="empty-state">
                  <i class="bi bi-cash-coin d-block"></i>
                  <p>Tidak ada pengajuan penarikan pada periode ini</p>
                </div>
              </td>
            </tr>
            @endforelse
          </tbody>
          @if($rekapPenarikan->count() > 0)
          <tfoot>
            <tr class="table-light">
              <td colspan="6" class="fw-bold text-end text-muted" style="font-size:12px">TOTAL DICAIRKAN</td>
              <td class="text-end fw-bold text-success">
                Rp {{ number_format($rekapPenarikan->where('status','disetujui')->sum('jumlah'), 0, ',', '.') }}
              </td>
              <td></td>
            </tr>
          </tfoot>
          @endif
        </table>
      </div>
    </div>
  </div>

  {{-- ════════════════════════════════════════════════════════════════════════════
       TAB 3 – PENJUALAN PENGEPUL
  ════════════════════════════════════════════════════════════════════════════ --}}
  <div class="tab-panel" id="panel-pengepul">

    {{-- Stat cards --}}
    <div class="summary-grid">
      <div class="summary-card">
        <div class="summary-icon" style="background:#e0f2fe">
          <i class="bi bi-shop" style="color:#0369a1"></i>
        </div>
        <div>
          <div class="summary-value text-info">Rp {{ number_format($statPengepul->total_bulan_ini, 0, ',', '.') }}</div>
          <div class="summary-label">Penjualan Bulan Ini</div>
        </div>
      </div>
      <div class="summary-card">
        <div class="summary-icon" style="background:#dcfce7">
          <i class="bi bi-receipt" style="color:#15803d"></i>
        </div>
        <div>
          <div class="summary-value text-success">{{ $statPengepul->jumlah_transaksi }}</div>
          <div class="summary-label">Jumlah Transaksi</div>
        </div>
      </div>
      <div class="summary-card">
        <div class="summary-icon" style="background:#f3e8ff">
          <i class="bi bi-calendar-check" style="color:#7c3aed"></i>
        </div>
        <div>
          <div class="summary-value" style="color:#7c3aed">Rp {{ number_format($statPengepul->total_tahun_ini, 0, ',', '.') }}</div>
          <div class="summary-label">Akumulasi Tahun {{ $tahun }}</div>
        </div>
      </div>
      <div class="summary-card">
        <div class="summary-icon" style="background:#fff7ed">
          <i class="bi bi-graph-up" style="color:#ea580c"></i>
        </div>
        <div>
          <div class="summary-value text-warning">
            @if($statPengepul->jumlah_transaksi > 0)
              Rp {{ number_format($statPengepul->total_bulan_ini / $statPengepul->jumlah_transaksi, 0, ',', '.') }}
            @else
              —
            @endif
          </div>
          <div class="summary-label">Rata-rata per Transaksi</div>
        </div>
      </div>
    </div>

    {{-- Grafik pengepul --}}
    <div class="chart-card mb-4">
      <div class="chart-title"><i class="bi bi-bar-chart me-1 text-info"></i>Total Penjualan ke Pengepul per Bulan ({{ $tahun }})</div>
      <canvas id="chartPengepul" height="80"></canvas>
    </div>

    {{-- Tabel penjualan --}}
    <div class="report-card">
      <div class="report-card-header">
        <h6><i class="bi bi-shop me-2 text-info"></i>Riwayat Penjualan ke Pengepul –
          {{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }} {{ $tahun }}
        </h6>
        <span class="section-sub">{{ $rekapPengepul->count() }} transaksi</span>
      </div>
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead>
            <tr>
              <th>No</th>
              <th>Tanggal Jual</th>
              <th>Admin</th>
              <th>Catatan</th>
              <th class="text-end">Total Uang</th>
            </tr>
          </thead>
          <tbody>
            @forelse($rekapPengepul as $i => $pj)
            <tr>
              <td class="text-muted">{{ $i + 1 }}</td>
              <td style="font-size:12.5px">
                <i class="bi bi-calendar3 me-1 text-muted"></i>
                {{ \Carbon\Carbon::parse($pj->tanggal_jual)->format('d M Y') }}
              </td>
              <td>
                <span class="d-flex align-items-center gap-2">
                  <span style="width:26px;height:26px;border-radius:50%;background:#16a34a;color:#fff;display:inline-flex;align-items:center;justify-content:center;font-size:11px;font-weight:700">
                    {{ strtoupper(substr($pj->admin_name, 0, 1)) }}
                  </span>
                  {{ $pj->admin_name }}
                </span>
              </td>
              <td class="text-muted" style="font-size:12.5px">{{ $pj->catatan ?? '—' }}</td>
              <td class="text-end fw-bold text-info">Rp {{ number_format($pj->total_uang, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
              <td colspan="5">
                <div class="empty-state">
                  <i class="bi bi-shop d-block"></i>
                  <p>Tidak ada penjualan ke pengepul pada periode ini</p>
                </div>
              </td>
            </tr>
            @endforelse
          </tbody>
          @if($rekapPengepul->count() > 0)
          <tfoot>
            <tr class="table-light">
              <td colspan="4" class="fw-bold text-end text-muted" style="font-size:12px">TOTAL</td>
              <td class="text-end fw-bold text-info">
                Rp {{ number_format($rekapPengepul->sum('total_uang'), 0, ',', '.') }}
              </td>
            </tr>
          </tfoot>
          @endif
        </table>
      </div>
    </div>
  </div>

  {{-- ════════════════════════════════════════════════════════════════════════════
       TAB 4 – GRAFIK TAHUNAN (OVERVIEW)
  ════════════════════════════════════════════════════════════════════════════ --}}
  <div class="tab-panel" id="panel-overview">

    <div class="chart-card mb-4">
      <div class="chart-title">
        <i class="bi bi-bar-chart-line me-1 text-primary"></i>
        Perbandingan Setoran Nasabah vs Penjualan Pengepul vs Penarikan Dana – Tahun {{ $tahun }}
      </div>
      <canvas id="chartOverview" height="90"></canvas>
    </div>

    <div class="charts-row">
      <div class="chart-card">
        <div class="chart-title"><i class="bi bi-pie-chart me-1 text-success"></i>Kategori Sampah Terlaris – {{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }} {{ $tahun }}</div>
        <canvas id="chartKategori2" height="160"></canvas>
      </div>
      <div class="chart-card">
        <div class="chart-title"><i class="bi bi-bar-chart me-1 text-warning"></i>Distribusi Status Penarikan</div>
        <canvas id="chartStatusPenarikan" height="160"></canvas>
      </div>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// ── Data dari controller ─────────────────────────────────────
const grafikData        = @json($grafik);
const kategoriData      = @json($grafikKategori);
const grafikPenarikan   = @json($grafikPenarikan);
const grafikPengepul    = @json($grafikPengepul);
const statPenarikan     = @json($statPenarikan);

const bulanNames = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des'];

// ── Helper: isi array 12 bulan ───────────────────────────────
function toMonthArray(data, valueKey = 'total') {
  const arr = Array(12).fill(0);
  data.forEach(d => { arr[d.bulan - 1] = parseFloat(d[valueKey] || 0); });
  return arr;
}

// ── Chart 1: Setoran per bulan ───────────────────────────────
new Chart(document.getElementById('chartBulan'), {
  type: 'bar',
  data: {
    labels: bulanNames,
    datasets: [{
      label: 'Total Nilai (Rp)',
      data: toMonthArray(grafikData),
      backgroundColor: '#3b82f680',
      borderColor: '#3b82f6',
      borderWidth: 1.5,
      borderRadius: 6,
    }]
  },
  options: {
    responsive: true,
    plugins: { legend: { display: false } },
    scales: {
      y: { ticks: { callback: v => 'Rp ' + (v/1000).toLocaleString('id') + 'K' }, grid: { color: '#f1f5f9' } },
      x: { grid: { display: false } }
    }
  }
});

// ── Chart 2: Kategori donut ──────────────────────────────────
const donutColors = ['#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6','#ec4899','#06b6d4'];
function makeKategoriChart(canvasId) {
  const ctx = document.getElementById(canvasId);
  if (!ctx) return;
  if (kategoriData.length === 0) {
    ctx.parentElement.innerHTML += '<p class="text-muted text-center" style="font-size:12px">Tidak ada data kategori</p>';
    return;
  }
  new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: kategoriData.map(d => d.nama_kategori),
      datasets: [{
        data: kategoriData.map(d => parseFloat(d.total_berat)),
        backgroundColor: donutColors,
        borderWidth: 2, borderColor: '#fff',
      }]
    },
    options: {
      responsive: true,
      plugins: { legend: { position: 'bottom', labels: { font: { size: 11 }, padding: 12 } } }
    }
  });
}
makeKategoriChart('chartKategori');
makeKategoriChart('chartKategori2');

// ── Chart 3: Penarikan per bulan ─────────────────────────────
new Chart(document.getElementById('chartPenarikan'), {
  type: 'bar',
  data: {
    labels: bulanNames,
    datasets: [{
      label: 'Dana Dicairkan (Rp)',
      data: toMonthArray(grafikPenarikan),
      backgroundColor: '#f59e0b80',
      borderColor: '#f59e0b',
      borderWidth: 1.5,
      borderRadius: 6,
    }]
  },
  options: {
    responsive: true,
    plugins: { legend: { display: false } },
    scales: {
      y: { ticks: { callback: v => 'Rp ' + (v/1000).toLocaleString('id') + 'K' }, grid: { color: '#f1f5f9' } },
      x: { grid: { display: false } }
    }
  }
});

// ── Chart 4: Pengepul per bulan ──────────────────────────────
new Chart(document.getElementById('chartPengepul'), {
  type: 'bar',
  data: {
    labels: bulanNames,
    datasets: [{
      label: 'Penjualan Pengepul (Rp)',
      data: toMonthArray(grafikPengepul),
      backgroundColor: '#06b6d480',
      borderColor: '#06b6d4',
      borderWidth: 1.5,
      borderRadius: 6,
    }]
  },
  options: {
    responsive: true,
    plugins: { legend: { display: false } },
    scales: {
      y: { ticks: { callback: v => 'Rp ' + (v/1000).toLocaleString('id') + 'K' }, grid: { color: '#f1f5f9' } },
      x: { grid: { display: false } }
    }
  }
});

// ── Chart 5: Overview multi-dataset ─────────────────────────
new Chart(document.getElementById('chartOverview'), {
  type: 'bar',
  data: {
    labels: bulanNames,
    datasets: [
      {
        label: 'Setoran Nasabah',
        data: toMonthArray(grafikData),
        backgroundColor: '#3b82f680',
        borderColor: '#3b82f6',
        borderWidth: 1.5, borderRadius: 4,
      },
      {
        label: 'Penjualan Pengepul',
        data: toMonthArray(grafikPengepul),
        backgroundColor: '#06b6d480',
        borderColor: '#06b6d4',
        borderWidth: 1.5, borderRadius: 4,
      },
      {
        label: 'Dana Dicairkan',
        data: toMonthArray(grafikPenarikan),
        backgroundColor: '#f59e0b80',
        borderColor: '#f59e0b',
        borderWidth: 1.5, borderRadius: 4,
      }
    ]
  },
  options: {
    responsive: true,
    plugins: { legend: { position: 'top', labels: { font: { size: 11 } } } },
    scales: {
      y: { ticks: { callback: v => 'Rp ' + (v/1000).toLocaleString('id') + 'K' }, grid: { color: '#f1f5f9' } },
      x: { grid: { display: false } }
    }
  }
});

// ── Chart 6: Status penarikan (doughnut) ─────────────────────
new Chart(document.getElementById('chartStatusPenarikan'), {
  type: 'doughnut',
  data: {
    labels: ['Menunggu', 'Disetujui', 'Ditolak'],
    datasets: [{
      data: [
        parseFloat(statPenarikan.total_menunggu || 0),
        parseFloat(statPenarikan.total_disetujui || 0),
        parseFloat(statPenarikan.total_ditolak || 0),
      ],
      backgroundColor: ['#fbbf24', '#22c55e', '#ef4444'],
      borderWidth: 2, borderColor: '#fff',
    }]
  },
  options: {
    responsive: true,
    plugins: { legend: { position: 'bottom', labels: { font: { size: 11 }, padding: 12 } } }
  }
});

// ── Tab switching ────────────────────────────────────────────
document.querySelectorAll('.laporan-tab').forEach(btn => {
  btn.addEventListener('click', () => {
    const target = btn.dataset.tab;
    document.querySelectorAll('.laporan-tab').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('panel-' + target).classList.add('active');
  });
});
</script>
@endpush