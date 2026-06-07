@extends('layouts.nasabah')
@section('title','Ajukan Penarikan Dana')

@section('content')

<div class="row justify-content-center">
  <div class="col-md-7 col-lg-6">

    {{-- Header --}}
    <div class="d-flex align-items-center gap-2 mb-4">
      <a href="{{ route('nasabah.penarikan.index') }}"
         style="color:#64748b;text-decoration:none;font-size:13px">
        <i class="bi bi-arrow-left me-1"></i>Kembali
      </a>
    </div>

    {{-- Saldo Card --}}
    <div class="saldo-card mb-4" style="padding:20px 24px">
      <div style="font-size:12px;opacity:.8;margin-bottom:4px">Saldo Tabungan Anda</div>
      <div style="font-size:30px;font-weight:800" id="displaySaldo">
        Rp {{ number_format($nasabah->saldo, 0, ',', '.') }}
      </div>
      <div style="font-size:11px;opacity:.65;margin-top:4px">{{ $nasabah->user->name }} · {{ $nasabah->nik }}</div>
    </div>

    {{-- Form --}}
    <div class="table-card" style="padding:28px">
      <h6 style="font-weight:700;color:#0f172a;margin-bottom:20px">
        <i class="bi bi-cash-stack me-2" style="color:#16a34a"></i>Form Pengajuan Penarikan
      </h6>

      @if($errors->any())
        <div class="alert alert-danger mb-3">
          <ul class="mb-0 ps-3">
            @foreach($errors->all() as $e)
              <li style="font-size:13px">{{ $e }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form method="POST" action="{{ route('nasabah.penarikan.store') }}" id="formPenarikan">
        @csrf

        {{-- Jenis Penarikan --}}
        <div class="mb-4">
          <label class="form-label fw-semibold" style="font-size:13px;color:#374151">Jenis Pencairan</label>
          <div class="d-flex flex-column gap-2">
            <label class="d-flex align-items-center gap-3 p-3 pilihan-label" id="lblSegera"
                   style="border:1.5px solid #e2e8f0;border-radius:10px;cursor:pointer;background:#fff;transition:all .15s">
              <input type="radio" name="jenis" value="segera"
                     id="rdSegera" {{ old('jenis','segera') === 'segera' ? 'checked' : '' }}
                     style="accent-color:#16a34a;width:16px;height:16px">
              <div>
                <div style="font-size:13px;font-weight:600;color:#0f172a">
                  <i class="bi bi-lightning-charge me-1" style="color:#2563eb"></i>Cair Sekarang
                </div>
                <div style="font-size:11px;color:#64748b">Dana ingin langsung dicairkan, admin akan memprosesnya</div>
              </div>
            </label>

            <label class="d-flex align-items-center gap-3 p-3 pilihan-label" id="lblTerjadwal"
                   style="border:1.5px solid #e2e8f0;border-radius:10px;cursor:pointer;background:#fff;transition:all .15s">
              <input type="radio" name="jenis" value="terjadwal"
                     id="rdTerjadwal" {{ old('jenis') === 'terjadwal' ? 'checked' : '' }}
                     style="accent-color:#16a34a;width:16px;height:16px">
              <div>
                <div style="font-size:13px;font-weight:600;color:#0f172a">
                  <i class="bi bi-calendar-event me-1" style="color:#7c3aed"></i>Jadwalkan
                </div>
                <div style="font-size:11px;color:#64748b">Pilih tanggal agenda pencairan berikutnya</div>
              </div>
            </label>
          </div>
        </div>

        {{-- Input tanggal (muncul jika terjadwal) --}}
        <div id="wrapTanggal" class="mb-3" style="{{ old('jenis') === 'terjadwal' ? '' : 'display:none' }}">
          <label class="form-label" style="font-size:12px;font-weight:600;color:#475569">Tanggal Pencairan</label>
          <input type="date" name="tanggal_diminta" id="tanggalDiminta"
                 class="form-control" style="font-size:13px;max-width:220px"
                 min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                 value="{{ old('tanggal_diminta') }}">
          <div style="font-size:11px;color:#94a3b8;margin-top:4px">
            Sesuaikan dengan jadwal agenda bank sampah berikutnya
          </div>
        </div>

        {{-- Nominal --}}
        <div class="mb-3">
          <label class="form-label fw-semibold" style="font-size:13px;color:#374151">
            Nominal Penarikan
          </label>
          <div class="input-group">
            <span class="input-group-text" style="font-size:13px;background:#f8fafc;color:#64748b">Rp</span>
            <input type="number" name="jumlah" id="inputJumlah"
                   class="form-control" style="font-size:13px"
                   placeholder="Masukkan nominal..."
                   min="10000"
                   max="{{ $nasabah->saldo }}"
                   step="1000"
                   value="{{ old('jumlah') }}"
                   required>
          </div>
          <div class="d-flex justify-content-between mt-1">
            <span style="font-size:11px;color:#94a3b8">Minimal: Rp 10.000</span>
            <span style="font-size:11px;color:#94a3b8">
              Maksimal: <strong style="color:#16a34a">Rp {{ number_format($nasabah->saldo, 0, ',', '.') }}</strong>
            </span>
          </div>

          {{-- Tombol cepat --}}
          <div class="d-flex gap-2 mt-2 flex-wrap">
            @foreach([25,50,75,100] as $pct)
              <button type="button" class="btn btn-sm btn-pct"
                      data-pct="{{ $pct }}"
                      style="font-size:11px;background:#f1f5f9;border-radius:6px;border:1px solid #e2e8f0">
                {{ $pct }}%
              </button>
            @endforeach
          </div>
        </div>

        {{-- Catatan --}}
        <div class="mb-4">
          <label class="form-label" style="font-size:13px;color:#374151">
            Catatan <small class="text-muted">(opsional)</small>
          </label>
          <textarea name="catatan_nasabah" class="form-control" rows="2"
                    style="font-size:13px"
                    placeholder="Tuliskan keterangan jika ada...">{{ old('catatan_nasabah') }}</textarea>
        </div>

        <div class="d-flex gap-2">
          <button type="submit" class="btn px-4"
                  style="background:linear-gradient(135deg,#16a34a,#15803d);color:#fff;border-radius:9px;font-size:13px"
                  id="btnSubmit">
            <i class="bi bi-send me-1"></i>Kirim Pengajuan
          </button>
          <a href="{{ route('nasabah.penarikan.index') }}"
             class="btn px-4" style="background:#f1f5f9;border-radius:9px;font-size:13px">
            Batal
          </a>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
const maxSaldo = {{ $nasabah->saldo }};

// Highlight pilihan aktif
function updateJenisUI() {
  const rdS = document.getElementById('rdSegera');
  const rdT = document.getElementById('rdTerjadwal');
  const lblS = document.getElementById('lblSegera');
  const lblT = document.getElementById('lblTerjadwal');

  [{ rd: rdS, lbl: lblS }, { rd: rdT, lbl: lblT }].forEach(({ rd, lbl }) => {
    if (rd.checked) {
      lbl.style.borderColor = '#16a34a';
      lbl.style.background  = '#f0fdf4';
    } else {
      lbl.style.borderColor = '#e2e8f0';
      lbl.style.background  = '#fff';
    }
  });
  document.getElementById('wrapTanggal').style.display = rdT.checked ? 'block' : 'none';
}
document.querySelectorAll('input[name="jenis"]').forEach(r => r.addEventListener('change', updateJenisUI));
updateJenisUI();

// Tombol persentase cepat
document.querySelectorAll('.btn-pct').forEach(btn => {
  btn.addEventListener('click', function() {
    const pct = parseInt(this.dataset.pct);
    const nominal = Math.floor((maxSaldo * pct / 100) / 1000) * 1000;
    document.getElementById('inputJumlah').value = nominal;
    document.querySelectorAll('.btn-pct').forEach(b => {
      b.style.background   = '#f1f5f9';
      b.style.borderColor  = '#e2e8f0';
      b.style.color        = '';
    });
    this.style.background  = '#dcfce7';
    this.style.borderColor = '#16a34a';
    this.style.color       = '#166534';
  });
});
</script>
@endpush
