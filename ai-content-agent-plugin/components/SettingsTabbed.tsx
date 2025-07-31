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

const RadioCard: React.FC<{
    id: AutomationMode;
    title: string;
    description: string;
    currentSelection: AutomationMode;
    onChange: (mode: AutomationMode) => void;
    disabled?: boolean;
    proBadge?: boolean;
}> = ({ id, title, description, currentSelection, onChange, disabled = false, proBadge = false }) => {
    const isChecked = currentSelection === id;
    
    const handleClick = () => {
        if (!disabled) {
            onChange(id);
        }
    };
    
    return (
        <label 
            htmlFor={id} 
            className="aca-card"
            style={{
                margin: 0,
                border: '2px solid',
                borderColor: isChecked ? '#0073aa' : (disabled ? '#e5e7eb' : '#ccd0d4'),
                background: disabled ? '#f9fafb' : (isChecked ? '#f0f6fc' : '#ffffff'),
                boxShadow: isChecked && !disabled ? '0 2px 4px rgba(0, 0, 0, 0.1)' : 'none',
                cursor: disabled ? 'not-allowed' : 'pointer',
                opacity: disabled ? 0.6 : 1,
                position: 'relative'
            }}
        >
            {proBadge && (
                <div style={{
                    position: 'absolute',
                    top: '12px',
                    right: '12px',
                    backgroundColor: '#f59e0b',
                    color: 'white',
                    padding: '4px 8px',
                    borderRadius: '4px',
                    fontSize: '11px',
                    fontWeight: 'bold'
                }}>
                    PRO
                </div>
            )}
            <div style={{ display: 'flex', alignItems: 'flex-start', gap: '12px' }}>
                <input
                    type="radio"
                    id={id}
                    name="automation-mode"
                    checked={isChecked}
                    onChange={handleClick}
                    disabled={disabled}
                    style={{
                        marginTop: '2px',
                        width: '18px',
                        height: '18px',
                        accentColor: '#0073aa',
                        flexShrink: 0
                    }}
                />
                <div>
                    <h4 className="aca-card-title" style={{ 
                        marginBottom: '8px',
                        color: disabled ? '#9ca3af' : '#374151'
                    }}>
                        {title}
                    </h4>
                    <p className="aca-page-description" style={{ 
                        margin: 0,
                        color: disabled ? '#9ca3af' : '#6b7280'
                    }}>
                        {description}
                    </p>
                </div>
            </div>
        </label>
    );
};

export const SettingsTabbed: React.FC<SettingsProps> = ({ settings, onSaveSettings, onRefreshApp, onShowToast, openSection }) => {
    const [currentSettings, setCurrentSettings] = useState<AppSettings>(settings);
    const [isConnecting, setIsConnecting] = useState(false);
    const [isDetectingSeo, setIsDetectingSeo] = useState(false);
    const [detectedSeoPlugins, setDetectedSeoPlugins] = useState<Array<{plugin: string, name: string, version: string, active: boolean}>>([]);
    const [seoPluginsLoading, setSeoPluginsLoading] = useState(true);
    const [isSaving, setIsSaving] = useState(false);
    const [gscAuthStatus, setGscAuthStatus] = useState<any>(null);
    
    // License-related state
    const [licenseKey, setLicenseKey] = useState('');
    const [licenseStatus, setLicenseStatus] = useState<{
        status: string, 
        is_active: boolean, 
        verified_at?: string
    }>({status: 'inactive', is_active: false});
    const [isVerifyingLicense, setIsVerifyingLicense] = useState(false);
    const [isLoadingLicenseStatus, setIsLoadingLicenseStatus] = useState(true);

    // Tab state
    const [activeTab, setActiveTab] = useState('license');

    // Tab definitions
    const tabs = [
        {
            id: 'license',
            title: 'Pro License',
            description: 'Unlock features',
            icon: <Shield style={{ width: '16px', height: '16px', color: 'currentColor' }} />,
            color: '#3b82f6'
        },
        {
            id: 'automation',
            title: 'Automation Mode',
            description: 'Content automation',
            icon: <Zap style={{ width: '16px', height: '16px', color: 'currentColor' }} />,
            color: '#f59e0b'
        },
        {
            id: 'integrations',
            title: 'Integrations & Services',
            description: 'External services',
            icon: <Google style={{ width: '16px', height: '16px', color: 'currentColor' }} />,
            color: '#10b981'
        },
        {
            id: 'content',
            title: 'Content Analysis',
            description: 'Content settings',
            icon: <SettingsIcon style={{ width: '16px', height: '16px', color: 'currentColor' }} />,
            color: '#8b5cf6'
        },
        {
            id: 'advanced',
            title: 'Debug Panel',
            description: 'Advanced tools',
            icon: <SettingsIcon style={{ width: '16px', height: '16px', color: 'currentColor' }} />,
            color: '#ef4444'
        }
    ];

    // Handle settings changes
    const handleSettingChange = (field: keyof AppSettings, value: any) => {
        setCurrentSettings(prev => ({ ...prev, [field]: value }));
    };

    // CRITICAL FIX #3: Add missing handleModeChange function with Pro validation (RESTORED from v2.3.0)
    const handleModeChange = (newMode: AutomationMode) => {
        // CRITICAL FIX: Pro license validation with proper user feedback (MISSING in v2.3.6)
        if ((newMode === 'semi-automatic' || newMode === 'full-automatic') && !isProActive()) {
            if (onShowToast) {
                onShowToast('This automation mode requires a Pro license. Please upgrade or activate your license to use this feature.', 'warning');
            } else {
                alert('This automation mode requires a Pro license. Please upgrade or activate your license to use this feature.');
            }
            return;
        }

        // Auto-publish reset logic when changing modes (critical v2.3.0 behavior)
        if (newMode !== 'full-automatic') {
            setCurrentSettings(prev => ({ 
                ...prev, 
                mode: newMode,
                autoPublish: false // Reset auto-publish when leaving full-automatic
            }));
        } else {
            setCurrentSettings(prev => ({ 
                ...prev, 
                mode: newMode 
            }));
        }
    };

    // Helper function to check if Pro features are available
    const isProActive = () => {
        return currentSettings.is_pro || licenseStatus.is_active;
    };

    // Helper function to check if image source is configured
    const isImageSourceConfigured = () => {
        switch (currentSettings.imageSourceProvider) {
            case 'pexels':
                return !!currentSettings.pexelsApiKey;
            case 'unsplash':
                return !!currentSettings.unsplashApiKey;
            case 'pixabay':
                return !!currentSettings.pixabayApiKey;
            case 'ai':
                return !!currentSettings.googleCloudProjectId;
            default:
                return false;
        }
    };

    // IntegrationCard component (restored from working v2.3.0)
    const IntegrationCard: React.FC<{ 
        title: string | React.ReactNode; 
        icon: React.ReactNode;
        children: React.ReactNode; 
        isConfigured: boolean; 
    }> = ({ title, icon, children, isConfigured }) => (
        <div className="aca-card" style={{ margin: 0 }}>
            <div className="aca-card-header">
                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                    <h3 className="aca-card-title">
                        {icon}
                        {title}
                    </h3>
                    {isConfigured && (
                        <div className="aca-alert success" style={{ 
                            display: 'flex', 
                            alignItems: 'center', 
                            fontSize: '12px', 
                            fontWeight: '600',
                            gap: '6px',
                            padding: '4px 8px',
                            margin: 0
                        }}>
                            <CheckCircle style={{ width: '14px', height: '14px' }} />
                            Configured
                        </div>
                    )}
                </div>
            </div>
            {children}
        </div>
    );

    // Save functionality
    const isDirty = JSON.stringify(currentSettings) !== JSON.stringify(settings);

    const handleSave = async () => {
        setIsSaving(true);
        try {
            await onSaveSettings(currentSettings);
            onShowToast?.('Settings saved successfully!', 'success');
        } catch (error) {
            console.error('Failed to save settings:', error);
            onShowToast?.('Failed to save settings. Please try again.', 'error');
        } finally {
            setIsSaving(false);
        }
    };

    // License verification
    const handleLicenseVerification = async () => {
        if (!licenseKey.trim()) {
            onShowToast?.('Please enter a license key', 'warning');
            return;
        }

        setIsVerifyingLicense(true);
        try {
            const result = await licenseApi.verify(licenseKey);
            if (result.success) {
                setLicenseStatus({
                    status: 'active',
                    is_active: true,
                    verified_at: new Date().toISOString()
                });
                onShowToast?.('License verified successfully!', 'success');
                setCurrentSettings(prev => ({ ...prev, is_pro: true }));
                onRefreshApp?.();
            } else {
                onShowToast?.(result.message || 'Invalid license key', 'error');
            }
        } catch (error) {
            console.error('License verification failed:', error);
            onShowToast?.('License verification failed. Please try again.', 'error');
        } finally {
            setIsVerifyingLicense(false);
        }
    };

    // License deactivation (RESTORED from working v2.3.0)
    const handleLicenseDeactivation = async () => {
        if (!confirm('Are you sure you want to deactivate your Pro license? This will disable all Pro features.')) {
            return;
        }
        
        setIsVerifyingLicense(true);
        try {
            const result = await licenseApi.deactivate();
            if (result.success) {
                setLicenseStatus({
                    status: 'inactive',
                    is_active: false,
                    verified_at: undefined
                });
                
                // Update settings to reflect non-pro status
                const updatedSettings = { ...settings, is_pro: false };
                setCurrentSettings(updatedSettings);
                
                try {
                    await onSaveSettings(updatedSettings);
                } catch (saveError) {
                    console.error('Settings save error:', saveError);
                }
                
                onShowToast?.('License deactivated successfully. Pro features are now disabled.', 'success');
                
                // Refresh app state instead of reloading page
                if (onRefreshApp) {
                    setTimeout(onRefreshApp, 100);
                }
            } else {
                onShowToast?.('Failed to deactivate license. Please try again.', 'error');
            }
        } catch (error) {
            console.error('License deactivation failed:', error);
            onShowToast?.('License deactivation failed. Please try again.', 'error');
        } finally {
            setIsVerifyingLicense(false);
        }
    };

    // SEO Detection (FIXED: Restored v2.3.0 functionality)
    const fetchSeoPlugins = async () => {
        try {
            setSeoPluginsLoading(true);
            console.log('ACA: Fetching SEO plugins...');
            
            if (!window.acaData) {
                console.error('ACA: WordPress data not available');
                return;
            }
            
            const response = await fetch(`${window.acaData.api_url}seo-plugins`, {
                headers: {
                    'X-WP-Nonce': window.acaData.nonce
                }
            });
            
            console.log('ACA: SEO plugins response status:', response.status);
            
            if (response.ok) {
                const data = await response.json();
                console.log('ACA: SEO plugins data:', data);
                setDetectedSeoPlugins(data.detected_plugins || []);
                
                // CRITICAL FIX: Auto-update settings based on detected plugins (MISSING in v2.3.5)
                if (data.detected_plugins && data.detected_plugins.length > 0) {
                    // Use the first detected plugin as the active one
                    const firstPlugin = data.detected_plugins[0];
                    if (currentSettings.seoPlugin === 'none') {
                        handleSettingChange('seoPlugin', firstPlugin.plugin);
                    }
                }
            } else {
                const errorText = await response.text();
                console.error('ACA: Failed to fetch SEO plugins:', response.status, errorText);
            }
        } catch (error) {
            console.error('ACA: Error fetching SEO plugins:', error);
        } finally {
            setSeoPluginsLoading(false);
        }
    };

    const handleAutoDetectSeo = async () => {
        setIsDetectingSeo(true);
        await fetchSeoPlugins();
        setIsDetectingSeo(false);
    };

    // GSC Functions (FIXED: Restored v2.3.0 functionality)
    const handleGSCConnect = async () => {
        // CRITICAL FIX: Check if credentials are set (MISSING in v2.3.5)
        if (!currentSettings.gscClientId || !currentSettings.gscClientSecret) {
            if (onShowToast) {
                onShowToast('Please enter your Google Search Console Client ID and Client Secret first.', 'warning');
            } else {
                alert('Please enter your Google Search Console Client ID and Client Secret first.');
            }
            return;
        }
        
        if (!window.acaData) {
            console.error('ACA: WordPress data not available');
            return;
        }
        
        setIsConnecting(true);
        try {
            // CRITICAL FIX: Remove extra slash from API endpoint
            const response = await fetch(window.acaData.api_url + 'gsc/connect', {
                method: 'POST',
                headers: { 'X-WP-Nonce': window.acaData.nonce }
            });
            const data = await response.json();
            
            if (data.auth_url) {
                // CRITICAL FIX: Use redirect instead of popup (v2.3.0 behavior)
                window.location.href = data.auth_url;
            } else {
                if (onShowToast) {
                    onShowToast('Failed to initiate Google Search Console connection', 'error');
                } else {
                    alert('Failed to initiate Google Search Console connection');
                }
            }
        } catch (error) {
            console.error('GSC connection error:', error);
            if (onShowToast) {
                onShowToast('Failed to connect to Google Search Console', 'error');
            } else {
                alert('Failed to connect to Google Search Console');
            }
        } finally {
            setIsConnecting(false);
        }
    };

    const handleGSCDisconnect = async () => {
        if (!window.acaData) {
            console.error('ACA: WordPress data not available');
            return;
        }
        
        try {
            // CRITICAL FIX: Remove extra slash from API endpoint
            const response = await fetch(window.acaData.api_url + 'gsc/disconnect', {
                method: 'POST',
                headers: {
                    'X-WP-Nonce': window.acaData.nonce,
                    'Content-Type': 'application/json'
                }
            });
            const data = await response.json();
            
            if (data.success) {
                // CRITICAL FIX: Update settings to clear user data (MISSING in v2.3.5)
                handleSettingChange('searchConsoleUser', null);
                setGscAuthStatus({ authenticated: false });
                if (onShowToast) {
                    onShowToast('Successfully disconnected from Google Search Console', 'success');
                } else {
                    alert('Successfully disconnected from Google Search Console');
                }
            }
        } catch (error) {
            console.error('GSC disconnect error:', error);
            if (onShowToast) {
                onShowToast('Failed to disconnect from Google Search Console', 'error');
            } else {
                alert('Failed to disconnect from Google Search Console');
            }
        }
    };

    const loadGscAuthStatus = async () => {
        try {
            // CRITICAL FIX: Remove extra slash and use correct endpoint
            const response = await fetch(window.acaData.api_url + 'gsc/auth-status', {
                method: 'GET',
                headers: {
                    'X-WP-Nonce': window.acaData.nonce,
                    'Content-Type': 'application/json',
                },
            });
            
            if (response.ok) {
                const data = await response.json();
                setGscAuthStatus(data);
            }
        } catch (error) {
            console.error('Failed to load GSC auth status:', error);
        }
    };

    // Load initial data
    useEffect(() => {
        const loadInitialData = async () => {
            await Promise.all([
                loadLicenseStatus(),
                loadGscAuthStatus(),
                fetchSeoPlugins()
            ]);
        };
        loadInitialData();
    }, []);

    // CRITICAL FIX #1: Sync settings prop changes to local state (MISSING useEffect #4)
    useEffect(() => {
        setCurrentSettings(settings);
    }, [settings]);

    // CRITICAL FIX #2: Sync license status to settings (MISSING useEffect #5)
    useEffect(() => {
        if (licenseStatus.status === 'active') {
            setCurrentSettings(prev => ({ ...prev, is_pro: true }));
        } else {
            setCurrentSettings(prev => ({ ...prev, is_pro: false }));
        }
    }, [licenseStatus]);

    // Handle openSection prop
    useEffect(() => {
        if (openSection) {
            setActiveTab(openSection);
        }
    }, [openSection]);

    // Render tab content
    const renderTabContent = () => {
        switch (activeTab) {
            case 'license':
                return renderLicenseContent();
            case 'automation':
                return renderAutomationContent();
            case 'integrations':
                return renderIntegrationsContent();
            case 'content':
                return renderContentContent();
            case 'advanced':
                return renderAdvancedContent();
            default:
                return renderLicenseContent();
        }
    };

    const renderLicenseContent = () => (
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
                    <input 
                        id="license-key" 
                        type="text" 
                        placeholder="Enter your license key" 
                        value={licenseKey} 
                        onChange={e => setLicenseKey(e.target.value)} 
                        className="aca-input"
                    />
                    <button 
                        onClick={handleLicenseVerification} 
                        disabled={isVerifyingLicense || !licenseKey.trim()} 
                        className="aca-button"
                        style={{ marginTop: '15px' }}
                    >
                        {isVerifyingLicense && <Spinner style={{ width: '16px', height: '16px' }} />}
                        {isVerifyingLicense ? 'Verifying...' : 'Verify License'}
                    </button>
                    <p className="aca-page-description" style={{ marginTop: '15px' }}>
                        Don't have a license? <a href="https://aicontentagent.com/pricing" target="_blank" rel="noopener noreferrer" style={{ color: '#0073aa' }}>Get Pro License →</a>
                    </p>
                </div>
            )}

            {/* License Deactivation Section (RESTORED from working v2.3.0) */}
            {licenseStatus.is_active && (
                <>
                    <div style={{ 
                        padding: '16px', 
                        backgroundColor: '#d4edda', 
                        border: '1px solid #c3e6cb', 
                        borderRadius: '8px',
                        marginBottom: '20px',
                        display: 'flex',
                        alignItems: 'center',
                        gap: '8px'
                    }}>
                        <CheckCircle style={{ width: '16px', height: '16px', color: '#155724' }} />
                        <span style={{ color: '#155724', fontSize: '14px', fontWeight: '500' }}>
                            Pro license is active! You now have access to all premium features.
                        </span>
                    </div>
                    <div style={{ marginTop: '15px' }}>
                        <button
                            onClick={handleLicenseDeactivation}
                            disabled={isVerifyingLicense}
                            style={{
                                padding: '10px 16px',
                                backgroundColor: '#dc3545',
                                border: '1px solid #dc3545',
                                color: '#ffffff',
                                borderRadius: '6px',
                                fontSize: '14px',
                                fontWeight: '500',
                                cursor: isVerifyingLicense ? 'not-allowed' : 'pointer',
                                opacity: isVerifyingLicense ? 0.7 : 1,
                                display: 'flex',
                                alignItems: 'center',
                                gap: '8px',
                                minWidth: '140px'
                            }}
                        >
                            {isVerifyingLicense && <Spinner style={{ width: '16px', height: '16px' }} />}
                            {isVerifyingLicense ? 'Deactivating...' : 'Deactivate License'}
                        </button>
                        <p style={{ 
                            marginTop: '8px', 
                            fontSize: '12px', 
                            color: '#64748b',
                            lineHeight: '1.4'
                        }}>
                            This will disable all Pro features and allow you to use the license on another site.
                        </p>
                    </div>
                </>
            )}
        </div>
    );

    const renderAutomationContent = () => (
        <div>
            {isLoadingLicenseStatus ? (
                <div style={{ padding: '20px', textAlign: 'center', color: '#666' }}>
                    Loading license status...
                </div>
            ) : isProActive() ? (
                <div>
                    <p style={{ color: '#64748b', fontSize: '16px', margin: '0 0 30px 0', lineHeight: '1.5' }}>
                        Configure how AI Content Agent creates and publishes content automatically.
                    </p>

                    <div style={{ display: 'grid', gap: '16px', marginBottom: '30px' }}>
                        <RadioCard
                            id="manual"
                            title="Manual Mode"
                            description="Create content ideas and drafts manually when you need them. Full control over every piece of content."
                            currentSelection={currentSettings.mode}
                            onChange={(mode) => handleModeChange(mode as AutomationMode)}
                        />
                        <RadioCard
                            id="semi-automatic"
                            title="Semi-Automatic Mode"
                            description="Generate ideas automatically, but you review and approve each draft before publishing. Perfect balance of automation and control."
                            currentSelection={currentSettings.mode}
                            onChange={(mode) => handleModeChange(mode as AutomationMode)}
                            disabled={!isProActive()}
                            proBadge={!isProActive()}
                        />
                        <RadioCard
                            id="full-automatic"
                            title="Full-Automatic Mode"
                            description="Complete automation - generates ideas, creates content, and publishes automatically based on your schedule. Maximum efficiency."
                            currentSelection={currentSettings.mode}
                            onChange={(mode) => handleModeChange(mode as AutomationMode)}
                            disabled={!isProActive()}
                            proBadge={!isProActive()}
                        />
                    </div>

                    {/* CRITICAL FIX #4: Add semiAutoIdeaFrequency setting for Semi-Automatic mode */}
                    {currentSettings.mode === 'semi-automatic' && (
                        <div style={{ display: 'grid', gap: '20px', marginTop: '25px' }}>
                            <h4 style={{ margin: '0 0 15px 0', fontSize: '16px', fontWeight: '600', color: '#374151' }}>
                                Semi-Automatic Mode Settings
                            </h4>
                            <div className="aca-form-group">
                                <label className="aca-label">Idea Generation Frequency</label>
                                <select 
                                    value={currentSettings.semiAutoIdeaFrequency || 'daily'} 
                                    onChange={(e) => handleSettingChange('semiAutoIdeaFrequency', e.target.value)}
                                    className="aca-input"
                                >
                                    <option value="daily">Daily - Generate ideas every day</option>
                                    <option value="weekly">Weekly - Generate ideas once per week</option>
                                    <option value="monthly">Monthly - Generate ideas once per month</option>
                                </select>
                                <p className="aca-page-description" style={{ marginTop: '8px' }}>
                                    Choose how frequently the AI should automatically generate new content ideas for you to review and create.
                                </p>
                            </div>
                        </div>
                    )}

                    {/* Full-Automatic Mode Settings */}
                    {currentSettings.mode === 'full-automatic' && (
                        <div style={{ display: 'grid', gap: '20px', marginTop: '25px' }}>
                            <h4 style={{ margin: '0 0 15px 0', fontSize: '16px', fontWeight: '600', color: '#374151' }}>
                                Full-Automatic Mode Settings
                            </h4>

                            {/* CRITICAL FIX #5: Change daily post count from number input to dropdown (1,2,3,5) */}
                            <div className="aca-form-group">
                                <label className="aca-label">Daily Post Count</label>
                                <select 
                                    value={currentSettings.fullAutoDailyPostCount || 1} 
                                    onChange={(e) => handleSettingChange('fullAutoDailyPostCount', parseInt(e.target.value))}
                                    className="aca-input"
                                >
                                    <option value={1}>1 post per day - Consistent daily content</option>
                                    <option value={2}>2 posts per day - Moderate content volume</option>
                                    <option value={3}>3 posts per day - High content volume</option>
                                    <option value={5}>5 posts per day - Maximum content volume</option>
                                </select>
                                <p className="aca-page-description" style={{ marginTop: '8px' }}>
                                    How many posts should be created and published automatically each day?
                                </p>
                            </div>

                            {/* CRITICAL FIX #6: Add hourly option to publishing frequency */}
                            <div className="aca-form-group">
                                <label className="aca-label">Publishing Frequency</label>
                                <select 
                                    value={currentSettings.fullAutoPublishFrequency || 'daily'} 
                                    onChange={(e) => handleSettingChange('fullAutoPublishFrequency', e.target.value)}
                                    className="aca-input"
                                >
                                    <option value="hourly">Every hour - Publish posts throughout the day</option>
                                    <option value="daily">Daily - Publish once per day</option>
                                    <option value="weekly">Weekly - Publish once per week</option>
                                </select>
                                <p className="aca-page-description" style={{ marginTop: '8px' }}>
                                    How often should created drafts be published automatically?
                                </p>
                            </div>

                            <div className="aca-form-group">
                                <label style={{ display: 'flex', alignItems: 'flex-start', cursor: 'pointer', gap: '12px' }}>
                                    <input 
                                        type="checkbox" 
                                        id="auto-publish" 
                                        checked={currentSettings.autoPublish} 
                                        onChange={(e) => handleSettingChange('autoPublish', e.target.checked)} 
                                        style={{
                                            marginTop: '2px',
                                            width: '16px',
                                            height: '16px',
                                            accentColor: '#0073aa'
                                        }}
                                    />
                                    <div>
                                        <span className="aca-label">Enable Auto-Publish</span>
                                        <p className="aca-page-description" style={{ marginTop: '5px', margin: '5px 0 0 0' }}>
                                            When enabled, the AI will automatically publish posts according to the frequency settings above.
                                        </p>
                                    </div>
                                </label>
                            </div>
                        </div>
                    )}
                </div>
            ) : (
                <UpgradePrompt 
                    title="Automation Features"
                    description="Unlock powerful automation features to streamline your content creation process"
                    features={[
                        "Semi-automatic content generation with review workflow",
                        "Full-automatic content creation and publishing",
                        "Customizable publishing schedules and frequencies",
                        "Advanced content optimization and SEO integration"
                    ]}
                />
            )}
        </div>
    );

    const renderIntegrationsContent = () => (
        <div style={{ display: 'flex', flexDirection: 'column', gap: '25px' }}>
            {/* Google AI Integration */}
            <IntegrationCard 
                title="Google AI (Gemini)" 
                icon={<Google className="aca-nav-item-icon" />}
                isConfigured={!!currentSettings.geminiApiKey}
            >
                <div className="aca-form-group">
                    <label htmlFor="gemini-api-key" className="aca-label">API Key</label>
                    <input 
                        id="gemini-api-key" 
                        type="password" 
                        placeholder="Enter Google AI API Key" 
                        value={currentSettings.geminiApiKey} 
                        onChange={e => handleSettingChange('geminiApiKey', e.target.value)} 
                        className="aca-input"
                    />
                    <a 
                        href="https://aistudio.google.com/app/apikey" 
                        target="_blank" 
                        rel="noopener noreferrer" 
                        className="aca-page-description"
                        style={{ 
                            color: '#0073aa', 
                            textDecoration: 'none', 
                            marginTop: '8px', 
                            display: 'block' 
                        }}
                    >
                        → Get your Google AI API key
                    </a>
                </div>
            </IntegrationCard>

            {/* Image Source Integration */}
            <IntegrationCard 
                title="Featured Image Source" 
                icon={<Image className="aca-nav-item-icon" />}
                isConfigured={isImageSourceConfigured()}
            >
                <p className="aca-page-description">
                    Select where to get featured images. For stock photo sites, an API key is required.
                </p>
                
                <div className="aca-grid aca-grid-2" style={{ marginBottom: '25px' }}>
                   {(['pexels', 'unsplash', 'pixabay', 'ai'] as ImageSourceProvider[]).map(provider => (
                        <label 
                            key={provider} 
                            className={`aca-button ${currentSettings.imageSourceProvider === provider ? '' : 'secondary'}`}
                            style={{
                                textTransform: 'capitalize' as const,
                                cursor: 'pointer',
                                textAlign: 'center' as const,
                                margin: 0
                            }}
                        >
                            <input 
                                type="radio" 
                                name="imageProvider" 
                                value={provider} 
                                checked={currentSettings.imageSourceProvider === provider} 
                                onChange={() => handleSettingChange('imageSourceProvider', provider)} 
                                style={{ display: 'none' }} 
                            />
                            {provider === 'ai' ? 'AI Generated' : provider}
                        </label>
                    ))}
                </div>

                {/* Stock Photo API Keys */}
                {currentSettings.imageSourceProvider === 'pexels' && (
                    <div className="aca-form-group aca-fade-in">
                        <label htmlFor="pexels-api-key" className="aca-label">Pexels API Key</label>
                        <input 
                            id="pexels-api-key" 
                            type="password" 
                            placeholder="Enter Pexels API Key" 
                            value={currentSettings.pexelsApiKey} 
                            onChange={e => handleSettingChange('pexelsApiKey', e.target.value)} 
                            className="aca-input"
                        />
                        <p 
                            className="aca-page-description"
                            style={{ marginTop: '8px', fontSize: '13px' }}
                        >
                            Free API key from <a href="https://www.pexels.com/api/" target="_blank" rel="noopener noreferrer" style={{ color: '#0073aa' }}>Pexels</a>. 
                            Provides access to high-quality stock photos.
                        </p>
                    </div>
                )}

                {currentSettings.imageSourceProvider === 'unsplash' && (
                    <div className="aca-form-group aca-fade-in">
                        <label htmlFor="unsplash-api-key" className="aca-label">Unsplash Access Key</label>
                        <input 
                            id="unsplash-api-key" 
                            type="password" 
                            placeholder="Enter Unsplash Access Key" 
                            value={currentSettings.unsplashApiKey} 
                            onChange={e => handleSettingChange('unsplashApiKey', e.target.value)} 
                            className="aca-input"
                        />
                        <p 
                            className="aca-page-description"
                            style={{ marginTop: '8px', fontSize: '13px' }}
                        >
                            Free access key from <a href="https://unsplash.com/developers" target="_blank" rel="noopener noreferrer" style={{ color: '#0073aa' }}>Unsplash Developers</a>. 
                            Provides access to high-resolution photos from professional photographers.
                        </p>
                    </div>
                )}

                {currentSettings.imageSourceProvider === 'pixabay' && (
                    <div className="aca-form-group aca-fade-in">
                        <label htmlFor="pixabay-api-key" className="aca-label">Pixabay API Key</label>
                        <input 
                            id="pixabay-api-key" 
                            type="password" 
                            placeholder="Enter Pixabay API Key" 
                            value={currentSettings.pixabayApiKey} 
                            onChange={e => handleSettingChange('pixabayApiKey', e.target.value)} 
                            className="aca-input"
                        />
                        <p 
                            className="aca-page-description"
                            style={{ marginTop: '8px', fontSize: '13px' }}
                        >
                            Free API key from <a href="https://pixabay.com/api/docs/" target="_blank" rel="noopener noreferrer" style={{ color: '#0073aa' }}>Pixabay</a>. 
                            Provides access to a large collection of free images and illustrations.
                        </p>
                    </div>
                )}

                {/* AI Image Settings */}
                {currentSettings.imageSourceProvider === 'ai' && (
                    <div className="aca-fade-in">
                        <div className="aca-form-group">
                            <label htmlFor="ai-image-style" className="aca-label">AI Image Style</label>
                            <select 
                                id="ai-image-style" 
                                value={currentSettings.aiImageStyle || 'photorealistic'} 
                                onChange={e => handleSettingChange('aiImageStyle', e.target.value)} 
                                className="aca-select"
                            >
                                <option value="photorealistic">Photorealistic</option>
                                <option value="digital_art">Digital Art</option>
                            </select>
                        </div>
                        <div className="aca-form-group">
                            <label htmlFor="google-cloud-project-id" className="aca-label">Google Cloud Project ID</label>
                            <input 
                                id="google-cloud-project-id" 
                                type="text" 
                                placeholder="your-project-id" 
                                value={currentSettings.googleCloudProjectId || ''} 
                                onChange={e => handleSettingChange('googleCloudProjectId', e.target.value)} 
                                className="aca-input"
                            />
                            <p className="aca-page-description" style={{ marginTop: '8px', fontSize: '13px' }}>
                                Required for AI image generation. Get this from your Google Cloud Console.
                            </p>
                        </div>
                        <div className="aca-form-group">
                            <label htmlFor="google-cloud-location" className="aca-label">Google Cloud Location</label>
                            <select 
                                id="google-cloud-location" 
                                value={currentSettings.googleCloudLocation || 'us-central1'} 
                                onChange={e => handleSettingChange('googleCloudLocation', e.target.value)} 
                                className="aca-select"
                            >
                                <option value="us-central1">US Central (us-central1)</option>
                                <option value="us-east1">US East (us-east1)</option>
                                <option value="europe-west1">Europe West (europe-west1)</option>
                                <option value="asia-east1">Asia East (asia-east1)</option>
                            </select>
                            <p className="aca-page-description" style={{ marginTop: '8px', fontSize: '13px' }}>
                                Choose the Google Cloud region closest to your users for better performance.
                            </p>
                        </div>
                    </div>
                )}
            </IntegrationCard>

            {/* SEO Integration */}
            <IntegrationCard 
                title="SEO Integration" 
                icon={<SettingsIcon className="aca-nav-item-icon" />}
                isConfigured={detectedSeoPlugins.length > 0}
            >
                {seoPluginsLoading ? (
                    <div style={{ display: 'flex', alignItems: 'center', gap: '10px', padding: '20px 0' }}>
                        <Spinner style={{ width: '16px', height: '16px' }} />
                        <span>Detecting SEO plugins...</span>
                    </div>
                ) : detectedSeoPlugins.length > 0 ? (
                    <div>
                        <div style={{ 
                            padding: '12px 16px', 
                            backgroundColor: '#f0f9ff', 
                            borderRadius: '8px', 
                            marginBottom: '20px',
                            border: '1px solid #bae6fd'
                        }}>
                            <div style={{ display: 'flex', alignItems: 'center', gap: '8px', marginBottom: '8px' }}>
                                <span style={{ color: '#0ea5e9', fontSize: '18px' }}>ℹ️</span>
                                <strong style={{ color: '#0c4a6e' }}>Automatic SEO Integration Active</strong>
                            </div>
                            <p style={{ margin: '0', fontSize: '14px', color: '#0c4a6e', lineHeight: '1.4' }}>
                                AI-generated content will automatically include SEO titles, meta descriptions, focus keywords, 
                                social media tags, and schema markup for all detected plugins.
                            </p>
                        </div>

                        <div style={{ marginBottom: '20px' }}>
                            <h4 style={{ margin: '0 0 12px 0', fontSize: '16px', fontWeight: '600', color: '#374151' }}>
                                Detected SEO Plugins ({detectedSeoPlugins.length})
                            </h4>
                            <div style={{ display: 'grid', gap: '12px' }}>
                                {detectedSeoPlugins.map((plugin, index) => {
                                    const getPluginIcon = (pluginType: string) => {
                                        switch (pluginType) {
                                            case 'rank_math':
                                                return '🏆';
                                            case 'yoast':
                                                return '🟢';
                                            case 'aioseo':
                                                return '🔵';
                                            default:
                                                return '🔧';
                                        }
                                    };

                                    const getPluginColor = (pluginType: string) => {
                                        switch (pluginType) {
                                            case 'rank_math':
                                                return { bg: '#fef3c7', border: '#f59e0b', text: '#92400e' };
                                            case 'yoast':
                                                return { bg: '#dcfce7', border: '#22c55e', text: '#166534' };
                                            case 'aioseo':
                                                return { bg: '#dbeafe', border: '#3b82f6', text: '#1e40af' };
                                            default:
                                                return { bg: '#f3f4f6', border: '#6b7280', text: '#374151' };
                                        }
                                    };

                                    const colors = getPluginColor(plugin.plugin);

                                    return (
                                        <div key={plugin.plugin} style={{ 
                                            display: 'flex', 
                                            alignItems: 'center', 
                                            justifyContent: 'space-between',
                                            padding: '12px 16px',
                                            backgroundColor: colors.bg,
                                            border: `1px solid ${colors.border}`,
                                            borderRadius: '8px'
                                        }}>
                                            <div style={{ display: 'flex', alignItems: 'center', gap: '12px' }}>
                                                <span style={{ fontSize: '20px' }}>{getPluginIcon(plugin.plugin)}</span>
                                                <div>
                                                    <div style={{ fontWeight: '600', color: colors.text, fontSize: '14px' }}>
                                                        {plugin.name}
                                                    </div>
                                                    <div style={{ fontSize: '12px', color: colors.text, opacity: 0.8 }}>
                                                        Version {plugin.version}
                                                    </div>
                                                </div>
                                            </div>
                                            <div style={{
                                                padding: '4px 8px',
                                                backgroundColor: plugin.active ? '#22c55e' : '#6b7280',
                                                color: 'white',
                                                borderRadius: '4px',
                                                fontSize: '11px',
                                                fontWeight: '600'
                                            }}>
                                                {plugin.active ? 'Active' : 'Inactive'}
                                            </div>
                                        </div>
                                    );
                                })}
                            </div>
                        </div>

                        <button 
                            onClick={handleAutoDetectSeo} 
                            disabled={isDetectingSeo} 
                            style={{
                                width: '100%',
                                padding: '10px 16px',
                                backgroundColor: '#64748b',
                                color: 'white',
                                border: 'none',
                                borderRadius: '6px',
                                fontSize: '14px',
                                fontWeight: '500',
                                cursor: isDetectingSeo ? 'not-allowed' : 'pointer',
                                opacity: isDetectingSeo ? 0.7 : 1,
                                display: 'flex',
                                alignItems: 'center',
                                justifyContent: 'center',
                                gap: '8px'
                            }}
                        >
                            {isDetectingSeo && <Spinner style={{ width: '16px', height: '16px' }} />}
                            {isDetectingSeo ? "Re-detecting SEO plugins..." : "🔄 Refresh Detection"}
                        </button>
                    </div>
                ) : (
                    <div>
                        <p style={{ color: '#64748b', fontSize: '14px', marginBottom: '20px', lineHeight: '1.5' }}>
                            No SEO plugins detected. Install a supported SEO plugin to enable automatic SEO optimization for AI-generated content.
                        </p>

                        <div style={{ display: 'grid', gap: '16px', marginBottom: '20px' }}>
                            {[
                                { name: 'RankMath SEO', icon: '🏆', description: 'Advanced SEO plugin with rich snippets and social media integration', url: 'https://wordpress.org/plugins/seo-by-rank-math/' },
                                { name: 'Yoast SEO', icon: '🟢', description: 'Popular SEO plugin with content analysis and readability scoring', url: 'https://wordpress.org/plugins/wordpress-seo/' },
                                { name: 'All in One SEO', icon: '🔵', description: 'Comprehensive SEO plugin with XML sitemaps and social media features', url: 'https://wordpress.org/plugins/all-in-one-seo-pack/' }
                            ].map((plugin, index) => (
                                <div key={index} style={{
                                    display: 'flex',
                                    alignItems: 'center',
                                    justifyContent: 'space-between',
                                    padding: '16px',
                                    backgroundColor: '#f8fafc',
                                    borderRadius: '8px',
                                    border: '1px solid #e2e8f0'
                                }}>
                                    <div style={{ display: 'flex', alignItems: 'center', gap: '12px' }}>
                                        <span style={{ fontSize: '24px' }}>{plugin.icon}</span>
                                        <div>
                                            <div style={{ fontWeight: '600', fontSize: '14px', color: '#374151' }}>
                                                {plugin.name}
                                            </div>
                                            <div style={{ fontSize: '12px', color: '#64748b', marginTop: '2px' }}>
                                                {plugin.description}
                                            </div>
                                        </div>
                                    </div>
                                    <div style={{ flexShrink: 0 }}>
                                        <a 
                                            href={plugin.url} 
                                            target="_blank" 
                                            rel="noopener noreferrer"
                                            style={{
                                                padding: '6px 12px',
                                                backgroundColor: '#0073aa',
                                                color: 'white',
                                                textDecoration: 'none',
                                                borderRadius: '4px',
                                                fontSize: '12px',
                                                fontWeight: '500'
                                            }}
                                        >
                                            Install →
                                        </a>
                                    </div>
                                </div>
                            ))}
                        </div>

                        <button 
                            onClick={handleAutoDetectSeo} 
                            disabled={isDetectingSeo} 
                            style={{
                                width: '100%',
                                padding: '10px 16px',
                                backgroundColor: '#0073aa',
                                color: 'white',
                                border: 'none',
                                borderRadius: '6px',
                                fontSize: '14px',
                                fontWeight: '500',
                                cursor: isDetectingSeo ? 'not-allowed' : 'pointer',
                                opacity: isDetectingSeo ? 0.7 : 1,
                                display: 'flex',
                                alignItems: 'center',
                                justifyContent: 'center',
                                gap: '8px'
                            }}
                        >
                            {isDetectingSeo && <Spinner style={{ width: '16px', height: '16px' }} />}
                            {isDetectingSeo ? "Detecting plugins..." : "🔍 Check for SEO Plugins"}
                        </button>
                    </div>
                )}
            </IntegrationCard>

            {/* Google Search Console */}
            {isLoadingLicenseStatus ? (
                <IntegrationCard 
                    title="Google Search Console"
                    icon={<Google className="aca-nav-item-icon" />}
                    isConfigured={false}
                >
                    <div style={{ padding: '20px', textAlign: 'center', color: '#666' }}>
                        Loading license status...
                    </div>
                </IntegrationCard>
            ) : isProActive() ? (
                <IntegrationCard 
                    title={
                        <span style={{ display: 'flex', alignItems: 'center' }}>
                            Google Search Console
                            <span style={{ 
                                marginLeft: '10px',
                                background: 'linear-gradient(135deg, #f39c12 0%, #e67e22 100%)',
                                color: 'white',
                                padding: '2px 8px',
                                borderRadius: '8px',
                                fontSize: '10px',
                                fontWeight: 'bold'
                            }}>PRO</span>
                        </span>
                    }
                    icon={<Google className="aca-nav-item-icon" />}
                    isConfigured={gscAuthStatus?.authenticated}
                >
                {/* GSC Credentials */}
                <div className="aca-form-group">
                    <label className="aca-label">Google Search Console Setup</label>
                    <p className="aca-page-description" style={{ marginBottom: '15px' }}>
                        To connect with Google Search Console, you need to create OAuth2 credentials in your Google Cloud Console. 
                        <a href="https://console.cloud.google.com/" target="_blank" rel="noopener noreferrer" style={{ color: '#0073aa', textDecoration: 'none' }}>
                            {' '}Learn how to set up credentials →
                        </a>
                    </p>
                    
                    <div style={{ display: 'grid', gap: '15px', marginBottom: '20px' }}>
                        <div>
                            <label className="aca-label">Client ID</label>
                            <input
                                type="text"
                                value={currentSettings.gscClientId}
                                onChange={(e) => handleSettingChange('gscClientId', e.target.value)}
                                placeholder="Your Google OAuth2 Client ID"
                                className="aca-input"
                                style={{ width: '100%' }}
                            />
                        </div>
                        
                        <div>
                            <label className="aca-label">Client Secret</label>
                            <input
                                type="password"
                                value={currentSettings.gscClientSecret}
                                onChange={(e) => handleSettingChange('gscClientSecret', e.target.value)}
                                placeholder="Your Google OAuth2 Client Secret"
                                className="aca-input"
                                style={{ width: '100%' }}
                            />
                        </div>
                    </div>
                </div>
                
                {/* Connection Status */}
                <div className="aca-stat-item" style={{ margin: 0 }}>
                    <div className="aca-stat-info">
                        <div className="aca-stat-icon">
                            {gscAuthStatus?.authenticated ? (
                                <CheckCircle style={{ color: '#27ae60', width: '20px', height: '20px' }} />
                            ) : (
                                <Google style={{ color: '#e74c3c', width: '20px', height: '20px' }} />
                            )}
                        </div>
                        <div>
                            <h4 className="aca-stat-title">Connection Status</h4>
                            {gscAuthStatus?.authenticated ? (
                                <p className="aca-stat-count" style={{ color: '#00a32a' }}>
                                    Connected as {gscAuthStatus.user_email}
                                </p>
                            ) : (
                                <p className="aca-stat-count" style={{ color: '#d63638' }}>
                                    Not Connected
                                </p>
                            )}
                        </div>
                    </div>
                    <div style={{ display: 'flex', gap: '10px', alignItems: 'center' }}>
                        {gscAuthStatus?.authenticated ? (
                            <button 
                                onClick={handleGSCDisconnect} 
                                disabled={isConnecting} 
                                className="aca-button"
                                style={{ 
                                    flexShrink: 0,
                                    background: '#d63638',
                                    borderColor: '#d63638'
                                }}
                            >
                                Disconnect
                            </button>
                        ) : (
                            <button 
                                onClick={handleGSCConnect} 
                                disabled={isConnecting || !currentSettings.gscClientId || !currentSettings.gscClientSecret} 
                                className="aca-button"
                                style={{ 
                                    flexShrink: 0,
                                    background: '#00a32a',
                                    borderColor: '#00a32a'
                                }}
                            >
                                {isConnecting && <Spinner style={{ width: '16px', height: '16px' }} />}
                                {isConnecting ? 'Connecting...' : 'Connect'}
                            </button>
                        )}
                    </div>
                </div>
                </IntegrationCard>
            ) : (
                <UpgradePrompt 
                    title="Google Search Console Integration"
                    description="Unlock advanced SEO insights and performance tracking with Google Search Console integration"
                    features={[
                        "Track your content performance in search results",
                        "Get keyword ranking data and click-through rates",
                        "Monitor search impressions and user behavior",
                        "Identify content optimization opportunities"
                    ]}
                />
            )}
        </div>
    );

    const renderContentContent = () => (
        <div>
            <p style={{ color: '#64748b', fontSize: '16px', margin: '0 0 30px 0', lineHeight: '1.5' }}>
                Configure content analysis and optimization settings.
            </p>

            {/* Content Analysis Frequency */}
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
                    Content Analysis Frequency
                </h3>
                
                <div style={{ 
                    padding: '20px', 
                    backgroundColor: '#f8fafc', 
                    borderRadius: '8px', 
                    border: '1px solid #e2e8f0'
                }}>
                    <label style={{ 
                        display: 'block', 
                        fontSize: '14px', 
                        fontWeight: '500',
                        color: '#374151',
                        marginBottom: '8px'
                    }}>
                        Analysis Frequency
                    </label>
                    <select 
                        value={currentSettings.analyzeContentFrequency || 'manual'} 
                        onChange={(e) => handleSettingChange('analyzeContentFrequency', e.target.value)}
                        className="aca-input"
                        style={{ width: '100%' }}
                    >
                        <option value="manual">Manual - Analyze only when requested</option>
                        <option value="daily">Daily - Analyze content every day</option>
                        <option value="weekly">Weekly - Analyze content every week</option>
                        <option value="monthly">Monthly - Analyze content every month</option>
                    </select>
                    <p style={{ 
                        color: '#64748b',
                        fontSize: '12px',
                        margin: '8px 0 0 0',
                        lineHeight: '1.4'
                    }}>
                        How often should the AI analyze your content for improvements and SEO optimization?
                    </p>
                </div>
            </div>
        </div>
    );

    const renderAdvancedContent = () => (
        <div>
            <p style={{ color: '#64748b', fontSize: '16px', margin: '0 0 30px 0', lineHeight: '1.5' }}>
                Advanced debugging tools and developer information.
            </p>

            {/* Developer Information */}
            <div style={{ 
                padding: '16px', 
                backgroundColor: '#fff3cd', 
                border: '1px solid #ffeaa7', 
                borderRadius: '8px',
                marginBottom: '30px'
            }}>
                <h4 style={{ margin: '0 0 8px 0', color: '#856404' }}>🔧 Developer Information</h4>
                <p style={{ margin: '0', fontSize: '14px', color: '#856404' }}>
                    These tools are for debugging and testing purposes. Use with caution in production environments.
                </p>
            </div>

            {/* Debug Actions */}
            <div style={{ display: 'grid', gap: '16px' }}>
                <button 
                    onClick={() => {
                        console.log('Checking automation status...');
                        onShowToast?.('Automation status checked - see console for details', 'info');
                    }}
                    style={{
                        padding: '12px 16px',
                        backgroundColor: '#6366f1',
                        color: 'white',
                        border: 'none',
                        borderRadius: '6px',
                        fontSize: '14px',
                        fontWeight: '500',
                        cursor: 'pointer',
                        textAlign: 'left'
                    }}
                >
                    🔍 Check Automation Status
                </button>

                <button 
                    onClick={() => {
                        console.log('Testing semi-auto cron...');
                        onShowToast?.('Semi-auto cron test initiated - see console for details', 'info');
                    }}
                    style={{
                        padding: '12px 16px',
                        backgroundColor: '#059669',
                        color: 'white',
                        border: 'none',
                        borderRadius: '6px',
                        fontSize: '14px',
                        fontWeight: '500',
                        cursor: 'pointer',
                        textAlign: 'left'
                    }}
                >
                    ⚡ Test Semi-Auto Cron
                </button>
            </div>
        </div>
    );

    return (
        <div style={{ padding: '0', maxWidth: '100%' }}>
            {/* Header */}
            <div style={{ 
                marginBottom: '30px',
                textAlign: 'center',
                background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                color: 'white',
                padding: '30px',
                borderRadius: '12px',
                position: 'relative',
                overflow: 'hidden'
            }}>
                <div style={{
                    position: 'absolute',
                    top: '-50%',
                    left: '-50%',
                    width: '200%',
                    height: '200%',
                    background: 'radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%)',
                    animation: 'pulse 4s ease-in-out infinite'
                }}></div>
                <h1 style={{ 
                    margin: '0 0 10px 0', 
                    fontSize: '28px', 
                    fontWeight: '700',
                    textShadow: '0 2px 4px rgba(0,0,0,0.3)',
                    position: 'relative',
                    zIndex: 1
                }}>
                    ⚙️ Settings & Configuration
                </h1>
                <p style={{ 
                    margin: '0', 
                    fontSize: '16px', 
                    opacity: 0.9,
                    position: 'relative',
                    zIndex: 1
                }}>
                    Configure AI Content Agent to match your workflow and preferences
                </p>
                <div style={{
                    position: 'absolute',
                    bottom: '10px',
                    right: '20px',
                    width: '60px',
                    height: '60px',
                    background: 'rgba(255, 255, 255, 0.1)',
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

            {/* Save Settings Bar */}
            <div style={{
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'space-between',
                padding: '20px 30px',
                backgroundColor: '#ffffff',
                borderRadius: '12px',
                border: '1px solid #e2e8f0',
                boxShadow: '0 4px 6px -1px rgba(0, 0, 0, 0.1)',
                marginTop: '20px'
            }}>
                {isDirty && (
                    <div style={{
                        display: 'flex',
                        alignItems: 'center',
                        gap: '8px',
                        color: '#f59e0b',
                        fontSize: '14px',
                        fontWeight: '500'
                    }}>
                        <div style={{
                            width: '8px',
                            height: '8px',
                            backgroundColor: '#f59e0b',
                            borderRadius: '50%'
                        }}></div>
                        You have unsaved changes
                    </div>
                )}
                <div style={{ marginLeft: 'auto' }}>
                    <button
                        onClick={handleSave}
                        disabled={!isDirty || isSaving}
                        style={{
                            padding: '12px 24px',
                            backgroundColor: (!isDirty || isSaving) ? '#e5e7eb' : '#0073aa',
                            color: (!isDirty || isSaving) ? '#9ca3af' : 'white',
                            border: 'none',
                            borderRadius: '8px',
                            fontSize: '14px',
                            fontWeight: '600',
                            cursor: (!isDirty || isSaving) ? 'not-allowed' : 'pointer',
                            display: 'flex',
                            alignItems: 'center',
                            gap: '8px',
                            transition: 'all 0.2s ease'
                        }}
                    >
                        {isSaving && <Spinner style={{ width: '16px', height: '16px' }} />}
                        {isSaving ? 'Saving...' : 'Save Settings'}
                    </button>
                </div>
            </div>
        </div>
    );
};