@props(['paginator'])

@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between">
        <!-- Mobile Pagination -->
        <div class="flex justify-between flex-1 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-400 !text-gray-400 bg-gray-100 border border-gray-300 cursor-default leading-5 rounded-lg">
                    <i class="fas fa-chevron-left mr-2"></i>
                    Sebelumnya
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" 
                   class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 !text-gray-700 bg-white border border-gray-300 leading-5 rounded-lg hover:bg-gray-50 hover:!text-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-300 transition-all duration-200">
                    <i class="fas fa-chevron-left mr-2"></i>
                    Sebelumnya
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" 
                   class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-700 !text-gray-700 bg-white border border-gray-300 leading-5 rounded-lg hover:bg-gray-50 hover:!text-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-300 transition-all duration-200">
                    Selanjutnya
                    <i class="fas fa-chevron-right ml-2"></i>
                </a>
            @else
                <span class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-400 !text-gray-400 bg-gray-100 border border-gray-300 cursor-default leading-5 rounded-lg">
                    Selanjutnya
                    <i class="fas fa-chevron-right ml-2"></i>
                </span>
            @endif
        </div>

        <!-- Desktop Pagination -->
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-700 leading-5">
                    Menampilkan
                    <span class="font-medium">{{ $paginator->firstItem() }}</span>
                    sampai
                    <span class="font-medium">{{ $paginator->lastItem() }}</span>
                    dari
                    <span class="font-medium">{{ $paginator->total() }}</span>
                    artikel
                </p>
            </div>

            <div>
                <span class="relative z-0 inline-flex shadow-sm rounded-xl">
                    <!-- Previous Page Link -->
                    @if ($paginator->onFirstPage())
                        <span class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-400 !text-gray-400 bg-gray-100 border border-gray-300 cursor-default rounded-l-xl leading-5">
                            <i class="fas fa-chevron-left"></i>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" 
                           rel="prev" 
                           class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-600 !text-gray-600 bg-white border border-gray-300 rounded-l-xl leading-5 hover:bg-gray-50 hover:!text-blue-600 focus:z-10 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-300 transition-all duration-200" 
                           aria-label="Previous">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    @endif

                    <!-- Pagination Elements -->
                    @foreach ($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-bold text-white !text-white bg-blue-600 border border-blue-600 cursor-default leading-5">
                                {{ $page }}
                            </span>
                        @elseif ($page === 1 || $page === $paginator->lastPage() || ($page >= $paginator->currentPage() - 2 && $page <= $paginator->currentPage() + 2))
                            <a href="{{ $url }}" 
                               class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 !text-gray-700 bg-white border border-gray-300 leading-5 hover:bg-gray-50 hover:!text-blue-600 focus:z-10 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-300 transition-all duration-200">
                                {{ $page }}
                            </a>
                        @elseif ($page === $paginator->currentPage() - 3 || $page === $paginator->currentPage() + 3)
                            <span class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default leading-5">
                                ...
                            </span>
                        @endif
                    @endforeach

                    <!-- Next Page Link -->
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" 
                           rel="next" 
                           class="relative inline-flex items-center px-3 py-2 -ml-px text-sm font-medium text-gray-600 !text-gray-600 bg-white border border-gray-300 rounded-r-xl leading-5 hover:bg-gray-50 hover:!text-blue-600 focus:z-10 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-300 transition-all duration-200" 
                           aria-label="Next">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    @else
                        <span class="relative inline-flex items-center px-3 py-2 -ml-px text-sm font-medium text-gray-400 !text-gray-400 bg-gray-100 border border-gray-300 cursor-default rounded-r-xl leading-5">
                            <i class="fas fa-chevron-right"></i>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif