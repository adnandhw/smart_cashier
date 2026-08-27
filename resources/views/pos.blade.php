@extends('layouts.app')

@section('title', 'Kasir AI (POS)')
@section('page_title', 'Smart Cashier POS')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 h-auto lg:h-[calc(100vh-8.5rem)] overflow-visible lg:overflow-hidden">

    <!-- Left Column: Camera Viewer & Detection Info (7 Columns) -->
    <div class="lg:col-span-7 flex flex-col gap-4 h-auto lg:h-full overflow-visible lg:overflow-hidden">
        
        <!-- Live AI Camera Panel -->
        <div class="glass-card rounded-[24px] p-5 flex flex-col h-[450px] shrink-0 overflow-hidden shadow-xs">
            <!-- Header status inside camera panel -->
            <div class="flex items-center justify-between mb-3 shrink-0">
                <div class="flex items-center gap-2.5">
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                    </span>
                    <h3 class="font-bold text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">Live AI Camera Feed</h3>
                </div>
                
                <div class="flex items-center gap-2">
                    <!-- Auto-Add Switcher -->
                    <div class="flex items-center gap-2 bg-white border border-slate-200 px-3 py-1 rounded-xl text-xs font-semibold text-slate-600">
                        <span>Auto-Add ({{ round(($settings['confidence_threshold'] ?? 0.90) * 100) }}%+):</span>
                        <label class="switch-container">
                            <input type="checkbox" id="auto-add-toggle" class="switch-input" checked>
                            <span class="switch-slider"></span>
                        </label>
                    </div>

                    <!-- Toggle Camera Button -->
                    <button id="camera-toggle-btn" onclick="toggleCamera()" class="px-4 py-1.5 text-xs font-bold bg-white border border-slate-200 text-slate-600 hover:bg-amber-400 hover:text-white hover:border-amber-400 rounded-xl transition-all shadow-xs hover:shadow-md hover:shadow-amber-500/15 flex items-center gap-2">
                        <i data-lucide="video" class="w-3.5 h-3.5"></i> <span id="camera-btn-text">Aktifkan Kamera</span>
                    </button>
                </div>
            </div>

            <!-- Video Player Overlay Viewport -->
            <div class="flex-1 bg-slate-950 rounded-2xl relative flex items-center justify-center overflow-hidden border border-slate-200/50 dark:border-slate-800 shadow-inner">
                <!-- Video tag (hidden content, parsed by canvas) -->
                <video id="webcam-feed" autoplay playsinline muted class="absolute inset-0 w-full h-full object-cover pointer-events-none opacity-0 transition-opacity duration-300"></video>
                
                <!-- Bounding Box & Frame rendering canvas -->
                <canvas id="detection-canvas" class="absolute inset-0 w-full h-full object-cover pointer-events-none z-10"></canvas>
                
                <!-- Placeholder Screen -->
                <div id="camera-placeholder" class="text-center z-0 p-6 flex flex-col items-center justify-center space-y-3">
                    <div class="w-16 h-16 rounded-full bg-slate-900 dark:bg-slate-900 border border-slate-850 flex items-center justify-center text-slate-500">
                        <i data-lucide="video-off" class="w-8 h-8"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-700 dark:text-slate-300">Kamera Dinonaktifkan</h4>
                        <p class="text-xs text-slate-450 dark:text-slate-500 max-w-sm mt-1 mx-auto">Klik tombol "Aktifkan Kamera" di atas untuk memulai deteksi aneka kue secara otomatis.</p>
                    </div>
                </div>

                <!-- Model Loader Spinner -->
                <div id="model-loader-overlay" class="absolute inset-0 bg-slate-950/90 backdrop-blur-sm z-20 hidden flex-col items-center justify-center space-y-3">
                    <div class="w-10 h-10 border-4 border-amber-500 border-t-transparent rounded-full animate-spin"></div>
                    <div class="text-center">
                        <h5 class="text-xs font-bold text-slate-200 uppercase tracking-wider">Mengunduh Model YOLOv8-Nano</h5>
                        <p class="text-[10px] text-slate-450 mt-1 max-w-[240px] leading-relaxed">Menginisialisasi ONNX Runtime Web. Unduhan pertama (~12MB) membutuhkan waktu beberapa saat.</p>
                    </div>
                </div>
            </div>
            
            <!-- AI Metrics Indicator Footer -->
            <div class="mt-3 flex items-center justify-between text-[11px] text-slate-450 dark:text-slate-500 font-semibold px-1 flex-shrink-0">
                <div class="flex items-center gap-4">
                    <span class="flex items-center gap-1"><i data-lucide="cpu" class="w-3.5 h-3.5 text-slate-400"></i> YOLOv8-Nano Edge ONNX</span>
                    <span class="flex items-center gap-1"><i data-lucide="activity" class="w-3.5 h-3.5 text-slate-400"></i> FPS: <span id="fps-counter" class="font-mono text-slate-650 dark:text-slate-300">0</span></span>
                    <span class="flex items-center gap-1"><i data-lucide="timer" class="w-3.5 h-3.5 text-slate-400"></i> Latency: <span id="latency-counter" class="font-mono text-slate-650 dark:text-slate-300">0ms</span></span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="h-2 w-2 rounded-full bg-slate-350" id="status-dot"></span>
                    <span id="status-text" class="text-slate-400 uppercase tracking-wider text-[10px]">Ready</span>
                </div>
            </div>
        </div>

        <!-- Detected Current List logs (Bottom Left) -->
        <div class="glass-card rounded-[22px] p-4 h-40 flex flex-col shrink-0 shadow-sm overflow-hidden">
            <h4 class="font-bold text-[10px] text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2 shrink-0">Produk Terdeteksi (Live)</h4>
            <div class="flex-1 overflow-y-auto pr-1">
                <div id="detections-list" class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    <div class="col-span-full py-6 text-center text-slate-400 dark:text-slate-500 text-xs">
                        <i data-lucide="scan" class="w-6 h-6 mx-auto mb-1.5 opacity-55 animate-pulse"></i>
                        Arahkan kamera ke produk untuk mendeteksi.
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Right Column: Shopping Cart & Totals (5 Columns) -->
    <div class="lg:col-span-5 flex flex-col h-auto lg:h-full overflow-visible lg:overflow-hidden glass-card rounded-[22px] shadow-sm">
        
        <!-- Cart Header -->
        <div class="p-4 border-b border-slate-100 flex items-center justify-between shrink-0 bg-slate-50">
            <div class="flex items-center gap-2">
                <i data-lucide="shopping-cart" class="w-5 h-5 text-amber-500"></i>
                <h3 class="font-bold text-sm text-slate-700">Keranjang Belanja</h3>
            </div>
            <button onclick="clearCart()" class="text-xs text-rose-500 hover:text-rose-600 transition-colors font-bold flex items-center gap-1">
                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Kosongkan
            </button>
        </div>

        <!-- Cart Items List Scroll Container -->
        <div class="flex-1 overflow-y-auto p-4 space-y-3 bg-white" id="cart-container">
            <!-- Empty state placeholder -->
            <div class="text-center py-20 text-slate-400 space-y-2">
                <i data-lucide="shopping-bag" class="w-10 h-10 mx-auto opacity-45"></i>
                <p class="text-sm font-semibold">Keranjang Masih Kosong</p>
                <p class="text-xs text-slate-450 max-w-[200px] mx-auto">Tambahkan kue melalui deteksi kamera atau pilih langsung dari katalog.</p>
            </div>
        </div>

        <!-- Quick Catalog Fast Selection Picker -->
        <div class="p-4 border-t border-b border-slate-100 bg-slate-50 shrink-0 h-44 flex flex-col overflow-hidden">
            <div class="flex items-center justify-between mb-2 shrink-0">
                <span class="text-[10px] font-bold text-slate-450 uppercase tracking-wider">Katalog Produk UMKM</span>
                <!-- Fast Search input -->
                <div class="relative w-44">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-450">
                        <i data-lucide="search" class="w-3.5 h-3.5"></i>
                    </span>
                    <input type="text" id="catalog-search" oninput="filterCatalog()" placeholder="Cari kue..." 
                           class="w-full bg-white border border-slate-200 focus:border-amber-500 rounded-xl py-1.5 pl-8 pr-3 text-[10px] font-semibold outline-none transition-all text-slate-800">
                </div>
            </div>
            
            <!-- Horizontal Scroll items -->
            <div id="catalog-scroll-list" class="flex-1 overflow-x-auto overflow-y-hidden pb-1 flex gap-3 whitespace-nowrap scrollbar-thin">
                @foreach($products as $p)
                    <button id="catalog-btn-{{ $p->id }}" 
                            data-name="{{ strtolower($p->name) }}"
                            data-coco="{{ strtolower($p->coco_class ?? '') }}"
                            data-category="{{ strtolower($p->category->name ?? '') }}"
                            onclick="addToCart({{ $p->id }}, '{{ addslashes($p->name) }}', {{ $p->price }}, {{ $p->stock }})"
                            class="bg-white border border-slate-200/60 hover:border-amber-500 hover:shadow-md hover:shadow-amber-500/5 p-3 rounded-2xl text-left w-36 transition-all shrink-0 shadow-xs group">
                        <span class="block text-xs font-bold text-slate-800 truncate group-hover:text-amber-600">{{ $p->name }}</span>
                        <span class="block text-[9px] text-slate-400 truncate font-medium uppercase mt-0.5">{{ $p->category->name ?? 'Aneka Kue' }}</span>
                        <div class="flex items-center justify-between mt-2.5">
                            <span class="text-xs font-extrabold text-amber-600 font-mono">Rp{{ number_format($p->price, 0, ',', '.') }}</span>
                            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded @if($p->stock < 10) bg-rose-500/10 text-rose-500 @else bg-slate-100 text-slate-500 @endif">Stok: {{ $p->stock }}</span>
                        </div>
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Checkout Summary Form Panel -->
        <div class="p-4 bg-white shrink-0 space-y-3">
            <!-- Totals grid layout -->
            <div class="grid grid-cols-2 gap-3 text-xs border-b border-slate-200 pb-3">
                <div class="space-y-1.5">
                    <div class="flex justify-between text-slate-500 font-medium">
                        <span>Subtotal:</span>
                        <span class="font-bold text-slate-800" id="subtotal-text">Rp0</span>
                    </div>
                </div>
                <div class="space-y-1.5 border-l border-slate-200 pl-3">
                    <div class="flex items-center justify-between text-slate-500 font-medium">
                        <span>Diskon (Rp):</span>
                        <input type="number" id="discount-input" oninput="updateTotals()" placeholder="0" 
                               class="w-24 bg-white border border-slate-200 rounded-lg py-1 px-2 text-right font-bold text-xs outline-none focus:border-amber-500 text-slate-800">
                    </div>
                    <div class="flex justify-between font-bold text-slate-800 text-sm">
                        <span>Grand Total:</span>
                        <span class="text-amber-600" id="grand-total-text">Rp0</span>
                    </div>
                </div>
            </div>

            <!-- Cash checkout row -->
            <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 sm:items-center">
                <div class="flex-1">
                    <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Uang Dibayarkan (Tunai)</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-450 font-extrabold text-xs">Rp</span>
                        <input type="number" id="cash-paid" oninput="calculateChange()" placeholder="0" 
                               class="w-full bg-white border border-slate-200 focus:border-amber-500 rounded-xl py-2.5 pl-9 pr-3 text-sm font-extrabold tracking-wide outline-none transition-all text-slate-800">
                    </div>
                </div>
                <div class="w-full sm:w-48">
                    <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Kembalian</label>
                    <div class="px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-center">
                        <span class="text-sm font-black font-mono text-emerald-600" id="change-text">Rp0</span>
                    </div>
                </div>
            </div>

            <!-- Big Checkouts -->
            <button onclick="submitCheckout()" 
                    class="w-full py-3.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-white rounded-xl font-bold font-display shadow-lg shadow-amber-500/10 hover:shadow-amber-500/25 transition-all transform hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center gap-2">
                <i data-lucide="circle-check-big" class="w-5 h-5"></i> PROSES CHECKOUT TRANSAKSI
            </button>
        </div>

    </div>

</div>

<!-- INVOICE RECEIPT MODAL -->
<div id="receipt-modal" class="fixed inset-0 z-50 overflow-y-auto hidden flex items-center justify-center p-4">
    <!-- Overlay backdrop -->
    <div class="absolute inset-0 bg-slate-950/60 backdrop-blur-xs" onclick="closeReceiptModal()"></div>
    
    <!-- Receipt card container -->
    <div class="relative bg-white border border-slate-200 rounded-[28px] max-w-sm w-full shadow-2xl p-6 overflow-hidden transform scale-95 transition-all duration-300">
        <!-- Success Badge -->
        <div class="flex flex-col items-center justify-center text-center pb-4 mb-4 border-b border-dashed border-slate-200">
            <div class="w-12 h-12 rounded-full bg-emerald-500/15 text-emerald-600 flex items-center justify-center text-lg mb-3">
                <i data-lucide="check-circle" class="w-6 h-6"></i>
            </div>
            <h3 class="text-base font-extrabold text-slate-950">Pembayaran Sukses!</h3>
            <p class="text-[10px] text-slate-500 font-extrabold uppercase mt-0.5 tracking-wider">Aneka Kue Pak Yanto</p>
        </div>

        <!-- Receipt parameters -->
        <div class="space-y-2.5 text-xs text-slate-600">
            <div class="flex justify-between">
                <span>Invoice No:</span>
                <span class="font-mono font-bold text-slate-950" id="rec-invoice">-</span>
            </div>
            <div class="flex justify-between">
                <span>Tanggal:</span>
                <span class="font-bold text-slate-950" id="rec-date">-</span>
            </div>
            <div class="flex justify-between">
                <span>Kasir:</span>
                <span class="font-bold text-slate-950">{{ Auth::user()->name }}</span>
            </div>
            
            <div class="space-y-1 text-xs text-slate-600 border-b border-dashed border-slate-200 pb-3 mb-3">
                <div class="flex justify-between">
                    <span>Subtotal:</span>
                    <span class="font-bold text-slate-950" id="rec-subtotal">-</span>
                </div>
                <div class="flex justify-between">
                    <span>Diskon:</span>
                    <span class="font-bold text-rose-600" id="rec-discount">-</span>
                </div>
            </div>

            <div class="flex justify-between text-sm font-black border-b border-dashed border-slate-200 pb-3 mb-3">
                <span class="text-slate-950">Grand Total:</span>
                <span class="text-emerald-600 font-black" id="rec-total">-</span>
            </div>

            <div class="flex justify-between">
                <span>Tunai Diterima:</span>
                <span class="font-bold text-slate-950 font-mono" id="rec-paid">-</span>
            </div>
            <div class="flex justify-between">
                <span>Kembalian:</span>
                <span class="font-bold text-emerald-600 font-mono" id="rec-change">-</span>
            </div>
        </div>

        <!-- Buttons layout -->
        <div class="mt-6 flex gap-3">
            <button onclick="closeReceiptModal()" class="flex-1 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold transition-all text-xs border border-slate-200">
                Transaksi Baru
            </button>
            <button onclick="window.print()" class="flex-1 py-3 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold transition-all text-xs flex items-center justify-center gap-1.5 shadow-md shadow-emerald-500/20">
                <i data-lucide="printer" class="w-3.5 h-3.5"></i> Cetak Struk
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Load ONNX Runtime Web via CDN -->
<script src="https://cdn.jsdelivr.net/npm/onnxruntime-web/dist/ort.min.js"></script>

<script>
    // Explicitly set WASM paths for ONNX Runtime Web
    ort.env.wasm.wasmPaths = 'https://cdn.jsdelivr.net/npm/onnxruntime-web/dist/';

    // 1. Load Seeding Catalog Cache
    const productsCatalog = @json($products);
    
    // Config values
    const confidenceThreshold = parseFloat("{{ $settings['confidence_threshold'] ?? 0.90 }}");

    // POS Cart State
    let cart = [];

    // Camera and AI Detector engine states
    let localStream = null;
    let cameraActive = false;
    let detectionSession = null;
    let detectionRequestId = null;
    
    // Cooldown tracker for automated cart addition (prevent spam adds)
    let autoAddCooldowns = {};
    const COOLDOWN_MS = 3500; // 3.5 seconds cooldown per product class

    // FPS calculations
    let lastInferenceTime = Date.now();
    let fpsHistory = [];

    // Synthesizer Audio Context Beep sound
    function playBeepSound() {
        try {
            const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioCtx.createOscillator();
            const gainNode = audioCtx.createGain();

            oscillator.connect(gainNode);
            gainNode.connect(audioCtx.destination);

            oscillator.type = 'sine';
            oscillator.frequency.value = 950; // Pitch
            gainNode.gain.setValueAtTime(0.2, audioCtx.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.00001, audioCtx.currentTime + 0.12);

            oscillator.start();
            oscillator.stop(audioCtx.currentTime + 0.12);
        } catch (err) {
            console.error("Synthesizer audio error: ", err);
        }
    }

    /* =========================================================================
     * INFERENCE RUNNER ENGINE (ONNX Runtime YOLOv8-Nano)
     * ========================================================================= */

    // Load YOLOv8 model ONNX Session
    async function initModelSession() {
        if (detectionSession) return true;

        const loader = document.getElementById('model-loader-overlay');
        loader.classList.remove('hidden');
        document.getElementById('status-text').innerText = "Loading Model";
        document.getElementById('status-dot').className = "h-2 w-2 rounded-full bg-amber-500 animate-pulse";

        try {
            // Load custom best.onnx model from Laravel public folder
            detectionSession = await ort.InferenceSession.create('/models/best.onnx', {
                executionProviders: ['wasm'],
                graphOptimizationLevel: 'all'
            });
            console.log("ONNX Model Session Loaded Successfully.");
            loader.classList.add('hidden');
            document.getElementById('status-text').innerText = "Ready";
            document.getElementById('status-dot').className = "h-2 w-2 rounded-full bg-emerald-500";
            return true;
        } catch (err) {
            console.error("Failed to load ONNX model session: ", err);
            loader.classList.add('hidden');
            Swal.fire({
                icon: 'error',
                title: 'Gagal Memuat AI Model',
                text: 'ONNX model best.onnx tidak dapat diunduh atau diproses oleh browser: ' + err.message,
                confirmButtonColor: '#5C1D1B'
            });
            document.getElementById('status-text').innerText = "Error";
            document.getElementById('status-dot').className = "h-2 w-2 rounded-full bg-rose-500";
            return false;
        }
    }

    // Toggle camera Stream
    async function toggleCamera() {
        const btn = document.getElementById('camera-toggle-btn');
        const text = document.getElementById('camera-btn-text');
        const video = document.getElementById('webcam-feed');
        const placeholder = document.getElementById('camera-placeholder');

        if (!cameraActive) {
            // 1. Initialise ONNX session first
            const modelSuccess = await initModelSession();
            if (!modelSuccess) return;

            // 2. Start video stream
            placeholder.classList.add('hidden');
            video.classList.remove('opacity-0');
            video.classList.add('opacity-100');

            try {
                const stream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        width: { ideal: 640 },
                        height: { ideal: 480 },
                        facingMode: "environment"
                    },
                    audio: false
                });

                localStream = stream;
                video.srcObject = stream;
                video.onloadedmetadata = () => {
                    video.play();
                    cameraActive = true;
                    btn.className = "px-4 py-1.5 text-xs font-bold bg-rose-500 hover:bg-rose-400 text-white rounded-xl transition-all shadow-md shadow-rose-500/10 flex items-center gap-2";
                    text.innerText = "Matikan Kamera";
                    
                    // Trigger recursive requestAnimationFrame loop
                    detectionRequestId = requestAnimationFrame(processingLoop);
                };
            } catch (err) {
                console.error("Webcam media access denied: ", err);
                placeholder.classList.remove('hidden');
                video.classList.add('opacity-0');
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Akses Kamera',
                    text: 'Tidak dapat membuka webcam: ' + err.message,
                    confirmButtonColor: '#5C1D1B'
                });
            }
        } else {
            // Stop Camera stream
            stopCameraStream();
            btn.className = "px-4 py-1.5 text-xs font-bold bg-white border border-slate-200 text-slate-600 hover:bg-amber-400 hover:text-white hover:border-amber-400 rounded-xl transition-all shadow-xs hover:shadow-md hover:shadow-amber-500/15 flex items-center gap-2";
            text.innerText = "Aktifkan Kamera";
            placeholder.classList.remove('hidden');
            video.classList.add('opacity-0');
        }
    }

    function stopCameraStream() {
        if (localStream) {
            localStream.getTracks().forEach(track => track.stop());
            localStream = null;
        }
        if (detectionRequestId) {
            cancelAnimationFrame(detectionRequestId);
            detectionRequestId = null;
        }
        cameraActive = false;
        
        // Clear detection canvas drawing
        const canvas = document.getElementById('detection-canvas');
        const ctx = canvas.getContext('2d');
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        
        // Reset detections list
        document.getElementById('detections-list').innerHTML = `
            <div class="col-span-full py-6 text-center text-slate-400 dark:text-slate-555 text-xs">
                <i data-lucide="scan" class="w-6 h-6 mx-auto mb-1.5 opacity-55 animate-pulse"></i>
                Arahkan kamera ke produk untuk mendeteksi.
            </div>
        `;
        lucide.createIcons();
    }

    // Frame loops running inside requestAnimationFrame
    async function processingLoop() {
        if (!cameraActive || !localStream) return;

        const video = document.getElementById('webcam-feed');
        const canvas = document.getElementById('detection-canvas');
        
        // Align canvas dimensions with dynamic video bounding box width/height
        if (video.videoWidth > 0) {
            if (canvas.width !== video.videoWidth || canvas.height !== video.videoHeight) {
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
            }
            
            // Run prediction
            await predictFrame(video, canvas);
        }

        // Keep loop repeating
        if (cameraActive) {
            detectionRequestId = requestAnimationFrame(processingLoop);
        }
    }

    // Image processor helper: Resizes & normalises canvas content to Float32 RGB tensor
    function preprocessImage(video, targetW, targetH) {
        const offscreenCanvas = document.createElement('canvas');
        offscreenCanvas.width = targetW;
        offscreenCanvas.height = targetH;
        const ctx = offscreenCanvas.getContext('2d');
        
        // Draw frame resized to 640x640 square
        ctx.drawImage(video, 0, 0, targetW, targetH);
        
        const imgData = ctx.getImageData(0, 0, targetW, targetH);
        const data = imgData.data; // Size: 640 * 640 * 4 = 1638400 (RGBA)
        
        // Float arrays for RGB channels [1, 3, 640, 640]
        const inputTensor = new Float32Array(targetW * targetH * 3);
        const imageSize = targetW * targetH;

        for (let i = 0; i < imageSize; i++) {
            const r = data[i * 4];
            const g = data[i * 4 + 1];
            const b = data[i * 4 + 2];
            
            // Channel division by 255.0 to normalize
            inputTensor[i] = r / 255.0;                   // R channel
            inputTensor[imageSize + i] = g / 255.0;       // G channel
            inputTensor[imageSize * 2 + i] = b / 255.0;   // B channel
        }

        return inputTensor;
    }

    // Predict objects inside current camera frame
    async function predictFrame(video, overlayCanvas) {
        const startTime = Date.now();
        const inputW = 640;
        const inputH = 640;

        // 1. Preprocess frame to RGB flat arrays
        const preprocessedData = preprocessImage(video, inputW, inputH);

        // 2. Wrap into ONNX Tensor
        const inputTensor = new ort.Tensor('float32', preprocessedData, [1, 3, inputW, inputH]);

        try {
            // 3. Execute model session
            const outputs = await detectionSession.run({ images: inputTensor });
            
            // Get output name (output0)
            const outputNames = detectionSession.outputNames;
            const outputTensor = outputs[outputNames[0]]; // Shape: [1, 25, 8400]
            const outputData = outputTensor.data;         // Size: 210,000 float values

            // Calculate FPS & Inference delay
            const inferenceTime = Date.now() - startTime;
            const fps = Math.round(1000 / (Date.now() - lastInferenceTime));
            lastInferenceTime = Date.now();

            document.getElementById('fps-counter').innerText = fps;
            document.getElementById('latency-counter').innerText = inferenceTime + 'ms';

            // 4. Postprocess outputs
            // Columns: 8400 boxes, Rows: 25 attributes
            // [cx, cy, w, h, class0_score, class1_score, ..., class20_score]
            const numBoxes = 8400;
            const numClasses = 21;
            const scoreThreshold = 0.45; // Base filter
            const iouNmsThreshold = 0.45;

            const detections = [];

            for (let c = 0; c < numBoxes; c++) {
                // Find class with maximum score
                let maxClassScore = 0;
                let classId = -1;

                for (let classIdx = 0; classIdx < numClasses; classIdx++) {
                    const classScore = outputData[(4 + classIdx) * numBoxes + c];
                    if (classScore > maxClassScore) {
                        maxClassScore = classScore;
                        classId = classIdx;
                    }
                }

                // If class score is above threshold, save box
                if (maxClassScore > scoreThreshold) {
                    const cx = outputData[0 * numBoxes + c];
                    const cy = outputData[1 * numBoxes + c];
                    const w = outputData[2 * numBoxes + c];
                    const h = outputData[3 * numBoxes + c];

                    // Convert center coords to corners [x1, y1, w, h]
                    const x = cx - w / 2;
                    const y = cy - h / 2;

                    detections.push({
                        x: x,
                        y: y,
                        w: w,
                        h: h,
                        classId: classId,
                        score: maxClassScore
                    });
                }
            }

            // Apply NMS algorithm to eliminate overlaps
            const keptDetections = applyNmsBoxes(detections, iouNmsThreshold);

            // 5. Draw Bounding Boxes and log detections
            drawBoxes(keptDetections, video, overlayCanvas);
            
            // 6. Handle automation adding & backend logging
            handleAutoAddAndLogs(keptDetections, inferenceTime, fps);

        } catch (err) {
            console.error("Inference execution failed: ", err);
        }
    }

    // NMS calculation function
    function applyNmsBoxes(boxes, threshold) {
        boxes.sort((a, b) => b.score - a.score);
        const keep = [];
        const active = new Array(boxes.length).fill(true);

        for (let i = 0; i < boxes.length; i++) {
            if (!active[i]) continue;
            const boxA = boxes[i];
            keep.push(boxA);

            for (let j = i + 1; j < boxes.length; j++) {
                if (!active[j]) continue;
                const boxB = boxes[j];

                // Scale IoU evaluation
                const iouScore = calculateIoU(boxA, boxB);
                if (iouScore > threshold) {
                    active[j] = false;
                }
            }
        }
        return keep;
    }

    function calculateIoU(boxA, boxB) {
        const xA = Math.max(boxA.x, boxB.x);
        const yA = Math.max(boxA.y, boxB.y);
        const xB = Math.min(boxA.x + boxA.w, boxB.x + boxB.w);
        const yB = Math.min(boxA.y + boxA.h, boxB.y + boxB.h);

        const interWidth = Math.max(0, xB - xA);
        const interHeight = Math.max(0, yB - yA);
        const interArea = interWidth * interHeight;

        const areaA = boxA.w * boxA.h;
        const areaB = boxB.w * boxB.h;

        return interArea / (areaA + areaB - interArea);
    }

    // Render bounding boxes onto visual canvas overlay
    function drawBoxes(detections, video, canvas) {
        const ctx = canvas.getContext('2d');
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        // Get YOLO classes list
        // Ordered mapping of classes matching data.yaml index 0-20
        const classNames = [
            'arem arem', 'bacang', 'donut cokelat', 'ekler', 'horn fla', 'iceberg cheese cake',
            'ketan srikaya', 'lapis pepe cokelat', 'lapis pepe pandan', 'lemper', 'macaroni schotel',
            'nagasari', 'pai buah', 'pastel', 'pisang molen', 'risol mayonese', 'risol segitiga',
            'soes', 'sosis pastry', 'sosis solo', 'tahu baso'
        ];

        // Draw each box
        detections.forEach(det => {
            const className = classNames[det.classId] || 'Produk';
            
            // Map coordinates relative to current display scale
            // YOLO input is 640x640. Overlay display is e.g. video.videoWidth x video.videoHeight.
            const scaleX = canvas.width / 640;
            const scaleY = canvas.height / 640;

            const boxX = det.x * scaleX;
            const boxY = det.y * scaleY;
            const boxW = det.w * scaleX;
            const boxH = det.h * scaleY;

            // Draw rectangle border
            ctx.strokeStyle = '#5C1D1B'; // Amber color
            ctx.lineWidth = 3.5;
            ctx.lineJoin = 'round';
            ctx.strokeRect(boxX, boxY, boxW, boxH);

            // Draw label box
            const scorePercent = Math.round(det.score * 100) + '%';
            const labelText = `${className.toUpperCase()} (${scorePercent})`;
            
            ctx.font = 'bold 11px Inter, sans-serif';
            const textWidth = ctx.measureText(labelText).width;
            
            // Draw label background
            ctx.fillStyle = '#5C1D1B';
            ctx.fillRect(boxX - 1.5, boxY - 20, textWidth + 12, 20);

            // Draw text
            ctx.fillStyle = '#FFFFFF';
            ctx.fillText(labelText, boxX + 4.5, boxY - 6);
        });
    }

    // Auto-Add logic callback and logging server side
    function handleAutoAddAndLogs(detections, inferenceTime, fps) {
        const listEl = document.getElementById('detections-list');
        const autoAddEnabled = document.getElementById('auto-add-toggle').checked;
        const now = Date.now();

        if (detections.length === 0) {
            listEl.innerHTML = `
                <div class="col-span-full py-6 text-center text-slate-400 dark:text-slate-555 text-xs">
                    <i data-lucide="scan" class="w-6 h-6 mx-auto mb-1.5 opacity-55 animate-pulse"></i>
                    Arahkan kamera ke produk untuk mendeteksi.
                </div>
            `;
            lucide.createIcons();
            return;
        }

        const classNames = [
            'arem arem', 'bacang', 'donut cokelat', 'ekler', 'horn fla', 'iceberg cheese cake',
            'ketan srikaya', 'lapis pepe cokelat', 'lapis pepe pandan', 'lemper', 'macaroni schotel',
            'nagasari', 'pai buah', 'pastel', 'pisang molen', 'risol mayonese', 'risol segitiga',
            'soes', 'sosis pastry', 'sosis solo', 'tahu baso'
        ];

        let html = '';

        // Map detections to unique product structures
        const grouped = {};
        detections.forEach(det => {
            const label = classNames[det.classId];
            if (!grouped[label] || det.score > grouped[label].score) {
                grouped[label] = det;
            }
        });

        Object.keys(grouped).forEach(label => {
            const det = grouped[label];
            const confidence = det.score;
            const confidencePercent = Math.round(confidence * 100) + '%';
            
            // Find in seeded products catalog cache
            const matchedProduct = productsCatalog.find(p => p.coco_class === label);
            
            if (matchedProduct) {
                html += `
                    <div class="bg-amber-500/10 dark:bg-amber-400/10 border border-amber-500/20 px-3 py-2 rounded-2xl flex items-center justify-between gap-2 shadow-xs">
                        <div class="min-w-0">
                            <span class="block text-xs font-bold text-amber-700 dark:text-amber-400 truncate leading-tight">${matchedProduct.name}</span>
                            <span class="block text-[9px] text-slate-400 dark:text-slate-500 font-semibold mt-0.5">Conf: ${confidencePercent}</span>
                        </div>
                        <button onclick="addToCart(${matchedProduct.id}, '${escapeJsString(matchedProduct.name)}', ${matchedProduct.price}, ${matchedProduct.stock})" 
                                class="w-6 h-6 rounded-lg bg-amber-500 text-white flex items-center justify-center hover:bg-amber-450 active:scale-95 transition-all text-xs shrink-0">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                        </button>
                    </div>
                `;

                // Auto Add Flow (when confidence is greater than threshold and cooldown is cleared)
                if (confidence >= confidenceThreshold && autoAddEnabled) {
                    const cooldownKey = `prod_${matchedProduct.id}`;
                    if (!autoAddCooldowns[cooldownKey] || (now - autoAddCooldowns[cooldownKey] > COOLDOWN_MS)) {
                        // Play beep
                        playBeepSound();
                        
                        // Add to cart
                        addToCart(matchedProduct.id, matchedProduct.name, matchedProduct.price, matchedProduct.stock);
                        
                        // Record cooldown timestamp
                        autoAddCooldowns[cooldownKey] = now;

                        // Log this detection asynchronously to database
                        logDetectionToDatabase(matchedProduct.id, confidence, inferenceTime, fps);
                    }
                }
            } else {
                // Class exists in model, but not linked to products in catalog DB
                html += `
                    <div class="bg-slate-100 dark:bg-slate-800 border border-slate-200/50 dark:border-slate-700 px-3 py-2 rounded-2xl flex items-center justify-between gap-2 opacity-60">
                        <div class="min-w-0">
                            <span class="block text-xs font-bold text-slate-600 dark:text-slate-400 truncate capitalize leading-tight">${label}</span>
                            <span class="block text-[9px] text-slate-400 dark:text-slate-500 font-semibold">Bukan Produk POS</span>
                        </div>
                        <span class="h-2.5 w-2.5 rounded-full bg-slate-350 shrink-0"></span>
                    </div>
                `;
            }
        });

        listEl.innerHTML = html;
        lucide.createIcons();
    }

    // Call POST API back to Laravel to record AI statistics logs
    function logDetectionToDatabase(productId, confidence, inferenceTime, fps) {
        fetch('{{ route("ai.log") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                product_id: productId,
                confidence: confidence,
                inference_time_ms: inferenceTime,
                fps: fps
            })
        })
        .then(response => response.json())
        .then(data => {
            console.log("AI Log saved: ", data);
        })
        .catch(err => {
            console.error("AI logging error: ", err);
        });
    }

    /* =========================================================================
     * SHOPPING CART LOGIC HANDLERS
     * ========================================================================= */

    function addToCart(productId, name, price, stock) {
        const itemIdx = cart.findIndex(item => item.product_id === productId);

        if (itemIdx > -1) {
            // Check stock limit
            if (cart[itemIdx].quantity >= stock) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Stok Terbatas',
                    text: `Stok produk '${name}' di inventaris tidak mencukupi (Tersedia: ${stock} pcs).`,
                    confirmButtonColor: '#5C1D1B',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3500
                });
                return;
            }
            cart[itemIdx].quantity += 1;
        } else {
            if (stock <= 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Stok Habis',
                    text: `Produk '${name}' sudah habis.`,
                    confirmButtonColor: '#5C1D1B',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3500
                });
                return;
            }
            cart.push({
                product_id: productId,
                name: name,
                price: parseFloat(price),
                quantity: 1,
                stock: stock
            });
        }

        renderCart();
        
        // Trigger soft sound alert on manual additions as well
        playBeepSound();
    }

    function changeQty(productId, delta) {
        const itemIdx = cart.findIndex(item => item.product_id === productId);
        if (itemIdx > -1) {
            const item = cart[itemIdx];
            const newQty = item.quantity + delta;

            if (newQty <= 0) {
                cart.splice(itemIdx, 1);
            } else {
                if (newQty > item.stock) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Stok Habis',
                        text: `Stok inventaris maksimal: ${item.stock} pcs.`,
                        confirmButtonColor: '#5C1D1B'
                    });
                    return;
                }
                item.quantity = newQty;
            }
            renderCart();
        }
    }

    function removeItem(productId) {
        const itemIdx = cart.findIndex(item => item.product_id === productId);
        if (itemIdx > -1) {
            cart.splice(itemIdx, 1);
            renderCart();
        }
    }

    function clearCart() {
        cart = [];
        renderCart();
    }

    // Render active cart items to HTML
    function renderCart() {
        const container = document.getElementById('cart-container');
        if (cart.length === 0) {
            container.innerHTML = `
                <div class="text-center py-20 text-slate-400 dark:text-slate-550 space-y-2">
                    <i data-lucide="shopping-bag" class="w-10 h-10 mx-auto opacity-45"></i>
                    <p class="text-sm font-semibold">Keranjang Masih Kosong</p>
                    <p class="text-xs text-slate-450 dark:text-slate-500 max-w-[200px] mx-auto">Tambahkan kue melalui deteksi kamera atau pilih langsung dari katalog.</p>
                </div>
            `;
            lucide.createIcons();
            updateTotals();
            return;
        }

        let html = '';
        cart.forEach(item => {
            const itemTotal = item.price * item.quantity;
            html += `
                <div class="bg-white border border-slate-200/55 p-3.5 rounded-2xl flex items-center justify-between gap-3 shadow-xs hover:border-slate-300 transition-all text-slate-800">
                    <div class="min-w-0 flex-1">
                        <span class="block text-xs font-bold text-slate-800 truncate leading-tight">${item.name}</span>
                        <span class="block text-[10px] text-amber-600 font-extrabold mt-1 font-mono">Rp ${new Intl.NumberFormat('id-ID').format(item.price)}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button onclick="changeQty(${item.product_id}, -1)" class="w-7 h-7 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-550 flex items-center justify-center font-black transition-colors text-xs">
                            <i data-lucide="minus" class="w-3.5 h-3.5"></i>
                        </button>
                        <span class="w-6 text-center font-bold text-xs text-slate-700 font-mono">${item.quantity}</span>
                        <button onclick="changeQty(${item.product_id}, 1)" class="w-7 h-7 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-550 flex items-center justify-center font-black transition-colors text-xs">
                            <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                        </button>
                    </div>
                    <div class="text-right shrink-0 min-w-[70px]">
                        <span class="block text-xs font-bold text-slate-800 font-mono">Rp ${new Intl.NumberFormat('id-ID').format(itemTotal)}</span>
                        <button onclick="removeItem(${item.product_id})" class="text-[10px] text-rose-500 hover:text-rose-600 transition-colors font-bold mt-1">Hapus</button>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
        lucide.createIcons();
        updateTotals();
    }

    // Refresh totals details: Subtotal, Grand Total
    function updateTotals() {
        const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        
        let discount = parseFloat(document.getElementById('discount-input').value);
        if (isNaN(discount) || discount < 0) discount = 0;

        const grandTotal = Math.max(0, subtotal - discount);

        document.getElementById('subtotal-text').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(subtotal);
        if (document.getElementById('tax-text')) {
            document.getElementById('tax-text').innerText = 'Rp 0';
        }
        document.getElementById('grand-total-text').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(grandTotal);

        calculateChange();
    }

    // Cash Paid change updates
    function calculateChange() {
        const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        let discount = parseFloat(document.getElementById('discount-input').value);
        if (isNaN(discount)) discount = 0;
        
        const grandTotal = Math.max(0, subtotal - discount);

        const cashInput = document.getElementById('cash-paid');
        const changeEl = document.getElementById('change-text');

        let paid = parseFloat(cashInput.value);
        if (isNaN(paid)) paid = 0;

        if (paid < grandTotal || grandTotal === 0) {
            changeEl.innerText = "Rp 0";
            changeEl.className = "text-sm font-black font-mono text-rose-500";
        } else {
            const change = paid - grandTotal;
            changeEl.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(change);
            changeEl.className = "text-sm font-black font-mono text-emerald-600 dark:text-emerald-450";
        }
    }

    // Submit POS transaction to backend Laravel Endpoint
    function submitCheckout() {
        if (cart.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Keranjang Kosong',
                text: 'Silakan tambahkan kue ke dalam keranjang terlebih dahulu.',
                confirmButtonColor: '#5C1D1B'
            });
            return;
        }

        const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        let discount = parseFloat(document.getElementById('discount-input').value);
        if (isNaN(discount)) discount = 0;
        
        const grandTotal = Math.max(0, subtotal - discount);

        const cashInput = document.getElementById('cash-paid');
        let paid = parseFloat(cashInput.value);
        if (isNaN(paid) || paid <= 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Pembayaran Belum Diinput',
                text: 'Silakan masukkan jumlah uang tunai yang dibayarkan.',
                confirmButtonColor: '#5C1D1B'
            });
            return;
        }

        if (paid < grandTotal) {
            Swal.fire({
                icon: 'error',
                title: 'Uang Kurang',
                text: 'Uang pembayaran kurang dari total tagihan.',
                confirmButtonColor: '#5C1D1B'
            });
            return;
        }

        const payload = {
            items: cart.map(item => ({
                product_id: item.product_id,
                quantity: item.quantity
            })),
            discount: discount,
            tax: 0,
            paid_amount: paid
        };

        // Loading spinner while communicating with server
        Swal.fire({
            title: 'Memproses Transaksi...',
            html: 'Menyimpan penjualan dan memperbarui stok.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch('{{ route("checkout") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(res => {
            Swal.close();
            
            if (res.success) {
                // Populate invoice modal parameters
                document.getElementById('rec-invoice').innerText = res.data.transaction_code;
                document.getElementById('rec-date').innerText = res.data.created_at;
                document.getElementById('rec-subtotal').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(res.data.subtotal);
                document.getElementById('rec-discount').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(res.data.discount);
                if (document.getElementById('rec-tax')) {
                    document.getElementById('rec-tax').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(res.data.tax);
                }
                document.getElementById('rec-total').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(res.data.total_price);
                document.getElementById('rec-paid').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(res.data.paid_amount);
                document.getElementById('rec-change').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(res.data.change_amount);
                
                // Show modal overlay
                document.getElementById('receipt-modal').classList.remove('hidden');
                document.getElementById('receipt-modal').classList.add('flex');
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Checkout Gagal',
                    text: res.message,
                    confirmButtonColor: '#5C1D1B'
                });
            }
        })
        .catch(err => {
            Swal.close();
            Swal.fire({
                icon: 'error',
                title: 'Koneksi Terganggu',
                text: 'Gagal menghubungi server POS: ' + err.message,
                confirmButtonColor: '#5C1D1B'
            });
        });
    }

    function closeReceiptModal() {
        document.getElementById('receipt-modal').classList.add('hidden');
        document.getElementById('receipt-modal').classList.remove('flex');
        
        // Reset POS fields for a new client session
        clearCart();
        document.getElementById('discount-input').value = '';
        document.getElementById('cash-paid').value = '';
        
        // Soft reload seeded product data values async
        fetchProductsCatalog();
    }

    // Refresh catalog products async so we get updated stocks
    function fetchProductsCatalog() {
        fetch('{{ route("products") }}?json=1')
            .then(res => res.json())
            .then(data => {
                if (data && data.products) {
                    // Update cache array
                    Object.assign(productsCatalog, data.products);
                }
            })
            .catch(err => console.log(err));
    }

    /* =========================================================================
     * HELPER CATALOG AND WINDOW HANDLERS
     * ========================================================================= */

    function filterCatalog() {
        const query = document.getElementById('catalog-search').value.toLowerCase().trim();
        const items = document.querySelectorAll('#catalog-scroll-list button');

        items.forEach(btn => {
            const name = (btn.getAttribute('data-name') || '').toLowerCase();
            const coco = (btn.getAttribute('data-coco') || '').toLowerCase();
            const category = (btn.getAttribute('data-category') || '').toLowerCase();

            if (!query || name.includes(query) || coco.includes(query) || category.includes(query)) {
                btn.style.display = '';
            } else {
                btn.style.display = 'none';
            }
        });
    }

    function escapeJsString(str) {
        return str.replace(/\\/g, '\\\\').replace(/'/g, "\\'").replace(/"/g, '\\"');
    }

    // Shutdown camera stream automatically if user navigates away
    window.onbeforeunload = function() {
        stopCameraStream();
    };
</script>
@endsection
