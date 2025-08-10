import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// CSRF token setup
const token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

// Intercept responses to handle common errors
window.axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response?.status === 419) {
            // CSRF token mismatch - refresh the page
            window.location.reload();
        }
        
        if (error.response?.status === 401) {
            // Unauthorized - redirect to login
            window.location.href = '/login';
        }
        
        if (error.response?.status === 403) {
            // Forbidden - show error message
            console.error('Access denied:', error.response.data.message);
        }
        
        if (error.response?.status === 422) {
            // Validation errors - these are handled by the form components
            console.log('Validation errors:', error.response.data.errors);
        }
        
        if (error.response?.status >= 500) {
            // Server error - show generic error message
            console.error('Server error occurred. Please try again later.');
        }
        
        return Promise.reject(error);
    }
);

// Global error handler for unhandled promise rejections
window.addEventListener('unhandledrejection', event => {
    console.error('Unhandled promise rejection:', event.reason);
});

// Global error handler for JavaScript errors
window.addEventListener('error', event => {
    console.error('JavaScript error:', event.error);
});

// Utility functions
window.formatFileSize = (bytes) => {
    if (bytes === 0) return '0 Bytes';
    
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

window.formatDate = (date) => {
    if (!date) return '';
    
    const d = new Date(date);
    return d.toLocaleDateString();
};

window.formatDateTime = (date) => {
    if (!date) return '';
    
    const d = new Date(date);
    return d.toLocaleString();
};

window.debounce = (func, wait) => {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
};

window.throttle = (func, limit) => {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
};