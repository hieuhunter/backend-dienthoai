<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ql_dienthoaiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('admin/login', [AdminController::class, 'login']);

Route::group(['middleware' => ['auth:api']], function () {
    Route::get('admin/user', [AuthController::class, 'user']);
    Route::get('admin/logout', [AuthController::class, 'logout']);

    Route::get('admin/sanpham', [AdminController::class, 'listProduct']);
    Route::post('admin/newproducts', [AdminController::class, 'addnew_sp']);
    Route::delete('admin/products/{id}', [AdminController::class, 'destroy']);

    Route::post('admin/products', [AdminController::class, 'newProduct']);
    Route::get('admin/products/{id}', [AdminController::class, 'show_sp']);
    Route::put('admin/products/{id}', [AdminController::class, 'update_sp']);
    Route::get('admin/bill', [AdminController::class, 'listBill']);
    Route::get('admin/listUser', [AdminController::class, 'listUser']);


    Route::get('admin/category', [AdminController::class, 'danhmuc']);
    Route::get('admin/brand', [AdminController::class, 'thuonghieu']);
});

//
Route::get('product', [ql_dienthoaiController::class, 'product']);
Route::get('slproduct', [ql_dienthoaiController::class, 'slproduct']);    
Route::get('category', [ql_dienthoaiController::class, 'danhmuc']);
Route::get('brand', [ql_dienthoaiController::class, 'thuonghieu']);
Route::get('category/{id}', [ql_dienthoaiController::class, 'sptheodm']);
Route::get('brand/{id}', [ql_dienthoaiController::class, 'sptheoth']);

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);


Route::get('product/{id}', [ql_dienthoaiController::class, 'ctProduct']);

Route::group(['middleware' => ['auth:api']], function () {
    Route::get('addCart', [ql_dienthoaiController::class, 'addgiohang']);
    Route::delete('delete_cart', [ql_dienthoaiController::class, 'xoacart']);
    Route::delete('delete_all', [ql_dienthoaiController::class, 'xoatatcagh']);
    Route::get('listCart', [ql_dienthoaiController::class, 'danhsachcart']);
    Route::post('pay', [ql_dienthoaiController::class, 'thanhtoan']);
    Route::get('user', [AuthController::class, 'user']);

    Route::get('profile', [ql_dienthoaiController::class, 'thongtin']);
    Route::put('profile', [ql_dienthoaiController::class, 'capnhatthongtin']);
    
    Route::get('logout', [AuthController::class, 'logout']);

});
