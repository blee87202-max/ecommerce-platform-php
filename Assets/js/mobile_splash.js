/**
 * mobile_splash.js
 * JavaScript logic for Mobile Splash Screen
 * Part of the MVC architecture - Separated from HTML/PHP
 */

(() => {
    'use strict';

    // ===== Configuration =====
    const CONFIG = {
        animationDuration: 300,
        overlayId: 'mobile-splash-overlay',
        checkboxId: 'dont-show-again',
        baseUrl: typeof PROJECT_BASE !== 'undefined' ? PROJECT_BASE : '../'
    };

    // ===== Utility Functions =====
    
    /**
     * Get the base URL for API calls
     * @returns {string} Base URL
     */
    const getBaseUrl = () => {
        return CONFIG.baseUrl.endsWith('/') ? CONFIG.baseUrl : CONFIG.baseUrl + '/';
    };

    /**
     * Close the mobile splash overlay with animation
     */
    const closeMobileSplash = () => {
        const overlay = document.getElementById(CONFIG.overlayId);
        if (!overlay) return;

        overlay.style.animation = 'fadeOut 0.3s ease-out forwards';
        setTimeout(() => {
            if (overlay.parentNode) {
                overlay.parentNode.removeChild(overlay);
            }
        }, CONFIG.animationDuration);
    };

    /**
     * Continue shopping (close splash)
     */
    const continueShopping = () => {
        closeMobileSplash();
    };

    /**
     * Switch to desktop version
     */
    const viewDesktopVersion = () => {
        const apiUrl = getBaseUrl() + 'Api/set_view_preference.php?view=desktop';
        
        fetch(apiUrl, {
            method: 'GET',
            credentials: 'same-origin'
        })
        .then(response => {
            if (response.ok) {
                window.location.reload();
            } else {
                console.error('Failed to set view preference');
            }
        })
        .catch(error => {
            console.error('Error switching to desktop version:', error);
            // Fallback: reload anyway
            window.location.reload();
        });
    };

    /**
     * Handle "Don't show again" checkbox
     */
    const handleDontShowAgain = () => {
        const checkbox = document.getElementById(CONFIG.checkboxId);
        if (!checkbox || !checkbox.checked) return;

        const apiUrl = getBaseUrl() + 'Api/set_view_preference.php?action=hide_splash';
        
        fetch(apiUrl, {
            method: 'GET',
            credentials: 'same-origin'
        })
        .catch(error => {
            console.error('Error hiding splash:', error);
        });
    };

    /**
     * Setup event listeners for the splash screen
     */
    const setupEventListeners = () => {
        const overlay = document.getElementById(CONFIG.overlayId);
        if (!overlay) return;

        // Close button
        const closeBtn = overlay.querySelector('.splash-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', closeMobileSplash);
        }

        // Primary button (Continue Shopping)
        const primaryBtn = overlay.querySelector('.splash-btn-primary');
        if (primaryBtn) {
            primaryBtn.addEventListener('click', continueShopping);
        }

        // Secondary button (Desktop Version)
        const secondaryBtn = overlay.querySelector('.splash-btn-secondary');
        if (secondaryBtn) {
            secondaryBtn.addEventListener('click', viewDesktopVersion);
        }

        // Checkbox
        const checkbox = overlay.querySelector('.splash-checkbox input[type="checkbox"]');
        if (checkbox) {
            checkbox.addEventListener('change', handleDontShowAgain);
        }

        // Click outside to close (optional)
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) {
                closeMobileSplash();
            }
        });
    };

    /**
     * Inject fadeOut animation if not already present
     */
    const injectAnimations = () => {
        // Check if fadeOut animation already exists
        const styleSheets = document.styleSheets;
        let fadeOutExists = false;

        try {
            for (let i = 0; i < styleSheets.length; i++) {
                const rules = styleSheets[i].cssRules || styleSheets[i].rules;
                for (let j = 0; j < rules.length; j++) {
                    if (rules[j].name === 'fadeOut') {
                        fadeOutExists = true;
                        break;
                    }
                }
                if (fadeOutExists) break;
            }
        } catch (e) {
            // CORS or other issues, skip check
        }

        // If not found, inject it
        if (!fadeOutExists) {
            const style = document.createElement('style');
            style.textContent = `
                @keyframes fadeOut {
                    from {
                        opacity: 1;
                    }
                    to {
                        opacity: 0;
                    }
                }
            `;
            document.head.appendChild(style);
        }
    };

    /**
     * Initialize the mobile splash screen
     */
    const init = () => {
        const overlay = document.getElementById(CONFIG.overlayId);
        if (!overlay) return;

        // Inject animations
        injectAnimations();

        // Setup event listeners
        setupEventListeners();

        // Expose functions to global scope for inline handlers
        window.closeMobileSplash = closeMobileSplash;
        window.continueShopping = continueShopping;
        window.viewDesktopVersion = viewDesktopVersion;
        window.handleDontShowAgain = handleDontShowAgain;
    };

    // ===== Initialize on DOM Ready =====
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        // DOM is already ready
        init();
    }

})();