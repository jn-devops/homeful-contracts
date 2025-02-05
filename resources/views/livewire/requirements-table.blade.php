<div>
    {{-- Care about people's approval and you will be their prisoner. --}}
{{--    <x-filament-tables::table header="Sample Header">--}}

{{--    </x-filament-tables::table>--}}

    <div class="fi-ta-container w-full overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
        <table class="fi-ta-table w-full divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
            <thead class="fi-ta-header bg-gray-50 dark:bg-gray-700">
            <tr class="fi-ta-row">
                <th class="fi-ta-column px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                    Status
                </th>
                <th class="fi-ta-column px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                    Document Name
                </th>
                <th class="fi-ta-column px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                    Actions
                </th>
            </tr>
            </thead>
            <tbody class="fi-ta-body divide-y divide-gray-200 dark:divide-gray-700">
            @foreach ($requirements as $requirement)
                <tr class="fi-ta-row hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="fi-ta-cell px-4 py-2 text-sm text-gray-900 dark:text-gray-300">
                        {{ $requirement['status'] ?? 'Pending' }} <!-- Default to 'Pending' if status is missing -->
                    </td>
                    <td class="fi-ta-cell px-4 py-2 text-sm text-gray-900 dark:text-gray-300">
                        {{ $requirement }}
                    </td>
                    <td class="fi-ta-cell px-4 py-2 text-sm text-gray-900 dark:text-gray-300">
                        <button class="fi-ta-actions bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">
                            View
                        </button>
                        <button class="fi-ta-actions bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">
                            Delete
                        </button>
                    </td>
                </tr>
            @endforeach
            </tbody>
            <tfoot class="fi-ta-footer bg-gray-50 dark:bg-gray-700">
            <tr class="fi-ta-row">
                <td colspan="3" class="fi-ta-cell px-4 py-2 text-sm text-gray-900 dark:text-gray-300">
                    Total: {{ count($requirements) }} Documents
                </td>
            </tr>
            </tfoot>
        </table>
    </div>


</div>
