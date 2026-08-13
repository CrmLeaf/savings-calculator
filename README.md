# Payroll Savings Calculator

Estimate annual savings against another payroll provider, per employee.

Compares what you pay now with what you would pay instead, over the contract you would actually sign, with the migration cost counted rather than waved away.

One of the [CRMLeaf payroll tools](https://github.com/crmleaf). The arithmetic
and the dated statutory rate tables live in
[`crmleaf/payroll-core`](https://github.com/crmleaf/payroll-core); this package is
the thin skin that makes one calculator installable, mountable and embeddable on
its own.

> [!NOTE]
> A wrong figure or an out-of-date rate is almost always a
> [`payroll-core`](https://github.com/crmleaf/payroll-core/issues) matter, since
> that is where the tables live. Anything about this tool's routes, views or
> browser asset belongs here.

## Install

**Composer** - Laravel auto-discovers the service provider, so this is the whole
setup:

```bash
composer require crmleaf/savings-calculator
```

> [!NOTE]
> Not on Packagist yet. Until it is, point Composer at the two repositories in
> **your own project's** `composer.json` and the same `require` works, because
> Composer reads the tags:
>
> ```json
> "repositories": [
>     { "type": "vcs", "url": "https://github.com/crmleaf/savings-calculator.git" },
>     { "type": "vcs", "url": "https://github.com/crmleaf/payroll-core.git" }
> ]
> ```
>
> Both entries are needed, and they have to be in the root project: Composer
> ignores a `repositories` block inside an installed dependency, so listing only
> this package will not resolve `crmleaf/payroll-core`.

**npm** - the same calculation, re-exported from `@crmleaf/payroll-js` so you can
install this one tool and nothing else:

```bash
npm install @crmleaf/savings-calculator
```

> [!NOTE]
> Not on npm yet either. The script-tag route below needs no registry and works
> today. Installing this package straight from git will not resolve
> `@crmleaf/payroll-js`, for the same reason as above.

**A plain script tag** - no build step, no bundler, no server. Build the browser
bundle once and serve the file yourself:

```html
<script src="/js/payroll.min.js"></script>
<script>
const result = CrmleafPayroll.savings({
  employeeCount: 250,
  currentPerEmployeePerMonth: 120,
  proposedPerEmployeePerMonth: 75,
  currentAnnualPlatformFee: 60000,
});
console.log(result.explain);
</script>
```

`payroll.min.js` is the single-file browser build. Get it by running
`npm run build` in [`@crmleaf/payroll-js`][js] and copying `dist/payroll.min.js`
into whatever your site serves as static assets.

> A hosted CDN build is coming soon, which will reduce this to a single URL.
> Serving the file yourself works today and keeps working afterwards - it is the
> only option that needs no third-party request, so plenty of projects will want
> to stay on it.

### See it working first

`demo/index.html` in this repository is a working copy of Payroll Savings Calculator in one file:
the form, the calculation and the working, with no build step and no server. Drop
`payroll.min.js` beside it and open it from disk.

```bash
cp /path/to/payroll-js/dist/payroll.min.js demo/
open demo/index.html
```

Nothing on that page reaches the network, which is the point: it is a calculator
people paste salary figures into.

## Use it

**Plain PHP**, no framework and no container:

```php
use Crmleaf\Payroll\Calculators\SavingsCalculator;
use Crmleaf\Payroll\Money;

$result = (new SavingsCalculator())->calculate(
    employeeCount: 250,
    currentPerEmployeePerMonth: Money::fromRupees(120),
    proposedPerEmployeePerMonth: Money::fromRupees(75),
    currentAnnualPlatformFee: Money::fromRupees(60_000),
);

echo $result->explain();      // the formula with the real operands in it
echo $result->workings();     // every step, one per line, with its citation
print_r($result->toArray());  // snake_case, ready for JSON
```

**Laravel** - resolve it from the container, or type-hint it anywhere:

```php
use Crmleaf\Payroll\Calculators\SavingsCalculator;

public function show(SavingsCalculator $calculator)
{
    return $calculator->calculate(
        employeeCount: 250,
        currentPerEmployeePerMonth: Money::fromRupees(120),
        proposedPerEmployeePerMonth: Money::fromRupees(75),
        currentAnnualPlatformFee: Money::fromRupees(60_000),
    )->toArray();
}
```

**Blade** - one component, no controller:

```blade
<x-crmleaf::savings-calculator />
```

**HTTP** - off by default. Publish the config and turn the route on:

```bash
php artisan vendor:publish --tag=savings-calculator-config
```

```php
// config/savings-calculator.php
'route' => ['enabled' => true, 'prefix' => 'tools'],
```

```bash
curl -X POST https://example.test/tools/savings-calculator \
  -H 'Content-Type: application/json' \
  -H 'Accept: application/json' \
  -d '{"employee_count":250,"current_per_employee_per_month":120,"proposed_per_employee_per_month":75,"current_annual_platform_fee":60000}'
```

The JSON response carries the figures, the working and the statutory citations:

```json
{
  "tool": "savings-calculator",
  "data": { "…": "every figure, snake_case, with a *_formatted twin" },
  "explain": "the formula with the real operands substituted",
  "working": [{ "label": "…", "amount": 0, "formula": "…", "citation": "…" }],
  "citations": ["…"]
}
```

**JavaScript**:

```js
import { savings } from '@crmleaf/savings-calculator';

const result = savings({
  employeeCount: 250,
  currentPerEmployeePerMonth: 120,
  proposedPerEmployeePerMonth: 75,
  currentAnnualPlatformFee: 60000,
});
```

## No server needed

The maths here is arithmetic over versioned rate tables, so it runs anywhere.
The published asset binds the markup and computes in the browser:

```bash
php artisan vendor:publish --tag=savings-calculator-assets
```

```html
<section data-crmleaf-tool="savings-calculator">
  <form data-crmleaf-form> … </form>
  <div data-crmleaf-output hidden></div>
</section>

<script src="/js/payroll.min.js"></script>
<script src="/vendor/savings-calculator/savings-calculator.js"></script>
```

If the browser build is absent the script does nothing and the form posts to the
server instead, so the page works either way.

## Inputs

| Field | Type | Required | Default | Notes |
|-------|------|----------|---------|-------|
| `employee_count` | integer | Yes | `250` |  |
| `current_per_employee_per_month` | money (₹) | Yes | `120` |  |
| `proposed_per_employee_per_month` | money (₹) | Yes | `75` |  |
| `current_annual_platform_fee` | money (₹) | No | `60000` |  |
| `proposed_annual_platform_fee` | money (₹) | No | `0` |  |
| `migration_cost` | money (₹) | No | `50000` |  |
| `contract_months` | integer | No | `12` |  |

Optional fields you leave out are omitted from the call entirely, so the
calculator's own documented defaults apply.

## What the model assumes

Not a statutory calculation. It compares two per-employee-per-month prices over a contract term, including platform fees and the one-off cost of migrating, so the answer is a total cost of ownership rather than a headline rate.

Rates are data, not code: they live in dated tables with a cited source in
`crmleaf/payroll-core`, so a rate change is a new dated entry rather than an edit
to a constant.

> [!IMPORTANT]
> This package implements our reading of the applicable statutes and is provided
> without warranty. It is a calculation library, not tax advice. Verify against
> your own compliance obligations before relying on the output for statutory
> filing.

## Publishing

| Tag | Publishes |
|-----|-----------|
| `savings-calculator-config` | `config/savings-calculator.php` |
| `savings-calculator-views` | `resources/views/vendor/savings-calculator` |
| `savings-calculator-assets` | `public/vendor/savings-calculator` |

## Licence

[MIT](LICENSE) © CRMLeaf. Use it commercially, embed it, fork it.

[js]: https://github.com/crmleaf/payroll-js
