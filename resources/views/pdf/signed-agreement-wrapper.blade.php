<head>
    <meta charset="utf-8">

    <title>Signed Investment Agreement</title>

    <script>
        window.AgreementDocumentConfig = {
            /* Saved bodyHtml already contains final .new-page elements. */
            mode: 'pdf'
        };
    </script>

    @include('admin.investment.inv_agreement.partials.agreement-document-assets')

    <style>
        html,
        body {
            width: 210mm;
            background: #fff;
        }

        /* Saved HTML uses #file-print-area. This wrapper is optional. */
        #pdf-pages-container {
            width: 210mm;
            margin: 0;
            padding: 0;
        }

        #file-print-area>.new-page {
            margin: 0 !important;
        }
    </style>

    @if ($isLegacyDocument && filled($letterheadUrl))
        <style>
            .new-page {
                background-image: url('{{ $letterheadUrl }}') !important;
                background-size: 210mm 297mm !important;
                background-position: top left !important;
                background-repeat: no-repeat !important;
                print-color-adjust: exact !important;
                -webkit-print-color-adjust: exact !important;
            }
        </style>
    @endif
</head>

{{-- Recommended PDF wrapper body: do not repaginate saved HTML. --}}

<body>
    <div id="pdf-pages-container">
        <div id="file-print-area">
            {!! $bodyHtml !!}
        </div>
    </div>
</body>
