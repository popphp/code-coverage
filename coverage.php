<?php
$urls = [
    'pop-acl'           => '/pop-acl/index.html',
    'pop-archive'       => '/pop-archive/index.html',
    'pop-audit'         => '/pop-audit/index.html',
    'pop-auth'          => '/pop-auth/index.html',
    'pop-cache'         => '/pop-cache/index.html',
    'pop-code'          => '/pop-code/index.html',
    'pop-color'         => '/pop-color/index.html',
    'pop-config'        => '/pop-config/index.html',
    'pop-console'       => '/pop-console/index.html',
    'pop-cookie'        => '/pop-cookie/index.html',
    'pop-crypt'         => '/pop-crypt/index.html',
    'pop-css'           => '/pop-css/index.html',
    'pop-csv'           => '/pop-csv/index.html',
    'pop-crypt'         => '/pop-crypt/index.html',
    'pop-data'          => '/pop-data/index.html',
    'pop-debug'         => '/pop-debug/index.html',
    'pop-db'            => '/pop-db/index.html',
    'pop-dir'           => '/pop-dir/index.html',
    'pop-dom'           => '/pop-dom/index.html',
    'pop-feed'          => '/pop-feed/index.html',
    'pop-file'          => '/pop-file/index.html',
    'pop-filter'        => '/pop-filter/index.html',
    'pop-form'          => '/pop-form/index.html',
    'pop-geo'           => '/pop-geo/index.html',
    'pop-http'          => '/pop-http/index.html',
    'pop-i18n'          => '/pop-i18n/index.html',
    'pop-image'         => '/pop-image/index.html',
    'pop-kettle'        => '/pop-kettle/index.html',
    'pop-log'           => '/pop-log/index.html',
    'pop-mail'          => '/pop-mail/index.html',
    'pop-mime'          => '/pop-mime/index.html',
    'pop-nav'           => '/pop-nav/index.html',
    'pop-paginator'     => '/pop-paginator/index.html',
    'pop-parser'        => '/pop-parser/index.html',
    'pop-payment'       => '/pop-payment/index.html',
    'pop-pdf'           => '/pop-pdf/index.html',
    'pop-queue'         => '/pop-queue/index.html',
    'pop-session'       => '/pop-session/index.html',
    'pop-shipping'      => '/pop-shipping/index.html',
    'pop-storage'       => '/pop-storage/index.html',
    'popphp'            => '/popphp/index.html',
    'pop-shipping'      => '/pop-shipping/index.html',
    'pop-utils'         => '/pop-utils/index.html',
    'pop-validator'     => '/pop-validator/index.html',
    'pop-version'       => '/pop-version/index.html',
    'pop-view'          => '/pop-view/index.html'
];

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
