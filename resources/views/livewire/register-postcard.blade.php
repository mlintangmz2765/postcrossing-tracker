<div class="register-wrapper paper-texture py-20 px-6" x-data="{ img_d_preview: null, img_b_preview: null }">
    <div class="container max-w-4xl mx-auto airmail-border bg-white shadow-xl overflow-hidden">
        <div class="form-inner p-0">
            <div class="p-8 border-b-2 border-dashed border-gray-100 text-center">
                <h2 class="text-4xl font-handwriting text-pc-ink">New Registry Log</h2>
                <p class="vintage-label mt-2" style="color: #64748b; font-size: 0.8rem;">Step into the philately journey</p>
            </div>

            <form wire:submit.prevent="save" class="p-8 space-y-8">
                <!-- Type Selection -->
                <div class="flex space-x-4 p-2 bg-gray-100/50 rounded-xl border-2 border-dashed border-gray-200">
                    <button type="button" 
                        wire:click="$set('type', 'sent')"
                        class="flex-1 py-4 rounded-lg font-bold transition-all duration-300 transform {{ $type === 'sent' ? 'bg-pc-blue text-white shadow-lg -translate-y-1 scale-105' : 'bg-white text-gray-400 hover:text-pc-blue border border-gray-100' }}"
                        style="{{ $type === 'sent' ? 'background-color: #457b9d;' : '' }}">
                        <i class="bi bi-send me-2"></i> SENT ARCHIVE
                    </button>
                    <button type="button" 
                        wire:click="$set('type', 'received')"
                        class="flex-1 py-4 rounded-lg font-bold transition-all duration-300 transform {{ $type === 'received' ? 'bg-pc-red text-white shadow-lg -translate-y-1 scale-105' : 'bg-white text-gray-400 hover:text-pc-red border border-gray-100' }}"
                        style="{{ $type === 'received' ? 'background-color: #e63946;' : '' }}">
                        <i class="bi bi-box-arrow-in-down me-2"></i> RECEIVED LOG
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label class="vintage-label">Postcard ID</label>
                        <input type="text" wire:model="postcard_id" class="vintage-input w-full" placeholder="e.g. ID-123456">
                    </div>
                    
                    <div>
                        <label class="vintage-label">Contact Name</label>
                        <input type="text" wire:model.live.debounce.250ms="nama_kontak" list="contactList" class="vintage-input w-full" placeholder="Addressee Name">
                        <datalist id="contactList">
                            @foreach($contacts as $contactName)
                                <option value="{{ $contactName }}">
                            @endforeach
                        </datalist>
                    </div>

                    <div>
                        <label class="vintage-label">Phone Number</label>
                        <input type="text" wire:model="nomor_telepon" class="vintage-input w-full" placeholder="e.g. +62 812...">
                    </div>

                    <div class="md:col-span-2">
                        <label class="vintage-label">Mailing Address</label>
                        <textarea wire:model="alamat" rows="3" class="vintage-input w-full" required placeholder="Write the full address here..."></textarea>
                    </div>

                    <div>
                        <label class="vintage-label">Country</label>
                        <input type="text" id="negaraInput" wire:model.blur="negara" class="vintage-input w-full" placeholder="Where is it from/to?" required @blur="updateCurrencyFromCountry($el.value)">
                    </div>

                    <div wire:ignore
                         x-data="{ dateValue: @entangle('tanggal_kirim') }"
                         x-init="
                            flatpickr($refs.input, {
                                altInput: true,
                                altFormat: 'd/m/Y',
                                dateFormat: 'Y-m-d',
                                allowInput: true,
                                onChange: function(selectedDates, dateStr, instance) {
                                    dateValue = dateStr;
                                    $wire.set('tanggal_kirim', dateStr);
                                    instance.element.setAttribute('data-date-value', dateStr);
                                    updateRate(dateStr, 'sent');
                                },
                                onClose: function(selectedDates, dateStr, instance) {
                                    updateRate(dateStr, 'sent');
                                }
                            });
                         ">
                        <label class="vintage-label">Registry Date</label>
                        <input x-ref="input" type="text" id="tanggalKirim" x-model="dateValue" class="vintage-input w-full" required>
                        <small class="text-xs text-gray-400 block mt-1">(DD/MM/YYYY)</small>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-6 border-t-2 border-dashed border-gray-100">
                    <!-- Front Image -->
                    <div>
                        <label class="vintage-label">Front Visual (Artwork)</label>
                        <div class="relative group">
                            <div onclick="openScanner('d')" class="border-2 border-dashed border-gray-200 rounded-lg p-6 text-center cursor-pointer hover:bg-gray-50 transition group bg-gray-50">
                                <div x-show="!img_d_preview">
                                    <i class="bi bi-camera text-4xl text-gray-300 group-hover:text-pc-blue"></i>
                                    <p class="mt-2 text-xs font-postcard text-gray-400">SCAN FRONT</p>
                                </div>
                                <img x-show="img_d_preview" :src="img_d_preview" class="w-full h-auto max-h-[500px] object-contain rounded border-4 border-white shadow-md bg-gray-100">
                            </div>
                            <div x-show="img_d_preview" class="absolute top-4 right-4 flex gap-2 z-[100]" style="display: none;">
                                <button type="button" onclick="event.stopPropagation(); rotateFinal('d')" class="bg-blue-600 text-white p-2 rounded-full shadow-2xl hover:bg-blue-700 transition flex items-center justify-center border-2 border-white" style="width: 40px; height: 40px;" title="Rotate">
                                    <i class="bi bi-arrow-clockwise text-lg"></i>
                                </button>
                                <button type="button" onclick="event.stopPropagation(); const al = Alpine.$data(this.closest('[x-data]')); if(al) al.img_d_preview = null; Livewire.find(document.querySelector('.register-wrapper').getAttribute('data-wire-id')).set('img_d_data', null)" class="bg-red-600 text-white p-2 rounded-full shadow-2xl hover:bg-red-700 transition flex items-center justify-center border-2 border-white" style="width: 40px; height: 40px; background-color: #ef4444 !important;" title="Delete">
                                    <i class="bi bi-trash text-lg"></i>
                                </button>
                            </div>
                        </div>
                        <input type="file" id="input_d" accept="image/*" class="hidden" onchange="handleFile(event, 'd')">
                    </div>

                    <!-- Back Image -->
                    <div>
                        <label class="vintage-label">Back Message (Postal)</label>
                        <div class="relative group">
                            <div onclick="openScanner('b')" class="border-2 border-dashed border-gray-200 rounded-lg p-6 text-center cursor-pointer hover:bg-gray-50 transition group bg-gray-50">
                                <div x-show="!img_b_preview">
                                    <i class="bi bi-chat-left-text text-4xl text-gray-300 group-hover:text-pc-blue"></i>
                                    <p class="mt-2 text-xs font-postcard text-gray-400">SCAN BACK</p>
                                </div>
                                <img x-show="img_b_preview" :src="img_b_preview" class="w-full h-auto max-h-[500px] object-contain rounded border-4 border-white shadow-md bg-gray-100">
                            </div>
                            <div x-show="img_b_preview" class="absolute top-4 right-4 flex gap-2 z-[100]" style="display: none;">
                                <button type="button" onclick="event.stopPropagation(); rotateFinal('b')" class="bg-blue-600 text-white p-2 rounded-full shadow-2xl hover:bg-blue-700 transition flex items-center justify-center border-2 border-white" style="width: 40px; height: 40px;" title="Rotate">
                                    <i class="bi bi-arrow-clockwise text-lg"></i>
                                </button>
                                <button type="button" onclick="event.stopPropagation(); const al = Alpine.$data(this.closest('[x-data]')); if(al) al.img_b_preview = null; Livewire.find(document.querySelector('.register-wrapper').getAttribute('data-wire-id')).set('img_b_data', null)" class="bg-red-600 text-white p-2 rounded-full shadow-2xl hover:bg-red-700 transition flex items-center justify-center border-2 border-white" style="width: 40px; height: 40px; background-color: #ef4444 !important;" title="Delete">
                                    <i class="bi bi-trash text-lg"></i>
                                </button>
                            </div>
                        </div>
                        <input type="file" id="input_b" accept="image/*" class="hidden" onchange="handleFile(event, 'b')">
                    </div>
                </div>
                
                <!-- Stamps Section -->
                <div class="pt-6 border-t-2 border-dashed border-gray-100">
                     <label class="vintage-label">Philately Collection (Stamps)</label>
                     <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-4 mb-4" id="stamps-grid">
                         @foreach($stamp_data as $index => $stamp)
                            <div class="relative group aspect-square">
                                <img src="{{ $stamp }}" class="w-full h-full object-cover rounded border-2 border-white shadow-sm">
                                <button type="button" wire:click="removeStamp({{ $index }})" class="absolute -top-2 -right-2 bg-pc-red text-white rounded-full w-6 h-6 flex items-center justify-center text-xs shadow-md border-2 border-white">X</button>
                            </div>
                         @endforeach
                         
                         <button type="button" onclick="openScanner('s')" class="aspect-square flex flex-col items-center justify-center border-2 border-dotted border-gray-300 rounded-lg text-gray-400 hover:bg-gray-50 transition bg-gray-50">
                             <i class="bi bi-plus-circle text-2xl"></i>
                         </button>
                     </div>
                     <input type="file" id="input_s" accept="image/*" class="hidden" onchange="handleFile(event, 's')">
                </div>

                @if($type === 'received')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-6 border-t-2 border-dashed border-gray-100" wire:ignore>
                    <div wire:ignore
                         x-data="{ dateValue: @entangle('tanggal_terima') }"
                         x-init="
                            flatpickr($refs.input, {
                                defaultDate: dateValue,
                                altInput: true,
                                altFormat: 'd/m/Y',
                                dateFormat: 'Y-m-d',
                                allowInput: true,
                                onChange: function(selectedDates, dateStr, instance) {
                                    dateValue = dateStr;
                                    $wire.set('tanggal_terima', dateStr);
                                    instance.element.setAttribute('data-date-value', dateStr);
                                },
                                onClose: function(selectedDates, dateStr, instance) {

                                }
                            });
                         ">
                        <label class="vintage-label">Received Date</label>
                        <input x-ref="input" type="text" id="tanggalTerima" x-model="dateValue" class="vintage-input w-full">
                        <small class="text-xs text-gray-400 block mt-1">(DD/MM/YYYY)</small>
                    </div>
                    <div>
                        <label class="vintage-label">Currency</label>
                        <input type="text" id="mata_uang" wire:model.blur="mata_uang" class="vintage-input w-full uppercase" placeholder="USD/EUR/etc" @blur="updateRate()">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 pt-4" wire:ignore>
                    <div>
                        <label class="vintage-label">Amount</label>
                        <input type="number" id="nilai_asli" wire:model.live="nilai_asal" step="0.01" class="vintage-input w-full" placeholder="0.00" oninput="hitungIDR()">
                    </div>
                    <div>
                        <label class="vintage-label">Rate (IDR)</label>
                        <input type="number" id="kurs_idr" wire:model.live="kurs_idr" step="0.01" class="vintage-input w-full" oninput="hitungIDR()">
                    </div>
                    <div>
                        <label class="vintage-label">Total (IDR)</label>
                        <input type="number" id="biaya_prangko" wire:model="biaya_prangko" class="vintage-input w-full bg-blue-50" readonly>
                    </div>
                </div>

                @endif
                
                @if($type === 'sent')
                <div class="pt-6 border-t-2 border-dashed border-gray-100">
                    <label class="vintage-label">Total Postage (IDR)</label>
                    <input type="number" wire:model="biaya_prangko" class="vintage-input w-full text-lg" placeholder="0" required>
                </div>
                @else
                <div class="pt-6 border-t-2 border-dashed border-gray-100">
                    <label class="vintage-label">Calculated Total</label>
                    <div class="text-3xl font-postcard text-pc-blue bg-blue-50 p-4 rounded-lg text-center border-2 border-pc-blue border-dotted">
                        IDR {{ number_format((float)($biaya_prangko ?? 0), 0, ',', '.') }}
                    </div>
                </div>
                @endif

                <div class="pt-6 border-t-2 border-dashed border-gray-100">
                    <label class="vintage-label">Archive Description</label>
                    <textarea wire:model="deskripsi_gambar" rows="3" class="vintage-input w-full" placeholder="Write a short memory about this postcard..."></textarea>
                </div>

                <div class="pt-8">
                    <button type="submit" class="vintage-btn w-full text-xl py-5">
                        <i class="bi bi-journal-check me-2"></i> CONFIRM REGISTRY
                    </button>
                    <a href="{{ route('dashboard') }}" class="block text-center mt-6 text-gray-400 font-postcard hover:text-pc-ink transition">
                        <i class="bi bi-arrow-left"></i> Cancel and Return
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Scanner Modal (Hidden by default) -->
    <div id="scannerModal" class="fixed inset-0 z-50 bg-black flex flex-col hidden">
        <div class="flex-1 relative flex items-center justify-center overflow-hidden bg-black">
             <canvas id="scannerCanvas" class="max-w-full max-h-full touch-none"></canvas>
        </div>
        <div class="h-24 bg-gray-900 flex items-center justify-around px-4">
            <button type="button" onclick="closeScanner()" class="px-4 py-3 bg-gray-600 rounded-lg font-bold text-white uppercase">Cancel</button>
            <button type="button" onclick="rotateSource()" class="px-4 py-3 bg-blue-600 rounded-lg font-bold text-white uppercase"><i class="bi bi-arrow-clockwise"></i> Rotate</button>
            <button type="button" id="btnCropRegister" class="px-4 py-3 bg-green-500 rounded-lg font-bold text-white uppercase">Crop & Use</button>
        </div>
    </div>

    <script src="/js/opencv.js"></script>
    <script src="/js/jscanify.min.js"></script>
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.css') }}">
    <script>
    const defaultPoints = [{x: 0.15, y: 0.15}, {x: 0.85, y: 0.15}, {x: 0.85, y: 0.85}, {x: 0.15, y: 0.85}];
    var points = JSON.parse(JSON.stringify(defaultPoints)), draggingIndex = -1;
    var canvas, ctx, activeMode, originalImage, scanner;

    window.openScanner = (mode) => {
        activeMode = mode;
        document.getElementById('input_' + mode).click();
    };

    window.handleFile = (e, mode) => {
        const input = e.target;
        if (input.files && input.files[0]) {
             if(typeof cv === 'undefined' || !cv.Mat) { alert("Scanner loading..."); return; }
             if(!scanner) scanner = new jscanify();
             const reader = new FileReader();
             reader.onload = (evt) => {
                 originalImage = new Image();
                 originalImage.onload = () => {
                     document.getElementById('scannerModal').classList.remove('hidden');
                     autoDetect();
                 };
                 originalImage.src = evt.target.result;
             };
             reader.readAsDataURL(input.files[0]);
        }
    };

    window.rotateFinal = (mode) => {
        // Get the current component to access data
        const rootEl = document.querySelector('.register-wrapper');
        const componentId = rootEl ? rootEl.getAttribute('data-wire-id') : null;
        const component = Livewire.find(componentId);
        
        const alData = alEl ? window.Alpine.$data(alEl) : null;
        
        const preview = alData ? alData['img_' + mode + '_preview'] : null;
        if(!preview) return;

        const img = new Image();
        img.onload = () => {
            const canvas = document.createElement('canvas');
            canvas.width = img.height; canvas.height = img.width;
            const ctx = canvas.getContext('2d');
            ctx.translate(canvas.width/2, canvas.height/2);
            ctx.rotate(90 * Math.PI / 180);
            ctx.drawImage(img, -img.width/2, -img.height/2);
            const newData = canvas.toDataURL('image/jpeg', 0.85);
            
            if(alData) alData['img_' + mode + '_preview'] = newData;
            if(component) component.set('img_' + mode + '_data', newData);
        };
        img.src = preview;
    };
    
    window.closeScanner = () => document.getElementById('scannerModal').classList.add('hidden');

    window.rotateSource = () => {
        const can = document.createElement('canvas');
        can.width = originalImage.height; can.height = originalImage.width;
        const ctx = can.getContext('2d');
        ctx.translate(can.width/2, can.height/2);
        ctx.rotate(90 * Math.PI / 180);
        ctx.drawImage(originalImage, -originalImage.width/2, -originalImage.height/2);
        originalImage = new Image();
        originalImage.onload = () => autoDetect();
        originalImage.src = can.toDataURL();
    };

    document.addEventListener('DOMContentLoaded', () => {
        canvas = document.getElementById('scannerCanvas');
        ctx = canvas ? canvas.getContext('2d') : null;
        if(canvas) {
            canvas.addEventListener('mousedown', e => startDrag(getMousePos(e)));
            canvas.addEventListener('touchstart', e => { e.preventDefault(); startDrag(getMousePos(e)); });
            window.addEventListener('mousemove', e => { if(draggingIndex !== -1) doDrag(getMousePos(e)); });
            window.addEventListener('touchmove', e => { if(draggingIndex !== -1) doDrag(getMousePos(e)); });
            window.addEventListener('mouseup', () => draggingIndex = -1);
            window.addEventListener('touchend', () => draggingIndex = -1);
        }
    });

    function autoDetect() {
          const maxWidth = window.innerWidth * 0.95;
          const maxHeight = window.innerHeight - 150;
          const ratio = Math.min(maxWidth / originalImage.width, maxHeight / originalImage.height);
          canvas.width = originalImage.width * ratio; canvas.height = originalImage.height * ratio;
          try {
              const resultMat = cv.imread(originalImage);
              const paperContour = scanner.findPaperContour(resultMat);
              if (paperContour) {
                  const corners = scanner.getCornerPoints(paperContour);
                  // Ensure proper order for warpPerspective: TL, TR, BR, BL
                  points = corners.map(p => ({ x: p.x / originalImage.width, y: p.y / originalImage.height }));
              } else { points = JSON.parse(JSON.stringify(defaultPoints)); }
              resultMat.delete();
          } catch (e) { points = JSON.parse(JSON.stringify(defaultPoints)); }
          render();
    }

    function render() {
         ctx.clearRect(0, 0, canvas.width, canvas.height);
         ctx.drawImage(originalImage, 0, 0, canvas.width, canvas.height);
         ctx.strokeStyle = '#00ff00'; ctx.lineWidth = 3; ctx.beginPath();
         points.forEach((p, i) => { const x = p.x * canvas.width, y = p.y * canvas.height; if(i === 0) ctx.moveTo(x, y); else ctx.lineTo(x, y); });
         ctx.closePath(); ctx.stroke();
         points.forEach(p => {
              ctx.fillStyle = '#007bff'; ctx.beginPath(); ctx.arc(p.x * canvas.width, p.y * canvas.height, 8, 0, Math.PI * 2); ctx.fill();
              ctx.strokeStyle = 'white'; ctx.lineWidth = 3; ctx.stroke();
         });
    }

    function getMousePos(e) {
        const rect = canvas.getBoundingClientRect();
        const clientX = e.touches ? e.touches[0].clientX : e.clientX;
        const clientY = e.touches ? e.touches[0].clientY : e.clientY;
        return { x: clientX - rect.left, y: clientY - rect.top };
    }

    function startDrag(pos) {
        points.forEach((p, i) => {
            const dx = pos.x - (p.x * canvas.width), dy = pos.y - (p.y * canvas.height);
            if(Math.sqrt(dx*dx + dy*dy) < 50) draggingIndex = i;
        });
    }

    function doDrag(pos) {
        if(draggingIndex === -1) return;
        points[draggingIndex].x = Math.max(0, Math.min(1, pos.x / canvas.width));
        points[draggingIndex].y = Math.max(0, Math.min(1, pos.y / canvas.height));
        render();
    }
    
    
    document.addEventListener('click', function(e) {
        if (e.target && e.target.id === 'btnCropRegister') {
            processCropRegister();
        }
    });

    window.processCropRegister = function() {
        if (typeof cv === 'undefined' || !cv.Mat) { return; }
        try {
            const src = cv.imread(originalImage);
            const p = points.map(pt => ({ x: pt.x * src.cols, y: pt.y * src.rows }));
            
            // Re-calculate target dimensions properly
            const targetW = Math.max(Math.hypot(p[1].x - p[0].x, p[1].y - p[0].y), Math.hypot(p[2].x - p[3].x, p[2].y - p[3].y));
            const targetH = Math.max(Math.hypot(p[3].x - p[0].x, p[3].y - p[0].y), Math.hypot(p[2].x - p[1].x, p[2].y - p[1].y));
            
            const srcCoords = cv.matFromArray(4, 1, cv.CV_32FC2, [p[0].x, p[0].y, p[1].x, p[1].y, p[2].x, p[2].y, p[3].x, p[3].y]);
            const dstCoords = cv.matFromArray(4, 1, cv.CV_32FC2, [0, 0, targetW, 0, targetW, targetH, 0, targetH]);
            const M = cv.getPerspectiveTransform(srcCoords, dstCoords);
            const dst = new cv.Mat();
            cv.warpPerspective(src, dst, M, new cv.Size(targetW, targetH));
            
            const tempCanvas = document.createElement('canvas'); 
            cv.imshow(tempCanvas, dst);
            const finalCanvas = document.createElement('canvas');
            const MAX_DIM = 1200; 
            let scale = Math.min(1, MAX_DIM / Math.max(targetW, targetH));
            finalCanvas.width = targetW * scale; finalCanvas.height = targetH * scale;
            finalCanvas.getContext('2d').drawImage(tempCanvas, 0, 0, finalCanvas.width, finalCanvas.height);
            const dataUrl = finalCanvas.toDataURL('image/jpeg', 0.85);
            
            const rootEl = document.querySelector('.register-wrapper');
            const componentId = rootEl ? rootEl.getAttribute('data-wire-id') : null;
            const component = Livewire.find(componentId);

            if (component) {
                if (activeMode === 's') { 
                    component.set('stamp_data', [...component.get('stamp_data'), dataUrl]);
                } else { 
                    component.set('img_' + activeMode + '_data', dataUrl);
                    const alEl = document.querySelector('.register-wrapper');
                    if (alEl && window.Alpine) {
                        const data = window.Alpine.$data(alEl);
                        if (data) data['img_' + activeMode + '_preview'] = dataUrl;
                    }
                }
            }
            
            src.delete(); dst.delete(); M.delete(); srcCoords.delete(); dstCoords.delete();
            closeScanner();
        } catch(e) { console.error(e); }
    };


    function getComponent() {
        const root = document.querySelector('.register-wrapper');
        const id = root?.dataset.wireId;
        if (id) return Livewire.find(id);
        return null;
    }

    async function updateCurrencyFromCountry(country) {
        if (!country || country.length < 2) return;
        try {
            const response = await fetch(`https://restcountries.com/v3.1/translation/${encodeURIComponent(country)}`);
            const data = await response.json();
            if (data && data[0] && data[0].currencies) {
                const currencyCode = Object.keys(data[0].currencies)[0];
                const curInput = document.getElementById('mata_uang');
                if (curInput) {
                    curInput.value = currencyCode;
                    const comp = getComponent();
                    if(comp) await comp.set('mata_uang', currencyCode);
                }
                updateRate();
            }
        } catch (e) { }
    }

    async function updateRate(overrideDate = null, type = null) {
        const component = getComponent();
        if(!component) return;
        const cur = document.getElementById('mata_uang')?.value || component.get('mata_uang');
        if (!cur || cur === 'IDR') {
            const inp = document.getElementById('kurs_idr');
            if(inp) inp.value = "1.00";
            component.set('kurs_idr', 1);
            hitungIDR();
            return;
        }
        
        let tglKirim = document.getElementById('tanggalKirim')?.getAttribute('data-date-value');
        if (!tglKirim) tglKirim = document.getElementById('tanggalKirim')?.value;
        if (!tglKirim && component) tglKirim = await component.get('tanggal_kirim');

        if (type === 'sent' && overrideDate) tglKirim = overrideDate;

        const tgl = (tglKirim || new Date().toISOString().split('T')[0]);

        const rateDisplay = document.getElementById('kurs_idr');
        if (rateDisplay) rateDisplay.classList.add('animate-pulse', 'bg-yellow-50');

        const tryFetch = async (url) => {
            try {
                const res = await fetch(url);
                if (!res.ok) return null;
                const data = await res.json();
                if (data.rates && data.rates.IDR) return data.rates.IDR;
            } catch (e) {}
            return null;
        };

        let rate = null;
        rate = await tryFetch(`https://api.frankfurter.app/${tgl}?from=${cur}&to=IDR`);
        if (!rate) rate = await tryFetch(`https://api.frankfurter.app/latest?from=${cur}&to=IDR`);
        if (!rate) rate = await tryFetch(`https://open.er-api.com/v6/latest/${cur}`);

        if (rateDisplay) rateDisplay.classList.remove('animate-pulse', 'bg-yellow-50');
        if (rate) {
            if (rateDisplay) rateDisplay.value = rate.toFixed(2);
            component.set('kurs_idr', rate);
            hitungIDR();
        }
    }

    function hitungIDR() {
       const component = getComponent();
       if(!component) return;
       
       const nilaiInput = document.getElementById('nilai_asli');
       if (!nilaiInput) return; 
       
       const val = parseFloat(nilaiInput.value || 0);
       if (val <= 0) return; 
       
       const rate = parseFloat(document.getElementById('kurs_idr')?.value || 1);
       const totalDisplay = document.getElementById('biaya_prangko');
       const total = Math.round(val * rate);
       if (totalDisplay) totalDisplay.value = total;
       component.set('biaya_prangko', total);
    }


    </script>

@script
    window.livewire_register_id = $wire.id;
    $wire.on('check-currency', (event) => updateCurrencyFromCountry(event.country));
    $wire.on('check-rate', () => updateRate());
@endscript
</div>
