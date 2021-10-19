<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});
$router->group(['prefix' => 'initdb/postgres'], function () use ($router) {


});

$router->group(['prefix' => 'base'], function () use ($router) {
    $router->get('gs/code/{code}',  ['uses' => 'RabController@_GetGSByCode',]);
});

$router->group(['prefix' => 'rab'], function () use ($router) {
    $router->get('base/model',  ['uses' => 'RabController@_GetAllModels',]);
    $router->get('base/model/{model}',  ['uses' => 'RabController@_GetColumnType',]);

    $router->get('base/{model}',  ['uses' => 'RabController@_Get',]);
    $router->get('base/{model}/{id}',  ['uses' => 'RabController@_GetById',]);
    $router->post('base/{model}',  ['uses' => 'RabController@_Create']);
    $router->put('base/{model}/{id}',  ['uses' => 'RabController@_Update']);
    $router->delete('base/{model}/{id}',  ['uses' => 'RabController@_Delete']);

    $router->get('kavling',  ['uses' => 'RabController@getKavlingList',]);
    $router->get('kavling/all',  ['uses' => 'RabController@getKavlingListAll',]);
    $router->get('list',  ['uses' => 'RabController@getRabList',]);
    $router->get('detail',  ['uses' => 'RabController@getDetail',]);
    $router->get('detail/{id}',  ['uses' => 'RabController@getDetailbyRabId',]);
    $router->get('list_by_project_id/{projectId}',  ['uses' => 'RabController@getRabListByProjectId']);
    $router->get('get_by_id/{id}',  ['uses' => 'RabController@getRabById']);
    $router->get('project_work/get_by_rab_id/{rabId}',  ['uses' => 'RabController@getProjectWorkByRabId']);
    $router->get('get_material_pembelian_rutin',  ['uses' => 'RabController@getMaterialPembelianRutin']);
    $router->get('all_material',  ['uses' => 'RabController@getAllMaterial']);
    $router->get('all_material/{id}',  ['uses' => 'RabController@getAllMaterialByRabId']);
    $router->get('material_category_by_type/{type}',  ['uses' => 'RabController@getMaterialCategoryByType']);
    $router->post('material_by_category',  ['uses' => 'RabController@getMaterialByCategory']);
    $router->post('material_by_no',  ['uses' => 'RabController@getMaterialByNo']);

    $router->get('site/get_by_town_id/{townId}',  ['uses' => 'INV\SiteController@getByTownId']);
    $router->get('site/get_site',  ['uses' => 'INV\SiteController@getSite']);
    $router->get('project/get_by_site_id/{siteId}',  ['uses' => 'INV\ProjectController@getBySiteId']);
    $router->get('project_worksubd/{id}',  ['uses' => 'RabController@getProjectWorkSubD']);
});



$router->group(['prefix' => 'inv'], function () use ($router) {
    $router->get('base/{model}',  ['uses' => 'RabController@_Get',]);
    $router->get('base/{model}/{id}',  ['uses' => 'RabController@_GetById',]);
    $router->post('base/{model}',  ['uses' => 'RabController@_Create']);
    $router->put('base/{model}/{id}',  ['uses' => 'RabController@_Update']);
    $router->delete('base/{model}/{id}',  ['uses' => 'RabController@_Delete']);

    $router->get('purchase/{id}',  ['uses' => 'INV\PurchaseController@getPurchaseById']);
    $router->get('po/po_konstruksi',  ['uses' => 'INV\PurchaseController@getPOKonstruksi']);
    $router->get('po/po_khusus',  ['uses' => 'INV\PurchaseController@getPOKhusus']);
    $router->get('po/pembelian_khusus',  ['uses' => 'INV\PurchaseController@getPOKhususPembelianKhusus']);

    $router->get('po/po_khusus_approval',  ['uses' => 'INV\PurchaseController@getPOKhususApproval']);
    $router->get('po/purchase_d_by_purchase_id/{id}',  ['uses' => 'INV\PurchaseController@getPurchaseDByPurchaseId']);
    $router->get('po/all_open',  ['uses' => 'INV\PurchaseController@getAllOpenPurchase']);

    $router->get('purchase_approval/{id}',  ['uses' => 'INV\PurchaseController@getPurchaseApprovalByPurchaseId']);

    $router->get('inv_trx',  ['uses' => 'INV\InvTrxController@getAll']);
    $router->get('inv_trx/{id}',  ['uses' => 'INV\InvTrxController@getById']);
    $router->get('inv_trx/get_by_purchase_id/{id}',  ['uses' => 'INV\InvTrxController@getByPurchaseId']);
    $router->get('inv_trx/get_by_inv_request_id/{id}',  ['uses' => 'INV\InvTrxController@getByInvRequestId']);
    $router->get('stok',  ['uses' => 'INV\InvTrxController@getStokAllSite']);
    $router->get('stok/{id}',  ['uses' => 'INV\InvTrxController@getStokSite']);

    //Tagihan
    $router->get('getPurchase',  ['uses' => 'INV\PurchaseController@getPurchaseAll']);
    $router->get('getPurchase/detail/{id}',  ['uses' => 'INV\PurchaseController@getPurchaseDetail']);

    $router->get('pengambilan_barang',  ['uses' => 'INV\InvTrxController@getListRequestBarang']);
    $router->get('pengambilan_barang_detail/{id}',  ['uses' => 'INV\InvTrxController@getListRequestBarangDetail']);

    $router->get('pengeluaran_barang',  ['uses' => 'INV\InvTrxController@getListPengeluaranBarang']);

    $router->post('best_price',  ['uses' => 'INV\PurchaseController@postBestPrice']);

    $router->get('po_canceled',  ['uses' => 'INV\PurchaseController@getPoCanceled']);

    $router->get('transfer_stok',  ['uses' => 'INV\TransferStokController@getTransferStock']);
    $router->get('transfer_stok_detail/{transferStockId}',  ['uses' => 'INV\TransferStokController@getTransferStockDByTransferStockId']);

    $router->get('stok_opname',  ['uses' => 'INV\TransferStokController@getStockOpname']);
    $router->get('stok_opname_detail/{id}',  ['uses' => 'INV\TransferStokController@getStockOpnameDByStockOpnameId']);

    $router->post('mutasi_stok',  ['uses' => 'INV\InvTrxController@getMutasiStok']);
    $router->post('value_out',  ['uses' => 'INV\InvTrxController@getValueOut']);

    $router->get('penjualan_keluar', ['uses' => 'INV\InvTrxController@getPenjualanKeluarList']);
    $router->get('penjualan_keluar_detail/{id}', ['uses' => 'INV\InvTrxController@getListPenjualanKeluarDetail']);
});

$router->group(['prefix' => 'crm'], function () use ($router) {
    $router->get('base/{model}',  ['uses' => 'Crm@_Get',]);
    $router->get('base/{model}/{id}',  ['uses' => 'Crm@_GetById',]);
    $router->post('base/{model}',  ['uses' => 'Crm@_Create']);
    $router->put('base/{model}/{id}',  ['uses' => 'Crm@_Update']);
    $router->delete('base/{model}/{id}',  ['uses' => 'Crm@_Delete']);

    $router->get('customerdatamain',  ['uses' => 'Crm@getCustomerDataMain']);
    $router->get('customerdatamain/by_sales_id/{id}',  ['uses' => 'Crm@getCustomerDataMainBySalesId']);
    $router->get('customerdata',  ['uses' => 'Crm@getCustomerDataMain']);
    $router->get('customerdata/{id}',  ['uses' => 'Crm@getCustomerDataById']);
    $router->post('customerdata',  ['uses' => 'Crm@saveCustomerData']);
    $router->get('customerdata/lastfollowup/{id}',  ['uses' => 'Crm@getFinishFollowupHistoryByCustId']);

    $router->get('followuphistories/list/cust/{salespersonid}',  ['uses' => 'Crm@getFollowUpHistoriesBySalesId']);
    $router->get('followuphistories/cust/{id}/{salespersonid}',  ['uses' => 'Crm@getFollowUpHistoriesByCustId']);
    $router->get('followuphistories/{id}',  ['uses' => 'Crm@getFollowUpHistoriesById']);
    $router->post('followuphistories[/{mode}]',  ['uses' => 'Crm@saveFollowUpCustomer']);
    $router->get('followuphistories/last/{id}',  ['uses' => 'CRM\FollowupCustomer@getLastFollowUpHistoyResultByCustomerId']);
    $router->delete('deletecustomer/{customerId}', ['uses' => 'Crm@deleteFollowupData']);

    // $router->get('followuphistories/count',  ['uses' => 'CRM\FollowupCustomer@countFollowUp']);

    $router->get('dashboard/schedule/today/{salespersonid}',  ['uses' => 'Crm@countScheduleToday']);
    $router->get('dashboard/schedule/today/{salespersonid}/list',  ['uses' => 'Crm@listScheduleToday']);
    $router->get('dashboard/customer/count/{salespersonid}/{periode}',  ['uses' => 'Crm@countCustBySalesPerson']);
    $router->get('dashboard/customer/prospect/{salespersonid}/{prospectlevel}',  ['uses' => 'Crm@countProspectCustByLevel']);
    $router->get('dashboard/prospect/{salespersonid}',  ['uses' => 'Crm@countAllProspectCust']);
    $router->get('dashboard/spu/{salespersonid}',  ['uses' => 'Crm@countSPUbySalesPerson']);
    $router->get('dashboard/ajb/{salespersonid}',  ['uses' => 'Crm@countAJBbySalesPerson']);

    $router->get('unittrx/{id}',  ['uses' => 'CRM\UnitTransaction@getSaleTrxById']);
    $router->get('unittrx',  ['uses' => 'CRM\UnitTransaction@getSaleTrx']);
    $router->post('unittrx',  ['uses' => 'CRM\UnitTransaction@saveSaleTrx']);
    $router->post('unittrx/validate/{id}',  ['uses' => 'CRM\UnitTransaction@validateSaleTrx']);
    $router->get('doclist/type/{type}',  ['uses' => 'CRM\UnitTransaction@getMDocListList']);

    $router->get('request/specup/id/{id}',  ['uses' => 'CRM\UnitUpdateRequest@getSpecUpRequestById']);
    $router->get('request/specup/project/{id}',  ['uses' => 'CRM\UnitUpdateRequest@getSpecUpRequestByProjectId']);
    $router->post('request/specup',  ['uses' => 'CRM\UnitUpdateRequest@saveSpecUpRequest']);
    $router->get('request/specup/all',  ['uses' => 'CRM\UnitUpdateRequest@getSpecUpRequestList']);

    $router->get('request/discount/id/{id}',  ['uses' => 'CRM\UnitUpdateRequest@getDiscountRequestById']);
    $router->post('request/discount',  ['uses' => 'CRM\UnitUpdateRequest@saveDiscountRequest']);
    $router->get('request/discount/all',  ['uses' => 'CRM\UnitUpdateRequest@getDiscountRequestList']);
    $router->get('request/data/nup/{id}',  ['uses' => 'CRM\UnitTransaction@getNupRelatedData']);

    $router->get('spu/list',  ['uses' => 'Crm@getSPUList']);
    $router->get('spu/nup/cust/{id}',  ['uses' => 'CRM\UnitTransaction@getCustomerNup']);
    $router->get('spu/validate/{spu_id}',  ['uses' => 'Crm@validateSPUPayment']);
    $router->get('spu/print/header/{spu_id}',  ['uses' => 'CRM\UnitTransaction@getSpuPrintDataHeader']);
    $router->get('spu/print/detail/{spu_id}',  ['uses' => 'CRM\UnitTransaction@getSpuPrintDataDetail']);

    $router->get('nup/list',  ['uses' => 'Crm@getNUPList']);
    $router->get('bok/list',  ['uses' => 'Crm@getBOKList']);
    $router->get('spu/bok/cust/{id}',  ['uses' => 'CRM\UnitTransaction@getCustomerBok']);

    $router->get('ppjb/list',  ['uses' => 'Crm@getPPJBList']);
    $router->get('ppjb/spu/cust/{id}',  ['uses' => 'CRM\UnitTransaction@getCustomerSpu']);
    $router->get('ppjb/data/spu/{id}',  ['uses' => 'CRM\UnitTransaction@getSpuRelatedData']);
    $router->get('ppjb/data/cust/{id}',  ['uses' => 'CRM\UnitTransaction@getCustomerRelatedData']);
    $router->get('ppjb/data/project/{id}',  ['uses' => 'CRM\UnitTransaction@getProjectRelatedData']);

    $router->get('ppjb/print/header/{spu_id}',  ['uses' => 'CRM\UnitTransaction@getPpjbPrintDataHeader']);
    $router->get('ppjb/print/detail/{spu_id}',  ['uses' => 'CRM\UnitTransaction@getPpjbPrintDataDetail']);
    //sales
    $router->get('sales',  ['uses' => 'CRM\Sales@getSales']);
    $router->get('sales/{id}',  ['uses' => 'CRM\Sales@getSalesbyId']);
    $router->post('sales',  ['uses' => 'CRM\Sales@saveSales']);
    $router->put('sales/{id}',  ['uses' => 'CRM\Sales@updateSales']);
    $router->delete('sales/{id}',  ['uses' => 'CRM\Sales@deleteSales']);

     //KPR Simulation
    $router->get('kpr',  ['uses' => 'CRM\KprSimulation@getKpr']);
    $router->get('bank',  ['uses' => 'CRM\KprSimulation@getBank']);
    $router->get('kpr/{id}',  ['uses' => 'CRM\KprSimulation@getKprById']);
    $router->post('kpr',  ['uses' => 'CRM\KprSimulation@saveKpr']);
    $router->put('kpr/{id}',  ['uses' => 'CRM\KprSimulation@updateKpr']);
    $router->delete('kpr/{id}',  ['uses' => 'CRM\KprSimulation@deleteKpr']);

    $router->get('kprbank/name/all',  ['uses' => 'CRM\UnitTransaction@getKprBankName']);
    $router->get('kprbank/scheme/bankname/{name}',  ['uses' => 'CRM\UnitTransaction@getKprBankPaymentSchemeByName']);

    //sales
    $router->get('sales',  ['uses' => 'CRM\Sales@getSales']);
    $router->get('sales/{id}',  ['uses' => 'CRM\Sales@getSalesbyId']);
    $router->post('sales',  ['uses' => 'CRM\Sales@saveSales']);
    $router->put('sales/{id}',  ['uses' => 'CRM\Sales@updateSales']);
    $router->delete('sales/{id}',  ['uses' => 'CRM\Sales@deleteSales']);

    //KPR Simulation
    $router->get('kpr',  ['uses' => 'CRM\KprSimulation@getKpr']);
    $router->get('bank',  ['uses' => 'CRM\KprSimulation@getBank']);
    $router->get('kpr/{id}',  ['uses' => 'CRM\KprSimulation@getKprById']);
    $router->post('kpr',  ['uses' => 'CRM\KprSimulation@saveKpr']);
    $router->put('kpr/{id}',  ['uses' => 'CRM\KprSimulation@updateKpr']);
    $router->delete('kpr/{id}',  ['uses' => 'CRM\KprSimulation@deleteKpr']);
});


$router->group(['prefix' => 'user'], function () use ($router) {
    $router->post('login',  ['uses' => 'UserController@getLogin']);
});


$router->group(['prefix' => 'master'], function () use ($router) {
    //MSequences
    $router->post('m_sequence/generate_trx_no',  ['uses' => 'Master\MSequenceController@generateTransactionNumber']);
    $router->post('m_sequence/generate_trx_no/crm',  ['uses' => 'Master\MSequenceController@generateTransactionNumberCRM']);
    $router->post('m_sequence/update_trx_no/crm/{trxno}',  ['uses' => 'Master\MSequenceController@updateTransactionNumberCRM']);

});

$router->group(['prefix' => 'gallery'], function () use ($router) {
    $router->get('',  ['uses' => 'Gallery@getGallery']);
    $router->get('/{id}',  ['uses' => 'Gallery@getGalleryById']);
    $router->post('',  ['uses' => 'Gallery@saveGallery']);
    $router->put('/{id}',  ['uses' => 'Gallery@updateGallery']);
    $router->delete('/{id}',  ['uses' => 'Gallery@deleteGallery']);
});

$router->group(['prefix' => 'home'], function () use ($router) {
    $router->get('program/{id}',  ['uses' => 'HomeController@getProgramByUserId']);
});


