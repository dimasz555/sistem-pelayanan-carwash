<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Form Profil --}}
        <div>
            <form wire:submit="updateProfile">
                {{ $this->profileForm }}

                <div class="mt-6">
                    <x-filament::button type="submit" color="primary">
                        Update Profil
                    </x-filament::button>
                </div>
            </form>
        </div>

        {{-- Form Password --}}
        <div>
            <form wire:submit="updatePassword">
                {{ $this->passwordForm }}

                <div class="mt-6">
                    <x-filament::button type="submit" color="primary">
                        Update Password
                    </x-filament::button>
                </div>
            </form>
        </div>
    </div>
</x-filament-panels::page>
