<?php

namespace App\Services\Investment;

use App\Models\Company;
use App\Models\Investment;
use App\Models\InvestorLedger;
use App\Models\PartialWithdrawalBifurcation;
use App\Repositories\Investment\InvestmentContractDocumentRepository;
use App\Repositories\Investment\InvestmentRepository;
use App\Repositories\Investment\InvestorAgreementRepository;
use App\Repositories\Investment\InvestorLedgerRepository;
use App\Repositories\Investment\InvestorRepository;
use Carbon\Carbon;

class InvestmentContractService
{
    public function __construct(
        protected InvestmentRepository $investmentRepository,
        protected InvestorRepository $investorRepository,
        protected InvestorAgreementRepository $InvAgreementRepo,
        protected InvestmentContractDocumentRepository $investmentContractDocumentRepository,
        protected InvestorLedgerRepository $ledgerRepository
    ) {}

    public function sendContractDocument($docId, $companyId)
    {
        $docDetails = $this->investmentContractDocumentRepository->find($docId);
        // dump($docDetails);

        $docTypeId     = $docDetails->investor_agreement_type_id;
        // dd($docTypeId);

        if ($docTypeId == 1) {
            return $this->sendMudarabah($docId, $companyId);
        } elseif ($docTypeId == 2) {
            return $this->sendAddendum($docId, $companyId);
        } elseif ($docTypeId == 3) {
            return $this->sendPartWithdrawal($docId, $companyId);
        } elseif ($docTypeId == 4) {
            return $this->sendNovation($docId, $companyId);
        } elseif ($docTypeId == 5) {
            // dd("test32");
            return $this->sendTermination($docId, $companyId);
        }
    }

    public function sendMudarabah($docId, $companyId)
    {
        // dd("test");
        $invDocDetails = $this->investmentContractDocumentRepository->find($docId);

        $docTypeId     = $invDocDetails->investor_agreement_type_id;
        $investorData   = $invDocDetails->investor;
        $investmentId   = $invDocDetails->investment_id;

        // $investorData   = $this->investorRepository->find($investorId);
        $templateDocumentDetail = $this->InvAgreementRepo->findByType($docTypeId);

        if ($investmentId == 0) {
            $investments = $this->investmentRepository->getAllByCondition([
                'investor_id'       => $invDocDetails->investor_id,
                'investment_status' => 1,
                'company_id' => $companyId,
            ]);

            // dd($investments);
            return $this->sendMudarabahMultiple($invDocDetails, $templateDocumentDetail, $investorData, $investments);
        }

        $investmentData = $this->investmentRepository->find($investmentId);
        // dd("test");
        //
        return $this->buildMudarabahPayload($invDocDetails, $templateDocumentDetail, $investorData, $investmentData);
    }


    /**
     * Multiple investments — Annexure A per contract, Annexure B per company.
     */
    private function sendMudarabahMultiple($invDocDetails, $documentDetail, $investorData, $investments)
    {
        // dd('multiple');
        $byCompany = [];
        foreach ($investments as $inv) {
            $byCompany[$inv->company_id][] = $inv;
        }

        $annexureAMulti    = '';
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

                $annexureAMulti .= $this->buildSingleAnnexureA(
                    $companyData,
                    $inv,
                    $investorData,
                    $annexureACounter,
                    $InvestorProfitPerc,
                    $CompanyProfitPerc,
                    // 'english'
                );
                // $annexureAMulti .= $this->buildSingleAnnexureA(
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
        // dump($annexureAMulti);
        // ── Grand totals ─────────────────────────────────────────────────────────
        $investmentsCollection = collect($investments);
        $grandTotalInvested    = $investmentsCollection->sum('investment_amount');
        $grandTotalProfit      = $investmentsCollection->sum('profit_amount');
        $grandTotalPerInterval = $investmentsCollection->sum('profit_amount_per_interval');

        // dd($grandTotalInvested);

        $firstInv    = $investmentsCollection->first();
        $companyData = Company::find($firstInv->company_id);

        $InvestorProfitPerc = $firstInv->profit_perc * 100 / 50;
        $CompanyProfitPerc  = 100 - $InvestorProfitPerc;

        $htmlMulti = $documentDetail->template;

        $placeholdersMulti = [
            // Dates
            '{mudarabah_created_long_date_eng}'  => date('j \d\a\y \o\f F Y', strtotime($invDocDetails->generated_date)),
            '{mudarabah_created_long_date_ar}'   => arabicLongDate($invDocDetails->generated_date),
            '{mudarabah_created_short_date_eng}' => date('d M Y', strtotime($invDocDetails->generated_date)),
            '{mudarabah_created_short_date_ar}'  => arabicShortDate($invDocDetails->generated_date),

            // '{mudarabah_created_long_date_eng}'  => date('j \d\a\y \o\f F Y'),
            // '{mudarabah_created_long_date_ar}'   => arabicLongDate(date('Y-m-d')),
            // '{mudarabah_created_short_date_eng}' => date('d M Y'),
            // '{mudarabah_created_short_date_ar}'  => arabicShortDate(date('Y-m-d')),

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
            '{company_account_no}' => $firstInv->companyBank->account_number,
            '{company_iban}'       => $firstInv->companyBank->iban,

            // profit
            '{inv_profit_perc}' => $InvestorProfitPerc,
            '{company_profit_perc}' => $CompanyProfitPerc,

            // Grand totals
            '{invested_amount}' => number_format($grandTotalInvested, 2),
            '{invested_amount_eng}' => numberToEnglishWords($grandTotalInvested) . 'Dirhams Only',
            '{invested_amount_ar}' => numberToArabicWords($grandTotalInvested) . ' فقط',
            '{total_invested_amount}' => number_format($grandTotalInvested, 2),
            '{total_profit}'          => number_format($grandTotalProfit, 2),
            '{monthly_estimate}'      => 0, //number_format($grandTotalPerInterval, 2)

            // Annexure blocks
            '{annexA}' => $annexureAMulti,
            // '{annexure_a_ar}'  => $annexureA_Ar,
            '{profit_month_eng}' => $annexureB_Eng,
            '{profit_month_ar}'  => $annexureB_Ar,
            // '{date}' =>  now()->format('d/m/Y')
        ];
        // dump($annexureAMulti);
        $htmlMulti = str_replace(array_keys($placeholdersMulti), array_values($placeholdersMulti), $htmlMulti);
        // dd($htmlMulti);
        return [
            'html'       => $htmlMulti,
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
        // string $lang
    ): string {
        $annexureNoR = toRoman($annexureNo);
        $annexureNoA = toarabicLetterNumber($annexureNo);
        // $isAr = $lang === 'arabic';

        $annexureLabelEng = "ANNEXURE-A";
        $annexureLabelAr  = "الملحق";

        $invDateEng = date('d M Y', strtotime($inv->investment_date));
        $invDateAr  = arabicShortDate($inv->investment_date);

        $investedAmount = number_format($inv->investment_amount, 2);
        $gracePeriod    = $inv->grace_period;
        $tenureEng      = $inv->profitInterval->profit_interval_name;
        $tenureAr       = profitInterval_ar($inv->profitInterval->profit_interval_name);

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
                                    <p class='marginClass text-md' style='font-weight:700 !important;'>{$annexureLabelEng} ({$annexureNoR})</p>
                                </div>
                            </td>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='arabic'>
                                    <p class='marginClass text-md' style='font-weight:700 !important;'>{$annexureLabelAr} ({$annexureNoA})</p>
                                </div>
                            </td>
                        </tr>



                        <tr>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='english'>
                                    <p class='marginClass text-sm'>Investment Date: {$invDateEng}</p>
                                </div>
                            </td>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='arabic'>
                                    <p class='marginClass text-sm'>تاريخ الاستثمار: {$invDateAr}</p>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='english'>
                                    <p class='marginClass text-sm'>Investment Amount: {$investedAmount}/- AED</p>
                                </div>
                            </td>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='arabic'>
                                    <p class='marginClass text-sm'>مبلغ الاستثمار: {$investedAmount}/- درهم إماراتي</p>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='english'>
                                    <p class='marginClass text-sm'>Investor Name: {$investorData->investor_name}</p>
                                </div>
                            </td>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='arabic'>
                                    <p class='marginClass text-sm'>اسم المستثمر: {$investorData->investor_name_arabic}</p>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='english'>
                                    <p class='marginClass text-sm'>Mobile No: {$investorData->investor_mobile}</p>
                                </div>
                            </td>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='arabic'>
                                    <p class='marginClass text-sm'>رقم الهاتف المتحرك: {$investorData->investor_mobile}</p>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='english'>
                                    <p class='marginClass text-sm'>Email ID: {$investorData->investor_email}</p>
                                </div>
                            </td>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='arabic'>
                                    <p class='marginClass text-sm'>البريد الإلكتروني: {$investorData->investor_email}</p>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='english'>
                                    <p class='marginClass text-sm'>Address: {$investorData->investor_address}</p>
                                </div>
                            </td>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='arabic'>
                                    <p class='marginClass text-sm'>العنوان: {$investorData->investor_address}</p>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='english'>
                                    <p class='marginClass text-sm'>Investor ID/ Passport: {$investorData->id_number}</p>
                                </div>
                            </td>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='arabic'>
                                    <p class='marginClass text-sm'>هوية المستثمر/جواز السفر: {$investorData->id_number}</p>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='english'>
                                    <p class='marginClass text-sm'>Nationality: {$investorData->nationality->nationality_name}</p>
                                </div>
                            </td>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='arabic'>
                                    <p class='marginClass text-sm'>الجنسية: {$investorData->nationality->nationality_arabic_name}</p>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='english'>
                                    <p class='marginClass text-sm'>Country of Residence: {$investorData->countryOfResidence->nationality_name}</p>
                                </div>
                            </td>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='arabic'>
                                    <p class='marginClass text-sm'>بلد الإقامة: {$investorData->countryOfResidence->nationality_arabic_name}</p>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='english'>
                                    <p class='marginClass text-sm'>Passport No: {$investorData->passport_number}</p>
                                </div>
                            </td>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='arabic'>
                                    <p class='marginClass text-sm'>رقم جواز السفر: {$investorData->passport_number}</p>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='english'>
                                    <p class='marginClass text-sm'>Grace Period (Days): {$gracePeriod}</p>
                                </div>
                            </td>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='arabic'>
                                    <p class='marginClass text-sm'>فترة السماح (بالأيام): {$gracePeriod} يوم</p>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='english'>
                                    <p class='marginClass text-sm'>Profit Sharing Ratio: Investor {$invProfitPerc}% and Company {$companyProfitPerc}%</p>
                                </div>
                            </td>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='arabic'>
                                    <p class='marginClass text-sm'>نسبة توزيع الربح: المستثمر {$invProfitPerc}% و الشركة {$companyProfitPerc}%</p>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='english'>
                                    <p class='marginClass text-sm'>Mode of Payment: {$investorData->paymentMode->payment_mode_name}</p>
                                </div>
                            </td>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='arabic'>
                                    <p class='marginClass text-sm'>طريقة الدفع: {$investorData->paymentMode->payment_mode_arabic_name}</p>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='english'>
                                    <p class='marginClass text-sm'>Tenure of Profit: {$tenureEng}</p>
                                </div>
                            </td>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='arabic'>
                                    <p class='marginClass text-sm'>مدة الربح: {$tenureAr}</p>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='english'>
                                    <p class='marginClass text-sm'>Beneficiary Name: {$investorData->investorBanks[0]->investor_beneficiary}</p>
                                </div>
                            </td>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='arabic'>
                                    <p class='marginClass text-sm'>اسم المستفيد: {$investorData->investorBanks[0]->investor_beneficiary_arabic}</p>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='english'>
                                    <p class='marginClass text-sm'>Beneficiary Bank Name: {$investorData->investorBanks[0]->investor_bank_name}</p>
                                </div>
                            </td>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='arabic'>
                                    <p class='marginClass text-sm'>البنك المستفيد: {$investorData->investorBanks[0]->investor_bank_name_arabic}</p>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='english'>
                                    <p class='marginClass text-sm'>Beneficiary IBAN: {$investorData->investorBanks[0]->investor_iban}</p>
                                </div>
                            </td>
                            <td width='50%' style='border:1px solid #ccc;'>
                                <div class='arabic'>
                                    <p class='marginClass text-sm'>رقم آيبان الخاص بالمستفيد: {$investorData->investorBanks[0]->investor_iban}</p>
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
                    <div class='english'><p class='marginClass text-md'>{$monthEng}</p></div>
                </td>
                <td width='50%' style='border:1px solid #ccc; padding:6px;'>
                    <div class='english'><p class='marginClass text-md'>AED {$amtFmt}/-</p></div>
                </td>
            </tr>";

            $rowsAr .= "
            <tr>
                <td width='50%' style='border:1px solid #ccc; padding:6px;'>
                    <div class='arabic'><p class='marginClass text-md'>{$monthAr}</p></div>
                </td>
                <td width='50%' style='border:1px solid #ccc; padding:6px;'>
                    <div class='arabic'><p class='marginClass text-md'>{$amtFmt}/- درهم إماراتي</p></div>
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
    private function buildMudarabahPayload($invDocDetails, $documentDetail, $investorData, $investmentData): array
    {

        $companyData = Company::find($investmentData->company_id);
        $html        = $documentDetail->template;

        $InvestorProfitPerc = $investmentData->profit_perc * 100 / 50;
        $CompanyProfitPerc  = 100 - $InvestorProfitPerc;

        // // ── Start from next month of mudarabah created date ──────────────────────
        // $startDate = Carbon::now()->addMonth()->startOfMonth();
        // $firstPayoutDate = Carbon::createFromFormat('M Y', $inv->initial_profit_release_month)
        //     ->startOfMonth();

        // // ── Use endOfMonth on windowEnd to avoid any startOfMonth boundary issues ─
        // $windowEnd = $startDate->copy()->addMonths(12)->endOfMonth();

        // $nextDate = $firstPayoutDate->copy()->startOfMonth();

        // while ($nextDate->lessThanOrEqualTo($windowEnd)) {

        //     if ($nextDate->greaterThanOrEqualTo($startDate)) {
        //         $contractPayoutMonths[$inv->id][] = $nextDate->copy();
        //     }

        //     $nextDate = Carbon::parse(calculateNextProfitReleaseDate(
        //         0,
        //         $inv->profit_interval_id,
        //         $nextDate->format('M Y'),
        //         $inv->payoutBatch->batch_name
        //     ))->startOfMonth();
        // }

        // commented bcz we created one single function for Annex B
        // $startDate = Carbon::createFromFormat('M Y', $investmentData->initial_profit_release_month)
        //     ->startOfMonth();

        // $profitEng = '';
        // $profitAr  = '';

        // $nextProfitDate = calculateNextProfitReleaseDate(
        //     0,
        //     $investmentData->profit_interval_id,
        //     $investmentData->initial_profit_release_month,
        //     $investmentData->payoutBatch->batch_name
        // );
        // // dump($startDate);
        // // dump($nextProfitDate);
        // // dd("test");
        // //

        // for ($i = 0; $i < 12; $i++) {
        //     $currentMonth = $startDate->copy()->addMonths($i);
        //     $windowEnd = $startDate->copy()->addMonths(12)->endOfMonth();
        //     $nextDate = $startDate->copy()->startOfMonth();
        //     $profitAmount = 0;
        //     // dd($currentMonth,$windowEnd,$nextDate);
        //     // dump($nextDate->lessThanOrEqualTo($windowEnd));
        //     while ($nextDate->lessThanOrEqualTo($windowEnd)) {
        //         $profitAmount = $investmentData->profit_amount_per_interval;
        //         if ($nextDate->greaterThanOrEqualTo($startDate)) {
        //             $contractPayoutMonths[$investmentData->id][] = $nextDate->copy();
        //         }

        //         $nextDate = Carbon::parse(calculateNextProfitReleaseDate(
        //             0,
        //             $investmentData->profit_interval_id,
        //             $nextDate->format('M Y'),
        //             $investmentData->payoutBatch->batch_name
        //         ))->startOfMonth();
        //         // dump($nextDate);
        //     }
        //     // dd("test");
        //     // if ($currentMonth->equalTo($nextProfitDate) || $i === 0) {
        //     //     $profitAmount = $investmentData->profit_amount_per_interval;

        //     // if ($i > 0) {
        //     //     $nextProfitDate = calculateNextProfitReleaseDate(
        //     //         0,
        //     //         $investmentData->profit_interval_id,
        //     //         $nextProfitDate,
        //     //         $investmentData->payoutBatch->batch_name
        //     //     );

        //     //     dump($nextProfitDate);
        //     // }
        //     // }

        //     $profitEng .= "
        //     <tr>
        //         <td width='50%' style='border:1px solid #ccc;'>
        //             <div class='english'>
        //                 <p class='text-md'>" . ($i + 1) . ' ' . $currentMonth->format('M Y') . "</p>
        //             </div>
        //         </td>
        //         <td width='50%' style='border:1px solid #ccc;'>
        //             <div class='english'>
        //                 <p class='text-md'>AED " . number_format($profitAmount, 2) . "/-</p>
        //             </div>
        //         </td>
        //     </tr>";

        //     $profitAr .= "
        //     <tr>
        //         <td width='50%' style='border:1px solid #ccc;'>
        //             <div class='arabic'>
        //                 <p class='text-md'>" . ($i + 1) . ' ' . arabicMY($currentMonth->format('M Y')) . "</p>
        //             </div>
        //         </td>
        //         <td width='50%' style='border:1px solid #ccc;'>
        //             <div class='arabic'>
        //                 <p class='text-md'>" . number_format($profitAmount, 2) . "/- درهم إماراتي</p>
        //             </div>
        //         </td>
        //     </tr>";
        // }

        $profitData = $this->annextureB($investmentData);

        $profitEng = $profitData['profitEng'];
        $profitAr = $profitData['profitAr'];

        // dd("test");
        $annexureA = $this->buildSingleAnnexureA(
            $companyData,
            $investmentData,
            $investorData,
            1,
            $InvestorProfitPerc,
            $CompanyProfitPerc,
            // 'english'
        );
        // dd($investmentData->investment_date);


        $placeholders = [
            '{mudarabah_created_long_date_eng}'  => date('j \d\a\y \o\f F Y', strtotime($invDocDetails->generated_date)),
            '{mudarabah_created_long_date_ar}'   => arabicLongDate($invDocDetails->generated_date),
            '{mudarabah_created_short_date_eng}' => date('d M Y', strtotime($invDocDetails->generated_date)),
            '{mudarabah_created_short_date_ar}'  => arabicShortDate($invDocDetails->generated_date),

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
            '{company_account_no}' => $investmentData->companyBank->account_number,
            '{company_iban}'       => $investmentData->companyBank->iban,

            '{annexA}' => $annexureA,

            '{total_invested_amount}' => $investmentData->investment_amount,
            '{total_profit}'          => $investmentData->profit_amount,
            '{monthly_estimate}'      => $investmentData->profit_amount_per_interval,
            '{profit_month_eng}'      => $profitEng,
            '{profit_month_ar}'       => $profitAr,
            '{date}' => Carbon::parse($investmentData->investment_date)->format('d/m/Y'),
        ];

        $html = str_replace(array_keys($placeholders), array_values($placeholders), $html);
        // dd($html);

        return [
            'html'       => $html,
            'letterHead' => asset('storage/' . $companyData->letter_head_path),
        ];
    }


    public function sendAddendum($docId, $companyId)
    {
        $docDetails = $this->investmentContractDocumentRepository->find($docId);

        $docTypeId     = $docDetails->investor_agreement_type_id;
        $investor   = $docDetails->investor;
        $investment   = $docDetails->investment;

        // $investor   = $this->investorRepository->find($investorId);
        // $investment = $this->investmentRepository->find($investmentId);
        $documentDetail = $this->InvAgreementRepo->findByType($docTypeId);
        $company    = Company::findOrFail($companyId);

        $prevAmount = 0;
        $allInv = Investment::where(['investor_id' => $docDetails->investor_id, 'company_id' => $companyId])->where('id', '!=', $docDetails->investment_id)->get();
        foreach ($allInv as $key => $inv) {
            $prevAmount += $inv->investment_amount;
        }

        // dd($docDetails);

        $currentTotal = $prevAmount + $investment->investment_amount;
        $investmentDate       = Carbon::parse($investment->investment_date);
        $mudarabahCreatedDate = Carbon::parse($docDetails->mudarabahReference->generated_date ?? $docDetails->mudarabahReference->created_at);

        $html        = $documentDetail->template;

        // Annexture A
        $InvestorProfitPerc = $investment->profit_perc * 100 / 50;
        $CompanyProfitPerc  = 100 - $InvestorProfitPerc;
        $annexureA = $this->buildSingleAnnexureA(
            $company,
            $investment,
            $investor,
            1,
            $InvestorProfitPerc,
            $CompanyProfitPerc,
            // 'english'
        );
        // dd($annexureA);
        // Annexture B
        $profitData = $this->annextureB($investment);
        // dd($profitData);

        $vars = [
            '{investment_long_date_eng}'        => $investmentDate->format('jS \d\a\y \o\f F Y'),
            '{mudarabah_created_long_date_eng}' => $mudarabahCreatedDate->format('jS \d\a\y \o\f F Y'),
            '{investment_long_date_ar}'         => arabicLongDate($investmentDate),
            '{mudarabah_created_long_date_ar}'  => arabicLongDate($mudarabahCreatedDate),

            '{company_name_eng}'  => $company->company_name,
            '{company_name_ar}'   => $company->company_arabic_name,
            '{company_license}'   => $company->trade_license_number,
            '{company_reg}'       => $company->registration_no,

            '{investor_name_eng}' => $investor->investor_name,
            '{investor_name_ar}'  => $investor->investor_name_arabic,
            '{id_number}'         => $investor->id_number,

            '{tot_prev_invested_amount}'     => number_format($prevAmount, 2),
            '{tot_prev_invested_amount_eng}' => numberToEnglishWords($prevAmount),
            '{tot_prev_invested_amount_ar}'  => numberToArabicWords($prevAmount),

            '{current_invested_amount}'     => number_format($investment->investment_amount, 2),
            '{current_invested_amount_eng}' => numberToEnglishWords($investment->investment_amount),
            '{current_invested_amount_ar}'  => numberToArabicWords($investment->investment_amount),

            '{new_total_investment_amount}'     => number_format($currentTotal, 2),
            '{new_total_investment_amount_eng}' => numberToEnglishWords($currentTotal),
            '{new_total_investment_amount_ar}'  => numberToArabicWords($currentTotal),

            '{annexA}' => $this->buildAnnexureARows($docDetails->investor_id, $companyId, $mudarabahCreatedDate, $docId),
            '{date}' =>  Carbon::parse($investment->investment_date)->format('d/m/Y'),

            '{annexA1}' => $annexureA,


            '{total_invested_amount}' => $investment->investment_amount,
            '{total_profit}'          => $investment->profit_amount,
            '{monthly_estimate}'      => $investment->profit_amount_per_interval,
            '{profit_month_eng}'      => $profitData['profitEng'],
            '{profit_month_ar}'       => $profitData['profitAr'],

            // profit
            '{inv_profit_perc}' => $InvestorProfitPerc,
            '{company_profit_perc}' => $CompanyProfitPerc,
        ];



        $html = str_replace(array_keys($vars), array_values($vars), $html);

        return [
            'html'       => $html,
            'letterHead' => asset('storage/' . $company->letter_head_path),
        ];
        // return view('documents.addendum', compact('html', 'annexureRows'));
    }

    private function buildAnnexureARows(int $investorId, int $companyId, $mudarabahCreatedDate, $docId): string
    {
        $rows = $this->ledgerRows($investorId, $companyId, $mudarabahCreatedDate, $docId);

        // ── Now build HTML from all collected rows ────────────────────────────
        $html = '';

        foreach ($rows as $row) {
            $isTotal    = $row['type'] === 'total';
            $isWithdraw = $row['type'] === 'withdrawal';

            $rowStyle   = $isTotal    ? 'background:#f0f0f0; font-weight:bold;' : '';
            $amtStyle   = $isWithdraw ? 'color:#C0392B;' : '';
            $serial     = $row['serial'] ?? '';

            $html .= "
                <tr style='{$rowStyle}'>
                    <td width='50%' style='border:1px solid #ccc;'>
                        <div class='english'>
                            <p class='text-sm'>
                                {$serial} | {$row['particulars_eng']} | <span style='{$amtStyle}'>{$row['amount']}</span> | {$row['received_on']} | {$row['doc_date']}
                            </p>
                        </div>
                    </td>
                    <td width='50%' style='border:1px solid #ccc;'>
                        <div class='arabic'>
                            <p class='text-sm' dir='rtl'>
                                {$serial} | {$row['particulars_ar']} | <span style='{$amtStyle}'>{$row['amount']}</span> | {$row['received_on']} | {$row['doc_date']}
                            </p>
                        </div>
                    </td>
                </tr>";
        }

        return $html;
    }


    public function sendNovation($docId, $companyId)
    {
        $docDetails = $this->investmentContractDocumentRepository->find($docId);

        $docTypeId     = $docDetails->investor_agreement_type_id;
        $investor   = $docDetails->investor;
        $investments = Investment::where(['investor_id' => $docDetails->investor_id, 'company_id' => $companyId])
            ->orderBy('investment_date')
            ->get();

        $totalInvested  = Investment::where('investor_id', $docDetails->investor_id)->sum('investment_amount');


        // $investor   = $this->investorRepository->find($investorId);
        // $investment = $this->investmentRepository->find($investmentId);
        $documentDetail = $this->InvAgreementRepo->findByType($docTypeId);
        $company    = Company::findOrFail($companyId);

        $html        = $documentDetail->template;

        // $investmentDate       = Carbon::parse($investment->investment_date);
        $novationCreated       = Carbon::parse($docDetails->generated_date);


        $vars = [
            '{novation_created_date}' => $novationCreated->format('d/m/Y'),

            '{company_name}'  => $company->company_name,
            '{company_licence_no}'   => $company->trade_license_number,
            '{company_reg_no}'       => $company->registration_no,

            '{investor_name}' => $investor->investor_name,
            '{investor_id_no}'         => $investor->id_number,

            '{total_invested_amount}'     => number_format($totalInvested, 2),
            '{total_invested_eng}' => numberToEnglishWords($totalInvested) . ' Only',
            '{date}' =>  Carbon::parse($novationCreated)->format('d/m/Y')

        ];



        $html = str_replace(array_keys($vars), array_values($vars), $html);


        return [
            'html'       => $html,
            'letterHead' => asset('storage/' . $company->letter_head_path),
        ];
    }

    public function sendPartWithdrawal($docId, $companyId)
    {
        // dd("test");
        $docDetails = $this->investmentContractDocumentRepository->find($docId);

        $docTypeId     = $docDetails->investor_agreement_type_id;
        $investor   = $docDetails->investor;
        $investments = Investment::where(['investor_id' => $docDetails->investor_id, 'company_id' => $companyId])
            ->orderBy('investment_date')
            ->get();

        $invDocDetails = $this->investmentContractDocumentRepository->find($docId);
        // dd($invDocDetails);

        $ledger = $this->ledgerRepository->getfirstbyCond(['investment_contract_document_id' => $docId]);
        // dd($ledger);



        // $investor   = $this->investorRepository->find($investorId);
        // $investment = $this->investmentRepository->find($investmentId);
        $documentDetail = $this->InvAgreementRepo->findByType($docTypeId);
        $company    = Company::findOrFail($companyId);

        $html        = $documentDetail->template;

        // dd($html);

        // $investmentDate       = Carbon::parse($investment->investment_date);
        $withdrwalCreated       = Carbon::parse($docDetails->generated_date);
        $mudarabahCreatedDate       = Carbon::parse($invDocDetails->mudarabahReference->generated_date ?? $invDocDetails->mudarabahReference->created_at);
        // dd("test");

        $vars = [
            '{doc_created_date}' => $withdrwalCreated->format('d/m/Y'),

            '{mudarabah_created_date}' => $mudarabahCreatedDate->format('jS F Y'),
            '{mudarabah_created_date_ar}'  => arabicShortDate($mudarabahCreatedDate),

            '{investor_name_eng}'  => $investor->investor_name,
            '{investor_name_ar}'  => $investor->investor_name_arabic,
            '{investor_id}'  => $investor->id_number,

            '{withdrawal_amount}' => number_format($ledger->transaction_amount, 2),
            '{withdrawal_amount_eng}' => numberToEnglishWords($ledger->transaction_amount) . ' Only',
            '{withdrawal_amount_ar}' => numberToArabicWords($ledger->transaction_amount),

            '{company_name_eng}'  => $company->company_name,
            '{company_name_ar}'  => $company->company_name_arabic,
            '{company_licence_no}'   => $company->trade_license_number,
            '{company_reg_no}'       => $company->registration_no,

            '{investor_name}' => $investor->investor_name,
            '{investor_id_no}' => $investor->id_number,

            '{html_eng}' => $this->ledgerPartialWithdrawal($docDetails->investor_id, $companyId, $mudarabahCreatedDate, $docId)['html_eng'],
            '{html_ar}' => $this->ledgerPartialWithdrawal($docDetails->investor_id, $companyId, $mudarabahCreatedDate, $docId)['html_ar'],
            '{date}' =>  Carbon::parse($ledger->withdrawal_date)->format('d/m/Y')
        ];
        // dd($vars);

        $html = str_replace(array_keys($vars), array_values($vars), $html);
        // dd('test');

        return [
            'html'       => $html,
            'letterHead' => asset('storage/' . $company->letter_head_path),
        ];
    }

    public function ledgerPartialWithdrawal(int $investorId, int $companyId, $mudarabahCreatedDate, $docId)
    {
        // dd("test");
        // 1 | الاستثمار الأصلي | __________ | ______ | ______<br>
        //                                 2 | سحب جزئي | ___________ | ______ | ______<br>
        //                                 3 | استثمار إضافي | __________ | ______ | ______<br>
        //                                 4 | استثمار إضافي | __________ | ______ | ______<br>
        //                                 5 | إجمالي رأس المال المعدّل | ___________ | ______ | ______


        // 1 | Original Investment | __________ | ______ | ______<br>
        //                                 2 | Partial Withdrawal | ___________ | ______ | ______<br>
        //                                 3 | Additional Investment | __________ | ______ | ______<br>
        //                                 4 | Additional Investment | __________ | ______ | ______<br>
        //                                 5 | Total Revised Capital | ___________ | ______ | ______

        $rows = $this->ledgerRows($investorId, $companyId, $mudarabahCreatedDate, $docId);
        // dd($rows);

        // ── Now build HTML from all collected rows ────────────────────────────
        $html = '';
        $htmlAr = '';

        foreach ($rows as $row) {
            $isTotal    = $row['type'] === 'total';
            $isWithdraw = $row['type'] === 'withdrawal';

            $rowStyle   = $isTotal    ? 'background:#f0f0f0; font-weight:bold;' : '';
            $amtStyle   = $isWithdraw ? 'color:#C0392B;' : '';
            $serial     = $row['serial'] ?? '';

            $html .= "
                    {$serial} | {$row['particulars_eng']} | <span style='{$amtStyle}'>{$row['amount']}</span> | {$row['received_on']} | {$row['doc_date']}
                    <br>";
            $htmlAr .= "
                    {$serial} | {$row['particulars_ar']} | <span style='{$amtStyle}'>{$row['amount']}</span> | {$row['received_on']} | {$row['doc_date']}
                    <br>";
        }

        return [
            'html_eng' => $html,
            'html_ar' => $htmlAr
        ];
    }

    public function ledgerRows($investorId, $companyId, $mudarabahCreatedDate, $docId)
    {
        // dd("test");
        $rows   = [];
        $serial = 1;
        $last_invDate = date('d/m/Y');

        $ledger = $this->ledgerRepository->getfirstbyCond(['investor_id' => $investorId, 'company_id' => $companyId]);
        // dd($ledger);

        $investments = Investment::where(['investor_id' => $investorId, 'company_id' => $companyId])
            // ->orderBy('investment_date')
            ->get();

        foreach ($investments as $key => $inv) {

            $rows[] = [
                'serial'          => $serial++,
                'particulars_eng' => $key == 0 ? 'Original Investment' : 'Additional Investment',
                'particulars_ar'  => $key == 0 ? 'الاستثمار الأصلي'    : 'استثمار إضافي',
                'amount'          => number_format($inv->total_invested_amount, 2),
                'received_on'     => Carbon::parse($inv->investment_date)->format('d/m/Y'),
                'doc_date'        => Carbon::parse($mudarabahCreatedDate)->format('d/m/Y'),
                'type'            => 'investment',
            ];

            $last_invDate = Carbon::parse($inv->investment_date)->format('d/m/Y');


            // foreach (PartialWithdrawal::where('investment_id', $inv->id)->orderBy('withdrawal_date')->get() as $wd) {
            //     $rows[] = [
            //         'serial'          => $serial++,
            //         'particulars_eng' => 'Partial Withdrawal',
            //         'particulars_ar'  => 'سحب جزئي',
            //         'amount'          => '(' . number_format($wd->amount, 2) . ')',
            //         'received_on'     => Carbon::parse($wd->withdrawal_date)->format('d/m/Y'),
            //         'doc_date'        => Carbon::parse($wd->document_date ?? $wd->withdrawal_date)->format('d/m/Y'),
            //         'type'            => 'withdrawal',
            //     ];
            // }
        }

        $partial_ledger = $this->ledgerRepository->findByDocId($docId);
        // dd
        $ledger_id = $partial_ledger->id;

        foreach (InvestorLedger::where('investor_id',  $investorId)->where('company_id', $companyId)->where('investor_transaction_type_id', 3)->where('id', '<=', $ledger_id)->orderBy('withdrawal_date')->get() as $wd) {
            $rows[] = [
                'serial'          => $serial++,
                'particulars_eng' => 'Partial Withdrawal',
                'particulars_ar'  => 'سحب جزئي',
                'amount'          => '(' . number_format($wd->transaction_amount, 2) . ')',
                'received_on'     => Carbon::parse($wd->withdrawal_date)->format('d/m/Y'),
                'doc_date'        => Carbon::parse($wd->document_date ?? $wd->withdrawal_date)->format('d/m/Y'),
                'type'            => 'Partial withdrawal',
            ];
        }
        // $partial_withdrawal = PartialWithdrawalBifurcation::

        // Total row
        $totalInvested  = Investment::where('investor_id', $investorId)->where('company_id', $companyId)->sum('total_invested_amount');
        $totalWithdrawn = PartialWithdrawalBifurcation::whereIn(
            'investment_id',
            Investment::where('investor_id', $investorId)->where('company_id', $companyId)->pluck('id')
        )->where('ledger_id', $ledger_id)->sum('withdrawal_amount');
        // $totalWithdrawn = 0;

        $rows[] = [
            'serial'          => null,
            'particulars_eng' => 'Total Revised Capital',
            'particulars_ar'  => 'إجمالي رأس المال المعدّل',
            'amount'          => number_format($totalInvested - $totalWithdrawn, 2),
            'received_on'     => $last_invDate,
            'doc_date'        => Carbon::parse($mudarabahCreatedDate)->format('d/m/Y'),
            'type'            => 'total',
        ];

        return $rows;
    }
    public function sendTermination($docId, $companyId)
    {
        // dd("test");
        $docDetails = $this->investmentContractDocumentRepository->find($docId);

        // dd($docDetails);

        $docTypeId     = $docDetails->investor_agreement_type_id;
        $investor   = $docDetails->investor;
        // dd($investor);
        $investments = Investment::where(['investor_id' => $docDetails->investor_id, 'company_id' => $companyId])
            ->orderBy('investment_date')
            ->get();
        // dd($investments);
        // $investment = $this->investmentRepository->find($docDetails->investment_id);
        // dd($investment);

        $invDocDetails = $this->investmentContractDocumentRepository->find($docId);
        // dd($invDocDetails);

        $ledger = $this->ledgerRepository->getfirstbyCond(['investment_contract_document_id' => $docId]);
        // dd($ledger);

        $total_amount = ($ledger->transaction_amount ?? 0) + ($ledger->withdrawal_month_profit ?? 0);



        // $investor   = $this->investorRepository->find($investorId);
        // $investment = $this->investmentRepository->find($investmentId);
        $documentDetail = $this->InvAgreementRepo->findByType($docTypeId);
        $company    = Company::findOrFail($companyId);
        // dd($company);

        $html        = $documentDetail->template;

        // dd($html);

        $day = Carbon::parse($docDetails->created_at);
        $termination_requested_date = carbon::parse($ledger->requested_date);
        // $termination_requested_date = carbon::parse($ledger->termination_requested_date);


        // $investmentDate       = Carbon::parse($investment->investment_date);
        $withdrwalCreated       = Carbon::parse($docDetails->generated_date);
        $mudarabahCreatedDate       = Carbon::parse($invDocDetails->mudarabahReference->generated_date ?? $invDocDetails->mudarabahReference->created_at);
        // dd("test");


        // Build the underlined, "date1, date2 and date3" addendum dates string
        $addendumDates = $this->joinWithAnd(
            $investments->map(fn($inv) => '<span class="underline-date">'
                . Carbon::parse($inv->investment_date)->format('jS F Y')
                . '</span>')->toArray()
        );
        $vars = [

            '{settlement_day}'   => $day->format('d'),      // e.g. 15
            '{settlement_month}' => $day->format('F'),      // e.g. July
            '{settlement_year}'  => $day->format('Y'),       // e.g. 2026


            '{doc_created_date}' => $withdrwalCreated->format('d/m/Y'),

            '{mudarabah_created_date}' => $mudarabahCreatedDate->format('jS F Y'),
            '{mudarabah_created_date_ar}'  => arabicShortDate($mudarabahCreatedDate),

            '{investor_name_eng}'  => $investor->investor_name,
            '{investor_name_ar}'  => $investor->investor_name_arabic,
            '{investor_id}'  => $investor->id_number,

            // '{withdrawal_amount}' => number_format($ledger->transaction_amount, 2),
            // '{withdrawal_amount_eng}' => numberToEnglishWords($ledger->transaction_amount) . ' Only',
            // '{withdrawal_amount_ar}' => numberToArabicWords($ledger->transaction_amount),

            '{company_name_eng}'  => $company->company_name,
            // '{company_name_ar}'  => $company->company_name_arabic,
            '{company_licence_no}'   => $company->trade_license_number,
            '{company_reg_no}'       => $company->registration_no,

            '{investor_name}' => $investor->investor_name,
            '{investor_id_no}' => $investor->id_number,

            '{termination_requested_date}' => $termination_requested_date->format('d-m-Y'),

            '{addendum_dates}'    => $addendumDates,

            '{capital}' => $ledger->transaction_amount,
            '{profit}' => $ledger->withdrawal_month_profit,
            '{total_amount}' => $total_amount,

            '{date}' =>  Carbon::parse($ledger->withdrawal_date)->format('d/m/Y')

            // '{html_eng}' => $this->ledgerPartialWithdrawal($docDetails->investor_id, $companyId, $mudarabahCreatedDate, $docId)['html_eng'],
            // '{html_ar}' => $this->ledgerPartialWithdrawal($docDetails->investor_id, $companyId, $mudarabahCreatedDate, $docId)['html_ar']

        ];
        // dd($vars);

        $html = str_replace(array_keys($vars), array_values($vars), $html);
        // dd('test');

        return [
            'html'       => $html,
            'letterHead' => asset('storage/' . $company->letter_head_path),
            'investments'      => $investments,
        ];
    }

    private function joinWithAnd(array $items, bool $arabic = false): string
    {
        if (empty($items)) {
            return '____________________';
        }

        if (count($items) === 1) {
            return $items[0];
        }

        $last = array_pop($items);
        $andWord = $arabic ? 'و' : 'and';

        return implode(', ', $items) . ' ' . $andWord . ' ' . $last;
    }


    function annextureB($investmentData)
    {
        $profitRecord = $investmentData->profitRecords;
        // dd($profitRecord);
        // // dd($investmentData);
        // $startDate = Carbon::createFromFormat('M Y', $investmentData->initial_profit_release_month)
        //     ->startOfMonth();

        $profitEng = '';
        $profitAr  = '';


        // for ($i = 0; $i < 12; $i++) {
        //     $currentMonth = $startDate->copy()->addMonths($i);
        //     $windowEnd = $startDate->copy()->addMonths(12)->endOfMonth();
        //     $nextDate = $startDate->copy()->startOfMonth();
        //     $profitAmount = 0;

        //     while ($nextDate->lessThanOrEqualTo($windowEnd)) {
        //         $profitAmount = $investmentData->profit_amount_per_interval;
        //         if ($nextDate->greaterThanOrEqualTo($startDate)) {
        //             $contractPayoutMonths[$investmentData->id][] = $nextDate->copy();
        //         }

        //         $nextDate = Carbon::parse(calculateNextProfitReleaseDate(
        //             0,
        //             $investmentData->profit_interval_id,
        //             $nextDate->format('M Y'),
        //             $investmentData->payoutBatch->batch_name
        //         ))->startOfMonth();
        //     }
        foreach ($profitRecord as $key => $profit) {
            $profitAmount = $profit->profit_amount;
            $currentMonth = Carbon::parse($profit->profit_release_month);

            $profitEng .= "
            <tr>
                <td width='50%' style='border:1px solid #ccc;'>
                    <div class='english'>
                        <p class='text-md'>" . $key + 1 . ' ' . $currentMonth->format('M Y') . "</p>
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
                        <p class='text-md'>" . $key + 1 . ' ' . arabicMY($currentMonth->format('M Y')) . "</p>
                    </div>
                </td>
                <td width='50%' style='border:1px solid #ccc;'>
                    <div class='arabic'>
                        <p class='text-md'>" . number_format($profitAmount, 2) . "/- درهم إماراتي</p>
                    </div>
                </td>
            </tr>";
            // }
        }

        return [
            'profitEng' => $profitEng,
            'profitAr'  => $profitAr,
        ];
    }
}
