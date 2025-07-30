
import React, { useEffect } from 'react';
import { CheckCircle, XCircle, AlertTriangle, Info, X } from './Icons';

export interface ToastData {
    id: number;
    message: string;
    type: 'success' | 'error' | 'warning' | 'info';
}

interface ToastProps extends ToastData {
    onDismiss: (id: number) => void;
}

const ToastIcon: React.FC<{ type: ToastData['type'] }> = ({ type }) => {
    const iconProps = { style: { width: '16px', height: '16px', color: 'white' } };
    
    switch (type) {
        case 'success':
            return <CheckCircle {...iconProps} />;
        case 'error':
            return <XCircle {...iconProps} />;
        case 'warning':
            return <AlertTriangle {...iconProps} />;
        case 'info':
            return <Info {...iconProps} />;
        default:
            return <Info {...iconProps} />;
    }
};

export const Toast: React.FC<ToastProps> = ({ id, message, type, onDismiss }) => {
    const [isVisible, setIsVisible] = React.useState(false);
    const [isExiting, setIsExiting] = React.useState(false);

    useEffect(() => {
        // Animate in
        setTimeout(() => setIsVisible(true), 50);
        
        const timer = setTimeout(() => {
            handleDismiss();
        }, 5000);

        return () => clearTimeout(timer);
    }, [id]);

    const handleDismiss = () => {
        setIsExiting(true);
        setTimeout(() => onDismiss(id), 300);
    };

    const getToastColors = () => {
        switch (type) {
            case 'success':
                return {
                    bg: 'linear-gradient(135deg, #10b981 0%, #059669 100%)',
                    border: '#10b981',
                    shadow: 'rgba(16, 185, 129, 0.3)'
                };
            case 'error':
                return {
                    bg: 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)',
                    border: '#ef4444',
                    shadow: 'rgba(239, 68, 68, 0.3)'
                };
            case 'warning':
                return {
                    bg: 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)',
                    border: '#f59e0b',
                    shadow: 'rgba(245, 158, 11, 0.3)'
                };
            case 'info':
                return {
                    bg: 'linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)',
                    border: '#3b82f6',
                    shadow: 'rgba(59, 130, 246, 0.3)'
                };
            default:
                return {
                    bg: 'linear-gradient(135deg, #6b7280 0%, #4b5563 100%)',
                    border: '#6b7280',
                    shadow: 'rgba(107, 114, 128, 0.3)'
                };
        }
    };

    const colors = getToastColors();

    return (
        <div 
            style={{
                background: colors.bg,
                color: 'white',
                padding: '16px 20px',
                borderRadius: '12px',
                boxShadow: `0 8px 25px ${colors.shadow}, 0 4px 10px rgba(0, 0, 0, 0.1)`,
                border: `1px solid ${colors.border}`,
                backdropFilter: 'blur(10px)',
                transform: isExiting 
                    ? 'translateX(100%) scale(0.9)' 
                    : isVisible 
                        ? 'translateX(0) scale(1)' 
                        : 'translateX(100%) scale(0.9)',
                opacity: isExiting ? 0 : isVisible ? 1 : 0,
                transition: 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)',
                minWidth: '300px',
                maxWidth: '500px',
                position: 'relative',
                overflow: 'hidden'
            }}
        >
            {/* Progress bar */}
            <div
                style={{
                    position: 'absolute',
                    bottom: 0,
                    left: 0,
                    height: '3px',
                    background: 'rgba(255, 255, 255, 0.3)',
                    animation: 'aca-toast-progress 5s linear forwards',
                    transformOrigin: 'left'
                }}
            />
            
            <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}>
                <div style={{ display: 'flex', alignItems: 'center', flex: 1 }}>
                    <div style={{ 
                        display: 'flex', 
                        alignItems: 'center', 
                        justifyContent: 'center',
                        width: '24px',
                        height: '24px',
                        background: 'rgba(255, 255, 255, 0.2)',
                        borderRadius: '6px',
                        marginRight: '12px',
                        flexShrink: 0
                    }}>
                        <ToastIcon type={type} />
                    </div>
                    <span style={{ 
                        fontSize: '14px', 
                        fontWeight: '500',
                        lineHeight: '1.4',
                        wordBreak: 'break-word'
                    }}>
                        {message}
                    </span>
                </div>
                <button
                    onClick={handleDismiss}
                    style={{
                        background: 'rgba(255, 255, 255, 0.2)',
                        border: 'none',
                        color: 'white',
                        cursor: 'pointer',
                        padding: '6px',
                        marginLeft: '12px',
                        borderRadius: '6px',
                        display: 'flex',
                        alignItems: 'center',
                        justifyContent: 'center',
                        transition: 'all 0.2s ease',
                        flexShrink: 0
                    }}
                    onMouseEnter={(e) => {
                        e.currentTarget.style.background = 'rgba(255, 255, 255, 0.3)';
                        e.currentTarget.style.transform = 'scale(1.1)';
                    }}
                    onMouseLeave={(e) => {
                        e.currentTarget.style.background = 'rgba(255, 255, 255, 0.2)';
                        e.currentTarget.style.transform = 'scale(1)';
                    }}
                    aria-label="Dismiss notification"
                >
                    <X style={{ width: '14px', height: '14px' }} />
                </button>
            </div>
        </div>
    );
};
