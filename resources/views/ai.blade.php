@extends('layouts.app')

@section('title', 'AI Detection Monitor')
@section('page_title', 'AI Object Detection Diagnostics')

@section('content')
<div class="space-y-6">
    
    <!-- AI Overview Diagnostics Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="glass-card rounded-[22px] p-5 shadow-xs">
            <span class="text-[10px] font-bold text-slate-450 dark:text-slate-555 uppercase tracking-wider block">Model Object Detection</span>
            <span class="block text-sm font-extrabold text-slate-850 dark:text-white mt-2 leading-none">YOLOv8 Nano (Custom)</span>
            <span class="block text-[9px] text-amber-600 dark:text-amber-500 font-bold uppercase tracking-widest mt-2 leading-none">ONNX Runtime Web</span>
        </div>

        <div class="glass-card rounded-[22px] p-5 shadow-xs">
            <span class="text-[10px] font-bold text-slate-450 dark:text-slate-555 uppercase tracking-wider block">Jumlah Deteksi Hari Ini</span>
            <span class="block text-lg font-black text-slate-850 dark:text-white mt-2 leading-none">{{ $todayDetectionsCount }} Kali</span>
            <span class="block text-[10px] text-slate-400 dark:text-slate-500 font-semibold mt-2.5">Auto-logged on cart inserts</span>
        </div>

        <div class="glass-card rounded-[22px] p-5 shadow-xs">
            <span class="text-[10px] font-bold text-slate-450 dark:text-slate-555 uppercase tracking-wider block">Rata-Rata Akurasi</span>
            <span class="block text-lg font-black text-emerald-600 dark:text-emerald-450 mt-2 leading-none">{{ number_format($avgAccuracy, 1) }}%</span>
            <span class="block text-[10px] text-slate-400 dark:text-slate-500 font-semibold mt-2.5">Confidence average</span>
        </div>

        <div class="glass-card rounded-[22px] p-5 shadow-xs">
            <span class="text-[10px] font-bold text-slate-450 dark:text-slate-555 uppercase tracking-wider block">Rata-Rata Latensi</span>
            <span class="block text-lg font-black text-slate-850 dark:text-white mt-2 leading-none">{{ number_format($avgInferenceTime, 0) }}ms</span>
            <span class="block text-[10px] text-slate-400 dark:text-slate-500 font-semibold mt-2.5">Inference execution delay</span>
        </div>
    </div>

    <!-- Live Monitor Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Live Diagnostic Webcam (7 Columns) -->
        <div class="lg:col-span-7 flex flex-col glass-card rounded-[24px] p-5 shadow-xs h-[450px]">
            <div class="flex items-center justify-between mb-3 shrink-0">
                <div class="flex items-center gap-2">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                    </span>
                    <h3 class="font-bold text-xs uppercase tracking-wider text-slate-500">Diagnostic Stream</h3>
                </div>
                <button id="cam-toggle" onclick="toggleDiagnosticCam()" class="px-4 py-1.5 text-xs font-bold bg-white border border-slate-200 text-slate-600 hover:bg-amber-400 hover:text-white hover:border-amber-400 rounded-xl transition-all shadow-xs hover:shadow-md hover:shadow-amber-500/15 flex items-center gap-2">
                    <i data-lucide="video" class="w-3.5 h-3.5"></i> <span id="cam-text">Aktifkan Kamera Uji</span>
                </button>
            </div>

            <!-- Viewport -->
            <div class="flex-1 bg-slate-950 rounded-2xl relative flex items-center justify-center overflow-hidden border border-slate-200/50 dark:border-slate-800 shadow-inner">
                <video id="diag-video" autoplay playsinline muted class="absolute inset-0 w-full h-full object-cover opacity-0 transition-opacity duration-300"></video>
                <canvas id="diag-canvas" class="absolute inset-0 w-full h-full object-cover pointer-events-none z-10"></canvas>
                
                <!-- Placeholder -->
                <div id="diag-placeholder" class="text-center p-6 flex flex-col items-center justify-center space-y-3 z-0">
                    <div class="w-14 h-14 rounded-full bg-slate-900 border border-slate-850 flex items-center justify-center text-slate-500">
                        <i data-lucide="scan" class="w-7 h-7"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-700 dark:text-slate-350">Uji Coba Deteksi AI</h4>
                        <p class="text-xs text-slate-450 dark:text-slate-500 max-w-sm mt-1">Nyalakan kamera uji untuk menganalisis kecepatan FPS model YOLOv8 secara visual.</p>
                    </div>
                </div>

                <!-- Loader overlay -->
                <div id="diag-loader" class="absolute inset-0 bg-slate-950/95 z-20 hidden flex-col items-center justify-center space-y-3">
                    <div class="w-10 h-10 border-4 border-amber-500 border-t-transparent rounded-full animate-spin"></div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Menginisialisasi Engine ONNX...</span>
                </div>
            </div>
        </div>

        <!-- Scrolling Realtime Console Detections logs (5 Columns) -->
        <div class="lg:col-span-5 flex flex-col glass-card rounded-[24px] p-5 shadow-xs h-[450px] overflow-hidden">
            <h3 class="font-bold text-xs uppercase tracking-wider text-slate-500 dark:text-slate-450 mb-3 shrink-0">Realtime Detection Stream</h3>
            <div id="live-console" class="flex-1 bg-white rounded-2xl p-4 font-mono text-[10px] text-slate-800 overflow-y-auto space-y-2 border border-slate-200 shadow-sm">
                <div class="text-slate-500 font-semibold">[SYSTEM] Engine status: READY. Waiting for stream activation...</div>
            </div>
        </div>

    </div>

    <!-- Historical logs saved in DB -->
    <div class="glass-card rounded-[24px] p-6 shadow-xs">
        <div class="mb-5">
            <h3 class="text-sm font-bold text-slate-800 dark:text-white">Audit Log Deteksi AI Terbaru</h3>
            <p class="text-[10px] text-slate-450 dark:text-slate-500 font-medium">Log riwayat identifikasi produk yang masuk ke keranjang belanja kasir</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800 text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider">
                        <th class="py-3 px-4">Waktu Deteksi</th>
                        <th class="py-3 px-4">Kue Teridentifikasi</th>
                        <th class="py-3 px-4 text-center">Kelas</th>
                        <th class="py-3 px-4 text-center">Confidence Score</th>
                        <th class="py-3 px-4 text-center">Inference Latency</th>
                        <th class="py-3 px-4 text-center">Frame Rate</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100/60 dark:divide-slate-800/40 text-xs text-slate-700 dark:text-slate-350">
                    @forelse($recentLogs as $log)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/30 transition-colors">
                            <td class="py-3.5 px-4 font-medium">{{ $log->created_at->format('d M Y H:i:s') }}</td>
                            <td class="py-3.5 px-4 font-bold text-slate-850 dark:text-white">{{ $log->product->name ?? 'Produk Dihapus' }}</td>
                            <td class="py-3.5 px-4 text-center font-mono text-[11px] font-semibold text-slate-500 dark:text-slate-400">{{ $log->product->coco_class ?? '-' }}</td>
                            <td class="py-3.5 px-4 text-center font-bold text-emerald-600 dark:text-emerald-450 font-mono">{{ number_format($log->confidence * 100, 1) }}%</td>
                            <td class="py-3.5 px-4 text-center font-mono text-slate-500 dark:text-slate-450">{{ $log->inference_time_ms ? $log->inference_time_ms . ' ms' : '-' }}</td>
                            <td class="py-3.5 px-4 text-center font-mono text-slate-500 dark:text-slate-450">{{ $log->fps ? number_format($log->fps, 0) . ' fps' : '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-450 dark:text-slate-500 font-medium">
                                <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
                                Belum ada log deteksi AI yang terekam.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/onnxruntime-web/dist/ort.min.js"></script>
<script>
    ort.env.wasm.wasmPaths = 'https://cdn.jsdelivr.net/npm/onnxruntime-web/dist/';

    let diagnosticSession = null;
    let localStream = null;
    let cameraActive = false;
    let requestId = null;
    let lastTime = Date.now();

    function logConsole(message, type = 'info') {
        const consoleEl = document.getElementById('live-console');
        if (!consoleEl) return;
        
        const timestamp = new Date().toLocaleTimeString('id-ID');
        let colorClass = 'text-slate-800';
        if (type === 'system') colorClass = 'text-slate-500 font-bold';
        if (type === 'detect') colorClass = 'text-amber-400 font-extrabold';
        if (type === 'error') colorClass = 'text-rose-600 font-bold';

        const line = document.createElement('div');
        line.className = `${colorClass}`;
        line.innerHTML = `[${timestamp}] ${message}`;
        consoleEl.appendChild(line);
        consoleEl.scrollTop = consoleEl.scrollHeight;
        
        // Cap lines at 100 to prevent overflow memory lag
        if (consoleEl.children.length > 100) {
            consoleEl.removeChild(consoleEl.firstChild);
        }
    }

    async function toggleDiagnosticCam() {
        const btn = document.getElementById('cam-toggle');
        const text = document.getElementById('cam-text');
        const video = document.getElementById('diag-video');
        const canvas = document.getElementById('diag-canvas');
        const placeholder = document.getElementById('diag-placeholder');
        const loader = document.getElementById('diag-loader');

        if (!cameraActive) {
            loader.classList.remove('hidden');
            logConsole("[SYSTEM] Loading YOLOv8 ONNX Session...", 'system');

            try {
                if (!diagnosticSession) {
                    diagnosticSession = await ort.InferenceSession.create('/models/best.onnx', {
                        executionProviders: ['wasm']
                    });
                }
                
                logConsole("[SYSTEM] Model loaded successfully.", 'system');
                loader.classList.add('hidden');
                placeholder.classList.add('hidden');
                video.classList.remove('opacity-0');
                video.classList.add('opacity-100');

                const stream = await navigator.mediaDevices.getUserMedia({
                    video: { width: { ideal: 640 }, height: { ideal: 480 } },
                    audio: false
                });

                localStream = stream;
                video.srcObject = stream;
                video.onloadedmetadata = () => {
                    video.play();
                    cameraActive = true;
                    btn.className = "px-4 py-1.5 text-xs font-bold bg-rose-500 hover:bg-rose-400 text-white rounded-xl transition-all shadow-md shadow-rose-500/10 flex items-center gap-2";
                    text.innerText = "Matikan Kamera Uji";
                    logConsole("[SYSTEM] Camera stream started. Running inference loop...", 'system');
                    requestId = requestAnimationFrame(diagnosticLoop);
                };
            } catch (err) {
                console.error(err);
                loader.classList.add('hidden');
                logConsole(`[ERROR] Webcam startup failed: ${err.message}`, 'error');
            }
        } else {
            stopDiagnosticStream();
            btn.className = "px-4 py-1.5 text-xs font-bold bg-white border border-slate-200 text-slate-600 hover:bg-amber-400 hover:text-white hover:border-amber-400 rounded-xl transition-all shadow-xs hover:shadow-md hover:shadow-amber-500/15 flex items-center gap-2";
            text.innerText = "Aktifkan Kamera Uji";
            placeholder.classList.remove('hidden');
            video.classList.add('opacity-0');
        }
    }

    function stopDiagnosticStream() {
        if (localStream) {
            localStream.getTracks().forEach(track => track.stop());
            localStream = null;
        }
        if (requestId) {
            cancelAnimationFrame(requestId);
            requestId = null;
        }
        cameraActive = false;
        
        const canvas = document.getElementById('diag-canvas');
        const ctx = canvas.getContext('2d');
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        
        logConsole("[SYSTEM] Diagnostic stream stopped.", 'system');
    }

    async function diagnosticLoop() {
        if (!cameraActive || !localStream) return;

        const video = document.getElementById('diag-video');
        const canvas = document.getElementById('diag-canvas');

        if (video.videoWidth > 0) {
            if (canvas.width !== video.videoWidth || canvas.height !== video.videoHeight) {
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
            }
            await runDiagnosticInference(video, canvas);
        }

        if (cameraActive) {
            requestId = requestAnimationFrame(diagnosticLoop);
        }
    }

    // Helper functions copy-pasted for diagnostic isolation
    function preprocessFrame(video, targetW, targetH) {
        const offCanvas = document.createElement('canvas');
        offCanvas.width = targetW;
        offCanvas.height = targetH;
        const ctx = offCanvas.getContext('2d');
        ctx.drawImage(video, 0, 0, targetW, targetH);
        
        const imgData = ctx.getImageData(0, 0, targetW, targetH);
        const data = imgData.data;
        const tensor = new Float32Array(targetW * targetH * 3);
        const imageSize = targetW * targetH;

        for (let i = 0; i < imageSize; i++) {
            tensor[i] = data[i * 4] / 255.0;
            tensor[imageSize + i] = data[i * 4 + 1] / 255.0;
            tensor[imageSize * 2 + i] = data[i * 4 + 2] / 255.0;
        }
        return tensor;
    }

    async function runDiagnosticInference(video, canvas) {
        const startTime = Date.now();
        const preproc = preprocessFrame(video, 640, 640);
        const inputTensor = new ort.Tensor('float32', preproc, [1, 3, 640, 640]);

        try {
            const outputs = await diagnosticSession.run({ images: inputTensor });
            const outputNames = diagnosticSession.outputNames;
            const outputData = outputs[outputNames[0]].data;

            const inferenceTime = Date.now() - startTime;
            const fps = Math.round(1000 / (Date.now() - lastTime));
            lastTime = Date.now();

            // Classes list
            const classNames = [
                'arem arem', 'bacang', 'donut cokelat', 'ekler', 'horn fla', 'iceberg cheese cake',
                'ketan srikaya', 'lapis pepe cokelat', 'lapis pepe pandan', 'lemper', 'macaroni schotel',
                'nagasari', 'pai buah', 'pastel', 'pisang molen', 'risol mayonese', 'risol segitiga',
                'soes', 'sosis pastry', 'sosis solo', 'tahu baso'
            ];

            const detections = [];
            const numBoxes = 8400;

            for (let c = 0; c < numBoxes; c++) {
                let maxScore = 0;
                let classId = -1;
                for (let cl = 0; cl < 21; cl++) {
                    const score = outputData[(4 + cl) * numBoxes + c];
                    if (score > maxScore) {
                        maxScore = score;
                        classId = cl;
                    }
                }

                if (maxScore > 0.45) {
                    detections.push({
                        x: outputData[0 * numBoxes + c] - outputData[2 * numBoxes + c] / 2,
                        y: outputData[1 * numBoxes + c] - outputData[3 * numBoxes + c] / 2,
                        w: outputData[2 * numBoxes + c],
                        h: outputData[3 * numBoxes + c],
                        classId: classId,
                        score: maxScore
                    });
                }
            }

            // NMS
            const kept = [];
            detections.sort((a,b)=>b.score-a.score);
            const active = new Array(detections.length).fill(true);
            for (let i = 0; i < detections.length; i++) {
                if (!active[i]) continue;
                const boxA = detections[i];
                kept.push(boxA);
                for (let j = i + 1; j < detections.length; j++) {
                    if (!active[j]) continue;
                    const boxB = detections[j];
                    if (calculateIoU(boxA, boxB) > 0.45) active[j] = false;
                }
            }

            // Draw bounding boxes
            const ctx = canvas.getContext('2d');
            ctx.clearRect(0,0,canvas.width,canvas.height);
            kept.forEach(det => {
                const label = classNames[det.classId] || 'kue';
                const sx = canvas.width / 640;
                const sy = canvas.height / 640;
                
                ctx.strokeStyle = '#3b82f6'; // Blue for diagnostic
                ctx.lineWidth = 3;
                ctx.strokeRect(det.x*sx, det.y*sy, det.w*sx, det.h*sy);

                ctx.font = 'bold 10px monospace';
                ctx.fillStyle = '#3b82f6';
                const txt = `${label.toUpperCase()} (${Math.round(det.score*100)}%)`;
                ctx.fillRect(det.x*sx, det.y*sy - 15, ctx.measureText(txt).width + 6, 15);
                ctx.fillStyle = '#FFFFFF';
                ctx.fillText(txt, det.x*sx + 3, det.y*sy - 4);

                // Log to scrolling console
                logConsole(`DETECT: <b>${label}</b> (Score: ${Math.round(det.score*100)}%, Latency: ${inferenceTime}ms, FPS: ${fps})`, 'detect');
            });

        } catch (err) {
            console.error(err);
        }
    }

    function calculateIoU(boxA, boxB) {
        const xA = Math.max(boxA.x, boxB.x);
        const yA = Math.max(boxA.y, boxB.y);
        const xB = Math.min(boxA.x + boxA.w, boxB.x + boxB.w);
        const yB = Math.min(boxA.y + boxA.h, boxB.y + boxB.h);
        const interArea = Math.max(0, xB - xA) * Math.max(0, yB - yA);
        return interArea / (boxA.w * boxA.h + boxB.w * boxB.h - interArea);
    }

    window.onbeforeunload = function() {
        stopDiagnosticStream();
    };
</script>
@endsection
