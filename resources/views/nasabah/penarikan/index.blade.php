@extends('layouts.nasabah')
@section('title','Penarikan Dana')

@section('content')

{{-- Header + Tombol Ajukan --}}
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
  <div>
    <h5 style="font-weight:700;color:#0f172a;margin:0">Penarikan Dana</h5>
    <p style="font-size:12px;color:#64748b;margin:2px 0 0">Riwayat pengajuan pencairan saldo tabungan sampah Anda</p>
  </div>
  @if(!$adaMenunggu)
    <a href="{{ route('nasabah.penarikan.create') }}"
       class="btn"
       style="background:linear-gradient(135deg,#16a34a,#15803d);color:#fff;border-radius:10px;font-size:13px;padding:9px 18px;border:none">
      <i class="bi bi-plus-lg me-1"></i> Ajukan Penarikan
    </a>
  @else
    <span class="btn btn-sm disabled"
          style="background:#f1f5f9;color:#94a3b8;border-radius:10px;font-size:12px;cursor:not-allowed"
          title="Selesaikan pengajuan yang masih menunggu terlebih dahulu">
      <i class="bi bi-hourglass-split me-1"></i> Ada pengajuan menunggu
    </span>
  @endif
</div>

{{-- Banner Info saldo --}}
<div class="saldo-card mb-4" style="padding:20px 24px">
  <div style="font-size:12px;opacity:.8;margin-bottom:4px">Saldo Tabungan Saat Ini</div>
  <div style="font-size:28px;font-weight:800">Rp {{ number_format($nasabah->saldo, 0, ',', '.') }}</div>
  <div style="font-size:11px;opacity:.65;margin-top:4px">NIK: {{ $nasabah->nik }}</div>
</div>

{{-- Tabel Riwayat --}}
<div class="table-card">
  <div style="padding:14px 20px;border-bottom:1px solid #e2e8f0">
    <span style="font-size:13px;color:#64748b">Total {{ $penarikan->total() }} pengajuan</span>
  </div>
  <div class="table-responsive">
    <table class="table mb-0">
      <thead>
        <tr>
          <th>#</th>
          <th>Tgl Ajuan</th>
          <th>Jenis</th>
          <th>Nominal</th>
          <th>Tgl Diminta</th>
          <th>Tgl Cair</th>
          <th>Status</th>
          <th>Catatan</th>
        </tr>
      </thead>
      <tbody>
        @forelse($penarikan as $p)
        <tr>
          <td style="color:#94a3b8;font-size:12px">{{ $p->id }}</td>
          <td style="font-size:12px;color:#64748b">{{ $p->created_at->format('d M Y') }}</td>
          <td>
            @if($p->jenis === 'segera')
              <span class="badge" style="background:#dbeafe;color:#1d4ed8;font-size:11px">
                <i class="bi bi-lightning-charge me-1"></i>Sekarang
              </span>
            @else
              <span class="badge" style="background:#ede9fe;color:#6d28d9;font-size:11px">
                <i class="bi bi-calendar-event me-1"></i>Terjadwal
              </span>
            @endif
          </td>
          <td style="font-weight:700;color:#16a34a;font-size:13px">
            Rp {{ number_format($p->jumlah, 0, ',', '.') }}
          </td>
          <td style="font-size:12px">{{ $p->tanggal_diminta->format('d M Y') }}</td>
          <td style="font-size:12px">
            @if($p->tanggal_pencairan)
              <span style="color:#16a34a;font-weight:600">{{ $p->tanggal_pencairan->format('d M Y') }}</span>
            @else
              <span style="color:#94a3b8">-</span>
            @endif
          </td>
          <td>
            @php
              $badge = [
                'menunggu'  => ['bg'=>'#fef9c3','text'=>'#92400e','icon'=>'hourglass-split'],
                'disetujui' => ['bg'=>'#dcfce7','text'=>'#166534','icon'=>'check-circle'],
                'ditolak'   => ['bg'=>'#fee2e2','text'=>'#991b1b','icon'=>'x-circle'],
              ];
              $b = $badge[$p->status] ?? ['bg'=>'#f1f5f9','text'=>'#374151','icon'=>'dash'];
            @endphp
            <span class="badge" style="background:{{ $b['bg'] }};color:{{ $b['text'] }};font-size:11px">
              <i class="bi bi-{{ $b['icon'] }} me-1"></i>{{ $p->statusLabel() }}
            </span>
          </td>
          <td style="font-size:11px;max-width:160px">
            @if($p->status === 'ditolak' && $p->catatan_admin)
              <span style="color:#dc2626"><i class="bi bi-info-circle me-1"></i>{{ $p->catatan_admin }}</span>
            @elseif($p->catatan_nasabah)
              <span style="color:#64748b">{{ $p->catatan_nasabah }}</span>
            @else
              <span style="color:#cbd5e1">-</span>
            @endif
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="8" class="text-center text-muted py-5">
            <i class="bi bi-inbox" style="font-size:32px;display:block;margin-bottom:10px;color:#cbd5e1"></i>
            Belum ada pengajuan penarikan dana.<br>
            <a href="{{ route('nasabah.penarikan.create') }}"
               style="font-size:13px;color:#16a34a;text-decoration:none;margin-top:6px;display:inline-block">
              Ajukan sekarang →
            </a>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($penarikan->hasPages())
    <div class="d-flex justify-content-end p-3">{{ $penarikan->links() }}</div>
  @endif
</div>

@endsection
