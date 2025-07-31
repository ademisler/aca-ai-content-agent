/**
 * Advanced Accessibility Utilities for AI Content Agent
 * 
 * Provides comprehensive accessibility features including screen reader support,
 * voice navigation, keyboard navigation, and WCAG 2.1 AA compliance.
 * 
 * @package AI_Content_Agent
 * @version 2.3.8
 * @since 2.3.8
 */

/**
 * Screen Reader Announcer
 */
export class ScreenReaderAnnouncer {
    private announcer: HTMLElement;
    private politeAnnouncer: HTMLElement;
    
    constructor() {
        this.createAnnouncers();
    }
    
    /**
     * Create ARIA live regions for announcements
     */
    private createAnnouncers(): void {
        // Assertive announcer for urgent messages
        this.announcer = document.createElement('div');
        this.announcer.setAttribute('aria-live', 'assertive');
        this.announcer.setAttribute('aria-atomic', 'true');
        this.announcer.setAttribute('class', 'aca-sr-only');
        this.announcer.style.cssText = `
            position: absolute !important;
            width: 1px !important;
            height: 1px !important;
            padding: 0 !important;
            margin: -1px !important;
            overflow: hidden !important;
            clip: rect(0, 0, 0, 0) !important;
            white-space: nowrap !important;
            border: 0 !important;
        `;
        document.body.appendChild(this.announcer);
        
        // Polite announcer for non-urgent messages
        this.politeAnnouncer = document.createElement('div');
        this.politeAnnouncer.setAttribute('aria-live', 'polite');
        this.politeAnnouncer.setAttribute('aria-atomic', 'true');
        this.politeAnnouncer.setAttribute('class', 'aca-sr-only');
        this.politeAnnouncer.style.cssText = this.announcer.style.cssText;
        document.body.appendChild(this.politeAnnouncer);
    }
    
    /**
     * Announce message to screen readers
     */
    announce(message: string, priority: 'assertive' | 'polite' = 'polite'): void {
        const announcer = priority === 'assertive' ? this.announcer : this.politeAnnouncer;
        
        // Clear previous message
        announcer.textContent = '';
        
        // Add new message after a brief delay to ensure it's announced
        setTimeout(() => {
            announcer.textContent = message;
        }, 100);
        
        // Clear message after announcement
        setTimeout(() => {
            announcer.textContent = '';
        }, 1000);
    }
    
    /**
     * Announce status changes
     */
    announceStatus(status: string, context?: string): void {
        const message = context ? `${context}: ${status}` : status;
        this.announce(message, 'polite');
    }
    
    /**
     * Announce errors
     */
    announceError(error: string): void {
        this.announce(`Error: ${error}`, 'assertive');
    }
    
    /**
     * Announce success messages
     */
    announceSuccess(message: string): void {
        this.announce(`Success: ${message}`, 'polite');
    }
}

/**
 * Voice Navigation Support
 */
export class VoiceNavigation {
    private recognition: SpeechRecognition | null = null;
    private isListening: boolean = false;
    private commands: Map<string, () => void> = new Map();
    
    constructor() {
        this.initializeSpeechRecognition();
        this.registerDefaultCommands();
    }
    
    /**
     * Initialize speech recognition
     */
    private initializeSpeechRecognition(): void {
        if ('webkitSpeechRecognition' in window) {
            this.recognition = new (window as any).webkitSpeechRecognition();
        } else if ('SpeechRecognition' in window) {
            this.recognition = new (window as any).SpeechRecognition();
        }
        
        if (this.recognition) {
            this.recognition.continuous = true;
            this.recognition.interimResults = false;
            this.recognition.lang = 'en-US';
            
            this.recognition.onresult = (event) => {
                const command = event.results[event.results.length - 1][0].transcript.toLowerCase().trim();
                this.processCommand(command);
            };
            
            this.recognition.onerror = (event) => {
                console.error('Speech recognition error:', event.error);
                if (event.error === 'no-speech') {
                    this.restart();
                }
            };
            
            this.recognition.onend = () => {
                if (this.isListening) {
                    this.restart();
                }
            };
        }
    }
    
    /**
     * Register default voice commands
     */
    private registerDefaultCommands(): void {
        this.registerCommand('go to ideas', () => {
            this.navigateToView('ideas');
        });
        
        this.registerCommand('go to drafts', () => {
            this.navigateToView('drafts');
        });
        
        this.registerCommand('go to settings', () => {
            this.navigateToView('settings');
        });
        
        this.registerCommand('create new idea', () => {
            this.triggerAction('create-idea');
        });
        
        this.registerCommand('save settings', () => {
            this.triggerAction('save-settings');
        });
        
        this.registerCommand('help', () => {
            this.showVoiceCommands();
        });
        
        this.registerCommand('stop listening', () => {
            this.stop();
        });
    }
    
    /**
     * Register a voice command
     */
    registerCommand(command: string, action: () => void): void {
        this.commands.set(command.toLowerCase(), action);
    }
    
    /**
     * Process voice command
     */
    private processCommand(command: string): void {
        const action = this.commands.get(command);
        if (action) {
            action();
            screenReaderAnnouncer.announce(`Executed command: ${command}`);
        } else {
            // Try partial matching
            const matchingCommand = Array.from(this.commands.keys()).find(cmd => 
                cmd.includes(command) || command.includes(cmd)
            );
            
            if (matchingCommand) {
                const action = this.commands.get(matchingCommand);
                if (action) {
                    action();
                    screenReaderAnnouncer.announce(`Executed command: ${matchingCommand}`);
                }
            } else {
                screenReaderAnnouncer.announce(`Command not recognized: ${command}`);
            }
        }
    }
    
    /**
     * Start voice recognition
     */
    start(): void {
        if (this.recognition && !this.isListening) {
            this.isListening = true;
            this.recognition.start();
            screenReaderAnnouncer.announce('Voice navigation started. Say "help" for available commands.');
        }
    }
    
    /**
     * Stop voice recognition
     */
    stop(): void {
        if (this.recognition && this.isListening) {
            this.isListening = false;
            this.recognition.stop();
            screenReaderAnnouncer.announce('Voice navigation stopped.');
        }
    }
    
    /**
     * Restart voice recognition
     */
    private restart(): void {
        if (this.recognition && this.isListening) {
            this.recognition.start();
        }
    }
    
    /**
     * Navigate to a view
     */
    private navigateToView(view: string): void {
        const event = new CustomEvent('aca-navigate', { detail: { view } });
        document.dispatchEvent(event);
    }
    
    /**
     * Trigger an action
     */
    private triggerAction(action: string): void {
        const event = new CustomEvent('aca-action', { detail: { action } });
        document.dispatchEvent(event);
    }
    
    /**
     * Show available voice commands
     */
    private showVoiceCommands(): void {
        const commands = Array.from(this.commands.keys()).join(', ');
        screenReaderAnnouncer.announce(`Available commands: ${commands}`);
    }
    
    /**
     * Check if voice navigation is supported
     */
    isSupported(): boolean {
        return this.recognition !== null;
    }
}

/**
 * Keyboard Navigation Manager
 */
export class KeyboardNavigationManager {
    private focusableElements: string = `
        a[href]:not([disabled]),
        button:not([disabled]),
        textarea:not([disabled]),
        input:not([disabled]):not([type="hidden"]),
        select:not([disabled]),
        [tabindex]:not([tabindex="-1"]):not([disabled]),
        [contenteditable="true"]:not([disabled])
    `;
    
    constructor() {
        this.initializeKeyboardNavigation();
    }
    
    /**
     * Initialize keyboard navigation
     */
    private initializeKeyboardNavigation(): void {
        document.addEventListener('keydown', (event) => {
            this.handleKeyDown(event);
        });
        
        // Focus management for modals and dialogs
        document.addEventListener('aca-modal-open', (event: any) => {
            this.trapFocus(event.detail.modal);
        });
        
        document.addEventListener('aca-modal-close', () => {
            this.restoreFocus();
        });
    }
    
    /**
     * Handle keyboard events
     */
    private handleKeyDown(event: KeyboardEvent): void {
        // Skip navigation shortcuts
        if (event.altKey && !event.ctrlKey && !event.shiftKey) {
            switch (event.key) {
                case '1':
                    event.preventDefault();
                    this.focusSkipLink('main-content');
                    break;
                case '2':
                    event.preventDefault();
                    this.focusSkipLink('navigation');
                    break;
                case '3':
                    event.preventDefault();
                    this.focusSkipLink('settings');
                    break;
            }
        }
        
        // Escape key handling
        if (event.key === 'Escape') {
            this.handleEscape();
        }
        
        // Tab navigation in modals
        if (event.key === 'Tab') {
            const modal = document.querySelector('[role="dialog"]:not([hidden])') as HTMLElement;
            if (modal) {
                this.handleTabInModal(event, modal);
            }
        }
    }
    
    /**
     * Focus skip link target
     */
    private focusSkipLink(targetId: string): void {
        const target = document.getElementById(targetId);
        if (target) {
            target.focus();
            screenReaderAnnouncer.announce(`Jumped to ${targetId.replace('-', ' ')}`);
        }
    }
    
    /**
     * Handle escape key
     */
    private handleEscape(): void {
        // Close modals
        const modal = document.querySelector('[role="dialog"]:not([hidden])') as HTMLElement;
        if (modal) {
            const closeButton = modal.querySelector('[data-dismiss="modal"]') as HTMLElement;
            if (closeButton) {
                closeButton.click();
            }
        }
        
        // Close dropdowns
        const dropdown = document.querySelector('.dropdown.open') as HTMLElement;
        if (dropdown) {
            dropdown.classList.remove('open');
        }
    }
    
    /**
     * Trap focus within modal
     */
    private trapFocus(modal: HTMLElement): void {
        const focusableElements = modal.querySelectorAll(this.focusableElements);
        const firstElement = focusableElements[0] as HTMLElement;
        const lastElement = focusableElements[focusableElements.length - 1] as HTMLElement;
        
        // Focus first element
        if (firstElement) {
            firstElement.focus();
        }
        
        // Store current focus for restoration
        modal.setAttribute('data-previous-focus', document.activeElement?.id || '');
    }
    
    /**
     * Handle tab navigation in modal
     */
    private handleTabInModal(event: KeyboardEvent, modal: HTMLElement): void {
        const focusableElements = modal.querySelectorAll(this.focusableElements);
        const firstElement = focusableElements[0] as HTMLElement;
        const lastElement = focusableElements[focusableElements.length - 1] as HTMLElement;
        
        if (event.shiftKey) {
            // Shift + Tab
            if (document.activeElement === firstElement) {
                event.preventDefault();
                lastElement.focus();
            }
        } else {
            // Tab
            if (document.activeElement === lastElement) {
                event.preventDefault();
                firstElement.focus();
            }
        }
    }
    
    /**
     * Restore focus after modal closes
     */
    private restoreFocus(): void {
        const modal = document.querySelector('[role="dialog"][data-previous-focus]') as HTMLElement;
        if (modal) {
            const previousFocusId = modal.getAttribute('data-previous-focus');
            if (previousFocusId) {
                const previousElement = document.getElementById(previousFocusId) as HTMLElement;
                if (previousElement) {
                    previousElement.focus();
                }
            }
            modal.removeAttribute('data-previous-focus');
        }
    }
    
    /**
     * Make element focusable
     */
    makeFocusable(element: HTMLElement): void {
        if (!element.hasAttribute('tabindex')) {
            element.setAttribute('tabindex', '0');
        }
    }
    
    /**
     * Remove element from tab order
     */
    removeFromTabOrder(element: HTMLElement): void {
        element.setAttribute('tabindex', '-1');
    }
}

/**
 * WCAG Compliance Checker
 */
export class WCAGComplianceChecker {
    private violations: Array<{
        element: HTMLElement;
        rule: string;
        level: 'A' | 'AA' | 'AAA';
        description: string;
    }> = [];
    
    /**
     * Check WCAG compliance
     */
    checkCompliance(): Array<any> {
        this.violations = [];
        
        this.checkColorContrast();
        this.checkAltText();
        this.checkHeadingStructure();
        this.checkFormLabels();
        this.checkFocusManagement();
        this.checkARIAAttributes();
        
        return this.violations;
    }
    
    /**
     * Check color contrast ratios
     */
    private checkColorContrast(): void {
        const textElements = document.querySelectorAll('p, span, div, a, button, input, textarea, label');
        
        textElements.forEach((element) => {
            const styles = window.getComputedStyle(element as HTMLElement);
            const color = styles.color;
            const backgroundColor = styles.backgroundColor;
            
            // Skip if transparent or no background
            if (backgroundColor === 'rgba(0, 0, 0, 0)' || backgroundColor === 'transparent') {
                return;
            }
            
            const contrast = this.calculateContrastRatio(color, backgroundColor);
            const fontSize = parseFloat(styles.fontSize);
            const fontWeight = styles.fontWeight;
            
            const isLargeText = fontSize >= 18 || (fontSize >= 14 && (fontWeight === 'bold' || parseInt(fontWeight) >= 700));
            const requiredRatio = isLargeText ? 3 : 4.5;
            
            if (contrast < requiredRatio) {
                this.violations.push({
                    element: element as HTMLElement,
                    rule: 'WCAG 2.1 1.4.3 Contrast (Minimum)',
                    level: 'AA',
                    description: `Color contrast ratio ${contrast.toFixed(2)} is below required ${requiredRatio}`
                });
            }
        });
    }
    
    /**
     * Check alt text for images
     */
    private checkAltText(): void {
        const images = document.querySelectorAll('img');
        
        images.forEach((img) => {
            if (!img.hasAttribute('alt')) {
                this.violations.push({
                    element: img,
                    rule: 'WCAG 2.1 1.1.1 Non-text Content',
                    level: 'A',
                    description: 'Image missing alt attribute'
                });
            } else if (img.getAttribute('alt') === '') {
                // Check if decorative image should have empty alt
                const isDecorative = img.hasAttribute('role') && img.getAttribute('role') === 'presentation';
                if (!isDecorative) {
                    this.violations.push({
                        element: img,
                        rule: 'WCAG 2.1 1.1.1 Non-text Content',
                        level: 'A',
                        description: 'Image has empty alt text but may not be decorative'
                    });
                }
            }
        });
    }
    
    /**
     * Check heading structure
     */
    private checkHeadingStructure(): void {
        const headings = document.querySelectorAll('h1, h2, h3, h4, h5, h6');
        let previousLevel = 0;
        
        headings.forEach((heading) => {
            const level = parseInt(heading.tagName.charAt(1));
            
            if (level > previousLevel + 1) {
                this.violations.push({
                    element: heading as HTMLElement,
                    rule: 'WCAG 2.1 1.3.1 Info and Relationships',
                    level: 'A',
                    description: `Heading level ${level} skips from level ${previousLevel}`
                });
            }
            
            previousLevel = level;
        });
    }
    
    /**
     * Check form labels
     */
    private checkFormLabels(): void {
        const formControls = document.querySelectorAll('input:not([type="hidden"]), textarea, select');
        
        formControls.forEach((control) => {
            const hasLabel = this.hasAssociatedLabel(control as HTMLElement);
            const hasAriaLabel = control.hasAttribute('aria-label') || control.hasAttribute('aria-labelledby');
            
            if (!hasLabel && !hasAriaLabel) {
                this.violations.push({
                    element: control as HTMLElement,
                    rule: 'WCAG 2.1 1.3.1 Info and Relationships',
                    level: 'A',
                    description: 'Form control missing label or aria-label'
                });
            }
        });
    }
    
    /**
     * Check focus management
     */
    private checkFocusManagement(): void {
        const focusableElements = document.querySelectorAll('a, button, input, textarea, select, [tabindex]');
        
        focusableElements.forEach((element) => {
            const tabIndex = element.getAttribute('tabindex');
            
            if (tabIndex && parseInt(tabIndex) > 0) {
                this.violations.push({
                    element: element as HTMLElement,
                    rule: 'WCAG 2.1 2.4.3 Focus Order',
                    level: 'A',
                    description: 'Positive tabindex values can create confusing focus order'
                });
            }
        });
    }
    
    /**
     * Check ARIA attributes
     */
    private checkARIAAttributes(): void {
        const elementsWithAria = document.querySelectorAll('[aria-describedby], [aria-labelledby]');
        
        elementsWithAria.forEach((element) => {
            const describedBy = element.getAttribute('aria-describedby');
            const labelledBy = element.getAttribute('aria-labelledby');
            
            if (describedBy && !document.getElementById(describedBy)) {
                this.violations.push({
                    element: element as HTMLElement,
                    rule: 'WCAG 2.1 4.1.2 Name, Role, Value',
                    level: 'A',
                    description: `aria-describedby references non-existent element: ${describedBy}`
                });
            }
            
            if (labelledBy && !document.getElementById(labelledBy)) {
                this.violations.push({
                    element: element as HTMLElement,
                    rule: 'WCAG 2.1 4.1.2 Name, Role, Value',
                    level: 'A',
                    description: `aria-labelledby references non-existent element: ${labelledBy}`
                });
            }
        });
    }
    
    /**
     * Check if form control has associated label
     */
    private hasAssociatedLabel(control: HTMLElement): boolean {
        const id = control.id;
        if (id) {
            const label = document.querySelector(`label[for="${id}"]`);
            if (label) return true;
        }
        
        // Check if wrapped in label
        const parentLabel = control.closest('label');
        return !!parentLabel;
    }
    
    /**
     * Calculate contrast ratio between two colors
     */
    private calculateContrastRatio(color1: string, color2: string): number {
        const rgb1 = this.parseColor(color1);
        const rgb2 = this.parseColor(color2);
        
        const l1 = this.getRelativeLuminance(rgb1);
        const l2 = this.getRelativeLuminance(rgb2);
        
        const lighter = Math.max(l1, l2);
        const darker = Math.min(l1, l2);
        
        return (lighter + 0.05) / (darker + 0.05);
    }
    
    /**
     * Parse color string to RGB values
     */
    private parseColor(color: string): [number, number, number] {
        const div = document.createElement('div');
        div.style.color = color;
        document.body.appendChild(div);
        const computedColor = window.getComputedStyle(div).color;
        document.body.removeChild(div);
        
        const match = computedColor.match(/rgb\((\d+),\s*(\d+),\s*(\d+)\)/);
        if (match) {
            return [parseInt(match[1]), parseInt(match[2]), parseInt(match[3])];
        }
        
        return [0, 0, 0]; // Default to black
    }
    
    /**
     * Get relative luminance of RGB color
     */
    private getRelativeLuminance([r, g, b]: [number, number, number]): number {
        const sRGB = [r, g, b].map(c => {
            c = c / 255;
            return c <= 0.03928 ? c / 12.92 : Math.pow((c + 0.055) / 1.055, 2.4);
        });
        
        return 0.2126 * sRGB[0] + 0.7152 * sRGB[1] + 0.0722 * sRGB[2];
    }
}

// Global instances
export const screenReaderAnnouncer = new ScreenReaderAnnouncer();
export const voiceNavigation = new VoiceNavigation();
export const keyboardNavigation = new KeyboardNavigationManager();
export const wcagChecker = new WCAGComplianceChecker();

/**
 * Initialize accessibility features
 */
export function initializeAccessibility(): void {
    // Add skip links
    addSkipLinks();
    
    // Initialize focus management
    initializeFocusManagement();
    
    // Add accessibility toolbar
    addAccessibilityToolbar();
    
    // Check compliance in development
    if (process.env.NODE_ENV === 'development') {
        const violations = wcagChecker.checkCompliance();
        if (violations.length > 0) {
            console.warn('WCAG Compliance violations found:', violations);
        }
    }
}

/**
 * Add skip links for keyboard navigation
 */
function addSkipLinks(): void {
    const skipLinks = document.createElement('div');
    skipLinks.className = 'aca-skip-links';
    skipLinks.innerHTML = `
        <a href="#main-content" class="aca-skip-link">Skip to main content</a>
        <a href="#navigation" class="aca-skip-link">Skip to navigation</a>
        <a href="#settings" class="aca-skip-link">Skip to settings</a>
    `;
    
    document.body.insertBefore(skipLinks, document.body.firstChild);
}

/**
 * Initialize focus management
 */
function initializeFocusManagement(): void {
    // Add focus indicators
    const style = document.createElement('style');
    style.textContent = `
        .aca-skip-link {
            position: absolute;
            top: -40px;
            left: 6px;
            background: #000;
            color: #fff;
            padding: 8px;
            text-decoration: none;
            z-index: 100000;
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        .aca-skip-link:focus {
            top: 6px;
            opacity: 1;
        }
        
        .aca-focus-visible {
            outline: 2px solid #005fcc;
            outline-offset: 2px;
        }
        
        .aca-sr-only {
            position: absolute !important;
            width: 1px !important;
            height: 1px !important;
            padding: 0 !important;
            margin: -1px !important;
            overflow: hidden !important;
            clip: rect(0, 0, 0, 0) !important;
            white-space: nowrap !important;
            border: 0 !important;
        }
    `;
    document.head.appendChild(style);
}

/**
 * Add accessibility toolbar
 */
function addAccessibilityToolbar(): void {
    const toolbar = document.createElement('div');
    toolbar.className = 'aca-accessibility-toolbar';
    toolbar.innerHTML = `
        <button type="button" id="aca-voice-toggle" aria-pressed="false">
            <span class="aca-sr-only">Toggle voice navigation</span>
            🎤
        </button>
        <button type="button" id="aca-contrast-toggle" aria-pressed="false">
            <span class="aca-sr-only">Toggle high contrast</span>
            🎨
        </button>
        <button type="button" id="aca-font-size-toggle" aria-pressed="false">
            <span class="aca-sr-only">Increase font size</span>
            🔍
        </button>
    `;
    
    // Add toolbar styles
    const style = document.createElement('style');
    style.textContent = `
        .aca-accessibility-toolbar {
            position: fixed;
            top: 10px;
            right: 10px;
            z-index: 99999;
            display: flex;
            gap: 5px;
            background: rgba(0, 0, 0, 0.8);
            padding: 5px;
            border-radius: 5px;
        }
        
        .aca-accessibility-toolbar button {
            background: transparent;
            border: 1px solid #fff;
            color: #fff;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
            font-size: 16px;
        }
        
        .aca-accessibility-toolbar button:hover,
        .aca-accessibility-toolbar button:focus {
            background: rgba(255, 255, 255, 0.2);
        }
        
        .aca-accessibility-toolbar button[aria-pressed="true"] {
            background: #005fcc;
        }
    `;
    document.head.appendChild(style);
    
    document.body.appendChild(toolbar);
    
    // Add event listeners
    document.getElementById('aca-voice-toggle')?.addEventListener('click', toggleVoiceNavigation);
    document.getElementById('aca-contrast-toggle')?.addEventListener('click', toggleHighContrast);
    document.getElementById('aca-font-size-toggle')?.addEventListener('click', toggleFontSize);
}

/**
 * Toggle voice navigation
 */
function toggleVoiceNavigation(): void {
    const button = document.getElementById('aca-voice-toggle');
    if (!button) return;
    
    const isActive = button.getAttribute('aria-pressed') === 'true';
    
    if (isActive) {
        voiceNavigation.stop();
        button.setAttribute('aria-pressed', 'false');
    } else {
        if (voiceNavigation.isSupported()) {
            voiceNavigation.start();
            button.setAttribute('aria-pressed', 'true');
        } else {
            screenReaderAnnouncer.announce('Voice navigation is not supported in this browser');
        }
    }
}

/**
 * Toggle high contrast mode
 */
function toggleHighContrast(): void {
    const button = document.getElementById('aca-contrast-toggle');
    if (!button) return;
    
    const isActive = button.getAttribute('aria-pressed') === 'true';
    
    if (isActive) {
        document.body.classList.remove('aca-high-contrast');
        button.setAttribute('aria-pressed', 'false');
        screenReaderAnnouncer.announce('High contrast mode disabled');
    } else {
        document.body.classList.add('aca-high-contrast');
        button.setAttribute('aria-pressed', 'true');
        screenReaderAnnouncer.announce('High contrast mode enabled');
    }
}

/**
 * Toggle large font size
 */
function toggleFontSize(): void {
    const button = document.getElementById('aca-font-size-toggle');
    if (!button) return;
    
    const isActive = button.getAttribute('aria-pressed') === 'true';
    
    if (isActive) {
        document.body.classList.remove('aca-large-font');
        button.setAttribute('aria-pressed', 'false');
        screenReaderAnnouncer.announce('Large font size disabled');
    } else {
        document.body.classList.add('aca-large-font');
        button.setAttribute('aria-pressed', 'true');
        screenReaderAnnouncer.announce('Large font size enabled');
    }
}