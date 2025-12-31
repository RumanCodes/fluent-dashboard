
// Toggle functionality for Fluent Dashboard
document.addEventListener('DOMContentLoaded', () => {
    const toggleOptions = document.querySelectorAll('.fluent-toggle-option');
    const togglePill = document.querySelector('.fluent-toggle-pill');
    const modeInput = document.getElementById('fluent-dashboard-mode');

    if (!toggleOptions.length || !togglePill || !modeInput) {
        return;
    }

    // Check if FluentDashboard object is available
    if (typeof FluentDashboard === 'undefined') {
        console.error('FluentDashboard object not found');
        return;
    }

    // Add click handlers to both toggle options
    toggleOptions.forEach(option => {
        option.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const targetMode = this.getAttribute('data-mode');
            const currentMode = modeInput.value;

            // Don't do anything if clicking the already active option
            if (targetMode === currentMode) {
                return;
            }

            // Switch the mode
            toggleDashboardMode(targetMode);
        });
    });

    function toggleDashboardMode(mode) {
        // Add loading state
        togglePill.classList.add('loading');

        // Disable clicks during request
        toggleOptions.forEach(opt => opt.style.pointerEvents = 'none');

        // Update UI immediately for better UX
        updateUI(mode);

        fetch(`${FluentDashboard.restUrl}toggle`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': FluentDashboard.nonce
            },
            credentials: 'same-origin',
            body: JSON.stringify({ mode: mode })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update the hidden input
                    modeInput.value = mode;

                    // Show success notification
                    showNotification('Dashboard mode updated successfully!', 'success');

                    // Reload the page to apply changes
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                } else {
                    // Revert UI on error
                    const originalMode = mode === 'fluent' ? 'standard' : 'fluent';
                    updateUI(originalMode);
                    showNotification(data.message || 'Failed to toggle dashboard mode', 'error');
                }
            })
            .catch(error => {
                console.error('Error toggling dashboard mode:', error);
                // Revert UI on error
                const originalMode = mode === 'fluent' ? 'standard' : 'fluent';
                updateUI(originalMode);
                showNotification('An error occurred while switching dashboard mode.', 'error');
            })
            .finally(() => {
                togglePill.classList.remove('loading');
                toggleOptions.forEach(opt => opt.style.pointerEvents = '');
            });
    }

    function updateUI(mode) {
        // Update pill state
        if (mode === 'fluent') {
            togglePill.classList.remove('active-wp');
            togglePill.classList.add('active-fluent');
        } else {
            togglePill.classList.remove('active-fluent');
            togglePill.classList.add('active-wp');
        }

        // Update active states on options
        toggleOptions.forEach(option => {
            const optionMode = option.getAttribute('data-mode');
            if (optionMode === mode) {
                option.classList.add('active');
            } else {
                option.classList.remove('active');
            }
        });
    }

    function showNotification(message, type) {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fluent-notification fluent-notification-${type}`;
        notification.textContent = message;

        // Add styles
        Object.assign(notification.style, {
            position: 'fixed',
            top: '50px',
            right: '20px',
            padding: '12px 20px',
            borderRadius: '8px',
            backgroundColor: type === 'success' ? '#3BD2FC' : '#ef4444',
            color: '#ffffff',
            fontSize: '14px',
            fontWeight: '600',
            boxShadow: '0 4px 12px rgba(0, 0, 0, 0.15)',
            zIndex: '999999',
            opacity: '0',
            transform: 'translateY(-10px)',
            transition: 'all 0.3s ease',
            maxWidth: '300px',
            wordWrap: 'break-word'
        });

        // Append to body
        document.body.appendChild(notification);

        // Trigger animation
        setTimeout(() => {
            notification.style.opacity = '1';
            notification.style.transform = 'translateY(0)';
        }, 10);

        // Remove after 3 seconds
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateY(-10px)';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }

    // Prevent default link behavior on the admin bar item
    const adminBarItem = document.querySelector('#wp-admin-bar-fluent-dashboard-toggle > .ab-item');
    if (adminBarItem) {
        adminBarItem.addEventListener('click', function(e) {
            e.preventDefault();
        });
    }
});

// Vue app for dashboard (if needed)
import { createApp } from 'vue'
import AdminApp from "./AdminApp.vue";

function mountApp(component, selector) {
    const el = document.querySelector(selector)
    if (el) {
        const app = createApp(component)
        app.mount(selector)
    }
}

// Only mount Vue app if the element exists
if (document.getElementById('my-vue-admin-app')) {
    mountApp(AdminApp, '#my-vue-admin-app')
}
