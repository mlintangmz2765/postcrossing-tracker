{{-- "Sent from" picker for sent cards. Expects $recentOrigins and the PicksOrigin properties. --}}
<div class="origin-picker" x-data="{
        status: '',
        locate() {
            if (! navigator.geolocation) { this.status = 'Location is not available in this browser.'; return; }
            this.status = 'Locating…';
            navigator.geolocation.getCurrentPosition(
                (p) => {
                    this.$wire.set('origin_lat', Number(p.coords.latitude.toFixed(6)));
                    this.$wire.set('origin_lng', Number(p.coords.longitude.toFixed(6)));
                    this.status = 'Filled in from this device (±' + Math.round(p.coords.accuracy) + ' m).';
                },
                (e) => { this.status = 'Could not get location: ' + e.message + (window.isSecureContext ? '' : ' (browsers only allow this over HTTPS)'); },
                { enableHighAccuracy: true, timeout: 15000 }
            );
        }
    }">
    <label class="vintage-label">Sent From</label>
    <select wire:model.live="origin_choice" id="originChoice" class="vintage-input w-full">
        <option value="home">{{ \App\Support\Place::home()->name }} (home)</option>
        @foreach ($recentOrigins as $recent)
            <option value="{{ $recent->id }}">{{ $recent->name }}</option>
        @endforeach
        <option value="new">+ New place…</option>
    </select>
    <small class="text-xs text-gray-400 block mt-1">Distance is measured from here. The place name is shown to the recipient; the exact location is not.</small>

    @if ($origin_choice === 'new')
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-3">
            <input type="text" wire:model="origin_name" id="originName" class="vintage-input w-full" maxlength="150" placeholder="Place name, e.g. Lisbon, Portugal">
            <input type="number" step="any" wire:model="origin_lat" id="originLat" class="vintage-input w-full" placeholder="Latitude">
            <input type="number" step="any" wire:model="origin_lng" id="originLng" class="vintage-input w-full" placeholder="Longitude">
        </div>
        <button type="button" @click="locate()" class="text-xs text-pc-blue hover:underline font-bold mt-2" style="font-family: 'Special Elite', cursive;">
            <i class="bi bi-crosshair"></i> USE MY CURRENT LOCATION
        </button>
        <span class="text-xs text-gray-500 ml-2" x-text="status"></span>
        @error('origin_name') <span class="text-red-500 text-xs block">{{ $message }}</span> @enderror
        @error('origin_lat') <span class="text-red-500 text-xs block">{{ $message }}</span> @enderror
        @error('origin_lng') <span class="text-red-500 text-xs block">{{ $message }}</span> @enderror
    @endif
</div>
