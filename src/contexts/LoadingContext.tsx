import React, { createContext, useContext, useState, useCallback, ReactNode } from 'react';

export interface LoadingState {
    [key: string]: boolean;
}

interface LoadingContextType {
    loadingStates: LoadingState;
    setLoading: (key: string, isLoading: boolean) => void;
    isLoading: (key: string) => boolean;
    isAnyLoading: () => boolean;
    clearAllLoading: () => void;
}

const LoadingContext = createContext<LoadingContextType | undefined>(undefined);

export const useLoading = () => {
    const context = useContext(LoadingContext);
    if (context === undefined) {
        throw new Error('useLoading must be used within a LoadingProvider');
    }
    return context;
};

interface LoadingProviderProps {
    children: ReactNode;
}

export const LoadingProvider: React.FC<LoadingProviderProps> = ({ children }) => {
    const [loadingStates, setLoadingStates] = useState<LoadingState>({});

    const setLoading = useCallback((key: string, isLoading: boolean) => {
        setLoadingStates(prev => ({
            ...prev,
            [key]: isLoading
        }));
    }, []);

    const isLoading = useCallback((key: string) => {
        return loadingStates[key] || false;
    }, [loadingStates]);

    const isAnyLoading = useCallback(() => {
        return Object.values(loadingStates).some(loading => loading);
    }, [loadingStates]);

    const clearAllLoading = useCallback(() => {
        setLoadingStates({});
    }, []);

    const value: LoadingContextType = {
        loadingStates,
        setLoading,
        isLoading,
        isAnyLoading,
        clearAllLoading
    };

    return (
        <LoadingContext.Provider value={value}>
            {children}
        </LoadingContext.Provider>
    );
};

// Hook for easier loading management with automatic cleanup
export const useLoadingState = (key: string) => {
    const { setLoading, isLoading } = useLoading();
    
    const startLoading = useCallback(() => {
        setLoading(key, true);
    }, [key, setLoading]);
    
    const stopLoading = useCallback(() => {
        setLoading(key, false);
    }, [key, setLoading]);
    
    const withLoading = useCallback(async (fn: () => Promise<any>) => {
        startLoading();
        try {
            const result = await fn();
            return result;
        } finally {
            stopLoading();
        }
    }, [startLoading, stopLoading]);
    
    return {
        isLoading: isLoading(key),
        startLoading,
        stopLoading,
        withLoading
    };
};

export default LoadingContext;