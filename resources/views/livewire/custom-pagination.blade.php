<div>
    @if ($paginator->hasPages())
        <div class="flex items-center justify-between mt-4">
            
            <!-- Pagination Links -->
            <div class="flex items-center gap-1">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <span class="px-3 py-1 text-gray-300 border border-gray-200 rounded cursor-not-allowed">
                        <i class="bi bi-chevron-left"></i>
                    </span>
                @else
                    <button wire:click="previousPage" wire:loading.attr="disabled" class="px-3 py-1 text-gray-600 border border-gray-300 rounded hover:bg-gray-100">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <span class="px-2 text-gray-500">{{ $element }}</span>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="px-3 py-1 text-white bg-blue-600 border border-blue-600 rounded">{{ $page }}</span>
                            @else
                                <button wire:click="gotoPage({{ $page }})" class="px-3 py-1 text-gray-600 border border-gray-300 rounded hover:bg-gray-100">{{ $page }}</button>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <button wire:click="nextPage" wire:loading.attr="disabled" class="px-3 py-1 text-gray-600 border border-gray-300 rounded hover:bg-gray-100">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                @else
                    <span class="px-3 py-1 text-gray-300 border border-gray-200 rounded cursor-not-allowed">
                        <i class="bi bi-chevron-right"></i>
                    </span>
                @endif
            </div>

            <!-- per Page Selector -->
            <div class="flex items-center gap-2 text-sm text-gray-600">
                <span>Rows:</span>
                <select wire:model.live="perPage" class="border-gray-300 rounded text-sm focus:ring-blue-500 focus:border-blue-500 py-1 px-2">
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
            
        </div>
    @else
         <!-- Still show per page even if no pagination needed? Only if rows > 0, maybe. -->
         <div class="flex justify-end mt-4 text-sm text-gray-600">
             <div class="flex items-center gap-2">
                <span>Rows:</span>
                <select wire:model.live="perPage" class="border-gray-300 rounded text-sm focus:ring-blue-500 focus:border-blue-500 py-1 px-2">
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
         </div>
    @endif
</div>
