@extends('layouts.admin')
@section('title','Detail Transaksi')
@section('page-title','Detail Transaksi #{{ $transaksi->id }}')

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-8">
    <!-- Header info -->
    <div class="table-card mb-3">
      <div class="card-header">
        <h6>Transaksi #{{ $transaksi->id }}</h6>
        <a href="{{ route('admin.transaksi.index') }}" class="btn btn-sm"
           style="background:#f1f5f9;font-size:12px">
          <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
      </div>
      <div class="p-4">
        <div class="row g-3">
          <div class="col-md-6">
            <div style="font-size:12px;color:#64748b;margin-bottom:4px">Nasabah</div>
            <div style="font-weight:700;font-size:15px">{{ $transaksi->nasabah->user->name }}</div>
            <div style="font-size:12px;color:#94a3b8">{{ $transaksi->nasabah->no_rekening }}</div>
          </div>
          <div class="col-md-3">
            <div style="font-size:12px;color:#64748b;margin-bottom:4px">Tanggal Setoran</div>
            <div style="font-weight:600">{{ $transaksi->tanggal->format('d M Y') }}</div>
          </div>
          <div class="col-md-3">
            <div style="font-size:12px;color:#64748b;margin-bottom:4px">Dicatat oleh</div>
            <div style="font-weight:600">{{ $transaksi->admin->name }}</div>
          </div>
        </div>

        @if($transaksi->catatan)
          <div class="mt-3 p-3" style="background:#f8fafc;border-radius:8px;font-size:13px;color:#64748b">
            <i class="bi bi-chat-text me-1"></i>{{ $transaksi->catatan }}
          </div>
        @endif
      </div>
    </div>

    <!-- Detail sampah -->
    <div class="table-card mb-3">
      <div class="card-header"><h6>Rincian Sampah Disetorkan</h6></div>
      <div class="table-responsive">
        <table class="table mb-0">
          <thead>
            <tr>
              <th>#</th><th>Kategori</th><th>Jenis</th>
              <th>Berat (kg)</th><th>Harga/kg</th><th>Nilai</th>
            </tr>
          </thead>
          <tbody>
            @foreach($transaksi->detail as $i => $d)
            <tr>
              <td style="color:#94a3b8;font-size:12px">{{ $i+1 }}</td>
              <td style="font-weight:600;font-size:13px">{{ $d->kategori->nama_kategori }}</td>
              <td>
                <span class="badge" style="background:#f1f5f9;color:#374151;font-size:11px">
                  {{ str_replace('_',' ', ucfirst($d->kategori->jenis)) }}
                </span>
              </td>
              <td style="font-size:13px">{{ number_format($d->berat_kg, 3) }} kg</td>
              <td style="font-size:13px">Rp {{ number_format($d->kategori->harga_per_kg, 0, ',', '.') }}</td>
              <td style="font-weight:700;color:#16a34a">
                Rp {{ number_format($d->nilai, 0, ',', '.') }}
              </td>
            </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr style="background:#f0fdf4">
              <td colspan="5" class="text-end fw-bold" style="font-size:14px">
                TOTAL NILAI SETORAN
              </td>
              <td style="font-size:16px;font-weight:800;color:#15803d">
                Rp {{ number_format($transaksi->total_nilai, 0, ',', '.') }}
              </td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>

    <!-- Saldo setelah transaksi -->
    <div class="alert" style="background:#f0fdf4;border:1px solid #86efac">
      <div class="d-flex align-items-center gap-3">
        <div style="font-size:28px">💰</div>
        <div>
          <div style="font-size:12px;color:#15803d">Saldo nasabah setelah transaksi ini</div>
          <div style="font-size:20px;font-weight:800;color:#15803d">
            Rp {{ number_format($transaksi->nasabah->saldo, 0, ',', '.') }}
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection