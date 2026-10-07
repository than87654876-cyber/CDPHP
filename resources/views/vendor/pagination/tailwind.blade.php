@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
        {{-- Mobile Controls --}}
        <div class="flex items-center justify-between w-full sm:hidden gap-2">
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center px-3 py-1.5 font-bold text-slate-400 bg-slate-100 border border-slate-200 cursor-not-allowed rounded-xl text-xs">
                    &laquo; Trước
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="inline-flex items-center px-3 py-1.5 font-bold text-slate-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 text-xs shadow-2xs transition-colors">
                    &laquo; Trước
                </a>
            @endif

            <span class="text-xs font-semibold text-slate-500">
                Trang {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}
            </span>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="inline-flex items-center px-3 py-1.5 font-bold text-slate-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 text-xs shadow-2xs transition-colors">
                    Sau &raquo;
                </a>
            @else
                <span class="inline-flex items-center px-3 py-1.5 font-bold text-slate-400 bg-slate-100 border border-slate-200 cursor-not-allowed rounded-xl text-xs">
                    Sau &raquo;
                </span>
            @endif
        </div>

        {{-- Desktop / Tablet Controls --}}
        <div class="hidden sm:flex sm:items-center sm:justify-between w-full">
            <div>
                <p class="text-xs text-slate-500 font-medium">
                    Hiển thị 
                    <span class="font-extrabold text-slate-800">{{ $paginator->firstItem() ?? 0 }}</span>
                    đến 
                    <span class="font-extrabold text-slate-800">{{ $paginator->lastItem() ?? 0 }}</span>
                    trong 
                    <span class="font-extrabold text-slate-800">{{ $paginator->total() }}</span> 
                    kết quả
                </p>
            </div>

            <div class="flex items-center gap-1">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <span aria-disabled="true" aria-label="Trang trước">
                        <span class="inline-flex items-center justify-center w-8 h-8 text-slate-300 bg-slate-50 border border-slate-200/60 rounded-xl cursor-not-allowed" aria-hidden="true">
                            <i class="fas fa-chevron-left text-[10px]"></i>
                        </span>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center justify-center w-8 h-8 text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-100 hover:text-slate-900 transition-colors shadow-2xs" aria-label="Trang trước">
                        <i class="fas fa-chevron-left text-[10px]"></i>
                    </a>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <span aria-disabled="true">
                            <span class="inline-flex items-center justify-center w-8 h-8 text-slate-400 font-bold text-xs select-none">{{ $element }}</span>
                        </span>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page">
                                    <span class="inline-flex items-center justify-center min-w-8 h-8 px-2.5 font-black text-xs text-white bg-[#ee4d2d] rounded-xl shadow-xs cursor-default">
                                        {{ $page }}
                                    </span>
                                </span>
                            @else
                                <a href="{{ $url }}" class="inline-flex items-center justify-center min-w-8 h-8 px-2.5 font-bold text-xs text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-100 hover:text-slate-900 transition-colors shadow-2xs" aria-label="Trang {{ $page }}">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center justify-center w-8 h-8 text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-100 hover:text-slate-900 transition-colors shadow-2xs" aria-label="Trang sau">
                        <i class="fas fa-chevron-right text-[10px]"></i>
                    </a>
                @else
                    <span aria-disabled="true" aria-label="Trang sau">
                        <span class="inline-flex items-center justify-center w-8 h-8 text-slate-300 bg-slate-50 border border-slate-200/60 rounded-xl cursor-not-allowed" aria-hidden="true">
                            <i class="fas fa-chevron-right text-[10px]"></i>
                        </span>
                    </span>
                @endif
            </div>
        </div>
    </nav>
@endif
