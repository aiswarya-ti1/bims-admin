<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/login','/addAssociate','/getHash','/addProject','/addFeedback','/addQARating','/changeQAStatus','/file_uploadAgreement','/file_uploadAadhar','/certification','/saveRate','/changeRegStatus','/createLogin','/addAvgRating','/addProductRate','/addServiceRate','/addNewBrand','/addNewSeg','/addNewGroup','/addNewCat','/addNewSubCat','/addCSVFile', '/addNewSerSeg', '/addNewSerCat','/addNewProduct','/addNewAttrb','/addUpdateLog','/addRegisterLog','/addCertifyLog', '/updateAssociate'
, '/addCustomer'  ,'/addLead' , '/saveWork', '/updateLead','/updateWork', '/design_upload', '/addMatEstimation','/updatePMQAWork' , '/addLabEstimation' ,
'/addNewItem', '/addLineItemsCSV','/addLineItem', '/addProductList','/saveCustItems', '/updateProdDetails','/saveProdDetails', '/saveLabDetails', '/saveMatLabDetails',
'/addMatLabLineItem', '/updateAssignees','/saveAssocList', '/saveTenderMatDetails',
 '/saveTenderLabDetails', '/addLabLineItem', '/updateAssocVisit', '/updateWorkStatus',
 '/addPaymentTerms','/saveWorkSchedule','/savePaymentSchedule', '/saveKeys', '/finishWO','/saveTerms','/saveCustLabItems','/saveCustLabMatItems',
 '/addTenderLabItems','/saveWorkSales', '/saveCustKeys', '/addTenderMatLabItems', '/actualAssocVisit', '/signupAssoc', '/saveDetails',
 '/profileUpload','/editProfileDetails','/deletePost','/sendOTP','/postComment', '/saveContractorDetails', '/addServiceDetails',
 '/saveServices','/saveArticle', '/resetPassword' , '/saveFeedback', '/saveQAFeedback', '/postQAFiles','/rejectArticle', '/workStart', 
 '/saveWorkDate', '/savePayDate', '/savePRItemDetails','/savePO', '/addSupplier', '/updateGoodsDate', 
 '/editPRItemDetails','/updateContractorPaymentDetails','/updateSupplierPaymentDetails', '/initiatePayment',
 '/updateMFee','/approveInitPay','/updateContractorInitPaymentDetails','/updateWOIssueDate','/signedWO','/finishAmend','/saveReMeasureDetails','/saveCustTerms',
'/changeLeadStatus','/initiateWO','/editWorkSchDetails','/deleteWorkSch','/deletePaySched','/editPaySchDetails','/updateAmendIssueDate',
'/woSignUp','/woSignedUp','/reEstimateTender','/finishWork','/saveExtraServices','/saveExtraPayment',
'/updateRemeasureIssueDate', '/savePriority','/editPriority','/updateReceivedPayment','/getFilteredRecPay', 
'/updateAmendReason', '/updateRates','/workLost','/generateReport','/getSegCategories','/addSegment','/addService','/addItem','/getFilteredAssocs',
'/addNewService','/biws_SignUp','/biws_SignIn','/biws_addLead','/biws_assocSignIn','/biws_addWork','/changeCustStatus',
'/biws_updateSiteAnalysisDate','/biws_addLabLineItem','/biws_saveAssocList','/biws_saveTenderKeys','/addMaterialAssoc',
'/getProductGroup','/removeSegmentGroup','addNewSegment','/biws_saveTerms','/addNewProdSeg','/addProdGroup','/biws_saveWorkDays',
'/biws_saveTenderLabDetails','/biws_saveCustKeys','/biws_delInTenderKeys','/biws_saveTenderTerms','/biws_saveCustTerms',
'/biws_delInTenderTerm','/biws_finishTender','/biws_editTender','/biws_deleteTender','/biws_pushTenderToCust',
'/sendLetterOfIntrest','/biws_updateReqSiteVisit','/biws_confirmAssoc','/biws_rejectAssoc', '/biws_saveRate',
'/biws_pushBackToPMA','/biws_finishWO','/getEndDate','/sendMail','/biws_addAssociate','/biws_updateAssociate','/biws_AssocSignUp'.
'/biws_CreateToken','/CreateToken','/editMaterialAssoc','/changeActiveStatus','/biws_sendOTP',
'/addTemplateEst','/addTempSchedules','/initializePayment','/completePayment','/addNewUser',
'/markNotification','/woSigned','/updateWOMFee','/saveEmailID','/cust_woSigned','/assoc_woSigned',
'/biws_addCustomer','/biws_resetPassword','/biws_addNewEnquiry'];
}
