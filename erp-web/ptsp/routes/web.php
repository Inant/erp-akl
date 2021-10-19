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
    Route::get('/get_inv_by_purchase_id/{id}', 'INV\PenerimaanBarangController@getPenerimaanByPurchaseIdJson');
    Route::get('/print/{id}', 'INV\PenerimaanBarangController@printPenerimaanBarang');
});

Route::group(['middleware' => 'auth', 'prefix' => '/material_request'], function(){
    Route::get('/', 'INV\PengambilanBarangController@index');
    Route::get('/request', 'INV\PengambilanBarangController@request');
    Route::post('/request', 'INV\PengambilanBarangController@requestPost');
    Route::get('/get_material', 'INV\PengambilanBarangController@getAllMaterialJson');
    Route::get('/list', 'INV\PengambilanBarangController@getListPengambilanBarang');
    Route::get('/list_detail/{id}', 'INV\PengambilanBarangController@getListPengambilanBarangDetail');
    Route::get('/material_rab', 'INV\PengambilanBarangController@getAllMaterialRabJson');
});

Route::group(['middleware' => 'auth', 'prefix' => '/pengeluaran_barang'], function(){
    Route::get('/', 'INV\PengambilanBarangController@indexPengeluaran');
    Route::post('/', 'INV\PengambilanBarangController@indexPengeluaranPost');
    Route::get('/list', 'INV\PengambilanBarangController@getListPengeluaranBarangJson');
    Route::get('/print/{id}', 'INV\PengambilanBarangController@printPengeluaranBarang');
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

});

Route::group(['middleware' => 'auth', 'prefix' => '/inventory'], function(){
    Route::get('/', 'INV\TransactionController@index');
    Route::post('/', 'INV\TransactionController@indexPost');
    Route::get('/stock', 'INV\TransactionController@siteStockIndex');
    
    Route::get('/stok_json', 'INV\TransactionController@getStok');

    Route::get('/purchase/all', 'INV\TransactionController@getPurchaseJson');
    Route::get('/purchase/detail/{id}', 'INV\TransactionController@getPurchaseDetJson');
    Route::get('/purchase', 'INV\TransactionController@getPurchase');
    Route::get('/update/{id}', 'INV\TransactionController@isClosed');
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

});


#########################################
// RAB ROUTE
#########################################
Route::group(['middleware' => 'auth', 'prefix' => '/rab'], function(){
    //route page
    Route::get('/', 'RAB\RabController@index');
    Route::get('/add', 'RAB\RabController@add');
    Route::post('/add', 'RAB\RabController@addPost');
    Route::get('/edit/{id}', 'RAB\RabController@edit');
    Route::post('/edit', 'RAB\RabController@editPost');
    Route::post('/save_project_work', 'RAB\RabController@saveProjectWork');
    Route::post('/save_project_worksub', 'RAB\RabController@saveProjectWorkSub');
    Route::post('/save_project_worksub_d', 'RAB\RabController@saveProjectWorkSubD');
    Route::post('/edit_length_work_sub', 'RAB\RabController@editLengthWorkSub');
    Route::post('/edit_project_worksub_d', 'RAB\RabController@saveEditProjectWorkSubD');

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
});

Route::group(['middleware' => 'auth', 'prefix' => '/role'], function(){
    Route::get('/', 'Setting\RoleController@index');
    Route::get('/permission/{id}', 'Setting\RoleController@permission');
    Route::get('/give_access_ajax', 'Setting\RoleController@giveAccessAjax');
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


// Route::group(['middleware' => 'auth'], function(){
//     Route::get('/{any}', function () {
//         return view('theme.default');
//     })->where('any', '.*');
// });

Route::get('/unauthorized', function () {
    return view('unauthorized');
});
