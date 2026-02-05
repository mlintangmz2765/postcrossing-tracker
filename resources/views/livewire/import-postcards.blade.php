<div class="import-wrapper paper-texture py-20 px-6">
    <div class="container max-w-4xl mx-auto airmail-border bg-white shadow-xl overflow-hidden p-10">
        <div class="text-center mb-10">
            <h2 class="text-4xl font-handwriting text-pc-ink">Mass Data Import</h2>
            <p class="vintage-label mt-2" style="color: #64748b; font-size: 0.8rem;">Update your collection via CSV file</p>
        </div>

        @if($message)
            <div class="mb-8 p-4 rounded-lg text-center font-bold {{ $messageType === 'success' ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-red-100 text-red-700 border border-red-200' }}">
                {{ $message }}
            </div>
        @endif

        <form wire:submit.prevent="import" class="space-y-8">
            <div class="border-4 border-dashed border-gray-100 p-10 rounded-xl text-center hover:bg-gray-50 transition cursor-pointer relative">
                <input type="file" wire:model="file_csv" class="absolute inset-0 opacity-0 cursor-pointer" accept=".csv,.txt">
                <div class="space-y-4">
                    <i class="bi bi-file-earmark-arrow-up text-6xl text-gray-300"></i>
                    <div class="text-gray-500 font-postcard">
                        @if($file_csv)
                            <span class="text-pc-blue font-bold">{{ $file_csv->getClientOriginalName() }}</span>
                        @else
                            Click or drag CSV file here to upload
                        @endif
                    </div>
                </div>
            </div>

            <div wire:loading wire:target="file_csv" class="text-center text-blue-500 font-bold animate-pulse">
                Uploading file...
            </div>

            <button type="submit" 
                    wire:loading.attr="disabled"
                    class="vintage-btn w-full text-xl py-5 {{ !$file_csv ? 'opacity-50 cursor-not-allowed' : '' }}">
                <span wire:loading.remove wire:target="import">
                    <i class="bi bi-rocket-takeoff me-2"></i> START IMPORT PROCESS
                </span>
                <span wire:loading wire:target="import">
                    <i class="bi bi-hourglass-split animate-spin me-2"></i> PROCESSING DATA...
                </span>
            </button>
        </form>

        <div class="mt-12 pt-8 border-t-2 border-dashed border-gray-100">
            <h3 class="font-handwriting text-2xl mb-6 text-pc-ink">CSV Structure Guide</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="p-6 bg-blue-50/50 rounded-lg border border-blue-100">
                    <h4 class="font-bold text-pc-blue mb-2"><i class="bi bi-send me-2"></i> SENT POSTCARDS</h4>
                    <p class="text-sm text-gray-600 mb-4">Use 11-column structure. Make sure phone number column is present even if empty.</p>
                </div>
                
                <div class="p-6 bg-red-50/50 rounded-lg border border-red-100">
                    <h4 class="font-bold text-pc-red mb-2"><i class="bi bi-box-arrow-in-down me-2"></i> RECEIVED POSTCARDS</h4>
                    <p class="text-sm text-gray-600 mb-4">Use 10-column structure (no phone column). Rates will be fetched automatically.</p>
                </div>
            </div>

            <div class="mt-8 overflow-x-auto">
                <table class="w-full text-sm font-postcard border-collapse border border-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="border border-gray-200 p-3 italic">No</th>
                            <th class="border border-gray-200 p-3 italic">Field Name</th>
                            <th class="border border-gray-200 p-3 italic">Format / Rule</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="border border-gray-200 p-3 text-center">1</td><td class="border border-gray-200 p-3 font-bold">type</td><td class="border border-gray-200 p-3">"sent" or "received"</td></tr>
                        <tr><td class="border border-gray-200 p-3 text-center">3</td><td class="border border-gray-200 p-3 font-bold">tgl_kirim</td><td class="border border-gray-200 p-3">DD/MM/YYYY (Determines rate)</td></tr>
                        <tr><td class="border border-gray-200 p-3 text-center">7</td><td class="border border-gray-200 p-3 font-bold">alamat</td><td class="border border-gray-200 p-3">Full address for mapping</td></tr>
                        <tr><td class="border border-gray-200 p-3 text-center">8</td><td class="border border-gray-200 p-3 font-bold">negara</td><td class="border border-gray-200 p-3">Origin/Destination country</td></tr>
                        <tr><td class="border border-gray-200 p-3 text-center">9</td><td class="border border-gray-200 p-3 font-bold">biaya_asal</td><td class="border border-gray-200 p-3">Digits only (0.85 or 0,85)</td></tr>
                        <tr><td class="border border-gray-200 p-3 text-center">10</td><td class="border border-gray-200 p-3 font-bold">mata_uang</td><td class="border border-gray-200 p-3">ISO Code (USD, EUR, etc)</td></tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-10 text-center">
                <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-pc-ink transition font-postcard">
                    <i class="bi bi-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
