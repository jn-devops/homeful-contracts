<?php

use App\Mappings\Pipelines\TransformPipe;
use App\Models\Mapping;
use Brick\Money\Money;

test('it resolves and applies transformers correctly', function () {
    $mapping = Mapping::factory()->make([
        'transformer' => 'UpperCase, ReverseString',
    ]);

    $pipe = new TransformPipe($mapping);

    $result = $pipe->handle('hello world', fn($value) => $value);

    expect($result)->toBe('DLROW OLLEH');  // Assuming transformers work as expected
});

test('currency transformer works', function () {
    $mapping = Mapping::factory()->make([
        'transformer' => 'ToMajorUnit, Currency?symbol=₱&decimals=1',
    ]);

    $pipe = new TransformPipe($mapping);

    $result = $pipe->handle('100000', fn($value) => $value);

    expect($result)->toBe('₱1,000.0');  // Assuming transformers work as expected
});

test('international currency transformer works', function () {
    $mapping = Mapping::factory()->make([
        'transformer' => 'ToMajorUnit, Currency?symbol=PHP',
    ]);

    $pipe = new TransformPipe($mapping);

    $result = $pipe->handle('100000', fn($value) => $value);

    expect($result)->toBe('PHP 1,000.00');  // Assuming transformers work as expected
});

use App\Mappings\Transformers\TitleCaseTransformer;

test('it applies title case and preserves suffixes', function () {
    $transformer = new TitleCaseTransformer();

    // Test cases
    $testCases = [
        ['input' => '  JOHN DOE JR.  ', 'expected' => 'John Doe Jr.'],
        ['input' => 'JANE SMITH III', 'expected' => 'Jane Smith III'],
        ['input' => '  lester hurtado  ', 'expected' => 'Lester Hurtado'],
        ['input' => 'MICHAEL BROWN SR.', 'expected' => 'Michael Brown Sr.'],
        ['input' => 'CHRISTIAN SANTOS II', 'expected' => 'Christian Santos II'],
        ['input' => 'JUAN DELA CRUZ V', 'expected' => 'Juan Dela Cruz V'],
        ['input' => '  anaïs santos ', 'expected' => 'Anaïs Santos'],
        ['input' => 'KAREN DOE', 'expected' => 'Karen Doe'],
    ];

    foreach ($testCases as $case) {
        $result = $transformer->transform(['value' => $case['input']]);
        expect($result['value'])->toBe($case['expected']);
    }
});

test('it handles names with no suffixes correctly', function () {
    $transformer = new TitleCaseTransformer();

    $result = $transformer->transform(['value' => 'john smith']);
    expect($result['value'])->toBe('John Smith');
});

test('it trims leading and trailing spaces', function () {
    $transformer = new TitleCaseTransformer();

    $result = $transformer->transform(['value' => '   MARY JANE DOE   ']);
    expect($result['value'])->toBe('Mary Jane Doe');
});

test('it works with empty or already formatted input', function () {
    $transformer = new TitleCaseTransformer();

    expect($transformer->transform(['value' => '']))->toBe(['value' => '']);
    expect($transformer->transform(['value' => 'John Doe Jr.']))->toBe(['value' => 'John Doe Jr.']);
});

use App\Mappings\Transformers\ConcatTransformer;

test('ConcatTransformer appends before and after words correctly', function () {
    $transformer = new ConcatTransformer('before=Hello&after=World');

    $result = $transformer->transform(['value' => 'PHP']);

    expect($result['value'])->toBe('Hello PHP World');
});

test('ConcatTransformer only appends before word', function () {
    $transformer = new ConcatTransformer('before=Mr.');

    $result = $transformer->transform(['value' => 'John Doe']);

    expect($result['value'])->toBe('Mr. John Doe');
});

test('ConcatTransformer only appends after word', function () {
    $transformer = new ConcatTransformer('after=Inc.');

    $result = $transformer->transform(['value' => 'TechCorp']);

    expect($result['value'])->toBe('TechCorp Inc.');
});

test('ConcatTransformer does not add extra spaces when before and after are empty', function () {
    $transformer = new ConcatTransformer();

    $result = $transformer->transform(['value' => 'CleanText']);

    expect($result['value'])->toBe('CleanText');
});

test('ConcatTransformer trims extra spaces properly', function () {
    $transformer = new ConcatTransformer('before=  Welcome  &after=  ! ');

    $result = $transformer->transform(['value' => '   Home   ']);

    expect($result['value'])->toBe('Welcome Home !');
});

use App\Mappings\Transformers\ToMajorUnitTransformer;

test('ToMajorUnitTransformer converts minor to major units (default Money object)', function () {
    $transformer = new ToMajorUnitTransformer();

    $result = $transformer->transform(['value' => 100000]);

    expect($result['value'])->toBeInstanceOf(Money::class);
    expect($result['value']->getAmount()->toFloat())->toBe(1000.00);
});

test('ToMajorUnitTransformer converts minor to major units as float', function () {
    $transformer = new ToMajorUnitTransformer('type=float');

    $result = $transformer->transform(['value' => 100000]);

    expect($result['value'])->toBe(1000.00);
});

test('ToMajorUnitTransformer converts minor to major units as integer', function () {
    $transformer = new ToMajorUnitTransformer('type=integer');

    $result = $transformer->transform(['value' => 100000]);

    expect($result['value'])->toBe(1000);
});

test('ToMajorUnitTransformer handles zero values', function () {
    $transformer = new ToMajorUnitTransformer();

    $result = $transformer->transform(['value' => 0]);

    expect($result['value'])->toBeInstanceOf(Money::class);
    expect($result['value']->getAmount()->toFloat())->toBe(0.00);
});

test('ToMajorUnitTransformer rounds up correctly', function () {
    $transformer = new ToMajorUnitTransformer();

    $result = $transformer->transform(['value' => 999]);

    expect($result['value'])->toBeInstanceOf(Money::class);
    expect($result['value']->getAmount()->toFloat())->toBe(9.99); // Rounded up due to RoundingMode::UP
});

test('ToMajorUnitTransformer falls back to float if type is invalid', function () {
    $transformer = new ToMajorUnitTransformer('type=invalid');

    $result = $transformer->transform(['value' => 100000]);

    expect($result['value'])->toBe(1000.0);
});

use App\Mappings\Transformers\NumberPercentTransformer;

test('NumberPercentTransformer correctly converts numbers to percentages', function () {
    $transformer = new NumberPercentTransformer();

    $result = $transformer->transform(['value' => 25]);

    expect($result['value'])->toBe('25%');
});

test('NumberPercentTransformer applies precision correctly', function () {
    $transformer = new NumberPercentTransformer('precision=1');

    $result = $transformer->transform(['value' => 12.34]);

    expect($result['value'])->toBe('12.3%');
});

use App\Mappings\Transformers\NumberFormatTransformer;

test('NumberFormatTransformer correctly formats numbers', function () {
    $transformer = new NumberFormatTransformer();

    $result = $transformer->transform(['value' => 1000000]);

    expect($result['value'])->toBe('1,000,000');
});

test('NumberFormatTransformer applies precision correctly', function () {
    $transformer = new NumberFormatTransformer('precision=2');

    $result = $transformer->transform(['value' => 1000]);

    expect($result['value'])->toBe('1,000.00');
});

test('NumberFormatTransformer supports locale-based formatting', function () {
    $transformer = new NumberFormatTransformer('locale=de');

    $result = $transformer->transform(['value' => 1000]);

    // In German locale, comma is used as decimal separator
    expect($result['value'])->toBe('1.000');
});

use App\Mappings\Transformers\NumberSpellTransformer;

test('NumberSpellTransformer correctly spells out numbers', function () {
    $transformer = new NumberSpellTransformer();

    $result = $transformer->transform(['value' => 100]);

    expect($result['value'])->toBe('one hundred');
});

test('NumberSpellTransformer applies "after" threshold correctly', function () {
    $transformer = new NumberSpellTransformer('after=10');

    $result = $transformer->transform(['value' => 11]);

    expect($result['value'])->toBe('eleven');
});

test('NumberSpellTransformer applies "until" threshold correctly', function () {
    $transformer = new NumberSpellTransformer('until=10');

    $result = $transformer->transform(['value' => 11]);

    expect($result['value'])->toBe('11'); // Not spelled out since it's beyond "until"
});

test('NumberSpellTransformer applies both "after" and "until" options', function () {
    $transformer = new NumberSpellTransformer('after=5&until=15');

    expect($transformer->transform(['value' => 3])['value'])->toBe('3'); // Below "after", so not spelled
    expect($transformer->transform(['value' => 12])['value'])->toBe('twelve'); // Between "after" and "until", so spelled
    expect($transformer->transform(['value' => 16])['value'])->toBe('16'); // Beyond "until", not spelled
});

use App\Mappings\Transformers\NumberAbbreviateTransformer;

test('NumberAbbreviateTransformer correctly abbreviates large numbers', function () {
    $transformer = new NumberAbbreviateTransformer();

    $result = $transformer->transform(['value' => 1000000]);

    expect($result['value'])->toBe('1M');
});

test('NumberAbbreviateTransformer applies precision correctly', function () {
    $transformer = new NumberAbbreviateTransformer('precision=2');

    $result = $transformer->transform(['value' => 1500000]);

    expect($result['value'])->toBe('1.50M');
});

test('NumberAbbreviateTransformer abbreviates thousands correctly', function () {
    $transformer = new NumberAbbreviateTransformer('precision=1');

    $result = $transformer->transform(['value' => 2500]);

    expect($result['value'])->toBe('2.5K');
});

test('NumberAbbreviateTransformer abbreviates billions correctly', function () {
    $transformer = new NumberAbbreviateTransformer();

    $result = $transformer->transform(['value' => 2000000000]);

    expect($result['value'])->toBe('2B');
});

use App\Mappings\Transformers\NumberTrimTransformer;

test('NumberTrimTransformer removes trailing zeros from whole numbers', function () {
    $transformer = new NumberTrimTransformer();

    $result = $transformer->transform(['value' => 100.000]);

    expect($result['value'])->toBe(100);
});

test('NumberTrimTransformer preserves decimals when necessary', function () {
    $transformer = new NumberTrimTransformer();

    $result = $transformer->transform(['value' => 123.45]);

    expect($result['value'])->toBe(123.45);
});

test('NumberTrimTransformer removes decimals when necessary', function () {
    $transformer = new NumberTrimTransformer();

    $result = $transformer->transform(['value' => 123.0]);

    expect($result['value'])->toBe(123);
});

test('NumberTrimTransformer applies precision correctly', function () {
    $transformer = new NumberTrimTransformer();

    $result = $transformer->transform(['value' => 123.45000]);

    expect($result['value'])->toBe(123.45);
});

test('NumberTrimTransformer keeps decimal point only when needed', function () {
    $transformer = new NumberTrimTransformer();

    $result = $transformer->transform(['value' => 0.5000]);

    expect($result['value'])->toBe(0.5);
});

use App\Mappings\Transformers\LowerCaseTransformer;

test('LowerCaseTransformer converts text to lowercase', function () {
    $transformer = new LowerCaseTransformer();

    $result = $transformer->transform(['value' => 'HeLLo WoRLd']);

    expect($result['value'])->toBe('hello world');
});

test('LowerCaseTransformer trims whitespace before converting', function () {
    $transformer = new LowerCaseTransformer();

    $result = $transformer->transform(['value' => '   HeLLo WoRLd   ']);

    expect($result['value'])->toBe('hello world');
});

test('LowerCaseTransformer handles empty strings correctly', function () {
    $transformer = new LowerCaseTransformer();

    $result = $transformer->transform(['value' => '   ']);

    expect($result['value'])->toBe('');
});

test('LowerCaseTransformer handles strings with special characters correctly', function () {
    $transformer = new LowerCaseTransformer();

    $result = $transformer->transform(['value' => '  PHP is AWESOME!  ']);

    expect($result['value'])->toBe('php is awesome!');
});

test('LowerCaseTransformer preserves non-alphabetic characters', function () {
    $transformer = new LowerCaseTransformer();

    $result = $transformer->transform(['value' => '1234 AB!@#']);

    expect($result['value'])->toBe('1234 ab!@#');
});
