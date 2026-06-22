@extends('admin.layout.admin_master')

@section('custom_css')
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
                /* margin-top: 4px; */
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
                margin-bottom: none !important;
                /* margin: 4px; */
            }
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
@endsection

@section('content')
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

                {!! $template->template !!}

                <div class="mt-4 mb-5 text-center no-print">
                    <a href="{{ route('legal_template.index') }}" class="btn btn-secondary mr-2">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                    {{-- <button onclick="printInvoice()" class="btn btn-primary">
                        <i class="fas fa-print"></i> Print
                    </button> --}}
                </div>

            </div>
        </section>
    </div>
@endsection

@section('custom_js')
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
            LETTERHEAD: "{{ asset('images/investment_letter_head.png') }}"
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
@endsection
