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
</body>

</html>
