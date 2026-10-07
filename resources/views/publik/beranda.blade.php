@extends('layouts.publik')

@section('konten')
    <section class="bg-emerald-50">
        <div class="mx-auto max-w-6xl px-4 py-12 sm:py-16">
            <h1 class="text-2xl font-semibold text-slate-900 sm:text-4xl">Yayasan Puspita Bangsa Ciputat</h1>
            <p class="mt-3 max-w-2xl text-slate-600">SMP Puspita Bangsa dan SMK Puspita Bangsa.</p>
        </div>
    </section>

    <section class="mx-auto grid max-w-6xl gap-4 px-4 py-10 sm:grid-cols-2">
        <article class="rounded-xl border border-slate-200 p-6">
            <h2 class="text-lg font-semibold text-slate-900">SMP Puspita Bangsa</h2>
            <p class="mt-2 text-slate-600">Tingkat VII, VIII, dan IX.</p>
        </article>

        <article class="rounded-xl border border-slate-200 p-6">
            <h2 class="text-lg font-semibold text-slate-900">SMK Puspita Bangsa</h2>
            <p class="mt-2 text-slate-600">Tingkat X, XI, dan XII dengan lima konsentrasi keahlian:</p>
            <ul class="mt-3 list-inside list-disc space-y-1 text-slate-600">
                <li>Perhotelan</li>
                <li>Rekayasa Perangkat Lunak</li>
                <li>Manajemen Perkantoran</li>
                <li>Bisnis Retail</li>
                <li>Akuntansi</li>
            </ul>
        </article>
    </section>
@endsection
