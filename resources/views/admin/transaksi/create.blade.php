@extends('layouts.admin')
@section('title','Catat Setoran')
@section('page-title','Catat Transaksi Setoran Sampah')

@section('content')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<style>
  .select2-container .select2-selection--single { font-size: 13px; height: 38px; display: flex; align-items: center; }
  .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered { padding-top: 0; padding-bottom: 0; }
  .select2-container--bootstrap-5 .select2-dropdown { font-size: 13px; }
</style>

<div class="row justify-content-center">
  <div class="col-lg-8">
    <div class="table-card">
      <div class="card-header">
        <h6>Form Setoran Sampah</h6>
        <a href="{{ route('admin.transaksi.index') }}" class="btn btn-sm"
           style="background:#f1f5f9;font-size:12px">
          <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
      </div>
      <div class="p-4">
        @if($errors->any())
          <div class="alert alert-danger mb-3">
            <ul class="mb-0 ps-3">
              @foreach($errors->all() as $e)
                <li style="font-size:13px">{{ $e }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form method="POST" action="{{ route('admin.transaksi.store') }}" id="formTransaksi">
          @csrf
          <!-- Nasabah + Tanggal -->
          <div class="row mb-3">
            <div class="col-md-7">
              <label class="form-label fw-semibold" style="font-size:13px">Nasabah</label>
              <select name="nasabah_id" class="form-select" required id="selectNasabah">
                <option value="">-- Pilih Nasabah --</option>
                @foreach($nasabah as $n)
                  <option value="{{ $n->id }}"
                    data-saldo="{{ number_format($n->saldo, 0, ',', '.') }}"
                    {{ old('nasabah_id') == $n->id ? 'selected' : '' }}>
                    {{ $n->user->name }} ({{ $n->nik }})
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-md-5">
              <label class="form-label fw-semibold" style="font-size:13px">Tanggal Setoran</label>
              <input type="date" name="tanggal" class="form-control"
                     value="{{ old('tanggal', date('Y-m-d')) }}" required>
            </div>
          </div>

          <!-- Info saldo nasabah terpilih -->
          <div id="infoSaldo" class="alert mb-3"
               style="background:#f0fdf4;border:1px solid #bbf7d0;display:none">
            <i class="bi bi-wallet2 me-2" style="color:#16a34a"></i>
            <span style="font-size:13px;color:#15803d">
              Saldo saat ini: <strong id="textSaldo">-</strong>
            </span>
          </div>

          <!-- Tabel detail setoran -->
          <div class="table-card mb-3" style="border-radius:10px">
            <div class="card-header" style="padding:12px 16px">
              <h6 style="font-size:13px;margin:0">Detail Sampah yang Disetorkan</h6>
              <button type="button" id="btnTambahBaris"
                      class="btn btn-sm" style="background:#dcfce7;color:#16a34a;border-radius:7px;font-size:12px">
                <i class="bi bi-plus-lg me-1"></i>Tambah Jenis
              </button>
            </div>
            <div class="table-responsive">
              <table class="table mb-0" id="tabelDetail">
                <thead>
                  <tr>
                    <th style="width:45%">Kategori Sampah</th>
                    <th style="width:20%">Berat (kg)</th>
                    <th style="width:25%">Estimasi Nilai</th>
                    <th style="width:10%"></th>
                  </tr>
                </thead>
                <tbody id="tbodyDetail">
                  <!-- Baris pertama -->
                  <tr class="baris-detail">
                    <td>
                      <select name="kategori_id[]" class="form-select form-select-sm sel-kategori" required>
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($kategori as $k)
                          <option value="{{ $k->id }}" data-harga="{{ $k->harga_per_kg }}">
                            {{ $k->nama_kategori }} (Rp {{ number_format($k->harga_per_kg,0,',','.') }}/kg)
                          </option>
                        @endforeach
                      </select>
                    </td>
                    <td>
                      <input type="number" name="berat_kg[]" class="form-control form-control-sm inp-berat"
                             placeholder="0.000" min="0.001" step="0.001" required>
                    </td>
                    <td>
                      <span class="estimasi-nilai" style="font-size:13px;font-weight:600;color:#16a34a">Rp 0</span>
                    </td>
                    <td>
                      <button type="button" class="btn btn-sm btn-hapus"
                              style="background:#fee2e2;color:#dc2626;border-radius:7px;display:none">
                        <i class="bi bi-trash"></i>
                      </button>
                    </td>
                  </tr>
                </tbody>
                <tfoot>
                  <tr style="background:#f8fafc">
                    <td colspan="2" style="font-weight:600;font-size:13px;text-align:right">
                      Total Nilai:
                    </td>
                    <td colspan="2">
                      <span id="totalNilai" style="font-size:15px;font-weight:700;color:#16a34a">
                        Rp 0
                      </span>
                    </td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>

          <!-- Catatan -->
          <div class="mb-4">
            <label class="form-label fw-semibold" style="font-size:13px">
              Catatan <small class="text-muted">(opsional)</small>
            </label>
            <textarea name="catatan" class="form-control" rows="2"
                      placeholder="Catatan tambahan...">{{ old('catatan') }}</textarea>
          </div>

          <!-- ════════════════════════════════════
               SECTION: Penarikan Dana
               ════════════════════════════════════ -->
          <div class="mb-4" style="background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:12px;padding:20px">
            <div class="d-flex align-items-center gap-2 mb-3">
              <i class="bi bi-cash-stack" style="font-size:18px;color:#0f172a"></i>
              <span style="font-size:14px;font-weight:700;color:#0f172a">Pencairan Dana</span>
              <span class="badge" style="background:#fef3c7;color:#92400e;font-size:11px;font-weight:500">
                Tanyakan ke nasabah
              </span>
            </div>
            <p style="font-size:12px;color:#64748b;margin-bottom:12px">
              Apakah nasabah ingin mencairkan nilai setoran ini sekarang, dijadwalkan, atau disimpan dulu ke saldo?
            </p>

            <div class="d-flex flex-column gap-2" id="pilihanPenarikan">
              {{-- Opsi: Tidak (simpan ke saldo) --}}
              <label class="d-flex align-items-center gap-3 p-3" id="labelTidak"
                     style="border:1.5px solid #e2e8f0;border-radius:10px;cursor:pointer;background:#fff;transition:all .15s">
                <input type="radio" name="penarikan_jenis" value="tidak" checked
                       id="rdTidak" style="accent-color:#16a34a;width:16px;height:16px">
                <div>
                  <div style="font-size:13px;font-weight:600;color:#0f172a">
                    <i class="bi bi-piggy-bank me-1" style="color:#16a34a"></i> Simpan ke Saldo
                  </div>
                  <div style="font-size:11px;color:#64748b">Nilai langsung masuk ke saldo tabungan nasabah</div>
                </div>
              </label>

              {{-- Opsi: Cair sekarang --}}
              <label class="d-flex align-items-center gap-3 p-3" id="labelSegera"
                     style="border:1.5px solid #e2e8f0;border-radius:10px;cursor:pointer;background:#fff;transition:all .15s">
                <input type="radio" name="penarikan_jenis" value="segera"
                       id="rdSegera" style="accent-color:#16a34a;width:16px;height:16px">
                <div>
                  <div style="font-size:13px;font-weight:600;color:#0f172a">
                    <i class="bi bi-lightning-charge me-1" style="color:#2563eb"></i> Cair Sekarang
                  </div>
                  <div style="font-size:11px;color:#64748b">Dana langsung dicairkan hari ini, saldo tidak bertambah</div>
                </div>
              </label>

              {{-- Opsi: Jadwalkan --}}
              <label class="d-flex align-items-center gap-3 p-3" id="labelTerjadwal"
                     style="border:1.5px solid #e2e8f0;border-radius:10px;cursor:pointer;background:#fff;transition:all .15s">
                <input type="radio" name="penarikan_jenis" value="terjadwal"
                       id="rdTerjadwal" style="accent-color:#16a34a;width:16px;height:16px">
                <div>
                  <div style="font-size:13px;font-weight:600;color:#0f172a">
                    <i class="bi bi-calendar-event me-1" style="color:#7c3aed"></i> Jadwalkan
                  </div>
                  <div style="font-size:11px;color:#64748b">Nasabah minta cair di agenda bulan berikutnya</div>
                </div>
              </label>
            </div>

            {{-- Input tanggal (muncul jika pilih terjadwal) --}}
            <div id="wrapTanggalPenarikan" class="mt-3" style="display:none">
              <label class="form-label" style="font-size:12px;color:#475569;font-weight:600">
                Tanggal Pencairan yang Diminta
              </label>
              <input type="date" name="tanggal_penarikan" id="tanggalPenarikan"
                     class="form-control" style="font-size:13px;max-width:240px"
                     min="{{ date('Y-m-d') }}">
            </div>
          </div>

          <div class="d-flex gap-2">
            <button type="submit" class="btn px-4"
                    style="background:#16a34a;color:#fff;border-radius:9px;font-size:14px">
              <i class="bi bi-check-lg me-1"></i>Simpan Transaksi
            </button>
            <a href="{{ route('admin.transaksi.index') }}"
               class="btn px-4" style="background:#f1f5f9;border-radius:9px;font-size:14px">
              Batal
            </a>
          </div>
        </form>

      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<!-- jQuery & Select2 JS -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
  $('#selectNasabah').select2({
    theme: 'bootstrap-5',
    width: '100%',
    placeholder: '-- Pilih Nasabah --'
  });
  $('.sel-kategori').select2({
    theme: 'bootstrap-5',
    width: '100%',
    placeholder: '-- Pilih Kategori --'
  });
});

// ── Template baris baru ──────────────────────────────────
const kategoriOptions = `@foreach($kategori as $k)
  <option value="{{ $k->id }}" data-harga="{{ $k->harga_per_kg }}">
    {{ $k->nama_kategori }} (Rp {{ number_format($k->harga_per_kg,0,',','.') }}/kg)
  </option>
@endforeach`;

function buatBaris() {
  const tr = document.createElement('tr');
  tr.className = 'baris-detail';
  tr.innerHTML = `
    <td>
      <select name="kategori_id[]" class="form-select form-select-sm sel-kategori" required>
        <option value="">-- Pilih Kategori --</option>
        ${kategoriOptions}
      </select>
    </td>
    <td>
      <input type="number" name="berat_kg[]" class="form-control form-control-sm inp-berat"
             placeholder="0.000" min="0.001" step="0.001" required>
    </td>
    <td><span class="estimasi-nilai" style="font-size:13px;font-weight:600;color:#16a34a">Rp 0</span></td>
    <td>
      <button type="button" class="btn btn-sm btn-hapus"
              style="background:#fee2e2;color:#dc2626;border-radius:7px">
        <i class="bi bi-trash"></i>
      </button>
    </td>`;
  return tr;
}

// ── Hitung nilai per baris & total ──────────────────────
function hitungNilai() {
  let total = 0;
  document.querySelectorAll('.baris-detail').forEach(row => {
    const sel   = row.querySelector('.sel-kategori');
    const inp   = row.querySelector('.inp-berat');
    const span  = row.querySelector('.estimasi-nilai');
    const harga = parseFloat(sel.selectedOptions[0]?.dataset?.harga || 0);
    const berat = parseFloat(inp.value || 0);
    const nilai = harga * berat;
    total += nilai;
    span.textContent = 'Rp ' + nilai.toLocaleString('id-ID');
  });
  document.getElementById('totalNilai').textContent =
    'Rp ' + total.toLocaleString('id-ID');
}

// ── Tambah baris ────────────────────────────────────────
document.getElementById('btnTambahBaris').addEventListener('click', () => {
  const tbody = document.getElementById('tbodyDetail');
  const newRow = buatBaris();
  tbody.appendChild(newRow);
  
  // Init Select2 on the new row
  $(newRow).find('.sel-kategori').select2({
    theme: 'bootstrap-5',
    width: '100%',
    placeholder: '-- Pilih Kategori --'
  });

  // Tampilkan semua tombol hapus jika baris > 1
  const hapusBtns = document.querySelectorAll('.btn-hapus');
  hapusBtns.forEach(b => b.style.display = 'block');
  bindEvents();
});

// ── Hapus baris ─────────────────────────────────────────
function bindEvents() {
  document.querySelectorAll('.btn-hapus').forEach(btn => {
    btn.onclick = function() {
      // Destroy Select2 before removing to prevent memory leaks
      let sel = $(this).closest('tr').find('.sel-kategori');
      if (sel.data('select2')) {
        sel.select2('destroy');
      }
      this.closest('tr').remove();
      const hapusBtns = document.querySelectorAll('.btn-hapus');
      if (hapusBtns.length === 1) hapusBtns[0].style.display = 'none';
      hitungNilai();
    };
  });
  
  // Gunakan jQuery untuk event change pada Select2
  $('.sel-kategori').off('change').on('change', hitungNilai);
  document.querySelectorAll('.inp-berat').forEach(el => {
    el.oninput = hitungNilai;
    el.onchange = hitungNilai;
  });
}
bindEvents();

// ── Info saldo nasabah ───────────────────────────────────
$('#selectNasabah').on('change', function() {
  const opt = this.options[this.selectedIndex];
  const saldo = opt.dataset.saldo;
  if (saldo) {
    document.getElementById('textSaldo').textContent = 'Rp ' + saldo;
    document.getElementById('infoSaldo').style.display = 'block';
  } else {
    document.getElementById('infoSaldo').style.display = 'none';
  }
});

// ── Penarikan dana – highlight pilihan aktif ─────────────
const rdLabels = {
  rdTidak:     'labelTidak',
  rdSegera:    'labelSegera',
  rdTerjadwal: 'labelTerjadwal',
};
function updatePenarikanUI() {
  Object.entries(rdLabels).forEach(([rdId, labelId]) => {
    const rd    = document.getElementById(rdId);
    const label = document.getElementById(labelId);
    if (rd && label) {
      if (rd.checked) {
        label.style.borderColor = '#16a34a';
        label.style.background  = '#f0fdf4';
      } else {
        label.style.borderColor = '#e2e8f0';
        label.style.background  = '#fff';
      }
    }
  });
  const wrap = document.getElementById('wrapTanggalPenarikan');
  const rdT  = document.getElementById('rdTerjadwal');
  if (wrap && rdT) wrap.style.display = rdT.checked ? 'block' : 'none';
}
document.querySelectorAll('input[name="penarikan_jenis"]').forEach(r => {
  r.addEventListener('change', updatePenarikanUI);
});
updatePenarikanUI();
</script>
@endpush