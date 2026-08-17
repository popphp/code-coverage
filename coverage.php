<?php
require __DIR__ . '/lib/scan.php';

$components = coverage_scan(__DIR__);
$urls       = [];

foreach ($components as $component) {
    $urls[$component['name']] = '/' . $component['path'];
}

$colors = [
    50 => '#e05d44', // red
    60 => '#efb300', // orange
    70 => '#cec137', // yellow
    80 => '#a4c12e', // yellow-green
    90 => '#4dc81f'  // green
];

if (!isset($_GET['comp'])):
    header('HTTP/1.1 404 Not Found');
    echo '<html><head><title>Component Not Found</title></head><body><h1>Component Not Found</h1></body></html>';
    exit();
elseif (isset($_GET['comp']) && !isset($urls[$_GET['comp']])):
    header('HTTP/1.1 404 Not Found');
    echo '<html><head><title>Component Not Found</title></head><body><h1>Component Not Found</h1></body></html>';
    exit();
else:
    $contents = file_get_contents(__DIR__ . $urls[$_GET['comp']]);
    $coverage = substr($contents, (strpos($contents, 'aria-valuenow="') + 15));
    $coverage = round(substr($coverage, 0, strpos($coverage, '"')));

    if ($coverage >= 90):
        $color = $colors[90];
    elseif (($coverage < 90) && ($coverage >= 80)):
        $color = $colors[80];
    elseif (($coverage < 80) && ($coverage >= 70)):
        $color = $colors[70];
    elseif ($coverage < 70):
        $color = $colors[60];
    endif;

    header('HTTP/1.1 200 OK');
    header('Content-type: image/svg+xml');
    header('Content-disposition: filename=coverage.svg');

?><svg xmlns="http://www.w3.org/2000/svg" width="99" height="20">
    <linearGradient id="a" x2="0" y2="100%">
        <stop offset="0" stop-color="#bbb" stop-opacity=".1"/>
        <stop offset="1" stop-opacity=".1"/>
    </linearGradient>
    <rect rx="3" width="99" height="20" fill="#555"/>
    <rect rx="3" x="63" width="36" height="20" fill="<?=$color; ?>"/>
    <path fill="<?=$color; ?>" d="M63 0h4v20h-4z"/>
    <rect rx="3" width="99" height="20" fill="url(#a)"/>
    <g fill="#fff" text-anchor="middle" font-family="DejaVu Sans,Verdana,Geneva,sans-serif" font-size="11">
        <text x="32.5" y="15" fill="#010101" fill-opacity=".3">coverage</text>
        <text x="32.5" y="14">coverage</text>
        <text x="80" y="15" fill="#010101" fill-opacity=".3"><?=$coverage; ?>%</text><text x="80" y="14"><?=$coverage; ?>%</text>
    </g>
</svg>
<?php endif; ?>
