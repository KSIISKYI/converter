<?php

use App\Http\Controllers\InstanceController;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => 'instances',
        'as' => 'instances.',
    ],
    function () {
        Route::get(
            '',
            [InstanceController::class, 'index']
        )->name('index');

        Route::post(
            '',
            [InstanceController::class, 'store']
        )->name('store');

        Route::group(
            [
                'middleware' => 'ensure.instance.in.session',
            ],
            function () {
                Route::get(
                    '{instance}',
                    [InstanceController::class, 'show']
                )->name('show');
                Route::put(
                    '{instance}',
                    [InstanceController::class, 'update']
                )->name('update');
                Route::delete(
                    '{instance}',
                    [InstanceController::class, 'destroy']
                )->name('destroy');
                Route::post(
                    '{instance}/convert',
                    [InstanceController::class, 'convert']
                )->name('convert');
                Route::get(
                    '{instance}/download',
                    [InstanceController::class, 'download']
                )->name('download');
            }
        );
    }
);