<?php

namespace App\Console\Commands;

use App\Models\Agreement;
use App\Models\AgreementDocument;
use App\Models\AgreementPayment;
use App\Models\AgreementPaymentDetail;
use App\Models\AgreementTenant;
use App\Models\AgreementUnit;
use App\Models\Contract;
use App\Models\ContractPaymentReceivable;
use App\Models\ContractUnitDetail;
use App\Services\Agreement\AgreementService;
use App\Services\Contracts\ContractService;
use App\Services\Contracts\SubUnitDetailService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateAgreementsFromContracts extends Command
{

    protected SubUnitDetailService $subUnitDetailService;
    protected ContractService $contractService;
    protected AgreementService $agreementService;

    public function __construct(
        SubUnitDetailService $subUnitDetailService,
        ContractService $contractService,
        AgreementService $agreementService
    ) {
        parent::__construct();
        $this->subUnitDetailService = $subUnitDetailService;
        $this->contractService = $contractService;
        $this->agreementService = $agreementService;
    }
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-agreements-from-contracts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $contracts = Contract::with('contract_rentals', 'contract_unit')
            ->where('contract_status', 7)
            ->where('is_agreement_added', 0)
            ->get();
        // dd($contracts);

        foreach ($contracts as $ct) {
            if ($ct->contract_type_id == 2) {
                // dd('skip');
                DB::beginTransaction();

                try {
                    /** STEP 1: Create Agreement **/
                    $agreement_code = $this->agreementService->setProjectCode();
                    $agreementData = [
                        'company_id' => $ct->company_id,
                        'contract_id' => $ct->id,
                        'start_date' => dateFormatChange($ct->contract_detail->start_date, 'Y-m-d'),
                        'end_date'   => dateFormatChange($ct->contract_detail->end_date, 'Y-m-d'),
                        'duration_in_months' => $ct->contract_detail->duration_in_months,
                        'added_by' => 1,
                        'agreement_code' => $agreement_code
                    ];
                    // dd($agreementData);

                    $agreement = Agreement::create($agreementData);
                    // dd($agreement);
                    // Tenant
                    $tenantData = [
                        'agreement_id' => $agreement->id,
                        'tenant_name' => 'Faateh Real Estate',
                        'tenant_mobile' => '+971568856995',
                        'tenant_email' => 'adil@faateh.ae',
                        'nationality_id' => 12,
                        'tenant_address' => 'Dubai',
                        'added_by' => 1,
                        'contact_person' => 'Adil Faridi',
                        'contact_number' => '+971568856995',
                        'contact_email' => 'adil@faateh.ae',
                        // 'tenant_street' => $data['tenant_street'],
                        // 'tenant_city' => $data['tenant_city'],
                        'emirate_id' => 2,

                    ];
                    // dd($tenantData);
                    $tenant = AgreementTenant::create($tenantData);
                    // dd($tenant);


                    $path = 'agreements/documents/TRADE_LICENSE/faateh_license.pdf';

                    $agreementDocumentData = [
                        'agreement_id' => $agreement->id,
                        'document_type' => 3,
                        'document_number' => 973851,
                        'original_document_path' => $path,
                        'original_document_name' => 'faateh_license.pdf',
                        'added_by' => 1,
                    ];
                    $documents = AgreementDocument::create($agreementDocumentData);


                    /** STEP 3: Payment **/
                    $rent_annum = str_replace(',', '', $ct->contract_rentals->rent_receivable_per_annum);
                    // dd($rent_annum);
                    $installment_id = $ct->contract_rentals->receivable_installments;
                    // dd($installment_id);
                    $installment = $ct->contract_rentals->installment->installment_name;
                    // dd($installment);
                    $interval = 1;
                    $payment_data = [
                        'agreement_id' => $agreement->id,
                        'installment_id' => $installment_id ?? null,
                        'interval' => $interval ?? null,
                        'beneficiary' => $ct->company()->first()->company_name,
                        'added_by' => 1,
                        'total_rent_annum' => $rent_annum

                    ];
                    // dd($payment_data);
                    $payment = AgreementPayment::create($payment_data);
                    // dd($payment);
                    $receivables = ContractPaymentReceivable::where('contract_id', $ct->id)
                        // ->orderBy('installment_no')
                        ->get();
                    // dd($receivables);



                    $contractUnits = ContractUnitDetail::with('contractSubUnitDetails')->where('contract_id', $ct->id)->get();
                    // dd($contractUnits);
                    foreach ($contractUnits as $cu) {
                        $subunit_ids = $cu->contractSubUnitDetails()
                            ->pluck('id')
                            ->toArray();
                        // dd($subunit_ids);
                        $agreementUnit = [
                            'agreement_id' => $agreement->id,
                            'added_by' => 1,
                            'unit_type_id' => $cu->unit_type_id,
                            'contract_unit_details_id' => $cu->id,
                            // 'contract_subunit_details_id' => $subunit_ids,
                            'rent_per_month' => $cu->rent_per_unit_per_month,
                            'rent_per_annum_agreement' => $cu->rent_per_unit_per_annum,
                            'subunit_ids' => $subunit_ids,
                            'unit_revenue' => $cu->unit_revenue,
                        ];
                        // dd($agreementUnit);
                        $agreementUnit = AgreementUnit::create($agreementUnit);
                        // dd($agreementUnit);
                        foreach ($receivables as $receivable) {
                            $agreementUnitPayment = [
                                'agreement_payment_id' => $payment->id,
                                'status' => 'pending',
                                'added_by' => 1,
                                'agreement_id' => $agreement->id,
                                'agreement_payment_id' => $payment->id,
                                'contract_unit_id' => $cu->id,
                                'agreement_unit_id' => $agreementUnit->id,
                                'payment_mode_id' => 1,
                                'payment_date' => dateFormatChange($receivable->receivable_date, 'Y-m-d'),
                                'payment_amount' => $cu->rent_per_unit_per_month,
                                // 'bank_id' => $receivable->bank_id ?? null,
                                // 'cheque_number' => $detail['cheque_number'] ?? null,
                            ];
                            // dd($agreementUnitPayment);
                            $paymentDetail = AgreementPaymentDetail::create($agreementUnitPayment);
                        }
                        $cu->is_vacant = 1;
                        $cu->save();
                    }

                    /** STEP 4: Mark Contract Processed **/
                    $this->subUnitDetailService->allvacant($ct->id);
                    $this->contractService->updateAgreementStatus($ct->id);

                    DB::commit();

                    $this->info("Agreement created for contract ID: {$ct->id}");
                } catch (\Exception $e) {
                    DB::rollBack();
                    \Log::error($e);
                    $this->error("Failed for contract ID: {$ct->id}");
                }
            }
        }
    }
}
