/**
 * Loading Manager for AI Content Agent
 * 
 * Provides consistent loading states, spinners, and skeleton screens
 * across all components for a unified user experience.
 * 
 * @package AI_Content_Agent
 * @version 2.3.8
 * @since 2.3.8
 */

import React from 'react';

/**
 * Loading state types
 */
export enum LoadingType {
    SPINNER = 'spinner',
    SKELETON = 'skeleton',
    PROGRESS = 'progress',
    DOTS = 'dots',
    PULSE = 'pulse'
}

/**
 * Loading sizes
 */
export enum LoadingSize {
    SMALL = 'small',
    MEDIUM = 'medium',
    LARGE = 'large'
}

/**
 * Loading props interface
 */
interface LoadingProps {
    type?: LoadingType;
    size?: LoadingSize;
    message?: string;
    progress?: number;
    overlay?: boolean;
    color?: string;
    className?: string;
    style?: React.CSSProperties;
}

/**
 * Spinner component - MEDIUM PRIORITY FIX
 */
const Spinner: React.FC<LoadingProps> = ({ 
    size = LoadingSize.MEDIUM, 
    color = '#0073aa', 
    className = '', 
    style = {} 
}) => {
    const sizeMap = {
        [LoadingSize.SMALL]: '16px',
        [LoadingSize.MEDIUM]: '24px',
        [LoadingSize.LARGE]: '32px'
    };

    return (
        <div 
            className={`aca-spinner ${className}`}
            style={{
                width: sizeMap[size],
                height: sizeMap[size],
                border: `2px solid #f3f3f3`,
                borderTop: `2px solid ${color}`,
                borderRadius: '50%',
                animation: 'aca-spin 1s linear infinite',
                ...style
            }}
        >
            <style>{`
                @keyframes aca-spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
            `}</style>
        </div>
    );
};

/**
 * Dots loader component
 */
const DotsLoader: React.FC<LoadingProps> = ({ 
    size = LoadingSize.MEDIUM, 
    color = '#0073aa', 
    className = '', 
    style = {} 
}) => {
    const sizeMap = {
        [LoadingSize.SMALL]: '4px',
        [LoadingSize.MEDIUM]: '6px',
        [LoadingSize.LARGE]: '8px'
    };

    const dotSize = sizeMap[size];

    return (
        <div 
            className={`aca-dots-loader ${className}`}
            style={{
                display: 'flex',
                alignItems: 'center',
                gap: '4px',
                ...style
            }}
        >
            {[0, 1, 2].map(i => (
                <div
                    key={i}
                    style={{
                        width: dotSize,
                        height: dotSize,
                        backgroundColor: color,
                        borderRadius: '50%',
                        animation: `aca-dots-bounce 1.4s ease-in-out ${i * 0.16}s infinite both`
                    }}
                />
            ))}
            <style>{`
                @keyframes aca-dots-bounce {
                    0%, 80%, 100% { 
                        transform: scale(0);
                        opacity: 0.5;
                    } 
                    40% { 
                        transform: scale(1);
                        opacity: 1;
                    }
                }
            `}</style>
        </div>
    );
};

/**
 * Progress bar component
 */
const ProgressBar: React.FC<LoadingProps> = ({ 
    progress = 0, 
    size = LoadingSize.MEDIUM,
    color = '#0073aa', 
    className = '', 
    style = {} 
}) => {
    const heightMap = {
        [LoadingSize.SMALL]: '4px',
        [LoadingSize.MEDIUM]: '6px',
        [LoadingSize.LARGE]: '8px'
    };

    return (
        <div 
            className={`aca-progress-bar ${className}`}
            style={{
                width: '100%',
                height: heightMap[size],
                backgroundColor: '#f0f0f0',
                borderRadius: '3px',
                overflow: 'hidden',
                ...style
            }}
        >
            <div
                style={{
                    width: `${Math.max(0, Math.min(100, progress))}%`,
                    height: '100%',
                    backgroundColor: color,
                    borderRadius: '3px',
                    transition: 'width 0.3s ease',
                    background: `linear-gradient(90deg, ${color}, ${color}dd)`
                }}
            />
        </div>
    );
};

/**
 * Pulse loader component
 */
const PulseLoader: React.FC<LoadingProps> = ({ 
    size = LoadingSize.MEDIUM, 
    color = '#0073aa', 
    className = '', 
    style = {} 
}) => {
    const sizeMap = {
        [LoadingSize.SMALL]: '20px',
        [LoadingSize.MEDIUM]: '30px',
        [LoadingSize.LARGE]: '40px'
    };

    return (
        <div 
            className={`aca-pulse-loader ${className}`}
            style={{
                width: sizeMap[size],
                height: sizeMap[size],
                backgroundColor: color,
                borderRadius: '50%',
                animation: 'aca-pulse 1.5s ease-in-out infinite',
                ...style
            }}
        >
            <style>{`
                @keyframes aca-pulse {
                    0% {
                        transform: scale(0);
                        opacity: 1;
                    }
                    100% {
                        transform: scale(1);
                        opacity: 0;
                    }
                }
            `}</style>
        </div>
    );
};

/**
 * Skeleton loader component
 */
const SkeletonLoader: React.FC<LoadingProps & { 
    lines?: number; 
    height?: string; 
    width?: string 
}> = ({ 
    lines = 1, 
    height = '20px', 
    width = '100%', 
    className = '', 
    style = {} 
}) => {
    return (
        <div className={`aca-skeleton-loader ${className}`} style={style}>
            {Array.from({ length: lines }, (_, i) => (
                <div
                    key={i}
                    style={{
                        width: i === lines - 1 ? '80%' : width,
                        height,
                        backgroundColor: '#f0f0f0',
                        borderRadius: '4px',
                        marginBottom: i < lines - 1 ? '8px' : '0',
                        animation: 'aca-skeleton-loading 1.5s ease-in-out infinite alternate'
                    }}
                />
            ))}
            <style>{`
                @keyframes aca-skeleton-loading {
                    0% {
                        background-color: #f0f0f0;
                    }
                    100% {
                        background-color: #e0e0e0;
                    }
                }
            `}</style>
        </div>
    );
};

/**
 * Main Loading component - MEDIUM PRIORITY FIX
 */
export const Loading: React.FC<LoadingProps & { 
    lines?: number; 
    height?: string; 
    width?: string 
}> = ({ 
    type = LoadingType.SPINNER, 
    overlay = false, 
    message, 
    ...props 
}) => {
    const renderLoader = () => {
        switch (type) {
            case LoadingType.SPINNER:
                return <Spinner {...props} />;
            case LoadingType.DOTS:
                return <DotsLoader {...props} />;
            case LoadingType.PROGRESS:
                return <ProgressBar {...props} />;
            case LoadingType.PULSE:
                return <PulseLoader {...props} />;
            case LoadingType.SKELETON:
                return <SkeletonLoader {...props} />;
            default:
                return <Spinner {...props} />;
        }
    };

    const content = (
        <div 
            className="aca-loading-content"
            style={{
                display: 'flex',
                flexDirection: 'column',
                alignItems: 'center',
                gap: '12px'
            }}
        >
            {renderLoader()}
            {message && (
                <div 
                    style={{
                        fontSize: '14px',
                        color: '#666',
                        textAlign: 'center',
                        fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif'
                    }}
                >
                    {message}
                </div>
            )}
        </div>
    );

    if (overlay) {
        return (
            <div 
                className="aca-loading-overlay"
                style={{
                    position: 'fixed',
                    top: 0,
                    left: 0,
                    right: 0,
                    bottom: 0,
                    backgroundColor: 'rgba(255, 255, 255, 0.8)',
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                    zIndex: 9999,
                    backdropFilter: 'blur(2px)'
                }}
            >
                {content}
            </div>
        );
    }

    return content;
};

/**
 * Loading button component
 */
export const LoadingButton: React.FC<{
    loading: boolean;
    children: React.ReactNode;
    onClick?: () => void;
    disabled?: boolean;
    type?: 'button' | 'submit';
    variant?: 'primary' | 'secondary';
    size?: LoadingSize;
    className?: string;
    style?: React.CSSProperties;
}> = ({ 
    loading, 
    children, 
    onClick, 
    disabled = false, 
    type = 'button',
    variant = 'primary',
    size = LoadingSize.MEDIUM,
    className = '',
    style = {}
}) => {
    const baseStyles: React.CSSProperties = {
        position: 'relative',
        display: 'inline-flex',
        alignItems: 'center',
        justifyContent: 'center',
        gap: '8px',
        padding: size === LoadingSize.SMALL ? '6px 12px' : 
                size === LoadingSize.LARGE ? '12px 24px' : '8px 16px',
        fontSize: size === LoadingSize.SMALL ? '12px' : 
                  size === LoadingSize.LARGE ? '16px' : '14px',
        fontWeight: '500',
        border: 'none',
        borderRadius: '6px',
        cursor: loading || disabled ? 'not-allowed' : 'pointer',
        transition: 'all 0.2s ease',
        fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
        opacity: loading || disabled ? 0.7 : 1,
        backgroundColor: variant === 'primary' ? '#0073aa' : '#f0f0f0',
        color: variant === 'primary' ? 'white' : '#333',
        ...style
    };

    return (
        <button
            type={type}
            onClick={onClick}
            disabled={loading || disabled}
            className={`aca-loading-button ${className}`}
            style={baseStyles}
        >
            {loading && (
                <Spinner 
                    size={LoadingSize.SMALL} 
                    color={variant === 'primary' ? 'white' : '#0073aa'} 
                />
            )}
            <span style={{ opacity: loading ? 0.7 : 1 }}>
                {children}
            </span>
        </button>
    );
};

/**
 * Loading state hook for components
 */
export const useLoadingState = (initialState: Record<string, boolean> = {}) => {
    const [loadingStates, setLoadingStates] = React.useState(initialState);

    const setLoading = React.useCallback((key: string, loading: boolean) => {
        setLoadingStates(prev => ({ ...prev, [key]: loading }));
    }, []);

    const isLoading = React.useCallback((key: string) => {
        return loadingStates[key] || false;
    }, [loadingStates]);

    const isAnyLoading = React.useCallback(() => {
        return Object.values(loadingStates).some(Boolean);
    }, [loadingStates]);

    const clearAll = React.useCallback(() => {
        setLoadingStates({});
    }, []);

    return {
        loadingStates,
        setLoading,
        isLoading,
        isAnyLoading,
        clearAll
    };
};

/**
 * Loading wrapper component for async operations
 */
export const LoadingWrapper: React.FC<{
    loading: boolean;
    children: React.ReactNode;
    fallback?: React.ReactNode;
    type?: LoadingType;
    message?: string;
}> = ({ loading, children, fallback, type = LoadingType.SPINNER, message }) => {
    if (loading) {
        return fallback || <Loading type={type} message={message} />;
    }
    
    return <>{children}</>;
};

export default Loading;