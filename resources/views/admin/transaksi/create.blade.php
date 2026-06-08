@extends('layouts.admin')
@section('title','Catat Setoran')
@section('page-title','Catat Transaksi Setoran Sampah')

@section('content')
<!-- Tom Select CSS -->
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<style>
  .ts-wrapper.single .ts-control { padding: 6px 10px; font-size: 13px; min-height: 36px; }
  .ts-dropdown { font-size: 13px; }
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
                      <select name="kategori_id[]" id="sel_first" class="sel-kategori" required>
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
<!-- Tom Select JS -->
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

<script>
// ── Inisialisasi Tom Select – Nasabah ────────────────────
new TomSelect('#selectNasabah', {
  placeholder: '-- Pilih Nasabah --',
  allowEmptyOption: true,
  create: false,
  onChange: function(value) {
    const opt = document.querySelector('#selectNasabah option[value="' + value + '"]');
    const saldo = opt ? opt.dataset.saldo : null;
    if (saldo) {
      document.getElementById('textSaldo').textContent = 'Rp ' + saldo;
      document.getElementById('infoSaldo').style.display = 'block';
    } else {
      document.getElementById('infoSaldo').style.display = 'none';
    }
  }
});

// ── Fungsi init Tom Select untuk select kategori ─────────
function initTomSelectKategori(el) {
  return new TomSelect(el, {
    placeholder: '-- Pilih Kategori --',
    allowEmptyOption: true,
    create: false,
    onChange: function() {
      hitungNilai();
    }
  });
}

// Init baris pertama
initTomSelectKategori('#sel_first');

// ── Template data opsi kategori (dari server Blade) ───────
const kategoriData = [
  @foreach($kategori as $k)
  { value: "{{ $k->id }}", text: "{{ $k->nama_kategori }} (Rp {{ number_format($k->harga_per_kg,0,',','.') }}/kg)", harga: {{ $k->harga_per_kg }} },
  @endforeach
];

// ── Fungsi buat baris baru ────────────────────────────────
function buatBaris() {
  const tr = document.createElement('tr');
  tr.className = 'baris-detail';
  const randId = 'sel_' + Math.random().toString(36).substr(2, 9);

  // Buat elemen select secara manual (bukan innerHTML agar bersih)
  const td1 = document.createElement('td');
  const select = document.createElement('select');
  select.name = 'kategori_id[]';
  select.id = randId;
  select.required = true;

  // Opsi kosong
  const emptyOpt = document.createElement('option');
  emptyOpt.value = '';
  emptyOpt.textContent = '-- Pilih Kategori --';
  select.appendChild(emptyOpt);

  // Semua opsi kategori
  kategoriData.forEach(k => {
    const opt = document.createElement('option');
    opt.value = k.value;
    opt.dataset.harga = k.harga;
    opt.textContent = k.text;
    select.appendChild(opt);
  });
  td1.appendChild(select);

  const td2 = document.createElement('td');
  const input = document.createElement('input');
  input.type = 'number';
  input.name = 'berat_kg[]';
  input.className = 'form-control form-control-sm inp-berat';
  input.placeholder = '0.000';
  input.min = '0.001';
  input.step = '0.001';
  input.required = true;
  input.addEventListener('input', hitungNilai);
  input.addEventListener('change', hitungNilai);
  td2.appendChild(input);

  const td3 = document.createElement('td');
  const span = document.createElement('span');
  span.className = 'estimasi-nilai';
  span.style.cssText = 'font-size:13px;font-weight:600;color:#16a34a';
  span.textContent = 'Rp 0';
  td3.appendChild(span);

  const td4 = document.createElement('td');
  const btn = document.createElement('button');
  btn.type = 'button';
  btn.className = 'btn btn-sm btn-hapus';
  btn.style.cssText = 'background:#fee2e2;color:#dc2626;border-radius:7px';
  btn.innerHTML = '<i class="bi bi-trash"></i>';
  td4.appendChild(btn);

  tr.appendChild(td1);
  tr.appendChild(td2);
  tr.appendChild(td3);
  tr.appendChild(td4);

  return { tr, select };
}

// ── Hitung estimasi nilai per baris & total ───────────────
function hitungNilai() {
  let total = 0;
  document.querySelectorAll('.baris-detail').forEach(row => {
    const sel  = row.querySelector('[name="kategori_id[]"]');
    const inp  = row.querySelector('.inp-berat');
    const span = row.querySelector('.estimasi-nilai');
    if (!sel || !inp || !span) return;

    const selectedOpt = sel.options[sel.selectedIndex];
    const harga = selectedOpt ? parseFloat(selectedOpt.dataset.harga || 0) : 0;
    const berat = parseFloat(inp.value || 0);
    const nilai = harga * berat;
    total += nilai;
    span.textContent = 'Rp ' + nilai.toLocaleString('id-ID');
  });
  document.getElementById('totalNilai').textContent = 'Rp ' + total.toLocaleString('id-ID');
}

// ── Tambah baris baru ─────────────────────────────────────
document.getElementById('btnTambahBaris').addEventListener('click', () => {
  const tbody = document.getElementById('tbodyDetail');
  const { tr, select } = buatBaris();
  tbody.appendChild(tr);

  // Init Tom Select SETELAH elemen masuk ke DOM
  initTomSelectKategori(select);

  // Tampilkan semua tombol hapus
  document.querySelectorAll('.btn-hapus').forEach(b => b.style.display = 'block');

  // Bind tombol hapus
  bindHapus();
});

// ── Hapus baris ───────────────────────────────────────────
function bindHapus() {
  document.querySelectorAll('.btn-hapus').forEach(btn => {
    btn.onclick = function() {
      const tr = this.closest('tr');
      const sel = tr.querySelector('[name="kategori_id[]"]');
      if (sel && sel.tomselect) {
        sel.tomselect.destroy();
      }
      tr.remove();
      const btns = document.querySelectorAll('.btn-hapus');
      if (btns.length === 1) btns[0].style.display = 'none';
      hitungNilai();
    };
  });
}
bindHapus();

// Bind input berat pada baris pertama
document.querySelectorAll('.inp-berat').forEach(el => {
  el.addEventListener('input', hitungNilai);
  el.addEventListener('change', hitungNilai);
});

// ── Penarikan dana – highlight pilihan aktif ──────────────
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
      label.style.borderColor = rd.checked ? '#16a34a' : '#e2e8f0';
      label.style.background  = rd.checked ? '#f0fdf4' : '#fff';
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