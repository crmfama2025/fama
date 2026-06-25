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

    public function sendContractDocument($docTypeId, $investorId, $investmentId = 0)
    {
        if ($docTypeId == 1) {
            return $this->sendMudarabah($docTypeId, $investorId, $investmentId);
        }
    }

    public function sendMudarabah($docTypeId, $investorId, $investmentId = 0)
    {

        $investorData = $this->investorRepository->find($investorId);
        $documentDetail = $this->InvAgreementRepo->findByType($docTypeId);

        $investmentData = $this->investmentRepository->find($investmentId);

        $companyData = Company::find($investmentData->company_id);

        $html = $documentDetail->template;

        $InvestorProfitPerc = $investmentData->profit_perc * 100 / 50;
        $CompanyProfitPerc = 100 - $InvestorProfitPerc;


        $annexB = $this->getAnnexB($investmentData);
        $profitEng = $annexB['profitEng'];
        $profitAr = $annexB['profitAr'];

        // dd($profitEng);



        $placeholders = [
            // common Data
            '{mudarabah_created_long_date_eng}' => date('j \d\a\y \o\f F Y'),
            '{mudarabah_created_long_date_ar}' => arabicLongDate(date('Y-m-d')),
            '{mudarabah_created_short_date_eng}' => date('d M Y'),
            '{mudarabah_created_short_date_ar}' => arabicShortDate(date('Y-m-d')),


            // investor Data
            '{investor_name_eng}' => $investorData->investor_name,
            '{investor_name_ar}' => $investorData->investor_name_arabic,
            '{resident_country_eng}' => $investorData->countryOfResidence->nationality_name,
            '{resident_country_ar}' => $investorData->countryOfResidence->nationality_arabic_name,
            '{id_number}' => $investorData->id_number,
            '{investor_email}' => $investorData->investor_email,
            '{investor_mobile}' => $investorData->investor_mobile,
            '{investor_address}' => $investorData->investor_email,
            '{investor_id_no}' => $investorData->id_number,
            '{investor_nationality_eng}' => $investorData->nationality->nationality_name,
            '{investor_nationality_ar}' => $investorData->nationality->nationality_arabic_name,
            '{passport_no}' => $investorData->passport_number,

            // investment Data
            '{investment_date_eng}' => date('d M Y', strtotime($investmentData->investment_date)),
            '{investment_date_ar}' => arabicShortDate($investmentData->investment_date),
            '{invested_amount}' => $investmentData->investment_amount,
            '{invested_amount_eng}' => 'invested_amount_eng',
            '{invested_amount_ar}' => 'invested_amount_ar',
            '{grace_period}' => $investmentData->grace_period,
            '{mode_of_payment_eng}' => $investorData->paymentMode->payment_mode_name,
            '{mode_of_payment_ar}' => $investorData->paymentMode->payment_mode_arabic_name,
            '{tenure_eng}' => $investmentData->profitInterval->profit_interval_name,
            '{tenure_ar}' => 'tenure_ar',


            '{beneficiary_name_eng}' => $investorData->investorBanks[0]->investor_beneficiary,
            '{beneficiary_bankname_eng}' => $investorData->investorBanks[0]->investor_bank_name,
            '{beneficiary_name_ar}' => $investorData->investorBanks[0]->investor_beneficiary_arabic,
            '{beneficiary_bankname_ar}' => $investorData->investorBanks[0]->investor_bank_name_arabic,
            '{beneficiary_iban}' => $investorData->investorBanks[0]->investor_iban,


            // profit
            '{inv_profit_perc}' => $InvestorProfitPerc,
            '{company_profit_perc}' => $CompanyProfitPerc,


            // company data
            '{company_name_eng}' => $companyData->company_name,
            '{company_name_ar}' => $companyData->company_arabic_name,
            '{company_license}' => $companyData->trade_license_number,
            '{company_reg}' => $companyData->registration_no,
            '{company_email}' => $companyData->email,

            '{company_bank_eng}' => $investmentData->companyBank->bank_name,
            '{company_bank_ar}' => $investmentData->companyBank->bank_arabic_name,
            '{company_account_no}' => 'company_account_no',
            '{company_iban}' => 'company_iban',


            '{total_invested_amount}' => $investmentData->investment_amount,
            '{total_profit}' => $investmentData->profit_amount,
            '{monthly_estimate}' => $investmentData->profit_amount_per_interval,
            '{profit_month_eng}' => $profitEng,
            '{profit_month_ar}' => $profitAr,
        ];

        $html = str_replace(
            array_keys($placeholders),
            array_values($placeholders),
            $html
        );

        $array = [
            'html' => $html,
            'letterHead' => asset('storage/' . $companyData->letter_head_path)
        ];

        return $array;
    }
}
