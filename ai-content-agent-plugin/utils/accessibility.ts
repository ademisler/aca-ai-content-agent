// Accessibility utilities for WCAG 2.1 AA compliance

/**
 * Focus management utilities
 */
export const focusUtils = {
  /**
   * Trap focus within a container (for modals, dropdowns)
   */
  trapFocus: (container: HTMLElement) => {
    const focusableElements = container.querySelectorAll(
      'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
    );
    const firstElement = focusableElements[0] as HTMLElement;
    const lastElement = focusableElements[focusableElements.length - 1] as HTMLElement;

    const handleTabKey = (e: KeyboardEvent) => {
      if (e.key !== 'Tab') return;

      if (e.shiftKey) {
        if (document.activeElement === firstElement) {
          e.preventDefault();
          lastElement.focus();
        }
      } else {
        if (document.activeElement === lastElement) {
          e.preventDefault();
          firstElement.focus();
        }
      }
    };

    container.addEventListener('keydown', handleTabKey);
    return () => container.removeEventListener('keydown', handleTabKey);
  },

  /**
   * Focus the first focusable element in a container
   */
  focusFirst: (container: HTMLElement) => {
    const focusable = container.querySelector(
      'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
    ) as HTMLElement;
    focusable?.focus();
  },

  /**
   * Return focus to the previously focused element
   */
  returnFocus: (previousElement: HTMLElement | null) => {
    if (previousElement && document.contains(previousElement)) {
      previousElement.focus();
    }
  },
};

/**
 * Screen reader announcements
 */
export const announceToScreenReader = (message: string, priority: 'polite' | 'assertive' = 'polite') => {
  const announcement = document.createElement('div');
  announcement.setAttribute('aria-live', priority);
  announcement.setAttribute('aria-atomic', 'true');
  announcement.className = 'sr-only';
  announcement.textContent = message;
  
  document.body.appendChild(announcement);
  
  // Remove after announcement
  setTimeout(() => {
    if (document.body.contains(announcement)) {
      document.body.removeChild(announcement);
    }
  }, 1000);
};

/**
 * Keyboard navigation helpers
 */
export const keyboardUtils = {
  /**
   * Handle arrow key navigation in lists
   */
  handleArrowNavigation: (
    event: KeyboardEvent,
    items: HTMLElement[],
    currentIndex: number,
    onSelect?: (index: number) => void
  ) => {
    let newIndex = currentIndex;

    switch (event.key) {
      case 'ArrowDown':
        event.preventDefault();
        newIndex = currentIndex < items.length - 1 ? currentIndex + 1 : 0;
        break;
      case 'ArrowUp':
        event.preventDefault();
        newIndex = currentIndex > 0 ? currentIndex - 1 : items.length - 1;
        break;
      case 'Home':
        event.preventDefault();
        newIndex = 0;
        break;
      case 'End':
        event.preventDefault();
        newIndex = items.length - 1;
        break;
      case 'Enter':
      case ' ':
        event.preventDefault();
        onSelect?.(currentIndex);
        return currentIndex;
    }

    if (newIndex !== currentIndex) {
      items[newIndex]?.focus();
      return newIndex;
    }

    return currentIndex;
  },

  /**
   * Handle escape key to close modals/dropdowns
   */
  handleEscape: (callback: () => void) => {
    const handleKeyDown = (event: KeyboardEvent) => {
      if (event.key === 'Escape') {
        callback();
      }
    };

    document.addEventListener('keydown', handleKeyDown);
    return () => document.removeEventListener('keydown', handleKeyDown);
  },
};

/**
 * Color contrast utilities
 */
export const colorUtils = {
  /**
   * Check if color contrast meets WCAG AA standards (4.5:1 for normal text)
   */
  meetsContrastRequirement: (foreground: string, background: string): boolean => {
    const ratio = getContrastRatio(foreground, background);
    return ratio >= 4.5;
  },

  /**
   * Get high contrast version of a color
   */
  getHighContrastColor: (color: string, isDark: boolean = false): string => {
    // Simple high contrast implementation
    return isDark ? '#ffffff' : '#000000';
  },
};

/**
 * Calculate contrast ratio between two colors
 */
function getContrastRatio(color1: string, color2: string): number {
  const l1 = getLuminance(color1);
  const l2 = getLuminance(color2);
  const lighter = Math.max(l1, l2);
  const darker = Math.min(l1, l2);
  return (lighter + 0.05) / (darker + 0.05);
}

/**
 * Get relative luminance of a color
 */
function getLuminance(color: string): number {
  // Simplified luminance calculation
  // In a real implementation, you'd parse the color properly
  const rgb = hexToRgb(color);
  if (!rgb) return 0;

  const { r, g, b } = rgb;
  const [rs, gs, bs] = [r, g, b].map(c => {
    c = c / 255;
    return c <= 0.03928 ? c / 12.92 : Math.pow((c + 0.055) / 1.055, 2.4);
  });

  return 0.2126 * rs + 0.7152 * gs + 0.0722 * bs;
}

/**
 * Convert hex color to RGB
 */
function hexToRgb(hex: string): { r: number; g: number; b: number } | null {
  const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
  return result ? {
    r: parseInt(result[1], 16),
    g: parseInt(result[2], 16),
    b: parseInt(result[3], 16)
  } : null;
}

/**
 * ARIA utilities
 */
export const ariaUtils = {
  /**
   * Generate unique IDs for ARIA relationships
   */
  generateId: (prefix: string = 'aca'): string => {
    return `${prefix}-${Math.random().toString(36).substr(2, 9)}`;
  },

  /**
   * Set ARIA expanded state
   */
  setExpanded: (element: HTMLElement, expanded: boolean) => {
    element.setAttribute('aria-expanded', expanded.toString());
  },

  /**
   * Set ARIA selected state
   */
  setSelected: (element: HTMLElement, selected: boolean) => {
    element.setAttribute('aria-selected', selected.toString());
  },

  /**
   * Set ARIA pressed state for toggle buttons
   */
  setPressed: (element: HTMLElement, pressed: boolean) => {
    element.setAttribute('aria-pressed', pressed.toString());
  },
};

/**
 * High contrast mode detection and utilities
 */
export const highContrastUtils = {
  /**
   * Detect if user prefers high contrast
   */
  prefersHighContrast: (): boolean => {
    return window.matchMedia('(prefers-contrast: high)').matches;
  },

  /**
   * Detect if user prefers reduced motion
   */
  prefersReducedMotion: (): boolean => {
    return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  },

  /**
   * Apply high contrast styles
   */
  applyHighContrastStyles: () => {
    document.documentElement.classList.add('high-contrast');
  },

  /**
   * Remove high contrast styles
   */
  removeHighContrastStyles: () => {
    document.documentElement.classList.remove('high-contrast');
  },
};

/**
 * Skip link utility for keyboard navigation
 */
export const createSkipLink = (target: string, text: string = 'Skip to main content') => {
  const skipLink = document.createElement('a');
  skipLink.href = `#${target}`;
  skipLink.textContent = text;
  skipLink.className = 'skip-link sr-only focus:not-sr-only focus:absolute focus:top-0 focus:left-0 focus:z-50 focus:p-2 focus:bg-blue-600 focus:text-white';
  
  skipLink.addEventListener('click', (e) => {
    e.preventDefault();
    const targetElement = document.getElementById(target);
    if (targetElement) {
      targetElement.focus();
      targetElement.scrollIntoView();
    }
  });

  return skipLink;
};