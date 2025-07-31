import React from 'react';
import { Brain } from './Icons';
import { SettingsLayout } from './SettingsLayout';

interface SettingsAdvancedProps {
    onShowToast: (message: string, type: 'success' | 'error' | 'warning' | 'info') => void;
}

declare global {
    interface Window {
        acaData: {
            nonce: string;
            api_url: string;
            admin_url: string;
            plugin_url: string;
        };
    }
}

export const SettingsAdvanced: React.FC<SettingsAdvancedProps> = ({ 
    onShowToast 
}) => {
    const handleCheckAutomationStatus = () => {
        if (!window.acaData) {
            console.error('ACA: WordPress data not available');
            return;
        }
        fetch(window.acaData.api_url + 'debug/automation', {
            headers: { 'X-WP-Nonce': window.acaData.nonce }
        })
        .then(r => r.json())
        .then(data => {
            console.log('Automation Debug Info:', data);
            onShowToast('Debug info logged to console', 'info');
        })
        .catch(error => {
            console.error('Error checking automation status:', error);
            onShowToast('Failed to check automation status', 'error');
        });
    };

    const handleTestSemiAutoCron = () => {
        if (!window.acaData) {
            console.error('ACA: WordPress data not available');
            return;
        }
        fetch(window.acaData.api_url + 'debug/cron/semi-auto', {
            method: 'POST',
            headers: { 'X-WP-Nonce': window.acaData.nonce }
        })
        .then(r => r.json())
        .then(data => {
            onShowToast(data.message || 'Semi-auto cron triggered', 'success');
        })
        .catch(error => {
            console.error('Error testing semi-auto cron:', error);
            onShowToast('Failed to test semi-auto cron', 'error');
        });
    };

    const handleTestFullAutoCron = () => {
        if (!window.acaData) {
            console.error('ACA: WordPress data not available');
            return;
        }
        fetch(window.acaData.api_url + 'debug/cron/full-auto', {
            method: 'POST',
            headers: { 'X-WP-Nonce': window.acaData.nonce }
        })
        .then(r => r.json())
        .then(data => {
            onShowToast(data.message || 'Full-auto cron triggered', 'success');
        })
        .catch(error => {
            console.error('Error testing full-auto cron:', error);
            onShowToast('Failed to test full-auto cron', 'error');
        });
    };

    return (
        <SettingsLayout
            title="Advanced & Debug"
            description="Developer tools and advanced debugging features for automation testing"
            icon={<Brain style={{ width: '24px', height: '24px', color: 'white' }} />}
        >
            <div className="aca-alert info" style={{ marginBottom: '20px' }}>
                <p style={{ margin: 0, fontSize: '14px' }}>
                    <strong>🛠️ For Developers & Advanced Users:</strong> This panel is designed for testing and debugging automation features. 
                    Use these tools to manually trigger automation tasks, check cron job status, and troubleshoot issues. 
                    Regular users typically don't need to use this panel.
                </p>
            </div>
            
            <p className="aca-page-description" style={{ marginBottom: '20px' }}>
                Test automation functionality and check cron status. Click the buttons below to manually trigger automation tasks or check their status.
            </p>
            
            <div style={{ display: 'flex', gap: '10px', marginBottom: '20px', flexWrap: 'wrap' }}>
                <button 
                    className="aca-action-button"
                    onClick={handleCheckAutomationStatus}
                    style={{
                        padding: '12px 20px',
                        backgroundColor: '#3b82f6',
                        color: 'white',
                        border: 'none',
                        borderRadius: '8px',
                        cursor: 'pointer',
                        fontSize: '14px',
                        fontWeight: '500',
                        transition: 'background-color 0.2s'
                    }}
                    onMouseEnter={(e) => e.currentTarget.style.backgroundColor = '#2563eb'}
                    onMouseLeave={(e) => e.currentTarget.style.backgroundColor = '#3b82f6'}
                >
                    Check Automation Status
                </button>
                
                <button 
                    className="aca-action-button"
                    onClick={handleTestSemiAutoCron}
                    style={{
                        padding: '12px 20px',
                        backgroundColor: '#10b981',
                        color: 'white',
                        border: 'none',
                        borderRadius: '8px',
                        cursor: 'pointer',
                        fontSize: '14px',
                        fontWeight: '500',
                        transition: 'background-color 0.2s'
                    }}
                    onMouseEnter={(e) => e.currentTarget.style.backgroundColor = '#059669'}
                    onMouseLeave={(e) => e.currentTarget.style.backgroundColor = '#10b981'}
                >
                    Test Semi-Auto Cron
                </button>
                
                <button 
                    className="aca-action-button"
                    onClick={handleTestFullAutoCron}
                    style={{
                        padding: '12px 20px',
                        backgroundColor: '#f59e0b',
                        color: 'white',
                        border: 'none',
                        borderRadius: '8px',
                        cursor: 'pointer',
                        fontSize: '14px',
                        fontWeight: '500',
                        transition: 'background-color 0.2s'
                    }}
                    onMouseEnter={(e) => e.currentTarget.style.backgroundColor = '#d97706'}
                    onMouseLeave={(e) => e.currentTarget.style.backgroundColor = '#f59e0b'}
                >
                    Test Full-Auto Cron
                </button>
            </div>

            <div className="aca-alert warning" style={{ marginTop: '20px' }}>
                <p style={{ margin: 0, fontSize: '13px' }}>
                    <strong>⚠️ Important:</strong> These debug functions are for testing purposes only. 
                    They may trigger content generation or publishing actions. Use with caution on production sites.
                </p>
            </div>
        </SettingsLayout>
    );
};