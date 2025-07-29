// Debug Helper for AI Content Agent Plugin
// Add this script before your main index.js to catch errors

(function() {
    'use strict';
    
    // Enhanced error logging
    window.onerror = function(message, source, lineno, colno, error) {
        console.group('🚨 JavaScript Error Detected');
        console.error('Message:', message);
        console.error('Source:', source);
        console.error('Line:', lineno, 'Column:', colno);
        console.error('Error Object:', error);
        if (error && error.stack) {
            console.error('Stack Trace:', error.stack);
        }
        console.groupEnd();
        
        // Send to your error tracking service if needed
        // trackError({ message, source, lineno, colno, stack: error?.stack });
        
        return false; // Don't prevent default error handling
    };
    
    // Handle unhandled promise rejections
    window.addEventListener('unhandledrejection', function(event) {
        console.group('🚨 Unhandled Promise Rejection');
        console.error('Promise:', event.promise);
        console.error('Reason:', event.reason);
        console.groupEnd();
    });
    
    // Wrap React error boundaries
    if (window.React) {
        const originalCreateElement = window.React.createElement;
        window.React.createElement = function(...args) {
            try {
                return originalCreateElement.apply(this, args);
            } catch (error) {
                console.group('🚨 React createElement Error');
                console.error('Error in React.createElement:', error);
                console.error('Arguments:', args);
                console.groupEnd();
                throw error;
            }
        };
    }
    
    console.log('🔍 Debug helper loaded - Enhanced error tracking enabled');
})();