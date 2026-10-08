@extends('layouts.admin')

@section('judul', 'Anggota Rombel '.$rombel->nama)

@section('konten')
    <x-admin.header-halaman
        :pretitle="'Rombel · '.($rombel->satuanPendidikan?->nama ?? '—').' · '.($rombel->tahunAjaran?->nama ?? '—')"
        :judul="'Anggota '.$rombel->nama">
        <a href="{{ route('admin.master-data.rombel.index', ['tahun_ajaran' => $rombel->id_tahun_ajaran]) }}" class="btn btn-outline"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Kembali ke daftar rombel</a>
    </x-admin.header-halaman>

    @can('update', $rombel)
        <form method="POST" action="{{ route('admin.master-data.rombel.anggota.store', $rombel) }}" class="card">
            @csrf
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label" for="id_murid">Tambah murid</label>
                    @if ($pilihanMurid->isEmpty())
                        <div class="form-hint">Semua murid sudah memiliki rombel pada tahun ajaran {{ $rombel->tahunAjaran?->nama }}.</div>
                    @else
                        <select id="id_murid" name="id_murid[]" multiple size="8"
                            @class(['form-control', 'is-invalid' => $errors->has('id_murid') || $errors->has('id_murid.*')])>
                            @foreach ($pilihanMurid as $id => $teks)
                                <option value="{{ $id }}" @selected(in_array((string) $id, (array) old('id_murid', []), true))>{{ $teks }}</option>
                            @endforeach
                        </select>
                        <div class="form-hint">Hanya murid yang belum memiliki rombel pada tahun ajaran ini. Tahan Ctrl (atau Cmd) untuk memilih beberapa.</div>
                    @endif
                    @error('id_murid')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                    @error('id_murid.*')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            @if ($pilihanMurid->isNotEmpty())
                <div class="card-footer">
                    <button type="submit" class="btn btn-success"><i class="fa-solid fa-user-plus" aria-hidden="true"></i> Tambahkan ke rombel</button>
                </div>
            @endif
        </form>
    @endcan

    <div class="card">
        <div class="card-header">
            <div class="card-title">Daftar anggota ({{ $daftarAnggota->total() }} murid)</div>
        </div>

        @if ($daftarAnggota->isEmpty())
            <x-admin.kosong judul="Rombel ini belum memiliki anggota" />
        @else
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nama lengkap</th>
                            <th>NISN</th>
                            <th>Jenis kelamin</th>
                            <th class="kolom-aksi">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($daftarAnggota as $murid)
                            <tr>
                                <td>{{ $daftarAnggota->firstItem() + $loop->index }}</td>
                                <td class="cell-strong">{{ $murid->nama_lengkap }}</td>
                                <td>{{ $murid->nisn ?? '—' }}</td>
                                <td>{{ $murid->jenis_kelamin->label() }}</td>
                                <td class="kolom-aksi">
                                    @can('update', $rombel)
                                        <form method="POST" class="form-inline"
                                            action="{{ route('admin.master-data.rombel.anggota.destroy', [$rombel, $murid]) }}"
                                            data-konfirmasi="Keluarkan {{ $murid->nama_lengkap }} dari rombel {{ $rombel->nama }}?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-ghost btn-sm"><i class="fa-solid fa-user-minus" aria-hidden="true"></i> Keluarkan</button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @if ($daftarAnggota->hasPages())
            <div class="card-footer">
                {{ $daftarAnggota->links('layouts.partials.admin-paginasi') }}
            </div>
        @endif
    </div>
@endsection
