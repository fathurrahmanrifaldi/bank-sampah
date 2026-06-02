@extends('layouts.nasabah')
@section('title','Riwayat Transaksi')

@section('content')

<div class="table-card">
  <div style="padding:14px 20px;border-bottom:1px solid #e2e8f0">
    <span style="font-size:13px;color:#64748b">
      Total {{ $transaksi->total() }} transaksi terdaftar
    </span>
  </div>
  <div class="table-responsive">
    <table class="table mb-0">
      <thead>
        <tr>
          <th>#</th><th>Tanggal</th><th>Detail Sampah</th>
          <th>Total Berat</th><th>Nilai</th>
        </tr>
      </thead>
      <tbody>
        @forelse($transaksi as $i => $t)
        <tr>
          <td style="color:#94a3b8;font-size:12px">{{ $t->id }}</td>
          <td style="font-size:13px">{{ $t->tanggal->format('d M Y') }}</td>
          <td>
            @foreach($t->detail as $d)
              <div style="font-size:12px;color:#374151">
                {{ $d->kategori->nama_kategori }}
                <span style="color:#94a3b8">{{ number_format($d->berat_kg, 3) }} kg</span>
                = <span style="color:#16a34a">Rp {{ number_format($d->nilai, 0, ',', '.') }}</span>
              </div>
            @endforeach
          </td>
          <td style="font-size:13px">
            {{ number_format($t->detail->sum('berat_kg'), 2) }} kg
          </td>
          <td style="font-weight:700;color:#16a34a;font-size:13px">
            Rp {{ number_format($t->total_nilai, 0, ',', '.') }}
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="5" class="text-center text-muted py-4">
            Belum ada riwayat transaksi.
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($transaksi->hasPages())
    <div class="d-flex justify-content-end p-3">{{ $transaksi->links() }}</div>
  @endif
</div>
@endsection