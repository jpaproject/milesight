<x-app-layout>
    @push('styles')
        <!-- DataTables CSS - Default Style (bukan Bootstrap) -->
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

        <style>
            .dataTables_paginate .paginate_button.disabled {
                @apply opacity-50 cursor-not-allowed hover:bg-transparent;
            }

            .dataTables_info {
                @apply text-sm text-gray-600;
            }

            /* Table styling */
            table.dataTable {
                @apply w-full border-collapse;
            }

            table.dataTable thead th {
                @apply bg-gray-50 border-b-2 border-gray-200 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider;
            }

            table.dataTable tbody td {
                @apply px-4 py-3 whitespace-nowrap text-sm text-gray-900 border-b border-gray-200;
            }

            table.dataTable tbody tr:hover {
                @apply bg-gray-50;
            }

            /* Buttons styling */
            .dt-buttons {
                @apply mb-4 flex flex-wrap gap-2;
            }

            .dt-button {
                @apply px-4 py-2 text-sm font-medium rounded-md border focus:outline-none focus:ring-2 focus:ring-offset-2;
            }

            .dt-button:not(.disabled) {
                @apply cursor-pointer;
            }

            .dt-button .buttons-collection {
                background-color: "blue" !important;
            }


            /* Loading */
            .dataTables_processing {
                @apply fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white border border-gray-300 rounded-lg shadow-lg px-6 py-4 text-sm font-medium text-gray-700;
            }

            /* Responsive */
            .dtr-details {
                @apply bg-gray-50;
            }

            .status-badge {
                @apply px-2 py-1 text-xs font-medium rounded-full;
            }

            .status-active {
                @apply bg-green-100 text-green-800;
            }

            .status-inactive {
                @apply bg-red-100 text-red-800;
            }
        </style>
    @endpush


    <div x-data="{ pageName: `User Management` }">
        <x-breadcrumb />
    </div>


    <div
        class="mt-6 rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] overflow-hidden">
        <div class="px-6 py-4 mb-5">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">User List</h3>
                <div class="flex items-center gap-2">
                    <button onclick="refreshTable()"
                        class="inline-flex items-center px-3 py-2 border border-transparent shadow-sm text-sm leading-4 font-medium rounded-md text-white bg-yellow-500 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Refresh
                    </button>
                    <button
                        class="inline-flex items-center px-3 py-2 border border-transparent rounded-md shadow-sm leading-4 text-sm font-medium text-white bg-brand-500 hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                                clip-rule="evenodd" />
                        </svg>
                        Create User
                    </button>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto px-6 py-4">
            <table id="usersTable" class="min-w-full  border dark:border-gray-600 rounded">
                <thead class="bg-gray-200 dark:bg-gray-800">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-12">
                            No</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Name</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Email</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Username</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Role</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-32">
                            Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-transparent">
                    @foreach ($users as $index => $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center dark:text-white">
                                {{ $index + 1 }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-center dark:text-white">
                                {{ $user->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center dark:text-white">
                                {{ $user->email }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center dark:text-white">
                                {{ $user->username }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center dark:text-white capitalize">
                                {{ $user->role }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <button onclick="editUser({{ $user->id }})"
                                        class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button onclick="deleteUser({{ $user->id }})"
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
                var table = $('#usersTable').DataTable({
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
                            targets: [5], // Aksi
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
                                    columns: [1, 2, 3, 4]
                                }
                            },
                            {
                                extend: 'excel',
                                text: '📄 Export as Excel',
                                exportOptions: {
                                    columns: [1, 2, 3, 4]
                                }
                            },
                            {
                                extend: 'pdf',
                                text: '🧾 Export as PDF',
                                orientation: 'landscape',
                                pageSize: 'A4',
                                exportOptions: {
                                    columns: [1, 2, 3, 4]
                                }
                            },
                            {
                                extend: 'print',
                                text: '🖨️ Print',
                                exportOptions: {
                                    columns: [1, 2, 3, 4]
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

            // Action Functions
            function viewUser(id) {
                // Implementation untuk view user
                alert('View user dengan ID: ' + id);
            }

            function editUser(id) {
                // Implementation untuk edit user
                alert('Edit user dengan ID: ' + id);
            }

            function deleteUser(id) {
                if (confirm('Apakah Anda yakin ingin menghapus user ini?')) {
                    // Implementation untuk delete user
                    alert('Delete user dengan ID: ' + id);
                }
            }

            function openUserModal() {
                // Implementation untuk modal tambah user
                alert('Modal tambah user');
            }
        </script>
    @endpush
</x-app-layout>
