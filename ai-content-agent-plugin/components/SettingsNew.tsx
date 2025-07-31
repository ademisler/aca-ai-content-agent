import React, { useState, useEffect } from 'react';
import type { AppSettings, AutomationMode, ImageSourceProvider, AiImageStyle, SeoPlugin } from '../types';
import { 
    Spinner, 
    Google, 
    CheckCircle, 
    Settings as SettingsIcon, 
    Zap, 
    Image, 
    Shield 
} from './Icons';
import { UpgradePrompt } from './UpgradePrompt';
import { licenseApi } from '../services/wordpressApi';

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

interface SettingsProps {
    settings: AppSettings;
    onSaveSettings: (settings: AppSettings) => void;
    onRefreshApp?: () => void;
    onShowToast?: (message: string, type: 'success' | 'error' | 'warning' | 'info') => void;
    openSection?: string;
}

export const Settings: React.FC<SettingsProps> = ({ settings, onSaveSettings, onRefreshApp, onShowToast, openSection }) => {
    const [currentSettings, setCurrentSettings] = useState<AppSettings>(settings);
    const [activeTab, setActiveTab] = useState<string>(openSection || 'license');
    const [isSaving, setIsSaving] = useState(false);

    // Tab definitions
    const tabs = [
        {
            id: 'license',
            title: 'License & Pro Features',
            description: 'Manage your Pro license and unlock advanced features',
            icon: <Shield style={{ width: '18px', height: '18px', color: 'white' }} />
        },
        {
            id: 'automation',
            title: 'Automation Mode',
            description: 'Configure content generation and publishing automation',
            icon: <Zap style={{ width: '18px', height: '18px', color: 'white' }} />
        }
    ];

    // Get gradient color for each tab
    const getGradientColor = (tabId: string) => {
        switch (tabId) {
            case 'license': return 'linear-gradient(135deg, #3b82f6, #1d4ed8)';
            case 'automation': return 'linear-gradient(135deg, #f59e0b, #d97706)';
            default: return 'linear-gradient(135deg, #6b7280, #4b5563)';
        }
    };

    const handleSave = async () => {
        setIsSaving(true);
        setTimeout(() => {
            onSaveSettings(currentSettings);
            setIsSaving(false);
        }, 700);
    };

    const isDirty = JSON.stringify(currentSettings) !== JSON.stringify(settings);

    return (
        <div className="aca-fade-in">
            {/* Modern Settings Header */}
            <div style={{
                background: 'linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%)',
                borderRadius: '12px',
                padding: '30px',
                marginBottom: '30px',
                color: 'white'
            }}>
                <h1 style={{ fontSize: '28px', fontWeight: '700', margin: 0 }}>
                    Settings & Configuration
                </h1>
                <p style={{ fontSize: '14px', opacity: 0.85, margin: '8px 0 0 0' }}>
                    Configure your AI assistant with modern vertical tabs - no more scroll jumping!
                </p>
            </div>

            {/* Vertical Tab Layout */}
            <div style={{ display: 'flex', gap: '30px', minHeight: '600px' }}>
                {/* Left Sidebar - Tab Navigation */}
                <div style={{ width: '280px', flexShrink: 0 }}>
                    <div className="aca-card" style={{
                        background: 'linear-gradient(145deg, #fefefe 0%, #f8f9fa 100%)',
                        border: '1px solid #e2e8f0',
                        borderRadius: '12px',
                        padding: '0',
                        overflow: 'hidden'
                    }}>
                        {tabs.map((tab, index) => (
                            <div
                                key={tab.id}
                                onClick={() => setActiveTab(tab.id)}
                                style={{
                                    padding: '20px',
                                    cursor: 'pointer',
                                    borderBottom: index < tabs.length - 1 ? '1px solid #e2e8f0' : 'none',
                                    background: activeTab === tab.id 
                                        ? 'linear-gradient(135deg, rgba(99, 102, 241, 0.1) 0%, rgba(139, 92, 246, 0.1) 100%)'
                                        : 'transparent',
                                    borderLeft: activeTab === tab.id ? '4px solid #6366f1' : '4px solid transparent',
                                    transition: 'all 0.2s ease'
                                }}
                            >
                                <div style={{ display: 'flex', alignItems: 'center', gap: '12px', marginBottom: '8px' }}>
                                    <div style={{
                                        width: '36px',
                                        height: '36px',
                                        background: activeTab === tab.id ? getGradientColor(tab.id) : '#e2e8f0',
                                        borderRadius: '8px',
                                        display: 'flex',
                                        alignItems: 'center',
                                        justifyContent: 'center',
                                        transition: 'all 0.2s ease'
                                    }}>
                                        {tab.icon}
                                    </div>
                                    <h3 style={{
                                        margin: 0,
                                        fontSize: '16px',
                                        fontWeight: activeTab === tab.id ? '600' : '500',
                                        color: activeTab === tab.id ? '#6366f1' : '#374151',
                                        transition: 'all 0.2s ease'
                                    }}>
                                        {tab.title}
                                    </h3>
                                </div>
                                <p style={{
                                    margin: 0,
                                    fontSize: '13px',
                                    color: '#6b7280',
                                    lineHeight: '1.4',
                                    paddingLeft: '48px'
                                }}>
                                    {tab.description}
                                </p>
                            </div>
                        ))}
                    </div>
                </div>

                {/* Right Content Area */}
                <div style={{ flex: 1, minHeight: '600px' }}>
                    <div className="aca-card" style={{
                        background: 'linear-gradient(145deg, #fefefe 0%, #f8f9fa 100%)',
                        border: '1px solid #e2e8f0',
                        borderRadius: '12px',
                        padding: '30px',
                        height: '100%',
                        overflow: 'auto'
                    }}>
                        {/* Tab Content */}
                        {activeTab === 'license' && (
                            <div>
                                <h2 style={{ color: '#1e293b', marginBottom: '20px' }}>License & Pro Features</h2>
                                <div className="aca-card" style={{ marginBottom: '20px', padding: '20px' }}>
                                    <div style={{ display: 'flex', alignItems: 'center', gap: '15px' }}>
                                        <div style={{
                                            width: '50px',
                                            height: '50px',
                                            borderRadius: '12px',
                                            background: 'linear-gradient(135deg, #ef4444, #dc2626)',
                                            display: 'flex',
                                            alignItems: 'center',
                                            justifyContent: 'center'
                                        }}>
                                            <Shield style={{ color: 'white', width: '24px', height: '24px' }} />
                                        </div>
                                        <div>
                                            <div style={{ fontSize: '18px', fontWeight: '600', color: '#1e293b' }}>
                                                Free Version
                                            </div>
                                            <div style={{ fontSize: '14px', color: '#64748b' }}>
                                                Upgrade to unlock Pro features
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div className="aca-form-group">
                                    <label className="aca-label">License Key</label>
                                    <input type="text" className="aca-input" placeholder="Enter your Pro license key" />
                                    <p className="aca-page-description" style={{ marginTop: '10px' }}>
                                        Don't have a Pro license? <a href="https://ademisler.gumroad.com/l/ai-content-agent-pro" target="_blank" rel="noopener noreferrer" style={{ color: '#0073aa' }}>Purchase here</a>
                                    </p>
                                </div>
                            </div>
                        )}

                        {activeTab === 'automation' && (
                            <div>
                                <h2 style={{ color: '#1e293b', marginBottom: '20px' }}>Automation Mode</h2>
                                <div className="aca-alert aca-alert-info" style={{ marginBottom: '20px' }}>
                                    <p style={{ margin: 0 }}>Choose how much control you want over content creation and publishing.</p>
                                </div>
                                <div style={{ display: 'flex', flexDirection: 'column', gap: '15px' }}>
                                    <label className="aca-card" style={{
                                        margin: 0,
                                        border: '2px solid',
                                        borderColor: currentSettings.mode === 'manual' ? '#0073aa' : '#ccd0d4',
                                        background: currentSettings.mode === 'manual' ? '#f0f6fc' : '#ffffff',
                                        cursor: 'pointer'
                                    }}>
                                        <div style={{ display: 'flex', alignItems: 'flex-start', gap: '12px' }}>
                                            <input
                                                type="radio"
                                                name="automation-mode"
                                                checked={currentSettings.mode === 'manual'}
                                                onChange={() => setCurrentSettings(prev => ({ ...prev, mode: 'manual' }))}
                                                style={{ marginTop: '2px', accentColor: '#0073aa' }}
                                            />
                                            <div>
                                                <h4 className="aca-card-title" style={{ marginBottom: '8px' }}>Manual Mode</h4>
                                                <p className="aca-page-description" style={{ margin: 0 }}>
                                                    Full manual control over content creation and publishing
                                                </p>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            </div>

            {/* Save Button */}
            <div style={{ 
                display: 'flex', 
                justifyContent: 'flex-end',
                paddingTop: '25px',
                borderTop: '1px solid #f0f0f1',
                marginTop: '30px'
            }}>
                <button
                    onClick={handleSave}
                    disabled={!isDirty || isSaving}
                    className="aca-button large"
                >
                    {isSaving && <span className="aca-spinner"></span>}
                    {isSaving ? 'Saving...' : 'Save Settings'}
                </button>
            </div>
        </div>
    );
};
