import { Component, ErrorInfo, ReactNode } from 'react';

interface Props {
  children: ReactNode;
  fallback?: ReactNode;
}

interface State {
  hasError: boolean;
  error: Error | null;
  errorInfo: ErrorInfo | null;
}

class ErrorBoundary extends Component<Props, State> {
  constructor(props: Props) {
    super(props);
    this.state = {
      hasError: false,
      error: null,
      errorInfo: null
    };
  }

  static getDerivedStateFromError(error: Error): State {
    // Update state so the next render will show the fallback UI
    return {
      hasError: true,
      error,
      errorInfo: null
    };
  }

  componentDidCatch(error: Error, errorInfo: ErrorInfo) {
    // Enhanced error logging - MEDIUM PRIORITY FIX
    console.error('AI Content Agent Error Boundary caught an error:', error, errorInfo);
    
    // Update state with error info
    this.setState({
      error,
      errorInfo
    });
    
    // Enhanced WordPress error logging
    if (window.acaData) {
      // Send comprehensive error data to WordPress
      fetch(`${window.acaData.api_url}debug/error`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-WP-Nonce': window.acaData.nonce
        },
        body: JSON.stringify({
          error: error.message,
          stack: error.stack,
          componentStack: errorInfo.componentStack,
          // Enhanced error context
          timestamp: new Date().toISOString(),
          userAgent: navigator.userAgent,
          url: window.location.href,
          viewport: `${window.innerWidth}x${window.innerHeight}`,
          errorBoundary: true,
          severity: 'critical',
          // React-specific context
          reactVersion: React.version || 'unknown',
          componentName: this.constructor.name
        })
      }).catch(e => {
        console.error('Failed to log error to WordPress:', e);
      });
    }

    this.setState({
      error,
      errorInfo
    });
  }

  render() {
    if (this.state.hasError) {
      // Custom fallback UI
      if (this.props.fallback) {
        return this.props.fallback;
      }

      // Enhanced default error UI - MEDIUM PRIORITY FIX
      return (
        <div className="aca-error-boundary" style={{
          padding: '24px',
          margin: '20px',
          border: '2px solid #dc3545',
          borderRadius: '12px',
          backgroundColor: '#f8d7da',
          color: '#721c24',
          fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif'
        }}>
          <div style={{ display: 'flex', alignItems: 'center', marginBottom: '16px' }}>
            <span style={{ fontSize: '24px', marginRight: '12px' }}>🚨</span>
            <h2 style={{ margin: 0, color: '#721c24', fontSize: '20px', fontWeight: '600' }}>
              Something went wrong
            </h2>
          </div>
          
          <p style={{ marginBottom: '16px', lineHeight: '1.5' }}>
            The AI Content Agent encountered an unexpected error. The error has been automatically logged for investigation.
          </p>
          
          <div style={{ 
            backgroundColor: '#fff3cd', 
            border: '1px solid #ffeaa7', 
            borderRadius: '6px', 
            padding: '12px', 
            marginBottom: '16px',
            fontSize: '14px'
          }}>
            <strong>Error ID:</strong> ACA-{Date.now()}<br/>
            <strong>Time:</strong> {new Date().toLocaleString()}<br/>
            <strong>Component:</strong> {this.state.error?.name || 'Unknown'}
          </div>
          
          {process.env.NODE_ENV === 'development' && this.state.error && (
            <details style={{ marginTop: '10px' }}>
              <summary style={{ cursor: 'pointer', fontWeight: 'bold' }}>
                Error Details (Development Mode)
              </summary>
              <pre style={{
                backgroundColor: '#f1f1f1',
                padding: '10px',
                borderRadius: '4px',
                overflow: 'auto',
                fontSize: '12px',
                marginTop: '10px'
              }}>
                {this.state.error.toString()}
                {this.state.errorInfo?.componentStack}
              </pre>
            </details>
          )}
          
          <div style={{ marginTop: '15px' }}>
            <button
              onClick={() => window.location.reload()}
              style={{
                backgroundColor: '#007cba',
                color: 'white',
                border: 'none',
                padding: '8px 16px',
                borderRadius: '4px',
                cursor: 'pointer',
                marginRight: '10px'
              }}
            >
              Refresh Page
            </button>
            <button
              onClick={() => this.setState({ hasError: false, error: null, errorInfo: null })}
              style={{
                backgroundColor: '#6c757d',
                color: 'white',
                border: 'none',
                padding: '8px 16px',
                borderRadius: '4px',
                cursor: 'pointer'
              }}
            >
              Try Again
            </button>
          </div>
        </div>
      );
    }

    return this.props.children;
  }
}

export default ErrorBoundary;