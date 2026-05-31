@extends('layouts.admin')
@section('title','Detail Penjualan ke Pengepul')
@section('page-title','Detail Penjualan ke Pengepul')

@section('content')
<div class="mb-3">
  <a href="{{ route('admin.penjualan-pengepul.index') }}"
     style="font-size:13px;color:#64748b;text-decoration:none">
    <i class="bi bi-arrow-left me-1"></i>Kembali ke Daftar Penjualan
  </a>
</div>

<div class="row g-4">

  {{-- ── Kartu Utama: Detail Penjualan ── --}}
  <div class="col-lg-5">
    <div class="table-card">
      <div class="card-header">
        <h6><i class="bi bi-receipt me-2" style="color:#16a34a"></i>Detail Penjualan #{{ $penjualanPengepul->id }}</h6>
        <span style="font-size:11px;color:#94a3b8">
          Dicatat {{ $penjualanPengepul->created_at->diffForHumans() }}
        </span>
      </div>
      <div style="padding:24px">

        {{-- Nominal --}}
        <div style="text-align:center;padding:24px 0;border-bottom:1px solid #f1f5f9;margin-bottom:20px">
          <div style="font-size:11px;color:#94a3b8;text-transform:uppercase;letter-spacing:.8px;margin-bottom:6px">
            Total Uang Diterima dari Pengepul
          </div>
          <div style="font-size:36px;font-weight:800;color:#16a34a">
            Rp {{ number_format($penjualanPengepul->total_uang, 0, ',', '.') }}
          </div>
        </div>

        {{-- Info baris --}}
        <table style="width:100%;font-size:13px;border-collapse:separate;border-spacing:0 8px">
          <tr>
            <td style="color:#94a3b8;width:130px">Tanggal Jual</td>
            <td style="font-weight:600;color:#0f172a">
              <i class="bi bi-calendar3 me-1" style="color:#16a34a"></i>
              {{ $penjualanPengepul->tanggal_jual->translatedFormat('d F Y') }}
            </td>
          </tr>
          <tr>
            <td style="color:#94a3b8">Admin</td>
            <td style="font-weight:600;color:#0f172a">
              <i class="bi bi-person-circle me-1" style="color:#16a34a"></i>
              {{ $penjualanPengepul->admin->name }}
            </td>
          </tr>
          <tr>
            <td style="color:#94a3b8">Catatan</td>
            <td style="color:#374151">
              {{ $penjualanPengepul->catatan ?? '—' }}
            </td>
          </tr>
          <tr>
            <td style="color:#94a3b8">Dicatat pada</td>
            <td style="font-size:12px;color:#64748b">
              {{ $penjualanPengepul->created_at->translatedFormat('d F Y, H:i') }}
            </td>
          </tr>
        </table>

      </div>
    </div>
  </div>

  {{-- ── Ringkasan Setoran Nasabah Hari Itu ── --}}
  <div class="col-lg-7">
    <div class="table-card">
      <div class="card-header">
        <h6><i class="bi bi-people me-2" style="color:#0ea5e9"></i>Setoran Nasabah pada Hari yang Sama</h6>
        <span style="font-size:12px;color:#64748b;background:#f1f5f9;padding:3px 10px;border-radius:6px">
          {{ $penjualanPengepul->tanggal_jual->translatedFormat('d F Y') }}
        </span>
      </div>
      <div style="padding:20px">
        @if($ringkasan->jumlah_nasabah > 0)
          {{-- Stat mini --}}
          <div class="row g-3 mb-3">
            <div class="col-6">
              <div style="background:#f0fdf4;border-radius:10px;padding:14px 16px;border:1px solid #bbf7d0">
                <div style="font-size:11px;color:#15803d;font-weight:600;text-transform:uppercase;letter-spacing:.5px">
                  Nasabah Setor
                </div>
                <div style="font-size:28px;font-weight:700;color:#0f172a;margin-top:2px">
                  {{ $ringkasan->jumlah_nasabah }}
                </div>
              </div>
            </div>
            <div class="col-6">
              <div style="background:#f0fdf4;border-radius:10px;padding:14px 16px;border:1px solid #bbf7d0">
                <div style="font-size:11px;color:#15803d;font-weight:600;text-transform:uppercase;letter-spacing:.5px">
                  Nilai Setoran (Internal)
                </div>
                <div style="font-size:18px;font-weight:700;color:#0f172a;margin-top:2px">
                  Rp {{ number_format($ringkasan->nilai_total, 0, ',', '.') }}
                </div>
              </div>
            </div>
          </div>

          {{-- Selisih harga --}}
          @php
            $selisih = $penjualanPengepul->total_uang - $ringkasan->nilai_total;
            $selisihPct = $ringkasan->nilai_total > 0
              ? round(($selisih / $ringkasan->nilai_total) * 100, 1)
              : 0;
          @endphp
          <div class="mb-3 p-3" style="border-radius:8px;border:1px solid;
            {{ $selisih >= 0 ? 'background:#f0fdf4;border-color:#bbf7d0' : 'background:#fef2f2;border-color:#fecaca' }}">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;
              {{ $selisih >= 0 ? 'color:#15803d' : 'color:#dc2626' }}">
              Selisih Harga Jual vs Nilai Internal
            </div>
            <div style="font-size:20px;font-weight:800;margin-top:4px;
              {{ $selisih >= 0 ? 'color:#16a34a' : 'color:#dc2626' }}">
              {{ $selisih >= 0 ? '+' : '' }}Rp {{ number_format(abs($selisih), 0, ',', '.') }}
              <span style="font-size:13px;font-weight:600">({{ $selisih >= 0 ? '+' : '' }}{{ $selisihPct }}%)</span>
            </div>
          </div>

          {{-- Breakdown kategori --}}
          @if($ringkasan->kategori_breakdown->count() > 0)
          <div style="font-size:12px;font-weight:700;color:#374151;margin-bottom:8px;text-transform:uppercase;letter-spacing:.5px">
            Breakdown per Kategori
          </div>
          <div class="table-responsive">
            <table class="table table-sm mb-0" style="font-size:12px">
              <thead>
                <tr>
                  <th style="background:#f8fafc">Kategori</th>
                  <th style="background:#f8fafc;text-align:right">Total Berat</th>
                  <th style="background:#f8fafc;text-align:right">Nilai (Internal)</th>
                </tr>
              </thead>
              <tbody>
                @foreach($ringkasan->kategori_breakdown as $k)
                <tr>
                  <td>{{ $k->nama_kategori }}</td>
                  <td style="text-align:right;font-weight:600">{{ number_format($k->total_berat, 2) }} kg</td>
                  <td style="text-align:right;color:#16a34a;font-weight:600">
                    Rp {{ number_format($k->total_nilai, 0, ',', '.') }}
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          @endif

        @else
          <div class="text-center py-4" style="color:#94a3b8">
            <i class="bi bi-inbox" style="font-size:32px;display:block;margin-bottom:8px"></i>
            <div style="font-size:13px">Tidak ada setoran nasabah tercatat pada tanggal ini.</div>
          </div>
        @endif
      </div>
    </div>
  </div>

</div>
@endsection
