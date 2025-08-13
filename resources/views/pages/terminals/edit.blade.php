<x-app-layout>
    <div x-data="{ pageName: `Update Terminal` }">
        <x-breadcrumb />
    </div>

    @error('error')
        <x-error-alert message="{{ $message }}" />
    @enderror


    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <form method="POST" action={{ route('terminals.update', $terminal->id) }} class="p-6 space-y-4 ">
            @csrf
            @method('PUT')

            <div>
                <x-input-label for="name" :value="__('Name')" required class="text-xs" />
                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $terminal->name)"
                    required autofocus autocomplete="name" placeholder="Enter the terminal name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <a href="{{ route('terminals.index') }}"
                    class="px-4 py-2 text-xs font-medium text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-all duration-200">
                    Back
                </a>
                <button type="submit"
                    class="px-4 py-2 text-xs font-medium text-white bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl hover:from-blue-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transform hover:scale-105 transition-all duration-200 shadow-lg">
                    Update
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
