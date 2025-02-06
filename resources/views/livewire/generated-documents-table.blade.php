<div>
    {{-- Do your work, then step back. --}}
    <div class="fi-ta-container w-full overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
        <table class="fi-ta-table w-full divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
            <thead class="fi-ta-header bg-gray-50 dark:bg-gray-700">
            <tr class="fi-ta-row">
                <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 fi-table-header-cell-state">
                    <span class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">
                        No
                    </span>
                </th>
                <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 fi-table-header-cell-state text-left">
                    <span class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">
                        Document
                    </span>
                </th>
                <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 fi-table-header-cell-state">
                    <span class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">
                        Actions
                    </span>
                </th>
            </tr>
            </thead>
            <tbody class="fi-ta-body divide-y divide-gray-200 dark:divide-gray-700">
            @foreach ($generatedDocuments as $document)
                <tr class="fi-ta-row hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="fi-ta-cell px-4 py-2 text-sm text-gray-900 dark:text-gray-300">
                        {{ $loop->iteration }}
                    </td>
                    <td class="fi-ta-cell px-4 py-2 text-sm text-gray-900 dark:text-gray-300">
                        {{ $document['name'] }}
                    </td>
                    <td class="fi-ta-cell px-4 py-2 text-sm text-gray-900 dark:text-gray-300 flex flex-row items-center gap-4">
                        <div class="cursor-pointer"
                             x-data
                             @click="window.open('{{ $document["url"] }}', '_blank')"
                             wire:click
                        >
                            <svg width="16" height="13" viewBox="0 0 16 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8.00005 8.4999C8.53049 8.4999 9.03919 8.28919 9.41427 7.91412C9.78934 7.53904 10.0001 7.03034 10.0001 6.4999C10.0001 5.96947 9.78934 5.46076 9.41427 5.08569C9.03919 4.71062 8.53049 4.4999 8.00005 4.4999C7.46962 4.4999 6.96091 4.71062 6.58584 5.08569C6.21077 5.46076 6.00005 5.96947 6.00005 6.4999C6.00005 7.03034 6.21077 7.53904 6.58584 7.91412C6.96091 8.28919 7.46962 8.4999 8.00005 8.4999Z" fill="#A3A3A3"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M0.531253 6.9719C0.413736 6.66655 0.413736 6.32845 0.531253 6.0231C1.11304 4.5147 2.13802 3.21786 3.47122 2.30335C4.80443 1.38884 6.38334 0.899549 8.00005 0.899903C11.4057 0.899903 14.3145 3.0279 15.4689 6.0279C15.5865 6.3327 15.5857 6.6711 15.4689 6.9767C14.8871 8.48511 13.8621 9.78195 12.5289 10.6965C11.1957 11.611 9.61677 12.1003 8.00005 12.0999C4.59445 12.0999 1.68565 9.9719 0.531253 6.9719ZM11.2001 6.4999C11.2001 7.3486 10.8629 8.16253 10.2628 8.76264C9.66268 9.36276 8.84875 9.6999 8.00005 9.6999C7.15136 9.6999 6.33743 9.36276 5.73731 8.76264C5.13719 8.16253 4.80005 7.3486 4.80005 6.4999C4.80005 5.65121 5.13719 4.83728 5.73731 4.23716C6.33743 3.63704 7.15136 3.2999 8.00005 3.2999C8.84875 3.2999 9.66268 3.63704 10.2628 4.23716C10.8629 4.83728 11.2001 5.65121 11.2001 6.4999Z" fill="#A3A3A3"/>
                            </svg>
                        </div>

                    </td>
                </tr>
            @endforeach
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
