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
            /* margin: 20px 30px; */
            margin: 20px 30px;
            /* TOP RIGHT BOTTOM LEFT */
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 14px;
            /* background: url('{{ str_replace('\\', '/', public_path('images/fama-letterhead.png')) }}') no-repeat center center;
            background-size: cover; */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 7px;
        }

        th,
        td {
            /* border: 1px solid #000; */
            padding: 3px;
            text-align: left;
            vertical-align: top;
        }

        /* th {
            background: #f9f9f9;
        } */

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
    @if (!empty($company->letter_head_path))
        <img src="{{ public_path('storage/' . $company->letter_head_path) }}"
            style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1;">
    @endif
    {{-- Same contract tables as your normal view --}}
    @if ($page == 0)
        <div style="padding-bottom:60px; margin-left:20px;margin-right:20px;">
            @include('admin.projects.contract.includes.acknowledgement_content_print', [
                'contract' => $contract,
                'company' => $company,
            ])
        </div>
    @else
        @include('admin.projects.contract.includes.acknowledgement_content', ['contract' => $contract])
    @endif


    <div class="footer">
        Fama Real Estate — Generated on {{ now()->format('d/m/Y H:i') }}
    </div>
</body>

</html>
