<div class="edit-wrapper paper-texture" x-data="{ 
    new_stamp_previews: @entangle('newStampsBase64'),
    biaya_prangko: @entangle('biaya_prangko'),
    isScannerOpen: false,
    img_d_preview: '{{ $currentFotoDepan ? asset($currentFotoDepan) : '' }}',
    img_b_preview: '{{ $currentFotoBelakang ? asset($currentFotoBelakang) : '' }}'
}" data-wire-id="{{ $this->getId() }}">
    <style>
        .edit-wrapper {
            min-height: 100vh;
            padding: 80px 20px;
        }

        .container { 
            max-width: 1000px; 
            margin: auto; 
            background: white; 
            box-shadow: 0 15px 40px rgba(0,0,0,0.1); 
        }

        .form-inner {
            background: #fff;
            padding: 40px;
            border-radius: 4px;
            position: relative;
            z-index: 2;
        }

        .header { 
            border-bottom: 2px dashed #cbd5e1; 
            padding-bottom: 20px; 
            margin-bottom: 30px; 
            text-align: center;
        }
        
        .header h2 {
            font-family: 'Dancing Script', cursive;
            font-size: 3rem;
            color: var(--pc-ink);
            margin: 0;
        }

        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .full { grid-column: span 2; }
        
        .upload-card { 
            border: 2px dashed #cbd5e1; 
            padding: 20px; 
            text-align: center; 
            border-radius: 8px; 
            cursor: pointer; 
            background: #fdfdfd; 
            transition: 0.3s;
            font-family: 'Special Elite', monospace;
            color: #64748b;
        }
        
        .upload-card:hover {
            background: #f1f5f9;
            border-color: var(--pc-blue);
            color: var(--pc-blue);
        }

        .preview-img { 
            width: 100%; 
            border-radius: 4px; 
            border: 5px solid white; 
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            max-height: 600px; 
            object-fit: contain; 
            background: #f8fbff;
        }


        #scannerModal { position: fixed; z-index: 10000; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.95); flex-direction: column; }
        .scanner-body { flex: 1; display: flex; align-items: center; justify-content: center; overflow: hidden; }
        #scannerCanvas { max-width: 95vw; max-height: 85vh; cursor: crosshair; }
        .scanner-footer { padding: 20px; background: #1a1a1a; display: flex; align-items: center; justify-content: center; gap: 20px; }
        .btn-scan { padding: 12px 30px; border-radius: 4px; border: none; font-family: 'Special Elite', monospace; font-weight: bold; cursor: pointer; color: white; }
        
        .stamp-gallery { display: flex; gap: 15px; flex-wrap: wrap; margin-bottom: 20px; }
        .stamp-item { position: relative; width: 100px; height: 100px; }
        .stamp-item img { 
            width: 100%; 
            height: 100%; 
            object-fit: cover; 
            border-radius: 2px; 
            border: 4px solid white; 
            box-shadow: 0 2px 5px rgba(0,0,0,0.2); 
        }
        .btn-del-stamp { position: absolute; top: -8px; right: -8px; background: var(--pc-red); color: white; border-radius: 50%; width: 24px; height: 24px; text-align: center; line-height: 20px; border: 2px solid white; font-size: 16px; cursor: pointer; z-index: 5; }
        .btn-rot-stamp { position: absolute; top: -8px; left: -8px; background: var(--pc-blue); color: white; border-radius: 50%; width: 24px; height: 24px; text-align: center; line-height: 20px; border: 2px solid white; font-size: 16px; cursor: pointer; z-index: 5; }
    </style>

    <div class="container airmail-border">
        <div class="form-inner">
            <div class="header">
                <h2><i class="bi bi-pencil-square"></i> Archive Registry</h2>
                <div style="font-family: 'Special Elite'; color: #64748b; font-size: 0.9rem;">Updating Log: {{ strtoupper($type) }} ({{ $postcard_id ?: 'NO-ID' }})</div>
            </div>

            <form wire:submit.prevent="preUpdate">
                <div class="form-grid">
                    <div class="form-group"><label class="vintage-label">Postcard ID</label><input type="text" class="vintage-input" wire:model="postcard_id" value="{{ $postcard_id }}" placeholder="e.g. ID-123456">
                        @error('postcard_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group"><label class="vintage-label">Contact Name</label><input type="text" class="vintage-input" wire:model="nama_kontak" value="{{ $nama_kontak }}" placeholder="Addressee Name"></div>
                    <div class="form-group"><label class="vintage-label">Country</label><input type="text" class="vintage-input" id="negara" wire:model="negara" value="{{ $negara }}" @blur="getCurrencyFromCountry()" placeholder="Where is it from/to?">
                        @error('negara') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group"><label class="vintage-label">Phone Number</label><input type="text" class="vintage-input" wire:model="nomor_telepon" value="{{ $nomor_telepon }}" placeholder="e.g. +62 812..."></div>
                    <div class="form-group full">
                        <div class="flex justify-between items-end mb-1">
                            <label class="vintage-label mb-0">Mailing Address</label>
                            <button type="button" wire:click="refreshGeocoding" class="text-xs text-pc-blue hover:underline font-bold" style="font-family: 'Special Elite';">
                                <i class="bi bi-geo-alt-fill"></i> REFRESH LOCATION
                            </button>
                        </div>
                        <textarea class="vintage-input" wire:model="alamat" rows="3" placeholder="Write the full address here...">{{ $alamat }}</textarea>
                        @error('alamat') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        
                        @if (session()->has('geo_success'))
                            <div class="text-green-600 text-xs mt-1 font-bold"><i class="bi bi-check-circle"></i> {{ session('geo_success') }}</div>
                        @endif
                        @if (session()->has('geo_error'))
                            <div class="text-red-600 text-xs mt-1 font-bold"><i class="bi bi-exclamation-circle"></i> {{ session('geo_error') }}</div>
                        @endif
                    </div>
                    <div class="form-group" wire:ignore
                         x-data="{ dateValue: @entangle('tanggal_kirim') }"
                         x-init="
                            const fp = flatpickr($refs.input, {
                                defaultDate: dateValue,
                                altInput: true,
                                altFormat: 'd/m/Y',
                                dateFormat: 'Y-m-d',
                                allowInput: true,
                                onChange: function(selectedDates, dateStr, instance) {
                                    dateValue = dateStr;
                                    $wire.set('tanggal_kirim', dateStr);
                                    instance.element.setAttribute('data-date-value', dateStr);
                                    getAutoKurs(dateStr, 'sent');
                                },
                                onValueUpdate: function(selectedDates, dateStr, instance) {
                                    dateValue = dateStr;
                                    $wire.set('tanggal_kirim', dateStr);
                                    instance.element.setAttribute('data-date-value', dateStr);
                                },
                                onClose: function(selectedDates, dateStr, instance) {
                                    getAutoKurs(dateStr, 'sent');
                                }
                            });
                            fp.altInput.addEventListener('blur', () => {
                                if (fp.input.value) {
                                    $wire.set('tanggal_kirim', fp.input.value);
                                    getAutoKurs(fp.input.value, 'sent');
                                }
                            });
                         ">
                        <label class="vintage-label">Sent Date</label>
                        <input x-ref="input" type="text" class="vintage-input" id="tgl_kirim">
                        <small class="text-xs text-gray-400 block mt-1">(DD/MM/YYYY)</small>
                        @error('tanggal_kirim') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group" wire:ignore
                         x-data="{ dateValue: @entangle('tanggal_terima') }"
                         x-init="
                            const fp = flatpickr($refs.input, {
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
                                onValueUpdate: function(selectedDates, dateStr, instance) {
                                    dateValue = dateStr;
                                    $wire.set('tanggal_terima', dateStr);
                                    instance.element.setAttribute('data-date-value', dateStr);
                                },
                                onClose: function(selectedDates, dateStr, instance) {

                                }
                            });
                            fp.altInput.addEventListener('blur', () => {
                                if (fp.input.value) {
                                    $wire.set('tanggal_terima', fp.input.value);
                                }
                            });
                         ">
                        <label class="vintage-label">Received Date</label>
                        <input x-ref="input" type="text" class="vintage-input" id="tgl_terima">
                        <small class="text-xs text-gray-400 block mt-1">(DD/MM/YYYY)</small>
                    </div>

                    @if($type == 'received')
                    <div class="form-group full" wire:ignore>
                        <label class="vintage-label">Currency & Rate</label>
                        <div style="display:flex; gap:10px;">
                            <input type="number" id="nilai_asli" class="vintage-input" wire:model="nilai_asal" value="{{ $nilai_asal }}" step="0.01" placeholder="0.00" oninput="hitungIDR()">
                            <input type="text" id="mata_uang" class="vintage-input uppercase" wire:model="mata_uang" value="{{ $mata_uang }}" style="width:150px" @blur="getAutoKurs()" placeholder="USD/EUR/etc">
                            <input type="number" id="kurs_idr" class="vintage-input" wire:model="kurs_idr" value="{{ $kurs_idr }}" step="0.01" oninput="hitungIDR()">
                        </div>

                    </div>
                    @endif

                    <div class="form-group full"><label class="vintage-label">Total Postage (IDR)</label><input type="number" id="biaya_prangko" class="vintage-input" wire:model="biaya_prangko" value="{{ $biaya_prangko }}" step="any" placeholder="0"></div>
                    <div class="form-group full"><label class="vintage-label">Archive Description</label><textarea class="vintage-input" wire:model="deskripsi_gambar" rows="3" placeholder="Write a short memory about this postcard...">{{ $deskripsi_gambar }}</textarea></div>

                    <div class="form-group relative">
                        <label class="vintage-label">Front Visual (Artwork)</label>
                        <div class="upload-card" onclick="openScanner('front')">
                            <i class="bi bi-camera" style="font-size: 1.5rem; display: block;"></i>
                            SCAN FRONT
                        </div>
                        <div class="relative mt-4">
                            <img id="p_front" class="preview-img" :src="img_d_preview" x-show="img_d_preview" style="display:none">
                            <div id="btn-actions-front" class="absolute top-4 right-4 flex gap-2 z-[100]" x-show="img_d_preview" style="display:none">
                                <button type="button" onclick="rotateFinal('front')" class="bg-blue-600 text-white p-2 rounded-full shadow-2xl hover:bg-blue-700 transition flex items-center justify-center border-2 border-white" style="width: 40px; height: 40px;" title="Rotate">
                                    <i class="bi bi-arrow-clockwise text-lg"></i>
                                </button>
                                <button type="button" onclick="deletePreview('front')" class="bg-red-600 text-white p-2 rounded-full shadow-2xl hover:bg-red-700 transition flex items-center justify-center border-2 border-white" style="width: 40px; height: 40px; background-color: #ef4444 !important;" title="Delete">
                                    <i class="bi bi-trash text-lg"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="form-group relative">
                        <label class="vintage-label">Back Message (Postal)</label>
                        <div class="upload-card" onclick="openScanner('back')">
                            <i class="bi bi-chat-left-text" style="font-size: 1.5rem; display: block;"></i>
                            SCAN BACK
                        </div>
                        <div class="relative mt-4">
                            <img id="p_back" class="preview-img" :src="img_b_preview" x-show="img_b_preview" style="display:none">
                            <div id="btn-actions-back" class="absolute top-4 right-4 flex gap-2 z-[100]" x-show="img_b_preview" style="display:none">
                                <button type="button" onclick="rotateFinal('back')" class="bg-blue-600 text-white p-2 rounded-full shadow-2xl hover:bg-blue-700 transition flex items-center justify-center border-2 border-white" style="width: 40px; height: 40px;" title="Rotate">
                                    <i class="bi bi-arrow-clockwise text-lg"></i>
                                </button>
                                <button type="button" onclick="deletePreview('back')" class="bg-red-600 text-white p-2 rounded-full shadow-2xl hover:bg-red-700 transition flex items-center justify-center border-2 border-white" style="width: 40px; height: 40px; background-color: #ef4444 !important;" title="Delete">
                                    <i class="bi bi-trash text-lg"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="full" style="border-top: 2px dashed #eee; padding-top: 25px; margin-top: 15px;" wire:ignore>
                        <label class="vintage-label">Philately Collection (Stamps)</label>
                        <div class="stamp-gallery">
                            @foreach($existingStamps as $st)
                                <div class="stamp-item" wire:key="existing-stamp-{{ $st->id }}">
                                    <img src="{{ asset($st->foto_prangko) }}?t={{ time() }}">
                                    <button type="button" class="btn-rot-stamp" wire:click="rotateStamp({{ $st->id }})"><i class="bi bi-arrow-clockwise"></i></button>
                                    <button type="button" class="btn-del-stamp" wire:click="deleteStamp({{ $st->id }})" onclick="return confirm('Delete?')">×</button>
                                </div>
                            @endforeach
                            
                            <template x-for="(stamp, index) in new_stamp_previews" :key="'new-preview-'+index">
                                <div class="stamp-item">
                                    <img :src="stamp">
                                    <button type="button" class="btn-rot-stamp" @click="rotateNewStampPreview(index, stamp)"><i class="bi bi-arrow-clockwise"></i></button>
                                    <button type="button" class="btn-del-stamp" @click="removeNewStampPreview(index)">×</button>
                                </div>
                            </template>
                        </div>
                        
                        <div class="upload-card" onclick="openScanner('stamp')" style="border-style: dotted;">
                            <i class="bi bi-plus-circle" style="font-size: 1.2rem;"></i> ADD STAMP
                        </div>
                    </div>
                </div>

                <div class="mt-8">
                    <button type="submit" class="vintage-btn w-full" wire:loading.attr="disabled" wire:target="preUpdate">
                        <span wire:loading.remove wire:target="preUpdate">CONFIRM LOG UPDATES</span>
                        <span wire:loading wire:target="preUpdate">CHECKING DATA...</span>
                    </button>
                    
                    <!-- Smart Contact Conflict Modal -->
                    @if($showContactConflictModal)
                    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900 bg-opacity-75 backdrop-blur-sm">
                        <div class="bg-[#fdf6e3] rounded-lg shadow-2xl max-w-md w-full border-4 border-[#2c3e50] p-6 relative">
                            <div class="text-center mb-6">
                                <h3 class="text-2xl font-bold text-[#2c3e50] mb-2 font-special" style="font-family: 'Special Elite';">
                                    <i class="bi bi-person-lines-fill"></i> Contact Update
                                </h3>
                                <p class="text-gray-600 text-sm">
                                    This contact <strong>({{ $nama_kontak }})</strong> is shared by other postcards in your archive.
                                </p>
                            </div>
                            
                            <div class="space-y-3">
                                <button type="button" wire:click="resolveContactConflict('update_all')" class="w-full text-left p-4 rounded border-2 border-[#457b9d] bg-white hover:bg-blue-50 transition group">
                                    <div class="font-bold text-[#457b9d] group-hover:text-blue-700">Update for ALL Postcards</div>
                                    <div class="text-xs text-gray-500">The sender moved perfectly. Update their address everywhere.</div>
                                </button>
                                
                                <button type="button" wire:click="resolveContactConflict('create_new')" class="w-full text-left p-4 rounded border-2 border-[#e63946] bg-white hover:bg-red-50 transition group">
                                    <div class="font-bold text-[#e63946] group-hover:text-red-700">Update ONLY this Postcard</div>
                                    <div class="text-xs text-gray-500">Create a new separate contact profile for this specific card.</div>
                                </button>
                            </div>

                            <div class="mt-6 text-center">
                                <button type="button" wire:click="$set('showContactConflictModal', false)" class="text-xs text-gray-500 underline hover:text-gray-800">
                                    Cancel and Review
                                </button>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                
                <div class="flex justify-between items-center mt-8 pt-6 border-t-2 border-dashed border-gray-100">
                    <a href="{{ route('view', ['id' => $id]) }}" style="color:#94a3b8; text-decoration:none; font-family: 'Special Elite'; font-size: 0.9rem;">
                       <i class="bi bi-x-circle"></i> DISCARD
                    </a>
                    <button type="button" 
                            wire:click="deletePostcard" 
                            wire:confirm="Are you sure you want to DELETE this postcard permanently? This cannot be undone."
                            class="text-red-500 hover:text-red-700 transition font-bold" style="font-family: 'Special Elite'; font-size: 0.9rem;">
                        <i class="bi bi-trash"></i> DELETE ARCHIVE
                    </button>
                </div>
            </form>
        </div>
    </div>

    <input type="file" id="hiddenInput" accept="image/*" style="display:none">

    <div wire:ignore>
        <div id="scannerModal" x-show="isScannerOpen" style="position: fixed; z-index: 10000; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.95); flex-direction: column;">
            <div class="scanner-body"><canvas id="scannerCanvas"></canvas></div>
            <div class="scanner-footer">
                <button type="button" class="btn-scan uppercase" style="background:#444" onclick="closeScanner()">Cancel</button>
                <button type="button" class="btn-scan uppercase" style="background:#2563eb" onclick="rotateSource()"><i class="bi bi-arrow-clockwise"></i> Rotate</button>
                <button type="button" id="btnCropEdit" class="btn-scan uppercase" style="background:var(--success)">Crop & Use</button>
            </div>
        </div>
    </div>

    <script src="/js/opencv.js"></script>
    <script src="/js/jscanify.min.js"></script>
    <script>
    let activeMode = '';
    let originalImage = new Image();
    const defaultPoints = [{x: 0.15, y: 0.15}, {x: 0.85, y: 0.15}, {x: 0.85, y: 0.85}, {x: 0.15, y: 0.85}];
    let scanner;
    try { scanner = new jscanify(); } catch(e) { console.error("jscanify load error:", e); }
    const canvas = document.getElementById('scannerCanvas'), ctx = canvas.getContext('2d');
    let points = [], draggingIndex = -1;
        const fileInput = document.getElementById('hiddenInput');

        function openScanner(mode) {
            activeMode = mode;
            fileInput.click();
        }

        fileInput.addEventListener('change', (e) => {
            if (e.target.files && e.target.files[0]) {
                const reader = new FileReader();
                reader.onload = (ev) => {
                    originalImage.onload = () => {
                        const alEl = document.querySelector('.edit-wrapper');
                        if (alEl && window.Alpine) {
                            window.Alpine.$data(alEl).isScannerOpen = true;
                        }
                        autoDetect();
                    };
                    originalImage.src = ev.target.result;
                };
                reader.readAsDataURL(e.target.files[0]);
                e.target.value = "";
            }
        });

        function autoDetect() {
            const ratio = Math.min((window.innerWidth * 0.9) / originalImage.width, (window.innerHeight * 0.8) / originalImage.height);
            canvas.width = originalImage.width * ratio; canvas.height = originalImage.height * ratio;
            try {
                const resultMat = cv.imread(originalImage);
                const paperContour = scanner.findPaperContour(resultMat);
                if (paperContour) {
                    const corners = scanner.getCornerPoints(paperContour);
                    points = corners.map(p => ({ x: p.x / originalImage.width, y: p.y / originalImage.height }));
                } else { points = JSON.parse(JSON.stringify(defaultPoints)); }
                resultMat.delete();
            } catch (e) { points = JSON.parse(JSON.stringify(defaultPoints)); }
            render();
        }

        function render() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.drawImage(originalImage, 0, 0, canvas.width, canvas.height);
            ctx.strokeStyle = '#00ff00'; ctx.lineWidth = 4; ctx.beginPath();
            points.forEach((p, i) => { const x = p.x * canvas.width, y = p.y * canvas.height; if(i === 0) ctx.moveTo(x, y); else ctx.lineTo(x, y); });
            ctx.closePath(); ctx.stroke();
            points.forEach(p => {
                const x = p.x * canvas.width, y = p.y * canvas.height;
                ctx.fillStyle = 'white'; ctx.beginPath(); ctx.arc(x, y, 8, 0, Math.PI * 2); ctx.fill(); 
                ctx.fillStyle = '#007bff'; ctx.beginPath(); ctx.arc(x, y, 6, 0, Math.PI * 2); ctx.fill(); 
            });
        }

        canvas.addEventListener('mousedown', e => {
            const rect = canvas.getBoundingClientRect();
            const x = e.clientX - rect.left, y = e.clientY - rect.top;
            points.forEach((p, i) => { if(Math.sqrt((x - p.x*canvas.width)**2 + (y - p.y*canvas.height)**2) < 40) draggingIndex = i; });
        });
        window.addEventListener('mousemove', e => {
            if(draggingIndex === -1) return;
            const rect = canvas.getBoundingClientRect();
            points[draggingIndex].x = Math.max(0, Math.min(1, (e.clientX - rect.left) / canvas.width));
            points[draggingIndex].y = Math.max(0, Math.min(1, (e.clientY - rect.top) / canvas.height));
            render();
        });
        window.addEventListener('mouseup', () => draggingIndex = -1);

        
        document.addEventListener('click', function(e) {
            if (e.target && e.target.id === 'btnCropEdit') {
                processCropEdit();
            }
        });

        window.deletePreview = (mode) => {
            const rootEl = document.querySelector('.edit-wrapper');
            const alData = window.Alpine ? window.Alpine.$data(rootEl) : null;
            if(alData) {
                if(mode === 'front') alData.img_d_preview = '';
                else alData.img_b_preview = '';
            }
            
            const component = getComponent();
            if(component) component.set(mode === 'front' ? 'newFotoDepanBase64' : 'newFotoBelakangBase64', null);
            
            const fileInput = document.getElementById('hiddenInput');
            if (fileInput) fileInput.value = "";
        };

        window.rotateFinal = (mode) => {
            const rootEl = document.querySelector('.edit-wrapper');
            const alData = window.Alpine ? window.Alpine.$data(rootEl) : null;
            const preview = alData ? (mode === 'front' ? alData.img_d_preview : alData.img_b_preview) : null;
            if(!preview) return;

            const img = new Image();
            img.onload = () => {
                const can = document.createElement('canvas');
                can.width = img.height; can.height = img.width;
                const c = can.getContext('2d');
                c.translate(can.width/2, can.height/2);
                c.rotate(90 * Math.PI / 180);
                c.drawImage(img, -img.width/2, -img.height/2);
                const dataUrl = can.toDataURL('image/jpeg', 0.85);

                if(alData) {
                    if(mode === 'front') alData.img_d_preview = dataUrl;
                    else alData.img_b_preview = dataUrl;
                }

                const componentId = rootEl ? rootEl.getAttribute('data-wire-id') : null;
                const component = Livewire.find(componentId);
                if(component) component.set(mode === 'front' ? 'newFotoDepanBase64' : 'newFotoBelakangBase64', dataUrl, true);
            };
            img.src = preview;
        };

        window.rotateSource = () => {
            const can = document.createElement('canvas');
            can.width = originalImage.height; can.height = originalImage.width;
            const c = can.getContext('2d');
            c.translate(can.width/2, can.height/2);
            c.rotate(90 * Math.PI / 180);
            c.drawImage(originalImage, -originalImage.width/2, -originalImage.height/2);
            originalImage = new Image();
            originalImage.onload = () => autoDetect();
            originalImage.src = can.toDataURL();
        };

        window.processCropEdit = function() {
            if (typeof cv === 'undefined' || !cv.Mat) { return; }
            try {
                const src = cv.imread(originalImage);
                const p = points.map(pt => ({ x: pt.x * src.cols, y: pt.y * src.rows }));
                
                const targetW = Math.max(Math.hypot(p[1].x - p[0].x, p[1].y - p[0].y), Math.hypot(p[2].x - p[3].x, p[2].y - p[3].y));
                const targetH = Math.max(Math.hypot(p[3].x - p[0].x, p[3].y - p[0].y), Math.hypot(p[2].x - p[1].x, p[2].y - p[1].y));
                
                const srcCoords = cv.matFromArray(4, 1, cv.CV_32FC2, [p[0].x, p[0].y, p[1].x, p[1].y, p[2].x, p[2].y, p[3].x, p[3].y]);
                const dstCoords = cv.matFromArray(4, 1, cv.CV_32FC2, [0, 0, targetW, 0, targetW, targetH, 0, targetH]);
                const M = cv.getPerspectiveTransform(srcCoords, dstCoords);
                const dst = new cv.Mat();
                cv.warpPerspective(src, dst, M, new cv.Size(targetW, targetH));
                
                const tempCanvas = document.createElement('canvas'); cv.imshow(tempCanvas, dst);
                const finalCanvas = document.createElement('canvas');
                const MAX_DIM = 1200;
                let scale = Math.min(1, MAX_DIM / Math.max(targetW, targetH));
                finalCanvas.width = targetW * scale; finalCanvas.height = targetH * scale;
                finalCanvas.getContext('2d').drawImage(tempCanvas, 0, 0, finalCanvas.width, finalCanvas.height);
                const dataUrl = finalCanvas.toDataURL('image/jpeg', 0.85);

                const rootEl = document.querySelector('.edit-wrapper');
                const alData = window.Alpine ? window.Alpine.$data(rootEl) : null;
                const componentId = rootEl ? rootEl.getAttribute('data-wire-id') : null;
                const component = Livewire.find(componentId);

                if (component) {
                    if (activeMode === 'front') {
                        if(alData) alData.img_d_preview = dataUrl;
                        component.set('newFotoDepanBase64', dataUrl, true);
                    } else if (activeMode === 'back') {
                        if(alData) alData.img_b_preview = dataUrl;
                        component.set('newFotoBelakangBase64', dataUrl, true);
                    } else if (activeMode === 'stamp') {
                        const alEl = document.querySelector('.edit-wrapper');
                        const alData = alEl && window.Alpine ? window.Alpine.$data(alEl) : null;
                        if (alData) {
                            if (!Array.isArray(alData.new_stamp_previews)) {
                                alData.new_stamp_previews = [];
                            }
                            alData.new_stamp_previews.push(dataUrl);
                            component.set('newStampsBase64', alData.new_stamp_previews, true);
                        }
                    }
                }
                
                src.delete(); dst.delete(); M.delete(); srcCoords.delete(); dstCoords.delete(); 
                closeScanner();
            } catch(e) { console.error(e); }
        };

        window.rotateNewStampPreview = (index, base64) => {
            const img = new Image();
            img.onload = () => {
                const can = document.createElement('canvas');
                can.width = img.height; can.height = img.width;
                const c = can.getContext('2d');
                c.translate(can.width/2, can.height/2);
                c.rotate(90 * Math.PI / 180);
                c.drawImage(img, -img.width/2, -img.height/2);
                const dataUrl = can.toDataURL('image/jpeg', 0.85);
                
                const rootEl = document.querySelector('.edit-wrapper');
                const alData = window.Alpine ? window.Alpine.$data(rootEl) : null;
                if (alData) alData.new_stamp_previews[index] = dataUrl;

                const componentId = rootEl ? rootEl.getAttribute('data-wire-id') : null;
                const component = Livewire.find(componentId);
                if (component) {
                    const alData = window.Alpine ? window.Alpine.$data(rootEl) : null;
                    if (alData) {
                        alData.new_stamp_previews[index] = dataUrl;
                        component.set('newStampsBase64', alData.new_stamp_previews, true);
                    }
                }
            };
            img.src = base64;
        };

        window.removeNewStampPreview = (index) => {
            const rootEl = document.querySelector('.edit-wrapper');
            const alData = window.Alpine ? window.Alpine.$data(rootEl) : null;
            if (alData) {
                alData.new_stamp_previews.splice(index, 1);
                const comp = getComponent();
                if (comp) comp.set('newStampsBase64', alData.new_stamp_previews, true);
            }
        };


        function closeScanner() { 
            const alEl = document.querySelector('.edit-wrapper');
            if (alEl && window.Alpine) {
                window.Alpine.$data(alEl).isScannerOpen = false;
            }
        }

        function getComponent() {
            if (window.livewire_edit_id) {
                const comp = Livewire.find(window.livewire_edit_id);
                if (comp) return comp;
            }
            const root = document.querySelector('.edit-wrapper');
            const id = root?.getAttribute('data-wire-id');
            if (id) return Livewire.find(id);
            return null;
        }

        async function getCurrencyFromCountry() {
            const country = document.getElementById('negara').value;
            if (country.length < 2) return;
            try {
                const res = await fetch(`https://restcountries.com/v3.1/translation/${encodeURIComponent(country)}`);
                const data = await res.json();
                if (data && data[0] && data[0].currencies) {
                     const currencyCode = Object.keys(data[0].currencies)[0];
                     const curInput = document.getElementById('mata_uang');
                     if (curInput) {
                         curInput.value = currencyCode;
                         const comp = getComponent();
                         if(comp) await comp.set('mata_uang', currencyCode);
                     }
                     getAutoKurs();
                }
            } catch(e) { }
        }
        
        async function getAutoKurs(overrideDate = null, type = null) {
            const component = getComponent();
            if(!component) return;
            
            const cur = document.getElementById('mata_uang')?.value || component.get('mata_uang');
            const inp = document.getElementById('kurs_idr');

            if (!cur || cur === 'IDR') {
                if(inp) inp.value = "1.00";
                if(component) component.set('kurs_idr', 1);
                hitungIDR();
                return;
            }

            let tglKirim = document.getElementById('tgl_kirim')?.getAttribute('data-date-value');
            if (!tglKirim) tglKirim = document.getElementById('tgl_kirim')?.value;
            if (!tglKirim && component) tglKirim = await component.get('tanggal_kirim');

            const tgl = (tglKirim || new Date().toISOString().split('T')[0]);

            if (inp) inp.classList.add('animate-pulse', 'bg-yellow-50');

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

            if (inp) inp.classList.remove('animate-pulse', 'bg-yellow-50');

            if (rate) {
                if(inp) inp.value = rate.toFixed(2);
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
           const cur = document.getElementById('mata_uang')?.value || 'IDR';
           
           let total;
           if (cur === 'IDR') {
               total = Math.round(val);
               const rateInput = document.getElementById('kurs_idr');
               if (rateInput) rateInput.value = "1.00";
               component.set('kurs_idr', 1, true);
           } else {
               const rate = parseFloat(document.getElementById('kurs_idr')?.value || 1);
               total = Math.round(val * rate);
           }
           
           const totalDisplay = document.getElementById('biaya_prangko');
           if (totalDisplay) totalDisplay.value = total;

           const alEl = document.querySelector('.edit-wrapper');
           if (alEl && window.Alpine) {
               window.Alpine.$data(alEl).biaya_prangko = total;
           }
           component.set('biaya_prangko', total);
        }

    </script>

    @script
        window.livewire_edit_id = $wire.id;
    @endscript
</div>
