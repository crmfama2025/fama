<?php

namespace App\Services\Investment;

use App\Models\Company;
use App\Repositories\Investment\InvestmentRepository;
use App\Repositories\Investment\InvestorAgreementRepository;
use App\Repositories\Investment\InvestorRepository;
use Carbon\Carbon;

class InvestmentContractService
{
    public function __construct(
        protected InvestmentRepository $investmentRepository,
        protected InvestorRepository $investorRepository,
        protected InvestorAgreementRepository $InvAgreementRepo
    ) {}

    public function sendContractDocument($docTypeId, $investorId, $investmentId, $companyId)
    {
        if ($docTypeId == 1) {
            return $this->sendMudarabah($docTypeId, $investorId, $investmentId, $companyId);
        }
    }

    public function sendMudarabah($docTypeId, $investorId, $investmentId, $companyId)
    {
        $investorData   = $this->investorRepository->find($investorId);
        $documentDetail = $this->InvAgreementRepo->findByType($docTypeId);

        if ($investmentId == 0) {
            $investments = $this->investmentRepository->getAllByCondition([
                'investor_id'       => $investorId,
                'investment_status' => 1,
                'company_id' => $companyId,
            ]);

            // dd($investments);
            return $this->sendMudarabahMultiple($documentDetail, $investorData, $investments);
        }

        $investmentData = $this->investmentRepository->find($investmentId);
        return $this->buildMudarabahPayload($documentDetail, $investorData, $investmentData);
    }


    /**
     * Multiple investments — Annexure A per contract, Annexure B per company.
     */
    private function sendMudarabahMultiple($documentDetail, $investorData, $investments)
    {
        $byCompany = [];
        foreach ($investments as $inv) {
            $byCompany[$inv->company_id][] = $inv;
        }

        $annexureA    = '';
        // $annexureA_Ar     = '';
        $annexureB_Eng    = '';
        $annexureB_Ar     = '';
        $annexureACounter = 1;
        $annexureBCounter = 1;

        foreach ($byCompany as $companyId => $companyInvestments) {
            $companyData = Company::find($companyId);

            // ── Annexure A: one block per contract ───────────────────────────────
            foreach ($companyInvestments as $inv) {
                // dump($inv);
                $InvestorProfitPerc = $inv->profit_perc * 100 / 50;
                $CompanyProfitPerc  = 100 - $InvestorProfitPerc;

                $annexureA .= $this->buildSingleAnnexureA(
                    $companyData,
                    $inv,
                    $investorData,
                    $annexureACounter,
                    $InvestorProfitPerc,
                    $CompanyProfitPerc,
                    'english'
                );
                // $annexureA .= $this->buildSingleAnnexureA(
                //     $companyData,
                //     $inv,
                //     $investorData,
                //     $annexureACounter,
                //     $InvestorProfitPerc,
                //     $CompanyProfitPerc,
                //     'arabic'
                // );

                $annexureACounter++;
            }

            // ── Annexure B: one profit schedule per company ──────────────────────
            [$bEng, $bAr, $totalFmtEng, $totalFmtAr] = $this->buildCompanyProfitSchedule(
                $companyInvestments,
                $annexureBCounter
            );
            $annexureB_Eng .= $bEng;
            $annexureB_Ar  .= $bAr;

            $annexureBCounter++;
        }

        // ── Grand totals ─────────────────────────────────────────────────────────
        $investmentsCollection = collect($investments);
        $grandTotalInvested    = $investmentsCollection->sum('investment_amount');
        $grandTotalProfit      = $investmentsCollection->sum('profit_amount');
        $grandTotalPerInterval = $investmentsCollection->sum('profit_amount_per_interval');

        $firstInv    = $investmentsCollection->first();
        $companyData = Company::find($firstInv->company_id);

        $InvestorProfitPerc = $firstInv->profit_perc * 100 / 50;
        $CompanyProfitPerc  = 100 - $InvestorProfitPerc;

        $html = $documentDetail->template;

        $placeholders = [
            // Dates
            '{mudarabah_created_long_date_eng}'  => date('j \d\a\y \o\f F Y'),
            '{mudarabah_created_long_date_ar}'   => arabicLongDate(date('Y-m-d')),
            '{mudarabah_created_short_date_eng}' => date('d M Y'),
            '{mudarabah_created_short_date_ar}'  => arabicShortDate(date('Y-m-d')),

            // Investor
            '{investor_name_eng}'        => $investorData->investor_name,
            '{investor_name_ar}'         => $investorData->investor_name_arabic,
            '{resident_country_eng}'     => $investorData->countryOfResidence->nationality_name,
            '{resident_country_ar}'      => $investorData->countryOfResidence->nationality_arabic_name,
            '{id_number}'                => $investorData->id_number,
            '{investor_email}'           => $investorData->investor_email,
            '{investor_mobile}'          => $investorData->investor_mobile,
            '{investor_address}'         => $investorData->investor_email,
            '{investor_id_no}'           => $investorData->id_number,
            '{investor_nationality_eng}' => $investorData->nationality->nationality_name,
            '{investor_nationality_ar}'  => $investorData->nationality->nationality_arabic_name,
            '{passport_no}'              => $investorData->passport_number,
            '{mode_of_payment_eng}'      => $investorData->paymentMode->payment_mode_name,
            '{mode_of_payment_ar}'       => $investorData->paymentMode->payment_mode_arabic_name,

            // Beneficiary
            '{beneficiary_name_eng}'     => $investorData->investorBanks[0]->investor_beneficiary,
            '{beneficiary_bankname_eng}' => $investorData->investorBanks[0]->investor_bank_name,
            '{beneficiary_name_ar}'      => $investorData->investorBanks[0]->investor_beneficiary_arabic,
            '{beneficiary_bankname_ar}'  => $investorData->investorBanks[0]->investor_bank_name_arabic,
            '{beneficiary_iban}'         => $investorData->investorBanks[0]->investor_iban,

            // company data
            '{company_name_eng}' => $companyData->company_name,
            '{company_name_ar}' => $companyData->company_arabic_name,
            '{company_license}' => $companyData->trade_license_number,
            '{company_reg}' => $companyData->registration_no,
            '{company_email}' => $companyData->email,

            '{company_bank_eng}' => $firstInv->companyBank->bank_name,
            '{company_bank_ar}' => $firstInv->companyBank->bank_arabic_name,
            '{company_account_no}' => 'company_account_no',
            '{company_iban}' => 'company_iban',

            // profit
            '{inv_profit_perc}' => $InvestorProfitPerc,
            '{company_profit_perc}' => $CompanyProfitPerc,

            // Grand totals
            '{invested_amount}' => number_format($grandTotalInvested, 0),
            '{invested_amount_eng}' => numberToEnglishWords($grandTotalInvested) . ' Only',
            '{invested_amount_ar}' => numberToArabicWords($grandTotalInvested) . ' فقط',
            '{total_invested_amount}' => number_format($grandTotalInvested, 2),
            '{total_profit}'          => number_format($grandTotalProfit, 2),
            '{monthly_estimate}'      => number_format($grandTotalPerInterval, 2),

            // Annexure blocks
            '{annexA}' => $annexureA,
            // '{annexure_a_ar}'  => $annexureA_Ar,
            '{profit_month_eng}' => $annexureB_Eng,
            '{profit_month_ar}'  => $annexureB_Ar,
        ];

        $html = str_replace(array_keys($placeholders), array_values($placeholders), $html);

        return [
            'html'       => $html,
            'letterHead' => asset('storage/' . $companyData->letter_head_path),
        ];
    }


    /**
     * Annexure A — single contract block.
     * Called once per investment/contract.
     */
    private function buildSingleAnnexureA(
        $companyData,
        $inv,
        $investorData,
        int $annexureNo,
        float $invProfitPerc,
        float $companyProfitPerc,
        string $lang
    ): string {
        $annexureNoR = toRoman($annexureNo);
        $annexureNoA = toarabicLetterNumber($annexureNo);
        $isAr = $lang === 'arabic';

        $annexureLabelEng = "ANNEXURE-A ({$annexureNoR})";
        $annexureLabelAr  = "الملحق ({$annexureNoA})";

        $invDateEng = date('d M Y', strtotime($inv->investment_date));
        $invDateAr  = arabicShortDate($inv->investment_date);

        $investedAmount = number_format($inv->investment_amount, 2);
        $gracePeriod    = $inv->grace_period;
        $tenureEng      = $inv->profitInterval->profit_interval_name;
        $tenureAr       = 'tenure_ar'; // replace with actual arabic tenure when available

        $companyNameEng = $companyData->company_name;
        $companyNameAr  = $companyData->company_arabic_name;

        return "
            <tr data-row data-force-page='true'>
                <td colspan='2' style='padding:0;'>
                    <table width='100%' border='1' align='center' class='mt-15' cellpadding='0'
                        cellspacing='0' style='max-width:100%;'>

                        <tr style='background-color:#F2F2F2'>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='english'>
                                    <p class='text-md' style='font-weight:700 !important;'>{$annexureLabelEng}</p>
                                </div>
                            </td>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='arabic'>
                                    <p class='text-md' style='font-weight:700 !important;'>{$annexureLabelAr}</p>
                                </div>
                            </td>
                        </tr>

                        

                        <tr>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='english'>
                                    <p class='text-sm'>Investment Date: {$invDateEng}</p>
                                </div>
                            </td>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='arabic'>
                                    <p class='text-sm'>تاريخ الاستثمار: {$invDateAr}</p>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='english'>
                                    <p class='text-sm'>Investment Amount: {$investedAmount}/- AED</p>
                                </div>
                            </td>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='arabic'>
                                    <p class='text-sm'>مبلغ الاستثمار: {$investedAmount}/- درهم إماراتي</p>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='english'>
                                    <p class='text-sm'>Investor Name: {$investorData->investor_name}</p>
                                </div>
                            </td>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='arabic'>
                                    <p class='text-sm'>اسم المستثمر: {$investorData->investor_name_arabic}</p>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='english'>
                                    <p class='text-sm'>Mobile No: {$investorData->investor_mobile}</p>
                                </div>
                            </td>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='arabic'>
                                    <p class='text-sm'>رقم الهاتف المتحرك: {$investorData->investor_mobile}</p>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='english'>
                                    <p class='text-sm'>Email ID: {$investorData->investor_email}</p>
                                </div>
                            </td>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='arabic'>
                                    <p class='text-sm'>البريد الإلكتروني: {$investorData->investor_email}</p>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='english'>
                                    <p class='text-sm'>Address: {$investorData->investor_address}</p>
                                </div>
                            </td>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='arabic'>
                                    <p class='text-sm'>العنوان: {$investorData->investor_address}</p>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='english'>
                                    <p class='text-sm'>Investor ID/ Passport: {$investorData->id_number}</p>
                                </div>
                            </td>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='arabic'>
                                    <p class='text-sm'>هوية المستثمر/جواز السفر: {$investorData->id_number}</p>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='english'>
                                    <p class='text-sm'>Nationality: {$investorData->nationality->nationality_name}</p>
                                </div>
                            </td>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='arabic'>
                                    <p class='text-sm'>الجنسية: {$investorData->nationality->nationality_arabic_name}</p>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='english'>
                                    <p class='text-sm'>Country of Residence: {$investorData->countryOfResidence->nationality_name}</p>
                                </div>
                            </td>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='arabic'>
                                    <p class='text-sm'>بلد الإقامة: {$investorData->countryOfResidence->nationality_arabic_name}</p>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='english'>
                                    <p class='text-sm'>Passport No: {$investorData->passport_number}</p>
                                </div>
                            </td>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='arabic'>
                                    <p class='text-sm'>رقم جواز السفر: {$investorData->passport_number}</p>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='english'>
                                    <p class='text-sm'>Grace Period (Days): {$gracePeriod}</p>
                                </div>
                            </td>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='arabic'>
                                    <p class='text-sm'>فترة السماح (بالأيام): {$gracePeriod} يوم</p>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='english'>
                                    <p class='text-sm'>Profit Sharing Ratio: Investor {$invProfitPerc}% and Company {$companyProfitPerc}%</p>
                                </div>
                            </td>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='arabic'>
                                    <p class='text-sm'>نسبة توزيع الربح: المستثمر {$invProfitPerc}% و الشركة {$companyProfitPerc}%</p>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='english'>
                                    <p class='text-sm'>Mode of Payment: {$investorData->paymentMode->payment_mode_name}</p>
                                </div>
                            </td>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='arabic'>
                                    <p class='text-sm'>طريقة الدفع: {$investorData->paymentMode->payment_mode_arabic_name}</p>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='english'>
                                    <p class='text-sm'>Tenure of Profit: {$tenureEng}</p>
                                </div>
                            </td>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='arabic'>
                                    <p class='text-sm'>مدة الربح: {$tenureAr}</p>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='english'>
                                    <p class='text-sm'>Beneficiary Name: {$investorData->investorBanks[0]->investor_beneficiary}</p>
                                </div>
                            </td>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='arabic'>
                                    <p class='text-sm'>اسم المستفيد: {$investorData->investorBanks[0]->investor_beneficiary_arabic}</p>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='english'>
                                    <p class='text-sm'>Beneficiary Bank Name: {$investorData->investorBanks[0]->investor_bank_name}</p>
                                </div>
                            </td>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='arabic'>
                                    <p class='text-sm'>البنك المستفيد: {$investorData->investorBanks[0]->investor_bank_name_arabic}</p>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='english'>
                                    <p class='text-sm'>Beneficiary IBAN: {$investorData->investorBanks[0]->investor_iban}</p>
                                </div>
                            </td>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='arabic'>
                                    <p class='text-sm'>رقم آيبان الخاص بالمستفيد: {$investorData->investorBanks[0]->investor_iban}</p>
                                </div>
                            </td>
                        </tr>

                    </table>
                </td>
            </tr>";
    }


    /**
     * Annexure B — 12-month profit schedule for one company,
     * summing all its contracts per month.
     * Called once per company.
     */
    private function buildCompanyProfitSchedule(
        array $companyInvestments,
        int $annexureNo
    ): array {

        // ── Start from next month of mudarabah created date ──────────────────────
        $startDate = Carbon::now()->addMonth()->startOfMonth();

        // ── Pre-calculate ALL upcoming profit dates for each contract ─────────────
        // Instead of just storing "next date", we build a full list of
        // payout months for each contract within the 12-month window.
        $contractPayoutMonths = [];

        foreach ($companyInvestments as $inv) {
            $contractPayoutMonths[$inv->id] = [];

            $firstPayoutDate = Carbon::createFromFormat('M Y', $inv->initial_profit_release_month)
                ->startOfMonth();

            // ── Use endOfMonth on windowEnd to avoid any startOfMonth boundary issues ─
            $windowEnd = $startDate->copy()->addMonths(12)->endOfMonth();

            $nextDate = $firstPayoutDate->copy()->startOfMonth();

            while ($nextDate->lessThanOrEqualTo($windowEnd)) {

                if ($nextDate->greaterThanOrEqualTo($startDate)) {
                    $contractPayoutMonths[$inv->id][] = $nextDate->copy();
                }

                $nextDate = Carbon::parse(calculateNextProfitReleaseDate(
                    0,
                    $inv->profit_interval_id,
                    $nextDate->format('M Y'),
                    $inv->payoutBatch->batch_name
                ))->startOfMonth();
            }
        }
        // dd($contractPayoutMonths);
        $totalInvested = collect($companyInvestments)->sum('investment_amount');

        $rowsEng = '';
        $rowsAr  = '';

        for ($i = 0; $i < 12; $i++) {
            $currentMonth = $startDate->copy()->addMonths($i);
            $monthTotal   = 0;

            foreach ($companyInvestments as $inv) {
                // Check if any of this contract's payout months match current month
                foreach ($contractPayoutMonths[$inv->id] as $payoutDate) {
                    if ($currentMonth->equalTo($payoutDate)) {
                        $monthTotal += $inv->profit_amount_per_interval;
                        break; // No need to check further for this contract
                    }
                }
            }

            $amtFmt   = number_format($monthTotal, 2);
            $monthEng = ($i + 1) . ' ' . $currentMonth->format('M Y');
            $monthAr  = ($i + 1) . ' ' . arabicMY($currentMonth->format('M Y'));

            $rowsEng .= "
            <tr>
                <td width='50%' style='border:1px solid #ccc; padding:6px;'>
                    <div class='english'><p class='text-md'>{$monthEng}</p></div>
                </td>
                <td width='50%' style='border:1px solid #ccc; padding:6px;'>
                    <div class='english'><p class='text-md'>AED {$amtFmt}/-</p></div>
                </td>
            </tr>";

            $rowsAr .= "
            <tr>
                <td width='50%' style='border:1px solid #ccc; padding:6px;'>
                    <div class='arabic'><p class='text-md'>{$monthAr}</p></div>
                </td>
                <td width='50%' style='border:1px solid #ccc; padding:6px;'>
                    <div class='arabic'><p class='text-md'>{$amtFmt}/- درهم إماراتي</p></div>
                </td>
            </tr>";
        }

        $totalFmtEng = 'AED ' . number_format($totalInvested, 2);
        $totalFmtAr  = number_format($totalInvested, 2) . '/- درهم إماراتي';

        return [$rowsEng, $rowsAr, $totalFmtEng, $totalFmtAr];
    }

    /**
     * Original single-investment path — unchanged logic.
     */
    private function buildMudarabahPayload($documentDetail, $investorData, $investmentData): array
    {
        $companyData = Company::find($investmentData->company_id);
        $html        = $documentDetail->template;

        $InvestorProfitPerc = $investmentData->profit_perc * 100 / 50;
        $CompanyProfitPerc  = 100 - $InvestorProfitPerc;

        $startDate = Carbon::createFromFormat('M Y', $investmentData->initial_profit_release_month)
            ->startOfMonth();

        $profitEng = '';
        $profitAr  = '';

        $nextProfitDate = calculateNextProfitReleaseDate(
            0,
            $investmentData->profit_interval_id,
            $investmentData->initial_profit_release_month,
            $investmentData->payoutBatch->batch_name
        );

        for ($i = 0; $i < 12; $i++) {
            $currentMonth = $startDate->copy()->addMonths($i);
            $profitAmount = 0;

            if ($currentMonth->equalTo($nextProfitDate) || $i === 0) {
                $profitAmount = $investmentData->profit_amount_per_interval;

                if ($i > 0) {
                    $nextProfitDate = calculateNextProfitReleaseDate(
                        0,
                        $investmentData->profit_interval_id,
                        $nextProfitDate,
                        $investmentData->payoutBatch->batch_name
                    );
                }
            }

            $profitEng .= "
            <tr>
                <td width='50%' style='border:1px solid #ccc;'>
                    <div class='english'>
                        <p class='text-md'>" . ($i + 1) . ' ' . $currentMonth->format('M Y') . "</p>
                    </div>
                </td>
                <td width='50%' style='border:1px solid #ccc;'>
                    <div class='english'>
                        <p class='text-md'>AED " . number_format($profitAmount, 2) . "/-</p>
                    </div>
                </td>
            </tr>";

            $profitAr .= "
            <tr>
                <td width='50%' style='border:1px solid #ccc;'>
                    <div class='arabic'>
                        <p class='text-md'>" . ($i + 1) . ' ' . arabicMY($currentMonth->format('M Y')) . "</p>
                    </div>
                </td>
                <td width='50%' style='border:1px solid #ccc;'>
                    <div class='arabic'>
                        <p class='text-md'>" . number_format($profitAmount, 2) . "/- درهم إماراتي</p>
                    </div>
                </td>
            </tr>";
        }

        $annexureA = $this->buildSingleAnnexureA(
            $companyData,
            $investmentData,
            $investorData,
            1,
            $InvestorProfitPerc,
            $CompanyProfitPerc,
            'english'
        );


        $placeholders = [
            '{mudarabah_created_long_date_eng}'  => date('j \d\a\y \o\f F Y'),
            '{mudarabah_created_long_date_ar}'   => arabicLongDate(date('Y-m-d')),
            '{mudarabah_created_short_date_eng}' => date('d M Y'),
            '{mudarabah_created_short_date_ar}'  => arabicShortDate(date('Y-m-d')),

            '{investor_name_eng}'        => $investorData->investor_name,
            '{investor_name_ar}'         => $investorData->investor_name_arabic,
            '{resident_country_eng}'     => $investorData->countryOfResidence->nationality_name,
            '{resident_country_ar}'      => $investorData->countryOfResidence->nationality_arabic_name,
            '{id_number}'                => $investorData->id_number,
            '{investor_email}'           => $investorData->investor_email,
            '{investor_mobile}'          => $investorData->investor_mobile,
            '{investor_address}'         => $investorData->investor_email,
            '{investor_id_no}'           => $investorData->id_number,
            '{investor_nationality_eng}' => $investorData->nationality->nationality_name,
            '{investor_nationality_ar}'  => $investorData->nationality->nationality_arabic_name,
            '{passport_no}'              => $investorData->passport_number,
            '{investment_date_eng}'      => date('d M Y', strtotime($investmentData->investment_date)),
            '{investment_date_ar}'       => arabicShortDate($investmentData->investment_date),
            '{invested_amount}'          => $investmentData->investment_amount,
            '{invested_amount_eng}' => numberToEnglishWords($investmentData->investment_amount) . ' Only',
            '{invested_amount_ar}' => numberToArabicWords($investmentData->investment_amount) . ' فقط',
            '{grace_period}'             => $investmentData->grace_period,
            '{mode_of_payment_eng}'      => $investorData->paymentMode->payment_mode_name,
            '{mode_of_payment_ar}'       => $investorData->paymentMode->payment_mode_arabic_name,
            '{tenure_eng}'               => $investmentData->profitInterval->profit_interval_name,
            '{tenure_ar}'                => 'tenure_ar',

            '{beneficiary_name_eng}'     => $investorData->investorBanks[0]->investor_beneficiary,
            '{beneficiary_bankname_eng}' => $investorData->investorBanks[0]->investor_bank_name,
            '{beneficiary_name_ar}'      => $investorData->investorBanks[0]->investor_beneficiary_arabic,
            '{beneficiary_bankname_ar}'  => $investorData->investorBanks[0]->investor_bank_name_arabic,
            '{beneficiary_iban}'         => $investorData->investorBanks[0]->investor_iban,

            '{inv_profit_perc}'     => $InvestorProfitPerc,
            '{company_profit_perc}' => $CompanyProfitPerc,

            '{company_name_eng}' => $companyData->company_name,
            '{company_name_ar}'  => $companyData->company_arabic_name,
            '{company_license}'  => $companyData->trade_license_number,
            '{company_reg}'      => $companyData->registration_no,
            '{company_email}'    => $companyData->email,
            '{company_bank_eng}' => $investmentData->companyBank->bank_name,
            '{company_bank_ar}'  => $investmentData->companyBank->bank_arabic_name,
            '{company_account_no}' => 'company_account_no',
            '{company_iban}'       => 'company_iban',

            '{annexA}' => $annexureA,

            '{total_invested_amount}' => $investmentData->investment_amount,
            '{total_profit}'          => $investmentData->profit_amount,
            '{monthly_estimate}'      => $investmentData->profit_amount_per_interval,
            '{profit_month_eng}'      => $profitEng,
            '{profit_month_ar}'       => $profitAr,
        ];

        $html = str_replace(array_keys($placeholders), array_values($placeholders), $html);

        return [
            'html'       => $html,
            'letterHead' => asset('storage/' . $companyData->letter_head_path),
        ];
    }
}
