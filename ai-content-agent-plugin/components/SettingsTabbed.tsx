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
    openSection?: string; // Section to open when component loads
}

export const SettingsTabbed: React.FC<SettingsProps> = ({ settings, onSaveSettings, onRefreshApp, onShowToast, openSection }) => {
    const [activeTab, setActiveTab] = useState<string>('license');
    const [currentSettings, setCurrentSettings] = useState<AppSettings>(settings);
    const [licenseKey, setLicenseKey] = useState('');
    const [licenseStatus, setLicenseStatus] = useState<{
        status: string;
        is_active: boolean;
        verified_at?: string;
    }>({status: 'inactive', is_active: false});
    const [isVerifyingLicense, setIsVerifyingLicense] = useState(false);
    const [isLoadingLicenseStatus, setIsLoadingLicenseStatus] = useState(true);

    // Load license status on component mount
    useEffect(() => {
        const loadLicenseStatus = async () => {
            try {
                const status = await licenseApi.getStatus();
                setLicenseStatus({
                    status: status.status || 'inactive',
                    is_active: status.is_active || false,
                    verified_at: status.verified_at
                });
            } catch (error) {
                console.error('Failed to load license status:', error);
            } finally {
                setIsLoadingLicenseStatus(false);
            }
        };
        
        loadLicenseStatus();
    }, []);

    // Open specific section if requested
    useEffect(() => {
        if (openSection) {
            setActiveTab(openSection);
        }
    }, [openSection]);

    // Update currentSettings when settings prop changes
    useEffect(() => {
        setCurrentSettings(settings);
    }, [settings]);

    // Auto-save settings when they change
    useEffect(() => {
        if (JSON.stringify(currentSettings) !== JSON.stringify(settings)) {
            const timeoutId = setTimeout(() => {
                onSaveSettings(currentSettings);
            }, 1000); // Auto-save after 1 second of no changes

            return () => clearTimeout(timeoutId);
        }
    }, [currentSettings, settings, onSaveSettings]);

    // Tab definitions
    const tabs = [
        {
            id: 'license',
            title: 'Pro License',
            icon: <Shield style={{ width: '18px', height: '18px' }} />,
            description: 'Activate Pro features',
            color: '#3b82f6'
        },
        {
            id: 'automation',
            title: 'Automation Mode',
            icon: <Zap style={{ width: '18px', height: '18px' }} />,
            description: 'Configure automation',
            color: '#f59e0b'
        },
        {
            id: 'integrations',
            title: 'Integrations & Services',
            icon: <Google style={{ width: '18px', height: '18px' }} />,
            description: 'API connections',
            color: '#10b981'
        },
        {
            id: 'content',
            title: 'Content Analysis',
            icon: <SettingsIcon style={{ width: '18px', height: '18px' }} />,
            description: 'Analysis settings',
            color: '#8b5cf6'
        },
        {
            id: 'advanced',
            title: 'Debug Panel',
            icon: <SettingsIcon style={{ width: '18px', height: '18px' }} />,
            description: 'Advanced options',
            color: '#ef4444'
        }
    ];

    const handleLicenseVerification = async () => {
        if (!licenseKey.trim()) return;
        
        setIsVerifyingLicense(true);
        try {
            const result = await licenseApi.verify(licenseKey);
            if (result.valid) {
                setLicenseStatus({
                    status: 'active',
                    is_active: true,
                    verified_at: new Date().toISOString()
                });
                setLicenseKey('');
                if (onShowToast) {
                    onShowToast('Pro license activated successfully!', 'success');
                }
                if (onRefreshApp) {
                    onRefreshApp();
                }
            } else {
                if (onShowToast) {
                    onShowToast('Invalid license key. Please check and try again.', 'error');
                }
            }
        } catch (error) {
            console.error('License verification error:', error);
            if (onShowToast) {
                onShowToast('Failed to verify license. Please try again.', 'error');
            }
        } finally {
            setIsVerifyingLicense(false);
        }
    };

    const handleLicenseDeactivation = async () => {
        setIsVerifyingLicense(true);
        try {
            await licenseApi.deactivate();
            setLicenseStatus({
                status: 'inactive',
                is_active: false
            });
            if (onShowToast) {
                onShowToast('License deactivated successfully.', 'info');
            }
            if (onRefreshApp) {
                onRefreshApp();
            }
        } catch (error) {
            console.error('License deactivation error:', error);
            if (onShowToast) {
                onShowToast('Failed to deactivate license. Please try again.', 'error');
            }
        } finally {
            setIsVerifyingLicense(false);
        }
    };

    // Render tab content based on active tab
    const renderTabContent = () => {
        const currentTab = tabs.find(tab => tab.id === activeTab);
        
        return (
            <div>
                {/* Tab Header */}
                <div style={{ marginBottom: '30px' }}>
                    <div style={{ display: 'flex', alignItems: 'center', gap: '12px', marginBottom: '8px' }}>
                        <div style={{
                            width: '40px',
                            height: '40px',
                            background: currentTab ? `linear-gradient(135deg, ${currentTab.color}, ${currentTab.color}dd)` : '#f1f5f9',
                            borderRadius: '10px',
                            display: 'flex',
                            alignItems: 'center',
                            justifyContent: 'center',
                            color: 'white'
                        }}>
                            {currentTab?.icon}
                        </div>
                        <h2 style={{
                            fontSize: '24px',
                            fontWeight: '600',
                            margin: 0,
                            color: '#1e293b'
                        }}>
                            {currentTab?.title}
                        </h2>
                    </div>
                    <p style={{
                        color: '#64748b',
                        fontSize: '16px',
                        margin: 0,
                        lineHeight: '1.5'
                    }}>
                        {getTabDescription(activeTab)}
                    </p>
                </div>

                {/* Tab Content */}
                <div>
                    {getTabContent(activeTab)}
                </div>
            </div>
        );
    };

    const getTabDescription = (tabId: string): string => {
        switch (tabId) {
            case 'license': return 'Unlock advanced features and automation capabilities with your Pro license.';
            case 'automation': return 'Configure how AI Content Agent creates and publishes content automatically.';
            case 'integrations': return 'Connect to external services and configure how content is generated and optimized.';
            case 'content': return 'Customize content analysis settings and generation preferences.';
            case 'advanced': return 'Advanced debugging tools and developer options for troubleshooting.';
            default: return '';
        }
    };

    const getTabContent = (tabId: string): React.ReactNode => {
        switch (tabId) {
            case 'license': return renderLicenseContent();
            case 'automation': return renderAutomationContent();
            case 'integrations': return renderIntegrationsContent();
            case 'content': return renderContentContent();
            case 'advanced': return renderAdvancedContent();
            default: return <div>Content not found</div>;
        }
    };

    // License content render function
    const renderLicenseContent = () => {
        return (
            <div>
                <div className="aca-stat-item" style={{ margin: '0 0 20px 0' }}>
                    <div className="aca-stat-info">
                        <div className="aca-stat-icon">
                            {licenseStatus.is_active ? (
                                <CheckCircle style={{ color: '#27ae60', width: '20px', height: '20px' }} />
                            ) : (
                                <Shield style={{ color: '#e74c3c', width: '20px', height: '20px' }} />
                            )}
                        </div>
                        <div>
                            <div className="aca-stat-number">
                                {licenseStatus.is_active ? 'Pro Active' : 'Free Version'}
                            </div>
                            <div className="aca-stat-label">
                                {licenseStatus.is_active 
                                    ? `Verified ${licenseStatus.verified_at ? new Date(licenseStatus.verified_at).toLocaleDateString() : ''}`
                                    : 'Upgrade to unlock Pro features'
                                }
                            </div>
                        </div>
                    </div>
                </div>

                {!licenseStatus.is_active && (
                    <div className="aca-form-group">
                        <label className="aca-label" htmlFor="license-key">License Key</label>
                        <div style={{ display: 'flex', gap: '10px' }}>
                            <input
                                id="license-key"
                                type="text"
                                className="aca-input"
                                value={licenseKey}
                                onChange={(e) => setLicenseKey(e.target.value)}
                                placeholder="Enter your Pro license key"
                                disabled={isVerifyingLicense}
                            />
                            <button
                                onClick={handleLicenseVerification}
                                disabled={isVerifyingLicense || !licenseKey.trim()}
                                className="aca-button aca-button-primary"
                                style={{ minWidth: '120px' }}
                            >
                                {isVerifyingLicense ? (
                                    <>
                                        <Spinner className="aca-spinner" />
                                        Verifying...
                                    </>
                                ) : (
                                    'Verify License'
                                )}
                            </button>
                        </div>
                        <p className="aca-page-description" style={{ marginTop: '10px' }}>
                            Don't have a Pro license? <a href="https://ademisler.gumroad.com/l/ai-content-agent-pro" target="_blank" rel="noopener noreferrer" style={{ color: '#0073aa' }}>Purchase here</a>
                        </p>
                    </div>
                )}

                {licenseStatus.is_active && (
                    <>
                        <div className="aca-alert aca-alert-success" style={{ margin: '20px 0' }}>
                            <CheckCircle style={{ width: '16px', height: '16px', marginRight: '8px' }} />
                            Pro license is active! You now have access to all premium features.
                        </div>
                        <div style={{ marginTop: '15px' }}>
                            <button
                                onClick={handleLicenseDeactivation}
                                disabled={isVerifyingLicense}
                                className="aca-button aca-button-secondary"
                                style={{ 
                                    fontSize: '12px',
                                    padding: '6px 12px'
                                }}
                            >
                                {isVerifyingLicense ? (
                                    <>
                                        <Spinner className="aca-spinner" />
                                        Deactivating...
                                    </>
                                ) : (
                                    'Deactivate License'
                                )}
                            </button>
                            <p className="aca-page-description" style={{ marginTop: '8px', fontSize: '12px' }}>
                                This will disable all Pro features and allow you to use the license on another site.
                            </p>
                        </div>
                    </>
                )}
            </div>
        );
    };

    // Placeholder content functions - will be implemented with actual content
    const renderAutomationContent = () => (
        <div>
            <p>Automation Mode content will be implemented here.</p>
            <div style={{ padding: '20px', backgroundColor: '#f8f9fa', borderRadius: '8px', color: '#666' }}>
                This section will contain automation configuration options.
            </div>
        </div>
    );

    const renderIntegrationsContent = () => (
        <div>
            {/* Google AI Integration */}
            <div className="aca-form-group">
                <label className="aca-label" htmlFor="gemini-api-key">
                    <div style={{ display: 'flex', alignItems: 'center', gap: '8px' }}>
                        <Google style={{ width: '16px', height: '16px', color: '#4285f4' }} />
                        Google Gemini API Key
                    </div>
                </label>
                <input
                    id="gemini-api-key"
                    type="password"
                    className="aca-input"
                    value={currentSettings.geminiApiKey}
                    onChange={(e) => setCurrentSettings(prev => ({ ...prev, geminiApiKey: e.target.value }))}
                    placeholder="Enter your Google Gemini API key"
                />
                <p className="aca-page-description" style={{ marginTop: '8px' }}>
                    Get your free API key from <a href="https://makersuite.google.com/app/apikey" target="_blank" rel="noopener noreferrer" style={{ color: '#0073aa' }}>Google AI Studio</a>. 
                    This is required for all AI-powered features including content generation and idea creation.
                </p>
            </div>

            {/* Save Button */}
            <div style={{ marginTop: '30px', paddingTop: '20px', borderTop: '1px solid #e2e8f0' }}>
                <button
                    onClick={() => onSaveSettings(currentSettings)}
                    className="aca-button aca-button-primary"
                    style={{ marginRight: '10px' }}
                >
                    Save Settings
                </button>
                <p style={{ 
                    color: '#64748b', 
                    fontSize: '14px', 
                    margin: '10px 0 0 0',
                    fontStyle: 'italic'
                }}>
                    Settings are automatically saved when you make changes.
                </p>
            </div>
        </div>
    );

    const renderContentContent = () => (
        <div>
            <p>Content Analysis Settings will be implemented here.</p>
            <div style={{ padding: '20px', backgroundColor: '#f8f9fa', borderRadius: '8px', color: '#666' }}>
                This section will contain content analysis configuration options.
            </div>
        </div>
    );

    const renderAdvancedContent = () => (
        <div>
            <p>Debug Panel content will be implemented here.</p>
            <div style={{ padding: '20px', backgroundColor: '#f8f9fa', borderRadius: '8px', color: '#666' }}>
                This section will contain advanced debugging tools and developer options.
            </div>
        </div>
    );

    return (
        <div className="aca-fade-in" style={{
            maxHeight: 'calc(100vh - 100px)',
            overflowY: 'auto',
            paddingRight: '10px'
        }}>
            {/* Modern Settings Header */}
            <div style={{
                background: 'linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%)',
                borderRadius: '12px',
                padding: '30px',
                marginBottom: '30px',
                color: 'white',
                position: 'relative',
                overflow: 'hidden'
            }}>
                <div style={{ position: 'relative', zIndex: 2 }}>
                    <div style={{ display: 'flex', alignItems: 'center', gap: '12px', marginBottom: '12px' }}>
                        <div style={{
                            width: '48px',
                            height: '48px',
                            background: 'rgba(255,255,255,0.2)',
                            borderRadius: '12px',
                            display: 'flex',
                            alignItems: 'center',
                            justifyContent: 'center',
                            backdropFilter: 'blur(10px)'
                        }}>
                            <SettingsIcon style={{ width: '24px', height: '24px' }} />
                        </div>
                        <div>
                            <h1 style={{ 
                                fontSize: '28px', 
                                fontWeight: '700', 
                                margin: 0,
                                textShadow: '0 2px 4px rgba(0,0,0,0.1)',
                                color: 'white'
                            }}>
                                Settings & Configuration
                            </h1>
                            <div style={{ fontSize: '16px', opacity: 0.9, marginTop: '4px' }}>
                                Customize your AI Content Agent experience
                            </div>
                        </div>
                    </div>
                    <p style={{ 
                        fontSize: '14px', 
                        opacity: 0.85,
                        margin: 0,
                        maxWidth: '600px',
                        lineHeight: '1.5'
                    }}>
                        Configure automation modes, API integrations, and content generation preferences to optimize your workflow
                    </p>
                </div>
                {/* Decorative elements */}
                <div style={{
                    position: 'absolute',
                    top: '-30px',
                    right: '-30px',
                    width: '120px',
                    height: '120px',
                    background: 'rgba(255,255,255,0.1)',
                    borderRadius: '50%',
                    zIndex: 1
                }}></div>
                <div style={{
                    position: 'absolute',
                    bottom: '-20px',
                    left: '-20px',
                    width: '80px',
                    height: '80px',
                    background: 'rgba(255,255,255,0.05)',
                    borderRadius: '50%',
                    zIndex: 1
                }}></div>
            </div>

            {/* Tab Navigation */}
            <div style={{
                display: 'flex',
                flexWrap: 'wrap',
                gap: '4px',
                marginBottom: '30px',
                backgroundColor: '#ffffff',
                borderRadius: '12px',
                padding: '8px',
                border: '1px solid #e2e8f0',
                boxShadow: '0 4px 6px -1px rgba(0, 0, 0, 0.1)'
            }}>
                {tabs.map((tab) => (
                    <button
                        key={tab.id}
                        onClick={() => setActiveTab(tab.id)}
                        style={{
                            display: 'flex',
                            alignItems: 'center',
                            gap: '8px',
                            padding: '12px 16px',
                            border: 'none',
                            borderRadius: '8px',
                            background: activeTab === tab.id 
                                ? `linear-gradient(135deg, ${tab.color}, ${tab.color}dd)` 
                                : 'transparent',
                            color: activeTab === tab.id ? 'white' : '#64748b',
                            cursor: 'pointer',
                            fontSize: '14px',
                            fontWeight: activeTab === tab.id ? '600' : '500',
                            transition: 'all 0.2s ease',
                            flex: '1',
                            minWidth: '140px',
                            justifyContent: 'center',
                            boxShadow: activeTab === tab.id ? '0 2px 4px rgba(0,0,0,0.1)' : 'none'
                        }}
                        onMouseOver={(e) => {
                            if (activeTab !== tab.id) {
                                e.currentTarget.style.background = '#f8fafc';
                                e.currentTarget.style.color = tab.color;
                            }
                        }}
                        onMouseOut={(e) => {
                            if (activeTab !== tab.id) {
                                e.currentTarget.style.background = 'transparent';
                                e.currentTarget.style.color = '#64748b';
                            }
                        }}
                    >
                        <div style={{ 
                            color: 'inherit',
                            display: 'flex',
                            alignItems: 'center'
                        }}>
                            {tab.icon}
                        </div>
                        <div style={{ textAlign: 'left' }}>
                            <div style={{ lineHeight: '1.2', fontSize: '14px' }}>
                                {tab.title}
                            </div>
                            <div style={{ 
                                fontSize: '11px', 
                                opacity: 0.8, 
                                marginTop: '2px',
                                lineHeight: '1.2'
                            }}>
                                {tab.description}
                            </div>
                        </div>
                    </button>
                ))}
            </div>

            {/* Tab Content */}
            <div style={{
                backgroundColor: '#ffffff',
                borderRadius: '12px',
                border: '1px solid #e2e8f0',
                boxShadow: '0 4px 6px -1px rgba(0, 0, 0, 0.1)',
                padding: '30px',
                minHeight: '500px'
            }}>
                {renderTabContent()}
            </div>
        </div>
    );
};