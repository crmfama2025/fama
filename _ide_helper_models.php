<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property int|null $user_id
 * @property string $module
 * @property int|null $record_id
 * @property string $action
 * @property string $description
 * @property array|null $changes
 * @property string $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog whereChanges($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog whereModule($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog whereRecordId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog whereUserId($value)
 */
	class ActivityLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $agreement_code
 * @property int $contract_id
 * @property int $company_id
 * @property string $start_date
 * @property string $end_date
 * @property int $duration_in_months
 * @property int|null $duration_in_days
 * @property int $is_emirates_id_uploaded
 * @property int $is_passport_uploaded
 * @property int $is_visa_uploaded
 * @property int $is_signed_agreement_uploaded
 * @property int $is_trade_license_uploaded
 * @property int $agreement_status 0-Pending, 1-terminated
 * @property string|null $terminated_date
 * @property string|null $terminated_reason
 * @property int|null $terminated_by
 * @property int $added_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AgreementStatusLogs> $agreementStatusLogs
 * @property-read int|null $agreement_status_logs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AgreementSubunitRentBifurcation> $agreementSubunitRentBifurcations
 * @property-read int|null $agreement_subunit_rent_bifurcations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AgreementDocument> $agreement_documents
 * @property-read int|null $agreement_documents_count
 * @property-read \App\Models\AgreementPayment|null $agreement_payment
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AgreementPaymentDetail> $agreement_payment_details
 * @property-read int|null $agreement_payment_details_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AgreementUnit> $agreement_units
 * @property-read int|null $agreement_units_count
 * @property-read \App\Models\Company|null $company
 * @property-read \App\Models\Contract|null $contract
 * @property-read \App\Models\User|null $deletedBy
 * @property-read \App\Models\AgreementTenant|null $tenant
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AgreementUnit> $tenant_invoices
 * @property-read int|null $tenant_invoices_count
 * @method static \Illuminate\Database\Eloquent\Builder|Agreement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Agreement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Agreement onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Agreement query()
 * @method static \Illuminate\Database\Eloquent\Builder|Agreement whereAddedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agreement whereAgreementCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agreement whereAgreementStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agreement whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agreement whereContractId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agreement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agreement whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agreement whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agreement whereDurationInDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agreement whereDurationInMonths($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agreement whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agreement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agreement whereIsEmiratesIdUploaded($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agreement whereIsPassportUploaded($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agreement whereIsSignedAgreementUploaded($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agreement whereIsTradeLicenseUploaded($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agreement whereIsVisaUploaded($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agreement whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agreement whereTerminatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agreement whereTerminatedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agreement whereTerminatedReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agreement whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agreement whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agreement withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Agreement withoutTrashed()
 */
	class Agreement extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $agreement_id
 * @property int $document_type
 * @property string $document_number
 * @property string $original_document_path
 * @property string $original_document_name
 * @property int $added_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $issued_date
 * @property string|null $expiry_date
 * @property-read \App\Models\TenantIdentity|null $TenantIdentity
 * @property-read \App\Models\Agreement $agreement
 * @property-read \App\Models\User|null $deletedBy
 * @property-read mixed $document_url
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementDocument newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementDocument newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementDocument onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementDocument query()
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementDocument whereAddedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementDocument whereAgreementId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementDocument whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementDocument whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementDocument whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementDocument whereDocumentNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementDocument whereDocumentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementDocument whereExpiryDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementDocument whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementDocument whereIssuedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementDocument whereOriginalDocumentName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementDocument whereOriginalDocumentPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementDocument whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementDocument whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementDocument withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementDocument withoutTrashed()
 */
	class AgreementDocument extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $agreement_id
 * @property int $installment_id
 * @property int $interval
 * @property string $beneficiary
 * @property string $total_rent_annum
 * @property int $added_by
 * @property int $has_payment_fully_received
 * @property int $has_payment_received
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Agreement $agreement
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AgreementPaymentDetail> $agreementPaymentDetails
 * @property-read int|null $agreement_payment_details_count
 * @property-read \App\Models\User|null $deletedBy
 * @property-read \App\Models\Installment $installment
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementPayment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementPayment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementPayment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementPayment query()
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementPayment whereAddedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementPayment whereAgreementId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementPayment whereBeneficiary($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementPayment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementPayment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementPayment whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementPayment whereHasPaymentFullyReceived($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementPayment whereHasPaymentReceived($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementPayment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementPayment whereInstallmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementPayment whereInterval($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementPayment whereTotalRentAnnum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementPayment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementPayment whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementPayment withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementPayment withoutTrashed()
 */
	class AgreementPayment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $agreement_id
 * @property int $agreement_payment_id
 * @property int $payment_mode_id
 * @property string|null $bounced_date
 * @property string|null $bounced_reason
 * @property int|null $bounced_by
 * @property int $has_bounced
 * @property int|null $contract_unit_id
 * @property int $agreement_unit_id
 * @property int|null $bank_id
 * @property string|null $cheque_number
 * @property string|null $cheque_issuer
 * @property string|null $cheque_issuer_name
 * @property string|null $cheque_issuer_id
 * @property string $payment_date
 * @property string $payment_amount
 * @property int $is_payment_received 0-Pending, 1-Received, 2-Half Received,3-Bounced
 * @property int $added_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $terminate_status
 * @property int $transaction_type 1 = Receive, 2 = Pay Back
 * @property-read \App\Models\Agreement $agreement
 * @property-read \App\Models\AgreementPayment|null $agreementPayment
 * @property-read \App\Models\AgreementUnit|null $agreementUnit
 * @property-read \App\Models\Bank|null $bank
 * @property-read \App\Models\User|null $bouncedBy
 * @property-read \App\Models\ClearedReceivable|null $clearedReceivables
 * @property-read \App\Models\User|null $deletedBy
 * @property-read \App\Models\TenantInvoice|null $invoice
 * @property-read \App\Models\PaymentMode|null $paymentMode
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClearedReceivable> $receivedPayments
 * @property-read int|null $received_payments_count
 * @property-write mixed $paid_date
 * @property-write mixed $paymentdate
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementPaymentDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementPaymentDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementPaymentDetail onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementPaymentDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementPaymentDetail whereAddedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementPaymentDetail whereAgreementId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementPaymentDetail whereAgreementPaymentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementPaymentDetail whereAgreementUnitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementPaymentDetail whereBankId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementPaymentDetail whereBouncedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementPaymentDetail whereBouncedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementPaymentDetail whereBouncedReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementPaymentDetail whereChequeIssuer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementPaymentDetail whereChequeIssuerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementPaymentDetail whereChequeIssuerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementPaymentDetail whereChequeNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementPaymentDetail whereContractUnitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementPaymentDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementPaymentDetail whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementPaymentDetail whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementPaymentDetail whereHasBounced($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementPaymentDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementPaymentDetail whereIsPaymentReceived($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementPaymentDetail wherePaymentAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementPaymentDetail wherePaymentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementPaymentDetail wherePaymentModeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementPaymentDetail whereTerminateStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementPaymentDetail whereTransactionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementPaymentDetail whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementPaymentDetail whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementPaymentDetail withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementPaymentDetail withoutTrashed()
 */
	class AgreementPaymentDetail extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $agreement_id
 * @property int $old_status
 * @property int $new_status
 * @property string $changed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Agreement $agreement
 * @property-read \App\Models\User|null $deletedBy
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementStatusLogs newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementStatusLogs newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementStatusLogs onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementStatusLogs query()
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementStatusLogs whereAgreementId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementStatusLogs whereChangedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementStatusLogs whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementStatusLogs whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementStatusLogs whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementStatusLogs whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementStatusLogs whereNewStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementStatusLogs whereOldStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementStatusLogs whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementStatusLogs withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementStatusLogs withoutTrashed()
 */
	class AgreementStatusLogs extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $agreement_id
 * @property int $agreement_unit_id
 * @property int $contract_unit_details_id
 * @property int $contract_subunit_details_id
 * @property string $rent_per_month
 * @property int $added_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Agreement $agreement
 * @property-read \App\Models\AgreementUnit|null $agreementUnit
 * @property-read \App\Models\ContractSubunitDetail|null $contractSubunitDetail
 * @property-read \App\Models\ContractUnitDetail|null $contractUnitDetail
 * @property-read \App\Models\User|null $deletedBy
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementSubunitRentBifurcation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementSubunitRentBifurcation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementSubunitRentBifurcation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementSubunitRentBifurcation query()
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementSubunitRentBifurcation whereAddedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementSubunitRentBifurcation whereAgreementId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementSubunitRentBifurcation whereAgreementUnitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementSubunitRentBifurcation whereContractSubunitDetailsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementSubunitRentBifurcation whereContractUnitDetailsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementSubunitRentBifurcation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementSubunitRentBifurcation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementSubunitRentBifurcation whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementSubunitRentBifurcation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementSubunitRentBifurcation whereRentPerMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementSubunitRentBifurcation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementSubunitRentBifurcation whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementSubunitRentBifurcation withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementSubunitRentBifurcation withoutTrashed()
 */
	class AgreementSubunitRentBifurcation extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $agreement_id
 * @property string $tenant_name
 * @property string $tenant_mobile
 * @property string $tenant_email
 * @property int|null $nationality_id
 * @property string $tenant_address
 * @property int $added_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string $contact_person
 * @property string $contact_number
 * @property string $contact_email
 * @property string|null $tenant_street
 * @property string|null $tenant_city
 * @property string|null $emirate_id
 * @property-read \App\Models\Agreement $agreement
 * @property-read \App\Models\User|null $deletedBy
 * @property-read \App\Models\Nationality|null $nationality
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementTenant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementTenant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementTenant onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementTenant query()
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementTenant whereAddedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementTenant whereAgreementId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementTenant whereContactEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementTenant whereContactNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementTenant whereContactPerson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementTenant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementTenant whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementTenant whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementTenant whereEmirateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementTenant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementTenant whereNationalityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementTenant whereTenantAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementTenant whereTenantCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementTenant whereTenantEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementTenant whereTenantMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementTenant whereTenantName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementTenant whereTenantStreet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementTenant whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementTenant whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementTenant withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementTenant withoutTrashed()
 */
	class AgreementTenant extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $agreement_id
 * @property int $unit_type_id
 * @property int $contract_unit_details_id
 * @property int|null $contract_subunit_details_id
 * @property array|null $subunit_ids
 * @property string $rent_per_month
 * @property string $rent_per_annum_agreement
 * @property string $unit_revenue
 * @property int $added_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Agreement $agreement
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AgreementSubunitRentBifurcation> $agreementSubunitRentBifurcation
 * @property-read int|null $agreement_subunit_rent_bifurcation_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AgreementPaymentDetail> $agreement_payment_details
 * @property-read int|null $agreement_payment_details_count
 * @property-read \App\Models\ContractSubunitDetail|null $contractSubunitDetail
 * @property-read \App\Models\ContractUnitDetail|null $contractUnitDetail
 * @property-read \App\Models\User|null $deletedBy
 * @property-read \App\Models\UnitType|null $unitType
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementUnit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementUnit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementUnit onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementUnit query()
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementUnit whereAddedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementUnit whereAgreementId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementUnit whereContractSubunitDetailsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementUnit whereContractUnitDetailsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementUnit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementUnit whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementUnit whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementUnit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementUnit whereRentPerAnnumAgreement($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementUnit whereRentPerMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementUnit whereSubunitIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementUnit whereUnitRevenue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementUnit whereUnitTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementUnit whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementUnit whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementUnit withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AgreementUnit withoutTrashed()
 */
	class AgreementUnit extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $company_id
 * @property string $area_code
 * @property string $area_name
 * @property int $added_by
 * @property int|null $updated_by
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Company|null $company
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Locality> $localities
 * @property-read int|null $localities_count
 * @method static \Illuminate\Database\Eloquent\Builder|Area newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Area newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Area onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Area query()
 * @method static \Illuminate\Database\Eloquent\Builder|Area whereAddedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Area whereAreaCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Area whereAreaName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Area whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Area whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Area whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Area whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Area whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Area whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Area whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Area withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Area withoutTrashed()
 * @method static \Database\Factories\AreaFactory factory($count = null, $state = [])
 * @mixin \Eloquent
 * @property int|null $deleted_by
 * @property-read \App\Models\User|null $addedBy
 * @property-read \App\Models\User|null $deletedBy
 * @property-read \App\Models\User|null $updatedBy
 * @method static \Illuminate\Database\Eloquent\Builder|Area whereDeletedBy($value)
 */
	class Area extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $company_id
 * @property string $bank_code
 * @property string $bank_name
 * @property string $bank_short_code
 * @property int|null $added_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $addedBy
 * @property-read \App\Models\Company|null $company
 * @property-read \App\Models\User|null $deletedBy
 * @property-read \App\Models\User|null $updatedBy
 * @method static \Illuminate\Database\Eloquent\Builder|Bank accessible($action = null)
 * @method static \Database\Factories\BankFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Bank newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Bank newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Bank onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Bank query()
 * @method static \Illuminate\Database\Eloquent\Builder|Bank whereAddedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bank whereBankCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bank whereBankName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bank whereBankShortCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bank whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bank whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bank whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bank whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bank whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bank whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bank whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bank whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bank withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Bank withoutTrashed()
 */
	class Bank extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $agreement_id
 * @property int $agreement_payment_details_id
 * @property string $paid_amount
 * @property string $pending_amount
 * @property string $paid_date
 * @property int $paid_mode_id
 * @property int|null $paid_bank_id
 * @property string|null $paid_cheque_number
 * @property string|null $payment_remarks
 * @property int|null $paid_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $paid_company_id
 * @property-read \App\Models\AgreementPaymentDetail|null $agreementPaymentDetail
 * @property-read \App\Models\Bank|null $paidBank
 * @property-read \App\Models\User|null $paidBy
 * @property-read \App\Models\Company|null $paidCompany
 * @property-read \App\Models\PaymentMode|null $paidMode
 * @method static \Illuminate\Database\Eloquent\Builder|ClearedReceivable newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClearedReceivable newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClearedReceivable query()
 * @method static \Illuminate\Database\Eloquent\Builder|ClearedReceivable whereAgreementId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClearedReceivable whereAgreementPaymentDetailsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClearedReceivable whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClearedReceivable whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClearedReceivable wherePaidAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClearedReceivable wherePaidBankId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClearedReceivable wherePaidBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClearedReceivable wherePaidChequeNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClearedReceivable wherePaidCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClearedReceivable wherePaidDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClearedReceivable wherePaidModeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClearedReceivable wherePaymentRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClearedReceivable wherePendingAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClearedReceivable whereUpdatedAt($value)
 */
	class ClearedReceivable extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $industry_id
 * @property string $company_code
 * @property string $company_name
 * @property string $company_short_code
 * @property string|null $address
 * @property string|null $phone
 * @property string|null $email
 * @property string|null $website
 * @property int $added_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $addedBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Area> $areas
 * @property-read int|null $areas_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Bank> $banks
 * @property-read int|null $banks_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Contract> $contracts
 * @property-read int|null $contracts_count
 * @property-read \App\Models\User|null $deletedBy
 * @property-read \App\Models\Industry $industry
 * @property-write mixed $added_date
 * @property-write mixed $updated_date
 * @property-read \App\Models\User|null $updatedBy
 * @method static \Database\Factories\CompanyFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Company newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Company newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Company onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Company permittedForModule($module, $submodule = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Company query()
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereAddedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereCompanyCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereCompanyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereCompanyShortCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereIndustryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereWebsite($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Company withoutTrashed()
 */
	class Company extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $project_code
 * @property int $project_number
 * @property int $company_id
 * @property int $vendor_id
 * @property int $contract_type_id
 * @property string|null $contact_person
 * @property string|null $contact_number
 * @property int $area_id
 * @property int $locality_id
 * @property int $property_id
 * @property int $is_vendor_contract_uploaded
 * @property int $is_scope_generated
 * @property int $contract_status 0-Pending, 1-Processing, 2-Approved, 3-Rejected, 4-Send for Approval, 5-Approval on Hold, 6-Sign Pending, 7- Signed, 8-Expired, 9-Terminated, 10-Dropped
 * @property string|null $signed_at
 * @property int|null $signed_by
 * @property int $is_aknowledgement_uploaded
 * @property int $is_acknowledgement_released
 * @property string|null $acknowledgement_released_date
 * @property int|null $acknowledgement_released_by
 * @property int $is_cheque_copy_uploaded
 * @property int|null $parent_contract_id
 * @property int $contract_renewal_status 0-new, 1-renewed
 * @property int|null $renewal_count
 * @property string|null $renewal_date
 * @property int|null $renewed_by
 * @property int $renew_reject_status 1-rejected
 * @property string|null $renew_reject_reason
 * @property int|null $renew_rejected_by
 * @property int $added_by
 * @property int|null $updated_by
 * @property int|null $approved_by
 * @property int|null $deleted_by
 * @property int|null $scope_generated_by
 * @property string|null $rejected_reason
 * @property int|null $contract_rejected_by
 * @property int|null $is_agreement_added 0 - not added, 1 - added
 * @property int $has_agreement
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $approved_date
 * @property string|null $rejected_date
 * @property int $is_processing
 * @property string|null $terminated_date
 * @property string|null $terminated_reason
 * @property int|null $terminated_by
 * @property string|null $balance_amount
 * @property int $balance_received
 * @property int $indirect_company_id
 * @property int $indirect_contract_id
 * @property int $indirect_status 0-direct 1-indirect
 * @property int $is_indirect_contract 0-no 1-yes
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Agreement> $agreements
 * @property-read int|null $agreements_count
 * @property-read \App\Models\Area|null $area
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Contract> $children
 * @property-read int|null $children_count
 * @property-read \App\Models\Company|null $company
 * @property-read \App\Models\ContractDetail|null $contract_detail
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ContractDocument> $contract_documents
 * @property-read int|null $contract_documents_count
 * @property-read \App\Models\ContractOtc|null $contract_otc
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ContractPaymentDetail> $contract_payment_details
 * @property-read int|null $contract_payment_details_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ContractPaymentReceivable> $contract_payment_receivables
 * @property-read int|null $contract_payment_receivables_count
 * @property-read \App\Models\ContractPayment|null $contract_payments
 * @property-read \App\Models\ContractRental|null $contract_rentals
 * @property-read \App\Models\ContractScope|null $contract_scope
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ContractSubunitDetail> $contract_subunit_details
 * @property-read int|null $contract_subunit_details_count
 * @property-read \App\Models\ContractType|null $contract_type
 * @property-read \App\Models\ContractUnit|null $contract_unit
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ContractUnitDetail> $contract_unit_details
 * @property-read int|null $contract_unit_details_count
 * @property-read \App\Models\User|null $deletedBy
 * @property-read \App\Models\Company|null $indirectCompany
 * @property-read Contract|null $indirectContract
 * @property-read \App\Models\Locality|null $locality
 * @property-read Contract|null $parent
 * @property-read \App\Models\Property|null $property
 * @property-read \App\Models\Vendor|null $vendor
 * @method static \Illuminate\Database\Eloquent\Builder|Contract newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Contract newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Contract onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Contract query()
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereAcknowledgementReleasedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereAcknowledgementReleasedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereAddedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereApprovedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereApprovedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereAreaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereBalanceAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereBalanceReceived($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereContactNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereContactPerson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereContractRejectedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereContractRenewalStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereContractStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereContractTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereHasAgreement($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereIndirectCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereIndirectContractId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereIndirectStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereIsAcknowledgementReleased($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereIsAgreementAdded($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereIsAknowledgementUploaded($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereIsChequeCopyUploaded($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereIsIndirectContract($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereIsProcessing($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereIsScopeGenerated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereIsVendorContractUploaded($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereLocalityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereParentContractId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereProjectCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereProjectNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract wherePropertyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereRejectedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereRejectedReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereRenewRejectReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereRenewRejectStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereRenewRejectedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereRenewalCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereRenewalDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereRenewedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereScopeGeneratedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereSignedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereSignedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereTerminatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereTerminatedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereTerminatedReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereVendorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Contract withoutTrashed()
 */
	class Contract extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $contract_id
 * @property int $user_id
 * @property string $comment
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Contract $contract
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|ContractApprovalComment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractApprovalComment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractApprovalComment query()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractApprovalComment whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractApprovalComment whereContractId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractApprovalComment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractApprovalComment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractApprovalComment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractApprovalComment whereUserId($value)
 */
	class ContractApprovalComment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $contract_id
 * @property string|null $contract_fee
 * @property string|null $ejari
 * @property string $start_date
 * @property string $end_date
 * @property int $duration_in_months
 * @property int|null $duration_in_days
 * @property string $closing_date
 * @property int $grace_period
 * @property int $added_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Contract $contract
 * @property-read \App\Models\User|null $deletedBy
 * @method static \Illuminate\Database\Eloquent\Builder|ContractDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractDetail onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractDetail whereAddedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractDetail whereClosingDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractDetail whereContractFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractDetail whereContractId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractDetail whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractDetail whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractDetail whereDurationInDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractDetail whereDurationInMonths($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractDetail whereEjari($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractDetail whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractDetail whereGracePeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractDetail whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractDetail whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractDetail whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractDetail withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractDetail withoutTrashed()
 */
	class ContractDetail extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $contract_id
 * @property int|null $document_type_id
 * @property string $original_document_path
 * @property string $original_document_name
 * @property string|null $signed_document_path
 * @property string|null $signed_document_name
 * @property int $signed_status 0-unsinged, 1-mr.muneer signed,2- mr.muneer and vendor signed
 * @property int $added_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Contract $contract
 * @property-read \App\Models\User|null $deletedBy
 * @property-read \App\Models\DocumentType|null $document_type
 * @method static \Illuminate\Database\Eloquent\Builder|ContractDocument newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractDocument newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractDocument onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractDocument query()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractDocument whereAddedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractDocument whereContractId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractDocument whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractDocument whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractDocument whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractDocument whereDocumentTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractDocument whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractDocument whereOriginalDocumentName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractDocument whereOriginalDocumentPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractDocument whereSignedDocumentName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractDocument whereSignedDocumentPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractDocument whereSignedStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractDocument whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractDocument whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractDocument withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractDocument withoutTrashed()
 */
	class ContractDocument extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $contract_id
 * @property string|null $cost_of_development
 * @property string|null $cost_of_bed
 * @property string|null $cost_of_matress
 * @property string|null $appliances
 * @property string|null $decoration
 * @property string|null $dewa_deposit
 * @property string|null $cost_of_cabinets
 * @property string $added_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Contract $contract
 * @property-read \App\Models\User|null $deletedBy
 * @method static \Illuminate\Database\Eloquent\Builder|ContractOtc newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractOtc newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractOtc onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractOtc query()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractOtc whereAddedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractOtc whereAppliances($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractOtc whereContractId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractOtc whereCostOfBed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractOtc whereCostOfCabinets($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractOtc whereCostOfDevelopment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractOtc whereCostOfMatress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractOtc whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractOtc whereDecoration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractOtc whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractOtc whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractOtc whereDewaDeposit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractOtc whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractOtc whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractOtc whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractOtc withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractOtc withoutTrashed()
 */
	class ContractOtc extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $contract_id
 * @property int $contract_payment_detail_id
 * @property string $pending_amount
 * @property string $paid_amount
 * @property string $paid_date
 * @property int $paid_mode
 * @property int $paid_by
 * @property int|null $paid_bank
 * @property string|null $paid_cheque_number
 * @property string|null $payment_remarks
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $company_id
 * @property int $returned_status 0-Normal Clear, 1-Returned
 * @property-read \App\Models\Company|null $company
 * @property-read \App\Models\Contract|null $contract
 * @property-read \App\Models\ContractPaymentDetail|null $contractPaymentDetail
 * @property-read \App\Models\Bank|null $paidBank
 * @property-read \App\Models\User|null $paidBy
 * @property-read \App\Models\PaymentMode|null $paidMode
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPayableClear newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPayableClear newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPayableClear query()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPayableClear whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPayableClear whereContractId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPayableClear whereContractPaymentDetailId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPayableClear whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPayableClear whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPayableClear wherePaidAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPayableClear wherePaidBank($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPayableClear wherePaidBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPayableClear wherePaidChequeNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPayableClear wherePaidDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPayableClear wherePaidMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPayableClear wherePaymentRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPayableClear wherePendingAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPayableClear whereReturnedStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPayableClear whereUpdatedAt($value)
 */
	class ContractPayableClear extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $contract_id
 * @property int $installment_id
 * @property int $interval
 * @property string|null $beneficiary
 * @property int $added_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $has_payment_started
 * @property int $has_fully_paid
 * @property-read \App\Models\Contract $contract
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ContractPaymentDetail> $contractPaymentDetails
 * @property-read int|null $contract_payment_details_count
 * @property-read \App\Models\User|null $deletedBy
 * @property-read \App\Models\Installment|null $installment
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPayment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPayment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPayment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPayment query()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPayment whereAddedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPayment whereBeneficiary($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPayment whereContractId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPayment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPayment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPayment whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPayment whereHasFullyPaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPayment whereHasPaymentStarted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPayment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPayment whereInstallmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPayment whereInterval($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPayment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPayment whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPayment withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPayment withoutTrashed()
 */
	class ContractPayment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $contract_id
 * @property int $contract_payment_id
 * @property int $payment_mode_id
 * @property string $payment_date
 * @property string $payment_amount
 * @property int|null $bank_id
 * @property string|null $cheque_no
 * @property string|null $cheque_issuer
 * @property string|null $cheque_issuer_name
 * @property string|null $cheque_issuer_id
 * @property int $added_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $paid_status 0-not paid, 1-paid,  2-half paid
 * @property int $has_returned
 * @property string|null $returned_date
 * @property string|null $returned_reason
 * @property int $returned_by
 * @property int $terminate_status 0-Active, 1-Terminated
 * @property-read \App\Models\User|null $addedBy
 * @property-read \App\Models\Bank|null $bank
 * @property-read \App\Models\Contract $contract
 * @property-read \App\Models\ContractPayment $contract_payment
 * @property-read \App\Models\User|null $deletedBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ContractPayableClear> $payables
 * @property-read int|null $payables_count
 * @property-read \App\Models\PaymentMode|null $payment_mode
 * @property-read \App\Models\User|null $returnedBy
 * @property-read \App\Models\User|null $updatedBy
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPaymentDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPaymentDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPaymentDetail onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPaymentDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPaymentDetail whereAddedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPaymentDetail whereBankId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPaymentDetail whereChequeIssuer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPaymentDetail whereChequeIssuerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPaymentDetail whereChequeIssuerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPaymentDetail whereChequeNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPaymentDetail whereContractId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPaymentDetail whereContractPaymentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPaymentDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPaymentDetail whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPaymentDetail whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPaymentDetail whereHasReturned($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPaymentDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPaymentDetail wherePaidStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPaymentDetail wherePaymentAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPaymentDetail wherePaymentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPaymentDetail wherePaymentModeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPaymentDetail whereReturnedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPaymentDetail whereReturnedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPaymentDetail whereReturnedReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPaymentDetail whereTerminateStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPaymentDetail whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPaymentDetail whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPaymentDetail withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPaymentDetail withoutTrashed()
 */
	class ContractPaymentDetail extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $contract_id
 * @property string $receivable_date
 * @property string $receivable_amount
 * @property int $added_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Contract $contract
 * @property-read \App\Models\User|null $deletedBy
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPaymentReceivable newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPaymentReceivable newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPaymentReceivable onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPaymentReceivable query()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPaymentReceivable whereAddedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPaymentReceivable whereContractId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPaymentReceivable whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPaymentReceivable whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPaymentReceivable whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPaymentReceivable whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPaymentReceivable whereReceivableAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPaymentReceivable whereReceivableDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPaymentReceivable whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPaymentReceivable whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPaymentReceivable withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractPaymentReceivable withoutTrashed()
 */
	class ContractPaymentReceivable extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $contract_rental_code
 * @property int $contract_id
 * @property string $rent_per_annum_payable
 * @property string|null $commission_percentage
 * @property string|null $commission
 * @property string|null $deposit_percentage
 * @property string|null $deposit
 * @property string $rent_receivable_per_month
 * @property string $rent_receivable_per_annum
 * @property string $roi_perc
 * @property string $expected_profit
 * @property string $profit_percentage
 * @property string|null $receivable_start_date
 * @property int $receivable_installments
 * @property string $total_payment_to_vendor
 * @property string|null $total_otc
 * @property string $final_cost
 * @property string $initial_investment
 * @property int $added_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Contract $contract
 * @property-read \App\Models\User|null $deletedBy
 * @property-read \App\Models\Installment|null $installment
 * @method static \Illuminate\Database\Eloquent\Builder|ContractRental newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractRental newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractRental onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractRental query()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractRental whereAddedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractRental whereCommission($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractRental whereCommissionPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractRental whereContractId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractRental whereContractRentalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractRental whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractRental whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractRental whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractRental whereDeposit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractRental whereDepositPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractRental whereExpectedProfit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractRental whereFinalCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractRental whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractRental whereInitialInvestment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractRental whereProfitPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractRental whereReceivableInstallments($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractRental whereReceivableStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractRental whereRentPerAnnumPayable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractRental whereRentReceivablePerAnnum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractRental whereRentReceivablePerMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractRental whereRoiPerc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractRental whereTotalOtc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractRental whereTotalPaymentToVendor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractRental whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractRental whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractRental withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractRental withoutTrashed()
 */
	class ContractRental extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $contract_id
 * @property string $scope
 * @property string $file_name
 * @property int $generated_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Contract $contract
 * @property-read \App\Models\User|null $deletedBy
 * @method static \Illuminate\Database\Eloquent\Builder|ContractScope logs()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractScope newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractScope newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractScope onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractScope query()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractScope whereContractId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractScope whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractScope whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractScope whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractScope whereFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractScope whereGeneratedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractScope whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractScope whereScope($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractScope whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractScope whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractScope withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractScope withoutTrashed()
 */
	class ContractScope extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $contract_scope_id
 * @property int|null $user_id
 * @property string $action
 * @property string|null $description
 * @property array|null $old_values
 * @property array|null $new_values
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ContractScope $contractScope
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|ContractScopeLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractScopeLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractScopeLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractScopeLog whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractScopeLog whereContractScopeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractScopeLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractScopeLog whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractScopeLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractScopeLog whereNewValues($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractScopeLog whereOldValues($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractScopeLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractScopeLog whereUserId($value)
 */
	class ContractScopeLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $contract_template_id
 * @property string $page_type
 * @property int $x
 * @property int $y
 * @property int $width
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\VendorContractTemplate|null $contract_template
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSignatureDimension newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSignatureDimension newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSignatureDimension query()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSignatureDimension whereContractTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSignatureDimension whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSignatureDimension whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSignatureDimension wherePageType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSignatureDimension whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSignatureDimension whereWidth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSignatureDimension whereX($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSignatureDimension whereY($value)
 */
	class ContractSignatureDimension extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $contract_id
 * @property int $vendor_id
 * @property string $email_to
 * @property string $email_subject
 * @property string|null $email_body
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Contract|null $contract
 * @property-read \App\Models\Vendor|null $vendor
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSignedEmail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSignedEmail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSignedEmail query()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSignedEmail whereContractId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSignedEmail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSignedEmail whereEmailBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSignedEmail whereEmailSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSignedEmail whereEmailTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSignedEmail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSignedEmail whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSignedEmail whereVendorId($value)
 */
	class ContractSignedEmail extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $contract_id
 * @property int $old_status
 * @property int $new_status
 * @property string $changed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Agreement $contract
 * @property-read \App\Models\User|null $deletedBy
 * @method static \Illuminate\Database\Eloquent\Builder|ContractStatusLogs newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractStatusLogs newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractStatusLogs onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractStatusLogs query()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractStatusLogs whereChangedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractStatusLogs whereContractId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractStatusLogs whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractStatusLogs whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractStatusLogs whereNewStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractStatusLogs whereOldStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractStatusLogs whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractStatusLogs withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractStatusLogs withoutTrashed()
 */
	class ContractStatusLogs extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $contract_id
 * @property int $contract_unit_id
 * @property int $contract_unit_detail_id
 * @property string $subunit_no
 * @property int $subunit_type 1-partition, 2-bedspace, 3-room, 4-full flat
 * @property string $subunit_code proj. no / company code / unit no / subunit no
 * @property int $added_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $is_vacant
 * @property string|null $subunit_rent
 * @property-read \App\Models\Contract $contract
 * @property-read \App\Models\ContractUnit $contract_unit
 * @property-read \App\Models\ContractUnitDetail $contract_unit_detail
 * @property-read \App\Models\User|null $deletedBy
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSubunitDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSubunitDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSubunitDetail onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSubunitDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSubunitDetail whereAddedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSubunitDetail whereContractId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSubunitDetail whereContractUnitDetailId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSubunitDetail whereContractUnitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSubunitDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSubunitDetail whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSubunitDetail whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSubunitDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSubunitDetail whereIsVacant($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSubunitDetail whereSubunitCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSubunitDetail whereSubunitNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSubunitDetail whereSubunitRent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSubunitDetail whereSubunitType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSubunitDetail whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSubunitDetail whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSubunitDetail withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractSubunitDetail withoutTrashed()
 */
	class ContractSubunitDetail extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $contract_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $shortcode
 * @method static \Illuminate\Database\Eloquent\Builder|ContractType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractType query()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractType whereContractType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractType whereShortcode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractType whereUpdatedAt($value)
 */
	class ContractType extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $contract_unit_code
 * @property int $contract_id
 * @property int $building_type 0-normal, 1-full building
 * @property int $business_type 1-b2b, 2-b2c
 * @property int $watchman_room
 * @property int $no_of_units
 * @property string|null $unit_numbers
 * @property string|null $unit_type_count
 * @property string|null $unit_property_type
 * @property string|null $no_of_floors
 * @property string|null $floor_numbers
 * @property int $total_subunit_count_per_contract
 * @property int $added_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Contract $contract
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ContractUnitDetail> $contractUnitDetails
 * @property-read int|null $contract_unit_details_count
 * @property-read \App\Models\User|null $deletedBy
 * @property-read mixed $property_type
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnit onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnit query()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnit whereAddedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnit whereBuildingType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnit whereBusinessType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnit whereContractId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnit whereContractUnitCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnit whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnit whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnit whereFloorNumbers($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnit whereNoOfFloors($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnit whereNoOfUnits($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnit whereTotalSubunitCountPerContract($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnit whereUnitNumbers($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnit whereUnitPropertyType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnit whereUnitTypeCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnit whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnit whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnit whereWatchmanRoom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnit withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnit withoutTrashed()
 */
	class ContractUnit extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $contract_id
 * @property int $contract_unit_id
 * @property string|null $unit_number
 * @property int $unit_type_id
 * @property string $floor_no
 * @property int $unit_status_id
 * @property string $unit_rent_per_annum
 * @property int $fb_unit_count
 * @property int|null $unit_size_unit_id
 * @property int|null $unit_size
 * @property int $property_type_id
 * @property int $partition
 * @property int $bedspace
 * @property int $room
 * @property int $maid_room
 * @property int $total_partition
 * @property int $total_bedspace
 * @property int $total_room
 * @property string|null $rent_per_partition
 * @property string|null $rent_per_bedspace
 * @property string|null $rent_per_room
 * @property string $rent_per_flat
 * @property string $rent_per_unit_per_month
 * @property string $rent_per_unit_per_annum
 * @property string $total_rent_per_unit_per_month
 * @property string $subunittype 1-partition, 2-bedspace, 3-room, 4-full flat
 * @property int $subunitcount_per_unit
 * @property string $subunit_rent_per_unit
 * @property string|null $unit_profit_perc
 * @property int $added_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $is_vacant
 * @property string|null $unit_profit
 * @property string|null $unit_revenue
 * @property string|null $unit_amount_payable
 * @property string|null $unit_commission
 * @property string|null $unit_deposit
 * @property string $unit_rent_per_month
 * @property int $subunit_occupied_count
 * @property int $subunit_vacant_count
 * @property string $total_payment_received
 * @property string $total_payment_pending
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AgreementUnit> $agreementUnits
 * @property-read int|null $agreement_units_count
 * @property-read \App\Models\Contract $contract
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ContractSubunitDetail> $contractSubUnitDetails
 * @property-read int|null $contract_sub_unit_details_count
 * @property-read \App\Models\ContractUnit $contract_unit
 * @property-read \App\Models\User|null $deletedBy
 * @property-read \App\Models\PropertyType|null $property_type
 * @property-read \App\Models\UnitSizeUnit|null $unit_size_unit
 * @property-read \App\Models\UnitStatus|null $unit_status
 * @property-read \App\Models\UnitType|null $unit_type
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnitDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnitDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnitDetail onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnitDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnitDetail whereAddedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnitDetail whereBedspace($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnitDetail whereContractId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnitDetail whereContractUnitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnitDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnitDetail whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnitDetail whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnitDetail whereFbUnitCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnitDetail whereFloorNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnitDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnitDetail whereIsVacant($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnitDetail whereMaidRoom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnitDetail wherePartition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnitDetail wherePropertyTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnitDetail whereRentPerBedspace($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnitDetail whereRentPerFlat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnitDetail whereRentPerPartition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnitDetail whereRentPerRoom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnitDetail whereRentPerUnitPerAnnum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnitDetail whereRentPerUnitPerMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnitDetail whereRoom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnitDetail whereSubunitOccupiedCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnitDetail whereSubunitRentPerUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnitDetail whereSubunitVacantCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnitDetail whereSubunitcountPerUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnitDetail whereSubunittype($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnitDetail whereTotalBedspace($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnitDetail whereTotalPartition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnitDetail whereTotalPaymentPending($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnitDetail whereTotalPaymentReceived($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnitDetail whereTotalRentPerUnitPerMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnitDetail whereTotalRoom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnitDetail whereUnitAmountPayable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnitDetail whereUnitCommission($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnitDetail whereUnitDeposit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnitDetail whereUnitNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnitDetail whereUnitProfit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnitDetail whereUnitProfitPerc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnitDetail whereUnitRentPerAnnum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnitDetail whereUnitRentPerMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnitDetail whereUnitRevenue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnitDetail whereUnitSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnitDetail whereUnitSizeUnitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnitDetail whereUnitStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnitDetail whereUnitTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnitDetail whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnitDetail whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnitDetail withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractUnitDetail withoutTrashed()
 */
	class ContractUnitDetail extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $label_name
 * @property string $field_type
 * @property string $field_name
 * @property string $status_change_value
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $accept_types
 * @property-read \App\Models\ContractDocument|null $contractDocuments
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentType query()
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentType whereAcceptTypes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentType whereFieldName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentType whereFieldType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentType whereLabelName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentType whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentType whereStatusChangeValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentType whereUpdatedAt($value)
 */
	class DocumentType extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string|null $requested
 * @property string|null $response
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|EmailLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EmailLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EmailLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|EmailLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailLog whereRequested($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailLog whereResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailLog whereUpdatedAt($value)
 */
	class EmailLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Emirate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Emirate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Emirate query()
 * @method static \Illuminate\Database\Eloquent\Builder|Emirate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Emirate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Emirate whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Emirate whereUpdatedAt($value)
 */
	class Emirate extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Industry newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Industry newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Industry query()
 * @method static \Illuminate\Database\Eloquent\Builder|Industry whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Industry whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Industry whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Industry whereUpdatedAt($value)
 */
	class Industry extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $company_id
 * @property string $installment_code
 * @property string $installment_name
 * @property int $interval
 * @property int|null $added_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Company|null $company
 * @property-read \App\Models\User|null $deletedBy
 * @method static \Database\Factories\InstallmentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Installment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Installment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Installment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Installment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Installment whereAddedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Installment whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Installment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Installment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Installment whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Installment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Installment whereInstallmentCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Installment whereInstallmentName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Installment whereInterval($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Installment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Installment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Installment whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Installment withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Installment withoutTrashed()
 */
	class Installment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $investment_code
 * @property int $investor_id
 * @property int $payout_batch_id
 * @property int $company_id
 * @property int $profit_interval_id
 * @property int $invested_company_id
 * @property string $investment_amount
 * @property int $investment_type 0-New
 * @property string $received_amount
 * @property string $total_received_amount
 * @property string $balance_amount
 * @property int $has_fully_received 0-Not Received,1-fully Received,2-Partially Received
 * @property string $investment_date
 * @property int $investment_tenure In Months
 * @property int $grace_period In Days
 * @property string $maturity_date
 * @property string $profit_perc
 * @property string $profit_amount
 * @property string $profit_amount_per_interval
 * @property int|null $profit_release_date
 * @property string $initial_profit_release_month
 * @property string $total_profit_released
 * @property string $current_month_released
 * @property string $outstanding_profit
 * @property int $is_profit_processed 0-No,1-Yes
 * @property string|null $last_profit_released_date
 * @property string|null $next_profit_release_date
 * @property string|null $next_referral_commission_release_date
 * @property string|null $nominee_name
 * @property string|null $nominee_email
 * @property string|null $nominee_phone
 * @property int $company_bank_id
 * @property int $investor_bank_id
 * @property int $investment_status 0-Inactive,1-Active
 * @property int $terminate_status 0-Not Terminated, 1-Termination Requested,2-Terminated
 * @property int $reinvestment_or_not 0-No,1-Yes
 * @property int|null $parent_investment_id
 * @property int $has_reinvestment 0-No,1-Yes
 * @property int|null $reinvested_count
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property int $added_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $termination_requested_date
 * @property string|null $termination_date
 * @property int|null $termination_duration
 * @property string|null $termination_document
 * @property int|null $termination_requested_by
 * @property int|null $terminated_by
 * @property string $termination_outstanding
 * @property string $termination_referral_commission_outstanding
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Investment> $childInvestments
 * @property-read int|null $child_investments_count
 * @property-read \App\Models\Company|null $company
 * @property-read \App\Models\Bank|null $companyBank
 * @property-read \App\Models\User|null $deletedBy
 * @property-read mixed $formatted_investment_amount
 * @property-read mixed $is_active
 * @property-read \App\Models\Company|null $investedCompany
 * @property-read \App\Models\InvestmentDocument|null $investmentDocument
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\InvestmentReceivedPayment> $investmentReceivedPayments
 * @property-read int|null $investment_received_payments_count
 * @property-read \App\Models\InvestmentReferral|null $investmentReferral
 * @property-read \App\Models\Investor|null $investor
 * @property-read \App\Models\Bank|null $investorBank
 * @property-read Investment|null $parentInvestment
 * @property-read \App\Models\PayoutBatch|null $payoutBatch
 * @property-read \App\Models\ProfitInterval|null $profitInterval
 * @property-read \App\Models\ReferralCommissionFrequency|null $referralProfitFrequency
 * @method static \Illuminate\Database\Eloquent\Builder|Investment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Investment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Investment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Investment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Investment whereAddedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investment whereBalanceAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investment whereCompanyBankId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investment whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investment whereCurrentMonthReleased($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investment whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investment whereGracePeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investment whereHasFullyReceived($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investment whereHasReinvestment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investment whereInitialProfitReleaseMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investment whereInvestedCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investment whereInvestmentAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investment whereInvestmentCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investment whereInvestmentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investment whereInvestmentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investment whereInvestmentTenure($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investment whereInvestmentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investment whereInvestorBankId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investment whereInvestorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investment whereIsProfitProcessed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investment whereLastProfitReleasedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investment whereMaturityDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investment whereNextProfitReleaseDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investment whereNextReferralCommissionReleaseDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investment whereNomineeEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investment whereNomineeName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investment whereNomineePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investment whereOutstandingProfit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investment whereParentInvestmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investment wherePayoutBatchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investment whereProfitAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investment whereProfitAmountPerInterval($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investment whereProfitIntervalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investment whereProfitPerc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investment whereProfitReleaseDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investment whereReceivedAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investment whereReinvestedCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investment whereReinvestmentOrNot($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investment whereTerminateStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investment whereTerminatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investment whereTerminationDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investment whereTerminationDocument($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investment whereTerminationDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investment whereTerminationOutstanding($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investment whereTerminationReferralCommissionOutstanding($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investment whereTerminationRequestedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investment whereTerminationRequestedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investment whereTotalProfitReleased($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investment whereTotalReceivedAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investment whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investment withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Investment withoutTrashed()
 */
	class Investment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $investment_id
 * @property int $investor_id
 * @property string|null $investment_contract_file_name
 * @property string|null $investment_contract_file_path
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property int $added_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $deletedBy
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentDocument newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentDocument newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentDocument onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentDocument query()
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentDocument whereAddedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentDocument whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentDocument whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentDocument whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentDocument whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentDocument whereInvestmentContractFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentDocument whereInvestmentContractFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentDocument whereInvestmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentDocument whereInvestorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentDocument whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentDocument whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentDocument withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentDocument withoutTrashed()
 */
	class InvestmentDocument extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $investment_id
 * @property int $investor_id
 * @property int $is_initial_payment 0-False,1-True
 * @property string $received_amount
 * @property string $received_date
 * @property int $status 0-Inactive,1-Active
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property int $added_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $addedBy
 * @property-read \App\Models\User|null $deletedBy
 * @property-read \App\Models\User|null $updatedBy
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentReceivedPayment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentReceivedPayment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentReceivedPayment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentReceivedPayment query()
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentReceivedPayment whereAddedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentReceivedPayment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentReceivedPayment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentReceivedPayment whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentReceivedPayment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentReceivedPayment whereInvestmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentReceivedPayment whereInvestorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentReceivedPayment whereIsInitialPayment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentReceivedPayment whereReceivedAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentReceivedPayment whereReceivedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentReceivedPayment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentReceivedPayment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentReceivedPayment whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentReceivedPayment withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentReceivedPayment withoutTrashed()
 */
	class InvestmentReceivedPayment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $investment_id
 * @property int $investor_id
 * @property int $investor_referror_id
 * @property string $referral_commission_perc
 * @property string $referral_commission_amount
 * @property string $referral_commission_released_amount
 * @property string $referral_commission_pending_amount
 * @property int $referral_commission_frequency_id
 * @property int $referral_commission_status 0-not released,1-released,2-partially released
 * @property string|null $last_referral_commission_released_date
 * @property string $total_commission_pending
 * @property string $total_commission_released
 * @property string $current_month_commission_released
 * @property string $commission_released_perc
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property int $added_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $payment_terms_id
 * @property-read \App\Models\ReferralCommissionFrequency|null $commissionFrequency
 * @property-read \App\Models\User|null $deletedBy
 * @property-read \App\Models\Investment|null $investment
 * @property-read \App\Models\Investor|null $investor
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\InvestorPayout> $investorPayouts
 * @property-read int|null $investor_payouts_count
 * @property-read \App\Models\PaymentTerms|null $paymentTerm
 * @property-read \App\Models\Investor|null $referrer
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentReferral newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentReferral newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentReferral onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentReferral query()
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentReferral whereAddedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentReferral whereCommissionReleasedPerc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentReferral whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentReferral whereCurrentMonthCommissionReleased($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentReferral whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentReferral whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentReferral whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentReferral whereInvestmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentReferral whereInvestorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentReferral whereInvestorReferrorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentReferral whereLastReferralCommissionReleasedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentReferral wherePaymentTermsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentReferral whereReferralCommissionAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentReferral whereReferralCommissionFrequencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentReferral whereReferralCommissionPendingAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentReferral whereReferralCommissionPerc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentReferral whereReferralCommissionReleasedAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentReferral whereReferralCommissionStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentReferral whereTotalCommissionPending($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentReferral whereTotalCommissionReleased($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentReferral whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentReferral whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentReferral withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|InvestmentReferral withoutTrashed()
 */
	class InvestmentReferral extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $investor_code
 * @property string $investor_name
 * @property string $investor_mobile
 * @property string $investor_email
 * @property string $investor_address
 * @property int $nationality_id
 * @property int $country_of_residence
 * @property int $payment_mode_id
 * @property string $id_number
 * @property string $passport_number
 * @property int|null $referral_id
 * @property int $payout_batch_id
 * @property int|null $profit_release_date
 * @property int $status 0-inactive, 1-active
 * @property int $total_no_of_investments
 * @property string $total_invested_amount
 * @property string $total_profit_received
 * @property string $total_referal_commission
 * @property string $total_referral_commission_received
 * @property int $total_terminated_investments
 * @property int $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property int $is_id_uploaded
 * @property int $is_passport_uploaded
 * @property int $is_supp_doc_uploaded
 * @property int $is_ref_com_cont_uploaded
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string $total_principal_received
 * @property int $investor_relation_id
 * @property string|null $address_line2
 * @property string $city
 * @property string $state
 * @property string|null $postal_code
 * @property string $country_id
 * @property-read \App\Models\Nationality|null $country
 * @property-read \App\Models\Nationality|null $countryOfResidence
 * @property-read \App\Models\User|null $deletedBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Investment> $investments
 * @property-read int|null $investments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\InvestorBank> $investorBanks
 * @property-read int|null $investor_banks_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\InvestorDocument> $investorDocuments
 * @property-read int|null $investor_documents_count
 * @property-read \App\Models\InvestorRelation|null $investor_relation
 * @property-read \App\Models\Nationality|null $nationality
 * @property-read \App\Models\PaymentMode|null $paymentMode
 * @property-read \App\Models\PayoutBatch|null $payoutBatch
 * @property-read \App\Models\InvestorBank|null $primaryBank
 * @property-read Investor|null $referral
 * @method static \Illuminate\Database\Eloquent\Builder|Investor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Investor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Investor onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Investor query()
 * @method static \Illuminate\Database\Eloquent\Builder|Investor whereAddressLine2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investor whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investor whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investor whereCountryOfResidence($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investor whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investor whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investor whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investor whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investor whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investor whereIdNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investor whereInvestorAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investor whereInvestorCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investor whereInvestorEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investor whereInvestorMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investor whereInvestorName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investor whereInvestorRelationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investor whereIsIdUploaded($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investor whereIsPassportUploaded($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investor whereIsRefComContUploaded($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investor whereIsSuppDocUploaded($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investor whereNationalityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investor wherePassportNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investor wherePaymentModeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investor wherePayoutBatchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investor wherePostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investor whereProfitReleaseDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investor whereReferralId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investor whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investor whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investor whereTotalInvestedAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investor whereTotalNoOfInvestments($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investor whereTotalPrincipalReceived($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investor whereTotalProfitReceived($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investor whereTotalReferalCommission($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investor whereTotalReferralCommissionReceived($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investor whereTotalTerminatedInvestments($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investor whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investor whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Investor withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Investor withoutTrashed()
 */
	class Investor extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $investor_id
 * @property string $investor_beneficiary
 * @property string $investor_bank_name
 * @property string $investor_iban
 * @property int $is_primary
 * @property int $status
 * @property int $added_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $banking_region 1-local, 2-international
 * @property-read \App\Models\User|null $deletedBy
 * @property-read \App\Models\Investor|null $investor
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorBank newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorBank newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorBank onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorBank query()
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorBank whereAddedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorBank whereBankingRegion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorBank whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorBank whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorBank whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorBank whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorBank whereInvestorBankName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorBank whereInvestorBeneficiary($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorBank whereInvestorIban($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorBank whereInvestorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorBank whereIsPrimary($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorBank whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorBank whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorBank whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorBank withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorBank withoutTrashed()
 */
	class InvestorBank extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $investor_id
 * @property int $document_type_id
 * @property string $document_name
 * @property string $document_path
 * @property int $added_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $deletedBy
 * @property-read \App\Models\DocumentType|null $documentType
 * @property-read \App\Models\Investor|null $investor
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorDocument newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorDocument newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorDocument onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorDocument query()
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorDocument whereAddedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorDocument whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorDocument whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorDocument whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorDocument whereDocumentName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorDocument whereDocumentPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorDocument whereDocumentTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorDocument whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorDocument whereInvestorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorDocument whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorDocument whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorDocument withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorDocument withoutTrashed()
 */
	class InvestorDocument extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $message_setting_id
 * @property int $investor_id
 * @property int|null $investment_id
 * @property string $investor_mobile
 * @property string $investor_message_body
 * @property int $send_status
 * @property string $api_return
 * @property int $send_by
 * @property string $send_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorMessage query()
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorMessage whereApiReturn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorMessage whereInvestmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorMessage whereInvestorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorMessage whereInvestorMessageBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorMessage whereInvestorMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorMessage whereMessageSettingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorMessage whereSendAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorMessage whereSendBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorMessage whereSendStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorMessage whereUpdatedAt($value)
 */
	class InvestorMessage extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $payout_id
 * @property int $investor_id
 * @property string $amount_paid
 * @property string $paid_date
 * @property int $paid_mode_id
 * @property int|null $paid_bank
 * @property string|null $paid_cheque_number
 * @property string|null $payment_remarks
 * @property int $paid_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $investment_id
 * @property int|null $paid_company_id
 * @property-read \App\Models\User|null $deletedBy
 * @property-read \App\Models\Investment|null $investment
 * @property-read \App\Models\Investor|null $investor
 * @property-read \App\Models\InvestorPayout|null $investorPayout
 * @property-read \App\Models\Bank|null $paidBank
 * @property-read \App\Models\User|null $paidBy
 * @property-read \App\Models\PaymentMode|null $paymentMode
 * @property-read \App\Models\User|null $updatedBy
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorPaymentDistribution newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorPaymentDistribution newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorPaymentDistribution onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorPaymentDistribution query()
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorPaymentDistribution whereAmountPaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorPaymentDistribution whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorPaymentDistribution whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorPaymentDistribution whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorPaymentDistribution whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorPaymentDistribution whereInvestmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorPaymentDistribution whereInvestorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorPaymentDistribution wherePaidBank($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorPaymentDistribution wherePaidBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorPaymentDistribution wherePaidChequeNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorPaymentDistribution wherePaidCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorPaymentDistribution wherePaidDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorPaymentDistribution wherePaidModeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorPaymentDistribution wherePaymentRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorPaymentDistribution wherePayoutId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorPaymentDistribution whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorPaymentDistribution whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorPaymentDistribution withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorPaymentDistribution withoutTrashed()
 */
	class InvestorPaymentDistribution extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $investment_id
 * @property int $investor_id receiver
 * @property int $payout_type 1-profit, 2-commission, 3-principal
 * @property int|null $payout_reference_id type commission - referal table id
 * @property string $payout_release_month
 * @property string $payout_amount
 * @property string $amount_paid
 * @property string $amount_pending
 * @property int $is_processed
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $deletedBy
 * @property-read \App\Models\Investment|null $investment
 * @property-read \App\Models\InvestmentReferral|null $investmentReferral
 * @property-read \App\Models\Investor|null $investor
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\InvestorPaymentDistribution> $investorPayoutDistribution
 * @property-read int|null $investor_payout_distribution_count
 * @property-read \App\Models\Investor|null $investorReference
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorPayout newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorPayout newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorPayout onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorPayout query()
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorPayout whereAmountPaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorPayout whereAmountPending($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorPayout whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorPayout whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorPayout whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorPayout whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorPayout whereInvestmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorPayout whereInvestorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorPayout whereIsProcessed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorPayout wherePayoutAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorPayout wherePayoutReferenceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorPayout wherePayoutReleaseMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorPayout wherePayoutType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorPayout whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorPayout whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorPayout withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorPayout withoutTrashed()
 */
	class InvestorPayout extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $relation_name
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorRelation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorRelation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorRelation query()
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorRelation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorRelation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorRelation whereRelationName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorRelation whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvestorRelation whereUpdatedAt($value)
 */
	class InvestorRelation extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $company_id
 * @property int $area_id
 * @property string $locality_code
 * @property string $locality_name
 * @property int $added_by
 * @property int|null $updated_by
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Area|null $area
 * @property-read \App\Models\Company|null $company
 * @property-write mixed $added_date
 * @method static \Illuminate\Database\Eloquent\Builder|Locality newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Locality newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Locality onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Locality query()
 * @method static \Illuminate\Database\Eloquent\Builder|Locality whereAddedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Locality whereAreaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Locality whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Locality whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Locality whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Locality whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Locality whereLocalityCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Locality whereLocalityName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Locality whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Locality whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Locality whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Locality withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Locality withoutTrashed()
 * @method static \Database\Factories\LocalityFactory factory($count = null, $state = [])
 * @mixin \Eloquent
 * @property int|null $deleted_by
 * @property-read \App\Models\User|null $addedBy
 * @property-read \App\Models\User|null $deletedBy
 * @property-read \App\Models\User|null $updatedBy
 * @method static \Illuminate\Database\Eloquent\Builder|Locality whereDeletedBy($value)
 */
	class Locality extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $message_type 1-invitation, 2- profit release
 * @property string $message_body
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|MessageSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MessageSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MessageSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|MessageSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageSetting whereMessageBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageSetting whereMessageType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageSetting whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageSetting whereUpdatedAt($value)
 */
	class MessageSetting extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $company_id
 * @property string $nationality_code
 * @property string $nationality_name
 * @property string $nationality_short_code
 * @property int|null $added_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Company|null $company
 * @property-read \App\Models\User|null $deletedBy
 * @method static \Database\Factories\NationalityFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Nationality newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Nationality newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Nationality onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Nationality query()
 * @method static \Illuminate\Database\Eloquent\Builder|Nationality whereAddedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Nationality whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Nationality whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Nationality whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Nationality whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Nationality whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Nationality whereNationalityCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Nationality whereNationalityName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Nationality whereNationalityShortCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Nationality whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Nationality whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Nationality whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Nationality withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Nationality withoutTrashed()
 */
	class Nationality extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $company_id
 * @property string $payment_mode_code
 * @property string $payment_mode_name
 * @property string $payment_mode_short_code
 * @property int|null $added_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AgreementPaymentDetail> $agreementPaymentdetails
 * @property-read int|null $agreement_paymentdetails_count
 * @property-read \App\Models\Company|null $company
 * @property-read \App\Models\User|null $deletedBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ContractPaymentDetail> $paymentDetails
 * @property-read int|null $payment_details_count
 * @method static \Database\Factories\PaymentModeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMode onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMode query()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMode whereAddedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMode whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMode whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMode whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMode whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMode wherePaymentModeCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMode wherePaymentModeName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMode wherePaymentModeShortCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMode whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMode whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMode whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMode withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMode withoutTrashed()
 */
	class PaymentMode extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $term_name
 * @property int $status 1-Acive, 0-Inactive
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTerms newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTerms newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTerms query()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTerms whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTerms whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTerms whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTerms whereTermName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTerms whereUpdatedAt($value)
 */
	class PaymentTerms extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $batch_name
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|PayoutBatch newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PayoutBatch newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PayoutBatch query()
 * @method static \Illuminate\Database\Eloquent\Builder|PayoutBatch whereBatchName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PayoutBatch whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PayoutBatch whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PayoutBatch whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PayoutBatch whereUpdatedAt($value)
 */
	class PayoutBatch extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $permission_name
 * @property int|null $parent_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Permission> $children
 * @property-read int|null $children_count
 * @property-read Permission|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|Permission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Permission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Permission query()
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission wherePermissionName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereUpdatedAt($value)
 */
	class Permission extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $profit_interval_name
 * @property int $no_of_installments
 * @property int $interval
 * @property int $status 1-Acive, 0-Inactive
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ProfitInterval newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProfitInterval newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProfitInterval query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProfitInterval whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProfitInterval whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProfitInterval whereInterval($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProfitInterval whereNoOfInstallments($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProfitInterval whereProfitIntervalName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProfitInterval whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProfitInterval whereUpdatedAt($value)
 */
	class ProfitInterval extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $company_id
 * @property int $area_id
 * @property int $locality_id
 * @property int $property_type_id
 * @property string $property_code
 * @property string $property_name
 * @property string|null $property_size
 * @property int|null $property_size_unit
 * @property string $plot_no
 * @property int $added_by
 * @property int|null $updated_by
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Area|null $area
 * @property-read \App\Models\Company|null $company
 * @property-read \App\Models\Locality|null $locality
 * @property-read \App\Models\PropertySizeUnit|null $propertySizeUnit
 * @property-read \App\Models\PropertyType|null $propertyType
 * @method static \Illuminate\Database\Eloquent\Builder|Property newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Property newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Property onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Property query()
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereAddedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereAreaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereLocalityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property wherePlotNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property wherePropertyCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property wherePropertyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property wherePropertySize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property wherePropertySizeUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property wherePropertyTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Property withoutTrashed()
 * @method static \Database\Factories\PropertyFactory factory($count = null, $state = [])
 * @mixin \Eloquent
 * @property string|null $latitude
 * @property string|null $longitude
 * @property string|null $address
 * @property string|null $location
 * @property string|null $remarks
 * @property int|null $deleted_by
 * @property string|null $makani_number
 * @property-read \App\Models\User|null $addedBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Contract> $contracts
 * @property-read int|null $contracts_count
 * @property-read \App\Models\User|null $deletedBy
 * @property-read \App\Models\User|null $updatedBy
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereMakaniNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereRemarks($value)
 */
	class Property extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $unit_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|PropertySizeUnit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PropertySizeUnit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PropertySizeUnit query()
 * @method static \Illuminate\Database\Eloquent\Builder|PropertySizeUnit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PropertySizeUnit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PropertySizeUnit whereUnitName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PropertySizeUnit whereUpdatedAt($value)
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|PropertySizeUnit whereDeletedAt($value)
 * @mixin \Eloquent
 */
	class PropertySizeUnit extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $company_id
 * @property string $property_type_code
 * @property string $property_type
 * @property int $added_by
 * @property int|null $updated_by
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Company|null $company
 * @method static \Illuminate\Database\Eloquent\Builder|PropertyType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PropertyType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PropertyType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PropertyType query()
 * @method static \Illuminate\Database\Eloquent\Builder|PropertyType whereAddedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PropertyType whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PropertyType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PropertyType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PropertyType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PropertyType wherePropertyType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PropertyType wherePropertyTypeCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PropertyType whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PropertyType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PropertyType whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PropertyType withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PropertyType withoutTrashed()
 * @method static \Database\Factories\PropertyTypeFactory factory($count = null, $state = [])
 * @mixin \Eloquent
 * @property int|null $deleted_by
 * @property-read \App\Models\User|null $deletedBy
 * @method static \Illuminate\Database\Eloquent\Builder|PropertyType whereDeletedBy($value)
 */
	class PropertyType extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $commission_frequency_name
 * @property int $no_of_installments
 * @property int $status 1-Acive, 0-Inactive
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\InvestmentReferral> $investmentReferrals
 * @property-read int|null $investment_referrals_count
 * @method static \Illuminate\Database\Eloquent\Builder|ReferralCommissionFrequency newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ReferralCommissionFrequency newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ReferralCommissionFrequency query()
 * @method static \Illuminate\Database\Eloquent\Builder|ReferralCommissionFrequency whereCommissionFrequencyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReferralCommissionFrequency whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReferralCommissionFrequency whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReferralCommissionFrequency whereNoOfInstallments($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReferralCommissionFrequency whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReferralCommissionFrequency whereUpdatedAt($value)
 */
	class ReferralCommissionFrequency extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $identity_type
 * @property string|null $first_field_name
 * @property string|null $first_field_id
 * @property string|null $first_field_type
 * @property string|null $first_field_label
 * @property string $second_field_name
 * @property string $second_field_id
 * @property string $second_field_type
 * @property string $second_field_label
 * @property bool $show_status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|TenantIdentity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TenantIdentity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TenantIdentity query()
 * @method static \Illuminate\Database\Eloquent\Builder|TenantIdentity whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TenantIdentity whereFirstFieldId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TenantIdentity whereFirstFieldLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TenantIdentity whereFirstFieldName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TenantIdentity whereFirstFieldType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TenantIdentity whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TenantIdentity whereIdentityType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TenantIdentity whereSecondFieldId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TenantIdentity whereSecondFieldLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TenantIdentity whereSecondFieldName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TenantIdentity whereSecondFieldType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TenantIdentity whereShowStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TenantIdentity whereUpdatedAt($value)
 */
	class TenantIdentity extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $agreement_id
 * @property int $agreement_payment_detail_id
 * @property string $invoice_path
 * @property string $invoice_file_name
 * @property int $added_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $deletedBy
 * @method static \Illuminate\Database\Eloquent\Builder|TenantInvoice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TenantInvoice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TenantInvoice onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|TenantInvoice query()
 * @method static \Illuminate\Database\Eloquent\Builder|TenantInvoice whereAddedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TenantInvoice whereAgreementId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TenantInvoice whereAgreementPaymentDetailId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TenantInvoice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TenantInvoice whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TenantInvoice whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TenantInvoice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TenantInvoice whereInvoiceFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TenantInvoice whereInvoicePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TenantInvoice whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TenantInvoice whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TenantInvoice withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|TenantInvoice withoutTrashed()
 */
	class TenantInvoice extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $unit_size_unit
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|UnitSizeUnit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UnitSizeUnit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UnitSizeUnit query()
 * @method static \Illuminate\Database\Eloquent\Builder|UnitSizeUnit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UnitSizeUnit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UnitSizeUnit whereUnitSizeUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UnitSizeUnit whereUpdatedAt($value)
 */
	class UnitSizeUnit extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $unit_status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|UnitStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UnitStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UnitStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|UnitStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UnitStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UnitStatus whereUnitStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UnitStatus whereUpdatedAt($value)
 */
	class UnitStatus extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $unit_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|UnitType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UnitType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UnitType query()
 * @method static \Illuminate\Database\Eloquent\Builder|UnitType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UnitType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UnitType whereUnitType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UnitType whereUpdatedAt($value)
 */
	class UnitType extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $company_id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string|null $phone
 * @property string $username
 * @property string $password
 * @property int $user_type 1:super_admin,2:admin,3:agent
 * @property string|null $agent_code
 * @property string|null $remember_token
 * @property string|null $password_reset_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAgentCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePasswordResetToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUserType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUsername($value)
 * @mixin \Eloquent
 * @property string $user_code
 * @property int $user_type_id
 * @property string|null $profile_photo
 * @property string|null $profile_path
 * @property int|null $added_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Company|null $company
 * @property-read User|null $deletedBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @method static \Illuminate\Database\Eloquent\Builder|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAddedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereProfilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereProfilePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUserCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUserTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User withoutTrashed()
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $permission_id
 * @property int|null $company_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|UserPermission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserPermission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserPermission onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|UserPermission query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserPermission whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPermission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPermission whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPermission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPermission wherePermissionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPermission whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPermission whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPermission withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|UserPermission withoutTrashed()
 */
	class UserPermission extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $user_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|UserType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserType query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserType whereUserType($value)
 */
	class UserType extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $company_id
 * @property string $vendor_code
 * @property string $vendor_name
 * @property string|null $vendor_phone
 * @property string|null $vendor_email
 * @property string|null $vendor_address
 * @property string|null $accountant_name
 * @property string|null $accountant_phone
 * @property string|null $accountant_email
 * @property string|null $contact_person
 * @property string|null $contact_person_phone
 * @property string|null $contact_person_email
 * @property int|null $added_by
 * @property int|null $updated_by
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Company|null $company
 * @method static \Database\Factories\VendorFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor query()
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereAccountantEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereAccountantName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereAccountantPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereAddedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereContactPerson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereContactPersonEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereContactPersonPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereVendorAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereVendorCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereVendorEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereVendorName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereVendorPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor withoutTrashed()
 * @mixin \Eloquent
 * @property string|null $landline_number
 * @property string|null $location
 * @property string|null $remarks
 * @property int|null $deleted_by
 * @property int $contract_template_id
 * @property-read \App\Models\User|null $addedBy
 * @property-read \App\Models\VendorContractTemplate|null $contractTemplate
 * @property-read \App\Models\VendorContractTemplate|null $contract_template
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Contract> $contracts
 * @property-read int|null $contracts_count
 * @property-read \App\Models\User|null $deletedBy
 * @property-read \App\Models\User|null $updatedBy
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereContractTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereLandlineNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vendor whereRemarks($value)
 */
	class Vendor extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $template_name
 * @property int $version
 * @property int $status 1=active, 0=inactive
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ContractSignatureDimension> $contract_signature_dimensions
 * @property-read int|null $contract_signature_dimensions_count
 * @property-read \App\Models\Vendor|null $vendors
 * @method static \Illuminate\Database\Eloquent\Builder|VendorContractTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VendorContractTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VendorContractTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder|VendorContractTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VendorContractTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VendorContractTemplate whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VendorContractTemplate whereTemplateName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VendorContractTemplate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VendorContractTemplate whereVersion($value)
 */
	class VendorContractTemplate extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $investor_id
 * @property string $phone
 * @property string|null $template_id
 * @property string|null $variables
 * @property string|null $payload
 * @property string|null $response
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|WhatsappMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WhatsappMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WhatsappMessage query()
 * @method static \Illuminate\Database\Eloquent\Builder|WhatsappMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WhatsappMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WhatsappMessage whereInvestorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WhatsappMessage wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WhatsappMessage wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WhatsappMessage whereResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WhatsappMessage whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WhatsappMessage whereTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WhatsappMessage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WhatsappMessage whereVariables($value)
 */
	class WhatsappMessage extends \Eloquent {}
}

