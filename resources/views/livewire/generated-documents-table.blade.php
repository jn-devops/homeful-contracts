<div>
    {{-- Document Table --}}
    <div class="fi-ta-container w-full overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
        <table class="fi-ta-table w-full divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
            <thead class="fi-ta-header bg-gray-50 dark:bg-gray-700">
            <tr class="fi-ta-row">
                <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 fi-table-header-cell-state">
                        <span class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white text-left">
                            No
                        </span>
                </th>
                <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 fi-table-header-cell-state text-left">
                    <div class="flex items-center justify-between">
                            <span class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">
                                Document
                            </span>
                        <div class="flex items-center gap-2">
                            <x-filament::input.wrapper>
                                <x-filament::input.select wire:model.defer="selectedSet" wire:change="fetchDocuments">
                                    <option value="">Select a Document Set</option>
                                    @foreach($documentSets as $set)
                                        <option value="{{ $set['code'] }}">{{ $set['name'] }}</option>
                                    @endforeach
                                </x-filament::input.select>
                            </x-filament::input.wrapper>
                            <x-filament::loading-indicator class="h-5 w-5" wire:loading />
                        </div>
                    </div>
                </th>
            </tr>
            </thead>

            <tbody class="fi-ta-body divide-y divide-gray-200 dark:divide-gray-700 wire:loading.class="opacity-50"" >
            {{-- Loading State --}}
            <tr wire:loading wire:target="fetchDocuments">
                <td colspan="2" class="text-center py-4 text-sm text-gray-500">
                    Generating documents...
                </td>
            </tr>

            {{-- Show Documents As They Are Fetched --}}
            @forelse ($generatedDocuments as $index => $document)
                <tr class="fi-ta-row hover:bg-gray-50 dark:hover:bg-gray-700" wire:key="document-{{ $index }}">
                    <td class="fi-ta-cell px-4 py-2 text-sm text-gray-900 dark:text-gray-300 text-center">
                        {{ $loop->iteration }}
                    </td>
                    <td class="fi-ta-cell px-4 py-2 text-sm text-gray-900 dark:text-gray-300">
                        <div class="flex items-center justify-between">
                            <span>{{ $document['name'] }}</span>
                            <div class="flex items-center gap-4">
                                <x-filament::icon-button
                                    icon="heroicon-m-eye"
                                    size="sm"
                                    onclick="window.open({{ json_encode($document['url']) }}, '_blank')"
                                />
                                <x-filament::icon-button
                                    icon="heroicon-m-arrow-down-tray"
                                    size="sm"
                                    wire:click="downloadDocument('{{ addslashes($document['url']) }}')"
                                />
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr wire:loading.remove wire:target="fetchDocuments">
                    <td colspan="2" class="text-center py-4 text-sm text-gray-500">
                        No documents found.
                    </td>
                </tr>
            @endforelse
            </tbody>

            <tfoot class="fi-ta-footer bg-gray-50 dark:bg-gray-700">
            <tr class="fi-ta-row">
                <td colspan="3" class="fi-ta-cell px-4 py-2 text-sm text-gray-900 dark:text-gray-300">
                    Total: {{ count($generatedDocuments) }} Documents
                </td>
            </tr>
            </tfoot>
        </table>
    </div>
</div>
