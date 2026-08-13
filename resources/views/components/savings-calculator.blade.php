@props([
    'action' => null,
    'method' => 'post',
    'defaults' => [],
    'input' => [],
    'result' => null,
    'error' => null,
    'heading' => 'Payroll Savings Calculator',
    'tagline' => 'Estimate annual savings against another payroll provider, per employee.',
    'showWorking' => true,
])

<section class="crmleaf-tool crmleaf-tool--savings-calculator" data-crmleaf-tool="savings-calculator">
    <header class="crmleaf-tool__header">
        <h2 class="crmleaf-tool__heading">{{ $heading }}</h2>
        <p class="crmleaf-tool__tagline">{{ $tagline }}</p>
    </header>

    @if ($error)
        <p class="crmleaf-tool__error" role="alert">{{ $error }}</p>
    @endif

    <form class="crmleaf-tool__form"
          method="{{ strtolower($method) === 'get' ? 'get' : 'post' }}"
          action="{{ $action }}"
          data-crmleaf-form>
        @if (strtolower($method) !== 'get')
            @csrf
        @endif

        <label class="crmleaf-field">
            <span>Employees on payroll</span>
            <input type="number" step="1" inputmode="numeric" name="employee_count" value="{{ old('employee_count', $input['employee_count'] ?? ($defaults['employee_count'] ?? '')) }}" required>
        </label>

        <label class="crmleaf-field">
            <span>Current price per employee per month</span>
            <input type="number" step="0.01" min="0" inputmode="decimal" name="current_per_employee_per_month" value="{{ old('current_per_employee_per_month', $input['current_per_employee_per_month'] ?? ($defaults['current_per_employee_per_month'] ?? '')) }}" required>
        </label>

        <label class="crmleaf-field">
            <span>Proposed price per employee per month</span>
            <input type="number" step="0.01" min="0" inputmode="decimal" name="proposed_per_employee_per_month" value="{{ old('proposed_per_employee_per_month', $input['proposed_per_employee_per_month'] ?? ($defaults['proposed_per_employee_per_month'] ?? '')) }}" required>
        </label>

        <label class="crmleaf-field">
            <span>Current annual platform fee</span>
            <input type="number" step="0.01" min="0" inputmode="decimal" name="current_annual_platform_fee" value="{{ old('current_annual_platform_fee', $input['current_annual_platform_fee'] ?? ($defaults['current_annual_platform_fee'] ?? '')) }}">
        </label>

        <label class="crmleaf-field">
            <span>Proposed annual platform fee</span>
            <input type="number" step="0.01" min="0" inputmode="decimal" name="proposed_annual_platform_fee" value="{{ old('proposed_annual_platform_fee', $input['proposed_annual_platform_fee'] ?? ($defaults['proposed_annual_platform_fee'] ?? '')) }}">
        </label>

        <label class="crmleaf-field">
            <span>One-off migration cost</span>
            <input type="number" step="0.01" min="0" inputmode="decimal" name="migration_cost" value="{{ old('migration_cost', $input['migration_cost'] ?? ($defaults['migration_cost'] ?? '')) }}">
        </label>

        <label class="crmleaf-field">
            <span>Contract length in months</span>
            <input type="number" step="1" inputmode="numeric" name="contract_months" value="{{ old('contract_months', $input['contract_months'] ?? ($defaults['contract_months'] ?? '')) }}">
        </label>

        <input type="hidden" name="tool" value="savings-calculator">

        <div class="crmleaf-tool__actions">
            <button type="submit" class="crmleaf-tool__submit">Calculate</button>
        </div>
    </form>

    {{-- The client-side path writes its answer here; the server-side path fills it below. --}}
    <div class="crmleaf-tool__output" data-crmleaf-output hidden></div>

    @if ($result)
        <div class="crmleaf-tool__result">
            <p class="crmleaf-tool__explain"><code>{{ $result->explain() }}</code></p>

            <table class="crmleaf-tool__figures">
                <tbody>
                @foreach ($result->toArray() as $key => $value)
                    @continue(is_array($value) || str_ends_with((string) $key, '_formatted'))
                    <tr>
                        <th scope="row">{{ ucfirst(str_replace('_', ' ', (string) $key)) }}</th>
                        <td>{{ $result->toArray()[$key.'_formatted'] ?? (is_bool($value) ? ($value ? 'Yes' : 'No') : $value) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            @if ($showWorking && count($result->steps()))
                <details class="crmleaf-tool__working" open>
                    <summary>How this was worked out</summary>
                    <ol>
                        @foreach ($result->steps() as $step)
                            <li>
                                <span class="crmleaf-step__label">{{ $step->label }}</span>
                                @if ($step->amount)
                                    <span class="crmleaf-step__amount">{{ $step->amount->format() }}</span>
                                @endif
                                @if ($step->formula)
                                    <code class="crmleaf-step__formula">{{ $step->formula }}</code>
                                @endif
                                @if ($step->citation)
                                    <small class="crmleaf-step__citation">{{ $step->citation }}</small>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                </details>
            @endif

            @if (count($result->citations()))
                <ul class="crmleaf-tool__citations">
                    @foreach ($result->citations() as $citation)
                        <li>{{ $citation }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endif
</section>
