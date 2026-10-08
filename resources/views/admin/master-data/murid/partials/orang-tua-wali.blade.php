{{-- Orang tua/wali murid. Membutuhkan $murid (dengan relasi orangTuaWali), $pilihanHubungan, $pilihanOrangTuaWali. --}}
<div class="card" id="orang-tua-wali">
    <div class="card-header">
        <div>
            <div class="card-title">Orang Tua/Wali</div>
            <div class="card-subtitle">Murid bersaudara dapat ditautkan ke data orang tua yang sama.</div>
        </div>
    </div>

    @if ($murid->orangTuaWali->isEmpty())
        <x-admin.kosong judul="Belum ada orang tua/wali yang ditautkan" />
    @else
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Hubungan</th>
                        <th>Nama</th>
                        <th>Nomor HP</th>
                        <th>Pekerjaan</th>
                        <th class="kolom-aksi">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($murid->orangTuaWali->sortBy(fn ($orangTua) => $orangTua->pivot->hubungan->value) as $orangTua)
                        <tr>
                            <td>{{ $orangTua->pivot->hubungan->label() }}</td>
                            <td class="cell-strong">{{ $orangTua->nama }}</td>
                            <td>{{ $orangTua->nomor_hp ?? '—' }}</td>
                            <td>{{ $orangTua->pekerjaan?->label() ?? '—' }}</td>
                            <td class="kolom-aksi">
                                <a href="{{ route('admin.master-data.orang-tua-wali.edit', $orangTua) }}" class="btn btn-warning btn-sm">Ubah data</a>
                                <form method="POST" class="form-inline"
                                    action="{{ route('admin.master-data.murid.orang-tua-wali.destroy', [$murid, $orangTua]) }}"
                                    data-konfirmasi="Lepas tautan {{ $orangTua->nama }} dari murid ini?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-ghost btn-sm">Lepas</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @if ($pilihanHubungan->isNotEmpty())
        <form method="POST" action="{{ route('admin.master-data.murid.orang-tua-wali.store', $murid) }}" class="card-footer">
            @csrf
            <div class="form-row cols-3">
                <x-admin.select name="hubungan" label="Hubungan" required :pilihan="$pilihanHubungan" />
                <x-admin.select name="id_orang_tua_wali" label="Pilih data orang tua/wali yang sudah ada" required
                    :pilihan="$pilihanOrangTuaWali" kosong="— Pilih —" />
                <div class="form-group filter-aksi">
                    <button type="submit" class="btn btn-outline">Tautkan</button>
                </div>
            </div>
            <div class="form-hint">
                Belum ada datanya?
                @foreach ($pilihanHubungan as $nilai => $teks)
                    <a href="{{ route('admin.master-data.orang-tua-wali.create', ['murid' => $murid->id_murid, 'hubungan' => $nilai]) }}">Tambah {{ strtolower($teks) }} baru</a>@if (! $loop->last) · @endif
                @endforeach
            </div>
        </form>
    @endif
</div>
