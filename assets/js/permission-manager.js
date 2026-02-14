/**
 * Permission Controller JavaScript
 * Manages UI elements based on user permissions
 * Usage: Include this file in your HTML pages with auth_check.php included
 */

(function() {
    'use strict';

    /**
     * PermissionManager - Control UI visibility based on user permissions
     */
    const PermissionManager = {
        // Store current user permissions from auth data
        permissions: window.AuthData || {},

        /**
         * Initialize permission management
         */
        init: function() {
            // Wait for DOM to be ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => this.applyPermissions());
            } else {
                this.applyPermissions();
            }

            // Also apply permissions after any AJAX requests
            this.setupMutationObserver();
        },

        /**
         * Apply permissions to all UI elements
         */
        applyPermissions: function() {
            // Control button visibility
            this.controlButtons();

            // Control form visibility
            this.controlForms();

            // Control sections visibility
            this.controlSections();

            // Setup action handlers with permission checks
            this.setupActionHandlers();
        },

        /**
         * Control button visibility based on permissions
         * Buttons should have data-permission attribute
         * Examples:
         * - data-permission="add_project"
         * - data-permission="edit_skills"
         * - data-permission="delete_project"
         */
        controlButtons: function() {
            const buttons = document.querySelectorAll('[data-permission]');

            buttons.forEach(button => {
                const permissionCode = button.getAttribute('data-permission');
                const allowed = this.hasPermission(permissionCode);

                if (allowed) {
                    button.classList.remove('permission-denied');
                    button.disabled = false;
                    button.style.display = '';
                } else {
                    button.classList.add('permission-denied');
                    button.disabled = true;
                    button.style.display = 'none';

                    // Add title to show why it's disabled
                    button.title = `Access denied: You don't have permission to perform this action.`;
                }
            });
        },

        /**
         * Control form visibility based on permissions
         * Forms should have data-permission-form attribute
         */
        controlForms: function() {
            const forms = document.querySelectorAll('[data-permission-form]');

            forms.forEach(form => {
                const permissionCode = form.getAttribute('data-permission-form');
                const allowed = this.hasPermission(permissionCode);

                if (allowed) {
                    form.style.display = '';
                    form.classList.remove('permission-denied');
                } else {
                    form.style.display = 'none';
                    form.classList.add('permission-denied');
                }
            });
        },

        /**
         * Control section/div visibility
         * Sections should have data-permission-section attribute
         */
        controlSections: function() {
            const sections = document.querySelectorAll('[data-permission-section]');

            sections.forEach(section => {
                const permissionCode = section.getAttribute('data-permission-section');
                const allowed = this.hasPermission(permissionCode);

                if (allowed) {
                    section.style.display = '';
                    section.classList.remove('permission-denied');
                } else {
                    section.style.display = 'none';
                    section.classList.add('permission-denied');
                }
            });
        },

        /**
         * Check if user has specific permission
         * @param {string} permissionCode - Permission code (e.g., 'add_project', 'edit_skills')
         * @returns {boolean}
         */
        hasPermission: function(permissionCode) {
            if (!permissionCode || !this.permissions) {
                return false;
            }

            const perm = this.permissions;

            // Parse permission by prefix
            if (permissionCode.startsWith('add_') || permissionCode.startsWith('create_')) {
                return perm.canCreate === true;
            }
            if (permissionCode.startsWith('view_') || permissionCode.startsWith('read_')) {
                return perm.canRead === true;
            }
            if (permissionCode.startsWith('edit_') || permissionCode.startsWith('update_')) {
                return perm.canUpdate === true;
            }
            if (permissionCode.startsWith('delete_') || permissionCode.startsWith('remove_')) {
                return perm.canDelete === true;
            }

            // Check if exact permission exists in all_permissions array
            if (perm.all_permissions && Array.isArray(perm.all_permissions)) {
                return perm.all_permissions.includes(permissionCode);
            }

            return false;
        },

        /**
         * Setup action handlers that require permission checks
         */
        setupActionHandlers: function() {
            // Add click handler to all permission-controlled buttons
            const buttons = document.querySelectorAll('[data-permission][data-action]');

            buttons.forEach(button => {
                button.addEventListener('click', (e) => {
                    const permissionCode = button.getAttribute('data-permission');

                    if (!this.hasPermission(permissionCode)) {
                        e.preventDefault();
                        e.stopPropagation();
                        alert('Access Denied: You do not have permission to perform this action.');
                        return false;
                    }
                });
            });
        },

        /**
         * Setup observer for dynamically added elements
         */
        setupMutationObserver: function() {
            const observer = new MutationObserver((mutations) => {
                // Re-apply permissions to newly added elements
                mutations.forEach((mutation) => {
                    if (mutation.type === 'childList') {
                        // Check if any permission-controlled elements were added
                        const newElements = mutation.addedNodes;
                        let hasPermissionElements = false;

                        newElements.forEach(node => {
                            if (node.nodeType === 1) { // Element nodes only
                                if (node.hasAttribute && (
                                    node.hasAttribute('data-permission') ||
                                    node.hasAttribute('data-permission-form') ||
                                    node.hasAttribute('data-permission-section')
                                )) {
                                    hasPermissionElements = true;
                                }
                            }
                        });

                        if (hasPermissionElements) {
                            this.applyPermissions();
                        }
                    }
                });
            });

            // Start observing the document for changes
            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        },

        /**
         * Get current user permissions
         */
        getPermissions: function() {
            return this.permissions;
        },

        /**
         * Check if user can perform specific CRUD operation
         */
        canCreate: function() {
            return this.permissions.canCreate === true;
        },

        canRead: function() {
            return this.permissions.canRead === true;
        },

        canUpdate: function() {
            return this.permissions.canUpdate === true;
        },

        canDelete: function() {
            return this.permissions.canDelete === true;
        },

        /**
         * Show permission denied message
         */
        showAccessDenied: function(message) {
            message = message || 'Access Denied: You do not have permission to perform this action.';
            alert(message);
        },

        /**
         * Refresh UI permissions (useful after permission updates)
         */
        refresh: function() {
            // Update permissions from AuthData
            if (window.AuthData) {
                this.permissions = window.AuthData;
            }
            this.applyPermissions();
        }
    };

    /**
     * Initialize when DOM is ready
     */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            PermissionManager.init();
        });
    } else {
        PermissionManager.init();
    }

    // Expose globally
    window.PermissionManager = PermissionManager;
})();
