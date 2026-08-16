/**
 * Pop PHP Framework Code Coverage
 *
 * Filtering and sorting for the component index. No dependencies.
 */
(function () {
    'use strict';

    var filter    = document.getElementById('filter');
    var grid      = document.getElementById('components');
    var count     = document.getElementById('count');
    var empty     = document.getElementById('empty');
    var sideNav   = document.querySelector('.side-nav');
    var sideEmpty = document.querySelector('.side-nav-empty');
    var toggle    = document.querySelector('.nav-toggle');
    var body      = document.getElementById('sidebar-body');

    var cards = Array.prototype.slice.call(grid.querySelectorAll('.card'));
    var links = Array.prototype.slice.call(sideNav.querySelectorAll('a'));

    /**
     * Show only the components whose name contains the search term.
     */
    function applyFilter() {
        var term    = filter.value.trim().toLowerCase();
        var visible = 0;

        cards.forEach(function (card) {
            var match = card.dataset.name.indexOf(term) !== -1;
            card.hidden = !match;
            if (match) {
                visible++;
            }
        });

        links.forEach(function (link) {
            link.parentNode.hidden = link.dataset.name.indexOf(term) === -1;
        });

        count.textContent = visible + (visible === 1 ? ' component' : ' components');
        empty.hidden      = (visible > 0);
        sideEmpty.hidden  = (visible > 0);
    }

    /**
     * Reorder the grid by component name or by line coverage.
     *
     * Ascending means A to Z by name, or worst coverage first. popphp leads
     * the ascending name order, matching how the page is first rendered.
     */
    function applySort(key, dir) {
        var sign = (dir === 'desc') ? -1 : 1;

        cards.sort(function (a, b) {
            var result;

            if (key === 'coverage') {
                result = parseFloat(a.dataset.coverage) - parseFloat(b.dataset.coverage);
            } else if (a.dataset.name === 'popphp' || b.dataset.name === 'popphp') {
                result = (a.dataset.name === 'popphp') ? -1 : 1;
            } else {
                result = a.dataset.name.localeCompare(b.dataset.name);
            }

            return result * sign;
        });

        cards.forEach(function (card) {
            grid.appendChild(card);
        });
    }

    filter.addEventListener('input', applyFilter);

    filter.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            filter.value = '';
            applyFilter();
        }
    });

    // "/" anywhere on the page jumps to the filter box
    document.addEventListener('keydown', function (e) {
        if (e.key === '/' && document.activeElement !== filter) {
            e.preventDefault();
            if (body && toggle && getComputedStyle(toggle).display !== 'none') {
                openNav();
            }
            filter.focus();
        }
    });

    // Clicking the active chip reverses it; clicking the other one starts ascending
    var sortButtons = Array.prototype.slice.call(document.querySelectorAll('.sort-btn'));
    var sortKey     = 'name';

    sortButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            var key = button.dataset.sort;
            var dir = 'asc';

            if (key === sortKey) {
                dir = (button.dataset.dir === 'asc') ? 'desc' : 'asc';
            }

            sortKey = key;

            sortButtons.forEach(function (other) {
                other.classList.toggle('is-active', other === button);
                if (other !== button) {
                    other.dataset.dir = 'asc';
                    other.setAttribute('aria-label', 'Sort by ' + other.dataset.sort);
                }
            });

            button.dataset.dir = dir;
            button.setAttribute(
                'aria-label',
                'Sort by ' + key + ', currently ' + (dir === 'asc' ? 'ascending' : 'descending')
            );

            applySort(key, dir);
        });
    });

    function openNav() {
        body.classList.add('is-open');
        toggle.setAttribute('aria-expanded', 'true');
    }

    /**
     * Light is the default; a dark preference is remembered across visits.
     * The head of the document applies the stored value before first paint.
     */
    var THEME_KEY   = 'pop-coverage-theme';
    var themeButton = document.querySelector('.theme-toggle');
    var root        = document.documentElement;

    function setTheme(theme) {
        if (theme === 'dark') {
            root.setAttribute('data-theme', 'dark');
        } else {
            root.removeAttribute('data-theme');
        }

        themeButton.setAttribute(
            'aria-label',
            theme === 'dark' ? 'Switch to light theme' : 'Switch to dark theme'
        );

        try {
            localStorage.setItem(THEME_KEY, theme);
        } catch (e) {}
    }

    if (themeButton) {
        setTheme(root.getAttribute('data-theme') === 'dark' ? 'dark' : 'light');

        themeButton.addEventListener('click', function () {
            setTheme(root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark');
        });
    }

    if (toggle && body) {
        toggle.addEventListener('click', function () {
            var open = body.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
    }
}());
