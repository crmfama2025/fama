<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Agreement PDF</title>
    <style>
        /* @media print {
            body {

                color: #000 !important;
                background: url('{{ public_path('images/fama-letterhead.png') }}') no-repeat center center;
                background-size: cover;
            }

        } */


        @page {
            margin: 20px 30px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 15px;
            /* background: url('{{ str_replace('\\', '/', public_path('images/fama-letterhead.png')) }}') no-repeat center center;
            background-size: cover; */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        th,
        td {
            /* border: 1px solid #000; */
            padding: 5px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #f9f9f9;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .header-table td {
            border: none;
        }

        .logo {
            width: 100px;
        }

        .section-title {
            font-weight: bold;
            text-align: center;
            margin: 15px 0 5px;
        }

        .footer {
            position: fixed;
            bottom: 10px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #666;
        }

        .bg-letterhead img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
</head>

<body>
    {{-- <div class="bg-letterhead">
        <img src="{{ str_replace('\\', '/', public_path('images/fama-letterhead.png')) }}">
    </div> --}}

    {{-- Same contract tables as your normal view --}}
    @include('admin.projects.contract.includes.acknowledgement_content', ['contract' => $contract])


    <div class="footer">
        Fama Real Estate — Generated on {{ now()->format('d/m/Y H:i') }}
    </div>
</body>

</html>
