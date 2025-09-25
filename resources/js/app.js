// Minimal JavaScript for the application
import './bootstrap';

// Only load what we need
document.addEventListener('DOMContentLoaded', function() {
    
    // Initialize tooltips if present
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    if (tooltipTriggerList.length > 0) {
        // Dynamically import Bootstrap tooltip
        import('bootstrap/js/dist/tooltip').then(({ default: Tooltip }) => {
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new Tooltip(tooltipTriggerEl);
            });
        });
    }
    
    // Initialize dropdowns if present
    const dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
    if (dropdownElementList.length > 0) {
        import('bootstrap/js/dist/dropdown').then(({ default: Dropdown }) => {
            dropdownElementList.map(function (dropdownToggleEl) {
                return new Dropdown(dropdownToggleEl);
            });
        });
    }
    
    // Initialize collapse/navbar toggle if present
    const collapseElementList = [].slice.call(document.querySelectorAll('[data-bs-toggle="collapse"]'));
    if (collapseElementList.length > 0) {
        import('bootstrap/js/dist/collapse').then(({ default: Collapse }) => {
            collapseElementList.map(function (collapseEl) {
                return new Collapse(collapseEl, { toggle: false });
            });
        });
    }
    
    // Auto-dismiss alerts
    const alertList = document.querySelectorAll('.alert-dismissible');
    alertList.forEach(function(alert) {
        setTimeout(function() {
            const alertInstance = bootstrap.Alert ? new bootstrap.Alert(alert) : null;
            if (alertInstance) {
                alertInstance.close();
            } else {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 150);
            }
        }, 5000);
    });
    
    // Form validation helpers
    const forms = document.querySelectorAll('.needs-validation');
    Array.prototype.slice.call(forms).forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
    
    // Loading states for buttons
    const submitButtons = document.querySelectorAll('button[type="submit"]');
    submitButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            if (this.form && this.form.checkValidity()) {
                this.disabled = true;
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Loading...';
                
                // Re-enable after 10 seconds as fallback
                setTimeout(() => {
                    this.disabled = false;
                    this.innerHTML = originalText;
                }, 10000);
            }
        });
    });
    
    // Smooth scrolling for anchor links
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    anchorLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
    
    // Table responsive wrapper
    const tables = document.querySelectorAll('table:not(.table-responsive table)');
    tables.forEach(function(table) {
        if (!table.parentElement.classList.contains('table-responsive')) {
            const wrapper = document.createElement('div');
            wrapper.className = 'table-responsive';
            table.parentNode.insertBefore(wrapper, table);
            wrapper.appendChild(table);
        }
    });
    
    // Auto-resize textareas
    const textareas = document.querySelectorAll('textarea');
    textareas.forEach(function(textarea) {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = this.scrollHeight + 'px';
        });
    });
});

// Utility functions
window.showAlert = function(message, type = 'success') {
    const alertContainer = document.getElementById('alert-container') || document.body;
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show`;
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    alertContainer.insertAdjacentElement('afterbegin', alert);
    
    // Auto dismiss after 5 seconds
    setTimeout(() => {
        if (alert.parentElement) {
            alert.classList.remove('show');
            setTimeout(() => alert.remove(), 150);
        }
    }, 5000);
};

// Performance optimization
window.addEventListener('load', function() {
    // Remove loading states
    document.body.classList.remove('loading');
    
    // Lazy load images if any
    const lazyImages = document.querySelectorAll('img[data-src]');
    if (lazyImages.length > 0 && 'IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });
        
        lazyImages.forEach(img => imageObserver.observe(img));
    }
});