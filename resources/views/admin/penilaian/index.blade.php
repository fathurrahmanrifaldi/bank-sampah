@extends('layouts.admin')
@section('title','Penilaian Nasabah Terbaik')
@section('page-title','Penilaian Nasabah Terbaik')

@section('content')
<!-- Form hitung penilaian -->
<div class="table-card mb-4">
  <div class="card-header"><h6>🏆 Hitung Penilaian Periode</h6></div>
  <div class="p-4">
    <form method="POST" action="{{ route('admin.penilaian.hitung') }}" class="row g-3 align-items-end">
      @csrf
      <div class="col-auto">
        <label class="form-label fw-semibold" style="font-size:13px">Bulan</label>
        <select name="bulan" class="form-select">
          @for($i=1;$i<=12;$i++)
            <option value="{{ $i }}" {{ $bulan==$i ? 'selected' : '' }}>
              {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
            </option>
          @endfor
        </select>
      </div>
      <div class="col-auto">
        <label class="form-label fw-semibold" style="font-size:13px">Tahun</label>
        <input type="number" name="tahun" class="form-control"
               value="{{ $tahun }}" min="2020" max="2099" style="width:110px">
      </div>
      <div class="col-auto">
        <button type="submit" class="btn px-4"
                style="background:#16a34a;color:#fff;border-radius:9px;font-size:14px"
                onclick="return confirm('Hitung ulang penilaian untuk periode ini?')">
          <i class="bi bi-calculator me-1"></i>Hitung Sekarang
        </button>
      </div>
      <div class="col-auto">
        <a href="{{ route('admin.penilaian.index', ['bulan'=>$bulan,'tahun'=>$tahun]) }}"
           class="btn px-3" style="background:#f1f5f9;border-radius:9px;font-size:14px">
          <i class="bi bi-eye me-1"></i>Lihat Hasil
        </a>
      </div>
    </form>

    <div class="mt-3 p-3" style="background:#fef9c3;border-radius:8px;font-size:12px;color:#854d0e">
      <i class="bi bi-info-circle me-1"></i>
      <strong>Rumus skor:</strong> Berat (50%) + Frekuensi setor (30%) + Nilai ekonomis (20%).
      Setiap kriteria dinormalisasi min-max (0–100). Predikat: Emas ≥80, Perak ≥60, Perunggu &lt;60.
    </div>
  </div>
</div>

<!-- Hasil penilaian -->
<div class="table-card">
  <div class="card-header">
    <h6>Hasil Penilaian —
      {{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }} {{ $tahun }}
    </h6>
  </div>
  <div class="table-responsive">
    <table class="table mb-0">
      <thead>
        <tr>
          <th>Peringkat</th><th>Nasabah</th>
          <th>Total Berat</th><th>Jml Setor</th>
          <th>Total Nilai</th><th>Skor</th><th>Predikat</th>
        </tr>
      </thead>
      <tbody>
        @forelse($hasil as $i => $h)
        <tr>
          <td>
            @if($i === 0) <span style="font-size:20px">🥇</span>
            @elseif($i === 1) <span style="font-size:20px">🥈</span>
            @elseif($i === 2) <span style="font-size:20px">🥉</span>
            @else <span style="font-size:13px;color:#94a3b8">{{ $i+1 }}</span>
            @endif
          </td>
          <td>
            <div style="font-weight:600;font-size:13px">{{ $h->nasabah->user->name }}</div>
            <div style="font-size:11px;color:#94a3b8">{{ $h->nasabah->nik }}</div>
          </td>
          <td style="font-size:13px">{{ number_format($h->total_berat, 2) }} kg</td>
          <td>
            <span class="badge" style="background:#dbeafe;color:#1d4ed8;font-size:12px">
              {{ $h->jumlah_setor }}× setor
            </span>
          </td>
          <td style="font-size:13px;color:#16a34a;font-weight:600">
            Rp {{ number_format($h->total_nilai, 0, ',', '.') }}
          </td>
          <td>
            <div style="display:flex;align-items:center;gap:8px">
              <div style="background:#e2e8f0;border-radius:20px;height:8px;width:80px;overflow:hidden">
                <div style="background:#16a34a;height:100%;width:{{ $h->skor }}%;border-radius:20px"></div>
              </div>
              <span style="font-weight:700;font-size:13px">{{ $h->skor }}</span>
            </div>
          </td>
          <td>
            @if($h->predikat === 'Emas')
              <span class="badge badge-emas px-3 py-2">🥇 Emas</span>
            @elseif($h->predikat === 'Perak')
              <span class="badge badge-perak px-3 py-2">🥈 Perak</span>
            @else
              <span class="badge badge-perunggu px-3 py-2">🥉 Perunggu</span>
            @endif
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7" class="text-center text-muted py-4">
            Belum ada penilaian untuk periode ini. Klik "Hitung Sekarang" di atas.
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection