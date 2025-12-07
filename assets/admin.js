// Admin-specific JavaScript for WebDev Settings Management

import $ from 'jquery';
import Convert from 'ansi-to-html';

// Initialize DataTables for lists
$(document).ready(function() {
    // Services table
    if ($('#services-table').length) {
        $('#services-table').DataTable({
            responsive: true,
            autoWidth: false,
            pageLength: 25,
            order: [[0, 'asc']],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search services..."
            }
        });
    }

    // Tasks table
    if ($('#tasks-table').length) {
        $('#tasks-table').DataTable({
            responsive: true,
            autoWidth: false,
            pageLength: 25,
            order: [[0, 'asc']],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search tasks..."
            }
        });
    }

    // Tests table
    if ($('#tests-table').length) {
        $('#tests-table').DataTable({
            responsive: true,
            autoWidth: false,
            pageLength: 25,
            order: [[0, 'asc']],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search tests..."
            }
        });
    }

    // Projects table
    if ($('#projects-table').length) {
        let compatibilityFilter = 'compatible'; // Default filter
        const projectsTableElement = $('#projects-table')[0];
        
        // Custom filter function for WebDev compatibility
        const compatibilityFilterFunction = function(settings, data, dataIndex) {
            // Only apply to projects table - check if this is the projects table
            if (settings.nTable !== projectsTableElement) {
                return true;
            }
            
            const table = $('#projects-table').DataTable();
            const row = table.row(dataIndex).node();
            // Use attr() to get the raw string value, not jQuery's data() which may convert it
            const compatibleValue = $(row).attr('data-compatible');
            const isCompatible = compatibleValue === 'true';
            
            if (compatibilityFilter === 'compatible') {
                return isCompatible;
            } else if (compatibilityFilter === 'incompatible') {
                return !isCompatible;
            } else {
                return true; // Show all
            }
        };
        
        $.fn.dataTable.ext.search.push(compatibilityFilterFunction);
        
        const projectsTable = $('#projects-table').DataTable({
            responsive: true,
            autoWidth: false,
            pageLength: 10,
            order: [[0, 'asc']],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search projects..."
            }
        });

        // Filter by WebDev compatibility
        $('.filter-btn').on('click', function() {
            const filter = $(this).data('filter');
            
            // Update button states
            $('.filter-btn').removeClass('active');
            $(this).addClass('active');
            
            // Update filter state
            compatibilityFilter = filter;
            
            // Redraw table to apply filter
            projectsTable.draw();
        });

        // Set default filter to compatible (trigger initial filter)
        projectsTable.draw();
    }

    // Test execution handler
    $(document).on('click', '.test-execute-link', function(e) {
        e.preventDefault();
        const projectPath = $(this).data('project-path');
        const testKey = $(this).data('test-key');
        const testName = $(this).data('test-name');
        
        if (!projectPath || !testKey) {
            console.error('Missing project path or test key');
            return;
        }
        
        executeTest(projectPath, testKey, testName);
    });

    // IDE opening handler
    $(document).on('click', '.ide-open-link', function(e) {
        e.preventDefault();
        const projectPath = $(this).data('project-path');
        const ide = $(this).data('ide');
        
        if (!projectPath || !ide) {
            console.error('Missing project path or IDE');
            return;
        }
        
        openIde(projectPath, ide);
    });

    // Log tables
    if ($('.log-table').length) {
        $('.log-table').each(function() {
            $(this).DataTable({
                responsive: true,
                autoWidth: false,
                pageLength: 50,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                order: [[0, 'desc']]
            });
        });
    }

    // Service toggle (active/inactive)
    $('.service-toggle').on('change', function() {
        const serviceKey = $(this).data('service');
        const isActive = $(this).is(':checked');
        
        $.ajax({
            url: '/settings/services/toggle/' + serviceKey,
            method: 'POST',
            data: {
                active: isActive
            },
            success: function(response) {
                if (response.success) {
                    // Show success toast
                    $(document).Toasts('create', {
                        class: 'bg-success',
                        title: 'Success',
                        body: 'Service status updated successfully',
                        autohide: true,
                        delay: 3000
                    });
                }
            },
            error: function() {
                // Revert toggle
                this.checked = !isActive;
                
                // Show error toast
                $(document).Toasts('create', {
                    class: 'bg-danger',
                    title: 'Error',
                    body: 'Failed to update service status',
                    autohide: true,
                    delay: 3000
                });
            }
        });
    });

    // Delete confirmation
    $('.delete-confirm').on('click', function(e) {
        if (!confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
            e.preventDefault();
            return false;
        }
    });

    // Dynamic form collections (for tasks and tests)
    $('.add-collection-item').on('click', function(e) {
        e.preventDefault();
        const container = $(this).data('container');
        const prototype = $('#' + container).data('prototype');
        const index = $('#' + container).children('.collection-item').length;
        
        const newForm = prototype.replace(/__name__/g, index);
        $(newForm).appendTo('#' + container);
    });

    // Remove collection item
    $(document).on('click', '.remove-collection-item', function(e) {
        e.preventDefault();
        $(this).closest('.collection-item').remove();
    });

    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // Initialize popovers
    $('[data-toggle="popover"]').popover();

    // Project Selection Modal
    const projectModal = $('#projectSelectionModal');
    if (projectModal.length) {
        // Check if project is selected (from data attribute)
        const projectSelected = projectModal.data('project-selected') === 'true' || 
                               projectModal.data('project-selected') === true;
        
        // Load projects when modal is shown (attach handler BEFORE showing modal)
        projectModal.on('show.bs.modal', function() {
            loadProjects();
        });

        // Also handle when modal is already shown (in case it was shown before handler was attached)
        projectModal.on('shown.bs.modal', function() {
            // If projects haven't been loaded yet, load them
            if ($('#project-list').children().length === 0 && $('#project-list-loading').is(':visible')) {
                loadProjects();
            }
        });

        // Handle project selection
        $(document).on('click', '.project-item', function(e) {
            e.preventDefault();
            const path = $(this).data('path');
            if (path) {
                selectProject(path);
            }
        });

        // Handle search input
        $(document).on('input', '#project-search', function() {
            filterProjects($(this).val().toLowerCase());
        });

        // Auto-open modal if no project is selected (do this AFTER attaching handlers)
        if (!projectSelected) {
            // Show modal using Bootstrap 5 API
            try {
                // Try Bootstrap 5 native API first
                if (window.bootstrap && window.bootstrap.Modal) {
                    const modalInstance = new window.bootstrap.Modal(projectModal[0], {
                        backdrop: 'static',
                        keyboard: false
                    });
                    modalInstance.show();
                } else {
                    // Fallback to jQuery method
                    projectModal.modal({
                        backdrop: 'static',
                        keyboard: false
                    });
                }
            } catch (e) {
                console.error('Error showing modal:', e);
                // Fallback to jQuery method
                projectModal.modal({
                    backdrop: 'static',
                    keyboard: false
                });
            }
            // Call loadProjects directly - the event handler is a backup
            // Use a small delay to ensure modal is in DOM
            setTimeout(function() {
                loadProjects();
            }, 50);
        }
    }
});

// Store all projects for filtering
let allProjects = [];

// Load projects for the modal
function loadProjects() {
    const loadingEl = $('#project-list-loading');
    const errorEl = $('#project-list-error');
    const errorMessageEl = $('#project-list-error-message');
    const containerEl = $('#project-list-container');
    const listEl = $('#project-list');
    const emptyEl = $('#project-list-empty');
    const searchInput = $('#project-search');

    // Show loading, hide others
    loadingEl.show();
    errorEl.hide();
    containerEl.hide();
    emptyEl.hide();
    listEl.empty();
    searchInput.val(''); // Clear search

    $.ajax({
        url: '/project/list',
        method: 'GET',
        success: function(response) {
            loadingEl.hide();

            if (response.success && response.projects && response.projects.length > 0) {
                // Store all projects for filtering
                allProjects = response.projects;
                
                // Display projects
                displayProjects(allProjects);
                containerEl.show();
            } else {
                allProjects = [];
                emptyEl.show();
                containerEl.show();
            }
        },
        error: function(xhr, status, error) {
            loadingEl.hide();
            let errorMessage = 'Failed to load projects. Please try again.';
            
            if (xhr.responseJSON && xhr.responseJSON.error) {
                errorMessage = xhr.responseJSON.error;
            } else if (xhr.status === 0) {
                errorMessage = 'Network error. Please check your connection.';
            } else if (xhr.status) {
                errorMessage = `Error ${xhr.status}: ${error || 'Unknown error'}`;
            }

            errorMessageEl.text(errorMessage);
            errorEl.show();
            containerEl.show();
        }
    });
}

// Display projects in the list
function displayProjects(projects) {
    const listEl = $('#project-list');
    const noResultsEl = $('#project-list-no-results');
    const emptyEl = $('#project-list-empty');
    
    listEl.empty();
    emptyEl.hide();
    
    if (projects.length === 0) {
        noResultsEl.show();
        return;
    }
    
    noResultsEl.hide();
    
    projects.forEach(function(project) {
        const projectItem = $('<a>')
            .addClass('list-group-item list-group-item-action project-item')
            .attr('href', '#')
            .attr('data-path', project.path)
            .html(`
                <div class="d-flex w-100 justify-content-between">
                    <h6 class="mb-1">
                        <i class="fas fa-folder text-primary"></i> ${escapeHtml(project.name)}
                    </h6>
                </div>
                <div class="mt-2">
                    ${project.phpVersion ? `<span class="badge bg-info me-1">PHP ${escapeHtml(project.phpVersion)}</span>` : ''}
                    ${project.nodejsVersion ? `<span class="badge bg-success">Node.js ${escapeHtml(project.nodejsVersion)}</span>` : ''}
                </div>
                <small class="text-muted d-block mt-1">${escapeHtml(project.path)}</small>
            `);
        listEl.append(projectItem);
    });
}

// Filter projects by search term
function filterProjects(searchTerm) {
    if (!searchTerm || searchTerm.trim() === '') {
        displayProjects(allProjects);
        return;
    }
    
    const filtered = allProjects.filter(function(project) {
        return project.name.toLowerCase().includes(searchTerm);
    });
    
    displayProjects(filtered);
}

// Select a project
function selectProject(path) {
    const projectModal = $('#projectSelectionModal');
    const modalBody = projectModal.find('.modal-body');
    
    // Show loading state
    modalBody.html(`
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Selecting project...</span>
            </div>
            <p class="mt-2 text-muted">Selecting project...</p>
        </div>
    `);

    $.ajax({
        url: '/project/select',
        method: 'POST',
        data: {
            path: path
        },
        success: function(response) {
            if (response.success) {
                // Reload the page to reflect the new project selection
                window.location.reload();
            } else {
                showProjectError(response.error || 'Failed to select project');
            }
        },
        error: function(xhr) {
            let errorMessage = 'Failed to select project. Please try again.';
            
            if (xhr.responseJSON && xhr.responseJSON.error) {
                errorMessage = xhr.responseJSON.error;
            }

            showProjectError(errorMessage);
        }
    });
}

// Show error in modal
function showProjectError(message) {
    const projectModal = $('#projectSelectionModal');
    const modalBody = projectModal.find('.modal-body');
    
    modalBody.html(`
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i> ${escapeHtml(message)}
        </div>
        <button type="button" class="btn btn-primary" onclick="loadProjects(); $('#project-list-error').hide();">
            <i class="fas fa-redo"></i> Try Again
        </button>
    `);
}

// Escape HTML to prevent XSS
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text ? text.replace(/[&<>"']/g, m => map[m]) : '';
}

// Flash message auto-hide
setTimeout(function() {
    $('.alert-dismissible').fadeOut('slow');
}, 5000);

// IDE opening function
function openIde(projectPath, ide) {
    $.ajax({
        url: '/projects/ide/open',
        method: 'POST',
        data: {
            projectPath: projectPath,
            ide: ide
        },
        timeout: 10000, // 10 seconds timeout
        success: function(response) {
            if (response.success) {
                // Show success toast
                $(document).Toasts('create', {
                    class: 'bg-success',
                    title: 'Success',
                    body: response.message || 'Opening IDE...',
                    autohide: true,
                    delay: 3000
                });
            } else {
                // Show error toast
                $(document).Toasts('create', {
                    class: 'bg-danger',
                    title: 'Error',
                    body: response.error || 'Failed to open IDE',
                    autohide: true,
                    delay: 5000
                });
            }
        },
        error: function(xhr, status, error) {
            let errorMessage = 'Failed to open IDE. Please try again.';
            
            if (xhr.responseJSON && xhr.responseJSON.error) {
                errorMessage = xhr.responseJSON.error;
            } else if (xhr.status === 0) {
                errorMessage = 'Network error. Please check your connection.';
            } else if (xhr.status) {
                errorMessage = `Error ${xhr.status}: ${error || 'Unknown error'}`;
            }
            
            // Show error toast
            $(document).Toasts('create', {
                class: 'bg-danger',
                title: 'Error',
                body: errorMessage,
                autohide: true,
                delay: 5000
            });
        }
    });
}

// Test execution function
function executeTest(projectPath, testKey, testName) {
    const modal = $('#testExecutionModal');
    const titleEl = $('#test-execution-title');
    const loadingEl = $('#test-execution-loading');
    const outputEl = $('#test-execution-output');
    const terminalOutputEl = $('#test-terminal-output');
    const errorEl = $('#test-execution-error');
    const errorMessageEl = $('#test-execution-error-message');
    
    // Initialize ANSI converter
    const convert = new Convert({
        fg: '#FFF',
        bg: '#000',
        newline: true,
        escapeXML: true
    });
    
    // Show modal and reset state
    titleEl.text(testName || 'Executing Test');
    loadingEl.removeClass('d-none');
    outputEl.addClass('d-none');
    errorEl.addClass('d-none');
    terminalOutputEl.empty();
    
    // Show modal
    if (window.bootstrap && window.bootstrap.Modal) {
        const modalInstance = new window.bootstrap.Modal(modal[0]);
        modalInstance.show();
    } else {
        modal.modal('show');
    }
    
    // Execute test via AJAX
    $.ajax({
        url: '/projects/tests/execute',
        method: 'POST',
        data: {
            projectPath: projectPath,
            testKey: testKey
        },
        timeout: 360000, // 6 minutes timeout
        success: function(response) {
            loadingEl.addClass('d-none');
            
            if (response.success) {
                // Convert ANSI to HTML
                const htmlOutput = convert.toHtml(response.output);
                
                // Display output
                terminalOutputEl.html(htmlOutput);
                outputEl.removeClass('d-none');
                
                // Scroll to bottom
                const terminalContainer = terminalOutputEl.parent();
                terminalContainer.scrollTop(terminalContainer[0].scrollHeight);
                
                // Update title with exit code
                if (response.exitCode !== undefined && response.exitCode !== 0) {
                    titleEl.html(`${testName || 'Test'} <span class="badge badge-danger">Exit Code: ${response.exitCode}</span>`);
                } else {
                    titleEl.html(`${testName || 'Test'} <span class="badge badge-success">Success</span>`);
                }
            } else {
                errorMessageEl.text(response.error || 'Failed to execute test');
                errorEl.removeClass('d-none');
            }
        },
        error: function(xhr, status, error) {
            loadingEl.addClass('d-none');
            let errorMessage = 'Failed to execute test. Please try again.';
            
            if (xhr.responseJSON && xhr.responseJSON.error) {
                errorMessage = xhr.responseJSON.error;
            } else if (xhr.status === 0) {
                errorMessage = 'Network error. Please check your connection.';
            } else if (xhr.status === 408 || status === 'timeout') {
                errorMessage = 'Test execution timed out. The command may be taking too long.';
            } else if (xhr.status) {
                errorMessage = `Error ${xhr.status}: ${error || 'Unknown error'}`;
            }
            
            errorMessageEl.text(errorMessage);
            errorEl.removeClass('d-none');
        }
    });
}

console.log('Admin JavaScript loaded successfully!');
