@extends('layouts.admin')
@section('title','Edit Transaksi #' . $transaksi->id)
@section('page-title','Edit Transaksi #' . $transaksi->id)

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
        <h6>Edit Transaksi #{{ $transaksi->id }}</h6>
        <a href="{{ route('admin.transaksi.index') }}" class="btn btn-sm"
           style="background:#f1f5f9;font-size:12px">
          <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
      </div>
      <div class="p-4">

        {{-- Info nasabah (read-only, tidak bisa diubah) --}}
        <div class="mb-3 p-3 d-flex align-items-center gap-3"
             style="background:#f8fafc;border-radius:10px;border:1px solid #e2e8f0">
          <div style="width:38px;height:38px;border-radius:50%;background:#dcfce7;display:flex;align-items:center;justify-content:center">
            <i class="bi bi-person" style="color:#16a34a;font-size:16px"></i>
          </div>
          <div>
            <div style="font-weight:700;font-size:14px">{{ $transaksi->nasabah->user->name }}</div>
            <div style="font-size:11px;color:#94a3b8">NIK: {{ $transaksi->nasabah->nik }} &bull; Saldo: Rp {{ number_format($transaksi->nasabah->saldo, 0, ',', '.') }}</div>
          </div>
          <span class="badge ms-auto" style="background:#fef3c7;color:#92400e;font-size:11px">Nasabah tidak dapat diubah</span>
        </div>

        @if($errors->any())
          <div class="alert alert-danger mb-3">
            <ul class="mb-0 ps-3">
              @foreach($errors->all() as $e)
                <li style="font-size:13px">{{ $e }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form method="POST" action="{{ route('admin.transaksi.update', $transaksi) }}" id="formEdit">
          @csrf @method('PUT')

          {{-- Tanggal --}}
          <div class="mb-3">
            <label class="form-label fw-semibold" style="font-size:13px">Tanggal Setoran</label>
            <input type="date" name="tanggal" class="form-control"
                   value="{{ old('tanggal', $transaksi->tanggal->format('Y-m-d')) }}" required
                   style="max-width:220px">
          </div>

          {{-- Tabel detail setoran --}}
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
                  @foreach($transaksi->detail as $i => $d)
                  <tr class="baris-detail">
                    <td>
                      <select name="kategori_id[]" id="sel_{{ $i }}" class="sel-kategori" required>
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($kategori as $k)
                          <option value="{{ $k->id }}" data-harga="{{ $k->harga_per_kg }}"
                            {{ $d->kategori_id == $k->id ? 'selected' : '' }}>
                            {{ $k->nama_kategori }} (Rp {{ number_format($k->harga_per_kg,0,',','.') }}/kg)
                          </option>
                        @endforeach
                      </select>
                    </td>
                    <td>
                      <input type="number" name="berat_kg[]" class="form-control form-control-sm inp-berat"
                             placeholder="0.000" min="0.001" step="0.001"
                             value="{{ old('berat_kg.' . $i, $d->berat_kg) }}" required>
                    </td>
                    <td>
                      <span class="estimasi-nilai" style="font-size:13px;font-weight:600;color:#16a34a">
                        Rp {{ number_format($d->nilai, 0, ',', '.') }}
                      </span>
                    </td>
                    <td>
                      <button type="button" class="btn btn-sm btn-hapus"
                              style="background:#fee2e2;color:#dc2626;border-radius:7px;{{ $loop->first ? 'display:none' : '' }}">
                        <i class="bi bi-trash"></i>
                      </button>
                    </td>
                  </tr>
                  @endforeach
                </tbody>
                <tfoot>
                  <tr style="background:#f8fafc">
                    <td colspan="2" style="font-weight:600;font-size:13px;text-align:right">Total Nilai:</td>
                    <td colspan="2">
                      <span id="totalNilai" style="font-size:15px;font-weight:700;color:#16a34a">
                        Rp {{ number_format($transaksi->total_nilai, 0, ',', '.') }}
                      </span>
                    </td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>

          {{-- Catatan --}}
          <div class="mb-4">
            <label class="form-label fw-semibold" style="font-size:13px">
              Catatan <small class="text-muted">(opsional)</small>
            </label>
            <textarea name="catatan" class="form-control" rows="2"
                      placeholder="Catatan tambahan...">{{ old('catatan', $transaksi->catatan) }}</textarea>
          </div>

          <div class="d-flex gap-2">
            <button type="submit" class="btn px-4"
                    style="background:#1d4ed8;color:#fff;border-radius:9px;font-size:14px">
              <i class="bi bi-check-lg me-1"></i>Simpan Perubahan
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
// ── Fungsi init Tom Select untuk select kategori ─────────
function initTomSelectKategori(el) {
  return new TomSelect(el, {
    placeholder: '-- Pilih Kategori --',
    allowEmptyOption: true,
    create: false,
    onChange: function() { hitungNilai(); }
  });
}

// Init semua baris existing
document.querySelectorAll('.sel-kategori').forEach(el => {
  initTomSelectKategori(el);
});

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

  const td1 = document.createElement('td');
  const select = document.createElement('select');
  select.name = 'kategori_id[]';
  select.id = randId;
  select.required = true;

  const emptyOpt = document.createElement('option');
  emptyOpt.value = '';
  emptyOpt.textContent = '-- Pilih Kategori --';
  select.appendChild(emptyOpt);

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

  tr.appendChild(td1); tr.appendChild(td2); tr.appendChild(td3); tr.appendChild(td4);
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
  initTomSelectKategori(select);
  document.querySelectorAll('.btn-hapus').forEach(b => b.style.display = 'block');
  bindHapus();
});

// ── Hapus baris ───────────────────────────────────────────
function bindHapus() {
  document.querySelectorAll('.btn-hapus').forEach(btn => {
    btn.onclick = function() {
      const tr = this.closest('tr');
      const sel = tr.querySelector('[name="kategori_id[]"]');
      if (sel && sel.tomselect) sel.tomselect.destroy();
      tr.remove();
      const btns = document.querySelectorAll('.btn-hapus');
      if (btns.length === 1) btns[0].style.display = 'none';
      hitungNilai();
    };
  });
}
bindHapus();

// Bind input berat pada baris existing
document.querySelectorAll('.inp-berat').forEach(el => {
  el.addEventListener('input', hitungNilai);
  el.addEventListener('change', hitungNilai);
});

// Tampilkan/sembunyikan tombol hapus berdasarkan jumlah baris
if (document.querySelectorAll('.baris-detail').length > 1) {
  document.querySelectorAll('.btn-hapus').forEach(b => b.style.display = 'block');
}
</script>
@endpush
