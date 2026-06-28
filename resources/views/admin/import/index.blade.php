@extends('layouts.admin')
@section('title', 'Import Data Historis')
@section('page-title', 'Import Data Historis')

@push('head-styles')
<style>
/* ── Dropzone ───────────────────────────────────────────────────────────── */
.dropzone-area {
  border: 2px dashed #cbd5e1;
  border-radius: 14px;
  background: #f8fafc;
  padding: 48px 24px;
  text-align: center;
  cursor: pointer;
  transition: all .2s;
  position: relative;
}
.dropzone-area:hover,
.dropzone-area.dragover { border-color: #16a34a; background: #f0fdf4; }
.dropzone-area input[type="file"] {
  position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%;
}
.dropzone-icon { font-size: 48px; color: #94a3b8; margin-bottom: 12px; line-height: 1; }
.dropzone-area.has-file .dropzone-icon { color: #16a34a; }
.dropzone-label { font-size: 15px; font-weight: 600; color: #334155; }
.dropzone-sub   { font-size: 12.5px; color: #94a3b8; margin-top: 6px; }

/* ── Step Guide ─────────────────────────────────────────────────────────── */
.step-list { counter-reset: step; list-style: none; padding: 0; margin: 0; }
.step-list li {
  counter-increment: step;
  display: flex; gap: 14px; align-items: flex-start;
  padding: 12px 0;
  border-bottom: 1px solid #f1f5f9;
}
.step-list li:last-child { border-bottom: none; }
.step-num {
  min-width: 28px; height: 28px; border-radius: 50%;
  background: linear-gradient(135deg,#16a34a,#22c55e);
  color: #fff; font-size: 12px; font-weight: 700;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.step-text { font-size: 13.5px; color: #374151; line-height: 1.5; padding-top: 3px; }
.step-text strong { color: #0f172a; }

/* ── Result Card ────────────────────────────────────────────────────────── */
.result-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 12px; margin-bottom: 16px; }
.result-item {
  background: #fff; border-radius: 10px; border: 1px solid #e2e8f0;
  padding: 16px; text-align: center;
}
.result-num  { font-size: 28px; font-weight: 700; line-height: 1; }
.result-lbl  { font-size: 11.5px; color: #64748b; margin-top: 4px; }

/* ── Column hint table ──────────────────────────────────────────────────── */
.col-hint { font-size: 11.5px; }
.col-hint td, .col-hint th { padding: 6px 10px !important; }

/* ── Progress overlay ───────────────────────────────────────────────────── */
#upload-progress {
  display: none;
  position: fixed; inset: 0; background: rgba(15,23,42,.65);
  z-index: 9999; align-items: center; justify-content: center; flex-direction: column;
}
#upload-progress.show { display: flex; }
.progress-box {
  background: #fff; border-radius: 16px; padding: 36px 48px;
  text-align: center; min-width: 280px;
}
.spinner {
  width: 44px; height: 44px; border: 4px solid #e2e8f0;
  border-top-color: #16a34a; border-radius: 50%;
  animation: spin .7s linear infinite; margin: 0 auto 16px;
}
@keyframes spin { to { transform: rotate(360deg); } }
</style>
@endpush

@section('content')
<div class="container-fluid">

  {{-- ═══ Hasil Import (flash) ══════════════════════════════════════════════ --}}
  @if(session('import_success'))
    @php $res = session('import_results'); @endphp
    <div class="table-card mb-4" style="border-top:3px solid #16a34a">
      <div class="card-header">
        <h6><i class="bi bi-check-circle-fill me-2" style="color:#16a34a"></i>Import Selesai!</h6>
      </div>
      <div style="padding:20px">
        <div class="result-grid">
          <div class="result-item">
            <div class="result-num text-success">{{ $res['sukses'] }}</div>
            <div class="result-lbl">Nasabah Berhasil Diimport</div>
          </div>
          <div class="result-item">
            <div class="result-num" style="color:#f59e0b">{{ count($res['created_users']) }}</div>
            <div class="result-lbl">Akun Baru Dibuat</div>
          </div>
          <div class="result-item">
            <div class="result-num" style="color:#94a3b8">{{ $res['dilewati'] }}</div>
            <div class="result-lbl">Baris Dilewati (tidak ada data)</div>
          </div>
        </div>

        @if(!empty($res['created_users']))
          <div class="alert" style="background:#fef9c3;color:#713f12;border-radius:10px;font-size:13px">
            <i class="bi bi-person-plus me-2"></i>
            <strong>Akun baru dibuat untuk:</strong>
            {{ implode(', ', $res['created_users']) }}.
            Password default: <code>nasabah123</code>. Harap ganti NIK & data profil nasabah tersebut.
          </div>
        @endif

        @if(!empty($res['errors']))
          <div class="alert" style="background:#fee2e2;color:#7f1d1d;border-radius:10px;font-size:12.5px">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>Peringatan pada {{ count($res['errors']) }} baris:</strong>
            <ul class="mb-0 mt-1">
              @foreach($res['errors'] as $e)
                <li>{{ $e }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <div class="d-flex gap-2 mt-2">
          <a href="{{ route('admin.penilaian.index') }}" class="btn btn-sm"
             style="background:linear-gradient(135deg,#16a34a,#22c55e);color:#fff;font-weight:600;border:none;padding:8px 20px;border-radius:9px">
            <i class="bi bi-trophy me-1"></i>Hitung Nasabah Terbaik
          </a>
          <a href="{{ route('admin.nasabah.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-people me-1"></i>Kelola Nasabah
          </a>
        </div>
      </div>
    </div>
  @endif

  @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4">
      <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <div class="row g-4">

    {{-- ═══ KOLOM KIRI: Form Upload ════════════════════════════════════════ --}}
    <div class="col-lg-7">
      <div class="table-card">
        <div class="card-header">
          <h6><i class="bi bi-upload me-2" style="color:#16a34a"></i>Upload File CSV</h6>
          <a href="{{ route('admin.import.template') }}" class="btn btn-sm"
             style="background:#0f172a;color:#fff;font-size:12px;border:none;border-radius:8px;padding:6px 14px">
            <i class="bi bi-download me-1"></i>Download Template
          </a>
        </div>
        <div style="padding:24px">

          <form method="POST" action="{{ route('admin.import.store') }}"
                enctype="multipart/form-data" id="importForm">
            @csrf

            {{-- Dropzone --}}
            <div class="dropzone-area mb-4" id="dropzone">
              <input type="file" name="file" id="fileInput" accept=".csv,.txt">
              <div class="dropzone-icon"><i class="bi bi-file-earmark-spreadsheet"></i></div>
              <div class="dropzone-label" id="dropzoneLabel">Klik atau seret file CSV ke sini</div>
              <div class="dropzone-sub">Format: .csv &nbsp;|&nbsp; Maks. 10 MB</div>
            </div>

            @error('file')
              <div class="alert alert-danger py-2 mb-3" style="font-size:13px">{{ $message }}</div>
            @enderror

            {{-- Info kategori tersedia --}}
            @php
              $availCats = \App\Models\KategoriSampah::orderBy('id')->get();
            @endphp
            <div class="alert mb-4" style="background:#f0f9ff;color:#075985;border-radius:10px;font-size:12.5px">
              <i class="bi bi-info-circle me-1"></i>
              <strong>Kategori tersedia ({{ $availCats->count() }}):</strong>
              {{ $availCats->pluck('nama_kategori')->join(', ') }}.<br>
              Sistem akan menggunakan kategori-kategori ini secara bergantian sesuai jumlah keragaman.
            </div>

            <button type="submit" id="submitBtn"
                    class="btn w-100"
                    style="background:linear-gradient(135deg,#16a34a,#22c55e);color:#fff;font-weight:700;
                           border:none;padding:13px;border-radius:11px;font-size:15px"
                    disabled>
              <i class="bi bi-cloud-upload me-2"></i>Mulai Import Data
            </button>
          </form>

        </div>
      </div>
    </div>

    {{-- ═══ KOLOM KANAN: Panduan ═══════════════════════════════════════════ --}}
    <div class="col-lg-5">

      {{-- Panduan Langkah --}}
      <div class="table-card mb-4">
        <div class="card-header">
          <h6><i class="bi bi-journal-text me-2" style="color:#4f46e5"></i>Cara Import Data</h6>
        </div>
        <div style="padding:20px">
          <ol class="step-list">
            <li>
              <div class="step-num">1</div>
              <div class="step-text">
                <strong>Download template CSV</strong> dengan tombol di atas untuk mendapatkan format yang benar.
              </div>
            </li>
            <li>
              <div class="step-num">2</div>
              <div class="step-text">
                Buka template dan <strong>isi data dari Excel lama Anda</strong>. Kolom bulan diisi
                <em>jumlah kali setoran</em> di bulan tersebut (bukan berat).
              </div>
            </li>
            <li>
              <div class="step-num">3</div>
              <div class="step-text">
                Simpan file dari Excel sebagai <strong>"CSV UTF-8 (delimited)"</strong>.
              </div>
            </li>
            <li>
              <div class="step-num">4</div>
              <div class="step-text">Upload file CSV dan klik <strong>Mulai Import Data</strong>.</div>
            </li>
            <li>
              <div class="step-num">5</div>
              <div class="step-text">
                Setelah selesai, pergi ke menu <strong>Nasabah Terbaik → Hitung SAW</strong>
                untuk mendapatkan peringkat.
              </div>
            </li>
          </ol>
        </div>
      </div>

      {{-- Format Kolom --}}
      <div class="table-card">
        <div class="card-header">
          <h6><i class="bi bi-table me-2" style="color:#f59e0b"></i>Format Kolom CSV</h6>
        </div>
        <div class="table-responsive">
          <table class="table col-hint mb-0">
            <thead>
              <tr>
                <th>Kolom</th>
                <th>Keterangan</th>
                <th>Contoh</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><strong>No</strong></td>
                <td>Nomor urut (diabaikan)</td>
                <td>1</td>
              </tr>
              <tr>
                <td><strong>Nama Nasabah</strong></td>
                <td>Nama lengkap nasabah</td>
                <td>Budi Santoso</td>
              </tr>
              <tr>
                <td><strong>Sep 2025</strong> <span class="badge" style="background:#e0f2fe;color:#0369a1;font-size:9px">dst</span></td>
                <td>Jumlah setoran di bulan itu</td>
                <td>3</td>
              </tr>
              <tr>
                <td><strong>Total Berat (kg)</strong></td>
                <td>Total berat seluruh periode</td>
                <td>45.50</td>
              </tr>
              <tr>
                <td><strong>Total Keragaman Jenis Sampah</strong></td>
                <td>Berapa jenis sampah berbeda</td>
                <td>3</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div style="padding:12px 16px;background:#fef9c3;font-size:12px;color:#713f12;border-top:1px solid #fef08a">
          <i class="bi bi-exclamation-triangle me-1"></i>
          Nasabah yang belum ada akan dibuatkan akun baru dengan password default <code>nasabah123</code>.
        </div>
      </div>

    </div>
  </div>
</div>

{{-- Progress overlay --}}
<div id="upload-progress">
  <div class="progress-box">
    <div class="spinner"></div>
    <div style="font-size:15px;font-weight:600;color:#0f172a">Sedang memproses...</div>
    <div style="font-size:12.5px;color:#64748b;margin-top:6px">Mohon tunggu, jangan tutup halaman ini</div>
  </div>
</div>
@endsection

@push('scripts')
<script>
const dropzone   = document.getElementById('dropzone');
const fileInput  = document.getElementById('fileInput');
const submitBtn  = document.getElementById('submitBtn');
const dropLabel  = document.getElementById('dropzoneLabel');
const importForm = document.getElementById('importForm');
const progressEl = document.getElementById('upload-progress');

// Dropzone interactions
fileInput.addEventListener('change', function () {
  if (this.files.length > 0) {
    dropLabel.textContent = '✓ ' + this.files[0].name;
    dropzone.classList.add('has-file');
    submitBtn.disabled = false;
    submitBtn.style.opacity = '1';
  }
});

['dragover', 'dragenter'].forEach(ev => {
  dropzone.addEventListener(ev, e => { e.preventDefault(); dropzone.classList.add('dragover'); });
});
['dragleave', 'drop'].forEach(ev => {
  dropzone.addEventListener(ev, e => { e.preventDefault(); dropzone.classList.remove('dragover'); });
});
dropzone.addEventListener('drop', e => {
  e.preventDefault();
  fileInput.files = e.dataTransfer.files;
  fileInput.dispatchEvent(new Event('change'));
});

// Show progress on submit
importForm.addEventListener('submit', function () {
  if (!fileInput.files.length) return;
  progressEl.classList.add('show');
});
</script>
@endpush
