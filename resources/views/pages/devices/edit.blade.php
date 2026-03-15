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
            <input type="hidden" name="is_active" value="{{ old('is_active', $device->is_active) }}">

            <!-- Name -->
            <div>
                <x-input-label for="name" :value="__('Name')" required class="text-xs" />
                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $device->name)"
                    required autocomplete="name" placeholder="Enter the name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
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
