<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        @font-face {
            font-family: 'Amiri';
            src: url('{{ asset('assets/fonts/Amiri/Amiri-Regular.ttf') }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'Amiri';
            src: url('{{ asset('assets/fonts/Amiri/Amiri-Bold.ttf') }}') format('truetype');
            font-weight: bold;
            font-style: normal;
        }

        @font-face {
            font-family: 'Amiri';
            src: url('{{ asset('assets/fonts/Amiri/Amiri-Italic.ttf') }}') format('truetype');
            font-weight: normal;
            font-style: italic;
        }

        @font-face {
            font-family: 'Amiri';
            src: url('{{ asset('assets/fonts/Amiri/Amiri-BoldItalic.ttf') }}') format('truetype');
            font-weight: bold;
            font-style: italic;
        }

        @font-face {
            font-family: 'Times New Roman';
            src: url('{{ asset('assets/fonts/times.ttf') }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }


        * {
            box-sizing: border-box;
        }

        @page {
            size: A4 portrait;
            margin: 0;
        }

        html,
        body {
            width: 210mm;
            margin: 0 !important;
            padding: 0 !important;
            background: #fff;
        }

        #pdf-pages-container {
            width: 210mm;
            margin: 0;
            padding: 0;
        }


        /* .new-page {
            position: relative;
            width: 210mm;
            height: 297mm;
            min-height: 297mm;
            background-color: #fff;
            background-size: 210mm 297mm;
            background-repeat: no-repeat;
            background-position: top left;
            font-family: 'Times New Roman', Times, serif;
            font-size: 12px;
            overflow: hidden;
            page-break-after: always;
        }

        .new-page:last-of-type {
            page-break-after: auto;
        } */

        .new-page {
            position: relative;

            width: 210mm;
            height: 297mm;
            min-height: 297mm;

            margin: 0;
            padding: 0;

            background-color: #fff;
            background-size: 210mm 297mm !important;
            background-repeat: no-repeat !important;
            background-position: top left !important;

            font-family: 'Times New Roman', Times, serif;
            font-size: 12px;

            overflow: hidden;

            page-break-after: always;
            break-after: page;
        }

        #pdf-pages-container>.new-page:last-child {
            page-break-after: auto;
            break-after: auto;
        }

        .new-page.annexure-page,
        .new-page.letter-sheet {
            background-image: none !important;
            background-color: #fff !important;
        }

        .file-content {
            position: relative;
            width: 100%;
            height: 100%;

            /*
            * Content ends at 239mm:
            * 297mm - 58mm bottom clearance.
            *
            * This protects the signatures and letterhead footer.
            */
            padding: 34mm 16mm 58mm 16mm;

            box-sizing: border-box;
        }

        .file-content.page-subsequent {
            padding-top: 35mm;
        }

        #pdf-pages-container {
            width: 210mm;
            margin: 0;
            padding: 0;
        }

        .arabic {
            direction: rtl;
            text-align: right;
            padding-right: 3px;
            unicode-bidi: embed;
            font-family: 'Amiri', serif;
            line-height: 15px;
            /* Arabic script needs slightly more than Latin */
        }

        .english {
            direction: ltr;
            padding-left: 3px;
            text-align: left;
            font-family: 'Times New Roman', Times, serif;
            line-height: 15px;
        }

        .ltr-number {
            direction: ltr;
            unicode-bidi: isolate;
            display: inline-block;
            text-align: left;
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
            line-height: 16px;
        }

        .mt-15 {
            padding-top: 15px;
        }

        strong {
            font-weight: 700 !important;
        }

        .marginClass {
            margin: 4px;
            line-height: 16px;
        }

        .underline-date {
            text-decoration: underline;
            text-underline-offset: 3px;
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

        .sig-placed-wrap {
            position: absolute;
            width: 40mm;
            height: 16mm;
        }

        .sig-placed-wrap img {
            width: 100%;
            height: 100%;
        }

        .customsign {
            margin-bottom: 120px !important;
        }

        .customsignInvestor {
            margin-bottom: 100px !important;
        }



        /* investment annexure style */
        .letter-sheet {
            background: #fff;
            max-width: 800px;
            /* margin: 40px auto; */
            padding: 127px 70px;
            /* box-shadow: 0 0 15px rgba(0, 0, 0, 0.08); */
            /* border: 1px solid #e0e0e0; */
        }

        .letter-title {
            text-align: center;
            font-weight: 700;
            font-size: 1.4rem;
            letter-spacing: 0.5px;
            margin-bottom: 40px;
        }

        .letter-meta {
            margin-bottom: 30px;
        }

        .letter-meta .label {
            font-weight: 600;
        }

        .letter-subject {
            font-weight: 700;
            margin: 25px 0;
        }

        .investment-table th {
            background: #e8eaed;
            font-weight: 600;
            border-top: none;
        }

        .investment-table td,
        .investment-table th {
            vertical-align: middle;
            padding: 12px 16px;
        }

        .investment-table tfoot td {
            font-weight: 700;
            background: #e8eaed;
            border-top: 2px solid #333;
        }

        .signature-block {
            margin-top: 100px;
        }

        .signature-line {
            display: inline-block;
            border-bottom: 1px solid #333;
            min-width: 260px;
            margin-top: 45px;
        }

        .sig-placed-wrap.annexure-signature {
            top: 689.275px !important;
        }
    </style>
</head>

<body>
    {!! $bodyHtml !!}
    <script>
        document.addEventListener('DOMContentLoaded', async function() {
            /*
             * Wait for Arabic and custom fonts before measuring table rows.
             * Row heights can change after fonts finish loading.
             */
            if (document.fonts && document.fonts.ready) {
                await document.fonts.ready;
            }

            var MM_TO_PX = 96 / 25.4;
            var PAGE_H_MM = 297;

            /*
             * These values must exactly match the PDF wrapper CSS.
             */
            var PAD_TOP_P1_MM = 34;
            var PAD_TOP_PN_MM = 35;
            var PAD_BOT_MM = 58;

            var maxH_p1 =
                (PAGE_H_MM - PAD_TOP_P1_MM - PAD_BOT_MM) *
                MM_TO_PX;

            var maxH_pn =
                (PAGE_H_MM - PAD_TOP_PN_MM - PAD_BOT_MM) *
                MM_TO_PX;

            /*
             * Remove a previous generated container if this script is
             * accidentally executed more than once.
             */
            var existingContainer =
                document.getElementById('pdf-pages-container');

            if (existingContainer) {
                existingContainer.remove();
            }

            var allOldPages = Array.from(
                document.querySelectorAll('.new-page')
            );

            /*
             * Preserve annexure pages. They have their own structure and
             * must not be flattened into agreement table rows.
             *
             * This supports either:
             *   .new-page.annexure-page
             * or:
             *   .new-page.letter-sheet
             */
            var annexurePages = allOldPages.filter(function(page) {
                return (
                    page.classList.contains('annexure-page') ||
                    page.classList.contains('letter-sheet')
                );
            });

            /*
             * Only normal agreement pages should be repaginated.
             */
            var oldPages = allOldPages.filter(function(page) {
                return (
                    !page.classList.contains('annexure-page') &&
                    !page.classList.contains('letter-sheet')
                );
            });

            if (oldPages.length === 0) {
                await waitForPdfImages();
                document.body.dataset.pdfReady = 'true';
                return;
            }

            /*
             * Capture the letterhead before removing the original pages.
             */
            var bgUrl = null;

            var inlineStyle =
                oldPages[0].getAttribute('style') || '';

            var backgroundMatch = inlineStyle.match(
                /background-image:\s*url\((["']?)(.*?)\1\)/
            );

            if (backgroundMatch) {
                bgUrl = backgroundMatch[2];
            }

            /*
             * Fall back to computed CSS if the letterhead is not inline.
             */
            if (!bgUrl) {
                var computedBackground = window
                    .getComputedStyle(oldPages[0])
                    .backgroundImage;

                var computedMatch = computedBackground.match(
                    /^url\((["']?)(.*?)\1\)$/
                );

                if (computedMatch) {
                    bgUrl = computedMatch[2];
                }
            }

            /*
             * Save the original signature image sources before rebuilding
             * and removing the old pages.
             */
            var investorImg = document.querySelector(
                '.sig-placed-wrap[data-signer="investor"] img'
            );

            var companyImg = document.querySelector(
                '.sig-placed-wrap[data-signer="company"] img'
            );

            var investorSrc = investorImg ?
                investorImg.src :
                null;

            var companySrc = companyImg ?
                companyImg.src :
                null;

            /*
             * Flatten all agreement rows while preserving their order.
             *
             * table.rows is safer than requiring an explicit tbody because
             * browsers may insert tbody automatically.
             */
            var rows = [];

            oldPages.forEach(function(page) {
                var table = page.querySelector(
                    '.file-content > table'
                );

                if (!table) {
                    return;
                }

                Array.from(table.rows).forEach(function(row) {
                    rows.push(row);
                });
            });

            var parent = oldPages[0].parentNode;

            /*
             * This container must be attached before row measurement.
             * Detached elements return a height of zero.
             */
            var container = document.createElement('div');
            container.id = 'pdf-pages-container';

            parent.insertBefore(container, oldPages[0]);

            var pages = [];
            var currentTbody = null;
            var maxH = 0;

            function newPage(isFirst) {
                var page = document.createElement('div');
                page.className = 'new-page';
                page.style.position = 'relative';

                if (bgUrl) {
                    page.style.backgroundImage =
                        "url('" + bgUrl + "')";
                }

                var content = document.createElement('div');

                content.className =
                    'file-content' +
                    (isFirst ? '' : ' page-subsequent');

                var table = document.createElement('table');

                table.setAttribute('width', '100%');
                table.setAttribute('border', '0');
                table.setAttribute('cellpadding', '0');
                table.setAttribute('cellspacing', '0');

                var tbody = document.createElement('tbody');

                table.appendChild(tbody);
                content.appendChild(table);
                page.appendChild(content);
                container.appendChild(page);

                pages.push(page);
                currentTbody = tbody;

                maxH = isFirst ?
                    maxH_p1 :
                    maxH_pn;

                return page;
            }

            /*
             * Start agreement page 1.
             */
            newPage(true);

            rows.forEach(function(row) {
                var forcePage =
                    row.getAttribute('data-force-page') ===
                    'true';

                var ownSignaturePad =
                    row.hasAttribute(
                        'data-own-signature-pad'
                    );

                /*
                 * Force special rows onto a fresh page.
                 */
                if (
                    (forcePage || ownSignaturePad) &&
                    currentTbody.children.length > 0
                ) {
                    newPage(false);
                }

                currentTbody.appendChild(row);

                /*
                 * The container is attached to the DOM, so this now returns
                 * the real rendered table height.
                 */
                var measuredHeight = currentTbody
                    .closest('table')
                    .getBoundingClientRect()
                    .height;

                /*
                 * If the new row causes overflow, move it to a fresh page.
                 *
                 * Do not move a force-page or own-signature-pad row again.
                 */
                if (
                    measuredHeight > maxH &&
                    currentTbody.children.length > 1 &&
                    !forcePage &&
                    !ownSignaturePad
                ) {
                    currentTbody.removeChild(row);

                    newPage(false);
                    currentTbody.appendChild(row);
                }
            });

            /*
             * Remove only the original agreement pages.
             * Annexures are deliberately preserved.
             */
            oldPages.forEach(function(page) {
                page.remove();
            });

            /*
             * Add signatures to all newly generated agreement pages.
             */
            pages.forEach(function(page, pageIndex) {
                var hasOwnSignaturePad = Boolean(
                    page.querySelector(
                        '[data-own-signature-pad]'
                    )
                );

                if (hasOwnSignaturePad) {
                    addSlotSignatures(page, pageIndex);
                } else {
                    addDefaultSignatures(page, pageIndex);
                }
            });

            /*
             * Move preserved annexures after all generated agreement pages.
             */
            annexurePages.forEach(function(annexurePage) {
                container.appendChild(annexurePage);
            });

            /*
             * Wait until newly restored signature images and all other
             * document images finish loading.
             */
            await waitForPdfImages();

            /*
             * Let the browser finish one additional layout/paint cycle.
             */
            await nextAnimationFrame();
            await nextAnimationFrame();

            /*
             * Browsershot waits for this marker before creating the PDF.
             */
            document.body.dataset.pdfReady = 'true';

            /*
             * Place signatures relative to their explicit slots.
             */
            function addSlotSignatures(page, pageIndex) {
                var slots = page.querySelectorAll(
                    '[data-signature-slot]'
                );

                slots.forEach(function(slot) {
                    var slotId = slot.getAttribute(
                        'data-signature-slot'
                    );

                    var signer = slot.getAttribute(
                        'data-signer'
                    );

                    var src = signer === 'investor' ?
                        investorSrc :
                        companySrc;

                    if (!src) {
                        return;
                    }

                    var pageRect =
                        page.getBoundingClientRect();

                    var slotRect =
                        slot.getBoundingClientRect();

                    var wrap = createSignatureWrap(
                        signer,
                        src
                    );

                    wrap.setAttribute(
                        'data-slot-id',
                        slotId || ''
                    );

                    wrap.setAttribute(
                        'data-spot-key',
                        pageIndex + '-' + (slotId || signer)
                    );

                    wrap.style.width = '61mm';
                    wrap.style.height = '28mm';

                    /*
                     * Retains your current custom slot offsets.
                     */
                    var topAdjustment =
                        signer === 'investor' ?
                        22 :
                        59;

                    wrap.style.top =
                        (
                            slotRect.top -
                            pageRect.top +
                            topAdjustment
                        ) + 'px';

                    wrap.style.left =
                        (
                            slotRect.left -
                            pageRect.left +
                            73
                        ) + 'px';

                    page.appendChild(wrap);
                });
            }

            /*
             * Place signatures in the default footer positions.
             */
            function addDefaultSignatures(
                page,
                pageIndex
            ) {
                if (investorSrc) {
                    var investorWrap =
                        createSignatureWrap(
                            'investor',
                            investorSrc
                        );

                    investorWrap.setAttribute(
                        'data-spot-key',
                        pageIndex + '-default-investor'
                    );

                    investorWrap.style.right = '5mm';
                    investorWrap.style.bottom = '31mm';
                    investorWrap.style.width = '60mm';
                    investorWrap.style.height = '28mm';

                    page.appendChild(investorWrap);
                }

                if (companySrc) {
                    var companyWrap =
                        createSignatureWrap(
                            'company',
                            companySrc
                        );

                    companyWrap.setAttribute(
                        'data-spot-key',
                        pageIndex + '-default-company'
                    );

                    companyWrap.style.left = '5mm';
                    companyWrap.style.bottom = '31mm';
                    companyWrap.style.width = '60mm';
                    companyWrap.style.height = '28mm';

                    page.appendChild(companyWrap);
                }
            }

            /*
             * Create a standard absolute signature element.
             */
            function createSignatureWrap(
                signer,
                imageSource
            ) {
                var wrap = document.createElement('div');

                wrap.className = 'sig-placed-wrap';
                wrap.setAttribute(
                    'data-signer',
                    signer
                );

                wrap.style.position = 'absolute';

                var image = document.createElement('img');
                image.src = imageSource;
                image.alt = signer + ' signature';

                image.style.width = '100%';
                image.style.height = '100%';
                image.style.objectFit = 'contain';

                wrap.appendChild(image);

                return wrap;
            }

            /*
             * Resolve only after all img elements finish loading or fail.
             * Error handling prevents one missing image from hanging the job.
             */
            function waitForPdfImages() {
                var imagePromises = Array.from(
                    document.images
                ).map(function(image) {
                    if (image.complete) {
                        return Promise.resolve();
                    }

                    return new Promise(function(resolve) {
                        image.addEventListener(
                            'load',
                            resolve, {
                                once: true
                            }
                        );

                        image.addEventListener(
                            'error',
                            resolve, {
                                once: true
                            }
                        );
                    });
                });

                return Promise.all(imagePromises);
            }

            function nextAnimationFrame() {
                return new Promise(function(resolve) {
                    window.requestAnimationFrame(resolve);
                });
            }
        });
    </script>
</body>

</html>
