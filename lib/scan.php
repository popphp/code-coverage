<?php
/**
 * Coverage report scanner.
 *
 * Reads the php-code-coverage HTML reports that live in this directory and
 * pulls the headline numbers out of each one, so the index page never has to
 * be hand-maintained. Drop in a new component directory and it shows up.
 */

/**
 * Scan $root for coverage reports, one per immediate subdirectory.
 *
 * @param  string $root
 * @return array  list of component arrays, popphp first then alphabetical
 */
function coverage_scan(string $root): array
{
    $components = [];

    foreach (glob($root . '/*/index.html') as $file) {
        $name   = basename(dirname($file));
        $parsed = coverage_parse(file_get_contents($file));

        if ($parsed !== null) {
            $components[] = ['name' => $name, 'path' => $name . '/index.html'] + $parsed;
        }
    }

    usort($components, function ($a, $b) {
        // popphp is the core framework, so it leads the list
        if ($a['name'] === 'popphp') return -1;
        if ($b['name'] === 'popphp') return 1;
        return strcmp($a['name'], $b['name']);
    });

    return $components;
}

/**
 * Parse the Total row and footer out of a report's index.html.
 *
 * @param  string $html
 * @return array|null null when the file is not a report we understand
 */
function coverage_parse(string $html): ?array
{
    if (!preg_match('/<tbody>(.*?)<\/tbody>/s', $html, $body)) {
        return null;
    }
    if (!preg_match('/<tr>(?:(?!<\/tr>).)*?>Total<.*?<\/tr>/s', $body[1], $row)) {
        return null;
    }

    // The Total row carries six right-aligned cells, in pairs:
    // lines %, lines ratio, methods %, methods ratio, classes %, classes ratio
    preg_match_all('/<div align="right">(.*?)<\/div>/s', $row[0], $cells);

    if (count($cells[1]) < 6) {
        return null;
    }

    $metrics = [];
    foreach (['lines' => 0, 'methods' => 2, 'classes' => 4] as $metric => $i) {
        $metrics[$metric] = [
            'percent' => coverage_percent($cells[1][$i]),
            'ratio'   => coverage_ratio($cells[1][$i + 1]),
        ];
    }

    return [
        'metrics'   => $metrics,
        'coverage'  => $metrics['lines']['percent'],
        'generated' => coverage_generated($html),
        'version'   => coverage_match('/php-code-coverage ([\d.]+)/', $html),
        'phpunit'   => coverage_match('/PHPUnit ([\d.]+)/', $html),
        'php'       => coverage_match('/PHP ([\d.]+)/', $html),
    ];
}

/**
 * "98.58%" => 98.58, "n/a" => null
 */
function coverage_percent(string $cell): ?float
{
    $cell = trim(strip_tags($cell));

    return preg_match('/([\d.]+)\s*%/', $cell, $m) ? (float)$m[1] : null;
}

/**
 * "1531&nbsp;/&nbsp;1553" => [1531, 1553]
 */
function coverage_ratio(string $cell): array
{
    $cell = trim(str_replace('&nbsp;', ' ', strip_tags($cell)));

    return preg_match('/(\d+)\s*\/\s*(\d+)/', $cell, $m)
        ? ['covered' => (int)$m[1], 'total' => (int)$m[2]]
        : ['covered' => 0, 'total' => 0];
}

/**
 * Pull the generation timestamp out of the report footer.
 *
 * @return int|null unix timestamp
 */
function coverage_generated(string $html): ?int
{
    if (!preg_match('/<\/a> at ([^<]+?)\.\s*<\/small>/s', $html, $m)) {
        return null;
    }

    $time = strtotime(trim($m[1]));

    return ($time === false) ? null : $time;
}

function coverage_match(string $pattern, string $html): ?string
{
    return preg_match($pattern, $html, $m) ? $m[1] : null;
}

/**
 * Coverage level, on the same thresholds the badge in coverage.php uses.
 *
 * The level becomes a CSS class rather than an inline color, so that the
 * light and dark themes can each pick a shade that stays readable.
 */
function coverage_level(?float $percent): string
{
    if ($percent === null)  return 'na';
    if ($percent >= 90)     return 'high';
    if ($percent >= 80)     return 'good';
    if ($percent >= 70)     return 'mid';

    return 'low';
}
