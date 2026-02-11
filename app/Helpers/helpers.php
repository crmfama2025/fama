<?php

use App\Models\Agreement;
use App\Models\AgreementPaymentDetail;
use App\Models\AgreementSubunitRentBifurcation;
use App\Models\ClearedReceivable;
use App\Models\Contract;
use App\Models\ContractPaymentDetail;
use App\Models\ContractSubunitDetail;
use App\Models\ContractUnitDetail;
use App\Models\Installment;
use App\Models\Investment;
use App\Models\InvestmentReceivedPayment;
use App\Models\InvestmentReferral;
use App\Models\Investor;
use App\Models\InvestorPayout;
use App\Models\PaymentMode;
use App\Models\Property;
use App\Models\Vendor;
use App\Repositories\Agreement\AgreementRepository;
use App\Repositories\Contracts\ContractRepository;
use App\Repositories\Investment\InvestmentReferralRepository;
use App\Repositories\Investment\InvestmentRepository;
use App\Repositories\Investment\InvestorRepository;
use App\Services\Contracts\ContractService;
use Carbon\Carbon;

if (!function_exists('toNumeric')) {
    /**
     * Convert a mixed value (like "60,000.00" or "AED 5,500") to a clean float.
     */
    function toNumeric($value): float
    {
        if (is_null($value)) {
            return 0.0;
        }

        // Remove commas, currency symbols, and any non-numeric chars except dot and minus
        $cleaned = preg_replace('/[^\d.\-]/', '', $value);

        return (float) $cleaned;
    }
}

function dateFormatChange($value, $format)
{
    return $value ? Carbon::parse($value)->format($format) : null;
}


function subunitNoGeneration($subUnitData, $key, $i, $subunit_type)
{
    // dump($subUnitData);
    // print_r($subunit_type);
    // dd('increement room no - ' . $i);
    // if (isset($subUnitData['is_partition'][$key])) {
    if ($subunit_type == '1') {
        $subunitno = 'P' . $i;
        $subunitrent = $subUnitData['rent_per_partition'];
    }
    // }
    // if (isset($subUnitData['is_bedspace'][$key])) {
    else if ($subunit_type == '2') {
        $subunitno = 'BS' . $i;
        $subunitrent = $subUnitData['rent_per_bedspace'];
    }
    // }
    // if (isset($subUnitData['is_room'][$key])) {
    else if ($subunit_type == '3') {
        $subunitno = 'R' . $i;
        $subunitrent = $subUnitData['rent_per_room'];
    }
    // }

    else {
        $subunitno = 'FL' . $i;
        $subunitrent = $subUnitData['rent_per_flat'];
    }

    return ['subunitno' => $subunitno, 'subunitrent' => $subunitrent];
}


function subUnitCount($subUnitData, $i)
{
    // dd($subUnitData);
    $subunitcount = 0;
    if (isset($subUnitData['is_partition'][$i])) {
        if ($subUnitData['is_partition'][$i]) {
            $subunitcount += $subUnitData['partition'][$i];
        }
    }

    if (isset($subUnitData['is_bedspace'][$i])) {
        if ($subUnitData['is_bedspace'][$i]) {
            $subunitcount += $subUnitData['bedspace'][$i];
        }
    }

    if (isset($subUnitData['is_room'][$i])) {
        if ($subUnitData['is_room'][$i]) {
            $subunitcount += $subUnitData['room'][$i];
        }
    }

    if (!isset($subUnitData['is_room'][$i]) && !isset($subUnitData['is_bedspace'][$i]) && !isset($subUnitData['is_partition'][$i])) {
        $subunitcount = 1;
    }

    return $subunitcount;
}

function subUnitTypeSingle($subUnitData, $i)
{
    $subunit_type = '0';
    if (!empty($subUnitData['is_partition'][$i]) && $subUnitData['is_partition'][$i] == '1') {
        $subunit_type = 1;
    }

    if (!empty($subUnitData['is_bedspace'][$i]) && $subUnitData['is_bedspace'][$i] == '2') {
        $subunit_type = 2;
    }

    if (!empty($subUnitData['is_room'][$i]) && $subUnitData['is_room'][$i] == '3') {
        $subunit_type = 3;
    }

    if (empty($subUnitData['is_room'][$i]) && empty($subUnitData['is_bedspace'][$i]) && empty($subUnitData['is_partition'][$i])) {
        $subunit_type = 4;
    }

    // if (isset($subUnitData['is_partition'][$i])) {
    //     if ($subUnitData['is_partition'][$i] == '1') {
    //         $subunit_type = '1';
    //     } else if ($subUnitData['is_partition'][$i] == '2') {
    //         $subunit_type = '2';
    //     } else {
    //         $subunit_type = '3';
    //     }
    // } else {
    //     $subunit_type = '4';
    // }

    return $subunit_type;
}

function subUnitType($subUnitData, $i)
{
    $types = [];

    if (!empty($subUnitData['is_partition'][$i]) && $subUnitData['is_partition'][$i] == '1') {
        $types[1] = $subUnitData['partition'][$i] ?? 0;
    }

    if (!empty($subUnitData['is_bedspace'][$i]) && $subUnitData['is_bedspace'][$i] == '2') {
        $types[2] = $subUnitData['bedspace'][$i] ?? 0;
    }

    if (!empty($subUnitData['is_room'][$i]) && $subUnitData['is_room'][$i] == '3') {
        $types[3] = $subUnitData['room'][$i] ?? 0;
    }

    if (empty($subUnitData['is_room'][$i]) && empty($subUnitData['is_bedspace'][$i]) && empty($subUnitData['is_partition'][$i])) {
        $types[4] = 1;
    }

    return $types; // [type => requiredCount]
}

function getPartitionValue($dataArr, $key, $receivable_installments)
{
    $partition = 0;
    $bedspace = 0;
    $room = 0;
    $rent_per_unit_per_month = 0;
    $rent_per_unit_per_annum = 0;
    $subunittype = 0;
    $subunitcount_per_unit = 0;
    $subunit_rent_per_unit = 0;
    $total_rent_per_unit_per_month = 0;
    // dump($dataArr);
    if (array_key_exists('partition', $dataArr) && isset($dataArr['partition'][$key])) {
        // dump($dataArr['partition']);
        if ($dataArr['partition'][$key] == 1) {
            $partition = 1;
            // dd($dataArr['rent_per_partition']);
            $rent_per_unit_per_month += $dataArr['rent_per_partition'];
            $subunittype = 1;
            $subunitcount_per_unit += $dataArr['total_partition'][$key];
            $subunit_rent_per_unit += $dataArr['rent_per_partition'];
            $total_rent_per_unit_per_month += $dataArr['total_partition'][$key] * $dataArr['rent_per_partition'];
        }
    }

    if (array_key_exists('bedspace', $dataArr) && isset($dataArr['bedspace'][$key])) {
        if ($dataArr['bedspace'][$key] == 2) {
            $bedspace = 1;
            $rent_per_unit_per_month += $dataArr['rent_per_bedspace'];
            $subunittype = $subunittype ? $subunittype . ', 2' : 2;
            $subunitcount_per_unit += $dataArr['total_bedspace'][$key];
            $subunit_rent_per_unit += $dataArr['rent_per_bedspace'];
            $total_rent_per_unit_per_month += $dataArr['total_bedspace'][$key] * $dataArr['rent_per_bedspace'];
        }
    }

    if (array_key_exists('room', $dataArr) && isset($dataArr['room'][$key])) {
        if ($dataArr['room'][$key] == 3) {
            $room = 1;
            $rent_per_unit_per_month += $dataArr['rent_per_room'];
            $subunittype = $subunittype ? $subunittype . ', 3' : 3;
            $subunitcount_per_unit += $dataArr['total_room'][$key];
            $subunit_rent_per_unit += $dataArr['rent_per_room'];
            $total_rent_per_unit_per_month += $dataArr['total_room'][$key] * $dataArr['rent_per_room'];
        }
    }

    if (!isset($dataArr['partition'][$key]) && !isset($dataArr['bedspace'][$key]) && !isset($dataArr['room'][$key])) {
        $rent_per_unit_per_month = $dataArr['rent_per_flat'];
        $subunittype = 4;
        $subunitcount_per_unit = 1;
        $subunit_rent_per_unit = $dataArr['rent_per_flat'];
        $total_rent_per_unit_per_month  = $dataArr['rent_per_flat'];
    }

    // dump($bedspace);
    // dump($room);
    $rent_per_flat = $dataArr['rent_per_flat'];
    $installment = Installment::find($receivable_installments);

    if ($installment->installment_name == '14') {
        $installment = '13';
    } else {
        $installment = $installment->installment_name;
    }

    if (isset($dataArr['unit_profit'])) {
        // print('profit');
        $rent_per_flat = $dataArr['unit_revenue'][$key] / $installment;
        $rent_per_unit_per_month = $rent_per_flat;
        $subunit_rent_per_unit = $rent_per_flat;
        $total_rent_per_unit_per_month  = $rent_per_flat;
    }
    // print('rent_per_unit_per_month - ' . $rent_per_unit_per_month);
    // print('subunit_rent_per_unit - ' . $subunit_rent_per_unit);
    // print('total_rent_per_unit_per_month - ' . $total_rent_per_unit_per_month);
    $rent_per_unit_per_annum = $rent_per_unit_per_month * $installment;

    $total_rent_per_unit_per_annum = $total_rent_per_unit_per_month * $installment;

    $retData = array(
        'partition' => $partition,
        'bedspace' => $bedspace,
        'room' => $room,
        'rent_per_flat' => $rent_per_flat,
        'rent_per_unit_per_month' => $rent_per_unit_per_month,
        'rent_per_unit_per_annum' => $rent_per_unit_per_annum,
        'subunittype' => $subunittype,
        'subunitcount_per_unit' => $subunitcount_per_unit,
        'subunit_rent_per_unit' => $subunit_rent_per_unit,
        'total_rent_per_unit_per_month' => $total_rent_per_unit_per_month,
        'total_rent_per_unit_per_annum' => $total_rent_per_unit_per_annum
    );
    // dd($retData);
    return $retData;
}

function subunittypeName($subunittype)
{
    $types = array_unique(explode(', ', $subunittype));

    $subunit_name = '';
    foreach ($types as $key => $type) {
        if ($type == 1) {
            $name = 'PARTITION';
        } else if ($type == 2) {
            $name = 'BEDSPACE';
        } else if ($type == 3) {
            $name = 'ROOM';
        } else {
            $name = 'FULL FLAT';
        }

        $subunit_name = ($subunit_name) ? $subunit_name . ', ' . $name : $name;
    }

    return $subunit_name;
}

function subunittypeCount($unitDetails)
{
    $types = explode(',', $unitDetails->subunittype);

    $subunitCount = '';
    $unitrent = '';
    foreach ($types as $key => $type) {
        if ($type == 1) {
            $unitCount = $unitDetails->total_partition;
            $rent = $unitDetails->rent_per_partition;
        } else if ($type == 2) {
            $unitCount = $unitDetails->total_bedspace;
            $rent = $unitDetails->rent_per_bedspace;
        } else if ($type == 3) {
            $unitCount = $unitDetails->total_room;
            $rent = $unitDetails->rent_per_room;
        } else {
            $unitCount = 1;
            $rent = $unitDetails->rent_per_flat;
        }

        $subunitCount = ($subunitCount) ? $subunitCount . ', ' . $unitCount : $unitCount;
        $unitrent = ($unitrent) ? $unitrent . ' - ' . 'AED ' . $rent : 'AED ' . $rent;
    }

    return ['subunitCount' => $subunitCount, 'unitrent' => $unitrent];
}


// need to change
function getAccommodationDetails($unitDetails)
{
    // // dd($unitDetails);
    // $accocmmodation = $title = $price_title = '';
    // $total_price = $price = 0;

    // if ($unitDetails->partition != null) {
    //     $title = 'Partition';
    //     $price_title = 'Per Partiton';
    //     $accocmmodation = $unitDetails->total_partition;
    //     $price = $unitDetails->rent_per_partition;
    // }
    // if ($unitDetails->bedspace != null) {
    //     $title = 'Bedspace';
    //     $price_title = 'Per Bedspace';
    //     $accocmmodation = $unitDetails->total_bedspace;
    //     $price = $unitDetails->rent_per_bedspace;
    // }
    // if ($unitDetails->room != null) {
    //     $title = 'Room';
    //     $price_title = 'Per Room';
    //     $accocmmodation = $unitDetails->total_room;
    //     $price = $unitDetails->rent_per_room;
    // }

    // if ($unitDetails->partition == null && $unitDetails->bedspace == null && $unitDetails->room == null) {
    //     $title = 'Full Flat';
    //     $price_title = 'Per Flat';
    //     $accocmmodation = 1;
    //     $price = $unitDetails->rent_per_flat;
    // }

    // $total_price = $unitDetails->total_rent_per_unit_per_month;

    // $return = array(
    //     'title' => $title,
    //     'price_title' => $price_title,
    //     'accommodation' => $accocmmodation,
    //     'price' => $price,
    //     'total_price' => $total_price
    // );

    // return $return;

    $rows = [];

    if (!empty($unitDetails->total_room)) {
        $rows[] = [
            'type'  => 'Room',
            'count' => $unitDetails->total_room,
            'rent'  => tonumeric($unitDetails->rent_per_room),
        ];
    }

    if (!empty($unitDetails->total_partition)) {
        $rows[] = [
            'type'  => 'Partition',
            'count' => $unitDetails->total_partition,
            'rent'  => tonumeric($unitDetails->rent_per_partition),
        ];
    }

    if (!empty($unitDetails->total_bedspace)) {
        $rows[] = [
            'type'  => 'BS',
            'count' => $unitDetails->total_bedspace,
            'rent'  => tonumeric($unitDetails->rent_per_bedspace),
        ];
    }

    if (empty($unitDetails->total_bedspace) && empty($unitDetails->total_room) && empty($unitDetails->total_partition)) {
        $rows[] = [
            'type'  => 'FL',
            'count' => 1,
            'rent'  => tonumeric($unitDetails->rent_per_flat),
        ];
    }


    return [
        'subunits'    => $rows,
        'total_price' => $unitDetails->total_rent_per_unit_per_month,
    ];
}

function formatNumber($number)
{
    return number_format(toNumeric($number), 2, '.', ',');
}
function getOccupiedDetails($unitId)
{
    $totalSubunits = ContractSubunitDetail::where('contract_unit_detail_id', $unitId)->count();
    $OccupiedSubunitCount = ContractSubunitDetail::where('contract_unit_detail_id', $unitId)
        ->where('is_vacant', 1)
        ->count();

    $VacantSubunitCount = $totalSubunits - $OccupiedSubunitCount;
    return [
        'occupied' => $OccupiedSubunitCount,
        'vacant' => $VacantSubunitCount,
        'totalsubunits' => $totalSubunits
    ];
}
function getPaymentDetails($paymentId, $unitId)
{
    $payment_details = AgreementPaymentDetail::where('agreement_payment_id', $paymentId)
        ->where('contract_unit_id', $unitId)
        ->where('terminate_status', 0);
    // $totalPaidAmount = 0;
    // foreach ($payment_details as $detail) {
    //     $totalPaidAmount += $payment_details->clearedReceivables->paid_amount;
    // }
    $totalPaidAmount = $payment_details->sum('paid_amount');
    $totalPaymentAmount = $payment_details->sum('payment_amount');
    $pendingAmount = $totalPaymentAmount - $totalPaidAmount;
    return [
        'received' => $totalPaidAmount,
        'pending' => $pendingAmount,
        'total' => $totalPaymentAmount
    ];
}
function makeUnitVacant($unitId, $contract_id)
{
    $unit = ContractUnitDetail::findOrFail($unitId);
    $unit->update([
        'is_vacant' => 0,
        'subunit_occupied_count' => 0,
        'subunit_vacant_count' => $unit->subunitcount_per_unit,
    ]);
    $subunit = ContractSubunitDetail::where('contract_unit_detail_id', $unitId)->get();
    foreach ($subunit as $sub) {
        $sub->is_vacant = 0;
        $sub->save();
    }
}

function getVacantUnits($id)
{
    $unit_count = ContractUnitDetail::where('contract_id', $id)->where('is_vacant', 0)->count();
    return $unit_count;
}

function getOccupiedUnits($id)
{
    $contract = Contract::find($id);

    $occupied = $vacant = $paymentReceived = $payamentPending = 0;
    foreach ($contract->contract_unit_details as $contractUnitdetail) {
        $occupied += $contractUnitdetail->subunit_occupied_count;
        $vacant += $contractUnitdetail->subunit_vacant_count;
        $paymentReceived += $contractUnitdetail->total_payment_received;
        $payamentPending += $contractUnitdetail->total_payment_pending;
    }

    return [
        'occupied' => $occupied,
        'vacant' => $vacant,
        'paymentReceived' => $paymentReceived,
        'payamentPending' => $payamentPending
    ];
}
// function paymentStatus($agreementid)
// {
//     $paid = AgreementPaymentDetail::where('agreement_id', $agreementid)
//         ->where('terminate_status', 0)
//         ->SUM('paid_amount');
//     // dd($paid);

//     return $paid;
// }
function paymentStatus($agreementid)
{
    $payment = AgreementPaymentDetail::where('agreement_id', $agreementid)
        ->where('terminate_status', 0)
        ->first();

    if ($payment && in_array($payment->is_payment_received, [0])) {
        return true;
    }

    return false;
}
function makeContractAvailable($contract_id)
{
    $contract = Contract::find($contract_id);
    $contract->is_agreement_added = 0;
    $hasagreement = Agreement::where('contract_id', $contract_id)->exists();
    if (!$hasagreement) {
        $contract->has_agreement = 0;
    }
    $contract->save();
}

function contractStatusUpdate($status, $contract_id)
{
    $data = ['contract_status' => $status];
    $contract = Contract::find($contract_id);
    $contract->update($data);

    // dump($data);
}

function renewalCount()
{
    $service = app(ContractService::class);  // resolve service
    return $service->getRenewalDataCount();
}

function contractStatusName($contract_status)
{
    if ($contract_status == 0) return 'Pending';
    elseif ($contract_status == 1) return 'Processing';
    elseif ($contract_status == 2) return 'Approved';
    elseif ($contract_status == 3) return 'Rejected';
    elseif ($contract_status == 4) return 'Approval Pending';
    elseif ($contract_status == 5) return 'Approval on Hold';
    elseif ($contract_status == 6) return 'Partially Signed';
    elseif ($contract_status == 7) return 'Fully Signed';
    elseif ($contract_status == 8) return 'Expired';
    elseif ($contract_status == 9) return 'Terminated';
}

function contractStatusClass($contract_status)
{
    if ($contract_status == 0) return 'badge badge-warning text-black';
    elseif ($contract_status == 1) return 'badge badge-info text-white';
    elseif ($contract_status == 2) return 'badge badge-success text-white';
    elseif ($contract_status == 3) return 'badge badge-danger text-white';
    elseif ($contract_status == 4) return 'badge badge-df text-white';
    elseif ($contract_status == 5) return 'badge badge-secondary text-white';
    elseif ($contract_status == 6) return 'badge bg-gradient-maroon text-white';
    elseif ($contract_status == 7) return 'badge bg-gradient-lightblue text-white';
    elseif ($contract_status == 8) return 'badge badge-dark text-white';
    elseif ($contract_status == 9) return 'badge badge-danger text-white';
}

function statusCount($status)
{
    $repository = app(ContractRepository::class);

    $filter = [
        'search' => contractStatusName($status)
    ];
    return $repository->getQuery($filter)->count();
}
function getAgreementExpiringCounts()
{
    // $today = Carbon::today();
    // $oneMonthLater = Carbon::today()->addMonth(1)->format('Y-m-d');


    // $expiredCount = Agreement::where('agreement_status', [0, 2])
    //     ->where('end_date', '<=', $oneMonthLater)
    //     ->count();
    // dd($expiredCount);
    $repo = app(AgreementRepository::class);
    $expired = $repo->getExpired();
    $expiredCount = $expired->count();
    // dd($expiredCount);

    return $expiredCount;
}

function getVendorsHaveContract()
{
    $vendors = Vendor::where('status', 1)
        ->whereHas('contracts')
        ->get();

    return $vendors;
}

function getPropertiesHaveContract()
{
    $properties = Property::where('status', 1)
        ->whereHas('contracts')
        ->get();

    return $properties;
}

function getPaymentModeHaveContract()
{
    $paymentmodes = PaymentMode::where('status', 1)
        ->whereHas('paymentDetails')
        ->get();

    return $paymentmodes;
}

function totalPaidPayable($payables)
{
    $paid = 0;
    foreach ($payables as $payable) {
        $paid += $payable->paid_amount;
    }

    return $paid;
}

function getComposition($contractId, $detailId)
{
    $details = ContractPaymentDetail::where('contract_id', $contractId)->orderBy('id')->get();

    $totalInstallments = $details->count();
    $currentInstallmentIndex = $details->pluck('id')->search($detailId) + 1;

    return $currentInstallmentIndex . '/' . $totalInstallments;
}

function getUnitshaveAgreement()
{
    $units = ContractUnitDetail::with('contract')->whereHas('agreementUnits')
        ->get();

    return $units;
}
function getPaymentModeHaveAgreement()
{
    $paymentmodes = PaymentMode::where('status', 1)
        ->whereHas('agreementPaymentDetails')
        ->get();

    return $paymentmodes;
}
function checkAgreementPayment($agreement_payment_id)
{
    $total = AgreementPaymentDetail::where('agreement_payment_id', $agreement_payment_id)
        ->where('terminate_status', 0)
        ->count();

    $received = AgreementPaymentDetail::where('agreement_payment_id', $agreement_payment_id)
        ->where('terminate_status', 0)
        ->where('is_payment_received', 1)
        ->count();
    if ($total === $received) {
        return true;
    } else {
        return false;
    }
}
function getReceivableAmount($agreement_payment_detail_id)
{
    // dd("test");
    $received_amount = ClearedReceivable::where('agreement_payment_details_id', $agreement_payment_detail_id)->sum('paid_amount');
    $receivable = AgreementPaymentDetail::where('id', $agreement_payment_detail_id)->first();

    $receivable_amount = $receivable?->payment_amount;
    $balance_to_pay = $receivable_amount - $received_amount;
    return $balance_to_pay;
}
function updateContractUnitPayments($contract_unit_details_id, $paid_amount)
{
    // dd($paid_amount);
    $contract_unit_detail = ContractUnitDetail::find($contract_unit_details_id);
    // dd($contract_unit_detail);

    if (!$contract_unit_detail) {
        return false;
    }

    $contract_unit_detail->total_payment_received += $paid_amount;
    $contract_unit_detail->total_payment_pending -= $paid_amount;
    $contract_unit_detail->save();

    return $contract_unit_detail;
}
function investmentStatus($invest_amount, $received_amount)
{
    if ($invest_amount == $received_amount) {
        return 1;
    } elseif ($invest_amount > $received_amount) {
        return 2;
    } else {
        return 0;
    }
}
function InvestmentTypestatus($investor_id)
{
    return Investment::where('investor_id', $investor_id)->exists() ? 1 : 0;
}
function updateInvestor($investorId, $investmentId)
{
    $investmentCount = Investment::where('investor_id', $investorId)->count();

    $investedAmount = Investment::where('investor_id', $investorId)->sum('investment_amount');


    Investor::where('id', $investorId)->update([
        'total_no_of_investments' => $investmentCount,
        'total_invested_amount' => $investedAmount,
        'status' => 1
    ]);
}
function updateReferralCommission($referrorId)
{
    $investor = Investor::find($referrorId);
    $totalPending = InvestmentReferral::where('investor_referror_id', $referrorId)
        ->sum('referral_commission_amount');

    $investor->total_referal_commission = $totalPending;
    $investor->save();

    return $investor;
}
function parseDate($date)
{
    if (!$date) return null;
    return Carbon::parse($date)->format('Y-m-d');
}
function paymentFullyReceived($investmentId)
{
    $investment = Investment::with('investmentReceivedPayments')->find($investmentId);

    if (!$investment) {
        return false;
    }

    $investmentAmount = (float) $investment->investment_amount;

    // Sum only received payments
    $totalReceived = (float) $investment->investmentReceivedPayments()
        ->where('status', 1)
        ->sum('received_amount');

    if ($investmentAmount == $totalReceived) {
        return true;
    } else {
        return false;
    }
}
function getFormattedDate($date)
{
    if (!$date) return null;
    return Carbon::parse($date)->format('d-m-Y');
}
function updateInvestmentBalance($investmentId)
{
    $investment = Investment::find($investmentId);

    $totalReceived = InvestmentReceivedPayment::where('investment_id', $investmentId)
        ->sum('received_amount');

    $balanceAmount = $investment->investment_amount - $totalReceived;


    $investment->balance_amount = $balanceAmount;
    $investment->total_received_amount = $totalReceived;
    if ($totalReceived == $investment->investment_amount) {
        $investment->has_fully_received = 1;
    }
    $investment->save();

    return $investment;
}
function calculateNextProfitReleaseDate($grace_period, $profit_interval_id, $investment_date, $batch_name)
{
    $date = Carbon::parse($investment_date)
        ->addDays($grace_period);

    // Extract batch start day from range (e.g. "11-20" → 11)
    [$batchStartDay] = explode('-', $batch_name);
    $batchStartDay = (int) $batchStartDay;

    switch ($profit_interval_id) {
        case 1: // Monthly
            $date->addMonth();
            break;
        case 2: // Quarterly
            $date->addMonths(3);
            break;
        case 3: // Half-Yearly
            $date->addMonths(6);
            break;
        case 4: // Yearly
            $date->addYear();
            break;
        case 5: // Every 2 months
            $date->addMonths(2);
            break;
    }

    // Set date to batch first day (avoid invalid dates like Feb 30)
    $date->day = min($batchStartDay, $date->daysInMonth);

    return $date->format('Y-m-d');
}

function currMonthProfit($colname, $investor_id)
{
    $investment = Investment::where('investor_id', $investor_id)
        ->selectRaw('
        SUM(' . $colname . ') as total
    ')
        ->first();

    return $investment->total;
}


function getPayoutDate($row)
{
    return match ($row->payout_type) {
        1 => optional($row->investment)->next_profit_release_date,
        2 => optional($row->investment)->next_referral_commission_release_date,
        3 => optional($row->investment)->termination_date,
        4 => optional($row->investment)->termination_date,
        default => null,
    };
}

function getPayoutTypeLabel(int $type): string
{
    return match ($type) {
        1 => 'Profit',
        2 => 'Referral Commission',
        3 => 'Principal',
        default => 'Unknown',
    };
}


function calculateNextReferralReleaseDate($referral_commission_frequency_id, $last_released_date)
{
    $investmentDate = Carbon::parse($last_released_date);

    $nextProfitReleaseDate = null;
    switch ($referral_commission_frequency_id) {
        case 1: // single
            $nextProfitReleaseDate = $investmentDate->copy()->addMonth();
            break;
        // case 2: // twice
        //     $nextProfitReleaseDate = $investmentDate->copy()->addMonths(6);
        //     break;
        case 2: // multiple
            $nextProfitReleaseDate = $investmentDate->copy()->addMonths(2);
            break;
    }

    return $nextProfitReleaseDate->format('Y-m-d');
}
function  next_referralcomm_date($referral, $last_released_date, $payment_terms_id, $payoutdata)
{
    $investmentDate = Carbon::parse($last_released_date);

    $nextProfitReleaseDate = null;

    if ($referral->referral_commission_frequency_id == 1) {
        if ($payment_terms_id == 1) {
            if ($payoutdata->is_processed == 1) {
                $nextProfitReleaseDate = null;
            }
        } elseif ($payment_terms_id == 2) {
            if ($payoutdata->is_processed == 1) {
                $nextProfitReleaseDate = null;
            } elseif ($payoutdata->is_processed == 2) {
                $nextProfitReleaseDate = $investmentDate->copy()->addMonth();
            }
        } elseif ($payment_terms_id == 3) {
            if ($payoutdata->is_processed == 1) {
                $nextProfitReleaseDate = null;
            }
        } elseif ($payment_terms_id == 4) {
            if ($payoutdata->is_processed == 1) {
                $nextProfitReleaseDate = null;
            } elseif ($payoutdata->is_processed == 2) {
                $nextProfitReleaseDate = $investmentDate->copy()->addMonths(2);
            }
        } elseif ($payment_terms_id == 5) {
            if ($payoutdata->is_processed == 1) {
                $nextProfitReleaseDate = null;
            } elseif ($payoutdata->is_processed == 2) {
                $nextProfitReleaseDate = $investmentDate->copy()->addMonths(6);
            }
        }
    } elseif ($referral->referral_commission_frequency_id == 2) {
        if ($payment_terms_id == 1) {
            $nextProfitReleaseDate = $investmentDate->copy()->addMonths(12);
        } elseif ($payment_terms_id == 2) {
            $nextProfitReleaseDate = $investmentDate->copy()->addMonth();
        } elseif ($payment_terms_id == 3) {
            $nextProfitReleaseDate = $investmentDate->copy()->addMonths(12);
        } elseif ($payment_terms_id == 4) {
            $nextProfitReleaseDate = $investmentDate->copy()->addMonths(2);
        } elseif ($payment_terms_id == 5) {
            $nextProfitReleaseDate = $investmentDate->copy()->addMonths(6);
        }
    }
    if ($nextProfitReleaseDate) {
        return $nextProfitReleaseDate->format('Y-m-d');
    } else {
        return $nextProfitReleaseDate;
    }
}


// payout distribution updates
function  updateInvestmentOnDistribution($payoutData, $distributedData)
{
    $repository = app(InvestmentRepository::class);
    $investmentId = $payoutData->investment_id;


    $investment = $repository->find($investmentId);

    $nextCommDate = Carbon::parse($investment->next_referral_commission_release_date)->format('Y-m-d');
    $nextProfitRelDate = Carbon::parse($investment->next_profit_release_date)->format('Y-m-d');
    $lastProfitReleased = Carbon::parse($investment->last_profit_released_date)->format('Y-m-d');

    $profitReleased = $principalReleased = 0;

    if ($payoutData->payout_type == 2) {
        // $nextCommDate = calculateNextReferralReleaseDate($investment->investmentReferral->referral_commission_frequency_id, $distributedData->paid_date);
        $nextCommDate = next_referralcomm_date($investment->investmentReferral, $distributedData->paid_date, $investment->investmentReferral->payment_terms_id, $payoutData);
    } elseif ($payoutData->payout_type == 3) {
        $principalReleased = toNumeric($distributedData->amount_paid);
    } else {
        if ($payoutData->amount_pending != 0) {
            $nextProfitRelDate = calculateNextProfitReleaseDate(0, $investment->profit_interval_id, $distributedData->paid_date, $investment->payoutBatch->batch_name);
        }
        $lastProfitReleased = Carbon::parse($distributedData->paid_date)->format('Y-m-d');
        $profitReleased = toNumeric($distributedData->amount_paid);
    }

    $investmentArr = array(
        'total_profit_released' => toNumeric($investment->total_profit_released) + $profitReleased,
        'current_month_released' => getcurrMonthRelease(date('Y-m'), $investmentId, 1),
        'outstanding_profit' => getOutstangingInvestmentProfit($investmentId, $payoutData->payout_type),
        'last_profit_released_date' => $lastProfitReleased,
        'next_profit_release_date' => $nextProfitRelDate,
        'next_referral_commission_release_date' => $nextCommDate,
        'updated_by' => auth()->user()->id,
    );

    if ($payoutData->amount_pending == 0) {
        terminateStatusChange($investmentId);
    }

    return $repository->update($investmentId, $investmentArr);
}


function refCommUpdateOnDistribution($payoutData, $distributedData)
{
    $repository = app(InvestmentReferralRepository::class);
    $referralId = $payoutData->payout_reference_id;


    $refComm = $repository->find($referralId);

    $bal = toNumeric($refComm->referral_commission_amount) - toNumeric($payoutData->amount_paid);
    $totCommRel = toNumeric($refComm->total_commission_released) + toNumeric($distributedData->amount_paid);

    $refCommArr = array(
        // 'referral_commission_status' => ($bal > 0) ? 2 : 1,
        'referral_commission_status' => referral_commission_status($bal, $refComm),
        'last_referral_commission_released_date' => Carbon::parse($distributedData->paid_date)->format('Y-m-d'),
        'total_commission_pending' => $bal,
        'total_commission_released' => $totCommRel,
        'current_month_commission_released' => getcurrMonthRelease(date('Y-m'), $payoutData->investment_id, 2),
        'commission_released_perc' => $totCommRel / $refComm->referral_commission_amount,
        'updated_by' => auth()->user()->id,
    );

    return $repository->update($referralId, $refCommArr);
}
function referral_commission_status($bal, $refComm)
{
    if ($bal == 0) {
        if ($refComm->referral_commission_frequency_id == 1) {
            return 1;
        } elseif ($refComm->referral_commission_frequency_id == 2) {
            return 2;
        }
    } elseif ($bal > 0) {
        return 2;
    }
}

function investorUpdateOnDistribution($payoutData, $distributedData)
{
    $repository = app(InvestorRepository::class);
    $investorId = $payoutData->investor_id;

    $investor = $repository->find($payoutData->investor_id);

    $investorArr = array(
        'total_profit_received' => getcurrMonthRelease(null, null, 1, $investorId),
        'total_referral_commission_received' => getcurrMonthRelease(null, null, 2, $investorId),
        'total_principal_received' => getcurrMonthRelease(null, null, 3, $investorId),
        'updated_by' => auth()->user()->id,
    );

    if ($payoutData->amount_pending == 0) {
        terminateInvestorUpdates($investorId);
    }
    // dd($investorArr);
    return $repository->update($payoutData->investor_id, $investorArr);
}


function getcurrMonthRelease($month = null, $investmentId = null, $type = null, $investorId = null)
{
    if ($investorId) {
        $cond = array(
            'investor_id' => $investorId,
            'payout_type' => $type
        );
    } else {
        $cond = array(
            'payout_release_month' => $month,
            'investment_id' => $investmentId,
            'payout_type' => $type
        );
    }


    $payouts = InvestorPayout::where($cond)->get();

    $release = 0;
    foreach ($payouts as $payout) {
        $release += $payout->amount_paid;
    }

    return $release;
}

function getOutstangingInvestmentProfit($investmentId, $payoutType)
{
    $payouts = InvestorPayout::where([
        'investment_id' => $investmentId,
        'is_processed' => 0,
        'payout_type' => 1
    ])->get();

    $balance = 0;
    foreach ($payouts as $payout) {
        $balance += $payout->amount_pending;
    }

    return $balance;
}

function terminateStatusChange($investmentId)
{
    $data = [
        'investment_status' => 0,
        'terminate_status' => 2
    ];
    $inv = Investment::find($investmentId);
    $inv->update($data);
}

function terminateInvestorUpdates($investorid)
{
    $repository = app(InvestmentRepository::class);
    $activeInvestments = $repository->getActiveInvestmentByInvestment($investorid);

    if ($activeInvestments->count() == 0) {
        $data['status'] = 0;
    }

    $inv = Investor::find($investorid);
    $data['total_terminated_investments'] = $inv->total_terminated_investments + 1;
    $inv->update($data);
}

function paymentModeCount($details): string
{
    $modes = $details
        ->groupBy(fn($item) => $item->payment_mode->payment_mode_name)
        ->map(fn($items) => $items->count());

    return collect($modes)->map(function ($count, $mode) {

        $countText = $count === 1 ? 'One' : $count;
        $modeText  = strtoupper($mode === 'Cheque' ? 'PDC' : $mode);

        if ($count > 1) {
            if ($modeText === 'PDC') {
                $modeText = "PDC's";   // 👈 required format
            } else {
                $modeText = Str::plural($modeText);
            }
        }

        return "{$countText} {$modeText}";
    })->implode(' & ');
}

function formatUnitTypes(string $text): string
{
    return collect(explode(',', $text))
        ->map(function ($item) {
            // Match: number (TYPE)
            preg_match('/(\d+)\s*\(([^)]+)\)/', trim($item), $matches);

            $count = $matches[1];              // 1
            $type  = $matches[2]; // 1bhk, 2bhk

            return "{$type}({$count})";
        })
        ->implode(',');
}


function userAddedCount($model = null, $userId = null)
{
    if (!$model || !class_exists($model)) {
        return 0;
    }

    $query = $model::query();

    if ($userId != null) {
        $query->where('added_by', $userId);
    }

    return $query->count();
}
function format_k($number)
{
    if ($number >= 1000000000) {
        return rtrim(rtrim(number_format($number / 1000000000, 1), '0'), '.') . 'B';
    }

    if ($number >= 1000000) {
        return rtrim(rtrim(number_format($number / 1000000, 1), '0'), '.') . 'M';
    }

    if ($number >= 1000) {
        return rtrim(rtrim(number_format($number / 1000, 1), '0'), '.') . 'K';
    }

    return number_format($number);
}
function deleteBifurcations($contract_unit_details_id)
{
    $bifurcations = AgreementSubunitRentBifurcation::where('contract_unit_details_id', $contract_unit_details_id)->get();

    foreach ($bifurcations as $bifurcation) {
        $bifurcation->delete();
    }
}
function updateContractUnitReceivablePayback($contract_unit_details_id, $paid_amount)
{
    // dd($paid_amount);
    $contract_unit_detail = ContractUnitDetail::find($contract_unit_details_id);
    // dd($contract_unit_detail);

    if (!$contract_unit_detail) {
        return false;
    }

    $contract_unit_detail->total_payment_received -= $paid_amount;
    $contract_unit_detail->total_payment_pending += $paid_amount;
    // dd();
    $contract_unit_detail->save();

    return $contract_unit_detail;
}
