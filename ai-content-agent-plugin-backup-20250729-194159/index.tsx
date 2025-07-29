
import React from 'react';
import ReactDOM from 'react-dom/client';
import App from './App';
import './index.css';

// Error boundary component
class ErrorBoundary extends React.Component<
  { children: React.ReactNode },
  { hasError: boolean; error?: Error }
> {
  constructor(props: { children: React.ReactNode }) {
    super(props);
    this.state = { hasError: false };
  }

  static getDerivedStateFromError(error: Error) {
    return { hasError: true, error };
  }

  componentDidCatch(error: Error, errorInfo: React.ErrorInfo) {
    console.error('ACA Plugin Error:', error, errorInfo);
  }

  render() {
    if (this.state.hasError) {
      return (
        <div style={{ 
          padding: '20px', 
          background: '#ffebee', 
          border: '1px solid #f44336', 
          borderRadius: '4px',
          margin: '20px'
        }}>
          <h2 style={{ color: '#d32f2f', margin: '0 0 10px 0' }}>
            AI Content Agent - Loading Error
          </h2>
          <p style={{ margin: '0 0 10px 0' }}>
            The plugin interface failed to load. This might be a browser cache issue.
          </p>
          <p style={{ margin: '0 0 10px 0', fontSize: '12px', color: '#666' }}>
            Error: {this.state.error?.message || 'Unknown error'}
          </p>
          <button 
            onClick={() => window.location.reload()} 
            style={{ 
              padding: '8px 16px', 
              background: '#1976d2', 
              color: 'white', 
              border: 'none', 
              borderRadius: '4px',
              cursor: 'pointer'
            }}
          >
            Reload Page
          </button>
        </div>
      );
    }

    return this.props.children;
  }
}

const rootElement = document.getElementById('root');
if (!rootElement) {
  throw new Error("Could not find root element to mount to");
}

const root = ReactDOM.createRoot(rootElement);
root.render(
  <ErrorBoundary>
    <App />
  </ErrorBoundary>
);
