// AI Content Agent Debug Script
// Add this to the WordPress admin page to help debug the error

(function() {
    'use strict';
    
    console.log('ACA Debug: Starting debug script');
    console.log('ACA Debug: WordPress data available:', !!window.acaData);
    
    if (window.acaData) {
        console.log('ACA Debug: WordPress data:', window.acaData);
    }
    
    // Check if root element exists
    const rootElement = document.getElementById('root');
    console.log('ACA Debug: Root element found:', !!rootElement);
    
    if (rootElement) {
        console.log('ACA Debug: Root element HTML:', rootElement.innerHTML.substring(0, 200));
    }
    
    // Monitor for React errors
    const originalError = console.error;
    console.error = function(...args) {
        if (args[0] && args[0].toString().includes('React')) {
            console.log('ACA Debug: React error detected:', args);
        }
        originalError.apply(console, args);
    };
    
    // Check for React and ReactDOM
    console.log('ACA Debug: React available:', typeof window.React !== 'undefined');
    console.log('ACA Debug: ReactDOM available:', typeof window.ReactDOM !== 'undefined');
    
    // Log when the script loads
    window.addEventListener('load', function() {
        console.log('ACA Debug: Page fully loaded');
        setTimeout(function() {
            const rootContent = document.getElementById('root');
            if (rootContent) {
                console.log('ACA Debug: Root content after load:', rootContent.innerHTML.substring(0, 200));
            }
        }, 1000);
    });
    
    console.log('ACA Debug: Debug script initialized');
})();