<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>REAL ESTATE | CRM</title>

    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="{{ asset('assets/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="{{ asset('assets/toastr/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/adminlte.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}?v=3">
    <link rel="stylesheet" href="{{ asset('assets/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/adminlte.min.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&display=swap" rel="stylesheet">

    <style>
        #file-print-area {

            /* ── SCREEN VIEW ── */
            .new-page {
                position: relative;
                width: 210mm;
                min-height: 297mm;
                margin: 0 auto 40px auto;
                background-color: #fff;
                background-image: url("{{ asset('images/investment_letter_head.png') }}");
                background-size: 210mm 297mm;
                background-repeat: no-repeat;
                background-position: top left;
                font-family: 'Times New Roman', Times, serif;
                font-size: 12px;
                box-shadow: 0 4px 24px #aaa;
                box-sizing: border-box;
                overflow: hidden;
            }

            /*
         * Page 1 — top: 29mm clears your letterhead header
         *           bottom: 48mm clears your letterhead footer
         */
            .file-content {
                position: relative;
                padding: 29mm 16mm 48mm 16mm;
                box-sizing: border-box;
            }

            /*
         * Pages 2+ — your letterhead has a slightly taller header zone on continuation pages
         *             top: 33mm (was .page-top in your original code)
         */
            .file-content.page-subsequent {
                padding-top: 33mm;
            }

            .arabic {
                direction: rtl;
                text-align: right;
                padding-right: 3px;
                unicode-bidi: embed;
                font-family: amiri;
            }

            .english {
                direction: ltr;
                padding-left: 3px;
                text-align: left;
                font-family: "Times New Roman";
            }

            .text-lg {
                font-size: 12pt !important;
                font-weight: 700 !important;
            }

            .text-md {
                font-size: 8.5pt !important;
                font-weight: 700 !important;
                margin-top: 4px;
            }

            .text-sm {
                font-size: 8pt !important;
            }

            .mt-15 {
                padding-top: 15px;
            }

            strong {
                font-weight: 700 !important;
            }

            p {
                margin: 4px;
            }
        }

        /* ── SIGNATURE STAMP ── */
        .signature-stamp {
            position: absolute;
            bottom: 14mm;
            /* tune: distance from page bottom */
            left: 16mm;
            /* tune: left side for investor signature */
            width: 40mm;
            height: 16mm;
            z-index: 10;
            pointer-events: none;
        }

        .signature-stamp img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            object-position: left bottom;
        }

        .signature-label {
            position: absolute;
            bottom: 10mm;
            left: 16mm;
            font-size: 6.5pt;
            color: #444;
            font-family: 'Times New Roman', serif;
            width: 40mm;
            text-align: left;
        }

        /* Modal overlay */
        .sig-modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.55);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }

        .sig-modal-overlay.active {
            display: flex;
        }

        .sig-modal {
            background: #fff;
            border-radius: 10px;
            padding: 28px 28px 20px;
            width: 480px;
            max-width: 95vw;
            box-shadow: 0 8px 40px rgba(0, 0, 0, 0.25);
        }

        .sig-modal h5 {
            margin: 0 0 16px;
            font-size: 15px;
            font-weight: 700;
            color: #333;
        }

        .sig-tabs {
            display: flex;
            gap: 8px;
            margin-bottom: 14px;
        }

        .sig-tab {
            flex: 1;
            padding: 8px;
            border: 1.5px solid #ccc;
            border-radius: 6px;
            background: #f8f8f8;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            text-align: center;
            transition: all .2s;
        }

        .sig-tab.active {
            border-color: #007bff;
            background: #e8f0fe;
            color: #007bff;
        }

        .sig-panel {
            display: none;
        }

        .sig-panel.active {
            display: block;
        }

        #sig-canvas {
            border: 1.5px solid #ccc;
            border-radius: 6px;
            background: #fafafa;
            cursor: crosshair;
            display: block;
            width: 100%;
            touch-action: none;
        }

        .sig-canvas-hint {
            font-size: 11px;
            color: #aaa;
            text-align: center;
            margin-top: 5px;
        }

        .sig-upload-area {
            border: 2px dashed #ccc;
            border-radius: 8px;
            padding: 28px 16px;
            text-align: center;
            cursor: pointer;
            transition: border-color .2s;
        }

        .sig-upload-area:hover {
            border-color: #007bff;
        }

        .sig-upload-area input[type="file"] {
            display: none;
        }

        .sig-upload-preview {
            display: none;
            margin-top: 12px;
        }

        .sig-upload-preview img {
            max-height: 80px;
            max-width: 100%;
            border: 1px solid #eee;
            border-radius: 4px;
            padding: 4px;
        }

        .sig-actions {
            display: flex;
            gap: 8px;
            margin-top: 16px;
            justify-content: flex-end;
        }

        .sig-btn {
            padding: 8px 18px;
            border-radius: 6px;
            border: none;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }

        .sig-btn-clear {
            background: #f1f1f1;
            color: #555;
        }

        .sig-btn-apply {
            background: #007bff;
            color: #fff;
        }

        .sig-btn-apply:disabled {
            background: #aaa;
            cursor: not-allowed;
        }

        .sig-btn-cancel {
            background: #fff;
            color: #888;
            border: 1px solid #ddd;
        }

        /* ── PRINT ── */
        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                box-sizing: border-box;
            }

            @page {
                size: A4 portrait;
                margin: 0;
            }

            html,
            body {
                width: 210mm;
                height: 297mm;
                margin: 0 !important;
                padding: 0 !important;
                background: #fff;
            }

            .no-print {
                display: none !important;
            }

            #file-print-area,
            .content-wrapper,
            .content,
            .container-fluid {
                width: 210mm !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .new-page {
                width: 210mm !important;
                height: 297mm !important;
                min-height: 297mm !important;
                margin: 0 !important;
                box-shadow: none !important;
                overflow: hidden !important;
                page-break-after: always;
                break-after: page;
                background-image: url("{{ asset('images/investment_letter_head.png') }}") !important;
                background-size: 210mm 297mm !important;
                background-repeat: no-repeat !important;
                background-position: top left !important;
            }

            .new-page:last-child {
                page-break-after: auto;
                break-after: auto;
            }

            .file-content {
                padding: 29mm 16mm 48mm 16mm !important;
            }

            .file-content.page-subsequent {
                padding-top: 33mm !important;
            }

            table {
                width: 100%;
                border-collapse: collapse;
            }

            td {
                vertical-align: top;
            }

            p {
                margin: 4px !important;
            }
        }
    </style>
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <section class="content">
            <div class="container-fluid">

                <section class="content-header no-print">
                    <div class="container-fluid">
                        <div class="row mb-2"></div>
                    </div>
                </section>

                {{-- Pages are injected here by JS --}}
                <div id="file-print-area"></div>

                {!! $data['html'] !!}

                <div class="mt-4 mb-5 text-center no-print">
                    <a href="{{ route('invoices.generated') }}" class="btn btn-secondary mr-2">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>

                    <button onclick="openSignatureModal()" class="btn btn-success mr-2">
                        <i class="fas fa-signature"></i> Add Signature
                    </button>

                    <button onclick="printInvoice()" class="btn btn-primary">
                        <i class="fas fa-print"></i> Print
                    </button>
                </div>

            </div>
        </section>
    </div>

    <!-- ── SIGNATURE MODAL ── -->
    <div class="sig-modal-overlay" id="sigModalOverlay">
        <div class="sig-modal">
            <h5>✍️ Investor Signature</h5>
            <p style="font-size:12px;color:#888;margin:-8px 0 14px;">
                Your signature will appear on every page of this agreement.
            </p>

            <!-- Tabs -->
            <div class="sig-tabs">
                <div class="sig-tab active" data-tab="draw">✏️ Draw Signature</div>
                <div class="sig-tab" data-tab="upload">📁 Upload Signature</div>
            </div>

            <!-- Draw Panel -->
            <div class="sig-panel active" id="sig-panel-draw">
                <canvas id="sig-canvas" width="420" height="160"></canvas>
                <p class="sig-canvas-hint">Draw your signature above using mouse or touch</p>
            </div>

            <!-- Upload Panel -->
            <div class="sig-panel" id="sig-panel-upload">
                <div class="sig-upload-area" id="sigUploadArea"
                    onclick="document.getElementById('sigFileInput').click()">
                    <div style="font-size:28px;">📂</div>
                    <p style="margin:6px 0 2px;font-size:13px;font-weight:600;">Click to upload signature image</p>
                    <p style="font-size:11px;color:#aaa;">PNG, JPG — transparent background recommended</p>
                    <input type="file" id="sigFileInput" accept="image/*">
                </div>
                <div class="sig-upload-preview" id="sigUploadPreview">
                    <img id="sigUploadImg" src="" alt="Signature preview">
                    <p style="font-size:11px;color:#888;margin-top:4px;">Preview — <a href="#" id="sigChangeFile"
                            style="color:#007bff;">change file</a></p>
                </div>
            </div>

            <!-- Actions -->
            <div class="sig-actions">
                <button class="sig-btn sig-btn-cancel" id="sigCancelBtn">Cancel</button>
                <button class="sig-btn sig-btn-clear" id="sigClearBtn">Clear</button>
                <button class="sig-btn sig-btn-apply" id="sigApplyBtn" disabled>Apply to All Pages</button>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/toastr/toastr.min.js') }}"></script>
    <script src="{{ asset('assets/select2/js/select2.full.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.22.5/dist/sweetalert2.all.min.js"></script>

    <script>
        // ── CONFIG — must match your CSS padding values exactly ──────────────────
        const CFG = {
            MM_TO_PX: 96 / 25.4, // browser renders screen at 96 dpi
            PAGE_H_MM: 297,
            PAD_TOP_P1_MM: 29, // .file-content padding-top  (page 1)
            PAD_TOP_PN_MM: 33, // .page-subsequent padding-top    (pages 2+)
            PAD_BOT_MM: 38, // footer clearance (same all pages)
            LETTERHEAD: "{{ $data['letterHead'] }}"
        };

        const CONTENT_H_P1 = (CFG.PAGE_H_MM - CFG.PAD_TOP_P1_MM - CFG.PAD_BOT_MM) * CFG.MM_TO_PX;
        const CONTENT_H_PN = (CFG.PAGE_H_MM - CFG.PAD_TOP_PN_MM - CFG.PAD_BOT_MM) * CFG.MM_TO_PX;

        // ── AUTO PAGINATOR ───────────────────────────────────────────────────────
        function buildPages() {
            const source = document.getElementById('all-rows-source');
            const printArea = document.getElementById('file-print-area');
            if (!source || !printArea) return;

            const rows = Array.from(source.querySelectorAll('tr[data-row]'));
            printArea.innerHTML = '';

            let pageIndex = 0;
            let usedH = 0;
            let currentTbl = null;
            let maxH = 0;

            function newPage() {
                const isFirst = (pageIndex === 0);
                pageIndex++;

                const page = document.createElement('div');
                page.className = 'new-page';
                page.style.backgroundImage = `url('${CFG.LETTERHEAD}')`;

                const content = document.createElement('div');
                content.className = 'file-content' + (isFirst ? '' : ' page-subsequent');

                const tbl = document.createElement('table');
                tbl.setAttribute('width', '100%');
                tbl.setAttribute('border', '0');
                tbl.setAttribute('cellpadding', '0');
                tbl.setAttribute('cellspacing', '0');

                content.appendChild(tbl);
                page.appendChild(content);
                printArea.appendChild(page);

                currentTbl = tbl;
                usedH = 0;
                maxH = isFirst ? CONTENT_H_P1 : CONTENT_H_PN;
            }

            newPage(); // start page 1

            rows.forEach((row, index) => {
                // ── FORCE NEW PAGE (annexures always start fresh) ──
                if (row.getAttribute('data-force-page') === 'true' && usedH > 0) {
                    newPage();
                }

                currentTbl.appendChild(row);
                const rowH = row.getBoundingClientRect().height;

                if (usedH + rowH > maxH && usedH > 0) {
                    currentTbl.removeChild(row);
                    newPage();
                    currentTbl.appendChild(row);
                    usedH += rowH;
                    return;
                }

                usedH += rowH;

                // ── ORPHAN HEADING GUARD ──
                const isHeading = row.style.backgroundColor || row.getAttribute('style')?.includes(
                    'background-color');
                const nextRow = rows[index + 1];
                if (isHeading && nextRow && !nextRow.getAttribute('data-force-page')) {
                    currentTbl.appendChild(nextRow);
                    const nextRowH = nextRow.getBoundingClientRect().height;
                    currentTbl.removeChild(nextRow);
                    if (usedH + nextRowH > maxH) {
                        currentTbl.removeChild(row);
                        usedH -= rowH;
                        newPage();
                        currentTbl.appendChild(row);
                        usedH += rowH;
                    }
                }
            });
        }

        document.addEventListener('DOMContentLoaded', buildPages);

        // ── PRINT ────────────────────────────────────────────────────────────────
        function printInvoice() {
            const invoiceHtml = document.getElementById('file-print-area').innerHTML;
            const styles = Array.from(document.querySelectorAll('style'))
                .map(s => s.innerHTML).join('\n');

            const win = window.open('', '_blank');
            win.document.write(`<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Investment Agreement</title>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&display=swap" rel="stylesheet">
    <style>
        ${styles}
        * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; color-adjust: exact !important; box-sizing: border-box; }
        @page { size: A4 portrait; margin: 0; }
        html, body { width: 210mm; margin: 0 !important; padding: 0 !important; }
        #file-print-area { width: 210mm !important; margin: 0 !important; padding: 0 !important; }
        .new-page {
            width: 210mm !important; height: 297mm !important; min-height: 297mm !important;
            margin: 0 !important; box-shadow: none !important; overflow: hidden !important;
            page-break-after: always; break-after: page;
            background-size: 210mm 297mm !important; background-repeat: no-repeat !important; background-position: top left !important;
        }
        .new-page:last-child { page-break-after: auto; break-after: auto; }
        .file-content { padding: 29mm 16mm 48mm 16mm !important; }
        .file-content.page-subsequent { padding-top: 33mm !important; }
        .no-print { display: none !important; }
        table { width: 100%; border-collapse: collapse; }
        td { vertical-align: top; }
        p { margin: 4px !important; }
        .signature-stamp {
            position: absolute !important;
            bottom: 14mm !important;
            left: 16mm !important;
            width: 40mm !important;
            height: 16mm !important;
            z-index: 10 !important;
            pointer-events: none !important;
        }
        .signature-stamp img {
            width: 100% !important;
            height: 100% !important;
            object-fit: contain !important;
            object-position: left bottom !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .signature-label {
            position: absolute !important;
            bottom: 10mm !important;
            left: 16mm !important;
            font-size: 6.5pt !important;
            color: #444 !important;
            font-family: 'Times New Roman', serif !important;
            width: 40mm !important;
            text-align: left !important;
        }
    </style>
</head>
<body>
    <div id="file-print-area">${invoiceHtml}</div>
    <script>
        window.onload = function () {
            setTimeout(function () { window.print(); }, 1000);
            window.onafterprint = function () { window.close(); };
        };
    <\/script>
</body>
</html>`);
            win.document.close();
        }
    </script>

    <script>
        // ════════════════════════════════════════════════
        // SIGNATURE PAD
        // ════════════════════════════════════════════════
        let signatureDataUrl = null; // final approved signature
        let activeTab = 'draw';
        let uploadedDataUrl = null;

        // ── Canvas drawing ───────────────────────────────
        const canvas = document.getElementById('sig-canvas');
        const ctx = canvas.getContext('2d');
        let isDrawing = false;
        let hasDrawn = false;

        function getPos(e) {
            const rect = canvas.getBoundingClientRect();
            const scaleX = canvas.width / rect.width;
            const scaleY = canvas.height / rect.height;
            const clientX = e.touches ? e.touches[0].clientX : e.clientX;
            const clientY = e.touches ? e.touches[0].clientY : e.clientY;
            return {
                x: (clientX - rect.left) * scaleX,
                y: (clientY - rect.top) * scaleY
            };
        }

        canvas.addEventListener('mousedown', e => {
            isDrawing = true;
            ctx.beginPath();
            const p = getPos(e);
            ctx.moveTo(p.x, p.y);
        });
        canvas.addEventListener('mousemove', e => {
            if (!isDrawing) return;
            const p = getPos(e);
            ctx.lineWidth = 2;
            ctx.lineCap = 'round';
            ctx.strokeStyle = '#1a1a2e';
            ctx.lineTo(p.x, p.y);
            ctx.stroke();
            hasDrawn = true;
            checkApplyBtn();
        });
        canvas.addEventListener('mouseup', () => {
            isDrawing = false;
        });
        canvas.addEventListener('mouseleave', () => {
            isDrawing = false;
        });
        canvas.addEventListener('touchstart', e => {
            e.preventDefault();
            isDrawing = true;
            ctx.beginPath();
            const p = getPos(e);
            ctx.moveTo(p.x, p.y);
        }, {
            passive: false
        });
        canvas.addEventListener('touchmove', e => {
            e.preventDefault();
            if (!isDrawing) return;
            const p = getPos(e);
            ctx.lineWidth = 2;
            ctx.lineCap = 'round';
            ctx.strokeStyle = '#1a1a2e';
            ctx.lineTo(p.x, p.y);
            ctx.stroke();
            hasDrawn = true;
            checkApplyBtn();
        }, {
            passive: false
        });
        canvas.addEventListener('touchend', () => {
            isDrawing = false;
        });

        // ── Tabs ─────────────────────────────────────────
        document.querySelectorAll('.sig-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                activeTab = this.dataset.tab;
                document.querySelectorAll('.sig-tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.sig-panel').forEach(p => p.classList.remove('active'));
                this.classList.add('active');
                document.getElementById('sig-panel-' + activeTab).classList.add('active');
                checkApplyBtn();
            });
        });

        // ── File upload ───────────────────────────────────
        document.getElementById('sigFileInput').addEventListener('change', function() {
            const file = this.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = e => {
                uploadedDataUrl = e.target.result;
                document.getElementById('sigUploadImg').src = uploadedDataUrl;
                document.getElementById('sigUploadArea').style.display = 'none';
                document.getElementById('sigUploadPreview').style.display = 'block';
                checkApplyBtn();
            };
            reader.readAsDataURL(file);
        });

        document.getElementById('sigChangeFile').addEventListener('click', e => {
            e.preventDefault();
            uploadedDataUrl = null;
            document.getElementById('sigFileInput').value = '';
            document.getElementById('sigUploadArea').style.display = 'block';
            document.getElementById('sigUploadPreview').style.display = 'none';
            checkApplyBtn();
        });

        // ── Apply button state ────────────────────────────
        function checkApplyBtn() {
            const ready = (activeTab === 'draw' && hasDrawn) || (activeTab === 'upload' && uploadedDataUrl);
            document.getElementById('sigApplyBtn').disabled = !ready;
        }

        // ── Clear ─────────────────────────────────────────
        document.getElementById('sigClearBtn').addEventListener('click', () => {
            if (activeTab === 'draw') {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                hasDrawn = false;
            } else {
                uploadedDataUrl = null;
                document.getElementById('sigFileInput').value = '';
                document.getElementById('sigUploadArea').style.display = 'block';
                document.getElementById('sigUploadPreview').style.display = 'none';
            }
            checkApplyBtn();
        });

        // ── Cancel ────────────────────────────────────────
        document.getElementById('sigCancelBtn').addEventListener('click', closeSignatureModal);

        // ── Open / Close modal ────────────────────────────
        function openSignatureModal() {
            document.getElementById('sigModalOverlay').classList.add('active');

            // Re-sync canvas pixel size to its CSS display size after modal becomes visible
            setTimeout(() => {
                const rect = canvas.getBoundingClientRect();
                if (rect.width > 0 && canvas.width !== Math.round(rect.width)) {
                    // preserve existing drawing
                    const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                    canvas.width = Math.round(rect.width);
                    canvas.height = 160;
                    ctx.putImageData(imageData, 0, 0);
                }
            }, 50);
        }

        function closeSignatureModal() {
            document.getElementById('sigModalOverlay').classList.remove('active');
        }

        // ── Apply: stamp signature on every page ──────────
        document.getElementById('sigApplyBtn').addEventListener('click', () => {
            // Get final data URL
            if (activeTab === 'draw') {
                signatureDataUrl = canvas.toDataURL('image/png');
            } else {
                signatureDataUrl = uploadedDataUrl;
            }

            stampSignatureOnAllPages(signatureDataUrl);
            closeSignatureModal();
        });

        function stampSignatureOnAllPages(dataUrl) {
            // Remove any existing stamps
            document.querySelectorAll('.signature-stamp, .signature-label').forEach(el => el.remove());

            const pages = document.querySelectorAll('.new-page');

            pages.forEach((page, i) => {
                page.style.position = 'relative';

                // ── Signature image stamp ──
                const stamp = document.createElement('div');
                stamp.className = 'signature-stamp';

                const img = document.createElement('img');
                img.src = dataUrl;
                img.style.cssText = 'width:100%;height:100%;object-fit:contain;object-position:left bottom;';
                stamp.appendChild(img);
                page.appendChild(stamp);

                // ── Label below signature ──
                const label = document.createElement('div');
                label.className = 'signature-label';
                label.innerHTML = 'Investor Signature<br>Page ' + (i + 1) + ' of ' + pages.length;
                page.appendChild(label);
            });

            toastr.success('Signature applied to all ' + pages.length + ' pages.');
        }
    </script>
</body>

</html>
