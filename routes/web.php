<?php

Auth::routes();

Route::get('/', 'DashboardController@index');

Route::get('/dashboard', 'DashboardController@index')->name('dashboard');

Route::get('donation_page/{id}', 'DashboardController@donationPage');

Route::get('/user', 'DashboardController@user');

Route::get('user/donation_switch', 'DashboardController@donationSwitch');

// Payments
Route::resource('/payment_requests', 'PaymentRequestController');

Route::post('/payment_requests/donate', 'PaymentRequestController@donate');

Route::post('/payment_requests/{id}/pay', 'PaymentRequestController@pay');

Route::get('/payment_requests/{id}/share', 'PaymentRequestController@share');

Route::post('/payment_requests/share_request', 'PaymentRequestController@shareRequest');

Route::get('/payment_requests/{id}/pay_plan/{nrOfDates?}', 'PaymentRequestController@payPlan');

Route::post('/payment_requests/{id}/pay_plan_confirm', 'PaymentRequestController@payPlanConfirm');

Route::get('/payment_requests/{id}/force_pay', 'PaymentRequestController@forcePay');

Route::delete('payment_requests/{id}/remove_received', 'PaymentRequestController@removeReceived');

// Contacts/Groups
Route::post('/contacts/store', 'ContactController@storeGroup');

Route::get('/contacts/{id}/edit_group', 'ContactController@editGroup');

Route::post('/contacts/add_user_to_group', 'ContactController@addUserToGroup');

Route::delete('/contacts/{id}/destroy_group', 'ContactController@destroyGroup');

Route::delete('/contacts/remove_user_from_group', 'ContactController@removeUserFromGroup');

Route::post('/contacts/add_contact', 'ContactController@addContact');

Route::delete('/contacts/remove_contact', 'ContactController@removeContact');

// Bank Accounts
Route::get('/bank_accounts', 'BankAccountController@index')->name('bank_accounts');

Route::get('/bank_accounts/{id}', 'BankAccountController@show');

Route::get('/bank_accounts/{id}/download', 'BankAccountController@downloadBankAccountOverview');

Route::post('/bank_accounts/add_bank_account', 'BankAccountController@addBankAccount');

Route::delete('/bank_accounts/{id}/remove_bank_account', 'BankAccountController@destroy');

// Misc
Route::get('/{any}','DashboardController@index');

Route::get('/testdrive', function() {
    Storage::cloud()->put('test.txt', 'Hello World');
});
