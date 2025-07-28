import React, { Component, ErrorInfo, ReactNode } from 'react';
import { AlertTriangle, RefreshCw } from './Icons';

interface Props {
    children: ReactNode;
    fallback?: ReactNode;
}

interface State {
    hasError: boolean;
    error: Error | null;
    errorInfo: ErrorInfo | null;
    retryCount: number;
}

export class ErrorBoundary extends Component<Props, State> {
    private maxRetries = 3;

    constructor(props: Props) {
        super(props);
        this.state = {
            hasError: false,
            error: null,
            errorInfo: null,
            retryCount: 0
        };
    }

    static getDerivedStateFromError(error: Error): Partial<State> {
        return {
            hasError: true,
            error
        };
    }

    componentDidCatch(error: Error, errorInfo: ErrorInfo) {
        this.setState({
            error,
            errorInfo
        });

        // Log error to console in development
        if (process.env.NODE_ENV === 'development') {
            console.error('ErrorBoundary caught an error:', error, errorInfo);
        }

        // In production, you might want to send this to an error reporting service
        // Example: errorReportingService.captureException(error, { extra: errorInfo });
    }

    handleRetry = () => {
        if (this.state.retryCount < this.maxRetries) {
            this.setState(prevState => ({
                hasError: false,
                error: null,
                errorInfo: null,
                retryCount: prevState.retryCount + 1
            }));
        }
    };

    handleReload = () => {
        window.location.reload();
    };

    render() {
        if (this.state.hasError) {
            // Custom fallback UI
            if (this.props.fallback) {
                return this.props.fallback;
            }

            // Default fallback UI
            return (
                <div className="min-h-screen bg-slate-950 flex items-center justify-center p-4">
                    <div className="bg-slate-800 border border-slate-700 rounded-lg p-8 max-w-md w-full">
                        <div className="flex items-center mb-6">
                            <div className="bg-red-900/30 p-3 rounded-lg mr-4">
                                <AlertTriangle className="h-8 w-8 text-red-400" />
                            </div>
                            <div>
                                <h2 className="text-xl font-bold text-white">Something went wrong</h2>
                                <p className="text-slate-400 text-sm">An unexpected error occurred</p>
                            </div>
                        </div>

                        {process.env.NODE_ENV === 'development' && this.state.error && (
                            <div className="bg-slate-900 border border-slate-700 rounded p-4 mb-6">
                                <h3 className="text-sm font-semibold text-red-400 mb-2">Error Details:</h3>
                                <p className="text-xs text-slate-300 font-mono break-all">
                                    {this.state.error.message}
                                </p>
                                {this.state.errorInfo && (
                                    <details className="mt-2">
                                        <summary className="text-xs text-slate-400 cursor-pointer hover:text-slate-300">
                                            Stack Trace
                                        </summary>
                                        <pre className="text-xs text-slate-400 mt-2 whitespace-pre-wrap">
                                            {this.state.errorInfo.componentStack}
                                        </pre>
                                    </details>
                                )}
                            </div>
                        )}

                        <div className="space-y-3">
                            {this.state.retryCount < this.maxRetries && (
                                <button
                                    onClick={this.handleRetry}
                                    className="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-4 rounded-lg transition-colors flex items-center justify-center"
                                >
                                    <RefreshCw className="h-4 w-4 mr-2" />
                                    Try Again ({this.maxRetries - this.state.retryCount} attempts left)
                                </button>
                            )}
                            
                            <button
                                onClick={this.handleReload}
                                className="w-full bg-slate-700 hover:bg-slate-600 text-white font-semibold py-2.5 px-4 rounded-lg transition-colors"
                            >
                                Reload Page
                            </button>

                            <div className="text-center pt-2">
                                <p className="text-xs text-slate-500">
                                    If this problem persists, please contact support
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            );
        }

        return this.props.children;
    }
}

// Higher-order component for easier usage
export const withErrorBoundary = <P extends object>(
    Component: React.ComponentType<P>,
    fallback?: ReactNode
) => {
    const WrappedComponent = (props: P) => (
        <ErrorBoundary fallback={fallback}>
            <Component {...props} />
        </ErrorBoundary>
    );
    
    WrappedComponent.displayName = `withErrorBoundary(${Component.displayName || Component.name})`;
    
    return WrappedComponent;
};

export default ErrorBoundary;