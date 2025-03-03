<div>
    {{-- Care about people's approval and you will be their prisoner. --}}
{{--    <x-filament-tables::table header="Sample Header">--}}

{{--    </x-filament-tables::table>--}}
    <div class="fi-ta-container w-full overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
        <table class="fi-ta-table w-full divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
            <thead class="fi-ta-header bg-gray-50 dark:bg-gray-700">
            <tr class="fi-ta-row">
                <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 fi-table-header-cell-state text-left">
                    <span class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">
                        No
                    </span>
                </th>
                <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 fi-table-header-cell-state text-left">
                    <span class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">
                        Status
                    </span>
                </th>
                <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 fi-table-header-cell-state text-left">
                    <span class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">
                        Document Name
                    </span>
                </th>
                <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 fi-table-header-cell-state text-left">
                    <span class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">
                        URL
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
            @foreach ($requirements as $requirement)
                <tr class="fi-ta-row hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="fi-ta-cell px-4 py-2 text-sm text-gray-900 dark:text-gray-300">
                        {{ $loop->iteration }}
                    </td>
                    <td class="fi-ta-cell px-4 py-2 text-sm text-gray-900 dark:text-gray-300 flex gap-2 items-center">
                        @if ($requirement['status'] == 'Uploaded')
                            <svg class="w-4 h-4 text-green-900" style="color: rgb(20 83 45 / var(--tw-text-opacity, 1));" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm13.707-1.293a1 1 0 0 0-1.414-1.414L11 12.586l-1.793-1.793a1 1 0 0 0-1.414 1.414l2.5 2.5a1 1 0 0 0 1.414 0l4-4Z" clip-rule="evenodd"/>
                            </svg>
                            {{ $requirement['status'] }}
                        @else
                            <svg class="w-4 h-4 text-orange-600" style="color: rgb(234 88 12 / var(--tw-text-opacity, 1));" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm11-4a1 1 0 1 0-2 0v4a1 1 0 0 0 .293.707l3 3a1 1 0 0 0 1.414-1.414L13 11.586V8Z" clip-rule="evenodd"/>
                            </svg>
                            {{ $requirement['status'] }}
                        @endif
                    </td>
                    <td class="fi-ta-cell px-4 py-2 text-sm text-gray-900 dark:text-gray-300">
                        {{ $requirement['description'] }}
                    </td>
                    <td class="fi-ta-cell px-4 py-2 text-sm text-gray-900 dark:text-gray-300">
                        <a href="{{ $requirement['url'] }}" target="_blank">
                            {{ $requirement['url'] }}
                        </a>
                    </td>
                    <td class="fi-ta-cell px-4 py-2 text-sm text-gray-900 dark:text-gray-300 text-center" >
                        {{-- <input type="file" id="documentInput{{$record->id}}" wire:model="document" class="hidden" /> --}}
                        <div class="cursor-pointer w-full flex items-center justify-center" wire:click="viewImage('{{ $requirement['description'] }}')">
                            <svg width="16" height="13" viewBox="0 0 16 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8.00005 8.4999C8.53049 8.4999 9.03919 8.28919 9.41427 7.91412C9.78934 7.53904 10.0001 7.03034 10.0001 6.4999C10.0001 5.96947 9.78934 5.46076 9.41427 5.08569C9.03919 4.71062 8.53049 4.4999 8.00005 4.4999C7.46962 4.4999 6.96091 4.71062 6.58584 5.08569C6.21077 5.46076 6.00005 5.96947 6.00005 6.4999C6.00005 7.03034 6.21077 7.53904 6.58584 7.91412C6.96091 8.28919 7.46962 8.4999 8.00005 8.4999Z" fill="#A3A3A3"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M0.531253 6.9719C0.413736 6.66655 0.413736 6.32845 0.531253 6.0231C1.11304 4.5147 2.13802 3.21786 3.47122 2.30335C4.80443 1.38884 6.38334 0.899549 8.00005 0.899903C11.4057 0.899903 14.3145 3.0279 15.4689 6.0279C15.5865 6.3327 15.5857 6.6711 15.4689 6.9767C14.8871 8.48511 13.8621 9.78195 12.5289 10.6965C11.1957 11.611 9.61677 12.1003 8.00005 12.0999C4.59445 12.0999 1.68565 9.9719 0.531253 6.9719ZM11.2001 6.4999C11.2001 7.3486 10.8629 8.16253 10.2628 8.76264C9.66268 9.36276 8.84875 9.6999 8.00005 9.6999C7.15136 9.6999 6.33743 9.36276 5.73731 8.76264C5.13719 8.16253 4.80005 7.3486 4.80005 6.4999C4.80005 5.65121 5.13719 4.83728 5.73731 4.23716C6.33743 3.63704 7.15136 3.2999 8.00005 3.2999C8.84875 3.2999 9.66268 3.63704 10.2628 4.23716C10.8629 4.83728 11.2001 5.65121 11.2001 6.4999Z" fill="#A3A3A3"/>
                            </svg>
                        </div>
                        {{-- <div wire:click="chooseFile('{{$requirement['description']}}')" id="upload_document" onclick="document.getElementById('documentInput{{$record->id}}').click();" class="cursor-pointer">
                            <svg width="14" height="13" viewBox="0 0 14 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M7 13C7.13261 13 7.25979 12.9473 7.35355 12.8536C7.44732 12.7598 7.5 12.6326 7.5 12.5V4.70667L9.64667 6.85333C9.69244 6.90246 9.74764 6.94186 9.80897 6.96919C9.87031 6.99652 9.93652 7.01121 10.0037 7.01239C10.0708 7.01358 10.1375 7.00123 10.1997 6.97608C10.262 6.95093 10.3185 6.9135 10.366 6.86602C10.4135 6.81855 10.4509 6.76199 10.4761 6.69973C10.5012 6.63747 10.5136 6.57079 10.5124 6.50365C10.5112 6.43652 10.4965 6.37031 10.4692 6.30897C10.4419 6.24764 10.4025 6.19244 10.3533 6.14667L7.35333 3.14667C7.25958 3.05303 7.1325 3.00044 7 3.00044C6.8675 3.00044 6.74042 3.05303 6.64667 3.14667L3.64667 6.14667C3.59754 6.19244 3.55814 6.24764 3.53081 6.30897C3.50348 6.37031 3.48879 6.43652 3.48761 6.50365C3.48642 6.57079 3.49877 6.63747 3.52392 6.69973C3.54907 6.76199 3.5865 6.81855 3.63397 6.86602C3.68145 6.9135 3.73801 6.95093 3.80027 6.97608C3.86253 7.00123 3.92921 7.01358 3.99635 7.01239C4.06348 7.01121 4.12969 6.99652 4.19103 6.96919C4.25236 6.94186 4.30756 6.90246 4.35333 6.85333L6.5 4.70667V12.5C6.5 12.6326 6.55268 12.7598 6.64645 12.8536C6.74022 12.9473 6.86739 13 7 13ZM1 4C1.13261 4 1.25979 3.94732 1.35355 3.85355C1.44732 3.75978 1.5 3.63261 1.5 3.5V2C1.5 1.73478 1.60536 1.48043 1.79289 1.29289C1.98043 1.10536 2.23478 1 2.5 1H11.5C11.7652 1 12.0196 1.10536 12.2071 1.29289C12.3946 1.48043 12.5 1.73478 12.5 2V3.5C12.5 3.63261 12.5527 3.75978 12.6464 3.85355C12.7402 3.94732 12.8674 4 13 4C13.1326 4 13.2598 3.94732 13.3536 3.85355C13.4473 3.75978 13.5 3.63261 13.5 3.5V2C13.5 1.46957 13.2893 0.960858 12.9142 0.585786C12.5391 0.210713 12.0304 0 11.5 0H2.5C1.96957 0 1.46086 0.210713 1.08579 0.585786C0.710714 0.960858 0.5 1.46957 0.5 2V3.5C0.5 3.63261 0.552678 3.75978 0.646447 3.85355C0.740215 3.94732 0.867392 4 1 4Z" fill="url(#paint0_linear_3507_11625)"/>
                                <defs>
                                    <linearGradient id="paint0_linear_3507_11625" x1="19.0488" y1="13" x2="-0.964055" y2="11.3422" gradientUnits="userSpaceOnUse">
                                        <stop stop-color="#FCB115"/>
                                        <stop offset="1" stop-color="#C87A2C"/>
                                    </linearGradient>
                                </defs>
                            </svg>
                            @error('document') <span class="error">{{ $message }}</span> @enderror
                        </div> --}}
                        {{-- @if ($requirement['description'] == $chosenFile)
                            <div wire:click="uploadDoc('{{$requirement['description']}}')" class="cursor-pointer">
                                <svg class="w-6 h-6 text-gray-400 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" d="M5 3a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7.414A2 2 0 0 0 20.414 6L18 3.586A2 2 0 0 0 16.586 3H5Zm3 11a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v6H8v-6Zm1-7V5h6v2a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1Z" clip-rule="evenodd"/>
                                    <path fill-rule="evenodd" d="M14 17h-4v-2h4v2Z" clip-rule="evenodd"/>
                                </svg>
                            </div>

                        @endif --}}

                    </td>
                </tr>
            @endforeach
            </tbody>
            <tfoot class="fi-ta-footer bg-gray-50 dark:bg-gray-700">
            <tr class="fi-ta-row">
                <td colspan="4" class="fi-ta-cell px-4 py-2 text-sm text-gray-900 dark:text-gray-300">
                    Total: {{ count($requirements) }} Documents
                </td>
            </tr>
            </tfoot>
        </table>
    </div>

    <script>
        // document.addEventListener('livewire:load', function () {
        //     Livewire.on('openNewTab', url => {
        //         window.open(url, '_blank');
        //     });
        // });
        window.addEventListener('openNewTab', event => {
            window.open(event.detail, '_blank');
        });
    </script>
</div>
