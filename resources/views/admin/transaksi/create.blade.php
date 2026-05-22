@extends('layouts.admin')
@section('title','Catat Setoran')
@section('page-title','Catat Transaksi Setoran Sampah')

@section('content')
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
<script>
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
  tbody.appendChild(buatBaris());
  // Tampilkan semua tombol hapus jika baris > 1
  const hapusBtns = document.querySelectorAll('.btn-hapus');
  hapusBtns.forEach(b => b.style.display = 'block');
  bindEvents();
});

// ── Hapus baris ─────────────────────────────────────────
function bindEvents() {
  document.querySelectorAll('.btn-hapus').forEach(btn => {
    btn.onclick = function() {
      this.closest('tr').remove();
      const hapusBtns = document.querySelectorAll('.btn-hapus');
      if (hapusBtns.length === 1) hapusBtns[0].style.display = 'none';
      hitungNilai();
    };
  });
  document.querySelectorAll('.sel-kategori, .inp-berat').forEach(el => {
    el.oninput = hitungNilai;
    el.onchange = hitungNilai;
  });
}
bindEvents();

// ── Info saldo nasabah ───────────────────────────────────
document.getElementById('selectNasabah').addEventListener('change', function() {
  const opt = this.selectedOptions[0];
  const saldo = opt.dataset.saldo;
  if (saldo) {
    document.getElementById('textSaldo').textContent = 'Rp ' + saldo;
    document.getElementById('infoSaldo').style.display = 'block';
  } else {
    document.getElementById('infoSaldo').style.display = 'none';
  }
});
</script>
@endpush