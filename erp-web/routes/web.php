<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    // return view('theme.default');
    return redirect('home');
});

Auth::routes();
Route::get('/home', 'HomeController@index')->name('home');
Route::get('/home/kurva_s', 'HomeController@kurvaS');
Route::get('/home/get_order_no/{id}', 'HomeController@getOrderNo');
Route::get('/home/get_req_no/{id}', 'HomeController@getReqNo');
Route::get('/home/get_kurva/{id}', 'HomeController@getKurva');
Route::post('/home/import_excel', 'HomeController@importPost');
Route::get('/home/report_production', 'HomeController@reportProduction');
Route::get('/home/get_estimate_result/{id}', 'HomeController@getEsimateResult');
Route::get('/home/hutang_due_date', 'HomeController@getSupplierDueDate');
Route::get('/home/po_open', 'HomeController@getPOOpen');
Route::get('/home/po_asset_open', 'HomeController@getPOAssetOpen');
Route::post('/home/create-memo', 'HomeController@createMemo');
Route::get('/home/get-my-memo', 'HomeController@getMyMemo');
Route::get('/home/get-my-memo-to', 'HomeController@getMyMemoTo');
Route::get('/home/delete_memo/{id}', 'HomeController@deleteMemo');
Route::get('/home/detail_memo/{id}', 'HomeController@detailMemo');
Route::post('/home/edit-memo', 'HomeController@editMemo');
Route::get('/home/get_project_walk', 'HomeController@getProjectWalk');
Route::get('/home/import_compare', 'HomeController@compareMaterial');
Route::post('/home/import_compare', 'HomeController@importCompare');
Route::get('/home/get_bill_open', 'HomeController@getBillOpen');
Route::get('/home/get_bill_install_open', 'HomeController@getBillInstallOpen');
Route::get('/home/jurnal_amortisasi', 'INV\TransactionController@jurnalAmortisasi');
#########################################
// INV ROUTE
#########################################
Route::group(['middleware' => 'auth', 'prefix' => '/pembelian'], function(){
    Route::get('/', 'INV\PembelianRutinController@index');
    // Route::get('/create', 'INV\PembelianRutinController@create');
    Route::post('/create', 'INV\PembelianRutinController@createPost');
    Route::get('/material_pembelian_rutin', 'INV\PembelianRutinController@getMaterialPembelianRutin');
    Route::get('/supplier', 'INV\PembelianRutinController@getAllSupplier');
    Route::get('/po_canceled', 'INV\PembelianRutinController@getPoCanceled');
    Route::get('/best_prices/{id}', 'INV\PembelianRutinController@getBestPrices');
    Route::get('/material_request_suggestion', 'INV\PembelianRutinController@getMaterialRequestSuggestion');
    Route::post('/import_material', 'INV\PembelianRutinController@importMaterialPost');
    Route::post('/signature_request/{id}', 'INV\PembelianRutinController@formSignaturePO');
    
    
});

Route::group(['middleware' => 'auth', 'prefix' => '/pembelian_khusus'], function(){
    Route::get('/', 'INV\PembelianKhususController@index');
    Route::get('/{id}', 'INV\PembelianKhususController@pembelianKhusus');
    Route::post('/', 'INV\PembelianKhususController@pembelianKhususPost');
    Route::get('/list/all', 'INV\PoController@getPOKhususPembelianKhususJson');

});

Route::group(['middleware' => 'auth', 'prefix' => '/penerimaan_barang'], function(){
    Route::get('/', 'INV\PenerimaanBarangController@index');
    Route::get('/receive/{id}', 'INV\PenerimaanBarangController@receive');
    Route::post('/receive', 'INV\PenerimaanBarangController@receivePost');
    Route::get('/decline/{id}', 'INV\PenerimaanBarangController@decline');
    Route::get('/list', 'INV\PenerimaanBarangController@getAllOpenPurchase');
    Route::get('/detail/{id}', 'INV\PenerimaanBarangController@getPenerimaanDetailJson');
    Route::get('/detail_by_id/{id}', 'INV\PenerimaanBarangController@getPenerimaanDetailJsonById');
    Route::get('/get_inv_by_purchase_id/{id}', 'INV\PenerimaanBarangController@getPenerimaanByPurchaseIdJson');
    Route::get('/print/{id}', 'INV\PenerimaanBarangController@printPenerimaanBarang');
    Route::get('/close_purchase', 'INV\PenerimaanBarangController@closePurchase');
    Route::get('/list_close', 'INV\PenerimaanBarangController@getAllClosePurchase');
    Route::get('/print_else/{id}', 'INV\PenerimaanBarangController@printPenerimaanBarang2');
    Route::get('/cek_po_by_material', 'INV\PenerimaanBarangController@getPOByMaterial');
    Route::get('/atk', 'INV\PenerimaanBarangController@indexATK');
    Route::get('/list_atk', 'INV\PenerimaanBarangController@getAllOpenPurchaseAsset');
    Route::get('/receive_atk/{id}', 'INV\PenerimaanBarangController@receiveATK');
    Route::post('/receive_atk', 'INV\PenerimaanBarangController@receiveATKPost');
    Route::get('/decline_atk/{id}', 'INV\PenerimaanBarangController@declineAtk');
    Route::get('/detail_atk/{id}', 'INV\PenerimaanBarangController@getPenerimaanATKDetailJson');
    Route::get('/detail_atk_by_id/{id}', 'INV\PenerimaanBarangController@getPenerimaanATKDetailJsonById');
    Route::get('/get_inv_by_purchase_asset_id/{id}', 'INV\PenerimaanBarangController@getPenerimaanByPurchaseAssetIdJson');
    Route::get('/print_atk/{id}', 'INV\PenerimaanBarangController@printPenerimaanATK');
    Route::get('/close_purchase_atk', 'INV\PenerimaanBarangController@closePurchaseATK');
    Route::get('/list_close_atk', 'INV\PenerimaanBarangController@getAllClosePurchaseATK');
    Route::get('/print_else_atk/{id}', 'INV\PenerimaanBarangController@printPenerimaanATK2');
});

Route::group(['middleware' => 'auth', 'prefix' => '/material_request'], function(){
    Route::get('/', 'INV\PengambilanBarangController@index');
    Route::get('/request', 'INV\PengambilanBarangController@request');
    Route::post('/request', 'INV\PengambilanBarangController@requestPost');
    Route::get('/get_material', 'INV\PengambilanBarangController@getAllMaterialJson');
    Route::get('/get_material_without_atk', 'INV\PengambilanBarangController@getAllMaterialWithoutATKJson');
    Route::get('/get_material_atk', 'INV\PengambilanBarangController@getAllMaterialATKJson');
    Route::get('/list', 'INV\PengambilanBarangController@getListPengambilanBarang');
    Route::get('/acc_list', 'INV\PengambilanBarangController@getListPengambilanBarangAcc');
    Route::get('/auth_list', 'INV\PengambilanBarangController@getListPengambilanBarangAuth');
    Route::get('/list_pengembalian', 'INV\PengambilanBarangController@getListPengembalianBarang');
    Route::get('/list_detail/{id}', 'INV\PengambilanBarangController@getListPengambilanBarangDetail');
    Route::get('/list_detail_acc/{id}', 'INV\PengambilanBarangController@getListAccPengambilanBarangDetail');
    Route::get('/material_rab', 'INV\PengambilanBarangController@getAllMaterialRabJson');
    Route::get('/material_rab2', 'INV\PengambilanBarangController@getAllMaterialRabJsonByPW');
    Route::get('/material_rab3', 'INV\PengambilanBarangController@getAllMaterialRabJsonByRequestDev');
    Route::get('/returnlist', 'INV\PengambilanBarangController@return_list');
    Route::get('/listreturn', 'INV\PengambilanBarangController@returnListJson');
    Route::get('/listreturn_detail/{id}', 'INV\PengambilanBarangController@getListPengembalianBarangDetail');
    Route::get('/returnadd', 'INV\PengambilanBarangController@return');
    Route::post('/returnadd', 'INV\PengambilanBarangController@returnPost');
    Route::get('/list_sisa/{id}', 'INV\PengambilanBarangController@getSisaMaterialRequestDetail');
    Route::get('/get-product-subs/{id}', 'INV\PengambilanBarangController@getProductSubRab');
    Route::get('/get-product-subs-by-pw/{id}/{limit}', 'INV\PengambilanBarangController@getProductSubByPW');
    Route::get('/get-project-work/{id}', 'INV\PengambilanBarangController@getProjectWorkRab');
    Route::get('/get-request-work/{id}', 'INV\PengambilanBarangController@getRequestWorkByRab');
    Route::get('/project_dev_request/{id}', 'INV\PengambilanBarangController@getRequestWorkDetail');
    Route::get('/re-request', 'INV\PengambilanBarangController@reRequest');
    Route::post('/re-request', 'INV\PengambilanBarangController@reRequestPost');
    Route::get('/get_inv_req_by_rab/{id}', 'INV\PengambilanBarangController@getInvReqByRab');
    Route::get('/material_support', 'INV\PengambilanBarangController@indexMaterialSupport');
    Route::get('/request_material_support', 'INV\PengambilanBarangController@requestSupport');
    Route::post('/request_material_support', 'INV\PengambilanBarangController@saveRequestSupport');
    Route::get('/list_sp', 'INV\PengambilanBarangController@getListPengambilanSP');
    Route::get('/print_label/{id}', 'INV\PengambilanBarangController@printLabel');
    Route::get('/get_inv_id/{id}', 'INV\PengambilanBarangController@getInvRequest');
    Route::get('/get_prod_subs/{reqId}/{rabId}/{limit}', 'INV\PengambilanBarangController@getProductSubByKavling');
    Route::get('/get_label_install_order/{id}', 'INV\PengambilanBarangController@getLabelInstallOrder');
    Route::get('/get_inv_by_label', 'INV\PengambilanBarangController@getInvRequestByLabel');
});

Route::group(['middleware' => 'auth', 'prefix' => '/pengeluaran_barang'], function(){
    Route::get('/', 'INV\PengambilanBarangController@indexPengeluaran');
    Route::get('/form/{id}', 'INV\PengambilanBarangController@pengeluaranForm');
    Route::post('/', 'INV\PengambilanBarangController@indexPengeluaranPost');
    Route::get('/list', 'INV\PengambilanBarangController@getListPengeluaranBarangJson');
    Route::get('/list_pengeluaran_material', 'INV\PengambilanBarangController@getListPengeluaranMaterialJson');
    Route::get('/print/{id}', 'INV\PengambilanBarangController@printPengeluaranBarang');
    Route::get('/laporan-pengeluaran-material', 'INV\PengambilanBarangController@laporanPengeluaranMaterial');
    Route::post('/laporan-pengeluaran-material', 'INV\PengambilanBarangController@laporanPengeluaranMaterial');
    Route::get('/export-laporan-pengeluaran-material', 'INV\PengambilanBarangController@exportLaporanPengeluaranMaterial');
    Route::get('/list-pengeluaran', 'INV\PengambilanBarangController@listPengeluaran');
    Route::get('/hapus/{id}', 'INV\PengambilanBarangController@hapusPengeluaran');
});

Route::group(['middleware' => 'auth', 'prefix' => '/auth_pengambilan_barang'], function(){
    Route::get('/', 'INV\PengambilanBarangController@indexAuthPengambilanBarang');
    Route::post('/', 'INV\PengambilanBarangController@indexAuthPengambilanBarangPost');
    Route::get('/list', 'INV\PengambilanBarangController@getAuthPengambilanBarang');
});

Route::group(['middleware' => 'auth', 'prefix' => '/transfer_stok'], function(){
    Route::get('/', 'INV\TransferStokController@index');
    Route::get('/list', 'INV\TransferStokController@listTransferStokJson');
    Route::get('/list_detail/{id}', 'INV\TransferStokController@listTransferStokDetailJson');
    Route::get('/site', 'INV\TransferStokController@getSiteJson');
    Route::get('/create', 'INV\TransferStokController@create');
    Route::post('/create', 'INV\TransferStokController@createPost');
    Route::get('/warehouse', 'INV\TransferStokController@indexWarehouse');
    Route::get('/create_warehouse', 'INV\TransferStokController@createTSWarehouse');
    Route::post('/create_warehouse', 'INV\TransferStokController@saveTSWarehouse');
    Route::get('/list_warehouse_ts', 'INV\TransferStokController@listTransferStokWarehouseJson');
    Route::get('/detail_tsw/{id}', 'INV\TransferStokController@detailTSWarehouse');
    Route::get('kirim_ts_warehouse/', 'INV\TransferStokController@indexPengirimanTSWarehouse');
    Route::get('/list_kirim_ts_warehouse', 'INV\TransferStokController@pengirimanTSWarehouseJson');
    Route::get('/form_kirim_ts_warehouse/{id}', 'INV\TransferStokController@kirimPengirimanTSWarehouse');
    Route::post('/form_kirim_ts_warehouse', 'INV\TransferStokController@kirimPengirimanTSWarehousePost');
    Route::get('/list_terima_ts_warehouse', 'INV\TransferStokController@penerimaTSWarehouseJson');
    Route::get('terima_ts_warehouse/', 'INV\TransferStokController@indexPenerimaanTSWarehouse');
    Route::get('/form_terima_ts_warehouse/{id}', 'INV\TransferStokController@terimaPenerimaanTSWarehouse');
    Route::post('/form_terima_ts_warehouse', 'INV\TransferStokController@terimaPenerimaanTSWarehousePost');
    Route::get('/surat_jalan_ts/{id}', 'INV\TransferStokController@suratJalanTSWarehouse');
});

Route::group(['middleware' => 'auth', 'prefix' => '/pengiriman_ts'], function(){
    Route::get('/', 'INV\TransferStokController@indexPengiriman');
    Route::get('/kirim/{id}', 'INV\TransferStokController@kirimPengiriman');
    Route::get('/tolak/{id}', 'INV\TransferStokController@tolakPengiriman');
    Route::post('/kirim', 'INV\TransferStokController@kirimPengirimanPost');
});

Route::group(['middleware' => 'auth', 'prefix' => '/penerimaan_ts'], function(){
    Route::get('/', 'INV\TransferStokController@indexPenerimaan');
    Route::get('/terima/{id}', 'INV\TransferStokController@terimaPenerimaan');
    Route::post('/terima', 'INV\TransferStokController@terimaPenerimaanPost');
    Route::get('/print/{id}', 'INV\TransferStokController@printPenerimaan');
});

Route::group(['middleware' => 'auth', 'prefix' => '/po_spesial'], function(){
    Route::get('/', 'INV\PoController@poKhususIndex');
    Route::get('/all', 'INV\PoController@getPOKhususJson');
});

Route::group(['middleware' => 'auth', 'prefix' => '/po_spesial_approval'], function(){
    Route::get('/', 'INV\PoController@poKhususApprovalIndex');
    Route::get('/{id}', 'INV\PoController@poKhususApproval');
    Route::get('/approve/{id}', 'INV\PoController@poKhususApprovalApprove');
    Route::post('/approve', 'INV\PoController@poKhususApprovalApprovePost');
    Route::get('/decline/{id}', 'INV\PoController@poKhususApprovalDecline');
    Route::get('/list/all', 'INV\PoController@getPOKhususApprovalJson');
});

Route::group(['middleware' => 'auth', 'prefix' => '/po_konstruksi'], function(){
    Route::get('/', 'INV\PoController@poKonstruksiIndex');
    Route::get('/all', 'INV\PoController@getPOKonstruksiJson');
    Route::get('/detail/{id}', 'INV\PoController@getPODetailJson');
    Route::get('/print/{id}', 'INV\PoController@printPO');
    Route::get('/atk', 'INV\PoController@poATKIndex');
    Route::get('/atk_all', 'INV\PoController@getPOAtkJson');
    Route::get('/detail_atk/{id}', 'INV\PoController@getPOATKDetailJson');
    Route::get('/print_atk/{id}', 'INV\PoController@printPOATK');
    Route::get('/po_ao', 'INV\PoController@poKonstruksiWithAOIndex');
    Route::get('/all_ao', 'INV\PoController@getPOKonstruksiWithAOJson');
    Route::get('/acc_ao_form/{id}', 'INV\PoController@formAccAO');
    Route::post('/acc_ao_form/signature_holding/{id}', 'INV\PoController@formAccAOSignatureHolding');
    Route::post('/acc_ao_form/signature_supplier/{id}', 'INV\PoController@formAccAOSignatureSupplier');
    Route::post('/approve', 'INV\PoController@approvePoAO');
    Route::get('/po_asset_ao', 'INV\PoController@poAssetWithAOIndex');
    Route::get('/all_ao_asset', 'INV\PoController@getPOAssetWithAOJson');
    Route::get('/acc_ao_asset_form/{id}', 'INV\PoController@formAccAOAsset');
    Route::post('/acc_ao_asset_form/signature_holding/{id}', 'INV\PoController@formAccAOAssetSignatureHolding');
    Route::post('/acc_ao_asset_form/signature_supplier/{id}', 'INV\PoController@formAccAOAssetSignatureSupplier');
    Route::post('/approve_asset', 'INV\PoController@approvePoAOAsset');
    Route::get('/purchase_by_id/{id}', 'INV\PoController@getPurchaseById');
    Route::get('/print_po_ao/{id}', 'INV\PoController@printPOBeforeACC');
    Route::get('/print_po_asset_ao/{id}', 'INV\PoController@printPOAssetBeforeACC');
    Route::get('/print_jasa/{id}', 'INV\PembelianJasaController@printPO');
    Route::post('/saveSignatureDirector/{id}', 'INV\PoController@saveSignatureDirector');
    Route::post('/saveSignatureManager/{id}', 'INV\PoController@saveSignatureManager');
    Route::post('/saveATKSignatureDirector/{id}', 'INV\PoController@saveATKSignatureDirector');
    Route::post('/saveATKSignatureManager/{id}', 'INV\PoController@saveATKSignatureManager');
    Route::post('/signatureRequest', 'INV\PoController@signatureRequest');
    Route::get('/export_po', 'INV\PoController@exportPO');
    Route::get('/export_po_atk', 'INV\PoController@exportPOAsset');
    Route::get('/export_po_service', 'INV\PoController@exportPOService');

});

Route::group(['middleware' => 'auth', 'prefix' => '/inventory'], function(){
    Route::get('/', 'INV\TransactionController@index');
    Route::post('/', 'INV\TransactionController@indexPost');
    Route::get('/stock', 'INV\TransactionController@siteStockIndex');
    
    Route::get('/stok_json', 'INV\TransactionController@getStok');
    Route::get('/get-warehouse', 'INV\TransactionController@getWarehouse');
    Route::get('/stok_rest_json', 'INV\TransactionController@getStokRest');
    Route::get('/stok_json_all', 'INV\TransactionController@getStokSiteAll');

    Route::get('/purchase/all', 'INV\TransactionController@getPurchaseJson');
    Route::get('/purchase/detail/{id}', 'INV\TransactionController@getPurchaseDetJson');
    Route::get('/purchase', 'INV\TransactionController@getPurchase');
    Route::post('/purchase', 'INV\TransactionController@getPurchase');
    Route::post('/export_purchase', 'INV\TransactionController@exportPurchase');
    Route::get('/update/{id}', 'INV\TransactionController@isClosed');
    Route::get('/purchase/cetak/{id}', 'INV\TransactionController@cetakPurchase');
    Route::get('/purchase/multiple-cetak', 'INV\TransactionController@multipleCetakPurchase');

    Route::get('/acc_product', 'INV\TransactionController@listAccProduct');
    Route::get('/json_acc_product', 'INV\TransactionController@jsonAccProduct');
    Route::get('/json_acc_detail/{id}', 'INV\TransactionController@jsonAccProductDetail');
    Route::get('/add_acc_prod', 'INV\TransactionController@addAccProduct');
    Route::post('/add_acc_prod', 'INV\TransactionController@addAccProductPost');
    Route::get('/get-product-subs/{id}', 'INV\TransactionController@getProductSubByRab');
    Route::get('/calc_stock', 'INV\TransactionController@calcStock');
    Route::get('/form_bill_supplier', 'INV\TransactionController@formBillSupplier');
    Route::get('/get_po', 'INV\TransactionController@suggestPurchaseJson');
    Route::get('/get_inv/{id}', 'INV\TransactionController@getInvByPurchaseId');
    Route::get('/get_po_asset', 'INV\TransactionController@suggestPurchaseAssetJson');
    Route::get('/get_inv_asset/{id}', 'INV\TransactionController@getInvByPurchaseAssetId');
    Route::post('/save_bill_supplier', 'INV\TransactionController@saveBillSupplier');
    Route::get('/get_payment_list', 'INV\TransactionController@getPaymentSupplier');
    Route::get('/get_total_inv/{id}/{pd_id}', 'INV\TransactionController@getTotalInvByPurchaseId');
    Route::get('/get_total_inv_asset/{id}/{pd_id}', 'INV\TransactionController@getTotalInvByPurchaseAssetId');
    Route::get('/paid_credit/{id}', 'INV\TransactionController@formPaidCredit');
    Route::post('/save_credit_paid', 'INV\TransactionController@saveCreditPaid');
    Route::get('/detail_payment/{id}', 'INV\TransactionController@detailPayment');
    Route::get('/material_asset', 'INV\TransactionController@materialAssetList');
    Route::get('/list_material_asset', 'INV\TransactionController@jsonMaterialAssetList');
    Route::post('/add_amortisasi', 'INV\TransactionController@addAmortisasiAsset');
    Route::get('/jurnal_amortisasi', 'INV\TransactionController@jurnalAmortisasi');
    Route::get('/get_item_stock', 'INV\TransactionController@getItemStock');
    Route::get('/get_po_service', 'INV\TransactionController@suggestPurchaseServiceJson');
    Route::get('/get_inv_service/{id}', 'INV\TransactionController@getInvByPurchaseServiceId');
    Route::get('/get_total_inv_service/{id}/{pd_id}', 'INV\TransactionController@getTotalInvByPurchaseServiceId');
    Route::get('/stock_d', 'INV\TransactionController@getStokAllD');
    Route::get('/stock_d_json', 'INV\TransactionController@getStokAllDJson');
    Route::get('/debt_list', 'INV\TransactionController@debtList');
    Route::post('/debt_list', 'INV\TransactionController@debtList');
    Route::get('/piutang_list', 'INV\TransactionController@piutangList');
    Route::post('/piutang_list', 'INV\TransactionController@piutangList');
    Route::get('/po_history', 'INV\TransactionController@historyPurchaseBySupplier');
    Route::post('/po_history', 'INV\TransactionController@historyPurchaseBySupplier');
    Route::get('/age_debt', 'INV\TransactionController@ageDebtSupplier');
    Route::post('/age_debt_json', 'INV\TransactionController@ageDebtSupplierJson');
    Route::get('/paid_list_customer', 'INV\TransactionController@paidListCustomer');
    Route::get('/paid_customer', 'INV\TransactionController@paidCustomerBill');
    Route::get('/get_bill_customer/{id}', 'INV\TransactionController@getBillCustomerJson');
    Route::get('/get_bill_d_customer/{id}', 'INV\TransactionController@getBillDetailCustomerJson');
    Route::post('/save_bill_cust', 'INV\TransactionController@saveBillCust');
    Route::get('/get-saldo-lebih-kurang-customer/{id}', 'INV\TransactionController@getSaldoLebihKurangCustomer');
    Route::get('/list_paid_customers', 'INV\TransactionController@listPaidCustomers');
    Route::get('/list_paid_customers_d/{id}', 'INV\TransactionController@listPaidCustomersD');
    Route::get('/paid_list_supplier', 'INV\TransactionController@paidListSupplier');
    Route::get('/paid_supplier', 'INV\TransactionController@paidSupplierBill');
    Route::get('/get_bill_supplier/{id}', 'INV\TransactionController@getBillSupplierJson');
    Route::get('/get_bill_d_supplier/{id}', 'INV\TransactionController@getBillDetailSupplierJson');
    Route::post('/save_paid_supplier', 'INV\TransactionController@savePaidSupplier');
    Route::get('/list_paid_supplier', 'INV\TransactionController@listPaidSuppliers');
    Route::get('/list_paid_supplier_d/{id}', 'INV\TransactionController@listPaidSuppliersD');
    Route::post('/export_debt_list', 'INV\TransactionController@exportDebtList');
    Route::post('/export_piutang_list', 'INV\TransactionController@exportPiutangList');
    Route::post('/export_history_po', 'INV\TransactionController@exportHistoryPurchaseBySupplier');
    Route::post('/export_age_debt', 'INV\TransactionController@exportAgeDebtSupplier');
    Route::get('/piutang_all', 'INV\TransactionController@piutangAll');
    Route::post('/piutang_all', 'INV\TransactionController@piutangAll');
    Route::get('/piutang_all2', 'INV\TransactionController@piutangAll2');
    Route::post('/piutang_all2', 'INV\TransactionController@piutangAll2');
    Route::get('/piutang_saldo', 'INV\TransactionController@piutangSaldoAll');
    Route::post('/piutang_saldo', 'INV\TransactionController@piutangSaldoAll');
    Route::get('/sell_customer', 'INV\TransactionController@sellCustomer');
    Route::post('/sell_customer', 'INV\TransactionController@sellCustomer');
    Route::get('/material_req', 'INV\TransactionController@recaptMaterialRequest');
    Route::get('/get_material_req/{id}', 'INV\TransactionController@getRecaptMaterialRequest');
    Route::get('/debt_supplier', 'INV\TransactionController@debtSupplier');
    Route::post('/debt_supplier', 'INV\TransactionController@debtSupplier');
    Route::get('/bill_supplier_recapt', 'INV\TransactionController@billSupplierRecapt');
    Route::post('/bill_supplier_recapt', 'INV\TransactionController@billSupplierRecapt');
    Route::get('/get_kavling/{id}', 'INV\TransactionController@getKavlingInRab');
    Route::get('/bidd_report', 'INV\TransactionController@biddReport');
    Route::get('/get_bidd_report/{id}', 'INV\TransactionController@getBiddReport');
    Route::get('/calc_price', 'INV\TransactionController@calcPrice');
    Route::post('/calc_price', 'INV\TransactionController@calcPrice');
    Route::get('/history_stock', 'INV\TransactionController@siteStockHistory');
    Route::get('/history_stock_json', 'INV\TransactionController@siteStockHistoryJson');
    Route::post('/export_sell_customer', 'INV\TransactionController@exportSellCustomer');
    Route::post('/export_piutang_all', 'INV\TransactionController@exportPiutangAll');
    Route::post('/export_piutang_all2', 'INV\TransactionController@exportPiutangAll2');
    Route::post('/export_debt_supplier', 'INV\TransactionController@exportDebtSupplier');
    Route::get('/recapt_debt', 'INV\TransactionController@recaptDebt');
    Route::post('/recapt_debt', 'INV\TransactionController@recaptDebt');
    Route::get('/report_stock_in', 'INV\TransactionController@recaptStockIn');
    Route::post('/report_stock_in', 'INV\TransactionController@recaptStockIn');
    Route::post('/export_stock_in', 'INV\TransactionController@exportRecaptStockIn');
    Route::get('/get_surat_jalan', 'INV\TransactionController@suggestSuratJalanJson');
    Route::get('/get_surat_jalan_jasa', 'INV\TransactionController@suggestSuratJalanJasaJson');
    Route::post('/get_detail_surat_jalan', 'INV\TransactionController@detailSuratJalanJson');
    Route::post('/get_detail_surat_jalan_jasa', 'INV\TransactionController@detailSuratJalanJasaJson');
    Route::get('/stock_card', 'INV\TransactionController@stockCard');
    Route::post('/stock_card', 'INV\TransactionController@stockCard');
    Route::get('/export_stock', 'INV\TransactionController@exportStock');
    Route::post('/export_stock_in_tax', 'INV\TransactionController@exportRecaptStockInTax');
    Route::get('/hitung_stok', 'INV\TransactionController@indexHitungStok');
    Route::post('/hitung_stok', 'INV\TransactionController@hitungStok');
    Route::get('/stock_periodic', 'INV\TransactionController@siteStockPeriodic');
    Route::get('/stock_periodic_json', 'INV\TransactionController@siteStockPeriodicJson');
    Route::post('/export_stock_periodic', 'INV\TransactionController@exportStockPeriodic');
    Route::post('/hitung_stok_item', 'INV\TransactionController@hitungStokItem');
    Route::get('/report_item_buy', 'INV\TransactionController@reportItemBuy');
    Route::post('/report_item_buy', 'INV\TransactionController@reportItemBuy');
    Route::post('/export_item_buy', 'INV\TransactionController@exportItemBuy');
    Route::get('/stok_in', 'INV\TransactionController@getStockIn');
    Route::get('/stok_out', 'INV\TransactionController@getStockOut');
    Route::get('/list_detail_paid_cust', 'INV\TransactionController@listDetailPaidCust');
    Route::get('/list_detail_paid_sppl', 'INV\TransactionController@listDetailPaidSppl');
    Route::get('/export_paid_supplier', 'INV\TransactionController@exportPaidSuppliers');
    Route::get('/stock_adjustment', 'INV\TransactionController@stockAdjustmentList');
    Route::get('/stock_adjustment_create', 'INV\TransactionController@stockAdjustmentForm');
    Route::post('/stock_adjustment_store', 'INV\TransactionController@stockAdjustmentStore');
    Route::get('/stock_adjustment_json', 'INV\TransactionController@listStockAdjustmentJson');
    Route::get('/stock_adjustment_detail_json/{id}', 'INV\TransactionController@listStockAdjustmentDetailJson');
    Route::post('/export_recapt_debt', 'INV\TransactionController@exportRecaptDebt');
    Route::get('/hitungStokYear', 'INV\TransactionController@hitungStokYear');
    Route::post('/hitungStokYear', 'INV\TransactionController@hitungStokYear');
    Route::get('/calcPriceYear', 'INV\TransactionController@calcPriceYear');
    Route::post('/calcPriceYear', 'INV\TransactionController@calcPriceYear');
    Route::get('/ringkasan_umur_piutang', 'INV\TransactionController@ringkasanUmurPiutang');
    Route::post('/ringkasan_umur_piutang_json', 'INV\TransactionController@ringkasanUmurPiutangJson');
    Route::post('/export_ringkasan_umur_piutang', 'INV\TransactionController@exportRingkasanUmurPiutang');
});

Route::group(['middleware' => 'auth', 'prefix' => '/stok_opname'], function(){
    Route::get('/', 'INV\StockOpnameController@index');
    Route::get('/create', 'INV\StockOpnameController@create');
    Route::post('/create', 'INV\StockOpnameController@createPost');
    Route::get('/material_by_no', 'INV\StockOpnameController@materialByNoJson');

    Route::get('/list', 'INV\StockOpnameController@listStokOpnameJson');
    Route::get('/list_detail/{id}', 'INV\StockOpnameController@listStokOpnameDetailJson');

    Route::get('/print_stok', 'INV\StockOpnameController@printAllStock');

});

Route::group(['middleware' => 'auth', 'prefix' => '/master_material'], function(){
    Route::get('/', 'INV\MasterController@indexMasterMaterial');
    Route::get('/list', 'INV\MasterController@GetItemJson');
    Route::get('/create', 'INV\MasterController@createItem');
    Route::post('/create', 'INV\MasterController@createItemPost');
    Route::get('/edit/{id}', 'INV\MasterController@editItem');
    Route::post('/edit/{id}', 'INV\MasterController@editItemPost');
    Route::get('/delete/{id}', 'INV\MasterController@deleteItem');
    Route::get('/get_spare_part', 'INV\MasterController@getSparePart');
});

Route::group(['middleware' => 'auth', 'prefix' => '/master_satuan'], function(){
    Route::get('/', 'INV\MasterController@indexMasterSatuan');
    Route::get('/list', 'INV\MasterController@GetUnitJson');
    Route::get('/create', 'INV\MasterController@createUnit');
    Route::post('/create', 'INV\MasterController@createUnitPost');
    Route::get('/edit/{id}', 'INV\MasterController@editUnit');
    Route::post('/edit/{id}', 'INV\MasterController@editUnitPost');
    Route::get('/delete/{id}', 'INV\MasterController@deleteUnit');
});

Route::group(['middleware' => 'auth', 'prefix' => '/master_kavling'], function(){
    Route::get('/', 'INV\MasterController@indexMasterKavling');
    Route::get('/list', 'INV\MasterController@GetKavlingJson');
    Route::get('/create', 'INV\MasterController@createKavling');
    Route::post('/create', 'INV\MasterController@createKavlingPost');
    Route::get('/edit/{id}', 'INV\MasterController@editKavling');
    Route::post('/edit/{id}', 'INV\MasterController@editKavlingPost');
    Route::get('/delete/{id}', 'INV\MasterController@deleteKavling');
    Route::post('/create_json', 'INV\MasterController@createKavlingPostJson');
    Route::get('/get_kavling_by_cust/{id}', 'INV\MasterController@getKavlingByCust');
    Route::get('/get_kavling_by_id/{id}', 'INV\MasterController@getKavlingById');
    Route::post('/update_json', 'INV\MasterController@updateKavlingPostJson');
    Route::post('/create_project_json', 'INV\MasterController@createProjectCustPostJson');
    Route::get('/get_project_cust', 'INV\MasterController@getCustProject');
});

Route::group(['middleware' => 'auth', 'prefix' => '/master_suplier'], function(){
    Route::get('/', 'INV\MasterController@indexMasterSuplier');
    Route::get('/list', 'INV\MasterController@GetSuplierJson');
    Route::get('/create', 'INV\MasterController@createSuplier');
    Route::post('/create', 'INV\MasterController@createSuplierPost');
    Route::get('/edit/{id}', 'INV\MasterController@editSuplier');
    Route::post('/edit/{id}', 'INV\MasterController@editSuplierPost');
    Route::get('/delete/{id}', 'INV\MasterController@deleteSuplier');
});

Route::group(['middleware' => 'auth', 'prefix' => '/alat_kerja_request'], function(){
    Route::get('/', 'INV\PengambilanAlatKerjaController@index');
    Route::get('/list', 'INV\PengambilanAlatKerjaController@getListPengambilanBarang');
    Route::get('/list_detail/{id}', 'INV\PengambilanAlatKerjaController@getListPengambilanBarangDetail');
    Route::get('/request', 'INV\PengambilanAlatKerjaController@request');
    Route::post('/request', 'INV\PengambilanAlatKerjaController@requestPost');
    Route::get('/get_material', 'INV\PengambilanBarangController@getAllMaterialJson');
    Route::get('/material_rab', 'INV\PengambilanBarangController@getAllMaterialRabJson');
});

Route::group(['middleware' => 'auth', 'prefix' => '/auth_alat_kerja'], function(){
    Route::get('/', 'INV\PengambilanAlatKerjaController@indexAuthPengambilanBarang');
    Route::post('/', 'INV\PengambilanAlatKerjaController@indexAuthPengambilanBarangPost');
    Route::get('/list', 'INV\PengambilanAlatKerjaController@getAuthPengambilanBarang');
});

Route::group(['middleware' => 'auth', 'prefix' => '/pengeluaran_alat_kerja'], function(){
    Route::get('/', 'INV\PengambilanAlatKerjaController@indexPengeluaran');
    Route::post('/', 'INV\PengambilanAlatKerjaController@indexPengeluaranPost');
    Route::get('/list', 'INV\PengambilanAlatKerjaController@getListPengeluaranBarangJson');
    Route::get('/print/{id}', 'INV\PengambilanAlatKerjaController@printPengeluaranBarang');
});

Route::group(['middleware' => 'auth', 'prefix' => '/penjualan_keluar'], function(){
    Route::get('/', 'INV\PenjualanKeluarController@index');
    Route::get('/create', 'INV\PenjualanKeluarController@create');
    Route::post('/create', 'INV\PenjualanKeluarController@createPost');
    Route::get('/list', 'INV\PenjualanKeluarController@listPenjualanKeluarJson');
    Route::get('/detail/{id}', 'INV\PenjualanKeluarController@listPenjualanKeluarDetailJson');
    Route::get('/kirim/{id}', 'INV\PenjualanKeluarController@send');
    Route::get('/pengajuan_detail/{id}', 'INV\PenjualanKeluarController@pengajuanInvSaleDetail');
    Route::post('/save_kirim', 'INV\PenjualanKeluarController@saveSend');
    Route::get('/paid', 'INV\PenjualanKeluarController@listPembayaranPenjualanKeluar');
    Route::get('/paid_form', 'INV\PenjualanKeluarController@formPembayaranPenjualanKeluar');
    Route::get('/get_bill/{id}', 'INV\PenjualanKeluarController@getBill');
    Route::post('/save_bill', 'INV\PenjualanKeluarController@saveBill');
    Route::get('/paid_list', 'INV\PenjualanKeluarController@listPaidSellItem');
    Route::get('/paid_all_detail', 'INV\PenjualanKeluarController@listDetailPaidSellItem');
    Route::get('/paid_list_detail/{id}', 'INV\PenjualanKeluarController@listPaidSellItemD');
    Route::get('/print_sppjb/{id}', 'INV\PenjualanKeluarController@printSPPJB');
    Route::get('/print_surat_jalan/{id}', 'INV\PenjualanKeluarController@printSuratJalan');

});


#########################################
// RAB ROUTE
#########################################
Route::group(['middleware' => 'auth', 'prefix' => '/rab'], function(){
    //route page
    Route::get('/', 'RAB\RabController@index');
    Route::get('/json', 'RAB\RabController@json');
    Route::get('/add', 'RAB\RabController@add');
    Route::post('/add', 'RAB\RabController@addPost');
    Route::get('/edit/{id}', 'RAB\RabController@edit');
    Route::post('/edit', 'RAB\RabController@editPost');
    Route::post('/save_project_work', 'RAB\RabController@saveProjectWork');
    Route::post('/edit_project_work', 'RAB\RabController@editProjectWork');
    Route::post('/save_project_worksub', 'RAB\RabController@saveProjectWorkSub');
    Route::post('/save_project_worksub_d', 'RAB\RabController@saveProjectWorkSubD');
    Route::post('/edit_length_work_sub', 'RAB\RabController@editLengthWorkSub');
    Route::post('/edit_project_worksub_d', 'RAB\RabController@saveEditProjectWorkSubD');
    Route::post('/update_project_worksub', 'RAB\RabController@updateProjectWorkSub');
    Route::get('/final_prod/{id}', 'RAB\RabController@finalProd');

    //route json
    Route::get('/get_site', 'RAB\RabController@getSiteNameJson');
    Route::get('/get_project', 'RAB\RabController@getProjectNameJson');
    Route::get('/get_rab_by_project_id', 'RAB\RabController@getListRabByProjectIdJson');
    Route::get('/get_project_work_by_rab_id/{id}', 'RAB\RabController@getProjectWorkByRabIdJson');
    Route::get('/get_all_m_unit', 'RAB\RabController@getAllMUnit');
    Route::get('/get_all_m_item', 'RAB\RabController@getAllMItem');
    Route::get('/get_category', 'RAB\RabController@getCategory');
    Route::get('/get_material_by_category', 'RAB\RabController@getMaterialByCategory');
    Route::get('/get_work_subs/{id}', 'RAB\RabController@getProjectWorkSubs');
    Route::get('/get_project_worksub_d_by_id/{id}', 'RAB\RabController@getProjectWorkSubDsById');
    Route::get('/get_product_order', 'RAB\RabController@getOrderProduct');
    Route::get('/get_project_by_order_id', 'RAB\RabController@getProjectByIdOrder');
    Route::get('/get_rab_by_order_id', 'RAB\RabController@getRabByIdOrder');
    Route::get('/show_all_mterial_group_by_material/{id}', 'RAB\RabController@showAllMaterialGroupByMaterial');
    Route::get('/calculate_all_material/{id}', 'RAB\RabController@calculateAllMaterialByRabId');
    Route::get('/get_kavling_by_order/{id}', 'RAB\RabController@getKavlingByOrder');
    Route::get('/delete_pwsd/{rab_id}/{id}', 'RAB\RabController@deletePwsd');
    Route::get('/get_worksubs', 'RAB\RabController@getWorksubs');
    Route::get('/get_worksub_d/{id}', 'RAB\RabController@getWorksubD');

    Route::post('/import_material/{pws_id}', 'RAB\RabController@importMaterialPost');
    Route::get('/export_material/{id}', 'RAB\RabController@exportMaterial');
    // rekomendasi dari product equivalen
    Route::get('/get_product_equivalent_recomendation/{rab_id}', 'RAB\RabController@getProductEquivalentByRabId');

});

#########################################
// CRM ROUTE
#########################################
Route::group(['middleware' => 'auth','prefix' => '/content'], function(){
    Route::get('/{category}/{id?}', 'CRM\PageContent@getPageContent');
});

Route::group(['middleware' => 'auth','prefix' => '/nuprecord'], function(){
    Route::get('/', 'CRM\NUP\NupRecord@index');
    Route::get('/{subcat}/{mode}/{id?}', 'CRM\NUP\NupRecord@addedit');
    Route::post('/{subcat}/{mode}/{id?}', 'CRM\NUP\NupRecord@addeditSubmit');
});

Route::group(['middleware' => 'auth','prefix' => '/spurecord'], function(){
    Route::get('/', 'CRM\SPU\SpuRecord@index');
    Route::get('/print/{id?}', 'CRM\SPU\SpuRecord@print');
    Route::get('/{subcat}/{mode}/{id?}', 'CRM\SPU\SpuRecord@addedit');
    Route::post('/{subcat}/{mode}/{id?}', 'CRM\SPU\SpuRecord@addeditSubmit');
});

Route::group(['middleware' => 'auth','prefix' => '/ppjbrecord'], function(){
    Route::get('/', 'CRM\PPJB\PpjbRecord@index');
    Route::get('/print/{id?}', 'CRM\PPJB\PpjbRecord@print');
    Route::get('/{subcat}/{mode}/{id?}', 'CRM\PPJB\PpjbRecord@addedit');
    Route::post('/{subcat}/{mode}/{id?}', 'CRM\PPJB\PpjbRecord@addeditSubmit');
});


Route::group(['middleware' => 'auth','prefix' => '/discountrequest'], function(){
    Route::get('/', 'CRM\Discount\DiscountRequest@index');
    Route::get('/{subcat}/{mode}/{id?}', 'CRM\Discount\DiscountRequest@addedit');
    Route::post('/{subcat}/{mode}/{id?}', 'CRM\Discount\DiscountRequest@addeditSubmit');
});
Route::group(['middleware' => 'auth','prefix' => '/specuprequest'], function(){
    Route::get('/', 'CRM\Specup\SpecupRequest@index');
    Route::get('/{subcat}/{mode}/{id?}', 'CRM\Specup\SpecupRequest@addedit');
    Route::post('/{subcat}/{mode}/{id?}', 'CRM\Specup\SpecupRequest@addeditSubmit');
});


Route::group(['middleware' => 'auth','prefix' => '/customer'], function() {
    Route::get('/', 'CRM\Customer\CustomerController@index');
    Route::get('/detail/{id}', 'CRM\Customer\CustomerController@indexDetail');
    Route::get('/list', 'CRM\Customer\CustomerController@getCustomerList');
    Route::get('/dashboard', 'CRM\Customer\CustomerController@dashboard');
    Route::get('/getCustToday', 'CRM\Customer\CustomerController@getCustomerToday');
    Route::get('/getCustMonth', 'CRM\Customer\CustomerController@getCustomerMonth');
    Route::get('/getFollow/{id}/{nama}', 'CRM\Customer\CustomerController@getCountFollowUp');
    
    Route::get('/getCountCust/{nama}', 'CRM\Customer\CustomerController@getCountCust');
    Route::get('/add', 'CRM\Customer\CustomerController@addCustomer');
    Route::post('/save', 'CRM\Customer\CustomerController@saveCustomer');
    Route::get('/sales', 'CRM\Customer\CustomerController@addSales');
    Route::get('/edit/{id}', 'CRM\Customer\CustomerController@editCustomer');
    Route::post('/update/{id}', 'CRM\Customer\CustomerController@updateCustomer');
    Route::get('/json', 'CRM\Customer\CustomerController@getCustomerJson');
    
});

Route::group(['middleware' => 'auth','prefix' => '/followup'], function() {
    Route::get('/', 'CRM\Followup\FollowupController@index');
    Route::get('/cust/{id}', 'CRM\Followup\FollowupController@indexCust');
    Route::get('/detail/{id}', 'CRM\Followup\FollowupController@indexDetail');
    Route::get('/list', 'CRM\Followup\FollowupController@getCustomerFollowupList');
    Route::get('/sales', 'CRM\Followup\FollowupController@followUpList');
});


#########################################
// SETTING ROUTE
#########################################

Route::group(['middleware' => 'auth', 'prefix' => '/menu'], function(){
    Route::get('/menuByMainMenu', 'Setting\MenuController@menuByMainMenu');
    Route::get('/', 'Setting\MenuController@index')->name('menu');
    Route::get('/add', 'Setting\MenuController@add');
    Route::post('/add', 'Setting\MenuController@addPost')->name('menu/add');
    Route::get('/edit/{id}', 'Setting\MenuController@edit');
    Route::post('/edit/{id}', 'Setting\MenuController@editPost');
    Route::get('/delete/{id}', 'Setting\MenuController@delete');
    Route::get('/payment', 'Setting\MenuController@payment_id');
    Route::get('/payment/add', 'Setting\MenuController@payment_add');
    Route::post('/payment/add_payment', 'Setting\MenuController@payment_add_post');
    Route::get('/payment/edit/{id}', 'Setting\MenuController@payment_edit');
    Route::post('/payment/edit_payment', 'Setting\MenuController@payment_edit_post');
    Route::get('/payment/delete/{id}', 'Setting\MenuController@payment_delete');
    Route::get('/givefeed', 'Setting\MenuController@give_feed');
    Route::post('/feed', 'Setting\MenuController@get_feed');
    Route::get('/price', 'Setting\MenuController@price');

    //sales
    Route::get('/sales', 'Setting\MenuController@sales');
    Route::get('/sales/add', 'Setting\MenuController@salesAdd');
    Route::post('/sales/save', 'Setting\MenuController@salesSave');
    Route::get('/sales/edit/{id}', 'Setting\MenuController@salesEdit');
    Route::get('/sales/update', 'Setting\MenuController@salesUpdate');
    Route::get('/sales/delete/{id}', 'Setting\MenuController@salesDelete');

    //salesjson
    Route::get('/getSalesAll', 'Setting\MenuController@getSalesJson');
    Route::get('/getSalesById/{id}', 'Setting\MenuController@getSalesByIdJson');

    //KPR
    Route::get('/simulasi_kpr', 'Setting\MenuController@kpr');
    Route::get('/simulasi_kpr/add', 'Setting\MenuController@kprAdd');
    Route::post('/simulasi_kpr/save', 'Setting\MenuController@kprSave');
    Route::get('/simulasi_kpr/edit/{id}', 'Setting\MenuController@kprEdit');
    Route::get('/simulasi_kpr/update', 'Setting\MenuController@kprUpdate');
    Route::get('/simulasi_kpr/delete/{id}', 'Setting\MenuController@kprDelete');
    Route::get('/getKprJson', 'Setting\MenuController@getKprJson');

    //Gallery
    Route::get('/gambar', 'Setting\MenuController@gambar');
    Route::get('/gambar/add', 'Setting\MenuController@gambarAdd');
    Route::post('/gambar/save', 'Setting\MenuController@gambarSave');
    Route::get('/gambar/delete/{id}', 'Setting\MenuController@gambarDelete');
    Route::get('/getGambarJson', 'Setting\MenuController@getGambarJson');

    Route::get('/gallery', 'Setting\MenuController@gallery');
});


Route::group(['middleware' => 'auth', 'prefix' => '/user'], function(){
    Route::get('/', 'Setting\UserController@index')->name('user');
    Route::get('/add', 'Setting\UserController@add');
    Route::post('/adduser', 'Setting\UserController@adduser');
    Route::get('/edit/{id}', 'Setting\UserController@edit');
    Route::post('/edituser', 'Setting\UserController@edituser');
    Route::post('/edit_pass', 'Setting\UserController@edit_password');
    Route::get('/delete/{id}', 'Setting\UserController@delete');
    Route::get('/cek_code', 'Setting\UserController@cekCode');
    Route::get('/profile/{id}', 'Setting\UserController@profile');
    Route::post('/editprofile', 'Setting\UserController@editprofile');
    Route::post('/edit_profil_password', 'Setting\UserController@edit_profil_password');
});

Route::group(['middleware' => 'auth', 'prefix' => '/role'], function(){
    Route::get('/', 'Setting\RoleController@index');
    Route::get('/permission/{id}', 'Setting\RoleController@permission');
    Route::get('/give_access_ajax', 'Setting\RoleController@giveAccessAjax');
    Route::get('edit/{id}', 'Setting\RoleController@edit');
    Route::post('editrole', 'Setting\RoleController@editRole');
});

Route::group(['middleware' => 'auth', 'prefix' => '/dashboard'], function(){
    // Route::get('/', 'Info\InformationController@index');
    Route::get('/program', 'Info\InformationController@program');
    Route::get('/programList', 'Info\InformationController@programList');
    Route::get('/program/add', 'Info\InformationController@program_add');
    Route::post('/program/add_post', 'Info\InformationController@program_add_post');
    Route::get('/program/edit/{id}', 'Info\InformationController@program_edit');
    Route::post('/program/edit_post', 'Info\InformationController@program_edit_post');
    Route::get('/program/delete/{id}', 'Info\InformationController@program_delete');
});

Route::group(['middleware' => 'auth', 'prefix' => 'akuntansi'], function () {
    Route::get('/', 'Accounting\AkuntanController@index')->name('akuntansi.index');
    Route::get('/createakun', 'Accounting\AkuntanController@createAkun');
    Route::post('/storeakun', 'Accounting\AkuntanController@storeAkun');
    Route::get('/getNoAkun/{id}', 'Accounting\AkuntanController@getNoAkun');
    Route::get('/getLevel/{id}', 'Accounting\AkuntanController@getLevel');
    Route::get('/jurnal', 'Accounting\AkuntanController@jurnal');
    Route::post('/jurnal', 'Accounting\AkuntanController@jurnal');
    Route::get('/neraca', 'Accounting\AkuntanController@balanceSheet');
    Route::post('/neraca', 'Accounting\AkuntanController@balanceSheet');
    Route::get('/createjournal', 'Accounting\AkuntanController@createJournal');
    Route::post('/storejournal', 'Accounting\AkuntanController@storeJurnal');
    Route::get('/profit-loss', 'Accounting\AkuntanController@labaRugi');
    Route::post('/profit-loss', 'Accounting\AkuntanController@labaRugi');
    Route::get('/close-book', 'Accounting\AkuntanController@closeBook');
    Route::post('/close-book', 'Accounting\AkuntanController@closeBook');
    Route::get('/rekap-pc', 'Accounting\AkuntanController@rekapPc');
    Route::post('/rekap-pc', 'Accounting\AkuntanController@rekapPc');
    Route::get('/rekap-transaksi', 'Accounting\AkuntanController@rekapTransaksi');
    Route::post('/rekap-transaksi', 'Accounting\AkuntanController@rekapTransaksi');
    Route::get('/gl', 'Accounting\AkuntanController@generalLedger');
    Route::post('/gl', 'Accounting\AkuntanController@generalLedger');
    Route::get('/detail-gl/{id}', 'Accounting\AkuntanController@detailGL');
    Route::post('/detail-gl/{id}', 'Accounting\AkuntanController@detailGL');
    Route::get('/account_payment', 'Accounting\AkuntanController@accountPayment');
    Route::get('/account_adm', 'Accounting\AkuntanController@accountAdm');
    Route::get('/account_operasional', 'Accounting\AkuntanController@accountOpersional');
    Route::get('/profit-loss-project', 'Accounting\AkuntanController@labaRugiProyek');
    Route::get('/report_pl_proyek/{id}', 'Accounting\AkuntanController@getReportLabaRugiProyek');
    Route::get('/detail-trx-akun/{id}', 'Accounting\AkuntanController@detailJournal');
    Route::get('/detail-inv/{id}', 'Accounting\AkuntanController@detailInv');
    Route::get('/detail-req-dev/{id}', 'Accounting\AkuntanController@detailReqDev');
    Route::get('/hpp-proyek', 'Accounting\AkuntanController@hppProyek');
    Route::post('/report_hpp_proyek', 'Accounting\AkuntanController@getReportHppProyek');
    Route::get('/report_hpp_proyek/{id}', 'Accounting\AkuntanController@reportHppProyek');
    Route::get('/export_journal', 'Accounting\AkuntanController@exportJournal');
    Route::get('/cetak_bukti_kas_masuk/{id}', 'Accounting\AkuntanController@printCashIn');
    Route::get('/cetak_bukti_kas_keluar/{id}', 'Accounting\AkuntanController@printCashOut');
    Route::get('/laba_rugi_all', 'Accounting\AkuntanController@labaRugiAll');
    Route::post('/laba_rugi_all', 'Accounting\AkuntanController@labaRugiAll');
    Route::post('/import_jurnal', 'Accounting\AkuntanController@importJurnal');
    Route::get('/account_asset', 'Accounting\AkuntanController@accountAsset');
    Route::get('/jurnal_balik/{id}', 'Accounting\AkuntanController@journalReturn');
    Route::get('/fixPenerimaanJournal', 'Accounting\AkuntanController@fixPenerimaanJournal');
    Route::get('/export_neraca', 'Accounting\AkuntanController@exportNeraca');
    Route::get('/create_no/{id}/{type}', 'Accounting\AkuntanController@createNo');
    Route::get('/updateNoTrx', 'Accounting\AkuntanController@updateNoTrx');
    Route::get('/kas_report', 'Accounting\AkuntanController@kasReport');
    Route::get('/cash_json', 'Accounting\AkuntanController@cashJson');
    Route::post('/cash_in', 'Accounting\AkuntanController@cashIn');
    Route::post('/cash_out', 'Accounting\AkuntanController@cashOut');
    Route::get('/show_gl_detail', 'Accounting\AkuntanController@showJsonGLDetail');
    Route::get('/neraca_saldo', 'Accounting\AkuntanController@neracaSaldo');
    Route::post('/neraca_saldo', 'Accounting\AkuntanController@neracaSaldo');
    Route::get('/gl_all', 'Accounting\AkuntanController@allGLDetail');
    Route::post('/gl_all', 'Accounting\AkuntanController@allGLDetail');
    Route::get('/cut-saldo', 'Accounting\AkuntanController@cutSaldo');
    Route::post('/cut-saldo', 'Accounting\AkuntanController@cutSaldo');
    Route::get('/export_neraca_saldo', 'Accounting\AkuntanController@exportNeracaSaldo');
    Route::post('/export_gl', 'Accounting\AkuntanController@exportGeneralLedger');
    Route::post('/edit_source_no', 'Accounting\AkuntanController@editSourceNo');
    Route::get('/delete_jurnal/{id}', 'Accounting\AkuntanController@deleteJurnal');
    Route::get('/temp_profit_loss', 'Accounting\AkuntanController@ProfitLossTemp');
    Route::post('/temp_profit_loss', 'Accounting\AkuntanController@ProfitLossTemp');
    Route::get('/temp_profit_loss_kontrak', 'Accounting\AkuntanController@profitLossTempKontrak');
    Route::post('/temp_profit_loss_kontrak', 'Accounting\AkuntanController@profitLossTempKontrak');
    Route::get('/cash_flow', 'Accounting\AkuntanController@cashFlow');
    Route::post('/cash_flow', 'Accounting\AkuntanController@cashFlow');
    Route::get('/recapt_kas', 'Accounting\AkuntanController@recaptKasBank');
    Route::post('/recapt_kas', 'Accounting\AkuntanController@recaptKasBank');
    Route::get('/detail-inv-by-no', 'Accounting\AkuntanController@detailInvBySuratJalan');
    Route::get('/cutSaldoYear', 'Accounting\AkuntanController@cutSaldoYear');
    Route::post('/cutSaldoYear', 'Accounting\AkuntanController@cutSaldoYear');
    Route::post('/export_laba_rugi_all', 'Accounting\AkuntanController@exportLabaRugiAll');
    Route::get('/export_account', 'Accounting\AkuntanController@exportAccount');
    Route::get('/cek_kas/{id}', 'Accounting\AkuntanController@cekCashWarehouse');
        // Route::get('/orders/mark-line-order-as-served/{id}', 'Restaurant\OrderController@markLineOrderAsServed');
        
    Route::get('/export_temp_profit_loss', 'Accounting\AkuntanController@exportProfitLossTemp');
    });

Route::group(['middleware' => 'auth', 'prefix' => 'employee'], function () {
        Route::get('/', 'HRMS\EmployeeController@index');
        Route::get('/add', 'HRMS\EmployeeController@create');
        Route::post('/add_post', 'HRMS\EmployeeController@store');
        Route::get('/edit/{id}', 'HRMS\EmployeeController@edit');
        Route::post('/edit_post', 'HRMS\EmployeeController@update');
        Route::get('/delete/{id}', 'HRMS\EmployeeController@delete');
    });
Route::group(['middleware' => 'auth', 'prefix' => 'position'], function () {
        Route::get('/', 'HRMS\PositionController@index');
        Route::get('/add', 'HRMS\PositionController@create');
        Route::post('/add_post', 'HRMS\PositionController@store');
        Route::get('/edit/{id}', 'HRMS\PositionController@edit');
        Route::post('/edit_post', 'HRMS\PositionController@update');
        Route::get('/delete/{id}', 'HRMS\PositionController@delete');
    });
Route::group(['middleware' => 'auth', 'prefix' => 'salary'], function () {
        Route::get('/', 'HRMS\SalaryController@index');
        Route::get('/add', 'HRMS\SalaryController@create');
        Route::post('/add_post', 'HRMS\SalaryController@store');
        Route::get('/edit/{id}', 'HRMS\SalaryController@edit');
        Route::post('/edit_post', 'HRMS\SalaryController@update');
        Route::get('/delete/{id}', 'HRMS\SalaryController@delete');
        Route::get('/slip/{id}', 'HRMS\SalaryController@slip');
        Route::post('/slip/{id}', 'HRMS\SalaryController@slip');
        Route::post('/cetak', 'HRMS\SalaryController@cetak');
    });
Route::group(['middleware' => 'auth', 'prefix' => 'cuti'], function () {
        Route::get('/', 'HRMS\HRMSController@cuti_list');
        Route::post('/', 'HRMS\HRMSController@cuti_list');
        Route::post('/json', 'HRMS\HRMSController@cutiJson');
        Route::get('/form/{id}/{date}', 'HRMS\HrmsController@cutiForm');
        Route::post('/add_post/{id}/{date}', 'HRMS\HrmsController@cutiForm');
    });
Route::group(['middleware' => 'auth', 'prefix' => 'absensi'], function () {
        Route::get('/', 'HRMS\HrmsController@index');
        Route::get('/json', 'HRMS\HrmsController@json');
        Route::get('/edit/{id}/{date}', 'HRMS\HrmsController@edit');
        Route::post('/update', 'HRMS\HrmsController@update');
        Route::get('/month', 'HRMS\HrmsController@month');
        Route::post('/month', 'HRMS\HrmsController@month');
        Route::get('/import', 'HRMS\HrmsController@import');
        Route::post('/import', 'HRMS\HrmsController@importPost');
    });

    Route::group(['middleware' => 'auth', 'prefix' => '/master_product'], function(){
        Route::get('/', 'INV\MasterController@indexProduct');
        Route::get('/list', 'INV\MasterController@GetProductJson');
        Route::get('/create', 'INV\MasterController@createProduct');
        Route::post('/create', 'INV\MasterController@createProductPost');
        Route::post('/create_json', 'INV\MasterController@createProductPostJson');
        Route::get('/edit/{id}', 'INV\MasterController@editProduct');
        Route::post('/edit/{id}', 'INV\MasterController@editProductPost');
        Route::get('/delete/{id}', 'INV\MasterController@deleteProduct');
    });

    Route::group(['middleware' => 'auth', 'prefix' => '/master_product_equivalent'], function(){
        Route::get('/', 'INV\MasterController@indexProductEquivalent');
        Route::get('/list', 'INV\MasterController@GetProductEquivalentJson');
        Route::get('/create', 'INV\MasterController@createProductEquivalent');
        Route::post('/create', 'INV\MasterController@createProductEquivalentPost');
        Route::get('/edit/{id}', 'INV\MasterController@editProductEquivalent');
        Route::post('/edit/{id}', 'INV\MasterController@editProductEquivalentPost');
        // Route::get('/delete/{id}', 'INV\MasterController@deleteProductEquivalent');
        Route::get('/m_equivalent', 'INV\MasterController@GetMasterEquivalentJson');
    });

    Route::group(['middleware' => 'auth', 'prefix' => '/order'], function(){
        Route::get('/', 'INV\OrderController@index');
        Route::get('/create', 'INV\OrderController@create');
        Route::get('/edit/{id}', 'INV\OrderController@edit');
        Route::get('/suggest_product', 'INV\OrderController@suggestProduct');
        Route::post('/fetch', 'INV\OrderController@fetch');
        Route::get('/get-product/{id}', 'INV\OrderController@getProduct');
        Route::post('/save', 'INV\OrderController@save');
        Route::post('/update', 'INV\OrderController@update');
        Route::get('/list', 'INV\OrderController@GetOrderJson');
        Route::get('/detail/{id}', 'INV\OrderController@GetOrderDetailJson');
        Route::get('/delete/{id}', 'INV\OrderController@deleteOrder');
        Route::get('/bill', 'INV\OrderController@bills');
        Route::get('/list_bill_cust', 'INV\OrderController@GetOrderBillJson');
        Route::get('/bill/{id}', 'INV\OrderController@billForm');
        Route::post('/bill', 'INV\OrderController@saveBillPost');
        Route::post('/bill_other', 'INV\OrderController@saveBillOtherPost');
        Route::get('/delete_bill/{id}', 'INV\OrderController@deleteBill');
        Route::get('/print_bill/{id}', 'INV\OrderController@printBill');
        Route::get('/detail_customer_bill/{id}', 'INV\OrderController@detailCustomerBill');
        Route::post('/save_bill_detail', 'INV\OrderController@saveBillDetailPost');
        Route::get('/print_kwitansi/{id}', 'INV\OrderController@printBillKwitansi');
        Route::get('/create_akun', 'INV\OrderController@createAccount');
        Route::get('/close/{id}', 'INV\OrderController@closeProject');
        Route::post('/import_item_post', 'INV\OrderController@importItemPost');
        Route::get('/get_item_customer/{id}', 'INV\OrderController@getItemCustomer');
        Route::get('/create_install_order', 'INV\OrderController@createOrderInstall');
        Route::get('/detail_order/{id}', 'INV\OrderController@detailOrder');
        Route::post('/save_order_install', 'INV\OrderController@saveOrderInstall');
        Route::get('/list_order_install', 'INV\OrderController@GetOrderInstallJson');
        Route::get('/list_install_bill_cust', 'INV\OrderController@GetInstallOrderBillJson');
        Route::get('/bill_install/{id}', 'INV\OrderController@billInstallForm');
        Route::post('/bill_install', 'INV\OrderController@saveBillInstallPost');
        Route::post('/save_bill_install_detail', 'INV\OrderController@saveBillInstallDetailPost');
        Route::get('/print_bill_install/{id}', 'INV\OrderController@printBillInstall');
        Route::get('/print_kwitansi_install/{id}', 'INV\OrderController@printBillKwitansiInstall');
        Route::get('/detail_install/{id}', 'INV\OrderController@GetOrderInstallDetailJson');
        Route::get('/close_install/{id}', 'INV\OrderController@closeOrderInstall');
        Route::get('/delete_install/{id}', 'INV\OrderController@deleteOrderInstall');
        Route::post('/paid', 'INV\OrderController@savePaidPost');
        Route::get('/get_cust_project_order/{id}', 'INV\OrderController@getCustProjectOrder');
        Route::get('/list_tagihan', 'INV\OrderController@listTagihan');
        Route::get('/get_bill', 'INV\OrderController@getBillOrder');
        Route::get('/get_bill_install', 'INV\OrderController@getBillInstallOrder');
        Route::post('/save_work_temp', 'INV\OrderController@saveWorkTemp');
        Route::get('/get_work_install/{id}', 'INV\OrderController@getWorkInstall');
        Route::get('/get_work_install_order/{id}', 'INV\OrderController@getWorkInstallOrder');
        Route::post('/edit_spjb', 'INV\OrderController@editSPJB');
        Route::post('/edit_spk', 'INV\OrderController@editSPK');
        Route::get('/list_penagihan', 'INV\OrderController@listBillCustomer');
        Route::get('/list_penagihan_json', 'INV\OrderController@listBillCustomerJson');
        Route::post('/report_bill', 'INV\OrderController@reportFollowup');
        Route::get('/list_followup/{id}', 'INV\OrderController@listFollowUpJson');
        Route::get('/print_kwitansi_other/{id}', 'INV\OrderController@printKwitansiOther');
        Route::get('/export', 'INV\OrderController@export');
        Route::get('/export_install', 'INV\OrderController@exportInstall');
        Route::get('/export_bill_order', 'INV\OrderController@exportBillOrder');
        Route::get('/export_bill_install_order', 'INV\OrderController@exportBillInstallOrder');
        Route::get('/print_bill_word/{id}', 'INV\OrderController@printBillWord');
        Route::post('/updateProduct', 'INV\OrderController@updateProduct');
    });
Route::group(['middleware' => 'auth', 'prefix' => '/project_req_dev'], function(){
        Route::get('/', 'INV\ProjectReqDevController@index');
        Route::get('/create', 'INV\ProjectReqDevController@create');
        Route::post('/save', 'INV\ProjectReqDevController@save');
        Route::get('/json', 'INV\ProjectReqDevController@json');
        Route::get('/edit/{id}', 'INV\ProjectReqDevController@edit');
        Route::post('/update', 'INV\ProjectReqDevController@update');
        Route::get('/delete/{id}', 'INV\ProjectReqDevController@delete');
        Route::get('/getRab/{id}', 'INV\ProjectReqDevController@getRab');
        Route::get('/progress', 'INV\ProjectReqDevController@progressList');
        Route::get('/detailJson/{id}', 'INV\ProjectReqDevController@detailJson');
        Route::get('/report/{id}', 'INV\ProjectReqDevController@report');
        Route::get('/saveProduct/{id}', 'INV\ProjectReqDevController@saveProduct');
        Route::get('/get_product_label/{id}', 'INV\ProjectReqDevController@getProductLabel');
        Route::post('/save_product_label/{id}', 'INV\ProjectReqDevController@saveProductLabel');
        Route::get('/trx_product', 'INV\ProjectReqDevController@transactionProductList');
        Route::get('/trx_product_create', 'INV\ProjectReqDevController@createTransactionProduct');
        Route::get('/get_prd_by_cust/{id}', 'INV\ProjectReqDevController@getRequestByCustId');
        Route::get('/get_inv_product_list/{id}', 'INV\ProjectReqDevController@getInvOrders');
        Route::post('/save_trx_product', 'INV\ProjectReqDevController@saveTrxProduct');
        Route::get('/json_trx_product', 'INV\ProjectReqDevController@jsonAccProduct');
        Route::get('/print_surat_jalan/{id}', 'INV\ProjectReqDevController@printTrxProduct');
        Route::get('/get_payment/{id}', 'INV\ProjectReqDevController@getPaymentDetail');
        Route::post('/pay_bill', 'INV\ProjectReqDevController@billPayment');
        Route::get('/list_frame', 'INV\ProjectReqDevController@listFrameProduct');
        Route::get('/list_track_frame', 'INV\ProjectReqDevController@listTrackFrame');
        Route::get('/list_track_frame_dt/{id}', 'INV\ProjectReqDevController@getTrackFrameDetail');
        Route::get('/list_transfer', 'INV\ProjectReqDevController@transferProductJson');
        Route::get('/return_product', 'INV\ProjectReqDevController@returnProductList');
        Route::get('/return_product_form', 'INV\ProjectReqDevController@returnProductForm');
        Route::post('/return_product_label', 'INV\ProjectReqDevController@returnProductLabel');
        Route::get('/json_return_product', 'INV\ProjectReqDevController@jsonReturnProduct');
        Route::get('/kirim_ulang/{id}', 'INV\ProjectReqDevController@resend');
    });
Route::group(['middleware' => 'auth', 'prefix' => '/master_warehouse'], function(){
        Route::get('/', 'INV\MasterController@indexWarehouse');
        Route::get('/list', 'INV\MasterController@GetWarehouseJson');
        Route::get('/create', 'INV\MasterController@createWarehouse');
        Route::post('/create', 'INV\MasterController@createWarehousePost');
        Route::post('/create_json', 'INV\MasterController@createProductPostJson');
        Route::get('/edit/{id}', 'INV\MasterController@editWarehouse');
        Route::post('/edit/{id}', 'INV\MasterController@editWarehousePost');
        Route::get('/delete/{id}', 'INV\MasterController@deleteWarehouse');
        Route::get('/get_warehouse_by_site/{id}', 'INV\MasterController@getWarehouseBySite');
    });
Route::group(['middleware' => 'auth', 'prefix' => '/pembelian_asset'], function(){
        Route::get('/', 'INV\PembelianAssetController@index');
        // Route::get('/create', 'INV\PembelianRutinController@create');
        Route::post('/create', 'INV\PembelianAssetController@createPost');
    });
Route::group(['middleware' => 'auth', 'prefix' => '/payment'], function(){
        Route::get('/', 'CRM\PaymentController@index');
        Route::get('/create', 'CRM\PaymentController@createPaymentProject');
        Route::post('/save', 'CRM\PaymentController@savePaymentProject');
        Route::get('/list_payment_sdm', 'CRM\PaymentController@GetSDMPaymentJson');
        Route::get('/cost', 'CRM\PaymentController@indexPaymentOther');
        Route::get('/create_cost', 'CRM\PaymentController@createPaymentCost');
        Route::post('/save_cost', 'CRM\PaymentController@savePaymentCost');
        Route::get('/prod_weeks', 'CRM\PaymentController@indexPaymentWeek');
        Route::get('/create_prod_weeks', 'CRM\PaymentController@createPaymentWeek');
        Route::post('/save_prod_weeks', 'CRM\PaymentController@savePaymentWeek');
        Route::get('/list_prod_weeks', 'CRM\PaymentController@GetPaymentWeekJson');
        Route::get('/add_prod_week_dt/{id}', 'CRM\PaymentController@createPaymentWeekDT');
        Route::get('/prod_week_detail/{id}', 'CRM\PaymentController@getDetailPaidPerWeek');
        Route::post('/save_prod_week_dt', 'CRM\PaymentController@savePaymentWeekDT');
        Route::get('/cost_other', 'CRM\PaymentController@indexCostOther');
        Route::get('/add_payment_charge', 'CRM\PaymentController@addPaidCostOther');
        Route::post('/save_payment_charge', 'CRM\PaymentController@savePaidCostOther');
        Route::get('/list_payment_charge', 'CRM\PaymentController@GetPaidCostOther');
        Route::get('/install_order', 'CRM\PaymentController@instalOrderList');
        Route::get('/create_install_order', 'CRM\PaymentController@createPaymentInstallOrder');
        Route::post('/save_install_order', 'CRM\PaymentController@saveInstallOrder');
        Route::get('/list_payment_install_order', 'CRM\PaymentController@GetInstallOrderPaymentJson');
        Route::get('/debt', 'CRM\PaymentController@debtList');
        Route::get('/create_debt', 'CRM\PaymentController@createDebt');
        Route::post('/save_debt', 'CRM\PaymentController@saveDebt');
        Route::get('/list_debt', 'CRM\PaymentController@GetDebtJson');
        Route::get('/getBillInstall/{id}', 'CRM\PaymentController@getBillInstall');
        Route::get('/debt_paid/{id}', 'CRM\PaymentController@paidDebt');
        Route::get('/giro', 'CRM\PaymentController@giroList');
        Route::get('/giro_fill', 'CRM\PaymentController@giroListSupplier');
        Route::get('/json_giro', 'CRM\PaymentController@jsonGiro');
        Route::get('/json_giro_supplier', 'CRM\PaymentController@jsonGiroSupplier');
        Route::post('/save_giro_detail', 'CRM\PaymentController@saveGiroDetail');
        Route::post('/save_pengisian_giro', 'CRM\PaymentController@savePengisianGiro');
        Route::post('/save_paid_debt', 'CRM\PaymentController@savePaidDebt');
        Route::get('/paid_debt', 'CRM\PaymentController@paidDebtList');
        Route::get('/form_paid_debt', 'CRM\PaymentController@formPaidDebt');
        Route::get('/get_debt_json/{id}', 'CRM\PaymentController@getNoDebtJson');
        Route::get('/get_debt_detail_json/{id}', 'CRM\PaymentController@getDebtDetailJson');
        Route::post('/save_multiple_paid_debt', 'CRM\PaymentController@saveMultiplePaidDebt');
        Route::get('/list_paid_debt', 'CRM\PaymentController@getPaidDebtJson');
        Route::get('/get_paid_debt_detail/{id}', 'CRM\PaymentController@getPaidDebtDetailJson');
        Route::get('/delete_debt/{id}', 'CRM\PaymentController@deleteDebt');
        Route::post('/export_debt', 'CRM\PaymentController@exportDebt');
        Route::get('/list_detail_all', 'CRM\PaymentController@listDetailAll');
        Route::get('/bill_vendor', 'CRM\PaymentController@billVendor');
        Route::get('/create_bill_vendor', 'CRM\PaymentController@createBillVendor');
        Route::post('/save_bill_vendor', 'CRM\PaymentController@saveBillVendor');
        Route::get('/bill_vendor_json', 'CRM\PaymentController@billVendorJson');
        Route::get('/paid_bill_vendor', 'CRM\PaymentController@paidBillVendor');
        Route::get('/form_paid_bill_vendor', 'CRM\PaymentController@formPaidBillVendor');
        Route::get('/get_bill_vendor_json/{id}', 'CRM\PaymentController@getNoBillVendorJson');
        Route::post('/save_multiple_paid_bill_vendor', 'CRM\PaymentController@saveMultiplePaidBillVendor');
        Route::get('/list_paid_bill_vendor', 'CRM\PaymentController@getPaidBillVendorJson');
        Route::get('/get_paid_bill_vendor/{id}', 'CRM\PaymentController@getPaidBillVendorDetailJson');
        Route::get('/bill_vendor_detail_all', 'CRM\PaymentController@listPaidBillVendorDetailAll');
    });
Route::group(['middleware' => 'auth', 'prefix' => '/pembelian_jasa'], function(){
        Route::get('/', 'INV\PembelianJasaController@index');
        // Route::get('/create', 'INV\PembelianRutinController@create');
        Route::post('/create', 'INV\PembelianJasaController@createPost');
        Route::get('/service', 'INV\PembelianJasaController@poServiceIndex');
        Route::get('/service_all', 'INV\PembelianJasaController@getPOServiceJson');
        Route::get('/detail_service/{id}', 'INV\PembelianJasaController@getPOServiceDetailJson');
        Route::get('/print_service/{id}', 'INV\PembelianJasaController@printPOATK');
        Route::get('/po_service_ao', 'INV\PembelianJasaController@poAssetWithAOIndex');
        Route::get('/acc_ao_service_form/{id}', 'INV\PembelianJasaController@formAccAOService');
        Route::post('/acc_ao_service_form/signature_holding/{id}', 'INV\PembelianJasaController@formAccAOServiceSignatureHolding');
        Route::post('/acc_ao_service_form/signature_supplier/{id}', 'INV\PembelianJasaController@formAccAOServiceSignatureSupplier');
        Route::post('/approve_service', 'INV\PembelianJasaController@approvePoAOService');
        Route::post('/signature_request/{id}', 'INV\PembelianJasaController@formSignaturePO');
        Route::post('/saveSignatureDirector/{id}', 'INV\PembelianJasaController@saveSignatureDirector');
        Route::post('/saveSignatureManager/{id}', 'INV\PembelianJasaController@saveSignatureManager');
    });
Route::group(['middleware' => 'auth', 'prefix' => '/master_worksub'], function(){
        Route::get('/', 'INV\MasterController@indexWorksub');
        Route::get('/list', 'INV\MasterController@GetWorksubJson');
        Route::get('/create', 'INV\MasterController@createWorksub');
        Route::post('/create', 'INV\MasterController@createWorksubPost');
        Route::get('/edit/{id}', 'INV\MasterController@editWorksub');
        Route::post('/edit/{id}', 'INV\MasterController@editWorksubPost');
        Route::get('/delete/{id}', 'INV\MasterController@deleteWorksub');
    });
Route::group(['middleware' => 'auth', 'prefix' => '/penerimaan_service'], function(){
        Route::get('/', 'INV\PenerimaanServiceController@index');
        Route::get('/receive/{id}', 'INV\PenerimaanServiceController@receive');
        Route::post('/receive', 'INV\PenerimaanServiceController@receivePost');
        Route::get('/decline/{id}', 'INV\PenerimaanServiceController@decline');
        Route::get('/list', 'INV\PenerimaanServiceController@getAllOpenPurchase');
        Route::get('/detail/{id}', 'INV\PenerimaanServiceController@getPenerimaanDetailJson');
        Route::get('/get_inv_by_purchase_id/{id}', 'INV\PenerimaanServiceController@getPenerimaanByPurchaseIdJson');
        Route::get('/print/{id}', 'INV\PenerimaanServiceController@printPenerimaanBarang');
        Route::get('/close_purchase', 'INV\PenerimaanServiceController@closePurchase');
        Route::get('/list_close', 'INV\PenerimaanServiceController@getAllClosePurchase');
        Route::get('/print_else/{id}', 'INV\PenerimaanServiceController@printPenerimaanBarang2');
        Route::get('/cek_po_by_material', 'INV\PenerimaanServiceController@getPOByMaterial');
    });
// Route::group(['middleware' => 'auth'], function(){
//     Route::get('/{any}', function () {
//         return view('theme.default');
//     })->where('any', '.*');
// });

Route::get('/unauthorized', function () {
    return view('unauthorized');
});