
import React, { useEffect, useState } from 'react';
import { CheckCircle, AlertTriangle, Info, X } from './Icons';

export interface ToastData {
    id: number;
    message: string;
    type: 'success' | 'error' | 'warning' | 'info';
}

interface ToastProps extends ToastData {
    onDismiss: (id: number) => void;
}

const toastConfig = {
    success: {
        icon: <CheckCircle className="text-green-400 h-6 w-6" />,
    },
    error: {
        icon: <AlertTriangle className="text-red-400 h-6 w-6" />,
    },
    warning: {
        icon: <AlertTriangle className="text-yellow-400 h-6 w-6" />,
    },
    info: {
        icon: <Info className="text-blue-400 h-6 w-6" />,
    },
};

export const Toast: React.FC<ToastProps> = ({ id, message, type, onDismiss }) => {
    const { icon } = toastConfig[type];
    const [isExiting, setIsExiting] = useState(false);

    useEffect(() => {
        const exitTimer = setTimeout(() => {
            setIsExiting(true);
        }, 4200);

        const removeTimer = setTimeout(() => {
            onDismiss(id);
        }, 5000);

        return () => {
            clearTimeout(exitTimer);
            clearTimeout(removeTimer);
        };
    }, [id, onDismiss]);
    
    const handleDismiss = () => {
        setIsExiting(true);
        setTimeout(() => onDismiss(id), 400);
    }

    return (
        <div
            className={`
                bg-slate-800 border border-slate-700 text-white rounded-lg shadow-2xl flex items-center p-4
                transform transition-all duration-300 ease-in-out
                ${isExiting ? 'opacity-0 translate-y-4' : 'opacity-100 translate-y-0'}
            `}
        >
            <div className="flex-shrink-0 mr-3">{icon}</div>
            <p className="text-sm font-medium flex-grow">{message}</p>
            <button 
                onClick={handleDismiss}
                className="ml-4 p-1 rounded-full text-slate-400 hover:bg-slate-700 hover:text-white transition-colors"
                aria-label="Dismiss notification"
            >
                <X className="h-4 w-4" />
            </button>
        </div>
    );
};
