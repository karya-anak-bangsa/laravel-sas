@extends('layouts.admin')

@php($sedangMengubah = $murid->exists)

@section('judul', $sedangMengubah ? 'Ubah Murid' : 'Tambah Murid')

@section('konten')
    <x-admin.header-halaman pretitle="Master Data" :judul="$sedangMengubah ? 'Ubah Murid' : 'Tambah Murid'" />

    <form method="POST" class="card"
        action="{{ $sedangMengubah ? route('admin.master-data.murid.update', $murid) : route('admin.master-data.murid.store') }}">
        @csrf
        @if ($sedangMengubah)
            @method('PUT')
        @endif

        <div class="card-body">
            <h2 class="card-title">Identitas</h2>
            <x-admin.input name="nama_lengkap" label="Nama lengkap" :value="$murid->nama_lengkap" required maxlength="150" />
            <div class="form-row cols-3">
                <x-admin.select name="jenis_kelamin" label="Jenis kelamin" required
                    :pilihan="$pilihanJenisKelamin" :terpilih="$murid->jenis_kelamin" />
                <x-admin.input name="tempat_lahir" label="Tempat lahir" :value="$murid->tempat_lahir" maxlength="100" />
                <x-admin.input type="date" name="tanggal_lahir" label="Tanggal lahir" required
                    :value="$murid->tanggal_lahir?->format('Y-m-d')" />
            </div>
            <div class="form-row cols-3">
                <x-admin.input name="nisn" label="NISN" :value="$murid->nisn" inputmode="numeric" maxlength="10" bantuan="10 digit." />
                <x-admin.input name="nik" label="NIK" :value="$murid->nik" inputmode="numeric" maxlength="16" bantuan="16 digit." />
                <x-admin.input name="no_kk" label="Nomor KK" :value="$murid->no_kk" inputmode="numeric" maxlength="16" bantuan="16 digit." />
            </div>
            <div class="form-row cols-3">
                <x-admin.input name="no_seri_ijazah" label="No. seri ijazah" :value="$murid->no_seri_ijazah" maxlength="50" />
                <x-admin.input name="no_seri_skhus" label="No. seri SKHUS" :value="$murid->no_seri_skhus" maxlength="50" />
                <x-admin.select name="agama" label="Agama" :pilihan="$pilihanAgama" :terpilih="$murid->agama" />
            </div>

            <div class="form-group">
                <div class="form-label">Berkebutuhan khusus</div>
                <div class="pilihan-kolom">
                    @foreach ($pilihanBerkebutuhanKhusus as $kebutuhan)
                        <label class="form-check">
                            <input type="checkbox" name="berkebutuhan_khusus[]" value="{{ $kebutuhan->value }}" @checked(in_array($kebutuhan->value, $kebutuhanTerpilih, true))>
                            {{ $kebutuhan->label() }}
                        </label>
                    @endforeach
                </div>
                <div class="form-hint">Boleh lebih dari satu. Biarkan kosong jika tidak ada.</div>
                @error('berkebutuhan_khusus.*')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <h2 class="card-title">Alamat</h2>
            <x-admin.input name="alamat_jalan" label="Alamat jalan" :value="$murid->alamat_jalan" maxlength="255" />
            <div class="form-row cols-3">
                <x-admin.input name="rt" label="RT" :value="$murid->rt" inputmode="numeric" maxlength="3" />
                <x-admin.input name="rw" label="RW" :value="$murid->rw" inputmode="numeric" maxlength="3" />
                <x-admin.input name="dusun" label="Dusun" :value="$murid->dusun" maxlength="100" />
            </div>
            <div class="form-row cols-3">
                <x-admin.input name="kelurahan_desa" label="Kelurahan/desa" :value="$murid->kelurahan_desa" maxlength="100" />
                <x-admin.input name="kecamatan" label="Kecamatan" :value="$murid->kecamatan" maxlength="100" />
                <x-admin.input name="kode_pos" label="Kode pos" :value="$murid->kode_pos" inputmode="numeric" maxlength="5" />
            </div>
            <div class="form-row">
                <x-admin.select name="tempat_tinggal" label="Tempat tinggal"
                    :pilihan="$pilihanTempatTinggal" :terpilih="$murid->tempat_tinggal" />
                <x-admin.select name="moda_transportasi" label="Moda transportasi"
                    :pilihan="$pilihanModaTransportasi" :terpilih="$murid->moda_transportasi" />
            </div>

            <h2 class="card-title">Kontak dan bantuan</h2>
            <div class="form-row">
                <x-admin.input type="tel" name="nomor_hp" label="Nomor HP (WhatsApp)" :value="$murid->nomor_hp"
                    inputmode="tel" maxlength="20" placeholder="081234567890" />
                <x-admin.input type="email" name="email" label="Email" :value="$murid->email" maxlength="255" />
            </div>
            <div class="form-row">
                <x-admin.input name="no_kps_pkh" label="No. KPS/PKH" :value="$murid->no_kps_pkh" maxlength="50" />
                <x-admin.input name="no_kip" label="Nomor KIP" :value="$murid->no_kip" maxlength="50" />
            </div>
        </div>

        <div class="card-footer aksi-form">
            <button type="submit" class="btn btn-success">Simpan</button>
            <a href="{{ route('admin.master-data.murid.index') }}" class="btn btn-secondary">Kembali ke daftar</a>
        </div>
    </form>

    @if ($sedangMengubah)
        @include('admin.master-data.murid.partials.orang-tua-wali')
    @endif
@endsection
