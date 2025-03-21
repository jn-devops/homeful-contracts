<?php

use Homeful\Paymate\Paymate;
use App\Http\Controllers\{ContactVerifiedController,
    Contract\RequirementsController,
    PaymentCollectedController,
    PayloadChecker};
use Homeful\Contacts\Models\Customer;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('contact-verified', ContactVerifiedController::class)->name('contact-verified');

//added for paymate
Route::post('homeful-cashier', function (Request $request) { $response = (new Paymate())->payment_cashier($request) ; return $response;});
Route::post('homeful-online' , function (Request $request) { $response = (new Paymate())->payment_online($request)  ; return $response;});
Route::post('homeful-qrph'   , function (Request $request) { $response = (new Paymate())->payment_qrph($request)    ; return $response;});
Route::post('homeful-wallet' , function (Request $request) { $response = (new Paymate())->payment_wallet($request)  ; return $response;});
Route::post('inquiry' , function (Request $request) { $response = (new Paymate())->payment_inquiry($request)  ; return $response;});
Route::get('generatekey' , function (Request $request) { $response = (new Paymate())->generateKey()  ; return $response;});

Route::post('payment-collected', PaymentCollectedController::class)->name('payment-collected');

Route::post('check-payload', [PayloadChecker::class,'checkPayload'])->name('check-payload');

Route::post('check-mfiles', [PayloadChecker::class,'MFilesProcessorCheck'])->name('check-mfiles');
Route::post('requirement-matrix', [RequirementsController::class,'RequirementMatrix'])->name('requirement-matrix');
Route::post('requirement-matrix-filtered', [RequirementsController::class,'RequirementMatrixFiltered'])->name('requirement-matrix-filtered');

Route::post('get-contact-media/{id}', function($id){
    $customer = Customer::find($id);
    return response()->json($customer->getData());
});
