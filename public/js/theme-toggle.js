/**
 * CI4 CMS Theme Toggle
 * DaisyUI data-theme 기반 다크/라이트 모드 전환
 */
(function() {
    'use strict';

    var DARK = 'nord';
    var LIGHT = 'nord-light';
    var STORAGE_KEY = 'theme';
    var TRANSITION_CLASS = 'theme-transitioning';
    var TRANSITION_DURATION = 300;

    function getCurrentTheme() {
        return document.documentElement.getAttribute('data-theme') || DARK;
    }

    function setTheme(theme, animate) {
        if (animate) {
            document.documentElement.classList.add(TRANSITION_CLASS);
        }

        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem(STORAGE_KEY, theme);
        updateToggleUI(theme);

        if (animate) {
            setTimeout(function() {
                document.documentElement.classList.remove(TRANSITION_CLASS);
            }, TRANSITION_DURATION);
        }
    }

    function updateToggleUI(theme) {
        var isDark = (theme === DARK);
        var toggleButtons = document.querySelectorAll('.theme-toggle-btn');

        toggleButtons.forEach(function(btn) {
            var sunIcon = btn.querySelector('.theme-icon-sun');
            var moonIcon = btn.querySelector('.theme-icon-moon');
            var label = btn.querySelector('.theme-label');

            if (sunIcon && moonIcon) {
                sunIcon.classList.toggle('hidden', !isDark);
                moonIcon.classList.toggle('hidden', isDark);
            }
            if (label) {
                label.textContent = isDark ? '라이트 모드로 전환' : '다크 모드로 전환';
            }
            btn.setAttribute('aria-label', isDark ? '라이트 모드로 전환' : '다크 모드로 전환');
            btn.setAttribute('aria-pressed', isDark ? 'true' : 'false');
        });
    }

    function toggleTheme() {
        var current = getCurrentTheme();
        var next = (current === DARK) ? LIGHT : DARK;
        setTheme(next, true);
    }

    function watchSystemPreference() {
        if (!window.matchMedia) return;

        var mediaQuery = window.matchMedia('(prefers-color-scheme: light)');
        mediaQuery.addEventListener('change', function(e) {
            var stored = localStorage.getItem(STORAGE_KEY);
            if (!stored) {
                setTheme(e.matches ? LIGHT : DARK, true);
            }
        });
    }

    function init() {
        var toggleButtons = document.querySelectorAll('.theme-toggle-btn');
        toggleButtons.forEach(function(btn) {
            btn.addEventListener('click', toggleTheme);
        });

        updateToggleUI(getCurrentTheme());
        watchSystemPreference();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
