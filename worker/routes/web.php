<?php

use Illuminate\Support\Facades\Route;

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
    // return view('welcome');
    return redirect('/login');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::group(['middleware' => 'auth', 'prefix' => '/material_request'], function(){
    // Route::get('/create', 'INV\PembelianRutinController@create');
    Route::get('/list', 'HomeController@listMaterialRequest');
    Route::get('/list_detail_acc/{id}', 'HomeController@getListAccPengambilanBarangDetail');
    Route::get('/suggest', 'HomeController@suggestPW');
    Route::post('/get_project', 'HomeController@getProject');
    Route::post('/save_dev_request_d', 'HomeController@saveDevRequestD');
    Route::post('/update_dev_request_d', 'HomeController@updateDevRequestD');
    Route::get('/get_worker_list/{id}', 'HomeController@getWorkerList');
    Route::get('/duration_list/{id}', 'HomeController@getDurationList');
    Route::get('/report/{id}', 'HomeController@getReportWorker');
    Route::get('/getProjectWorkSub/{id}', 'HomeController@getProjectWorkSub');
    Route::get('/list_frame', 'HomeController@listFrameProduct');
    Route::get('/suggest_project_done', 'HomeController@suggestPWDone');
    Route::post('/form_frame', 'HomeController@trackFrameForm');
    Route::post('/save_track_frame', 'HomeController@saveTrackFrame');
    Route::get('/list_track_frame', 'HomeController@listTrackFrame');
    Route::get('/list_track_frame_dt/{id}', 'HomeController@getTrackFrameDetail');
    Route::get('/get_item_frame/{id}', 'HomeController@getItemFrame');
    Route::post('/save_frame_worker', 'HomeController@saveFrameWorker');
    Route::post('/save_frame_material_worker', 'HomeController@saveFrameMaterialWorker');
    Route::get('/get_item_frame_material/{id}', 'HomeController@getTrackFrameMaterialDetail');
    Route::get('/get_project_by_cust/{id}', 'HomeController@suggestProject');
    Route::get('/get_pw_by_inv/{id}', 'HomeController@suggestProjectWorks');
    Route::get('/run_project/{id}', 'HomeController@runProjects');
    Route::post('/create_work', 'HomeController@saveWorkD');
    Route::get('/get_label/{id}', 'HomeController@getLabel');
    Route::get('/close_work/{id}', 'HomeController@closeWork');
    Route::post('/get_worksub', 'HomeController@getWorkSub');
    Route::post('/save_worksub', 'HomeController@saveWorksub');
    Route::post('/add_worker', 'HomeController@addWorker');
    Route::post('/edit_worker', 'HomeController@editWorker');
    Route::get('/get_detail_progress/{id}', 'HomeController@getDetailProgress');
});