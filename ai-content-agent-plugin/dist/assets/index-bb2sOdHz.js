(function() {
  "use strict";
  var __vite_style__ = document.createElement("style");
  __vite_style__.textContent = `/* WordPress Admin Compatible Styles for AI Content Agent (ACA) */

/* CSS Variables for WordPress Admin Bar Height */

:root {
  --wp-admin-bar-height: 32px; /* Default desktop height */
  
  /* Enhanced contrast colors - WCAG AA compliant */
  --aca-text-high-contrast: #212529;    /* 16.75:1 on white */
  --aca-text-medium-contrast: #495057;  /* 8.59:1 on white */
  --aca-link-accessible: #0056b3;       /* 7.27:1 on white */
  --aca-link-hover-accessible: #004085; /* 9.67:1 on white */
  
  /* Status colors with proper contrast */
  --aca-success-accessible: #155724;    /* 7.44:1 on white */
  --aca-warning-accessible: #856404;    /* 5.94:1 on white */
  --aca-error-accessible: #721c24;      /* 8.80:1 on white */
}

@media screen and (max-width: 782px) {
  :root {
    --wp-admin-bar-height: 46px; /* WordPress mobile admin bar height */
  }
}

/* Handle no admin bar scenario */

body.no-admin-bar {
  --wp-admin-bar-height: 0px;
}

/* Ensure WordPress admin bar is touch-friendly on mobile */

@media screen and (max-width: 782px) {
  /* Don't interfere with WordPress admin bar, just ensure our elements don't block it */
  #wpadminbar {
    /* WordPress handles this, we just make sure we don't conflict */
    position: fixed !important;
    top: 0 !important;
    z-index: 99999 !important; /* WordPress standard */
  }
  
  /* Ensure our hamburger doesn't conflict with admin bar */
  .aca-mobile-hamburger {
    /* Position it safely away from admin bar */
    top: calc(var(--wp-admin-bar-height, 46px) + 5px) !important;
    pointer-events: auto !important;
    touch-action: manipulation !important;
  }
}

/* Remove Tailwind CDN import and create custom WordPress-compatible styles */

/* @import 'https://cdn.tailwindcss.com'; */

/* Reset and base styles for WordPress compatibility */

#root {
  margin: 0 !important;
  padding: 0 !important;
  background: #f0f0f1 !important;
  color: #1e1e1e !important;
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
  line-height: 1.4;
  font-size: 13px;
}

.wp-admin #root {
  margin: 0 !important;
  padding: 0 !important;
  background: #f0f0f1 !important;
}

.wp-admin #root * {
  box-sizing: border-box;
}

/* Main container */

.aca-container {
  display: flex;
  min-height: 100vh;
  background: #f0f0f1;
}

/* Sidebar styles */

.aca-sidebar {
  width: 240px;
  background: #23282d;
  border-right: 1px solid #ccd0d4;
  position: fixed;
  top: var(--wp-admin-bar-height, 32px); /* Dynamic height instead of fixed 32px */
  left: 160px; /* Right next to WordPress admin menu */
  height: calc(100vh - var(--wp-admin-bar-height, 32px)); /* Dynamic calculation instead of fixed 32px */
  z-index: 9999;
  overflow-y: auto; /* Allow internal scrolling */
  transform: translateX(-100%);
  transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 2px 0 20px rgba(0, 0, 0, 0.15);
  backdrop-filter: blur(10px);
}

.aca-sidebar.open {
  transform: translateX(0);
}

.aca-sidebar-header {
  padding: 20px;
  border-bottom: 1px solid #32373c;
}

.aca-sidebar-title {
  color: #ffffff;
  font-size: 18px;
  font-weight: 600;
  margin: 0 0 5px 0;
}

.aca-sidebar-subtitle {
  color: #a7aaad;
  font-size: 12px;
  text-decoration: none;
}

.aca-sidebar-subtitle:hover {
  color: #00a0d2;
}

.aca-sidebar-nav {
  padding: 10px 0;
}

.aca-nav-item {
  display: flex;
  align-items: center;
  width: 100%;
  padding: 12px 20px;
  color: #a7aaad;
  text-decoration: none;
  border: none;
  background: none;
  font-size: 13px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.aca-nav-item:hover {
  background: #32373c;
  color: #ffffff;
}

.aca-nav-item.active {
  background: #0073aa;
  color: #ffffff;
}

.aca-nav-item-icon {
  width: 20px;
  height: 20px;
  margin-right: 12px;
  fill: currentColor;
}

/* Fix for icons in main content area - they should be blue, not black */

.aca-main .aca-nav-item-icon {
  fill: #0073aa;
}

/* Icons in buttons should be white */

.aca-button .aca-nav-item-icon {
  fill: #ffffff;
}

/* Icons in secondary buttons should be blue */

.aca-button.secondary .aca-nav-item-icon {
  fill: #0073aa;
}

/* Icons in list items should be blue */

.aca-list-item .aca-nav-item-icon {
  fill: #0073aa;
}

/* Enhanced icon contrast for dark backgrounds */

.aca-sidebar .aca-nav-item-icon {
  fill: #a7aaad;
  transition: fill 0.2s ease;
}

.aca-sidebar .aca-nav-item:hover .aca-nav-item-icon {
  fill: #ffffff;
}

.aca-sidebar .aca-nav-item.active .aca-nav-item-icon {
  fill: #ffffff;
}

/* Icon contrast fixes for dark card backgrounds */

.aca-card[style*="background: #23282d"] .aca-nav-item-icon,
.aca-card[style*="background:#23282d"] .aca-nav-item-icon {
  fill: #ffffff !important;
}

/* Icon contrast for buttons on dark backgrounds */

.aca-button.secondary[style*="background: #23282d"] .aca-nav-item-icon,
.aca-button.secondary[style*="background:#23282d"] .aca-nav-item-icon {
  fill: #ffffff !important;
}

/* Better contrast for disabled buttons */

.aca-button:disabled .aca-nav-item-icon {
  fill: #a7aaad;
}

/* Ensure good contrast for all icon contexts */

.aca-nav-item-icon {
  filter: contrast(1.2);
}

/* Special handling for stroke-based icons in secondary buttons */

.aca-button.secondary .aca-nav-item-icon {
  fill: #0073aa;
  stroke: currentColor;
  stroke-width: 1.5;
}

.aca-button.secondary:hover .aca-nav-item-icon {
  fill: #005a87;
  stroke: currentColor;
}

/* Toast notification icons */

.aca-toast .aca-nav-item-icon {
  fill: currentColor;
  filter: brightness(1.1);
}

/* Dashboard action button icons - fix alignment and colors */

.aca-action-button .aca-action-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 48px;
  height: 48px;
  margin: 0 auto 12px auto;
  background: #f0f6ff;
  border-radius: 8px;
}

.aca-action-button .aca-action-icon svg {
  fill: #0073aa !important;
  width: 24px;
  height: 24px;
}

.aca-action-button:hover .aca-action-icon {
  background: #e3f2fd;
}

.aca-action-button:hover .aca-action-icon svg {
  fill: #005a87 !important;
}

/* Pipeline item icons - fix alignment and colors */

.aca-stat-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
  background: #f0f6ff;
  border-radius: 6px;
  flex-shrink: 0;
}

.aca-stat-icon svg {
  fill: #0073aa !important;
  width: 20px;
  height: 20px;
}

/* Main content area */

.aca-main {
  flex: 1;
  margin-left: 160px; /* Account for WordPress admin menu */
  padding: 20px;
  background: #f0f0f1;
  min-height: 100vh;
}

@media (min-width: 783px) {
  .aca-sidebar {
    transform: translateX(0); /* Show sidebar on desktop */
  }
  
  .aca-main {
    margin-left: 240px; /* Sidebar width exactly - border creates 1px gap */
    padding: 20px;
  }
  
  /* High specificity for normal state */
  body.wp-admin.toplevel_page_ai-content-agent-plugin .aca-main {
    margin-left: 240px !important; /* Sidebar width exactly - border creates 1px gap */
  }
}

/* Auto-fold WordPress menu support (when menu is collapsed) */

body.auto-fold .aca-sidebar {
  left: 36px !important; /* Collapsed WordPress menu width */
}

body.auto-fold .aca-main {
  margin-left: 240px !important; /* Sidebar width exactly - border creates 1px gap */
}

/* High specificity auto-fold rules */

body.wp-admin.auto-fold.toplevel_page_ai-content-agent-plugin .aca-main {
  margin-left: 240px !important; /* Sidebar width exactly - border creates 1px gap */
}

/* Auto-fold #root container adjustment */

body.auto-fold #root {
  left: 36px !important; /* Collapsed WordPress menu width */
  width: calc(100% - 36px) !important; /* Adjust width for collapsed menu */
}

/* Mobile header */

.aca-mobile-header {
  display: flex;
  align-items: center;
  padding: 15px 20px;
  background: #23282d;
  border-bottom: 1px solid #ccd0d4;
  margin: -20px -20px 20px -20px;
}

@media (min-width: 783px) {
  .aca-mobile-header {
    display: none;
  }
}

.aca-menu-toggle {
  background: none;
  border: none;
  color: #a7aaad;
  cursor: pointer;
  padding: 5px;
  margin-right: 10px;
}

.aca-menu-toggle:hover {
  color: #ffffff;
}

/* Page header */

.aca-page-header {
  margin-bottom: 30px;
  padding-bottom: 15px;
  border-bottom: 1px solid #ccd0d4;
}

.aca-page-title {
  font-size: 24px;
  font-weight: 400;
  color: #23282d;
  margin: 0 0 10px 0;
}

.aca-page-description {
  color: #646970;
  font-size: 13px;
  margin: 0;
}

/* Cards and containers */

.aca-card {
  background: #ffffff;
  border: 1px solid #ccd0d4;
  border-radius: 4px;
  padding: 20px;
  margin-bottom: 20px;
  box-shadow: 0 1px 1px rgba(0, 0, 0, 0.04);
}

.aca-card-header {
  margin-bottom: 15px;
  padding-bottom: 10px;
  border-bottom: 1px solid #f0f0f1;
}

.aca-card-title {
  font-size: 16px;
  font-weight: 600;
  color: #23282d;
  margin: 0;
}

/* Grid layouts */

.aca-grid {
  display: grid;
  gap: 20px;
}

.aca-grid-2 {
  grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
}

.aca-grid-3 {
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
}

.aca-grid-4 {
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
}

/* Buttons */

.aca-button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 8px 16px;
  border: 1px solid #0073aa;
  background: #0073aa;
  color: #ffffff !important;
  text-decoration: none;
  border-radius: 3px;
  font-size: 13px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.aca-button:hover {
  background: #005a87;
  border-color: #005a87;
  color: #ffffff !important;
}

.aca-button:focus {
  box-shadow: 0 0 0 2px #0073aa40;
  outline: none;
}

.aca-button.secondary {
  background: #ffffff;
  color: #0073aa !important;
  border-color: #0073aa;
}

.aca-button.secondary:hover {
  background: #f6f7f7;
  color: #005a87 !important;
}

.aca-button.large {
  padding: 12px 24px;
  font-size: 14px;
}

.aca-button:disabled {
  background: #f6f7f7 !important;
  border-color: #dcdcde;
  color: #a7aaad !important;
  cursor: not-allowed;
}

/* Form elements */

.aca-form-group {
  margin-bottom: 20px;
}

.aca-label {
  display: block;
  margin-bottom: 5px;
  font-weight: 600;
  color: #23282d;
}

.aca-input,
.aca-textarea,
.aca-select {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #8c8f94;
  border-radius: 4px;
  font-size: 13px;
  background: #ffffff;
  color: #2c3338;
}

.aca-input:focus,
.aca-textarea:focus,
.aca-select:focus {
  border-color: #0073aa;
  box-shadow: 0 0 0 2px #0073aa40;
  outline: none;
}

/* Alert/notification styles */

.aca-alert {
  padding: 15px;
  margin-bottom: 20px;
  border-left: 4px solid;
  border-radius: 0 4px 4px 0;
}

.aca-alert.success {
  background: #f0f6fc;
  border-color: #00a32a;
  color: #00a32a;
}

.aca-alert.error {
  background: #fcf0f1;
  border-color: #d63638;
  color: #d63638;
}

.aca-alert.warning {
  background: #fcf9e8;
  border-color: #dba617;
  color: #dba617;
}

.aca-alert.info {
  background: #f0f6fc;
  border-color: #0073aa;
  color: #0073aa;
}

/* Loading spinner */

.aca-spinner {
  display: inline-block;
  width: 16px;
  height: 16px;
  border: 2px solid #f3f3f3;
  border-top: 2px solid #0073aa;
  border-radius: 50%;
  animation: aca-spin 1s linear infinite;
  vertical-align: middle;
}

@keyframes aca-spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Stats/dashboard items */

.aca-stat-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 15px;
  background: #f6f7f7;
  border-radius: 4px;
  margin-bottom: 10px;
}

.aca-stat-info {
  display: flex;
  align-items: center;
}

.aca-stat-icon {
  width: 24px;
  height: 24px;
  margin-right: 12px;
  fill: #0073aa;
}

.aca-stat-title {
  font-weight: 600;
  color: #23282d;
  margin: 0 0 2px 0;
}

.aca-stat-count {
  color: #646970;
  font-size: 12px;
}

/* Action buttons */

.aca-action-button {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  padding: 20px;
  background: #ffffff;
  border: 1px solid #ccd0d4;
  border-radius: 4px;
  text-align: left;
  cursor: pointer;
  transition: all 0.2s ease;
  text-decoration: none;
  color: inherit;
}

.aca-action-button:hover {
  border-color: #0073aa;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  transform: translateY(-1px);
}

.aca-action-icon {
  width: 32px;
  height: 32px;
  padding: 8px;
  background: #0073aa;
  border-radius: 4px;
  margin-bottom: 12px;
  fill: #ffffff;
}

.aca-action-title {
  font-size: 16px;
  font-weight: 600;
  color: #23282d;
  margin: 0 0 5px 0;
}

.aca-action-description {
  color: #646970;
  font-size: 13px;
  margin: 0;
}

/* List items */

.aca-list {
  background: #ffffff;
  border: 1px solid #ccd0d4;
  border-radius: 4px;
  overflow: hidden;
}

.aca-list-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 15px 20px;
  border-bottom: 1px solid #f0f0f1;
}

.aca-list-item:last-child {
  border-bottom: none;
}

.aca-list-item:hover {
  background: #f6f7f7;
}

/* Overlay for mobile sidebar */

.aca-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  z-index: 9998;
  display: none;
}

.aca-overlay.show {
  display: block;
}

@media (max-width: 782px) {
  .aca-sidebar {
    z-index: 9999;
  }
}

/* Modern animation classes */

.aca-fade-in {
  animation: aca-fade-in 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.aca-slide-up {
  animation: aca-slide-up 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

.aca-scale-in {
  animation: aca-scale-in 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

@keyframes aca-fade-in {
  from {
    opacity: 0;
    transform: translateY(20px) scale(0.95);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

@keyframes aca-slide-up {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes aca-scale-in {
  from {
    opacity: 0;
    transform: scale(0.9);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

/* Toast notifications */

.aca-toast-container {
  position: fixed;
  bottom: 20px;
  right: 20px;
  z-index: 10000;
  max-width: 400px;
}

.aca-toast {
  padding: 15px 20px;
  margin-bottom: 10px;
  border-radius: 4px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  animation: aca-toast-slide-in 0.3s ease-out;
}

.aca-toast.success {
  background: #00a32a;
  color: #ffffff;
}

.aca-toast.error {
  background: #d63638;
  color: #ffffff;
}

.aca-toast.warning {
  background: #dba617;
  color: #ffffff;
}

.aca-toast.info {
  background: #0073aa;
  color: #ffffff;
}

@keyframes aca-toast-slide-in {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

@keyframes aca-toast-progress {
  from {
    width: 100%;
  }
  to {
    width: 0%;
  }
}

/* Custom scrollbar for webkit browsers */

::-webkit-scrollbar {
  width: 12px;
}

::-webkit-scrollbar-track {
  background: #f0f0f1;
}

::-webkit-scrollbar-thumb {
  background: #c3c4c7;
  border-radius: 6px;
}

::-webkit-scrollbar-thumb:hover {
  background: #a7aaad;
}

/* Enhanced Responsive Design */

@media (max-width: 1200px) {
  .aca-grid-2 {
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
  }
  
  .aca-welcome-section {
    padding: 25px !important;
  }
  
  .aca-welcome-section h1 {
    font-size: 24px !important;
  }
}

@media (max-width: 900px) {
  .aca-grid-2 {
    grid-template-columns: 1fr;
  }
  
  .aca-grid-4 {
    grid-template-columns: repeat(2, 1fr);
  }
  
  .aca-welcome-section {
    padding: 20px !important;
  }
  
  .aca-welcome-section h1 {
    font-size: 22px !important;
  }
  
  .aca-sidebar-header {
    padding: 15px !important;
  }
  
  .aca-sidebar-nav {
    padding: 0 12px !important;
  }
}

@media (max-width: 782px) {
  .aca-main {
    margin-left: 0 !important; /* Reset sidebar margin on mobile */
    padding: 15px;
  }
  
  .aca-grid-2,
  .aca-grid-3,
  .aca-grid-4 {
    grid-template-columns: 1fr;
  }
  
  .aca-card {
    padding: 15px;
  }
  
  /* Mobile-first welcome section */
  .aca-welcome-section {
    padding: 20px !important;
    margin-bottom: 20px !important;
  }
  
  .aca-welcome-section h1 {
    font-size: 20px !important;
  }
  
  .aca-welcome-section p {
    font-size: 14px !important;
  }
  
  /* Mobile sidebar improvements */
  .aca-sidebar {
    width: 100% !important;
    max-width: 320px;
    left: 0 !important; /* Full left on mobile */
    top: 46px !important; /* Mobile admin bar height */
    height: calc(100vh - 46px) !important; /* Fixed height on mobile */
  }
  
  .aca-sidebar-header {
    padding: 15px !important;
  }
  
  .aca-sidebar-header h1 {
    font-size: 16px !important;
  }
  
  .aca-sidebar-nav {
    padding: 0 12px !important;
  }
  
  /* Mobile toast positioning */
  .aca-toast-container {
    left: 10px !important;
    right: 10px !important;
    bottom: 10px !important;
    max-width: none !important;
  }
  
  /* Mobile button improvements */
  .aca-button {
    min-height: 44px;
    font-size: 14px;
  }
  
  .aca-action-button {
    min-height: 120px;
  }
  
  /* Mobile form improvements */
  .aca-input,
  .aca-textarea,
  .aca-select {
    min-height: 44px;
    font-size: 16px; /* Prevents zoom on iOS */
  }
}

@media (max-width: 480px) {
  .aca-main {
    padding: 10px;
  }
  
  .aca-card {
    padding: 12px;
    margin-bottom: 15px;
  }
  
  .aca-welcome-section {
    padding: 15px !important;
  }
  
  .aca-welcome-section h1 {
    font-size: 18px !important;
  }
  
  .aca-welcome-section div[style*="display: flex"] {
    flex-direction: column !important;
    gap: 8px !important;
  }
  
  /* Ultra-mobile optimizations */
  .aca-button {
    width: 100%;
    margin-bottom: 8px;
  }
  
  .aca-grid {
    gap: 10px !important;
  }
}

/* Accessibility Enhancements */

/* Focus indicators */

.aca-button:focus,
.aca-input:focus,
.aca-textarea:focus,
.aca-select:focus {
  outline: 2px solid #3b82f6;
  outline-offset: 2px;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* High contrast mode support */

@media (prefers-contrast: high) {
  .aca-button {
    border-width: 2px;
  }
  
  .aca-card {
    border-width: 2px;
  }
  
  .aca-nav-item {
    border: 1px solid transparent;
  }
  
  .aca-nav-item.active {
    border-color: currentColor;
  }
}

/* Reduced motion support */

@media (prefers-reduced-motion: reduce) {
  *,
  *::before,
  *::after {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
  }
  
  .aca-spinner::before,
  .aca-spinner::after {
    animation: none;
  }
  
  .aca-pulse {
    animation: none;
  }
  
  .aca-skeleton {
    animation: none;
    background: #f0f0f0;
  }
}

/* Dark mode support */

@media (prefers-color-scheme: dark) {
  .aca-skeleton {
    background: linear-gradient(90deg, #374151 25%, #4b5563 50%, #374151 75%);
  }
  
  ::-webkit-scrollbar-track {
    background: #374151;
  }
  
  ::-webkit-scrollbar-thumb {
    background: #6b7280;
  }
  
  ::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
  }
}

/* Touch target improvements */

@media (pointer: coarse) {
  .aca-button,
  .aca-nav-item,
  button {
    min-height: 44px;
    min-width: 44px;
  }
  
  .aca-input,
  .aca-textarea,
  .aca-select {
    min-height: 44px;
  }
}

/* WordPress admin bar compatibility */

@media screen and (min-width: 783px) {
  .wp-admin #root {
    margin-top: 0;
  }
}

/* AGGRESSIVE WORDPRESS ADMIN NOTIFICATIONS HIDING */

/* Hide all WordPress admin notices/notifications on plugin page */

body.toplevel_page_ai-content-agent .notice,
body.toplevel_page_ai-content-agent .updated,
body.toplevel_page_ai-content-agent .error,
body.toplevel_page_ai-content-agent .notice-warning,
body.toplevel_page_ai-content-agent .notice-error,
body.toplevel_page_ai-content-agent .notice-success,
body.toplevel_page_ai-content-agent .notice-info,
body.toplevel_page_ai-content-agent .update-nag,
body.toplevel_page_ai-content-agent .admin-notice,
body.toplevel_page_ai-content-agent .plugin-update-tr,
body.toplevel_page_ai-content-agent .update-message,
body.toplevel_page_ai-content-agent .wp-admin-notice,
body.toplevel_page_ai-content-agent .notice-dismiss,
body.toplevel_page_ai-content-agent .is-dismissible,
body.toplevel_page_ai-content-agent #wpbody-content > .notice,
body.toplevel_page_ai-content-agent #wpbody-content > .updated,
body.toplevel_page_ai-content-agent #wpbody-content > .error,
body.toplevel_page_ai-content-agent .wrap > .notice,
body.toplevel_page_ai-content-agent .wrap > .updated,
body.toplevel_page_ai-content-agent .wrap > .error,
body.toplevel_page_ai-content-agent #wpbody-content > div[class*="notice"],
body.toplevel_page_ai-content-agent #wpbody-content > div[class*="updated"],
body.toplevel_page_ai-content-agent #wpbody-content > div[class*="error"],
body.toplevel_page_ai-content-agent #wpbody-content > div[id*="message"],
body.toplevel_page_ai-content-agent #wpbody-content > div[id*="notice"],
body.toplevel_page_ai-content-agent #wpbody-content > *:not(#root) {
  display: none !important;
  visibility: hidden !important;
  opacity: 0 !important;
  height: 0 !important;
  margin: 0 !important;
  padding: 0 !important;
  position: absolute !important;
  top: -9999px !important;
  left: -9999px !important;
}

/* Allow our own plugin notices to show if needed */

body.toplevel_page_ai-content-agent #root .notice,
body.toplevel_page_ai-content-agent #root .error,
body.toplevel_page_ai-content-agent #root .updated,
body.toplevel_page_ai-content-agent .aca-notice {
  display: block !important;
  visibility: visible !important;
  opacity: 1 !important;
  height: auto !important;
}

/* CALENDAR OVERFLOW AND LAYOUT FIXES */

/* Fix calendar container overflow issues */

body.toplevel_page_ai-content-agent .aca-main {
  overflow-x: hidden !important;
  width: 100% !important;
  max-width: 100% !important;
}

/* Calendar grid container fixes */

body.toplevel_page_ai-content-agent .aca-card {
  overflow: hidden !important;
  width: 100% !important;
  max-width: 100% !important;
  box-sizing: border-box !important;
}

/* Calendar day cells overflow control */

body.toplevel_page_ai-content-agent .aca-card-content > div[style*="display: grid"] > div {
  overflow: hidden !important;
  word-wrap: break-word !important;
  word-break: break-word !important;
  max-width: 100% !important;
  box-sizing: border-box !important;
}

/* Post items in calendar cells - prevent overflow */

body.toplevel_page_ai-content-agent .aca-card-content div[style*="flex-direction: column"] > div {
  overflow: hidden !important;
  text-overflow: ellipsis !important;
  white-space: nowrap !important;
  max-width: 100% !important;
  word-break: break-word !important;
}

/* Ensure calendar doesn't exceed main content bounds */

body.toplevel_page_ai-content-agent .aca-card-content {
  overflow-x: auto !important;
  overflow-y: visible !important;
  max-width: 100% !important;
}

/* SETTINGS DROPDOWN SMOOTH BEHAVIOR */

/* Prevent scroll jump on collapsible sections - disable smooth for fixed position */

body.toplevel_page_ai-content-agent {
  scroll-behavior: auto !important;
}

/* Apply smooth behavior only to our container, not the whole page */

body.toplevel_page_ai-content-agent #root {
  scroll-behavior: smooth !important;
}

/* Bu sınıf eklendiğinde anlık scroll geçişi sağlar */

body.toplevel_page_ai-content-agent #root.no-smooth-scroll {
  scroll-behavior: auto !important;
}

/* Optimize collapsible section transitions */

body.toplevel_page_ai-content-agent .aca-card [id^="section-content-"] {
  transform-origin: top !important;
  backface-visibility: hidden !important;
  -webkit-backface-visibility: hidden !important;
  will-change: transform, opacity !important;
  contain: layout style paint !important;
}

/* Prevent layout shifts during transitions */

body.toplevel_page_ai-content-agent .aca-card {
  contain: layout !important;
}

/* Smooth transitions without affecting scroll */

body.toplevel_page_ai-content-agent .aca-card [id^="section-content-"] * {
  will-change: auto !important;
}

/* Smooth header click behavior */

body.toplevel_page_ai-content-agent .aca-card-header[role="button"] {
  transition: background-color 0.2s ease !important;
}

body.toplevel_page_ai-content-agent .aca-card-header[role="button"]:hover {
  background-color: rgba(0, 0, 0, 0.02) !important;
}

body.toplevel_page_ai-content-agent .aca-card-header[role="button"]:active {
  background-color: rgba(0, 0, 0, 0.05) !important;
  transform: translateY(1px) !important;
  transition: all 0.1s ease !important;
}

/* Progress bar animation for loading states */

@keyframes aca-progress-slide {
  0% {
    transform: translateX(-100%);
  }
  100% {
    transform: translateX(300%);
  }
}

/* Enhanced loading states */

.aca-button:disabled {
  opacity: 0.7;
  cursor: not-allowed !important;
}

.aca-button.loading {
  position: relative;
  overflow: hidden;
}

.aca-button.loading::after {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
  animation: aca-shimmer 1.5s infinite;
}

@keyframes aca-shimmer {
  0% {
    left: -100%;
  }
  100% {
    left: 100%;
  }
}

/* Modern spinner animations */

.aca-spinner {
  display: inline-block;
  width: 16px;
  height: 16px;
  position: relative;
  vertical-align: middle;
}

.aca-spinner::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  border: 2px solid transparent;
  border-top: 2px solid #3b82f6;
  border-right: 2px solid #3b82f6;
  border-radius: 50%;
  animation: aca-spin-modern 0.8s cubic-bezier(0.4, 0, 0.2, 1) infinite;
}

.aca-spinner::after {
  content: '';
  position: absolute;
  top: 2px;
  left: 2px;
  width: calc(100% - 4px);
  height: calc(100% - 4px);
  border: 2px solid transparent;
  border-top: 2px solid #60a5fa;
  border-radius: 50%;
  animation: aca-spin-modern 1.2s cubic-bezier(0.4, 0, 0.2, 1) infinite reverse;
}

@keyframes aca-spin-modern {
  0% { 
    transform: rotate(0deg);
    opacity: 1;
  }
  50% {
    opacity: 0.7;
  }
  100% { 
    transform: rotate(360deg);
    opacity: 1;
  }
}

/* Pulse loading animation */

.aca-pulse {
  animation: aca-pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes aca-pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

/* Skeleton loading */

.aca-skeleton {
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: aca-skeleton-loading 1.5s infinite;
}

@keyframes aca-skeleton-loading {
  0% {
    background-position: 200% 0;
  }
  100% {
    background-position: -200% 0;
  }
}

/* Loading overlay for cards */

.aca-card.loading {
  position: relative;
  pointer-events: none;
}

.aca-card.loading::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(255, 255, 255, 0.8);
  z-index: 10;
  border-radius: 6px;
}

.aca-card.loading::after {
  content: 'Processing...';
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  z-index: 11;
  font-size: 14px;
  color: #0073aa;
  font-weight: 500;
}

/* WordPress Admin Bar Compatibility */

@media screen and (max-width: 782px) {
  .wp-admin .aca-sidebar {
    left: 0 !important; /* Full left on mobile */
    top: 46px !important; /* Mobile admin bar height */
    height: calc(100vh - 46px) !important;
  }
}

@media screen and (min-width: 783px) {
  .wp-admin .aca-sidebar {
    left: 160px !important; /* Right next to WordPress admin menu */
    top: 32px !important; /* Desktop admin bar height */
    height: calc(100vh - 32px) !important;
  }
}

/* No admin bar fallback */

.no-admin-bar .aca-sidebar {
  left: 160px !important; /* Right next to WordPress admin menu */
  top: 0 !important;
  height: 100vh !important;
}

/* Fix for WordPress clear divs causing unwanted spacing - More specific selectors */

body.toplevel_page_ai-content-agent #wpbody-content > .clear,
body.toplevel_page_ai-content-agent #wpbody > .clear,
body.toplevel_page_ai-content-agent .clear {
  display: none !important;
  height: 0 !important;
  margin: 0 !important;
  padding: 0 !important;
}

/* Additional fix for screen-meta and other spacing issues */

body.toplevel_page_ai-content-agent #screen-meta {
  margin-bottom: 0 !important;
  padding-bottom: 0 !important;
}

/* COMPREHENSIVE WORDPRESS ADMIN OVERRIDE - Remove ALL spacing */

body.toplevel_page_ai-content-agent #wpbody-content {
  padding: 0 !important;
  margin: 0 !important;
}

body.toplevel_page_ai-content-agent #wpbody {
  padding: 0 !important;
  margin: 0 !important;
}

body.toplevel_page_ai-content-agent #wpcontent {
  padding: 0 !important;
  margin: 0 !important;
}

/* Ensure our root element takes full space and overrides everything */

body.toplevel_page_ai-content-agent #root {
  margin: 0 !important;
  padding: 0 !important;
  width: calc(100% - 160px) !important; /* Account for WordPress admin menu */
  height: calc(100vh - 32px) !important; /* Account for WordPress admin bar */
  position: fixed !important;
  top: 32px !important; /* Account for WordPress admin bar */
  left: 160px !important; /* Right next to WordPress admin menu */
  right: 0 !important;
  bottom: 0 !important;
  z-index: 999999 !important;
  background: #f0f0f1 !important;
  overflow-y: auto !important;
  overflow-x: hidden !important;
}

/* Prevent page jumping when Settings dropdowns open */

body.toplevel_page_ai-content-agent #root .aca-main {
  max-height: calc(100vh - 32px) !important;
  overflow-y: auto !important;
  scroll-behavior: smooth !important;
}

/* Responsive WordPress admin menu adjustments */

@media screen and (max-width: 960px) and (min-width: 783px) {
  /* Collapsed WordPress menu adjustments */
  .aca-sidebar {
    left: 36px !important; /* Collapsed WordPress menu width */
  }
  
  .aca-main {
    margin-left: 240px !important; /* Sidebar width exactly - border creates 1px gap */
  }
  
  body.toplevel_page_ai-content-agent #root {
    width: calc(100% - 36px) !important; /* Collapsed menu width */
    left: 36px !important; /* Collapsed menu width */
    height: calc(100vh - 32px) !important;
  }
}

@media screen and (max-width: 782px) {
  body.toplevel_page_ai-content-agent #root {
    width: 100% !important; /* Mobile: no side menu */
    left: 0 !important;
    height: calc(100vh - 46px) !important; /* Mobile admin bar is taller */
    top: 46px !important;
  }
}

/* Make our container take full space and override WordPress */

body.toplevel_page_ai-content-agent .aca-container {
  margin: 0 !important;
  padding: 0 !important;
  width: 100% !important;
  height: 100vh !important;
  position: relative !important;
  display: flex !important;
  background: #f0f0f1 !important;
}

/* Hide WordPress admin footer elements on plugin page */

body.toplevel_page_ai-content-agent #footer-thankyou,
body.toplevel_page_ai-content-agent #footer-upgrade,
body.toplevel_page_ai-content-agent #wpfooter {
  display: none !important;
}

/* Enhanced responsive design - ACA specific improvements */

@media screen and (max-width: 768px) {
  /* Improve mobile sidebar without breaking existing 782px styles */
  .aca-sidebar.is-mobile-optimized {
    width: 100% !important;
    max-width: 320px !important;
    transform: translateX(-100%);
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  }
  
  .aca-sidebar.is-mobile-optimized.is-open {
    transform: translateX(0);
  }
  
  /* Improve touch targets for mobile */
  .aca-nav-item {
    min-height: 44px;
    display: flex;
    align-items: center;
  }
  
  .aca-nav-item a {
    padding: 12px 20px;
    width: 100%;
    display: block;
  }
}

/* Mobile overlay for sidebar */

.aca-mobile-overlay {
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  z-index: 99998;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.aca-mobile-overlay.is-visible {
  display: block;
  opacity: 1;
}

/* Mobile menu toggle */

.aca-mobile-menu-toggle {
  display: none;
  position: fixed;
  top: 50px;
  left: 20px;
  z-index: 100000;
  background: #0073aa;
  color: white;
  border: none;
  border-radius: 4px;
  padding: 8px 12px;
  cursor: pointer;
  font-size: 16px;
}

@media screen and (max-width: 768px) {
  .aca-mobile-menu-toggle {
    display: block;
  }
}

/* WCAG AA compliant color improvements */

.aca-plugin-wrapper .aca-accessible-text {
  color: var(--aca-text-high-contrast);
}

.aca-plugin-wrapper .aca-accessible-link {
  color: var(--aca-link-accessible);
}

.aca-plugin-wrapper .aca-accessible-link:hover {
  color: var(--aca-link-hover-accessible);
}

/* High contrast mode support */

@media (prefers-contrast: high) {
  .aca-plugin-wrapper {
    --aca-text-high-contrast: #000000;
    --aca-link-accessible: #0000ee;
  }
}

/* Enhanced Mobile Navigation Fixes - Use higher specificity instead of !important */

@media screen and (max-width: 782px) {
  /* Ensure smooth transition at breakpoint */
  .aca-container .aca-sidebar {
    will-change: transform; /* Optimize animations */
  }
  /* Fix sidebar positioning on mobile */
  .aca-container .aca-sidebar {
    position: fixed;
    top: var(--wp-admin-bar-height, 46px);
    left: 0;
    width: 280px;
    height: calc(100vh - var(--wp-admin-bar-height, 46px));
    transform: translateX(-100%);
    transition: transform 0.3s ease-in-out;
    z-index: 99998; /* Below WordPress admin bar (99999) */
    box-shadow: 2px 0 20px rgba(0, 0, 0, 0.3);
    pointer-events: none; /* Prevent click-through when hidden */
    touch-action: pan-y; /* Allow vertical scrolling */
  }
  
  /* Show sidebar when active - support both open and mobile-active classes */
  .aca-container .aca-sidebar.mobile-active,
  .aca-container .aca-sidebar.open {
    transform: translateX(0);
    pointer-events: auto; /* Re-enable interactions when visible */
  }
  
  /* Improve mobile navigation items */
  .aca-container .aca-sidebar .aca-nav-item {
    min-height: 48px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  }
  
  .aca-container .aca-sidebar .aca-nav-item a {
    padding: 14px 20px;
    font-size: 16px;
    line-height: 1.4;
    display: flex;
    align-items: center;
    gap: 12px;
  }
  
  /* Mobile hamburger menu */
  .aca-mobile-hamburger {
    display: block !important;
    position: fixed !important;
    top: calc(var(--wp-admin-bar-height, 46px) + 10px) !important;
    left: 15px !important;
    z-index: 99997; /* Below sidebar but above content */
    background: #0073aa !important;
    color: white !important;
    border: none !important;
    border-radius: 6px !important;
    padding: 12px 14px !important; /* Larger touch target */
    cursor: pointer !important;
    font-size: 18px !important;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2) !important;
    transition: all 0.2s ease !important;
    min-width: 44px !important; /* WCAG minimum touch target */
    min-height: 44px !important; /* WCAG minimum touch target */
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    -webkit-tap-highlight-color: transparent !important; /* Remove iOS highlight */
    touch-action: manipulation !important; /* Prevent zoom on double-tap */
  }
  
  .aca-mobile-hamburger:hover {
    background: #005a87 !important;
    transform: scale(1.05) !important;
  }
  
  .aca-mobile-hamburger:active {
    transform: scale(0.95) !important;
  }
  
  /* Main content adjustment for mobile */
  .aca-main-content {
    margin-left: 0 !important;
    padding: 20px 15px !important;
    min-height: calc(100vh - var(--wp-admin-bar-height, 46px)) !important;
  }
  
  /* Mobile overlay for sidebar */
  .aca-mobile-overlay {
    display: none !important;
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    width: 100% !important;
    height: 100% !important;
    background: rgba(0, 0, 0, 0.6) !important;
    z-index: 99996; /* Below hamburger menu */
    opacity: 0 !important;
    transition: opacity 0.3s ease !important;
  }
  
  .aca-mobile-overlay.active {
    display: block !important;
    opacity: 1 !important;
  }
  
  /* Ensure touch targets are accessible */
  .aca-sidebar button,
  .aca-sidebar a,
  .aca-sidebar input[type="button"],
  .aca-sidebar input[type="submit"] {
    min-height: 44px !important;
    min-width: 44px !important;
  }
  
  /* Fix mobile scrolling issues */
  .aca-sidebar {
    overflow-y: auto !important;
    -webkit-overflow-scrolling: touch !important;
  }
  
  /* Prevent body scroll when sidebar is open */
  body.aca-sidebar-open {
    overflow: hidden !important;
    position: fixed !important;
    width: 100% !important;
  }
}

/* Tablet specific adjustments */

@media screen and (min-width: 783px) and (max-width: 1024px) {
  .aca-sidebar {
    width: 220px !important;
  }
  
  .aca-main-content {
    margin-left: 220px !important;
  }
}

/* Desktop cleanup - ensure mobile states don't persist */

@media screen and (min-width: 783px) {
  .aca-container .aca-sidebar {
    position: relative !important;
    transform: none !important;
    z-index: auto !important;
    pointer-events: auto !important;
    width: auto !important;
    height: auto !important;
    box-shadow: none !important;
  }
  
  .aca-mobile-hamburger {
    display: none !important;
  }
  
  .aca-mobile-overlay {
    display: none !important;
  }
  
  /* Remove mobile-specific body classes on desktop */
  body.aca-sidebar-open {
    overflow: auto !important;
  }
}/*$vite$:1*/`;
  document.head.appendChild(__vite_style__);
  function getDefaultExportFromCjs(x) {
    return x && x.__esModule && Object.prototype.hasOwnProperty.call(x, "default") ? x["default"] : x;
  }
  var jsxRuntime = { exports: {} };
  var reactJsxRuntime_production_min = {};
  var react = { exports: {} };
  var react_production_min = {};
  /**
   * @license React
   * react.production.min.js
   *
   * Copyright (c) Facebook, Inc. and its affiliates.
   *
   * This source code is licensed under the MIT license found in the
   * LICENSE file in the root directory of this source tree.
   */
  var hasRequiredReact_production_min;
  function requireReact_production_min() {
    if (hasRequiredReact_production_min) return react_production_min;
    hasRequiredReact_production_min = 1;
    var l = Symbol.for("react.element"), n = Symbol.for("react.portal"), p = Symbol.for("react.fragment"), q = Symbol.for("react.strict_mode"), r = Symbol.for("react.profiler"), t = Symbol.for("react.provider"), u = Symbol.for("react.context"), v = Symbol.for("react.forward_ref"), w = Symbol.for("react.suspense"), x = Symbol.for("react.memo"), y = Symbol.for("react.lazy"), z = Symbol.iterator;
    function A(a) {
      if (null === a || "object" !== typeof a) return null;
      a = z && a[z] || a["@@iterator"];
      return "function" === typeof a ? a : null;
    }
    var B = { isMounted: function() {
      return false;
    }, enqueueForceUpdate: function() {
    }, enqueueReplaceState: function() {
    }, enqueueSetState: function() {
    } }, C = Object.assign, D = {};
    function E(a, b, e) {
      this.props = a;
      this.context = b;
      this.refs = D;
      this.updater = e || B;
    }
    E.prototype.isReactComponent = {};
    E.prototype.setState = function(a, b) {
      if ("object" !== typeof a && "function" !== typeof a && null != a) throw Error("setState(...): takes an object of state variables to update or a function which returns an object of state variables.");
      this.updater.enqueueSetState(this, a, b, "setState");
    };
    E.prototype.forceUpdate = function(a) {
      this.updater.enqueueForceUpdate(this, a, "forceUpdate");
    };
    function F() {
    }
    F.prototype = E.prototype;
    function G(a, b, e) {
      this.props = a;
      this.context = b;
      this.refs = D;
      this.updater = e || B;
    }
    var H = G.prototype = new F();
    H.constructor = G;
    C(H, E.prototype);
    H.isPureReactComponent = true;
    var I = Array.isArray, J = Object.prototype.hasOwnProperty, K = { current: null }, L = { key: true, ref: true, __self: true, __source: true };
    function M(a, b, e) {
      var d, c = {}, k = null, h = null;
      if (null != b) for (d in void 0 !== b.ref && (h = b.ref), void 0 !== b.key && (k = "" + b.key), b) J.call(b, d) && !L.hasOwnProperty(d) && (c[d] = b[d]);
      var g = arguments.length - 2;
      if (1 === g) c.children = e;
      else if (1 < g) {
        for (var f = Array(g), m = 0; m < g; m++) f[m] = arguments[m + 2];
        c.children = f;
      }
      if (a && a.defaultProps) for (d in g = a.defaultProps, g) void 0 === c[d] && (c[d] = g[d]);
      return { $$typeof: l, type: a, key: k, ref: h, props: c, _owner: K.current };
    }
    function N(a, b) {
      return { $$typeof: l, type: a.type, key: b, ref: a.ref, props: a.props, _owner: a._owner };
    }
    function O(a) {
      return "object" === typeof a && null !== a && a.$$typeof === l;
    }
    function escape(a) {
      var b = { "=": "=0", ":": "=2" };
      return "$" + a.replace(/[=:]/g, function(a2) {
        return b[a2];
      });
    }
    var P = /\/+/g;
    function Q(a, b) {
      return "object" === typeof a && null !== a && null != a.key ? escape("" + a.key) : b.toString(36);
    }
    function R(a, b, e, d, c) {
      var k = typeof a;
      if ("undefined" === k || "boolean" === k) a = null;
      var h = false;
      if (null === a) h = true;
      else switch (k) {
        case "string":
        case "number":
          h = true;
          break;
        case "object":
          switch (a.$$typeof) {
            case l:
            case n:
              h = true;
          }
      }
      if (h) return h = a, c = c(h), a = "" === d ? "." + Q(h, 0) : d, I(c) ? (e = "", null != a && (e = a.replace(P, "$&/") + "/"), R(c, b, e, "", function(a2) {
        return a2;
      })) : null != c && (O(c) && (c = N(c, e + (!c.key || h && h.key === c.key ? "" : ("" + c.key).replace(P, "$&/") + "/") + a)), b.push(c)), 1;
      h = 0;
      d = "" === d ? "." : d + ":";
      if (I(a)) for (var g = 0; g < a.length; g++) {
        k = a[g];
        var f = d + Q(k, g);
        h += R(k, b, e, f, c);
      }
      else if (f = A(a), "function" === typeof f) for (a = f.call(a), g = 0; !(k = a.next()).done; ) k = k.value, f = d + Q(k, g++), h += R(k, b, e, f, c);
      else if ("object" === k) throw b = String(a), Error("Objects are not valid as a React child (found: " + ("[object Object]" === b ? "object with keys {" + Object.keys(a).join(", ") + "}" : b) + "). If you meant to render a collection of children, use an array instead.");
      return h;
    }
    function S(a, b, e) {
      if (null == a) return a;
      var d = [], c = 0;
      R(a, d, "", "", function(a2) {
        return b.call(e, a2, c++);
      });
      return d;
    }
    function T(a) {
      if (-1 === a._status) {
        var b = a._result;
        b = b();
        b.then(function(b2) {
          if (0 === a._status || -1 === a._status) a._status = 1, a._result = b2;
        }, function(b2) {
          if (0 === a._status || -1 === a._status) a._status = 2, a._result = b2;
        });
        -1 === a._status && (a._status = 0, a._result = b);
      }
      if (1 === a._status) return a._result.default;
      throw a._result;
    }
    var U = { current: null }, V = { transition: null }, W = { ReactCurrentDispatcher: U, ReactCurrentBatchConfig: V, ReactCurrentOwner: K };
    function X2() {
      throw Error("act(...) is not supported in production builds of React.");
    }
    react_production_min.Children = { map: S, forEach: function(a, b, e) {
      S(a, function() {
        b.apply(this, arguments);
      }, e);
    }, count: function(a) {
      var b = 0;
      S(a, function() {
        b++;
      });
      return b;
    }, toArray: function(a) {
      return S(a, function(a2) {
        return a2;
      }) || [];
    }, only: function(a) {
      if (!O(a)) throw Error("React.Children.only expected to receive a single React element child.");
      return a;
    } };
    react_production_min.Component = E;
    react_production_min.Fragment = p;
    react_production_min.Profiler = r;
    react_production_min.PureComponent = G;
    react_production_min.StrictMode = q;
    react_production_min.Suspense = w;
    react_production_min.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED = W;
    react_production_min.act = X2;
    react_production_min.cloneElement = function(a, b, e) {
      if (null === a || void 0 === a) throw Error("React.cloneElement(...): The argument must be a React element, but you passed " + a + ".");
      var d = C({}, a.props), c = a.key, k = a.ref, h = a._owner;
      if (null != b) {
        void 0 !== b.ref && (k = b.ref, h = K.current);
        void 0 !== b.key && (c = "" + b.key);
        if (a.type && a.type.defaultProps) var g = a.type.defaultProps;
        for (f in b) J.call(b, f) && !L.hasOwnProperty(f) && (d[f] = void 0 === b[f] && void 0 !== g ? g[f] : b[f]);
      }
      var f = arguments.length - 2;
      if (1 === f) d.children = e;
      else if (1 < f) {
        g = Array(f);
        for (var m = 0; m < f; m++) g[m] = arguments[m + 2];
        d.children = g;
      }
      return { $$typeof: l, type: a.type, key: c, ref: k, props: d, _owner: h };
    };
    react_production_min.createContext = function(a) {
      a = { $$typeof: u, _currentValue: a, _currentValue2: a, _threadCount: 0, Provider: null, Consumer: null, _defaultValue: null, _globalName: null };
      a.Provider = { $$typeof: t, _context: a };
      return a.Consumer = a;
    };
    react_production_min.createElement = M;
    react_production_min.createFactory = function(a) {
      var b = M.bind(null, a);
      b.type = a;
      return b;
    };
    react_production_min.createRef = function() {
      return { current: null };
    };
    react_production_min.forwardRef = function(a) {
      return { $$typeof: v, render: a };
    };
    react_production_min.isValidElement = O;
    react_production_min.lazy = function(a) {
      return { $$typeof: y, _payload: { _status: -1, _result: a }, _init: T };
    };
    react_production_min.memo = function(a, b) {
      return { $$typeof: x, type: a, compare: void 0 === b ? null : b };
    };
    react_production_min.startTransition = function(a) {
      var b = V.transition;
      V.transition = {};
      try {
        a();
      } finally {
        V.transition = b;
      }
    };
    react_production_min.unstable_act = X2;
    react_production_min.useCallback = function(a, b) {
      return U.current.useCallback(a, b);
    };
    react_production_min.useContext = function(a) {
      return U.current.useContext(a);
    };
    react_production_min.useDebugValue = function() {
    };
    react_production_min.useDeferredValue = function(a) {
      return U.current.useDeferredValue(a);
    };
    react_production_min.useEffect = function(a, b) {
      return U.current.useEffect(a, b);
    };
    react_production_min.useId = function() {
      return U.current.useId();
    };
    react_production_min.useImperativeHandle = function(a, b, e) {
      return U.current.useImperativeHandle(a, b, e);
    };
    react_production_min.useInsertionEffect = function(a, b) {
      return U.current.useInsertionEffect(a, b);
    };
    react_production_min.useLayoutEffect = function(a, b) {
      return U.current.useLayoutEffect(a, b);
    };
    react_production_min.useMemo = function(a, b) {
      return U.current.useMemo(a, b);
    };
    react_production_min.useReducer = function(a, b, e) {
      return U.current.useReducer(a, b, e);
    };
    react_production_min.useRef = function(a) {
      return U.current.useRef(a);
    };
    react_production_min.useState = function(a) {
      return U.current.useState(a);
    };
    react_production_min.useSyncExternalStore = function(a, b, e) {
      return U.current.useSyncExternalStore(a, b, e);
    };
    react_production_min.useTransition = function() {
      return U.current.useTransition();
    };
    react_production_min.version = "18.3.1";
    return react_production_min;
  }
  var hasRequiredReact;
  function requireReact() {
    if (hasRequiredReact) return react.exports;
    hasRequiredReact = 1;
    {
      react.exports = requireReact_production_min();
    }
    return react.exports;
  }
  /**
   * @license React
   * react-jsx-runtime.production.min.js
   *
   * Copyright (c) Facebook, Inc. and its affiliates.
   *
   * This source code is licensed under the MIT license found in the
   * LICENSE file in the root directory of this source tree.
   */
  var hasRequiredReactJsxRuntime_production_min;
  function requireReactJsxRuntime_production_min() {
    if (hasRequiredReactJsxRuntime_production_min) return reactJsxRuntime_production_min;
    hasRequiredReactJsxRuntime_production_min = 1;
    var f = requireReact(), k = Symbol.for("react.element"), l = Symbol.for("react.fragment"), m = Object.prototype.hasOwnProperty, n = f.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED.ReactCurrentOwner, p = { key: true, ref: true, __self: true, __source: true };
    function q(c, a, g) {
      var b, d = {}, e = null, h = null;
      void 0 !== g && (e = "" + g);
      void 0 !== a.key && (e = "" + a.key);
      void 0 !== a.ref && (h = a.ref);
      for (b in a) m.call(a, b) && !p.hasOwnProperty(b) && (d[b] = a[b]);
      if (c && c.defaultProps) for (b in a = c.defaultProps, a) void 0 === d[b] && (d[b] = a[b]);
      return { $$typeof: k, type: c, key: e, ref: h, props: d, _owner: n.current };
    }
    reactJsxRuntime_production_min.Fragment = l;
    reactJsxRuntime_production_min.jsx = q;
    reactJsxRuntime_production_min.jsxs = q;
    return reactJsxRuntime_production_min;
  }
  var hasRequiredJsxRuntime;
  function requireJsxRuntime() {
    if (hasRequiredJsxRuntime) return jsxRuntime.exports;
    hasRequiredJsxRuntime = 1;
    {
      jsxRuntime.exports = requireReactJsxRuntime_production_min();
    }
    return jsxRuntime.exports;
  }
  var jsxRuntimeExports = requireJsxRuntime();
  var reactExports = requireReact();
  const React = /* @__PURE__ */ getDefaultExportFromCjs(reactExports);
  var client = {};
  var reactDom = { exports: {} };
  var reactDom_production_min = {};
  var scheduler = { exports: {} };
  var scheduler_production_min = {};
  /**
   * @license React
   * scheduler.production.min.js
   *
   * Copyright (c) Facebook, Inc. and its affiliates.
   *
   * This source code is licensed under the MIT license found in the
   * LICENSE file in the root directory of this source tree.
   */
  var hasRequiredScheduler_production_min;
  function requireScheduler_production_min() {
    if (hasRequiredScheduler_production_min) return scheduler_production_min;
    hasRequiredScheduler_production_min = 1;
    (function(exports) {
      function f(a, b) {
        var c = a.length;
        a.push(b);
        a: for (; 0 < c; ) {
          var d = c - 1 >>> 1, e = a[d];
          if (0 < g(e, b)) a[d] = b, a[c] = e, c = d;
          else break a;
        }
      }
      function h(a) {
        return 0 === a.length ? null : a[0];
      }
      function k(a) {
        if (0 === a.length) return null;
        var b = a[0], c = a.pop();
        if (c !== b) {
          a[0] = c;
          a: for (var d = 0, e = a.length, w = e >>> 1; d < w; ) {
            var m = 2 * (d + 1) - 1, C = a[m], n = m + 1, x = a[n];
            if (0 > g(C, c)) n < e && 0 > g(x, C) ? (a[d] = x, a[n] = c, d = n) : (a[d] = C, a[m] = c, d = m);
            else if (n < e && 0 > g(x, c)) a[d] = x, a[n] = c, d = n;
            else break a;
          }
        }
        return b;
      }
      function g(a, b) {
        var c = a.sortIndex - b.sortIndex;
        return 0 !== c ? c : a.id - b.id;
      }
      if ("object" === typeof performance && "function" === typeof performance.now) {
        var l = performance;
        exports.unstable_now = function() {
          return l.now();
        };
      } else {
        var p = Date, q = p.now();
        exports.unstable_now = function() {
          return p.now() - q;
        };
      }
      var r = [], t = [], u = 1, v = null, y = 3, z = false, A = false, B = false, D = "function" === typeof setTimeout ? setTimeout : null, E = "function" === typeof clearTimeout ? clearTimeout : null, F = "undefined" !== typeof setImmediate ? setImmediate : null;
      "undefined" !== typeof navigator && void 0 !== navigator.scheduling && void 0 !== navigator.scheduling.isInputPending && navigator.scheduling.isInputPending.bind(navigator.scheduling);
      function G(a) {
        for (var b = h(t); null !== b; ) {
          if (null === b.callback) k(t);
          else if (b.startTime <= a) k(t), b.sortIndex = b.expirationTime, f(r, b);
          else break;
          b = h(t);
        }
      }
      function H(a) {
        B = false;
        G(a);
        if (!A) if (null !== h(r)) A = true, I(J);
        else {
          var b = h(t);
          null !== b && K(H, b.startTime - a);
        }
      }
      function J(a, b) {
        A = false;
        B && (B = false, E(L), L = -1);
        z = true;
        var c = y;
        try {
          G(b);
          for (v = h(r); null !== v && (!(v.expirationTime > b) || a && !M()); ) {
            var d = v.callback;
            if ("function" === typeof d) {
              v.callback = null;
              y = v.priorityLevel;
              var e = d(v.expirationTime <= b);
              b = exports.unstable_now();
              "function" === typeof e ? v.callback = e : v === h(r) && k(r);
              G(b);
            } else k(r);
            v = h(r);
          }
          if (null !== v) var w = true;
          else {
            var m = h(t);
            null !== m && K(H, m.startTime - b);
            w = false;
          }
          return w;
        } finally {
          v = null, y = c, z = false;
        }
      }
      var N = false, O = null, L = -1, P = 5, Q = -1;
      function M() {
        return exports.unstable_now() - Q < P ? false : true;
      }
      function R() {
        if (null !== O) {
          var a = exports.unstable_now();
          Q = a;
          var b = true;
          try {
            b = O(true, a);
          } finally {
            b ? S() : (N = false, O = null);
          }
        } else N = false;
      }
      var S;
      if ("function" === typeof F) S = function() {
        F(R);
      };
      else if ("undefined" !== typeof MessageChannel) {
        var T = new MessageChannel(), U = T.port2;
        T.port1.onmessage = R;
        S = function() {
          U.postMessage(null);
        };
      } else S = function() {
        D(R, 0);
      };
      function I(a) {
        O = a;
        N || (N = true, S());
      }
      function K(a, b) {
        L = D(function() {
          a(exports.unstable_now());
        }, b);
      }
      exports.unstable_IdlePriority = 5;
      exports.unstable_ImmediatePriority = 1;
      exports.unstable_LowPriority = 4;
      exports.unstable_NormalPriority = 3;
      exports.unstable_Profiling = null;
      exports.unstable_UserBlockingPriority = 2;
      exports.unstable_cancelCallback = function(a) {
        a.callback = null;
      };
      exports.unstable_continueExecution = function() {
        A || z || (A = true, I(J));
      };
      exports.unstable_forceFrameRate = function(a) {
        0 > a || 125 < a ? console.error("forceFrameRate takes a positive int between 0 and 125, forcing frame rates higher than 125 fps is not supported") : P = 0 < a ? Math.floor(1e3 / a) : 5;
      };
      exports.unstable_getCurrentPriorityLevel = function() {
        return y;
      };
      exports.unstable_getFirstCallbackNode = function() {
        return h(r);
      };
      exports.unstable_next = function(a) {
        switch (y) {
          case 1:
          case 2:
          case 3:
            var b = 3;
            break;
          default:
            b = y;
        }
        var c = y;
        y = b;
        try {
          return a();
        } finally {
          y = c;
        }
      };
      exports.unstable_pauseExecution = function() {
      };
      exports.unstable_requestPaint = function() {
      };
      exports.unstable_runWithPriority = function(a, b) {
        switch (a) {
          case 1:
          case 2:
          case 3:
          case 4:
          case 5:
            break;
          default:
            a = 3;
        }
        var c = y;
        y = a;
        try {
          return b();
        } finally {
          y = c;
        }
      };
      exports.unstable_scheduleCallback = function(a, b, c) {
        var d = exports.unstable_now();
        "object" === typeof c && null !== c ? (c = c.delay, c = "number" === typeof c && 0 < c ? d + c : d) : c = d;
        switch (a) {
          case 1:
            var e = -1;
            break;
          case 2:
            e = 250;
            break;
          case 5:
            e = 1073741823;
            break;
          case 4:
            e = 1e4;
            break;
          default:
            e = 5e3;
        }
        e = c + e;
        a = { id: u++, callback: b, priorityLevel: a, startTime: c, expirationTime: e, sortIndex: -1 };
        c > d ? (a.sortIndex = c, f(t, a), null === h(r) && a === h(t) && (B ? (E(L), L = -1) : B = true, K(H, c - d))) : (a.sortIndex = e, f(r, a), A || z || (A = true, I(J)));
        return a;
      };
      exports.unstable_shouldYield = M;
      exports.unstable_wrapCallback = function(a) {
        var b = y;
        return function() {
          var c = y;
          y = b;
          try {
            return a.apply(this, arguments);
          } finally {
            y = c;
          }
        };
      };
    })(scheduler_production_min);
    return scheduler_production_min;
  }
  var hasRequiredScheduler;
  function requireScheduler() {
    if (hasRequiredScheduler) return scheduler.exports;
    hasRequiredScheduler = 1;
    {
      scheduler.exports = requireScheduler_production_min();
    }
    return scheduler.exports;
  }
  /**
   * @license React
   * react-dom.production.min.js
   *
   * Copyright (c) Facebook, Inc. and its affiliates.
   *
   * This source code is licensed under the MIT license found in the
   * LICENSE file in the root directory of this source tree.
   */
  var hasRequiredReactDom_production_min;
  function requireReactDom_production_min() {
    if (hasRequiredReactDom_production_min) return reactDom_production_min;
    hasRequiredReactDom_production_min = 1;
    var aa = requireReact(), ca = requireScheduler();
    function p(a) {
      for (var b = "https://reactjs.org/docs/error-decoder.html?invariant=" + a, c = 1; c < arguments.length; c++) b += "&args[]=" + encodeURIComponent(arguments[c]);
      return "Minified React error #" + a + "; visit " + b + " for the full message or use the non-minified dev environment for full errors and additional helpful warnings.";
    }
    var da = /* @__PURE__ */ new Set(), ea = {};
    function fa(a, b) {
      ha(a, b);
      ha(a + "Capture", b);
    }
    function ha(a, b) {
      ea[a] = b;
      for (a = 0; a < b.length; a++) da.add(b[a]);
    }
    var ia = !("undefined" === typeof window || "undefined" === typeof window.document || "undefined" === typeof window.document.createElement), ja = Object.prototype.hasOwnProperty, ka = /^[:A-Z_a-z\u00C0-\u00D6\u00D8-\u00F6\u00F8-\u02FF\u0370-\u037D\u037F-\u1FFF\u200C-\u200D\u2070-\u218F\u2C00-\u2FEF\u3001-\uD7FF\uF900-\uFDCF\uFDF0-\uFFFD][:A-Z_a-z\u00C0-\u00D6\u00D8-\u00F6\u00F8-\u02FF\u0370-\u037D\u037F-\u1FFF\u200C-\u200D\u2070-\u218F\u2C00-\u2FEF\u3001-\uD7FF\uF900-\uFDCF\uFDF0-\uFFFD\-.0-9\u00B7\u0300-\u036F\u203F-\u2040]*$/, la = {}, ma = {};
    function oa(a) {
      if (ja.call(ma, a)) return true;
      if (ja.call(la, a)) return false;
      if (ka.test(a)) return ma[a] = true;
      la[a] = true;
      return false;
    }
    function pa(a, b, c, d) {
      if (null !== c && 0 === c.type) return false;
      switch (typeof b) {
        case "function":
        case "symbol":
          return true;
        case "boolean":
          if (d) return false;
          if (null !== c) return !c.acceptsBooleans;
          a = a.toLowerCase().slice(0, 5);
          return "data-" !== a && "aria-" !== a;
        default:
          return false;
      }
    }
    function qa(a, b, c, d) {
      if (null === b || "undefined" === typeof b || pa(a, b, c, d)) return true;
      if (d) return false;
      if (null !== c) switch (c.type) {
        case 3:
          return !b;
        case 4:
          return false === b;
        case 5:
          return isNaN(b);
        case 6:
          return isNaN(b) || 1 > b;
      }
      return false;
    }
    function v(a, b, c, d, e, f, g) {
      this.acceptsBooleans = 2 === b || 3 === b || 4 === b;
      this.attributeName = d;
      this.attributeNamespace = e;
      this.mustUseProperty = c;
      this.propertyName = a;
      this.type = b;
      this.sanitizeURL = f;
      this.removeEmptyString = g;
    }
    var z = {};
    "children dangerouslySetInnerHTML defaultValue defaultChecked innerHTML suppressContentEditableWarning suppressHydrationWarning style".split(" ").forEach(function(a) {
      z[a] = new v(a, 0, false, a, null, false, false);
    });
    [["acceptCharset", "accept-charset"], ["className", "class"], ["htmlFor", "for"], ["httpEquiv", "http-equiv"]].forEach(function(a) {
      var b = a[0];
      z[b] = new v(b, 1, false, a[1], null, false, false);
    });
    ["contentEditable", "draggable", "spellCheck", "value"].forEach(function(a) {
      z[a] = new v(a, 2, false, a.toLowerCase(), null, false, false);
    });
    ["autoReverse", "externalResourcesRequired", "focusable", "preserveAlpha"].forEach(function(a) {
      z[a] = new v(a, 2, false, a, null, false, false);
    });
    "allowFullScreen async autoFocus autoPlay controls default defer disabled disablePictureInPicture disableRemotePlayback formNoValidate hidden loop noModule noValidate open playsInline readOnly required reversed scoped seamless itemScope".split(" ").forEach(function(a) {
      z[a] = new v(a, 3, false, a.toLowerCase(), null, false, false);
    });
    ["checked", "multiple", "muted", "selected"].forEach(function(a) {
      z[a] = new v(a, 3, true, a, null, false, false);
    });
    ["capture", "download"].forEach(function(a) {
      z[a] = new v(a, 4, false, a, null, false, false);
    });
    ["cols", "rows", "size", "span"].forEach(function(a) {
      z[a] = new v(a, 6, false, a, null, false, false);
    });
    ["rowSpan", "start"].forEach(function(a) {
      z[a] = new v(a, 5, false, a.toLowerCase(), null, false, false);
    });
    var ra = /[\-:]([a-z])/g;
    function sa(a) {
      return a[1].toUpperCase();
    }
    "accent-height alignment-baseline arabic-form baseline-shift cap-height clip-path clip-rule color-interpolation color-interpolation-filters color-profile color-rendering dominant-baseline enable-background fill-opacity fill-rule flood-color flood-opacity font-family font-size font-size-adjust font-stretch font-style font-variant font-weight glyph-name glyph-orientation-horizontal glyph-orientation-vertical horiz-adv-x horiz-origin-x image-rendering letter-spacing lighting-color marker-end marker-mid marker-start overline-position overline-thickness paint-order panose-1 pointer-events rendering-intent shape-rendering stop-color stop-opacity strikethrough-position strikethrough-thickness stroke-dasharray stroke-dashoffset stroke-linecap stroke-linejoin stroke-miterlimit stroke-opacity stroke-width text-anchor text-decoration text-rendering underline-position underline-thickness unicode-bidi unicode-range units-per-em v-alphabetic v-hanging v-ideographic v-mathematical vector-effect vert-adv-y vert-origin-x vert-origin-y word-spacing writing-mode xmlns:xlink x-height".split(" ").forEach(function(a) {
      var b = a.replace(
        ra,
        sa
      );
      z[b] = new v(b, 1, false, a, null, false, false);
    });
    "xlink:actuate xlink:arcrole xlink:role xlink:show xlink:title xlink:type".split(" ").forEach(function(a) {
      var b = a.replace(ra, sa);
      z[b] = new v(b, 1, false, a, "http://www.w3.org/1999/xlink", false, false);
    });
    ["xml:base", "xml:lang", "xml:space"].forEach(function(a) {
      var b = a.replace(ra, sa);
      z[b] = new v(b, 1, false, a, "http://www.w3.org/XML/1998/namespace", false, false);
    });
    ["tabIndex", "crossOrigin"].forEach(function(a) {
      z[a] = new v(a, 1, false, a.toLowerCase(), null, false, false);
    });
    z.xlinkHref = new v("xlinkHref", 1, false, "xlink:href", "http://www.w3.org/1999/xlink", true, false);
    ["src", "href", "action", "formAction"].forEach(function(a) {
      z[a] = new v(a, 1, false, a.toLowerCase(), null, true, true);
    });
    function ta(a, b, c, d) {
      var e = z.hasOwnProperty(b) ? z[b] : null;
      if (null !== e ? 0 !== e.type : d || !(2 < b.length) || "o" !== b[0] && "O" !== b[0] || "n" !== b[1] && "N" !== b[1]) qa(b, c, e, d) && (c = null), d || null === e ? oa(b) && (null === c ? a.removeAttribute(b) : a.setAttribute(b, "" + c)) : e.mustUseProperty ? a[e.propertyName] = null === c ? 3 === e.type ? false : "" : c : (b = e.attributeName, d = e.attributeNamespace, null === c ? a.removeAttribute(b) : (e = e.type, c = 3 === e || 4 === e && true === c ? "" : "" + c, d ? a.setAttributeNS(d, b, c) : a.setAttribute(b, c)));
    }
    var ua = aa.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED, va = Symbol.for("react.element"), wa = Symbol.for("react.portal"), ya = Symbol.for("react.fragment"), za = Symbol.for("react.strict_mode"), Aa = Symbol.for("react.profiler"), Ba = Symbol.for("react.provider"), Ca = Symbol.for("react.context"), Da = Symbol.for("react.forward_ref"), Ea = Symbol.for("react.suspense"), Fa = Symbol.for("react.suspense_list"), Ga = Symbol.for("react.memo"), Ha = Symbol.for("react.lazy");
    var Ia = Symbol.for("react.offscreen");
    var Ja = Symbol.iterator;
    function Ka(a) {
      if (null === a || "object" !== typeof a) return null;
      a = Ja && a[Ja] || a["@@iterator"];
      return "function" === typeof a ? a : null;
    }
    var A = Object.assign, La;
    function Ma(a) {
      if (void 0 === La) try {
        throw Error();
      } catch (c) {
        var b = c.stack.trim().match(/\n( *(at )?)/);
        La = b && b[1] || "";
      }
      return "\n" + La + a;
    }
    var Na = false;
    function Oa(a, b) {
      if (!a || Na) return "";
      Na = true;
      var c = Error.prepareStackTrace;
      Error.prepareStackTrace = void 0;
      try {
        if (b) if (b = function() {
          throw Error();
        }, Object.defineProperty(b.prototype, "props", { set: function() {
          throw Error();
        } }), "object" === typeof Reflect && Reflect.construct) {
          try {
            Reflect.construct(b, []);
          } catch (l) {
            var d = l;
          }
          Reflect.construct(a, [], b);
        } else {
          try {
            b.call();
          } catch (l) {
            d = l;
          }
          a.call(b.prototype);
        }
        else {
          try {
            throw Error();
          } catch (l) {
            d = l;
          }
          a();
        }
      } catch (l) {
        if (l && d && "string" === typeof l.stack) {
          for (var e = l.stack.split("\n"), f = d.stack.split("\n"), g = e.length - 1, h = f.length - 1; 1 <= g && 0 <= h && e[g] !== f[h]; ) h--;
          for (; 1 <= g && 0 <= h; g--, h--) if (e[g] !== f[h]) {
            if (1 !== g || 1 !== h) {
              do
                if (g--, h--, 0 > h || e[g] !== f[h]) {
                  var k = "\n" + e[g].replace(" at new ", " at ");
                  a.displayName && k.includes("<anonymous>") && (k = k.replace("<anonymous>", a.displayName));
                  return k;
                }
              while (1 <= g && 0 <= h);
            }
            break;
          }
        }
      } finally {
        Na = false, Error.prepareStackTrace = c;
      }
      return (a = a ? a.displayName || a.name : "") ? Ma(a) : "";
    }
    function Pa(a) {
      switch (a.tag) {
        case 5:
          return Ma(a.type);
        case 16:
          return Ma("Lazy");
        case 13:
          return Ma("Suspense");
        case 19:
          return Ma("SuspenseList");
        case 0:
        case 2:
        case 15:
          return a = Oa(a.type, false), a;
        case 11:
          return a = Oa(a.type.render, false), a;
        case 1:
          return a = Oa(a.type, true), a;
        default:
          return "";
      }
    }
    function Qa(a) {
      if (null == a) return null;
      if ("function" === typeof a) return a.displayName || a.name || null;
      if ("string" === typeof a) return a;
      switch (a) {
        case ya:
          return "Fragment";
        case wa:
          return "Portal";
        case Aa:
          return "Profiler";
        case za:
          return "StrictMode";
        case Ea:
          return "Suspense";
        case Fa:
          return "SuspenseList";
      }
      if ("object" === typeof a) switch (a.$$typeof) {
        case Ca:
          return (a.displayName || "Context") + ".Consumer";
        case Ba:
          return (a._context.displayName || "Context") + ".Provider";
        case Da:
          var b = a.render;
          a = a.displayName;
          a || (a = b.displayName || b.name || "", a = "" !== a ? "ForwardRef(" + a + ")" : "ForwardRef");
          return a;
        case Ga:
          return b = a.displayName || null, null !== b ? b : Qa(a.type) || "Memo";
        case Ha:
          b = a._payload;
          a = a._init;
          try {
            return Qa(a(b));
          } catch (c) {
          }
      }
      return null;
    }
    function Ra(a) {
      var b = a.type;
      switch (a.tag) {
        case 24:
          return "Cache";
        case 9:
          return (b.displayName || "Context") + ".Consumer";
        case 10:
          return (b._context.displayName || "Context") + ".Provider";
        case 18:
          return "DehydratedFragment";
        case 11:
          return a = b.render, a = a.displayName || a.name || "", b.displayName || ("" !== a ? "ForwardRef(" + a + ")" : "ForwardRef");
        case 7:
          return "Fragment";
        case 5:
          return b;
        case 4:
          return "Portal";
        case 3:
          return "Root";
        case 6:
          return "Text";
        case 16:
          return Qa(b);
        case 8:
          return b === za ? "StrictMode" : "Mode";
        case 22:
          return "Offscreen";
        case 12:
          return "Profiler";
        case 21:
          return "Scope";
        case 13:
          return "Suspense";
        case 19:
          return "SuspenseList";
        case 25:
          return "TracingMarker";
        case 1:
        case 0:
        case 17:
        case 2:
        case 14:
        case 15:
          if ("function" === typeof b) return b.displayName || b.name || null;
          if ("string" === typeof b) return b;
      }
      return null;
    }
    function Sa(a) {
      switch (typeof a) {
        case "boolean":
        case "number":
        case "string":
        case "undefined":
          return a;
        case "object":
          return a;
        default:
          return "";
      }
    }
    function Ta(a) {
      var b = a.type;
      return (a = a.nodeName) && "input" === a.toLowerCase() && ("checkbox" === b || "radio" === b);
    }
    function Ua(a) {
      var b = Ta(a) ? "checked" : "value", c = Object.getOwnPropertyDescriptor(a.constructor.prototype, b), d = "" + a[b];
      if (!a.hasOwnProperty(b) && "undefined" !== typeof c && "function" === typeof c.get && "function" === typeof c.set) {
        var e = c.get, f = c.set;
        Object.defineProperty(a, b, { configurable: true, get: function() {
          return e.call(this);
        }, set: function(a2) {
          d = "" + a2;
          f.call(this, a2);
        } });
        Object.defineProperty(a, b, { enumerable: c.enumerable });
        return { getValue: function() {
          return d;
        }, setValue: function(a2) {
          d = "" + a2;
        }, stopTracking: function() {
          a._valueTracker = null;
          delete a[b];
        } };
      }
    }
    function Va(a) {
      a._valueTracker || (a._valueTracker = Ua(a));
    }
    function Wa(a) {
      if (!a) return false;
      var b = a._valueTracker;
      if (!b) return true;
      var c = b.getValue();
      var d = "";
      a && (d = Ta(a) ? a.checked ? "true" : "false" : a.value);
      a = d;
      return a !== c ? (b.setValue(a), true) : false;
    }
    function Xa(a) {
      a = a || ("undefined" !== typeof document ? document : void 0);
      if ("undefined" === typeof a) return null;
      try {
        return a.activeElement || a.body;
      } catch (b) {
        return a.body;
      }
    }
    function Ya(a, b) {
      var c = b.checked;
      return A({}, b, { defaultChecked: void 0, defaultValue: void 0, value: void 0, checked: null != c ? c : a._wrapperState.initialChecked });
    }
    function Za(a, b) {
      var c = null == b.defaultValue ? "" : b.defaultValue, d = null != b.checked ? b.checked : b.defaultChecked;
      c = Sa(null != b.value ? b.value : c);
      a._wrapperState = { initialChecked: d, initialValue: c, controlled: "checkbox" === b.type || "radio" === b.type ? null != b.checked : null != b.value };
    }
    function ab(a, b) {
      b = b.checked;
      null != b && ta(a, "checked", b, false);
    }
    function bb(a, b) {
      ab(a, b);
      var c = Sa(b.value), d = b.type;
      if (null != c) if ("number" === d) {
        if (0 === c && "" === a.value || a.value != c) a.value = "" + c;
      } else a.value !== "" + c && (a.value = "" + c);
      else if ("submit" === d || "reset" === d) {
        a.removeAttribute("value");
        return;
      }
      b.hasOwnProperty("value") ? cb(a, b.type, c) : b.hasOwnProperty("defaultValue") && cb(a, b.type, Sa(b.defaultValue));
      null == b.checked && null != b.defaultChecked && (a.defaultChecked = !!b.defaultChecked);
    }
    function db(a, b, c) {
      if (b.hasOwnProperty("value") || b.hasOwnProperty("defaultValue")) {
        var d = b.type;
        if (!("submit" !== d && "reset" !== d || void 0 !== b.value && null !== b.value)) return;
        b = "" + a._wrapperState.initialValue;
        c || b === a.value || (a.value = b);
        a.defaultValue = b;
      }
      c = a.name;
      "" !== c && (a.name = "");
      a.defaultChecked = !!a._wrapperState.initialChecked;
      "" !== c && (a.name = c);
    }
    function cb(a, b, c) {
      if ("number" !== b || Xa(a.ownerDocument) !== a) null == c ? a.defaultValue = "" + a._wrapperState.initialValue : a.defaultValue !== "" + c && (a.defaultValue = "" + c);
    }
    var eb = Array.isArray;
    function fb(a, b, c, d) {
      a = a.options;
      if (b) {
        b = {};
        for (var e = 0; e < c.length; e++) b["$" + c[e]] = true;
        for (c = 0; c < a.length; c++) e = b.hasOwnProperty("$" + a[c].value), a[c].selected !== e && (a[c].selected = e), e && d && (a[c].defaultSelected = true);
      } else {
        c = "" + Sa(c);
        b = null;
        for (e = 0; e < a.length; e++) {
          if (a[e].value === c) {
            a[e].selected = true;
            d && (a[e].defaultSelected = true);
            return;
          }
          null !== b || a[e].disabled || (b = a[e]);
        }
        null !== b && (b.selected = true);
      }
    }
    function gb(a, b) {
      if (null != b.dangerouslySetInnerHTML) throw Error(p(91));
      return A({}, b, { value: void 0, defaultValue: void 0, children: "" + a._wrapperState.initialValue });
    }
    function hb(a, b) {
      var c = b.value;
      if (null == c) {
        c = b.children;
        b = b.defaultValue;
        if (null != c) {
          if (null != b) throw Error(p(92));
          if (eb(c)) {
            if (1 < c.length) throw Error(p(93));
            c = c[0];
          }
          b = c;
        }
        null == b && (b = "");
        c = b;
      }
      a._wrapperState = { initialValue: Sa(c) };
    }
    function ib(a, b) {
      var c = Sa(b.value), d = Sa(b.defaultValue);
      null != c && (c = "" + c, c !== a.value && (a.value = c), null == b.defaultValue && a.defaultValue !== c && (a.defaultValue = c));
      null != d && (a.defaultValue = "" + d);
    }
    function jb(a) {
      var b = a.textContent;
      b === a._wrapperState.initialValue && "" !== b && null !== b && (a.value = b);
    }
    function kb(a) {
      switch (a) {
        case "svg":
          return "http://www.w3.org/2000/svg";
        case "math":
          return "http://www.w3.org/1998/Math/MathML";
        default:
          return "http://www.w3.org/1999/xhtml";
      }
    }
    function lb(a, b) {
      return null == a || "http://www.w3.org/1999/xhtml" === a ? kb(b) : "http://www.w3.org/2000/svg" === a && "foreignObject" === b ? "http://www.w3.org/1999/xhtml" : a;
    }
    var mb, nb = function(a) {
      return "undefined" !== typeof MSApp && MSApp.execUnsafeLocalFunction ? function(b, c, d, e) {
        MSApp.execUnsafeLocalFunction(function() {
          return a(b, c, d, e);
        });
      } : a;
    }(function(a, b) {
      if ("http://www.w3.org/2000/svg" !== a.namespaceURI || "innerHTML" in a) a.innerHTML = b;
      else {
        mb = mb || document.createElement("div");
        mb.innerHTML = "<svg>" + b.valueOf().toString() + "</svg>";
        for (b = mb.firstChild; a.firstChild; ) a.removeChild(a.firstChild);
        for (; b.firstChild; ) a.appendChild(b.firstChild);
      }
    });
    function ob(a, b) {
      if (b) {
        var c = a.firstChild;
        if (c && c === a.lastChild && 3 === c.nodeType) {
          c.nodeValue = b;
          return;
        }
      }
      a.textContent = b;
    }
    var pb = {
      animationIterationCount: true,
      aspectRatio: true,
      borderImageOutset: true,
      borderImageSlice: true,
      borderImageWidth: true,
      boxFlex: true,
      boxFlexGroup: true,
      boxOrdinalGroup: true,
      columnCount: true,
      columns: true,
      flex: true,
      flexGrow: true,
      flexPositive: true,
      flexShrink: true,
      flexNegative: true,
      flexOrder: true,
      gridArea: true,
      gridRow: true,
      gridRowEnd: true,
      gridRowSpan: true,
      gridRowStart: true,
      gridColumn: true,
      gridColumnEnd: true,
      gridColumnSpan: true,
      gridColumnStart: true,
      fontWeight: true,
      lineClamp: true,
      lineHeight: true,
      opacity: true,
      order: true,
      orphans: true,
      tabSize: true,
      widows: true,
      zIndex: true,
      zoom: true,
      fillOpacity: true,
      floodOpacity: true,
      stopOpacity: true,
      strokeDasharray: true,
      strokeDashoffset: true,
      strokeMiterlimit: true,
      strokeOpacity: true,
      strokeWidth: true
    }, qb = ["Webkit", "ms", "Moz", "O"];
    Object.keys(pb).forEach(function(a) {
      qb.forEach(function(b) {
        b = b + a.charAt(0).toUpperCase() + a.substring(1);
        pb[b] = pb[a];
      });
    });
    function rb(a, b, c) {
      return null == b || "boolean" === typeof b || "" === b ? "" : c || "number" !== typeof b || 0 === b || pb.hasOwnProperty(a) && pb[a] ? ("" + b).trim() : b + "px";
    }
    function sb(a, b) {
      a = a.style;
      for (var c in b) if (b.hasOwnProperty(c)) {
        var d = 0 === c.indexOf("--"), e = rb(c, b[c], d);
        "float" === c && (c = "cssFloat");
        d ? a.setProperty(c, e) : a[c] = e;
      }
    }
    var tb = A({ menuitem: true }, { area: true, base: true, br: true, col: true, embed: true, hr: true, img: true, input: true, keygen: true, link: true, meta: true, param: true, source: true, track: true, wbr: true });
    function ub(a, b) {
      if (b) {
        if (tb[a] && (null != b.children || null != b.dangerouslySetInnerHTML)) throw Error(p(137, a));
        if (null != b.dangerouslySetInnerHTML) {
          if (null != b.children) throw Error(p(60));
          if ("object" !== typeof b.dangerouslySetInnerHTML || !("__html" in b.dangerouslySetInnerHTML)) throw Error(p(61));
        }
        if (null != b.style && "object" !== typeof b.style) throw Error(p(62));
      }
    }
    function vb(a, b) {
      if (-1 === a.indexOf("-")) return "string" === typeof b.is;
      switch (a) {
        case "annotation-xml":
        case "color-profile":
        case "font-face":
        case "font-face-src":
        case "font-face-uri":
        case "font-face-format":
        case "font-face-name":
        case "missing-glyph":
          return false;
        default:
          return true;
      }
    }
    var wb = null;
    function xb(a) {
      a = a.target || a.srcElement || window;
      a.correspondingUseElement && (a = a.correspondingUseElement);
      return 3 === a.nodeType ? a.parentNode : a;
    }
    var yb = null, zb = null, Ab = null;
    function Bb(a) {
      if (a = Cb(a)) {
        if ("function" !== typeof yb) throw Error(p(280));
        var b = a.stateNode;
        b && (b = Db(b), yb(a.stateNode, a.type, b));
      }
    }
    function Eb(a) {
      zb ? Ab ? Ab.push(a) : Ab = [a] : zb = a;
    }
    function Fb() {
      if (zb) {
        var a = zb, b = Ab;
        Ab = zb = null;
        Bb(a);
        if (b) for (a = 0; a < b.length; a++) Bb(b[a]);
      }
    }
    function Gb(a, b) {
      return a(b);
    }
    function Hb() {
    }
    var Ib = false;
    function Jb(a, b, c) {
      if (Ib) return a(b, c);
      Ib = true;
      try {
        return Gb(a, b, c);
      } finally {
        if (Ib = false, null !== zb || null !== Ab) Hb(), Fb();
      }
    }
    function Kb(a, b) {
      var c = a.stateNode;
      if (null === c) return null;
      var d = Db(c);
      if (null === d) return null;
      c = d[b];
      a: switch (b) {
        case "onClick":
        case "onClickCapture":
        case "onDoubleClick":
        case "onDoubleClickCapture":
        case "onMouseDown":
        case "onMouseDownCapture":
        case "onMouseMove":
        case "onMouseMoveCapture":
        case "onMouseUp":
        case "onMouseUpCapture":
        case "onMouseEnter":
          (d = !d.disabled) || (a = a.type, d = !("button" === a || "input" === a || "select" === a || "textarea" === a));
          a = !d;
          break a;
        default:
          a = false;
      }
      if (a) return null;
      if (c && "function" !== typeof c) throw Error(p(231, b, typeof c));
      return c;
    }
    var Lb = false;
    if (ia) try {
      var Mb = {};
      Object.defineProperty(Mb, "passive", { get: function() {
        Lb = true;
      } });
      window.addEventListener("test", Mb, Mb);
      window.removeEventListener("test", Mb, Mb);
    } catch (a) {
      Lb = false;
    }
    function Nb(a, b, c, d, e, f, g, h, k) {
      var l = Array.prototype.slice.call(arguments, 3);
      try {
        b.apply(c, l);
      } catch (m) {
        this.onError(m);
      }
    }
    var Ob = false, Pb = null, Qb = false, Rb = null, Sb = { onError: function(a) {
      Ob = true;
      Pb = a;
    } };
    function Tb(a, b, c, d, e, f, g, h, k) {
      Ob = false;
      Pb = null;
      Nb.apply(Sb, arguments);
    }
    function Ub(a, b, c, d, e, f, g, h, k) {
      Tb.apply(this, arguments);
      if (Ob) {
        if (Ob) {
          var l = Pb;
          Ob = false;
          Pb = null;
        } else throw Error(p(198));
        Qb || (Qb = true, Rb = l);
      }
    }
    function Vb(a) {
      var b = a, c = a;
      if (a.alternate) for (; b.return; ) b = b.return;
      else {
        a = b;
        do
          b = a, 0 !== (b.flags & 4098) && (c = b.return), a = b.return;
        while (a);
      }
      return 3 === b.tag ? c : null;
    }
    function Wb(a) {
      if (13 === a.tag) {
        var b = a.memoizedState;
        null === b && (a = a.alternate, null !== a && (b = a.memoizedState));
        if (null !== b) return b.dehydrated;
      }
      return null;
    }
    function Xb(a) {
      if (Vb(a) !== a) throw Error(p(188));
    }
    function Yb(a) {
      var b = a.alternate;
      if (!b) {
        b = Vb(a);
        if (null === b) throw Error(p(188));
        return b !== a ? null : a;
      }
      for (var c = a, d = b; ; ) {
        var e = c.return;
        if (null === e) break;
        var f = e.alternate;
        if (null === f) {
          d = e.return;
          if (null !== d) {
            c = d;
            continue;
          }
          break;
        }
        if (e.child === f.child) {
          for (f = e.child; f; ) {
            if (f === c) return Xb(e), a;
            if (f === d) return Xb(e), b;
            f = f.sibling;
          }
          throw Error(p(188));
        }
        if (c.return !== d.return) c = e, d = f;
        else {
          for (var g = false, h = e.child; h; ) {
            if (h === c) {
              g = true;
              c = e;
              d = f;
              break;
            }
            if (h === d) {
              g = true;
              d = e;
              c = f;
              break;
            }
            h = h.sibling;
          }
          if (!g) {
            for (h = f.child; h; ) {
              if (h === c) {
                g = true;
                c = f;
                d = e;
                break;
              }
              if (h === d) {
                g = true;
                d = f;
                c = e;
                break;
              }
              h = h.sibling;
            }
            if (!g) throw Error(p(189));
          }
        }
        if (c.alternate !== d) throw Error(p(190));
      }
      if (3 !== c.tag) throw Error(p(188));
      return c.stateNode.current === c ? a : b;
    }
    function Zb(a) {
      a = Yb(a);
      return null !== a ? $b(a) : null;
    }
    function $b(a) {
      if (5 === a.tag || 6 === a.tag) return a;
      for (a = a.child; null !== a; ) {
        var b = $b(a);
        if (null !== b) return b;
        a = a.sibling;
      }
      return null;
    }
    var ac = ca.unstable_scheduleCallback, bc = ca.unstable_cancelCallback, cc = ca.unstable_shouldYield, dc = ca.unstable_requestPaint, B = ca.unstable_now, ec = ca.unstable_getCurrentPriorityLevel, fc = ca.unstable_ImmediatePriority, gc = ca.unstable_UserBlockingPriority, hc = ca.unstable_NormalPriority, ic = ca.unstable_LowPriority, jc = ca.unstable_IdlePriority, kc = null, lc = null;
    function mc(a) {
      if (lc && "function" === typeof lc.onCommitFiberRoot) try {
        lc.onCommitFiberRoot(kc, a, void 0, 128 === (a.current.flags & 128));
      } catch (b) {
      }
    }
    var oc = Math.clz32 ? Math.clz32 : nc, pc = Math.log, qc = Math.LN2;
    function nc(a) {
      a >>>= 0;
      return 0 === a ? 32 : 31 - (pc(a) / qc | 0) | 0;
    }
    var rc = 64, sc = 4194304;
    function tc(a) {
      switch (a & -a) {
        case 1:
          return 1;
        case 2:
          return 2;
        case 4:
          return 4;
        case 8:
          return 8;
        case 16:
          return 16;
        case 32:
          return 32;
        case 64:
        case 128:
        case 256:
        case 512:
        case 1024:
        case 2048:
        case 4096:
        case 8192:
        case 16384:
        case 32768:
        case 65536:
        case 131072:
        case 262144:
        case 524288:
        case 1048576:
        case 2097152:
          return a & 4194240;
        case 4194304:
        case 8388608:
        case 16777216:
        case 33554432:
        case 67108864:
          return a & 130023424;
        case 134217728:
          return 134217728;
        case 268435456:
          return 268435456;
        case 536870912:
          return 536870912;
        case 1073741824:
          return 1073741824;
        default:
          return a;
      }
    }
    function uc(a, b) {
      var c = a.pendingLanes;
      if (0 === c) return 0;
      var d = 0, e = a.suspendedLanes, f = a.pingedLanes, g = c & 268435455;
      if (0 !== g) {
        var h = g & ~e;
        0 !== h ? d = tc(h) : (f &= g, 0 !== f && (d = tc(f)));
      } else g = c & ~e, 0 !== g ? d = tc(g) : 0 !== f && (d = tc(f));
      if (0 === d) return 0;
      if (0 !== b && b !== d && 0 === (b & e) && (e = d & -d, f = b & -b, e >= f || 16 === e && 0 !== (f & 4194240))) return b;
      0 !== (d & 4) && (d |= c & 16);
      b = a.entangledLanes;
      if (0 !== b) for (a = a.entanglements, b &= d; 0 < b; ) c = 31 - oc(b), e = 1 << c, d |= a[c], b &= ~e;
      return d;
    }
    function vc(a, b) {
      switch (a) {
        case 1:
        case 2:
        case 4:
          return b + 250;
        case 8:
        case 16:
        case 32:
        case 64:
        case 128:
        case 256:
        case 512:
        case 1024:
        case 2048:
        case 4096:
        case 8192:
        case 16384:
        case 32768:
        case 65536:
        case 131072:
        case 262144:
        case 524288:
        case 1048576:
        case 2097152:
          return b + 5e3;
        case 4194304:
        case 8388608:
        case 16777216:
        case 33554432:
        case 67108864:
          return -1;
        case 134217728:
        case 268435456:
        case 536870912:
        case 1073741824:
          return -1;
        default:
          return -1;
      }
    }
    function wc(a, b) {
      for (var c = a.suspendedLanes, d = a.pingedLanes, e = a.expirationTimes, f = a.pendingLanes; 0 < f; ) {
        var g = 31 - oc(f), h = 1 << g, k = e[g];
        if (-1 === k) {
          if (0 === (h & c) || 0 !== (h & d)) e[g] = vc(h, b);
        } else k <= b && (a.expiredLanes |= h);
        f &= ~h;
      }
    }
    function xc(a) {
      a = a.pendingLanes & -1073741825;
      return 0 !== a ? a : a & 1073741824 ? 1073741824 : 0;
    }
    function yc() {
      var a = rc;
      rc <<= 1;
      0 === (rc & 4194240) && (rc = 64);
      return a;
    }
    function zc(a) {
      for (var b = [], c = 0; 31 > c; c++) b.push(a);
      return b;
    }
    function Ac(a, b, c) {
      a.pendingLanes |= b;
      536870912 !== b && (a.suspendedLanes = 0, a.pingedLanes = 0);
      a = a.eventTimes;
      b = 31 - oc(b);
      a[b] = c;
    }
    function Bc(a, b) {
      var c = a.pendingLanes & ~b;
      a.pendingLanes = b;
      a.suspendedLanes = 0;
      a.pingedLanes = 0;
      a.expiredLanes &= b;
      a.mutableReadLanes &= b;
      a.entangledLanes &= b;
      b = a.entanglements;
      var d = a.eventTimes;
      for (a = a.expirationTimes; 0 < c; ) {
        var e = 31 - oc(c), f = 1 << e;
        b[e] = 0;
        d[e] = -1;
        a[e] = -1;
        c &= ~f;
      }
    }
    function Cc(a, b) {
      var c = a.entangledLanes |= b;
      for (a = a.entanglements; c; ) {
        var d = 31 - oc(c), e = 1 << d;
        e & b | a[d] & b && (a[d] |= b);
        c &= ~e;
      }
    }
    var C = 0;
    function Dc(a) {
      a &= -a;
      return 1 < a ? 4 < a ? 0 !== (a & 268435455) ? 16 : 536870912 : 4 : 1;
    }
    var Ec, Fc, Gc, Hc, Ic, Jc = false, Kc = [], Lc = null, Mc = null, Nc = null, Oc = /* @__PURE__ */ new Map(), Pc = /* @__PURE__ */ new Map(), Qc = [], Rc = "mousedown mouseup touchcancel touchend touchstart auxclick dblclick pointercancel pointerdown pointerup dragend dragstart drop compositionend compositionstart keydown keypress keyup input textInput copy cut paste click change contextmenu reset submit".split(" ");
    function Sc(a, b) {
      switch (a) {
        case "focusin":
        case "focusout":
          Lc = null;
          break;
        case "dragenter":
        case "dragleave":
          Mc = null;
          break;
        case "mouseover":
        case "mouseout":
          Nc = null;
          break;
        case "pointerover":
        case "pointerout":
          Oc.delete(b.pointerId);
          break;
        case "gotpointercapture":
        case "lostpointercapture":
          Pc.delete(b.pointerId);
      }
    }
    function Tc(a, b, c, d, e, f) {
      if (null === a || a.nativeEvent !== f) return a = { blockedOn: b, domEventName: c, eventSystemFlags: d, nativeEvent: f, targetContainers: [e] }, null !== b && (b = Cb(b), null !== b && Fc(b)), a;
      a.eventSystemFlags |= d;
      b = a.targetContainers;
      null !== e && -1 === b.indexOf(e) && b.push(e);
      return a;
    }
    function Uc(a, b, c, d, e) {
      switch (b) {
        case "focusin":
          return Lc = Tc(Lc, a, b, c, d, e), true;
        case "dragenter":
          return Mc = Tc(Mc, a, b, c, d, e), true;
        case "mouseover":
          return Nc = Tc(Nc, a, b, c, d, e), true;
        case "pointerover":
          var f = e.pointerId;
          Oc.set(f, Tc(Oc.get(f) || null, a, b, c, d, e));
          return true;
        case "gotpointercapture":
          return f = e.pointerId, Pc.set(f, Tc(Pc.get(f) || null, a, b, c, d, e)), true;
      }
      return false;
    }
    function Vc(a) {
      var b = Wc(a.target);
      if (null !== b) {
        var c = Vb(b);
        if (null !== c) {
          if (b = c.tag, 13 === b) {
            if (b = Wb(c), null !== b) {
              a.blockedOn = b;
              Ic(a.priority, function() {
                Gc(c);
              });
              return;
            }
          } else if (3 === b && c.stateNode.current.memoizedState.isDehydrated) {
            a.blockedOn = 3 === c.tag ? c.stateNode.containerInfo : null;
            return;
          }
        }
      }
      a.blockedOn = null;
    }
    function Xc(a) {
      if (null !== a.blockedOn) return false;
      for (var b = a.targetContainers; 0 < b.length; ) {
        var c = Yc(a.domEventName, a.eventSystemFlags, b[0], a.nativeEvent);
        if (null === c) {
          c = a.nativeEvent;
          var d = new c.constructor(c.type, c);
          wb = d;
          c.target.dispatchEvent(d);
          wb = null;
        } else return b = Cb(c), null !== b && Fc(b), a.blockedOn = c, false;
        b.shift();
      }
      return true;
    }
    function Zc(a, b, c) {
      Xc(a) && c.delete(b);
    }
    function $c() {
      Jc = false;
      null !== Lc && Xc(Lc) && (Lc = null);
      null !== Mc && Xc(Mc) && (Mc = null);
      null !== Nc && Xc(Nc) && (Nc = null);
      Oc.forEach(Zc);
      Pc.forEach(Zc);
    }
    function ad(a, b) {
      a.blockedOn === b && (a.blockedOn = null, Jc || (Jc = true, ca.unstable_scheduleCallback(ca.unstable_NormalPriority, $c)));
    }
    function bd(a) {
      function b(b2) {
        return ad(b2, a);
      }
      if (0 < Kc.length) {
        ad(Kc[0], a);
        for (var c = 1; c < Kc.length; c++) {
          var d = Kc[c];
          d.blockedOn === a && (d.blockedOn = null);
        }
      }
      null !== Lc && ad(Lc, a);
      null !== Mc && ad(Mc, a);
      null !== Nc && ad(Nc, a);
      Oc.forEach(b);
      Pc.forEach(b);
      for (c = 0; c < Qc.length; c++) d = Qc[c], d.blockedOn === a && (d.blockedOn = null);
      for (; 0 < Qc.length && (c = Qc[0], null === c.blockedOn); ) Vc(c), null === c.blockedOn && Qc.shift();
    }
    var cd = ua.ReactCurrentBatchConfig, dd = true;
    function ed(a, b, c, d) {
      var e = C, f = cd.transition;
      cd.transition = null;
      try {
        C = 1, fd(a, b, c, d);
      } finally {
        C = e, cd.transition = f;
      }
    }
    function gd(a, b, c, d) {
      var e = C, f = cd.transition;
      cd.transition = null;
      try {
        C = 4, fd(a, b, c, d);
      } finally {
        C = e, cd.transition = f;
      }
    }
    function fd(a, b, c, d) {
      if (dd) {
        var e = Yc(a, b, c, d);
        if (null === e) hd(a, b, d, id, c), Sc(a, d);
        else if (Uc(e, a, b, c, d)) d.stopPropagation();
        else if (Sc(a, d), b & 4 && -1 < Rc.indexOf(a)) {
          for (; null !== e; ) {
            var f = Cb(e);
            null !== f && Ec(f);
            f = Yc(a, b, c, d);
            null === f && hd(a, b, d, id, c);
            if (f === e) break;
            e = f;
          }
          null !== e && d.stopPropagation();
        } else hd(a, b, d, null, c);
      }
    }
    var id = null;
    function Yc(a, b, c, d) {
      id = null;
      a = xb(d);
      a = Wc(a);
      if (null !== a) if (b = Vb(a), null === b) a = null;
      else if (c = b.tag, 13 === c) {
        a = Wb(b);
        if (null !== a) return a;
        a = null;
      } else if (3 === c) {
        if (b.stateNode.current.memoizedState.isDehydrated) return 3 === b.tag ? b.stateNode.containerInfo : null;
        a = null;
      } else b !== a && (a = null);
      id = a;
      return null;
    }
    function jd(a) {
      switch (a) {
        case "cancel":
        case "click":
        case "close":
        case "contextmenu":
        case "copy":
        case "cut":
        case "auxclick":
        case "dblclick":
        case "dragend":
        case "dragstart":
        case "drop":
        case "focusin":
        case "focusout":
        case "input":
        case "invalid":
        case "keydown":
        case "keypress":
        case "keyup":
        case "mousedown":
        case "mouseup":
        case "paste":
        case "pause":
        case "play":
        case "pointercancel":
        case "pointerdown":
        case "pointerup":
        case "ratechange":
        case "reset":
        case "resize":
        case "seeked":
        case "submit":
        case "touchcancel":
        case "touchend":
        case "touchstart":
        case "volumechange":
        case "change":
        case "selectionchange":
        case "textInput":
        case "compositionstart":
        case "compositionend":
        case "compositionupdate":
        case "beforeblur":
        case "afterblur":
        case "beforeinput":
        case "blur":
        case "fullscreenchange":
        case "focus":
        case "hashchange":
        case "popstate":
        case "select":
        case "selectstart":
          return 1;
        case "drag":
        case "dragenter":
        case "dragexit":
        case "dragleave":
        case "dragover":
        case "mousemove":
        case "mouseout":
        case "mouseover":
        case "pointermove":
        case "pointerout":
        case "pointerover":
        case "scroll":
        case "toggle":
        case "touchmove":
        case "wheel":
        case "mouseenter":
        case "mouseleave":
        case "pointerenter":
        case "pointerleave":
          return 4;
        case "message":
          switch (ec()) {
            case fc:
              return 1;
            case gc:
              return 4;
            case hc:
            case ic:
              return 16;
            case jc:
              return 536870912;
            default:
              return 16;
          }
        default:
          return 16;
      }
    }
    var kd = null, ld = null, md = null;
    function nd() {
      if (md) return md;
      var a, b = ld, c = b.length, d, e = "value" in kd ? kd.value : kd.textContent, f = e.length;
      for (a = 0; a < c && b[a] === e[a]; a++) ;
      var g = c - a;
      for (d = 1; d <= g && b[c - d] === e[f - d]; d++) ;
      return md = e.slice(a, 1 < d ? 1 - d : void 0);
    }
    function od(a) {
      var b = a.keyCode;
      "charCode" in a ? (a = a.charCode, 0 === a && 13 === b && (a = 13)) : a = b;
      10 === a && (a = 13);
      return 32 <= a || 13 === a ? a : 0;
    }
    function pd() {
      return true;
    }
    function qd() {
      return false;
    }
    function rd(a) {
      function b(b2, d, e, f, g) {
        this._reactName = b2;
        this._targetInst = e;
        this.type = d;
        this.nativeEvent = f;
        this.target = g;
        this.currentTarget = null;
        for (var c in a) a.hasOwnProperty(c) && (b2 = a[c], this[c] = b2 ? b2(f) : f[c]);
        this.isDefaultPrevented = (null != f.defaultPrevented ? f.defaultPrevented : false === f.returnValue) ? pd : qd;
        this.isPropagationStopped = qd;
        return this;
      }
      A(b.prototype, { preventDefault: function() {
        this.defaultPrevented = true;
        var a2 = this.nativeEvent;
        a2 && (a2.preventDefault ? a2.preventDefault() : "unknown" !== typeof a2.returnValue && (a2.returnValue = false), this.isDefaultPrevented = pd);
      }, stopPropagation: function() {
        var a2 = this.nativeEvent;
        a2 && (a2.stopPropagation ? a2.stopPropagation() : "unknown" !== typeof a2.cancelBubble && (a2.cancelBubble = true), this.isPropagationStopped = pd);
      }, persist: function() {
      }, isPersistent: pd });
      return b;
    }
    var sd = { eventPhase: 0, bubbles: 0, cancelable: 0, timeStamp: function(a) {
      return a.timeStamp || Date.now();
    }, defaultPrevented: 0, isTrusted: 0 }, td = rd(sd), ud = A({}, sd, { view: 0, detail: 0 }), vd = rd(ud), wd, xd, yd, Ad = A({}, ud, { screenX: 0, screenY: 0, clientX: 0, clientY: 0, pageX: 0, pageY: 0, ctrlKey: 0, shiftKey: 0, altKey: 0, metaKey: 0, getModifierState: zd, button: 0, buttons: 0, relatedTarget: function(a) {
      return void 0 === a.relatedTarget ? a.fromElement === a.srcElement ? a.toElement : a.fromElement : a.relatedTarget;
    }, movementX: function(a) {
      if ("movementX" in a) return a.movementX;
      a !== yd && (yd && "mousemove" === a.type ? (wd = a.screenX - yd.screenX, xd = a.screenY - yd.screenY) : xd = wd = 0, yd = a);
      return wd;
    }, movementY: function(a) {
      return "movementY" in a ? a.movementY : xd;
    } }), Bd = rd(Ad), Cd = A({}, Ad, { dataTransfer: 0 }), Dd = rd(Cd), Ed = A({}, ud, { relatedTarget: 0 }), Fd = rd(Ed), Gd = A({}, sd, { animationName: 0, elapsedTime: 0, pseudoElement: 0 }), Hd = rd(Gd), Id = A({}, sd, { clipboardData: function(a) {
      return "clipboardData" in a ? a.clipboardData : window.clipboardData;
    } }), Jd = rd(Id), Kd = A({}, sd, { data: 0 }), Ld = rd(Kd), Md = {
      Esc: "Escape",
      Spacebar: " ",
      Left: "ArrowLeft",
      Up: "ArrowUp",
      Right: "ArrowRight",
      Down: "ArrowDown",
      Del: "Delete",
      Win: "OS",
      Menu: "ContextMenu",
      Apps: "ContextMenu",
      Scroll: "ScrollLock",
      MozPrintableKey: "Unidentified"
    }, Nd = {
      8: "Backspace",
      9: "Tab",
      12: "Clear",
      13: "Enter",
      16: "Shift",
      17: "Control",
      18: "Alt",
      19: "Pause",
      20: "CapsLock",
      27: "Escape",
      32: " ",
      33: "PageUp",
      34: "PageDown",
      35: "End",
      36: "Home",
      37: "ArrowLeft",
      38: "ArrowUp",
      39: "ArrowRight",
      40: "ArrowDown",
      45: "Insert",
      46: "Delete",
      112: "F1",
      113: "F2",
      114: "F3",
      115: "F4",
      116: "F5",
      117: "F6",
      118: "F7",
      119: "F8",
      120: "F9",
      121: "F10",
      122: "F11",
      123: "F12",
      144: "NumLock",
      145: "ScrollLock",
      224: "Meta"
    }, Od = { Alt: "altKey", Control: "ctrlKey", Meta: "metaKey", Shift: "shiftKey" };
    function Pd(a) {
      var b = this.nativeEvent;
      return b.getModifierState ? b.getModifierState(a) : (a = Od[a]) ? !!b[a] : false;
    }
    function zd() {
      return Pd;
    }
    var Qd = A({}, ud, { key: function(a) {
      if (a.key) {
        var b = Md[a.key] || a.key;
        if ("Unidentified" !== b) return b;
      }
      return "keypress" === a.type ? (a = od(a), 13 === a ? "Enter" : String.fromCharCode(a)) : "keydown" === a.type || "keyup" === a.type ? Nd[a.keyCode] || "Unidentified" : "";
    }, code: 0, location: 0, ctrlKey: 0, shiftKey: 0, altKey: 0, metaKey: 0, repeat: 0, locale: 0, getModifierState: zd, charCode: function(a) {
      return "keypress" === a.type ? od(a) : 0;
    }, keyCode: function(a) {
      return "keydown" === a.type || "keyup" === a.type ? a.keyCode : 0;
    }, which: function(a) {
      return "keypress" === a.type ? od(a) : "keydown" === a.type || "keyup" === a.type ? a.keyCode : 0;
    } }), Rd = rd(Qd), Sd = A({}, Ad, { pointerId: 0, width: 0, height: 0, pressure: 0, tangentialPressure: 0, tiltX: 0, tiltY: 0, twist: 0, pointerType: 0, isPrimary: 0 }), Td = rd(Sd), Ud = A({}, ud, { touches: 0, targetTouches: 0, changedTouches: 0, altKey: 0, metaKey: 0, ctrlKey: 0, shiftKey: 0, getModifierState: zd }), Vd = rd(Ud), Wd = A({}, sd, { propertyName: 0, elapsedTime: 0, pseudoElement: 0 }), Xd = rd(Wd), Yd = A({}, Ad, {
      deltaX: function(a) {
        return "deltaX" in a ? a.deltaX : "wheelDeltaX" in a ? -a.wheelDeltaX : 0;
      },
      deltaY: function(a) {
        return "deltaY" in a ? a.deltaY : "wheelDeltaY" in a ? -a.wheelDeltaY : "wheelDelta" in a ? -a.wheelDelta : 0;
      },
      deltaZ: 0,
      deltaMode: 0
    }), Zd = rd(Yd), $d = [9, 13, 27, 32], ae = ia && "CompositionEvent" in window, be = null;
    ia && "documentMode" in document && (be = document.documentMode);
    var ce = ia && "TextEvent" in window && !be, de = ia && (!ae || be && 8 < be && 11 >= be), ee = String.fromCharCode(32), fe = false;
    function ge(a, b) {
      switch (a) {
        case "keyup":
          return -1 !== $d.indexOf(b.keyCode);
        case "keydown":
          return 229 !== b.keyCode;
        case "keypress":
        case "mousedown":
        case "focusout":
          return true;
        default:
          return false;
      }
    }
    function he(a) {
      a = a.detail;
      return "object" === typeof a && "data" in a ? a.data : null;
    }
    var ie = false;
    function je(a, b) {
      switch (a) {
        case "compositionend":
          return he(b);
        case "keypress":
          if (32 !== b.which) return null;
          fe = true;
          return ee;
        case "textInput":
          return a = b.data, a === ee && fe ? null : a;
        default:
          return null;
      }
    }
    function ke(a, b) {
      if (ie) return "compositionend" === a || !ae && ge(a, b) ? (a = nd(), md = ld = kd = null, ie = false, a) : null;
      switch (a) {
        case "paste":
          return null;
        case "keypress":
          if (!(b.ctrlKey || b.altKey || b.metaKey) || b.ctrlKey && b.altKey) {
            if (b.char && 1 < b.char.length) return b.char;
            if (b.which) return String.fromCharCode(b.which);
          }
          return null;
        case "compositionend":
          return de && "ko" !== b.locale ? null : b.data;
        default:
          return null;
      }
    }
    var le = { color: true, date: true, datetime: true, "datetime-local": true, email: true, month: true, number: true, password: true, range: true, search: true, tel: true, text: true, time: true, url: true, week: true };
    function me(a) {
      var b = a && a.nodeName && a.nodeName.toLowerCase();
      return "input" === b ? !!le[a.type] : "textarea" === b ? true : false;
    }
    function ne(a, b, c, d) {
      Eb(d);
      b = oe(b, "onChange");
      0 < b.length && (c = new td("onChange", "change", null, c, d), a.push({ event: c, listeners: b }));
    }
    var pe = null, qe = null;
    function re(a) {
      se(a, 0);
    }
    function te(a) {
      var b = ue(a);
      if (Wa(b)) return a;
    }
    function ve(a, b) {
      if ("change" === a) return b;
    }
    var we = false;
    if (ia) {
      var xe;
      if (ia) {
        var ye = "oninput" in document;
        if (!ye) {
          var ze = document.createElement("div");
          ze.setAttribute("oninput", "return;");
          ye = "function" === typeof ze.oninput;
        }
        xe = ye;
      } else xe = false;
      we = xe && (!document.documentMode || 9 < document.documentMode);
    }
    function Ae() {
      pe && (pe.detachEvent("onpropertychange", Be), qe = pe = null);
    }
    function Be(a) {
      if ("value" === a.propertyName && te(qe)) {
        var b = [];
        ne(b, qe, a, xb(a));
        Jb(re, b);
      }
    }
    function Ce(a, b, c) {
      "focusin" === a ? (Ae(), pe = b, qe = c, pe.attachEvent("onpropertychange", Be)) : "focusout" === a && Ae();
    }
    function De(a) {
      if ("selectionchange" === a || "keyup" === a || "keydown" === a) return te(qe);
    }
    function Ee(a, b) {
      if ("click" === a) return te(b);
    }
    function Fe(a, b) {
      if ("input" === a || "change" === a) return te(b);
    }
    function Ge(a, b) {
      return a === b && (0 !== a || 1 / a === 1 / b) || a !== a && b !== b;
    }
    var He = "function" === typeof Object.is ? Object.is : Ge;
    function Ie(a, b) {
      if (He(a, b)) return true;
      if ("object" !== typeof a || null === a || "object" !== typeof b || null === b) return false;
      var c = Object.keys(a), d = Object.keys(b);
      if (c.length !== d.length) return false;
      for (d = 0; d < c.length; d++) {
        var e = c[d];
        if (!ja.call(b, e) || !He(a[e], b[e])) return false;
      }
      return true;
    }
    function Je(a) {
      for (; a && a.firstChild; ) a = a.firstChild;
      return a;
    }
    function Ke(a, b) {
      var c = Je(a);
      a = 0;
      for (var d; c; ) {
        if (3 === c.nodeType) {
          d = a + c.textContent.length;
          if (a <= b && d >= b) return { node: c, offset: b - a };
          a = d;
        }
        a: {
          for (; c; ) {
            if (c.nextSibling) {
              c = c.nextSibling;
              break a;
            }
            c = c.parentNode;
          }
          c = void 0;
        }
        c = Je(c);
      }
    }
    function Le(a, b) {
      return a && b ? a === b ? true : a && 3 === a.nodeType ? false : b && 3 === b.nodeType ? Le(a, b.parentNode) : "contains" in a ? a.contains(b) : a.compareDocumentPosition ? !!(a.compareDocumentPosition(b) & 16) : false : false;
    }
    function Me() {
      for (var a = window, b = Xa(); b instanceof a.HTMLIFrameElement; ) {
        try {
          var c = "string" === typeof b.contentWindow.location.href;
        } catch (d) {
          c = false;
        }
        if (c) a = b.contentWindow;
        else break;
        b = Xa(a.document);
      }
      return b;
    }
    function Ne(a) {
      var b = a && a.nodeName && a.nodeName.toLowerCase();
      return b && ("input" === b && ("text" === a.type || "search" === a.type || "tel" === a.type || "url" === a.type || "password" === a.type) || "textarea" === b || "true" === a.contentEditable);
    }
    function Oe(a) {
      var b = Me(), c = a.focusedElem, d = a.selectionRange;
      if (b !== c && c && c.ownerDocument && Le(c.ownerDocument.documentElement, c)) {
        if (null !== d && Ne(c)) {
          if (b = d.start, a = d.end, void 0 === a && (a = b), "selectionStart" in c) c.selectionStart = b, c.selectionEnd = Math.min(a, c.value.length);
          else if (a = (b = c.ownerDocument || document) && b.defaultView || window, a.getSelection) {
            a = a.getSelection();
            var e = c.textContent.length, f = Math.min(d.start, e);
            d = void 0 === d.end ? f : Math.min(d.end, e);
            !a.extend && f > d && (e = d, d = f, f = e);
            e = Ke(c, f);
            var g = Ke(
              c,
              d
            );
            e && g && (1 !== a.rangeCount || a.anchorNode !== e.node || a.anchorOffset !== e.offset || a.focusNode !== g.node || a.focusOffset !== g.offset) && (b = b.createRange(), b.setStart(e.node, e.offset), a.removeAllRanges(), f > d ? (a.addRange(b), a.extend(g.node, g.offset)) : (b.setEnd(g.node, g.offset), a.addRange(b)));
          }
        }
        b = [];
        for (a = c; a = a.parentNode; ) 1 === a.nodeType && b.push({ element: a, left: a.scrollLeft, top: a.scrollTop });
        "function" === typeof c.focus && c.focus();
        for (c = 0; c < b.length; c++) a = b[c], a.element.scrollLeft = a.left, a.element.scrollTop = a.top;
      }
    }
    var Pe = ia && "documentMode" in document && 11 >= document.documentMode, Qe = null, Re = null, Se = null, Te = false;
    function Ue(a, b, c) {
      var d = c.window === c ? c.document : 9 === c.nodeType ? c : c.ownerDocument;
      Te || null == Qe || Qe !== Xa(d) || (d = Qe, "selectionStart" in d && Ne(d) ? d = { start: d.selectionStart, end: d.selectionEnd } : (d = (d.ownerDocument && d.ownerDocument.defaultView || window).getSelection(), d = { anchorNode: d.anchorNode, anchorOffset: d.anchorOffset, focusNode: d.focusNode, focusOffset: d.focusOffset }), Se && Ie(Se, d) || (Se = d, d = oe(Re, "onSelect"), 0 < d.length && (b = new td("onSelect", "select", null, b, c), a.push({ event: b, listeners: d }), b.target = Qe)));
    }
    function Ve(a, b) {
      var c = {};
      c[a.toLowerCase()] = b.toLowerCase();
      c["Webkit" + a] = "webkit" + b;
      c["Moz" + a] = "moz" + b;
      return c;
    }
    var We = { animationend: Ve("Animation", "AnimationEnd"), animationiteration: Ve("Animation", "AnimationIteration"), animationstart: Ve("Animation", "AnimationStart"), transitionend: Ve("Transition", "TransitionEnd") }, Xe = {}, Ye = {};
    ia && (Ye = document.createElement("div").style, "AnimationEvent" in window || (delete We.animationend.animation, delete We.animationiteration.animation, delete We.animationstart.animation), "TransitionEvent" in window || delete We.transitionend.transition);
    function Ze(a) {
      if (Xe[a]) return Xe[a];
      if (!We[a]) return a;
      var b = We[a], c;
      for (c in b) if (b.hasOwnProperty(c) && c in Ye) return Xe[a] = b[c];
      return a;
    }
    var $e = Ze("animationend"), af = Ze("animationiteration"), bf = Ze("animationstart"), cf = Ze("transitionend"), df = /* @__PURE__ */ new Map(), ef = "abort auxClick cancel canPlay canPlayThrough click close contextMenu copy cut drag dragEnd dragEnter dragExit dragLeave dragOver dragStart drop durationChange emptied encrypted ended error gotPointerCapture input invalid keyDown keyPress keyUp load loadedData loadedMetadata loadStart lostPointerCapture mouseDown mouseMove mouseOut mouseOver mouseUp paste pause play playing pointerCancel pointerDown pointerMove pointerOut pointerOver pointerUp progress rateChange reset resize seeked seeking stalled submit suspend timeUpdate touchCancel touchEnd touchStart volumeChange scroll toggle touchMove waiting wheel".split(" ");
    function ff(a, b) {
      df.set(a, b);
      fa(b, [a]);
    }
    for (var gf = 0; gf < ef.length; gf++) {
      var hf = ef[gf], jf = hf.toLowerCase(), kf = hf[0].toUpperCase() + hf.slice(1);
      ff(jf, "on" + kf);
    }
    ff($e, "onAnimationEnd");
    ff(af, "onAnimationIteration");
    ff(bf, "onAnimationStart");
    ff("dblclick", "onDoubleClick");
    ff("focusin", "onFocus");
    ff("focusout", "onBlur");
    ff(cf, "onTransitionEnd");
    ha("onMouseEnter", ["mouseout", "mouseover"]);
    ha("onMouseLeave", ["mouseout", "mouseover"]);
    ha("onPointerEnter", ["pointerout", "pointerover"]);
    ha("onPointerLeave", ["pointerout", "pointerover"]);
    fa("onChange", "change click focusin focusout input keydown keyup selectionchange".split(" "));
    fa("onSelect", "focusout contextmenu dragend focusin keydown keyup mousedown mouseup selectionchange".split(" "));
    fa("onBeforeInput", ["compositionend", "keypress", "textInput", "paste"]);
    fa("onCompositionEnd", "compositionend focusout keydown keypress keyup mousedown".split(" "));
    fa("onCompositionStart", "compositionstart focusout keydown keypress keyup mousedown".split(" "));
    fa("onCompositionUpdate", "compositionupdate focusout keydown keypress keyup mousedown".split(" "));
    var lf = "abort canplay canplaythrough durationchange emptied encrypted ended error loadeddata loadedmetadata loadstart pause play playing progress ratechange resize seeked seeking stalled suspend timeupdate volumechange waiting".split(" "), mf = new Set("cancel close invalid load scroll toggle".split(" ").concat(lf));
    function nf(a, b, c) {
      var d = a.type || "unknown-event";
      a.currentTarget = c;
      Ub(d, b, void 0, a);
      a.currentTarget = null;
    }
    function se(a, b) {
      b = 0 !== (b & 4);
      for (var c = 0; c < a.length; c++) {
        var d = a[c], e = d.event;
        d = d.listeners;
        a: {
          var f = void 0;
          if (b) for (var g = d.length - 1; 0 <= g; g--) {
            var h = d[g], k = h.instance, l = h.currentTarget;
            h = h.listener;
            if (k !== f && e.isPropagationStopped()) break a;
            nf(e, h, l);
            f = k;
          }
          else for (g = 0; g < d.length; g++) {
            h = d[g];
            k = h.instance;
            l = h.currentTarget;
            h = h.listener;
            if (k !== f && e.isPropagationStopped()) break a;
            nf(e, h, l);
            f = k;
          }
        }
      }
      if (Qb) throw a = Rb, Qb = false, Rb = null, a;
    }
    function D(a, b) {
      var c = b[of];
      void 0 === c && (c = b[of] = /* @__PURE__ */ new Set());
      var d = a + "__bubble";
      c.has(d) || (pf(b, a, 2, false), c.add(d));
    }
    function qf(a, b, c) {
      var d = 0;
      b && (d |= 4);
      pf(c, a, d, b);
    }
    var rf = "_reactListening" + Math.random().toString(36).slice(2);
    function sf(a) {
      if (!a[rf]) {
        a[rf] = true;
        da.forEach(function(b2) {
          "selectionchange" !== b2 && (mf.has(b2) || qf(b2, false, a), qf(b2, true, a));
        });
        var b = 9 === a.nodeType ? a : a.ownerDocument;
        null === b || b[rf] || (b[rf] = true, qf("selectionchange", false, b));
      }
    }
    function pf(a, b, c, d) {
      switch (jd(b)) {
        case 1:
          var e = ed;
          break;
        case 4:
          e = gd;
          break;
        default:
          e = fd;
      }
      c = e.bind(null, b, c, a);
      e = void 0;
      !Lb || "touchstart" !== b && "touchmove" !== b && "wheel" !== b || (e = true);
      d ? void 0 !== e ? a.addEventListener(b, c, { capture: true, passive: e }) : a.addEventListener(b, c, true) : void 0 !== e ? a.addEventListener(b, c, { passive: e }) : a.addEventListener(b, c, false);
    }
    function hd(a, b, c, d, e) {
      var f = d;
      if (0 === (b & 1) && 0 === (b & 2) && null !== d) a: for (; ; ) {
        if (null === d) return;
        var g = d.tag;
        if (3 === g || 4 === g) {
          var h = d.stateNode.containerInfo;
          if (h === e || 8 === h.nodeType && h.parentNode === e) break;
          if (4 === g) for (g = d.return; null !== g; ) {
            var k = g.tag;
            if (3 === k || 4 === k) {
              if (k = g.stateNode.containerInfo, k === e || 8 === k.nodeType && k.parentNode === e) return;
            }
            g = g.return;
          }
          for (; null !== h; ) {
            g = Wc(h);
            if (null === g) return;
            k = g.tag;
            if (5 === k || 6 === k) {
              d = f = g;
              continue a;
            }
            h = h.parentNode;
          }
        }
        d = d.return;
      }
      Jb(function() {
        var d2 = f, e2 = xb(c), g2 = [];
        a: {
          var h2 = df.get(a);
          if (void 0 !== h2) {
            var k2 = td, n = a;
            switch (a) {
              case "keypress":
                if (0 === od(c)) break a;
              case "keydown":
              case "keyup":
                k2 = Rd;
                break;
              case "focusin":
                n = "focus";
                k2 = Fd;
                break;
              case "focusout":
                n = "blur";
                k2 = Fd;
                break;
              case "beforeblur":
              case "afterblur":
                k2 = Fd;
                break;
              case "click":
                if (2 === c.button) break a;
              case "auxclick":
              case "dblclick":
              case "mousedown":
              case "mousemove":
              case "mouseup":
              case "mouseout":
              case "mouseover":
              case "contextmenu":
                k2 = Bd;
                break;
              case "drag":
              case "dragend":
              case "dragenter":
              case "dragexit":
              case "dragleave":
              case "dragover":
              case "dragstart":
              case "drop":
                k2 = Dd;
                break;
              case "touchcancel":
              case "touchend":
              case "touchmove":
              case "touchstart":
                k2 = Vd;
                break;
              case $e:
              case af:
              case bf:
                k2 = Hd;
                break;
              case cf:
                k2 = Xd;
                break;
              case "scroll":
                k2 = vd;
                break;
              case "wheel":
                k2 = Zd;
                break;
              case "copy":
              case "cut":
              case "paste":
                k2 = Jd;
                break;
              case "gotpointercapture":
              case "lostpointercapture":
              case "pointercancel":
              case "pointerdown":
              case "pointermove":
              case "pointerout":
              case "pointerover":
              case "pointerup":
                k2 = Td;
            }
            var t = 0 !== (b & 4), J = !t && "scroll" === a, x = t ? null !== h2 ? h2 + "Capture" : null : h2;
            t = [];
            for (var w = d2, u; null !== w; ) {
              u = w;
              var F = u.stateNode;
              5 === u.tag && null !== F && (u = F, null !== x && (F = Kb(w, x), null != F && t.push(tf(w, F, u))));
              if (J) break;
              w = w.return;
            }
            0 < t.length && (h2 = new k2(h2, n, null, c, e2), g2.push({ event: h2, listeners: t }));
          }
        }
        if (0 === (b & 7)) {
          a: {
            h2 = "mouseover" === a || "pointerover" === a;
            k2 = "mouseout" === a || "pointerout" === a;
            if (h2 && c !== wb && (n = c.relatedTarget || c.fromElement) && (Wc(n) || n[uf])) break a;
            if (k2 || h2) {
              h2 = e2.window === e2 ? e2 : (h2 = e2.ownerDocument) ? h2.defaultView || h2.parentWindow : window;
              if (k2) {
                if (n = c.relatedTarget || c.toElement, k2 = d2, n = n ? Wc(n) : null, null !== n && (J = Vb(n), n !== J || 5 !== n.tag && 6 !== n.tag)) n = null;
              } else k2 = null, n = d2;
              if (k2 !== n) {
                t = Bd;
                F = "onMouseLeave";
                x = "onMouseEnter";
                w = "mouse";
                if ("pointerout" === a || "pointerover" === a) t = Td, F = "onPointerLeave", x = "onPointerEnter", w = "pointer";
                J = null == k2 ? h2 : ue(k2);
                u = null == n ? h2 : ue(n);
                h2 = new t(F, w + "leave", k2, c, e2);
                h2.target = J;
                h2.relatedTarget = u;
                F = null;
                Wc(e2) === d2 && (t = new t(x, w + "enter", n, c, e2), t.target = u, t.relatedTarget = J, F = t);
                J = F;
                if (k2 && n) b: {
                  t = k2;
                  x = n;
                  w = 0;
                  for (u = t; u; u = vf(u)) w++;
                  u = 0;
                  for (F = x; F; F = vf(F)) u++;
                  for (; 0 < w - u; ) t = vf(t), w--;
                  for (; 0 < u - w; ) x = vf(x), u--;
                  for (; w--; ) {
                    if (t === x || null !== x && t === x.alternate) break b;
                    t = vf(t);
                    x = vf(x);
                  }
                  t = null;
                }
                else t = null;
                null !== k2 && wf(g2, h2, k2, t, false);
                null !== n && null !== J && wf(g2, J, n, t, true);
              }
            }
          }
          a: {
            h2 = d2 ? ue(d2) : window;
            k2 = h2.nodeName && h2.nodeName.toLowerCase();
            if ("select" === k2 || "input" === k2 && "file" === h2.type) var na = ve;
            else if (me(h2)) if (we) na = Fe;
            else {
              na = De;
              var xa = Ce;
            }
            else (k2 = h2.nodeName) && "input" === k2.toLowerCase() && ("checkbox" === h2.type || "radio" === h2.type) && (na = Ee);
            if (na && (na = na(a, d2))) {
              ne(g2, na, c, e2);
              break a;
            }
            xa && xa(a, h2, d2);
            "focusout" === a && (xa = h2._wrapperState) && xa.controlled && "number" === h2.type && cb(h2, "number", h2.value);
          }
          xa = d2 ? ue(d2) : window;
          switch (a) {
            case "focusin":
              if (me(xa) || "true" === xa.contentEditable) Qe = xa, Re = d2, Se = null;
              break;
            case "focusout":
              Se = Re = Qe = null;
              break;
            case "mousedown":
              Te = true;
              break;
            case "contextmenu":
            case "mouseup":
            case "dragend":
              Te = false;
              Ue(g2, c, e2);
              break;
            case "selectionchange":
              if (Pe) break;
            case "keydown":
            case "keyup":
              Ue(g2, c, e2);
          }
          var $a;
          if (ae) b: {
            switch (a) {
              case "compositionstart":
                var ba = "onCompositionStart";
                break b;
              case "compositionend":
                ba = "onCompositionEnd";
                break b;
              case "compositionupdate":
                ba = "onCompositionUpdate";
                break b;
            }
            ba = void 0;
          }
          else ie ? ge(a, c) && (ba = "onCompositionEnd") : "keydown" === a && 229 === c.keyCode && (ba = "onCompositionStart");
          ba && (de && "ko" !== c.locale && (ie || "onCompositionStart" !== ba ? "onCompositionEnd" === ba && ie && ($a = nd()) : (kd = e2, ld = "value" in kd ? kd.value : kd.textContent, ie = true)), xa = oe(d2, ba), 0 < xa.length && (ba = new Ld(ba, a, null, c, e2), g2.push({ event: ba, listeners: xa }), $a ? ba.data = $a : ($a = he(c), null !== $a && (ba.data = $a))));
          if ($a = ce ? je(a, c) : ke(a, c)) d2 = oe(d2, "onBeforeInput"), 0 < d2.length && (e2 = new Ld("onBeforeInput", "beforeinput", null, c, e2), g2.push({ event: e2, listeners: d2 }), e2.data = $a);
        }
        se(g2, b);
      });
    }
    function tf(a, b, c) {
      return { instance: a, listener: b, currentTarget: c };
    }
    function oe(a, b) {
      for (var c = b + "Capture", d = []; null !== a; ) {
        var e = a, f = e.stateNode;
        5 === e.tag && null !== f && (e = f, f = Kb(a, c), null != f && d.unshift(tf(a, f, e)), f = Kb(a, b), null != f && d.push(tf(a, f, e)));
        a = a.return;
      }
      return d;
    }
    function vf(a) {
      if (null === a) return null;
      do
        a = a.return;
      while (a && 5 !== a.tag);
      return a ? a : null;
    }
    function wf(a, b, c, d, e) {
      for (var f = b._reactName, g = []; null !== c && c !== d; ) {
        var h = c, k = h.alternate, l = h.stateNode;
        if (null !== k && k === d) break;
        5 === h.tag && null !== l && (h = l, e ? (k = Kb(c, f), null != k && g.unshift(tf(c, k, h))) : e || (k = Kb(c, f), null != k && g.push(tf(c, k, h))));
        c = c.return;
      }
      0 !== g.length && a.push({ event: b, listeners: g });
    }
    var xf = /\r\n?/g, yf = /\u0000|\uFFFD/g;
    function zf(a) {
      return ("string" === typeof a ? a : "" + a).replace(xf, "\n").replace(yf, "");
    }
    function Af(a, b, c) {
      b = zf(b);
      if (zf(a) !== b && c) throw Error(p(425));
    }
    function Bf() {
    }
    var Cf = null, Df = null;
    function Ef(a, b) {
      return "textarea" === a || "noscript" === a || "string" === typeof b.children || "number" === typeof b.children || "object" === typeof b.dangerouslySetInnerHTML && null !== b.dangerouslySetInnerHTML && null != b.dangerouslySetInnerHTML.__html;
    }
    var Ff = "function" === typeof setTimeout ? setTimeout : void 0, Gf = "function" === typeof clearTimeout ? clearTimeout : void 0, Hf = "function" === typeof Promise ? Promise : void 0, Jf = "function" === typeof queueMicrotask ? queueMicrotask : "undefined" !== typeof Hf ? function(a) {
      return Hf.resolve(null).then(a).catch(If);
    } : Ff;
    function If(a) {
      setTimeout(function() {
        throw a;
      });
    }
    function Kf(a, b) {
      var c = b, d = 0;
      do {
        var e = c.nextSibling;
        a.removeChild(c);
        if (e && 8 === e.nodeType) if (c = e.data, "/$" === c) {
          if (0 === d) {
            a.removeChild(e);
            bd(b);
            return;
          }
          d--;
        } else "$" !== c && "$?" !== c && "$!" !== c || d++;
        c = e;
      } while (c);
      bd(b);
    }
    function Lf(a) {
      for (; null != a; a = a.nextSibling) {
        var b = a.nodeType;
        if (1 === b || 3 === b) break;
        if (8 === b) {
          b = a.data;
          if ("$" === b || "$!" === b || "$?" === b) break;
          if ("/$" === b) return null;
        }
      }
      return a;
    }
    function Mf(a) {
      a = a.previousSibling;
      for (var b = 0; a; ) {
        if (8 === a.nodeType) {
          var c = a.data;
          if ("$" === c || "$!" === c || "$?" === c) {
            if (0 === b) return a;
            b--;
          } else "/$" === c && b++;
        }
        a = a.previousSibling;
      }
      return null;
    }
    var Nf = Math.random().toString(36).slice(2), Of = "__reactFiber$" + Nf, Pf = "__reactProps$" + Nf, uf = "__reactContainer$" + Nf, of = "__reactEvents$" + Nf, Qf = "__reactListeners$" + Nf, Rf = "__reactHandles$" + Nf;
    function Wc(a) {
      var b = a[Of];
      if (b) return b;
      for (var c = a.parentNode; c; ) {
        if (b = c[uf] || c[Of]) {
          c = b.alternate;
          if (null !== b.child || null !== c && null !== c.child) for (a = Mf(a); null !== a; ) {
            if (c = a[Of]) return c;
            a = Mf(a);
          }
          return b;
        }
        a = c;
        c = a.parentNode;
      }
      return null;
    }
    function Cb(a) {
      a = a[Of] || a[uf];
      return !a || 5 !== a.tag && 6 !== a.tag && 13 !== a.tag && 3 !== a.tag ? null : a;
    }
    function ue(a) {
      if (5 === a.tag || 6 === a.tag) return a.stateNode;
      throw Error(p(33));
    }
    function Db(a) {
      return a[Pf] || null;
    }
    var Sf = [], Tf = -1;
    function Uf(a) {
      return { current: a };
    }
    function E(a) {
      0 > Tf || (a.current = Sf[Tf], Sf[Tf] = null, Tf--);
    }
    function G(a, b) {
      Tf++;
      Sf[Tf] = a.current;
      a.current = b;
    }
    var Vf = {}, H = Uf(Vf), Wf = Uf(false), Xf = Vf;
    function Yf(a, b) {
      var c = a.type.contextTypes;
      if (!c) return Vf;
      var d = a.stateNode;
      if (d && d.__reactInternalMemoizedUnmaskedChildContext === b) return d.__reactInternalMemoizedMaskedChildContext;
      var e = {}, f;
      for (f in c) e[f] = b[f];
      d && (a = a.stateNode, a.__reactInternalMemoizedUnmaskedChildContext = b, a.__reactInternalMemoizedMaskedChildContext = e);
      return e;
    }
    function Zf(a) {
      a = a.childContextTypes;
      return null !== a && void 0 !== a;
    }
    function $f() {
      E(Wf);
      E(H);
    }
    function ag(a, b, c) {
      if (H.current !== Vf) throw Error(p(168));
      G(H, b);
      G(Wf, c);
    }
    function bg(a, b, c) {
      var d = a.stateNode;
      b = b.childContextTypes;
      if ("function" !== typeof d.getChildContext) return c;
      d = d.getChildContext();
      for (var e in d) if (!(e in b)) throw Error(p(108, Ra(a) || "Unknown", e));
      return A({}, c, d);
    }
    function cg(a) {
      a = (a = a.stateNode) && a.__reactInternalMemoizedMergedChildContext || Vf;
      Xf = H.current;
      G(H, a);
      G(Wf, Wf.current);
      return true;
    }
    function dg(a, b, c) {
      var d = a.stateNode;
      if (!d) throw Error(p(169));
      c ? (a = bg(a, b, Xf), d.__reactInternalMemoizedMergedChildContext = a, E(Wf), E(H), G(H, a)) : E(Wf);
      G(Wf, c);
    }
    var eg = null, fg = false, gg = false;
    function hg(a) {
      null === eg ? eg = [a] : eg.push(a);
    }
    function ig(a) {
      fg = true;
      hg(a);
    }
    function jg() {
      if (!gg && null !== eg) {
        gg = true;
        var a = 0, b = C;
        try {
          var c = eg;
          for (C = 1; a < c.length; a++) {
            var d = c[a];
            do
              d = d(true);
            while (null !== d);
          }
          eg = null;
          fg = false;
        } catch (e) {
          throw null !== eg && (eg = eg.slice(a + 1)), ac(fc, jg), e;
        } finally {
          C = b, gg = false;
        }
      }
      return null;
    }
    var kg = [], lg = 0, mg = null, ng = 0, og = [], pg = 0, qg = null, rg = 1, sg = "";
    function tg(a, b) {
      kg[lg++] = ng;
      kg[lg++] = mg;
      mg = a;
      ng = b;
    }
    function ug(a, b, c) {
      og[pg++] = rg;
      og[pg++] = sg;
      og[pg++] = qg;
      qg = a;
      var d = rg;
      a = sg;
      var e = 32 - oc(d) - 1;
      d &= ~(1 << e);
      c += 1;
      var f = 32 - oc(b) + e;
      if (30 < f) {
        var g = e - e % 5;
        f = (d & (1 << g) - 1).toString(32);
        d >>= g;
        e -= g;
        rg = 1 << 32 - oc(b) + e | c << e | d;
        sg = f + a;
      } else rg = 1 << f | c << e | d, sg = a;
    }
    function vg(a) {
      null !== a.return && (tg(a, 1), ug(a, 1, 0));
    }
    function wg(a) {
      for (; a === mg; ) mg = kg[--lg], kg[lg] = null, ng = kg[--lg], kg[lg] = null;
      for (; a === qg; ) qg = og[--pg], og[pg] = null, sg = og[--pg], og[pg] = null, rg = og[--pg], og[pg] = null;
    }
    var xg = null, yg = null, I = false, zg = null;
    function Ag(a, b) {
      var c = Bg(5, null, null, 0);
      c.elementType = "DELETED";
      c.stateNode = b;
      c.return = a;
      b = a.deletions;
      null === b ? (a.deletions = [c], a.flags |= 16) : b.push(c);
    }
    function Cg(a, b) {
      switch (a.tag) {
        case 5:
          var c = a.type;
          b = 1 !== b.nodeType || c.toLowerCase() !== b.nodeName.toLowerCase() ? null : b;
          return null !== b ? (a.stateNode = b, xg = a, yg = Lf(b.firstChild), true) : false;
        case 6:
          return b = "" === a.pendingProps || 3 !== b.nodeType ? null : b, null !== b ? (a.stateNode = b, xg = a, yg = null, true) : false;
        case 13:
          return b = 8 !== b.nodeType ? null : b, null !== b ? (c = null !== qg ? { id: rg, overflow: sg } : null, a.memoizedState = { dehydrated: b, treeContext: c, retryLane: 1073741824 }, c = Bg(18, null, null, 0), c.stateNode = b, c.return = a, a.child = c, xg = a, yg = null, true) : false;
        default:
          return false;
      }
    }
    function Dg(a) {
      return 0 !== (a.mode & 1) && 0 === (a.flags & 128);
    }
    function Eg(a) {
      if (I) {
        var b = yg;
        if (b) {
          var c = b;
          if (!Cg(a, b)) {
            if (Dg(a)) throw Error(p(418));
            b = Lf(c.nextSibling);
            var d = xg;
            b && Cg(a, b) ? Ag(d, c) : (a.flags = a.flags & -4097 | 2, I = false, xg = a);
          }
        } else {
          if (Dg(a)) throw Error(p(418));
          a.flags = a.flags & -4097 | 2;
          I = false;
          xg = a;
        }
      }
    }
    function Fg(a) {
      for (a = a.return; null !== a && 5 !== a.tag && 3 !== a.tag && 13 !== a.tag; ) a = a.return;
      xg = a;
    }
    function Gg(a) {
      if (a !== xg) return false;
      if (!I) return Fg(a), I = true, false;
      var b;
      (b = 3 !== a.tag) && !(b = 5 !== a.tag) && (b = a.type, b = "head" !== b && "body" !== b && !Ef(a.type, a.memoizedProps));
      if (b && (b = yg)) {
        if (Dg(a)) throw Hg(), Error(p(418));
        for (; b; ) Ag(a, b), b = Lf(b.nextSibling);
      }
      Fg(a);
      if (13 === a.tag) {
        a = a.memoizedState;
        a = null !== a ? a.dehydrated : null;
        if (!a) throw Error(p(317));
        a: {
          a = a.nextSibling;
          for (b = 0; a; ) {
            if (8 === a.nodeType) {
              var c = a.data;
              if ("/$" === c) {
                if (0 === b) {
                  yg = Lf(a.nextSibling);
                  break a;
                }
                b--;
              } else "$" !== c && "$!" !== c && "$?" !== c || b++;
            }
            a = a.nextSibling;
          }
          yg = null;
        }
      } else yg = xg ? Lf(a.stateNode.nextSibling) : null;
      return true;
    }
    function Hg() {
      for (var a = yg; a; ) a = Lf(a.nextSibling);
    }
    function Ig() {
      yg = xg = null;
      I = false;
    }
    function Jg(a) {
      null === zg ? zg = [a] : zg.push(a);
    }
    var Kg = ua.ReactCurrentBatchConfig;
    function Lg(a, b, c) {
      a = c.ref;
      if (null !== a && "function" !== typeof a && "object" !== typeof a) {
        if (c._owner) {
          c = c._owner;
          if (c) {
            if (1 !== c.tag) throw Error(p(309));
            var d = c.stateNode;
          }
          if (!d) throw Error(p(147, a));
          var e = d, f = "" + a;
          if (null !== b && null !== b.ref && "function" === typeof b.ref && b.ref._stringRef === f) return b.ref;
          b = function(a2) {
            var b2 = e.refs;
            null === a2 ? delete b2[f] : b2[f] = a2;
          };
          b._stringRef = f;
          return b;
        }
        if ("string" !== typeof a) throw Error(p(284));
        if (!c._owner) throw Error(p(290, a));
      }
      return a;
    }
    function Mg(a, b) {
      a = Object.prototype.toString.call(b);
      throw Error(p(31, "[object Object]" === a ? "object with keys {" + Object.keys(b).join(", ") + "}" : a));
    }
    function Ng(a) {
      var b = a._init;
      return b(a._payload);
    }
    function Og(a) {
      function b(b2, c2) {
        if (a) {
          var d2 = b2.deletions;
          null === d2 ? (b2.deletions = [c2], b2.flags |= 16) : d2.push(c2);
        }
      }
      function c(c2, d2) {
        if (!a) return null;
        for (; null !== d2; ) b(c2, d2), d2 = d2.sibling;
        return null;
      }
      function d(a2, b2) {
        for (a2 = /* @__PURE__ */ new Map(); null !== b2; ) null !== b2.key ? a2.set(b2.key, b2) : a2.set(b2.index, b2), b2 = b2.sibling;
        return a2;
      }
      function e(a2, b2) {
        a2 = Pg(a2, b2);
        a2.index = 0;
        a2.sibling = null;
        return a2;
      }
      function f(b2, c2, d2) {
        b2.index = d2;
        if (!a) return b2.flags |= 1048576, c2;
        d2 = b2.alternate;
        if (null !== d2) return d2 = d2.index, d2 < c2 ? (b2.flags |= 2, c2) : d2;
        b2.flags |= 2;
        return c2;
      }
      function g(b2) {
        a && null === b2.alternate && (b2.flags |= 2);
        return b2;
      }
      function h(a2, b2, c2, d2) {
        if (null === b2 || 6 !== b2.tag) return b2 = Qg(c2, a2.mode, d2), b2.return = a2, b2;
        b2 = e(b2, c2);
        b2.return = a2;
        return b2;
      }
      function k(a2, b2, c2, d2) {
        var f2 = c2.type;
        if (f2 === ya) return m(a2, b2, c2.props.children, d2, c2.key);
        if (null !== b2 && (b2.elementType === f2 || "object" === typeof f2 && null !== f2 && f2.$$typeof === Ha && Ng(f2) === b2.type)) return d2 = e(b2, c2.props), d2.ref = Lg(a2, b2, c2), d2.return = a2, d2;
        d2 = Rg(c2.type, c2.key, c2.props, null, a2.mode, d2);
        d2.ref = Lg(a2, b2, c2);
        d2.return = a2;
        return d2;
      }
      function l(a2, b2, c2, d2) {
        if (null === b2 || 4 !== b2.tag || b2.stateNode.containerInfo !== c2.containerInfo || b2.stateNode.implementation !== c2.implementation) return b2 = Sg(c2, a2.mode, d2), b2.return = a2, b2;
        b2 = e(b2, c2.children || []);
        b2.return = a2;
        return b2;
      }
      function m(a2, b2, c2, d2, f2) {
        if (null === b2 || 7 !== b2.tag) return b2 = Tg(c2, a2.mode, d2, f2), b2.return = a2, b2;
        b2 = e(b2, c2);
        b2.return = a2;
        return b2;
      }
      function q(a2, b2, c2) {
        if ("string" === typeof b2 && "" !== b2 || "number" === typeof b2) return b2 = Qg("" + b2, a2.mode, c2), b2.return = a2, b2;
        if ("object" === typeof b2 && null !== b2) {
          switch (b2.$$typeof) {
            case va:
              return c2 = Rg(b2.type, b2.key, b2.props, null, a2.mode, c2), c2.ref = Lg(a2, null, b2), c2.return = a2, c2;
            case wa:
              return b2 = Sg(b2, a2.mode, c2), b2.return = a2, b2;
            case Ha:
              var d2 = b2._init;
              return q(a2, d2(b2._payload), c2);
          }
          if (eb(b2) || Ka(b2)) return b2 = Tg(b2, a2.mode, c2, null), b2.return = a2, b2;
          Mg(a2, b2);
        }
        return null;
      }
      function r(a2, b2, c2, d2) {
        var e2 = null !== b2 ? b2.key : null;
        if ("string" === typeof c2 && "" !== c2 || "number" === typeof c2) return null !== e2 ? null : h(a2, b2, "" + c2, d2);
        if ("object" === typeof c2 && null !== c2) {
          switch (c2.$$typeof) {
            case va:
              return c2.key === e2 ? k(a2, b2, c2, d2) : null;
            case wa:
              return c2.key === e2 ? l(a2, b2, c2, d2) : null;
            case Ha:
              return e2 = c2._init, r(
                a2,
                b2,
                e2(c2._payload),
                d2
              );
          }
          if (eb(c2) || Ka(c2)) return null !== e2 ? null : m(a2, b2, c2, d2, null);
          Mg(a2, c2);
        }
        return null;
      }
      function y(a2, b2, c2, d2, e2) {
        if ("string" === typeof d2 && "" !== d2 || "number" === typeof d2) return a2 = a2.get(c2) || null, h(b2, a2, "" + d2, e2);
        if ("object" === typeof d2 && null !== d2) {
          switch (d2.$$typeof) {
            case va:
              return a2 = a2.get(null === d2.key ? c2 : d2.key) || null, k(b2, a2, d2, e2);
            case wa:
              return a2 = a2.get(null === d2.key ? c2 : d2.key) || null, l(b2, a2, d2, e2);
            case Ha:
              var f2 = d2._init;
              return y(a2, b2, c2, f2(d2._payload), e2);
          }
          if (eb(d2) || Ka(d2)) return a2 = a2.get(c2) || null, m(b2, a2, d2, e2, null);
          Mg(b2, d2);
        }
        return null;
      }
      function n(e2, g2, h2, k2) {
        for (var l2 = null, m2 = null, u = g2, w = g2 = 0, x = null; null !== u && w < h2.length; w++) {
          u.index > w ? (x = u, u = null) : x = u.sibling;
          var n2 = r(e2, u, h2[w], k2);
          if (null === n2) {
            null === u && (u = x);
            break;
          }
          a && u && null === n2.alternate && b(e2, u);
          g2 = f(n2, g2, w);
          null === m2 ? l2 = n2 : m2.sibling = n2;
          m2 = n2;
          u = x;
        }
        if (w === h2.length) return c(e2, u), I && tg(e2, w), l2;
        if (null === u) {
          for (; w < h2.length; w++) u = q(e2, h2[w], k2), null !== u && (g2 = f(u, g2, w), null === m2 ? l2 = u : m2.sibling = u, m2 = u);
          I && tg(e2, w);
          return l2;
        }
        for (u = d(e2, u); w < h2.length; w++) x = y(u, e2, w, h2[w], k2), null !== x && (a && null !== x.alternate && u.delete(null === x.key ? w : x.key), g2 = f(x, g2, w), null === m2 ? l2 = x : m2.sibling = x, m2 = x);
        a && u.forEach(function(a2) {
          return b(e2, a2);
        });
        I && tg(e2, w);
        return l2;
      }
      function t(e2, g2, h2, k2) {
        var l2 = Ka(h2);
        if ("function" !== typeof l2) throw Error(p(150));
        h2 = l2.call(h2);
        if (null == h2) throw Error(p(151));
        for (var u = l2 = null, m2 = g2, w = g2 = 0, x = null, n2 = h2.next(); null !== m2 && !n2.done; w++, n2 = h2.next()) {
          m2.index > w ? (x = m2, m2 = null) : x = m2.sibling;
          var t2 = r(e2, m2, n2.value, k2);
          if (null === t2) {
            null === m2 && (m2 = x);
            break;
          }
          a && m2 && null === t2.alternate && b(e2, m2);
          g2 = f(t2, g2, w);
          null === u ? l2 = t2 : u.sibling = t2;
          u = t2;
          m2 = x;
        }
        if (n2.done) return c(
          e2,
          m2
        ), I && tg(e2, w), l2;
        if (null === m2) {
          for (; !n2.done; w++, n2 = h2.next()) n2 = q(e2, n2.value, k2), null !== n2 && (g2 = f(n2, g2, w), null === u ? l2 = n2 : u.sibling = n2, u = n2);
          I && tg(e2, w);
          return l2;
        }
        for (m2 = d(e2, m2); !n2.done; w++, n2 = h2.next()) n2 = y(m2, e2, w, n2.value, k2), null !== n2 && (a && null !== n2.alternate && m2.delete(null === n2.key ? w : n2.key), g2 = f(n2, g2, w), null === u ? l2 = n2 : u.sibling = n2, u = n2);
        a && m2.forEach(function(a2) {
          return b(e2, a2);
        });
        I && tg(e2, w);
        return l2;
      }
      function J(a2, d2, f2, h2) {
        "object" === typeof f2 && null !== f2 && f2.type === ya && null === f2.key && (f2 = f2.props.children);
        if ("object" === typeof f2 && null !== f2) {
          switch (f2.$$typeof) {
            case va:
              a: {
                for (var k2 = f2.key, l2 = d2; null !== l2; ) {
                  if (l2.key === k2) {
                    k2 = f2.type;
                    if (k2 === ya) {
                      if (7 === l2.tag) {
                        c(a2, l2.sibling);
                        d2 = e(l2, f2.props.children);
                        d2.return = a2;
                        a2 = d2;
                        break a;
                      }
                    } else if (l2.elementType === k2 || "object" === typeof k2 && null !== k2 && k2.$$typeof === Ha && Ng(k2) === l2.type) {
                      c(a2, l2.sibling);
                      d2 = e(l2, f2.props);
                      d2.ref = Lg(a2, l2, f2);
                      d2.return = a2;
                      a2 = d2;
                      break a;
                    }
                    c(a2, l2);
                    break;
                  } else b(a2, l2);
                  l2 = l2.sibling;
                }
                f2.type === ya ? (d2 = Tg(f2.props.children, a2.mode, h2, f2.key), d2.return = a2, a2 = d2) : (h2 = Rg(f2.type, f2.key, f2.props, null, a2.mode, h2), h2.ref = Lg(a2, d2, f2), h2.return = a2, a2 = h2);
              }
              return g(a2);
            case wa:
              a: {
                for (l2 = f2.key; null !== d2; ) {
                  if (d2.key === l2) if (4 === d2.tag && d2.stateNode.containerInfo === f2.containerInfo && d2.stateNode.implementation === f2.implementation) {
                    c(a2, d2.sibling);
                    d2 = e(d2, f2.children || []);
                    d2.return = a2;
                    a2 = d2;
                    break a;
                  } else {
                    c(a2, d2);
                    break;
                  }
                  else b(a2, d2);
                  d2 = d2.sibling;
                }
                d2 = Sg(f2, a2.mode, h2);
                d2.return = a2;
                a2 = d2;
              }
              return g(a2);
            case Ha:
              return l2 = f2._init, J(a2, d2, l2(f2._payload), h2);
          }
          if (eb(f2)) return n(a2, d2, f2, h2);
          if (Ka(f2)) return t(a2, d2, f2, h2);
          Mg(a2, f2);
        }
        return "string" === typeof f2 && "" !== f2 || "number" === typeof f2 ? (f2 = "" + f2, null !== d2 && 6 === d2.tag ? (c(a2, d2.sibling), d2 = e(d2, f2), d2.return = a2, a2 = d2) : (c(a2, d2), d2 = Qg(f2, a2.mode, h2), d2.return = a2, a2 = d2), g(a2)) : c(a2, d2);
      }
      return J;
    }
    var Ug = Og(true), Vg = Og(false), Wg = Uf(null), Xg = null, Yg = null, Zg = null;
    function $g() {
      Zg = Yg = Xg = null;
    }
    function ah(a) {
      var b = Wg.current;
      E(Wg);
      a._currentValue = b;
    }
    function bh(a, b, c) {
      for (; null !== a; ) {
        var d = a.alternate;
        (a.childLanes & b) !== b ? (a.childLanes |= b, null !== d && (d.childLanes |= b)) : null !== d && (d.childLanes & b) !== b && (d.childLanes |= b);
        if (a === c) break;
        a = a.return;
      }
    }
    function ch(a, b) {
      Xg = a;
      Zg = Yg = null;
      a = a.dependencies;
      null !== a && null !== a.firstContext && (0 !== (a.lanes & b) && (dh = true), a.firstContext = null);
    }
    function eh(a) {
      var b = a._currentValue;
      if (Zg !== a) if (a = { context: a, memoizedValue: b, next: null }, null === Yg) {
        if (null === Xg) throw Error(p(308));
        Yg = a;
        Xg.dependencies = { lanes: 0, firstContext: a };
      } else Yg = Yg.next = a;
      return b;
    }
    var fh = null;
    function gh(a) {
      null === fh ? fh = [a] : fh.push(a);
    }
    function hh(a, b, c, d) {
      var e = b.interleaved;
      null === e ? (c.next = c, gh(b)) : (c.next = e.next, e.next = c);
      b.interleaved = c;
      return ih(a, d);
    }
    function ih(a, b) {
      a.lanes |= b;
      var c = a.alternate;
      null !== c && (c.lanes |= b);
      c = a;
      for (a = a.return; null !== a; ) a.childLanes |= b, c = a.alternate, null !== c && (c.childLanes |= b), c = a, a = a.return;
      return 3 === c.tag ? c.stateNode : null;
    }
    var jh = false;
    function kh(a) {
      a.updateQueue = { baseState: a.memoizedState, firstBaseUpdate: null, lastBaseUpdate: null, shared: { pending: null, interleaved: null, lanes: 0 }, effects: null };
    }
    function lh(a, b) {
      a = a.updateQueue;
      b.updateQueue === a && (b.updateQueue = { baseState: a.baseState, firstBaseUpdate: a.firstBaseUpdate, lastBaseUpdate: a.lastBaseUpdate, shared: a.shared, effects: a.effects });
    }
    function mh(a, b) {
      return { eventTime: a, lane: b, tag: 0, payload: null, callback: null, next: null };
    }
    function nh(a, b, c) {
      var d = a.updateQueue;
      if (null === d) return null;
      d = d.shared;
      if (0 !== (K & 2)) {
        var e = d.pending;
        null === e ? b.next = b : (b.next = e.next, e.next = b);
        d.pending = b;
        return ih(a, c);
      }
      e = d.interleaved;
      null === e ? (b.next = b, gh(d)) : (b.next = e.next, e.next = b);
      d.interleaved = b;
      return ih(a, c);
    }
    function oh(a, b, c) {
      b = b.updateQueue;
      if (null !== b && (b = b.shared, 0 !== (c & 4194240))) {
        var d = b.lanes;
        d &= a.pendingLanes;
        c |= d;
        b.lanes = c;
        Cc(a, c);
      }
    }
    function ph(a, b) {
      var c = a.updateQueue, d = a.alternate;
      if (null !== d && (d = d.updateQueue, c === d)) {
        var e = null, f = null;
        c = c.firstBaseUpdate;
        if (null !== c) {
          do {
            var g = { eventTime: c.eventTime, lane: c.lane, tag: c.tag, payload: c.payload, callback: c.callback, next: null };
            null === f ? e = f = g : f = f.next = g;
            c = c.next;
          } while (null !== c);
          null === f ? e = f = b : f = f.next = b;
        } else e = f = b;
        c = { baseState: d.baseState, firstBaseUpdate: e, lastBaseUpdate: f, shared: d.shared, effects: d.effects };
        a.updateQueue = c;
        return;
      }
      a = c.lastBaseUpdate;
      null === a ? c.firstBaseUpdate = b : a.next = b;
      c.lastBaseUpdate = b;
    }
    function qh(a, b, c, d) {
      var e = a.updateQueue;
      jh = false;
      var f = e.firstBaseUpdate, g = e.lastBaseUpdate, h = e.shared.pending;
      if (null !== h) {
        e.shared.pending = null;
        var k = h, l = k.next;
        k.next = null;
        null === g ? f = l : g.next = l;
        g = k;
        var m = a.alternate;
        null !== m && (m = m.updateQueue, h = m.lastBaseUpdate, h !== g && (null === h ? m.firstBaseUpdate = l : h.next = l, m.lastBaseUpdate = k));
      }
      if (null !== f) {
        var q = e.baseState;
        g = 0;
        m = l = k = null;
        h = f;
        do {
          var r = h.lane, y = h.eventTime;
          if ((d & r) === r) {
            null !== m && (m = m.next = {
              eventTime: y,
              lane: 0,
              tag: h.tag,
              payload: h.payload,
              callback: h.callback,
              next: null
            });
            a: {
              var n = a, t = h;
              r = b;
              y = c;
              switch (t.tag) {
                case 1:
                  n = t.payload;
                  if ("function" === typeof n) {
                    q = n.call(y, q, r);
                    break a;
                  }
                  q = n;
                  break a;
                case 3:
                  n.flags = n.flags & -65537 | 128;
                case 0:
                  n = t.payload;
                  r = "function" === typeof n ? n.call(y, q, r) : n;
                  if (null === r || void 0 === r) break a;
                  q = A({}, q, r);
                  break a;
                case 2:
                  jh = true;
              }
            }
            null !== h.callback && 0 !== h.lane && (a.flags |= 64, r = e.effects, null === r ? e.effects = [h] : r.push(h));
          } else y = { eventTime: y, lane: r, tag: h.tag, payload: h.payload, callback: h.callback, next: null }, null === m ? (l = m = y, k = q) : m = m.next = y, g |= r;
          h = h.next;
          if (null === h) if (h = e.shared.pending, null === h) break;
          else r = h, h = r.next, r.next = null, e.lastBaseUpdate = r, e.shared.pending = null;
        } while (1);
        null === m && (k = q);
        e.baseState = k;
        e.firstBaseUpdate = l;
        e.lastBaseUpdate = m;
        b = e.shared.interleaved;
        if (null !== b) {
          e = b;
          do
            g |= e.lane, e = e.next;
          while (e !== b);
        } else null === f && (e.shared.lanes = 0);
        rh |= g;
        a.lanes = g;
        a.memoizedState = q;
      }
    }
    function sh(a, b, c) {
      a = b.effects;
      b.effects = null;
      if (null !== a) for (b = 0; b < a.length; b++) {
        var d = a[b], e = d.callback;
        if (null !== e) {
          d.callback = null;
          d = c;
          if ("function" !== typeof e) throw Error(p(191, e));
          e.call(d);
        }
      }
    }
    var th = {}, uh = Uf(th), vh = Uf(th), wh = Uf(th);
    function xh(a) {
      if (a === th) throw Error(p(174));
      return a;
    }
    function yh(a, b) {
      G(wh, b);
      G(vh, a);
      G(uh, th);
      a = b.nodeType;
      switch (a) {
        case 9:
        case 11:
          b = (b = b.documentElement) ? b.namespaceURI : lb(null, "");
          break;
        default:
          a = 8 === a ? b.parentNode : b, b = a.namespaceURI || null, a = a.tagName, b = lb(b, a);
      }
      E(uh);
      G(uh, b);
    }
    function zh() {
      E(uh);
      E(vh);
      E(wh);
    }
    function Ah(a) {
      xh(wh.current);
      var b = xh(uh.current);
      var c = lb(b, a.type);
      b !== c && (G(vh, a), G(uh, c));
    }
    function Bh(a) {
      vh.current === a && (E(uh), E(vh));
    }
    var L = Uf(0);
    function Ch(a) {
      for (var b = a; null !== b; ) {
        if (13 === b.tag) {
          var c = b.memoizedState;
          if (null !== c && (c = c.dehydrated, null === c || "$?" === c.data || "$!" === c.data)) return b;
        } else if (19 === b.tag && void 0 !== b.memoizedProps.revealOrder) {
          if (0 !== (b.flags & 128)) return b;
        } else if (null !== b.child) {
          b.child.return = b;
          b = b.child;
          continue;
        }
        if (b === a) break;
        for (; null === b.sibling; ) {
          if (null === b.return || b.return === a) return null;
          b = b.return;
        }
        b.sibling.return = b.return;
        b = b.sibling;
      }
      return null;
    }
    var Dh = [];
    function Eh() {
      for (var a = 0; a < Dh.length; a++) Dh[a]._workInProgressVersionPrimary = null;
      Dh.length = 0;
    }
    var Fh = ua.ReactCurrentDispatcher, Gh = ua.ReactCurrentBatchConfig, Hh = 0, M = null, N = null, O = null, Ih = false, Jh = false, Kh = 0, Lh = 0;
    function P() {
      throw Error(p(321));
    }
    function Mh(a, b) {
      if (null === b) return false;
      for (var c = 0; c < b.length && c < a.length; c++) if (!He(a[c], b[c])) return false;
      return true;
    }
    function Nh(a, b, c, d, e, f) {
      Hh = f;
      M = b;
      b.memoizedState = null;
      b.updateQueue = null;
      b.lanes = 0;
      Fh.current = null === a || null === a.memoizedState ? Oh : Ph;
      a = c(d, e);
      if (Jh) {
        f = 0;
        do {
          Jh = false;
          Kh = 0;
          if (25 <= f) throw Error(p(301));
          f += 1;
          O = N = null;
          b.updateQueue = null;
          Fh.current = Qh;
          a = c(d, e);
        } while (Jh);
      }
      Fh.current = Rh;
      b = null !== N && null !== N.next;
      Hh = 0;
      O = N = M = null;
      Ih = false;
      if (b) throw Error(p(300));
      return a;
    }
    function Sh() {
      var a = 0 !== Kh;
      Kh = 0;
      return a;
    }
    function Th() {
      var a = { memoizedState: null, baseState: null, baseQueue: null, queue: null, next: null };
      null === O ? M.memoizedState = O = a : O = O.next = a;
      return O;
    }
    function Uh() {
      if (null === N) {
        var a = M.alternate;
        a = null !== a ? a.memoizedState : null;
      } else a = N.next;
      var b = null === O ? M.memoizedState : O.next;
      if (null !== b) O = b, N = a;
      else {
        if (null === a) throw Error(p(310));
        N = a;
        a = { memoizedState: N.memoizedState, baseState: N.baseState, baseQueue: N.baseQueue, queue: N.queue, next: null };
        null === O ? M.memoizedState = O = a : O = O.next = a;
      }
      return O;
    }
    function Vh(a, b) {
      return "function" === typeof b ? b(a) : b;
    }
    function Wh(a) {
      var b = Uh(), c = b.queue;
      if (null === c) throw Error(p(311));
      c.lastRenderedReducer = a;
      var d = N, e = d.baseQueue, f = c.pending;
      if (null !== f) {
        if (null !== e) {
          var g = e.next;
          e.next = f.next;
          f.next = g;
        }
        d.baseQueue = e = f;
        c.pending = null;
      }
      if (null !== e) {
        f = e.next;
        d = d.baseState;
        var h = g = null, k = null, l = f;
        do {
          var m = l.lane;
          if ((Hh & m) === m) null !== k && (k = k.next = { lane: 0, action: l.action, hasEagerState: l.hasEagerState, eagerState: l.eagerState, next: null }), d = l.hasEagerState ? l.eagerState : a(d, l.action);
          else {
            var q = {
              lane: m,
              action: l.action,
              hasEagerState: l.hasEagerState,
              eagerState: l.eagerState,
              next: null
            };
            null === k ? (h = k = q, g = d) : k = k.next = q;
            M.lanes |= m;
            rh |= m;
          }
          l = l.next;
        } while (null !== l && l !== f);
        null === k ? g = d : k.next = h;
        He(d, b.memoizedState) || (dh = true);
        b.memoizedState = d;
        b.baseState = g;
        b.baseQueue = k;
        c.lastRenderedState = d;
      }
      a = c.interleaved;
      if (null !== a) {
        e = a;
        do
          f = e.lane, M.lanes |= f, rh |= f, e = e.next;
        while (e !== a);
      } else null === e && (c.lanes = 0);
      return [b.memoizedState, c.dispatch];
    }
    function Xh(a) {
      var b = Uh(), c = b.queue;
      if (null === c) throw Error(p(311));
      c.lastRenderedReducer = a;
      var d = c.dispatch, e = c.pending, f = b.memoizedState;
      if (null !== e) {
        c.pending = null;
        var g = e = e.next;
        do
          f = a(f, g.action), g = g.next;
        while (g !== e);
        He(f, b.memoizedState) || (dh = true);
        b.memoizedState = f;
        null === b.baseQueue && (b.baseState = f);
        c.lastRenderedState = f;
      }
      return [f, d];
    }
    function Yh() {
    }
    function Zh(a, b) {
      var c = M, d = Uh(), e = b(), f = !He(d.memoizedState, e);
      f && (d.memoizedState = e, dh = true);
      d = d.queue;
      $h(ai.bind(null, c, d, a), [a]);
      if (d.getSnapshot !== b || f || null !== O && O.memoizedState.tag & 1) {
        c.flags |= 2048;
        bi(9, ci.bind(null, c, d, e, b), void 0, null);
        if (null === Q) throw Error(p(349));
        0 !== (Hh & 30) || di(c, b, e);
      }
      return e;
    }
    function di(a, b, c) {
      a.flags |= 16384;
      a = { getSnapshot: b, value: c };
      b = M.updateQueue;
      null === b ? (b = { lastEffect: null, stores: null }, M.updateQueue = b, b.stores = [a]) : (c = b.stores, null === c ? b.stores = [a] : c.push(a));
    }
    function ci(a, b, c, d) {
      b.value = c;
      b.getSnapshot = d;
      ei(b) && fi(a);
    }
    function ai(a, b, c) {
      return c(function() {
        ei(b) && fi(a);
      });
    }
    function ei(a) {
      var b = a.getSnapshot;
      a = a.value;
      try {
        var c = b();
        return !He(a, c);
      } catch (d) {
        return true;
      }
    }
    function fi(a) {
      var b = ih(a, 1);
      null !== b && gi(b, a, 1, -1);
    }
    function hi(a) {
      var b = Th();
      "function" === typeof a && (a = a());
      b.memoizedState = b.baseState = a;
      a = { pending: null, interleaved: null, lanes: 0, dispatch: null, lastRenderedReducer: Vh, lastRenderedState: a };
      b.queue = a;
      a = a.dispatch = ii.bind(null, M, a);
      return [b.memoizedState, a];
    }
    function bi(a, b, c, d) {
      a = { tag: a, create: b, destroy: c, deps: d, next: null };
      b = M.updateQueue;
      null === b ? (b = { lastEffect: null, stores: null }, M.updateQueue = b, b.lastEffect = a.next = a) : (c = b.lastEffect, null === c ? b.lastEffect = a.next = a : (d = c.next, c.next = a, a.next = d, b.lastEffect = a));
      return a;
    }
    function ji() {
      return Uh().memoizedState;
    }
    function ki(a, b, c, d) {
      var e = Th();
      M.flags |= a;
      e.memoizedState = bi(1 | b, c, void 0, void 0 === d ? null : d);
    }
    function li(a, b, c, d) {
      var e = Uh();
      d = void 0 === d ? null : d;
      var f = void 0;
      if (null !== N) {
        var g = N.memoizedState;
        f = g.destroy;
        if (null !== d && Mh(d, g.deps)) {
          e.memoizedState = bi(b, c, f, d);
          return;
        }
      }
      M.flags |= a;
      e.memoizedState = bi(1 | b, c, f, d);
    }
    function mi(a, b) {
      return ki(8390656, 8, a, b);
    }
    function $h(a, b) {
      return li(2048, 8, a, b);
    }
    function ni(a, b) {
      return li(4, 2, a, b);
    }
    function oi(a, b) {
      return li(4, 4, a, b);
    }
    function pi(a, b) {
      if ("function" === typeof b) return a = a(), b(a), function() {
        b(null);
      };
      if (null !== b && void 0 !== b) return a = a(), b.current = a, function() {
        b.current = null;
      };
    }
    function qi(a, b, c) {
      c = null !== c && void 0 !== c ? c.concat([a]) : null;
      return li(4, 4, pi.bind(null, b, a), c);
    }
    function ri() {
    }
    function si(a, b) {
      var c = Uh();
      b = void 0 === b ? null : b;
      var d = c.memoizedState;
      if (null !== d && null !== b && Mh(b, d[1])) return d[0];
      c.memoizedState = [a, b];
      return a;
    }
    function ti(a, b) {
      var c = Uh();
      b = void 0 === b ? null : b;
      var d = c.memoizedState;
      if (null !== d && null !== b && Mh(b, d[1])) return d[0];
      a = a();
      c.memoizedState = [a, b];
      return a;
    }
    function ui(a, b, c) {
      if (0 === (Hh & 21)) return a.baseState && (a.baseState = false, dh = true), a.memoizedState = c;
      He(c, b) || (c = yc(), M.lanes |= c, rh |= c, a.baseState = true);
      return b;
    }
    function vi(a, b) {
      var c = C;
      C = 0 !== c && 4 > c ? c : 4;
      a(true);
      var d = Gh.transition;
      Gh.transition = {};
      try {
        a(false), b();
      } finally {
        C = c, Gh.transition = d;
      }
    }
    function wi() {
      return Uh().memoizedState;
    }
    function xi(a, b, c) {
      var d = yi(a);
      c = { lane: d, action: c, hasEagerState: false, eagerState: null, next: null };
      if (zi(a)) Ai(b, c);
      else if (c = hh(a, b, c, d), null !== c) {
        var e = R();
        gi(c, a, d, e);
        Bi(c, b, d);
      }
    }
    function ii(a, b, c) {
      var d = yi(a), e = { lane: d, action: c, hasEagerState: false, eagerState: null, next: null };
      if (zi(a)) Ai(b, e);
      else {
        var f = a.alternate;
        if (0 === a.lanes && (null === f || 0 === f.lanes) && (f = b.lastRenderedReducer, null !== f)) try {
          var g = b.lastRenderedState, h = f(g, c);
          e.hasEagerState = true;
          e.eagerState = h;
          if (He(h, g)) {
            var k = b.interleaved;
            null === k ? (e.next = e, gh(b)) : (e.next = k.next, k.next = e);
            b.interleaved = e;
            return;
          }
        } catch (l) {
        } finally {
        }
        c = hh(a, b, e, d);
        null !== c && (e = R(), gi(c, a, d, e), Bi(c, b, d));
      }
    }
    function zi(a) {
      var b = a.alternate;
      return a === M || null !== b && b === M;
    }
    function Ai(a, b) {
      Jh = Ih = true;
      var c = a.pending;
      null === c ? b.next = b : (b.next = c.next, c.next = b);
      a.pending = b;
    }
    function Bi(a, b, c) {
      if (0 !== (c & 4194240)) {
        var d = b.lanes;
        d &= a.pendingLanes;
        c |= d;
        b.lanes = c;
        Cc(a, c);
      }
    }
    var Rh = { readContext: eh, useCallback: P, useContext: P, useEffect: P, useImperativeHandle: P, useInsertionEffect: P, useLayoutEffect: P, useMemo: P, useReducer: P, useRef: P, useState: P, useDebugValue: P, useDeferredValue: P, useTransition: P, useMutableSource: P, useSyncExternalStore: P, useId: P, unstable_isNewReconciler: false }, Oh = { readContext: eh, useCallback: function(a, b) {
      Th().memoizedState = [a, void 0 === b ? null : b];
      return a;
    }, useContext: eh, useEffect: mi, useImperativeHandle: function(a, b, c) {
      c = null !== c && void 0 !== c ? c.concat([a]) : null;
      return ki(
        4194308,
        4,
        pi.bind(null, b, a),
        c
      );
    }, useLayoutEffect: function(a, b) {
      return ki(4194308, 4, a, b);
    }, useInsertionEffect: function(a, b) {
      return ki(4, 2, a, b);
    }, useMemo: function(a, b) {
      var c = Th();
      b = void 0 === b ? null : b;
      a = a();
      c.memoizedState = [a, b];
      return a;
    }, useReducer: function(a, b, c) {
      var d = Th();
      b = void 0 !== c ? c(b) : b;
      d.memoizedState = d.baseState = b;
      a = { pending: null, interleaved: null, lanes: 0, dispatch: null, lastRenderedReducer: a, lastRenderedState: b };
      d.queue = a;
      a = a.dispatch = xi.bind(null, M, a);
      return [d.memoizedState, a];
    }, useRef: function(a) {
      var b = Th();
      a = { current: a };
      return b.memoizedState = a;
    }, useState: hi, useDebugValue: ri, useDeferredValue: function(a) {
      return Th().memoizedState = a;
    }, useTransition: function() {
      var a = hi(false), b = a[0];
      a = vi.bind(null, a[1]);
      Th().memoizedState = a;
      return [b, a];
    }, useMutableSource: function() {
    }, useSyncExternalStore: function(a, b, c) {
      var d = M, e = Th();
      if (I) {
        if (void 0 === c) throw Error(p(407));
        c = c();
      } else {
        c = b();
        if (null === Q) throw Error(p(349));
        0 !== (Hh & 30) || di(d, b, c);
      }
      e.memoizedState = c;
      var f = { value: c, getSnapshot: b };
      e.queue = f;
      mi(ai.bind(
        null,
        d,
        f,
        a
      ), [a]);
      d.flags |= 2048;
      bi(9, ci.bind(null, d, f, c, b), void 0, null);
      return c;
    }, useId: function() {
      var a = Th(), b = Q.identifierPrefix;
      if (I) {
        var c = sg;
        var d = rg;
        c = (d & ~(1 << 32 - oc(d) - 1)).toString(32) + c;
        b = ":" + b + "R" + c;
        c = Kh++;
        0 < c && (b += "H" + c.toString(32));
        b += ":";
      } else c = Lh++, b = ":" + b + "r" + c.toString(32) + ":";
      return a.memoizedState = b;
    }, unstable_isNewReconciler: false }, Ph = {
      readContext: eh,
      useCallback: si,
      useContext: eh,
      useEffect: $h,
      useImperativeHandle: qi,
      useInsertionEffect: ni,
      useLayoutEffect: oi,
      useMemo: ti,
      useReducer: Wh,
      useRef: ji,
      useState: function() {
        return Wh(Vh);
      },
      useDebugValue: ri,
      useDeferredValue: function(a) {
        var b = Uh();
        return ui(b, N.memoizedState, a);
      },
      useTransition: function() {
        var a = Wh(Vh)[0], b = Uh().memoizedState;
        return [a, b];
      },
      useMutableSource: Yh,
      useSyncExternalStore: Zh,
      useId: wi,
      unstable_isNewReconciler: false
    }, Qh = { readContext: eh, useCallback: si, useContext: eh, useEffect: $h, useImperativeHandle: qi, useInsertionEffect: ni, useLayoutEffect: oi, useMemo: ti, useReducer: Xh, useRef: ji, useState: function() {
      return Xh(Vh);
    }, useDebugValue: ri, useDeferredValue: function(a) {
      var b = Uh();
      return null === N ? b.memoizedState = a : ui(b, N.memoizedState, a);
    }, useTransition: function() {
      var a = Xh(Vh)[0], b = Uh().memoizedState;
      return [a, b];
    }, useMutableSource: Yh, useSyncExternalStore: Zh, useId: wi, unstable_isNewReconciler: false };
    function Ci(a, b) {
      if (a && a.defaultProps) {
        b = A({}, b);
        a = a.defaultProps;
        for (var c in a) void 0 === b[c] && (b[c] = a[c]);
        return b;
      }
      return b;
    }
    function Di(a, b, c, d) {
      b = a.memoizedState;
      c = c(d, b);
      c = null === c || void 0 === c ? b : A({}, b, c);
      a.memoizedState = c;
      0 === a.lanes && (a.updateQueue.baseState = c);
    }
    var Ei = { isMounted: function(a) {
      return (a = a._reactInternals) ? Vb(a) === a : false;
    }, enqueueSetState: function(a, b, c) {
      a = a._reactInternals;
      var d = R(), e = yi(a), f = mh(d, e);
      f.payload = b;
      void 0 !== c && null !== c && (f.callback = c);
      b = nh(a, f, e);
      null !== b && (gi(b, a, e, d), oh(b, a, e));
    }, enqueueReplaceState: function(a, b, c) {
      a = a._reactInternals;
      var d = R(), e = yi(a), f = mh(d, e);
      f.tag = 1;
      f.payload = b;
      void 0 !== c && null !== c && (f.callback = c);
      b = nh(a, f, e);
      null !== b && (gi(b, a, e, d), oh(b, a, e));
    }, enqueueForceUpdate: function(a, b) {
      a = a._reactInternals;
      var c = R(), d = yi(a), e = mh(c, d);
      e.tag = 2;
      void 0 !== b && null !== b && (e.callback = b);
      b = nh(a, e, d);
      null !== b && (gi(b, a, d, c), oh(b, a, d));
    } };
    function Fi(a, b, c, d, e, f, g) {
      a = a.stateNode;
      return "function" === typeof a.shouldComponentUpdate ? a.shouldComponentUpdate(d, f, g) : b.prototype && b.prototype.isPureReactComponent ? !Ie(c, d) || !Ie(e, f) : true;
    }
    function Gi(a, b, c) {
      var d = false, e = Vf;
      var f = b.contextType;
      "object" === typeof f && null !== f ? f = eh(f) : (e = Zf(b) ? Xf : H.current, d = b.contextTypes, f = (d = null !== d && void 0 !== d) ? Yf(a, e) : Vf);
      b = new b(c, f);
      a.memoizedState = null !== b.state && void 0 !== b.state ? b.state : null;
      b.updater = Ei;
      a.stateNode = b;
      b._reactInternals = a;
      d && (a = a.stateNode, a.__reactInternalMemoizedUnmaskedChildContext = e, a.__reactInternalMemoizedMaskedChildContext = f);
      return b;
    }
    function Hi(a, b, c, d) {
      a = b.state;
      "function" === typeof b.componentWillReceiveProps && b.componentWillReceiveProps(c, d);
      "function" === typeof b.UNSAFE_componentWillReceiveProps && b.UNSAFE_componentWillReceiveProps(c, d);
      b.state !== a && Ei.enqueueReplaceState(b, b.state, null);
    }
    function Ii(a, b, c, d) {
      var e = a.stateNode;
      e.props = c;
      e.state = a.memoizedState;
      e.refs = {};
      kh(a);
      var f = b.contextType;
      "object" === typeof f && null !== f ? e.context = eh(f) : (f = Zf(b) ? Xf : H.current, e.context = Yf(a, f));
      e.state = a.memoizedState;
      f = b.getDerivedStateFromProps;
      "function" === typeof f && (Di(a, b, f, c), e.state = a.memoizedState);
      "function" === typeof b.getDerivedStateFromProps || "function" === typeof e.getSnapshotBeforeUpdate || "function" !== typeof e.UNSAFE_componentWillMount && "function" !== typeof e.componentWillMount || (b = e.state, "function" === typeof e.componentWillMount && e.componentWillMount(), "function" === typeof e.UNSAFE_componentWillMount && e.UNSAFE_componentWillMount(), b !== e.state && Ei.enqueueReplaceState(e, e.state, null), qh(a, c, e, d), e.state = a.memoizedState);
      "function" === typeof e.componentDidMount && (a.flags |= 4194308);
    }
    function Ji(a, b) {
      try {
        var c = "", d = b;
        do
          c += Pa(d), d = d.return;
        while (d);
        var e = c;
      } catch (f) {
        e = "\nError generating stack: " + f.message + "\n" + f.stack;
      }
      return { value: a, source: b, stack: e, digest: null };
    }
    function Ki(a, b, c) {
      return { value: a, source: null, stack: null != c ? c : null, digest: null != b ? b : null };
    }
    function Li(a, b) {
      try {
        console.error(b.value);
      } catch (c) {
        setTimeout(function() {
          throw c;
        });
      }
    }
    var Mi = "function" === typeof WeakMap ? WeakMap : Map;
    function Ni(a, b, c) {
      c = mh(-1, c);
      c.tag = 3;
      c.payload = { element: null };
      var d = b.value;
      c.callback = function() {
        Oi || (Oi = true, Pi = d);
        Li(a, b);
      };
      return c;
    }
    function Qi(a, b, c) {
      c = mh(-1, c);
      c.tag = 3;
      var d = a.type.getDerivedStateFromError;
      if ("function" === typeof d) {
        var e = b.value;
        c.payload = function() {
          return d(e);
        };
        c.callback = function() {
          Li(a, b);
        };
      }
      var f = a.stateNode;
      null !== f && "function" === typeof f.componentDidCatch && (c.callback = function() {
        Li(a, b);
        "function" !== typeof d && (null === Ri ? Ri = /* @__PURE__ */ new Set([this]) : Ri.add(this));
        var c2 = b.stack;
        this.componentDidCatch(b.value, { componentStack: null !== c2 ? c2 : "" });
      });
      return c;
    }
    function Si(a, b, c) {
      var d = a.pingCache;
      if (null === d) {
        d = a.pingCache = new Mi();
        var e = /* @__PURE__ */ new Set();
        d.set(b, e);
      } else e = d.get(b), void 0 === e && (e = /* @__PURE__ */ new Set(), d.set(b, e));
      e.has(c) || (e.add(c), a = Ti.bind(null, a, b, c), b.then(a, a));
    }
    function Ui(a) {
      do {
        var b;
        if (b = 13 === a.tag) b = a.memoizedState, b = null !== b ? null !== b.dehydrated ? true : false : true;
        if (b) return a;
        a = a.return;
      } while (null !== a);
      return null;
    }
    function Vi(a, b, c, d, e) {
      if (0 === (a.mode & 1)) return a === b ? a.flags |= 65536 : (a.flags |= 128, c.flags |= 131072, c.flags &= -52805, 1 === c.tag && (null === c.alternate ? c.tag = 17 : (b = mh(-1, 1), b.tag = 2, nh(c, b, 1))), c.lanes |= 1), a;
      a.flags |= 65536;
      a.lanes = e;
      return a;
    }
    var Wi = ua.ReactCurrentOwner, dh = false;
    function Xi(a, b, c, d) {
      b.child = null === a ? Vg(b, null, c, d) : Ug(b, a.child, c, d);
    }
    function Yi(a, b, c, d, e) {
      c = c.render;
      var f = b.ref;
      ch(b, e);
      d = Nh(a, b, c, d, f, e);
      c = Sh();
      if (null !== a && !dh) return b.updateQueue = a.updateQueue, b.flags &= -2053, a.lanes &= ~e, Zi(a, b, e);
      I && c && vg(b);
      b.flags |= 1;
      Xi(a, b, d, e);
      return b.child;
    }
    function $i(a, b, c, d, e) {
      if (null === a) {
        var f = c.type;
        if ("function" === typeof f && !aj(f) && void 0 === f.defaultProps && null === c.compare && void 0 === c.defaultProps) return b.tag = 15, b.type = f, bj(a, b, f, d, e);
        a = Rg(c.type, null, d, b, b.mode, e);
        a.ref = b.ref;
        a.return = b;
        return b.child = a;
      }
      f = a.child;
      if (0 === (a.lanes & e)) {
        var g = f.memoizedProps;
        c = c.compare;
        c = null !== c ? c : Ie;
        if (c(g, d) && a.ref === b.ref) return Zi(a, b, e);
      }
      b.flags |= 1;
      a = Pg(f, d);
      a.ref = b.ref;
      a.return = b;
      return b.child = a;
    }
    function bj(a, b, c, d, e) {
      if (null !== a) {
        var f = a.memoizedProps;
        if (Ie(f, d) && a.ref === b.ref) if (dh = false, b.pendingProps = d = f, 0 !== (a.lanes & e)) 0 !== (a.flags & 131072) && (dh = true);
        else return b.lanes = a.lanes, Zi(a, b, e);
      }
      return cj(a, b, c, d, e);
    }
    function dj(a, b, c) {
      var d = b.pendingProps, e = d.children, f = null !== a ? a.memoizedState : null;
      if ("hidden" === d.mode) if (0 === (b.mode & 1)) b.memoizedState = { baseLanes: 0, cachePool: null, transitions: null }, G(ej, fj), fj |= c;
      else {
        if (0 === (c & 1073741824)) return a = null !== f ? f.baseLanes | c : c, b.lanes = b.childLanes = 1073741824, b.memoizedState = { baseLanes: a, cachePool: null, transitions: null }, b.updateQueue = null, G(ej, fj), fj |= a, null;
        b.memoizedState = { baseLanes: 0, cachePool: null, transitions: null };
        d = null !== f ? f.baseLanes : c;
        G(ej, fj);
        fj |= d;
      }
      else null !== f ? (d = f.baseLanes | c, b.memoizedState = null) : d = c, G(ej, fj), fj |= d;
      Xi(a, b, e, c);
      return b.child;
    }
    function gj(a, b) {
      var c = b.ref;
      if (null === a && null !== c || null !== a && a.ref !== c) b.flags |= 512, b.flags |= 2097152;
    }
    function cj(a, b, c, d, e) {
      var f = Zf(c) ? Xf : H.current;
      f = Yf(b, f);
      ch(b, e);
      c = Nh(a, b, c, d, f, e);
      d = Sh();
      if (null !== a && !dh) return b.updateQueue = a.updateQueue, b.flags &= -2053, a.lanes &= ~e, Zi(a, b, e);
      I && d && vg(b);
      b.flags |= 1;
      Xi(a, b, c, e);
      return b.child;
    }
    function hj(a, b, c, d, e) {
      if (Zf(c)) {
        var f = true;
        cg(b);
      } else f = false;
      ch(b, e);
      if (null === b.stateNode) ij(a, b), Gi(b, c, d), Ii(b, c, d, e), d = true;
      else if (null === a) {
        var g = b.stateNode, h = b.memoizedProps;
        g.props = h;
        var k = g.context, l = c.contextType;
        "object" === typeof l && null !== l ? l = eh(l) : (l = Zf(c) ? Xf : H.current, l = Yf(b, l));
        var m = c.getDerivedStateFromProps, q = "function" === typeof m || "function" === typeof g.getSnapshotBeforeUpdate;
        q || "function" !== typeof g.UNSAFE_componentWillReceiveProps && "function" !== typeof g.componentWillReceiveProps || (h !== d || k !== l) && Hi(b, g, d, l);
        jh = false;
        var r = b.memoizedState;
        g.state = r;
        qh(b, d, g, e);
        k = b.memoizedState;
        h !== d || r !== k || Wf.current || jh ? ("function" === typeof m && (Di(b, c, m, d), k = b.memoizedState), (h = jh || Fi(b, c, h, d, r, k, l)) ? (q || "function" !== typeof g.UNSAFE_componentWillMount && "function" !== typeof g.componentWillMount || ("function" === typeof g.componentWillMount && g.componentWillMount(), "function" === typeof g.UNSAFE_componentWillMount && g.UNSAFE_componentWillMount()), "function" === typeof g.componentDidMount && (b.flags |= 4194308)) : ("function" === typeof g.componentDidMount && (b.flags |= 4194308), b.memoizedProps = d, b.memoizedState = k), g.props = d, g.state = k, g.context = l, d = h) : ("function" === typeof g.componentDidMount && (b.flags |= 4194308), d = false);
      } else {
        g = b.stateNode;
        lh(a, b);
        h = b.memoizedProps;
        l = b.type === b.elementType ? h : Ci(b.type, h);
        g.props = l;
        q = b.pendingProps;
        r = g.context;
        k = c.contextType;
        "object" === typeof k && null !== k ? k = eh(k) : (k = Zf(c) ? Xf : H.current, k = Yf(b, k));
        var y = c.getDerivedStateFromProps;
        (m = "function" === typeof y || "function" === typeof g.getSnapshotBeforeUpdate) || "function" !== typeof g.UNSAFE_componentWillReceiveProps && "function" !== typeof g.componentWillReceiveProps || (h !== q || r !== k) && Hi(b, g, d, k);
        jh = false;
        r = b.memoizedState;
        g.state = r;
        qh(b, d, g, e);
        var n = b.memoizedState;
        h !== q || r !== n || Wf.current || jh ? ("function" === typeof y && (Di(b, c, y, d), n = b.memoizedState), (l = jh || Fi(b, c, l, d, r, n, k) || false) ? (m || "function" !== typeof g.UNSAFE_componentWillUpdate && "function" !== typeof g.componentWillUpdate || ("function" === typeof g.componentWillUpdate && g.componentWillUpdate(d, n, k), "function" === typeof g.UNSAFE_componentWillUpdate && g.UNSAFE_componentWillUpdate(d, n, k)), "function" === typeof g.componentDidUpdate && (b.flags |= 4), "function" === typeof g.getSnapshotBeforeUpdate && (b.flags |= 1024)) : ("function" !== typeof g.componentDidUpdate || h === a.memoizedProps && r === a.memoizedState || (b.flags |= 4), "function" !== typeof g.getSnapshotBeforeUpdate || h === a.memoizedProps && r === a.memoizedState || (b.flags |= 1024), b.memoizedProps = d, b.memoizedState = n), g.props = d, g.state = n, g.context = k, d = l) : ("function" !== typeof g.componentDidUpdate || h === a.memoizedProps && r === a.memoizedState || (b.flags |= 4), "function" !== typeof g.getSnapshotBeforeUpdate || h === a.memoizedProps && r === a.memoizedState || (b.flags |= 1024), d = false);
      }
      return jj(a, b, c, d, f, e);
    }
    function jj(a, b, c, d, e, f) {
      gj(a, b);
      var g = 0 !== (b.flags & 128);
      if (!d && !g) return e && dg(b, c, false), Zi(a, b, f);
      d = b.stateNode;
      Wi.current = b;
      var h = g && "function" !== typeof c.getDerivedStateFromError ? null : d.render();
      b.flags |= 1;
      null !== a && g ? (b.child = Ug(b, a.child, null, f), b.child = Ug(b, null, h, f)) : Xi(a, b, h, f);
      b.memoizedState = d.state;
      e && dg(b, c, true);
      return b.child;
    }
    function kj(a) {
      var b = a.stateNode;
      b.pendingContext ? ag(a, b.pendingContext, b.pendingContext !== b.context) : b.context && ag(a, b.context, false);
      yh(a, b.containerInfo);
    }
    function lj(a, b, c, d, e) {
      Ig();
      Jg(e);
      b.flags |= 256;
      Xi(a, b, c, d);
      return b.child;
    }
    var mj = { dehydrated: null, treeContext: null, retryLane: 0 };
    function nj(a) {
      return { baseLanes: a, cachePool: null, transitions: null };
    }
    function oj(a, b, c) {
      var d = b.pendingProps, e = L.current, f = false, g = 0 !== (b.flags & 128), h;
      (h = g) || (h = null !== a && null === a.memoizedState ? false : 0 !== (e & 2));
      if (h) f = true, b.flags &= -129;
      else if (null === a || null !== a.memoizedState) e |= 1;
      G(L, e & 1);
      if (null === a) {
        Eg(b);
        a = b.memoizedState;
        if (null !== a && (a = a.dehydrated, null !== a)) return 0 === (b.mode & 1) ? b.lanes = 1 : "$!" === a.data ? b.lanes = 8 : b.lanes = 1073741824, null;
        g = d.children;
        a = d.fallback;
        return f ? (d = b.mode, f = b.child, g = { mode: "hidden", children: g }, 0 === (d & 1) && null !== f ? (f.childLanes = 0, f.pendingProps = g) : f = pj(g, d, 0, null), a = Tg(a, d, c, null), f.return = b, a.return = b, f.sibling = a, b.child = f, b.child.memoizedState = nj(c), b.memoizedState = mj, a) : qj(b, g);
      }
      e = a.memoizedState;
      if (null !== e && (h = e.dehydrated, null !== h)) return rj(a, b, g, d, h, e, c);
      if (f) {
        f = d.fallback;
        g = b.mode;
        e = a.child;
        h = e.sibling;
        var k = { mode: "hidden", children: d.children };
        0 === (g & 1) && b.child !== e ? (d = b.child, d.childLanes = 0, d.pendingProps = k, b.deletions = null) : (d = Pg(e, k), d.subtreeFlags = e.subtreeFlags & 14680064);
        null !== h ? f = Pg(h, f) : (f = Tg(f, g, c, null), f.flags |= 2);
        f.return = b;
        d.return = b;
        d.sibling = f;
        b.child = d;
        d = f;
        f = b.child;
        g = a.child.memoizedState;
        g = null === g ? nj(c) : { baseLanes: g.baseLanes | c, cachePool: null, transitions: g.transitions };
        f.memoizedState = g;
        f.childLanes = a.childLanes & ~c;
        b.memoizedState = mj;
        return d;
      }
      f = a.child;
      a = f.sibling;
      d = Pg(f, { mode: "visible", children: d.children });
      0 === (b.mode & 1) && (d.lanes = c);
      d.return = b;
      d.sibling = null;
      null !== a && (c = b.deletions, null === c ? (b.deletions = [a], b.flags |= 16) : c.push(a));
      b.child = d;
      b.memoizedState = null;
      return d;
    }
    function qj(a, b) {
      b = pj({ mode: "visible", children: b }, a.mode, 0, null);
      b.return = a;
      return a.child = b;
    }
    function sj(a, b, c, d) {
      null !== d && Jg(d);
      Ug(b, a.child, null, c);
      a = qj(b, b.pendingProps.children);
      a.flags |= 2;
      b.memoizedState = null;
      return a;
    }
    function rj(a, b, c, d, e, f, g) {
      if (c) {
        if (b.flags & 256) return b.flags &= -257, d = Ki(Error(p(422))), sj(a, b, g, d);
        if (null !== b.memoizedState) return b.child = a.child, b.flags |= 128, null;
        f = d.fallback;
        e = b.mode;
        d = pj({ mode: "visible", children: d.children }, e, 0, null);
        f = Tg(f, e, g, null);
        f.flags |= 2;
        d.return = b;
        f.return = b;
        d.sibling = f;
        b.child = d;
        0 !== (b.mode & 1) && Ug(b, a.child, null, g);
        b.child.memoizedState = nj(g);
        b.memoizedState = mj;
        return f;
      }
      if (0 === (b.mode & 1)) return sj(a, b, g, null);
      if ("$!" === e.data) {
        d = e.nextSibling && e.nextSibling.dataset;
        if (d) var h = d.dgst;
        d = h;
        f = Error(p(419));
        d = Ki(f, d, void 0);
        return sj(a, b, g, d);
      }
      h = 0 !== (g & a.childLanes);
      if (dh || h) {
        d = Q;
        if (null !== d) {
          switch (g & -g) {
            case 4:
              e = 2;
              break;
            case 16:
              e = 8;
              break;
            case 64:
            case 128:
            case 256:
            case 512:
            case 1024:
            case 2048:
            case 4096:
            case 8192:
            case 16384:
            case 32768:
            case 65536:
            case 131072:
            case 262144:
            case 524288:
            case 1048576:
            case 2097152:
            case 4194304:
            case 8388608:
            case 16777216:
            case 33554432:
            case 67108864:
              e = 32;
              break;
            case 536870912:
              e = 268435456;
              break;
            default:
              e = 0;
          }
          e = 0 !== (e & (d.suspendedLanes | g)) ? 0 : e;
          0 !== e && e !== f.retryLane && (f.retryLane = e, ih(a, e), gi(d, a, e, -1));
        }
        tj();
        d = Ki(Error(p(421)));
        return sj(a, b, g, d);
      }
      if ("$?" === e.data) return b.flags |= 128, b.child = a.child, b = uj.bind(null, a), e._reactRetry = b, null;
      a = f.treeContext;
      yg = Lf(e.nextSibling);
      xg = b;
      I = true;
      zg = null;
      null !== a && (og[pg++] = rg, og[pg++] = sg, og[pg++] = qg, rg = a.id, sg = a.overflow, qg = b);
      b = qj(b, d.children);
      b.flags |= 4096;
      return b;
    }
    function vj(a, b, c) {
      a.lanes |= b;
      var d = a.alternate;
      null !== d && (d.lanes |= b);
      bh(a.return, b, c);
    }
    function wj(a, b, c, d, e) {
      var f = a.memoizedState;
      null === f ? a.memoizedState = { isBackwards: b, rendering: null, renderingStartTime: 0, last: d, tail: c, tailMode: e } : (f.isBackwards = b, f.rendering = null, f.renderingStartTime = 0, f.last = d, f.tail = c, f.tailMode = e);
    }
    function xj(a, b, c) {
      var d = b.pendingProps, e = d.revealOrder, f = d.tail;
      Xi(a, b, d.children, c);
      d = L.current;
      if (0 !== (d & 2)) d = d & 1 | 2, b.flags |= 128;
      else {
        if (null !== a && 0 !== (a.flags & 128)) a: for (a = b.child; null !== a; ) {
          if (13 === a.tag) null !== a.memoizedState && vj(a, c, b);
          else if (19 === a.tag) vj(a, c, b);
          else if (null !== a.child) {
            a.child.return = a;
            a = a.child;
            continue;
          }
          if (a === b) break a;
          for (; null === a.sibling; ) {
            if (null === a.return || a.return === b) break a;
            a = a.return;
          }
          a.sibling.return = a.return;
          a = a.sibling;
        }
        d &= 1;
      }
      G(L, d);
      if (0 === (b.mode & 1)) b.memoizedState = null;
      else switch (e) {
        case "forwards":
          c = b.child;
          for (e = null; null !== c; ) a = c.alternate, null !== a && null === Ch(a) && (e = c), c = c.sibling;
          c = e;
          null === c ? (e = b.child, b.child = null) : (e = c.sibling, c.sibling = null);
          wj(b, false, e, c, f);
          break;
        case "backwards":
          c = null;
          e = b.child;
          for (b.child = null; null !== e; ) {
            a = e.alternate;
            if (null !== a && null === Ch(a)) {
              b.child = e;
              break;
            }
            a = e.sibling;
            e.sibling = c;
            c = e;
            e = a;
          }
          wj(b, true, c, null, f);
          break;
        case "together":
          wj(b, false, null, null, void 0);
          break;
        default:
          b.memoizedState = null;
      }
      return b.child;
    }
    function ij(a, b) {
      0 === (b.mode & 1) && null !== a && (a.alternate = null, b.alternate = null, b.flags |= 2);
    }
    function Zi(a, b, c) {
      null !== a && (b.dependencies = a.dependencies);
      rh |= b.lanes;
      if (0 === (c & b.childLanes)) return null;
      if (null !== a && b.child !== a.child) throw Error(p(153));
      if (null !== b.child) {
        a = b.child;
        c = Pg(a, a.pendingProps);
        b.child = c;
        for (c.return = b; null !== a.sibling; ) a = a.sibling, c = c.sibling = Pg(a, a.pendingProps), c.return = b;
        c.sibling = null;
      }
      return b.child;
    }
    function yj(a, b, c) {
      switch (b.tag) {
        case 3:
          kj(b);
          Ig();
          break;
        case 5:
          Ah(b);
          break;
        case 1:
          Zf(b.type) && cg(b);
          break;
        case 4:
          yh(b, b.stateNode.containerInfo);
          break;
        case 10:
          var d = b.type._context, e = b.memoizedProps.value;
          G(Wg, d._currentValue);
          d._currentValue = e;
          break;
        case 13:
          d = b.memoizedState;
          if (null !== d) {
            if (null !== d.dehydrated) return G(L, L.current & 1), b.flags |= 128, null;
            if (0 !== (c & b.child.childLanes)) return oj(a, b, c);
            G(L, L.current & 1);
            a = Zi(a, b, c);
            return null !== a ? a.sibling : null;
          }
          G(L, L.current & 1);
          break;
        case 19:
          d = 0 !== (c & b.childLanes);
          if (0 !== (a.flags & 128)) {
            if (d) return xj(a, b, c);
            b.flags |= 128;
          }
          e = b.memoizedState;
          null !== e && (e.rendering = null, e.tail = null, e.lastEffect = null);
          G(L, L.current);
          if (d) break;
          else return null;
        case 22:
        case 23:
          return b.lanes = 0, dj(a, b, c);
      }
      return Zi(a, b, c);
    }
    var zj, Aj, Bj, Cj;
    zj = function(a, b) {
      for (var c = b.child; null !== c; ) {
        if (5 === c.tag || 6 === c.tag) a.appendChild(c.stateNode);
        else if (4 !== c.tag && null !== c.child) {
          c.child.return = c;
          c = c.child;
          continue;
        }
        if (c === b) break;
        for (; null === c.sibling; ) {
          if (null === c.return || c.return === b) return;
          c = c.return;
        }
        c.sibling.return = c.return;
        c = c.sibling;
      }
    };
    Aj = function() {
    };
    Bj = function(a, b, c, d) {
      var e = a.memoizedProps;
      if (e !== d) {
        a = b.stateNode;
        xh(uh.current);
        var f = null;
        switch (c) {
          case "input":
            e = Ya(a, e);
            d = Ya(a, d);
            f = [];
            break;
          case "select":
            e = A({}, e, { value: void 0 });
            d = A({}, d, { value: void 0 });
            f = [];
            break;
          case "textarea":
            e = gb(a, e);
            d = gb(a, d);
            f = [];
            break;
          default:
            "function" !== typeof e.onClick && "function" === typeof d.onClick && (a.onclick = Bf);
        }
        ub(c, d);
        var g;
        c = null;
        for (l in e) if (!d.hasOwnProperty(l) && e.hasOwnProperty(l) && null != e[l]) if ("style" === l) {
          var h = e[l];
          for (g in h) h.hasOwnProperty(g) && (c || (c = {}), c[g] = "");
        } else "dangerouslySetInnerHTML" !== l && "children" !== l && "suppressContentEditableWarning" !== l && "suppressHydrationWarning" !== l && "autoFocus" !== l && (ea.hasOwnProperty(l) ? f || (f = []) : (f = f || []).push(l, null));
        for (l in d) {
          var k = d[l];
          h = null != e ? e[l] : void 0;
          if (d.hasOwnProperty(l) && k !== h && (null != k || null != h)) if ("style" === l) if (h) {
            for (g in h) !h.hasOwnProperty(g) || k && k.hasOwnProperty(g) || (c || (c = {}), c[g] = "");
            for (g in k) k.hasOwnProperty(g) && h[g] !== k[g] && (c || (c = {}), c[g] = k[g]);
          } else c || (f || (f = []), f.push(
            l,
            c
          )), c = k;
          else "dangerouslySetInnerHTML" === l ? (k = k ? k.__html : void 0, h = h ? h.__html : void 0, null != k && h !== k && (f = f || []).push(l, k)) : "children" === l ? "string" !== typeof k && "number" !== typeof k || (f = f || []).push(l, "" + k) : "suppressContentEditableWarning" !== l && "suppressHydrationWarning" !== l && (ea.hasOwnProperty(l) ? (null != k && "onScroll" === l && D("scroll", a), f || h === k || (f = [])) : (f = f || []).push(l, k));
        }
        c && (f = f || []).push("style", c);
        var l = f;
        if (b.updateQueue = l) b.flags |= 4;
      }
    };
    Cj = function(a, b, c, d) {
      c !== d && (b.flags |= 4);
    };
    function Dj(a, b) {
      if (!I) switch (a.tailMode) {
        case "hidden":
          b = a.tail;
          for (var c = null; null !== b; ) null !== b.alternate && (c = b), b = b.sibling;
          null === c ? a.tail = null : c.sibling = null;
          break;
        case "collapsed":
          c = a.tail;
          for (var d = null; null !== c; ) null !== c.alternate && (d = c), c = c.sibling;
          null === d ? b || null === a.tail ? a.tail = null : a.tail.sibling = null : d.sibling = null;
      }
    }
    function S(a) {
      var b = null !== a.alternate && a.alternate.child === a.child, c = 0, d = 0;
      if (b) for (var e = a.child; null !== e; ) c |= e.lanes | e.childLanes, d |= e.subtreeFlags & 14680064, d |= e.flags & 14680064, e.return = a, e = e.sibling;
      else for (e = a.child; null !== e; ) c |= e.lanes | e.childLanes, d |= e.subtreeFlags, d |= e.flags, e.return = a, e = e.sibling;
      a.subtreeFlags |= d;
      a.childLanes = c;
      return b;
    }
    function Ej(a, b, c) {
      var d = b.pendingProps;
      wg(b);
      switch (b.tag) {
        case 2:
        case 16:
        case 15:
        case 0:
        case 11:
        case 7:
        case 8:
        case 12:
        case 9:
        case 14:
          return S(b), null;
        case 1:
          return Zf(b.type) && $f(), S(b), null;
        case 3:
          d = b.stateNode;
          zh();
          E(Wf);
          E(H);
          Eh();
          d.pendingContext && (d.context = d.pendingContext, d.pendingContext = null);
          if (null === a || null === a.child) Gg(b) ? b.flags |= 4 : null === a || a.memoizedState.isDehydrated && 0 === (b.flags & 256) || (b.flags |= 1024, null !== zg && (Fj(zg), zg = null));
          Aj(a, b);
          S(b);
          return null;
        case 5:
          Bh(b);
          var e = xh(wh.current);
          c = b.type;
          if (null !== a && null != b.stateNode) Bj(a, b, c, d, e), a.ref !== b.ref && (b.flags |= 512, b.flags |= 2097152);
          else {
            if (!d) {
              if (null === b.stateNode) throw Error(p(166));
              S(b);
              return null;
            }
            a = xh(uh.current);
            if (Gg(b)) {
              d = b.stateNode;
              c = b.type;
              var f = b.memoizedProps;
              d[Of] = b;
              d[Pf] = f;
              a = 0 !== (b.mode & 1);
              switch (c) {
                case "dialog":
                  D("cancel", d);
                  D("close", d);
                  break;
                case "iframe":
                case "object":
                case "embed":
                  D("load", d);
                  break;
                case "video":
                case "audio":
                  for (e = 0; e < lf.length; e++) D(lf[e], d);
                  break;
                case "source":
                  D("error", d);
                  break;
                case "img":
                case "image":
                case "link":
                  D(
                    "error",
                    d
                  );
                  D("load", d);
                  break;
                case "details":
                  D("toggle", d);
                  break;
                case "input":
                  Za(d, f);
                  D("invalid", d);
                  break;
                case "select":
                  d._wrapperState = { wasMultiple: !!f.multiple };
                  D("invalid", d);
                  break;
                case "textarea":
                  hb(d, f), D("invalid", d);
              }
              ub(c, f);
              e = null;
              for (var g in f) if (f.hasOwnProperty(g)) {
                var h = f[g];
                "children" === g ? "string" === typeof h ? d.textContent !== h && (true !== f.suppressHydrationWarning && Af(d.textContent, h, a), e = ["children", h]) : "number" === typeof h && d.textContent !== "" + h && (true !== f.suppressHydrationWarning && Af(
                  d.textContent,
                  h,
                  a
                ), e = ["children", "" + h]) : ea.hasOwnProperty(g) && null != h && "onScroll" === g && D("scroll", d);
              }
              switch (c) {
                case "input":
                  Va(d);
                  db(d, f, true);
                  break;
                case "textarea":
                  Va(d);
                  jb(d);
                  break;
                case "select":
                case "option":
                  break;
                default:
                  "function" === typeof f.onClick && (d.onclick = Bf);
              }
              d = e;
              b.updateQueue = d;
              null !== d && (b.flags |= 4);
            } else {
              g = 9 === e.nodeType ? e : e.ownerDocument;
              "http://www.w3.org/1999/xhtml" === a && (a = kb(c));
              "http://www.w3.org/1999/xhtml" === a ? "script" === c ? (a = g.createElement("div"), a.innerHTML = "<script><\/script>", a = a.removeChild(a.firstChild)) : "string" === typeof d.is ? a = g.createElement(c, { is: d.is }) : (a = g.createElement(c), "select" === c && (g = a, d.multiple ? g.multiple = true : d.size && (g.size = d.size))) : a = g.createElementNS(a, c);
              a[Of] = b;
              a[Pf] = d;
              zj(a, b, false, false);
              b.stateNode = a;
              a: {
                g = vb(c, d);
                switch (c) {
                  case "dialog":
                    D("cancel", a);
                    D("close", a);
                    e = d;
                    break;
                  case "iframe":
                  case "object":
                  case "embed":
                    D("load", a);
                    e = d;
                    break;
                  case "video":
                  case "audio":
                    for (e = 0; e < lf.length; e++) D(lf[e], a);
                    e = d;
                    break;
                  case "source":
                    D("error", a);
                    e = d;
                    break;
                  case "img":
                  case "image":
                  case "link":
                    D(
                      "error",
                      a
                    );
                    D("load", a);
                    e = d;
                    break;
                  case "details":
                    D("toggle", a);
                    e = d;
                    break;
                  case "input":
                    Za(a, d);
                    e = Ya(a, d);
                    D("invalid", a);
                    break;
                  case "option":
                    e = d;
                    break;
                  case "select":
                    a._wrapperState = { wasMultiple: !!d.multiple };
                    e = A({}, d, { value: void 0 });
                    D("invalid", a);
                    break;
                  case "textarea":
                    hb(a, d);
                    e = gb(a, d);
                    D("invalid", a);
                    break;
                  default:
                    e = d;
                }
                ub(c, e);
                h = e;
                for (f in h) if (h.hasOwnProperty(f)) {
                  var k = h[f];
                  "style" === f ? sb(a, k) : "dangerouslySetInnerHTML" === f ? (k = k ? k.__html : void 0, null != k && nb(a, k)) : "children" === f ? "string" === typeof k ? ("textarea" !== c || "" !== k) && ob(a, k) : "number" === typeof k && ob(a, "" + k) : "suppressContentEditableWarning" !== f && "suppressHydrationWarning" !== f && "autoFocus" !== f && (ea.hasOwnProperty(f) ? null != k && "onScroll" === f && D("scroll", a) : null != k && ta(a, f, k, g));
                }
                switch (c) {
                  case "input":
                    Va(a);
                    db(a, d, false);
                    break;
                  case "textarea":
                    Va(a);
                    jb(a);
                    break;
                  case "option":
                    null != d.value && a.setAttribute("value", "" + Sa(d.value));
                    break;
                  case "select":
                    a.multiple = !!d.multiple;
                    f = d.value;
                    null != f ? fb(a, !!d.multiple, f, false) : null != d.defaultValue && fb(
                      a,
                      !!d.multiple,
                      d.defaultValue,
                      true
                    );
                    break;
                  default:
                    "function" === typeof e.onClick && (a.onclick = Bf);
                }
                switch (c) {
                  case "button":
                  case "input":
                  case "select":
                  case "textarea":
                    d = !!d.autoFocus;
                    break a;
                  case "img":
                    d = true;
                    break a;
                  default:
                    d = false;
                }
              }
              d && (b.flags |= 4);
            }
            null !== b.ref && (b.flags |= 512, b.flags |= 2097152);
          }
          S(b);
          return null;
        case 6:
          if (a && null != b.stateNode) Cj(a, b, a.memoizedProps, d);
          else {
            if ("string" !== typeof d && null === b.stateNode) throw Error(p(166));
            c = xh(wh.current);
            xh(uh.current);
            if (Gg(b)) {
              d = b.stateNode;
              c = b.memoizedProps;
              d[Of] = b;
              if (f = d.nodeValue !== c) {
                if (a = xg, null !== a) switch (a.tag) {
                  case 3:
                    Af(d.nodeValue, c, 0 !== (a.mode & 1));
                    break;
                  case 5:
                    true !== a.memoizedProps.suppressHydrationWarning && Af(d.nodeValue, c, 0 !== (a.mode & 1));
                }
              }
              f && (b.flags |= 4);
            } else d = (9 === c.nodeType ? c : c.ownerDocument).createTextNode(d), d[Of] = b, b.stateNode = d;
          }
          S(b);
          return null;
        case 13:
          E(L);
          d = b.memoizedState;
          if (null === a || null !== a.memoizedState && null !== a.memoizedState.dehydrated) {
            if (I && null !== yg && 0 !== (b.mode & 1) && 0 === (b.flags & 128)) Hg(), Ig(), b.flags |= 98560, f = false;
            else if (f = Gg(b), null !== d && null !== d.dehydrated) {
              if (null === a) {
                if (!f) throw Error(p(318));
                f = b.memoizedState;
                f = null !== f ? f.dehydrated : null;
                if (!f) throw Error(p(317));
                f[Of] = b;
              } else Ig(), 0 === (b.flags & 128) && (b.memoizedState = null), b.flags |= 4;
              S(b);
              f = false;
            } else null !== zg && (Fj(zg), zg = null), f = true;
            if (!f) return b.flags & 65536 ? b : null;
          }
          if (0 !== (b.flags & 128)) return b.lanes = c, b;
          d = null !== d;
          d !== (null !== a && null !== a.memoizedState) && d && (b.child.flags |= 8192, 0 !== (b.mode & 1) && (null === a || 0 !== (L.current & 1) ? 0 === T && (T = 3) : tj()));
          null !== b.updateQueue && (b.flags |= 4);
          S(b);
          return null;
        case 4:
          return zh(), Aj(a, b), null === a && sf(b.stateNode.containerInfo), S(b), null;
        case 10:
          return ah(b.type._context), S(b), null;
        case 17:
          return Zf(b.type) && $f(), S(b), null;
        case 19:
          E(L);
          f = b.memoizedState;
          if (null === f) return S(b), null;
          d = 0 !== (b.flags & 128);
          g = f.rendering;
          if (null === g) if (d) Dj(f, false);
          else {
            if (0 !== T || null !== a && 0 !== (a.flags & 128)) for (a = b.child; null !== a; ) {
              g = Ch(a);
              if (null !== g) {
                b.flags |= 128;
                Dj(f, false);
                d = g.updateQueue;
                null !== d && (b.updateQueue = d, b.flags |= 4);
                b.subtreeFlags = 0;
                d = c;
                for (c = b.child; null !== c; ) f = c, a = d, f.flags &= 14680066, g = f.alternate, null === g ? (f.childLanes = 0, f.lanes = a, f.child = null, f.subtreeFlags = 0, f.memoizedProps = null, f.memoizedState = null, f.updateQueue = null, f.dependencies = null, f.stateNode = null) : (f.childLanes = g.childLanes, f.lanes = g.lanes, f.child = g.child, f.subtreeFlags = 0, f.deletions = null, f.memoizedProps = g.memoizedProps, f.memoizedState = g.memoizedState, f.updateQueue = g.updateQueue, f.type = g.type, a = g.dependencies, f.dependencies = null === a ? null : { lanes: a.lanes, firstContext: a.firstContext }), c = c.sibling;
                G(L, L.current & 1 | 2);
                return b.child;
              }
              a = a.sibling;
            }
            null !== f.tail && B() > Gj && (b.flags |= 128, d = true, Dj(f, false), b.lanes = 4194304);
          }
          else {
            if (!d) if (a = Ch(g), null !== a) {
              if (b.flags |= 128, d = true, c = a.updateQueue, null !== c && (b.updateQueue = c, b.flags |= 4), Dj(f, true), null === f.tail && "hidden" === f.tailMode && !g.alternate && !I) return S(b), null;
            } else 2 * B() - f.renderingStartTime > Gj && 1073741824 !== c && (b.flags |= 128, d = true, Dj(f, false), b.lanes = 4194304);
            f.isBackwards ? (g.sibling = b.child, b.child = g) : (c = f.last, null !== c ? c.sibling = g : b.child = g, f.last = g);
          }
          if (null !== f.tail) return b = f.tail, f.rendering = b, f.tail = b.sibling, f.renderingStartTime = B(), b.sibling = null, c = L.current, G(L, d ? c & 1 | 2 : c & 1), b;
          S(b);
          return null;
        case 22:
        case 23:
          return Hj(), d = null !== b.memoizedState, null !== a && null !== a.memoizedState !== d && (b.flags |= 8192), d && 0 !== (b.mode & 1) ? 0 !== (fj & 1073741824) && (S(b), b.subtreeFlags & 6 && (b.flags |= 8192)) : S(b), null;
        case 24:
          return null;
        case 25:
          return null;
      }
      throw Error(p(156, b.tag));
    }
    function Ij(a, b) {
      wg(b);
      switch (b.tag) {
        case 1:
          return Zf(b.type) && $f(), a = b.flags, a & 65536 ? (b.flags = a & -65537 | 128, b) : null;
        case 3:
          return zh(), E(Wf), E(H), Eh(), a = b.flags, 0 !== (a & 65536) && 0 === (a & 128) ? (b.flags = a & -65537 | 128, b) : null;
        case 5:
          return Bh(b), null;
        case 13:
          E(L);
          a = b.memoizedState;
          if (null !== a && null !== a.dehydrated) {
            if (null === b.alternate) throw Error(p(340));
            Ig();
          }
          a = b.flags;
          return a & 65536 ? (b.flags = a & -65537 | 128, b) : null;
        case 19:
          return E(L), null;
        case 4:
          return zh(), null;
        case 10:
          return ah(b.type._context), null;
        case 22:
        case 23:
          return Hj(), null;
        case 24:
          return null;
        default:
          return null;
      }
    }
    var Jj = false, U = false, Kj = "function" === typeof WeakSet ? WeakSet : Set, V = null;
    function Lj(a, b) {
      var c = a.ref;
      if (null !== c) if ("function" === typeof c) try {
        c(null);
      } catch (d) {
        W(a, b, d);
      }
      else c.current = null;
    }
    function Mj(a, b, c) {
      try {
        c();
      } catch (d) {
        W(a, b, d);
      }
    }
    var Nj = false;
    function Oj(a, b) {
      Cf = dd;
      a = Me();
      if (Ne(a)) {
        if ("selectionStart" in a) var c = { start: a.selectionStart, end: a.selectionEnd };
        else a: {
          c = (c = a.ownerDocument) && c.defaultView || window;
          var d = c.getSelection && c.getSelection();
          if (d && 0 !== d.rangeCount) {
            c = d.anchorNode;
            var e = d.anchorOffset, f = d.focusNode;
            d = d.focusOffset;
            try {
              c.nodeType, f.nodeType;
            } catch (F) {
              c = null;
              break a;
            }
            var g = 0, h = -1, k = -1, l = 0, m = 0, q = a, r = null;
            b: for (; ; ) {
              for (var y; ; ) {
                q !== c || 0 !== e && 3 !== q.nodeType || (h = g + e);
                q !== f || 0 !== d && 3 !== q.nodeType || (k = g + d);
                3 === q.nodeType && (g += q.nodeValue.length);
                if (null === (y = q.firstChild)) break;
                r = q;
                q = y;
              }
              for (; ; ) {
                if (q === a) break b;
                r === c && ++l === e && (h = g);
                r === f && ++m === d && (k = g);
                if (null !== (y = q.nextSibling)) break;
                q = r;
                r = q.parentNode;
              }
              q = y;
            }
            c = -1 === h || -1 === k ? null : { start: h, end: k };
          } else c = null;
        }
        c = c || { start: 0, end: 0 };
      } else c = null;
      Df = { focusedElem: a, selectionRange: c };
      dd = false;
      for (V = b; null !== V; ) if (b = V, a = b.child, 0 !== (b.subtreeFlags & 1028) && null !== a) a.return = b, V = a;
      else for (; null !== V; ) {
        b = V;
        try {
          var n = b.alternate;
          if (0 !== (b.flags & 1024)) switch (b.tag) {
            case 0:
            case 11:
            case 15:
              break;
            case 1:
              if (null !== n) {
                var t = n.memoizedProps, J = n.memoizedState, x = b.stateNode, w = x.getSnapshotBeforeUpdate(b.elementType === b.type ? t : Ci(b.type, t), J);
                x.__reactInternalSnapshotBeforeUpdate = w;
              }
              break;
            case 3:
              var u = b.stateNode.containerInfo;
              1 === u.nodeType ? u.textContent = "" : 9 === u.nodeType && u.documentElement && u.removeChild(u.documentElement);
              break;
            case 5:
            case 6:
            case 4:
            case 17:
              break;
            default:
              throw Error(p(163));
          }
        } catch (F) {
          W(b, b.return, F);
        }
        a = b.sibling;
        if (null !== a) {
          a.return = b.return;
          V = a;
          break;
        }
        V = b.return;
      }
      n = Nj;
      Nj = false;
      return n;
    }
    function Pj(a, b, c) {
      var d = b.updateQueue;
      d = null !== d ? d.lastEffect : null;
      if (null !== d) {
        var e = d = d.next;
        do {
          if ((e.tag & a) === a) {
            var f = e.destroy;
            e.destroy = void 0;
            void 0 !== f && Mj(b, c, f);
          }
          e = e.next;
        } while (e !== d);
      }
    }
    function Qj(a, b) {
      b = b.updateQueue;
      b = null !== b ? b.lastEffect : null;
      if (null !== b) {
        var c = b = b.next;
        do {
          if ((c.tag & a) === a) {
            var d = c.create;
            c.destroy = d();
          }
          c = c.next;
        } while (c !== b);
      }
    }
    function Rj(a) {
      var b = a.ref;
      if (null !== b) {
        var c = a.stateNode;
        switch (a.tag) {
          case 5:
            a = c;
            break;
          default:
            a = c;
        }
        "function" === typeof b ? b(a) : b.current = a;
      }
    }
    function Sj(a) {
      var b = a.alternate;
      null !== b && (a.alternate = null, Sj(b));
      a.child = null;
      a.deletions = null;
      a.sibling = null;
      5 === a.tag && (b = a.stateNode, null !== b && (delete b[Of], delete b[Pf], delete b[of], delete b[Qf], delete b[Rf]));
      a.stateNode = null;
      a.return = null;
      a.dependencies = null;
      a.memoizedProps = null;
      a.memoizedState = null;
      a.pendingProps = null;
      a.stateNode = null;
      a.updateQueue = null;
    }
    function Tj(a) {
      return 5 === a.tag || 3 === a.tag || 4 === a.tag;
    }
    function Uj(a) {
      a: for (; ; ) {
        for (; null === a.sibling; ) {
          if (null === a.return || Tj(a.return)) return null;
          a = a.return;
        }
        a.sibling.return = a.return;
        for (a = a.sibling; 5 !== a.tag && 6 !== a.tag && 18 !== a.tag; ) {
          if (a.flags & 2) continue a;
          if (null === a.child || 4 === a.tag) continue a;
          else a.child.return = a, a = a.child;
        }
        if (!(a.flags & 2)) return a.stateNode;
      }
    }
    function Vj(a, b, c) {
      var d = a.tag;
      if (5 === d || 6 === d) a = a.stateNode, b ? 8 === c.nodeType ? c.parentNode.insertBefore(a, b) : c.insertBefore(a, b) : (8 === c.nodeType ? (b = c.parentNode, b.insertBefore(a, c)) : (b = c, b.appendChild(a)), c = c._reactRootContainer, null !== c && void 0 !== c || null !== b.onclick || (b.onclick = Bf));
      else if (4 !== d && (a = a.child, null !== a)) for (Vj(a, b, c), a = a.sibling; null !== a; ) Vj(a, b, c), a = a.sibling;
    }
    function Wj(a, b, c) {
      var d = a.tag;
      if (5 === d || 6 === d) a = a.stateNode, b ? c.insertBefore(a, b) : c.appendChild(a);
      else if (4 !== d && (a = a.child, null !== a)) for (Wj(a, b, c), a = a.sibling; null !== a; ) Wj(a, b, c), a = a.sibling;
    }
    var X2 = null, Xj = false;
    function Yj(a, b, c) {
      for (c = c.child; null !== c; ) Zj(a, b, c), c = c.sibling;
    }
    function Zj(a, b, c) {
      if (lc && "function" === typeof lc.onCommitFiberUnmount) try {
        lc.onCommitFiberUnmount(kc, c);
      } catch (h) {
      }
      switch (c.tag) {
        case 5:
          U || Lj(c, b);
        case 6:
          var d = X2, e = Xj;
          X2 = null;
          Yj(a, b, c);
          X2 = d;
          Xj = e;
          null !== X2 && (Xj ? (a = X2, c = c.stateNode, 8 === a.nodeType ? a.parentNode.removeChild(c) : a.removeChild(c)) : X2.removeChild(c.stateNode));
          break;
        case 18:
          null !== X2 && (Xj ? (a = X2, c = c.stateNode, 8 === a.nodeType ? Kf(a.parentNode, c) : 1 === a.nodeType && Kf(a, c), bd(a)) : Kf(X2, c.stateNode));
          break;
        case 4:
          d = X2;
          e = Xj;
          X2 = c.stateNode.containerInfo;
          Xj = true;
          Yj(a, b, c);
          X2 = d;
          Xj = e;
          break;
        case 0:
        case 11:
        case 14:
        case 15:
          if (!U && (d = c.updateQueue, null !== d && (d = d.lastEffect, null !== d))) {
            e = d = d.next;
            do {
              var f = e, g = f.destroy;
              f = f.tag;
              void 0 !== g && (0 !== (f & 2) ? Mj(c, b, g) : 0 !== (f & 4) && Mj(c, b, g));
              e = e.next;
            } while (e !== d);
          }
          Yj(a, b, c);
          break;
        case 1:
          if (!U && (Lj(c, b), d = c.stateNode, "function" === typeof d.componentWillUnmount)) try {
            d.props = c.memoizedProps, d.state = c.memoizedState, d.componentWillUnmount();
          } catch (h) {
            W(c, b, h);
          }
          Yj(a, b, c);
          break;
        case 21:
          Yj(a, b, c);
          break;
        case 22:
          c.mode & 1 ? (U = (d = U) || null !== c.memoizedState, Yj(a, b, c), U = d) : Yj(a, b, c);
          break;
        default:
          Yj(a, b, c);
      }
    }
    function ak(a) {
      var b = a.updateQueue;
      if (null !== b) {
        a.updateQueue = null;
        var c = a.stateNode;
        null === c && (c = a.stateNode = new Kj());
        b.forEach(function(b2) {
          var d = bk.bind(null, a, b2);
          c.has(b2) || (c.add(b2), b2.then(d, d));
        });
      }
    }
    function ck(a, b) {
      var c = b.deletions;
      if (null !== c) for (var d = 0; d < c.length; d++) {
        var e = c[d];
        try {
          var f = a, g = b, h = g;
          a: for (; null !== h; ) {
            switch (h.tag) {
              case 5:
                X2 = h.stateNode;
                Xj = false;
                break a;
              case 3:
                X2 = h.stateNode.containerInfo;
                Xj = true;
                break a;
              case 4:
                X2 = h.stateNode.containerInfo;
                Xj = true;
                break a;
            }
            h = h.return;
          }
          if (null === X2) throw Error(p(160));
          Zj(f, g, e);
          X2 = null;
          Xj = false;
          var k = e.alternate;
          null !== k && (k.return = null);
          e.return = null;
        } catch (l) {
          W(e, b, l);
        }
      }
      if (b.subtreeFlags & 12854) for (b = b.child; null !== b; ) dk(b, a), b = b.sibling;
    }
    function dk(a, b) {
      var c = a.alternate, d = a.flags;
      switch (a.tag) {
        case 0:
        case 11:
        case 14:
        case 15:
          ck(b, a);
          ek(a);
          if (d & 4) {
            try {
              Pj(3, a, a.return), Qj(3, a);
            } catch (t) {
              W(a, a.return, t);
            }
            try {
              Pj(5, a, a.return);
            } catch (t) {
              W(a, a.return, t);
            }
          }
          break;
        case 1:
          ck(b, a);
          ek(a);
          d & 512 && null !== c && Lj(c, c.return);
          break;
        case 5:
          ck(b, a);
          ek(a);
          d & 512 && null !== c && Lj(c, c.return);
          if (a.flags & 32) {
            var e = a.stateNode;
            try {
              ob(e, "");
            } catch (t) {
              W(a, a.return, t);
            }
          }
          if (d & 4 && (e = a.stateNode, null != e)) {
            var f = a.memoizedProps, g = null !== c ? c.memoizedProps : f, h = a.type, k = a.updateQueue;
            a.updateQueue = null;
            if (null !== k) try {
              "input" === h && "radio" === f.type && null != f.name && ab(e, f);
              vb(h, g);
              var l = vb(h, f);
              for (g = 0; g < k.length; g += 2) {
                var m = k[g], q = k[g + 1];
                "style" === m ? sb(e, q) : "dangerouslySetInnerHTML" === m ? nb(e, q) : "children" === m ? ob(e, q) : ta(e, m, q, l);
              }
              switch (h) {
                case "input":
                  bb(e, f);
                  break;
                case "textarea":
                  ib(e, f);
                  break;
                case "select":
                  var r = e._wrapperState.wasMultiple;
                  e._wrapperState.wasMultiple = !!f.multiple;
                  var y = f.value;
                  null != y ? fb(e, !!f.multiple, y, false) : r !== !!f.multiple && (null != f.defaultValue ? fb(
                    e,
                    !!f.multiple,
                    f.defaultValue,
                    true
                  ) : fb(e, !!f.multiple, f.multiple ? [] : "", false));
              }
              e[Pf] = f;
            } catch (t) {
              W(a, a.return, t);
            }
          }
          break;
        case 6:
          ck(b, a);
          ek(a);
          if (d & 4) {
            if (null === a.stateNode) throw Error(p(162));
            e = a.stateNode;
            f = a.memoizedProps;
            try {
              e.nodeValue = f;
            } catch (t) {
              W(a, a.return, t);
            }
          }
          break;
        case 3:
          ck(b, a);
          ek(a);
          if (d & 4 && null !== c && c.memoizedState.isDehydrated) try {
            bd(b.containerInfo);
          } catch (t) {
            W(a, a.return, t);
          }
          break;
        case 4:
          ck(b, a);
          ek(a);
          break;
        case 13:
          ck(b, a);
          ek(a);
          e = a.child;
          e.flags & 8192 && (f = null !== e.memoizedState, e.stateNode.isHidden = f, !f || null !== e.alternate && null !== e.alternate.memoizedState || (fk = B()));
          d & 4 && ak(a);
          break;
        case 22:
          m = null !== c && null !== c.memoizedState;
          a.mode & 1 ? (U = (l = U) || m, ck(b, a), U = l) : ck(b, a);
          ek(a);
          if (d & 8192) {
            l = null !== a.memoizedState;
            if ((a.stateNode.isHidden = l) && !m && 0 !== (a.mode & 1)) for (V = a, m = a.child; null !== m; ) {
              for (q = V = m; null !== V; ) {
                r = V;
                y = r.child;
                switch (r.tag) {
                  case 0:
                  case 11:
                  case 14:
                  case 15:
                    Pj(4, r, r.return);
                    break;
                  case 1:
                    Lj(r, r.return);
                    var n = r.stateNode;
                    if ("function" === typeof n.componentWillUnmount) {
                      d = r;
                      c = r.return;
                      try {
                        b = d, n.props = b.memoizedProps, n.state = b.memoizedState, n.componentWillUnmount();
                      } catch (t) {
                        W(d, c, t);
                      }
                    }
                    break;
                  case 5:
                    Lj(r, r.return);
                    break;
                  case 22:
                    if (null !== r.memoizedState) {
                      gk(q);
                      continue;
                    }
                }
                null !== y ? (y.return = r, V = y) : gk(q);
              }
              m = m.sibling;
            }
            a: for (m = null, q = a; ; ) {
              if (5 === q.tag) {
                if (null === m) {
                  m = q;
                  try {
                    e = q.stateNode, l ? (f = e.style, "function" === typeof f.setProperty ? f.setProperty("display", "none", "important") : f.display = "none") : (h = q.stateNode, k = q.memoizedProps.style, g = void 0 !== k && null !== k && k.hasOwnProperty("display") ? k.display : null, h.style.display = rb("display", g));
                  } catch (t) {
                    W(a, a.return, t);
                  }
                }
              } else if (6 === q.tag) {
                if (null === m) try {
                  q.stateNode.nodeValue = l ? "" : q.memoizedProps;
                } catch (t) {
                  W(a, a.return, t);
                }
              } else if ((22 !== q.tag && 23 !== q.tag || null === q.memoizedState || q === a) && null !== q.child) {
                q.child.return = q;
                q = q.child;
                continue;
              }
              if (q === a) break a;
              for (; null === q.sibling; ) {
                if (null === q.return || q.return === a) break a;
                m === q && (m = null);
                q = q.return;
              }
              m === q && (m = null);
              q.sibling.return = q.return;
              q = q.sibling;
            }
          }
          break;
        case 19:
          ck(b, a);
          ek(a);
          d & 4 && ak(a);
          break;
        case 21:
          break;
        default:
          ck(
            b,
            a
          ), ek(a);
      }
    }
    function ek(a) {
      var b = a.flags;
      if (b & 2) {
        try {
          a: {
            for (var c = a.return; null !== c; ) {
              if (Tj(c)) {
                var d = c;
                break a;
              }
              c = c.return;
            }
            throw Error(p(160));
          }
          switch (d.tag) {
            case 5:
              var e = d.stateNode;
              d.flags & 32 && (ob(e, ""), d.flags &= -33);
              var f = Uj(a);
              Wj(a, f, e);
              break;
            case 3:
            case 4:
              var g = d.stateNode.containerInfo, h = Uj(a);
              Vj(a, h, g);
              break;
            default:
              throw Error(p(161));
          }
        } catch (k) {
          W(a, a.return, k);
        }
        a.flags &= -3;
      }
      b & 4096 && (a.flags &= -4097);
    }
    function hk(a, b, c) {
      V = a;
      ik(a);
    }
    function ik(a, b, c) {
      for (var d = 0 !== (a.mode & 1); null !== V; ) {
        var e = V, f = e.child;
        if (22 === e.tag && d) {
          var g = null !== e.memoizedState || Jj;
          if (!g) {
            var h = e.alternate, k = null !== h && null !== h.memoizedState || U;
            h = Jj;
            var l = U;
            Jj = g;
            if ((U = k) && !l) for (V = e; null !== V; ) g = V, k = g.child, 22 === g.tag && null !== g.memoizedState ? jk(e) : null !== k ? (k.return = g, V = k) : jk(e);
            for (; null !== f; ) V = f, ik(f), f = f.sibling;
            V = e;
            Jj = h;
            U = l;
          }
          kk(a);
        } else 0 !== (e.subtreeFlags & 8772) && null !== f ? (f.return = e, V = f) : kk(a);
      }
    }
    function kk(a) {
      for (; null !== V; ) {
        var b = V;
        if (0 !== (b.flags & 8772)) {
          var c = b.alternate;
          try {
            if (0 !== (b.flags & 8772)) switch (b.tag) {
              case 0:
              case 11:
              case 15:
                U || Qj(5, b);
                break;
              case 1:
                var d = b.stateNode;
                if (b.flags & 4 && !U) if (null === c) d.componentDidMount();
                else {
                  var e = b.elementType === b.type ? c.memoizedProps : Ci(b.type, c.memoizedProps);
                  d.componentDidUpdate(e, c.memoizedState, d.__reactInternalSnapshotBeforeUpdate);
                }
                var f = b.updateQueue;
                null !== f && sh(b, f, d);
                break;
              case 3:
                var g = b.updateQueue;
                if (null !== g) {
                  c = null;
                  if (null !== b.child) switch (b.child.tag) {
                    case 5:
                      c = b.child.stateNode;
                      break;
                    case 1:
                      c = b.child.stateNode;
                  }
                  sh(b, g, c);
                }
                break;
              case 5:
                var h = b.stateNode;
                if (null === c && b.flags & 4) {
                  c = h;
                  var k = b.memoizedProps;
                  switch (b.type) {
                    case "button":
                    case "input":
                    case "select":
                    case "textarea":
                      k.autoFocus && c.focus();
                      break;
                    case "img":
                      k.src && (c.src = k.src);
                  }
                }
                break;
              case 6:
                break;
              case 4:
                break;
              case 12:
                break;
              case 13:
                if (null === b.memoizedState) {
                  var l = b.alternate;
                  if (null !== l) {
                    var m = l.memoizedState;
                    if (null !== m) {
                      var q = m.dehydrated;
                      null !== q && bd(q);
                    }
                  }
                }
                break;
              case 19:
              case 17:
              case 21:
              case 22:
              case 23:
              case 25:
                break;
              default:
                throw Error(p(163));
            }
            U || b.flags & 512 && Rj(b);
          } catch (r) {
            W(b, b.return, r);
          }
        }
        if (b === a) {
          V = null;
          break;
        }
        c = b.sibling;
        if (null !== c) {
          c.return = b.return;
          V = c;
          break;
        }
        V = b.return;
      }
    }
    function gk(a) {
      for (; null !== V; ) {
        var b = V;
        if (b === a) {
          V = null;
          break;
        }
        var c = b.sibling;
        if (null !== c) {
          c.return = b.return;
          V = c;
          break;
        }
        V = b.return;
      }
    }
    function jk(a) {
      for (; null !== V; ) {
        var b = V;
        try {
          switch (b.tag) {
            case 0:
            case 11:
            case 15:
              var c = b.return;
              try {
                Qj(4, b);
              } catch (k) {
                W(b, c, k);
              }
              break;
            case 1:
              var d = b.stateNode;
              if ("function" === typeof d.componentDidMount) {
                var e = b.return;
                try {
                  d.componentDidMount();
                } catch (k) {
                  W(b, e, k);
                }
              }
              var f = b.return;
              try {
                Rj(b);
              } catch (k) {
                W(b, f, k);
              }
              break;
            case 5:
              var g = b.return;
              try {
                Rj(b);
              } catch (k) {
                W(b, g, k);
              }
          }
        } catch (k) {
          W(b, b.return, k);
        }
        if (b === a) {
          V = null;
          break;
        }
        var h = b.sibling;
        if (null !== h) {
          h.return = b.return;
          V = h;
          break;
        }
        V = b.return;
      }
    }
    var lk = Math.ceil, mk = ua.ReactCurrentDispatcher, nk = ua.ReactCurrentOwner, ok = ua.ReactCurrentBatchConfig, K = 0, Q = null, Y = null, Z = 0, fj = 0, ej = Uf(0), T = 0, pk = null, rh = 0, qk = 0, rk = 0, sk = null, tk = null, fk = 0, Gj = Infinity, uk = null, Oi = false, Pi = null, Ri = null, vk = false, wk = null, xk = 0, yk = 0, zk = null, Ak = -1, Bk = 0;
    function R() {
      return 0 !== (K & 6) ? B() : -1 !== Ak ? Ak : Ak = B();
    }
    function yi(a) {
      if (0 === (a.mode & 1)) return 1;
      if (0 !== (K & 2) && 0 !== Z) return Z & -Z;
      if (null !== Kg.transition) return 0 === Bk && (Bk = yc()), Bk;
      a = C;
      if (0 !== a) return a;
      a = window.event;
      a = void 0 === a ? 16 : jd(a.type);
      return a;
    }
    function gi(a, b, c, d) {
      if (50 < yk) throw yk = 0, zk = null, Error(p(185));
      Ac(a, c, d);
      if (0 === (K & 2) || a !== Q) a === Q && (0 === (K & 2) && (qk |= c), 4 === T && Ck(a, Z)), Dk(a, d), 1 === c && 0 === K && 0 === (b.mode & 1) && (Gj = B() + 500, fg && jg());
    }
    function Dk(a, b) {
      var c = a.callbackNode;
      wc(a, b);
      var d = uc(a, a === Q ? Z : 0);
      if (0 === d) null !== c && bc(c), a.callbackNode = null, a.callbackPriority = 0;
      else if (b = d & -d, a.callbackPriority !== b) {
        null != c && bc(c);
        if (1 === b) 0 === a.tag ? ig(Ek.bind(null, a)) : hg(Ek.bind(null, a)), Jf(function() {
          0 === (K & 6) && jg();
        }), c = null;
        else {
          switch (Dc(d)) {
            case 1:
              c = fc;
              break;
            case 4:
              c = gc;
              break;
            case 16:
              c = hc;
              break;
            case 536870912:
              c = jc;
              break;
            default:
              c = hc;
          }
          c = Fk(c, Gk.bind(null, a));
        }
        a.callbackPriority = b;
        a.callbackNode = c;
      }
    }
    function Gk(a, b) {
      Ak = -1;
      Bk = 0;
      if (0 !== (K & 6)) throw Error(p(327));
      var c = a.callbackNode;
      if (Hk() && a.callbackNode !== c) return null;
      var d = uc(a, a === Q ? Z : 0);
      if (0 === d) return null;
      if (0 !== (d & 30) || 0 !== (d & a.expiredLanes) || b) b = Ik(a, d);
      else {
        b = d;
        var e = K;
        K |= 2;
        var f = Jk();
        if (Q !== a || Z !== b) uk = null, Gj = B() + 500, Kk(a, b);
        do
          try {
            Lk();
            break;
          } catch (h) {
            Mk(a, h);
          }
        while (1);
        $g();
        mk.current = f;
        K = e;
        null !== Y ? b = 0 : (Q = null, Z = 0, b = T);
      }
      if (0 !== b) {
        2 === b && (e = xc(a), 0 !== e && (d = e, b = Nk(a, e)));
        if (1 === b) throw c = pk, Kk(a, 0), Ck(a, d), Dk(a, B()), c;
        if (6 === b) Ck(a, d);
        else {
          e = a.current.alternate;
          if (0 === (d & 30) && !Ok(e) && (b = Ik(a, d), 2 === b && (f = xc(a), 0 !== f && (d = f, b = Nk(a, f))), 1 === b)) throw c = pk, Kk(a, 0), Ck(a, d), Dk(a, B()), c;
          a.finishedWork = e;
          a.finishedLanes = d;
          switch (b) {
            case 0:
            case 1:
              throw Error(p(345));
            case 2:
              Pk(a, tk, uk);
              break;
            case 3:
              Ck(a, d);
              if ((d & 130023424) === d && (b = fk + 500 - B(), 10 < b)) {
                if (0 !== uc(a, 0)) break;
                e = a.suspendedLanes;
                if ((e & d) !== d) {
                  R();
                  a.pingedLanes |= a.suspendedLanes & e;
                  break;
                }
                a.timeoutHandle = Ff(Pk.bind(null, a, tk, uk), b);
                break;
              }
              Pk(a, tk, uk);
              break;
            case 4:
              Ck(a, d);
              if ((d & 4194240) === d) break;
              b = a.eventTimes;
              for (e = -1; 0 < d; ) {
                var g = 31 - oc(d);
                f = 1 << g;
                g = b[g];
                g > e && (e = g);
                d &= ~f;
              }
              d = e;
              d = B() - d;
              d = (120 > d ? 120 : 480 > d ? 480 : 1080 > d ? 1080 : 1920 > d ? 1920 : 3e3 > d ? 3e3 : 4320 > d ? 4320 : 1960 * lk(d / 1960)) - d;
              if (10 < d) {
                a.timeoutHandle = Ff(Pk.bind(null, a, tk, uk), d);
                break;
              }
              Pk(a, tk, uk);
              break;
            case 5:
              Pk(a, tk, uk);
              break;
            default:
              throw Error(p(329));
          }
        }
      }
      Dk(a, B());
      return a.callbackNode === c ? Gk.bind(null, a) : null;
    }
    function Nk(a, b) {
      var c = sk;
      a.current.memoizedState.isDehydrated && (Kk(a, b).flags |= 256);
      a = Ik(a, b);
      2 !== a && (b = tk, tk = c, null !== b && Fj(b));
      return a;
    }
    function Fj(a) {
      null === tk ? tk = a : tk.push.apply(tk, a);
    }
    function Ok(a) {
      for (var b = a; ; ) {
        if (b.flags & 16384) {
          var c = b.updateQueue;
          if (null !== c && (c = c.stores, null !== c)) for (var d = 0; d < c.length; d++) {
            var e = c[d], f = e.getSnapshot;
            e = e.value;
            try {
              if (!He(f(), e)) return false;
            } catch (g) {
              return false;
            }
          }
        }
        c = b.child;
        if (b.subtreeFlags & 16384 && null !== c) c.return = b, b = c;
        else {
          if (b === a) break;
          for (; null === b.sibling; ) {
            if (null === b.return || b.return === a) return true;
            b = b.return;
          }
          b.sibling.return = b.return;
          b = b.sibling;
        }
      }
      return true;
    }
    function Ck(a, b) {
      b &= ~rk;
      b &= ~qk;
      a.suspendedLanes |= b;
      a.pingedLanes &= ~b;
      for (a = a.expirationTimes; 0 < b; ) {
        var c = 31 - oc(b), d = 1 << c;
        a[c] = -1;
        b &= ~d;
      }
    }
    function Ek(a) {
      if (0 !== (K & 6)) throw Error(p(327));
      Hk();
      var b = uc(a, 0);
      if (0 === (b & 1)) return Dk(a, B()), null;
      var c = Ik(a, b);
      if (0 !== a.tag && 2 === c) {
        var d = xc(a);
        0 !== d && (b = d, c = Nk(a, d));
      }
      if (1 === c) throw c = pk, Kk(a, 0), Ck(a, b), Dk(a, B()), c;
      if (6 === c) throw Error(p(345));
      a.finishedWork = a.current.alternate;
      a.finishedLanes = b;
      Pk(a, tk, uk);
      Dk(a, B());
      return null;
    }
    function Qk(a, b) {
      var c = K;
      K |= 1;
      try {
        return a(b);
      } finally {
        K = c, 0 === K && (Gj = B() + 500, fg && jg());
      }
    }
    function Rk(a) {
      null !== wk && 0 === wk.tag && 0 === (K & 6) && Hk();
      var b = K;
      K |= 1;
      var c = ok.transition, d = C;
      try {
        if (ok.transition = null, C = 1, a) return a();
      } finally {
        C = d, ok.transition = c, K = b, 0 === (K & 6) && jg();
      }
    }
    function Hj() {
      fj = ej.current;
      E(ej);
    }
    function Kk(a, b) {
      a.finishedWork = null;
      a.finishedLanes = 0;
      var c = a.timeoutHandle;
      -1 !== c && (a.timeoutHandle = -1, Gf(c));
      if (null !== Y) for (c = Y.return; null !== c; ) {
        var d = c;
        wg(d);
        switch (d.tag) {
          case 1:
            d = d.type.childContextTypes;
            null !== d && void 0 !== d && $f();
            break;
          case 3:
            zh();
            E(Wf);
            E(H);
            Eh();
            break;
          case 5:
            Bh(d);
            break;
          case 4:
            zh();
            break;
          case 13:
            E(L);
            break;
          case 19:
            E(L);
            break;
          case 10:
            ah(d.type._context);
            break;
          case 22:
          case 23:
            Hj();
        }
        c = c.return;
      }
      Q = a;
      Y = a = Pg(a.current, null);
      Z = fj = b;
      T = 0;
      pk = null;
      rk = qk = rh = 0;
      tk = sk = null;
      if (null !== fh) {
        for (b = 0; b < fh.length; b++) if (c = fh[b], d = c.interleaved, null !== d) {
          c.interleaved = null;
          var e = d.next, f = c.pending;
          if (null !== f) {
            var g = f.next;
            f.next = e;
            d.next = g;
          }
          c.pending = d;
        }
        fh = null;
      }
      return a;
    }
    function Mk(a, b) {
      do {
        var c = Y;
        try {
          $g();
          Fh.current = Rh;
          if (Ih) {
            for (var d = M.memoizedState; null !== d; ) {
              var e = d.queue;
              null !== e && (e.pending = null);
              d = d.next;
            }
            Ih = false;
          }
          Hh = 0;
          O = N = M = null;
          Jh = false;
          Kh = 0;
          nk.current = null;
          if (null === c || null === c.return) {
            T = 1;
            pk = b;
            Y = null;
            break;
          }
          a: {
            var f = a, g = c.return, h = c, k = b;
            b = Z;
            h.flags |= 32768;
            if (null !== k && "object" === typeof k && "function" === typeof k.then) {
              var l = k, m = h, q = m.tag;
              if (0 === (m.mode & 1) && (0 === q || 11 === q || 15 === q)) {
                var r = m.alternate;
                r ? (m.updateQueue = r.updateQueue, m.memoizedState = r.memoizedState, m.lanes = r.lanes) : (m.updateQueue = null, m.memoizedState = null);
              }
              var y = Ui(g);
              if (null !== y) {
                y.flags &= -257;
                Vi(y, g, h, f, b);
                y.mode & 1 && Si(f, l, b);
                b = y;
                k = l;
                var n = b.updateQueue;
                if (null === n) {
                  var t = /* @__PURE__ */ new Set();
                  t.add(k);
                  b.updateQueue = t;
                } else n.add(k);
                break a;
              } else {
                if (0 === (b & 1)) {
                  Si(f, l, b);
                  tj();
                  break a;
                }
                k = Error(p(426));
              }
            } else if (I && h.mode & 1) {
              var J = Ui(g);
              if (null !== J) {
                0 === (J.flags & 65536) && (J.flags |= 256);
                Vi(J, g, h, f, b);
                Jg(Ji(k, h));
                break a;
              }
            }
            f = k = Ji(k, h);
            4 !== T && (T = 2);
            null === sk ? sk = [f] : sk.push(f);
            f = g;
            do {
              switch (f.tag) {
                case 3:
                  f.flags |= 65536;
                  b &= -b;
                  f.lanes |= b;
                  var x = Ni(f, k, b);
                  ph(f, x);
                  break a;
                case 1:
                  h = k;
                  var w = f.type, u = f.stateNode;
                  if (0 === (f.flags & 128) && ("function" === typeof w.getDerivedStateFromError || null !== u && "function" === typeof u.componentDidCatch && (null === Ri || !Ri.has(u)))) {
                    f.flags |= 65536;
                    b &= -b;
                    f.lanes |= b;
                    var F = Qi(f, h, b);
                    ph(f, F);
                    break a;
                  }
              }
              f = f.return;
            } while (null !== f);
          }
          Sk(c);
        } catch (na) {
          b = na;
          Y === c && null !== c && (Y = c = c.return);
          continue;
        }
        break;
      } while (1);
    }
    function Jk() {
      var a = mk.current;
      mk.current = Rh;
      return null === a ? Rh : a;
    }
    function tj() {
      if (0 === T || 3 === T || 2 === T) T = 4;
      null === Q || 0 === (rh & 268435455) && 0 === (qk & 268435455) || Ck(Q, Z);
    }
    function Ik(a, b) {
      var c = K;
      K |= 2;
      var d = Jk();
      if (Q !== a || Z !== b) uk = null, Kk(a, b);
      do
        try {
          Tk();
          break;
        } catch (e) {
          Mk(a, e);
        }
      while (1);
      $g();
      K = c;
      mk.current = d;
      if (null !== Y) throw Error(p(261));
      Q = null;
      Z = 0;
      return T;
    }
    function Tk() {
      for (; null !== Y; ) Uk(Y);
    }
    function Lk() {
      for (; null !== Y && !cc(); ) Uk(Y);
    }
    function Uk(a) {
      var b = Vk(a.alternate, a, fj);
      a.memoizedProps = a.pendingProps;
      null === b ? Sk(a) : Y = b;
      nk.current = null;
    }
    function Sk(a) {
      var b = a;
      do {
        var c = b.alternate;
        a = b.return;
        if (0 === (b.flags & 32768)) {
          if (c = Ej(c, b, fj), null !== c) {
            Y = c;
            return;
          }
        } else {
          c = Ij(c, b);
          if (null !== c) {
            c.flags &= 32767;
            Y = c;
            return;
          }
          if (null !== a) a.flags |= 32768, a.subtreeFlags = 0, a.deletions = null;
          else {
            T = 6;
            Y = null;
            return;
          }
        }
        b = b.sibling;
        if (null !== b) {
          Y = b;
          return;
        }
        Y = b = a;
      } while (null !== b);
      0 === T && (T = 5);
    }
    function Pk(a, b, c) {
      var d = C, e = ok.transition;
      try {
        ok.transition = null, C = 1, Wk(a, b, c, d);
      } finally {
        ok.transition = e, C = d;
      }
      return null;
    }
    function Wk(a, b, c, d) {
      do
        Hk();
      while (null !== wk);
      if (0 !== (K & 6)) throw Error(p(327));
      c = a.finishedWork;
      var e = a.finishedLanes;
      if (null === c) return null;
      a.finishedWork = null;
      a.finishedLanes = 0;
      if (c === a.current) throw Error(p(177));
      a.callbackNode = null;
      a.callbackPriority = 0;
      var f = c.lanes | c.childLanes;
      Bc(a, f);
      a === Q && (Y = Q = null, Z = 0);
      0 === (c.subtreeFlags & 2064) && 0 === (c.flags & 2064) || vk || (vk = true, Fk(hc, function() {
        Hk();
        return null;
      }));
      f = 0 !== (c.flags & 15990);
      if (0 !== (c.subtreeFlags & 15990) || f) {
        f = ok.transition;
        ok.transition = null;
        var g = C;
        C = 1;
        var h = K;
        K |= 4;
        nk.current = null;
        Oj(a, c);
        dk(c, a);
        Oe(Df);
        dd = !!Cf;
        Df = Cf = null;
        a.current = c;
        hk(c);
        dc();
        K = h;
        C = g;
        ok.transition = f;
      } else a.current = c;
      vk && (vk = false, wk = a, xk = e);
      f = a.pendingLanes;
      0 === f && (Ri = null);
      mc(c.stateNode);
      Dk(a, B());
      if (null !== b) for (d = a.onRecoverableError, c = 0; c < b.length; c++) e = b[c], d(e.value, { componentStack: e.stack, digest: e.digest });
      if (Oi) throw Oi = false, a = Pi, Pi = null, a;
      0 !== (xk & 1) && 0 !== a.tag && Hk();
      f = a.pendingLanes;
      0 !== (f & 1) ? a === zk ? yk++ : (yk = 0, zk = a) : yk = 0;
      jg();
      return null;
    }
    function Hk() {
      if (null !== wk) {
        var a = Dc(xk), b = ok.transition, c = C;
        try {
          ok.transition = null;
          C = 16 > a ? 16 : a;
          if (null === wk) var d = false;
          else {
            a = wk;
            wk = null;
            xk = 0;
            if (0 !== (K & 6)) throw Error(p(331));
            var e = K;
            K |= 4;
            for (V = a.current; null !== V; ) {
              var f = V, g = f.child;
              if (0 !== (V.flags & 16)) {
                var h = f.deletions;
                if (null !== h) {
                  for (var k = 0; k < h.length; k++) {
                    var l = h[k];
                    for (V = l; null !== V; ) {
                      var m = V;
                      switch (m.tag) {
                        case 0:
                        case 11:
                        case 15:
                          Pj(8, m, f);
                      }
                      var q = m.child;
                      if (null !== q) q.return = m, V = q;
                      else for (; null !== V; ) {
                        m = V;
                        var r = m.sibling, y = m.return;
                        Sj(m);
                        if (m === l) {
                          V = null;
                          break;
                        }
                        if (null !== r) {
                          r.return = y;
                          V = r;
                          break;
                        }
                        V = y;
                      }
                    }
                  }
                  var n = f.alternate;
                  if (null !== n) {
                    var t = n.child;
                    if (null !== t) {
                      n.child = null;
                      do {
                        var J = t.sibling;
                        t.sibling = null;
                        t = J;
                      } while (null !== t);
                    }
                  }
                  V = f;
                }
              }
              if (0 !== (f.subtreeFlags & 2064) && null !== g) g.return = f, V = g;
              else b: for (; null !== V; ) {
                f = V;
                if (0 !== (f.flags & 2048)) switch (f.tag) {
                  case 0:
                  case 11:
                  case 15:
                    Pj(9, f, f.return);
                }
                var x = f.sibling;
                if (null !== x) {
                  x.return = f.return;
                  V = x;
                  break b;
                }
                V = f.return;
              }
            }
            var w = a.current;
            for (V = w; null !== V; ) {
              g = V;
              var u = g.child;
              if (0 !== (g.subtreeFlags & 2064) && null !== u) u.return = g, V = u;
              else b: for (g = w; null !== V; ) {
                h = V;
                if (0 !== (h.flags & 2048)) try {
                  switch (h.tag) {
                    case 0:
                    case 11:
                    case 15:
                      Qj(9, h);
                  }
                } catch (na) {
                  W(h, h.return, na);
                }
                if (h === g) {
                  V = null;
                  break b;
                }
                var F = h.sibling;
                if (null !== F) {
                  F.return = h.return;
                  V = F;
                  break b;
                }
                V = h.return;
              }
            }
            K = e;
            jg();
            if (lc && "function" === typeof lc.onPostCommitFiberRoot) try {
              lc.onPostCommitFiberRoot(kc, a);
            } catch (na) {
            }
            d = true;
          }
          return d;
        } finally {
          C = c, ok.transition = b;
        }
      }
      return false;
    }
    function Xk(a, b, c) {
      b = Ji(c, b);
      b = Ni(a, b, 1);
      a = nh(a, b, 1);
      b = R();
      null !== a && (Ac(a, 1, b), Dk(a, b));
    }
    function W(a, b, c) {
      if (3 === a.tag) Xk(a, a, c);
      else for (; null !== b; ) {
        if (3 === b.tag) {
          Xk(b, a, c);
          break;
        } else if (1 === b.tag) {
          var d = b.stateNode;
          if ("function" === typeof b.type.getDerivedStateFromError || "function" === typeof d.componentDidCatch && (null === Ri || !Ri.has(d))) {
            a = Ji(c, a);
            a = Qi(b, a, 1);
            b = nh(b, a, 1);
            a = R();
            null !== b && (Ac(b, 1, a), Dk(b, a));
            break;
          }
        }
        b = b.return;
      }
    }
    function Ti(a, b, c) {
      var d = a.pingCache;
      null !== d && d.delete(b);
      b = R();
      a.pingedLanes |= a.suspendedLanes & c;
      Q === a && (Z & c) === c && (4 === T || 3 === T && (Z & 130023424) === Z && 500 > B() - fk ? Kk(a, 0) : rk |= c);
      Dk(a, b);
    }
    function Yk(a, b) {
      0 === b && (0 === (a.mode & 1) ? b = 1 : (b = sc, sc <<= 1, 0 === (sc & 130023424) && (sc = 4194304)));
      var c = R();
      a = ih(a, b);
      null !== a && (Ac(a, b, c), Dk(a, c));
    }
    function uj(a) {
      var b = a.memoizedState, c = 0;
      null !== b && (c = b.retryLane);
      Yk(a, c);
    }
    function bk(a, b) {
      var c = 0;
      switch (a.tag) {
        case 13:
          var d = a.stateNode;
          var e = a.memoizedState;
          null !== e && (c = e.retryLane);
          break;
        case 19:
          d = a.stateNode;
          break;
        default:
          throw Error(p(314));
      }
      null !== d && d.delete(b);
      Yk(a, c);
    }
    var Vk;
    Vk = function(a, b, c) {
      if (null !== a) if (a.memoizedProps !== b.pendingProps || Wf.current) dh = true;
      else {
        if (0 === (a.lanes & c) && 0 === (b.flags & 128)) return dh = false, yj(a, b, c);
        dh = 0 !== (a.flags & 131072) ? true : false;
      }
      else dh = false, I && 0 !== (b.flags & 1048576) && ug(b, ng, b.index);
      b.lanes = 0;
      switch (b.tag) {
        case 2:
          var d = b.type;
          ij(a, b);
          a = b.pendingProps;
          var e = Yf(b, H.current);
          ch(b, c);
          e = Nh(null, b, d, a, e, c);
          var f = Sh();
          b.flags |= 1;
          "object" === typeof e && null !== e && "function" === typeof e.render && void 0 === e.$$typeof ? (b.tag = 1, b.memoizedState = null, b.updateQueue = null, Zf(d) ? (f = true, cg(b)) : f = false, b.memoizedState = null !== e.state && void 0 !== e.state ? e.state : null, kh(b), e.updater = Ei, b.stateNode = e, e._reactInternals = b, Ii(b, d, a, c), b = jj(null, b, d, true, f, c)) : (b.tag = 0, I && f && vg(b), Xi(null, b, e, c), b = b.child);
          return b;
        case 16:
          d = b.elementType;
          a: {
            ij(a, b);
            a = b.pendingProps;
            e = d._init;
            d = e(d._payload);
            b.type = d;
            e = b.tag = Zk(d);
            a = Ci(d, a);
            switch (e) {
              case 0:
                b = cj(null, b, d, a, c);
                break a;
              case 1:
                b = hj(null, b, d, a, c);
                break a;
              case 11:
                b = Yi(null, b, d, a, c);
                break a;
              case 14:
                b = $i(null, b, d, Ci(d.type, a), c);
                break a;
            }
            throw Error(p(
              306,
              d,
              ""
            ));
          }
          return b;
        case 0:
          return d = b.type, e = b.pendingProps, e = b.elementType === d ? e : Ci(d, e), cj(a, b, d, e, c);
        case 1:
          return d = b.type, e = b.pendingProps, e = b.elementType === d ? e : Ci(d, e), hj(a, b, d, e, c);
        case 3:
          a: {
            kj(b);
            if (null === a) throw Error(p(387));
            d = b.pendingProps;
            f = b.memoizedState;
            e = f.element;
            lh(a, b);
            qh(b, d, null, c);
            var g = b.memoizedState;
            d = g.element;
            if (f.isDehydrated) if (f = { element: d, isDehydrated: false, cache: g.cache, pendingSuspenseBoundaries: g.pendingSuspenseBoundaries, transitions: g.transitions }, b.updateQueue.baseState = f, b.memoizedState = f, b.flags & 256) {
              e = Ji(Error(p(423)), b);
              b = lj(a, b, d, c, e);
              break a;
            } else if (d !== e) {
              e = Ji(Error(p(424)), b);
              b = lj(a, b, d, c, e);
              break a;
            } else for (yg = Lf(b.stateNode.containerInfo.firstChild), xg = b, I = true, zg = null, c = Vg(b, null, d, c), b.child = c; c; ) c.flags = c.flags & -3 | 4096, c = c.sibling;
            else {
              Ig();
              if (d === e) {
                b = Zi(a, b, c);
                break a;
              }
              Xi(a, b, d, c);
            }
            b = b.child;
          }
          return b;
        case 5:
          return Ah(b), null === a && Eg(b), d = b.type, e = b.pendingProps, f = null !== a ? a.memoizedProps : null, g = e.children, Ef(d, e) ? g = null : null !== f && Ef(d, f) && (b.flags |= 32), gj(a, b), Xi(a, b, g, c), b.child;
        case 6:
          return null === a && Eg(b), null;
        case 13:
          return oj(a, b, c);
        case 4:
          return yh(b, b.stateNode.containerInfo), d = b.pendingProps, null === a ? b.child = Ug(b, null, d, c) : Xi(a, b, d, c), b.child;
        case 11:
          return d = b.type, e = b.pendingProps, e = b.elementType === d ? e : Ci(d, e), Yi(a, b, d, e, c);
        case 7:
          return Xi(a, b, b.pendingProps, c), b.child;
        case 8:
          return Xi(a, b, b.pendingProps.children, c), b.child;
        case 12:
          return Xi(a, b, b.pendingProps.children, c), b.child;
        case 10:
          a: {
            d = b.type._context;
            e = b.pendingProps;
            f = b.memoizedProps;
            g = e.value;
            G(Wg, d._currentValue);
            d._currentValue = g;
            if (null !== f) if (He(f.value, g)) {
              if (f.children === e.children && !Wf.current) {
                b = Zi(a, b, c);
                break a;
              }
            } else for (f = b.child, null !== f && (f.return = b); null !== f; ) {
              var h = f.dependencies;
              if (null !== h) {
                g = f.child;
                for (var k = h.firstContext; null !== k; ) {
                  if (k.context === d) {
                    if (1 === f.tag) {
                      k = mh(-1, c & -c);
                      k.tag = 2;
                      var l = f.updateQueue;
                      if (null !== l) {
                        l = l.shared;
                        var m = l.pending;
                        null === m ? k.next = k : (k.next = m.next, m.next = k);
                        l.pending = k;
                      }
                    }
                    f.lanes |= c;
                    k = f.alternate;
                    null !== k && (k.lanes |= c);
                    bh(
                      f.return,
                      c,
                      b
                    );
                    h.lanes |= c;
                    break;
                  }
                  k = k.next;
                }
              } else if (10 === f.tag) g = f.type === b.type ? null : f.child;
              else if (18 === f.tag) {
                g = f.return;
                if (null === g) throw Error(p(341));
                g.lanes |= c;
                h = g.alternate;
                null !== h && (h.lanes |= c);
                bh(g, c, b);
                g = f.sibling;
              } else g = f.child;
              if (null !== g) g.return = f;
              else for (g = f; null !== g; ) {
                if (g === b) {
                  g = null;
                  break;
                }
                f = g.sibling;
                if (null !== f) {
                  f.return = g.return;
                  g = f;
                  break;
                }
                g = g.return;
              }
              f = g;
            }
            Xi(a, b, e.children, c);
            b = b.child;
          }
          return b;
        case 9:
          return e = b.type, d = b.pendingProps.children, ch(b, c), e = eh(e), d = d(e), b.flags |= 1, Xi(a, b, d, c), b.child;
        case 14:
          return d = b.type, e = Ci(d, b.pendingProps), e = Ci(d.type, e), $i(a, b, d, e, c);
        case 15:
          return bj(a, b, b.type, b.pendingProps, c);
        case 17:
          return d = b.type, e = b.pendingProps, e = b.elementType === d ? e : Ci(d, e), ij(a, b), b.tag = 1, Zf(d) ? (a = true, cg(b)) : a = false, ch(b, c), Gi(b, d, e), Ii(b, d, e, c), jj(null, b, d, true, a, c);
        case 19:
          return xj(a, b, c);
        case 22:
          return dj(a, b, c);
      }
      throw Error(p(156, b.tag));
    };
    function Fk(a, b) {
      return ac(a, b);
    }
    function $k(a, b, c, d) {
      this.tag = a;
      this.key = c;
      this.sibling = this.child = this.return = this.stateNode = this.type = this.elementType = null;
      this.index = 0;
      this.ref = null;
      this.pendingProps = b;
      this.dependencies = this.memoizedState = this.updateQueue = this.memoizedProps = null;
      this.mode = d;
      this.subtreeFlags = this.flags = 0;
      this.deletions = null;
      this.childLanes = this.lanes = 0;
      this.alternate = null;
    }
    function Bg(a, b, c, d) {
      return new $k(a, b, c, d);
    }
    function aj(a) {
      a = a.prototype;
      return !(!a || !a.isReactComponent);
    }
    function Zk(a) {
      if ("function" === typeof a) return aj(a) ? 1 : 0;
      if (void 0 !== a && null !== a) {
        a = a.$$typeof;
        if (a === Da) return 11;
        if (a === Ga) return 14;
      }
      return 2;
    }
    function Pg(a, b) {
      var c = a.alternate;
      null === c ? (c = Bg(a.tag, b, a.key, a.mode), c.elementType = a.elementType, c.type = a.type, c.stateNode = a.stateNode, c.alternate = a, a.alternate = c) : (c.pendingProps = b, c.type = a.type, c.flags = 0, c.subtreeFlags = 0, c.deletions = null);
      c.flags = a.flags & 14680064;
      c.childLanes = a.childLanes;
      c.lanes = a.lanes;
      c.child = a.child;
      c.memoizedProps = a.memoizedProps;
      c.memoizedState = a.memoizedState;
      c.updateQueue = a.updateQueue;
      b = a.dependencies;
      c.dependencies = null === b ? null : { lanes: b.lanes, firstContext: b.firstContext };
      c.sibling = a.sibling;
      c.index = a.index;
      c.ref = a.ref;
      return c;
    }
    function Rg(a, b, c, d, e, f) {
      var g = 2;
      d = a;
      if ("function" === typeof a) aj(a) && (g = 1);
      else if ("string" === typeof a) g = 5;
      else a: switch (a) {
        case ya:
          return Tg(c.children, e, f, b);
        case za:
          g = 8;
          e |= 8;
          break;
        case Aa:
          return a = Bg(12, c, b, e | 2), a.elementType = Aa, a.lanes = f, a;
        case Ea:
          return a = Bg(13, c, b, e), a.elementType = Ea, a.lanes = f, a;
        case Fa:
          return a = Bg(19, c, b, e), a.elementType = Fa, a.lanes = f, a;
        case Ia:
          return pj(c, e, f, b);
        default:
          if ("object" === typeof a && null !== a) switch (a.$$typeof) {
            case Ba:
              g = 10;
              break a;
            case Ca:
              g = 9;
              break a;
            case Da:
              g = 11;
              break a;
            case Ga:
              g = 14;
              break a;
            case Ha:
              g = 16;
              d = null;
              break a;
          }
          throw Error(p(130, null == a ? a : typeof a, ""));
      }
      b = Bg(g, c, b, e);
      b.elementType = a;
      b.type = d;
      b.lanes = f;
      return b;
    }
    function Tg(a, b, c, d) {
      a = Bg(7, a, d, b);
      a.lanes = c;
      return a;
    }
    function pj(a, b, c, d) {
      a = Bg(22, a, d, b);
      a.elementType = Ia;
      a.lanes = c;
      a.stateNode = { isHidden: false };
      return a;
    }
    function Qg(a, b, c) {
      a = Bg(6, a, null, b);
      a.lanes = c;
      return a;
    }
    function Sg(a, b, c) {
      b = Bg(4, null !== a.children ? a.children : [], a.key, b);
      b.lanes = c;
      b.stateNode = { containerInfo: a.containerInfo, pendingChildren: null, implementation: a.implementation };
      return b;
    }
    function al(a, b, c, d, e) {
      this.tag = b;
      this.containerInfo = a;
      this.finishedWork = this.pingCache = this.current = this.pendingChildren = null;
      this.timeoutHandle = -1;
      this.callbackNode = this.pendingContext = this.context = null;
      this.callbackPriority = 0;
      this.eventTimes = zc(0);
      this.expirationTimes = zc(-1);
      this.entangledLanes = this.finishedLanes = this.mutableReadLanes = this.expiredLanes = this.pingedLanes = this.suspendedLanes = this.pendingLanes = 0;
      this.entanglements = zc(0);
      this.identifierPrefix = d;
      this.onRecoverableError = e;
      this.mutableSourceEagerHydrationData = null;
    }
    function bl(a, b, c, d, e, f, g, h, k) {
      a = new al(a, b, c, h, k);
      1 === b ? (b = 1, true === f && (b |= 8)) : b = 0;
      f = Bg(3, null, null, b);
      a.current = f;
      f.stateNode = a;
      f.memoizedState = { element: d, isDehydrated: c, cache: null, transitions: null, pendingSuspenseBoundaries: null };
      kh(f);
      return a;
    }
    function cl(a, b, c) {
      var d = 3 < arguments.length && void 0 !== arguments[3] ? arguments[3] : null;
      return { $$typeof: wa, key: null == d ? null : "" + d, children: a, containerInfo: b, implementation: c };
    }
    function dl(a) {
      if (!a) return Vf;
      a = a._reactInternals;
      a: {
        if (Vb(a) !== a || 1 !== a.tag) throw Error(p(170));
        var b = a;
        do {
          switch (b.tag) {
            case 3:
              b = b.stateNode.context;
              break a;
            case 1:
              if (Zf(b.type)) {
                b = b.stateNode.__reactInternalMemoizedMergedChildContext;
                break a;
              }
          }
          b = b.return;
        } while (null !== b);
        throw Error(p(171));
      }
      if (1 === a.tag) {
        var c = a.type;
        if (Zf(c)) return bg(a, c, b);
      }
      return b;
    }
    function el(a, b, c, d, e, f, g, h, k) {
      a = bl(c, d, true, a, e, f, g, h, k);
      a.context = dl(null);
      c = a.current;
      d = R();
      e = yi(c);
      f = mh(d, e);
      f.callback = void 0 !== b && null !== b ? b : null;
      nh(c, f, e);
      a.current.lanes = e;
      Ac(a, e, d);
      Dk(a, d);
      return a;
    }
    function fl(a, b, c, d) {
      var e = b.current, f = R(), g = yi(e);
      c = dl(c);
      null === b.context ? b.context = c : b.pendingContext = c;
      b = mh(f, g);
      b.payload = { element: a };
      d = void 0 === d ? null : d;
      null !== d && (b.callback = d);
      a = nh(e, b, g);
      null !== a && (gi(a, e, g, f), oh(a, e, g));
      return g;
    }
    function gl(a) {
      a = a.current;
      if (!a.child) return null;
      switch (a.child.tag) {
        case 5:
          return a.child.stateNode;
        default:
          return a.child.stateNode;
      }
    }
    function hl(a, b) {
      a = a.memoizedState;
      if (null !== a && null !== a.dehydrated) {
        var c = a.retryLane;
        a.retryLane = 0 !== c && c < b ? c : b;
      }
    }
    function il(a, b) {
      hl(a, b);
      (a = a.alternate) && hl(a, b);
    }
    function jl() {
      return null;
    }
    var kl = "function" === typeof reportError ? reportError : function(a) {
      console.error(a);
    };
    function ll(a) {
      this._internalRoot = a;
    }
    ml.prototype.render = ll.prototype.render = function(a) {
      var b = this._internalRoot;
      if (null === b) throw Error(p(409));
      fl(a, b, null, null);
    };
    ml.prototype.unmount = ll.prototype.unmount = function() {
      var a = this._internalRoot;
      if (null !== a) {
        this._internalRoot = null;
        var b = a.containerInfo;
        Rk(function() {
          fl(null, a, null, null);
        });
        b[uf] = null;
      }
    };
    function ml(a) {
      this._internalRoot = a;
    }
    ml.prototype.unstable_scheduleHydration = function(a) {
      if (a) {
        var b = Hc();
        a = { blockedOn: null, target: a, priority: b };
        for (var c = 0; c < Qc.length && 0 !== b && b < Qc[c].priority; c++) ;
        Qc.splice(c, 0, a);
        0 === c && Vc(a);
      }
    };
    function nl(a) {
      return !(!a || 1 !== a.nodeType && 9 !== a.nodeType && 11 !== a.nodeType);
    }
    function ol(a) {
      return !(!a || 1 !== a.nodeType && 9 !== a.nodeType && 11 !== a.nodeType && (8 !== a.nodeType || " react-mount-point-unstable " !== a.nodeValue));
    }
    function pl() {
    }
    function ql(a, b, c, d, e) {
      if (e) {
        if ("function" === typeof d) {
          var f = d;
          d = function() {
            var a2 = gl(g);
            f.call(a2);
          };
        }
        var g = el(b, d, a, 0, null, false, false, "", pl);
        a._reactRootContainer = g;
        a[uf] = g.current;
        sf(8 === a.nodeType ? a.parentNode : a);
        Rk();
        return g;
      }
      for (; e = a.lastChild; ) a.removeChild(e);
      if ("function" === typeof d) {
        var h = d;
        d = function() {
          var a2 = gl(k);
          h.call(a2);
        };
      }
      var k = bl(a, 0, false, null, null, false, false, "", pl);
      a._reactRootContainer = k;
      a[uf] = k.current;
      sf(8 === a.nodeType ? a.parentNode : a);
      Rk(function() {
        fl(b, k, c, d);
      });
      return k;
    }
    function rl(a, b, c, d, e) {
      var f = c._reactRootContainer;
      if (f) {
        var g = f;
        if ("function" === typeof e) {
          var h = e;
          e = function() {
            var a2 = gl(g);
            h.call(a2);
          };
        }
        fl(b, g, a, e);
      } else g = ql(c, b, a, e, d);
      return gl(g);
    }
    Ec = function(a) {
      switch (a.tag) {
        case 3:
          var b = a.stateNode;
          if (b.current.memoizedState.isDehydrated) {
            var c = tc(b.pendingLanes);
            0 !== c && (Cc(b, c | 1), Dk(b, B()), 0 === (K & 6) && (Gj = B() + 500, jg()));
          }
          break;
        case 13:
          Rk(function() {
            var b2 = ih(a, 1);
            if (null !== b2) {
              var c2 = R();
              gi(b2, a, 1, c2);
            }
          }), il(a, 1);
      }
    };
    Fc = function(a) {
      if (13 === a.tag) {
        var b = ih(a, 134217728);
        if (null !== b) {
          var c = R();
          gi(b, a, 134217728, c);
        }
        il(a, 134217728);
      }
    };
    Gc = function(a) {
      if (13 === a.tag) {
        var b = yi(a), c = ih(a, b);
        if (null !== c) {
          var d = R();
          gi(c, a, b, d);
        }
        il(a, b);
      }
    };
    Hc = function() {
      return C;
    };
    Ic = function(a, b) {
      var c = C;
      try {
        return C = a, b();
      } finally {
        C = c;
      }
    };
    yb = function(a, b, c) {
      switch (b) {
        case "input":
          bb(a, c);
          b = c.name;
          if ("radio" === c.type && null != b) {
            for (c = a; c.parentNode; ) c = c.parentNode;
            c = c.querySelectorAll("input[name=" + JSON.stringify("" + b) + '][type="radio"]');
            for (b = 0; b < c.length; b++) {
              var d = c[b];
              if (d !== a && d.form === a.form) {
                var e = Db(d);
                if (!e) throw Error(p(90));
                Wa(d);
                bb(d, e);
              }
            }
          }
          break;
        case "textarea":
          ib(a, c);
          break;
        case "select":
          b = c.value, null != b && fb(a, !!c.multiple, b, false);
      }
    };
    Gb = Qk;
    Hb = Rk;
    var sl = { usingClientEntryPoint: false, Events: [Cb, ue, Db, Eb, Fb, Qk] }, tl = { findFiberByHostInstance: Wc, bundleType: 0, version: "18.3.1", rendererPackageName: "react-dom" };
    var ul = { bundleType: tl.bundleType, version: tl.version, rendererPackageName: tl.rendererPackageName, rendererConfig: tl.rendererConfig, overrideHookState: null, overrideHookStateDeletePath: null, overrideHookStateRenamePath: null, overrideProps: null, overridePropsDeletePath: null, overridePropsRenamePath: null, setErrorHandler: null, setSuspenseHandler: null, scheduleUpdate: null, currentDispatcherRef: ua.ReactCurrentDispatcher, findHostInstanceByFiber: function(a) {
      a = Zb(a);
      return null === a ? null : a.stateNode;
    }, findFiberByHostInstance: tl.findFiberByHostInstance || jl, findHostInstancesForRefresh: null, scheduleRefresh: null, scheduleRoot: null, setRefreshHandler: null, getCurrentFiber: null, reconcilerVersion: "18.3.1-next-f1338f8080-20240426" };
    if ("undefined" !== typeof __REACT_DEVTOOLS_GLOBAL_HOOK__) {
      var vl = __REACT_DEVTOOLS_GLOBAL_HOOK__;
      if (!vl.isDisabled && vl.supportsFiber) try {
        kc = vl.inject(ul), lc = vl;
      } catch (a) {
      }
    }
    reactDom_production_min.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED = sl;
    reactDom_production_min.createPortal = function(a, b) {
      var c = 2 < arguments.length && void 0 !== arguments[2] ? arguments[2] : null;
      if (!nl(b)) throw Error(p(200));
      return cl(a, b, null, c);
    };
    reactDom_production_min.createRoot = function(a, b) {
      if (!nl(a)) throw Error(p(299));
      var c = false, d = "", e = kl;
      null !== b && void 0 !== b && (true === b.unstable_strictMode && (c = true), void 0 !== b.identifierPrefix && (d = b.identifierPrefix), void 0 !== b.onRecoverableError && (e = b.onRecoverableError));
      b = bl(a, 1, false, null, null, c, false, d, e);
      a[uf] = b.current;
      sf(8 === a.nodeType ? a.parentNode : a);
      return new ll(b);
    };
    reactDom_production_min.findDOMNode = function(a) {
      if (null == a) return null;
      if (1 === a.nodeType) return a;
      var b = a._reactInternals;
      if (void 0 === b) {
        if ("function" === typeof a.render) throw Error(p(188));
        a = Object.keys(a).join(",");
        throw Error(p(268, a));
      }
      a = Zb(b);
      a = null === a ? null : a.stateNode;
      return a;
    };
    reactDom_production_min.flushSync = function(a) {
      return Rk(a);
    };
    reactDom_production_min.hydrate = function(a, b, c) {
      if (!ol(b)) throw Error(p(200));
      return rl(null, a, b, true, c);
    };
    reactDom_production_min.hydrateRoot = function(a, b, c) {
      if (!nl(a)) throw Error(p(405));
      var d = null != c && c.hydratedSources || null, e = false, f = "", g = kl;
      null !== c && void 0 !== c && (true === c.unstable_strictMode && (e = true), void 0 !== c.identifierPrefix && (f = c.identifierPrefix), void 0 !== c.onRecoverableError && (g = c.onRecoverableError));
      b = el(b, null, a, 1, null != c ? c : null, e, false, f, g);
      a[uf] = b.current;
      sf(a);
      if (d) for (a = 0; a < d.length; a++) c = d[a], e = c._getVersion, e = e(c._source), null == b.mutableSourceEagerHydrationData ? b.mutableSourceEagerHydrationData = [c, e] : b.mutableSourceEagerHydrationData.push(
        c,
        e
      );
      return new ml(b);
    };
    reactDom_production_min.render = function(a, b, c) {
      if (!ol(b)) throw Error(p(200));
      return rl(null, a, b, false, c);
    };
    reactDom_production_min.unmountComponentAtNode = function(a) {
      if (!ol(a)) throw Error(p(40));
      return a._reactRootContainer ? (Rk(function() {
        rl(null, null, a, false, function() {
          a._reactRootContainer = null;
          a[uf] = null;
        });
      }), true) : false;
    };
    reactDom_production_min.unstable_batchedUpdates = Qk;
    reactDom_production_min.unstable_renderSubtreeIntoContainer = function(a, b, c, d) {
      if (!ol(c)) throw Error(p(200));
      if (null == a || void 0 === a._reactInternals) throw Error(p(38));
      return rl(a, b, c, false, d);
    };
    reactDom_production_min.version = "18.3.1-next-f1338f8080-20240426";
    return reactDom_production_min;
  }
  var hasRequiredReactDom;
  function requireReactDom() {
    if (hasRequiredReactDom) return reactDom.exports;
    hasRequiredReactDom = 1;
    function checkDCE() {
      if (typeof __REACT_DEVTOOLS_GLOBAL_HOOK__ === "undefined" || typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.checkDCE !== "function") {
        return;
      }
      try {
        __REACT_DEVTOOLS_GLOBAL_HOOK__.checkDCE(checkDCE);
      } catch (err) {
        console.error(err);
      }
    }
    {
      checkDCE();
      reactDom.exports = requireReactDom_production_min();
    }
    return reactDom.exports;
  }
  var hasRequiredClient;
  function requireClient() {
    if (hasRequiredClient) return client;
    hasRequiredClient = 1;
    var m = requireReactDom();
    {
      client.createRoot = m.createRoot;
      client.hydrateRoot = m.hydrateRoot;
    }
    return client;
  }
  var clientExports = requireClient();
  const ReactDOM = /* @__PURE__ */ getDefaultExportFromCjs(clientExports);
  const makeApiCall = async (path, options = {}) => {
    if (!window.acaData) {
      console.error("ACA Error: window.acaData is not defined. Plugin scripts may not be loaded correctly.");
      throw new Error("WordPress data not available");
    }
    const defaultHeaders = {
      "Content-Type": "application/json",
      "X-WP-Nonce": window.acaData.nonce
    };
    const mergedOptions = { ...options, headers: { ...defaultHeaders, ...options.headers } };
    const response = await fetch(`${window.acaData.api_url}${path}`, {
      ...mergedOptions
    });
    if (!response.ok) {
      let errorMessage = `HTTP error! status: ${response.status}`;
      try {
        const contentType = response.headers.get("content-type");
        if (contentType && contentType.includes("application/json")) {
          const errorData = await response.json();
          errorMessage = errorData.message || errorMessage;
        } else {
          const htmlText = await response.text();
          if (htmlText.includes("critical error")) {
            errorMessage = "WordPress critical error occurred. Check server logs.";
          } else {
            errorMessage = `Server returned non-JSON response: ${response.status}`;
          }
        }
      } catch (parseError) {
        errorMessage = `Failed to parse error response: ${response.status}`;
      }
      throw new Error(errorMessage);
    }
    return response.json();
  };
  const settingsApi = {
    get: () => makeApiCall("settings"),
    save: (settings) => makeApiCall("settings", {
      method: "POST",
      body: JSON.stringify(settings)
    }),
    update: (settings) => makeApiCall("settings", {
      method: "POST",
      body: JSON.stringify(settings)
    })
  };
  const styleGuideApi = {
    get: () => makeApiCall("style-guide"),
    analyze: () => makeApiCall("style-guide/analyze", { method: "POST" }),
    save: (styleGuide) => makeApiCall("style-guide", {
      method: "POST",
      body: JSON.stringify(styleGuide)
    }),
    update: (styleGuide) => makeApiCall("style-guide", {
      method: "POST",
      body: JSON.stringify(styleGuide)
    })
  };
  const ideasApi = {
    get: () => makeApiCall("ideas"),
    generate: (count = 5) => makeApiCall("ideas/generate", {
      method: "POST",
      body: JSON.stringify({ count })
    }),
    generateSimilar: (ideaId) => makeApiCall("ideas/similar", {
      method: "POST",
      body: JSON.stringify({ ideaId })
    }),
    create: (idea) => makeApiCall("ideas", {
      method: "POST",
      body: JSON.stringify(idea)
    }),
    update: (id, updates) => makeApiCall(`ideas/${id}`, {
      method: "PUT",
      body: JSON.stringify(updates)
    }),
    delete: (id) => makeApiCall(`ideas/${id}`, { method: "DELETE" }),
    restore: (id) => makeApiCall(`ideas/${id}/restore`, { method: "POST" }),
    permanentDelete: (id) => makeApiCall(`ideas/${id}/permanent-delete`, { method: "DELETE" })
  };
  const draftsApi = {
    get: () => makeApiCall("drafts"),
    createFromIdea: (ideaId) => makeApiCall("drafts/create", {
      method: "POST",
      body: JSON.stringify({ ideaId })
    }),
    update: (id, updates) => makeApiCall(`drafts/${id}`, {
      method: "PUT",
      body: JSON.stringify(updates)
    }),
    schedule: (id, scheduledDate) => makeApiCall(`drafts/${id}/schedule`, {
      method: "POST",
      body: JSON.stringify({ scheduledDate })
    })
  };
  const publishedApi = {
    get: () => makeApiCall("published"),
    publish: (draftId) => makeApiCall(`drafts/${draftId}/publish`, {
      method: "POST"
    }),
    updateDate: (postId, newDate, shouldConvertToDraft = false) => makeApiCall(`published/${postId}/update-date`, {
      method: "POST",
      body: JSON.stringify({ newDate, shouldConvertToDraft })
    })
  };
  const activityApi = {
    get: () => makeApiCall("activity-logs"),
    create: (log) => makeApiCall("activity-logs", {
      method: "POST",
      body: JSON.stringify(log)
    })
  };
  const licenseApi = {
    verify: async (licenseKey) => {
      try {
        const result = await makeApiCall("license/verify", {
          method: "POST",
          body: JSON.stringify({ license_key: licenseKey })
        });
        console.log("License verification result:", result);
        return result;
      } catch (error) {
        console.error("License API error:", error);
        throw error;
      }
    },
    deactivate: async () => {
      try {
        const result = await makeApiCall("license/deactivate", {
          method: "POST"
        });
        console.log("License deactivation result:", result);
        return result;
      } catch (error) {
        console.error("License deactivation error:", error);
        throw error;
      }
    },
    getStatus: () => makeApiCall("license/status")
  };
  const contentFreshnessApi = {
    analyzeAll: (limit) => makeApiCall("content-freshness/analyze", {
      method: "POST",
      body: JSON.stringify({ limit })
    }),
    analyzeSingle: (postId) => makeApiCall(`content-freshness/analyze/${postId}`, {
      method: "POST"
    }),
    updateContent: (postId, updateType) => makeApiCall(`content-freshness/update/${postId}`, {
      method: "POST",
      body: JSON.stringify({ update_type: updateType })
    }),
    getSettings: () => makeApiCall("content-freshness/settings"),
    saveSettings: (settings) => makeApiCall("content-freshness/settings", {
      method: "POST",
      body: JSON.stringify(settings)
    }),
    getPosts: (limit, status) => {
      const params = new URLSearchParams();
      if (limit) params.append("limit", limit.toString());
      if (status) params.append("status", status);
      const queryString = params.toString();
      return makeApiCall(`content-freshness/posts${queryString ? "?" + queryString : ""}`);
    },
    getPostsNeedingUpdates: (limit) => {
      const params = new URLSearchParams();
      if (limit) params.append("limit", limit.toString());
      const queryString = params.toString();
      return makeApiCall(`content-freshness/posts/needing-updates${queryString ? "?" + queryString : ""}`);
    }
  };
  var SchemaType;
  (function(SchemaType2) {
    SchemaType2["STRING"] = "string";
    SchemaType2["NUMBER"] = "number";
    SchemaType2["INTEGER"] = "integer";
    SchemaType2["BOOLEAN"] = "boolean";
    SchemaType2["ARRAY"] = "array";
    SchemaType2["OBJECT"] = "object";
  })(SchemaType || (SchemaType = {}));
  /**
   * @license
   * Copyright 2024 Google LLC
   *
   * Licensed under the Apache License, Version 2.0 (the "License");
   * you may not use this file except in compliance with the License.
   * You may obtain a copy of the License at
   *
   *   http://www.apache.org/licenses/LICENSE-2.0
   *
   * Unless required by applicable law or agreed to in writing, software
   * distributed under the License is distributed on an "AS IS" BASIS,
   * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   * See the License for the specific language governing permissions and
   * limitations under the License.
   */
  var ExecutableCodeLanguage;
  (function(ExecutableCodeLanguage2) {
    ExecutableCodeLanguage2["LANGUAGE_UNSPECIFIED"] = "language_unspecified";
    ExecutableCodeLanguage2["PYTHON"] = "python";
  })(ExecutableCodeLanguage || (ExecutableCodeLanguage = {}));
  var Outcome;
  (function(Outcome2) {
    Outcome2["OUTCOME_UNSPECIFIED"] = "outcome_unspecified";
    Outcome2["OUTCOME_OK"] = "outcome_ok";
    Outcome2["OUTCOME_FAILED"] = "outcome_failed";
    Outcome2["OUTCOME_DEADLINE_EXCEEDED"] = "outcome_deadline_exceeded";
  })(Outcome || (Outcome = {}));
  /**
   * @license
   * Copyright 2024 Google LLC
   *
   * Licensed under the Apache License, Version 2.0 (the "License");
   * you may not use this file except in compliance with the License.
   * You may obtain a copy of the License at
   *
   *   http://www.apache.org/licenses/LICENSE-2.0
   *
   * Unless required by applicable law or agreed to in writing, software
   * distributed under the License is distributed on an "AS IS" BASIS,
   * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   * See the License for the specific language governing permissions and
   * limitations under the License.
   */
  const POSSIBLE_ROLES = ["user", "model", "function", "system"];
  var HarmCategory;
  (function(HarmCategory2) {
    HarmCategory2["HARM_CATEGORY_UNSPECIFIED"] = "HARM_CATEGORY_UNSPECIFIED";
    HarmCategory2["HARM_CATEGORY_HATE_SPEECH"] = "HARM_CATEGORY_HATE_SPEECH";
    HarmCategory2["HARM_CATEGORY_SEXUALLY_EXPLICIT"] = "HARM_CATEGORY_SEXUALLY_EXPLICIT";
    HarmCategory2["HARM_CATEGORY_HARASSMENT"] = "HARM_CATEGORY_HARASSMENT";
    HarmCategory2["HARM_CATEGORY_DANGEROUS_CONTENT"] = "HARM_CATEGORY_DANGEROUS_CONTENT";
    HarmCategory2["HARM_CATEGORY_CIVIC_INTEGRITY"] = "HARM_CATEGORY_CIVIC_INTEGRITY";
  })(HarmCategory || (HarmCategory = {}));
  var HarmBlockThreshold;
  (function(HarmBlockThreshold2) {
    HarmBlockThreshold2["HARM_BLOCK_THRESHOLD_UNSPECIFIED"] = "HARM_BLOCK_THRESHOLD_UNSPECIFIED";
    HarmBlockThreshold2["BLOCK_LOW_AND_ABOVE"] = "BLOCK_LOW_AND_ABOVE";
    HarmBlockThreshold2["BLOCK_MEDIUM_AND_ABOVE"] = "BLOCK_MEDIUM_AND_ABOVE";
    HarmBlockThreshold2["BLOCK_ONLY_HIGH"] = "BLOCK_ONLY_HIGH";
    HarmBlockThreshold2["BLOCK_NONE"] = "BLOCK_NONE";
  })(HarmBlockThreshold || (HarmBlockThreshold = {}));
  var HarmProbability;
  (function(HarmProbability2) {
    HarmProbability2["HARM_PROBABILITY_UNSPECIFIED"] = "HARM_PROBABILITY_UNSPECIFIED";
    HarmProbability2["NEGLIGIBLE"] = "NEGLIGIBLE";
    HarmProbability2["LOW"] = "LOW";
    HarmProbability2["MEDIUM"] = "MEDIUM";
    HarmProbability2["HIGH"] = "HIGH";
  })(HarmProbability || (HarmProbability = {}));
  var BlockReason;
  (function(BlockReason2) {
    BlockReason2["BLOCKED_REASON_UNSPECIFIED"] = "BLOCKED_REASON_UNSPECIFIED";
    BlockReason2["SAFETY"] = "SAFETY";
    BlockReason2["OTHER"] = "OTHER";
  })(BlockReason || (BlockReason = {}));
  var FinishReason;
  (function(FinishReason2) {
    FinishReason2["FINISH_REASON_UNSPECIFIED"] = "FINISH_REASON_UNSPECIFIED";
    FinishReason2["STOP"] = "STOP";
    FinishReason2["MAX_TOKENS"] = "MAX_TOKENS";
    FinishReason2["SAFETY"] = "SAFETY";
    FinishReason2["RECITATION"] = "RECITATION";
    FinishReason2["LANGUAGE"] = "LANGUAGE";
    FinishReason2["BLOCKLIST"] = "BLOCKLIST";
    FinishReason2["PROHIBITED_CONTENT"] = "PROHIBITED_CONTENT";
    FinishReason2["SPII"] = "SPII";
    FinishReason2["MALFORMED_FUNCTION_CALL"] = "MALFORMED_FUNCTION_CALL";
    FinishReason2["OTHER"] = "OTHER";
  })(FinishReason || (FinishReason = {}));
  var TaskType;
  (function(TaskType2) {
    TaskType2["TASK_TYPE_UNSPECIFIED"] = "TASK_TYPE_UNSPECIFIED";
    TaskType2["RETRIEVAL_QUERY"] = "RETRIEVAL_QUERY";
    TaskType2["RETRIEVAL_DOCUMENT"] = "RETRIEVAL_DOCUMENT";
    TaskType2["SEMANTIC_SIMILARITY"] = "SEMANTIC_SIMILARITY";
    TaskType2["CLASSIFICATION"] = "CLASSIFICATION";
    TaskType2["CLUSTERING"] = "CLUSTERING";
  })(TaskType || (TaskType = {}));
  var FunctionCallingMode;
  (function(FunctionCallingMode2) {
    FunctionCallingMode2["MODE_UNSPECIFIED"] = "MODE_UNSPECIFIED";
    FunctionCallingMode2["AUTO"] = "AUTO";
    FunctionCallingMode2["ANY"] = "ANY";
    FunctionCallingMode2["NONE"] = "NONE";
  })(FunctionCallingMode || (FunctionCallingMode = {}));
  var DynamicRetrievalMode;
  (function(DynamicRetrievalMode2) {
    DynamicRetrievalMode2["MODE_UNSPECIFIED"] = "MODE_UNSPECIFIED";
    DynamicRetrievalMode2["MODE_DYNAMIC"] = "MODE_DYNAMIC";
  })(DynamicRetrievalMode || (DynamicRetrievalMode = {}));
  /**
   * @license
   * Copyright 2024 Google LLC
   *
   * Licensed under the Apache License, Version 2.0 (the "License");
   * you may not use this file except in compliance with the License.
   * You may obtain a copy of the License at
   *
   *   http://www.apache.org/licenses/LICENSE-2.0
   *
   * Unless required by applicable law or agreed to in writing, software
   * distributed under the License is distributed on an "AS IS" BASIS,
   * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   * See the License for the specific language governing permissions and
   * limitations under the License.
   */
  class GoogleGenerativeAIError extends Error {
    constructor(message) {
      super(`[GoogleGenerativeAI Error]: ${message}`);
    }
  }
  class GoogleGenerativeAIResponseError extends GoogleGenerativeAIError {
    constructor(message, response) {
      super(message);
      this.response = response;
    }
  }
  class GoogleGenerativeAIFetchError extends GoogleGenerativeAIError {
    constructor(message, status, statusText, errorDetails) {
      super(message);
      this.status = status;
      this.statusText = statusText;
      this.errorDetails = errorDetails;
    }
  }
  class GoogleGenerativeAIRequestInputError extends GoogleGenerativeAIError {
  }
  class GoogleGenerativeAIAbortError extends GoogleGenerativeAIError {
  }
  /**
   * @license
   * Copyright 2024 Google LLC
   *
   * Licensed under the Apache License, Version 2.0 (the "License");
   * you may not use this file except in compliance with the License.
   * You may obtain a copy of the License at
   *
   *   http://www.apache.org/licenses/LICENSE-2.0
   *
   * Unless required by applicable law or agreed to in writing, software
   * distributed under the License is distributed on an "AS IS" BASIS,
   * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   * See the License for the specific language governing permissions and
   * limitations under the License.
   */
  const DEFAULT_BASE_URL = "https://generativelanguage.googleapis.com";
  const DEFAULT_API_VERSION = "v1beta";
  const PACKAGE_VERSION = "0.24.1";
  const PACKAGE_LOG_HEADER = "genai-js";
  var Task;
  (function(Task2) {
    Task2["GENERATE_CONTENT"] = "generateContent";
    Task2["STREAM_GENERATE_CONTENT"] = "streamGenerateContent";
    Task2["COUNT_TOKENS"] = "countTokens";
    Task2["EMBED_CONTENT"] = "embedContent";
    Task2["BATCH_EMBED_CONTENTS"] = "batchEmbedContents";
  })(Task || (Task = {}));
  class RequestUrl {
    constructor(model, task, apiKey, stream, requestOptions) {
      this.model = model;
      this.task = task;
      this.apiKey = apiKey;
      this.stream = stream;
      this.requestOptions = requestOptions;
    }
    toString() {
      var _a, _b;
      const apiVersion = ((_a = this.requestOptions) === null || _a === void 0 ? void 0 : _a.apiVersion) || DEFAULT_API_VERSION;
      const baseUrl = ((_b = this.requestOptions) === null || _b === void 0 ? void 0 : _b.baseUrl) || DEFAULT_BASE_URL;
      let url = `${baseUrl}/${apiVersion}/${this.model}:${this.task}`;
      if (this.stream) {
        url += "?alt=sse";
      }
      return url;
    }
  }
  function getClientHeaders(requestOptions) {
    const clientHeaders = [];
    if (requestOptions === null || requestOptions === void 0 ? void 0 : requestOptions.apiClient) {
      clientHeaders.push(requestOptions.apiClient);
    }
    clientHeaders.push(`${PACKAGE_LOG_HEADER}/${PACKAGE_VERSION}`);
    return clientHeaders.join(" ");
  }
  async function getHeaders(url) {
    var _a;
    const headers = new Headers();
    headers.append("Content-Type", "application/json");
    headers.append("x-goog-api-client", getClientHeaders(url.requestOptions));
    headers.append("x-goog-api-key", url.apiKey);
    let customHeaders = (_a = url.requestOptions) === null || _a === void 0 ? void 0 : _a.customHeaders;
    if (customHeaders) {
      if (!(customHeaders instanceof Headers)) {
        try {
          customHeaders = new Headers(customHeaders);
        } catch (e) {
          throw new GoogleGenerativeAIRequestInputError(`unable to convert customHeaders value ${JSON.stringify(customHeaders)} to Headers: ${e.message}`);
        }
      }
      for (const [headerName, headerValue] of customHeaders.entries()) {
        if (headerName === "x-goog-api-key") {
          throw new GoogleGenerativeAIRequestInputError(`Cannot set reserved header name ${headerName}`);
        } else if (headerName === "x-goog-api-client") {
          throw new GoogleGenerativeAIRequestInputError(`Header name ${headerName} can only be set using the apiClient field`);
        }
        headers.append(headerName, headerValue);
      }
    }
    return headers;
  }
  async function constructModelRequest(model, task, apiKey, stream, body, requestOptions) {
    const url = new RequestUrl(model, task, apiKey, stream, requestOptions);
    return {
      url: url.toString(),
      fetchOptions: Object.assign(Object.assign({}, buildFetchOptions(requestOptions)), { method: "POST", headers: await getHeaders(url), body })
    };
  }
  async function makeModelRequest(model, task, apiKey, stream, body, requestOptions = {}, fetchFn = fetch) {
    const { url, fetchOptions } = await constructModelRequest(model, task, apiKey, stream, body, requestOptions);
    return makeRequest(url, fetchOptions, fetchFn);
  }
  async function makeRequest(url, fetchOptions, fetchFn = fetch) {
    let response;
    try {
      response = await fetchFn(url, fetchOptions);
    } catch (e) {
      handleResponseError(e, url);
    }
    if (!response.ok) {
      await handleResponseNotOk(response, url);
    }
    return response;
  }
  function handleResponseError(e, url) {
    let err = e;
    if (err.name === "AbortError") {
      err = new GoogleGenerativeAIAbortError(`Request aborted when fetching ${url.toString()}: ${e.message}`);
      err.stack = e.stack;
    } else if (!(e instanceof GoogleGenerativeAIFetchError || e instanceof GoogleGenerativeAIRequestInputError)) {
      err = new GoogleGenerativeAIError(`Error fetching from ${url.toString()}: ${e.message}`);
      err.stack = e.stack;
    }
    throw err;
  }
  async function handleResponseNotOk(response, url) {
    let message = "";
    let errorDetails;
    try {
      const json = await response.json();
      message = json.error.message;
      if (json.error.details) {
        message += ` ${JSON.stringify(json.error.details)}`;
        errorDetails = json.error.details;
      }
    } catch (e) {
    }
    throw new GoogleGenerativeAIFetchError(`Error fetching from ${url.toString()}: [${response.status} ${response.statusText}] ${message}`, response.status, response.statusText, errorDetails);
  }
  function buildFetchOptions(requestOptions) {
    const fetchOptions = {};
    if ((requestOptions === null || requestOptions === void 0 ? void 0 : requestOptions.signal) !== void 0 || (requestOptions === null || requestOptions === void 0 ? void 0 : requestOptions.timeout) >= 0) {
      const controller = new AbortController();
      if ((requestOptions === null || requestOptions === void 0 ? void 0 : requestOptions.timeout) >= 0) {
        setTimeout(() => controller.abort(), requestOptions.timeout);
      }
      if (requestOptions === null || requestOptions === void 0 ? void 0 : requestOptions.signal) {
        requestOptions.signal.addEventListener("abort", () => {
          controller.abort();
        });
      }
      fetchOptions.signal = controller.signal;
    }
    return fetchOptions;
  }
  /**
   * @license
   * Copyright 2024 Google LLC
   *
   * Licensed under the Apache License, Version 2.0 (the "License");
   * you may not use this file except in compliance with the License.
   * You may obtain a copy of the License at
   *
   *   http://www.apache.org/licenses/LICENSE-2.0
   *
   * Unless required by applicable law or agreed to in writing, software
   * distributed under the License is distributed on an "AS IS" BASIS,
   * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   * See the License for the specific language governing permissions and
   * limitations under the License.
   */
  function addHelpers(response) {
    response.text = () => {
      if (response.candidates && response.candidates.length > 0) {
        if (response.candidates.length > 1) {
          console.warn(`This response had ${response.candidates.length} candidates. Returning text from the first candidate only. Access response.candidates directly to use the other candidates.`);
        }
        if (hadBadFinishReason(response.candidates[0])) {
          throw new GoogleGenerativeAIResponseError(`${formatBlockErrorMessage(response)}`, response);
        }
        return getText(response);
      } else if (response.promptFeedback) {
        throw new GoogleGenerativeAIResponseError(`Text not available. ${formatBlockErrorMessage(response)}`, response);
      }
      return "";
    };
    response.functionCall = () => {
      if (response.candidates && response.candidates.length > 0) {
        if (response.candidates.length > 1) {
          console.warn(`This response had ${response.candidates.length} candidates. Returning function calls from the first candidate only. Access response.candidates directly to use the other candidates.`);
        }
        if (hadBadFinishReason(response.candidates[0])) {
          throw new GoogleGenerativeAIResponseError(`${formatBlockErrorMessage(response)}`, response);
        }
        console.warn(`response.functionCall() is deprecated. Use response.functionCalls() instead.`);
        return getFunctionCalls(response)[0];
      } else if (response.promptFeedback) {
        throw new GoogleGenerativeAIResponseError(`Function call not available. ${formatBlockErrorMessage(response)}`, response);
      }
      return void 0;
    };
    response.functionCalls = () => {
      if (response.candidates && response.candidates.length > 0) {
        if (response.candidates.length > 1) {
          console.warn(`This response had ${response.candidates.length} candidates. Returning function calls from the first candidate only. Access response.candidates directly to use the other candidates.`);
        }
        if (hadBadFinishReason(response.candidates[0])) {
          throw new GoogleGenerativeAIResponseError(`${formatBlockErrorMessage(response)}`, response);
        }
        return getFunctionCalls(response);
      } else if (response.promptFeedback) {
        throw new GoogleGenerativeAIResponseError(`Function call not available. ${formatBlockErrorMessage(response)}`, response);
      }
      return void 0;
    };
    return response;
  }
  function getText(response) {
    var _a, _b, _c, _d;
    const textStrings = [];
    if ((_b = (_a = response.candidates) === null || _a === void 0 ? void 0 : _a[0].content) === null || _b === void 0 ? void 0 : _b.parts) {
      for (const part of (_d = (_c = response.candidates) === null || _c === void 0 ? void 0 : _c[0].content) === null || _d === void 0 ? void 0 : _d.parts) {
        if (part.text) {
          textStrings.push(part.text);
        }
        if (part.executableCode) {
          textStrings.push("\n```" + part.executableCode.language + "\n" + part.executableCode.code + "\n```\n");
        }
        if (part.codeExecutionResult) {
          textStrings.push("\n```\n" + part.codeExecutionResult.output + "\n```\n");
        }
      }
    }
    if (textStrings.length > 0) {
      return textStrings.join("");
    } else {
      return "";
    }
  }
  function getFunctionCalls(response) {
    var _a, _b, _c, _d;
    const functionCalls = [];
    if ((_b = (_a = response.candidates) === null || _a === void 0 ? void 0 : _a[0].content) === null || _b === void 0 ? void 0 : _b.parts) {
      for (const part of (_d = (_c = response.candidates) === null || _c === void 0 ? void 0 : _c[0].content) === null || _d === void 0 ? void 0 : _d.parts) {
        if (part.functionCall) {
          functionCalls.push(part.functionCall);
        }
      }
    }
    if (functionCalls.length > 0) {
      return functionCalls;
    } else {
      return void 0;
    }
  }
  const badFinishReasons = [
    FinishReason.RECITATION,
    FinishReason.SAFETY,
    FinishReason.LANGUAGE
  ];
  function hadBadFinishReason(candidate) {
    return !!candidate.finishReason && badFinishReasons.includes(candidate.finishReason);
  }
  function formatBlockErrorMessage(response) {
    var _a, _b, _c;
    let message = "";
    if ((!response.candidates || response.candidates.length === 0) && response.promptFeedback) {
      message += "Response was blocked";
      if ((_a = response.promptFeedback) === null || _a === void 0 ? void 0 : _a.blockReason) {
        message += ` due to ${response.promptFeedback.blockReason}`;
      }
      if ((_b = response.promptFeedback) === null || _b === void 0 ? void 0 : _b.blockReasonMessage) {
        message += `: ${response.promptFeedback.blockReasonMessage}`;
      }
    } else if ((_c = response.candidates) === null || _c === void 0 ? void 0 : _c[0]) {
      const firstCandidate = response.candidates[0];
      if (hadBadFinishReason(firstCandidate)) {
        message += `Candidate was blocked due to ${firstCandidate.finishReason}`;
        if (firstCandidate.finishMessage) {
          message += `: ${firstCandidate.finishMessage}`;
        }
      }
    }
    return message;
  }
  function __await(v) {
    return this instanceof __await ? (this.v = v, this) : new __await(v);
  }
  function __asyncGenerator(thisArg, _arguments, generator) {
    if (!Symbol.asyncIterator) throw new TypeError("Symbol.asyncIterator is not defined.");
    var g = generator.apply(thisArg, _arguments || []), i, q = [];
    return i = {}, verb("next"), verb("throw"), verb("return"), i[Symbol.asyncIterator] = function() {
      return this;
    }, i;
    function verb(n) {
      if (g[n]) i[n] = function(v) {
        return new Promise(function(a, b) {
          q.push([n, v, a, b]) > 1 || resume(n, v);
        });
      };
    }
    function resume(n, v) {
      try {
        step(g[n](v));
      } catch (e) {
        settle(q[0][3], e);
      }
    }
    function step(r) {
      r.value instanceof __await ? Promise.resolve(r.value.v).then(fulfill, reject) : settle(q[0][2], r);
    }
    function fulfill(value) {
      resume("next", value);
    }
    function reject(value) {
      resume("throw", value);
    }
    function settle(f, v) {
      if (f(v), q.shift(), q.length) resume(q[0][0], q[0][1]);
    }
  }
  typeof SuppressedError === "function" ? SuppressedError : function(error, suppressed, message) {
    var e = new Error(message);
    return e.name = "SuppressedError", e.error = error, e.suppressed = suppressed, e;
  };
  /**
   * @license
   * Copyright 2024 Google LLC
   *
   * Licensed under the Apache License, Version 2.0 (the "License");
   * you may not use this file except in compliance with the License.
   * You may obtain a copy of the License at
   *
   *   http://www.apache.org/licenses/LICENSE-2.0
   *
   * Unless required by applicable law or agreed to in writing, software
   * distributed under the License is distributed on an "AS IS" BASIS,
   * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   * See the License for the specific language governing permissions and
   * limitations under the License.
   */
  const responseLineRE = /^data\: (.*)(?:\n\n|\r\r|\r\n\r\n)/;
  function processStream(response) {
    const inputStream = response.body.pipeThrough(new TextDecoderStream("utf8", { fatal: true }));
    const responseStream = getResponseStream(inputStream);
    const [stream1, stream2] = responseStream.tee();
    return {
      stream: generateResponseSequence(stream1),
      response: getResponsePromise(stream2)
    };
  }
  async function getResponsePromise(stream) {
    const allResponses = [];
    const reader = stream.getReader();
    while (true) {
      const { done, value } = await reader.read();
      if (done) {
        return addHelpers(aggregateResponses(allResponses));
      }
      allResponses.push(value);
    }
  }
  function generateResponseSequence(stream) {
    return __asyncGenerator(this, arguments, function* generateResponseSequence_1() {
      const reader = stream.getReader();
      while (true) {
        const { value, done } = yield __await(reader.read());
        if (done) {
          break;
        }
        yield yield __await(addHelpers(value));
      }
    });
  }
  function getResponseStream(inputStream) {
    const reader = inputStream.getReader();
    const stream = new ReadableStream({
      start(controller) {
        let currentText = "";
        return pump();
        function pump() {
          return reader.read().then(({ value, done }) => {
            if (done) {
              if (currentText.trim()) {
                controller.error(new GoogleGenerativeAIError("Failed to parse stream"));
                return;
              }
              controller.close();
              return;
            }
            currentText += value;
            let match = currentText.match(responseLineRE);
            let parsedResponse;
            while (match) {
              try {
                parsedResponse = JSON.parse(match[1]);
              } catch (e) {
                controller.error(new GoogleGenerativeAIError(`Error parsing JSON response: "${match[1]}"`));
                return;
              }
              controller.enqueue(parsedResponse);
              currentText = currentText.substring(match[0].length);
              match = currentText.match(responseLineRE);
            }
            return pump();
          }).catch((e) => {
            let err = e;
            err.stack = e.stack;
            if (err.name === "AbortError") {
              err = new GoogleGenerativeAIAbortError("Request aborted when reading from the stream");
            } else {
              err = new GoogleGenerativeAIError("Error reading from the stream");
            }
            throw err;
          });
        }
      }
    });
    return stream;
  }
  function aggregateResponses(responses) {
    const lastResponse = responses[responses.length - 1];
    const aggregatedResponse = {
      promptFeedback: lastResponse === null || lastResponse === void 0 ? void 0 : lastResponse.promptFeedback
    };
    for (const response of responses) {
      if (response.candidates) {
        let candidateIndex = 0;
        for (const candidate of response.candidates) {
          if (!aggregatedResponse.candidates) {
            aggregatedResponse.candidates = [];
          }
          if (!aggregatedResponse.candidates[candidateIndex]) {
            aggregatedResponse.candidates[candidateIndex] = {
              index: candidateIndex
            };
          }
          aggregatedResponse.candidates[candidateIndex].citationMetadata = candidate.citationMetadata;
          aggregatedResponse.candidates[candidateIndex].groundingMetadata = candidate.groundingMetadata;
          aggregatedResponse.candidates[candidateIndex].finishReason = candidate.finishReason;
          aggregatedResponse.candidates[candidateIndex].finishMessage = candidate.finishMessage;
          aggregatedResponse.candidates[candidateIndex].safetyRatings = candidate.safetyRatings;
          if (candidate.content && candidate.content.parts) {
            if (!aggregatedResponse.candidates[candidateIndex].content) {
              aggregatedResponse.candidates[candidateIndex].content = {
                role: candidate.content.role || "user",
                parts: []
              };
            }
            const newPart = {};
            for (const part of candidate.content.parts) {
              if (part.text) {
                newPart.text = part.text;
              }
              if (part.functionCall) {
                newPart.functionCall = part.functionCall;
              }
              if (part.executableCode) {
                newPart.executableCode = part.executableCode;
              }
              if (part.codeExecutionResult) {
                newPart.codeExecutionResult = part.codeExecutionResult;
              }
              if (Object.keys(newPart).length === 0) {
                newPart.text = "";
              }
              aggregatedResponse.candidates[candidateIndex].content.parts.push(newPart);
            }
          }
        }
        candidateIndex++;
      }
      if (response.usageMetadata) {
        aggregatedResponse.usageMetadata = response.usageMetadata;
      }
    }
    return aggregatedResponse;
  }
  /**
   * @license
   * Copyright 2024 Google LLC
   *
   * Licensed under the Apache License, Version 2.0 (the "License");
   * you may not use this file except in compliance with the License.
   * You may obtain a copy of the License at
   *
   *   http://www.apache.org/licenses/LICENSE-2.0
   *
   * Unless required by applicable law or agreed to in writing, software
   * distributed under the License is distributed on an "AS IS" BASIS,
   * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   * See the License for the specific language governing permissions and
   * limitations under the License.
   */
  async function generateContentStream(apiKey, model, params, requestOptions) {
    const response = await makeModelRequest(
      model,
      Task.STREAM_GENERATE_CONTENT,
      apiKey,
      /* stream */
      true,
      JSON.stringify(params),
      requestOptions
    );
    return processStream(response);
  }
  async function generateContent(apiKey, model, params, requestOptions) {
    const response = await makeModelRequest(
      model,
      Task.GENERATE_CONTENT,
      apiKey,
      /* stream */
      false,
      JSON.stringify(params),
      requestOptions
    );
    const responseJson = await response.json();
    const enhancedResponse = addHelpers(responseJson);
    return {
      response: enhancedResponse
    };
  }
  /**
   * @license
   * Copyright 2024 Google LLC
   *
   * Licensed under the Apache License, Version 2.0 (the "License");
   * you may not use this file except in compliance with the License.
   * You may obtain a copy of the License at
   *
   *   http://www.apache.org/licenses/LICENSE-2.0
   *
   * Unless required by applicable law or agreed to in writing, software
   * distributed under the License is distributed on an "AS IS" BASIS,
   * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   * See the License for the specific language governing permissions and
   * limitations under the License.
   */
  function formatSystemInstruction(input) {
    if (input == null) {
      return void 0;
    } else if (typeof input === "string") {
      return { role: "system", parts: [{ text: input }] };
    } else if (input.text) {
      return { role: "system", parts: [input] };
    } else if (input.parts) {
      if (!input.role) {
        return { role: "system", parts: input.parts };
      } else {
        return input;
      }
    }
  }
  function formatNewContent(request) {
    let newParts = [];
    if (typeof request === "string") {
      newParts = [{ text: request }];
    } else {
      for (const partOrString of request) {
        if (typeof partOrString === "string") {
          newParts.push({ text: partOrString });
        } else {
          newParts.push(partOrString);
        }
      }
    }
    return assignRoleToPartsAndValidateSendMessageRequest(newParts);
  }
  function assignRoleToPartsAndValidateSendMessageRequest(parts) {
    const userContent = { role: "user", parts: [] };
    const functionContent = { role: "function", parts: [] };
    let hasUserContent = false;
    let hasFunctionContent = false;
    for (const part of parts) {
      if ("functionResponse" in part) {
        functionContent.parts.push(part);
        hasFunctionContent = true;
      } else {
        userContent.parts.push(part);
        hasUserContent = true;
      }
    }
    if (hasUserContent && hasFunctionContent) {
      throw new GoogleGenerativeAIError("Within a single message, FunctionResponse cannot be mixed with other type of part in the request for sending chat message.");
    }
    if (!hasUserContent && !hasFunctionContent) {
      throw new GoogleGenerativeAIError("No content is provided for sending chat message.");
    }
    if (hasUserContent) {
      return userContent;
    }
    return functionContent;
  }
  function formatCountTokensInput(params, modelParams) {
    var _a;
    let formattedGenerateContentRequest = {
      model: modelParams === null || modelParams === void 0 ? void 0 : modelParams.model,
      generationConfig: modelParams === null || modelParams === void 0 ? void 0 : modelParams.generationConfig,
      safetySettings: modelParams === null || modelParams === void 0 ? void 0 : modelParams.safetySettings,
      tools: modelParams === null || modelParams === void 0 ? void 0 : modelParams.tools,
      toolConfig: modelParams === null || modelParams === void 0 ? void 0 : modelParams.toolConfig,
      systemInstruction: modelParams === null || modelParams === void 0 ? void 0 : modelParams.systemInstruction,
      cachedContent: (_a = modelParams === null || modelParams === void 0 ? void 0 : modelParams.cachedContent) === null || _a === void 0 ? void 0 : _a.name,
      contents: []
    };
    const containsGenerateContentRequest = params.generateContentRequest != null;
    if (params.contents) {
      if (containsGenerateContentRequest) {
        throw new GoogleGenerativeAIRequestInputError("CountTokensRequest must have one of contents or generateContentRequest, not both.");
      }
      formattedGenerateContentRequest.contents = params.contents;
    } else if (containsGenerateContentRequest) {
      formattedGenerateContentRequest = Object.assign(Object.assign({}, formattedGenerateContentRequest), params.generateContentRequest);
    } else {
      const content = formatNewContent(params);
      formattedGenerateContentRequest.contents = [content];
    }
    return { generateContentRequest: formattedGenerateContentRequest };
  }
  function formatGenerateContentInput(params) {
    let formattedRequest;
    if (params.contents) {
      formattedRequest = params;
    } else {
      const content = formatNewContent(params);
      formattedRequest = { contents: [content] };
    }
    if (params.systemInstruction) {
      formattedRequest.systemInstruction = formatSystemInstruction(params.systemInstruction);
    }
    return formattedRequest;
  }
  function formatEmbedContentInput(params) {
    if (typeof params === "string" || Array.isArray(params)) {
      const content = formatNewContent(params);
      return { content };
    }
    return params;
  }
  /**
   * @license
   * Copyright 2024 Google LLC
   *
   * Licensed under the Apache License, Version 2.0 (the "License");
   * you may not use this file except in compliance with the License.
   * You may obtain a copy of the License at
   *
   *   http://www.apache.org/licenses/LICENSE-2.0
   *
   * Unless required by applicable law or agreed to in writing, software
   * distributed under the License is distributed on an "AS IS" BASIS,
   * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   * See the License for the specific language governing permissions and
   * limitations under the License.
   */
  const VALID_PART_FIELDS = [
    "text",
    "inlineData",
    "functionCall",
    "functionResponse",
    "executableCode",
    "codeExecutionResult"
  ];
  const VALID_PARTS_PER_ROLE = {
    user: ["text", "inlineData"],
    function: ["functionResponse"],
    model: ["text", "functionCall", "executableCode", "codeExecutionResult"],
    // System instructions shouldn't be in history anyway.
    system: ["text"]
  };
  function validateChatHistory(history) {
    let prevContent = false;
    for (const currContent of history) {
      const { role, parts } = currContent;
      if (!prevContent && role !== "user") {
        throw new GoogleGenerativeAIError(`First content should be with role 'user', got ${role}`);
      }
      if (!POSSIBLE_ROLES.includes(role)) {
        throw new GoogleGenerativeAIError(`Each item should include role field. Got ${role} but valid roles are: ${JSON.stringify(POSSIBLE_ROLES)}`);
      }
      if (!Array.isArray(parts)) {
        throw new GoogleGenerativeAIError("Content should have 'parts' property with an array of Parts");
      }
      if (parts.length === 0) {
        throw new GoogleGenerativeAIError("Each Content should have at least one part");
      }
      const countFields = {
        text: 0,
        inlineData: 0,
        functionCall: 0,
        functionResponse: 0,
        fileData: 0,
        executableCode: 0,
        codeExecutionResult: 0
      };
      for (const part of parts) {
        for (const key of VALID_PART_FIELDS) {
          if (key in part) {
            countFields[key] += 1;
          }
        }
      }
      const validParts = VALID_PARTS_PER_ROLE[role];
      for (const key of VALID_PART_FIELDS) {
        if (!validParts.includes(key) && countFields[key] > 0) {
          throw new GoogleGenerativeAIError(`Content with role '${role}' can't contain '${key}' part`);
        }
      }
      prevContent = true;
    }
  }
  function isValidResponse(response) {
    var _a;
    if (response.candidates === void 0 || response.candidates.length === 0) {
      return false;
    }
    const content = (_a = response.candidates[0]) === null || _a === void 0 ? void 0 : _a.content;
    if (content === void 0) {
      return false;
    }
    if (content.parts === void 0 || content.parts.length === 0) {
      return false;
    }
    for (const part of content.parts) {
      if (part === void 0 || Object.keys(part).length === 0) {
        return false;
      }
      if (part.text !== void 0 && part.text === "") {
        return false;
      }
    }
    return true;
  }
  /**
   * @license
   * Copyright 2024 Google LLC
   *
   * Licensed under the Apache License, Version 2.0 (the "License");
   * you may not use this file except in compliance with the License.
   * You may obtain a copy of the License at
   *
   *   http://www.apache.org/licenses/LICENSE-2.0
   *
   * Unless required by applicable law or agreed to in writing, software
   * distributed under the License is distributed on an "AS IS" BASIS,
   * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   * See the License for the specific language governing permissions and
   * limitations under the License.
   */
  const SILENT_ERROR = "SILENT_ERROR";
  class ChatSession {
    constructor(apiKey, model, params, _requestOptions = {}) {
      this.model = model;
      this.params = params;
      this._requestOptions = _requestOptions;
      this._history = [];
      this._sendPromise = Promise.resolve();
      this._apiKey = apiKey;
      if (params === null || params === void 0 ? void 0 : params.history) {
        validateChatHistory(params.history);
        this._history = params.history;
      }
    }
    /**
     * Gets the chat history so far. Blocked prompts are not added to history.
     * Blocked candidates are not added to history, nor are the prompts that
     * generated them.
     */
    async getHistory() {
      await this._sendPromise;
      return this._history;
    }
    /**
     * Sends a chat message and receives a non-streaming
     * {@link GenerateContentResult}.
     *
     * Fields set in the optional {@link SingleRequestOptions} parameter will
     * take precedence over the {@link RequestOptions} values provided to
     * {@link GoogleGenerativeAI.getGenerativeModel }.
     */
    async sendMessage(request, requestOptions = {}) {
      var _a, _b, _c, _d, _e, _f;
      await this._sendPromise;
      const newContent = formatNewContent(request);
      const generateContentRequest = {
        safetySettings: (_a = this.params) === null || _a === void 0 ? void 0 : _a.safetySettings,
        generationConfig: (_b = this.params) === null || _b === void 0 ? void 0 : _b.generationConfig,
        tools: (_c = this.params) === null || _c === void 0 ? void 0 : _c.tools,
        toolConfig: (_d = this.params) === null || _d === void 0 ? void 0 : _d.toolConfig,
        systemInstruction: (_e = this.params) === null || _e === void 0 ? void 0 : _e.systemInstruction,
        cachedContent: (_f = this.params) === null || _f === void 0 ? void 0 : _f.cachedContent,
        contents: [...this._history, newContent]
      };
      const chatSessionRequestOptions = Object.assign(Object.assign({}, this._requestOptions), requestOptions);
      let finalResult;
      this._sendPromise = this._sendPromise.then(() => generateContent(this._apiKey, this.model, generateContentRequest, chatSessionRequestOptions)).then((result) => {
        var _a2;
        if (isValidResponse(result.response)) {
          this._history.push(newContent);
          const responseContent = Object.assign({
            parts: [],
            // Response seems to come back without a role set.
            role: "model"
          }, (_a2 = result.response.candidates) === null || _a2 === void 0 ? void 0 : _a2[0].content);
          this._history.push(responseContent);
        } else {
          const blockErrorMessage = formatBlockErrorMessage(result.response);
          if (blockErrorMessage) {
            console.warn(`sendMessage() was unsuccessful. ${blockErrorMessage}. Inspect response object for details.`);
          }
        }
        finalResult = result;
      }).catch((e) => {
        this._sendPromise = Promise.resolve();
        throw e;
      });
      await this._sendPromise;
      return finalResult;
    }
    /**
     * Sends a chat message and receives the response as a
     * {@link GenerateContentStreamResult} containing an iterable stream
     * and a response promise.
     *
     * Fields set in the optional {@link SingleRequestOptions} parameter will
     * take precedence over the {@link RequestOptions} values provided to
     * {@link GoogleGenerativeAI.getGenerativeModel }.
     */
    async sendMessageStream(request, requestOptions = {}) {
      var _a, _b, _c, _d, _e, _f;
      await this._sendPromise;
      const newContent = formatNewContent(request);
      const generateContentRequest = {
        safetySettings: (_a = this.params) === null || _a === void 0 ? void 0 : _a.safetySettings,
        generationConfig: (_b = this.params) === null || _b === void 0 ? void 0 : _b.generationConfig,
        tools: (_c = this.params) === null || _c === void 0 ? void 0 : _c.tools,
        toolConfig: (_d = this.params) === null || _d === void 0 ? void 0 : _d.toolConfig,
        systemInstruction: (_e = this.params) === null || _e === void 0 ? void 0 : _e.systemInstruction,
        cachedContent: (_f = this.params) === null || _f === void 0 ? void 0 : _f.cachedContent,
        contents: [...this._history, newContent]
      };
      const chatSessionRequestOptions = Object.assign(Object.assign({}, this._requestOptions), requestOptions);
      const streamPromise = generateContentStream(this._apiKey, this.model, generateContentRequest, chatSessionRequestOptions);
      this._sendPromise = this._sendPromise.then(() => streamPromise).catch((_ignored) => {
        throw new Error(SILENT_ERROR);
      }).then((streamResult) => streamResult.response).then((response) => {
        if (isValidResponse(response)) {
          this._history.push(newContent);
          const responseContent = Object.assign({}, response.candidates[0].content);
          if (!responseContent.role) {
            responseContent.role = "model";
          }
          this._history.push(responseContent);
        } else {
          const blockErrorMessage = formatBlockErrorMessage(response);
          if (blockErrorMessage) {
            console.warn(`sendMessageStream() was unsuccessful. ${blockErrorMessage}. Inspect response object for details.`);
          }
        }
      }).catch((e) => {
        if (e.message !== SILENT_ERROR) {
          console.error(e);
        }
      });
      return streamPromise;
    }
  }
  /**
   * @license
   * Copyright 2024 Google LLC
   *
   * Licensed under the Apache License, Version 2.0 (the "License");
   * you may not use this file except in compliance with the License.
   * You may obtain a copy of the License at
   *
   *   http://www.apache.org/licenses/LICENSE-2.0
   *
   * Unless required by applicable law or agreed to in writing, software
   * distributed under the License is distributed on an "AS IS" BASIS,
   * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   * See the License for the specific language governing permissions and
   * limitations under the License.
   */
  async function countTokens(apiKey, model, params, singleRequestOptions) {
    const response = await makeModelRequest(model, Task.COUNT_TOKENS, apiKey, false, JSON.stringify(params), singleRequestOptions);
    return response.json();
  }
  /**
   * @license
   * Copyright 2024 Google LLC
   *
   * Licensed under the Apache License, Version 2.0 (the "License");
   * you may not use this file except in compliance with the License.
   * You may obtain a copy of the License at
   *
   *   http://www.apache.org/licenses/LICENSE-2.0
   *
   * Unless required by applicable law or agreed to in writing, software
   * distributed under the License is distributed on an "AS IS" BASIS,
   * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   * See the License for the specific language governing permissions and
   * limitations under the License.
   */
  async function embedContent(apiKey, model, params, requestOptions) {
    const response = await makeModelRequest(model, Task.EMBED_CONTENT, apiKey, false, JSON.stringify(params), requestOptions);
    return response.json();
  }
  async function batchEmbedContents(apiKey, model, params, requestOptions) {
    const requestsWithModel = params.requests.map((request) => {
      return Object.assign(Object.assign({}, request), { model });
    });
    const response = await makeModelRequest(model, Task.BATCH_EMBED_CONTENTS, apiKey, false, JSON.stringify({ requests: requestsWithModel }), requestOptions);
    return response.json();
  }
  /**
   * @license
   * Copyright 2024 Google LLC
   *
   * Licensed under the Apache License, Version 2.0 (the "License");
   * you may not use this file except in compliance with the License.
   * You may obtain a copy of the License at
   *
   *   http://www.apache.org/licenses/LICENSE-2.0
   *
   * Unless required by applicable law or agreed to in writing, software
   * distributed under the License is distributed on an "AS IS" BASIS,
   * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   * See the License for the specific language governing permissions and
   * limitations under the License.
   */
  class GenerativeModel {
    constructor(apiKey, modelParams, _requestOptions = {}) {
      this.apiKey = apiKey;
      this._requestOptions = _requestOptions;
      if (modelParams.model.includes("/")) {
        this.model = modelParams.model;
      } else {
        this.model = `models/${modelParams.model}`;
      }
      this.generationConfig = modelParams.generationConfig || {};
      this.safetySettings = modelParams.safetySettings || [];
      this.tools = modelParams.tools;
      this.toolConfig = modelParams.toolConfig;
      this.systemInstruction = formatSystemInstruction(modelParams.systemInstruction);
      this.cachedContent = modelParams.cachedContent;
    }
    /**
     * Makes a single non-streaming call to the model
     * and returns an object containing a single {@link GenerateContentResponse}.
     *
     * Fields set in the optional {@link SingleRequestOptions} parameter will
     * take precedence over the {@link RequestOptions} values provided to
     * {@link GoogleGenerativeAI.getGenerativeModel }.
     */
    async generateContent(request, requestOptions = {}) {
      var _a;
      const formattedParams = formatGenerateContentInput(request);
      const generativeModelRequestOptions = Object.assign(Object.assign({}, this._requestOptions), requestOptions);
      return generateContent(this.apiKey, this.model, Object.assign({ generationConfig: this.generationConfig, safetySettings: this.safetySettings, tools: this.tools, toolConfig: this.toolConfig, systemInstruction: this.systemInstruction, cachedContent: (_a = this.cachedContent) === null || _a === void 0 ? void 0 : _a.name }, formattedParams), generativeModelRequestOptions);
    }
    /**
     * Makes a single streaming call to the model and returns an object
     * containing an iterable stream that iterates over all chunks in the
     * streaming response as well as a promise that returns the final
     * aggregated response.
     *
     * Fields set in the optional {@link SingleRequestOptions} parameter will
     * take precedence over the {@link RequestOptions} values provided to
     * {@link GoogleGenerativeAI.getGenerativeModel }.
     */
    async generateContentStream(request, requestOptions = {}) {
      var _a;
      const formattedParams = formatGenerateContentInput(request);
      const generativeModelRequestOptions = Object.assign(Object.assign({}, this._requestOptions), requestOptions);
      return generateContentStream(this.apiKey, this.model, Object.assign({ generationConfig: this.generationConfig, safetySettings: this.safetySettings, tools: this.tools, toolConfig: this.toolConfig, systemInstruction: this.systemInstruction, cachedContent: (_a = this.cachedContent) === null || _a === void 0 ? void 0 : _a.name }, formattedParams), generativeModelRequestOptions);
    }
    /**
     * Gets a new {@link ChatSession} instance which can be used for
     * multi-turn chats.
     */
    startChat(startChatParams) {
      var _a;
      return new ChatSession(this.apiKey, this.model, Object.assign({ generationConfig: this.generationConfig, safetySettings: this.safetySettings, tools: this.tools, toolConfig: this.toolConfig, systemInstruction: this.systemInstruction, cachedContent: (_a = this.cachedContent) === null || _a === void 0 ? void 0 : _a.name }, startChatParams), this._requestOptions);
    }
    /**
     * Counts the tokens in the provided request.
     *
     * Fields set in the optional {@link SingleRequestOptions} parameter will
     * take precedence over the {@link RequestOptions} values provided to
     * {@link GoogleGenerativeAI.getGenerativeModel }.
     */
    async countTokens(request, requestOptions = {}) {
      const formattedParams = formatCountTokensInput(request, {
        model: this.model,
        generationConfig: this.generationConfig,
        safetySettings: this.safetySettings,
        tools: this.tools,
        toolConfig: this.toolConfig,
        systemInstruction: this.systemInstruction,
        cachedContent: this.cachedContent
      });
      const generativeModelRequestOptions = Object.assign(Object.assign({}, this._requestOptions), requestOptions);
      return countTokens(this.apiKey, this.model, formattedParams, generativeModelRequestOptions);
    }
    /**
     * Embeds the provided content.
     *
     * Fields set in the optional {@link SingleRequestOptions} parameter will
     * take precedence over the {@link RequestOptions} values provided to
     * {@link GoogleGenerativeAI.getGenerativeModel }.
     */
    async embedContent(request, requestOptions = {}) {
      const formattedParams = formatEmbedContentInput(request);
      const generativeModelRequestOptions = Object.assign(Object.assign({}, this._requestOptions), requestOptions);
      return embedContent(this.apiKey, this.model, formattedParams, generativeModelRequestOptions);
    }
    /**
     * Embeds an array of {@link EmbedContentRequest}s.
     *
     * Fields set in the optional {@link SingleRequestOptions} parameter will
     * take precedence over the {@link RequestOptions} values provided to
     * {@link GoogleGenerativeAI.getGenerativeModel }.
     */
    async batchEmbedContents(batchEmbedContentRequest, requestOptions = {}) {
      const generativeModelRequestOptions = Object.assign(Object.assign({}, this._requestOptions), requestOptions);
      return batchEmbedContents(this.apiKey, this.model, batchEmbedContentRequest, generativeModelRequestOptions);
    }
  }
  /**
   * @license
   * Copyright 2024 Google LLC
   *
   * Licensed under the Apache License, Version 2.0 (the "License");
   * you may not use this file except in compliance with the License.
   * You may obtain a copy of the License at
   *
   *   http://www.apache.org/licenses/LICENSE-2.0
   *
   * Unless required by applicable law or agreed to in writing, software
   * distributed under the License is distributed on an "AS IS" BASIS,
   * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   * See the License for the specific language governing permissions and
   * limitations under the License.
   */
  class GoogleGenerativeAI {
    constructor(apiKey) {
      this.apiKey = apiKey;
    }
    /**
     * Gets a {@link GenerativeModel} instance for the provided model name.
     */
    getGenerativeModel(modelParams, requestOptions) {
      if (!modelParams.model) {
        throw new GoogleGenerativeAIError(`Must provide a model name. Example: genai.getGenerativeModel({ model: 'my-model-name' })`);
      }
      return new GenerativeModel(this.apiKey, modelParams, requestOptions);
    }
    /**
     * Creates a {@link GenerativeModel} instance from provided content cache.
     */
    getGenerativeModelFromCachedContent(cachedContent, modelParams, requestOptions) {
      if (!cachedContent.name) {
        throw new GoogleGenerativeAIRequestInputError("Cached content must contain a `name` field.");
      }
      if (!cachedContent.model) {
        throw new GoogleGenerativeAIRequestInputError("Cached content must contain a `model` field.");
      }
      const disallowedDuplicates = ["model", "systemInstruction"];
      for (const key of disallowedDuplicates) {
        if ((modelParams === null || modelParams === void 0 ? void 0 : modelParams[key]) && cachedContent[key] && (modelParams === null || modelParams === void 0 ? void 0 : modelParams[key]) !== cachedContent[key]) {
          if (key === "model") {
            const modelParamsComp = modelParams.model.startsWith("models/") ? modelParams.model.replace("models/", "") : modelParams.model;
            const cachedContentComp = cachedContent.model.startsWith("models/") ? cachedContent.model.replace("models/", "") : cachedContent.model;
            if (modelParamsComp === cachedContentComp) {
              continue;
            }
          }
          throw new GoogleGenerativeAIRequestInputError(`Different value for "${key}" specified in modelParams (${modelParams[key]}) and cachedContent (${cachedContent[key]})`);
        }
      }
      const modelParamsFromCache = Object.assign(Object.assign({}, modelParams), { model: cachedContent.model, tools: cachedContent.tools, toolConfig: cachedContent.toolConfig, systemInstruction: cachedContent.systemInstruction, cachedContent });
      return new GenerativeModel(this.apiKey, modelParamsFromCache, requestOptions);
    }
  }
  let genAI = null;
  const setGeminiApiKey = (key) => {
    if (key && key.trim() !== "") {
      try {
        genAI = new GoogleGenerativeAI(key);
      } catch (error) {
        console.error("Failed to initialize GoogleGenerativeAI:", error);
        genAI = null;
      }
    } else {
      genAI = null;
    }
  };
  const modelConfig = {
    primary: "gemini-2.0-flash",
    fallback: "gemini-1.5-pro",
    maxRetries: 3,
    retryDelay: 2e3
    // 2 seconds
  };
  const testGeminiApiKey = async (apiKey) => {
    try {
      if (!apiKey || apiKey.trim() === "") {
        return { success: false, error: "API key is required" };
      }
      const testAI = new GoogleGenerativeAI(apiKey);
      const model = testAI.getGenerativeModel({ model: modelConfig.primary });
      const result = await model.generateContent({
        contents: [{ role: "user", parts: [{ text: "Hello" }] }],
        generationConfig: {
          temperature: 0.1,
          maxOutputTokens: 10
        }
      });
      const response = result.response;
      if (response && response.text) {
        return { success: true };
      } else {
        return { success: false, error: "Invalid response from API" };
      }
    } catch (error) {
      console.error("Gemini API key test failed:", error);
      if (error.message?.includes("API_KEY_INVALID")) {
        return { success: false, error: "Invalid API key" };
      } else if (error.message?.includes("quota")) {
        return { success: false, error: "API quota exceeded" };
      } else if (error.message?.includes("permission")) {
        return { success: false, error: "API key does not have required permissions" };
      } else {
        return { success: false, error: error.message || "Unknown error occurred" };
      }
    }
  };
  const Lightbulb = ({ className, style }) => /* @__PURE__ */ jsxRuntimeExports.jsxs("svg", { xmlns: "http://www.w3.org/2000/svg", width: "24", height: "24", viewBox: "0 0 24 24", fill: "none", stroke: "currentColor", strokeWidth: "2", strokeLinecap: "round", strokeLinejoin: "round", className, style, children: [
    /* @__PURE__ */ jsxRuntimeExports.jsx("path", { d: "M15 14c.2-1 .7-1.7 1.5-2.5 1-.9 1.5-2.2 1.5-3.5A6 6 0 0 0 6 8c0 1 .2 2.2 1.5 3.5.7.7 1.3 1.5 1.5 2.5" }),
    /* @__PURE__ */ jsxRuntimeExports.jsx("path", { d: "M9 18h6" }),
    /* @__PURE__ */ jsxRuntimeExports.jsx("path", { d: "M10 22h4" })
  ] });
  const BookOpen = ({ className, style }) => /* @__PURE__ */ jsxRuntimeExports.jsxs("svg", { xmlns: "http://www.w3.org/2000/svg", width: "24", height: "24", viewBox: "0 0 24 24", fill: "none", stroke: "currentColor", strokeWidth: "2", strokeLinecap: "round", strokeLinejoin: "round", className, style, children: [
    /* @__PURE__ */ jsxRuntimeExports.jsx("path", { d: "M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z" }),
    /* @__PURE__ */ jsxRuntimeExports.jsx("path", { d: "M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z" })
  ] });
  const Settings = ({ className, style }) => /* @__PURE__ */ jsxRuntimeExports.jsxs("svg", { xmlns: "http://www.w3.org/2000/svg", width: "24", height: "24", viewBox: "0 0 24 24", fill: "none", stroke: "currentColor", strokeWidth: "2", strokeLinecap: "round", strokeLinejoin: "round", className, style, children: [
    /* @__PURE__ */ jsxRuntimeExports.jsx("path", { d: "M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 0 2.82l-.15.08a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.38a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1 0-2.82l.15.08a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z" }),
    /* @__PURE__ */ jsxRuntimeExports.jsx("circle", { cx: "12", cy: "12", r: "3" })
  ] });
  const LayoutDashboard = ({ className, style }) => /* @__PURE__ */ jsxRuntimeExports.jsxs("svg", { xmlns: "http://www.w3.org/2000/svg", width: "24", height: "24", viewBox: "0 0 24 24", fill: "none", stroke: "currentColor", strokeWidth: "2", strokeLinecap: "round", strokeLinejoin: "round", className, style, children: [
    /* @__PURE__ */ jsxRuntimeExports.jsx("rect", { width: "7", height: "9", x: "3", y: "3", rx: "1" }),
    /* @__PURE__ */ jsxRuntimeExports.jsx("rect", { width: "7", height: "5", x: "14", y: "3", rx: "1" }),
    /* @__PURE__ */ jsxRuntimeExports.jsx("rect", { width: "7", height: "9", x: "14", y: "12", rx: "1" }),
    /* @__PURE__ */ jsxRuntimeExports.jsx("rect", { width: "7", height: "5", x: "3", y: "16", rx: "1" })
  ] });
  const Brain = ({ className, style }) => /* @__PURE__ */ jsxRuntimeExports.jsxs("svg", { xmlns: "http://www.w3.org/2000/svg", width: "24", height: "24", viewBox: "0 0 24 24", fill: "none", stroke: "currentColor", strokeWidth: "2", strokeLinecap: "round", strokeLinejoin: "round", className, style, children: [
    /* @__PURE__ */ jsxRuntimeExports.jsx("path", { d: "M12 5a3 3 0 1 0-5.997.125 4 4 0 0 0-2.526 5.77 4 4 0 0 0 .556 6.588A4 4 0 1 0 12 18Z" }),
    /* @__PURE__ */ jsxRuntimeExports.jsx("path", { d: "M12 5a3 3 0 1 1 5.997.125 4 4 0 0 1 2.526 5.77 4 4 0 0 1-.556 6.588A4 4 0 1 1 12 18Z" }),
    /* @__PURE__ */ jsxRuntimeExports.jsx("path", { d: "M15 13a4.5 4.5 0 0 1-3-4 4.5 4.5 0 0 1-3 4" }),
    /* @__PURE__ */ jsxRuntimeExports.jsx("path", { d: "M17.599 6.5a3 3 0 0 0 .399-1.375" }),
    /* @__PURE__ */ jsxRuntimeExports.jsx("path", { d: "M6.003 5.125A3 3 0 0 0 6.401 6.5" }),
    /* @__PURE__ */ jsxRuntimeExports.jsx("path", { d: "M3.477 10.896a4 4 0 0 1 .585-.396" }),
    /* @__PURE__ */ jsxRuntimeExports.jsx("path", { d: "M19.938 10.5a4 4 0 0 1 .585.396" }),
    /* @__PURE__ */ jsxRuntimeExports.jsx("path", { d: "M6 18a4 4 0 0 1-1.967-.516" }),
    /* @__PURE__ */ jsxRuntimeExports.jsx("path", { d: "M19.967 17.484A4 4 0 0 1 18 18" })
  ] });
  const FileText = ({ className, style }) => /* @__PURE__ */ jsxRuntimeExports.jsxs("svg", { xmlns: "http://www.w3.org/2000/svg", width: "24", height: "24", viewBox: "0 0 24 24", fill: "none", stroke: "currentColor", strokeWidth: "2", strokeLinecap: "round", strokeLinejoin: "round", className, style, children: [
    /* @__PURE__ */ jsxRuntimeExports.jsx("path", { d: "M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z" }),
    /* @__PURE__ */ jsxRuntimeExports.jsx("path", { d: "M14 2v4a2 2 0 0 0 2 2h4" }),
    /* @__PURE__ */ jsxRuntimeExports.jsx("path", { d: "M10 9H8" }),
    /* @__PURE__ */ jsxRuntimeExports.jsx("path", { d: "M16 13H8" }),
    /* @__PURE__ */ jsxRuntimeExports.jsx("path", { d: "M16 17H8" })
  ] });
  const AlertTriangle = ({ className, style }) => /* @__PURE__ */ jsxRuntimeExports.jsxs("svg", { xmlns: "http://www.w3.org/2000/svg", width: "24", height: "24", viewBox: "0 0 24 24", fill: "none", stroke: "currentColor", strokeWidth: "2", strokeLinecap: "round", strokeLinejoin: "round", className, style, children: [
    /* @__PURE__ */ jsxRuntimeExports.jsx("path", { d: "m21.73 18-8-14a2 2 0 0 0-3.46 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z" }),
    /* @__PURE__ */ jsxRuntimeExports.jsx("path", { d: "M12 9v4" }),
    /* @__PURE__ */ jsxRuntimeExports.jsx("path", { d: "M12 17h.01" })
  ] });
  const CheckCircle = ({ className, style }) => /* @__PURE__ */ jsxRuntimeExports.jsxs("svg", { xmlns: "http://www.w3.org/2000/svg", width: "24", height: "24", viewBox: "0 0 24 24", fill: "none", stroke: "currentColor", strokeWidth: "2", strokeLinecap: "round", strokeLinejoin: "round", className, style, children: [
    /* @__PURE__ */ jsxRuntimeExports.jsx("path", { d: "M22 11.08V12a10 10 0 1 1-5.93-9.14" }),
    /* @__PURE__ */ jsxRuntimeExports.jsx("path", { d: "m9 11 3 3L22 4" })
  ] });
  const XCircle = ({ className, style }) => /* @__PURE__ */ jsxRuntimeExports.jsxs("svg", { xmlns: "http://www.w3.org/2000/svg", width: "24", height: "24", viewBox: "0 0 24 24", fill: "none", stroke: "currentColor", strokeWidth: "2", strokeLinecap: "round", strokeLinejoin: "round", className, style, children: [
    /* @__PURE__ */ jsxRuntimeExports.jsx("circle", { cx: "12", cy: "12", r: "10" }),
    /* @__PURE__ */ jsxRuntimeExports.jsx("path", { d: "m15 9-6 6" }),
    /* @__PURE__ */ jsxRuntimeExports.jsx("path", { d: "m9 9 6 6" })
  ] });
  const Clock = ({ className, style }) => /* @__PURE__ */ jsxRuntimeExports.jsxs("svg", { xmlns: "http://www.w3.org/2000/svg", width: "24", height: "24", viewBox: "0 0 24 24", fill: "none", stroke: "currentColor", strokeWidth: "2", strokeLinecap: "round", strokeLinejoin: "round", className, style, children: [
    /* @__PURE__ */ jsxRuntimeExports.jsx("circle", { cx: "12", cy: "12", r: "10" }),
    /* @__PURE__ */ jsxRuntimeExports.jsx("polyline", { points: "12,6 12,12 16,14" })
  ] });
  const Info = ({ className, style }) => /* @__PURE__ */ jsxRuntimeExports.jsxs("svg", { xmlns: "http://www.w3.org/2000/svg", width: "24", height: "24", viewBox: "0 0 24 24", fill: "none", stroke: "currentColor", strokeWidth: "2", strokeLinecap: "round", strokeLinejoin: "round", className, style, children: [
    /* @__PURE__ */ jsxRuntimeExports.jsx("circle", { cx: "12", cy: "12", r: "10" }),
    /* @__PURE__ */ jsxRuntimeExports.jsx("path", { d: "M12 16v-4" }),
    /* @__PURE__ */ jsxRuntimeExports.jsx("path", { d: "M12 8h.01" })
  ] });
  const X = ({ className, style }) => /* @__PURE__ */ jsxRuntimeExports.jsxs("svg", { xmlns: "http://www.w3.org/2000/svg", width: "24", height: "24", viewBox: "0 0 24 24", fill: "none", stroke: "currentColor", strokeWidth: "2", strokeLinecap: "round", strokeLinejoin: "round", className, style, children: [
    /* @__PURE__ */ jsxRuntimeExports.jsx("path", { d: "M18 6 6 18" }),
    /* @__PURE__ */ jsxRuntimeExports.jsx("path", { d: "m6 6 12 12" })
  ] });
  const Spinner = ({ className, style }) => /* @__PURE__ */ jsxRuntimeExports.jsx("svg", { xmlns: "http://www.w3.org/2000/svg", width: "24", height: "24", viewBox: "0 0 24 24", fill: "none", stroke: "currentColor", strokeWidth: "2", strokeLinecap: "round", strokeLinejoin: "round", className: `${className} animate-spin`, style, children: /* @__PURE__ */ jsxRuntimeExports.jsx("path", { d: "M21 12a9 9 0 1 1-6.219-8.56" }) });
  const Send = ({ className, style }) => /* @__PURE__ */ jsxRuntimeExports.jsxs("svg", { xmlns: "http://www.w3.org/2000/svg", width: "24", height: "24", viewBox: "0 0 24 24", fill: "none", stroke: "currentColor", strokeWidth: "2", strokeLinecap: "round", strokeLinejoin: "round", className, style, children: [
    /* @__PURE__ */ jsxRuntimeExports.jsx("path", { d: "m22 2-7 20-4-9-9-4Z" }),
    /* @__PURE__ */ jsxRuntimeExports.jsx("path", { d: "M22 2 11 13" })
  ] });
  const Menu = ({ className, style }) => /* @__PURE__ */ jsxRuntimeExports.jsxs("svg", { xmlns: "http://www.w3.org/2000/svg", width: "24", height: "24", viewBox: "0 0 24 24", fill: "none", stroke: "currentColor", strokeWidth: "2", strokeLinecap: "round", strokeLinejoin: "round", className, style, children: [
    /* @__PURE__ */ jsxRuntimeExports.jsx("line", { x1: "4", x2: "20", y1: "12", y2: "12" }),
    /* @__PURE__ */ jsxRuntimeExports.jsx("line", { x1: "4", x2: "20", y1: "6", y2: "6" }),
    /* @__PURE__ */ jsxRuntimeExports.jsx("line", { x1: "4", x2: "20", y1: "18", y2: "18" })
  ] });
  const Google = ({ className, style }) => /* @__PURE__ */ jsxRuntimeExports.jsx("svg", { xmlns: "http://www.w3.org/2000/svg", width: "24", height: "24", viewBox: "0 0 24 24", fill: "currentColor", className, style, children: /* @__PURE__ */ jsxRuntimeExports.jsx("path", { d: "M21.35,11.1H12.18V13.83H18.69C18.36,17.64 15.19,19.27 12.19,19.27C8.36,19.27 5,16.25 5,12C5,7.9 8.2,4.73 12.19,4.73C14.76,4.73 16.04,5.66 17.14,6.64L19.31,4.5C17.22,2.69 14.86,1.5 12.19,1.5C7.03,1.5 3,5.5 3,12C3,18.45 7.03,22.5 12.19,22.5C17.6,22.5 21.5,18.53 21.5,12.23C21.5,11.66 21.45,11.38 21.35,11.1Z" }) });
  const Trash = ({ className, style }) => /* @__PURE__ */ jsxRuntimeExports.jsxs("svg", { xmlns: "http://www.w3.org/2000/svg", width: "24", height: "24", viewBox: "0 0 24 24", fill: "none", stroke: "currentColor", strokeWidth: "2", strokeLinecap: "round", strokeLinejoin: "round", className, style, children: [
    /* @__PURE__ */ jsxRuntimeExports.jsx("path", { d: "M3 6h18" }),
    /* @__PURE__ */ jsxRuntimeExports.jsx("path", { d: "M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" })
  ] });
  const Pencil = ({ className, style }) => /* @__PURE__ */ jsxRuntimeExports.jsxs("svg", { xmlns: "http://www.w3.org/2000/svg", width: "24", height: "24", viewBox: "0 0 24 24", fill: "none", stroke: "currentColor", strokeWidth: "2", strokeLinecap: "round", strokeLinejoin: "round", className, style, children: [
    /* @__PURE__ */ jsxRuntimeExports.jsx("path", { d: "M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" }),
    /* @__PURE__ */ jsxRuntimeExports.jsx("path", { d: "m15 5 4 4" })
  ] });
  const Edit = ({ className, style }) => /* @__PURE__ */ jsxRuntimeExports.jsxs("svg", { xmlns: "http://www.w3.org/2000/svg", width: "24", height: "24", viewBox: "0 0 24 24", fill: "none", stroke: "currentColor", strokeWidth: "2", strokeLinecap: "round", strokeLinejoin: "round", className, style, children: [
    /* @__PURE__ */ jsxRuntimeExports.jsx("path", { d: "M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" }),
    /* @__PURE__ */ jsxRuntimeExports.jsx("path", { d: "m15 5 4 4" })
  ] });
  const Archive = ({ className, style }) => /* @__PURE__ */ jsxRuntimeExports.jsxs("svg", { xmlns: "http://www.w3.org/2000/svg", width: "24", height: "24", viewBox: "0 0 24 24", fill: "none", stroke: "currentColor", strokeWidth: "2", strokeLinecap: "round", strokeLinejoin: "round", className, style, children: [
    /* @__PURE__ */ jsxRuntimeExports.jsx("rect", { width: "20", height: "5", x: "2", y: "3", rx: "1" }),
    /* @__PURE__ */ jsxRuntimeExports.jsx("path", { d: "M4 8v11a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8" }),
    /* @__PURE__ */ jsxRuntimeExports.jsx("path", { d: "m10 12 4 0" })
  ] });
  const Calendar = ({ className, style }) => /* @__PURE__ */ jsxRuntimeExports.jsxs("svg", { xmlns: "http://www.w3.org/2000/svg", width: "24", height: "24", viewBox: "0 0 24 24", fill: "none", stroke: "currentColor", strokeWidth: "2", strokeLinecap: "round", strokeLinejoin: "round", className, style, children: [
    /* @__PURE__ */ jsxRuntimeExports.jsx("rect", { width: "18", height: "18", x: "3", y: "4", rx: "2", ry: "2" }),
    /* @__PURE__ */ jsxRuntimeExports.jsx("line", { x1: "16", x2: "16", y1: "2", y2: "6" }),
    /* @__PURE__ */ jsxRuntimeExports.jsx("line", { x1: "8", x2: "8", y1: "2", y2: "6" }),
    /* @__PURE__ */ jsxRuntimeExports.jsx("line", { x1: "3", x2: "21", y1: "10", y2: "10" })
  ] });
  const Sparkles = ({ className, style }) => /* @__PURE__ */ jsxRuntimeExports.jsx("svg", { xmlns: "http://www.w3.org/2000/svg", width: "24", height: "24", viewBox: "0 0 24 24", fill: "none", stroke: "currentColor", strokeWidth: "2", strokeLinecap: "round", strokeLinejoin: "round", className, style, children: /* @__PURE__ */ jsxRuntimeExports.jsx("path", { d: "m12 3-1.9 5.8-5.8 1.9 5.8 1.9 1.9 5.8 1.9-5.8 5.8-1.9-5.8-1.9zM5 21l1.9-5.8M17.1 17.1l5.8-1.9" }) });
  const ChevronLeft = ({ className, style }) => /* @__PURE__ */ jsxRuntimeExports.jsx("svg", { xmlns: "http://www.w3.org/2000/svg", width: "24", height: "24", viewBox: "0 0 24 24", fill: "none", stroke: "currentColor", strokeWidth: "2", strokeLinecap: "round", strokeLinejoin: "round", className, style, children: /* @__PURE__ */ jsxRuntimeExports.jsx("path", { d: "m15 18-6-6 6-6" }) });
  const ChevronRight = ({ className, style }) => /* @__PURE__ */ jsxRuntimeExports.jsx("svg", { xmlns: "http://www.w3.org/2000/svg", width: "24", height: "24", viewBox: "0 0 24 24", fill: "none", stroke: "currentColor", strokeWidth: "2", strokeLinecap: "round", strokeLinejoin: "round", className, style, children: /* @__PURE__ */ jsxRuntimeExports.jsx("path", { d: "m9 18 6-6-6-6" }) });
  const PlusCircle = ({ className, style }) => /* @__PURE__ */ jsxRuntimeExports.jsxs("svg", { xmlns: "http://www.w3.org/2000/svg", width: "24", height: "24", viewBox: "0 0 24 24", fill: "none", stroke: "currentColor", strokeWidth: "2", strokeLinecap: "round", strokeLinejoin: "round", className, style, children: [
    /* @__PURE__ */ jsxRuntimeExports.jsx("circle", { cx: "12", cy: "12", r: "10" }),
    /* @__PURE__ */ jsxRuntimeExports.jsx("line", { x1: "12", x2: "12", y1: "8", y2: "16" }),
    /* @__PURE__ */ jsxRuntimeExports.jsx("line", { x1: "8", x2: "16", y1: "12", y2: "12" })
  ] });
  const RefreshCw = ({ className, style }) => /* @__PURE__ */ jsxRuntimeExports.jsxs("svg", { xmlns: "http://www.w3.org/2000/svg", width: "24", height: "24", viewBox: "0 0 24 24", fill: "none", stroke: "currentColor", strokeWidth: "2", strokeLinecap: "round", strokeLinejoin: "round", className, style, children: [
    /* @__PURE__ */ jsxRuntimeExports.jsx("path", { d: "M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8" }),
    /* @__PURE__ */ jsxRuntimeExports.jsx("path", { d: "M21 3v5h-5" }),
    /* @__PURE__ */ jsxRuntimeExports.jsx("path", { d: "M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16" }),
    /* @__PURE__ */ jsxRuntimeExports.jsx("path", { d: "M3 21v-5h5" })
  ] });
  const Eye = ({ className, style }) => /* @__PURE__ */ jsxRuntimeExports.jsxs("svg", { xmlns: "http://www.w3.org/2000/svg", width: "24", height: "24", viewBox: "0 0 24 24", fill: "none", stroke: "currentColor", strokeWidth: "2", strokeLinecap: "round", strokeLinejoin: "round", className, style, children: [
    /* @__PURE__ */ jsxRuntimeExports.jsx("path", { d: "M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" }),
    /* @__PURE__ */ jsxRuntimeExports.jsx("circle", { cx: "12", cy: "12", r: "3" })
  ] });
  const Zap = ({ className, style }) => /* @__PURE__ */ jsxRuntimeExports.jsx("svg", { xmlns: "http://www.w3.org/2000/svg", width: "24", height: "24", viewBox: "0 0 24 24", fill: "none", stroke: "currentColor", strokeWidth: "2", strokeLinecap: "round", strokeLinejoin: "round", className, style, children: /* @__PURE__ */ jsxRuntimeExports.jsx("polygon", { points: "13,2 3,14 12,14 11,22 21,10 12,10" }) });
  const Image = ({ className, style }) => /* @__PURE__ */ jsxRuntimeExports.jsxs("svg", { xmlns: "http://www.w3.org/2000/svg", width: "24", height: "24", viewBox: "0 0 24 24", fill: "none", stroke: "currentColor", strokeWidth: "2", strokeLinecap: "round", strokeLinejoin: "round", className, style, children: [
    /* @__PURE__ */ jsxRuntimeExports.jsx("rect", { width: "18", height: "18", x: "3", y: "3", rx: "2", ry: "2" }),
    /* @__PURE__ */ jsxRuntimeExports.jsx("circle", { cx: "9", cy: "9", r: "2" }),
    /* @__PURE__ */ jsxRuntimeExports.jsx("path", { d: "m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21" })
  ] });
  const Shield = ({ className, style }) => /* @__PURE__ */ jsxRuntimeExports.jsxs("svg", { xmlns: "http://www.w3.org/2000/svg", width: "24", height: "24", viewBox: "0 0 24 24", fill: "none", stroke: "currentColor", strokeWidth: "2", strokeLinecap: "round", strokeLinejoin: "round", className, style, children: [
    /* @__PURE__ */ jsxRuntimeExports.jsx("path", { d: "M20 13c0 5-3.5 7.5-8 7.5s-8-2.5-8-7.5c0-1.3.3-2.6.7-3.8C6.4 7.6 8.7 6.5 12 6.5s5.6 1.1 7.3 3.2c.4 1.2.7 2.5.7 3.8z" }),
    /* @__PURE__ */ jsxRuntimeExports.jsx("path", { d: "m9 12 2 2 4-4" })
  ] });
  const TrendingUp = ({ className, style }) => /* @__PURE__ */ jsxRuntimeExports.jsxs("svg", { xmlns: "http://www.w3.org/2000/svg", width: "24", height: "24", viewBox: "0 0 24 24", fill: "none", stroke: "currentColor", strokeWidth: "2", strokeLinecap: "round", strokeLinejoin: "round", className, style, children: [
    /* @__PURE__ */ jsxRuntimeExports.jsx("polyline", { points: "22,7 13.5,15.5 8.5,10.5 2,17" }),
    /* @__PURE__ */ jsxRuntimeExports.jsx("polyline", { points: "16,7 22,7 22,13" })
  ] });
  const BarChart3 = ({ className, style }) => /* @__PURE__ */ jsxRuntimeExports.jsxs("svg", { xmlns: "http://www.w3.org/2000/svg", width: "24", height: "24", viewBox: "0 0 24 24", fill: "none", stroke: "currentColor", strokeWidth: "2", strokeLinecap: "round", strokeLinejoin: "round", className, style, children: [
    /* @__PURE__ */ jsxRuntimeExports.jsx("path", { d: "M3 3v18h18" }),
    /* @__PURE__ */ jsxRuntimeExports.jsx("path", { d: "M18 17V9" }),
    /* @__PURE__ */ jsxRuntimeExports.jsx("path", { d: "M13 17V5" }),
    /* @__PURE__ */ jsxRuntimeExports.jsx("path", { d: "M8 17v-3" })
  ] });
  const Globe = ({ className, style }) => /* @__PURE__ */ jsxRuntimeExports.jsxs("svg", { xmlns: "http://www.w3.org/2000/svg", width: "24", height: "24", viewBox: "0 0 24 24", fill: "none", stroke: "currentColor", strokeWidth: "2", strokeLinecap: "round", strokeLinejoin: "round", className, style, children: [
    /* @__PURE__ */ jsxRuntimeExports.jsx("circle", { cx: "12", cy: "12", r: "10" }),
    /* @__PURE__ */ jsxRuntimeExports.jsx("line", { x1: "2", y1: "12", x2: "22", y2: "12" }),
    /* @__PURE__ */ jsxRuntimeExports.jsx("path", { d: "M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" })
  ] });
  const Search = ({ className, style }) => /* @__PURE__ */ jsxRuntimeExports.jsxs("svg", { xmlns: "http://www.w3.org/2000/svg", width: "24", height: "24", viewBox: "0 0 24 24", fill: "none", stroke: "currentColor", strokeWidth: "2", strokeLinecap: "round", strokeLinejoin: "round", className, style, children: [
    /* @__PURE__ */ jsxRuntimeExports.jsx("circle", { cx: "11", cy: "11", r: "8" }),
    /* @__PURE__ */ jsxRuntimeExports.jsx("path", { d: "m21 21-4.4-4.4" })
  ] });
  const Target = ({ className, style }) => /* @__PURE__ */ jsxRuntimeExports.jsxs("svg", { xmlns: "http://www.w3.org/2000/svg", width: "24", height: "24", viewBox: "0 0 24 24", fill: "none", stroke: "currentColor", strokeWidth: "2", strokeLinecap: "round", strokeLinejoin: "round", className, style, children: [
    /* @__PURE__ */ jsxRuntimeExports.jsx("circle", { cx: "12", cy: "12", r: "10" }),
    /* @__PURE__ */ jsxRuntimeExports.jsx("circle", { cx: "12", cy: "12", r: "6" }),
    /* @__PURE__ */ jsxRuntimeExports.jsx("circle", { cx: "12", cy: "12", r: "2" })
  ] });
  const GeminiApiWarning = ({ onNavigateToSettings }) => {
    return /* @__PURE__ */ jsxRuntimeExports.jsxs(
      "div",
      {
        className: "aca-gemini-warning",
        style: {
          backgroundColor: "#fff3cd",
          border: "1px solid #ffeaa7",
          borderRadius: "4px",
          padding: "12px 16px",
          margin: "0 0 20px 0",
          display: "flex",
          alignItems: "center",
          gap: "12px",
          boxShadow: "0 2px 4px rgba(0,0,0,0.1)"
        },
        children: [
          /* @__PURE__ */ jsxRuntimeExports.jsx(
            AlertTriangle,
            {
              style: {
                width: "20px",
                height: "20px",
                color: "#856404",
                flexShrink: 0
              }
            }
          ),
          /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { flex: 1 }, children: [
            /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: {
              fontWeight: "600",
              color: "#856404",
              marginBottom: "4px",
              fontSize: "14px"
            }, children: "Gemini API Key Required" }),
            /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: {
              color: "#856404",
              fontSize: "13px",
              lineHeight: "1.4"
            }, children: "AI Content Agent (ACA) requires a Google Gemini API key to function. Please configure your API key in Settings to enable content generation, idea creation, and all AI-powered features." })
          ] }),
          /* @__PURE__ */ jsxRuntimeExports.jsxs(
            "button",
            {
              onClick: onNavigateToSettings,
              style: {
                backgroundColor: "#856404",
                color: "white",
                border: "none",
                borderRadius: "4px",
                padding: "8px 12px",
                fontSize: "12px",
                fontWeight: "500",
                cursor: "pointer",
                display: "flex",
                alignItems: "center",
                gap: "6px",
                flexShrink: 0,
                transition: "background-color 0.2s"
              },
              onMouseOver: (e) => e.currentTarget.style.backgroundColor = "#6c5ce7",
              onMouseOut: (e) => e.currentTarget.style.backgroundColor = "#856404",
              children: [
                /* @__PURE__ */ jsxRuntimeExports.jsx(Settings, { style: { width: "14px", height: "14px" } }),
                "Go to Settings"
              ]
            }
          )
        ]
      }
    );
  };
  const NavItem = ({ icon, label, view, currentView, onClick, badge }) => {
    const isActive = currentView === view;
    return /* @__PURE__ */ jsxRuntimeExports.jsxs(
      "button",
      {
        onClick,
        className: `aca-nav-item ${isActive ? "active" : ""}`,
        style: {
          position: "relative",
          display: "flex",
          alignItems: "center",
          gap: "12px",
          padding: "12px 16px",
          margin: "2px 0",
          borderRadius: "8px",
          border: "none",
          background: isActive ? "linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%)" : "transparent",
          color: isActive ? "white" : "#64748b",
          fontSize: "14px",
          fontWeight: isActive ? "600" : "500",
          cursor: "pointer",
          transition: "all 0.2s ease",
          width: "100%",
          textAlign: "left",
          boxShadow: isActive ? "0 4px 12px rgba(59, 130, 246, 0.3)" : "none"
        },
        onMouseEnter: (e) => {
          if (!isActive) {
            e.currentTarget.style.background = "#f1f5f9";
            e.currentTarget.style.color = "#1e293b";
          }
        },
        onMouseLeave: (e) => {
          if (!isActive) {
            e.currentTarget.style.background = "transparent";
            e.currentTarget.style.color = "#64748b";
          }
        },
        children: [
          /* @__PURE__ */ jsxRuntimeExports.jsx("span", { style: {
            display: "flex",
            alignItems: "center",
            justifyContent: "center",
            width: "20px",
            height: "20px"
          }, children: icon }),
          /* @__PURE__ */ jsxRuntimeExports.jsx("span", { style: { flex: 1 }, children: label }),
          badge && /* @__PURE__ */ jsxRuntimeExports.jsx("span", { style: {
            background: badge === "NEW" ? "linear-gradient(135deg, #10b981, #059669)" : "linear-gradient(135deg, #f59e0b, #d97706)",
            color: "white",
            fontSize: "10px",
            fontWeight: "700",
            padding: "2px 6px",
            borderRadius: "4px",
            textTransform: "uppercase",
            letterSpacing: "0.5px"
          }, children: badge })
        ]
      }
    );
  };
  const Sidebar = ({ currentView, setView, isOpen, closeSidebar }) => {
    const handleNavigation = (view) => {
      setView(view);
      if (window.innerWidth <= 782) {
        closeSidebar();
      }
    };
    React.useEffect(() => {
      const handleEscKey = (event) => {
        if (event.key === "Escape" && isOpen && window.innerWidth <= 782) {
          closeSidebar();
        }
      };
      if (isOpen) {
        document.addEventListener("keydown", handleEscKey);
      }
      return () => {
        document.removeEventListener("keydown", handleEscKey);
      };
    }, [isOpen, closeSidebar]);
    return /* @__PURE__ */ jsxRuntimeExports.jsxs("aside", { className: `aca-sidebar ${isOpen ? "open" : ""}`, children: [
      /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-sidebar-header", style: {
        background: "linear-gradient(135deg, #667eea 0%, #764ba2 100%)",
        color: "white",
        padding: "24px 20px",
        borderRadius: "0 0 16px 16px",
        marginBottom: "24px",
        position: "relative",
        overflow: "hidden",
        boxShadow: "0 4px 20px rgba(102, 126, 234, 0.3)"
      }, children: [
        /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { position: "relative", zIndex: 2 }, children: [
          /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", alignItems: "center", gap: "14px", marginBottom: "10px" }, children: [
            /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: {
              width: "44px",
              height: "44px",
              background: "rgba(255,255,255,0.25)",
              borderRadius: "12px",
              display: "flex",
              alignItems: "center",
              justifyContent: "center",
              backdropFilter: "blur(15px)",
              border: "1px solid rgba(255,255,255,0.2)",
              boxShadow: "0 2px 8px rgba(0,0,0,0.1)"
            }, children: /* @__PURE__ */ jsxRuntimeExports.jsx(Brain, { style: { width: "22px", height: "22px", color: "white" } }) }),
            /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { children: [
              /* @__PURE__ */ jsxRuntimeExports.jsx("h1", { style: {
                fontSize: "18px",
                fontWeight: "700",
                margin: 0,
                textShadow: "0 2px 4px rgba(0,0,0,0.1)",
                color: "white"
              }, children: "AI Content Agent" }),
              /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: { fontSize: "11px", opacity: 0.8, fontWeight: "500" }, children: "AI-Powered Content Creation" })
            ] })
          ] }),
          /* @__PURE__ */ jsxRuntimeExports.jsx(
            "a",
            {
              href: "https://ademisler.com/en",
              target: "_blank",
              rel: "noopener noreferrer",
              style: {
                color: "rgba(255,255,255,0.8)",
                textDecoration: "none",
                fontSize: "12px",
                display: "flex",
                alignItems: "center",
                gap: "4px",
                transition: "color 0.2s"
              },
              onMouseEnter: (e) => e.currentTarget.style.color = "white",
              onMouseLeave: (e) => e.currentTarget.style.color = "rgba(255,255,255,0.8)",
              children: "by Adem Isler ↗"
            }
          )
        ] }),
        /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: {
          position: "absolute",
          top: "-20px",
          right: "-20px",
          width: "60px",
          height: "60px",
          background: "rgba(255,255,255,0.1)",
          borderRadius: "50%",
          zIndex: 1
        } })
      ] }),
      /* @__PURE__ */ jsxRuntimeExports.jsxs("nav", { className: "aca-sidebar-nav", style: { padding: "0 16px" }, children: [
        /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { marginBottom: "24px" }, children: [
          /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: {
            fontSize: "11px",
            fontWeight: "600",
            color: "#94a3b8",
            textTransform: "uppercase",
            letterSpacing: "0.5px",
            marginBottom: "8px",
            paddingLeft: "16px"
          }, children: "Main" }),
          /* @__PURE__ */ jsxRuntimeExports.jsx(
            NavItem,
            {
              icon: /* @__PURE__ */ jsxRuntimeExports.jsx(LayoutDashboard, {}),
              label: "Dashboard",
              view: "dashboard",
              currentView,
              onClick: () => handleNavigation("dashboard")
            }
          ),
          /* @__PURE__ */ jsxRuntimeExports.jsx(
            NavItem,
            {
              icon: /* @__PURE__ */ jsxRuntimeExports.jsx(BookOpen, {}),
              label: "Style Guide",
              view: "style-guide",
              currentView,
              onClick: () => handleNavigation("style-guide")
            }
          )
        ] }),
        /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { marginBottom: "24px" }, children: [
          /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: {
            fontSize: "11px",
            fontWeight: "600",
            color: "#94a3b8",
            textTransform: "uppercase",
            letterSpacing: "0.5px",
            marginBottom: "8px",
            paddingLeft: "16px"
          }, children: "Content Creation" }),
          /* @__PURE__ */ jsxRuntimeExports.jsx(
            NavItem,
            {
              icon: /* @__PURE__ */ jsxRuntimeExports.jsx(Lightbulb, {}),
              label: "Idea Board",
              view: "ideas",
              currentView,
              onClick: () => handleNavigation("ideas")
            }
          ),
          /* @__PURE__ */ jsxRuntimeExports.jsx(
            NavItem,
            {
              icon: /* @__PURE__ */ jsxRuntimeExports.jsx(FileText, {}),
              label: "Drafts",
              view: "drafts",
              currentView,
              onClick: () => handleNavigation("drafts")
            }
          ),
          /* @__PURE__ */ jsxRuntimeExports.jsx(
            NavItem,
            {
              icon: /* @__PURE__ */ jsxRuntimeExports.jsx(Calendar, {}),
              label: "Calendar",
              view: "calendar",
              currentView,
              onClick: () => handleNavigation("calendar")
            }
          ),
          /* @__PURE__ */ jsxRuntimeExports.jsx(
            NavItem,
            {
              icon: /* @__PURE__ */ jsxRuntimeExports.jsx(Send, {}),
              label: "Published",
              view: "published",
              currentView,
              onClick: () => handleNavigation("published")
            }
          )
        ] }),
        /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { marginBottom: "24px" }, children: [
          /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: {
            fontSize: "11px",
            fontWeight: "600",
            color: "#94a3b8",
            textTransform: "uppercase",
            letterSpacing: "0.5px",
            marginBottom: "8px",
            paddingLeft: "16px"
          }, children: "Pro Features" }),
          /* @__PURE__ */ jsxRuntimeExports.jsx(
            NavItem,
            {
              icon: /* @__PURE__ */ jsxRuntimeExports.jsx(Sparkles, {}),
              label: "Content Freshness",
              badge: "NEW",
              view: "content-freshness",
              currentView,
              onClick: () => handleNavigation("content-freshness")
            }
          )
        ] })
      ] }),
      /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: {
        paddingTop: "20px",
        borderTop: "1px solid #e2e8f0",
        marginTop: "auto",
        padding: "20px 16px 0 16px"
      }, children: [
        /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: {
          fontSize: "11px",
          fontWeight: "600",
          color: "#94a3b8",
          textTransform: "uppercase",
          letterSpacing: "0.5px",
          marginBottom: "8px",
          paddingLeft: "16px"
        }, children: "Settings" }),
        /* @__PURE__ */ jsxRuntimeExports.jsx(
          NavItem,
          {
            icon: /* @__PURE__ */ jsxRuntimeExports.jsx(Shield, {}),
            label: "License",
            view: "settings_license",
            currentView,
            onClick: () => handleNavigation("settings_license")
          }
        ),
        /* @__PURE__ */ jsxRuntimeExports.jsx(
          NavItem,
          {
            icon: /* @__PURE__ */ jsxRuntimeExports.jsx(Zap, {}),
            label: "Automation",
            view: "settings_automation",
            currentView,
            onClick: () => handleNavigation("settings_automation")
          }
        ),
        /* @__PURE__ */ jsxRuntimeExports.jsx(
          NavItem,
          {
            icon: /* @__PURE__ */ jsxRuntimeExports.jsx(Globe, {}),
            label: "Integrations",
            view: "settings_integrations",
            currentView,
            onClick: () => handleNavigation("settings_integrations")
          }
        ),
        /* @__PURE__ */ jsxRuntimeExports.jsx(
          NavItem,
          {
            icon: /* @__PURE__ */ jsxRuntimeExports.jsx(Target, {}),
            label: "Content & SEO",
            view: "settings_content",
            currentView,
            onClick: () => handleNavigation("settings_content")
          }
        ),
        /* @__PURE__ */ jsxRuntimeExports.jsx(
          NavItem,
          {
            icon: /* @__PURE__ */ jsxRuntimeExports.jsx(Brain, {}),
            label: "Advanced",
            view: "settings_advanced",
            currentView,
            onClick: () => handleNavigation("settings_advanced")
          }
        )
      ] })
    ] });
  };
  const IconMap = {
    BookOpen,
    Lightbulb,
    FileText,
    Send,
    Settings,
    Archive,
    Edit,
    Calendar,
    Sparkles,
    PlusCircle,
    Trash,
    Pencil
  };
  const ActivityLogItem = ({ log }) => {
    const IconComponent = IconMap[log.icon] || Info;
    const timestamp = new Date(log.timestamp);
    const timeString = timestamp.toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" });
    return /* @__PURE__ */ jsxRuntimeExports.jsx("div", { className: "aca-list-item", style: { padding: "12px 0", borderBottom: "1px solid #f0f0f1" }, children: /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", alignItems: "flex-start", gap: "12px" }, children: [
      /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: {
        width: "32px",
        height: "32px",
        backgroundColor: "#f6f7f7",
        borderRadius: "50%",
        display: "flex",
        alignItems: "center",
        justifyContent: "center",
        flexShrink: 0
      }, children: /* @__PURE__ */ jsxRuntimeExports.jsx(IconComponent, { style: { width: "16px", height: "16px", fill: "#0073aa" } }) }),
      /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { flex: 1, minWidth: 0 }, children: [
        /* @__PURE__ */ jsxRuntimeExports.jsx("p", { style: { margin: "0 0 4px 0", fontSize: "13px", color: "#23282d", lineHeight: "1.4" }, children: log.details }),
        /* @__PURE__ */ jsxRuntimeExports.jsx("p", { style: { margin: 0, fontSize: "12px", color: "#646970" }, children: timeString })
      ] })
    ] }) });
  };
  const ActivityLogList = ({ logs }) => {
    if (logs.length === 0) {
      return /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: {
        textAlign: "center",
        padding: "40px 20px",
        color: "#646970",
        fontSize: "13px"
      }, children: [
        /* @__PURE__ */ jsxRuntimeExports.jsx(Info, { style: {
          width: "24px",
          height: "24px",
          margin: "0 auto 10px auto",
          display: "block",
          fill: "#a7aaad"
        } }),
        "No activity yet. Start by creating your style guide or generating ideas!"
      ] });
    }
    const today = /* @__PURE__ */ new Date();
    const yesterday = /* @__PURE__ */ new Date();
    yesterday.setDate(yesterday.getDate() - 1);
    const todayStr = today.toDateString();
    const yesterdayStr = yesterday.toDateString();
    const groupedLogs = logs.reduce((acc, log) => {
      const logDate = new Date(log.timestamp);
      let key;
      if (logDate.toDateString() === todayStr) {
        key = "Today";
      } else if (logDate.toDateString() === yesterdayStr) {
        key = "Yesterday";
      } else {
        key = logDate.toLocaleDateString(void 0, {
          year: "numeric",
          month: "long",
          day: "numeric"
        });
      }
      if (!acc[key]) {
        acc[key] = [];
      }
      acc[key].push(log);
      return acc;
    }, {});
    return /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: { maxHeight: "400px", overflowY: "auto" }, children: Object.entries(groupedLogs).map(([dateKey, dateLogs]) => /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { marginBottom: "20px" }, children: [
      /* @__PURE__ */ jsxRuntimeExports.jsx("h4", { style: {
        margin: "0 0 10px 0",
        fontSize: "12px",
        fontWeight: "600",
        color: "#646970",
        textTransform: "uppercase",
        letterSpacing: "0.5px"
      }, children: dateKey }),
      /* @__PURE__ */ jsxRuntimeExports.jsx("div", { children: dateLogs.map((log) => /* @__PURE__ */ jsxRuntimeExports.jsx(ActivityLogItem, { log }, log.id)) })
    ] }, dateKey)) });
  };
  const ActionButton = reactExports.memo(({ icon, title, description, onClick, disabled, isLoading, loadingTitle }) => {
    return /* @__PURE__ */ jsxRuntimeExports.jsxs(
      "button",
      {
        onClick,
        disabled: disabled || isLoading,
        className: "aca-action-button",
        "aria-label": isLoading ? loadingTitle : title,
        children: [
          /* @__PURE__ */ jsxRuntimeExports.jsx("div", { className: "aca-action-icon", children: icon }),
          /* @__PURE__ */ jsxRuntimeExports.jsxs("h3", { className: "aca-action-title", children: [
            isLoading ? loadingTitle || "Loading..." : title,
            isLoading && /* @__PURE__ */ jsxRuntimeExports.jsx("span", { className: "aca-spinner", style: { marginLeft: "8px" } })
          ] }),
          /* @__PURE__ */ jsxRuntimeExports.jsx("p", { className: "aca-action-description", children: description })
        ]
      }
    );
  });
  const PipelineItem = reactExports.memo(({ icon, title, count, description, view, onNavigate }) => /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-stat-item", children: [
    /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-stat-info", children: [
      /* @__PURE__ */ jsxRuntimeExports.jsx("div", { className: "aca-stat-icon", children: icon }),
      /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { children: [
        /* @__PURE__ */ jsxRuntimeExports.jsx("h4", { className: "aca-stat-title", children: title }),
        /* @__PURE__ */ jsxRuntimeExports.jsxs("p", { className: "aca-stat-count", children: [
          count,
          " ",
          description
        ] })
      ] })
    ] }),
    /* @__PURE__ */ jsxRuntimeExports.jsx(
      "button",
      {
        onClick: () => onNavigate(view),
        className: "aca-button",
        "aria-label": `View ${title.toLowerCase()}`,
        children: "View"
      }
    )
  ] }));
  const Dashboard = ({
    stats,
    lastAnalyzed,
    activityLogs,
    onNavigate,
    onGenerateIdeas,
    onUpdateStyleGuide,
    isLoadingIdeas,
    isLoadingStyle
  }) => {
    const isStyleGuideReady = !!lastAnalyzed;
    return /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-fade-in", children: [
      /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-welcome-section", style: {
        background: "linear-gradient(135deg, #667eea 0%, #764ba2 100%)",
        borderRadius: "12px",
        padding: "30px",
        marginBottom: "30px",
        color: "white",
        position: "relative",
        overflow: "hidden"
      }, children: [
        /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { position: "relative", zIndex: 2 }, children: [
          /* @__PURE__ */ jsxRuntimeExports.jsx("h1", { style: {
            fontSize: "28px",
            fontWeight: "700",
            marginBottom: "8px",
            textShadow: "0 2px 4px rgba(0,0,0,0.1)",
            color: "white"
          }, children: "Welcome to AI Content Agent" }),
          /* @__PURE__ */ jsxRuntimeExports.jsx("p", { style: {
            fontSize: "16px",
            opacity: 0.9,
            marginBottom: "20px",
            maxWidth: "600px"
          }, children: "Your intelligent content creation companion powered by Google Gemini AI" }),
          /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", gap: "15px", flexWrap: "wrap" }, children: [
            /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", alignItems: "center", gap: "6px" }, children: [
              /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: { width: "8px", height: "8px", backgroundColor: "#4ade80", borderRadius: "50%" } }),
              /* @__PURE__ */ jsxRuntimeExports.jsx("span", { style: { fontSize: "14px", opacity: 0.9 }, children: "AI-Powered" })
            ] }),
            /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", alignItems: "center", gap: "6px" }, children: [
              /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: { width: "8px", height: "8px", backgroundColor: "#60a5fa", borderRadius: "50%" } }),
              /* @__PURE__ */ jsxRuntimeExports.jsx("span", { style: { fontSize: "14px", opacity: 0.9 }, children: "Automated Workflow" })
            ] }),
            /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", alignItems: "center", gap: "6px" }, children: [
              /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: { width: "8px", height: "8px", backgroundColor: "#f59e0b", borderRadius: "50%" } }),
              /* @__PURE__ */ jsxRuntimeExports.jsx("span", { style: { fontSize: "14px", opacity: 0.9 }, children: "SEO Optimized" })
            ] })
          ] })
        ] }),
        /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: {
          position: "absolute",
          top: "-50px",
          right: "-50px",
          width: "150px",
          height: "150px",
          background: "rgba(255,255,255,0.1)",
          borderRadius: "50%",
          zIndex: 1
        } }),
        /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: {
          position: "absolute",
          bottom: "-30px",
          left: "-30px",
          width: "100px",
          height: "100px",
          background: "rgba(255,255,255,0.05)",
          borderRadius: "50%",
          zIndex: 1
        } })
      ] }),
      !isStyleGuideReady && /* @__PURE__ */ jsxRuntimeExports.jsx("div", { className: "aca-alert info", children: /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", alignItems: "center", justifyContent: "space-between", flexWrap: "wrap", gap: "15px" }, children: [
        /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", alignItems: "center" }, children: [
          /* @__PURE__ */ jsxRuntimeExports.jsx(Lightbulb, { style: { width: "24px", height: "24px", marginRight: "12px", flexShrink: 0 } }),
          /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { children: [
            /* @__PURE__ */ jsxRuntimeExports.jsx("h4", { style: { margin: "0 0 5px 0", fontWeight: "600", color: "#0073aa" }, children: "Get Started with AI Content Agent (ACA)" }),
            /* @__PURE__ */ jsxRuntimeExports.jsx("p", { style: { margin: 0, fontSize: "13px" }, children: "Create your Style Guide first to enable all features and generate on-brand content." })
          ] })
        ] }),
        /* @__PURE__ */ jsxRuntimeExports.jsx(
          "button",
          {
            onClick: () => onNavigate("style-guide"),
            className: "aca-button large",
            style: { flexShrink: 0 },
            children: "Create Style Guide"
          }
        )
      ] }) }),
      /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-grid aca-grid-2", style: { marginBottom: "30px" }, children: [
        /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-card", style: {
          background: "linear-gradient(145deg, #f8fafc 0%, #f1f5f9 100%)",
          border: "1px solid #e2e8f0",
          boxShadow: "0 4px 6px -1px rgba(0, 0, 0, 0.1)"
        }, children: [
          /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-card-header", style: { borderBottom: "1px solid #e2e8f0", paddingBottom: "15px" }, children: [
            /* @__PURE__ */ jsxRuntimeExports.jsxs("h2", { className: "aca-card-title", style: {
              display: "flex",
              alignItems: "center",
              gap: "8px",
              color: "#1e293b"
            }, children: [
              /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: {
                width: "32px",
                height: "32px",
                background: "linear-gradient(135deg, #3b82f6, #1d4ed8)",
                borderRadius: "8px",
                display: "flex",
                alignItems: "center",
                justifyContent: "center"
              }, children: /* @__PURE__ */ jsxRuntimeExports.jsx("span", { style: { color: "white", fontSize: "16px" }, children: "⚡" }) }),
              "Quick Actions"
            ] }),
            /* @__PURE__ */ jsxRuntimeExports.jsx("p", { style: { margin: "8px 0 0 0", color: "#64748b", fontSize: "14px" }, children: "Get started with AI-powered content creation" })
          ] }),
          /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-grid aca-grid-2", style: { marginTop: "20px" }, children: [
            /* @__PURE__ */ jsxRuntimeExports.jsx(
              ActionButton,
              {
                icon: /* @__PURE__ */ jsxRuntimeExports.jsx(Lightbulb, {}),
                title: "Generate Ideas",
                description: "Create fresh content ideas based on your style guide",
                onClick: onGenerateIdeas,
                disabled: !isStyleGuideReady,
                isLoading: isLoadingIdeas,
                loadingTitle: "Generating Ideas..."
              }
            ),
            /* @__PURE__ */ jsxRuntimeExports.jsx(
              ActionButton,
              {
                icon: /* @__PURE__ */ jsxRuntimeExports.jsx(BookOpen, {}),
                title: "Update Style Guide",
                description: "Analyze your site content to refresh your style guide",
                onClick: onUpdateStyleGuide,
                isLoading: isLoadingStyle,
                loadingTitle: "Analyzing Style..."
              }
            )
          ] })
        ] }),
        /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-card", style: {
          background: "linear-gradient(145deg, #fefefe 0%, #f8f9fa 100%)",
          border: "1px solid #e2e8f0",
          boxShadow: "0 4px 6px -1px rgba(0, 0, 0, 0.1)"
        }, children: [
          /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-card-header", style: { borderBottom: "1px solid #e2e8f0", paddingBottom: "15px" }, children: [
            /* @__PURE__ */ jsxRuntimeExports.jsxs("h2", { className: "aca-card-title", style: {
              display: "flex",
              alignItems: "center",
              gap: "8px",
              color: "#1e293b"
            }, children: [
              /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: {
                width: "32px",
                height: "32px",
                background: "linear-gradient(135deg, #10b981, #059669)",
                borderRadius: "8px",
                display: "flex",
                alignItems: "center",
                justifyContent: "center"
              }, children: /* @__PURE__ */ jsxRuntimeExports.jsx("span", { style: { color: "white", fontSize: "16px" }, children: "📊" }) }),
              "Content Pipeline"
            ] }),
            /* @__PURE__ */ jsxRuntimeExports.jsx("p", { style: { margin: "8px 0 0 0", color: "#64748b", fontSize: "14px" }, children: "Track your content creation progress" })
          ] }),
          /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", flexDirection: "column", gap: "10px", marginTop: "20px" }, children: [
            /* @__PURE__ */ jsxRuntimeExports.jsx(
              PipelineItem,
              {
                icon: /* @__PURE__ */ jsxRuntimeExports.jsx(Lightbulb, {}),
                title: "Ideas",
                count: stats.ideas,
                description: "content ideas",
                view: "ideas",
                onNavigate
              }
            ),
            /* @__PURE__ */ jsxRuntimeExports.jsx(
              PipelineItem,
              {
                icon: /* @__PURE__ */ jsxRuntimeExports.jsx(FileText, {}),
                title: "Drafts",
                count: stats.drafts,
                description: "draft posts",
                view: "drafts",
                onNavigate
              }
            ),
            /* @__PURE__ */ jsxRuntimeExports.jsx(
              PipelineItem,
              {
                icon: /* @__PURE__ */ jsxRuntimeExports.jsx(Send, {}),
                title: "Published",
                count: stats.published,
                description: "published posts",
                view: "published",
                onNavigate
              }
            )
          ] })
        ] })
      ] }),
      isStyleGuideReady && /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-card", children: [
        /* @__PURE__ */ jsxRuntimeExports.jsx("div", { className: "aca-card-header", children: /* @__PURE__ */ jsxRuntimeExports.jsxs("h2", { className: "aca-card-title", children: [
          /* @__PURE__ */ jsxRuntimeExports.jsx(CheckCircle, { style: { width: "20px", height: "20px", marginRight: "8px", fill: "#00a32a" } }),
          "Style Guide Active"
        ] }) }),
        /* @__PURE__ */ jsxRuntimeExports.jsxs("p", { style: { margin: "0 0 15px 0", color: "#646970" }, children: [
          "Last analyzed: ",
          new Date(lastAnalyzed).toLocaleDateString(),
          " at ",
          new Date(lastAnalyzed).toLocaleTimeString()
        ] }),
        /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", gap: "10px", flexWrap: "wrap" }, children: [
          /* @__PURE__ */ jsxRuntimeExports.jsx(
            "button",
            {
              onClick: () => onNavigate("style-guide"),
              className: "aca-button secondary",
              children: "View Style Guide"
            }
          ),
          /* @__PURE__ */ jsxRuntimeExports.jsx(
            "button",
            {
              onClick: onUpdateStyleGuide,
              className: "aca-button secondary",
              disabled: isLoadingStyle,
              children: isLoadingStyle ? "Updating..." : "Refresh Analysis"
            }
          )
        ] })
      ] }),
      stats.contentFreshness && /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-card", style: { marginBottom: "30px" }, children: [
        /* @__PURE__ */ jsxRuntimeExports.jsx("div", { className: "aca-card-header", children: /* @__PURE__ */ jsxRuntimeExports.jsxs("h2", { className: "aca-card-title", children: [
          /* @__PURE__ */ jsxRuntimeExports.jsx(Sparkles, { style: { marginRight: "8px" } }),
          "Content Freshness (Pro)"
        ] }) }),
        /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-grid aca-grid-4", children: [
          /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { textAlign: "center", padding: "15px" }, children: [
            /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: { fontSize: "24px", fontWeight: "bold", color: "#0073aa", marginBottom: "5px" }, children: stats.contentFreshness.total }),
            /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: { fontSize: "13px", color: "#666" }, children: "Total Posts" })
          ] }),
          /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { textAlign: "center", padding: "15px" }, children: [
            /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: { fontSize: "24px", fontWeight: "bold", color: "#28a745", marginBottom: "5px" }, children: stats.contentFreshness.analyzed }),
            /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: { fontSize: "13px", color: "#666" }, children: "Analyzed" })
          ] }),
          /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { textAlign: "center", padding: "15px" }, children: [
            /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: { fontSize: "24px", fontWeight: "bold", color: stats.contentFreshness.needsUpdate > 0 ? "#dc3545" : "#28a745", marginBottom: "5px" }, children: stats.contentFreshness.needsUpdate }),
            /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { fontSize: "13px", color: "#666", display: "flex", alignItems: "center", justifyContent: "center", gap: "4px" }, children: [
              stats.contentFreshness.needsUpdate > 0 && /* @__PURE__ */ jsxRuntimeExports.jsx(AlertTriangle, { style: { width: "14px", height: "14px" } }),
              "Need Updates"
            ] })
          ] }),
          /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { textAlign: "center", padding: "15px" }, children: [
            /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: {
              fontSize: "24px",
              fontWeight: "bold",
              color: stats.contentFreshness.averageScore >= 80 ? "#28a745" : stats.contentFreshness.averageScore >= 60 ? "#ffc107" : "#dc3545",
              marginBottom: "5px"
            }, children: stats.contentFreshness.averageScore }),
            /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: { fontSize: "13px", color: "#666" }, children: "Avg. Score" })
          ] })
        ] }),
        /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: { marginTop: "15px", textAlign: "center" }, children: /* @__PURE__ */ jsxRuntimeExports.jsxs(
          "button",
          {
            onClick: () => onNavigate("content-freshness"),
            className: "aca-button primary",
            style: { display: "inline-flex", alignItems: "center", gap: "6px" },
            children: [
              /* @__PURE__ */ jsxRuntimeExports.jsx(Sparkles, { style: { width: "16px", height: "16px" } }),
              "Manage Content Freshness"
            ]
          }
        ) })
      ] }),
      /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-card", children: [
        /* @__PURE__ */ jsxRuntimeExports.jsx("div", { className: "aca-card-header", children: /* @__PURE__ */ jsxRuntimeExports.jsx("h2", { className: "aca-card-title", children: "Recent Activity" }) }),
        /* @__PURE__ */ jsxRuntimeExports.jsx(ActivityLogList, { logs: activityLogs.slice(0, 10) })
      ] })
    ] });
  };
  const PREDEFINED_TONES = ["Friendly", "Conversational", "Formal", "Professional", "Technical", "Informative", "Witty", "Humorous"];
  const sentenceStructureMap = {
    0: "Primarily short and direct sentences",
    25: "Mostly short sentences with some variation",
    50: "Mix of short, punchy sentences and longer, more descriptive ones",
    75: "Mostly longer sentences with some short ones for impact",
    100: "Primarily complex sentences with multiple clauses"
  };
  const getClosestSliderValue = (description) => {
    if (!description) return 50;
    const entry = Object.entries(sentenceStructureMap).find(([, value]) => value === description);
    return entry ? parseInt(entry[0], 10) : 50;
  };
  const StyleGuideManager = ({ styleGuide, onAnalyze, onSaveStyleGuide, isLoading }) => {
    const [editableGuide, setEditableGuide] = reactExports.useState(styleGuide);
    const [isDirty, setIsDirty] = reactExports.useState(false);
    const [isSaving, setIsSaving] = reactExports.useState(false);
    const [selectedTones, setSelectedTones] = reactExports.useState(/* @__PURE__ */ new Set());
    const [sentenceSliderValue, setSentenceSliderValue] = reactExports.useState(50);
    reactExports.useEffect(() => {
      setEditableGuide(styleGuide);
      if (styleGuide) {
        const tones = styleGuide.tone ? styleGuide.tone.split(",").map((t) => t.trim()).filter(Boolean) : [];
        setSelectedTones(new Set(tones));
        setSentenceSliderValue(getClosestSliderValue(styleGuide.sentenceStructure));
      }
      setIsDirty(false);
    }, [styleGuide]);
    const markDirty = () => {
      if (!isDirty) {
        setIsDirty(true);
      }
    };
    const handleToneToggle = (tone) => {
      const newTones = new Set(selectedTones);
      if (newTones.has(tone)) {
        newTones.delete(tone);
      } else {
        newTones.add(tone);
      }
      setSelectedTones(newTones);
      if (editableGuide) {
        setEditableGuide({ ...editableGuide, tone: Array.from(newTones).join(", ") });
      }
      markDirty();
    };
    const handleSliderChange = (e) => {
      const value = parseInt(e.target.value, 10);
      setSentenceSliderValue(value);
      if (editableGuide) {
        const description = sentenceStructureMap[value] || sentenceStructureMap[50];
        setEditableGuide({ ...editableGuide, sentenceStructure: description });
      }
      markDirty();
    };
    const handleFieldChange = (field, value) => {
      if (editableGuide) {
        setEditableGuide({ ...editableGuide, [field]: value });
        markDirty();
      }
    };
    const handleSave = () => {
      if (editableGuide && isDirty) {
        setIsSaving(true);
        setTimeout(() => {
          onSaveStyleGuide(editableGuide);
          setIsSaving(false);
          setIsDirty(false);
        }, 700);
      }
    };
    return /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-fade-in", children: [
      /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: {
        background: "linear-gradient(135deg, #ef4444 0%, #dc2626 100%)",
        borderRadius: "12px",
        padding: "30px",
        marginBottom: "30px",
        color: "white",
        position: "relative",
        overflow: "hidden"
      }, children: [
        /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { position: "relative", zIndex: 2 }, children: [
          /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", alignItems: "center", gap: "12px", marginBottom: "12px" }, children: [
            /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: {
              width: "48px",
              height: "48px",
              background: "rgba(255,255,255,0.2)",
              borderRadius: "12px",
              display: "flex",
              alignItems: "center",
              justifyContent: "center",
              backdropFilter: "blur(10px)"
            }, children: /* @__PURE__ */ jsxRuntimeExports.jsx(BookOpen, { style: { width: "24px", height: "24px" } }) }),
            /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { children: [
              /* @__PURE__ */ jsxRuntimeExports.jsx("h1", { style: {
                fontSize: "28px",
                fontWeight: "700",
                margin: 0,
                textShadow: "0 2px 4px rgba(0,0,0,0.1)",
                color: "white"
              }, children: "Style Guide" }),
              /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: { fontSize: "16px", opacity: 0.9, marginTop: "4px" }, children: "Define your brand's unique voice" })
            ] })
          ] }),
          /* @__PURE__ */ jsxRuntimeExports.jsx("p", { style: {
            fontSize: "14px",
            opacity: 0.85,
            margin: 0,
            maxWidth: "600px",
            lineHeight: "1.5"
          }, children: "Define your brand's unique voice and writing style. The AI analyzes your content to keep this guide current and relevant." }),
          /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", gap: "20px", marginTop: "20px", flexWrap: "wrap" }, children: [
            /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", alignItems: "center", gap: "6px" }, children: [
              /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: { width: "8px", height: "8px", backgroundColor: "#fbbf24", borderRadius: "50%" } }),
              /* @__PURE__ */ jsxRuntimeExports.jsx("span", { style: { fontSize: "14px", opacity: 0.9 }, children: "AI-Powered Analysis" })
            ] }),
            /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", alignItems: "center", gap: "6px" }, children: [
              /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: { width: "8px", height: "8px", backgroundColor: "#4ade80", borderRadius: "50%" } }),
              /* @__PURE__ */ jsxRuntimeExports.jsx("span", { style: { fontSize: "14px", opacity: 0.9 }, children: "Brand Voice Definition" })
            ] }),
            /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", alignItems: "center", gap: "6px" }, children: [
              /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: { width: "8px", height: "8px", backgroundColor: "#60a5fa", borderRadius: "50%" } }),
              /* @__PURE__ */ jsxRuntimeExports.jsx("span", { style: { fontSize: "14px", opacity: 0.9 }, children: "Auto-Updated" })
            ] })
          ] })
        ] }),
        /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: {
          position: "absolute",
          top: "-30px",
          right: "-30px",
          width: "120px",
          height: "120px",
          background: "rgba(255,255,255,0.1)",
          borderRadius: "50%",
          zIndex: 1
        } }),
        /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: {
          position: "absolute",
          bottom: "-20px",
          left: "-20px",
          width: "80px",
          height: "80px",
          background: "rgba(255,255,255,0.05)",
          borderRadius: "50%",
          zIndex: 1
        } })
      ] }),
      /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-card", children: [
        /* @__PURE__ */ jsxRuntimeExports.jsx("div", { className: "aca-card-header", children: /* @__PURE__ */ jsxRuntimeExports.jsxs("h2", { className: "aca-card-title", children: [
          /* @__PURE__ */ jsxRuntimeExports.jsx(RefreshCw, { className: "aca-nav-item-icon" }),
          "Content Analysis"
        ] }) }),
        /* @__PURE__ */ jsxRuntimeExports.jsx("p", { className: "aca-page-description", children: "The AI periodically scans your published content to understand your writing style and automatically updates your guide." }),
        /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-stat-item", children: [
          /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-stat-info", children: [
            /* @__PURE__ */ jsxRuntimeExports.jsx("div", { className: "aca-stat-icon", children: /* @__PURE__ */ jsxRuntimeExports.jsx(CheckCircle, {}) }),
            /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { children: [
              /* @__PURE__ */ jsxRuntimeExports.jsx("h4", { className: "aca-stat-title", children: "Analysis Status" }),
              /* @__PURE__ */ jsxRuntimeExports.jsx("p", { className: "aca-stat-count", children: styleGuide?.lastAnalyzed ? `Last updated: ${new Date(styleGuide.lastAnalyzed).toLocaleDateString()} at ${new Date(styleGuide.lastAnalyzed).toLocaleTimeString()}` : "Never analyzed" })
            ] })
          ] }),
          /* @__PURE__ */ jsxRuntimeExports.jsxs(
            "button",
            {
              onClick: onAnalyze,
              disabled: isLoading,
              className: "aca-button large",
              children: [
                isLoading && /* @__PURE__ */ jsxRuntimeExports.jsx("span", { className: "aca-spinner" }),
                isLoading ? "Analyzing..." : "Analyze Content"
              ]
            }
          )
        ] })
      ] }),
      /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-card", children: [
        /* @__PURE__ */ jsxRuntimeExports.jsx("div", { className: "aca-card-header", children: /* @__PURE__ */ jsxRuntimeExports.jsxs("h2", { className: "aca-card-title", children: [
          /* @__PURE__ */ jsxRuntimeExports.jsx(BookOpen, { className: "aca-nav-item-icon" }),
          "Your Style Guide"
        ] }) }),
        /* @__PURE__ */ jsxRuntimeExports.jsx("p", { className: "aca-page-description", children: "Review and customize the AI-generated style guide below. These settings will influence all future content generation." }),
        editableGuide ? /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-grid", style: { gap: "30px" }, children: [
          /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-form-group", children: [
            /* @__PURE__ */ jsxRuntimeExports.jsx("label", { className: "aca-label", children: "Writing Tone & Voice" }),
            /* @__PURE__ */ jsxRuntimeExports.jsx("div", { className: "aca-grid aca-grid-3", style: { padding: "20px", background: "#f6f7f7", borderRadius: "4px", border: "1px solid #ccd0d4" }, children: PREDEFINED_TONES.map((tone) => /* @__PURE__ */ jsxRuntimeExports.jsx(
              "button",
              {
                onClick: () => handleToneToggle(tone),
                className: `aca-button ${selectedTones.has(tone) ? "" : "secondary"}`,
                style: { textAlign: "center" },
                children: tone
              },
              tone
            )) })
          ] }),
          /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-form-group", children: [
            /* @__PURE__ */ jsxRuntimeExports.jsx("label", { className: "aca-label", children: "Sentence Structure Preference" }),
            /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: {
              padding: "20px",
              background: "#f6f7f7",
              borderRadius: "4px",
              border: "1px solid #ccd0d4"
            }, children: [
              /* @__PURE__ */ jsxRuntimeExports.jsx(
                "input",
                {
                  type: "range",
                  min: "0",
                  max: "100",
                  step: "25",
                  value: sentenceSliderValue,
                  onChange: handleSliderChange,
                  className: "aca-input",
                  style: {
                    width: "100%",
                    height: "8px",
                    background: "#ddd",
                    borderRadius: "4px",
                    appearance: "none",
                    cursor: "pointer",
                    outline: "none"
                  }
                }
              ),
              /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-grid aca-grid-3", style: { fontSize: "11px", color: "#646970", marginTop: "12px", fontWeight: "500", textAlign: "center" }, children: [
                /* @__PURE__ */ jsxRuntimeExports.jsx("span", { children: "Short & Simple" }),
                /* @__PURE__ */ jsxRuntimeExports.jsx("span", { children: "Balanced Mix" }),
                /* @__PURE__ */ jsxRuntimeExports.jsx("span", { children: "Long & Complex" })
              ] }),
              /* @__PURE__ */ jsxRuntimeExports.jsx("div", { className: "aca-alert info", style: { marginTop: "15px", textAlign: "center", fontWeight: "500" }, children: sentenceStructureMap[sentenceSliderValue] })
            ] })
          ] }),
          /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-grid aca-grid-2", children: [
            /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-form-group", children: [
              /* @__PURE__ */ jsxRuntimeExports.jsx("label", { className: "aca-label", children: "Paragraph Length Style" }),
              /* @__PURE__ */ jsxRuntimeExports.jsx(
                "input",
                {
                  type: "text",
                  value: editableGuide.paragraphLength,
                  onChange: (e) => handleFieldChange("paragraphLength", e.target.value),
                  className: "aca-input",
                  placeholder: "e.g., Short paragraphs, 2-3 sentences"
                }
              )
            ] }),
            /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-form-group", children: [
              /* @__PURE__ */ jsxRuntimeExports.jsx("label", { className: "aca-label", children: "Formatting Preferences" }),
              /* @__PURE__ */ jsxRuntimeExports.jsx(
                "input",
                {
                  type: "text",
                  value: editableGuide.formattingStyle,
                  onChange: (e) => handleFieldChange("formattingStyle", e.target.value),
                  className: "aca-input",
                  placeholder: "e.g., Use bullet points, bold headings"
                }
              )
            ] })
          ] })
        ] }) : /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-card", style: {
          textAlign: "center",
          border: "2px dashed #ccd0d4",
          background: "#f9f9f9",
          padding: "40px 20px"
        }, children: [
          /* @__PURE__ */ jsxRuntimeExports.jsx(BookOpen, { style: { width: "48px", height: "48px", marginBottom: "15px", fill: "#a7aaad" } }),
          /* @__PURE__ */ jsxRuntimeExports.jsx("h4", { className: "aca-card-title", children: "No Style Guide Yet" }),
          /* @__PURE__ */ jsxRuntimeExports.jsx("p", { className: "aca-page-description", children: "Run your first content analysis to generate your personalized style guide." })
        ] }),
        editableGuide && /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: {
          marginTop: "30px",
          paddingTop: "20px",
          borderTop: "1px solid #f0f0f1",
          display: "flex",
          justifyContent: "space-between",
          alignItems: "center",
          gap: "15px"
        }, children: [
          isDirty && /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-alert warning", style: {
            display: "flex",
            alignItems: "center",
            fontSize: "13px",
            fontWeight: "500",
            gap: "8px",
            padding: "8px 12px",
            margin: 0
          }, children: [
            /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: {
              width: "8px",
              height: "8px",
              borderRadius: "50%",
              background: "#dba617"
            } }),
            "Unsaved changes"
          ] }),
          /* @__PURE__ */ jsxRuntimeExports.jsxs(
            "button",
            {
              onClick: handleSave,
              disabled: !isDirty || isSaving,
              className: "aca-button large",
              style: { marginLeft: "auto" },
              children: [
                isSaving && /* @__PURE__ */ jsxRuntimeExports.jsx("span", { className: "aca-spinner" }),
                isSaving ? "Saving..." : "Save Changes"
              ]
            }
          )
        ] })
      ] })
    ] });
  };
  const sourceStyles = {
    "ai": { background: "#e6f7e6", color: "#0a5d0a", borderColor: "#28a745" },
    "search-console": { background: "#e3f2fd", color: "#0d47a1", borderColor: "#2196f3" },
    "similar": { background: "#f3e5f5", color: "#4a148c", borderColor: "#9c27b0" },
    "manual": { background: "#f6f7f7", color: "#646970", borderColor: "#8c8f94" }
  };
  const sourceNames = {
    "ai": "AI Generated",
    "search-console": "Search Console",
    "similar": "Similar Idea",
    "manual": "Manual Entry"
  };
  const IdeaCard = ({ idea, onCreateDraft, onArchive, onUpdateTitle, onGenerateSimilar, isLoading, isGeneratingSimilar }) => {
    const [isEditing, setIsEditing] = reactExports.useState(false);
    const [title, setTitle] = reactExports.useState(idea.title);
    const inputRef = reactExports.useRef(null);
    reactExports.useEffect(() => {
      if (isEditing) {
        inputRef.current?.focus();
        inputRef.current?.select();
      }
    }, [isEditing]);
    const handleSave = () => {
      if (title.trim() && title.trim() !== idea.title) {
        onUpdateTitle(idea.id, title.trim());
      } else {
        setTitle(idea.title);
      }
      setIsEditing(false);
    };
    const handleKeyDown = (e) => {
      if (e.key === "Enter") {
        handleSave();
      } else if (e.key === "Escape") {
        setTitle(idea.title);
        setIsEditing(false);
      }
    };
    const sourceStyle = sourceStyles[idea.source];
    return /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: `aca-card ${isLoading ? "loading" : ""}`, style: { margin: 0, minHeight: "140px" }, children: [
      /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: { flexGrow: 1, marginBottom: "15px" }, children: isEditing ? /* @__PURE__ */ jsxRuntimeExports.jsx(
        "input",
        {
          ref: inputRef,
          type: "text",
          value: title,
          onChange: (e) => setTitle(e.target.value),
          onBlur: handleSave,
          onKeyDown: handleKeyDown,
          className: "aca-input",
          style: { fontSize: "16px", fontWeight: "500" }
        }
      ) : /* @__PURE__ */ jsxRuntimeExports.jsxs(
        "div",
        {
          onClick: () => setIsEditing(true),
          className: "aca-action-button",
          style: {
            padding: "8px 12px",
            margin: "-8px -12px",
            border: "1px solid transparent",
            display: "flex",
            alignItems: "center",
            gap: "8px",
            lineHeight: "1.4",
            fontSize: "16px",
            fontWeight: "500"
          },
          title: "Click to edit title",
          children: [
            /* @__PURE__ */ jsxRuntimeExports.jsx(Edit, { style: { width: "14px", height: "14px", color: "#0073aa" } }),
            idea.title
          ]
        }
      ) }),
      /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-list-item", style: { padding: "15px 0 0 0", margin: 0 }, children: [
        /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: {
          fontSize: "11px",
          fontWeight: "600",
          padding: "6px 12px",
          borderRadius: "4px",
          border: "1px solid",
          background: sourceStyle.background,
          color: sourceStyle.color,
          borderColor: sourceStyle.borderColor,
          flexShrink: 0
        }, children: sourceNames[idea.source] }),
        /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", alignItems: "center", gap: "8px", flexShrink: 0 }, children: [
          /* @__PURE__ */ jsxRuntimeExports.jsxs(
            "button",
            {
              onClick: () => onGenerateSimilar(idea),
              disabled: isGeneratingSimilar || isLoading,
              className: "aca-button secondary",
              style: { fontSize: "12px", padding: "6px 12px" },
              title: "Generate similar ideas",
              children: [
                isGeneratingSimilar ? /* @__PURE__ */ jsxRuntimeExports.jsx("span", { className: "aca-spinner", style: { width: "14px", height: "14px" } }) : /* @__PURE__ */ jsxRuntimeExports.jsx(Sparkles, { style: { width: "14px", height: "14px" } }),
                "Similar"
              ]
            }
          ),
          /* @__PURE__ */ jsxRuntimeExports.jsxs(
            "button",
            {
              onClick: () => onCreateDraft(idea),
              disabled: isLoading || isGeneratingSimilar,
              className: "aca-button",
              style: {
                fontSize: "12px",
                padding: "6px 16px",
                background: isLoading ? "#f6f7f7" : "#00a32a",
                borderColor: isLoading ? "#ccd0d4" : "#00a32a",
                color: isLoading ? "#a7aaad" : "#ffffff",
                cursor: isLoading ? "wait" : "pointer",
                minWidth: "120px",
                position: "relative",
                overflow: "hidden"
              },
              title: isLoading ? "AI is generating your draft..." : "Create draft from this idea",
              children: [
                isLoading ? /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", alignItems: "center", gap: "6px" }, children: [
                  /* @__PURE__ */ jsxRuntimeExports.jsx("span", { className: "aca-spinner", style: { width: "14px", height: "14px" } }),
                  /* @__PURE__ */ jsxRuntimeExports.jsx("span", { style: { fontSize: "11px" }, children: "Creating..." })
                ] }) : /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", alignItems: "center", gap: "6px" }, children: [
                  /* @__PURE__ */ jsxRuntimeExports.jsx(Sparkles, { style: { width: "14px", height: "14px" } }),
                  "Create Draft"
                ] }),
                isLoading && /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: {
                  position: "absolute",
                  bottom: 0,
                  left: 0,
                  width: "100%",
                  height: "2px",
                  background: "rgba(0, 163, 42, 0.3)",
                  overflow: "hidden"
                }, children: /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: {
                  width: "30%",
                  height: "100%",
                  background: "#00a32a",
                  animation: "aca-progress-slide 2s infinite linear"
                } }) })
              ]
            }
          ),
          /* @__PURE__ */ jsxRuntimeExports.jsx(
            "button",
            {
              onClick: () => onArchive(idea.id),
              className: "aca-button secondary",
              style: { fontSize: "12px", padding: "6px", minWidth: "auto", color: "#646970" },
              title: "Archive idea",
              children: /* @__PURE__ */ jsxRuntimeExports.jsx(Trash, { style: { width: "14px", height: "14px" } })
            }
          )
        ] })
      ] })
    ] });
  };
  const IdeaBoard = ({
    ideas,
    onGenerate,
    onCreateDraft,
    onArchive,
    onDeleteIdea,
    onRestoreIdea,
    onUpdateTitle,
    onGenerateSimilar,
    onAddIdea,
    isLoading,
    isLoadingDraft
  }) => {
    const [newIdeaTitle, setNewIdeaTitle] = reactExports.useState("");
    const handleAddIdeaSubmit = (e) => {
      e.preventDefault();
      if (newIdeaTitle.trim()) {
        onAddIdea(newIdeaTitle);
        setNewIdeaTitle("");
      }
    };
    const activeIdeas = ideas.filter((idea) => idea.status === "active");
    const archivedIdeas = ideas.filter((idea) => idea.status === "archived");
    return /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-fade-in", children: [
      /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: {
        background: "linear-gradient(135deg, #f59e0b 0%, #d97706 100%)",
        borderRadius: "12px",
        padding: "30px",
        marginBottom: "30px",
        color: "white",
        position: "relative",
        overflow: "hidden"
      }, children: [
        /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: { position: "relative", zIndex: 2 }, children: /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", justifyContent: "space-between", alignItems: "flex-start", flexWrap: "wrap", gap: "20px" }, children: [
          /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { children: [
            /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", alignItems: "center", gap: "12px", marginBottom: "12px" }, children: [
              /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: {
                width: "48px",
                height: "48px",
                background: "rgba(255,255,255,0.2)",
                borderRadius: "12px",
                display: "flex",
                alignItems: "center",
                justifyContent: "center",
                backdropFilter: "blur(10px)"
              }, children: /* @__PURE__ */ jsxRuntimeExports.jsx(Lightbulb, { style: { width: "24px", height: "24px" } }) }),
              /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { children: [
                /* @__PURE__ */ jsxRuntimeExports.jsx("h1", { style: {
                  fontSize: "28px",
                  fontWeight: "700",
                  margin: 0,
                  textShadow: "0 2px 4px rgba(0,0,0,0.1)",
                  color: "white"
                }, children: "Idea Board" }),
                /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: { fontSize: "16px", opacity: 0.9, marginTop: "4px" }, children: "AI-powered content inspiration" })
              ] })
            ] }),
            /* @__PURE__ */ jsxRuntimeExports.jsx("p", { style: {
              fontSize: "14px",
              opacity: 0.85,
              margin: 0,
              maxWidth: "600px",
              lineHeight: "1.5"
            }, children: "Generate fresh content ideas and transform your favorites into drafts. Click any title to edit it." })
          ] }),
          /* @__PURE__ */ jsxRuntimeExports.jsxs(
            "button",
            {
              onClick: onGenerate,
              disabled: isLoading,
              style: {
                background: "rgba(255,255,255,0.2)",
                border: "1px solid rgba(255,255,255,0.3)",
                color: "white",
                padding: "12px 24px",
                borderRadius: "8px",
                fontSize: "14px",
                fontWeight: "600",
                cursor: isLoading ? "not-allowed" : "pointer",
                display: "flex",
                alignItems: "center",
                gap: "8px",
                transition: "all 0.2s ease",
                backdropFilter: "blur(10px)",
                minWidth: "140px",
                justifyContent: "center"
              },
              onMouseEnter: (e) => {
                if (!isLoading) {
                  e.currentTarget.style.background = "rgba(255,255,255,0.3)";
                  e.currentTarget.style.transform = "translateY(-1px)";
                }
              },
              onMouseLeave: (e) => {
                if (!isLoading) {
                  e.currentTarget.style.background = "rgba(255,255,255,0.2)";
                  e.currentTarget.style.transform = "translateY(0)";
                }
              },
              children: [
                isLoading && /* @__PURE__ */ jsxRuntimeExports.jsx("span", { className: "aca-spinner" }),
                !isLoading && /* @__PURE__ */ jsxRuntimeExports.jsx(Sparkles, { style: { width: "16px", height: "16px" } }),
                isLoading ? "Generating..." : "Generate Ideas"
              ]
            }
          )
        ] }) }),
        /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: {
          position: "absolute",
          top: "-30px",
          right: "-30px",
          width: "120px",
          height: "120px",
          background: "rgba(255,255,255,0.1)",
          borderRadius: "50%",
          zIndex: 1
        } }),
        /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: {
          position: "absolute",
          bottom: "-20px",
          left: "-20px",
          width: "80px",
          height: "80px",
          background: "rgba(255,255,255,0.05)",
          borderRadius: "50%",
          zIndex: 1
        } })
      ] }),
      /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-card", children: [
        /* @__PURE__ */ jsxRuntimeExports.jsx("div", { className: "aca-card-header", children: /* @__PURE__ */ jsxRuntimeExports.jsxs("h2", { className: "aca-card-title", children: [
          /* @__PURE__ */ jsxRuntimeExports.jsx(PlusCircle, { className: "aca-nav-item-icon" }),
          "Add Your Own Idea"
        ] }) }),
        /* @__PURE__ */ jsxRuntimeExports.jsxs("form", { onSubmit: handleAddIdeaSubmit, style: { display: "flex", gap: "12px", alignItems: "flex-end" }, children: [
          /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-form-group", style: { flexGrow: 1, marginBottom: 0 }, children: [
            /* @__PURE__ */ jsxRuntimeExports.jsx("label", { className: "aca-label", htmlFor: "new-idea-input", children: "Idea Title" }),
            /* @__PURE__ */ jsxRuntimeExports.jsx(
              "input",
              {
                id: "new-idea-input",
                type: "text",
                value: newIdeaTitle,
                onChange: (e) => setNewIdeaTitle(e.target.value),
                placeholder: "Enter your content idea...",
                className: "aca-input"
              }
            )
          ] }),
          /* @__PURE__ */ jsxRuntimeExports.jsxs(
            "button",
            {
              type: "submit",
              disabled: !newIdeaTitle.trim(),
              className: "aca-button",
              style: {
                background: "#00a32a",
                borderColor: "#00a32a",
                flexShrink: 0,
                padding: "8px 16px"
              },
              children: [
                /* @__PURE__ */ jsxRuntimeExports.jsx(PlusCircle, { style: { width: "16px", height: "16px" } }),
                "Add Idea"
              ]
            }
          )
        ] })
      ] }),
      activeIdeas.length > 0 ? /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-card", children: [
        /* @__PURE__ */ jsxRuntimeExports.jsx("div", { className: "aca-card-header", children: /* @__PURE__ */ jsxRuntimeExports.jsxs("h2", { className: "aca-card-title", children: [
          /* @__PURE__ */ jsxRuntimeExports.jsx(Lightbulb, { className: "aca-nav-item-icon" }),
          "Active Ideas (",
          activeIdeas.length,
          ")"
        ] }) }),
        /* @__PURE__ */ jsxRuntimeExports.jsx("div", { className: "aca-grid aca-grid-2", children: activeIdeas.map((idea) => /* @__PURE__ */ jsxRuntimeExports.jsx(
          IdeaCard,
          {
            idea,
            onCreateDraft,
            onArchive,
            onUpdateTitle,
            onGenerateSimilar,
            isLoading: isLoadingDraft[`draft-${idea.id}`] || false,
            isGeneratingSimilar: isLoadingDraft[`similar-${idea.id}`] || false
          },
          idea.id
        )) })
      ] }) : /* @__PURE__ */ jsxRuntimeExports.jsx("div", { className: "aca-card", children: /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { textAlign: "center", padding: "60px 20px", color: "#646970" }, children: [
        /* @__PURE__ */ jsxRuntimeExports.jsx(Lightbulb, { style: { margin: "0 auto 20px auto", width: "48px", height: "48px", fill: "#a7aaad" } }),
        /* @__PURE__ */ jsxRuntimeExports.jsx("h3", { className: "aca-card-title", children: "No Active Ideas Yet" }),
        /* @__PURE__ */ jsxRuntimeExports.jsx("p", { className: "aca-page-description", style: { maxWidth: "400px", marginLeft: "auto", marginRight: "auto" }, children: "Get started by generating AI-powered content ideas or adding your own manually." }),
        /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: { display: "flex", gap: "10px", justifyContent: "center", flexWrap: "wrap" }, children: /* @__PURE__ */ jsxRuntimeExports.jsxs(
          "button",
          {
            onClick: onGenerate,
            disabled: isLoading,
            className: "aca-button large",
            children: [
              isLoading ? /* @__PURE__ */ jsxRuntimeExports.jsx("span", { className: "aca-spinner" }) : /* @__PURE__ */ jsxRuntimeExports.jsx(Lightbulb, { style: { width: "16px", height: "16px" } }),
              isLoading ? "Generating..." : "Generate Ideas"
            ]
          }
        ) })
      ] }) }),
      archivedIdeas.length > 0 && /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-card", children: [
        /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-card-header", children: [
          /* @__PURE__ */ jsxRuntimeExports.jsxs("h2", { className: "aca-card-title", children: [
            "Archived Ideas (",
            archivedIdeas.length,
            ")"
          ] }),
          /* @__PURE__ */ jsxRuntimeExports.jsx("p", { className: "aca-page-description", style: { margin: 0, fontSize: "14px" }, children: "Manage your archived ideas - restore them or delete permanently" })
        ] }),
        /* @__PURE__ */ jsxRuntimeExports.jsx("div", { className: "aca-list", children: archivedIdeas.map((idea, index) => /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-list-item", children: [
          /* @__PURE__ */ jsxRuntimeExports.jsx("span", { style: { flexGrow: 1, marginRight: "15px" }, children: idea.title }),
          /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: {
            fontSize: "11px",
            fontWeight: "600",
            padding: "4px 8px",
            borderRadius: "4px",
            background: sourceStyles[idea.source].background,
            color: sourceStyles[idea.source].color,
            border: `1px solid ${sourceStyles[idea.source].borderColor}`,
            flexShrink: 0,
            marginRight: "10px"
          }, children: sourceNames[idea.source] }),
          /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", gap: "8px", alignItems: "center" }, children: [
            onRestoreIdea && /* @__PURE__ */ jsxRuntimeExports.jsxs(
              "button",
              {
                onClick: () => onRestoreIdea(idea.id),
                className: "aca-button secondary",
                style: {
                  fontSize: "11px",
                  padding: "4px 8px",
                  minWidth: "auto",
                  background: "#e6f7e6",
                  borderColor: "#28a745",
                  color: "#0a5d0a"
                },
                title: "Restore to active ideas",
                children: [
                  /* @__PURE__ */ jsxRuntimeExports.jsx(Edit, { style: { width: "12px", height: "12px", marginRight: "4px" } }),
                  "Restore"
                ]
              }
            ),
            onDeleteIdea && /* @__PURE__ */ jsxRuntimeExports.jsx(
              "button",
              {
                onClick: () => {
                  if (window.confirm(`Are you sure you want to permanently delete "${idea.title}"? This action cannot be undone.`)) {
                    onDeleteIdea(idea.id);
                  }
                },
                className: "aca-button secondary",
                style: {
                  fontSize: "11px",
                  padding: "4px 8px",
                  minWidth: "auto",
                  background: "#ffeaea",
                  borderColor: "#dc3545",
                  color: "#721c24"
                },
                title: "Delete permanently",
                children: /* @__PURE__ */ jsxRuntimeExports.jsx(Trash, { style: { width: "12px", height: "12px" } })
              }
            )
          ] })
        ] }, idea.id)) })
      ] })
    ] });
  };
  const DraftCard = ({ draft, onSelectDraft, onPublish, isPublishing }) => {
    const isScheduled = !!draft.scheduledFor;
    return /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-card", style: {
      margin: 0,
      opacity: isPublishing ? 0.7 : 1,
      transform: isPublishing ? "scale(0.98)" : "scale(1)",
      transition: "all 0.3s ease",
      cursor: isPublishing ? "not-allowed" : "default"
    }, children: [
      /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { marginBottom: "15px" }, children: [
        /* @__PURE__ */ jsxRuntimeExports.jsx("h3", { className: "aca-card-title", style: { marginBottom: "8px" }, children: draft.title }),
        /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-page-description", style: { display: "flex", alignItems: "center", gap: "15px", flexWrap: "wrap" }, children: [
          /* @__PURE__ */ jsxRuntimeExports.jsxs("span", { children: [
            "Created: ",
            new Date(draft.createdAt).toLocaleDateString()
          ] }),
          isScheduled && /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: {
            display: "flex",
            alignItems: "center",
            gap: "6px",
            color: "#0073aa",
            fontWeight: "500"
          }, children: [
            /* @__PURE__ */ jsxRuntimeExports.jsx(Calendar, { style: { width: "14px", height: "14px" } }),
            "Scheduled: ",
            new Date(draft.scheduledFor).toLocaleDateString()
          ] })
        ] }),
        /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: {
          position: "absolute",
          top: "20px",
          right: "20px",
          fontSize: "11px",
          fontWeight: "600",
          padding: "6px 12px",
          borderRadius: "4px",
          border: "1px solid",
          background: isScheduled ? "#e3f2fd" : "#f6f7f7",
          color: isScheduled ? "#0d47a1" : "#646970",
          borderColor: isScheduled ? "#2196f3" : "#8c8f94"
        }, children: isScheduled ? "Scheduled" : "Draft" })
      ] }),
      /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-list-item", style: { padding: "15px 0 0 0", margin: 0, justifyContent: "flex-end", gap: "12px" }, children: [
        /* @__PURE__ */ jsxRuntimeExports.jsxs(
          "button",
          {
            onClick: () => {
              if (!window.acaData) {
                console.error("ACA Error: window.acaData is not defined");
                alert("WordPress data not available. Please refresh the page.");
                return;
              }
              const editUrl = `${window.acaData.admin_url}post.php?post=${draft.id}&action=edit`;
              window.open(editUrl, "_blank");
            },
            disabled: isPublishing,
            className: "aca-button secondary",
            style: {
              minWidth: "110px",
              background: "#ffffff",
              borderColor: "#0073aa",
              color: "#0073aa",
              boxShadow: "0 1px 3px rgba(0,0,0,0.1)"
            },
            children: [
              /* @__PURE__ */ jsxRuntimeExports.jsx(Eye, { className: "aca-nav-item-icon", style: { color: "#0073aa", strokeWidth: "1.5" } }),
              "View Draft"
            ]
          }
        ),
        /* @__PURE__ */ jsxRuntimeExports.jsx(
          "button",
          {
            onClick: () => onPublish(draft.id),
            disabled: isPublishing,
            className: "aca-button",
            style: {
              background: isPublishing ? "#f6f7f7" : "#00a32a",
              borderColor: isPublishing ? "#ccd0d4" : "#00a32a",
              color: isPublishing ? "#a7aaad" : "#ffffff",
              cursor: isPublishing ? "wait" : "pointer",
              minWidth: "120px"
            },
            children: isPublishing ? /* @__PURE__ */ jsxRuntimeExports.jsxs(jsxRuntimeExports.Fragment, { children: [
              /* @__PURE__ */ jsxRuntimeExports.jsx("span", { className: "aca-spinner" }),
              "Publishing..."
            ] }) : /* @__PURE__ */ jsxRuntimeExports.jsxs(jsxRuntimeExports.Fragment, { children: [
              /* @__PURE__ */ jsxRuntimeExports.jsx(Send, { className: "aca-nav-item-icon" }),
              "Publish Now"
            ] })
          }
        )
      ] })
    ] });
  };
  const DraftsList = ({ drafts, onSelectDraft, onPublish, publishingId, onNavigateToIdeas }) => {
    const scheduledDrafts = drafts.filter((draft) => draft.scheduledFor);
    const unscheduledDrafts = drafts.filter((draft) => !draft.scheduledFor);
    return /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-fade-in", children: [
      /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: {
        background: "linear-gradient(135deg, #10b981 0%, #059669 100%)",
        borderRadius: "12px",
        padding: "30px",
        marginBottom: "30px",
        color: "white",
        position: "relative",
        overflow: "hidden"
      }, children: [
        /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { position: "relative", zIndex: 2 }, children: [
          /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", alignItems: "center", gap: "12px", marginBottom: "12px" }, children: [
            /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: {
              width: "48px",
              height: "48px",
              background: "rgba(255,255,255,0.2)",
              borderRadius: "12px",
              display: "flex",
              alignItems: "center",
              justifyContent: "center",
              backdropFilter: "blur(10px)"
            }, children: /* @__PURE__ */ jsxRuntimeExports.jsx(FileText, { style: { width: "24px", height: "24px" } }) }),
            /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { children: [
              /* @__PURE__ */ jsxRuntimeExports.jsx("h1", { style: {
                fontSize: "28px",
                fontWeight: "700",
                margin: 0,
                textShadow: "0 2px 4px rgba(0,0,0,0.1)",
                color: "white"
              }, children: "Content Drafts" }),
              /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: { fontSize: "16px", opacity: 0.9, marginTop: "4px" }, children: "Review and publish your AI-generated content" })
            ] })
          ] }),
          /* @__PURE__ */ jsxRuntimeExports.jsx("p", { style: {
            fontSize: "14px",
            opacity: 0.85,
            margin: 0,
            maxWidth: "600px",
            lineHeight: "1.5"
          }, children: 'Review your AI-generated content drafts and publish them when ready. Click "View Draft" to edit before publishing.' }),
          /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", gap: "20px", marginTop: "20px", flexWrap: "wrap" }, children: [
            /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", alignItems: "center", gap: "6px" }, children: [
              /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: { width: "8px", height: "8px", backgroundColor: "#60a5fa", borderRadius: "50%" } }),
              /* @__PURE__ */ jsxRuntimeExports.jsxs("span", { style: { fontSize: "14px", opacity: 0.9 }, children: [
                drafts.length,
                " Total Drafts"
              ] })
            ] }),
            /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", alignItems: "center", gap: "6px" }, children: [
              /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: { width: "8px", height: "8px", backgroundColor: "#34d399", borderRadius: "50%" } }),
              /* @__PURE__ */ jsxRuntimeExports.jsxs("span", { style: { fontSize: "14px", opacity: 0.9 }, children: [
                scheduledDrafts.length,
                " Scheduled"
              ] })
            ] }),
            /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", alignItems: "center", gap: "6px" }, children: [
              /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: { width: "8px", height: "8px", backgroundColor: "#fbbf24", borderRadius: "50%" } }),
              /* @__PURE__ */ jsxRuntimeExports.jsxs("span", { style: { fontSize: "14px", opacity: 0.9 }, children: [
                unscheduledDrafts.length,
                " Ready to Publish"
              ] })
            ] })
          ] })
        ] }),
        /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: {
          position: "absolute",
          top: "-30px",
          right: "-30px",
          width: "120px",
          height: "120px",
          background: "rgba(255,255,255,0.1)",
          borderRadius: "50%",
          zIndex: 1
        } }),
        /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: {
          position: "absolute",
          bottom: "-20px",
          left: "-20px",
          width: "80px",
          height: "80px",
          background: "rgba(255,255,255,0.05)",
          borderRadius: "50%",
          zIndex: 1
        } })
      ] }),
      drafts.length > 0 ? /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", flexDirection: "column", gap: "30px" }, children: [
        scheduledDrafts.length > 0 && /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-card", children: [
          /* @__PURE__ */ jsxRuntimeExports.jsx("div", { className: "aca-card-header", children: /* @__PURE__ */ jsxRuntimeExports.jsxs("h2", { className: "aca-card-title", children: [
            /* @__PURE__ */ jsxRuntimeExports.jsx(Calendar, { className: "aca-nav-item-icon" }),
            "Scheduled Drafts (",
            scheduledDrafts.length,
            ")"
          ] }) }),
          /* @__PURE__ */ jsxRuntimeExports.jsx("div", { className: "aca-grid aca-grid-2", children: scheduledDrafts.map((draft) => /* @__PURE__ */ jsxRuntimeExports.jsx(
            DraftCard,
            {
              draft,
              onSelectDraft,
              onPublish,
              isPublishing: publishingId === draft.id
            },
            draft.id
          )) })
        ] }),
        unscheduledDrafts.length > 0 && /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-card", children: [
          /* @__PURE__ */ jsxRuntimeExports.jsx("div", { className: "aca-card-header", children: /* @__PURE__ */ jsxRuntimeExports.jsxs("h2", { className: "aca-card-title", children: [
            /* @__PURE__ */ jsxRuntimeExports.jsx(FileText, { className: "aca-nav-item-icon" }),
            "Ready to Publish (",
            unscheduledDrafts.length,
            ")"
          ] }) }),
          /* @__PURE__ */ jsxRuntimeExports.jsx("div", { className: "aca-grid aca-grid-2", children: unscheduledDrafts.map((draft) => /* @__PURE__ */ jsxRuntimeExports.jsx(
            DraftCard,
            {
              draft,
              onSelectDraft,
              onPublish,
              isPublishing: publishingId === draft.id
            },
            draft.id
          )) })
        ] })
      ] }) : /* @__PURE__ */ jsxRuntimeExports.jsx("div", { className: "aca-card", children: /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { textAlign: "center", padding: "60px 20px", color: "#646970" }, children: [
        /* @__PURE__ */ jsxRuntimeExports.jsx(FileText, { style: { margin: "0 auto 20px auto", width: "48px", height: "48px", fill: "#a7aaad" } }),
        /* @__PURE__ */ jsxRuntimeExports.jsx("h3", { className: "aca-card-title", children: "No Drafts Created Yet" }),
        /* @__PURE__ */ jsxRuntimeExports.jsx("p", { className: "aca-page-description", style: { maxWidth: "400px", marginLeft: "auto", marginRight: "auto" }, children: "Create content ideas first, then turn them into drafts to see them here." }),
        /* @__PURE__ */ jsxRuntimeExports.jsxs(
          "button",
          {
            onClick: onNavigateToIdeas || (() => window.location.hash = "#ideas"),
            className: "aca-button large",
            children: [
              /* @__PURE__ */ jsxRuntimeExports.jsx(FileText, { style: { width: "16px", height: "16px" } }),
              "Go to Idea Board"
            ]
          }
        )
      ] }) })
    ] });
  };
  const SettingsLayout = ({
    title,
    description,
    icon,
    children,
    actions
  }) => {
    return /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-page", children: [
      /* @__PURE__ */ jsxRuntimeExports.jsx("div", { className: "aca-page-header", style: {
        background: "linear-gradient(135deg, #667eea 0%, #764ba2 100%)",
        color: "white",
        padding: "32px",
        borderRadius: "12px",
        marginBottom: "32px",
        position: "relative",
        overflow: "hidden"
      }, children: /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { position: "relative", zIndex: 2 }, children: [
        /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", alignItems: "center", justifyContent: "space-between", marginBottom: "12px" }, children: [
          /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", alignItems: "center", gap: "16px" }, children: [
            /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: {
              width: "48px",
              height: "48px",
              background: "rgba(255,255,255,0.2)",
              borderRadius: "12px",
              display: "flex",
              alignItems: "center",
              justifyContent: "center"
            }, children: icon }),
            /* @__PURE__ */ jsxRuntimeExports.jsx("div", { children: /* @__PURE__ */ jsxRuntimeExports.jsx("h1", { style: { fontSize: "28px", fontWeight: "700", margin: 0, color: "white" }, children: title }) })
          ] }),
          actions && /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: {
            background: "rgba(255,255,255,0.1)",
            borderRadius: "8px",
            padding: "8px"
          }, children: actions })
        ] }),
        /* @__PURE__ */ jsxRuntimeExports.jsx("p", { style: {
          fontSize: "14px",
          opacity: 0.85,
          margin: 0,
          maxWidth: "600px",
          lineHeight: "1.5"
        }, children: description })
      ] }) }),
      /* @__PURE__ */ jsxRuntimeExports.jsx("div", { className: "aca-settings-content", children }),
      actions && /* @__PURE__ */ jsxRuntimeExports.jsx("div", { className: "aca-settings-actions", style: {
        marginTop: "32px",
        padding: "24px",
        background: "#f8fafc",
        borderRadius: "12px",
        borderTop: "1px solid #e2e8f0"
      }, children: actions })
    ] });
  };
  const SettingsLicense = ({
    settings,
    onSaveSettings,
    onShowToast
  }) => {
    const [licenseKey, setLicenseKey] = reactExports.useState("");
    const [licenseStatus, setLicenseStatus] = reactExports.useState({ status: "inactive", is_active: false });
    const [isVerifyingLicense, setIsVerifyingLicense] = reactExports.useState(false);
    const [isLoadingLicenseStatus, setIsLoadingLicenseStatus] = reactExports.useState(true);
    const [isDirty, setIsDirty] = reactExports.useState(false);
    reactExports.useEffect(() => {
      const loadLicenseStatus = async () => {
        try {
          const status = await licenseApi.getStatus();
          setLicenseStatus({
            status: status.status || "inactive",
            is_active: status.is_active || false,
            verified_at: status.verified_at
          });
        } catch (error) {
          console.error("Failed to load license status:", error);
        } finally {
          setIsLoadingLicenseStatus(false);
        }
      };
      loadLicenseStatus();
    }, []);
    const handleLicenseVerification = async () => {
      if (!licenseKey.trim()) {
        onShowToast("Please enter a license key", "warning");
        return;
      }
      setIsVerifyingLicense(true);
      try {
        const result = await licenseApi.verify(licenseKey);
        if (result.success) {
          setLicenseStatus({
            status: "active",
            is_active: true,
            verified_at: (/* @__PURE__ */ new Date()).toISOString()
          });
          setLicenseKey("");
          onShowToast("License verified successfully! Pro features are now active.", "success");
          const updatedSettings = { ...settings, is_pro: true };
          onSaveSettings(updatedSettings);
        } else {
          onShowToast(result.message || "License verification failed", "error");
        }
      } catch (error) {
        console.error("License verification error:", error);
        onShowToast("Failed to verify license. Please try again.", "error");
      } finally {
        setIsVerifyingLicense(false);
      }
    };
    const handleLicenseDeactivation = async () => {
      if (!confirm("Are you sure you want to deactivate your Pro license? This will disable all Pro features.")) {
        return;
      }
      setIsVerifyingLicense(true);
      try {
        const result = await licenseApi.deactivate();
        if (result.success) {
          setLicenseStatus({
            status: "inactive",
            is_active: false
          });
          onShowToast("License deactivated successfully. You can now use it on another site.", "success");
          const updatedSettings = { ...settings, is_pro: false };
          onSaveSettings(updatedSettings);
        } else {
          onShowToast(result.message || "License deactivation failed", "error");
        }
      } catch (error) {
        console.error("License deactivation error:", error);
        onShowToast("Failed to deactivate license. Please try again.", "error");
      } finally {
        setIsVerifyingLicense(false);
      }
    };
    if (isLoadingLicenseStatus) {
      return /* @__PURE__ */ jsxRuntimeExports.jsx(
        SettingsLayout,
        {
          title: "Pro License",
          description: "Unlock advanced features and automation capabilities",
          icon: /* @__PURE__ */ jsxRuntimeExports.jsx(Shield, { style: { width: "24px", height: "24px", color: "white" } }),
          children: /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { padding: "40px", textAlign: "center", color: "#666" }, children: [
            /* @__PURE__ */ jsxRuntimeExports.jsx(Spinner, { style: { width: "24px", height: "24px", marginBottom: "16px" } }),
            /* @__PURE__ */ jsxRuntimeExports.jsx("div", { children: "Loading license status..." })
          ] })
        }
      );
    }
    return /* @__PURE__ */ jsxRuntimeExports.jsxs(
      SettingsLayout,
      {
        title: "Pro License",
        description: "Unlock advanced features and automation capabilities",
        icon: /* @__PURE__ */ jsxRuntimeExports.jsx(Shield, { style: { width: "24px", height: "24px", color: "white" } }),
        children: [
          /* @__PURE__ */ jsxRuntimeExports.jsx("div", { className: "aca-stat-item", style: { margin: "0 0 20px 0" }, children: /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-stat-info", children: [
            /* @__PURE__ */ jsxRuntimeExports.jsx("div", { className: "aca-stat-icon", children: licenseStatus.is_active ? /* @__PURE__ */ jsxRuntimeExports.jsx(CheckCircle, { style: { color: "#27ae60", width: "20px", height: "20px" } }) : /* @__PURE__ */ jsxRuntimeExports.jsx(Shield, { style: { color: "#e74c3c", width: "20px", height: "20px" } }) }),
            /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { children: [
              /* @__PURE__ */ jsxRuntimeExports.jsx("div", { className: "aca-stat-number", children: licenseStatus.is_active ? "Pro Active" : "Free Version" }),
              /* @__PURE__ */ jsxRuntimeExports.jsx("div", { className: "aca-stat-label", children: licenseStatus.is_active ? `Verified ${licenseStatus.verified_at ? new Date(licenseStatus.verified_at).toLocaleDateString() : ""}` : "Upgrade to unlock Pro features" })
            ] })
          ] }) }),
          !licenseStatus.is_active && /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-form-group", children: [
            /* @__PURE__ */ jsxRuntimeExports.jsx("label", { className: "aca-label", htmlFor: "license-key", children: "License Key" }),
            /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", gap: "10px" }, children: [
              /* @__PURE__ */ jsxRuntimeExports.jsx(
                "input",
                {
                  id: "license-key",
                  type: "text",
                  className: "aca-input",
                  value: licenseKey,
                  onChange: (e) => setLicenseKey(e.target.value),
                  placeholder: "Enter your Pro license key",
                  disabled: isVerifyingLicense
                }
              ),
              /* @__PURE__ */ jsxRuntimeExports.jsx(
                "button",
                {
                  onClick: handleLicenseVerification,
                  disabled: isVerifyingLicense || !licenseKey.trim(),
                  className: "aca-button aca-button-primary",
                  style: { minWidth: "120px" },
                  children: isVerifyingLicense ? /* @__PURE__ */ jsxRuntimeExports.jsxs(jsxRuntimeExports.Fragment, { children: [
                    /* @__PURE__ */ jsxRuntimeExports.jsx(Spinner, { className: "aca-spinner" }),
                    "Verifying..."
                  ] }) : "Verify License"
                }
              )
            ] }),
            /* @__PURE__ */ jsxRuntimeExports.jsxs("p", { className: "aca-page-description", style: { marginTop: "10px" }, children: [
              "Don't have a Pro license? ",
              /* @__PURE__ */ jsxRuntimeExports.jsx("a", { href: "https://ademisler.gumroad.com/l/ai-content-agent-pro", target: "_blank", rel: "noopener noreferrer", style: { color: "#0073aa" }, children: "Purchase here" })
            ] })
          ] }),
          licenseStatus.is_active && /* @__PURE__ */ jsxRuntimeExports.jsxs(jsxRuntimeExports.Fragment, { children: [
            /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-alert aca-alert-success", style: { margin: "20px 0" }, children: [
              /* @__PURE__ */ jsxRuntimeExports.jsx(CheckCircle, { style: { width: "16px", height: "16px", marginRight: "8px" } }),
              "Pro license is active! You now have access to all premium features."
            ] }),
            /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { marginTop: "15px" }, children: [
              /* @__PURE__ */ jsxRuntimeExports.jsx(
                "button",
                {
                  onClick: handleLicenseDeactivation,
                  disabled: isVerifyingLicense,
                  className: "aca-button aca-button-secondary",
                  style: {
                    minWidth: "140px",
                    backgroundColor: "#dc3545",
                    borderColor: "#dc3545",
                    color: "#ffffff"
                  },
                  children: isVerifyingLicense ? /* @__PURE__ */ jsxRuntimeExports.jsxs(jsxRuntimeExports.Fragment, { children: [
                    /* @__PURE__ */ jsxRuntimeExports.jsx(Spinner, { className: "aca-spinner" }),
                    "Deactivating..."
                  ] }) : "Deactivate License"
                }
              ),
              /* @__PURE__ */ jsxRuntimeExports.jsx("p", { className: "aca-page-description", style: { marginTop: "8px", fontSize: "12px" }, children: "This will disable all Pro features and allow you to use the license on another site." })
            ] })
          ] })
        ]
      }
    );
  };
  const UpgradePrompt = ({
    title,
    description,
    features,
    gumroadUrl = "https://ademisler.gumroad.com/l/ai-content-agent-pro"
  }) => {
    return /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-card", style: {
      margin: 0,
      border: "2px solid #f0ad4e",
      background: "linear-gradient(135deg, #fff9e6 0%, #ffeaa7 100%)",
      position: "relative",
      overflow: "hidden"
    }, children: [
      /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: {
        position: "absolute",
        top: "15px",
        right: "15px",
        background: "linear-gradient(135deg, #f39c12 0%, #e67e22 100%)",
        color: "white",
        padding: "4px 12px",
        borderRadius: "12px",
        fontSize: "12px",
        fontWeight: "bold",
        boxShadow: "0 2px 4px rgba(0,0,0,0.1)"
      }, children: [
        /* @__PURE__ */ jsxRuntimeExports.jsx(Shield, { style: { width: "12px", height: "12px", marginRight: "4px" } }),
        "PRO"
      ] }),
      /* @__PURE__ */ jsxRuntimeExports.jsx("div", { className: "aca-card-header", children: /* @__PURE__ */ jsxRuntimeExports.jsxs("h2", { className: "aca-card-title", style: { marginTop: "0", color: "#d68910" }, children: [
        /* @__PURE__ */ jsxRuntimeExports.jsx(Zap, { className: "aca-nav-item-icon", style: { color: "#f39c12" } }),
        title
      ] }) }),
      /* @__PURE__ */ jsxRuntimeExports.jsx("p", { style: { color: "#8b6914", marginBottom: "20px", fontSize: "14px" }, children: description }),
      /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { marginBottom: "20px" }, children: [
        /* @__PURE__ */ jsxRuntimeExports.jsx("h4", { style: { color: "#d68910", marginBottom: "10px", fontSize: "14px" }, children: "🚀 Unlock Pro Features:" }),
        /* @__PURE__ */ jsxRuntimeExports.jsx("ul", { style: {
          listStyle: "none",
          padding: 0,
          margin: 0,
          color: "#8b6914"
        }, children: features.map((feature, index) => /* @__PURE__ */ jsxRuntimeExports.jsxs("li", { style: {
          marginBottom: "8px",
          paddingLeft: "20px",
          position: "relative",
          fontSize: "13px"
        }, children: [
          /* @__PURE__ */ jsxRuntimeExports.jsx("span", { style: {
            position: "absolute",
            left: "0",
            color: "#27ae60",
            fontWeight: "bold"
          }, children: "✓" }),
          feature
        ] }, index)) })
      ] }),
      /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: { textAlign: "center" }, children: /* @__PURE__ */ jsxRuntimeExports.jsxs(
        "a",
        {
          href: gumroadUrl,
          target: "_blank",
          rel: "noopener noreferrer",
          className: "aca-button aca-button-primary",
          style: {
            background: "linear-gradient(135deg, #f39c12 0%, #e67e22 100%)",
            border: "none",
            color: "white",
            padding: "12px 24px",
            borderRadius: "6px",
            textDecoration: "none",
            fontWeight: "bold",
            display: "inline-block",
            boxShadow: "0 3px 6px rgba(0,0,0,0.1)",
            transition: "all 0.3s ease"
          },
          onMouseOver: (e) => {
            e.currentTarget.style.transform = "translateY(-2px)";
            e.currentTarget.style.boxShadow = "0 5px 10px rgba(0,0,0,0.2)";
          },
          onMouseOut: (e) => {
            e.currentTarget.style.transform = "translateY(0)";
            e.currentTarget.style.boxShadow = "0 3px 6px rgba(0,0,0,0.1)";
          },
          children: [
            /* @__PURE__ */ jsxRuntimeExports.jsx(Shield, { style: { width: "16px", height: "16px", marginRight: "8px" } }),
            "Upgrade to Pro"
          ]
        }
      ) }),
      /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: {
        textAlign: "center",
        marginTop: "15px",
        fontSize: "12px",
        color: "#a67c00"
      }, children: "💡 One-time purchase • Lifetime updates" })
    ] });
  };
  const RadioCard = ({ id, title, description, currentSelection, onChange }) => {
    const isChecked = currentSelection === id;
    return /* @__PURE__ */ jsxRuntimeExports.jsx(
      "label",
      {
        htmlFor: id,
        className: "aca-card",
        style: {
          margin: 0,
          border: "2px solid",
          borderColor: isChecked ? "#0073aa" : "#ccd0d4",
          background: isChecked ? "#f0f6fc" : "#ffffff",
          boxShadow: isChecked ? "0 2px 4px rgba(0, 0, 0, 0.1)" : "none",
          cursor: "pointer"
        },
        children: /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", alignItems: "flex-start", gap: "12px" }, children: [
          /* @__PURE__ */ jsxRuntimeExports.jsx(
            "input",
            {
              type: "radio",
              id,
              name: "automation-mode",
              checked: isChecked,
              onChange: () => onChange(id),
              style: {
                marginTop: "2px",
                width: "18px",
                height: "18px",
                accentColor: "#0073aa",
                flexShrink: 0
              }
            }
          ),
          /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { children: [
            /* @__PURE__ */ jsxRuntimeExports.jsx("h4", { className: "aca-card-title", style: { marginBottom: "8px" }, children: title }),
            /* @__PURE__ */ jsxRuntimeExports.jsx("p", { className: "aca-page-description", style: { margin: 0 }, children: description })
          ] })
        ] })
      }
    );
  };
  const SettingsAutomation = ({
    settings,
    onSaveSettings,
    onShowToast
  }) => {
    const [currentSettings, setCurrentSettings] = reactExports.useState(settings);
    const [licenseStatus, setLicenseStatus] = reactExports.useState({ status: "inactive", is_active: false });
    const [isLoadingLicenseStatus, setIsLoadingLicenseStatus] = reactExports.useState(true);
    const [isDirty, setIsDirty] = reactExports.useState(false);
    const [isSaving, setIsSaving] = reactExports.useState(false);
    reactExports.useEffect(() => {
      const loadLicenseStatus = async () => {
        try {
          const data = await licenseApi.getStatus();
          setLicenseStatus({
            status: data.status || "inactive",
            is_active: data.is_active || false,
            verified_at: data.verified_at || void 0
          });
        } catch (error) {
          console.error("Failed to load license status:", error);
          setLicenseStatus({
            status: "inactive",
            is_active: false,
            verified_at: void 0
          });
        } finally {
          setIsLoadingLicenseStatus(false);
        }
      };
      loadLicenseStatus();
    }, []);
    reactExports.useEffect(() => {
      if (licenseStatus && licenseStatus.is_active !== void 0) ;
    }, [licenseStatus]);
    const isProActive = () => {
      return currentSettings.is_pro || licenseStatus.is_active;
    };
    const handleModeChange = (mode) => {
      const updatedSettings = { ...currentSettings, mode };
      setCurrentSettings(updatedSettings);
      setIsDirty(true);
    };
    const handleSettingChange = (key, value) => {
      const updatedSettings = { ...currentSettings, [key]: value };
      setCurrentSettings(updatedSettings);
      setIsDirty(true);
    };
    const handleSave = async () => {
      if (!isDirty) return;
      setIsSaving(true);
      try {
        await onSaveSettings(currentSettings);
        setIsDirty(false);
        onShowToast("Automation settings saved successfully!", "success");
      } catch (error) {
        onShowToast("Failed to save automation settings", "error");
      } finally {
        setIsSaving(false);
      }
    };
    if (isLoadingLicenseStatus) {
      return /* @__PURE__ */ jsxRuntimeExports.jsx(
        SettingsLayout,
        {
          title: "Automation Mode",
          description: "Configure how AI Content Agent creates and publishes content automatically",
          icon: /* @__PURE__ */ jsxRuntimeExports.jsx(Zap, { style: { width: "24px", height: "24px", color: "white" } }),
          children: /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: { padding: "40px", textAlign: "center", color: "#666" }, children: "Loading license status..." })
        }
      );
    }
    const saveButton = isDirty ? /* @__PURE__ */ jsxRuntimeExports.jsx(
      "button",
      {
        onClick: handleSave,
        disabled: isSaving,
        className: "aca-button aca-button-primary",
        style: { minWidth: "120px" },
        children: isSaving ? "Saving..." : "Save Changes"
      }
    ) : null;
    return /* @__PURE__ */ jsxRuntimeExports.jsx(
      SettingsLayout,
      {
        title: "Automation Mode",
        description: "Configure how AI Content Agent creates and publishes content automatically",
        icon: /* @__PURE__ */ jsxRuntimeExports.jsx(Zap, { style: { width: "24px", height: "24px", color: "white" } }),
        actions: saveButton,
        children: isProActive() ? /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { children: [
          /* @__PURE__ */ jsxRuntimeExports.jsx("p", { className: "aca-page-description", style: { marginBottom: "20px" }, children: "Choose how you want the AI Content Agent (ACA) to operate. You can change this at any time." }),
          /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", flexDirection: "column", gap: "15px" }, children: [
            /* @__PURE__ */ jsxRuntimeExports.jsx(
              RadioCard,
              {
                id: "manual",
                title: "Manual Mode",
                description: "You are in full control. Manually generate ideas and create drafts one by one.",
                currentSelection: currentSettings.mode,
                onChange: handleModeChange
              }
            ),
            /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-card", style: {
              margin: 0,
              border: "2px solid",
              borderColor: currentSettings.mode === "semi-automatic" ? "#0073aa" : "#ccd0d4",
              background: currentSettings.mode === "semi-automatic" ? "#f0f6fc" : "#ffffff",
              boxShadow: currentSettings.mode === "semi-automatic" ? "0 2px 4px rgba(0, 0, 0, 0.1)" : "none"
            }, children: [
              /* @__PURE__ */ jsxRuntimeExports.jsxs("label", { htmlFor: "semi-automatic", style: { display: "flex", alignItems: "flex-start", cursor: "pointer", gap: "12px" }, children: [
                /* @__PURE__ */ jsxRuntimeExports.jsx(
                  "input",
                  {
                    type: "radio",
                    id: "semi-automatic",
                    name: "automation-mode",
                    checked: currentSettings.mode === "semi-automatic",
                    onChange: () => handleModeChange("semi-automatic"),
                    style: {
                      marginTop: "2px",
                      width: "18px",
                      height: "18px",
                      accentColor: "#0073aa",
                      flexShrink: 0
                    }
                  }
                ),
                /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { children: [
                  /* @__PURE__ */ jsxRuntimeExports.jsx("h4", { className: "aca-card-title", style: { marginBottom: "8px" }, children: "Semi-Automatic Mode" }),
                  /* @__PURE__ */ jsxRuntimeExports.jsx("p", { className: "aca-page-description", style: { margin: 0 }, children: "The AI automatically generates new ideas periodically. You choose which ideas to turn into drafts." })
                ] })
              ] }),
              currentSettings.mode === "semi-automatic" && /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-form-group", style: {
                paddingLeft: "30px",
                paddingTop: "20px",
                marginTop: "20px",
                borderTop: "1px solid #e0e0e0",
                marginBottom: 0
              }, children: [
                /* @__PURE__ */ jsxRuntimeExports.jsx("label", { className: "aca-label", htmlFor: "semi-auto-frequency", children: "Idea Generation Frequency" }),
                /* @__PURE__ */ jsxRuntimeExports.jsxs(
                  "select",
                  {
                    id: "semi-auto-frequency",
                    className: "aca-input",
                    value: currentSettings.semiAutoIdeaFrequency || "weekly",
                    onChange: (e) => handleSettingChange("semiAutoIdeaFrequency", e.target.value),
                    style: { marginTop: "5px" },
                    children: [
                      /* @__PURE__ */ jsxRuntimeExports.jsx("option", { value: "daily", children: "Daily - Generate new ideas every day" }),
                      /* @__PURE__ */ jsxRuntimeExports.jsx("option", { value: "weekly", children: "Weekly - Generate new ideas every week" }),
                      /* @__PURE__ */ jsxRuntimeExports.jsx("option", { value: "monthly", children: "Monthly - Generate new ideas every month" })
                    ]
                  }
                ),
                /* @__PURE__ */ jsxRuntimeExports.jsx("p", { className: "aca-page-description", style: { marginTop: "5px", margin: "5px 0 0 0" }, children: "How often should the AI automatically generate new content ideas?" })
              ] })
            ] }),
            /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-card", style: {
              margin: 0,
              border: "2px solid",
              borderColor: currentSettings.mode === "full-automatic" ? "#0073aa" : "#ccd0d4",
              background: currentSettings.mode === "full-automatic" ? "#f0f6fc" : "#ffffff",
              boxShadow: currentSettings.mode === "full-automatic" ? "0 2px 4px rgba(0, 0, 0, 0.1)" : "none"
            }, children: [
              /* @__PURE__ */ jsxRuntimeExports.jsxs("label", { htmlFor: "full-automatic-radio", style: { display: "flex", alignItems: "flex-start", cursor: "pointer", gap: "12px" }, children: [
                /* @__PURE__ */ jsxRuntimeExports.jsx(
                  "input",
                  {
                    type: "radio",
                    id: "full-automatic-radio",
                    name: "automation-mode",
                    checked: currentSettings.mode === "full-automatic",
                    onChange: () => handleModeChange("full-automatic"),
                    style: {
                      marginTop: "2px",
                      width: "18px",
                      height: "18px",
                      accentColor: "#0073aa",
                      flexShrink: 0
                    }
                  }
                ),
                /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { children: [
                  /* @__PURE__ */ jsxRuntimeExports.jsx("h4", { className: "aca-card-title", style: { marginBottom: "8px" }, children: "Full-Automatic Mode (Set & Forget)" }),
                  /* @__PURE__ */ jsxRuntimeExports.jsx("p", { className: "aca-page-description", style: { margin: 0 }, children: "The AI handles everything: generates ideas, picks the best ones, and creates drafts automatically." })
                ] })
              ] }),
              currentSettings.mode === "full-automatic" && /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: {
                paddingLeft: "30px",
                paddingTop: "20px",
                marginTop: "20px",
                borderTop: "1px solid #e0e0e0",
                marginBottom: 0
              }, children: [
                /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-form-group", style: { marginBottom: "20px" }, children: [
                  /* @__PURE__ */ jsxRuntimeExports.jsx("label", { className: "aca-label", htmlFor: "daily-post-count", children: "Daily Post Count" }),
                  /* @__PURE__ */ jsxRuntimeExports.jsxs(
                    "select",
                    {
                      id: "daily-post-count",
                      className: "aca-input",
                      value: currentSettings.fullAutoDailyPostCount || 1,
                      onChange: (e) => handleSettingChange("fullAutoDailyPostCount", parseInt(e.target.value)),
                      style: { marginTop: "5px" },
                      children: [
                        /* @__PURE__ */ jsxRuntimeExports.jsx("option", { value: 1, children: "1 post per day" }),
                        /* @__PURE__ */ jsxRuntimeExports.jsx("option", { value: 2, children: "2 posts per day" }),
                        /* @__PURE__ */ jsxRuntimeExports.jsx("option", { value: 3, children: "3 posts per day" }),
                        /* @__PURE__ */ jsxRuntimeExports.jsx("option", { value: 5, children: "5 posts per day" })
                      ]
                    }
                  ),
                  /* @__PURE__ */ jsxRuntimeExports.jsx("p", { className: "aca-page-description", style: { marginTop: "5px", margin: "5px 0 0 0" }, children: "How many posts should be created daily in full-automatic mode?" })
                ] }),
                /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-form-group", style: { marginBottom: "20px" }, children: [
                  /* @__PURE__ */ jsxRuntimeExports.jsx("label", { className: "aca-label", htmlFor: "publish-frequency", children: "Publishing Frequency" }),
                  /* @__PURE__ */ jsxRuntimeExports.jsxs(
                    "select",
                    {
                      id: "publish-frequency",
                      className: "aca-input",
                      value: currentSettings.fullAutoPublishFrequency || "daily",
                      onChange: (e) => handleSettingChange("fullAutoPublishFrequency", e.target.value),
                      style: { marginTop: "5px" },
                      children: [
                        /* @__PURE__ */ jsxRuntimeExports.jsx("option", { value: "hourly", children: "Every hour - Publish posts throughout the day" }),
                        /* @__PURE__ */ jsxRuntimeExports.jsx("option", { value: "daily", children: "Daily - Publish once per day" }),
                        /* @__PURE__ */ jsxRuntimeExports.jsx("option", { value: "weekly", children: "Weekly - Publish once per week" })
                      ]
                    }
                  ),
                  /* @__PURE__ */ jsxRuntimeExports.jsx("p", { className: "aca-page-description", style: { marginTop: "5px", margin: "5px 0 0 0" }, children: "How often should created drafts be published automatically?" })
                ] }),
                /* @__PURE__ */ jsxRuntimeExports.jsx("div", { className: "aca-form-group", style: { marginBottom: 0 }, children: /* @__PURE__ */ jsxRuntimeExports.jsxs("label", { htmlFor: "auto-publish", style: { display: "flex", alignItems: "flex-start", cursor: "pointer", gap: "12px" }, children: [
                  /* @__PURE__ */ jsxRuntimeExports.jsx(
                    "input",
                    {
                      type: "checkbox",
                      id: "auto-publish",
                      checked: currentSettings.autoPublish,
                      onChange: (e) => handleSettingChange("autoPublish", e.target.checked),
                      style: {
                        marginTop: "2px",
                        width: "16px",
                        height: "16px",
                        accentColor: "#0073aa"
                      }
                    }
                  ),
                  /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { children: [
                    /* @__PURE__ */ jsxRuntimeExports.jsx("span", { className: "aca-label", children: "Enable Auto-Publish" }),
                    /* @__PURE__ */ jsxRuntimeExports.jsx("p", { className: "aca-page-description", style: { marginTop: "5px", margin: "5px 0 0 0" }, children: "When enabled, the AI will automatically publish posts according to the frequency settings above." })
                  ] })
                ] }) })
              ] })
            ] })
          ] })
        ] }) : /* @__PURE__ */ jsxRuntimeExports.jsx(
          UpgradePrompt,
          {
            title: "Automation Features Require Pro License",
            description: "Unlock powerful automation modes including semi-automatic and full-automatic content generation.",
            features: [
              "Semi-Automatic Mode - AI generates ideas automatically",
              "Full-Automatic Mode - Complete hands-off content creation",
              "Flexible scheduling options",
              "Auto-publish capabilities"
            ]
          }
        )
      }
    );
  };
  const IntegrationCard = ({ title, icon, children, isConfigured }) => /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-card", style: { margin: "0 0 24px 0" }, children: [
    /* @__PURE__ */ jsxRuntimeExports.jsx("div", { className: "aca-card-header", children: /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", justifyContent: "space-between", alignItems: "center", marginBottom: "16px" }, children: [
      /* @__PURE__ */ jsxRuntimeExports.jsxs("h3", { className: "aca-card-title", style: { display: "flex", alignItems: "center", gap: "12px", margin: 0 }, children: [
        /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: {
          width: "40px",
          height: "40px",
          background: "linear-gradient(135deg, #667eea 0%, #764ba2 100%)",
          borderRadius: "10px",
          display: "flex",
          alignItems: "center",
          justifyContent: "center"
        }, children: icon }),
        title
      ] }),
      isConfigured && /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-alert success", style: {
        display: "flex",
        alignItems: "center",
        fontSize: "12px",
        fontWeight: "600",
        gap: "6px",
        padding: "4px 8px",
        margin: 0
      }, children: [
        /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: {
          width: "8px",
          height: "8px",
          borderRadius: "50%",
          background: "#22c55e"
        } }),
        "Configured"
      ] })
    ] }) }),
    children
  ] });
  const SettingsIntegrations = ({
    settings,
    onSaveSettings,
    onShowToast
  }) => {
    const [currentSettings, setCurrentSettings] = reactExports.useState(settings);
    const [isDirty, setIsDirty] = reactExports.useState(false);
    const [isSaving, setIsSaving] = reactExports.useState(false);
    const [isTestingApi, setIsTestingApi] = reactExports.useState(false);
    const [apiTestResult, setApiTestResult] = reactExports.useState(null);
    const handleSettingChange = (key, value) => {
      const updatedSettings = { ...currentSettings, [key]: value };
      setCurrentSettings(updatedSettings);
      setIsDirty(true);
      if (key === "geminiApiKey") {
        setApiTestResult(null);
      }
    };
    const handleTestApiKey = async () => {
      if (!currentSettings.geminiApiKey || currentSettings.geminiApiKey.trim() === "") {
        setApiTestResult({ success: false, error: "Please enter an API key first" });
        return;
      }
      setIsTestingApi(true);
      setApiTestResult(null);
      try {
        const result = await testGeminiApiKey(currentSettings.geminiApiKey);
        setApiTestResult(result);
        if (result.success) {
          onShowToast("API key is valid and working!", "success");
        } else {
          onShowToast(`API key test failed: ${result.error}`, "error");
        }
      } catch (error) {
        setApiTestResult({ success: false, error: "Test failed unexpectedly" });
        onShowToast("API key test failed unexpectedly", "error");
      } finally {
        setIsTestingApi(false);
      }
    };
    const handleSave = async () => {
      if (!isDirty) return;
      setIsSaving(true);
      try {
        await onSaveSettings(currentSettings);
        setIsDirty(false);
        onShowToast("Integration settings saved successfully!", "success");
      } catch (error) {
        onShowToast("Failed to save integration settings", "error");
      } finally {
        setIsSaving(false);
      }
    };
    const isImageSourceConfigured = () => {
      switch (currentSettings.imageSourceProvider) {
        case "pexels":
          return !!currentSettings.pexelsApiKey;
        case "unsplash":
          return !!currentSettings.unsplashApiKey;
        case "pixabay":
          return !!currentSettings.pixabayApiKey;
        case "ai":
          return !!currentSettings.googleCloudProjectId;
        default:
          return false;
      }
    };
    const saveButton = isDirty ? /* @__PURE__ */ jsxRuntimeExports.jsx(
      "button",
      {
        onClick: handleSave,
        disabled: isSaving,
        className: "aca-button aca-button-primary",
        style: { minWidth: "120px" },
        children: isSaving ? "Saving..." : "Save Changes"
      }
    ) : null;
    return /* @__PURE__ */ jsxRuntimeExports.jsxs(
      SettingsLayout,
      {
        title: "Integrations & Services",
        description: "Connect to external services and configure how content is generated",
        icon: /* @__PURE__ */ jsxRuntimeExports.jsx(Globe, { style: { width: "24px", height: "24px", color: "white" } }),
        actions: saveButton,
        children: [
          /* @__PURE__ */ jsxRuntimeExports.jsx(
            IntegrationCard,
            {
              title: "Google AI (Gemini)",
              icon: /* @__PURE__ */ jsxRuntimeExports.jsx(Google, { style: { width: "20px", height: "20px", color: "white" } }),
              isConfigured: !!currentSettings.geminiApiKey,
              children: /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-form-group", children: [
                /* @__PURE__ */ jsxRuntimeExports.jsx("label", { htmlFor: "gemini-api-key", className: "aca-label", children: "API Key" }),
                /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", gap: "12px", alignItems: "flex-start" }, children: [
                  /* @__PURE__ */ jsxRuntimeExports.jsx(
                    "input",
                    {
                      id: "gemini-api-key",
                      type: "password",
                      placeholder: "Enter Google AI API Key",
                      value: currentSettings.geminiApiKey,
                      onChange: (e) => handleSettingChange("geminiApiKey", e.target.value),
                      className: "aca-input",
                      style: { flex: 1 }
                    }
                  ),
                  /* @__PURE__ */ jsxRuntimeExports.jsx(
                    "button",
                    {
                      onClick: handleTestApiKey,
                      disabled: isTestingApi || !currentSettings.geminiApiKey,
                      className: "aca-button secondary",
                      style: {
                        minWidth: "100px",
                        display: "flex",
                        alignItems: "center",
                        gap: "6px",
                        fontSize: "14px"
                      },
                      children: isTestingApi ? /* @__PURE__ */ jsxRuntimeExports.jsxs(jsxRuntimeExports.Fragment, { children: [
                        /* @__PURE__ */ jsxRuntimeExports.jsx("span", { className: "aca-spinner", style: { width: "14px", height: "14px" } }),
                        "Testing..."
                      ] }) : /* @__PURE__ */ jsxRuntimeExports.jsxs(jsxRuntimeExports.Fragment, { children: [
                        apiTestResult?.success ? /* @__PURE__ */ jsxRuntimeExports.jsx(CheckCircle, { style: { width: "14px", height: "14px", color: "#22c55e" } }) : apiTestResult?.error ? /* @__PURE__ */ jsxRuntimeExports.jsx(AlertTriangle, { style: { width: "14px", height: "14px", color: "#ef4444" } }) : null,
                        "Test API"
                      ] })
                    }
                  )
                ] }),
                apiTestResult && /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: {
                  marginTop: "8px",
                  padding: "8px 12px",
                  borderRadius: "6px",
                  fontSize: "13px",
                  backgroundColor: apiTestResult.success ? "#dcfce7" : "#fef2f2",
                  border: `1px solid ${apiTestResult.success ? "#22c55e" : "#ef4444"}`,
                  color: apiTestResult.success ? "#166534" : "#dc2626"
                }, children: apiTestResult.success ? /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", alignItems: "center", gap: "6px" }, children: [
                  /* @__PURE__ */ jsxRuntimeExports.jsx(CheckCircle, { style: { width: "14px", height: "14px" } }),
                  "API key is valid and working correctly"
                ] }) : /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", alignItems: "center", gap: "6px" }, children: [
                  /* @__PURE__ */ jsxRuntimeExports.jsx(AlertTriangle, { style: { width: "14px", height: "14px" } }),
                  apiTestResult.error
                ] }) }),
                /* @__PURE__ */ jsxRuntimeExports.jsx(
                  "a",
                  {
                    href: "https://aistudio.google.com/app/apikey",
                    target: "_blank",
                    rel: "noopener noreferrer",
                    className: "aca-page-description",
                    style: {
                      color: "#0073aa",
                      textDecoration: "none",
                      marginTop: "8px",
                      display: "block"
                    },
                    children: "→ Get your Google AI API key"
                  }
                )
              ] })
            }
          ),
          /* @__PURE__ */ jsxRuntimeExports.jsxs(
            IntegrationCard,
            {
              title: "Featured Image Source",
              icon: /* @__PURE__ */ jsxRuntimeExports.jsx(Image, { style: { width: "20px", height: "20px", color: "white" } }),
              isConfigured: isImageSourceConfigured(),
              children: [
                /* @__PURE__ */ jsxRuntimeExports.jsx("p", { className: "aca-page-description", style: { marginBottom: "20px" }, children: "Select where to get featured images. For stock photo sites, an API key is required." }),
                /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: {
                  display: "grid",
                  gridTemplateColumns: "repeat(auto-fit, minmax(120px, 1fr))",
                  gap: "12px",
                  marginBottom: "25px"
                }, children: ["pexels", "unsplash", "pixabay", "ai"].map((provider) => /* @__PURE__ */ jsxRuntimeExports.jsxs(
                  "label",
                  {
                    className: `aca-button ${currentSettings.imageSourceProvider === provider ? "" : "secondary"}`,
                    style: {
                      textTransform: "capitalize",
                      cursor: "pointer",
                      textAlign: "center",
                      margin: 0,
                      padding: "12px 8px",
                      fontSize: "14px"
                    },
                    children: [
                      /* @__PURE__ */ jsxRuntimeExports.jsx(
                        "input",
                        {
                          type: "radio",
                          name: "image-source-provider",
                          value: provider,
                          checked: currentSettings.imageSourceProvider === provider,
                          onChange: (e) => handleSettingChange("imageSourceProvider", e.target.value),
                          style: { display: "none" }
                        }
                      ),
                      provider === "ai" ? "AI Generated" : provider
                    ]
                  },
                  provider
                )) }),
                currentSettings.imageSourceProvider === "ai" && /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-fade-in", children: [
                  /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-form-group", children: [
                    /* @__PURE__ */ jsxRuntimeExports.jsx("label", { htmlFor: "ai-image-style", className: "aca-label", children: "AI Image Style" }),
                    /* @__PURE__ */ jsxRuntimeExports.jsxs(
                      "select",
                      {
                        id: "ai-image-style",
                        value: currentSettings.aiImageStyle,
                        onChange: (e) => handleSettingChange("aiImageStyle", e.target.value),
                        className: "aca-input",
                        style: { maxWidth: "200px" },
                        children: [
                          /* @__PURE__ */ jsxRuntimeExports.jsx("option", { value: "photorealistic", children: "Photorealistic" }),
                          /* @__PURE__ */ jsxRuntimeExports.jsx("option", { value: "digital_art", children: "Digital Art" })
                        ]
                      }
                    )
                  ] }),
                  /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-form-group", children: [
                    /* @__PURE__ */ jsxRuntimeExports.jsx("label", { htmlFor: "google-cloud-project-id", className: "aca-label", children: "Google Cloud Project ID" }),
                    /* @__PURE__ */ jsxRuntimeExports.jsx(
                      "input",
                      {
                        id: "google-cloud-project-id",
                        type: "text",
                        placeholder: "Enter your Google Cloud Project ID",
                        value: currentSettings.googleCloudProjectId || "",
                        onChange: (e) => handleSettingChange("googleCloudProjectId", e.target.value),
                        className: "aca-input"
                      }
                    ),
                    /* @__PURE__ */ jsxRuntimeExports.jsx("p", { className: "aca-page-description", style: { marginTop: "8px", fontSize: "13px" }, children: "Required for AI image generation using Google's Imagen API" })
                  ] }),
                  /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-form-group", children: [
                    /* @__PURE__ */ jsxRuntimeExports.jsx("label", { htmlFor: "google-cloud-location", className: "aca-label", children: "Google Cloud Location" }),
                    /* @__PURE__ */ jsxRuntimeExports.jsxs(
                      "select",
                      {
                        id: "google-cloud-location",
                        value: currentSettings.googleCloudLocation || "us-central1",
                        onChange: (e) => handleSettingChange("googleCloudLocation", e.target.value),
                        className: "aca-input",
                        style: { maxWidth: "200px" },
                        children: [
                          /* @__PURE__ */ jsxRuntimeExports.jsx("option", { value: "us-central1", children: "us-central1" }),
                          /* @__PURE__ */ jsxRuntimeExports.jsx("option", { value: "us-east1", children: "us-east1" }),
                          /* @__PURE__ */ jsxRuntimeExports.jsx("option", { value: "us-west1", children: "us-west1" }),
                          /* @__PURE__ */ jsxRuntimeExports.jsx("option", { value: "europe-west1", children: "europe-west1" }),
                          /* @__PURE__ */ jsxRuntimeExports.jsx("option", { value: "asia-southeast1", children: "asia-southeast1" })
                        ]
                      }
                    ),
                    /* @__PURE__ */ jsxRuntimeExports.jsx("p", { className: "aca-page-description", style: { marginTop: "8px", fontSize: "13px" }, children: "Choose the Google Cloud region closest to your users" })
                  ] }),
                  /* @__PURE__ */ jsxRuntimeExports.jsx(
                    "a",
                    {
                      href: "https://cloud.google.com/vertex-ai/generative-ai/docs/image/overview",
                      target: "_blank",
                      rel: "noopener noreferrer",
                      className: "aca-page-description",
                      style: {
                        color: "#0073aa",
                        textDecoration: "none",
                        marginTop: "8px",
                        display: "block"
                      },
                      children: "→ Learn how to set up Google Cloud Vertex AI for Imagen"
                    }
                  )
                ] }),
                currentSettings.imageSourceProvider === "pexels" && /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-form-group aca-fade-in", children: [
                  /* @__PURE__ */ jsxRuntimeExports.jsx("label", { htmlFor: "pexels-api-key", className: "aca-label", children: "Pexels API Key" }),
                  /* @__PURE__ */ jsxRuntimeExports.jsx(
                    "input",
                    {
                      id: "pexels-api-key",
                      type: "password",
                      placeholder: "Enter Pexels API Key",
                      value: currentSettings.pexelsApiKey,
                      onChange: (e) => handleSettingChange("pexelsApiKey", e.target.value),
                      className: "aca-input"
                    }
                  ),
                  /* @__PURE__ */ jsxRuntimeExports.jsx(
                    "a",
                    {
                      href: "https://www.pexels.com/api/",
                      target: "_blank",
                      rel: "noopener noreferrer",
                      className: "aca-page-description",
                      style: {
                        color: "#0073aa",
                        textDecoration: "none",
                        marginTop: "8px",
                        display: "block"
                      },
                      children: "→ Get your Pexels API key"
                    }
                  )
                ] }),
                currentSettings.imageSourceProvider === "unsplash" && /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-form-group aca-fade-in", children: [
                  /* @__PURE__ */ jsxRuntimeExports.jsx("label", { htmlFor: "unsplash-api-key", className: "aca-label", children: "Unsplash Access Key" }),
                  /* @__PURE__ */ jsxRuntimeExports.jsx(
                    "input",
                    {
                      id: "unsplash-api-key",
                      type: "password",
                      placeholder: "Enter Unsplash Access Key",
                      value: currentSettings.unsplashApiKey,
                      onChange: (e) => handleSettingChange("unsplashApiKey", e.target.value),
                      className: "aca-input"
                    }
                  ),
                  /* @__PURE__ */ jsxRuntimeExports.jsx(
                    "a",
                    {
                      href: "https://unsplash.com/developers",
                      target: "_blank",
                      rel: "noopener noreferrer",
                      className: "aca-page-description",
                      style: {
                        color: "#0073aa",
                        textDecoration: "none",
                        marginTop: "8px",
                        display: "block"
                      },
                      children: "→ Get your Unsplash Access key"
                    }
                  )
                ] }),
                currentSettings.imageSourceProvider === "pixabay" && /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-form-group aca-fade-in", children: [
                  /* @__PURE__ */ jsxRuntimeExports.jsx("label", { htmlFor: "pixabay-api-key", className: "aca-label", children: "Pixabay API Key" }),
                  /* @__PURE__ */ jsxRuntimeExports.jsx(
                    "input",
                    {
                      id: "pixabay-api-key",
                      type: "password",
                      placeholder: "Enter Pixabay API Key",
                      value: currentSettings.pixabayApiKey,
                      onChange: (e) => handleSettingChange("pixabayApiKey", e.target.value),
                      className: "aca-input"
                    }
                  ),
                  /* @__PURE__ */ jsxRuntimeExports.jsx(
                    "a",
                    {
                      href: "https://pixabay.com/api/docs/",
                      target: "_blank",
                      rel: "noopener noreferrer",
                      className: "aca-page-description",
                      style: {
                        color: "#0073aa",
                        textDecoration: "none",
                        marginTop: "8px",
                        display: "block"
                      },
                      children: "→ Get your Pixabay API key"
                    }
                  )
                ] })
              ]
            }
          )
        ]
      }
    );
  };
  const SettingsContent = ({
    settings,
    onSaveSettings,
    onShowToast,
    isProActive
  }) => {
    const [currentSettings, setCurrentSettings] = reactExports.useState(settings);
    const [isDirty, setIsDirty] = reactExports.useState(false);
    const [isSaving, setIsSaving] = reactExports.useState(false);
    const [detectedSeoPlugins, setDetectedSeoPlugins] = reactExports.useState([]);
    const [seoPluginsLoading, setSeoPluginsLoading] = reactExports.useState(true);
    const [isDetectingSeo, setIsDetectingSeo] = reactExports.useState(false);
    const [gscAuthStatus, setGscAuthStatus] = reactExports.useState(null);
    const [isConnecting, setIsConnecting] = reactExports.useState(false);
    const handleSettingChange = (key, value) => {
      const updatedSettings = { ...currentSettings, [key]: value };
      setCurrentSettings(updatedSettings);
      setIsDirty(true);
    };
    const handleSave = async () => {
      if (!isDirty) return;
      setIsSaving(true);
      try {
        await onSaveSettings(currentSettings);
        setIsDirty(false);
        onShowToast("Content settings saved successfully!", "success");
      } catch (error) {
        onShowToast("Failed to save content settings", "error");
      } finally {
        setIsSaving(false);
      }
    };
    reactExports.useEffect(() => {
      const loadSeoPlugins = async () => {
        if (!window.acaData) return;
        try {
          const response = await fetch(window.acaData.api_url + "seo-plugins", {
            headers: { "X-WP-Nonce": window.acaData.nonce }
          });
          const data = await response.json();
          console.log("ACA: SEO plugins data:", data);
          if (data.success) {
            setDetectedSeoPlugins(data.plugins || data.detected_plugins || []);
          } else {
            setDetectedSeoPlugins(data.detected_plugins || []);
          }
        } catch (error) {
          console.error("Failed to load SEO plugins:", error);
        } finally {
          setSeoPluginsLoading(false);
        }
      };
      const loadGscStatus = async () => {
        if (!window.acaData) return;
        try {
          const response = await fetch(window.acaData.api_url + "gsc/status", {
            headers: { "X-WP-Nonce": window.acaData.nonce }
          });
          const data = await response.json();
          setGscAuthStatus(data);
        } catch (error) {
          console.error("Failed to load GSC status:", error);
        }
      };
      loadSeoPlugins();
      loadGscStatus();
    }, []);
    const handleAutoDetectSeo = async () => {
      if (!window.acaData) return;
      setIsDetectingSeo(true);
      try {
        const response = await fetch(window.acaData.api_url + "seo-plugins", {
          method: "POST",
          headers: { "X-WP-Nonce": window.acaData.nonce }
        });
        const data = await response.json();
        console.log("ACA: SEO plugins refresh data:", data);
        if (data.success) {
          setDetectedSeoPlugins(data.plugins || data.detected_plugins || []);
          onShowToast("SEO plugins detection completed!", "success");
        } else {
          setDetectedSeoPlugins(data.detected_plugins || []);
          if (data.detected_plugins && data.detected_plugins.length > 0) {
            onShowToast("SEO plugins detection completed!", "success");
          } else {
            onShowToast("Failed to detect SEO plugins", "error");
          }
        }
      } catch (error) {
        console.error("SEO plugin detection error:", error);
        onShowToast("Failed to detect SEO plugins", "error");
      } finally {
        setIsDetectingSeo(false);
      }
    };
    const handleGSCConnect = async () => {
      if (!window.acaData || !currentSettings.gscClientId || !currentSettings.gscClientSecret) return;
      setIsConnecting(true);
      try {
        const response = await fetch(window.acaData.api_url + "gsc/connect", {
          method: "POST",
          headers: {
            "X-WP-Nonce": window.acaData.nonce,
            "Content-Type": "application/json"
          },
          body: JSON.stringify({
            client_id: currentSettings.gscClientId,
            client_secret: currentSettings.gscClientSecret
          })
        });
        const data = await response.json();
        if (data.success && data.auth_url) {
          window.open(data.auth_url, "_blank");
          onShowToast("Opening Google authorization window...", "info");
        } else {
          onShowToast(data.message || "Failed to connect to Google Search Console", "error");
        }
      } catch (error) {
        console.error("GSC connection error:", error);
        onShowToast("Failed to connect to Google Search Console", "error");
      } finally {
        setIsConnecting(false);
      }
    };
    const handleGSCDisconnect = async () => {
      if (!window.acaData) return;
      setIsConnecting(true);
      try {
        const response = await fetch(window.acaData.api_url + "gsc/disconnect", {
          method: "POST",
          headers: { "X-WP-Nonce": window.acaData.nonce }
        });
        const data = await response.json();
        if (data.success) {
          setGscAuthStatus({ authenticated: false });
          onShowToast("Disconnected from Google Search Console", "success");
        } else {
          onShowToast("Failed to disconnect from Google Search Console", "error");
        }
      } catch (error) {
        console.error("GSC disconnection error:", error);
        onShowToast("Failed to disconnect from Google Search Console", "error");
      } finally {
        setIsConnecting(false);
      }
    };
    const saveButton = isDirty ? /* @__PURE__ */ jsxRuntimeExports.jsx(
      "button",
      {
        onClick: handleSave,
        disabled: isSaving,
        className: "aca-button aca-button-primary",
        style: { minWidth: "120px" },
        children: isSaving ? "Saving..." : "Save Changes"
      }
    ) : null;
    return /* @__PURE__ */ jsxRuntimeExports.jsxs(
      SettingsLayout,
      {
        title: "Content & SEO",
        description: "Configure content analysis and SEO optimization settings",
        icon: /* @__PURE__ */ jsxRuntimeExports.jsx(Target, { style: { width: "24px", height: "24px", color: "white" } }),
        actions: saveButton,
        children: [
          /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-card", style: { margin: "0 0 24px 0" }, children: [
            /* @__PURE__ */ jsxRuntimeExports.jsx("div", { className: "aca-card-header", children: /* @__PURE__ */ jsxRuntimeExports.jsxs("h3", { className: "aca-card-title", style: { display: "flex", alignItems: "center", gap: "12px", margin: "0 0 16px 0" }, children: [
              /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: {
                width: "40px",
                height: "40px",
                background: "linear-gradient(135deg, #667eea 0%, #764ba2 100%)",
                borderRadius: "10px",
                display: "flex",
                alignItems: "center",
                justifyContent: "center"
              }, children: /* @__PURE__ */ jsxRuntimeExports.jsx(Search, { style: { width: "20px", height: "20px", color: "white" } }) }),
              "Content Analysis"
            ] }) }),
            /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-form-group", children: [
              /* @__PURE__ */ jsxRuntimeExports.jsx("label", { className: "aca-label", htmlFor: "analyze-frequency", children: "Analysis Frequency" }),
              /* @__PURE__ */ jsxRuntimeExports.jsxs(
                "select",
                {
                  id: "analyze-frequency",
                  className: "aca-input",
                  value: currentSettings.analyzeContentFrequency || "manual",
                  onChange: (e) => handleSettingChange("analyzeContentFrequency", e.target.value),
                  style: { marginTop: "5px" },
                  children: [
                    /* @__PURE__ */ jsxRuntimeExports.jsx("option", { value: "manual", children: "Manual - Only when you click the analyze button" }),
                    /* @__PURE__ */ jsxRuntimeExports.jsx("option", { value: "daily", children: "Daily - Analyze content automatically every day" }),
                    /* @__PURE__ */ jsxRuntimeExports.jsx("option", { value: "weekly", children: "Weekly - Analyze content automatically every week" }),
                    /* @__PURE__ */ jsxRuntimeExports.jsx("option", { value: "monthly", children: "Monthly - Analyze content automatically every month" })
                  ]
                }
              ),
              /* @__PURE__ */ jsxRuntimeExports.jsx("p", { className: "aca-page-description", style: { marginTop: "5px", margin: "5px 0 0 0" }, children: "How often should the AI automatically analyze your site content to update the style guide? Manual mode gives you full control." })
            ] })
          ] }),
          /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-card", style: { margin: "0 0 24px 0" }, children: [
            /* @__PURE__ */ jsxRuntimeExports.jsx("div", { className: "aca-card-header", children: /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", justifyContent: "space-between", alignItems: "center", marginBottom: "16px" }, children: [
              /* @__PURE__ */ jsxRuntimeExports.jsxs("h3", { className: "aca-card-title", style: { display: "flex", alignItems: "center", gap: "12px", margin: 0 }, children: [
                /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: {
                  width: "40px",
                  height: "40px",
                  background: "linear-gradient(135deg, #667eea 0%, #764ba2 100%)",
                  borderRadius: "10px",
                  display: "flex",
                  alignItems: "center",
                  justifyContent: "center"
                }, children: /* @__PURE__ */ jsxRuntimeExports.jsx(Target, { style: { width: "20px", height: "20px", color: "white" } }) }),
                "SEO Integration"
              ] }),
              detectedSeoPlugins.length > 0 && /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-alert success", style: {
                display: "flex",
                alignItems: "center",
                fontSize: "12px",
                fontWeight: "600",
                gap: "6px",
                padding: "4px 8px",
                margin: 0
              }, children: [
                /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: {
                  width: "8px",
                  height: "8px",
                  borderRadius: "50%",
                  background: "#22c55e"
                } }),
                detectedSeoPlugins.length,
                " Plugin",
                detectedSeoPlugins.length > 1 ? "s" : "",
                " Detected"
              ] })
            ] }) }),
            seoPluginsLoading ? /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", alignItems: "center", gap: "10px", padding: "20px 0" }, children: [
              /* @__PURE__ */ jsxRuntimeExports.jsx("span", { className: "aca-spinner" }),
              /* @__PURE__ */ jsxRuntimeExports.jsx("span", { children: "Detecting SEO plugins..." })
            ] }) : detectedSeoPlugins.length > 0 ? /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { children: [
              /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: {
                padding: "12px 16px",
                backgroundColor: "#f0f9ff",
                borderRadius: "8px",
                marginBottom: "20px",
                border: "1px solid #bae6fd"
              }, children: [
                /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", alignItems: "center", gap: "8px", marginBottom: "8px" }, children: [
                  /* @__PURE__ */ jsxRuntimeExports.jsx("span", { style: { color: "#0ea5e9", fontSize: "18px" }, children: "ℹ️" }),
                  /* @__PURE__ */ jsxRuntimeExports.jsx("strong", { style: { color: "#0c4a6e" }, children: "Automatic SEO Integration Active" })
                ] }),
                /* @__PURE__ */ jsxRuntimeExports.jsx("p", { style: { margin: "0", fontSize: "14px", color: "#0c4a6e", lineHeight: "1.4" }, children: "AI-generated content will automatically include SEO titles, meta descriptions, focus keywords, social media tags, and schema markup for all detected plugins." })
              ] }),
              /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { marginBottom: "20px" }, children: [
                /* @__PURE__ */ jsxRuntimeExports.jsxs("h4", { style: { margin: "0 0 12px 0", fontSize: "16px", fontWeight: "600", color: "#374151" }, children: [
                  "Detected SEO Plugins (",
                  detectedSeoPlugins.length,
                  ")"
                ] }),
                /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: { display: "grid", gap: "12px" }, children: detectedSeoPlugins.map((plugin, index) => {
                  const getPluginIcon = (pluginType) => {
                    switch (pluginType) {
                      case "rank_math":
                        return "🏆";
                      case "yoast":
                        return "🟢";
                      case "aioseo":
                        return "🔵";
                      default:
                        return "🔧";
                    }
                  };
                  const getPluginColor = (pluginType) => {
                    switch (pluginType) {
                      case "rank_math":
                        return { bg: "#fef3c7", border: "#f59e0b", text: "#92400e" };
                      case "yoast":
                        return { bg: "#dcfce7", border: "#22c55e", text: "#166534" };
                      case "aioseo":
                        return { bg: "#dbeafe", border: "#3b82f6", text: "#1e40af" };
                      default:
                        return { bg: "#f3f4f6", border: "#6b7280", text: "#374151" };
                    }
                  };
                  const colors = getPluginColor(plugin.plugin);
                  return /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: {
                    padding: "16px",
                    backgroundColor: colors.bg,
                    borderRadius: "8px",
                    border: `1px solid ${colors.border}`,
                    position: "relative"
                  }, children: /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", alignItems: "center", gap: "12px" }, children: [
                    /* @__PURE__ */ jsxRuntimeExports.jsx("span", { style: { fontSize: "24px" }, children: getPluginIcon(plugin.plugin) }),
                    /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { flex: 1 }, children: [
                      /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", alignItems: "center", gap: "8px", marginBottom: "4px" }, children: [
                        /* @__PURE__ */ jsxRuntimeExports.jsx("strong", { style: { color: colors.text, fontSize: "15px" }, children: plugin.name }),
                        /* @__PURE__ */ jsxRuntimeExports.jsxs("span", { style: {
                          color: "#6b7280",
                          fontSize: "13px",
                          backgroundColor: "rgba(255,255,255,0.7)",
                          padding: "2px 6px",
                          borderRadius: "4px"
                        }, children: [
                          "v",
                          plugin.version
                        ] })
                      ] }),
                      /* @__PURE__ */ jsxRuntimeExports.jsx("p", { style: { margin: "0", fontSize: "13px", color: colors.text, lineHeight: "1.4" }, children: "Automatic integration includes: SEO titles, meta descriptions, focus keywords, social media tags, and schema markup." })
                    ] }),
                    /* @__PURE__ */ jsxRuntimeExports.jsxs("span", { style: {
                      backgroundColor: "#22c55e",
                      color: "white",
                      padding: "4px 8px",
                      borderRadius: "12px",
                      fontSize: "12px",
                      fontWeight: "600",
                      display: "flex",
                      alignItems: "center",
                      gap: "4px"
                    }, children: [
                      /* @__PURE__ */ jsxRuntimeExports.jsx("span", { style: { fontSize: "10px" }, children: "●" }),
                      "ACTIVE"
                    ] })
                  ] }) }, plugin.plugin);
                }) })
              ] }),
              /* @__PURE__ */ jsxRuntimeExports.jsxs(
                "button",
                {
                  onClick: handleAutoDetectSeo,
                  disabled: isDetectingSeo,
                  className: "aca-button secondary",
                  style: { width: "100%", justifyContent: "center" },
                  children: [
                    isDetectingSeo && /* @__PURE__ */ jsxRuntimeExports.jsx("span", { className: "aca-spinner" }),
                    isDetectingSeo ? "Re-detecting SEO plugins..." : "🔄 Refresh Detection"
                  ]
                }
              )
            ] }) : /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { children: [
              /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: {
                padding: "20px",
                backgroundColor: "#fef3c7",
                borderRadius: "8px",
                marginBottom: "20px",
                border: "1px solid #f59e0b",
                textAlign: "center"
              }, children: [
                /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: { fontSize: "48px", marginBottom: "12px" }, children: "⚠️" }),
                /* @__PURE__ */ jsxRuntimeExports.jsx("h4", { style: { margin: "0 0 8px 0", color: "#92400e", fontSize: "16px" }, children: "No SEO Plugins Detected" }),
                /* @__PURE__ */ jsxRuntimeExports.jsx("p", { style: { margin: "0 0 16px 0", color: "#92400e", fontSize: "14px", lineHeight: "1.4" }, children: "Install one of the supported SEO plugins to enable automatic SEO data integration for your AI-generated content." })
              ] }),
              /* @__PURE__ */ jsxRuntimeExports.jsxs(
                "button",
                {
                  onClick: handleAutoDetectSeo,
                  disabled: isDetectingSeo,
                  className: "aca-button primary",
                  style: { width: "100%", justifyContent: "center" },
                  children: [
                    isDetectingSeo && /* @__PURE__ */ jsxRuntimeExports.jsx("span", { className: "aca-spinner" }),
                    isDetectingSeo ? "Detecting plugins..." : "🔍 Check for SEO Plugins"
                  ]
                }
              )
            ] })
          ] }),
          isProActive ? /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-card", style: { margin: "0 0 24px 0" }, children: [
            /* @__PURE__ */ jsxRuntimeExports.jsx("div", { className: "aca-card-header", children: /* @__PURE__ */ jsxRuntimeExports.jsxs("h3", { className: "aca-card-title", style: { display: "flex", alignItems: "center", gap: "12px", margin: "0 0 16px 0" }, children: [
              /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: {
                width: "40px",
                height: "40px",
                background: "linear-gradient(135deg, #667eea 0%, #764ba2 100%)",
                borderRadius: "10px",
                display: "flex",
                alignItems: "center",
                justifyContent: "center"
              }, children: /* @__PURE__ */ jsxRuntimeExports.jsx(Google, { style: { width: "20px", height: "20px", color: "white" } }) }),
              "Google Search Console",
              /* @__PURE__ */ jsxRuntimeExports.jsx("span", { style: {
                marginLeft: "10px",
                background: "linear-gradient(135deg, #f39c12 0%, #e67e22 100%)",
                color: "white",
                padding: "2px 8px",
                borderRadius: "8px",
                fontSize: "10px",
                fontWeight: "bold"
              }, children: "PRO" })
            ] }) }),
            /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-form-group", children: [
              /* @__PURE__ */ jsxRuntimeExports.jsx("label", { className: "aca-label", children: "Google Search Console Setup" }),
              /* @__PURE__ */ jsxRuntimeExports.jsx("p", { className: "aca-page-description", style: { marginBottom: "15px" }, children: "To connect with Google Search Console, you need to create OAuth2 credentials in your Google Cloud Console." }),
              /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "grid", gap: "15px", marginBottom: "20px" }, children: [
                /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { children: [
                  /* @__PURE__ */ jsxRuntimeExports.jsx("label", { className: "aca-label", children: "Client ID" }),
                  /* @__PURE__ */ jsxRuntimeExports.jsx(
                    "input",
                    {
                      type: "text",
                      value: currentSettings.gscClientId,
                      onChange: (e) => handleSettingChange("gscClientId", e.target.value),
                      placeholder: "Your Google OAuth2 Client ID",
                      className: "aca-input"
                    }
                  )
                ] }),
                /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { children: [
                  /* @__PURE__ */ jsxRuntimeExports.jsx("label", { className: "aca-label", children: "Client Secret" }),
                  /* @__PURE__ */ jsxRuntimeExports.jsx(
                    "input",
                    {
                      type: "password",
                      value: currentSettings.gscClientSecret,
                      onChange: (e) => handleSettingChange("gscClientSecret", e.target.value),
                      placeholder: "Your Google OAuth2 Client Secret",
                      className: "aca-input"
                    }
                  )
                ] })
              ] })
            ] }),
            /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-stat-item", style: { margin: 0 }, children: [
              /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-stat-info", children: [
                /* @__PURE__ */ jsxRuntimeExports.jsx("div", { className: "aca-stat-icon", children: /* @__PURE__ */ jsxRuntimeExports.jsx(Google, {}) }),
                /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { children: [
                  /* @__PURE__ */ jsxRuntimeExports.jsx("h4", { className: "aca-stat-title", children: "Connection Status" }),
                  gscAuthStatus?.authenticated ? /* @__PURE__ */ jsxRuntimeExports.jsxs("p", { className: "aca-stat-count", style: { color: "#00a32a" }, children: [
                    "Connected as ",
                    gscAuthStatus.user_email
                  ] }) : /* @__PURE__ */ jsxRuntimeExports.jsx("p", { className: "aca-stat-count", children: "Use search data to generate strategic content ideas" })
                ] })
              ] }),
              gscAuthStatus?.authenticated ? /* @__PURE__ */ jsxRuntimeExports.jsx(
                "button",
                {
                  onClick: handleGSCDisconnect,
                  disabled: isConnecting,
                  className: "aca-button",
                  style: {
                    flexShrink: 0,
                    background: "#d63638",
                    borderColor: "#d63638"
                  },
                  children: isConnecting ? "Disconnecting..." : "Disconnect"
                }
              ) : /* @__PURE__ */ jsxRuntimeExports.jsxs(
                "button",
                {
                  onClick: handleGSCConnect,
                  disabled: isConnecting || !currentSettings.gscClientId || !currentSettings.gscClientSecret,
                  className: "aca-button",
                  style: {
                    flexShrink: 0,
                    background: "#00a32a",
                    borderColor: "#00a32a"
                  },
                  children: [
                    isConnecting && /* @__PURE__ */ jsxRuntimeExports.jsx("span", { className: "aca-spinner" }),
                    isConnecting ? "Connecting..." : "Connect"
                  ]
                }
              )
            ] })
          ] }) : /* @__PURE__ */ jsxRuntimeExports.jsx(
            UpgradePrompt,
            {
              title: "Google Search Console Integration",
              description: "Connect your GSC account to generate content ideas based on your search performance data and improve SEO targeting.",
              features: [
                "Data-driven content ideas from your search queries",
                "Target keywords you're already ranking for",
                "Identify content gaps and opportunities",
                "Improve content relevance and SEO performance"
              ]
            }
          )
        ]
      }
    );
  };
  const SettingsAdvanced = ({
    onShowToast
  }) => {
    const handleCheckAutomationStatus = () => {
      if (!window.acaData) {
        console.error("ACA: WordPress data not available");
        return;
      }
      fetch(window.acaData.api_url + "debug/automation", {
        headers: { "X-WP-Nonce": window.acaData.nonce }
      }).then((r) => r.json()).then((data) => {
        console.log("Automation Debug Info:", data);
        onShowToast("Debug info logged to console", "info");
      }).catch((error) => {
        console.error("Error checking automation status:", error);
        onShowToast("Failed to check automation status", "error");
      });
    };
    const handleTestSemiAutoCron = () => {
      if (!window.acaData) {
        console.error("ACA: WordPress data not available");
        return;
      }
      fetch(window.acaData.api_url + "debug/cron/semi-auto", {
        method: "POST",
        headers: { "X-WP-Nonce": window.acaData.nonce }
      }).then((r) => r.json()).then((data) => {
        onShowToast(data.message || "Semi-auto cron triggered", "success");
      }).catch((error) => {
        console.error("Error testing semi-auto cron:", error);
        onShowToast("Failed to test semi-auto cron", "error");
      });
    };
    const handleTestFullAutoCron = () => {
      if (!window.acaData) {
        console.error("ACA: WordPress data not available");
        return;
      }
      fetch(window.acaData.api_url + "debug/cron/full-auto", {
        method: "POST",
        headers: { "X-WP-Nonce": window.acaData.nonce }
      }).then((r) => r.json()).then((data) => {
        onShowToast(data.message || "Full-auto cron triggered", "success");
      }).catch((error) => {
        console.error("Error testing full-auto cron:", error);
        onShowToast("Failed to test full-auto cron", "error");
      });
    };
    return /* @__PURE__ */ jsxRuntimeExports.jsxs(
      SettingsLayout,
      {
        title: "Advanced & Debug",
        description: "Developer tools and advanced debugging features for automation testing",
        icon: /* @__PURE__ */ jsxRuntimeExports.jsx(Brain, { style: { width: "24px", height: "24px", color: "white" } }),
        children: [
          /* @__PURE__ */ jsxRuntimeExports.jsx("div", { className: "aca-alert info", style: { marginBottom: "20px" }, children: /* @__PURE__ */ jsxRuntimeExports.jsxs("p", { style: { margin: 0, fontSize: "14px" }, children: [
            /* @__PURE__ */ jsxRuntimeExports.jsx("strong", { children: "🛠️ For Developers & Advanced Users:" }),
            " This panel is designed for testing and debugging automation features. Use these tools to manually trigger automation tasks, check cron job status, and troubleshoot issues. Regular users typically don't need to use this panel."
          ] }) }),
          /* @__PURE__ */ jsxRuntimeExports.jsx("p", { className: "aca-page-description", style: { marginBottom: "20px" }, children: "Test automation functionality and check cron status. Click the buttons below to manually trigger automation tasks or check their status." }),
          /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", gap: "10px", marginBottom: "20px", flexWrap: "wrap" }, children: [
            /* @__PURE__ */ jsxRuntimeExports.jsx(
              "button",
              {
                className: "aca-action-button",
                onClick: handleCheckAutomationStatus,
                style: {
                  padding: "12px 20px",
                  backgroundColor: "#3b82f6",
                  color: "white",
                  border: "none",
                  borderRadius: "8px",
                  cursor: "pointer",
                  fontSize: "14px",
                  fontWeight: "500",
                  transition: "background-color 0.2s"
                },
                onMouseEnter: (e) => e.currentTarget.style.backgroundColor = "#2563eb",
                onMouseLeave: (e) => e.currentTarget.style.backgroundColor = "#3b82f6",
                children: "Check Automation Status"
              }
            ),
            /* @__PURE__ */ jsxRuntimeExports.jsx(
              "button",
              {
                className: "aca-action-button",
                onClick: handleTestSemiAutoCron,
                style: {
                  padding: "12px 20px",
                  backgroundColor: "#10b981",
                  color: "white",
                  border: "none",
                  borderRadius: "8px",
                  cursor: "pointer",
                  fontSize: "14px",
                  fontWeight: "500",
                  transition: "background-color 0.2s"
                },
                onMouseEnter: (e) => e.currentTarget.style.backgroundColor = "#059669",
                onMouseLeave: (e) => e.currentTarget.style.backgroundColor = "#10b981",
                children: "Test Semi-Auto Cron"
              }
            ),
            /* @__PURE__ */ jsxRuntimeExports.jsx(
              "button",
              {
                className: "aca-action-button",
                onClick: handleTestFullAutoCron,
                style: {
                  padding: "12px 20px",
                  backgroundColor: "#f59e0b",
                  color: "white",
                  border: "none",
                  borderRadius: "8px",
                  cursor: "pointer",
                  fontSize: "14px",
                  fontWeight: "500",
                  transition: "background-color 0.2s"
                },
                onMouseEnter: (e) => e.currentTarget.style.backgroundColor = "#d97706",
                onMouseLeave: (e) => e.currentTarget.style.backgroundColor = "#f59e0b",
                children: "Test Full-Auto Cron"
              }
            )
          ] }),
          /* @__PURE__ */ jsxRuntimeExports.jsx("div", { className: "aca-alert warning", style: { marginTop: "20px" }, children: /* @__PURE__ */ jsxRuntimeExports.jsxs("p", { style: { margin: 0, fontSize: "13px" }, children: [
            /* @__PURE__ */ jsxRuntimeExports.jsx("strong", { children: "⚠️ Important:" }),
            " These debug functions are for testing purposes only. They may trigger content generation or publishing actions. Use with caution on production sites."
          ] }) })
        ]
      }
    );
  };
  const DraftModal = ({ draft, onClose, onSave, settings }) => {
    const { seoPlugin } = settings;
    const [seoStatus, setSeoStatus] = reactExports.useState("idle");
    const [seoAnalysis, setSeoAnalysis] = reactExports.useState(null);
    const [isSaving, setIsSaving] = reactExports.useState(false);
    const [editedTitle, setEditedTitle] = reactExports.useState(draft.title);
    const [editedContent, setEditedContent] = reactExports.useState(draft.content);
    const [editedMetaTitle, setEditedMetaTitle] = reactExports.useState(draft.metaTitle);
    const [editedMetaDescription, setEditedMetaDescription] = reactExports.useState(draft.metaDescription);
    const [editedFocusKeywords, setEditedFocusKeywords] = reactExports.useState([]);
    const originalFocusKeywords = Array.from({ length: 5 }, (_, i) => draft.focusKeywords?.[i] || "");
    const isDirty = draft.title !== editedTitle || draft.content !== editedContent || draft.metaTitle !== editedMetaTitle || draft.metaDescription !== editedMetaDescription || JSON.stringify(originalFocusKeywords) !== JSON.stringify(editedFocusKeywords);
    reactExports.useEffect(() => {
      setEditedTitle(draft.title);
      setEditedContent(draft.content);
      setEditedMetaTitle(draft.metaTitle);
      setEditedMetaDescription(draft.metaDescription);
      setEditedFocusKeywords(Array.from({ length: 5 }, (_, i) => draft.focusKeywords?.[i] || ""));
      setSeoStatus("idle");
      setSeoAnalysis(null);
    }, [draft]);
    const handleSendToSeoPlugin = () => {
      if (seoStatus !== "idle") return;
      setSeoStatus("sending");
      setTimeout(() => {
        setSeoAnalysis({
          score: Math.floor(Math.random() * (95 - 75 + 1) + 75),
          // Random score between 75-95
          suggestions: [
            { text: "Anahtar kelime ilk paragrafta geçiyor.", type: "good" },
            { text: "Meta açıklama uzunluğu ideal.", type: "good" },
            { text: "Yüksek otoriteye sahip bir siteye harici bir bağlantı eklemeyi düşünün.", type: "improvement" }
          ]
        });
        setSeoStatus("analyzed");
      }, 1500);
    };
    const handleKeywordChange = (index, value) => {
      const newKeywords = [...editedFocusKeywords];
      newKeywords[index] = value;
      setEditedFocusKeywords(newKeywords);
    };
    const handleSave = () => {
      if (!isDirty) return;
      setIsSaving(true);
      const updates = {
        title: editedTitle,
        content: editedContent,
        metaTitle: editedMetaTitle,
        metaDescription: editedMetaDescription,
        focusKeywords: editedFocusKeywords
      };
      setTimeout(() => {
        onSave(draft.id, updates);
        setIsSaving(false);
      }, 700);
    };
    const seoPluginName = seoPlugin === "rank_math" ? "Rank Math" : "Yoast SEO";
    return /* @__PURE__ */ jsxRuntimeExports.jsx("div", { className: "fixed inset-0 bg-black bg-opacity-80 flex items-center justify-center z-40 p-0 sm:p-4", onClick: onClose, children: /* @__PURE__ */ jsxRuntimeExports.jsxs(
      "div",
      {
        className: "bg-slate-800 sm:rounded-lg shadow-2xl w-full max-w-5xl h-full sm:h-[90vh] flex flex-col overflow-hidden border border-slate-700",
        onClick: (e) => e.stopPropagation(),
        children: [
          /* @__PURE__ */ jsxRuntimeExports.jsxs("header", { className: "flex-shrink-0 p-4 border-b border-slate-700 flex justify-between items-center bg-slate-800/80 backdrop-blur-sm", children: [
            /* @__PURE__ */ jsxRuntimeExports.jsx(
              "input",
              {
                type: "text",
                value: editedTitle,
                onChange: (e) => setEditedTitle(e.target.value),
                className: "text-xl font-bold text-white bg-transparent border-0 focus:ring-0 w-full p-0",
                placeholder: "Article Title"
              }
            ),
            /* @__PURE__ */ jsxRuntimeExports.jsx("button", { onClick: onClose, className: "text-slate-400 hover:text-white p-1 rounded-full hover:bg-slate-600 flex-shrink-0 ml-4", children: /* @__PURE__ */ jsxRuntimeExports.jsx(X, { className: "h-6 w-6" }) })
          ] }),
          /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "flex-grow overflow-y-auto p-4 pt-6 sm:p-8 space-y-8", children: [
            /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { children: [
              /* @__PURE__ */ jsxRuntimeExports.jsx("h3", { className: "text-lg font-semibold mb-3 text-sky-400", children: "Featured Image" }),
              /* @__PURE__ */ jsxRuntimeExports.jsx("img", { src: draft.featuredImage, alt: draft.title, className: "w-full h-auto max-h-96 object-cover rounded-md shadow-lg border border-slate-700" })
            ] }),
            /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "max-w-prose mx-auto", children: [
              /* @__PURE__ */ jsxRuntimeExports.jsx("label", { htmlFor: "content-editor", className: "text-lg font-semibold mb-3 text-sky-400 block", children: "Content (Markdown)" }),
              /* @__PURE__ */ jsxRuntimeExports.jsx(
                "textarea",
                {
                  id: "content-editor",
                  value: editedContent,
                  onChange: (e) => setEditedContent(e.target.value),
                  className: "w-full h-96 bg-slate-900/50 border border-slate-600 rounded-md p-4 text-slate-300 text-base leading-relaxed focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition",
                  placeholder: "Write your article content here..."
                }
              )
            ] }),
            /* @__PURE__ */ jsxRuntimeExports.jsx("div", { className: "max-w-prose mx-auto pt-6 border-t border-slate-700", children: /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "bg-slate-900 p-6 rounded-lg border border-slate-700/80 shadow-inner", children: [
              /* @__PURE__ */ jsxRuntimeExports.jsx("h3", { className: "text-lg font-semibold text-green-400 mb-4", children: "SEO Data" }),
              /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "space-y-4", children: [
                /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { children: [
                  /* @__PURE__ */ jsxRuntimeExports.jsx("label", { htmlFor: "meta-title-editor", className: "block text-sm font-medium text-slate-400 mb-1", children: "Meta Title" }),
                  /* @__PURE__ */ jsxRuntimeExports.jsx(
                    "input",
                    {
                      id: "meta-title-editor",
                      type: "text",
                      value: editedMetaTitle,
                      onChange: (e) => setEditedMetaTitle(e.target.value),
                      className: "w-full bg-slate-700 border border-slate-600 rounded-md p-2 text-sm text-slate-200 focus:ring-2 focus:ring-blue-500",
                      placeholder: "SEO Meta Title"
                    }
                  )
                ] }),
                /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { children: [
                  /* @__PURE__ */ jsxRuntimeExports.jsx("label", { htmlFor: "meta-desc-editor", className: "block text-sm font-medium text-slate-400 mb-1", children: "Meta Description" }),
                  /* @__PURE__ */ jsxRuntimeExports.jsx(
                    "textarea",
                    {
                      id: "meta-desc-editor",
                      value: editedMetaDescription,
                      onChange: (e) => setEditedMetaDescription(e.target.value),
                      rows: 3,
                      className: "w-full bg-slate-700 border border-slate-600 rounded-md p-2 text-sm text-slate-300 leading-relaxed focus:ring-2 focus:ring-blue-500",
                      placeholder: "SEO Meta Description"
                    }
                  )
                ] }),
                /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { children: [
                  /* @__PURE__ */ jsxRuntimeExports.jsx("label", { htmlFor: "focus-keywords", className: "block text-sm font-medium text-slate-400 mb-2", children: "Focus Keywords" }),
                  /* @__PURE__ */ jsxRuntimeExports.jsx("div", { id: "focus-keywords", className: "space-y-2", children: editedFocusKeywords.map((keyword, index) => /* @__PURE__ */ jsxRuntimeExports.jsx(
                    "input",
                    {
                      type: "text",
                      value: keyword,
                      onChange: (e) => handleKeywordChange(index, e.target.value),
                      className: "w-full bg-slate-700 border border-slate-600 rounded-md p-2 text-sm text-slate-200 focus:ring-2 focus:ring-blue-500",
                      placeholder: `Focus Keyword ${index + 1}`
                    },
                    index
                  )) })
                ] })
              ] }),
              seoAnalysis && seoStatus === "analyzed" && /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "mt-6 pt-6 border-t border-slate-700/60 animate-fade-in-fast", style: { "--animation-duration": "500ms" }, children: [
                /* @__PURE__ */ jsxRuntimeExports.jsx("h4", { className: "font-semibold text-slate-200 mb-3", children: "SEO Analysis Results" }),
                /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "flex items-center gap-4 bg-slate-800/50 p-3 rounded-md", children: [
                  /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: `flex items-baseline justify-center h-16 w-16 rounded-full border-4 ${seoAnalysis.score > 80 ? "border-green-500" : "border-yellow-500"}`, children: [
                    /* @__PURE__ */ jsxRuntimeExports.jsx("span", { className: "text-2xl font-bold text-white", children: seoAnalysis.score }),
                    /* @__PURE__ */ jsxRuntimeExports.jsx("span", { className: "text-sm text-slate-400 -ml-1", children: "/100" })
                  ] }),
                  /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "flex-1", children: [
                    /* @__PURE__ */ jsxRuntimeExports.jsx("p", { className: `font-bold text-lg ${seoAnalysis.score > 80 ? "text-green-400" : "text-yellow-400"}`, children: "İyi" }),
                    /* @__PURE__ */ jsxRuntimeExports.jsx("p", { className: "text-sm text-slate-400", children: "Yazınız iyi optimize edilmiş." })
                  ] })
                ] }),
                /* @__PURE__ */ jsxRuntimeExports.jsx("ul", { className: "mt-4 space-y-2 text-sm", children: seoAnalysis.suggestions.map((tip, index) => /* @__PURE__ */ jsxRuntimeExports.jsxs("li", { className: "flex items-start gap-2.5", children: [
                  tip.type === "good" ? /* @__PURE__ */ jsxRuntimeExports.jsx(CheckCircle, { className: "h-4 w-4 mt-1 text-green-500 flex-shrink-0" }) : /* @__PURE__ */ jsxRuntimeExports.jsx(Info, { className: "h-4 w-4 mt-1 text-blue-400 flex-shrink-0" }),
                  /* @__PURE__ */ jsxRuntimeExports.jsx("span", { className: "text-slate-300", children: tip.text })
                ] }, index)) })
              ] }),
              seoPlugin !== "none" && /* @__PURE__ */ jsxRuntimeExports.jsx("div", { className: "mt-6 text-right", children: /* @__PURE__ */ jsxRuntimeExports.jsxs(
                "button",
                {
                  onClick: handleSendToSeoPlugin,
                  disabled: seoStatus !== "idle",
                  className: "bg-blue-600 text-white font-bold py-2 px-5 rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center disabled:bg-slate-600 disabled:cursor-not-allowed min-w-[160px]",
                  children: [
                    seoStatus === "sending" && /* @__PURE__ */ jsxRuntimeExports.jsx(Spinner, { className: "mr-2 h-5 w-5" }),
                    seoStatus === "idle" && `${seoPluginName} ile Analiz Et`,
                    seoStatus === "sending" && "Analiz ediliyor...",
                    seoStatus === "analyzed" && /* @__PURE__ */ jsxRuntimeExports.jsxs(jsxRuntimeExports.Fragment, { children: [
                      /* @__PURE__ */ jsxRuntimeExports.jsx(CheckCircle, { className: "mr-2 h-5 w-5" }),
                      " Analiz Edildi!"
                    ] })
                  ]
                }
              ) })
            ] }) })
          ] }),
          /* @__PURE__ */ jsxRuntimeExports.jsxs("footer", { className: "flex-shrink-0 p-3 border-t border-slate-700 flex justify-end items-center bg-slate-800/80 space-x-3", children: [
            /* @__PURE__ */ jsxRuntimeExports.jsx(
              "button",
              {
                onClick: onClose,
                className: "bg-slate-600 text-white px-4 py-2 rounded-md text-sm font-semibold hover:bg-slate-700 transition-colors",
                children: "Kapat"
              }
            ),
            /* @__PURE__ */ jsxRuntimeExports.jsxs(
              "button",
              {
                onClick: handleSave,
                disabled: !isDirty || isSaving,
                className: "bg-blue-600 text-white font-bold px-5 py-2 rounded-md hover:bg-blue-700 transition-colors flex items-center justify-center disabled:bg-slate-500 disabled:cursor-not-allowed min-w-[100px]",
                children: [
                  isSaving ? /* @__PURE__ */ jsxRuntimeExports.jsx(Spinner, { className: "mr-2 h-5 w-5" }) : null,
                  isSaving ? "Kaydediliyor..." : "Kaydet"
                ]
              }
            )
          ] })
        ]
      }
    ) });
  };
  const ToastIcon = ({ type }) => {
    const iconProps = { style: { width: "16px", height: "16px", color: "white" } };
    switch (type) {
      case "success":
        return /* @__PURE__ */ jsxRuntimeExports.jsx(CheckCircle, { ...iconProps });
      case "error":
        return /* @__PURE__ */ jsxRuntimeExports.jsx(XCircle, { ...iconProps });
      case "warning":
        return /* @__PURE__ */ jsxRuntimeExports.jsx(AlertTriangle, { ...iconProps });
      case "info":
        return /* @__PURE__ */ jsxRuntimeExports.jsx(Info, { ...iconProps });
      default:
        return /* @__PURE__ */ jsxRuntimeExports.jsx(Info, { ...iconProps });
    }
  };
  const Toast = ({ id, message, type, onDismiss }) => {
    const [isVisible, setIsVisible] = React.useState(false);
    const [isExiting, setIsExiting] = React.useState(false);
    reactExports.useEffect(() => {
      if (!document.querySelector("#aca-toast-styles")) {
        const style = document.createElement("style");
        style.id = "aca-toast-styles";
        style.textContent = `
                @keyframes aca-toast-progress {
                    from { transform: scaleX(1); }
                    to { transform: scaleX(0); }
                }
            `;
        document.head.appendChild(style);
      }
      setTimeout(() => setIsVisible(true), 50);
      const timer = setTimeout(() => {
        handleDismiss();
      }, 5e3);
      return () => clearTimeout(timer);
    }, [id]);
    const handleDismiss = (e) => {
      if (e) {
        e.preventDefault();
        e.stopPropagation();
      }
      setIsExiting(true);
      setTimeout(() => onDismiss(id), 300);
    };
    const getToastColors = () => {
      switch (type) {
        case "success":
          return {
            bg: "linear-gradient(135deg, #10b981 0%, #059669 100%)",
            border: "#10b981",
            shadow: "rgba(16, 185, 129, 0.3)"
          };
        case "error":
          return {
            bg: "linear-gradient(135deg, #ef4444 0%, #dc2626 100%)",
            border: "#ef4444",
            shadow: "rgba(239, 68, 68, 0.3)"
          };
        case "warning":
          return {
            bg: "linear-gradient(135deg, #f59e0b 0%, #d97706 100%)",
            border: "#f59e0b",
            shadow: "rgba(245, 158, 11, 0.3)"
          };
        case "info":
          return {
            bg: "linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)",
            border: "#3b82f6",
            shadow: "rgba(59, 130, 246, 0.3)"
          };
        default:
          return {
            bg: "linear-gradient(135deg, #6b7280 0%, #4b5563 100%)",
            border: "#6b7280",
            shadow: "rgba(107, 114, 128, 0.3)"
          };
      }
    };
    const colors = getToastColors();
    return /* @__PURE__ */ jsxRuntimeExports.jsxs(
      "div",
      {
        style: {
          background: colors.bg,
          color: "white",
          padding: "16px 20px",
          borderRadius: "12px",
          boxShadow: `0 8px 25px ${colors.shadow}, 0 4px 10px rgba(0, 0, 0, 0.1)`,
          border: `1px solid ${colors.border}`,
          backdropFilter: "blur(10px)",
          transform: isExiting ? "translateX(100%) scale(0.9)" : isVisible ? "translateX(0) scale(1)" : "translateX(100%) scale(0.9)",
          opacity: isExiting ? 0 : isVisible ? 1 : 0,
          transition: "all 0.3s cubic-bezier(0.4, 0, 0.2, 1)",
          minWidth: "300px",
          maxWidth: "500px",
          position: "relative",
          overflow: "hidden"
        },
        children: [
          /* @__PURE__ */ jsxRuntimeExports.jsx(
            "div",
            {
              style: {
                position: "absolute",
                bottom: 0,
                left: 0,
                height: "3px",
                background: "rgba(255, 255, 255, 0.3)",
                animation: "aca-toast-progress 5s linear forwards",
                transformOrigin: "left"
              }
            }
          ),
          /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", alignItems: "center", justifyContent: "space-between" }, children: [
            /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", alignItems: "center", flex: 1 }, children: [
              /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: {
                display: "flex",
                alignItems: "center",
                justifyContent: "center",
                width: "24px",
                height: "24px",
                background: "rgba(255, 255, 255, 0.2)",
                borderRadius: "6px",
                marginRight: "12px",
                flexShrink: 0
              }, children: /* @__PURE__ */ jsxRuntimeExports.jsx(ToastIcon, { type }) }),
              /* @__PURE__ */ jsxRuntimeExports.jsx("span", { style: {
                fontSize: "14px",
                fontWeight: "500",
                lineHeight: "1.4",
                wordBreak: "break-word"
              }, children: message })
            ] }),
            /* @__PURE__ */ jsxRuntimeExports.jsx(
              "button",
              {
                onClick: (e) => handleDismiss(e),
                style: {
                  background: "rgba(255, 255, 255, 0.2)",
                  border: "none",
                  color: "white",
                  cursor: "pointer",
                  padding: "6px",
                  marginLeft: "12px",
                  borderRadius: "6px",
                  display: "flex",
                  alignItems: "center",
                  justifyContent: "center",
                  transition: "all 0.2s ease",
                  flexShrink: 0,
                  minWidth: "28px",
                  minHeight: "28px"
                },
                onMouseEnter: (e) => {
                  e.currentTarget.style.background = "rgba(255, 255, 255, 0.3)";
                  e.currentTarget.style.transform = "scale(1.1)";
                },
                onMouseLeave: (e) => {
                  e.currentTarget.style.background = "rgba(255, 255, 255, 0.2)";
                  e.currentTarget.style.transform = "scale(1)";
                },
                "aria-label": "Dismiss notification",
                title: "Close notification",
                children: /* @__PURE__ */ jsxRuntimeExports.jsx(X, { style: { width: "14px", height: "14px" } })
              }
            )
          ] })
        ]
      }
    );
  };
  const PublishedPostCard = ({ post, onSelectPost }) => {
    return /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-card", style: { margin: 0, position: "relative" }, children: [
      /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { marginBottom: "15px", paddingRight: "100px" }, children: [
        /* @__PURE__ */ jsxRuntimeExports.jsx("h3", { className: "aca-card-title", style: {
          color: "#00a32a",
          marginBottom: "8px"
        }, children: post.title }),
        /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-page-description", style: {
          display: "flex",
          alignItems: "center",
          gap: "15px",
          flexWrap: "wrap"
        }, children: [
          /* @__PURE__ */ jsxRuntimeExports.jsxs("span", { children: [
            "Published: ",
            post.publishedAt ? new Date(post.publishedAt).toLocaleDateString() : "N/A"
          ] }),
          post.publishedAt && /* @__PURE__ */ jsxRuntimeExports.jsx("span", { children: new Date(post.publishedAt).toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" }) })
        ] }),
        /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: {
          position: "absolute",
          top: "16px",
          right: "16px",
          fontSize: "11px",
          fontWeight: "600",
          padding: "4px 8px",
          borderRadius: "6px",
          background: "linear-gradient(135deg, #10b981, #059669)",
          color: "white",
          display: "flex",
          alignItems: "center",
          gap: "4px",
          boxShadow: "0 2px 4px rgba(16, 185, 129, 0.2)"
        }, children: [
          /* @__PURE__ */ jsxRuntimeExports.jsx(CheckCircle, { style: { width: "12px", height: "12px" } }),
          "Published"
        ] })
      ] }),
      /* @__PURE__ */ jsxRuntimeExports.jsx("div", { className: "aca-list-item", style: { padding: "15px 0 0 0", margin: 0, justifyContent: "flex-end" }, children: /* @__PURE__ */ jsxRuntimeExports.jsxs(
        "button",
        {
          onClick: () => onSelectPost(post),
          className: "aca-button secondary",
          style: {
            background: "#ffffff",
            borderColor: "#0073aa",
            color: "#0073aa",
            boxShadow: "0 1px 3px rgba(0,0,0,0.1)"
          },
          children: [
            /* @__PURE__ */ jsxRuntimeExports.jsx(Eye, { className: "aca-nav-item-icon", style: { color: "#0073aa", strokeWidth: "1.5" } }),
            "View Content"
          ]
        }
      ) })
    ] });
  };
  const PublishedList = ({ posts, onSelectPost, onNavigateToDrafts }) => {
    const sortedPosts = [...posts].sort((a, b) => {
      if (!a.publishedAt) return 1;
      if (!b.publishedAt) return -1;
      return new Date(b.publishedAt).getTime() - new Date(a.publishedAt).getTime();
    });
    return /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-fade-in", children: [
      /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: {
        background: "linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%)",
        borderRadius: "12px",
        padding: "30px",
        marginBottom: "30px",
        color: "white",
        position: "relative",
        overflow: "hidden"
      }, children: [
        /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { position: "relative", zIndex: 2 }, children: [
          /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", alignItems: "center", gap: "12px", marginBottom: "12px" }, children: [
            /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: {
              width: "48px",
              height: "48px",
              background: "rgba(255,255,255,0.2)",
              borderRadius: "12px",
              display: "flex",
              alignItems: "center",
              justifyContent: "center",
              backdropFilter: "blur(10px)"
            }, children: /* @__PURE__ */ jsxRuntimeExports.jsx(Send, { style: { width: "24px", height: "24px" } }) }),
            /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { children: [
              /* @__PURE__ */ jsxRuntimeExports.jsx("h1", { style: {
                fontSize: "28px",
                fontWeight: "700",
                margin: 0,
                textShadow: "0 2px 4px rgba(0,0,0,0.1)",
                color: "white"
              }, children: "Published Posts" }),
              /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: { fontSize: "16px", opacity: 0.9, marginTop: "4px" }, children: "Your successful content publications" })
            ] })
          ] }),
          /* @__PURE__ */ jsxRuntimeExports.jsx("p", { style: {
            fontSize: "14px",
            opacity: 0.85,
            margin: 0,
            maxWidth: "600px",
            lineHeight: "1.5"
          }, children: 'Congratulations! Here are your successfully published articles. Click "View Content" to see the full post.' }),
          /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", gap: "20px", marginTop: "20px", flexWrap: "wrap" }, children: [
            /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", alignItems: "center", gap: "6px" }, children: [
              /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: { width: "8px", height: "8px", backgroundColor: "#4ade80", borderRadius: "50%" } }),
              /* @__PURE__ */ jsxRuntimeExports.jsxs("span", { style: { fontSize: "14px", opacity: 0.9 }, children: [
                posts.length,
                " Published Articles"
              ] })
            ] }),
            /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", alignItems: "center", gap: "6px" }, children: [
              /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: { width: "8px", height: "8px", backgroundColor: "#60a5fa", borderRadius: "50%" } }),
              /* @__PURE__ */ jsxRuntimeExports.jsx("span", { style: { fontSize: "14px", opacity: 0.9 }, children: "AI-Generated Content" })
            ] }),
            /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", alignItems: "center", gap: "6px" }, children: [
              /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: { width: "8px", height: "8px", backgroundColor: "#fbbf24", borderRadius: "50%" } }),
              /* @__PURE__ */ jsxRuntimeExports.jsx("span", { style: { fontSize: "14px", opacity: 0.9 }, children: "SEO Optimized" })
            ] })
          ] })
        ] }),
        /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: {
          position: "absolute",
          top: "-30px",
          right: "-30px",
          width: "120px",
          height: "120px",
          background: "rgba(255,255,255,0.1)",
          borderRadius: "50%",
          zIndex: 1
        } }),
        /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: {
          position: "absolute",
          bottom: "-20px",
          left: "-20px",
          width: "80px",
          height: "80px",
          background: "rgba(255,255,255,0.05)",
          borderRadius: "50%",
          zIndex: 1
        } })
      ] }),
      posts.length > 0 ? /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-card", children: [
        /* @__PURE__ */ jsxRuntimeExports.jsx("div", { className: "aca-card-header", children: /* @__PURE__ */ jsxRuntimeExports.jsxs("h2", { className: "aca-card-title", children: [
          /* @__PURE__ */ jsxRuntimeExports.jsx(Send, { className: "aca-nav-item-icon" }),
          "Your Published Content (",
          posts.length,
          ")"
        ] }) }),
        /* @__PURE__ */ jsxRuntimeExports.jsx("div", { className: "aca-grid aca-grid-2", children: sortedPosts.map((post) => /* @__PURE__ */ jsxRuntimeExports.jsx(
          PublishedPostCard,
          {
            post,
            onSelectPost
          },
          post.id
        )) })
      ] }) : /* @__PURE__ */ jsxRuntimeExports.jsx("div", { className: "aca-card", children: /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { textAlign: "center", padding: "60px 20px", color: "#646970" }, children: [
        /* @__PURE__ */ jsxRuntimeExports.jsx(Send, { style: { margin: "0 auto 20px auto", width: "48px", height: "48px", fill: "#a7aaad" } }),
        /* @__PURE__ */ jsxRuntimeExports.jsx("h3", { className: "aca-card-title", children: "No Published Posts Yet" }),
        /* @__PURE__ */ jsxRuntimeExports.jsx("p", { className: "aca-page-description", style: { maxWidth: "400px", marginLeft: "auto", marginRight: "auto" }, children: "Create drafts and publish them to see your success stories here." }),
        /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: { display: "flex", gap: "10px", justifyContent: "center", flexWrap: "wrap" }, children: /* @__PURE__ */ jsxRuntimeExports.jsxs(
          "button",
          {
            onClick: onNavigateToDrafts || (() => window.location.hash = "#drafts"),
            className: "aca-button large",
            children: [
              /* @__PURE__ */ jsxRuntimeExports.jsx(Send, { style: { width: "16px", height: "16px" } }),
              "Go to Drafts"
            ]
          }
        ) })
      ] }) })
    ] });
  };
  const ContentCalendar = ({ drafts, publishedPosts, onScheduleDraft, onSelectPost, onPublishDraft, onUpdatePostDate }) => {
    const [currentDate, setCurrentDate] = reactExports.useState(/* @__PURE__ */ new Date());
    const [dragOverDate, setDragOverDate] = reactExports.useState(null);
    const [draggedDraft, setDraggedDraft] = reactExports.useState(null);
    const [expandedDates, setExpandedDates] = reactExports.useState(/* @__PURE__ */ new Set());
    const unscheduledDrafts = drafts.filter((draft) => !draft.scheduledFor);
    const scheduledDrafts = drafts.filter((draft) => draft.scheduledFor);
    const openWordPressEditor = (postId) => {
      if (!window.acaData) {
        console.error("ACA Error: window.acaData is not defined");
        alert("WordPress data not available. Please refresh the page.");
        return;
      }
      const editUrl = `${window.acaData.admin_url}post.php?post=${postId}&action=edit`;
      window.open(editUrl, "_blank");
    };
    const getPostsForDate = (date) => {
      const dateStr = date.toISOString().split("T")[0];
      const scheduled = scheduledDrafts.filter((draft) => {
        if (!draft.scheduledFor) return false;
        const scheduledDate = new Date(draft.scheduledFor);
        return scheduledDate.toISOString().split("T")[0] === dateStr;
      });
      const published = publishedPosts.filter((post) => {
        if (!post.publishedAt) return false;
        const publishedDate = new Date(post.publishedAt);
        return publishedDate.toISOString().split("T")[0] === dateStr;
      });
      return { scheduled, published };
    };
    const toggleDateExpansion = (date) => {
      const dateStr = date.toISOString().split("T")[0];
      const newExpanded = new Set(expandedDates);
      if (newExpanded.has(dateStr)) {
        newExpanded.delete(dateStr);
      } else {
        newExpanded.add(dateStr);
      }
      setExpandedDates(newExpanded);
    };
    const handleDragStart = (e, draft) => {
      e.dataTransfer.setData("text/plain", draft.id.toString());
      setDraggedDraft(draft);
    };
    const handleDragOver = (e, date) => {
      e.preventDefault();
      setDragOverDate(date);
    };
    const handleDrop = (e, date) => {
      e.preventDefault();
      const postId = e.dataTransfer.getData("text/plain");
      if (postId && draggedDraft) {
        const today = /* @__PURE__ */ new Date();
        today.setHours(0, 0, 0, 0);
        const dropDate = new Date(date);
        dropDate.setHours(0, 0, 0, 0);
        const isPublishedPost = draggedDraft.status === "published";
        if (isPublishedPost) {
          if (dropDate > today) {
            const confirmConvert = window.confirm(
              `You're moving a published post to a future date (${date.toLocaleDateString()}). This will convert it to a scheduled draft. Do you want to continue?`
            );
            if (confirmConvert && onUpdatePostDate) {
              onUpdatePostDate(parseInt(postId), date.toISOString(), true);
            }
          } else {
            const confirmMove = window.confirm(
              `You're changing the publish date of this post to ${date.toLocaleDateString()}. Do you want to continue?`
            );
            if (confirmMove && onUpdatePostDate) {
              onUpdatePostDate(parseInt(postId), date.toISOString(), false);
            }
          }
        } else {
          if (dropDate < today) {
            const confirmPublish = window.confirm(
              `You're scheduling this draft for a past date (${date.toLocaleDateString()}). This will publish the post immediately. Do you want to continue?`
            );
            if (confirmPublish) {
              if (onPublishDraft) {
                onPublishDraft(parseInt(postId));
              } else {
                onScheduleDraft(parseInt(postId), date.toISOString());
              }
            }
          } else {
            onScheduleDraft(parseInt(postId), date.toISOString());
          }
        }
      }
      setDragOverDate(null);
      setDraggedDraft(null);
    };
    const handleDragLeave = (e) => {
      e.preventDefault();
      if (!e.currentTarget.contains(e.relatedTarget)) {
        setDragOverDate(null);
      }
    };
    const startOfMonth = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
    const endOfMonth = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);
    const startDayOfWeek = startOfMonth.getDay();
    const daysInMonth = [];
    for (let i = 0; i < startDayOfWeek; i++) {
      daysInMonth.push(null);
    }
    for (let day = 1; day <= endOfMonth.getDate(); day++) {
      daysInMonth.push(new Date(currentDate.getFullYear(), currentDate.getMonth(), day));
    }
    const nextMonth = () => setCurrentDate(new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 1));
    const prevMonth = () => setCurrentDate(new Date(currentDate.getFullYear(), currentDate.getMonth() - 1, 1));
    const renderPostItem = (post, type, isCompact = false) => {
      const isScheduled = type === "scheduled";
      const style = isScheduled ? {
        background: "#fff3cd",
        border: "1px solid #ffc107",
        color: "#856404"
      } : {
        background: "#d4edda",
        border: "1px solid #28a745",
        color: "#155724"
      };
      return /* @__PURE__ */ jsxRuntimeExports.jsxs(
        "div",
        {
          draggable: true,
          onDragStart: (e) => handleDragStart(e, post),
          onClick: () => openWordPressEditor(post.id),
          className: "aca-action-button",
          style: {
            ...style,
            padding: isCompact ? "3px 5px" : "5px 7px",
            fontSize: isCompact ? "9px" : "10px",
            overflow: "hidden",
            textOverflow: "ellipsis",
            whiteSpace: "nowrap",
            cursor: "pointer",
            display: "flex",
            alignItems: "center",
            gap: "3px",
            fontWeight: "500",
            margin: 0,
            borderRadius: "3px",
            minHeight: isCompact ? "18px" : "20px",
            transition: "all 0.2s ease"
          },
          title: `${isScheduled ? "Scheduled" : "Published"}: ${post.title || `${isScheduled ? "Draft" : "Post"} ${post.id}`} (Click to edit, drag to reschedule)`,
          children: [
            isScheduled ? /* @__PURE__ */ jsxRuntimeExports.jsx(Clock, { style: { width: isCompact ? "8px" : "9px", height: isCompact ? "8px" : "9px", flexShrink: 0 } }) : /* @__PURE__ */ jsxRuntimeExports.jsx(Eye, { style: { width: isCompact ? "8px" : "9px", height: isCompact ? "8px" : "9px", flexShrink: 0 } }),
            /* @__PURE__ */ jsxRuntimeExports.jsx("span", { style: {
              flex: 1,
              overflow: "hidden",
              textOverflow: "ellipsis",
              whiteSpace: "nowrap",
              maxWidth: "100%",
              wordBreak: "break-word"
            }, children: post.title || `${isScheduled ? "Draft" : "Post"} ${post.id}` }),
            /* @__PURE__ */ jsxRuntimeExports.jsx(Edit, { style: { width: isCompact ? "6px" : "7px", height: isCompact ? "6px" : "7px", flexShrink: 0 } })
          ]
        },
        `${type}-${post.id}`
      );
    };
    return /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-fade-in", children: [
      /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: {
        background: "linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%)",
        borderRadius: "12px",
        padding: "30px",
        marginBottom: "30px",
        color: "white",
        position: "relative",
        overflow: "hidden"
      }, children: [
        /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { position: "relative", zIndex: 2 }, children: [
          /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", alignItems: "center", gap: "12px", marginBottom: "12px" }, children: [
            /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: {
              width: "48px",
              height: "48px",
              background: "rgba(255,255,255,0.2)",
              borderRadius: "12px",
              display: "flex",
              alignItems: "center",
              justifyContent: "center",
              backdropFilter: "blur(10px)"
            }, children: /* @__PURE__ */ jsxRuntimeExports.jsx(Calendar, { style: { width: "24px", height: "24px" } }) }),
            /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { children: [
              /* @__PURE__ */ jsxRuntimeExports.jsx("h1", { style: {
                fontSize: "28px",
                fontWeight: "700",
                margin: 0,
                textShadow: "0 2px 4px rgba(0,0,0,0.1)",
                color: "white"
              }, children: "Content Calendar" }),
              /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: { fontSize: "16px", opacity: 0.9, marginTop: "4px" }, children: "Schedule and organize your content timeline" })
            ] })
          ] }),
          /* @__PURE__ */ jsxRuntimeExports.jsx("p", { style: {
            fontSize: "14px",
            opacity: 0.85,
            margin: 0,
            maxWidth: "600px",
            lineHeight: "1.5"
          }, children: "Schedule your drafts by dragging them onto calendar dates. View scheduled and published content timeline." }),
          /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", gap: "20px", marginTop: "20px", flexWrap: "wrap" }, children: [
            /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", alignItems: "center", gap: "6px" }, children: [
              /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: { width: "8px", height: "8px", backgroundColor: "#fbbf24", borderRadius: "50%" } }),
              /* @__PURE__ */ jsxRuntimeExports.jsxs("span", { style: { fontSize: "14px", opacity: 0.9 }, children: [
                scheduledDrafts.length,
                " Scheduled"
              ] })
            ] }),
            /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", alignItems: "center", gap: "6px" }, children: [
              /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: { width: "8px", height: "8px", backgroundColor: "#4ade80", borderRadius: "50%" } }),
              /* @__PURE__ */ jsxRuntimeExports.jsxs("span", { style: { fontSize: "14px", opacity: 0.9 }, children: [
                publishedPosts.length,
                " Published"
              ] })
            ] }),
            /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", alignItems: "center", gap: "6px" }, children: [
              /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: { width: "8px", height: "8px", backgroundColor: "#60a5fa", borderRadius: "50%" } }),
              /* @__PURE__ */ jsxRuntimeExports.jsxs("span", { style: { fontSize: "14px", opacity: 0.9 }, children: [
                unscheduledDrafts.length,
                " Unscheduled"
              ] })
            ] })
          ] })
        ] }),
        /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: {
          position: "absolute",
          top: "-30px",
          right: "-30px",
          width: "120px",
          height: "120px",
          background: "rgba(255,255,255,0.1)",
          borderRadius: "50%",
          zIndex: 1
        } }),
        /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: {
          position: "absolute",
          bottom: "-20px",
          left: "-20px",
          width: "80px",
          height: "80px",
          background: "rgba(255,255,255,0.05)",
          borderRadius: "50%",
          zIndex: 1
        } })
      ] }),
      /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", flexDirection: "column", gap: "20px" }, children: [
        /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-card", children: [
          /* @__PURE__ */ jsxRuntimeExports.jsx("div", { className: "aca-card-header", children: /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", justifyContent: "space-between", alignItems: "center" }, children: [
            /* @__PURE__ */ jsxRuntimeExports.jsxs("h2", { className: "aca-card-title", children: [
              /* @__PURE__ */ jsxRuntimeExports.jsx(Calendar, { className: "aca-nav-item-icon" }),
              currentDate.toLocaleDateString("en-US", { month: "long", year: "numeric" })
            ] }),
            /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", gap: "8px" }, children: [
              /* @__PURE__ */ jsxRuntimeExports.jsx("button", { onClick: prevMonth, className: "aca-action-button", children: /* @__PURE__ */ jsxRuntimeExports.jsx(ChevronLeft, { style: { width: "20px", height: "20px" } }) }),
              /* @__PURE__ */ jsxRuntimeExports.jsx("button", { onClick: nextMonth, className: "aca-action-button", children: /* @__PURE__ */ jsxRuntimeExports.jsx(ChevronRight, { style: { width: "20px", height: "20px" } }) })
            ] })
          ] }) }),
          /* @__PURE__ */ jsxRuntimeExports.jsx("div", { className: "aca-card-content", children: /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: {
            display: "grid",
            gridTemplateColumns: "repeat(7, 1fr)",
            gap: "1px",
            backgroundColor: "#e0e0e0",
            border: "1px solid #e0e0e0",
            width: "100%",
            maxWidth: "100%",
            overflow: "hidden",
            boxSizing: "border-box"
          }, children: [
            ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"].map((day) => /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: {
              backgroundColor: "#f8f9fa",
              padding: "12px 8px",
              textAlign: "center",
              fontWeight: "600",
              fontSize: "14px",
              color: "#495057"
            }, children: day }, day)),
            daysInMonth.map((date, index) => {
              if (!date) {
                return /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: { backgroundColor: "#fff", minHeight: "120px" } }, index);
              }
              const { scheduled, published } = getPostsForDate(date);
              const totalPosts = scheduled.length + published.length;
              const isToday = date.toDateString() === (/* @__PURE__ */ new Date()).toDateString();
              const isDragOver = dragOverDate && date.getTime() === dragOverDate.getTime();
              const dateStr = date.toISOString().split("T")[0];
              const isExpanded = expandedDates.has(dateStr);
              const maxVisiblePosts = 3;
              const shouldShowExpander = totalPosts > maxVisiblePosts;
              const visibleScheduled = isExpanded ? scheduled : scheduled.slice(0, Math.min(maxVisiblePosts, scheduled.length));
              const remainingSlots = isExpanded ? published.length : Math.max(0, maxVisiblePosts - scheduled.length);
              const visiblePublished = published.slice(0, remainingSlots);
              const hiddenCount = totalPosts - visibleScheduled.length - visiblePublished.length;
              return /* @__PURE__ */ jsxRuntimeExports.jsxs(
                "div",
                {
                  onDragOver: (e) => handleDragOver(e, date),
                  onDrop: (e) => handleDrop(e, date),
                  onDragLeave: handleDragLeave,
                  style: {
                    backgroundColor: isDragOver ? "#e3f2fd" : "#fff",
                    minHeight: "120px",
                    padding: "8px",
                    position: "relative",
                    border: isToday ? "2px solid #2196f3" : "none",
                    cursor: "pointer",
                    overflow: "hidden",
                    wordWrap: "break-word",
                    wordBreak: "break-word",
                    maxWidth: "100%",
                    boxSizing: "border-box"
                  },
                  children: [
                    /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: {
                      position: "absolute",
                      top: "8px",
                      left: "8px",
                      fontWeight: isToday ? "700" : "600",
                      fontSize: "16px",
                      color: isToday ? "#2196f3" : "#333",
                      zIndex: 1
                    }, children: date.getDate() }),
                    totalPosts > 0 && /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: {
                      position: "absolute",
                      top: "8px",
                      right: "8px",
                      background: totalPosts > 3 ? "#ff9800" : "#4caf50",
                      color: "white",
                      borderRadius: "50%",
                      width: "18px",
                      height: "18px",
                      display: "flex",
                      alignItems: "center",
                      justifyContent: "center",
                      fontSize: "9px",
                      fontWeight: "bold",
                      zIndex: 1
                    }, children: totalPosts }),
                    /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: {
                      display: "flex",
                      flexDirection: "column",
                      gap: "2px",
                      marginTop: "28px",
                      flex: 1
                    }, children: [
                      visibleScheduled.map(
                        (draft) => renderPostItem(draft, "scheduled", !isExpanded && totalPosts > 2)
                      ),
                      visiblePublished.map(
                        (post) => renderPostItem(post, "published", !isExpanded && totalPosts > 2)
                      ),
                      shouldShowExpander && /* @__PURE__ */ jsxRuntimeExports.jsx(
                        "button",
                        {
                          onClick: (e) => {
                            e.stopPropagation();
                            toggleDateExpansion(date);
                          },
                          style: {
                            background: "#f0f0f0",
                            border: "1px solid #ccc",
                            color: "#666",
                            padding: "3px 6px",
                            fontSize: "9px",
                            borderRadius: "2px",
                            cursor: "pointer",
                            textAlign: "center",
                            fontWeight: "500",
                            transition: "all 0.2s ease"
                          },
                          onMouseOver: (e) => {
                            e.currentTarget.style.background = "#e0e0e0";
                          },
                          onMouseOut: (e) => {
                            e.currentTarget.style.background = "#f0f0f0";
                          },
                          children: isExpanded ? "Show Less" : `+${hiddenCount} More`
                        }
                      )
                    ] })
                  ]
                },
                date.getTime()
              );
            })
          ] }) })
        ] }),
        /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-card", children: [
          /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-card-header", children: [
            /* @__PURE__ */ jsxRuntimeExports.jsx("h3", { className: "aca-card-title", children: "Unscheduled Drafts" }),
            /* @__PURE__ */ jsxRuntimeExports.jsx("p", { style: { margin: 0, fontSize: "14px", color: "#666" }, children: "Drag these drafts onto calendar dates to schedule them" })
          ] }),
          /* @__PURE__ */ jsxRuntimeExports.jsx("div", { className: "aca-card-content", children: unscheduledDrafts.length === 0 ? /* @__PURE__ */ jsxRuntimeExports.jsx("p", { style: { textAlign: "center", color: "#666", margin: "20px 0" }, children: "No unscheduled drafts available" }) : /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: { display: "grid", gridTemplateColumns: "repeat(auto-fill, minmax(250px, 1fr))", gap: "12px" }, children: unscheduledDrafts.map((draft) => /* @__PURE__ */ jsxRuntimeExports.jsxs(
            "div",
            {
              draggable: true,
              onDragStart: (e) => handleDragStart(e, draft),
              className: "aca-action-button",
              style: {
                background: "#f8f9fa",
                border: "1px solid #dee2e6",
                color: "#495057",
                padding: "12px",
                cursor: "grab",
                borderRadius: "6px",
                transition: "all 0.2s ease"
              },
              onMouseDown: (e) => e.currentTarget.style.cursor = "grabbing",
              onMouseUp: (e) => e.currentTarget.style.cursor = "grab",
              title: `Drag to schedule: ${draft.title}`,
              children: [
                /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: { fontWeight: "600", marginBottom: "4px" }, children: draft.title || `Draft ${draft.id}` }),
                /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { fontSize: "12px", color: "#6c757d" }, children: [
                  "Created: ",
                  new Date(draft.createdAt).toLocaleDateString()
                ] })
              ]
            },
            draft.id
          )) }) })
        ] }),
        /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-card", children: [
          /* @__PURE__ */ jsxRuntimeExports.jsx("div", { className: "aca-card-header", children: /* @__PURE__ */ jsxRuntimeExports.jsx("h3", { className: "aca-card-title", children: "How to Use" }) }),
          /* @__PURE__ */ jsxRuntimeExports.jsx("div", { className: "aca-card-content", children: /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "grid", gridTemplateColumns: "repeat(auto-fit, minmax(300px, 1fr))", gap: "20px" }, children: [
            /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { children: [
              /* @__PURE__ */ jsxRuntimeExports.jsx("h4", { className: "aca-label", children: "📅 Scheduling Drafts" }),
              /* @__PURE__ */ jsxRuntimeExports.jsx("p", { style: { margin: 0, fontSize: "14px", color: "#666" }, children: "Drag any unscheduled draft from the sidebar and drop it onto a calendar date to schedule it for publication." })
            ] }),
            /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { children: [
              /* @__PURE__ */ jsxRuntimeExports.jsx("h4", { className: "aca-label", children: "🔄 Rescheduling" }),
              /* @__PURE__ */ jsxRuntimeExports.jsx("p", { style: { margin: 0, fontSize: "14px", color: "#666" }, children: "Scheduled drafts (yellow) can be dragged to different dates to reschedule them." })
            ] }),
            /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { children: [
              /* @__PURE__ */ jsxRuntimeExports.jsx("h4", { className: "aca-label", children: "✏️ Editing Content" }),
              /* @__PURE__ */ jsxRuntimeExports.jsx("p", { style: { margin: 0, fontSize: "14px", color: "#666" }, children: "Click on any scheduled draft (yellow) or published post (green) to open it in WordPress editor." })
            ] }),
            /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { children: [
              /* @__PURE__ */ jsxRuntimeExports.jsx("h4", { className: "aca-label", children: "📊 Smart Display" }),
              /* @__PURE__ */ jsxRuntimeExports.jsx("p", { style: { margin: 0, fontSize: "14px", color: "#666" }, children: 'Days with many posts show first 3 items. Click "+X More" to expand and see all content.' })
            ] })
          ] }) })
        ] })
      ] })
    ] });
  };
  const getPriorityColor = (priority) => {
    if (!priority) return "#666";
    switch (priority) {
      case 5:
        return "#dc3545";
      // Urgent - Red
      case 4:
        return "#fd7e14";
      // High - Orange
      case 3:
        return "#ffc107";
      // Medium - Yellow
      case 2:
        return "#28a745";
      // Low - Green
      case 1:
        return "#6c757d";
      // Very low - Gray
      default:
        return "#666";
    }
  };
  const getPriorityLabel = (priority) => {
    if (!priority) return "Unknown";
    switch (priority) {
      case 5:
        return "Urgent";
      case 4:
        return "High";
      case 3:
        return "Medium";
      case 2:
        return "Low";
      case 1:
        return "Very Low";
      default:
        return "Unknown";
    }
  };
  const FreshnessCard = ({
    post,
    onAnalyze,
    onUpdate,
    isAnalyzing,
    isUpdating
  }) => {
    const [showDetails, setShowDetails] = reactExports.useState(false);
    const priorityColor = getPriorityColor(post.update_priority);
    const priorityLabel = getPriorityLabel(post.update_priority);
    const formatDate = (dateString) => {
      if (!dateString) return "Never";
      return new Date(dateString).toLocaleDateString();
    };
    const getScoreColor = (score) => {
      if (!score) return "#666";
      if (score >= 80) return "#28a745";
      if (score >= 60) return "#ffc107";
      if (score >= 40) return "#fd7e14";
      return "#dc3545";
    };
    return /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-card", style: { margin: 0 }, children: [
      /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", justifyContent: "space-between", alignItems: "flex-start", marginBottom: "15px" }, children: [
        /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { flex: 1 }, children: [
          /* @__PURE__ */ jsxRuntimeExports.jsx("h3", { style: { margin: "0 0 8px 0", fontSize: "16px", lineHeight: "1.4" }, children: post.post_title }),
          /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", gap: "15px", fontSize: "13px", color: "#666" }, children: [
            /* @__PURE__ */ jsxRuntimeExports.jsxs("span", { children: [
              "Published: ",
              formatDate(post.post_date)
            ] }),
            /* @__PURE__ */ jsxRuntimeExports.jsxs("span", { children: [
              "Modified: ",
              formatDate(post.post_modified)
            ] }),
            post.last_analyzed && /* @__PURE__ */ jsxRuntimeExports.jsxs("span", { children: [
              "Analyzed: ",
              formatDate(post.last_analyzed)
            ] })
          ] })
        ] }),
        /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", alignItems: "center", gap: "10px" }, children: [
          post.freshness_score !== null && /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: {
            textAlign: "center",
            padding: "8px 12px",
            borderRadius: "6px",
            backgroundColor: getScoreColor(post.freshness_score) + "20",
            border: `1px solid ${getScoreColor(post.freshness_score)}40`
          }, children: [
            /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: {
              fontSize: "18px",
              fontWeight: "bold",
              color: getScoreColor(post.freshness_score)
            }, children: Math.round(post.freshness_score) }),
            /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: { fontSize: "11px", color: "#666" }, children: "Score" })
          ] }),
          post.update_priority && /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: {
            textAlign: "center",
            padding: "4px 8px",
            borderRadius: "4px",
            backgroundColor: priorityColor + "20",
            border: `1px solid ${priorityColor}40`,
            fontSize: "11px",
            color: priorityColor,
            fontWeight: "500"
          }, children: priorityLabel })
        ] })
      ] }),
      post.needs_update && /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-alert warning", style: { margin: "10px 0", fontSize: "13px" }, children: [
        /* @__PURE__ */ jsxRuntimeExports.jsx(AlertTriangle, { style: { width: "16px", height: "16px", marginRight: "8px" } }),
        "This content may need updating to maintain freshness and relevance."
      ] }),
      post.analysis && showDetails && /* @__PURE__ */ jsxRuntimeExports.jsxs(
        "div",
        {
          id: `details-${post.ID}`,
          style: {
            marginTop: "15px",
            padding: "15px",
            backgroundColor: "#f8f9fa",
            borderRadius: "6px",
            fontSize: "13px"
          },
          children: [
            /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "grid", gridTemplateColumns: "repeat(auto-fit, minmax(120px, 1fr))", gap: "10px", marginBottom: "15px" }, children: [
              /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { children: [
                /* @__PURE__ */ jsxRuntimeExports.jsx("strong", { children: "Age Score:" }),
                " ",
                Math.round(post.analysis.age_score)
              ] }),
              /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { children: [
                /* @__PURE__ */ jsxRuntimeExports.jsx("strong", { children: "SEO Score:" }),
                " ",
                Math.round(post.analysis.seo_score)
              ] }),
              /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { children: [
                /* @__PURE__ */ jsxRuntimeExports.jsx("strong", { children: "AI Score:" }),
                " ",
                Math.round(post.analysis.ai_score)
              ] }),
              /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { children: [
                /* @__PURE__ */ jsxRuntimeExports.jsx("strong", { children: "Days Old:" }),
                " ",
                post.analysis.days_old
              ] })
            ] }),
            post.analysis.suggestions.length > 0 && /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { marginBottom: "10px" }, children: [
              /* @__PURE__ */ jsxRuntimeExports.jsx("strong", { children: "Suggestions:" }),
              /* @__PURE__ */ jsxRuntimeExports.jsx("ul", { style: { margin: "5px 0 0 20px", padding: 0 }, children: post.analysis.suggestions.map((suggestion, index) => /* @__PURE__ */ jsxRuntimeExports.jsx("li", { style: { marginBottom: "3px" }, children: suggestion }, index)) })
            ] })
          ]
        }
      ),
      /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", gap: "10px", marginTop: "15px" }, children: [
        /* @__PURE__ */ jsxRuntimeExports.jsxs(
          "button",
          {
            onClick: () => onAnalyze(post.ID),
            disabled: isAnalyzing,
            className: "aca-button secondary",
            style: { display: "flex", alignItems: "center", gap: "6px" },
            children: [
              isAnalyzing ? /* @__PURE__ */ jsxRuntimeExports.jsx(Spinner, { className: "h-4 w-4" }) : /* @__PURE__ */ jsxRuntimeExports.jsx(BarChart3, { className: "h-4 w-4" }),
              isAnalyzing ? "Analyzing..." : "Analyze"
            ]
          }
        ),
        post.needs_update && /* @__PURE__ */ jsxRuntimeExports.jsxs(
          "button",
          {
            onClick: () => onUpdate(post.ID),
            disabled: isUpdating,
            className: "aca-button primary",
            style: { display: "flex", alignItems: "center", gap: "6px" },
            children: [
              isUpdating ? /* @__PURE__ */ jsxRuntimeExports.jsx(Spinner, { className: "h-4 w-4" }) : /* @__PURE__ */ jsxRuntimeExports.jsx(RefreshCw, { className: "h-4 w-4" }),
              isUpdating ? "Updating..." : "Queue Update"
            ]
          }
        ),
        /* @__PURE__ */ jsxRuntimeExports.jsx(
          "button",
          {
            onClick: () => setShowDetails(!showDetails),
            className: "aca-button secondary",
            style: { marginLeft: "auto" },
            "aria-expanded": showDetails,
            "aria-controls": `details-${post.ID}`,
            children: showDetails ? "Hide Details" : "Show Details"
          }
        )
      ] })
    ] });
  };
  const ContentFreshnessManager = ({ onShowToast, settings: appSettings }) => {
    const [posts, setPosts] = reactExports.useState([]);
    const [settings, setSettings] = reactExports.useState({
      analysisFrequency: "weekly",
      autoUpdate: false,
      updateThreshold: 70,
      enabled: true
    });
    const [isLoading, setIsLoading] = reactExports.useState(false);
    const [isAnalyzing, setIsAnalyzing] = reactExports.useState({});
    const [isUpdating, setIsUpdating] = reactExports.useState({});
    const [filter, setFilter] = reactExports.useState("all");
    const [showSettings, setShowSettings] = reactExports.useState(false);
    const isProActive = () => {
      return appSettings?.is_pro === true;
    };
    const loadPosts = reactExports.useCallback(async () => {
      setIsLoading(true);
      try {
        const response = await contentFreshnessApi.getPosts(20, filter === "all" ? void 0 : filter);
        if (response && response.success) {
          setPosts(response.posts || []);
        } else {
          const errorMessage = response?.message || "Failed to load posts";
          onShowToast(errorMessage, "error");
          setPosts([]);
        }
      } catch (error) {
        console.error("Error loading posts:", error);
        const errorMessage = error instanceof Error ? error.message : "Error loading posts";
        onShowToast(errorMessage, "error");
        setPosts([]);
      } finally {
        setIsLoading(false);
      }
    }, [filter, onShowToast]);
    const loadSettings = reactExports.useCallback(async () => {
      try {
        const response = await contentFreshnessApi.getSettings();
        if (response && response.success) {
          setSettings(response.settings);
        } else {
          console.warn("Failed to load freshness settings, using defaults");
        }
      } catch (error) {
        console.error("Error loading settings:", error);
      }
    }, []);
    reactExports.useEffect(() => {
      loadPosts();
      loadSettings();
    }, [loadPosts, loadSettings]);
    const handleAnalyzeAll = async () => {
      setIsLoading(true);
      try {
        const response = await contentFreshnessApi.analyzeAll(10);
        if (response.success) {
          onShowToast(`Analyzed ${response.analyzed_count} posts successfully`, "success");
          loadPosts();
        } else {
          onShowToast("Unable to analyze posts. Please check your Pro license and try again.", "error");
        }
      } catch (error) {
        console.error("Error analyzing posts:", error);
        onShowToast("Analysis failed due to a connection error. Please try again.", "error");
      } finally {
        setIsLoading(false);
      }
    };
    const handleAnalyzeSingle = async (postId) => {
      setIsAnalyzing((prev) => ({ ...prev, [postId]: true }));
      try {
        const response = await contentFreshnessApi.analyzeSingle(postId);
        if (response.success) {
          onShowToast(`Analysis completed for "${response.post_title}"`, "success");
          setPosts((prev) => prev.map(
            (post) => post.ID === postId ? {
              ...post,
              freshness_score: response.analysis.score,
              needs_update: response.analysis.needs_update,
              update_priority: response.analysis.priority,
              last_analyzed: (/* @__PURE__ */ new Date()).toISOString(),
              analysis: response.analysis
            } : post
          ));
        } else {
          onShowToast("Unable to analyze this post. Please check your Pro license.", "error");
        }
      } catch (error) {
        console.error("Error analyzing post:", error);
        onShowToast("Post analysis failed. Please check your connection and try again.", "error");
      } finally {
        setIsAnalyzing((prev) => ({ ...prev, [postId]: false }));
      }
    };
    const handleUpdateContent = async (postId) => {
      setIsUpdating((prev) => ({ ...prev, [postId]: true }));
      try {
        const response = await contentFreshnessApi.updateContent(postId);
        if (response.success) {
          onShowToast("Content update queued successfully", "success");
        } else {
          onShowToast("Unable to queue content update. Please try again.", "error");
        }
      } catch (error) {
        console.error("Error updating content:", error);
        onShowToast("Content update failed. Please check your connection and try again.", "error");
      } finally {
        setIsUpdating((prev) => ({ ...prev, [postId]: false }));
      }
    };
    const handleSaveSettings = async (newSettings) => {
      try {
        const response = await contentFreshnessApi.saveSettings(newSettings);
        if (response.success) {
          setSettings(newSettings);
          onShowToast("Settings saved successfully", "success");
          setShowSettings(false);
        } else {
          onShowToast("Unable to save settings. Please try again.", "error");
        }
      } catch (error) {
        console.error("Error saving settings:", error);
        onShowToast("Settings save failed. Please check your connection and try again.", "error");
      }
    };
    const needsUpdateCount = posts.filter((post) => post.needs_update).length;
    const analyzedCount = posts.filter((post) => post.freshness_score !== null).length;
    const averageScore = posts.length > 0 ? Math.round(posts.reduce((sum, post) => sum + (post.freshness_score || 0), 0) / posts.length) : 0;
    return /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-fade-in", children: [
      /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: {
        background: "linear-gradient(135deg, #06b6d4 0%, #0891b2 100%)",
        borderRadius: "12px",
        padding: "30px",
        marginBottom: "30px",
        color: "white",
        position: "relative",
        overflow: "hidden"
      }, children: [
        /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { position: "relative", zIndex: 2 }, children: [
          /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", alignItems: "center", gap: "12px", marginBottom: "12px" }, children: [
            /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: {
              width: "48px",
              height: "48px",
              background: "rgba(255,255,255,0.2)",
              borderRadius: "12px",
              display: "flex",
              alignItems: "center",
              justifyContent: "center",
              backdropFilter: "blur(10px)"
            }, children: /* @__PURE__ */ jsxRuntimeExports.jsx(Sparkles, { style: { width: "24px", height: "24px" } }) }),
            /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { children: [
              /* @__PURE__ */ jsxRuntimeExports.jsx("h1", { style: {
                fontSize: "28px",
                fontWeight: "700",
                margin: 0,
                textShadow: "0 2px 4px rgba(0,0,0,0.1)",
                color: "white"
              }, children: "Content Freshness Manager" }),
              /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: { fontSize: "16px", opacity: 0.9, marginTop: "4px" }, children: "AI-powered content analysis and updates" })
            ] })
          ] }),
          /* @__PURE__ */ jsxRuntimeExports.jsx("p", { style: {
            fontSize: "14px",
            opacity: 0.85,
            margin: 0,
            maxWidth: "600px",
            lineHeight: "1.5"
          }, children: "Keep your content up-to-date with AI-powered analysis and update recommendations" }),
          /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", gap: "20px", marginTop: "20px", flexWrap: "wrap" }, children: [
            /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", alignItems: "center", gap: "6px" }, children: [
              /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: { width: "8px", height: "8px", backgroundColor: "#4ade80", borderRadius: "50%" } }),
              /* @__PURE__ */ jsxRuntimeExports.jsx("span", { style: { fontSize: "14px", opacity: 0.9 }, children: "Pro Feature" })
            ] }),
            /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", alignItems: "center", gap: "6px" }, children: [
              /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: { width: "8px", height: "8px", backgroundColor: "#fbbf24", borderRadius: "50%" } }),
              /* @__PURE__ */ jsxRuntimeExports.jsx("span", { style: { fontSize: "14px", opacity: 0.9 }, children: "Content Analysis" })
            ] }),
            /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", alignItems: "center", gap: "6px" }, children: [
              /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: { width: "8px", height: "8px", backgroundColor: "#60a5fa", borderRadius: "50%" } }),
              /* @__PURE__ */ jsxRuntimeExports.jsx("span", { style: { fontSize: "14px", opacity: 0.9 }, children: "Update Recommendations" })
            ] })
          ] })
        ] }),
        /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: {
          position: "absolute",
          top: "-30px",
          right: "-30px",
          width: "120px",
          height: "120px",
          background: "rgba(255,255,255,0.1)",
          borderRadius: "50%",
          zIndex: 1
        } }),
        /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: {
          position: "absolute",
          bottom: "-20px",
          left: "-20px",
          width: "80px",
          height: "80px",
          background: "rgba(255,255,255,0.05)",
          borderRadius: "50%",
          zIndex: 1
        } })
      ] }),
      !isProActive() ? /* @__PURE__ */ jsxRuntimeExports.jsx(
        UpgradePrompt,
        {
          title: "Content Freshness Manager - Pro Feature",
          description: "Unlock AI-powered content analysis and automatic freshness management to keep your content up-to-date and engaging",
          features: [
            "AI-powered content freshness analysis",
            "Automatic content update recommendations",
            "Freshness scoring and priority ranking",
            "Bulk content analysis and updates",
            "Advanced content performance tracking",
            "Automated content refresh scheduling"
          ]
        }
      ) : /* @__PURE__ */ jsxRuntimeExports.jsxs(jsxRuntimeExports.Fragment, { children: [
        /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-grid aca-grid-4", style: { marginBottom: "30px" }, children: [
          /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-card", style: { textAlign: "center", margin: 0 }, children: [
            /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: { fontSize: "32px", fontWeight: "bold", color: "#0073aa", marginBottom: "5px" }, children: posts.length }),
            /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: { fontSize: "14px", color: "#666" }, children: "Total Posts" })
          ] }),
          /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-card", style: { textAlign: "center", margin: 0 }, children: [
            /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: { fontSize: "32px", fontWeight: "bold", color: "#28a745", marginBottom: "5px" }, children: analyzedCount }),
            /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: { fontSize: "14px", color: "#666" }, children: "Analyzed" })
          ] }),
          /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-card", style: { textAlign: "center", margin: 0 }, children: [
            /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: { fontSize: "32px", fontWeight: "bold", color: "#dc3545", marginBottom: "5px" }, children: needsUpdateCount }),
            /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: { fontSize: "14px", color: "#666" }, children: "Need Updates" })
          ] }),
          /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-card", style: { textAlign: "center", margin: 0 }, children: [
            /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: { fontSize: "32px", fontWeight: "bold", color: "#ffc107", marginBottom: "5px" }, children: averageScore }),
            /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: { fontSize: "14px", color: "#666" }, children: "Avg. Score" })
          ] })
        ] }),
        /* @__PURE__ */ jsxRuntimeExports.jsx("div", { className: "aca-card", style: { marginBottom: "30px" }, children: /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", justifyContent: "space-between", alignItems: "center", flexWrap: "wrap", gap: "15px" }, children: [
          /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", gap: "10px", alignItems: "center" }, children: [
            /* @__PURE__ */ jsxRuntimeExports.jsxs(
              "button",
              {
                onClick: handleAnalyzeAll,
                disabled: isLoading,
                className: "aca-button primary",
                style: { display: "flex", alignItems: "center", gap: "6px" },
                children: [
                  isLoading ? /* @__PURE__ */ jsxRuntimeExports.jsx(Spinner, { className: "h-4 w-4" }) : /* @__PURE__ */ jsxRuntimeExports.jsx(TrendingUp, { className: "h-4 w-4" }),
                  isLoading ? "Analyzing..." : "Analyze All Posts"
                ]
              }
            ),
            /* @__PURE__ */ jsxRuntimeExports.jsxs(
              "button",
              {
                onClick: () => setShowSettings(!showSettings),
                className: "aca-button secondary",
                style: { display: "flex", alignItems: "center", gap: "6px" },
                children: [
                  /* @__PURE__ */ jsxRuntimeExports.jsx(Settings, { className: "h-4 w-4" }),
                  "Settings"
                ]
              }
            )
          ] }),
          /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", gap: "10px", alignItems: "center" }, children: [
            /* @__PURE__ */ jsxRuntimeExports.jsx("span", { style: { fontSize: "14px", color: "#666" }, children: "Filter:" }),
            /* @__PURE__ */ jsxRuntimeExports.jsxs(
              "select",
              {
                value: filter,
                onChange: (e) => setFilter(e.target.value),
                className: "aca-input",
                style: { width: "auto", minWidth: "120px" },
                "aria-label": "Filter posts by status",
                children: [
                  /* @__PURE__ */ jsxRuntimeExports.jsx("option", { value: "all", children: "All Posts" }),
                  /* @__PURE__ */ jsxRuntimeExports.jsx("option", { value: "needs_update", children: "Needs Update" }),
                  /* @__PURE__ */ jsxRuntimeExports.jsx("option", { value: "fresh", children: "Fresh Content" })
                ]
              }
            )
          ] })
        ] }) }),
        showSettings && /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-card", style: { marginBottom: "30px" }, children: [
          /* @__PURE__ */ jsxRuntimeExports.jsx("h3", { style: { marginTop: 0 }, children: "Content Freshness Settings" }),
          /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "grid", gridTemplateColumns: "repeat(auto-fit, minmax(250px, 1fr))", gap: "20px" }, children: [
            /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-form-group", children: [
              /* @__PURE__ */ jsxRuntimeExports.jsx("label", { children: "Analysis Frequency" }),
              /* @__PURE__ */ jsxRuntimeExports.jsxs(
                "select",
                {
                  value: settings.analysisFrequency,
                  onChange: (e) => setSettings({ ...settings, analysisFrequency: e.target.value }),
                  className: "aca-input",
                  children: [
                    /* @__PURE__ */ jsxRuntimeExports.jsx("option", { value: "daily", children: "Daily" }),
                    /* @__PURE__ */ jsxRuntimeExports.jsx("option", { value: "weekly", children: "Weekly" }),
                    /* @__PURE__ */ jsxRuntimeExports.jsx("option", { value: "monthly", children: "Monthly" }),
                    /* @__PURE__ */ jsxRuntimeExports.jsx("option", { value: "manual", children: "Manual Only" })
                  ]
                }
              )
            ] }),
            /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-form-group", children: [
              /* @__PURE__ */ jsxRuntimeExports.jsxs("label", { children: [
                "Update Threshold (",
                settings.updateThreshold,
                "%)"
              ] }),
              /* @__PURE__ */ jsxRuntimeExports.jsx(
                "input",
                {
                  type: "range",
                  min: "30",
                  max: "90",
                  value: settings.updateThreshold,
                  onChange: (e) => setSettings({ ...settings, updateThreshold: parseInt(e.target.value) }),
                  className: "aca-input",
                  style: { width: "100%" },
                  "aria-label": `Update threshold: ${settings.updateThreshold} percent`
                }
              ),
              /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", justifyContent: "space-between", fontSize: "12px", color: "#666", marginTop: "5px" }, children: [
                /* @__PURE__ */ jsxRuntimeExports.jsx("span", { children: "More Updates (30%)" }),
                /* @__PURE__ */ jsxRuntimeExports.jsx("span", { children: "Fewer Updates (90%)" })
              ] })
            ] })
          ] }),
          /* @__PURE__ */ jsxRuntimeExports.jsx("div", { className: "aca-form-group", children: /* @__PURE__ */ jsxRuntimeExports.jsxs("label", { children: [
            /* @__PURE__ */ jsxRuntimeExports.jsx(
              "input",
              {
                type: "checkbox",
                checked: settings.autoUpdate,
                onChange: (e) => setSettings({ ...settings, autoUpdate: e.target.checked }),
                style: { marginRight: "8px" }
              }
            ),
            "Enable automatic content updates (high-confidence suggestions only)"
          ] }) }),
          /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { display: "flex", gap: "10px", marginTop: "20px" }, children: [
            /* @__PURE__ */ jsxRuntimeExports.jsx(
              "button",
              {
                onClick: () => handleSaveSettings(settings),
                className: "aca-button primary",
                children: "Save Settings"
              }
            ),
            /* @__PURE__ */ jsxRuntimeExports.jsx(
              "button",
              {
                onClick: () => setShowSettings(false),
                className: "aca-button secondary",
                children: "Cancel"
              }
            )
          ] })
        ] }),
        /* @__PURE__ */ jsxRuntimeExports.jsx("div", { style: { display: "flex", flexDirection: "column", gap: "20px" }, children: isLoading && posts.length === 0 ? /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: { textAlign: "center", padding: "40px" }, children: [
          /* @__PURE__ */ jsxRuntimeExports.jsx(Spinner, { className: "h-8 w-8", style: { marginBottom: "10px" } }),
          /* @__PURE__ */ jsxRuntimeExports.jsx("p", { children: "Loading posts..." })
        ] }) : posts.length === 0 ? /* @__PURE__ */ jsxRuntimeExports.jsx("div", { className: "aca-card", style: { textAlign: "center", padding: "40px" }, children: /* @__PURE__ */ jsxRuntimeExports.jsx("p", { style: { margin: 0, color: "#666" }, children: "No posts found matching the current filter." }) }) : posts.map((post) => /* @__PURE__ */ jsxRuntimeExports.jsx(
          FreshnessCard,
          {
            post,
            onAnalyze: handleAnalyzeSingle,
            onUpdate: handleUpdateContent,
            isAnalyzing: isAnalyzing[post.ID] || false,
            isUpdating: isUpdating[post.ID] || false
          },
          post.ID
        )) })
      ] })
    ] });
  };
  const App = () => {
    const [view, setView] = reactExports.useState("dashboard");
    const [styleGuide, setStyleGuide] = reactExports.useState(null);
    const [ideas, setIdeas] = reactExports.useState([]);
    const [posts, setPosts] = reactExports.useState([]);
    const [selectedDraft, setSelectedDraft] = reactExports.useState(null);
    const [activityLogs, setActivityLogs] = reactExports.useState([]);
    const [settings, setSettings] = reactExports.useState({
      mode: "manual",
      autoPublish: false,
      searchConsoleUser: null,
      gscClientId: "",
      gscClientSecret: "",
      imageSourceProvider: "pexels",
      // Changed default to simplest option
      aiImageStyle: "photorealistic",
      googleCloudProjectId: "",
      googleCloudLocation: "us-central1",
      pexelsApiKey: "",
      unsplashApiKey: "",
      pixabayApiKey: "",
      seoPlugin: "none",
      // Auto-detected, kept for backward compatibility
      geminiApiKey: "",
      // Automation frequency settings with defaults
      semiAutoIdeaFrequency: "weekly",
      fullAutoDailyPostCount: 1,
      fullAutoPublishFrequency: "daily",
      analyzeContentFrequency: "manual"
    });
    const [isLoading, setIsLoading] = reactExports.useState({});
    const [toasts, setToasts] = reactExports.useState([]);
    const [isSidebarOpen, setIsSidebarOpen] = reactExports.useState(false);
    const [publishingId, setPublishingId] = reactExports.useState(null);
    const [contentFreshness, setContentFreshness] = reactExports.useState(null);
    const [settingsOpenSection, setSettingsOpenSection] = reactExports.useState(void 0);
    const drafts = posts.filter((p) => p.status === "draft");
    const publishedPosts = posts.filter((p) => p.status === "published");
    const isGeminiApiConfigured = !!(settings.geminiApiKey && settings.geminiApiKey.trim());
    reactExports.useEffect(() => {
      if (view !== "settings") {
        setSettingsOpenSection(void 0);
      }
    }, [view]);
    const addToast = reactExports.useCallback((toast) => {
      const id = Date.now();
      setToasts((prev) => [...prev, { ...toast, id }]);
    }, []);
    const showToast = reactExports.useCallback((message, type) => {
      addToast({ message, type });
    }, [addToast]);
    const addLogEntry = reactExports.useCallback((type, details, icon) => {
      const newLog = {
        id: Date.now(),
        type,
        details,
        timestamp: (/* @__PURE__ */ new Date()).toISOString(),
        icon
      };
      setActivityLogs((prev) => [newLog, ...prev.slice(0, 49)]);
      activityApi.create({
        type,
        message: details,
        icon
      }).catch(console.error);
    }, []);
    const removeToast = reactExports.useCallback((id) => {
      setToasts((currentToasts) => currentToasts.filter((toast) => toast.id !== id));
    }, []);
    const handleAnalyzeStyle = reactExports.useCallback(async (showToast2 = true) => {
      setIsLoading((prev) => ({ ...prev, style: true }));
      try {
        const updatedStyleGuide = await styleGuideApi.analyze();
        setStyleGuide(updatedStyleGuide);
        if (showToast2) {
          addToast({ message: "Style guide updated successfully!", type: "success" });
        }
        addLogEntry("style_analyzed", "Style guide analyzed and updated", "BookOpen");
      } catch (error) {
        const errorMessage = error instanceof Error ? error.message : "Failed to analyze style guide";
        if (showToast2) {
          addToast({ message: errorMessage, type: "error" });
        }
      } finally {
        setIsLoading((prev) => ({ ...prev, style: false }));
      }
    }, [addToast, addLogEntry]);
    const handleSaveStyleGuide = reactExports.useCallback(async (updatedGuide) => {
      try {
        const savedGuide = await styleGuideApi.update(updatedGuide);
        setStyleGuide(savedGuide);
        addToast({ message: "Style guide saved successfully!", type: "success" });
        addLogEntry("style_updated", "Style guide manually updated", "BookOpen");
      } catch (error) {
        const errorMessage = error instanceof Error ? error.message : "Failed to save style guide";
        addToast({ message: errorMessage, type: "error" });
      }
    }, [addToast, addLogEntry]);
    const handleGenerateIdeas = reactExports.useCallback(async (showToast2 = true, count = 5) => {
      if (!styleGuide) {
        addToast({ message: "Please create a style guide first", type: "warning" });
        return;
      }
      setIsLoading((prev) => ({ ...prev, ideas: true }));
      try {
        const newIdeas = await ideasApi.generate(count);
        setIdeas((prev) => [...newIdeas, ...prev]);
        if (showToast2) {
          addToast({ message: `Generated ${newIdeas.length} new ideas!`, type: "success" });
        }
        addLogEntry("ideas_generated", `Generated ${newIdeas.length} new content ideas`, "Lightbulb");
      } catch (error) {
        const errorMessage = error instanceof Error ? error.message : "Failed to generate ideas";
        if (showToast2) {
          addToast({ message: errorMessage, type: "error" });
        }
      } finally {
        setIsLoading((prev) => ({ ...prev, ideas: false }));
      }
    }, [styleGuide, addToast, addLogEntry]);
    const handleGenerateSimilarIdeas = reactExports.useCallback(async (baseIdea) => {
      if (!styleGuide) {
        addToast({ message: "Please create a style guide first", type: "warning" });
        return;
      }
      setIsLoading((prev) => ({ ...prev, [`similar_${baseIdea.id}`]: true }));
      try {
        const similarIdeas = await ideasApi.generateSimilar(baseIdea.id);
        setIdeas((prev) => [...similarIdeas, ...prev]);
        addToast({ message: `Generated ${similarIdeas.length} similar ideas!`, type: "success" });
        addLogEntry("similar_ideas_generated", `Generated ${similarIdeas.length} ideas similar to "${baseIdea.title}"`, "Lightbulb");
      } catch (error) {
        const errorMessage = error instanceof Error ? error.message : "Failed to generate similar ideas";
        addToast({ message: errorMessage, type: "error" });
      } finally {
        setIsLoading((prev) => ({ ...prev, [`similar_${baseIdea.id}`]: false }));
      }
    }, [styleGuide, addToast, addLogEntry]);
    const handleCreateDraft = reactExports.useCallback(async (ideaId) => {
      if (!styleGuide) {
        addToast({ message: "Please create a style guide first", type: "warning" });
        return;
      }
      const idea = ideas.find((i) => i.id === ideaId);
      if (!idea) {
        addToast({ message: "Idea not found", type: "error" });
        return;
      }
      console.log("Creating draft for idea:", idea);
      setIsLoading((prev) => ({ ...prev, [`draft_${ideaId}`]: true }));
      addToast({
        message: `🤖 AI is generating draft for "${idea.title}"... This may take a moment.`,
        type: "info"
      });
      try {
        console.log("Calling draftsApi.createFromIdea with ideaId:", ideaId);
        const draft = await draftsApi.createFromIdea(ideaId);
        console.log("Draft created successfully:", draft);
        setPosts((prev) => [draft, ...prev]);
        const updatedIdea = { ...idea, status: "archived" };
        await ideasApi.update(ideaId, updatedIdea);
        setIdeas((prev) => prev.map((i) => i.id === ideaId ? updatedIdea : i));
        addToast({
          message: `Draft "${draft.title}" created successfully! Check WordPress admin for the post.`,
          type: "success"
        });
        addLogEntry("draft_created", `Created draft: "${draft.title}" with full WordPress integration`, "FileText");
      } catch (error) {
        console.error("Error creating draft:", error);
        let errorMessage = "Failed to create draft. Please try again.";
        if (error instanceof Error) {
          const message = error.message.toLowerCase();
          if (message.includes("503") || message.includes("overloaded") || message.includes("unavailable")) {
            errorMessage = "🤖 AI service is temporarily overloaded. Please wait a moment and try again.";
          } else if (message.includes("timeout")) {
            errorMessage = "⏱️ Request timed out. The AI service might be busy. Please try again.";
          } else if (message.includes("api key")) {
            errorMessage = "🔑 AI API key is not configured. Please check your settings.";
          } else if (message.includes("style guide")) {
            errorMessage = "📋 Style guide is required. Please create one first.";
          } else if (message.includes("after") && message.includes("attempts")) {
            errorMessage = "🔄 AI service is currently unavailable after multiple attempts. Please try again in a few minutes.";
          } else if (message.includes("ai content generation failed")) {
            errorMessage = "🤖 AI content generation failed. The service might be temporarily unavailable. Please try again.";
          }
        }
        addToast({ message: errorMessage, type: "error" });
      } finally {
        setIsLoading((prev) => ({ ...prev, [`draft_${ideaId}`]: false }));
      }
    }, [ideas, styleGuide, addToast, addLogEntry]);
    const handleUpdateDraft = reactExports.useCallback(async (draftId, updates) => {
      try {
        const updatedDraft = await draftsApi.update(draftId, updates);
        setPosts((prev) => prev.map((p) => p.id === draftId ? updatedDraft : p));
        addToast({ message: "Draft updated successfully!", type: "success" });
        addLogEntry("draft_updated", `Updated draft: "${updatedDraft.title}"`, "FileText");
      } catch (error) {
        const errorMessage = error instanceof Error ? error.message : "Failed to update draft";
        addToast({ message: errorMessage, type: "error" });
      }
    }, [addToast, addLogEntry]);
    const handlePublishPost = reactExports.useCallback(async (draftId) => {
      setPublishingId(draftId);
      try {
        const publishedPost = await publishedApi.publish(draftId);
        setPosts((prev) => prev.map((p) => p.id === draftId ? publishedPost : p));
        addToast({ message: "Post published successfully!", type: "success" });
        addLogEntry("post_published", `Published post: "${publishedPost.title}"`, "Send");
      } catch (error) {
        const errorMessage = error instanceof Error ? error.message : "Failed to publish post";
        addToast({ message: errorMessage, type: "error" });
      } finally {
        setPublishingId(null);
      }
    }, [addToast, addLogEntry]);
    const handleUpdatePostDate = reactExports.useCallback(async (postId, newDate, shouldConvertToDraft = false) => {
      try {
        const updatedPost = await publishedApi.updateDate(postId, newDate, shouldConvertToDraft);
        setPosts((prev) => prev.map((p) => p.id === postId ? updatedPost : p));
        const action = shouldConvertToDraft ? "converted to scheduled draft" : "publish date updated";
        addToast({ message: `Post ${action} successfully!`, type: "success" });
        addLogEntry("draft_updated", `Post "${updatedPost.title}" ${action}`, "Calendar");
      } catch (error) {
        const errorMessage = error instanceof Error ? error.message : "Failed to update post date";
        addToast({ message: errorMessage, type: "error" });
      }
    }, [addToast, addLogEntry]);
    const handleScheduleDraft = reactExports.useCallback(async (draftId, scheduledDate) => {
      try {
        const updatedDraft = await draftsApi.schedule(draftId, scheduledDate);
        setPosts((prev) => prev.map((p) => p.id === draftId ? updatedDraft : p));
        const formattedDate = new Date(scheduledDate).toLocaleDateString();
        const draftTitle = updatedDraft.title || `Draft ${draftId}`;
        addToast({ message: `Draft "${draftTitle}" scheduled for ${formattedDate}!`, type: "success" });
        addLogEntry("draft_scheduled", `Scheduled draft: "${draftTitle}" for ${formattedDate}`, "Calendar");
      } catch (error) {
        const errorMessage = error instanceof Error ? error.message : "Failed to schedule draft";
        addToast({ message: errorMessage, type: "error" });
        console.error("Error scheduling draft:", error);
      }
    }, [addToast, addLogEntry]);
    const handleArchiveIdea = reactExports.useCallback(async (ideaId) => {
      try {
        const idea = ideas.find((i) => i.id === ideaId);
        if (!idea) return;
        await ideasApi.delete(ideaId);
        const updatedIdea = { ...idea, status: "archived" };
        setIdeas((prev) => prev.map((i) => i.id === ideaId ? updatedIdea : i));
        addToast({ message: "Idea archived successfully!", type: "success" });
        addLogEntry("idea_archived", `Archived idea: "${idea.title}"`, "Archive");
      } catch (error) {
        const errorMessage = error instanceof Error ? error.message : "Failed to archive idea";
        addToast({ message: errorMessage, type: "error" });
      }
    }, [ideas, addToast, addLogEntry]);
    const handleDeleteIdea = reactExports.useCallback(async (ideaId) => {
      try {
        const idea = ideas.find((i) => i.id === ideaId);
        if (!idea) return;
        await ideasApi.permanentDelete(ideaId);
        setIdeas((prev) => prev.filter((i) => i.id !== ideaId));
        addToast({ message: "Idea deleted permanently!", type: "success" });
        addLogEntry("idea_updated", `Permanently deleted idea: "${idea.title}"`, "Trash");
      } catch (error) {
        const errorMessage = error instanceof Error ? error.message : "Failed to delete idea";
        addToast({ message: errorMessage, type: "error" });
      }
    }, [ideas, addToast, addLogEntry]);
    const handleRestoreIdea = reactExports.useCallback(async (ideaId) => {
      try {
        const idea = ideas.find((i) => i.id === ideaId);
        if (!idea) return;
        await ideasApi.restore(ideaId);
        const updatedIdea = { ...idea, status: "active" };
        setIdeas((prev) => prev.map((i) => i.id === ideaId ? updatedIdea : i));
        addToast({ message: "Idea restored successfully!", type: "success" });
        addLogEntry("idea_restored", `Restored idea: "${idea.title}"`, "Edit");
      } catch (error) {
        const errorMessage = error instanceof Error ? error.message : "Failed to restore idea";
        addToast({ message: errorMessage, type: "error" });
      }
    }, [ideas, addToast, addLogEntry]);
    const handleGenerateIdeasAndNavigate = reactExports.useCallback(async () => {
      await handleGenerateIdeas(false, 5);
      setView("ideas");
    }, [handleGenerateIdeas]);
    const handleUpdateIdeaTitle = reactExports.useCallback(async (ideaId, newTitle) => {
      try {
        const idea = ideas.find((i) => i.id === ideaId);
        if (!idea) return;
        const updatedIdea = { ...idea, title: newTitle };
        await ideasApi.update(ideaId, updatedIdea);
        setIdeas((prev) => prev.map((i) => i.id === ideaId ? updatedIdea : i));
        addToast({ message: "Idea updated successfully!", type: "success" });
        addLogEntry("idea_updated", `Updated idea title to: "${newTitle}"`, "Edit");
      } catch (error) {
        const errorMessage = error instanceof Error ? error.message : "Failed to update idea";
        addToast({ message: errorMessage, type: "error" });
      }
    }, [ideas, addToast, addLogEntry]);
    const handleSaveSettings = reactExports.useCallback(async (newSettings) => {
      try {
        await settingsApi.update(newSettings);
        setSettings(newSettings);
        addToast({ message: "Settings saved successfully!", type: "success" });
        addLogEntry("settings_updated", "Plugin settings updated", "Settings");
      } catch (error) {
        const errorMessage = error instanceof Error ? error.message : "Failed to save settings";
        addToast({ message: errorMessage, type: "error" });
      }
    }, [addToast, addLogEntry]);
    reactExports.useCallback(async () => {
      let refreshErrors = [];
      try {
        const settingsData = await settingsApi.get();
        setSettings(settingsData || {
          mode: "manual",
          autoPublish: false,
          searchConsoleUser: null,
          gscClientId: "",
          gscClientSecret: "",
          gscSiteUrl: "",
          geminiApiKey: "",
          is_pro: false,
          seoPlugin: "none",
          imageSource: "none",
          aiImageStyle: "photographic"
        });
      } catch (error) {
        console.error("Failed to refresh settings:", error);
        refreshErrors.push("settings");
      }
      try {
        const ideasData = await ideasApi.get();
        setIdeas(ideasData || []);
      } catch (error) {
        console.error("Failed to refresh ideas:", error);
        refreshErrors.push("ideas");
      }
      let draftsData = [];
      let publishedData = [];
      try {
        draftsData = await draftsApi.get();
      } catch (error) {
        console.error("Failed to refresh drafts:", error);
        refreshErrors.push("drafts");
      }
      try {
        publishedData = await publishedApi.get();
      } catch (error) {
        console.error("Failed to refresh published posts:", error);
        refreshErrors.push("published");
      }
      setPosts([...draftsData || [], ...publishedData || []]);
      try {
        const activityData = await activityApi.get();
        setActivityLogs(activityData || []);
      } catch (error) {
        console.error("Failed to refresh activity logs:", error);
        refreshErrors.push("activity");
      }
      if (refreshErrors.includes("settings")) {
        addToast({ message: "Failed to refresh app settings", type: "error" });
      } else if (refreshErrors.length > 0) {
        console.log("Some non-critical data failed to refresh:", refreshErrors);
      }
    }, [addToast]);
    const handleAddIdea = reactExports.useCallback(async (title, description) => {
      try {
        const newIdea = {
          title: title.trim(),
          description: description?.trim() || "",
          status: "active",
          createdAt: (/* @__PURE__ */ new Date()).toISOString(),
          tags: []
        };
        const createdIdea = await ideasApi.create(newIdea);
        setIdeas((prev) => [createdIdea, ...prev]);
        addToast({ message: "Idea added successfully!", type: "success" });
        addLogEntry("idea_added", `Manually added idea: "${title.trim()}"`, "PlusCircle");
      } catch (error) {
        const errorMessage = error instanceof Error ? error.message : "Failed to add idea";
        addToast({ message: errorMessage, type: "error" });
      }
    }, [addToast, addLogEntry]);
    const renderView = () => {
      switch (view) {
        case "style-guide":
          return /* @__PURE__ */ jsxRuntimeExports.jsx(StyleGuideManager, { styleGuide, onAnalyze: () => handleAnalyzeStyle(false), onSaveStyleGuide: handleSaveStyleGuide, isLoading: isLoading["style"] });
        case "ideas":
          return /* @__PURE__ */ jsxRuntimeExports.jsx(
            IdeaBoard,
            {
              ideas,
              onGenerate: () => handleGenerateIdeas(false, 5),
              onCreateDraft: (idea) => handleCreateDraft(idea.id),
              onArchive: handleArchiveIdea,
              onDeleteIdea: handleDeleteIdea,
              onRestoreIdea: handleRestoreIdea,
              isLoading: isLoading["ideas"],
              isLoadingDraft: isLoading,
              onUpdateTitle: handleUpdateIdeaTitle,
              onGenerateSimilar: handleGenerateSimilarIdeas,
              onAddIdea: handleAddIdea
            }
          );
        case "drafts":
          return /* @__PURE__ */ jsxRuntimeExports.jsx(
            DraftsList,
            {
              drafts,
              onSelectDraft: setSelectedDraft,
              onPublish: handlePublishPost,
              publishingId,
              onNavigateToIdeas: () => setView("ideas")
            }
          );
        case "published":
          return /* @__PURE__ */ jsxRuntimeExports.jsx(
            PublishedList,
            {
              posts: publishedPosts,
              onSelectPost: setSelectedDraft,
              onNavigateToDrafts: () => setView("drafts")
            }
          );
        case "settings":
          return /* @__PURE__ */ jsxRuntimeExports.jsx(
            SettingsLicense,
            {
              settings,
              onSaveSettings: handleSaveSettings,
              onShowToast: showToast
            }
          );
        case "settings_license":
          return /* @__PURE__ */ jsxRuntimeExports.jsx(
            SettingsLicense,
            {
              settings,
              onSaveSettings: handleSaveSettings,
              onShowToast: showToast
            }
          );
        case "settings_automation":
          return /* @__PURE__ */ jsxRuntimeExports.jsx(
            SettingsAutomation,
            {
              settings,
              onSaveSettings: handleSaveSettings,
              onShowToast: showToast
            }
          );
        case "settings_integrations":
          return /* @__PURE__ */ jsxRuntimeExports.jsx(
            SettingsIntegrations,
            {
              settings,
              onSaveSettings: handleSaveSettings,
              onShowToast: showToast,
              isProActive: settings.is_pro
            }
          );
        case "settings_content":
          return /* @__PURE__ */ jsxRuntimeExports.jsx(
            SettingsContent,
            {
              settings,
              onSaveSettings: handleSaveSettings,
              onShowToast: showToast,
              isProActive: settings.is_pro
            }
          );
        case "settings_advanced":
          return /* @__PURE__ */ jsxRuntimeExports.jsx(
            SettingsAdvanced,
            {
              onShowToast: showToast
            }
          );
        case "calendar":
          return /* @__PURE__ */ jsxRuntimeExports.jsx(
            ContentCalendar,
            {
              drafts,
              publishedPosts,
              onScheduleDraft: handleScheduleDraft,
              onSelectPost: setSelectedDraft,
              onPublishDraft: handlePublishPost,
              onUpdatePostDate: handleUpdatePostDate
            }
          );
        case "content-freshness":
          return /* @__PURE__ */ jsxRuntimeExports.jsx(
            ContentFreshnessManager,
            {
              onShowToast: showToast,
              settings
            }
          );
        case "dashboard":
        default:
          return /* @__PURE__ */ jsxRuntimeExports.jsx(
            Dashboard,
            {
              stats: {
                ideas: ideas.filter((idea) => idea.status === "active").length,
                drafts: drafts.length,
                published: publishedPosts.length,
                contentFreshness
              },
              lastAnalyzed: styleGuide?.lastAnalyzed,
              activityLogs,
              onNavigate: setView,
              onGenerateIdeas: handleGenerateIdeasAndNavigate,
              onUpdateStyleGuide: () => handleAnalyzeStyle(false),
              isLoadingIdeas: isLoading["ideas"] || false,
              isLoadingStyle: isLoading["style"] || false
            }
          );
      }
    };
    reactExports.useEffect(() => {
      if (!window.acaData) {
        console.error("ACA Error: WordPress localized data not available");
        showToast("Plugin not properly loaded. Please refresh the page.", "error");
        return;
      }
      setGeminiApiKey(settings.geminiApiKey);
    }, [settings.geminiApiKey, showToast]);
    reactExports.useEffect(() => {
      const loadInitialData = async () => {
        const results = await Promise.allSettled([
          settingsApi.get(),
          styleGuideApi.get(),
          ideasApi.get(),
          draftsApi.get(),
          publishedApi.get(),
          activityApi.get()
        ]);
        const [settingsResult, styleGuideResult, ideasResult, draftsResult, publishedResult, activityResult] = results;
        let settingsData = null;
        let styleGuideData = null;
        let ideasData = null;
        let draftsData = null;
        let publishedData = null;
        let activityData = null;
        const failedLoads = [];
        if (settingsResult.status === "fulfilled") {
          settingsData = settingsResult.value;
        } else {
          console.error("Failed to load settings:", settingsResult.reason);
          failedLoads.push("Settings");
        }
        if (styleGuideResult.status === "fulfilled") {
          styleGuideData = styleGuideResult.value;
        } else {
          console.error("Failed to load style guide:", styleGuideResult.reason);
          failedLoads.push("Style Guide");
        }
        if (ideasResult.status === "fulfilled") {
          ideasData = ideasResult.value;
        } else {
          console.error("Failed to load ideas:", ideasResult.reason);
          failedLoads.push("Ideas");
        }
        if (draftsResult.status === "fulfilled") {
          draftsData = draftsResult.value;
        } else {
          console.error("Failed to load drafts:", draftsResult.reason);
          failedLoads.push("Drafts");
        }
        if (publishedResult.status === "fulfilled") {
          publishedData = publishedResult.value;
        } else {
          console.error("Failed to load published posts:", publishedResult.reason);
          failedLoads.push("Published Posts");
        }
        if (activityResult.status === "fulfilled") {
          activityData = activityResult.value;
        } else {
          console.error("Failed to load activity logs:", activityResult.reason);
          failedLoads.push("Activity Logs");
        }
        try {
          const freshnessResponse = await contentFreshnessApi.getPosts(50, "all");
          if (freshnessResponse && freshnessResponse.success && freshnessResponse.posts) {
            const posts2 = freshnessResponse.posts;
            const needsUpdate = posts2.filter((post) => post.needs_update).length;
            const postsWithScores = posts2.filter((post) => post.freshness_score !== null);
            const averageScore = postsWithScores.length > 0 ? Math.round(postsWithScores.reduce((sum, post) => sum + (post.freshness_score || 0), 0) / postsWithScores.length) : 0;
            const analyzed = postsWithScores.filter((post) => post.freshness_score !== null).length;
            setContentFreshness({
              total: posts2.length,
              analyzed,
              needsUpdate,
              averageScore
            });
          }
        } catch (error) {
          console.log("Content freshness not available (Pro feature)");
        }
        setSettings(settingsData || {
          mode: "manual",
          autoPublish: false,
          searchConsoleUser: null,
          gscClientId: "",
          gscClientSecret: "",
          imageSourceProvider: "pexels",
          aiImageStyle: "photorealistic",
          pexelsApiKey: "",
          unsplashApiKey: "",
          pixabayApiKey: "",
          seoPlugin: "none",
          // Auto-detected, kept for backward compatibility
          geminiApiKey: "",
          // Automation frequency settings with defaults
          semiAutoIdeaFrequency: "weekly",
          fullAutoDailyPostCount: 1,
          fullAutoPublishFrequency: "daily",
          analyzeContentFrequency: "manual"
        });
        if (styleGuideData) {
          setStyleGuide(styleGuideData);
        }
        setIdeas(ideasData || []);
        setPosts([...draftsData || [], ...publishedData || []]);
        setActivityLogs(activityData || []);
        if (failedLoads.length > 0) {
          const failedItems = failedLoads.join(", ");
          addToast({
            message: `Some data could not be loaded: ${failedItems}. Plugin will work with available data.`,
            type: "warning"
          });
        }
      };
      loadInitialData();
    }, []);
    reactExports.useEffect(() => {
      const handleResize = () => {
        if (window.innerWidth > 782 && isSidebarOpen) {
          setIsSidebarOpen(false);
        }
        if (isSidebarOpen && window.innerWidth <= 782) {
          document.body.classList.add("aca-sidebar-open");
        } else {
          document.body.classList.remove("aca-sidebar-open");
        }
      };
      handleResize();
      window.addEventListener("resize", handleResize);
      return () => {
        window.removeEventListener("resize", handleResize);
        document.body.classList.remove("aca-sidebar-open");
      };
    }, [isSidebarOpen]);
    return /* @__PURE__ */ jsxRuntimeExports.jsxs(jsxRuntimeExports.Fragment, { children: [
      /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-container", children: [
        /* @__PURE__ */ jsxRuntimeExports.jsx(
          "button",
          {
            className: "aca-mobile-hamburger",
            onClick: (e) => {
              e.stopPropagation();
              e.preventDefault();
              setIsSidebarOpen(!isSidebarOpen);
            },
            "aria-label": "Toggle navigation menu",
            style: { display: window.innerWidth <= 782 ? "block" : "none" },
            children: "☰"
          }
        ),
        /* @__PURE__ */ jsxRuntimeExports.jsx(
          "div",
          {
            className: `aca-mobile-overlay ${isSidebarOpen ? "active" : ""}`,
            onClick: (e) => {
              e.stopPropagation();
              setIsSidebarOpen(false);
            }
          }
        ),
        /* @__PURE__ */ jsxRuntimeExports.jsx(
          Sidebar,
          {
            currentView: view,
            setView,
            isOpen: isSidebarOpen,
            closeSidebar: () => setIsSidebarOpen(false)
          }
        ),
        /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-main", children: [
          /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-mobile-header", children: [
            /* @__PURE__ */ jsxRuntimeExports.jsx(
              "button",
              {
                onClick: () => setIsSidebarOpen(true),
                className: "aca-menu-toggle",
                "aria-label": "Open menu",
                children: /* @__PURE__ */ jsxRuntimeExports.jsx(Menu, { className: "h-6 w-6" })
              }
            ),
            /* @__PURE__ */ jsxRuntimeExports.jsx("span", { className: "font-semibold text-white", children: "AI Content Agent (ACA)" })
          ] }),
          /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { className: "aca-fade-in", children: [
            !isGeminiApiConfigured && !view.startsWith("settings") && /* @__PURE__ */ jsxRuntimeExports.jsx(GeminiApiWarning, { onNavigateToSettings: () => {
              setView("settings_integrations");
            } }),
            renderView()
          ] })
        ] })
      ] }),
      selectedDraft && /* @__PURE__ */ jsxRuntimeExports.jsx(
        DraftModal,
        {
          draft: selectedDraft,
          onClose: () => setSelectedDraft(null),
          onSave: handleUpdateDraft,
          settings
        }
      ),
      /* @__PURE__ */ jsxRuntimeExports.jsx("div", { className: "aca-toast-container", children: toasts.map((toast) => /* @__PURE__ */ jsxRuntimeExports.jsx(Toast, { ...toast, onDismiss: removeToast }, toast.id)) })
    ] });
  };
  class ErrorBoundary extends React.Component {
    constructor(props) {
      super(props);
      this.state = { hasError: false };
    }
    static getDerivedStateFromError(error) {
      return { hasError: true, error };
    }
    componentDidCatch(error, errorInfo) {
      console.error("ACA Plugin Error:", error, errorInfo);
    }
    render() {
      if (this.state.hasError) {
        return /* @__PURE__ */ jsxRuntimeExports.jsxs("div", { style: {
          padding: "20px",
          background: "#ffebee",
          border: "1px solid #f44336",
          borderRadius: "4px",
          margin: "20px"
        }, children: [
          /* @__PURE__ */ jsxRuntimeExports.jsx("h2", { style: { color: "#d32f2f", margin: "0 0 10px 0" }, children: "AI Content Agent (ACA) - Loading Error" }),
          /* @__PURE__ */ jsxRuntimeExports.jsx("p", { style: { margin: "0 0 10px 0" }, children: "The plugin interface failed to load. This might be a browser cache issue." }),
          /* @__PURE__ */ jsxRuntimeExports.jsxs("p", { style: { margin: "0 0 10px 0", fontSize: "12px", color: "#666" }, children: [
            "Error: ",
            this.state.error?.message || "Unknown error"
          ] }),
          /* @__PURE__ */ jsxRuntimeExports.jsx(
            "button",
            {
              onClick: () => window.location.reload(),
              style: {
                padding: "8px 16px",
                background: "#1976d2",
                color: "white",
                border: "none",
                borderRadius: "4px",
                cursor: "pointer"
              },
              children: "Reload Page"
            }
          )
        ] });
      }
      return this.props.children;
    }
  }
  const rootElement = document.getElementById("root");
  if (!rootElement) {
    throw new Error("Could not find root element to mount to");
  }
  const root = ReactDOM.createRoot(rootElement);
  root.render(
    /* @__PURE__ */ jsxRuntimeExports.jsx(ErrorBoundary, { children: /* @__PURE__ */ jsxRuntimeExports.jsx(App, {}) })
  );
})();
