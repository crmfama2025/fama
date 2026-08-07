{{-- <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Investment Annexure</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"> --}}
{{-- <style>
    body {
        background: #eef0f2;
        font-family: 'Segoe UI', Arial, sans-serif;
        color: #222;
    }

    .letter-sheet {
        background: #fff;
        max-width: 800px;
        margin: 40px auto;
        padding: 127px 70px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.08);
        border: 1px solid #e0e0e0;
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
        margin-top: 70px;
    }

    .signature-line {
        display: inline-block;
        border-bottom: 1px solid #333;
        min-width: 260px;
        margin-top: 45px;
    }

    @media print {
        body {
            background: #fff;
        }

        .letter-sheet {
            box-shadow: none;
            border: none;
            margin: 0;
            padding: 20px;
        }
    }
</style> --}}
{{-- </head> --}}

{{-- <body> --}}

<div class="letter-sheet new-page">

    <div class="d-flex justify-content-between align-items-start">
        <h1 class="letter-title flex-grow-1">Investment Annexure</h1>
    </div>

    <div class="letter-meta">
        <span class="label">Date:</span> <span id="letter-date">{{ now()->format('d/m/Y') }}</span>
    </div>

    <div class="letter-meta">
        <div>To,</div>
        <div class="font-weight-bold" id="recipient-name">Mr./Ms. {{ ucfirst($investor->investor_name) }}</div>
    </div>

    <div class="letter-subject">
        Subject: Confirmation and Breakdown of Investments
    </div>

    <p>Dear Sir,</p>

    <p>
        Please find below the detailed breakdown of your investment:
        <strong>AED <span id="total-amount">{{ number_format($investments['grand_total'], 2) }}</span>/-</strong>
    </p>


    <h6 class="font-weight-bold mt-4 mb-3">Investment Breakdown:</h6>

    <table class="table table-bordered investment-table" id="breakdown-table">
        <thead>
            <tr>
                <th>Company Name</th>
                <th class="text-right">Invested Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($investments['companies'] as $company)
                @if ($company->total_invested > 0)
                    <tr>
                        <td>{{ $company->company_name }}</td>
                        <td class="text-right">AED {{ number_format($company->total_invested, 2) }}/-</td>
                    </tr>
                @endif
            @endforeach

        </tbody>
        <tfoot>
            <tr>
                <td>Total</td>
                <td class="text-right">AED {{ number_format($investments['grand_total'], 2) }}/-</td>
            </tr>
        </tfoot>
    </table>

    <div class="signature-block" data-own-signature-pad="true">
        <div class="font-weight-bold" id="signee-name">{{ ucfirst($investor->investor_name) }}</div>
        <br>
        <div data-signature-slot="investor_en" data-signer="investor">
            Signature:
        </div>
    </div>

</div>

{{-- </body>

</html> --}}
