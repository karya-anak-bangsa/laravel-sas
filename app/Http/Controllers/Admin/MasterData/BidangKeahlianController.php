<?php

namespace App\Http\Controllers\Admin\MasterData;

use App\Actions\MasterData\HapusBidangKeahlian;
use App\Http\Controllers\Controller;
use App\Http\Requests\MasterData\BidangKeahlianRequest;
use App\Models\BidangKeahlian;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class BidangKeahlianController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', BidangKeahlian::class);

        return view('admin.master-data.bidang-keahlian.index', [
            'daftarBidangKeahlian' => BidangKeahlian::query()
                ->withCount('programKeahlian')
                ->orderBy('nama')
                ->orderBy('id_bidang_keahlian')
                ->paginate(15),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', BidangKeahlian::class);

        return view('admin.master-data.bidang-keahlian.form', [
            'bidangKeahlian' => new BidangKeahlian,
        ]);
    }

    public function store(BidangKeahlianRequest $request): RedirectResponse
    {
        BidangKeahlian::create($request->validated());

        return redirect()->route('admin.master-data.bidang-keahlian.index')
            ->with('status', 'Bidang keahlian berhasil ditambahkan.');
    }

    public function edit(BidangKeahlian $bidangKeahlian): View
    {
        Gate::authorize('update', $bidangKeahlian);

        return view('admin.master-data.bidang-keahlian.form', [
            'bidangKeahlian' => $bidangKeahlian,
        ]);
    }

    public function update(BidangKeahlianRequest $request, BidangKeahlian $bidangKeahlian): RedirectResponse
    {
        $bidangKeahlian->update($request->validated());

        return redirect()->route('admin.master-data.bidang-keahlian.index')
            ->with('status', 'Bidang keahlian berhasil diperbarui.');
    }

    public function destroy(BidangKeahlian $bidangKeahlian, HapusBidangKeahlian $hapus): RedirectResponse
    {
        Gate::authorize('delete', $bidangKeahlian);

        $hapus->handle($bidangKeahlian);

        return redirect()->route('admin.master-data.bidang-keahlian.index')
            ->with('status', 'Bidang keahlian berhasil dihapus.');
    }
}
