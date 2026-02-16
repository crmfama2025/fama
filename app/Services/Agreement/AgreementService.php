<?php

namespace App\Services\Agreement;

use App\Models\Agreement;
use App\Models\AgreementDocument;
use App\Models\Contract;
use App\Models\ContractSubunitDetail;
use App\Models\ContractUnitDetail;
use App\Repositories\Agreement\AgreementDocRepository;
use App\Repositories\Agreement\AgreementPaymentDetailRepository;
use App\Repositories\Agreement\AgreementPaymentRepository;
use App\Repositories\Agreement\AgreementRepository;
use App\Repositories\Agreement\AgreementTenantRepository;
use App\Repositories\Agreement\AgreementUnitRepository;
use App\Services\Contracts\ContractService;
use App\Services\Contracts\SubUnitDetailService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;


class AgreementService
{
    public function __construct(
        protected AgreementRepository $agreementRepository,
        protected AgreementDocRepository $agreementDocRepository,
        protected AgreementTenantRepository $agreementTenantRepository,
        protected AgreementPaymentRepository $agreementPaymentRepository,
        protected AgreementPaymentDetailRepository $agreementPaymentDetailRepository,
        protected AgreementUnitRepository $agreementUnitRepository,
        protected AgreementTenantService $agreementTenantService,
        protected AgreementPaymentDetailService $agreementPaymentDetailService,
        protected AgreementPaymentService $agreementPaymentService,
        protected AgreementUnitService $agreementUnitService,
        protected AgreementDocumentService $agreementDocumentService,
        protected SubUnitDetailService $subUnitDetailserv,
        protected ContractService $contractService

    ) {}



    public function getAll()
    {
        return $this->agreementRepository->all();
    }
    public function getAllAgreements()
    {
        return $this->agreementRepository->getAllAgreements();
    }

    public function getById($id)
    {
        return $this->agreementRepository->find($id);
    }

    public function createOrRestore(array $data, $user_id = null)
    {
        $this->validate($data);
        $data['added_by'] = $user_id ?: auth()->user()->id;
        $data['agreement_code'] = $this->setProjectCode();

        DB::beginTransaction();

        // dd($data);


        try {
            // STEP 1: Create Agreement
            $agreementData = [
                'company_id' => $data['company_id'],
                'contract_id' => $data['contract_id'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'duration_in_months' => $data['duration_in_months'],
                'added_by' => $data['added_by'],
                'agreement_code' => $data['agreement_code'],
            ];
            $this->validate($agreementData);

            // dd('test');

            $agreement = $this->agreementRepository->create($agreementData);
            // dd($agreement);
            $this->agreementDocumentService->storeDocuments(
                $agreement,
                $data['documents'] ?? [],
                $data['added_by']
            );

            // STEP 3: Insert Tenant Info
            $tenantData = [
                'agreement_id' => $agreement->id,
                'tenant_name' => $data['tenant_name'] ?? null,
                'tenant_mobile' => $data['tenant_mobile'] ?? null,
                'tenant_email' => $data['tenant_email'] ?? null,
                'nationality_id' => $data['nationality_id'] ?? null,
                'tenant_address' => $data['tenant_address'] ?? null,
                'added_by' => $data['added_by'],
                'contact_person' => $data['contact_person'],
                'contact_number' => $data['contact_number'],
                'contact_email' => $data['contact_email'],
                'tenant_street' => $data['tenant_street'],
                'tenant_city' => $data['tenant_city'],
                'emirate_id' => $data['emirate_id']

            ];

            $this->agreementTenantService->create($tenantData);

            $payment_data = [
                'agreement_id' => $agreement->id,
                'installment_id' => $data['installment_id'] ?? null,
                'interval' => $data['interval'] ?? null,
                'beneficiary' => $data['beneficiary'] ?? null,
                'added_by' => $data['added_by'],
                'total_rent_annum' => $data['total_rent_per_annum']

            ];
            $payment = $this->agreementPaymentService->create($payment_data);
            $ct = Contract::with('contract_rentals', 'contract_unit')
                ->where('id', $agreement->contract_id)
                ->first();
            $count = $ct->contract_rentals->receivable_installments;
            $rent_annum = str_replace(',', '', $ct->contract_rentals->rent_receivable_per_annum);
            // dd($rent_annum);


            if ($data['contract_type'] == 2) {

                foreach ($data['unit_detail'] as $unit) {

                    $contractUnitDetail = ContractUnitDetail::find($unit['contract_unit_details_id']);
                    $rent_annum_agreement = $contractUnitDetail->rent_per_unit_per_annum;
                    $subunit_ids = ContractSubunitDetail::where('contract_unit_detail_id', $unit['contract_unit_details_id'])
                        ->pluck('id')
                        ->toArray();

                    $unitdata = [
                        'agreement_id' => $agreement->id,
                        'added_by' => $data['added_by'],
                        'unit_type_id' => $unit['unit_type_id'],
                        'contract_unit_details_id' => $unit['contract_unit_details_id'],
                        'contract_subunit_details_id' => $unit['contract_subunit_details_id'] ?? null,
                        'rent_per_month' => $unit['rent_per_month'],
                        // 'rent_per_annum_agreement' => $rent_annum_agreement,
                        'rent_per_annum_agreement' => $unit['rent_per_annum'],
                        'subunit_ids' => $subunit_ids,
                        'unit_revenue' => $contractUnitDetail['unit_revenue'],
                    ];

                    // Create agreement unit record
                    $createdUnit = $this->agreementUnitService->create($unitdata);
                    $agreementUnitId = $createdUnit->id ?? null;


                    // Now handle payments related to this unit
                    if (!empty($data['payment_detail'][$unit['contract_unit_details_id']])) {
                        // dd("tet");
                        foreach ($data['payment_detail'][$unit['contract_unit_details_id']] as $detail) {
                            if (empty($detail['payment_mode_id']) || empty($detail['payment_amount'])) {
                                continue;
                            }

                            $detail_data = [
                                'agreement_id' => $agreement->id,
                                'agreement_payment_id' => $payment->id,
                                'contract_unit_id' => $unit['contract_unit_details_id'],
                                'agreement_unit_id' => $agreementUnitId,
                                'payment_mode_id' => $detail['payment_mode_id'],
                                'payment_date' => $detail['payment_date'] ?? null,
                                'payment_amount' => $detail['payment_amount'],
                                'bank_id' => $detail['bank_id'] ?? null,
                                'cheque_number' => $detail['cheque_number'] ?? null,
                                'added_by' => $data['added_by'],
                                // 'unit_revenue' => $contractUnitDetail['rent_per_unit_per_annum'],
                            ];
                            // dd($detail_data);

                            $this->agreementPaymentDetailService->create($detail_data);
                            // dd("testr");
                        }
                    }

                    $this->subUnitDetailserv->allVacant(
                        $agreement->contract_id
                    );
                    // $this->subUnitDetailserv->Updatepaymentdetails(
                    //     $payment->id,
                    //     $createdUnit['contract_unit_details_id']
                    // );
                }
            } else if ($data['contract_type'] == 1 && $ct->contract_unit->business_type == 1) {
                // dd("test");
                // Default single insert (contract type != 2)
                // $rent_annum_agreement = $data['rent_per_month'] * $count;
                $rent_annum_agreement = $data['total_rent_per_annum'];

                // Reindex payment details so $index matches correctly
                $data['payment_detail'] = array_values($data['payment_detail']);

                foreach ($data['unit_detail'] as $index => $unit) {
                    $contractUnitDetail = ContractUnitDetail::find($unit['contract_unit_details_id']);
                    $subunit_ids = ContractSubunitDetail::where('contract_unit_detail_id', $unit['contract_unit_details_id'])
                        ->pluck('id')
                        ->toArray();

                    $unitdata = [
                        'agreement_id' => $agreement->id,
                        'added_by' => $data['added_by'],
                        'unit_type_id' => $unit['unit_type_id'],
                        'contract_unit_details_id' => $unit['contract_unit_details_id'],
                        'contract_subunit_details_id' => $unit['contract_subunit_details_id'] ?? null,
                        'rent_per_month' => $unit['rent_per_month'],
                        'rent_per_annum_agreement' => $rent_annum_agreement,
                        'subunit_ids' => $subunit_ids,
                        'unit_revenue' => $contractUnitDetail['unit_revenue'],
                    ];

                    $createdUnit = $this->agreementUnitService->create($unitdata);
                    $agreementUnitId = $createdUnit->id;

                    // Correct: now index matches correctly
                    $paymentDetailsForThisUnit = $data['payment_detail'][$index] ?? [];
                    // dd($paymentDetailsForThisUnit);

                    foreach ($paymentDetailsForThisUnit as $detail) {

                        // dd($detail);

                        if (empty($detail['payment_mode_id']) || empty($detail['payment_amount'])) {
                            continue;
                        }

                        $detail_data = [
                            'agreement_id' => $agreement->id,
                            'agreement_payment_id' => $payment->id,
                            'agreement_unit_id' => $agreementUnitId,
                            'payment_mode_id' => $detail['payment_mode_id'],
                            'payment_date' => $detail['payment_date'] ?? null,
                            'payment_amount' => $detail['payment_amount'],
                            'bank_id' => $detail['bank_id'] ?? null,
                            'cheque_number' => $detail['cheque_number'] ?? null,
                            'added_by' => $data['added_by'],
                        ];

                        $this->agreementPaymentDetailService->create($detail_data);
                    }

                    $this->subUnitDetailserv->markSubunitOccupied(
                        $unit['contract_unit_details_id'],
                        $unit['contract_subunit_details_id'] ?? null
                    );

                    if ($ct->contract_unit->business_type == 1) {
                        $this->subUnitDetailserv->markUnitOccupied($unit['contract_unit_details_id']);
                    }

                    // $this->subUnitDetailserv->Updatepaymentdetails(
                    //     $payment->id,
                    //     $createdUnit['contract_unit_details_id']
                    // );
                }
            } else {
                // dd("test");
                // Default single insert (contract type != 2)
                // $rent_annum_agreement = $data['rent_per_month'] * $count;
                $rent_annum_agreement = $data['total_rent_per_annum'];

                // dd($rent_annum_agreement);
                foreach ($data['unit_detail'] as $unit) {
                    $contractUnitDetail = ContractUnitDetail::find($unit['contract_unit_details_id']);
                    $subunit_ids = $unit['contract_subunit_details_id'] ?? [];
                    if (!is_array($subunit_ids)) {
                        $subunit_ids = [$subunit_ids];
                    }

                    $subunit_ids = array_map('intval', $subunit_ids);

                    // dd($unit);
                    $unitdata = [
                        'agreement_id' => $agreement->id,
                        'added_by' => $data['added_by'],
                        'unit_type_id' => $unit['unit_type_id'],
                        'contract_unit_details_id' => $unit['contract_unit_details_id'],
                        'contract_subunit_details_id' => $unit['contract_subunit_details_id'] ?? null,
                        'rent_per_month' => $unit['rent_per_month'],
                        'rent_per_annum_agreement' => $rent_annum_agreement,
                        'subunit_ids' => $subunit_ids,
                        // 'unit_revenue' => $contractUnitDetail['rent_per_unit_per_annum'],
                        'unit_revenue' => $rent_annum_agreement
                    ];

                    $createdUnit = $this->agreementUnitService->create($unitdata);
                    $agreementUnitId = $createdUnit->id ?? null;
                    // dd($data['payment_detail']);

                    foreach ($data['payment_detail'] as $detail) {
                        if (empty($detail['payment_mode_id']) || empty($detail['payment_amount'])) {
                            continue;
                        }
                        // dd($detail);

                        $detail_data = [
                            'agreement_id' => $agreement->id,
                            'agreement_payment_id' => $payment->id,
                            'agreement_unit_id' => $agreementUnitId,
                            'payment_mode_id' => $detail['payment_mode_id'],
                            'payment_date' => $detail['payment_date'] ?? null,
                            'payment_amount' => $detail['payment_amount'],
                            'bank_id' => $detail['bank_id'] ?? null,
                            'cheque_number' => $detail['cheque_number'] ?? null,
                            'added_by' => $data['added_by'],
                            'contract_unit_id' => $createdUnit['contract_unit_details_id']
                        ];

                        $this->agreementPaymentDetailService->create($detail_data);
                    }
                    $this->subUnitDetailserv->markSubunitOccupied($unit['contract_unit_details_id'], $unit['contract_subunit_details_id'] ?? null);
                    // if ($ct->contract_unit->business_type == 1) {
                    //     $this->subUnitDetailserv->markUnitOccupied($unit['contract_unit_details_id']);
                    // }
                    // $this->subUnitDetailserv->Updatepaymentdetails(
                    //     $payment->id,
                    //     $createdUnit['contract_unit_details_id']
                    // );
                }
            }

            // dd("testsub");
            $contract_id = $ct->id;

            $this->contractService->updateAgreementStatus($contract_id);


            // $this->subUnitDetailserv->markSubunitOccupied($data['contract_subunit_details_id']);
            // $contract_id = $ct->id;
            // $this->contractService->updateAgreementStatus($contract_id);




            DB::commit();

            return $agreement;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function setProjectCode($addval = 1)
    {
        $codeService = new \App\Services\CodeGeneratorService();
        return $codeService->generateNextCode('agreements', 'agreement_code', 'AGR', 5, $addval);
    }

    public function update($id, array $data)
    {
        // dd($data);
        $this->validate($data, $id);
        $data['updated_by'] = auth()->user()->id;

        DB::beginTransaction();

        try {
            // STEP 1: Update Agreement
            $agreement = $this->agreementRepository->find($id);
            // dd($agreement);

            $agreementData = [
                'company_id' => $data['company_id'] ?? $agreement->company_id,
                'contract_id' => $data['contract_id'] ?? $agreement->contract_id,
                'start_date' => $data['start_date'] ?? $agreement->start_date,
                'end_date' => $data['end_date'] ?? $agreement->end_date,
                'duration_in_months' => $data['duration_in_months'] ?? $agreement->duration_in_months,
                'updated_by' => $data['updated_by'],
            ];

            $this->agreementRepository->update($id, $agreementData);
            $documents_id = AgreementDocument::where('agreement_id', $id)->pluck('id');
            // dd($data['documents'], $documents_id);
            // dd($data['documents']);
            $this->agreementDocumentService->update($agreement, $data['documents'], $data['updated_by']);

            // STEP 3: Update Tenant Info

            $tenantData = [
                'tenant_name' => $data['tenant_name'],
                'tenant_mobile' => $data['tenant_mobile'] ?? null,
                'tenant_email' => $data['tenant_email'] ?? null,
                'nationality_id' => $data['nationality_id'] ?? null,
                'tenant_address' => $data['tenant_address'] ?? null,
                'updated_by' => $data['updated_by'],
                'id' => $data['tenant_id'],
                'contact_person' => $data['contact_person'],
                'contact_number' => $data['contact_number'],
                'contact_email' => $data['contact_email'],
                'tenant_street' => $data['tenant_street'],
                'tenant_city' => $data['tenant_city'],
                'emirate_id' => $data['emirate_id']
            ];
            $this->agreementTenantService->update($tenantData);

            $payment_data = [
                'agreement_id' => $agreement->id,
                'installment_id' => $data['installment_id'] ?? null,
                'interval' => $data['interval'] ?? null,
                'beneficiary' => $data['beneficiary'] ?? null,
                'updated_by' => $data['updated_by'],
                'total_rent_annum' => $data['total_rent_per_annum'],
                'id' => $data['payment_id']

            ];
            $payment = $this->agreementPaymentService->update($payment_data);

            // STEP 4: Update Units & Vacancies
            $ct = Contract::with('contract_rentals', 'contract_unit')
                ->where('id', $agreement->contract_id)
                ->first();
            // dd($ct);
            $count = $ct->contract_rentals->receivable_installments;
            // $rent_annum = $ct->contract_rentals->rent_receivable_per_annum;
            $rent_annum = str_replace(',', '', $ct->contract_rentals->rent_receivable_per_annum);


            // dd($data['unit_detail']);


            // Insert updated units
            if ($data['contract_type'] == 2) {
                $unitids = $this->agreementRepository->findunits($id);
                // dd($unitids);
                foreach ($data['unit_detail'] as $index => &$unit) {
                    if (isset($unitids[$index])) {
                        $unit['agreement_unit_id'] = $unitids[$index];
                    } else {
                        $unit['agreement_unit_id'] = null;
                    }
                }
                unset($unit);
                foreach ($data['unit_detail'] as $unit) {
                    // $rent_annum_agreement = $unit['rent_per_month'] * $count;
                    // $rent_annum_agreement = $rent_annum;
                    $contractUnitDetail = ContractUnitDetail::find($unit['contract_unit_details_id']);
                    $rent_annum_agreement = $contractUnitDetail->rent_per_unit_per_annum;
                    $subunit_ids = ContractSubunitDetail::where('contract_unit_detail_id', $unit['contract_unit_details_id'])
                        ->pluck('id')
                        ->toArray();
                    $unitData = [
                        'agreement_id' => $agreement->id,
                        'updated_by' => $data['updated_by'],
                        'unit_type_id' => $unit['unit_type_id'],
                        'contract_unit_details_id' => $unit['contract_unit_details_id'],
                        'contract_subunit_details_id' => $unit['contract_subunit_details_id'] ?? null,
                        'rent_per_month' => $unit['rent_per_month'],
                        'rent_per_annum_agreement' => $rent_annum_agreement,
                        'id' => $unit['agreement_unit_id'],
                        'subunit_ids' => $subunit_ids,
                        'unit_revenue' => $contractUnitDetail['unit_revenue'],
                    ];

                    $createdUnit = $this->agreementUnitService->update($unitData);


                    foreach ($data['payment_detail'][$unit['agreement_unit_id']] as $detail) {
                        if (empty($detail['payment_mode_id']) || empty($detail['payment_amount'])) continue;
                        // dd($detail);

                        $detail_data = [
                            'agreement_id' => $agreement->id,
                            'agreement_payment_id' => $payment->id,
                            'contract_unit_id' => $unitData['contract_unit_details_id'],
                            'agreement_unit_id' => $createdUnit->id,
                            'payment_mode_id' => $detail['payment_mode_id'],
                            'payment_date' => $detail['payment_date'] ?? null,
                            'payment_amount' => $detail['payment_amount'],
                            'bank_id' => $detail['bank_id'] ?? null,
                            'cheque_number' => $detail['cheque_number'] ?? null,
                            'updated_by' => $data['updated_by'],
                            'id' => $detail['detail_id']
                        ];
                        // dd($detail_data);

                        $this->agreementPaymentDetailService->update($detail_data);
                    }
                    // }
                    $this->subUnitDetailserv->markSubunitOccupied(
                        $unitData['contract_unit_details_id'],
                        $unitData['contract_subunit_details_id'] ?? null
                    );
                    // $this->subUnitDetailserv->Updatepaymentdetails(
                    //     $payment->id,
                    //     $createdUnit['contract_unit_details_id']
                    // );
                }
            } else if ($data['contract_type'] == 1 && $ct->contract_unit->business_type == 1) {
                foreach ($data['unit_detail'] as $index => $unit) {
                    // dd($unit);
                    $agUnitId = $unit['agreement_unit_id'] ?? null;
                    $contractUnitDetail = ContractUnitDetail::find($unit['contract_unit_details_id']);
                    $subunit_ids = ContractSubunitDetail::where('contract_unit_detail_id', $unit['contract_unit_details_id'])
                        ->pluck('id')
                        ->toArray();
                    $unitdata = [
                        'id' => $agUnitId,
                        'agreement_id' => $agreement->id,
                        'added_by' => $data['updated_by'],
                        'unit_type_id' => $unit['unit_type_id'],
                        'contract_unit_details_id' => $unit['contract_unit_details_id'],
                        'contract_subunit_details_id' => $unit['contract_subunit_details_id'] ?? null,
                        'rent_per_month' => $unit['rent_per_month'],
                        'rent_per_annum_agreement' => $data['total_rent_per_annum'],
                        'subunit_ids' => $subunit_ids,
                        'unit_revenue' => $contractUnitDetail['unit_revenue'],
                    ];
                    if ($agUnitId) {
                        // update
                        $exisistingUnit = $this->agreementUnitService->getById($agUnitId);
                        if ($exisistingUnit->contract_unit_details_id != $unit['contract_unit_details_id']) {
                            makeUnitVacant($exisistingUnit->contract_unit_details_id, $ct->id);
                            deleteBifurcations($exisistingUnit->contract_unit_details_id);
                        }
                        $createdUnit = $this->agreementUnitService->update($unitdata);
                    } else {
                        // create
                        $createdUnit = $this->agreementUnitService->create($unitdata);
                    }
                    $agreementUnitId = $createdUnit->id;
                    $unitId = $unit['contract_unit_details_id'];
                    $paymentDetailsForThisUnit = $data['payment_detail'][$unitId] ?? [];

                    // installment number change

                    // STEP 1: Get existing payment detail IDs from DB
                    $existingPaymentIds = $this->agreementPaymentDetailService
                        ->getByAgreementUnitId($agreementUnitId)
                        ->pluck('id')
                        ->toArray();

                    //  STEP 2: Collect new IDs from the request
                    $newPaymentIds = collect($data['payment_detail'][$unitId] ?? [])
                        ->pluck('detail_id')
                        ->filter()
                        ->toArray();

                    //  STEP 3: Find which ones to delete
                    $toDelete = array_diff($existingPaymentIds, $newPaymentIds);

                    // dd($toDelete);

                    //  Delete removed payments
                    if (!empty($toDelete)) {
                        $this->agreementPaymentDetailService->deleteByIds($toDelete);
                    }

                    foreach ($paymentDetailsForThisUnit as $detail) {
                        if (empty($detail['payment_mode_id']) || empty($detail['payment_amount'])) {
                            continue;
                        }

                        $detail_data = [
                            'agreement_id' => $agreement->id,
                            'agreement_payment_id' => $payment->id,
                            'agreement_unit_id' => $agreementUnitId,
                            'payment_mode_id' => $detail['payment_mode_id'],
                            'payment_date' => $detail['payment_date'] ?? null,
                            'payment_amount' => $detail['payment_amount'],
                            'bank_id' => $detail['bank_id'] ?? null,
                            'cheque_number' => $detail['cheque_number'] ?? null,
                            'updated_by' => $data['updated_by'],
                            'id' => $detail['detail_id'] ?? null,
                        ];
                        $this->agreementPaymentDetailService->updateOrCreate($detail_data);
                    }
                    $this->subUnitDetailserv->markSubunitOccupied(
                        $unit['contract_unit_details_id'],
                        $unit['contract_subunit_details_id'] ?? null
                    );
                    if ($ct->contract_unit->business_type == 1) {
                        $this->subUnitDetailserv->markUnitOccupied($unit['contract_unit_details_id']);
                    }
                    // $this->subUnitDetailserv->Updatepaymentdetails(
                    //     $payment->id,
                    //     $createdUnit->contract_unit_details_id
                    // );
                }
            } else {
                // Default single insert (contract type != 2)
                // $rent_annum_agreement = $data['rent_per_month'] * $count;
                $rent_annum_agreement = $data['total_rent_per_annum'];
                // dd($data['unit_detail']);

                // in the case of untnumber change
                foreach ($data['unit_detail'] as $unit) {
                    $exisistingUnit = $this->agreementUnitService->getById($unit['agreement_unit_id']);
                    if ($exisistingUnit->contract_unit_details_id != $unit['contract_unit_details_id']) {
                        ContractUnitDetail::where('id', $exisistingUnit->contract_unit_details_id)->update(['is_vacant' => 0]);
                    }
                    if ($exisistingUnit->contract_subunit_details_id != $unit['contract_subunit_details_id']) {
                        ContractSubunitDetail::where('id', $exisistingUnit->contract_subunit_details_id)->update(['is_vacant' => 0]);
                    }
                    $contractUnitDetail = ContractUnitDetail::find($unit['contract_unit_details_id']);
                    $subunit_ids = $unit['contract_subunit_details_id'] ?? [];
                    if (!is_array($subunit_ids)) {
                        $subunit_ids = [$subunit_ids];
                    }

                    $subunit_ids = array_map('intval', $subunit_ids);


                    $unitdata = [
                        'agreement_id' => $agreement->id,
                        'updated_by' => $data['updated_by'],
                        'unit_type_id' => $unit['unit_type_id'],
                        'contract_unit_details_id' => $unit['contract_unit_details_id'],
                        'contract_subunit_details_id' => $unit['contract_subunit_details_id'] ?? null,
                        'rent_per_month' => $unit['rent_per_month'],
                        'rent_per_annum_agreement' => $rent_annum_agreement,
                        'id' => $unit['agreement_unit_id'],
                        'subunit_ids' => $subunit_ids,
                        // 'unit_revenue' => $contractUnitDetail['rent_per_unit_per_annum'],
                        'unit_revenue' => $rent_annum_agreement
                    ];
                    // dd($unitdata);

                    $updatedUnit = $this->agreementUnitService->update($unitdata);
                    $agreementUnitId = $updatedUnit->id ?? null;

                    // STEP 1: Get existing payment detail IDs from DB
                    $existingPaymentIds = $this->agreementPaymentDetailService
                        ->getByAgreementId($agreement->id)
                        ->pluck('id')
                        ->toArray();

                    //  STEP 2: Collect new IDs from the request
                    $newPaymentIds = collect($data['payment_detail'] ?? [])
                        ->pluck('id')
                        ->filter()
                        ->toArray();

                    //  STEP 3: Find which ones to delete
                    $toDelete = array_diff($existingPaymentIds, $newPaymentIds);

                    // dd($toDelete);

                    //  Delete removed payments
                    if (!empty($toDelete)) {
                        $this->agreementPaymentDetailService->deleteByIds($toDelete);
                    }

                    foreach ($data['payment_detail'] ?? [] as $detail) {
                        if (empty($detail['payment_mode_id']) || empty($detail['payment_amount'])) {
                            continue;
                        }

                        $detail_data = [
                            'agreement_id' => $agreement->id,
                            'agreement_payment_id' => $payment->id,
                            'agreement_unit_id' => $agreementUnitId,
                            'payment_mode_id' => $detail['payment_mode_id'],
                            'payment_date' => $detail['payment_date'] ?? null,
                            'payment_amount' => $detail['payment_amount'],
                            'bank_id' => $detail['bank_id'] ?? null,
                            'cheque_number' => $detail['cheque_number'] ?? null,
                            'updated_by' => $data['updated_by'],
                            'id' => $detail['id'] ?? null,
                            'contract_unit_id' => $updatedUnit['contract_unit_details_id']
                        ];

                        // Update existing or create new
                        $this->agreementPaymentDetailService->updateOrCreate($detail_data);
                    }


                    $this->subUnitDetailserv->markSubunitOccupied($updatedUnit->contract_unit_details_id, $updatedUnit->contract_subunit_details_id ?? null);
                    // $this->subUnitDetailserv->Updatepaymentdetails(
                    //     $payment->id,
                    //     $updatedUnit['contract_unit_details_id']
                    // );
                }
            }


            //STEP 5: Update Contract Status if needed
            $this->contractService->updateAgreementStatus($ct->id);

            DB::commit();

            return $agreement;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }


    private function validate($data, $id = null)
    {
        $validator = Validator::make($data, [
            'company_id' => 'required',
            'contract_id' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'duration_in_months' => 'required'
        ], []);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
    public function getDataTable(array $filters = [])
    {
        $query = $this->agreementRepository->getQuery($filters);
        // dd($query);

        $columns = [
            ['data' => 'DT_RowIndex', 'name' => 'id'],
            ['data' => 'agreemant_code', 'name' => 'agreemant_code'],
            ['data' => 'company_name', 'name' => 'company_name'],
            ['data' => 'project_number', 'name' => 'project_number'],
            ['data' => 'business_type', 'name' => 'business_type'],
            ['data' => 'property_name', 'name' => 'property_name'],
            ['data' => 'tenant_details', 'name' => 'tenant_details'],
            ['data' => 'start_date', 'name' => 'start_date'],
            ['data' => 'end_date', 'name' => 'end_date'],
            ['data' => 'is_signed_agreement_uploaded', 'name' => 'is_signed_agreement_uploaded'],
            ['data' => 'agreement_status', 'name' => 'agreement_status'],
            ['data' => 'created_at', 'name' => 'created_at'],
            ['data' => 'action', 'name' => 'action', 'orderable' => true, 'searchable' => true],
        ];

        return datatables()

            ->of($query)
            ->addIndexColumn()
            ->addColumn('agreement_code', fn($row) =>  ucfirst($row->agreement_code) ?? '-')
            ->addColumn('company_name', fn($row) => $row->company->company_name ?? '-')
            // ->addColumn('project_number', fn($row) => 'P - ' . $row->contract->project_number ?? '-')
            ->addColumn('project_number', function ($row) {
                // dd($row);
                $number = 'P - ' . $row->contract->project_number ?? '-';
                $type = $row->contract_type ?? '-';


                // return "<strong class=''>{$number}</strong><p class='mb-0'><span>{$type}</span></p>
                // </p>";
                $badgeClass = '';
                if ($row->contract->contract_type_id == 1) {
                    $badgeClass = 'badge badge-df text-dark';
                } elseif ($row->contract->contract_type_id == 2) {
                    $badgeClass = 'badge badge-ff text-dark';
                } else {
                    $badgeClass = 'badge badge-secondary';
                }

                return "<strong>{$number}</strong>
                    <p class='mb-0'>
                        <span class='{$badgeClass}'>{$type}</span>
                    </p>";
            })
            ->addColumn('property_name', function ($row) {
                $unitNumbers = $row->agreement_units
                    ->map(fn($au) => optional($au->contractUnitDetail)->unit_number)
                    ->filter()
                    ->implode(', ');
                $sub = optional(
                    $row->agreement_units->first()?->contractSubunitDetail
                )->subunit_no;
                $sub =  $sub ? 'SubUnit - ' . $sub : '-';
                // ->implode(', ');
                $pt = $row->contract->property->property_name ?? '-';
                // $unit = $row->contract->property->property_name
                return "{$pt} <p class='mb-0'>
                    <span class='text-bold'>Unit(s) - {$unitNumbers}</span>
                </p>
                <p class='mb-0'>
                    <span class='text-bold'>{$sub}</span>
                </p>";
            })
            ->addColumn('tenant_details', function ($row) {
                $name = $row->tenant_name ?? '-';
                $email = $row->tenant_email ?? '-';
                $phone = $row->tenant_mobile ?? '-';

                return "<strong class='text-capitalize'>{$name}</strong><p class='mb-0 text-primary'>{$email}</p><p class='text-muted small'>
                    <i class='fa fa-phone-alt text-danger'></i> <span class='font-weight-bold'>{$phone}</span>
                </p>";
            })
            ->addColumn('project_number', function ($row) {
                // dd($row);
                $number = 'P - ' . $row->contract->project_number ?? '-';
                $type = $row->contract_type ?? '-';

                // return "<strong class=''>{$number}</strong><p class='mb-0'><span>{$type}</span></p>
                // </p>";
                $badgeClass = '';
                if ($row->contract->contract_type_id == 1) {
                    $badgeClass = 'badge badge-df text-dark';
                } elseif ($row->contract->contract_type_id == 2) {
                    $badgeClass = 'badge badge-ff text-dark';
                } else {
                    $badgeClass = 'badge badge-secondary';
                }

                return "<strong>{$number}</strong>
                <p class='mb-0'>
                    <span class='{$badgeClass}'>{$type}</span>
                </p>";
            })
            ->addColumn('business_type', function ($row) {
                if ($row->business_type == 1) {
                    $type = "B2B";
                } elseif ($row->business_type == 2) {
                    $type = "B2C";
                } else {
                    $type = "-";
                }

                return "<strong class='text-uppercase'>{$type}</strong>";
            })
            ->addColumn('start_date', fn($row) => getFormattedDate($row->start_date))
            ->addColumn('end_date', fn($row) => getFormattedDate($row->end_date))
            ->addColumn('is_signed_agreement_uploaded', fn($row) => $row->is_signed_agreement_uploaded ?? '-')
            ->addColumn('agreement_status', fn($row) => $row->agreement_status ?? '-')
            ->addColumn('created_at', fn($row) => $row->created_at ?? '-')


            ->addColumn('action', function ($row) {
                $editUrl = route('agreement.edit', $row->id);
                $printUrl = route('agreement.printview', $row->id);
                $viewUrl = route('agreement.show', $row->id);
                $docUrl = route('agreement.documents', $row->id);
                $action = '';


                if (auth()->user()->hasAnyPermission(['agreement.view'], $row->company_id)) {
                    $action .= '<a href="' . $viewUrl . '" class="btn btn-primary btn-sm m-1"
                    title="View Installments"><i class="fas fa-eye"></i></a>';
                }

                if (auth()->user()->hasAnyPermission(['agreement.document_upload'], $row->company_id)) {
                    $action .= '<a href="' . $docUrl . '" class="btn btn-warning btn-sm m-1"
                    title="documents"><i class="fas fa-file"></i></a>';
                }

                if (auth()->user()->hasAnyPermission(['agreement.agreement_view'], $row->company_id) && ($row->contract->contract_type_id == 2)) {
                    $action .= '<a href="' . $printUrl . '" class="btn btn-primary btn-sm m-1"
                    title="Agreement"><i class="fas fa-handshake"></i></a>';
                }

                if (auth()->user()->hasAnyPermission(['agreement.edit'], $row->company_id) && $row->agreement_status == 0 && !paymentStatus($row->id)) {

                    $action .= '<a href="' . $editUrl . '" class="btn btn-info  btn-sm m-1" title="Edit agreement"><i
                        class="fas fa-pencil-alt"></i></a>';
                }

                if (auth()->user()->hasAnyPermission(['agreement.delete'], $row->company_id) && !paymentStatus($row->id) && $row->agreement_status == 0) {

                    $action .= '<a class="btn btn-danger  btn-sm m-1" onclick="deleteConf(' . $row->id . ')" title="delete"><i
                        class="fas fa-trash"></i></a>';
                }

                // $action .= '<a href="#" class="btn btn-danger btn-sm m-1 open-terminate-modal" title="Terminate" id="openTerminatemodal"
                //      data-id="' . $row->id . '" ><i class="fas fa-file-signature"></i></a>
                // ';
                if (auth()->user()->hasAnyPermission(['agreement.terminate'], $row->company_id) && ($row->agreement_status == 0)) {
                    $action .= '<a href="#" class="btn btn-danger btn-sm m-1 open-terminate-modal" title="Terminate" data-id="' . $row->id . '" data-company-id="' . $row->contract->company_id . '">
                        <i class="fas fa-file-signature"></i>
                    </a>';
                }



                return $action ?: '-';
            })

            ->rawColumns(['tenant_details', 'action', 'project_number', 'business_type', 'start_date', 'end_date', 'property_name'])
            // ->rawColumns(['action'])
            ->with(['columns' => $columns])
            ->toJson();
    }
    public function getDetails($id)
    {
        return $this->agreementRepository->getDetails($id);
    }
    public function delete($id)
    {

        return $this->agreementRepository->delete($id);
    }
    public function terminate($data)
    {
        // dd($data);

        return $this->agreementRepository->terminate($data);
    }

    public function getExpired(array $filters = [])
    {
        $query = $this->agreementRepository->getExpired($filters);
        // dd($query);

        $columns = [
            ['data' => 'DT_RowIndex', 'name' => 'id'],
            ['data' => 'agreemant_code', 'name' => 'agreemant_code'],
            ['data' => 'company_name', 'name' => 'company_name'],
            ['data' => 'project_number', 'name' => 'project_number'],
            ['data' => 'business_type', 'name' => 'business_type'],
            ['data' => 'tenant_details', 'name' => 'tenant_details'],
            ['data' => 'start_date', 'name' => 'start_date'],
            ['data' => 'end_date', 'name' => 'end_date'],
            ['data' => 'is_signed_agreement_uploaded', 'name' => 'is_signed_agreement_uploaded'],
            ['data' => 'agreement_status', 'name' => 'agreement_status'],
            ['data' => 'created_at', 'name' => 'created_at'],
            ['data' => 'action', 'name' => 'action', 'orderable' => true, 'searchable' => true],
        ];

        return datatables()

            ->of($query)
            ->addIndexColumn()
            ->addColumn('agreement_code', fn($row) =>  ucfirst($row->agreement_code) ?? '-')
            ->addColumn('company_name', fn($row) => $row->company->company_name ?? '-')
            // ->addColumn('project_number', fn($row) => 'P - ' . $row->contract->project_number ?? '-')
            ->addColumn('project_number', function ($row) {
                // dd($row);
                $number = 'P - ' . $row->contract->project_number ?? '-';
                $type = $row->contract_type ?? '-';

                // return "<strong class=''>{$number}</strong><p class='mb-0'><span>{$type}</span></p>
                // </p>";
                $badgeClass = '';
                if ($row->contract->contract_type_id == 1) {
                    $badgeClass = 'badge badge-df text-dark';
                } elseif ($row->contract->contract_type_id == 2) {
                    $badgeClass = 'badge badge-ff text-dark';
                } else {
                    $badgeClass = 'badge badge-secondary';
                }

                return "<strong>{$number}</strong>
            <p class='mb-0'>
                <span class='{$badgeClass}'>{$type}</span>
            </p>";
            })
            ->addColumn('tenant_details', function ($row) {
                $name = $row->tenant_name ?? '-';
                $email = $row->tenant_email ?? '-';
                $phone = $row->tenant_mobile ?? '-';

                return "<strong class='text-capitalize'>{$name}</strong><p class='mb-0 text-primary'>{$email}</p><p class='text-muted small'>
                    <i class='fa fa-phone-alt text-danger'></i> <span class='font-weight-bold'>{$phone}</span>
                </p>";
            })
            ->addColumn('project_number', function ($row) {
                // dd($row);
                $number = 'P - ' . $row->contract->project_number ?? '-';
                $type = $row->contract_type ?? '-';

                // return "<strong class=''>{$number}</strong><p class='mb-0'><span>{$type}</span></p>
                // </p>";
                $badgeClass = '';
                if ($row->contract->contract_type_id == 1) {
                    $badgeClass = 'badge badge-df text-dark';
                } elseif ($row->contract->contract_type_id == 2) {
                    $badgeClass = 'badge badge-ff text-dark';
                } else {
                    $badgeClass = 'badge badge-secondary';
                }

                return "<strong>{$number}</strong>
            <p class='mb-0'>
                <span class='{$badgeClass}'>{$type}</span>
            </p>";
            })
            ->addColumn('business_type', function ($row) {
                if ($row->business_type == 1) {
                    $type = "B2B";
                } elseif ($row->business_type == 2) {
                    $type = "B2C";
                } else {
                    $type = "-";
                }

                return "<strong class='text-uppercase'>{$type}</strong>";
            })
            // ->addColumn('start_date', fn($row) => $row->start_date ?? '-')
            // ->addColumn('end_date', fn($row) => $row->end_date ?? '-')
            ->addColumn('is_signed_agreement_uploaded', fn($row) => $row->is_signed_agreement_uploaded ?? '-')
            ->addColumn('agreement_status', fn($row) => $row->agreement_status ?? '-')
            ->addColumn('created_at', fn($row) => $row->created_at ?? '-')

            ->addColumn('start_date', fn($row) => getFormattedDate($row->start_date))
            ->addColumn('end_date', fn($row) => getFormattedDate($row->end_date))
            ->addColumn('action', function ($row) {
                $renewUrl = route('agreement.renew', $row->id);
                $action = '';
                if (auth()->user()->hasAnyPermission(['agreement.renew'], $row->company_id)) {
                    $action .= '<a href="' . $renewUrl . '" class="btn btn-info  btn-sm m-1" title="Renew agreement"><i class="fas fa-sync-alt"></i></a>';
                    return $action ?: '-';
                }
            })

            ->rawColumns(['tenant_details', 'action', 'project_number', 'business_type', 'start_date', 'end_date'])
            // ->rawColumns(['action'])
            ->with(['columns' => $columns])
            ->toJson();
    }

    public function rentBifurcationStore($data)
    {
        return $this->agreementRepository->rentBifurcationStore($data);
    }
}
