<div class="edit-wrapper paper-texture" data-wire-id="{{ $this->getId() }}">
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
            margin-top: 15px; 
            border: 5px solid white; 
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            max-height: 300px; 
            object-fit: contain; 
        }

        /* Scanner Modal Styles */
        #scannerModal { display: none; position: fixed; z-index: 10000; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.95); flex-direction: column; }
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

    <!-- Scripts for Scanner -->
    <script src="{{ asset('vendor/opencv/opencv.js') }}" type="text/javascript"></script>
    <script src="{{ asset('vendor/jscanify/jscanify.min.js') }}"></script>

    <div class="container airmail-border">
        <div class="form-inner">
            <div class="header">
                <h2><i class="bi bi-pencil-square"></i> Archive Registry</h2>
                <div style="font-family: 'Special Elite'; color: #64748b; font-size: 0.9rem;">Updating Log: {{ strtoupper($type) }} ({{ $postcard_id ?: 'NO-ID' }})</div>
            </div>

            <form wire:submit.prevent="update">
                <div class="form-grid">
                    <div class="form-group"><label class="vintage-label">Postcard ID</label><input type="text" class="vintage-input" wire:model="postcard_id" placeholder="ID-123456"></div>
                    <div class="form-group"><label class="vintage-label">Contact Name</label><input type="text" class="vintage-input" wire:model="nama_kontak" placeholder="Recipient Name"></div>
                    <div class="form-group"><label class="vintage-label">Country</label><input type="text" class="vintage-input" id="negara" wire:model="negara" @blur="getCurrencyFromCountry()" placeholder="Origin Country"></div>
                    <div class="form-group"><label class="vintage-label">Phone Number</label><input type="text" class="vintage-input" wire:model="nomor_telepon" placeholder="+00 000 0000"></div>
                    <div class="form-group full"><label class="vintage-label">Mailing Address</label><textarea class="vintage-input" wire:model="alamat" rows="3" placeholder="Full postal address..."></textarea></div>
                    <div class="form-group"><label class="vintage-label">Sent Date</label><input type="date" class="vintage-input" id="tgl_kirim" wire:model="tanggal_kirim" @change="getAutoKurs()"></div>
                    <div class="form-group"><label class="vintage-label">Received Date</label><input type="date" class="vintage-input" id="tgl_terima" wire:model="tanggal_terima" @change="getAutoKurs()"></div>

                    @if($type == 'received')
                    <div class="form-group full" wire:ignore>
                        <label class="vintage-label">Currency & Rate</label>
                        <div style="display:flex; gap:10px;">
                            <input type="number" id="nilai_asli" class="vintage-input" wire:model="nilai_asal" step="0.01" placeholder="Val" oninput="hitungIDR()">
                            <input type="text" id="mata_uang" class="vintage-input uppercase" wire:model="mata_uang" style="width:150px" @blur="getAutoKurs()" placeholder="CUR">
                            <input type="number" id="kurs_idr" class="vintage-input" wire:model="kurs_idr" step="0.01" oninput="hitungIDR()">
                        </div>

                    </div>
                    @endif

                    <div class="form-group full"><label class="vintage-label">Total Postage (IDR)</label><input type="number" id="biaya_prangko" class="vintage-input" wire:model="biaya_prangko" step="any" placeholder="0"></div>
                    <div class="form-group full"><label class="vintage-label">Archive Description</label><textarea class="vintage-input" wire:model="deskripsi_gambar" rows="3" placeholder="Write a short memory about this postcard..."></textarea></div>

                    <!-- Images -->
                    <div class="form-group">
                        <label class="vintage-label">Front Visual</label>
                        <div class="upload-card" onclick="openScanner('front')">
                            <i class="bi bi-camera" style="font-size: 1.5rem; display: block;"></i>
                            SCAN FRONT
                        </div>
                        <img id="p_front" class="preview-img" src="{{ $currentFotoDepan ? asset($currentFotoDepan) : '' }}" style="{{ $currentFotoDepan ? '' : 'display:none' }}">
                    </div>

                    <div class="form-group">
                        <label class="vintage-label">Back Message</label>
                        <div class="upload-card" onclick="openScanner('back')">
                            <i class="bi bi-camera" style="font-size: 1.5rem; display: block;"></i>
                            SCAN BACK
                        </div>
                        <img id="p_back" class="preview-img" src="{{ $currentFotoBelakang ? asset($currentFotoBelakang) : '' }}" style="{{ $currentFotoBelakang ? '' : 'display:none' }}">
                    </div>

                    <!-- Stamps -->
                    <div class="full" style="border-top: 2px dashed #eee; padding-top: 25px; margin-top: 15px;">
                        <label class="vintage-label">Philately Collection (Stamps)</label>
                        <div class="stamp-gallery">
                            @foreach($existingStamps as $st)
                                <div class="stamp-item">
                                    <img src="{{ asset($st->foto_prangko) }}?t={{ time() }}">
                                    <button type="button" class="btn-rot-stamp" wire:click="rotateStamp({{ $st->id }})"><i class="bi bi-arrow-clockwise"></i></button>
                                    <button type="button" class="btn-del-stamp" wire:click="deleteStamp({{ $st->id }})" onclick="return confirm('Delete?')">×</button>
                                </div>
                            @endforeach
                            
                            <!-- New Stamps Preview -->
                            <div id="new-stamps-container" style="display: contents;"></div>
                        </div>
                        
                        <div class="upload-card" onclick="openScanner('stamp')" style="border-style: dotted;">
                            <i class="bi bi-plus-circle" style="font-size: 1.2rem;"></i> ADD STAMP
                        </div>
                    </div>
                </div>

                <button type="submit" class="vintage-btn w-full mt-8">CONFIRM LOG UPDATES</button>
                
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

    <!-- Hidden Input for File Selection -->
    <input type="file" id="hiddenInput" accept="image/*" style="display:none">

    <!-- Scanner Modal -->
    <div id="scannerModal">
        <div class="scanner-body"><canvas id="scannerCanvas"></canvas></div>
        <div class="scanner-footer">
            <button class="btn-scan" style="background:#444" onclick="closeScanner()">CANCEL</button>
            <button class="btn-scan" style="background:var(--success)" id="btnWarp">CROP & USE</button>
        </div>
    </div>

    <script>
        const scanner = new jscanify();
        let activeMode = '', originalImage = new Image();
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
                        document.getElementById('scannerModal').style.display = 'flex';
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
                } else { points = [{x: 0.15, y: 0.15}, {x: 0.85, y: 0.15}, {x: 0.85, y: 0.85}, {x: 0.15, y: 0.85}]; }
                resultMat.delete();
            } catch (e) { points = [{x: 0.15, y: 0.15}, {x: 0.85, y: 0.15}, {x: 0.85, y: 0.85}, {x: 0.15, y: 0.85}]; }
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
                ctx.fillStyle = 'white'; ctx.beginPath(); ctx.arc(x, y, 14, 0, Math.PI * 2); ctx.fill(); 
                ctx.fillStyle = '#007bff'; ctx.beginPath(); ctx.arc(x, y, 10, 0, Math.PI * 2); ctx.fill(); 
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

        document.getElementById('btnWarp').onclick = function() {
            const src = cv.imread(originalImage);
            const p = points.map(pt => ({ x: pt.x * src.cols, y: pt.y * src.rows }));
            const targetW = Math.max(Math.hypot(p[1].x - p[0].x, p[1].y - p[0].y), Math.hypot(p[2].x - p[3].x, p[2].y - p[3].y));
            const targetH = Math.max(Math.hypot(p[3].x - p[0].x, p[3].y - p[0].y), Math.hypot(p[2].x - p[1].x, p[2].y - p[1].y));
            const srcCoords = cv.matFromArray(4, 1, cv.CV_32FC2, [p[0].x, p[0].y, p[1].x, p[1].y, p[2].x, p[2].y, p[3].x, p[3].y]);
            const dstCoords = cv.matFromArray(4, 1, cv.CV_32FC2, [0, 0, targetW, 0, targetW, targetH, 0, targetH]);
            const M = cv.getPerspectiveTransform(srcCoords, dstCoords), dst = new cv.Mat();
            cv.warpPerspective(src, dst, M, new cv.Size(targetW, targetH));
            
            const tempCanvas = document.createElement('canvas'); cv.imshow(tempCanvas, dst);
            const finalCanvas = document.createElement('canvas');
            const MAX_DIM = 1200;
            let scale = Math.min(1, MAX_DIM / Math.max(tempCanvas.width, tempCanvas.height));
            finalCanvas.width = tempCanvas.width * scale; finalCanvas.height = tempCanvas.height * scale;
            finalCanvas.getContext('2d').drawImage(tempCanvas, 0, 0, finalCanvas.width, finalCanvas.height);
            const dataUrl = finalCanvas.toDataURL('image/jpeg', 0.8);

            // Pass to Livewire
            if (activeMode === 'front') {
                @this.set('newFotoDepanBase64', dataUrl);
                document.getElementById('p_front').src = dataUrl;
                document.getElementById('p_front').style.display = 'block';
            } else if (activeMode === 'back') {
                @this.set('newFotoBelakangBase64', dataUrl);
                document.getElementById('p_back').src = dataUrl;
                document.getElementById('p_back').style.display = 'block';
            } else if (activeMode === 'stamp') {
                // Determine index for array append (simple approximation, Livewire array access is tricky from JS direct push)
                // We'll let Livewire manage the array. We bind to a temporary variable or method?
                // Best way for array in Livewire from JS: $wire.set('newStampsBase64', [...old, new])
                // Or use a method: $wire.call('addStamp', dataUrl) -> but that triggers roundtrip.
                // Alternative: $wire.newStampsBase64.push(dataUrl) ?? No.
                // We will append to a local JS array and sync, OR just wire:model a hidden input (hard with arrays).
                // Let's use a Livewire hook or method since array update is complex.
                // Wait, simply accessing current value:
                let currentStamps = @this.get('newStampsBase64') || [];
                currentStamps.push(dataUrl);
                @this.set('newStampsBase64', currentStamps);
                
                // Show preview locally
                const div = document.createElement('div'); div.className = 'stamp-item';
                div.innerHTML = `<img src="${dataUrl}">`;
                document.getElementById('new-stamps-container').appendChild(div);
            }

            src.delete(); dst.delete(); M.delete(); srcCoords.delete(); dstCoords.delete(); 
            closeScanner();
        };

        function closeScanner() { document.getElementById('scannerModal').style.display = 'none'; }

        // --- Exchange Rate Logics (Global) ---
        function getComponent() {
            const root = document.querySelector('.edit-wrapper');
            const id = root?.dataset.wireId;
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
        
        async function getAutoKurs() {
            const component = getComponent();
            if(!component) return;
            const cur = document.getElementById('mata_uang')?.value || component.get('mata_uang');
            const tglKirim = document.getElementById('tgl_kirim')?.value;
            const tglTerima = document.getElementById('tgl_terima')?.value;
            const tgl = tglTerima || tglKirim || new Date().toISOString().split('T')[0];
            const inp = document.getElementById('kurs_idr');
            
            if (!cur || cur === 'IDR') {
                if(inp) inp.value = "1.00";
                component.set('kurs_idr', 1);
                hitungIDR();
                return;
            }

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
           const val = parseFloat(document.getElementById('nilai_asli')?.value || 0);
           const rate = parseFloat(document.getElementById('kurs_idr')?.value || 1);
           const totalDisplay = document.getElementById('biaya_prangko');
           if (val >= 0) {
               const total = Math.round(val * rate);
               if (totalDisplay) totalDisplay.value = total;
               component.set('biaya_prangko', total);
           }
        }



@script
    window.livewire_edit_id = $wire.id;
@endscript
</div>
