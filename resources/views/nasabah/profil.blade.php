@extends('layouts.nasabah')
@section('title','Profil Saya')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="stat-card">
            <h5 class="mb-4 text-center">Edit Profil</h5>
            
            @if($errors->any())
                <div class="alert alert-danger mb-4">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('nasabah.profil.update') }}">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label class="form-label text-muted" style="font-size: 13px;">Nama Lengkap</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label text-muted" style="font-size: 13px;">Email</label>
                        <input type="email" class="form-control bg-light" value="{{ $user->email }}" disabled>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted" style="font-size: 13px;">NIK</label>
                        <input type="text" class="form-control bg-light" value="{{ $nasabah->nik }}" disabled>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted" style="font-size: 13px;">No. HP <small>(Opsional)</small></label>
                    <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp', $nasabah->no_hp) }}">
                </div>

                <div class="mb-4">
                    <label class="form-label text-muted" style="font-size: 13px;">Alamat Lengkap <small>(Opsional)</small></label>
                    <textarea name="alamat" class="form-control" rows="3">{{ old('alamat', $nasabah->alamat) }}</textarea>
                </div>

                <hr class="my-4">
                <h6 class="mb-3">Ubah Password </h6>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label text-muted" style="font-size: 13px;">Password Baru</label>
                        <div class="input-group">
                            <input type="password" name="password" class="form-control" placeholder="Biarkan kosong jika tidak ingin mengubah">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword(this)" style="border-color: #e2e8f0;">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted" style="font-size: 13px;">Konfirmasi Password Baru</label>
                        <div class="input-group">
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password baru">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword(this)" style="border-color: #e2e8f0;">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn" style="background:#16a34a;color:#fff;border-radius:8px;padding:10px 20px;">
                        <i class="bi bi-save me-2"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function togglePassword(btn) {
    const input = btn.previousElementSibling;
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}
</script>
@endpush
