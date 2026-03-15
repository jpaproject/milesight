<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

        <style>
            .modal-backdrop {
                backdrop-filter: blur(8px);
            }

            .modal-content {
                animation: modalSlideIn 0.3s ease-out;
            }

            @keyframes modalSlideIn {
                from {
                    opacity: 0;
                    transform: translateY(-20px) scale(0.95);
                }

                to {
                    opacity: 1;
                    transform: translateY(0) scale(1);
                }
            }

            .form-input:focus {
                box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            }
        </style>
    @endpush


    <div x-data="{ pageName: `Device Management` }">
        <x-breadcrumb />
    </div>

    @session('success')
        <x-success-alert message="{{ session('success') }}" />
    @endsession

    @error('error')
        <x-error-alert message="{{ $message }}" />
    @enderror


    <div x-data="{ createModal: {{ session('show_create_modal') ? 'true' : 'false' }}, deleteModal: false, deviceId: null }"
        class ="mt-6 rounded-2xl border border-gray-200 bg-white dark:border-gray-800
        dark:bg-white/[0.03] overflow-hidden shadow-md">
        <div class="px-6 py-4 mb-5">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Device List</h3>
                <div class="flex items-center gap-2">
                    <button onclick="refreshTable()"
                        class="inline-flex items-center px-3 py-2 border border-transparent shadow-sm text-sm leading-4 font-medium rounded-md text-white bg-yellow-500 hover:bg-yellow-600 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Refresh
                    </button>
                    <button @click="createModal = true"
                        class="inline-flex items-center px-3 py-2 border border-transparent rounded-lg shadow-sm leading-4 text-sm font-medium text-white bg-brand-500 hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                                clip-rule="evenodd" />
                        </svg>
                        Create Device
                    </button>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto px-6 py-4">
            <table id="devicesTable" class="min-w-full border dark:border-gray-600 rounded">
                <thead class="bg-gray-200 dark:bg-gray-800">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-12">
                            No</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Device Name</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-32">
                            Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-transparent">
                    @foreach ($devices as $index => $device)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center dark:text-white">
                                {{ $index + 1 }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-center dark:text-white">
                                {{ $device->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('devices.edit', $device->id) }}"
                                        class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <button @click="deleteModal = true; deviceId = {{ $device->id }}"
                                        class="text-red-600 hover:text-red-900" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Modal -->
        <div x-show="createModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="absolute inset-0 z-50 flex items-center justify-center bg-black/60 modal-backdrop">

                <div @click.outside="createModal = false" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl w-full max-w-md mx-4 shadow-2xl modal-content max-h-[80vh] overflow-auto scroll-thin">

                <form method="POST" action={{ route('devices.store') }} class="p-6 space-y-4">
                    @csrf
                    <input type="hidden" name="is_active" value="1">
                    <h2 class="text-md font-semibold text-gray-800 dark:text-white">Create New Device</h2>

                    @error('error')
                        <x-error-alert message="{{ $message }}" />
                    @enderror
                    @if ($errors->has('area_id'))
                        <x-error-alert message="{{ $errors->first('area_id') }}" />
                    @endif

                    <div>
                        <x-input-label for="name" :value="__('Name')" required class="text-xs" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                            :value="old('name')" required autofocus autocomplete="name" placeholder="Enter the name" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" @click="createModal = false"
                            class="px-4 py-2 text-xs font-medium text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-all duration-200">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-4 py-2 text-xs font-medium text-white bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl hover:from-blue-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transform hover:scale-105 transition-all duration-200 shadow-lg">
                            Create Device
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="deleteModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="absolute inset-0 z-50 flex items-center justify-center bg-black/60 modal-backdrop">

            <div @click.outside="deleteModal = false" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                class="bg-white rounded-2xl  w-full max-w-md mx-4 shadow-2xl modal-content">

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 w-full max-w-md">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Confirm Deletion</h2>
                    <p class="text-gray-600 dark:text-gray-300">Are you sure you want to delete this device?</p>

                    <div class="mt-6 flex justify-end gap-2">
                        <button @click="deleteModal = false"
                            class="px-4 py-2 text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                            Cancel
                        </button>
                        <form method="POST" :action="`/devices/${deviceId}`">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>


    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

        <script>
            $(document).ready(function() {
                // Initialize DataTable
                var table = $('#devicesTable').DataTable({
                    processing: true,
                    pageLength: 10,
                    lengthMenu: [
                        [10, 25, 50, 100, -1],
                        [10, 25, 50, 100, "All"]
                    ],

                    columnDefs: [{
                            targets: [0],
                            orderable: false,
                            searchable: false,
                            className: 'text-center'
                        },
                        {
                            targets: [2], // Aksi
                            orderable: false,
                            searchable: false,
                            className: 'text-center'
                        }
                    ],


                    // Buttons Configuration dengan Tailwind Classes
                    dom: '<"flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4"<"flex sm:flex-row sm:items-center gap-3"lB><""f>>rtip',
                    buttons: [{
                        extend: 'collection',
                        text: 'Export',
                        className: '',
                        autoClose: true,
                        buttons: [{
                                extend: 'copy',
                                text: '📋 Copy to clipboard',
                                exportOptions: {
                                    columns: [1]
                                }
                            },
                            {
                                extend: 'excel',
                                text: '📄 Export as Excel',
                                exportOptions: {
                                    columns: [1]
                                }
                            },
                            {
                                extend: 'pdf',
                                text: '🧾 Export as PDF',
                                orientation: 'landscape',
                                pageSize: 'A4',
                                exportOptions: {
                                    columns: [1]
                                }
                            },
                            {
                                extend: 'print',
                                text: '🖨️ Print',
                                exportOptions: {
                                    columns: [1]
                                }
                            }
                        ]
                    }],

                    // Search Configuration
                    search: {
                        caseInsensitive: true
                    }
                });

                // Refresh table function
                window.refreshTable = function() {
                    location.reload();
                }
            });
        </script>
    @endpush
</x-app-layout>
