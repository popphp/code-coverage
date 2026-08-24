<?php
require __DIR__ . '/lib/scan.php';

$components = coverage_scan(__DIR__);
$stamps     = array_filter(array_column($components, 'generated'));
$latest     = $stamps ? max($stamps) : null;
$first      = $components[0] ?? null;

$metricLabels = ['lines' => 'Lines', 'methods' => 'Methods', 'classes' => 'Classes'];
?><!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>Pop PHP Framework Code Coverage</title>

    <meta name="author" content="Pop PHP Framework" />
    <meta name="keywords" content="pop php framework code coverage" />
    <meta name="description" content="Code coverage reports for the Pop PHP Framework components" />
    <meta name="robots" content="all" />

    <link type="image/png" rel="shortcut icon" href="https://media.popphp.org/img/pop-php-icon.png" />
    <link rel="icon" type="image/png" href="https://media.popphp.org/img/pop-php-icon.png" />
    <link rel="stylesheet" href="css/styles.css" />

    <script>
        // Applied before first paint so a saved dark preference does not flash light
        (function () {
            try {
                if (localStorage.getItem('pop-coverage-theme') === 'dark') {
                    document.documentElement.setAttribute('data-theme', 'dark');
                }
            } catch (e) {}
        }());
    </script>

</head>

<body>

    <a class="skip-link" href="#components">Skip to components</a>

    <aside class="sidebar" id="sidebar">

        <div class="sidebar-head">
            <div class="head-row">
                <a class="brand" href="./">
                    <img class="brand-mark brand-mark-light" src="assets/img/pop-php-logo-orange.png" alt="Pop PHP Framework" />
                    <img class="brand-mark brand-mark-dark" src="assets/img/pop-php-logo-white.png" alt="" />
                </a>
                <div class="head-buttons">
                    <button class="icon-btn theme-toggle" type="button" aria-label="Switch to dark theme">
                        <svg class="icon-sun" viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="12" cy="12" r="4.2" />
                            <path d="M12 1.8v3M12 19.2v3M4.6 4.6l2.1 2.1M17.3 17.3l2.1 2.1M1.8 12h3M19.2 12h3M4.6 19.4l2.1-2.1M17.3 6.7l2.1-2.1" />
                        </svg>
                        <svg class="icon-moon" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M20.7 14.6A9 9 0 1 1 9.4 3.3a7.2 7.2 0 0 0 11.3 11.3Z" />
                        </svg>
                    </button>
                    <button class="icon-btn nav-toggle" type="button" aria-expanded="false" aria-controls="sidebar-body">
                        <span class="nav-toggle-bars" aria-hidden="true"></span>
                        <span class="sr-only">Toggle component list</span>
                    </button>
                </div>
            </div>
            <p class="brand-sub">Code Coverage</p>
        </div>

        <div class="sidebar-body" id="sidebar-body">

            <?php if ($components): ?>
            <div class="filter">
                <svg class="filter-icon" viewBox="0 0 16 16" aria-hidden="true">
                    <path d="M11.7 10.3a6 6 0 1 0-1.4 1.4l3.3 3.3 1.4-1.4-3.3-3.3ZM7 11a4 4 0 1 1 0-8 4 4 0 0 1 0 8Z" />
                </svg>
                <input type="search" id="filter" placeholder="Filter components" aria-label="Filter components"
                       autocomplete="off" spellcheck="false" />
                <kbd class="filter-key" aria-hidden="true">/</kbd>
            </div>

            <nav class="side-nav-wrap" aria-label="Components">
                <ul class="side-nav">
                    <?php foreach ($components as $component): ?>
                    <li>
                        <a href="<?= htmlspecialchars($component['path']) ?>" target="_blank" rel="noopener"
                           data-name="<?= htmlspecialchars($component['name']) ?>">
                            <span class="side-nav-name"><?= htmlspecialchars($component['name']) ?></span>
                            <span class="side-nav-pct lv-<?= coverage_level($component['coverage']) ?>">
                                <?= $component['coverage'] === null ? 'n/a' : round($component['coverage']) . '%' ?>
                            </span>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <p class="side-nav-empty" hidden>No matching components</p>
            </nav>
            <?php endif; ?>

            <?php if ($first !== null): ?>
            <div class="sidebar-foot">
                <p>php-code-coverage <?= htmlspecialchars($first['version'] ?? '') ?></p>
                <p>PHPUnit <?= htmlspecialchars($first['phpunit'] ?? '') ?> &middot; PHP <?= htmlspecialchars($first['php'] ?? '') ?></p>
            </div>
            <?php endif; ?>

        </div>

    </aside>

    <main class="main">

        <header class="hero">
            <img class="hero-logo" src="assets/img/pop-php-logo.png" alt="" />
            <h1>Code Coverage</h1>
            <p class="hero-lead">
                Test coverage reports for every component of the<br />
                <a href="https://www.popphp.org/" target="_blank" rel="noopener">Pop PHP Framework</a>.
            </p>
            <?php if ($components): ?>
            <p class="hero-meta">
                <span><strong><?= count($components) ?></strong> components</span>
                <?php if ($latest): ?>
                <span>Last generated <strong><?= date('M j, Y', $latest) ?></strong></span>
                <?php endif; ?>
            </p>
            <?php endif; ?>
        </header>

        <?php if ($components): ?>

        <div class="toolbar">
            <p class="count" id="count" aria-live="polite"><?= count($components) ?> components</p>
            <div class="sort" role="group" aria-label="Sort components">
                <span class="sort-label">Sort</span>
                <button type="button" class="sort-btn is-active" data-sort="name" data-dir="asc"
                        aria-label="Sort by name, currently ascending">
                    Name<span class="sort-arrow" aria-hidden="true"></span>
                </button>
                <button type="button" class="sort-btn" data-sort="coverage" data-dir="asc"
                        aria-label="Sort by coverage">
                    Coverage<span class="sort-arrow" aria-hidden="true"></span>
                </button>
            </div>
        </div>

        <div class="grid" id="components">

            <?php foreach ($components as $component): ?>
            <a class="card" href="<?= htmlspecialchars($component['path']) ?>" target="_blank" rel="noopener"
               data-name="<?= htmlspecialchars($component['name']) ?>"
               data-coverage="<?= $component['coverage'] ?? -1 ?>">

                <div class="card-head">
                    <h2><?= htmlspecialchars($component['name']) ?></h2>
                    <img class="badge" src="coverage.php?comp=<?= urlencode($component['name']) ?>"
                         width="99" height="20" alt="<?= $component['coverage'] === null ? 'coverage unknown' : 'coverage ' . round($component['coverage']) . '%' ?>" />
                </div>

                <dl class="metrics">
                    <?php foreach ($metricLabels as $key => $label): ?>
                    <?php $metric = $component['metrics'][$key]; ?>
                    <div class="metric">
                        <dt><?= $label ?></dt>
                        <dd>
                            <span class="metric-pct"><?= $metric['percent'] === null ? 'n/a' : number_format($metric['percent'], 2) . '%' ?></span>
                            <span class="metric-ratio"><?= $metric['ratio']['covered'] ?>&thinsp;/&thinsp;<?= $metric['ratio']['total'] ?></span>
                        </dd>
                        <div class="bar">
                            <span class="lv-<?= coverage_level($metric['percent']) ?>" style="width: <?= $metric['percent'] ?? 0 ?>%"></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </dl>

                <?php if ($component['generated']): ?>
                <p class="card-foot">Generated <?= date('M j, Y', $component['generated']) ?></p>
                <?php endif; ?>

            </a>
            <?php endforeach; ?>

        </div>

        <p class="grid-empty" id="empty" hidden>No components match that filter.</p>

        <?php else: ?>

        <section class="empty-state">
            <h2>No reports available</h2>
            <p>Coverage reports have not been published yet. Please check back soon.</p>
        </section>

        <?php endif; ?>

        <footer class="foot">
            <p>
                <a href="https://www.popphp.org/" target="_blank" rel="noopener">popphp.org</a> &middot;
                <a href="https://docs.popphp.org/" target="_blank" rel="noopener">Documentation</a> &middot;
                <a href="https://github.com/popphp" target="_blank" rel="noopener">GitHub</a>
            </p>
        </footer>

    </main>

    <script src="js/app.js"></script>

</body>

</html>
