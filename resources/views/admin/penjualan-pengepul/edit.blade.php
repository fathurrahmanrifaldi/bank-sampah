@extends('layouts.admin')
@section('title','Edit Penjualan ke Pengepul')
@section('page-title','Edit Penjualan ke Pengepul')

@section('content')
<div class="mb-3">
  <a href="{{ route('admin.penjualan-pengepul.index') }}"
     style="font-size:13px;color:#64748b;text-decoration:none">
    <i class="bi bi-arrow-left me-1"></i>Kembali ke Daftar Penjualan
  </a>
</div>

<div class="row g-4">

  {{-- ── Kolom Kiri: Form Edit ── --}}
  <div class="col-lg-5">
    <div class="table-card">
      <div class="card-header">
        <h6><i class="bi bi-pencil-square me-2" style="color:#ca8a04"></i>Edit Penjualan #{{ $penjualanPengepul->id }}</h6>
      </div>
      <div style="padding:24px">
        <form method="POST"
              action="{{ route('admin.penjualan-pengepul.update', $penjualanPengepul) }}"
              id="formEdit">
          @csrf
          @method('PUT')

          {{-- Tanggal Jual --}}
          <div class="mb-4">
            <label class="form-label" style="font-size:13px;font-weight:600;color:#374151">
              Tanggal Jual <span class="text-danger">*</span>
            </label>
            <input type="date" name="tanggal_jual" id="tanggal_jual"
                   class="form-control @error('tanggal_jual') is-invalid @enderror"
                   value="{{ old('tanggal_jual', $penjualanPengepul->tanggal_jual->toDateString()) }}"
                   max="{{ today()->toDateString() }}"
                   style="border-radius:8px;font-size:13px"
                   required>
            @error('tanggal_jual')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="form-text" style="font-size:11px;color:#94a3b8;margin-top:4px">
              Ubah tanggal untuk melihat ringkasan setoran nasabah pada hari tersebut.
            </div>
          </div>

          {{-- Total Uang dari Pengepul --}}
          <div class="mb-4">
            <label class="form-label" style="font-size:13px;font-weight:600;color:#374151">
              Total Uang dari Pengepul <span class="text-danger">*</span>
            </label>
            <div class="input-group">
              <span class="input-group-text" style="background:#f8fafc;border-color:#e2e8f0;font-size:13px;color:#64748b">Rp</span>
              <input type="number" name="total_uang" id="total_uang"
                     class="form-control @error('total_uang') is-invalid @enderror"
                     value="{{ old('total_uang', (int) $penjualanPengepul->total_uang) }}"
                     min="1" step="1" placeholder="0"
                     style="border-radius:0 8px 8px 0;font-size:14px;font-weight:600"
                     required>
              @error('total_uang')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div id="previewNominal" style="font-size:12px;color:#16a34a;margin-top:6px;font-weight:600;min-height:18px">
              = Rp {{ number_format($penjualanPengepul->total_uang, 0, ',', '.') }}
            </div>
          </div>

          {{-- Catatan --}}
          <div class="mb-4">
            <label class="form-label" style="font-size:13px;font-weight:600;color:#374151">
              Catatan <span class="text-muted" style="font-weight:400">(opsional)</span>
            </label>
            <textarea name="catatan" rows="3"
                      class="form-control @error('catatan') is-invalid @enderror"
                      placeholder="Misal: pembayaran tunai, cek, nama pengepul, dll."
                      style="border-radius:8px;font-size:13px;resize:none">{{ old('catatan', $penjualanPengepul->catatan) }}</textarea>
            @error('catatan')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- Info Admin (dicatat & diedit oleh) --}}
          <div class="mb-4 p-3" style="background:#f8fafc;border-radius:8px;border:1px solid #e2e8f0">
            <div style="font-size:11px;color:#94a3b8;margin-bottom:6px">Info Catatan</div>
            <div style="font-size:12px;color:#64748b">
              <i class="bi bi-person-circle me-1"></i>
              Dicatat oleh: <strong>{{ $penjualanPengepul->admin->name }}</strong>
            </div>
            <div style="font-size:12px;color:#94a3b8;margin-top:4px">
              <i class="bi bi-clock me-1"></i>
              {{ $penjualanPengepul->created_at->translatedFormat('d F Y, H:i') }}
            </div>
          </div>

          {{-- Actions --}}
          <div class="d-flex gap-2">
            <button type="submit" class="btn flex-fill"
                    style="background:#ca8a04;color:#fff;border-radius:8px;font-size:13px;font-weight:600;padding:10px">
              <i class="bi bi-check-lg me-1"></i>Simpan Perubahan
            </button>
            <a href="{{ route('admin.penjualan-pengepul.index') }}"
               class="btn btn-sm" style="background:#f1f5f9;color:#475569;border-radius:8px;padding:10px 16px;font-size:13px">
              Batal
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>

  {{-- ── Kolom Kanan: Ringkasan Setoran ── --}}
  <div class="col-lg-7">
    <div class="table-card">
      <div class="card-header">
        <h6><i class="bi bi-info-circle me-2" style="color:#0ea5e9"></i>Ringkasan Setoran Nasabah</h6>
        <span id="labelTanggal" style="font-size:12px;color:#64748b;background:#f1f5f9;padding:3px 10px;border-radius:6px">
          {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}
        </span>
      </div>
      <div style="padding:20px">
        @if($ringkasan->jumlah_nasabah > 0)
          <div class="row g-3 mb-3">
            <div class="col-6">
              <div style="background:#f0fdf4;border-radius:10px;padding:14px 16px;border:1px solid #bbf7d0">
                <div style="font-size:11px;color:#15803d;font-weight:600;text-transform:uppercase;letter-spacing:.5px">
                  Nasabah Setor Hari Ini
                </div>
                <div style="font-size:24px;font-weight:700;color:#0f172a;margin-top:2px">
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

          <div class="mt-3 p-3" style="background:#fefce8;border-radius:8px;border:1px solid #fde68a">
            <i class="bi bi-lightbulb me-1" style="color:#ca8a04"></i>
            <span style="font-size:12px;color:#92400e">
              Nilai di atas adalah <strong>harga internal</strong> nasabah.
              Masukkan nominal yang disepakati dengan pengepul.
            </span>
          </div>
        @else
          <div class="text-center py-5" style="color:#94a3b8">
            <i class="bi bi-inbox" style="font-size:36px;display:block;margin-bottom:8px"></i>
            <div style="font-size:13px">Tidak ada setoran nasabah pada tanggal ini.</div>
          </div>
        @endif
      </div>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script>
// Preview nominal
document.getElementById('total_uang').addEventListener('input', function () {
  const val = parseInt(this.value);
  const el  = document.getElementById('previewNominal');
  el.textContent = val > 0 ? '= Rp ' + val.toLocaleString('id-ID') : '';
});

// Auto-reload ringkasan saat tanggal berubah
document.getElementById('tanggal_jual').addEventListener('change', function () {
  const url = new URL(window.location.href);
  url.searchParams.set('tanggal_reload', this.value);

  // Kirim request AJAX untuk update ringkasan tanpa reload full page
  // Fallback: redirect ke edit URL dengan param tanggal baru
  const editUrl = new URL(window.location.href);
  editUrl.searchParams.set('tanggal_override', this.value);
  // Simpan nilai nominal agar tidak hilang setelah reload
  const nominal  = document.getElementById('total_uang').value;
  const catatan  = document.querySelector('[name="catatan"]').value;
  sessionStorage.setItem('edit_nominal', nominal);
  sessionStorage.setItem('edit_catatan', catatan);
  window.location.search = '?tanggal_override=' + this.value;
});

// Restore nilai dari sessionStorage jika ada
window.addEventListener('DOMContentLoaded', function () {
  const nominal = sessionStorage.getItem('edit_nominal');
  const catatan = sessionStorage.getItem('edit_catatan');
  if (nominal) {
    document.getElementById('total_uang').value = nominal;
    const ev = document.getElementById('previewNominal');
    ev.textContent = '= Rp ' + parseInt(nominal).toLocaleString('id-ID');
    sessionStorage.removeItem('edit_nominal');
  }
  if (catatan) {
    document.querySelector('[name="catatan"]').value = catatan;
    sessionStorage.removeItem('edit_catatan');
  }
});
</script>
@endpush
