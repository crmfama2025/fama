{{--
    Shared agreement PDF/preview assets.

    Before including this partial, define window.AgreementDocumentConfig:

    Signing page:
        mode: 'signing'
        letterhead: @json($data['letterHead'])

    PDF wrapper:
        mode: 'pdf'

    The signing page paginates #all-rows-source into #file-print-area.
    The PDF wrapper renders the already-paginated saved HTML without rebuilding it.
--}}

<style>
    @font-face {
        font-family: 'Agreement Arabic';
        src: url('{{ asset('assets/fonts/Amiri/Amiri-Regular.ttf') }}') format('truetype');
        font-weight: 400;
        font-style: normal;
        font-display: block;
    }

    @font-face {
        font-family: 'Agreement Arabic';
        src: url('{{ asset('assets/fonts/Amiri/Amiri-Bold.ttf') }}') format('truetype');
        font-weight: 700;
        font-style: normal;
        font-display: block;
    }

    @font-face {
        font-family: 'Agreement Arabic';
        src: url('{{ asset('assets/fonts/Amiri/Amiri-Italic.ttf') }}') format('truetype');
        font-weight: 400;
        font-style: italic;
        font-display: block;
    }

    @font-face {
        font-family: 'Agreement Arabic';
        src: url('{{ asset('assets/fonts/Amiri/Amiri-BoldItalic.ttf') }}') format('truetype');
        font-weight: 700;
        font-style: italic;
        font-display: block;
    }

    @font-face {
        font-family: 'Agreement English';
        src: url('{{ asset('assets/fonts/times.ttf') }}') format('truetype');
        font-weight: 400;
        font-style: normal;
        font-display: block;
    }

    {{--
        If timesbd.ttf, timesi.ttf and timesbi.ttf exist, add their matching
        @font-face declarations here. With only times.ttf, Chromium will
        synthesize English bold/italic consistently in both views.
    --}} :root {
        --agreement-page-width: 210mm;
        --agreement-page-height: 297mm;
        --agreement-first-top: 34mm;
        --agreement-next-top: 35mm;
        --agreement-side-padding: 16mm;
        --agreement-bottom-padding: 58mm;
        --agreement-font-size: 12px;
        --agreement-line-height: 15px;
        --agreement-signature-width: 60mm;
        --agreement-signature-height: 28mm;
    }

    @page {
        size: A4 portrait;
        margin: 0;
    }

    *,
    *::before,
    *::after {
        box-sizing: border-box;
    }

    html,
    body {
        margin: 0;
        padding: 0;
    }

    #file-print-area,
    #pdf-pages-container {
        width: var(--agreement-page-width);
        margin: 0 auto;
        padding: 0;
        font-family: 'Agreement English', serif;
        font-size: var(--agreement-font-size);
        font-weight: 400;
        line-height: var(--agreement-line-height);
        font-kerning: normal;
        font-variant-ligatures: none;
    }

    #file-print-area>.new-page,
    #pdf-pages-container>.new-page {
        position: relative;
        display: flow-root;
        width: var(--agreement-page-width);
        height: var(--agreement-page-height);
        min-height: var(--agreement-page-height);
        margin: 0 auto 40px;
        padding: 0;
        overflow: hidden;
        background-color: #fff;
        background-size: var(--agreement-page-width) var(--agreement-page-height) !important;
        background-repeat: no-repeat !important;
        background-position: top left !important;
        font-family: 'Agreement English', serif;
        font-size: var(--agreement-font-size);
        line-height: var(--agreement-line-height);
        page-break-after: always;
        break-after: page;
    }

    #file-print-area>.new-page:last-child,
    #pdf-pages-container>.new-page:last-child {
        page-break-after: auto;
        break-after: auto;
    }

    #file-print-area>.new-page.annexure-page,
    #file-print-area>.letter-sheet.new-page,
    #pdf-pages-container>.new-page.annexure-page,
    #pdf-pages-container>.letter-sheet.new-page {
        background-image: none !important;
        background-color: #fff !important;
    }

    #file-print-area .file-content,
    #pdf-pages-container .file-content {
        position: relative;
        width: 100%;
        height: var(--agreement-page-height);
        margin: 0;
        padding: var(--agreement-first-top) var(--agreement-side-padding) var(--agreement-bottom-padding);
    }

    #file-print-area .file-content.page-subsequent,
    #pdf-pages-container .file-content.page-subsequent {
        padding-top: var(--agreement-next-top);
    }

    #file-print-area table,
    #pdf-pages-container table {
        width: 100%;
        border-collapse: collapse;
        font-family: inherit;
        font-size: inherit;
        line-height: inherit;
    }

    #file-print-area td,
    #file-print-area th,
    #pdf-pages-container td,
    #pdf-pages-container th {
        vertical-align: top;
    }

    #file-print-area tr,
    #file-print-area td,
    #file-print-area th,
    #pdf-pages-container tr,
    #pdf-pages-container td,
    #pdf-pages-container th {
        page-break-inside: avoid;
        break-inside: avoid;
    }

    #file-print-area p,
    #pdf-pages-container p {
        margin: 4px !important;
    }

    #file-print-area .english,
    #pdf-pages-container .english {
        direction: ltr;
        text-align: left;
        padding-left: 3px;
        font-family: 'Agreement English', serif;
        line-height: var(--agreement-line-height);
    }

    #file-print-area .arabic,
    #pdf-pages-container .arabic {
        direction: rtl;
        text-align: right;
        padding-right: 3px;
        unicode-bidi: embed;
        font-family: 'Agreement Arabic', serif;
        line-height: var(--agreement-line-height);
    }

    /*
    * General Arabic fallback.
    */
    #file-print-area [lang="ar"],
    #file-print-area [dir="rtl"],
    #pdf-pages-container [lang="ar"],
    #pdf-pages-container [dir="rtl"] {
        font-family: 'Agreement Arabic', serif;
    }

    #file-print-area .ltr-number,
    #pdf-pages-container .ltr-number {
        direction: ltr;
        unicode-bidi: isolate;
        display: inline-block;
        text-align: left;
    }

    #file-print-area strong,
    #file-print-area b,
    #pdf-pages-container strong,
    #pdf-pages-container b {
        font-weight: 700;
    }

    #file-print-area em,
    #file-print-area i,
    #pdf-pages-container em,
    #pdf-pages-container i {
        font-style: italic;
    }

    #file-print-area .text-lg,
    #pdf-pages-container .text-lg {
        font-size: 12pt !important;
        font-weight: 700 !important;
    }

    #file-print-area .text-md,
    #pdf-pages-container .text-md {
        margin-top: 4px;
        font-size: 8.5pt !important;
        font-weight: 700 !important;
    }

    #file-print-area .text-sm,
    #pdf-pages-container .text-sm {
        font-size: 8pt !important;
        line-height: 16px;
    }

    #file-print-area .marginClass,
    #pdf-pages-container .marginClass {
        margin: 4px;
        line-height: 16px;
    }

    #file-print-area .mt-15,
    #pdf-pages-container .mt-15 {
        padding-top: 15px;
    }

    #file-print-area .customsign,
    #pdf-pages-container .customsign {
        margin-bottom: 120px !important;
    }

    #file-print-area .customsignInvestor,
    #pdf-pages-container .customsignInvestor {
        margin-bottom: 100px !important;
    }

    #file-print-area .text-medium,
    #pdf-pages-container .text-medium {
        margin-top: 4px;
        font-size: 9.5pt !important;
    }

    #file-print-area .underline-date,
    #pdf-pages-container .underline-date {
        text-decoration: underline;
        text-underline-offset: 3px;
    }

    #file-print-area .sig-placed-wrap,
    #pdf-pages-container .sig-placed-wrap {
        position: absolute !important;
        z-index: 20;
        width: var(--agreement-signature-width) !important;
        height: var(--agreement-signature-height) !important;
    }

    #file-print-area .sig-placed-wrap img,
    #pdf-pages-container .sig-placed-wrap img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    #file-print-area .signature-slot,
    #pdf-pages-container .signature-slot {
        position: relative;
        min-height: 16mm;
        padding-right: 42mm;
    }

    #file-print-area .signature-table,
    #pdf-pages-container .signature-table {
        width: 100% !important;
        table-layout: fixed !important;
        border-collapse: collapse !important;
    }

    #file-print-area .signature-table td,
    #pdf-pages-container .signature-table td {
        width: 50% !important;
        vertical-align: top !important;
    }

    /* Bootstrap helpers used inside saved agreement and annexure HTML. */
    #file-print-area .d-flex,
    #pdf-pages-container .d-flex {
        display: flex !important;
    }

    #file-print-area .justify-content-between,
    #pdf-pages-container .justify-content-between {
        justify-content: space-between !important;
    }

    #file-print-area .align-items-start,
    #pdf-pages-container .align-items-start {
        align-items: flex-start !important;
    }

    #file-print-area .flex-grow-1,
    #pdf-pages-container .flex-grow-1 {
        flex-grow: 1 !important;
    }

    #file-print-area .text-right,
    #pdf-pages-container .text-right {
        text-align: right !important;
    }

    #file-print-area .font-weight-bold,
    #pdf-pages-container .font-weight-bold {
        font-weight: 700 !important;
    }

    #file-print-area .mt-4,
    #pdf-pages-container .mt-4 {
        margin-top: 1.5rem !important;
    }

    #file-print-area .mb-3,
    #pdf-pages-container .mb-3 {
        margin-bottom: 1rem !important;
    }

    /* Shared annexure page layout. */
    #file-print-area>.letter-sheet.new-page,
    #pdf-pages-container>.letter-sheet.new-page {
        width: var(--agreement-page-width);
        max-width: none;
        height: var(--agreement-page-height);
        min-height: var(--agreement-page-height);
        margin: 0;
        padding: 28mm 18mm;
        overflow: hidden;
        border: 0;
        box-shadow: none;
        background: #fff !important;
        font-family: 'Agreement English', serif;
        color: #222;
    }

    #file-print-area .letter-title,
    #pdf-pages-container .letter-title {
        margin: 0 0 12mm;
        text-align: center;
        font-size: 16pt;
        line-height: 1.2;
        font-weight: 700;
        letter-spacing: 0.5px;
    }

    #file-print-area .letter-meta,
    #pdf-pages-container .letter-meta {
        margin-bottom: 7mm;
        font-size: 10pt;
        line-height: 1.4;
    }

    #file-print-area .letter-meta .label,
    #pdf-pages-container .letter-meta .label {
        font-weight: 600;
    }

    #file-print-area .letter-subject,
    #pdf-pages-container .letter-subject {
        margin: 8mm 0;
        font-size: 10pt;
        font-weight: 700;
    }

    #file-print-area .letter-sheet p,
    #pdf-pages-container .letter-sheet p {
        margin: 0 0 4mm !important;
        font-size: 10pt;
        line-height: 1.45;
    }

    #file-print-area .investment-table,
    #pdf-pages-container .investment-table {
        width: 100%;
        margin-top: 3mm;
        border-collapse: collapse;
        font-size: 9.5pt;
    }

    #file-print-area .investment-table th,
    #file-print-area .investment-table td,
    #pdf-pages-container .investment-table th,
    #pdf-pages-container .investment-table td {
        padding: 3.5mm 4mm;
        vertical-align: middle;
        border: 1px solid #aaa;
    }

    #file-print-area .investment-table th,
    #pdf-pages-container .investment-table th {
        background: #e8eaed !important;
        border-top: 0;
        font-weight: 600;
    }

    #file-print-area .investment-table tfoot td,
    #pdf-pages-container .investment-table tfoot td {
        background: #e8eaed !important;
        border-top: 2px solid #333;
        font-weight: 700;
    }

    #file-print-area .signature-block,
    #pdf-pages-container .signature-block {
        position: relative;
        min-height: 30mm;
        margin-top: 25mm;
        font-size: 10pt;
    }

    #file-print-area .signature-line,
    #pdf-pages-container .signature-line {
        display: inline-block;
        min-width: 260px;
        margin-top: 45px;
        border-bottom: 1px solid #333;
    }

    #file-print-area .annexure-signature-slot,
    #pdf-pages-container .annexure-signature-slot {
        position: relative;
        min-height: 16mm;
        padding-top: 3mm;
        padding-right: 42mm;
    }

    #file-print-area .sig-placed-wrap.annexure-signature img,
    #pdf-pages-container .sig-placed-wrap.annexure-signature img {
        object-position: left bottom;
    }

    @media print {

        html,
        body,
        #file-print-area,
        #pdf-pages-container {
            width: var(--agreement-page-width) !important;
            margin: 0 !important;
            padding: 0 !important;
            background: #fff !important;
        }

        #file-print-area>.new-page,
        #pdf-pages-container>.new-page {
            width: var(--agreement-page-width) !important;
            height: var(--agreement-page-height) !important;
            min-height: var(--agreement-page-height) !important;
            margin: 0 !important;
            box-shadow: none !important;
            overflow: hidden !important;
        }

        .no-print,
        .sig-placeholder-btn,
        .sig-placed-remove {
            display: none !important;
        }
    }
</style>

<script>
    window.AgreementDocument = (function() {
        'use strict';

        var MM_TO_PX = 96 / 25.4;
        var PAGE_H_MM = 297;
        var PAD_TOP_P1_MM = 34;
        var PAD_TOP_PN_MM = 35;
        var PAD_BOT_MM = 58;

        function config() {
            return window.AgreementDocumentConfig || {};
        }

        async function waitForFonts() {
            if (!document.fonts || !document.fonts.ready) {
                return;
            }

            await document.fonts.ready;

            await Promise.all([
                document.fonts.load('12px "Agreement English"'),
                document.fonts.load('700 12px "Agreement English"'),
                document.fonts.load('12px "Agreement Arabic"'),
                document.fonts.load('700 12px "Agreement Arabic"')
            ]);
        }

        function waitForImages() {
            return Promise.all(
                Array.from(document.images).map(function(image) {
                    if (image.complete) {
                        return Promise.resolve();
                    }

                    return new Promise(function(resolve) {
                        image.addEventListener('load', resolve, {
                            once: true
                        });
                        image.addEventListener('error', resolve, {
                            once: true
                        });
                    });
                })
            );
        }

        function nextFrame() {
            return new Promise(function(resolve) {
                window.requestAnimationFrame(resolve);
            });
        }

        function createPage(printArea, isFirst, letterhead) {
            var page = document.createElement('div');
            page.className = 'new-page';

            if (letterhead) {
                page.style.backgroundImage = 'url("' + letterhead + '")';
            }

            var content = document.createElement('div');
            content.className = 'file-content' + (isFirst ? '' : ' page-subsequent');

            var table = document.createElement('table');
            table.setAttribute('width', '100%');
            table.setAttribute('border', '0');
            table.setAttribute('cellpadding', '0');
            table.setAttribute('cellspacing', '0');

            var tbody = document.createElement('tbody');
            table.appendChild(tbody);
            content.appendChild(table);
            page.appendChild(content);
            printArea.appendChild(page);

            return {
                page: page,
                table: table,
                tbody: tbody,
                maxHeight: (
                    PAGE_H_MM -
                    (isFirst ? PAD_TOP_P1_MM : PAD_TOP_PN_MM) -
                    PAD_BOT_MM
                ) * MM_TO_PX
            };
        }

        function paginateSigningPage() {
            var source = document.getElementById('all-rows-source');
            var printArea = document.getElementById('file-print-area');

            if (!source || !printArea) {
                return;
            }

            var annexures = Array.from(
                document.querySelectorAll('.letter-sheet.new-page, .new-page.annexure-page')
            ).filter(function(page) {
                return !printArea.contains(page);
            });

            var rows = Array.from(source.querySelectorAll('tr[data-row]'));
            var letterhead = config().letterhead || '';

            printArea.innerHTML = '';

            var pageIndex = 0;
            var current = createPage(printArea, true, letterhead);
            pageIndex++;

            rows.forEach(function(row) {
                var forcePage = row.getAttribute('data-force-page') === 'true';

                if (forcePage && current.tbody.children.length > 0) {
                    current = createPage(printArea, false, letterhead);
                    pageIndex++;
                }

                current.tbody.appendChild(row);

                var measuredHeight = current.table.getBoundingClientRect().height;

                if (
                    measuredHeight > current.maxHeight &&
                    current.tbody.children.length > 1 &&
                    !forcePage
                ) {
                    current.tbody.removeChild(row);
                    current = createPage(printArea, false, letterhead);
                    pageIndex++;
                    current.tbody.appendChild(row);
                }
            });

            annexures.forEach(function(annexure) {
                printArea.appendChild(annexure);
            });

            document.dispatchEvent(new CustomEvent('agreement:paginated', {
                detail: {
                    pageCount: pageIndex
                }
            }));
        }

        async function initialize() {
            document.body.dataset.pdfReady = 'false';

            await waitForFonts();

            if (config().mode === 'signing') {
                paginateSigningPage();
            }

            await waitForImages();
            await nextFrame();
            await nextFrame();

            document.body.dataset.pdfReady = 'true';
            document.dispatchEvent(new CustomEvent('agreement:ready'));
        }

        return {
            initialize: initialize,
            paginateSigningPage: paginateSigningPage,
            waitForFonts: waitForFonts,
            waitForImages: waitForImages
        };
    }());

    document.addEventListener('DOMContentLoaded', function() {
        window.AgreementDocument.initialize().catch(function(error) {
            console.error('Agreement document initialization failed:', error);
            document.body.dataset.pdfReady = 'error';
        });
    });
</script>
