/**
 * Interactive archive table: search + client-side pagination + rows-per-page.
 */
(function () {
	'use strict';

	function text(key, fallback) {
		var i18n = (window.tributecityGigListArchive && window.tributecityGigListArchive.i18n) || {};
		return i18n[key] || fallback;
	}

	function format(template) {
		var args = Array.prototype.slice.call(arguments, 1);
		return String(template).replace(/%(\d+)\$d|%d/g, function (match, n) {
			if (n) {
				return String(args[parseInt(n, 10) - 1]);
			}
			return String(args.shift());
		});
	}

	function parsePerPage(value, total) {
		if (value === 'all' || value === '0') {
			return Math.max(total, 1);
		}
		var n = parseInt(value, 10);
		if (!n || n < 1) {
			return 10;
		}
		return n;
	}

	function initPanel(panel) {
		var body = panel.querySelector('[data-tcgl-archive-body]');
		var searchInput = panel.querySelector('.tributecity-gig-list__archive-search');
		var statusEl = panel.querySelector('[data-tcgl-archive-status]');
		var emptyEl = panel.querySelector('[data-tcgl-archive-empty]');
		var pager = panel.querySelector('[data-tcgl-archive-pager]');
		var prevBtn = panel.querySelector('[data-tcgl-page-prev]');
		var nextBtn = panel.querySelector('[data-tcgl-page-next]');
		var pageStatus = panel.querySelector('[data-tcgl-page-status]');
		var perPageSelect = panel.querySelector('[data-tcgl-per-page]');

		if (!body) {
			return;
		}

		var rows = Array.prototype.slice.call(body.querySelectorAll('[data-tcgl-row]'));
		var initial = panel.getAttribute('data-per-page') || '10';
		// Map legacy default 15 → 10 for the new control.
		if (String(initial) === '15') {
			initial = '10';
		}

		var state = {
			query: '',
			page: 1,
			perPageValue: initial,
			filtered: rows.slice()
		};

		if (perPageSelect) {
			// Prefer a valid option; default to 10.
			var hasOption = false;
			Array.prototype.forEach.call(perPageSelect.options, function (opt) {
				if (opt.value === String(state.perPageValue)) {
					hasOption = true;
				}
			});
			if (!hasOption) {
				state.perPageValue = '10';
			}
			perPageSelect.value = String(state.perPageValue);
		}

		function currentPerPage() {
			return parsePerPage(state.perPageValue, state.filtered.length || rows.length);
		}

		function isShowAll() {
			return state.perPageValue === 'all' || state.perPageValue === '0';
		}

		function applyFilter() {
			var q = state.query.trim().toLowerCase();
			if (!q) {
				state.filtered = rows.slice();
			} else {
				state.filtered = rows.filter(function (row) {
					var blob = row.getAttribute('data-search') || '';
					return blob.indexOf(q) !== -1;
				});
			}
			state.page = 1;
			render();
		}

		function totalPages() {
			var perPage = currentPerPage();
			if (isShowAll()) {
				return 1;
			}
			return Math.max(1, Math.ceil(state.filtered.length / perPage));
		}

		function render() {
			var perPage = currentPerPage();
			var pages = totalPages();
			if (state.page > pages) {
				state.page = pages;
			}
			if (state.page < 1) {
				state.page = 1;
			}

			var start = isShowAll() ? 0 : (state.page - 1) * perPage;
			var end = isShowAll() ? state.filtered.length : start + perPage;
			var visibleCount = 0;

			rows.forEach(function (row) {
				row.hidden = true;
				row.setAttribute('aria-hidden', 'true');
			});

			state.filtered.forEach(function (row, index) {
				if (index >= start && index < end) {
					row.hidden = false;
					row.removeAttribute('aria-hidden');
					visibleCount += 1;
				}
			});

			if (emptyEl) {
				emptyEl.hidden = state.filtered.length > 0;
			}
			if (body) {
				body.hidden = state.filtered.length === 0;
			}

			if (statusEl) {
				if (state.filtered.length === 0) {
					statusEl.textContent = text('noResults', 'No shows match your search.');
				} else if (isShowAll() || visibleCount >= state.filtered.length) {
					statusEl.textContent = format(
						text('resultsAll', 'Showing all %d shows'),
						state.filtered.length
					);
				} else {
					statusEl.textContent = format(
						text('results', 'Showing %1$d of %2$d shows'),
						visibleCount,
						state.filtered.length
					);
				}
			}

			// Pager nav (prev/next/status) can hide when everything fits one page,
			// but the rows control stays available via the select.
			var multi = !isShowAll() && state.filtered.length > perPage;
			if (prevBtn) {
				prevBtn.hidden = !multi || state.filtered.length === 0;
				prevBtn.disabled = state.page <= 1;
			}
			if (nextBtn) {
				nextBtn.hidden = !multi || state.filtered.length === 0;
				nextBtn.disabled = state.page >= pages;
			}
			if (pageStatus) {
				pageStatus.hidden = !multi || state.filtered.length === 0;
				pageStatus.textContent = format(
					text('pageStatus', 'Page %1$d of %2$d'),
					state.page,
					pages
				);
			}

			// Keep the whole pager bar visible whenever there are results so the rows select remains usable.
			if (pager) {
				pager.hidden = state.filtered.length === 0;
			}
		}

		if (searchInput) {
			searchInput.addEventListener('input', function () {
				state.query = searchInput.value || '';
				applyFilter();
			});
		}

		if (perPageSelect) {
			perPageSelect.addEventListener('change', function () {
				state.perPageValue = perPageSelect.value || '10';
				state.page = 1;
				panel.setAttribute('data-per-page', state.perPageValue);
				render();
			});
		}

		if (prevBtn) {
			prevBtn.addEventListener('click', function () {
				if (state.page > 1) {
					state.page -= 1;
					render();
				}
			});
		}

		if (nextBtn) {
			nextBtn.addEventListener('click', function () {
				if (state.page < totalPages()) {
					state.page += 1;
					render();
				}
			});
		}

		render();
	}

	function boot() {
		var panels = document.querySelectorAll('[data-tcgl-archive]');
		panels.forEach(initPanel);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}
})();
