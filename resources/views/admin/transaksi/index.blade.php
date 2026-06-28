@extends('layouts.admin')
@section('title','Transaksi Setoran')
@section('page-title','Transaksi Setoran Sampah')

@section('content')

{{-- Flash messages --}}
@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show mb-3" role="alert" style="font-size:13px">
    <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
    <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
  </div>
@endif
@if(session('import_stats'))
  @php $st = session('import_stats'); @endphp
  <div class="alert alert-info alert-dismissible fade show mb-3" role="alert" style="font-size:13px">
    <strong><i class="bi bi-file-earmark-excel me-1"></i>Hasil Import Excel:</strong>
    <ul class="mb-0 mt-1">
      <li>✅ Transaksi berhasil diimpor: <strong>{{ $st['transaksi_dibuat'] }}</strong></li>
      <li>👤 Nasabah baru dibuat otomatis: <strong>{{ $st['nasabah_baru'] }}</strong></li>
      <li>⏭️ Baris dilewati (kosong/invalid): <strong>{{ $st['baris_dilewati'] }}</strong></li>
      @if(!empty($st['kategori_tidak_cocok']))
        <li>⚠️ Kolom kategori tidak dikenali: <code>{{ implode(', ', $st['kategori_tidak_cocok']) }}</code></li>
      @endif
    </ul>
    <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
  </div>
@endif
@if(session('error'))
  <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert" style="font-size:13px">
    <i class="bi bi-exclamation-triangle me-1"></i>{{ session('error') }}
    <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
  </div>
@endif

<div class="d-flex justify-content-between align-items-center mb-3">
  <p class="text-muted mb-0" style="font-size:13px">Total {{ $transaksi->total() }} transaksi</p>
  <div class="d-flex gap-2">
    {{-- Tombol Import Excel --}}
    <button type="button" class="btn btn-sm" data-bs-toggle="modal" data-bs-target="#modalImport"
            style="background:#dbeafe;color:#1d4ed8;border-radius:8px;font-size:12px">
      <i class="bi bi-file-earmark-excel me-1"></i>Import Excel
    </button>
    <a href="{{ route('admin.transaksi.create') }}" class="btn btn-sm"
       style="background:#16a34a;color:#fff;border-radius:8px">
      <i class="bi bi-plus-lg me-1"></i>Catat Setoran Baru
    </a>
  </div>
</div>

<div class="table-card">
  <div class="card-header">
    <h6>Daftar Transaksi</h6>
    <form class="d-flex gap-2">
      <input type="month" name="periode" value="{{ request('periode') }}"
             class="form-control form-control-sm" style="border-radius:8px;width:160px">
      <button class="btn btn-sm" style="background:#f1f5f9">Filter</button>
    </form>
  </div>
  <div class="table-responsive">
    <table class="table mb-0">
      <thead>
        <tr>
          <th>#</th><th>Nasabah</th><th>Tanggal</th>
          <th>Jml Jenis</th><th>Total Nilai</th><th>Dicatat oleh</th><th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($transaksi as $t)
        <tr>
          <td style="color:#94a3b8;font-size:12px">{{ $t->id }}</td>
          <td>
            <div style="font-weight:600;font-size:13px">{{ $t->nasabah->user->name }}</div>
            <div style="font-size:11px;color:#94a3b8">{{ $t->nasabah->nik }}</div>
          </td>
          <td style="font-size:13px">{{ $t->tanggal->format('d M Y') }}</td>
          <td>
            <span class="badge" style="background:#f1f5f9;color:#374151;font-size:12px">
              {{ $t->detail->count() }} jenis
            </span>
          </td>
          <td style="font-weight:700;color:#16a34a;font-size:13px">
            Rp {{ number_format($t->total_nilai, 0, ',', '.') }}
          </td>
          <td style="font-size:12px;color:#64748b">{{ $t->admin->name }}</td>
          <td>
            <div class="d-flex gap-1">
              <a href="{{ route('admin.transaksi.show', $t) }}"
                 class="btn btn-sm" style="background:#dcfce7;color:#16a34a;border-radius:7px" title="Lihat">
                <i class="bi bi-eye"></i>
              </a>
              <a href="{{ route('admin.transaksi.edit', $t) }}"
                 class="btn btn-sm" style="background:#dbeafe;color:#1d4ed8;border-radius:7px" title="Edit">
                <i class="bi bi-pencil"></i>
              </a>
              <form action="{{ route('admin.transaksi.destroy', $t) }}" method="POST" class="d-inline"
                    onsubmit="return confirm('Hapus transaksi #{{ $t->id }}?\nSaldo nasabah akan disesuaikan.')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm" style="background:#fee2e2;color:#dc2626;border-radius:7px" title="Hapus">
                  <i class="bi bi-trash"></i>
                </button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7" class="text-center text-muted py-4">Belum ada transaksi.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($transaksi->hasPages())
    <div class="d-flex align-items-center justify-content-between px-3 py-2" style="border-top:1px solid #f1f5f9">
      <div style="font-size:12px;color:#94a3b8">
        Menampilkan {{ $transaksi->firstItem() }}–{{ $transaksi->lastItem() }} dari {{ $transaksi->total() }} transaksi
      </div>
      <div class="d-flex gap-1 align-items-center">
        {{-- Prev --}}
        @if($transaksi->onFirstPage())
          <span class="btn btn-sm disabled" style="background:#f1f5f9;color:#cbd5e1;border-radius:7px;font-size:12px">
            <i class="bi bi-chevron-left"></i>
          </span>
        @else
          <a href="{{ $transaksi->previousPageUrl() }}" class="btn btn-sm" style="background:#f1f5f9;color:#374151;border-radius:7px;font-size:12px">
            <i class="bi bi-chevron-left"></i>
          </a>
        @endif

        {{-- Nomor halaman --}}
        @foreach($transaksi->getUrlRange(1, $transaksi->lastPage()) as $page => $url)
          @if($page == $transaksi->currentPage())
            <span class="btn btn-sm" style="background:#16a34a;color:#fff;border-radius:7px;font-size:12px">{{ $page }}</span>
          @else
            <a href="{{ $url }}" class="btn btn-sm" style="background:#f1f5f9;color:#374151;border-radius:7px;font-size:12px">{{ $page }}</a>
          @endif
        @endforeach

        {{-- Next --}}
        @if($transaksi->hasMorePages())
          <a href="{{ $transaksi->nextPageUrl() }}" class="btn btn-sm" style="background:#f1f5f9;color:#374151;border-radius:7px;font-size:12px">
            <i class="bi bi-chevron-right"></i>
          </a>
        @else
          <span class="btn btn-sm disabled" style="background:#f1f5f9;color:#cbd5e1;border-radius:7px;font-size:12px">
            <i class="bi bi-chevron-right"></i>
          </span>
        @endif
      </div>
    </div>
  @endif
</div>

{{-- ══════════════════════════════════════════════════════════
     MODAL: Import Excel
══════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalImport" tabindex="-1" aria-labelledby="modalImportLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content" style="border-radius:14px;border:none">

      {{-- Header --}}
      <div class="modal-header" style="background:linear-gradient(135deg,#1d4ed8,#3b82f6);border-radius:14px 14px 0 0">
        <div class="d-flex align-items-center gap-2">
          <i class="bi bi-file-earmark-excel" style="font-size:22px;color:#fff"></i>
          <div>
            <h5 class="modal-title mb-0" id="modalImportLabel" style="color:#fff;font-size:16px">Import Data Setoran dari Excel</h5>
            <small style="color:#bfdbfe;font-size:11px">Migrasi data historis dari file Excel ke sistem</small>
          </div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      {{-- Body --}}
      <div class="modal-body p-4">

        {{-- Info format --}}
        <div class="mb-4 p-3" style="background:#f0f9ff;border:1px solid #bae6fd;border-radius:10px">
          <div class="d-flex align-items-start gap-2">
            <i class="bi bi-info-circle" style="color:#0284c7;font-size:16px;margin-top:2px"></i>
            <div>
              <div style="font-size:13px;font-weight:600;color:#0c4a6e;margin-bottom:6px">Format File Excel yang Diterima</div>
              <ul style="font-size:12px;color:#075985;margin:0;padding-left:18px;line-height:1.8">
                <li>File berformat <strong>.xlsx</strong> atau <strong>.xls</strong></li>
                <li>Setiap sheet diberi nama bulan: <strong>JANUARI, FEBRUARI, ..., JUNI</strong> (dst.)</li>
                <li>Baris pertama adalah header/judul kolom</li>
                <li>Struktur kolom: <code>Tanggal | No | Nama Nasabah | [Besi] | [Botol Plastik] | [dst...]</code></li>
                <li>Nama kolom kategori sampah akan dicocokkan secara fleksibel dengan data kategori di sistem</li>
                <li>Nasabah yang belum terdaftar akan <strong>dibuat akun secara otomatis</strong></li>
              </ul>
            </div>
          </div>
        </div>

        {{-- Contoh tabel format --}}
        <div class="mb-4">
          <div style="font-size:12px;font-weight:600;color:#64748b;margin-bottom:6px;text-transform:uppercase;letter-spacing:.5px">
            Contoh Format Excel
          </div>
          <div class="table-responsive" style="border-radius:8px;border:1px solid #e2e8f0">
            <table class="table table-sm table-bordered mb-0" style="font-size:11px;min-width:500px">
              <thead style="background:#f8fafc">
                <tr>
                  <th class="text-center" style="width:90px">Tanggal</th>
                  <th class="text-center" style="width:35px">No</th>
                  <th>Nama Nasabah</th>
                  <th class="text-center">Besi</th>
                  <th class="text-center">Botol Plastik</th>
                  <th class="text-center">Alumunium</th>
                  <th class="text-center">...</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td class="text-center text-muted">05/01/2024</td>
                  <td class="text-center">1</td>
                  <td>Budi Santoso</td>
                  <td class="text-center">2.5</td>
                  <td class="text-center">1</td>
                  <td class="text-center">0</td>
                  <td class="text-center">...</td>
                </tr>
                <tr>
                  <td class="text-center text-muted">12/01/2024</td>
                  <td class="text-center">2</td>
                  <td>Siti Aminah</td>
                  <td class="text-center">0</td>
                  <td class="text-center">3</td>
                  <td class="text-center">0.75</td>
                  <td class="text-center">...</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        {{-- Form upload --}}
        <form action="{{ route('admin.transaksi.import') }}" method="POST" enctype="multipart/form-data" id="formImport">
          @csrf
          <div class="mb-3">
            <label for="fileExcel" class="form-label fw-semibold" style="font-size:13px">
              Pilih File Excel <span class="text-danger">*</span>
            </label>
            <input type="file" name="file_excel" id="fileExcel" class="form-control" accept=".xlsx,.xls"
                   style="font-size:13px" required>
            <div class="form-text" style="font-size:11px">Hanya file .xlsx atau .xls yang diterima. Maks 10 MB.</div>
          </div>

          {{-- Progress bar (ditampilkan saat upload) --}}
          <div id="uploadProgress" style="display:none" class="mb-3">
            <div style="font-size:12px;color:#64748b;margin-bottom:4px">
              <i class="bi bi-hourglass-split me-1"></i>Sedang memproses file, harap tunggu...
            </div>
            <div class="progress" style="height:6px;border-radius:3px">
              <div class="progress-bar progress-bar-striped progress-bar-animated"
                   style="width:100%;background:#1d4ed8"></div>
            </div>
          </div>

          <div class="d-flex gap-2 justify-content-end">
            <button type="button" class="btn btn-sm" data-bs-dismiss="modal"
                    style="background:#f1f5f9;border-radius:8px;font-size:13px">Batal</button>
            <button type="submit" id="btnImport" class="btn btn-sm"
                    style="background:#1d4ed8;color:#fff;border-radius:8px;font-size:13px">
              <i class="bi bi-upload me-1"></i>Mulai Import
            </button>
          </div>
        </form>

      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
// Tampilkan progress bar saat form submit
document.getElementById('formImport').addEventListener('submit', function() {
  document.getElementById('uploadProgress').style.display = 'block';
  document.getElementById('btnImport').disabled = true;
  document.getElementById('btnImport').innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Memproses...';
});
</script>
@endpush

@endsection