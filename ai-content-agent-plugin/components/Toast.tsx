
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
    const iconProps = { style: { width: '20px', height: '20px', marginRight: '10px' } };
    
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
    useEffect(() => {
        const timer = setTimeout(() => {
            onDismiss(id);
        }, 5000);

        return () => clearTimeout(timer);
    }, [id, onDismiss]);

    return (
        <div className={`aca-toast ${type}`}>
            <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}>
                <div style={{ display: 'flex', alignItems: 'center' }}>
                    <ToastIcon type={type} />
                    <span>{message}</span>
                </div>
                <button
                    onClick={() => onDismiss(id)}
                    style={{
                        background: 'none',
                        border: 'none',
                        color: 'inherit',
                        cursor: 'pointer',
                        padding: '2px',
                        marginLeft: '10px',
                        opacity: 0.7
                    }}
                    onMouseEnter={(e) => e.currentTarget.style.opacity = '1'}
                    onMouseLeave={(e) => e.currentTarget.style.opacity = '0.7'}
                    aria-label="Dismiss notification"
                >
                    <X style={{ width: '16px', height: '16px' }} />
                </button>
            </div>
        </div>
    );
};
