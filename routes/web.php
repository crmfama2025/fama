<?php

use App\Http\Controllers\AgreementController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\PayableClearingController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\InstallmentController;
use App\Http\Controllers\InvesmentSOAController;
use App\Http\Controllers\InvestmentController;
use App\Http\Controllers\InvestorController;
use App\Http\Controllers\InvestorPaymentDistributionController;
use App\Http\Controllers\LocalityController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\NationalityController;
use App\Http\Controllers\PaymentModeController;
use App\Http\Controllers\PdfSignController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\PropertyTypeController;
use App\Http\Controllers\ReceivablesClearingController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VendorController;
use App\Services\BrevoService;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/', [LoginController::class, 'login'])->name('login');
Route::post('do-login', [LoginController::class, 'doLogin'])->name('do.login');
Route::get('forgot-password', [LoginController::class, 'forgotPassword'])->name('forgot.password');
Route::post('do-forgotpassword', [LoginController::class, 'doForgotPassword'])->name('do.forgot.password');
Route::get('reset-password/{token}', [LoginController::class, 'resetPassword'])->name('reset.password');
Route::post('do-reset-password', [LoginController::class, 'doResetPassword'])->name('do.reset.password');

Route::middleware(['auth'])->group(function () {
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');

    Route::resource('areas', AreaController::class);
    Route::resource('dashboard', DashboardController::class);
    Route::resource('locality', LocalityController::class);
    Route::resource('property_type', PropertyTypeController::class);
    Route::resource('property', PropertyController::class);
    Route::resource('vendors', VendorController::class);
    Route::resource('bank', BankController::class);
    Route::resource('installment', InstallmentController::class);
    Route::resource('payment_mode', PaymentModeController::class);
    Route::resource('nationality', NationalityController::class);
    Route::resource('user', UserController::class);
    Route::resource('company', CompanyController::class);
    Route::resource('contract', ContractController::class);
    Route::resource('agreement', AgreementController::class);
    Route::resource('investment', InvestmentController::class);
    Route::resource('investor', InvestorController::class);
    Route::resource('investorPayout', InvestorPaymentDistributionController::class);
    Route::resource('tenant', TenantController::class);





    Route::post('import-area', [AreaController::class, 'import'])->name('import.area');
    Route::get('area-list', [AreaController::class, 'getAreas'])->name('area.list');
    Route::get('export-areas', [AreaController::class, 'export'])->name('area.export');
    Route::get('get-by-company/{company_id?}', [AreaController::class, 'getByCompany'])->name('area.getbycompany');

    Route::get('export-localities', [LocalityController::class, 'export'])->name('locality.export');
    Route::get('locality-list', [LocalityController::class, 'getLocalities'])->name('locality.list');
    Route::post('import-locality', [LocalityController::class, 'import'])->name('import.locality');

    Route::get('propertyType-list', [PropertyTypeController::class, 'getPropertyType'])->name('property_type.list');
    Route::post('import-property-type', [PropertyTypeController::class, 'importPropertyType'])->name('import.propertytype');
    Route::get('export-property-type', [PropertyTypeController::class, 'exportPropertyType'])->name('propertyType.export');

    Route::get('property-list', [propertyController::class, 'getProperties'])->name('property.list');
    Route::get('export-property', [PropertyController::class, 'exportProperty'])->name('property.export');
    Route::post('import-property', [PropertyController::class, 'importProperty'])->name('import.property');

    Route::get('vendor-list', [VendorController::class, 'getVendors'])->name('vendor.list');
    Route::post('import-vendor', [VendorController::class, 'importVendor'])->name('import.vendor');
    Route::get('export-vendor', [VendorController::class, 'exportVendor'])->name('vendor.export');

    Route::get('bank-list', [BankController::class, 'getBanks'])->name('bank.list');
    Route::get('export-bank', [BankController::class, 'exportBanks'])->name('bank.export');
    Route::post('import-bank', [BankController::class, 'importBank'])->name('import.bank');

    Route::get('installment-list', [InstallmentController::class, 'getInstallments'])->name('installment.list');
    Route::get('export-installment', [InstallmentController::class, 'exportInstallments'])->name('installment.export');
    Route::post('import-installment', [InstallmentController::class, 'importInstallment'])->name('import.installment');

    Route::get('payment-mode-list', [PaymentModeController::class, 'getPaymentModes'])->name('paymentMode.list');
    Route::get('export-payment-mode', [PaymentModeController::class, 'exportPaymentModes'])->name('paymentMode.export');
    Route::post('import-payment-mode', [PaymentModeController::class, 'importPaymentMode'])->name('import.paymentMode');

    Route::get('nationality-list', [NationalityController::class, 'getNationalities'])->name('nationality.list');
    Route::get('export-nationality', [NationalityController::class, 'exportNationalities'])->name('nationality.export');
    Route::post('import-nationality', [NationalityController::class, 'importNationality'])->name('import.nationality');

    Route::get('user-list', [UserController::class, 'getUsers'])->name('user.list');
    Route::get('user-createoredit/{id?}', [UserController::class, 'createOrEdit'])->name('user.createoredit');
    Route::get('export-user', [UserController::class, 'exportUsers'])->name('user.export');
    Route::get('user-profile', [UserController::class, 'userProfile'])->name('user.profile');



    Route::get('company-list', [CompanyController::class, 'getCompanies'])->name('company.list');
    Route::get('export-company', [CompanyController::class, 'exportCompany'])->name('company.export');

    Route::get('contract-list', [ContractController::class, 'getContracts'])->name('contract.list');
    Route::get('contract-documents/{id}', [ContractController::class, 'contract_documents'])->name('contract.documents');
    Route::post('contract-documents/contract-document-upload', [ContractController::class, 'document_upload'])->name('contract.document_upload');
    Route::get('contract-allocated/{id}', [ContractController::class, 'allocatedDetails'])->name('contract.allocated');
    Route::get('export-contract', [ContractController::class, 'exportContract'])->name('contract.export');

    Route::delete('contracts/unit-detail/{id}', [ContractController::class, 'deleteUnitDetail'])
        ->name('contracts.unit-detail.delete');
    Route::delete('contracts/payment-detail/{id}', [ContractController::class, 'deletePaymentDetail'])
        ->name('contracts.payment-detail.delete');
    Route::delete('contracts/payment-receivable/{id}', [ContractController::class, 'deletePaymentReceivable'])
        ->name('contracts.payment-receivable.delete');


    Route::get('agreement-list', [AgreementController::class, 'getAgreements'])->name('agreement.list');
    Route::get('export-agreement', [AgreementController::class, 'exportAgreement'])->name('agreement.export');
    Route::get('print-view/{id}', [AgreementController::class, 'print_view'])->name('agreement.printview');
    Route::get('agreement/{id}/print', [AgreementController::class, 'print'])->name('agreement.print');
    Route::get('agreement-documents/{id}', [AgreementController::class, 'agreementDocuments'])->name('agreement.documents');
    Route::post('agreement-documents-upload/{id}', [AgreementController::class, 'documentUpload'])->name('agreement.documentUpload');
    Route::post('agreement-terminate', [AgreementController::class, 'terminate'])->name('agreement.terminate');
    Route::post('agreement-invoice-upload', [AgreementController::class, 'invoice_upload'])->name('agreement.invoiceUpload');
    Route::post('/agreement-unit/delete/{unitId}', [AgreementController::class, 'delete_unit'])->name('agreement.deleteUnit');
    Route::get('expiring-list', [AgreementController::class, 'getAgreementsExpiring'])->name('agreement.expiring-list');
    Route::get('expiring-lists', [AgreementController::class, 'getAgreementsExpiringTable'])->name('agreement.expiringlisttable');
    Route::get('agreement/{id}/renew', [AgreementController::class, 'renewAgreement'])->name('agreement.renew');

    // renewal
    Route::get('renewal-pending-list', [ContractController::class, 'getRenewalPendingContracts'])->name('contract.renewal_pending_list');
    Route::get('renewal-list', [ContractController::class, 'getRenewalContractsList'])->name('contract.renewal_list');
    Route::get('contract/{id}/renew', [ContractController::class, 'renewContracts'])->name('contract.renew');
    Route::post('contract/{id}/reject-renewal', [ContractController::class, 'rejectRenewal'])->name('contract.reject_renew');


    // projectScope
    Route::get('/export-building-summary/{id}', [ContractController::class, 'exportBuildingSummary']);
    Route::get('/download-summary/{id}/{filename}', [ContractController::class, 'downloadSummary'])
        ->name('contract.downloadSummary');
    Route::get('/download-scope/{id}', [ContractController::class, 'downloadScope']);

    Route::get(
        '/contracts/{id}/terminated-agreement-details',
        [ContractController::class, 'getTerminatedAgreementDetails']
    );
    Route::get('/contracts/{contract}/check-agreement', [ContractController::class, 'checkAgreement']);
    Route::get('contract-approval/{id}', [ContractController::class, 'contractApproval'])->name('contract.approve');
    Route::post('contract-reject', [ContractController::class, 'rejectContract'])->name('contract.reject');
    Route::post('contract-sendcomment', [ContractController::class, 'sendComments'])->name('contract.sendComment');
    Route::get('contract-approval-list', [ContractController::class, 'approvalListContract'])->name('contract.approve.list');
    Route::post('contract-send-for-approval', [ContractController::class, 'sendForApproval'])->name('contract.sendapprove');
    Route::post('approve', [ContractController::class, 'approveContract'])->name('approve');

    // test sign
    Route::get('/signed-pdf/{id}', [PdfSignController::class, 'signedPdf'])->name('sign.contract');

    Route::post('/save-signed-pdf', [PdfSignController::class, 'saveSignedPdf']);
    Route::get('/contracts/{id}/comments', [ContractController::class, 'getComments']);
    Route::get('/contracts/{id}/acknowledgement', [ContractController::class, 'acknowledgement_view'])->name('contracts.acknowledgement');
    Route::get('/contracts/{id}/print-acknowledgement', [ContractController::class, 'acknowledgement_print'])->name('contracts.acknowledgement.print');
    Route::get('/contracts/{id}/release', [ContractController::class, 'release'])->name('contracts.release');



    Route::get('finance/receivable-cheque-clearing', [ReceivablesClearingController::class, 'receivableChequeClearing'])->name('tenant.cheque.clearing');
    Route::get('finance/receivable-cheque-clearing-list', [ReceivablesClearingController::class, 'receivableChequeClearingList'])->name('tenant.cheque.list');
    Route::post('finance/receivable-cheque-clear', [ReceivablesClearingController::class, 'receivableChequeClearSubmit'])->name('receivable.cheque.clear.submit');
    Route::post('finance/bounced_cheque', [ReceivablesClearingController::class, 'receivableChequeBounceSubmit'])->name('receivable.cheque.bounce.submit');
    Route::post('finance/receivables/export', [ReceivablesClearingController::class, 'export'])->name('tanantReceivables.export');

    Route::get('finance/receivable-report', [ReceivablesClearingController::class, 'receivableReport'])->name('finance.receivables.report');
    Route::get('finance/receivable-report-list', [ReceivablesClearingController::class, 'receivableReportList'])->name('tenant.receivables.report.list');
    Route::post('finance/receivable-report-export', [ReceivablesClearingController::class, 'receivableReportExport'])->name('receivableReport.export');

    Route::get('finance/payable-cheque-clearing', [PayableClearingController::class, 'payableChequeClearing'])->name('finance.payable.clearing');
    Route::get('finance/payable-list', [PayableClearingController::class, 'getPayables'])->name('payable.list');
    Route::post('finance/payable-save', [PayableClearingController::class, 'submitPayables'])->name('payable.save');
    Route::post('finance/retun-save', [PayableClearingController::class, 'submitReturns'])->name('return.save');
    Route::get('finance/cleared-list', [PayableClearingController::class, 'crearedList'])->name('cleared.list');
    Route::get('finance/cleared-data', [PayableClearingController::class, 'getClearedData'])->name('cleared.data');
    Route::get('finance/export-payables', [PayableClearingController::class, 'exportPayables'])->name('payables.report.export');
    Route::get('finance/export-payable-pending', [PayableClearingController::class, 'exportPayablePending'])->name('payables.pending.export');


    Route::get('investor-list', [InvestorController::class, 'getInvestors'])->name('investor.list');
    Route::post('investor/add-investor-bank', [InvestorController::class, 'addorUpdateInvestorBank'])->name('investor.bank.save');
    Route::prefix('admin')->group(function () {
        Route::get(
            'investor/export-investors',
            [InvestorController::class, 'exportInvestors']
        )->name('investor.export');
    });
    Route::get('investor/get-investor-bank/{id}', [InvestorController::class, 'getInvestorBankDetails'])->name('investor.bank');


    Route::get('payout/payout-pending-list', [InvestorPaymentDistributionController::class, 'getPayouts'])->name('payout.pending.list');
    Route::get('payout/distributed-report', [InvestorPaymentDistributionController::class, 'distributedReport'])->name('distributed.report');
    Route::get('payout/distributed-list', [InvestorPaymentDistributionController::class, 'getDistributedList'])->name('distributed.list');
    Route::post('payout/payout-distribute-save', [InvestorPaymentDistributionController::class, 'savePayouts'])->name('payout.distribute.save');
    Route::get('payout/export-distribute', [InvestorPaymentDistributionController::class, 'exportDistribute'])->name('payout.report.export');
    Route::get('payout/export-payout-pending', [InvestorPaymentDistributionController::class, 'exportPayoutPending'])->name('payout.pending.export');


    Route::get('investments/investments', [InvestmentController::class, 'getInvestments'])->name('investment.list');
    Route::post('investments/investments', [InvestmentController::class, 'addpendingInvestment'])->name('investment.submit.pending');
    Route::get('investments/export-investment', [InvestmentController::class, 'exportInvestment'])->name('investment.export');
    Route::post('investments/investments/update', [InvestmentController::class, 'updatePendingInvestment'])->name(('investment.submit.pending.update'));
    Route::post('investments/investments/terminate-request', [InvestmentController::class, 'terminateRequestSubmit'])->name(('investment.submit.termination'));

    Route::get('investments/investment-soa', [InvesmentSOAController::class, 'index'])->name('investment-soa.list');
    Route::get('investments/investment-soa/data', [InvesmentSOAController::class, 'getData'])->name('investment-soa.data');


    Route::get('/send-test-template', function () {

        $brevoService = new BrevoService();

        $result = $brevoService->sendEmail(
            [
                ['email' => 'geethufama@gmail.com', 'name' => 'Test User']
            ],
            'Test Stripo Template', // Subject
            'admin.emails.test-email', // Blade template
            ['name' => 'Test User'] // Data for template
        );

        return $result; // Will return "true" if email sent successfully
    });
    Route::get('/preview-email', function () {
        return view('admin.emails.test-email', ['name' => 'Test User']);
    });

    Route::post('/contracts/terminate', [ContractController::class, 'terminate']);

    Route::post('agreement/rent-bifurcation', [AgreementController::class, 'rentBifurcationStore'])->name('rent-bifurcation.store');
    Route::get('investments/referrals', [ReferralController::class, 'referrals'])->name('referrals.index');
    Route::get('investments/referrals-list', [ReferralController::class, 'getReferrals'])->name('referrals.list');
    Route::get('investments/referrals-view/{referral}', [ReferralController::class, 'show'])->name('referrals.show');
    Route::get('referrals/export-referral', [ReferralController::class, 'exportReferral'])->name('referral.export');

    Route::get('user-managePermission/{id?}', [UserController::class, 'managePermission'])->name('user.managePermission');
    Route::post('/user/company-permissions', [UserController::class, 'getCompanyPermissions'])
        ->name('user.company.permissions');
    Route::post(
        '/user/company-permissions/store',
        [UserController::class, 'storeCompanyPermissions']
    )->name('user.company.permissions.store');
    Route::get('/dashboard/filter', [DashboardController::class, 'show'])
        ->name('dashboard.filter');

    Route::get('/expiring_tenant_documents', [AgreementController::class, 'getInvestmentExpiredDocuments'])->name('tenantDocument.expiringList');
    Route::get('/expiring_tenant_documentsData', [AgreementController::class, 'getInvestmentExpiredDocumentslist'])->name('tenantDocument.expiringListdata');
    Route::get('/documentExpiry/export', [AgreementController::class, 'exportDocumentExpiry'])->name('documentexpiry.export');


    Route::get('tenants/list', [TenantController::class, 'list'])->name('tenant.list');
    Route::get('tenants/edit/{id}', [TenantController::class, 'edit'])->name('tenant.edit');
    Route::post('/tenant/remove-owner-documents', [TenantController::class, 'removeOwnerDocuments'])->name('tenant.remove.owner.documents');
    Route::get('export-tenant', [TenantController::class, 'export'])->name('tenant.export');
});




// Route::get('/download-scope/{id}', [ContractController::class, 'downloadScope']);
