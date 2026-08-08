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

    <div class="signature-block annexure-signature" data-own-signature-pad="true">
        <div class="font-weight-bold" id="signee-name">{{ ucfirst($investor->investor_name) }}</div>
        <br>
        <div class="annexure-signature-slot" data-signature-slot="investor_en" data-signer="investor">
            Signature:
        </div>
    </div>

</div>
