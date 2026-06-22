@extends('layouts.nasabah')
@section('title','Dashboard Saya')

@section('content')
<!-- Saldo card -->
<div class="saldo-card mb-4">
  <div style="font-size:13px;opacity:.8;margin-bottom:6px">
    <i class="bi bi-person-circle me-1"></i>
    {{ $nasabah->user->name }} · {{ $nasabah->nik }}
  </div>
  <div style="font-size:13px;opacity:.7;margin-bottom:4px">Saldo Tabungan Sampah</div>
  <div style="font-size:36px;font-weight:800;letter-spacing:-1px">
    Rp {{ number_format($nasabah->saldo, 0, ',', '.') }}
  </div>
  <div style="font-size:11px;opacity:.6;margin-top:8px">
    Terakhir diperbarui: {{ now()->format('d M Y, H:i') }}
  </div>
  @php
    $penarikanMenunggu = $nasabah->penarikanDana()->where('status','menunggu')->first();
  @endphp
  <div class="mt-3 d-flex gap-2 flex-wrap">
    @if($penarikanMenunggu)
      <a href="{{ route('nasabah.penarikan.index') }}"
         style="background:rgba(255,255,255,.2);color:#fff;border-radius:8px;font-size:12px;padding:7px 14px;text-decoration:none;border:1px solid rgba(255,255,255,.3)">
        <i class="bi bi-hourglass-split me-1"></i>
        Menunggu: Rp {{ number_format($penarikanMenunggu->jumlah, 0, ',', '.') }}
      </a>
    @else
      <a href="{{ route('nasabah.penarikan.create') }}"
         style="background:rgba(255,255,255,.2);color:#fff;border-radius:8px;font-size:12px;padding:7px 14px;text-decoration:none;border:1px solid rgba(255,255,255,.3)">
        <i class="bi bi-cash-stack me-1"></i>Ajukan Penarikan
      </a>
    @endif
    <a href="{{ route('nasabah.penarikan.index') }}"
       style="background:transparent;color:rgba(255,255,255,.75);border-radius:8px;font-size:12px;padding:7px 14px;text-decoration:none">
      Riwayat Penarikan →
    </a>
  </div>
</div>

<!-- Stat cards -->
<div class="row g-3 mb-4">
  <div class="col-sm-4">
    <div class="stat-card text-center">
      <div style="font-size:28px">🔄</div>
      <div style="font-size:22px;font-weight:700;color:#0f172a">{{ $totalTransaksi }}</div>
      <div style="font-size:12px;color:#64748b">Total Setoran</div>
    </div>
  </div>
  <div class="col-sm-4">
    <div class="stat-card text-center">
      <div style="font-size:28px">⚖️</div>
      <div style="font-size:22px;font-weight:700;color:#0f172a">{{ number_format($totalBerat, 1) }} kg</div>
      <div style="font-size:12px;color:#64748b">Total Berat Sampah</div>
    </div>
  </div>
  <div class="col-sm-4">
    <div class="stat-card text-center">
      <div style="font-size:28px">🏆</div>
      <div style="font-size:22px;font-weight:700;color:#0f172a">{{ $ranking ? '#'.$ranking : '-' }}</div>
      <div style="font-size:12px;color:#64748b">Ranking Bulan Ini</div>
    </div>
  </div>
</div>

<!-- 5 transaksi terbaru -->
<div class="table-card">
  <div style="padding:14px 20px;border-bottom:1px solid #e2e8f0;display:flex;justify-content:space-between;align-items:center">
    <span style="font-size:14px;font-weight:600">Riwayat Setoran Terakhir</span>
    <a href="{{ route('nasabah.riwayat') }}" style="font-size:12px;color:#16a34a">Lihat semua →</a>
  </div>
  <div class="table-responsive">
    <table class="table mb-0">
      <thead>
        <tr><th>Tanggal</th><th>Jenis Sampah</th><th>Nilai</th></tr>
      </thead>
      <tbody>
        @forelse($transaksiTerbaru as $t)
        <tr>
          <td style="font-size:13px">{{ $t->tanggal->format('d M Y') }}</td>
          <td>
            @foreach($t->detail as $d)
              <span class="badge me-1"
                    style="background:#dcfce7;color:#15803d;font-size:11px">
                {{ $d->kategori->nama_kategori }} {{ number_format($d->berat_kg,2) }}kg
              </span>
            @endforeach
          </td>
          <td style="font-weight:700;color:#16a34a;font-size:13px">
            Rp {{ number_format($t->total_nilai, 0, ',', '.') }}
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="3" class="text-center text-muted py-3">Belum ada setoran.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection