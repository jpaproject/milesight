<x-app-layout>
    <div x-data="{ pageName: `Update Device` }">
        <x-breadcrumb />
    </div>

    @error('error')
        <x-error-alert message="{{ $message }}" />
    @enderror
    @if ($errors->has('area_id'))
        <x-error-alert message="{{ $errors->first('area_id') }}" />
    @endif

    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">

        <form method="POST" action="{{ route('devices.update', $device->id) }}" class="p-6 space-y-4">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div>
                <x-input-label for="name" :value="__('Name')" required class="text-xs" />
                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $device->name)"
                    required autocomplete="name" placeholder="Enter the name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Status -->
            <div class="space-y-2">
                <x-input-label for="is_active" :value="__('Status')" required class="text-xs" />
                <select name="is_active" id="is_active"
                    class="form-input w-full px-4 py-2.5 transition-all duration-200 text-sm text-gray-800 bg-transparent border border-gray-300 rounded-lg appearance-none dark:bg-dark-900 h-11 bg-none shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800">

                    <option value="1" {{ old('is_active', $device->is_active) == '1' ? 'selected' : '' }}>✅ Active
                    </option>
                    <option value="0" {{ old('is_active', $device->is_active) == '0' ? 'selected' : '' }}>⛔
                        Inactive
                    </option>

                    </option>
                </select>
                <x-input-error :messages="$errors->get('is_active')" class="mt-2" />
            </div>

            <!-- Actions -->
            <div class="flex justify-end space-x-3 pt-4">
                <a href="{{ route('devices.index') }}"
                    class="px-4 py-2 text-xs font-medium text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200">
                    Back
                </a>
                <button type="submit"
                    class="px-4 py-2 text-xs font-medium text-white bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl">
                    Update
                </button>
            </div>
        </form>
    </div>

</x-app-layout>
