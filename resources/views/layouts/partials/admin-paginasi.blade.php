{{-- Tampilan paginasi area admin: $paginator->links('layouts.partials.admin-paginasi') --}}
@if ($paginator->hasPages())
    <div class="paginasi">
        <span class="paginasi-info">
            Menampilkan {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} dari {{ $paginator->total() }} data
        </span>

        <nav class="pagination" aria-label="Navigasi halaman">
            @if ($paginator->onFirstPage())
                <span class="page-btn" aria-disabled="true">&lsaquo;</span>
            @else
                <a class="page-btn" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Sebelumnya">&lsaquo;</a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="page-ellipsis">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $halaman => $url)
                        @if ($halaman == $paginator->currentPage())
                            <span class="page-btn active" aria-current="page">{{ $halaman }}</span>
                        @else
                            <a class="page-btn" href="{{ $url }}">{{ $halaman }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a class="page-btn" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Berikutnya">&rsaquo;</a>
            @else
                <span class="page-btn" aria-disabled="true">&rsaquo;</span>
            @endif
        </nav>
    </div>
@endif
