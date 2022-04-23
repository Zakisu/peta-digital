<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Store\AdminController;
use App\Http\Controllers\Store\DesaController;
use App\Http\Controllers\Store\TentangController;
use App\Http\Controllers\Auth\LoginController;



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
  return redirect('login');
});

Auth::routes();

Route::get('/admin',[AdminController::class, 'index']);
Route::get('/desa',[DesaController::class, 'index']);
Route::get('/tentang',[TentangController::class, 'index']);

/*admin */
Route::post('/create-admin',[AdminController::class, 'store'])->name('admin.store');
Route::get('/admins',[AdminController::class, 'listAdmin'])->name('admin.list');
Route::delete('/delete-admin/{id}',[AdminController::class, 'destroy'])->name('admin.destroy');
Route::put('/update-admin/{id}',[AdminController::class, 'update'])->name('admin.update');

/*about */
Route::get('/about',[TentangController::class, 'listAbout'])->name('about.list');
Route::put('/update-about/{id}',[TentangController::class, 'update'])->name('about.update');


/*desa */
Route::get('/desa/tambah',[DesaController::class, 'create'])->name('village.create');
Route::post('/create-village',[DesaController::class, 'store'])->name('village.store');

Route::get('/home', function () {
  return redirect('/admin');
});
