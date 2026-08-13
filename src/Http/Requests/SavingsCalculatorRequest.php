<?php

declare(strict_types=1);

namespace Crmleaf\Payroll\Tools\SavingsCalculator\Http\Requests;

use Crmleaf\Payroll\Money;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates the wire input for Payroll Savings Calculator and turns it into named arguments
 * for Crmleaf\Payroll\Calculators\SavingsCalculator::calculate().
 *
 * Optional fields that were not sent are left out of the payload entirely
 * rather than passed as null, so the calculator's own documented defaults apply
 * and there is exactly one place each default is written down.
 */
final class SavingsCalculatorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        if (!$this->submitted()) {
            return [];
        }

        return [
            'employee_count' => ['required', 'integer', 'min:1', 'max:1000000'],
            'current_per_employee_per_month' => ['required', 'numeric', 'min:0'],
            'proposed_per_employee_per_month' => ['required', 'numeric', 'min:0'],
            'current_annual_platform_fee' => ['nullable', 'numeric', 'min:0'],
            'proposed_annual_platform_fee' => ['nullable', 'numeric', 'min:0'],
            'migration_cost' => ['nullable', 'numeric', 'min:0'],
            'contract_months' => ['nullable', 'integer', 'min:1', 'max:120'],
        ];
    }

    /**
     * Named arguments for SavingsCalculator::calculate().
     *
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        /** @var array<string, mixed> $input */
        $input = $this->validated();

        $payload = [
            'employeeCount' => (int) $input['employee_count'],
            'currentPerEmployeePerMonth' => Money::fromRupees((float) $input['current_per_employee_per_month']),
            'proposedPerEmployeePerMonth' => Money::fromRupees((float) $input['proposed_per_employee_per_month']),
        ];

        if (array_key_exists('current_annual_platform_fee', $input) && $input['current_annual_platform_fee'] !== null) {
            $payload['currentAnnualPlatformFee'] = Money::fromRupees((float) $input['current_annual_platform_fee']);
        }

        if (array_key_exists('proposed_annual_platform_fee', $input) && $input['proposed_annual_platform_fee'] !== null) {
            $payload['proposedAnnualPlatformFee'] = Money::fromRupees((float) $input['proposed_annual_platform_fee']);
        }

        if (array_key_exists('migration_cost', $input) && $input['migration_cost'] !== null) {
            $payload['migrationCost'] = Money::fromRupees((float) $input['migration_cost']);
        }

        if (array_key_exists('contract_months', $input) && $input['contract_months'] !== null) {
            $payload['contractMonths'] = (int) $input['contract_months'];
        }

        return $payload;
    }

    /**
     * A bare GET renders an empty form; everything else is a submission.
     */
    public function submitted(): bool
    {
        return $this->isMethod('post') || $this->expectsJson() || $this->query->count() > 0;
    }
}
