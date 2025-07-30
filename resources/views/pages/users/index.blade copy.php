<x-app-layout>
    @push('styles')
    <!-- DataTables CSS - Default Style (bukan Bootstrap) -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">

    <!-- Custom Tailwind Overrides untuk DataTables -->
    <style>
        /* Override DataTables default dengan Tailwind classes */
        .dataTables_wrapper {
            @apply w-full;
        }

        .dataTables_length,
        .dataTables_filter,
        .dataTables_info,
        .dataTables_paginate {
            @apply my-4;
        }

        .dataTables_length label,
        .dataTables_filter label {
            @apply flex items-center gap-2 text-sm font-medium text-gray-700;
        }

        .dataTables_length select {
            @apply px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent;
        }

        .dataTables_filter input {
            @apply px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent ml-2;
        }

        .dataTables_paginate .paginate_button {
            @apply px-3 py-2 mx-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50 cursor-pointer;
        }

        .dataTables_paginate .paginate_button.current {
            @apply bg-blue-600 text-white border-blue-600 hover:bg-blue-700;
        }

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

    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="py-8">
            <!-- Header -->
            <div class="sm:flex sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Data Users</h1>
                    <p class="mt-2 text-sm text-gray-600">Kelola data pengguna sistem</p>
                </div>
                <div class="mt-4 sm:mt-0">
                    <button type="button"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        onclick="openUserModal()">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                                clip-rule="evenodd" />
                        </svg>
                        Tambah User
                    </button>
                </div>
            </div>

            <!-- Filters -->
            <div class="mt-6 bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Filter Data</h3>
                    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div>
                            <label for="statusFilter" class="block text-sm font-medium text-gray-700">Status</label>
                            <select id="statusFilter"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Semua Status</option>
                                <option value="Aktif">Aktif</option>
                                <option value="Nonaktif">Nonaktif</option>
                            </select>
                        </div>
                        <div>
                            <label for="dateMin" class="block text-sm font-medium text-gray-700">Tanggal Dari</label>
                            <input type="date" id="dateMin"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="dateMax" class="block text-sm font-medium text-gray-700">Tanggal Sampai</label>
                            <input type="date" id="dateMax"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="mt-6 bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">Daftar Users</h3>
                        <button onclick="refreshTable()"
                            class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Refresh
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table id="usersTable" class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">
                                    No</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nama</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Email</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tanggal Daftar</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($users as $index => $user)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                    {{ $index + 1 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div
                                                class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                <span
                                                    class="text-sm font-medium text-gray-700">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->email }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if ($user->status == 'active')
                                    <span class="status-badge status-active">Aktif</span>
                                    @else
                                    <span class="status-badge status-inactive">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    {{ $user->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <button onclick="viewUser({{ $user->id }})"
                                            class="text-blue-600 hover:text-blue-900" title="View">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                        <button onclick="editUser({{ $user->id }})"
                                            class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <button onclick="deleteUser({{ $user->id }})"
                                            class="text-red-600 hover:text-red-900" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2"
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
        </div>
    </div>





    @push('scripts')
    <!-- DataTables JS - Default (tanpa Bootstrap) -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            var table = $('#usersTable').DataTable({
                // Basic Configuration
                processing: true,
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "Semua"]
                ],

                // Language Configuration
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/id.json",
                    searchPlaceholder: "Cari data...",
                    search: "Pencarian:",
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    zeroRecords: "Data tidak ditemukan",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                    infoFiltered: "(disaring dari _MAX_ total data)",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    },
                    processing: "Memproses data..."
                },

                // Column Configuration
                columnDefs: [{
                        targets: [0], // No urut
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        targets: [3], // Status
                        className: 'text-center'
                    },
                    {
                        targets: [4], // Tanggal
                        type: 'date',
                        className: 'text-center'
                    },
                    {
                        targets: [5], // Aksi
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }
                ],

                // Responsive Configuration
                responsive: {
                    details: {
                        display: $.fn.dataTable.Responsive.display.childRowImmediate,
                        type: 'none',
                        target: ''
                    }
                },

                // Buttons Configuration dengan Tailwind Classes
                dom: '<"flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4"<"mb-2 sm:mb-0"l><"mb-2 sm:mb-0"B><"sm:ml-auto"f>>rtip',
                buttons: [{
                        extend: 'copy',
                        text: '<svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>Copy',
                        className: 'px-3 py-2 text-sm bg-gray-500 text-white rounded hover:bg-gray-600',
                        exportOptions: {
                            columns: [1, 2, 3, 4]
                        }
                    },
                    {
                        extend: 'csv',
                        text: '<svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>CSV',
                        className: 'px-3 py-2 text-sm bg-green-500 text-white rounded hover:bg-green-600',
                        exportOptions: {
                            columns: [1, 2, 3, 4]
                        }
                    },
                    {
                        extend: 'excel',
                        text: '<svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>Excel',
                        className: 'px-3 py-2 text-sm bg-green-600 text-white rounded hover:bg-green-700',
                        exportOptions: {
                            columns: [1, 2, 3, 4]
                        }
                    },
                    {
                        extend: 'pdf',
                        text: '<svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>PDF',
                        className: 'px-3 py-2 text-sm bg-red-500 text-white rounded hover:bg-red-600',
                        orientation: 'landscape',
                        exportOptions: {
                            columns: [1, 2, 3, 4]
                        }
                    },
                    {
                        extend: 'print',
                        text: '<svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>Print',
                        className: 'px-3 py-2 text-sm bg-blue-500 text-white rounded hover:bg-blue-600',
                        exportOptions: {
                            columns: [1, 2, 3, 4]
                        }
                    }
                ],

                // Order Configuration
                order: [
                    [4, 'desc']
                ], // Sort by tanggal desc

                // Search Configuration
                search: {
                    caseInsensitive: true
                }
            });

            // Custom search filters
            $('#statusFilter').on('change', function() {
                var status = this.value;
                if (status === '') {
                    table.column(3).search('').draw();
                } else {
                    table.column(3).search(status).draw();
                }
            });

            // Date range filter
            $.fn.dataTable.ext.search.push(
                function(settings, data, dataIndex) {
                    var min = $('#dateMin').val();
                    var max = $('#dateMax').val();

                    if (!min && !max) return true;

                    // Parse date dari kolom tanggal (index 4)
                    var dateStr = data[4]; // Format: "23/07/2025 14:30"
                    var dateParts = dateStr.split(' ')[0].split('/'); // Ambil bagian tanggal saja
                    var date = new Date(dateParts[2], dateParts[1] - 1, dateParts[0]); // yyyy, mm-1, dd

                    var minDate = min ? new Date(min) : null;
                    var maxDate = max ? new Date(max) : null;

                    if (!minDate && maxDate && date <= maxDate) return true;
                    if (minDate && !maxDate && date >= minDate) return true;
                    if (minDate && maxDate && date >= minDate && date <= maxDate) return true;
                    if (!minDate && !maxDate) return true;

                    return false;
                }
            );

            // Event listeners untuk date filter
            $('#dateMin, #dateMax').on('change', function() {
                table.draw();
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
