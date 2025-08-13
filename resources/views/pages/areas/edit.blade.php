<x-app-layout>
    <div x-data="{ pageName: `Update Area` }">
        <x-breadcrumb />
    </div>

    @error('error')
        <x-error-alert message="{{ $message }}" />
    @enderror


    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <form method="POST" action={{ route('areas.update', $area->id) }} class="p-6 space-y-4 ">
            @csrf
            @method('PUT')

            <div class="space-y-2">
                <x-input-label for="terminal_id" :value="__('Terminal')" required class="text-xs" />
                <select name="terminal_id" id="terminal_id"
                    class="form-input w-full px-4 py-2.5 transition-all duration-200 text-sm text-gray-800 bg-transparent border border-gray-300 rounded-lg appearance-none dark:bg-dark-900 h-11 bg-none shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                    autofocus>
                    <option value="">-- Select Terminal --</option>
                    @foreach ($terminals as $id => $name)
                        <option value="{{ $id }}"
                            {{ old('terminal_id', $area->terminal_id) == $id ? 'selected' : '' }}>
                            🏙️ {{ $name }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('teriminal_id')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="name" :value="__('Name')" required class="text-xs" />
                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $area->name)"
                    required autofocus autocomplete="name" placeholder="Enter the area name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <a href="{{ route('areas.index') }}"
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
