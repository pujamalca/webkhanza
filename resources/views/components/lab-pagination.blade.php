@props(['paginator'])

@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between">
        <!-- Mobile Pagination -->
        <div class="flex justify-between flex-1 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-400 !text-gray-400 bg-gray-100 border border-gray-300 cursor-default leading-5 rounded-lg" x-bind:class="darkMode ? 'bg-gray-700 border-gray-600 text-gray-500' : 'bg-gray-100 border-gray-300 text-gray-400'">
                    <i class="fas fa-chevron-left mr-2"></i>
                    Sebelumnya
                </span>
            @else
                <button type="button"
                        wire:click="previousPage('templates')"
                        class="relative inline-flex items-center px-4 py-2 text-sm font-medium leading-5 rounded-lg transition-all duration-200"
                        x-bind:class="darkMode ? 'text-gray-300 bg-gray-800 border-gray-600 hover:bg-gray-700 hover:text-gray-200' : 'text-gray-700 bg-white border-gray-300 hover:bg-gray-50 hover:text-gray-600'">
                    <i class="fas fa-chevron-left mr-2"></i>
                    Sebelumnya
                </button>
            @endif

            @if ($paginator->hasMorePages())
                <button type="button"
                        wire:click="nextPage('templates')"
                        class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium leading-5 rounded-lg transition-all duration-200"
                        x-bind:class="darkMode ? 'text-gray-300 bg-gray-800 border-gray-600 hover:bg-gray-700 hover:text-gray-200' : 'text-gray-700 bg-white border-gray-300 hover:bg-gray-50 hover:text-gray-600'">
                    Selanjutnya
                    <i class="fas fa-chevron-right ml-2"></i>
                </button>
            @else
                <span class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium cursor-default leading-5 rounded-lg"
                      x-bind:class="darkMode ? 'bg-gray-700 border-gray-600 text-gray-500' : 'bg-gray-100 border-gray-300 text-gray-400'">
                    Selanjutnya
                    <i class="fas fa-chevron-right ml-2"></i>
                </span>
            @endif
        </div>

        <!-- Desktop Pagination -->
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm leading-5" x-bind:class="darkMode ? 'text-gray-300' : 'text-gray-700'">
                    Menampilkan
                    <span class="font-medium">{{ $paginator->firstItem() }}</span>
                    sampai
                    <span class="font-medium">{{ $paginator->lastItem() }}</span>
                    dari
                    <span class="font-medium">{{ $paginator->total() }}</span>
                    kategori laboratorium
                </p>
            </div>

            <div>
                <span class="relative z-0 inline-flex shadow-sm rounded-xl">
                    <!-- Previous Page Link -->
                    @if ($paginator->onFirstPage())
                        <span class="relative inline-flex items-center px-3 py-2 text-sm font-medium cursor-default rounded-l-xl leading-5"
                              x-bind:class="darkMode ? 'bg-gray-700 border-gray-600 text-gray-500' : 'bg-gray-100 border-gray-300 text-gray-400'">
                            <i class="fas fa-chevron-left"></i>
                        </span>
                    @else
                        <button type="button"
                                wire:click="previousPage('templates')"
                                class="relative inline-flex items-center px-3 py-2 text-sm font-medium rounded-l-xl leading-5 transition-all duration-200"
                                x-bind:class="darkMode ? 'text-gray-300 bg-gray-800 border-gray-600 hover:bg-gray-700 hover:text-blue-400' : 'text-gray-600 bg-white border-gray-300 hover:bg-gray-50 hover:text-blue-600'"
                                aria-label="Previous">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                    @endif

                    <!-- Pagination Elements -->
                    @foreach ($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-bold cursor-default leading-5"
                                  x-bind:class="darkMode ? 'text-white bg-blue-600 border-blue-600' : 'text-white bg-blue-600 border-blue-600'">
                                {{ $page }}
                            </span>
                        @elseif ($page === 1 || $page === $paginator->lastPage() || ($page >= $paginator->currentPage() - 2 && $page <= $paginator->currentPage() + 2))
                            <button type="button"
                                    wire:click="gotoPage({{ $page }}, 'templates')"
                                    class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium leading-5 transition-all duration-200"
                                    x-bind:class="darkMode ? 'text-gray-300 bg-gray-800 border-gray-600 hover:bg-gray-700 hover:text-blue-400' : 'text-gray-700 bg-white border-gray-300 hover:bg-gray-50 hover:text-blue-600'">
                                {{ $page }}
                            </button>
                        @elseif ($page === $paginator->currentPage() - 3 || $page === $paginator->currentPage() + 3)
                            <span class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium cursor-default leading-5"
                                  x-bind:class="darkMode ? 'text-gray-500 bg-gray-800 border-gray-600' : 'text-gray-500 bg-white border-gray-300'">
                                ...
                            </span>
                        @endif
                    @endforeach

                    <!-- Next Page Link -->
                    @if ($paginator->hasMorePages())
                        <button type="button"
                                wire:click="nextPage('templates')"
                                class="relative inline-flex items-center px-3 py-2 -ml-px text-sm font-medium rounded-r-xl leading-5 transition-all duration-200"
                                x-bind:class="darkMode ? 'text-gray-300 bg-gray-800 border-gray-600 hover:bg-gray-700 hover:text-blue-400' : 'text-gray-600 bg-white border-gray-300 hover:bg-gray-50 hover:text-blue-600'"
                                aria-label="Next">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    @else
                        <span class="relative inline-flex items-center px-3 py-2 -ml-px text-sm font-medium cursor-default rounded-r-xl leading-5"
                              x-bind:class="darkMode ? 'bg-gray-700 border-gray-600 text-gray-500' : 'bg-gray-100 border-gray-300 text-gray-400'">
                            <i class="fas fa-chevron-right"></i>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif