import React, { useState, useEffect } from 'react';
import { Spinner, AlertTriangle, CheckCircle, Clock, Server, Database, Zap, RefreshCw, Download, Trash2 } from './Icons';
import { getApiHealth } from '../services/geminiService';

interface DebugPanelProps {
    onShowToast: (message: string, type: 'success' | 'error' | 'warning' | 'info') => void;
}

interface SystemStatus {
    php_version: string;
    wordpress_version: string;
    plugin_version: string;
    memory_limit: string;
    memory_usage: string;
    max_execution_time: string;
    wp_cron_enabled: boolean;
    database_status: 'healthy' | 'warning' | 'error';
    api_endpoints_status: 'healthy' | 'warning' | 'error';
}

interface ApiCall {
    id: string;
    timestamp: string;
    endpoint: string;
    method: string;
    duration: number;
    status: number;
    response_size: number;
    error?: string;
}

interface ErrorLog {
    id: string;
    timestamp: string;
    level: 'error' | 'warning' | 'info';
    message: string;
    file?: string;
    line?: number;
    context?: any;
}

interface PerformanceMetric {
    name: string;
    value: number;
    unit: string;
    status: 'good' | 'warning' | 'critical';
    description?: string;
}

export const DebugPanel: React.FC<DebugPanelProps> = ({ onShowToast }) => {
    const [activeTab, setActiveTab] = useState('system');
    const [isLoading, setIsLoading] = useState(false);
    const [systemStatus, setSystemStatus] = useState<SystemStatus | null>(null);
    const [apiCalls, setApiCalls] = useState<ApiCall[]>([]);
    const [errorLogs, setErrorLogs] = useState<ErrorLog[]>([]);
    const [performanceMetrics, setPerformanceMetrics] = useState<PerformanceMetric[]>([]);
    const [autoRefresh, setAutoRefresh] = useState(false);

    const tabs = [
        { id: 'system', title: 'System Status', icon: <Server style={{ width: '16px', height: '16px' }} /> },
        { id: 'api', title: 'API Calls', icon: <Zap style={{ width: '16px', height: '16px' }} /> },
        { id: 'errors', title: 'Error Logs', icon: <AlertTriangle style={{ width: '16px', height: '16px' }} /> },
        { id: 'performance', title: 'Performance', icon: <Database style={{ width: '16px', height: '16px' }} /> }
    ];

    // Fetch system status
    const fetchSystemStatus = async () => {
        try {
            const response = await fetch(`${window.acaData.api_url}debug/system-status`, {
                headers: { 'X-WP-Nonce': window.acaData.nonce }
            });
            
            if (response.ok) {
                const data = await response.json();
                setSystemStatus(data);
            }
        } catch (error) {
            console.error('Failed to fetch system status:', error);
        }
    };

    // Fetch API call logs
    const fetchApiCalls = async () => {
        try {
            const response = await fetch(`${window.acaData.api_url}debug/api-calls`, {
                headers: { 'X-WP-Nonce': window.acaData.nonce }
            });
            
            if (response.ok) {
                const data = await response.json();
                setApiCalls(data.calls || []);
            }
        } catch (error) {
            console.error('Failed to fetch API calls:', error);
        }
    };

    // Fetch error logs
    const fetchErrorLogs = async () => {
        try {
            const response = await fetch(`${window.acaData.api_url}debug/error-logs`, {
                headers: { 'X-WP-Nonce': window.acaData.nonce }
            });
            
            if (response.ok) {
                const data = await response.json();
                setErrorLogs(data.logs || []);
            }
        } catch (error) {
            console.error('Failed to fetch error logs:', error);
        }
    };

    // Fetch performance metrics
    const fetchPerformanceMetrics = async () => {
        try {
            const response = await fetch(`${window.acaData.api_url}debug/performance`, {
                headers: { 'X-WP-Nonce': window.acaData.nonce }
            });
            
            if (response.ok) {
                const data = await response.json();
                setPerformanceMetrics(data.metrics || []);
            }
        } catch (error) {
            console.error('Failed to fetch performance metrics:', error);
        }
    };

    // Clear logs
    const clearLogs = async (type: string) => {
        if (!confirm(`Are you sure you want to clear all ${type}?`)) return;

        try {
            const response = await fetch(`${window.acaData.api_url}debug/clear-logs`, {
                method: 'POST',
                headers: { 
                    'X-WP-Nonce': window.acaData.nonce,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ type })
            });
            
            if (response.ok) {
                onShowToast(`${type} cleared successfully`, 'success');
                if (type === 'api') setApiCalls([]);
                if (type === 'errors') setErrorLogs([]);
            }
        } catch (error) {
            console.error('Failed to clear logs:', error);
            onShowToast('Failed to clear logs', 'error');
        }
    };

    // Export logs
    const exportLogs = async (type: string) => {
        try {
            const response = await fetch(`${window.acaData.api_url}debug/export-logs`, {
                method: 'POST',
                headers: { 
                    'X-WP-Nonce': window.acaData.nonce,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ type })
            });
            
            if (response.ok) {
                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `aca-${type}-logs-${new Date().toISOString().split('T')[0]}.json`;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
                onShowToast('Logs exported successfully', 'success');
            }
        } catch (error) {
            console.error('Failed to export logs:', error);
            onShowToast('Failed to export logs', 'error');
        }
    };

    // Refresh all data
    const refreshAll = async () => {
        setIsLoading(true);
        try {
            await Promise.all([
                fetchSystemStatus(),
                fetchApiCalls(),
                fetchErrorLogs(),
                fetchPerformanceMetrics()
            ]);
        } finally {
            setIsLoading(false);
        }
    };

    // Auto refresh effect
    useEffect(() => {
        let interval: NodeJS.Timeout;
        if (autoRefresh) {
            interval = setInterval(refreshAll, 30000); // Every 30 seconds
        }
        return () => {
            if (interval) clearInterval(interval);
        };
    }, [autoRefresh]);

    // Initial load
    useEffect(() => {
        refreshAll();
    }, []);

    const getStatusColor = (status: string) => {
        switch (status) {
            case 'healthy':
            case 'good':
                return '#28a745';
            case 'warning':
                return '#ffc107';
            case 'error':
            case 'critical':
                return '#dc3545';
            default:
                return '#6c757d';
        }
    };

    const getStatusIcon = (status: string) => {
        switch (status) {
            case 'healthy':
            case 'good':
                return <CheckCircle style={{ width: '16px', height: '16px', color: '#28a745' }} />;
            case 'warning':
                return <AlertTriangle style={{ width: '16px', height: '16px', color: '#ffc107' }} />;
            case 'error':
            case 'critical':
                return <AlertTriangle style={{ width: '16px', height: '16px', color: '#dc3545' }} />;
            default:
                return <Clock style={{ width: '16px', height: '16px', color: '#6c757d' }} />;
        }
    };

    const formatBytes = (bytes: number) => {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    };

    const formatDuration = (ms: number) => {
        if (ms < 1000) return `${ms}ms`;
        return `${(ms / 1000).toFixed(2)}s`;
    };

    return (
        <div className="aca-fade-in">
            {/* Header */}
            <div style={{
                background: 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)',
                borderRadius: '12px',
                padding: '30px',
                marginBottom: '30px',
                color: 'white',
                position: 'relative',
                overflow: 'hidden'
            }}>
                <div style={{ position: 'relative', zIndex: 2 }}>
                    <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}>
                        <div>
                            <h1 style={{ fontSize: '28px', fontWeight: '700', margin: '0 0 8px 0' }}>
                                Debug Panel
                            </h1>
                            <p style={{ fontSize: '16px', opacity: 0.9, margin: 0 }}>
                                System monitoring and debugging tools
                            </p>
                        </div>
                        <div style={{ display: 'flex', gap: '12px', alignItems: 'center' }}>
                            <label style={{ display: 'flex', alignItems: 'center', gap: '8px', fontSize: '14px' }}>
                                <input
                                    type="checkbox"
                                    checked={autoRefresh}
                                    onChange={(e) => setAutoRefresh(e.target.checked)}
                                    style={{ accentColor: 'white' }}
                                />
                                Auto Refresh
                            </label>
                            <button
                                onClick={refreshAll}
                                disabled={isLoading}
                                style={{
                                    background: 'rgba(255,255,255,0.2)',
                                    border: '1px solid rgba(255,255,255,0.3)',
                                    borderRadius: '8px',
                                    padding: '8px 16px',
                                    color: 'white',
                                    cursor: 'pointer',
                                    display: 'flex',
                                    alignItems: 'center',
                                    gap: '8px'
                                }}
                            >
                                {isLoading ? <Spinner style={{ width: '16px', height: '16px' }} /> : <RefreshCw style={{ width: '16px', height: '16px' }} />}
                                Refresh
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {/* Tabs */}
            <div style={{ display: 'flex', borderBottom: '1px solid #e1e5e9', marginBottom: '30px' }}>
                {tabs.map(tab => (
                    <button
                        key={tab.id}
                        onClick={() => setActiveTab(tab.id)}
                        style={{
                            padding: '12px 24px',
                            border: 'none',
                            background: 'none',
                            cursor: 'pointer',
                            display: 'flex',
                            alignItems: 'center',
                            gap: '8px',
                            borderBottom: activeTab === tab.id ? '2px solid #0073aa' : '2px solid transparent',
                            color: activeTab === tab.id ? '#0073aa' : '#666',
                            fontWeight: activeTab === tab.id ? '600' : '400'
                        }}
                    >
                        {tab.icon}
                        {tab.title}
                    </button>
                ))}
            </div>

            {/* Tab Content */}
            {activeTab === 'system' && (
                <div className="aca-card">
                    <h3 style={{ marginBottom: '20px', display: 'flex', alignItems: 'center', gap: '8px' }}>
                        <Server style={{ width: '20px', height: '20px' }} />
                        System Status
                    </h3>
                    
                    {systemStatus ? (
                        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(300px, 1fr))', gap: '20px' }}>
                            <div>
                                <h4>Environment</h4>
                                <div style={{ display: 'flex', flexDirection: 'column', gap: '8px' }}>
                                    <div style={{ display: 'flex', justifyContent: 'space-between' }}>
                                        <span>PHP Version:</span>
                                        <strong>{systemStatus.php_version}</strong>
                                    </div>
                                    <div style={{ display: 'flex', justifyContent: 'space-between' }}>
                                        <span>WordPress:</span>
                                        <strong>{systemStatus.wordpress_version}</strong>
                                    </div>
                                    <div style={{ display: 'flex', justifyContent: 'space-between' }}>
                                        <span>Plugin Version:</span>
                                        <strong>{systemStatus.plugin_version}</strong>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <h4>Resources</h4>
                                <div style={{ display: 'flex', flexDirection: 'column', gap: '8px' }}>
                                    <div style={{ display: 'flex', justifyContent: 'space-between' }}>
                                        <span>Memory Limit:</span>
                                        <strong>{systemStatus.memory_limit}</strong>
                                    </div>
                                    <div style={{ display: 'flex', justifyContent: 'space-between' }}>
                                        <span>Memory Usage:</span>
                                        <strong>{systemStatus.memory_usage}</strong>
                                    </div>
                                    <div style={{ display: 'flex', justifyContent: 'space-between' }}>
                                        <span>Max Execution:</span>
                                        <strong>{systemStatus.max_execution_time}s</strong>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <h4>Services</h4>
                                <div style={{ display: 'flex', flexDirection: 'column', gap: '8px' }}>
                                    <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                                        <span>WP Cron:</span>
                                        <div style={{ display: 'flex', alignItems: 'center', gap: '4px' }}>
                                            {getStatusIcon(systemStatus.wp_cron_enabled ? 'healthy' : 'error')}
                                            <strong>{systemStatus.wp_cron_enabled ? 'Enabled' : 'Disabled'}</strong>
                                        </div>
                                    </div>
                                    <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                                        <span>Database:</span>
                                        <div style={{ display: 'flex', alignItems: 'center', gap: '4px' }}>
                                            {getStatusIcon(systemStatus.database_status)}
                                            <strong style={{ textTransform: 'capitalize' }}>{systemStatus.database_status}</strong>
                                        </div>
                                    </div>
                                    <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                                        <span>API Endpoints:</span>
                                        <div style={{ display: 'flex', alignItems: 'center', gap: '4px' }}>
                                            {getStatusIcon(systemStatus.api_endpoints_status)}
                                            <strong style={{ textTransform: 'capitalize' }}>{systemStatus.api_endpoints_status}</strong>
                                        </div>
                                    </div>
                                    <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                                        <span>Gemini API:</span>
                                        <div style={{ display: 'flex', alignItems: 'center', gap: '4px' }}>
                                            {(() => {
                                                const apiHealth = getApiHealth();
                                                const status = apiHealth.isHealthy ? 'healthy' : 'error';
                                                return (
                                                    <>
                                                        {getStatusIcon(status)}
                                                        <strong style={{ textTransform: 'capitalize' }}>
                                                            {apiHealth.isHealthy ? 'Healthy' : 'Unhealthy'}
                                                        </strong>
                                                        {apiHealth.responseTime > 0 && (
                                                            <span style={{ fontSize: '11px', color: '#666', marginLeft: '4px' }}>
                                                                ({apiHealth.responseTime}ms)
                                                            </span>
                                                        )}
                                                    </>
                                                );
                                            })()}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ) : (
                        <div style={{ textAlign: 'center', padding: '40px' }}>
                            <Spinner style={{ width: '32px', height: '32px' }} />
                            <p>Loading system status...</p>
                        </div>
                    )}
                </div>
            )}

            {activeTab === 'api' && (
                <div className="aca-card">
                    <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '20px' }}>
                        <h3 style={{ margin: 0, display: 'flex', alignItems: 'center', gap: '8px' }}>
                            <Zap style={{ width: '20px', height: '20px' }} />
                            API Call Logs ({apiCalls.length})
                        </h3>
                        <div style={{ display: 'flex', gap: '8px' }}>
                            <button
                                onClick={() => exportLogs('api')}
                                style={{
                                    padding: '6px 12px',
                                    border: '1px solid #0073aa',
                                    background: 'white',
                                    color: '#0073aa',
                                    borderRadius: '4px',
                                    cursor: 'pointer',
                                    display: 'flex',
                                    alignItems: 'center',
                                    gap: '4px',
                                    fontSize: '12px'
                                }}
                            >
                                <Download style={{ width: '12px', height: '12px' }} />
                                Export
                            </button>
                            <button
                                onClick={() => clearLogs('api')}
                                style={{
                                    padding: '6px 12px',
                                    border: '1px solid #dc3545',
                                    background: 'white',
                                    color: '#dc3545',
                                    borderRadius: '4px',
                                    cursor: 'pointer',
                                    display: 'flex',
                                    alignItems: 'center',
                                    gap: '4px',
                                    fontSize: '12px'
                                }}
                            >
                                <Trash2 style={{ width: '12px', height: '12px' }} />
                                Clear
                            </button>
                        </div>
                    </div>
                    
                    {apiCalls.length > 0 ? (
                        <div style={{ overflowX: 'auto' }}>
                            <table style={{ width: '100%', borderCollapse: 'collapse' }}>
                                <thead>
                                    <tr style={{ borderBottom: '1px solid #e1e5e9' }}>
                                        <th style={{ padding: '8px', textAlign: 'left', fontSize: '12px', fontWeight: '600' }}>Time</th>
                                        <th style={{ padding: '8px', textAlign: 'left', fontSize: '12px', fontWeight: '600' }}>Endpoint</th>
                                        <th style={{ padding: '8px', textAlign: 'left', fontSize: '12px', fontWeight: '600' }}>Method</th>
                                        <th style={{ padding: '8px', textAlign: 'left', fontSize: '12px', fontWeight: '600' }}>Duration</th>
                                        <th style={{ padding: '8px', textAlign: 'left', fontSize: '12px', fontWeight: '600' }}>Status</th>
                                        <th style={{ padding: '8px', textAlign: 'left', fontSize: '12px', fontWeight: '600' }}>Size</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {apiCalls.slice(0, 50).map(call => (
                                        <tr key={call.id} style={{ borderBottom: '1px solid #f1f3f4' }}>
                                            <td style={{ padding: '8px', fontSize: '11px', color: '#666' }}>
                                                {new Date(call.timestamp).toLocaleTimeString()}
                                            </td>
                                            <td style={{ padding: '8px', fontSize: '11px', fontFamily: 'monospace' }}>
                                                {call.endpoint}
                                            </td>
                                            <td style={{ padding: '8px', fontSize: '11px' }}>
                                                <span style={{
                                                    padding: '2px 6px',
                                                    borderRadius: '3px',
                                                    background: call.method === 'GET' ? '#e3f2fd' : '#fff3e0',
                                                    color: call.method === 'GET' ? '#1976d2' : '#f57c00',
                                                    fontSize: '10px'
                                                }}>
                                                    {call.method}
                                                </span>
                                            </td>
                                            <td style={{ padding: '8px', fontSize: '11px' }}>
                                                {formatDuration(call.duration)}
                                            </td>
                                            <td style={{ padding: '8px', fontSize: '11px' }}>
                                                <span style={{
                                                    color: call.status >= 400 ? '#dc3545' : call.status >= 300 ? '#ffc107' : '#28a745'
                                                }}>
                                                    {call.status}
                                                </span>
                                            </td>
                                            <td style={{ padding: '8px', fontSize: '11px' }}>
                                                {formatBytes(call.response_size)}
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    ) : (
                        <div style={{ textAlign: 'center', padding: '40px', color: '#666' }}>
                            <Zap style={{ width: '48px', height: '48px', opacity: 0.3, margin: '0 auto 16px' }} />
                            <p>No API calls recorded yet</p>
                        </div>
                    )}
                </div>
            )}

            {activeTab === 'errors' && (
                <div className="aca-card">
                    <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '20px' }}>
                        <h3 style={{ margin: 0, display: 'flex', alignItems: 'center', gap: '8px' }}>
                            <AlertTriangle style={{ width: '20px', height: '20px' }} />
                            Error Logs ({errorLogs.length})
                        </h3>
                        <div style={{ display: 'flex', gap: '8px' }}>
                            <button
                                onClick={() => exportLogs('errors')}
                                style={{
                                    padding: '6px 12px',
                                    border: '1px solid #0073aa',
                                    background: 'white',
                                    color: '#0073aa',
                                    borderRadius: '4px',
                                    cursor: 'pointer',
                                    display: 'flex',
                                    alignItems: 'center',
                                    gap: '4px',
                                    fontSize: '12px'
                                }}
                            >
                                <Download style={{ width: '12px', height: '12px' }} />
                                Export
                            </button>
                            <button
                                onClick={() => clearLogs('errors')}
                                style={{
                                    padding: '6px 12px',
                                    border: '1px solid #dc3545',
                                    background: 'white',
                                    color: '#dc3545',
                                    borderRadius: '4px',
                                    cursor: 'pointer',
                                    display: 'flex',
                                    alignItems: 'center',
                                    gap: '4px',
                                    fontSize: '12px'
                                }}
                            >
                                <Trash2 style={{ width: '12px', height: '12px' }} />
                                Clear
                            </button>
                        </div>
                    </div>
                    
                    {errorLogs.length > 0 ? (
                        <div style={{ display: 'flex', flexDirection: 'column', gap: '12px' }}>
                            {errorLogs.slice(0, 20).map(log => (
                                <div
                                    key={log.id}
                                    style={{
                                        padding: '12px',
                                        border: `1px solid ${getStatusColor(log.level)}40`,
                                        borderRadius: '6px',
                                        background: `${getStatusColor(log.level)}10`
                                    }}
                                >
                                    <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: '8px' }}>
                                        <div style={{ display: 'flex', alignItems: 'center', gap: '8px' }}>
                                            {getStatusIcon(log.level)}
                                            <span style={{
                                                fontSize: '11px',
                                                fontWeight: '600',
                                                textTransform: 'uppercase',
                                                color: getStatusColor(log.level)
                                            }}>
                                                {log.level}
                                            </span>
                                        </div>
                                        <span style={{ fontSize: '11px', color: '#666' }}>
                                            {new Date(log.timestamp).toLocaleString()}
                                        </span>
                                    </div>
                                    <p style={{ margin: '0 0 8px 0', fontSize: '13px', lineHeight: '1.4' }}>
                                        {log.message}
                                    </p>
                                    {log.file && (
                                        <div style={{ fontSize: '11px', color: '#666', fontFamily: 'monospace' }}>
                                            {log.file}{log.line && `:${log.line}`}
                                        </div>
                                    )}
                                </div>
                            ))}
                        </div>
                    ) : (
                        <div style={{ textAlign: 'center', padding: '40px', color: '#666' }}>
                            <CheckCircle style={{ width: '48px', height: '48px', opacity: 0.3, margin: '0 auto 16px', color: '#28a745' }} />
                            <p>No errors recorded - System running smoothly!</p>
                        </div>
                    )}
                </div>
            )}

            {activeTab === 'performance' && (
                <div className="aca-card">
                    <h3 style={{ marginBottom: '20px', display: 'flex', alignItems: 'center', gap: '8px' }}>
                        <Database style={{ width: '20px', height: '20px' }} />
                        Performance Metrics
                    </h3>
                    
                    {performanceMetrics.length > 0 ? (
                        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(250px, 1fr))', gap: '16px' }}>
                            {performanceMetrics.map((metric, index) => (
                                <div
                                    key={index}
                                    style={{
                                        padding: '16px',
                                        border: `1px solid ${getStatusColor(metric.status)}40`,
                                        borderRadius: '8px',
                                        background: `${getStatusColor(metric.status)}10`
                                    }}
                                >
                                    <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '8px' }}>
                                        <h4 style={{ margin: 0, fontSize: '14px' }}>{metric.name}</h4>
                                        {getStatusIcon(metric.status)}
                                    </div>
                                    <div style={{ fontSize: '24px', fontWeight: 'bold', color: getStatusColor(metric.status), marginBottom: '4px' }}>
                                        {metric.value}{metric.unit}
                                    </div>
                                    {metric.description && (
                                        <p style={{ margin: 0, fontSize: '12px', color: '#666' }}>
                                            {metric.description}
                                        </p>
                                    )}
                                </div>
                            ))}
                        </div>
                    ) : (
                        <div style={{ textAlign: 'center', padding: '40px', color: '#666' }}>
                            <Database style={{ width: '48px', height: '48px', opacity: 0.3, margin: '0 auto 16px' }} />
                            <p>No performance metrics available</p>
                        </div>
                    )}
                </div>
            )}
        </div>
    );
};