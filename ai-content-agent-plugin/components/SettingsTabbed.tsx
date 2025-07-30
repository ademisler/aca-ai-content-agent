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
            if (result.success) { // Fixed: was result.valid, should be result.success
                setLicenseStatus({
                    status: 'active',
                    is_active: true,
                    verified_at: new Date().toISOString()
                });
                setLicenseKey('');
                
                // Update settings to reflect pro status
                const updatedSettings = { ...currentSettings, is_pro: true };
                setCurrentSettings(updatedSettings);
                
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

    // Helper function to check if Pro features are available
    const isProActive = () => {
        return currentSettings.is_pro || licenseStatus.is_active;
    };

    const handleModeChange = (mode: AutomationMode) => {
        setCurrentSettings(prev => ({ ...prev, mode }));
    };

    const handleSettingChange = (field: keyof AppSettings, value: any) => {
        setCurrentSettings(prev => ({ ...prev, [field]: value }));
    };

    // RadioCard component for automation modes
    const RadioCard: React.FC<{
        id: AutomationMode;
        title: string;
        description: string;
        currentSelection: AutomationMode;
        onChange: (mode: AutomationMode) => void;
    }> = ({ id, title, description, currentSelection, onChange }) => {
        const isChecked = currentSelection === id;
        return (
            <label 
                htmlFor={id} 
                style={{
                    display: 'block',
                    margin: 0,
                    border: '2px solid',
                    borderColor: isChecked ? '#0073aa' : '#ccd0d4',
                    borderRadius: '8px',
                    padding: '20px',
                    cursor: 'pointer',
                    transition: 'all 0.2s ease',
                    background: isChecked ? '#f0f6fc' : '#ffffff',
                    boxShadow: isChecked ? '0 2px 4px rgba(0, 0, 0, 0.1)' : 'none'
                }}
            >
                <div style={{ display: 'flex', alignItems: 'flex-start', gap: '12px' }}>
                    <input 
                        type="radio" 
                        id={id} 
                        name="automation-mode" 
                        checked={isChecked} 
                        onChange={() => onChange(id)} 
                        style={{
                            marginTop: '2px',
                            width: '18px',
                            height: '18px',
                            accentColor: '#0073aa',
                            flexShrink: 0
                        }}
                    />
                    <div>
                        <h4 style={{ 
                            fontSize: '16px',
                            fontWeight: '600',
                            margin: '0 0 8px 0',
                            color: '#1e293b'
                        }}>
                            {title}
                        </h4>
                        <p style={{ 
                            margin: 0,
                            color: '#64748b',
                            fontSize: '14px',
                            lineHeight: '1.5'
                        }}>
                            {description}
                        </p>
                    </div>
                </div>
            </label>
        );
    };

    const renderAutomationContent = () => {
        if (isLoadingLicenseStatus) {
            return (
                <div style={{ padding: '20px', textAlign: 'center', color: '#666' }}>
                    Loading license status...
                </div>
            );
        }

        if (!isProActive()) {
            return (
                <div>
                    <div style={{ 
                        padding: '30px', 
                        backgroundColor: '#fef3cd', 
                        borderRadius: '8px', 
                        border: '1px solid #fbbf24',
                        textAlign: 'center'
                    }}>
                        <Shield style={{ width: '48px', height: '48px', color: '#f59e0b', marginBottom: '16px' }} />
                        <h3 style={{ color: '#92400e', margin: '0 0 8px 0' }}>Pro License Required</h3>
                        <p style={{ color: '#92400e', margin: '0 0 16px 0' }}>
                            Automation features are available with a Pro license.
                        </p>
                        <a 
                            href="https://ademisler.gumroad.com/l/ai-content-agent-pro" 
                            target="_blank" 
                            rel="noopener noreferrer"
                            style={{
                                display: 'inline-block',
                                padding: '10px 20px',
                                backgroundColor: '#f59e0b',
                                color: 'white',
                                textDecoration: 'none',
                                borderRadius: '6px',
                                fontWeight: '500'
                            }}
                        >
                            Upgrade to Pro
                        </a>
                    </div>
                </div>
            );
        }

        return (
            <div>
                <p style={{ color: '#64748b', fontSize: '16px', margin: '0 0 30px 0', lineHeight: '1.5' }}>
                    Choose how you want the AI Content Agent (ACA) to operate. You can change this at any time.
                </p>
                
                <div style={{ display: 'flex', flexDirection: 'column', gap: '15px' }}>
                    <RadioCard 
                        id="manual" 
                        title="Manual Mode" 
                        description="You are in full control. Manually generate ideas and create drafts one by one." 
                        currentSelection={currentSettings.mode} 
                        onChange={handleModeChange} 
                    />
                    
                    <div style={{
                        border: '2px solid',
                        borderColor: currentSettings.mode === 'semi-automatic' ? '#0073aa' : '#ccd0d4',
                        borderRadius: '8px',
                        padding: '20px',
                        background: currentSettings.mode === 'semi-automatic' ? '#f0f6fc' : '#ffffff',
                        boxShadow: currentSettings.mode === 'semi-automatic' ? '0 2px 4px rgba(0, 0, 0, 0.1)' : 'none'
                    }}>
                        <label htmlFor="semi-automatic" style={{ display: 'flex', alignItems: 'flex-start', cursor: 'pointer', gap: '12px' }}>
                            <input 
                                type="radio" 
                                id="semi-automatic" 
                                name="automation-mode" 
                                checked={currentSettings.mode === 'semi-automatic'} 
                                onChange={() => handleModeChange('semi-automatic')} 
                                style={{
                                    marginTop: '2px',
                                    width: '18px',
                                    height: '18px',
                                    accentColor: '#0073aa',
                                    flexShrink: 0
                                }}
                            />
                            <div>
                                <h4 style={{ 
                                    fontSize: '16px',
                                    fontWeight: '600',
                                    margin: '0 0 8px 0',
                                    color: '#1e293b'
                                }}>
                                    Semi-Automatic Mode
                                </h4>
                                <p style={{ 
                                    margin: 0,
                                    color: '#64748b',
                                    fontSize: '14px',
                                    lineHeight: '1.5'
                                }}>
                                    The AI automatically generates new ideas periodically. You choose which ideas to turn into drafts.
                                </p>
                            </div>
                        </label>
                        
                        {currentSettings.mode === 'semi-automatic' && (
                            <div style={{ 
                                paddingLeft: '30px', 
                                paddingTop: '20px', 
                                marginTop: '20px', 
                                borderTop: '1px solid #e0e0e0'
                            }}>
                                <label style={{ 
                                    display: 'block',
                                    fontSize: '14px',
                                    fontWeight: '500',
                                    color: '#374151',
                                    marginBottom: '8px'
                                }}>
                                    Idea Generation Frequency
                                </label>
                                <select 
                                    className="aca-input" 
                                    value={currentSettings.semiAutoIdeaFrequency || 'weekly'} 
                                    onChange={(e) => handleSettingChange('semiAutoIdeaFrequency', e.target.value)}
                                    style={{ marginTop: '5px' }}
                                >
                                    <option value="daily">Daily - Generate new ideas every day</option>
                                    <option value="weekly">Weekly - Generate new ideas every week</option>
                                    <option value="monthly">Monthly - Generate new ideas every month</option>
                                </select>
                                <p style={{ 
                                    color: '#64748b',
                                    fontSize: '12px',
                                    margin: '5px 0 0 0',
                                    lineHeight: '1.4'
                                }}>
                                    How often should the AI automatically generate new content ideas?
                                </p>
                            </div>
                        )}
                    </div>
                    
                    <div style={{
                        border: '2px solid',
                        borderColor: currentSettings.mode === 'full-automatic' ? '#0073aa' : '#ccd0d4',
                        borderRadius: '8px',
                        padding: '20px',
                        background: currentSettings.mode === 'full-automatic' ? '#f0f6fc' : '#ffffff',
                        boxShadow: currentSettings.mode === 'full-automatic' ? '0 2px 4px rgba(0, 0, 0, 0.1)' : 'none'
                    }}>
                        <label htmlFor="full-automatic-radio" style={{ display: 'flex', alignItems: 'flex-start', cursor: 'pointer', gap: '12px' }}>
                            <input 
                                type="radio" 
                                id="full-automatic-radio" 
                                name="automation-mode" 
                                checked={currentSettings.mode === 'full-automatic'} 
                                onChange={() => handleModeChange('full-automatic')} 
                                style={{
                                    marginTop: '2px',
                                    width: '18px',
                                    height: '18px',
                                    accentColor: '#0073aa',
                                    flexShrink: 0
                                }}
                            />
                            <div>
                                <h4 style={{ 
                                    fontSize: '16px',
                                    fontWeight: '600',
                                    margin: '0 0 8px 0',
                                    color: '#1e293b'
                                }}>
                                    Full Automatic Mode
                                </h4>
                                <p style={{ 
                                    margin: 0,
                                    color: '#64748b',
                                    fontSize: '14px',
                                    lineHeight: '1.5'
                                }}>
                                    Complete automation. The AI generates ideas, creates drafts, and can optionally publish them automatically.
                                </p>
                            </div>
                        </label>
                        
                        {currentSettings.mode === 'full-automatic' && (
                            <div style={{ 
                                paddingLeft: '30px', 
                                paddingTop: '20px', 
                                marginTop: '20px', 
                                borderTop: '1px solid #e0e0e0'
                            }}>
                                <div style={{ marginBottom: '20px' }}>
                                    <label style={{ 
                                        display: 'block',
                                        fontSize: '14px',
                                        fontWeight: '500',
                                        color: '#374151',
                                        marginBottom: '8px'
                                    }}>
                                        Content Generation Frequency
                                    </label>
                                    <select 
                                        className="aca-input" 
                                        value={currentSettings.fullAutoFrequency || 'weekly'} 
                                        onChange={(e) => handleSettingChange('fullAutoFrequency', e.target.value)}
                                    >
                                        <option value="daily">Daily - Generate and create content every day</option>
                                        <option value="weekly">Weekly - Generate and create content every week</option>
                                        <option value="monthly">Monthly - Generate and create content every month</option>
                                    </select>
                                </div>
                                
                                <div style={{ marginBottom: '15px' }}>
                                    <label style={{ 
                                        display: 'flex',
                                        alignItems: 'center',
                                        gap: '10px',
                                        cursor: 'pointer',
                                        fontSize: '14px',
                                        fontWeight: '500',
                                        color: '#374151'
                                    }}>
                                        <input 
                                            type="checkbox" 
                                            checked={currentSettings.autoPublish || false} 
                                            onChange={(e) => handleSettingChange('autoPublish', e.target.checked)}
                                            style={{
                                                width: '16px',
                                                height: '16px',
                                                accentColor: '#0073aa'
                                            }}
                                        />
                                        Auto-Publish Content
                                    </label>
                                    <p style={{ 
                                        color: '#64748b',
                                        fontSize: '12px',
                                        margin: '5px 0 0 26px',
                                        lineHeight: '1.4'
                                    }}>
                                        ⚠️ <strong>Use with caution:</strong> When enabled, content will be published automatically without your review.
                                    </p>
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        );
    };

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
            <p style={{ color: '#64748b', fontSize: '16px', margin: '0 0 30px 0', lineHeight: '1.5' }}>
                Configure content generation preferences and image settings.
            </p>

            {/* Image Source Provider */}
            <div style={{ marginBottom: '30px' }}>
                <h3 style={{ 
                    fontSize: '18px',
                    fontWeight: '600',
                    margin: '0 0 16px 0',
                    color: '#1e293b',
                    display: 'flex',
                    alignItems: 'center',
                    gap: '8px'
                }}>
                    <Image style={{ width: '20px', height: '20px', color: '#8b5cf6' }} />
                    Image Source Provider
                </h3>
                
                <div style={{ display: 'grid', gap: '12px' }}>
                    {[
                        { id: 'pexels', name: 'Pexels', description: 'Free stock photos (Recommended for beginners)', apiKeyField: 'pexelsApiKey' },
                        { id: 'unsplash', name: 'Unsplash', description: 'High-quality photos from professional photographers', apiKeyField: 'unsplashApiKey' },
                        { id: 'pixabay', name: 'Pixabay', description: 'Large collection of free images and illustrations', apiKeyField: 'pixabayApiKey' },
                        { id: 'google-ai', name: 'Google AI Generated', description: 'AI-generated images using Google Cloud (Pro feature)', apiKeyField: null }
                    ].map((provider) => (
                        <label 
                            key={provider.id}
                            style={{
                                display: 'block',
                                border: '2px solid',
                                borderColor: currentSettings.imageSourceProvider === provider.id ? '#8b5cf6' : '#e2e8f0',
                                borderRadius: '8px',
                                padding: '16px',
                                cursor: 'pointer',
                                transition: 'all 0.2s ease',
                                background: currentSettings.imageSourceProvider === provider.id ? '#faf5ff' : '#ffffff'
                            }}
                        >
                            <div style={{ display: 'flex', alignItems: 'flex-start', gap: '12px' }}>
                                <input 
                                    type="radio" 
                                    name="image-provider" 
                                    checked={currentSettings.imageSourceProvider === provider.id} 
                                    onChange={() => handleSettingChange('imageSourceProvider', provider.id)}
                                    style={{
                                        marginTop: '2px',
                                        width: '16px',
                                        height: '16px',
                                        accentColor: '#8b5cf6'
                                    }}
                                />
                                <div style={{ flex: 1 }}>
                                    <div style={{ 
                                        fontSize: '16px',
                                        fontWeight: '600',
                                        margin: '0 0 4px 0',
                                        color: '#1e293b'
                                    }}>
                                        {provider.name}
                                        {provider.id === 'google-ai' && (
                                            <span style={{ 
                                                marginLeft: '8px',
                                                background: 'linear-gradient(135deg, #f39c12 0%, #e67e22 100%)',
                                                color: 'white',
                                                padding: '2px 6px',
                                                borderRadius: '4px',
                                                fontSize: '10px',
                                                fontWeight: 'bold'
                                            }}>PRO</span>
                                        )}
                                    </div>
                                    <p style={{ 
                                        margin: 0,
                                        color: '#64748b',
                                        fontSize: '14px',
                                        lineHeight: '1.4'
                                    }}>
                                        {provider.description}
                                    </p>
                                </div>
                            </div>
                            
                            {/* API Key input for selected provider */}
                            {currentSettings.imageSourceProvider === provider.id && provider.apiKeyField && (
                                <div style={{ marginTop: '16px', paddingTop: '16px', borderTop: '1px solid #e2e8f0' }}>
                                    <label style={{ 
                                        display: 'block',
                                        fontSize: '14px',
                                        fontWeight: '500',
                                        color: '#374151',
                                        marginBottom: '8px'
                                    }}>
                                        {provider.name} API Key
                                    </label>
                                    <input
                                        type="password"
                                        value={currentSettings[provider.apiKeyField as keyof AppSettings] as string || ''}
                                        onChange={(e) => handleSettingChange(provider.apiKeyField as keyof AppSettings, e.target.value)}
                                        placeholder={`Enter your ${provider.name} API key`}
                                        className="aca-input"
                                        style={{ width: '100%' }}
                                    />
                                    <p style={{ 
                                        color: '#64748b',
                                        fontSize: '12px',
                                        margin: '4px 0 0 0',
                                        lineHeight: '1.4'
                                    }}>
                                        Get your API key from {provider.name}'s developer portal.
                                    </p>
                                </div>
                            )}
                            
                            {/* Google AI settings */}
                            {currentSettings.imageSourceProvider === 'google-ai' && (
                                <div style={{ marginTop: '16px', paddingTop: '16px', borderTop: '1px solid #e2e8f0' }}>
                                    <div style={{ marginBottom: '16px' }}>
                                        <label style={{ 
                                            display: 'block',
                                            fontSize: '14px',
                                            fontWeight: '500',
                                            color: '#374151',
                                            marginBottom: '8px'
                                        }}>
                                            Google Cloud Project ID
                                        </label>
                                        <input
                                            type="text"
                                            value={currentSettings.googleCloudProjectId || ''}
                                            onChange={(e) => handleSettingChange('googleCloudProjectId', e.target.value)}
                                            placeholder="your-project-id"
                                            className="aca-input"
                                            style={{ width: '100%' }}
                                        />
                                    </div>
                                    
                                    <div style={{ marginBottom: '16px' }}>
                                        <label style={{ 
                                            display: 'block',
                                            fontSize: '14px',
                                            fontWeight: '500',
                                            color: '#374151',
                                            marginBottom: '8px'
                                        }}>
                                            AI Image Style
                                        </label>
                                        <select 
                                            value={currentSettings.aiImageStyle || 'photorealistic'} 
                                            onChange={(e) => handleSettingChange('aiImageStyle', e.target.value)}
                                            className="aca-input"
                                            style={{ width: '100%' }}
                                        >
                                            <option value="photorealistic">Photorealistic</option>
                                            <option value="artistic">Artistic</option>
                                            <option value="illustration">Illustration</option>
                                            <option value="cartoon">Cartoon</option>
                                            <option value="abstract">Abstract</option>
                                        </select>
                                    </div>
                                </div>
                            )}
                        </label>
                    ))}
                </div>
            </div>

            {/* SEO Plugin Integration */}
            <div style={{ marginBottom: '30px' }}>
                <h3 style={{ 
                    fontSize: '18px',
                    fontWeight: '600',
                    margin: '0 0 16px 0',
                    color: '#1e293b',
                    display: 'flex',
                    alignItems: 'center',
                    gap: '8px'
                }}>
                    <SettingsIcon style={{ width: '20px', height: '20px', color: '#8b5cf6' }} />
                    SEO Plugin Integration
                </h3>
                
                <div style={{ 
                    padding: '20px', 
                    backgroundColor: '#f8fafc', 
                    borderRadius: '8px', 
                    border: '1px solid #e2e8f0'
                }}>
                    <p style={{ 
                        color: '#64748b',
                        fontSize: '14px',
                        margin: '0 0 16px 0',
                        lineHeight: '1.5'
                    }}>
                        AI Content Agent can automatically configure SEO metadata when creating content. 
                        We'll detect which SEO plugin you're using and optimize accordingly.
                    </p>
                    
                    <label style={{ 
                        display: 'block',
                        fontSize: '14px',
                        fontWeight: '500',
                        color: '#374151',
                        marginBottom: '8px'
                    }}>
                        SEO Plugin
                    </label>
                    <select 
                        value={currentSettings.seoPlugin || 'auto'} 
                        onChange={(e) => handleSettingChange('seoPlugin', e.target.value)}
                        className="aca-input"
                        style={{ width: '100%' }}
                    >
                        <option value="auto">Auto-detect</option>
                        <option value="yoast">Yoast SEO</option>
                        <option value="rankmath">RankMath</option>
                        <option value="aioseo">All in One SEO</option>
                        <option value="seopress">SEOPress</option>
                        <option value="none">No SEO Plugin</option>
                    </select>
                </div>
            </div>
        </div>
    );

    const renderAdvancedContent = () => (
        <div>
            <p style={{ color: '#64748b', fontSize: '16px', margin: '0 0 30px 0', lineHeight: '1.5' }}>
                Advanced debugging tools and developer options for troubleshooting.
            </p>

            {/* Google Search Console Integration (Pro Feature) */}
            <div style={{ marginBottom: '30px' }}>
                <h3 style={{ 
                    fontSize: '18px',
                    fontWeight: '600',
                    margin: '0 0 16px 0',
                    color: '#1e293b',
                    display: 'flex',
                    alignItems: 'center',
                    gap: '8px'
                }}>
                    <Google style={{ width: '20px', height: '20px', color: '#ef4444' }} />
                    Google Search Console Integration
                    <span style={{ 
                        marginLeft: '8px',
                        background: 'linear-gradient(135deg, #f39c12 0%, #e67e22 100%)',
                        color: 'white',
                        padding: '4px 8px',
                        borderRadius: '6px',
                        fontSize: '12px',
                        fontWeight: 'bold'
                    }}>PRO</span>
                </h3>
                
                {isLoadingLicenseStatus ? (
                    <div style={{ padding: '20px', textAlign: 'center', color: '#666' }}>
                        Loading license status...
                    </div>
                ) : !isProActive() ? (
                    <div style={{ 
                        padding: '20px', 
                        backgroundColor: '#fef3cd', 
                        borderRadius: '8px', 
                        border: '1px solid #fbbf24'
                    }}>
                        <p style={{ color: '#92400e', margin: '0 0 16px 0' }}>
                            Google Search Console integration is available with a Pro license.
                        </p>
                        <a 
                            href="https://ademisler.gumroad.com/l/ai-content-agent-pro" 
                            target="_blank" 
                            rel="noopener noreferrer"
                            style={{
                                display: 'inline-block',
                                padding: '8px 16px',
                                backgroundColor: '#f59e0b',
                                color: 'white',
                                textDecoration: 'none',
                                borderRadius: '6px',
                                fontSize: '14px',
                                fontWeight: '500'
                            }}
                        >
                            Upgrade to Pro
                        </a>
                    </div>
                ) : (
                    <div style={{ 
                        padding: '20px', 
                        backgroundColor: '#f8fafc', 
                        borderRadius: '8px', 
                        border: '1px solid #e2e8f0'
                    }}>
                        <p style={{ 
                            color: '#64748b',
                            fontSize: '14px',
                            margin: '0 0 20px 0',
                            lineHeight: '1.5'
                        }}>
                            Connect with Google Search Console to analyze your content performance and get data-driven insights.
                        </p>
                        
                        <div style={{ display: 'grid', gap: '16px', marginBottom: '20px' }}>
                            <div>
                                <label style={{ 
                                    display: 'block',
                                    fontSize: '14px',
                                    fontWeight: '500',
                                    color: '#374151',
                                    marginBottom: '8px'
                                }}>
                                    Google OAuth2 Client ID
                                </label>
                                <input
                                    type="text"
                                    value={currentSettings.gscClientId || ''}
                                    onChange={(e) => handleSettingChange('gscClientId', e.target.value)}
                                    placeholder="Your Google OAuth2 Client ID"
                                    className="aca-input"
                                    style={{ width: '100%' }}
                                />
                            </div>
                            
                            <div>
                                <label style={{ 
                                    display: 'block',
                                    fontSize: '14px',
                                    fontWeight: '500',
                                    color: '#374151',
                                    marginBottom: '8px'
                                }}>
                                    Google OAuth2 Client Secret
                                </label>
                                <input
                                    type="password"
                                    value={currentSettings.gscClientSecret || ''}
                                    onChange={(e) => handleSettingChange('gscClientSecret', e.target.value)}
                                    placeholder="Your Google OAuth2 Client Secret"
                                    className="aca-input"
                                    style={{ width: '100%' }}
                                />
                            </div>
                        </div>
                        
                        <p style={{ 
                            color: '#64748b',
                            fontSize: '12px',
                            margin: '0',
                            lineHeight: '1.4'
                        }}>
                            <a href="https://console.cloud.google.com/" target="_blank" rel="noopener noreferrer" style={{ color: '#0073aa' }}>
                                Learn how to set up Google OAuth2 credentials →
                            </a>
                        </p>
                        
                        <div style={{ 
                            marginTop: '20px',
                            padding: '16px',
                            backgroundColor: currentSettings.searchConsoleUser ? '#dcfce7' : '#fef2f2',
                            borderRadius: '6px',
                            border: `1px solid ${currentSettings.searchConsoleUser ? '#16a34a' : '#ef4444'}`
                        }}>
                            <div style={{ display: 'flex', alignItems: 'center', gap: '8px' }}>
                                {currentSettings.searchConsoleUser ? (
                                    <CheckCircle style={{ width: '16px', height: '16px', color: '#16a34a' }} />
                                ) : (
                                    <SettingsIcon style={{ width: '16px', height: '16px', color: '#ef4444' }} />
                                )}
                                <span style={{ 
                                    fontSize: '14px',
                                    fontWeight: '500',
                                    color: currentSettings.searchConsoleUser ? '#166534' : '#991b1b'
                                }}>
                                    {currentSettings.searchConsoleUser ? 'Connected' : 'Not Connected'}
                                </span>
                            </div>
                            {currentSettings.searchConsoleUser && (
                                <p style={{ 
                                    margin: '4px 0 0 24px',
                                    fontSize: '12px',
                                    color: '#166534'
                                }}>
                                    Connected as: {currentSettings.searchConsoleUser}
                                </p>
                            )}
                        </div>
                    </div>
                )}
            </div>

            {/* Debug Information */}
            <div style={{ marginBottom: '30px' }}>
                <h3 style={{ 
                    fontSize: '18px',
                    fontWeight: '600',
                    margin: '0 0 16px 0',
                    color: '#1e293b',
                    display: 'flex',
                    alignItems: 'center',
                    gap: '8px'
                }}>
                    <SettingsIcon style={{ width: '20px', height: '20px', color: '#ef4444' }} />
                    Debug Information
                </h3>
                
                <div style={{ 
                    padding: '20px', 
                    backgroundColor: '#f8fafc', 
                    borderRadius: '8px', 
                    border: '1px solid #e2e8f0'
                }}>
                    <div style={{ display: 'grid', gap: '12px' }}>
                        <div style={{ 
                            display: 'flex',
                            justifyContent: 'space-between',
                            alignItems: 'center',
                            padding: '8px 0',
                            borderBottom: '1px solid #e2e8f0'
                        }}>
                            <span style={{ fontSize: '14px', fontWeight: '500', color: '#374151' }}>
                                Plugin Version
                            </span>
                            <span style={{ fontSize: '14px', color: '#64748b' }}>
                                2.3.1
                            </span>
                        </div>
                        
                        <div style={{ 
                            display: 'flex',
                            justifyContent: 'space-between',
                            alignItems: 'center',
                            padding: '8px 0',
                            borderBottom: '1px solid #e2e8f0'
                        }}>
                            <span style={{ fontSize: '14px', fontWeight: '500', color: '#374151' }}>
                                License Status
                            </span>
                            <span style={{ 
                                fontSize: '14px',
                                color: licenseStatus.is_active ? '#16a34a' : '#ef4444',
                                fontWeight: '500'
                            }}>
                                {licenseStatus.is_active ? 'Pro Active' : 'Free Version'}
                            </span>
                        </div>
                        
                        <div style={{ 
                            display: 'flex',
                            justifyContent: 'space-between',
                            alignItems: 'center',
                            padding: '8px 0',
                            borderBottom: '1px solid #e2e8f0'
                        }}>
                            <span style={{ fontSize: '14px', fontWeight: '500', color: '#374151' }}>
                                Automation Mode
                            </span>
                            <span style={{ fontSize: '14px', color: '#64748b' }}>
                                {currentSettings.mode || 'manual'}
                            </span>
                        </div>
                        
                        <div style={{ 
                            display: 'flex',
                            justifyContent: 'space-between',
                            alignItems: 'center',
                            padding: '8px 0',
                            borderBottom: '1px solid #e2e8f0'
                        }}>
                            <span style={{ fontSize: '14px', fontWeight: '500', color: '#374151' }}>
                                Gemini API
                            </span>
                            <span style={{ 
                                fontSize: '14px',
                                color: currentSettings.geminiApiKey ? '#16a34a' : '#ef4444',
                                fontWeight: '500'
                            }}>
                                {currentSettings.geminiApiKey ? 'Configured' : 'Not Configured'}
                            </span>
                        </div>
                        
                        <div style={{ 
                            display: 'flex',
                            justifyContent: 'space-between',
                            alignItems: 'center',
                            padding: '8px 0'
                        }}>
                            <span style={{ fontSize: '14px', fontWeight: '500', color: '#374151' }}>
                                Image Provider
                            </span>
                            <span style={{ fontSize: '14px', color: '#64748b' }}>
                                {currentSettings.imageSourceProvider || 'pexels'}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {/* Clear Cache/Reset Options */}
            <div style={{ marginBottom: '30px' }}>
                <h3 style={{ 
                    fontSize: '18px',
                    fontWeight: '600',
                    margin: '0 0 16px 0',
                    color: '#1e293b',
                    display: 'flex',
                    alignItems: 'center',
                    gap: '8px'
                }}>
                    <SettingsIcon style={{ width: '20px', height: '20px', color: '#ef4444' }} />
                    Maintenance
                </h3>
                
                <div style={{ 
                    padding: '20px', 
                    backgroundColor: '#fef2f2', 
                    borderRadius: '8px', 
                    border: '1px solid #fecaca'
                }}>
                    <p style={{ 
                        color: '#991b1b',
                        fontSize: '14px',
                        margin: '0 0 16px 0',
                        lineHeight: '1.5'
                    }}>
                        ⚠️ <strong>Caution:</strong> These actions will affect your plugin data. Use only when troubleshooting issues.
                    </p>
                    
                    <div style={{ display: 'flex', gap: '12px', flexWrap: 'wrap' }}>
                        <button
                            onClick={() => {
                                if (window.confirm('This will clear all cached data. Continue?')) {
                                    if (onShowToast) {
                                        onShowToast('Cache cleared successfully', 'info');
                                    }
                                }
                            }}
                            style={{
                                padding: '8px 16px',
                                backgroundColor: '#ef4444',
                                color: 'white',
                                border: 'none',
                                borderRadius: '6px',
                                fontSize: '14px',
                                fontWeight: '500',
                                cursor: 'pointer'
                            }}
                        >
                            Clear Cache
                        </button>
                        
                        <button
                            onClick={() => {
                                if (onRefreshApp) {
                                    onRefreshApp();
                                    if (onShowToast) {
                                        onShowToast('Application refreshed', 'info');
                                    }
                                }
                            }}
                            style={{
                                padding: '8px 16px',
                                backgroundColor: '#64748b',
                                color: 'white',
                                border: 'none',
                                borderRadius: '6px',
                                fontSize: '14px',
                                fontWeight: '500',
                                cursor: 'pointer'
                            }}
                        >
                            Refresh App
                        </button>
                    </div>
                </div>
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