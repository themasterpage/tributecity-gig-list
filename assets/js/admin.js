/**
 * Admin enhancements for TributeCity Gig List styling tab.
 */
(function () {
	'use strict';

	function markSelected(container, cardSelector) {
		var cards = container.querySelectorAll(cardSelector);
		cards.forEach(function (card) {
			card.addEventListener('click', function () {
				if (card.closest('.is-disabled')) {
					return;
				}
				cards.forEach(function (c) {
					c.classList.remove('is-selected');
				});
				card.classList.add('is-selected');
			});
		});
	}

	function initThemePicker() {
		var useTheme = document.getElementById('tributecity_use_theme_styles');
		var picker = document.getElementById('tributecity-theme-picker');
		if (!useTheme || !picker) {
			return;
		}

		var radios = picker.querySelectorAll('input[type="radio"]');

		function sync() {
			var inherit = !!useTheme.checked;
			picker.classList.toggle('is-disabled', inherit);
			picker.setAttribute('aria-disabled', inherit ? 'true' : 'false');

			radios.forEach(function (radio) {
				radio.disabled = inherit;
			});
		}

		useTheme.addEventListener('change', sync);
		markSelected(picker, '.tributecity-theme-card');
		sync();
	}

	function initLayoutPicker() {
		var layoutPicker = document.getElementById('tributecity-layout-picker');
		if (!layoutPicker) {
			return;
		}

		markSelected(layoutPicker, '.tributecity-layout-card');

		var applyBtn = document.getElementById('tributecity-apply-theme-layout');
		var note = document.getElementById('tributecity-layout-suggestion-note');
		if (!applyBtn) {
			return;
		}

		var suggestions = {};
		try {
			suggestions = JSON.parse(layoutPicker.getAttribute('data-suggestions') || '{}') || {};
		} catch (e) {
			suggestions = {};
		}

		function selectedTheme() {
			var checked = document.querySelector(
				'#tributecity-theme-picker input[type="radio"]:checked'
			);
			return checked ? checked.value : '';
		}

		function applySuggestedLayout() {
			var theme = selectedTheme();
			var layout = suggestions[theme];
			if (!layout) {
				return;
			}

			var radio = document.getElementById('tributecity_list_layout_' + layout);
			if (!radio) {
				return;
			}

			radio.checked = true;
			var cards = layoutPicker.querySelectorAll('.tributecity-layout-card');
			cards.forEach(function (card) {
				card.classList.toggle('is-selected', card.getAttribute('for') === radio.id);
			});

			if (note) {
				note.textContent = 'Suggested layout for this theme: ' + layout + '.';
			}
		}

		applyBtn.addEventListener('click', function (event) {
			event.preventDefault();
			applySuggestedLayout();
		});
	}

	function initFontSizePicker() {
		var root = document.querySelector('.tributecity-font-size__options');
		if (!root) {
			return;
		}
		var options = root.querySelectorAll('.tributecity-font-size__option');
		options.forEach(function (option) {
			option.addEventListener('click', function () {
				options.forEach(function (o) {
					o.classList.remove('is-selected');
				});
				option.classList.add('is-selected');
			});
		});
	}

	function init() {
		initThemePicker();
		initLayoutPicker();
		initFontSizePicker();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
