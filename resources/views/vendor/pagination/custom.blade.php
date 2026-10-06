@if ($paginator->hasPages())
    <nav class="custom-pagination" role="navigation" aria-label="Navigasi Halaman Data">
        <ul class="pagination-nav-list">
            {{-- Tombol Sebelumnya --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true" aria-label="Sebelumnya">
                    <span class="page-link btn-prev" aria-hidden="true">
                        <span class="ms" style="font-size:18px;">chevron_left</span>
                        <span>Sebelumnya</span>
                    </span>
                </li>
            @else
                <li class="page-item">
                    <a href="{{ $paginator->previousPageUrl() }}" class="page-link btn-prev" rel="prev" aria-label="Sebelumnya">
                        <span class="ms" style="font-size:18px;">chevron_left</span>
                        <span>Sebelumnya</span>
                    </a>
                </li>
            @endif

            {{-- Nomor Halaman --}}
            @foreach ($elements as $element)
                {{-- Separator Titik-titik (...) --}}
                @if (is_string($element))
                    <li class="page-item disabled separator" aria-disabled="true">
                        <span class="page-link dots">{{ $element }}</span>
                    </li>
                @endif

                {{-- Link Halaman Berurutan --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page">
                                <span class="page-link current">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a href="{{ $url }}" class="page-link">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Tombol Berikutnya --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a href="{{ $paginator->nextPageUrl() }}" class="page-link btn-next" rel="next" aria-label="Berikutnya">
                        <span>Berikutnya</span>
                        <span class="ms" style="font-size:18px;">chevron_right</span>
                    </a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true" aria-label="Berikutnya">
                    <span class="page-link btn-next" aria-hidden="true">
                        <span>Berikutnya</span>
                        <span class="ms" style="font-size:18px;">chevron_right</span>
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif
