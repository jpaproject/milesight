<x-app-layout>
    <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">Dashboard</h2>

    <div class="mt-10 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4">
        @forelse ($areas as $id => $name)
            <div class="bg-white dark:bg-gray-800 flex justify-between items-center shadow-sm rounded-lg p-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                    {{ $name }}
                </h3>
                <div class="mt-3">
                    <a href={{ route('dashboard.show', $id) }}
                        class="inline-block px-4 py-2 bg-blue-600 text-white text-sm
                        font-medium rounded-lg hover:bg-blue-700">
                        Detail
                    </a>
                </div>
            </div>
        @empty
            <p>No areas found.</p>
        @endforelse
    </div>
</x-app-layout>
