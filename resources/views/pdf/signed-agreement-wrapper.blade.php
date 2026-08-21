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

        body {
            margin: 0;
            padding: 0;
        }

        .new-page {
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
        }

        .new-page.annexure-page {
            background-image: none !important;
        }

        /* Page 1 clearance */
        .file-content {
            position: relative;
            padding: 34mm 16mm 58mm 16mm;
        }

        /* Pages 2+ clearance */
        .file-content.page-subsequent {
            padding-top: 35mm;
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
        document.addEventListener('DOMContentLoaded', function() {
            var MM_TO_PX = 96 / 25.4;
            var PAGE_H_MM = 297;
            var PAD_TOP_P1_MM = 34; // must match .file-content padding-top (first page)
            var PAD_TOP_PN_MM = 35; // must match .file-content.page-subsequent padding-top
            var PAD_BOT_MM = 58; // must match .file-content padding-bottom

            var maxH_p1 = (PAGE_H_MM - PAD_TOP_P1_MM - PAD_BOT_MM) * MM_TO_PX;
            var maxH_pn = (PAGE_H_MM - PAD_TOP_PN_MM - PAD_BOT_MM) * MM_TO_PX;

            var oldPages = Array.from(document.querySelectorAll('.new-page'));
            if (oldPages.length === 0) return;

            var bgUrl = null;
            var m = (oldPages[0].getAttribute('style') || '').match(/background-image:\s*url\((["']?)(.*?)\1\)/);
            if (m) bgUrl = m[2];

            var investorImg = document.querySelector('.sig-placed-wrap[data-signer="investor"] img');
            var companyImg = document.querySelector('.sig-placed-wrap[data-signer="company"] img');
            var investorSrc = investorImg ? investorImg.src : null;
            var companySrc = companyImg ? companyImg.src : null;

            // Flatten every row across all saved pages, preserving original order.
            var rows = [];
            oldPages.forEach(function(page) {
                var tbody = page.querySelector('.file-content > table > tbody');
                if (!tbody) return;
                Array.from(tbody.children).forEach(function(tr) {
                    rows.push(tr);
                });
            });

            var container = document.createElement('div');
            var pages = [];
            var currentTbody, usedH, maxH;

            function newPage(isFirst) {
                var page = document.createElement('div');
                page.className = 'new-page';
                if (bgUrl) page.style.backgroundImage = "url('" + bgUrl + "')";
                page.style.position = 'relative';

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
                container.appendChild(page);

                pages.push(page);
                currentTbody = tbody;
                usedH = 0;
                maxH = isFirst ? maxH_p1 : maxH_pn;
                return page;
            }

            newPage(true);

            rows.forEach(function(row) {
                var forcePage = row.getAttribute('data-force-page') === 'true';
                var ownSigPad = row.hasAttribute('data-own-signature-pad');

                if ((forcePage || ownSigPad) && usedH > 0) newPage(false);

                currentTbody.appendChild(row);
                var rowH = row.getBoundingClientRect().height;

                if (usedH + rowH > maxH && usedH > 0 && !forcePage && !ownSigPad) {
                    currentTbody.removeChild(row);
                    newPage(false);
                    currentTbody.appendChild(row);
                    usedH = row.getBoundingClientRect().height;
                } else {
                    usedH += rowH;
                }
            });

            var parent = oldPages[0].parentNode;
            oldPages.forEach(function(p) {
                parent.removeChild(p);
            });
            pages.forEach(function(p) {
                parent.appendChild(p);
            });

            pages.forEach(function(page, i) {
                var hasOwnPad = !!page.querySelector('[data-own-signature-pad]');

                if (hasOwnPad) {
                    var slots = page.querySelectorAll('[data-signature-slot]');
                    slots.forEach(function(slot) {
                        var slotId = slot.getAttribute('data-signature-slot');
                        var signer = slot.getAttribute('data-signer');
                        var src = signer === 'investor' ? investorSrc : companySrc;
                        if (!src) return;

                        var pageRect = page.getBoundingClientRect();
                        var slotRect = slot.getBoundingClientRect();

                        var wrap = document.createElement('div');
                        wrap.className = 'sig-placed-wrap';
                        wrap.setAttribute('data-signer', signer);
                        wrap.setAttribute('data-slot-id', slotId);
                        wrap.setAttribute('data-spot-key', i + '-' + slotId);
                        wrap.style.position = 'absolute';
                        wrap.style.width = '61mm';
                        wrap.style.height = '28mm';
                        wrap.style.top = (slotRect.top - pageRect.top + (signer === 'investor' ?
                            22 : 59)) + 'px';
                        wrap.style.left = (slotRect.left - pageRect.left + 73) + 'px';

                        var img = document.createElement('img');
                        img.src = src;
                        wrap.appendChild(img);
                        page.appendChild(wrap);
                    });
                } else {
                    if (investorSrc) {
                        var iw = document.createElement('div');
                        iw.className = 'sig-placed-wrap';
                        iw.setAttribute('data-signer', 'investor');
                        iw.setAttribute('data-spot-key', i + '-default-investor');
                        iw.style.position = 'absolute';
                        iw.style.right = '5mm';
                        iw.style.bottom = '31mm';
                        iw.style.width = '60mm';
                        iw.style.height = '28mm';
                        var iimg = document.createElement('img');
                        iimg.src = investorSrc;
                        iw.appendChild(iimg);
                        page.appendChild(iw);
                    }
                    if (companySrc) {
                        var cw = document.createElement('div');
                        cw.className = 'sig-placed-wrap';
                        cw.setAttribute('data-signer', 'company');
                        cw.setAttribute('data-spot-key', i + '-default-company');
                        cw.style.position = 'absolute';
                        cw.style.left = '5mm';
                        cw.style.bottom = '31mm';
                        cw.style.width = '60mm';
                        cw.style.height = '28mm';
                        var cimg = document.createElement('img');
                        cimg.src = companySrc;
                        cw.appendChild(cimg);
                        page.appendChild(cw);
                    }
                }
            });
        });
    </script>
</body>

</html>
