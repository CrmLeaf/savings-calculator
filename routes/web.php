<?php

declare(strict_types=1);

use Crmleaf\Payroll\Tools\SavingsCalculator\Http\Controllers\SavingsCalculatorController;
use Illuminate\Support\Facades\Route;

/*
 * Loaded by SavingsCalculatorServiceProvider only when config('savings-calculator.route.enabled')
 * is true, so requiring the package never adds a URL on its own.
 */

/** @var \Illuminate\Contracts\Config\Repository $config */
$config = app('config');

Route::middleware((array) $config->get('savings-calculator.route.middleware', ['web']))
    ->prefix((string) $config->get('savings-calculator.route.prefix', 'tools'))
    ->group(static function () use ($config): void {
        Route::match(['get', 'post'], '/savings-calculator', SavingsCalculatorController::class)
            ->name((string) $config->get('savings-calculator.route.name', 'savings-calculator'));
    });
