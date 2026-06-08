@extends('layouts.admin')
@section('title','Penilaian Nasabah Terbaik')
@section('page-title','Penilaian Nasabah Terbaik')

@section('content')

{{-- ═══════════════════════════════════════════════════════════════
     FORM HITUNG PENILAIAN
═══════════════════════════════════════════════════════════════ --}}
<div class="table-card mb-4">
  <div class="card-header">
    <h6>🏆 Hitung Penilaian Nasabah Terbaik – Metode SAW</h6>
  </div>
  <div class="p-4">
    <form method="POST" action="{{ route('admin.penilaian.hitung') }}" class="row g-3 align-items-end">
      @csrf

      {{-- Pilih Semester --}}
      <div class="col-auto">
        <label class="form-label fw-semibold" style="font-size:13px">Semester</label>
        <select name="semester" class="form-select" style="min-width:200px">
          <option value="1" {{ $semester == 1 ? 'selected' : '' }}>
            Semester I &nbsp;(Januari – Juni)
          </option>
          <option value="2" {{ $semester == 2 ? 'selected' : '' }}>
            Semester II (Juli – Desember)
          </option>
        </select>
      </div>

      {{-- Pilih Tahun --}}
      <div class="col-auto">
        <label class="form-label fw-semibold" style="font-size:13px">Tahun</label>
        <input type="number" name="tahun" class="form-control"
               value="{{ $tahun }}" min="2020" max="2099" style="width:110px">
      </div>

      {{-- Tombol Hitung --}}
      <div class="col-auto">
        <button type="submit" class="btn px-4"
                style="background:#16a34a;color:#fff;border-radius:9px;font-size:14px"
                onclick="return confirm('Hitung ulang penilaian SAW untuk semester ini?\nData penilaian sebelumnya akan ditimpa.')">
          <i class="bi bi-calculator me-1"></i>Hitung Sekarang
        </button>
      </div>

      {{-- Tombol Lihat --}}
      <div class="col-auto">
        <a href="{{ route('admin.penilaian.index', ['semester'=>$semester,'tahun'=>$tahun]) }}"
           class="btn px-3" style="background:#f1f5f9;border-radius:9px;font-size:14px">
          <i class="bi bi-eye me-1"></i>Lihat Hasil
        </a>
      </div>
    </form>

    {{-- Info rumus SAW --}}
    <div class="mt-3 p-3" style="background:#fef9c3;border-radius:8px;font-size:12px;color:#854d0e">
      <i class="bi bi-info-circle me-1"></i>
      <strong>Metode SAW</strong> &mdash; 3 kriteria benefit, dijalankan per semester (6 bulan sekali):<br>
      <div class="mt-1 d-flex flex-wrap gap-3">
        <span>📊 <strong>C1 Konsistensi Setoran</strong> (50%) – rata-rata transaksi/bulan</span>
        <span>⚖️ <strong>C2 Total Berat</strong> (30%) – total kg sampah disetor</span>
        <span>🗂️ <strong>C3 Keragaman Kategori</strong> (20%) – jumlah jenis sampah unik</span>
      </div>
      <div class="mt-1">
        Normalisasi: r<sub>ij</sub> = x<sub>ij</sub> / max(x<sub>j</sub>).
        &nbsp;|&nbsp; Skor SAW = Σ(bobot × nilai normalisasi).
      </div>
    </div>
  </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════
     HASIL PENILAIAN
═══════════════════════════════════════════════════════════════ --}}
<div class="table-card">
  <div class="card-header d-flex align-items-center justify-content-between">
    <h6 class="mb-0">
      Hasil Penilaian SAW —
      {{ $semester == 1 ? 'Semester I (Jan–Jun)' : 'Semester II (Jul–Des)' }}
      {{ $tahun }}
    </h6>
    @if($hasil->count() > 0)
      <span class="badge" style="background:#dcfce7;color:#15803d;font-size:12px">
        {{ $hasil->count() }} nasabah dinilai
      </span>
    @endif
  </div>

  <div class="table-responsive">
    <table class="table mb-0" style="font-size:13px">
      <thead>
        <tr>
          <th style="width:60px">Rank</th>
          <th>Nasabah</th>
          {{-- Nilai mentah --}}
          <th class="text-center" title="Konsistensi: rata-rata transaksi per bulan">
            C1 Konsistensi<br><small class="text-muted fw-normal">(trx/bln)</small>
          </th>
          <th class="text-center" title="Total berat sampah selama semester">
            C2 Total Berat<br><small class="text-muted fw-normal">(kg)</small>
          </th>
          <th class="text-center" title="Jumlah kategori sampah unik yang disetorkan">
            C3 Keragaman<br><small class="text-muted fw-normal">(jenis)</small>
          </th>
          {{-- Skor SAW --}}
          <th class="text-center">Skor SAW</th>
        </tr>
      </thead>
      <tbody>
        @forelse($hasil as $i => $h)
        <tr @if($i < 3) style="background: {{ $i === 0 ? '#fffbeb' : ($i === 1 ? '#f8fafc' : '#fff8f2') }}" @endif>

          {{-- Rank --}}
          <td class="text-center">
            @if($i === 0) <span style="font-size:22px;line-height:1">🥇</span>
            @elseif($i === 1) <span style="font-size:22px;line-height:1">🥈</span>
            @elseif($i === 2) <span style="font-size:22px;line-height:1">🥉</span>
            @else <span style="color:#94a3b8;font-weight:600">#{{ $i + 1 }}</span>
            @endif
          </td>

          {{-- Nasabah --}}
          <td>
            <div style="font-weight:600">{{ $h->nasabah->user->name }}</div>
            <div style="font-size:11px;color:#94a3b8">NIK: {{ $h->nasabah->nik }}</div>
          </td>

          {{-- C1: Konsistensi --}}
          <td class="text-center">
            <div style="font-weight:600">{{ number_format($h->konsistensi, 2) }}</div>
            <div style="font-size:11px;color:#94a3b8">
              norm: {{ number_format($h->norm_konsistensi * 100, 1) }}%
            </div>
          </td>

          {{-- C2: Total Berat --}}
          <td class="text-center">
            <div style="font-weight:600">{{ number_format($h->total_berat, 2) }} kg</div>
            <div style="font-size:11px;color:#94a3b8">
              norm: {{ number_format($h->norm_total_berat * 100, 1) }}%
            </div>
          </td>

          {{-- C3: Keragaman --}}
          <td class="text-center">
            <div style="font-weight:600">{{ (int) $h->keragaman_kategori }} jenis</div>
            <div style="font-size:11px;color:#94a3b8">
              norm: {{ number_format($h->norm_keragaman * 100, 1) }}%
            </div>
          </td>

          {{-- Skor SAW --}}
          <td class="text-center">
            <div style="display:flex;flex-direction:column;align-items:center;gap:4px">
              <span style="font-weight:700;font-size:15px;color:#1e293b">
                {{ number_format($h->skor * 100, 2) }}
              </span>
              {{-- Progress bar --}}
              <div style="background:#e2e8f0;border-radius:20px;height:6px;width:70px;overflow:hidden">
                <div style="background:{{ $i === 0 ? '#f59e0b' : ($i === 1 ? '#64748b' : ($i === 2 ? '#b45309' : '#16a34a')) }};
                            height:100%;width:{{ min($h->skor * 100, 100) }}%;border-radius:20px;
                            transition:width .4s ease"></div>
              </div>
              <span style="font-size:10px;color:#94a3b8">dari 100</span>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6" class="text-center text-muted py-5">
            <div style="font-size:32px;margin-bottom:8px">🏆</div>
            Belum ada penilaian untuk periode ini.<br>
            <small>Klik <strong>"Hitung Sekarang"</strong> di atas untuk menjalankan perhitungan SAW.</small>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

{{-- Keterangan bobot SAW --}}
@if($hasil->count() > 0)
<div class="mt-3 p-3" style="background:#f8fafc;border-radius:8px;font-size:12px;color:#64748b;border:1px solid #e2e8f0">
  <strong>Keterangan perhitungan SAW:</strong>
  Setiap nilai kriteria dinormalisasi terhadap nilai maksimum pada periode ini (skala 0–1).
  Skor akhir = (C1_norm × 0.50) + (C2_norm × 0.30) + (C3_norm × 0.20),
  ditampilkan dalam skala 0–100.
</div>
@endif

@endsection