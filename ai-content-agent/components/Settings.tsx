
import React, { useState, useEffect, useCallback, useRef } from 'react';
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

// EMERGENCY FIX: CollapsibleSection component definition
interface CollapsibleSectionProps {
    id: string;
    title: string;
    description: string;
    icon: React.ReactNode;
    defaultOpen?: boolean;
    children: React.ReactNode;
}

const CollapsibleSection: React.FC<CollapsibleSectionProps> = ({ 
    id, title, description, icon, defaultOpen = false, children 
}) => {
    const [isOpen, setIsOpen] = useState(defaultOpen);
    
    return (
        <div className="aca-card" style={{ margin: '0 0 24px 0' }}>
            <button
                onClick={() => setIsOpen(!isOpen)}
                style={{
                    width: '100%',
                    padding: '20px',
                    border: 'none',
                    background: 'transparent',
                    textAlign: 'left',
                    cursor: 'pointer',
                    display: 'flex',
                    alignItems: 'center',
                    gap: '12px'
                }}
                aria-expanded={isOpen}
                aria-controls={`section-content-${id}`}
            >
                <div style={{
                    width: '40px',
                    height: '40px',
                    background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                    borderRadius: '10px',
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                    flexShrink: 0
                }}>
                    {icon}
                </div>
                <div style={{ flex: 1 }}>
                    <h3 style={{ margin: 0, fontSize: '18px', fontWeight: '600', color: '#1a202c' }}>
                        {title}
                    </h3>
                    <p style={{ margin: '4px 0 0 0', fontSize: '14px', color: '#64748b', lineHeight: '1.4' }}>
                        {description}
                    </p>
                </div>
                <div style={{ 
                    marginLeft: 'auto',
                    fontSize: '16px',
                    color: '#64748b',
                    transform: isOpen ? 'rotate(90deg)' : 'rotate(0deg)',
                    transition: 'transform 0.2s ease'
                }}>
                    ▶
                </div>
            </button>
            {isOpen && (
                <div 
                    id={`section-content-${id}`}
                    style={{ 
                        padding: '0 20px 20px 20px',
                        borderTop: '1px solid #e2e8f0'
                    }}
                >
                    {children}
                </div>
            )}
        </div>
    );
};

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
            className="aca-card"
            style={{
                margin: 0,
                border: '2px solid',
                borderColor: isChecked ? '#0073aa' : '#ccd0d4',
                background: isChecked ? '#f0f6fc' : '#ffffff',
                boxShadow: isChecked ? '0 2px 4px rgba(0, 0, 0, 0.1)' : 'none',
                cursor: 'pointer'
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
                    <h4 className="aca-card-title" style={{ marginBottom: '8px' }}>
                        {title}
                    </h4>
                    <p className="aca-page-description" style={{ margin: 0 }}>
                        {description}
                    </p>
                </div>
            </div>
        </label>
    );
};

const IntegrationCard: React.FC<{ 
    title: string; 
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

export const Settings: React.FC<SettingsProps> = ({ settings, onSaveSettings, onRefreshApp, onShowToast, openSection }) => {
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
    // Active tab state for vertical tab navigation
    const [activeTab, setActiveTab] = useState<string>('license');

    // Race condition protection - track active async operations
    const mountedRef = useRef(true);
    const activeRequestsRef = useRef(new Set<string>());
    
    // Cleanup on unmount
    useEffect(() => {
        return () => {
            mountedRef.current = false;
            activeRequestsRef.current.clear();
        };
    }, []);

    // Safe async state updater to prevent race conditions
    const safeAsyncUpdate = useCallback((requestId: string, updateFunction: () => void) => {
        if (mountedRef.current && !activeRequestsRef.current.has(requestId)) {
            updateFunction();
        }
    }, []);

    // Track async operations to prevent overlapping requests
    const startAsyncOperation = useCallback((requestId: string) => {
        activeRequestsRef.current.add(requestId);
    }, []);

    const endAsyncOperation = useCallback((requestId: string) => {
        activeRequestsRef.current.delete(requestId);
    }, []);

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
                // Keep default inactive status
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

    // Load GSC auth status on component mount
    useEffect(() => {
        const loadGscAuthStatus = async () => {
            if (!window.acaData) {
                console.error('ACA: WordPress data not available');
                return;
            }
            
            try {
                const response = await fetch(window.acaData.api_url + 'gsc/auth-status', {
                    headers: { 'X-WP-Nonce': window.acaData.nonce }
                });
                const status = await response.json();
                setGscAuthStatus(status);
            } catch (error) {
                console.error('Failed to load GSC auth status:', error);
            }
        };
        
        loadGscAuthStatus();
        fetchSeoPlugins();
        loadLicenseStatus();
    }, []);
    
    // Load license status
    const loadLicenseStatus = async () => {
        try {
            const status = await licenseApi.getStatus();
            setLicenseStatus(status);
        } catch (error) {
            console.error('Failed to load license status:', error);
        }
    };
    
    // Verify license key
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
                
                if (onShowToast) {
                    onShowToast('License deactivated successfully. Pro features are now disabled.', 'success');
                } else {
                    alert('License deactivated successfully. Pro features are now disabled.');
                }
                // Refresh app state instead of reloading page
                if (onRefreshApp) {
                    setTimeout(onRefreshApp, 100);
                }
            } else {
                if (onShowToast) {
                    onShowToast('Failed to deactivate license. Please try again.', 'error');
                } else {
                    alert('Failed to deactivate license. Please try again.');
                }
            }
        } catch (error) {
            console.error('License deactivation failed:', error);
            if (onShowToast) {
                onShowToast('License deactivation failed. Please try again.', 'error');
            } else {
                alert('License deactivation failed. Please try again.');
            }
        } finally {
            setIsVerifyingLicense(false);
        }
    };

    const handleLicenseVerification = async () => {
        if (!licenseKey.trim()) {
            if (onShowToast) {
                onShowToast('Please enter a license key', 'warning');
            } else {
                alert('Please enter a license key');
            }
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
                setLicenseKey(''); // Clear the input
                
                // Update settings to reflect pro status
                const updatedSettings = { ...settings, is_pro: true };
                setCurrentSettings(updatedSettings);
                
                // Save settings asynchronously without blocking
                try {
                    await onSaveSettings(updatedSettings);
                } catch (saveError) {
                    console.error('Settings save error:', saveError);
                    // Continue anyway, the license is still valid
                }
                
                if (onShowToast) {
                    onShowToast('License verified successfully! Pro features are now active.', 'success');
                } else {
                    alert('License verified successfully! Pro features are now active.');
                }
                // Refresh app state instead of reloading page
                if (onRefreshApp) {
                    setTimeout(onRefreshApp, 100);
                }
            } else {
                if (onShowToast) {
                    onShowToast('Invalid license key. Please check and try again.', 'error');
                } else {
                    alert('Invalid license key. Please check and try again.');
                }
            }
        } catch (error) {
            console.error('License verification failed:', error);
            let errorMessage = 'License verification failed. Please try again.';
            
            // Try to extract more specific error information
            if (error && typeof error === 'object') {
                if (error.message) {
                    errorMessage = `License verification failed: ${error.message}`;
                } else if (error.data && error.data.message) {
                    errorMessage = `License verification failed: ${error.data.message}`;
                }
            }
            
            if (onShowToast) {
                onShowToast(errorMessage, 'error');
            } else {
                alert(errorMessage);
            }
        } finally {
            setIsVerifyingLicense(false);
        }
    };

    useEffect(() => {
        setCurrentSettings(settings);
    }, [settings]);

    // Update settings when license status changes
    useEffect(() => {
        if (licenseStatus.is_active && !currentSettings.is_pro) {
            setCurrentSettings(prev => ({ ...prev, is_pro: true }));
        } else if (!licenseStatus.is_active && currentSettings.is_pro) {
            setCurrentSettings(prev => ({ ...prev, is_pro: false }));
        }
    }, [licenseStatus.is_active, currentSettings.is_pro]);

    const isDirty = JSON.stringify(currentSettings) !== JSON.stringify(settings);

    const handleSettingChange = (field: keyof AppSettings, value: any) => {
        setCurrentSettings(prev => ({ ...prev, [field]: value }));
    };
    
    // Helper function to check if Pro features are available
    const isProActive = () => {
        return currentSettings.is_pro || licenseStatus.is_active;
    };

    const handleModeChange = (mode: AutomationMode) => {
        // Prevent selection of pro modes without active license
        if ((mode === 'semi-automatic' || mode === 'full-automatic') && !isProActive()) {
            if (onShowToast) {
                onShowToast('This automation mode requires a Pro license. Please upgrade or activate your license to use this feature.', 'warning');
            } else {
                alert('This automation mode requires a Pro license. Please upgrade or activate your license to use this feature.');
            }
            return;
        }
        
        handleSettingChange('mode', mode);
        if (mode !== 'full-automatic') {
            handleSettingChange('autoPublish', false);
        }
    };

    const fetchSeoPlugins = useCallback(async () => {
        const requestId = 'fetchSeoPlugins';
        
        // Prevent overlapping requests
        if (activeRequestsRef.current.has(requestId)) {
            console.log('ACA: SEO plugins fetch already in progress, skipping...');
            return;
        }
        
        startAsyncOperation(requestId);
        
        try {
            safeAsyncUpdate(requestId, () => setSeoPluginsLoading(true));
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
                
                // Safe state updates to prevent race conditions
                safeAsyncUpdate(requestId, () => {
                    setDetectedSeoPlugins(data.detected_plugins || []);
                    
                    // Auto-update settings based on detected plugins
                    if (data.detected_plugins && data.detected_plugins.length > 0) {
                        // Use the first detected plugin as the active one
                        const firstPlugin = data.detected_plugins[0];
                        if (currentSettings.seoPlugin === 'none') {
                            handleSettingChange('seoPlugin', firstPlugin.plugin);
                        }
                    }
                });
            } else {
                const errorText = await response.text();
                console.error('ACA: Failed to fetch SEO plugins:', response.status, errorText);
            }
        } catch (error) {
            console.error('ACA: Error fetching SEO plugins:', error);
        } finally {
            safeAsyncUpdate(requestId, () => setSeoPluginsLoading(false));
            endAsyncOperation(requestId);
        }
    }, [safeAsyncUpdate, startAsyncOperation, endAsyncOperation, currentSettings.seoPlugin]);

    const handleAutoDetectSeo = () => {
        setIsDetectingSeo(true);
        fetchSeoPlugins().finally(() => {
            setIsDetectingSeo(false);
        });
    };

    const handleGSCConnect = async () => {
        // Check if credentials are set
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
            const response = await fetch(window.acaData.api_url + 'gsc/connect', {
                method: 'POST',
                headers: { 'X-WP-Nonce': window.acaData.nonce }
            });
            const data = await response.json();
            
            if (data.auth_url) {
                // Redirect to Google OAuth
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
            const response = await fetch(window.acaData.api_url + 'gsc/disconnect', {
                method: 'POST',
                headers: {
                    'X-WP-Nonce': window.acaData.nonce,
                    'Content-Type': 'application/json'
                }
            });
            const data = await response.json();
            
            if (data.success) {
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

    const handleSave = () => {
        setIsSaving(true);
        setTimeout(() => {
            onSaveSettings(currentSettings);
            setIsSaving(false);
        }, 700);
    };

    // Tab definitions with icons and descriptions
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
        },
        {
            id: 'integrations',
            title: 'Integrations & Services',
            description: 'Connect external services and APIs',
            icon: <Settings as SettingsIcon style={{ width: '18px', height: '18px', color: 'white' }} />
        },
        {
            id: 'content',
            title: 'Content & SEO Settings',
            description: 'Configure content generation and SEO optimization',
            icon: <Image style={{ width: '18px', height: '18px', color: 'white' }} />
        },
        {
            id: 'advanced',
            title: 'Advanced Settings',
            description: 'Developer tools and advanced configuration',
            icon: <Settings as SettingsIcon style={{ width: '18px', height: '18px', color: 'white' }} />
        }
    ];

    // Get gradient color for each tab
    const getGradientColor = (tabId: string) => {
        switch (tabId) {
            case 'license': return 'linear-gradient(135deg, #3b82f6, #1d4ed8)'; // Blue
            case 'automation': return 'linear-gradient(135deg, #f59e0b, #d97706)'; // Orange
            case 'integrations': return 'linear-gradient(135deg, #10b981, #059669)'; // Green
            case 'content': return 'linear-gradient(135deg, #8b5cf6, #7c3aed)'; // Purple
            case 'advanced': return 'linear-gradient(135deg, #ef4444, #dc2626)'; // Red
            default: return 'linear-gradient(135deg, #6b7280, #4b5563)'; // Gray
        }
    };
    
    const isImageSourceConfigured = currentSettings.imageSourceProvider === 'ai' ||
        (currentSettings.imageSourceProvider === 'pexels' && !!currentSettings.pexelsApiKey) ||
        (currentSettings.imageSourceProvider === 'unsplash' && !!currentSettings.unsplashApiKey) ||
        (currentSettings.imageSourceProvider === 'pixabay' && !!currentSettings.pixabayApiKey);

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

            {/* 1. License Activation - First Priority */}
            <CollapsibleSection
                id="license"
                title="Pro License Activation"
                description="Unlock advanced features and automation capabilities"
                icon={<Shield style={{ width: '18px', height: '18px', color: 'white' }} />}
                defaultOpen={true}
            >

                
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
                                    minWidth: '140px',
                                    backgroundColor: '#dc3545',
                                    borderColor: '#dc3545',
                                    color: '#ffffff'
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
            </CollapsibleSection>

            {/* 2. Automation Mode - Core Functionality */}
            <CollapsibleSection
                id="automation"
                title="Automation Mode"
                description="Configure how AI Content Agent creates and publishes content automatically"
                icon={<Zap style={{ width: '18px', height: '18px', color: 'white' }} />}
                defaultOpen={false}
            >
            {isLoadingLicenseStatus ? (
                <div style={{ padding: '20px', textAlign: 'center', color: '#666' }}>
                    Loading license status...
                </div>
            ) : isProActive() ? (
                <div>
                    <p className="aca-page-description">
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
                    <div className="aca-card" style={{
                        margin: 0,
                        border: '2px solid',
                        borderColor: currentSettings.mode === 'semi-automatic' ? '#0073aa' : '#ccd0d4',
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
                                <h4 className="aca-card-title" style={{ marginBottom: '8px' }}>
                                    Semi-Automatic Mode
                                </h4>
                                <p className="aca-page-description" style={{ margin: 0 }}>
                                    The AI automatically generates new ideas periodically. You choose which ideas to turn into drafts.
                                </p>
                            </div>
                        </label>
                        
                        {currentSettings.mode === 'semi-automatic' && (
                            <div className="aca-form-group" style={{ 
                                paddingLeft: '30px', 
                                paddingTop: '20px', 
                                marginTop: '20px', 
                                borderTop: '1px solid #e0e0e0',
                                marginBottom: 0
                            }}>
                                <label className="aca-label" htmlFor="semi-auto-frequency">Idea Generation Frequency</label>
                                <select 
                                    id="semi-auto-frequency"
                                    className="aca-input" 
                                    value={currentSettings.semiAutoIdeaFrequency || 'weekly'} 
                                    onChange={(e) => handleSettingChange('semiAutoIdeaFrequency', e.target.value)}
                                    style={{ marginTop: '5px' }}
                                >
                                    <option value="daily">Daily - Generate new ideas every day</option>
                                    <option value="weekly">Weekly - Generate new ideas every week</option>
                                    <option value="monthly">Monthly - Generate new ideas every month</option>
                                </select>
                                <p className="aca-page-description" style={{ marginTop: '5px', margin: '5px 0 0 0' }}>
                                    How often should the AI automatically generate new content ideas?
                                </p>
                            </div>
                        )}
                    </div>
                    
                    {/* Full Automatic with Auto-Publish Option */}
                    <div className="aca-card" style={{
                        margin: 0,
                        border: '2px solid',
                        borderColor: currentSettings.mode === 'full-automatic' ? '#0073aa' : '#ccd0d4',
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
                                <h4 className="aca-card-title" style={{ marginBottom: '8px' }}>
                                    Full-Automatic Mode (Set & Forget)
                                </h4>
                                <p className="aca-page-description" style={{ margin: 0 }}>
                                    The AI handles everything: generates ideas, picks the best ones, and creates drafts automatically.
                                </p>
                            </div>
                        </label>
                        
                        {currentSettings.mode === 'full-automatic' && (
                            <div style={{ 
                                paddingLeft: '30px', 
                                paddingTop: '20px', 
                                marginTop: '20px', 
                                borderTop: '1px solid #e0e0e0',
                                marginBottom: 0
                            }}>
                                {/* Daily Post Count */}
                                <div className="aca-form-group" style={{ marginBottom: '20px' }}>
                                    <label className="aca-label" htmlFor="daily-post-count">Daily Post Count</label>
                                    <select 
                                        id="daily-post-count"
                                        className="aca-input" 
                                        value={currentSettings.fullAutoDailyPostCount || 1} 
                                        onChange={(e) => handleSettingChange('fullAutoDailyPostCount', parseInt(e.target.value))}
                                        style={{ marginTop: '5px' }}
                                    >
                                        <option value={1}>1 post per day</option>
                                        <option value={2}>2 posts per day</option>
                                        <option value={3}>3 posts per day</option>
                                        <option value={5}>5 posts per day</option>
                                    </select>
                                    <p className="aca-page-description" style={{ marginTop: '5px', margin: '5px 0 0 0' }}>
                                        How many posts should be created daily in full-automatic mode?
                                    </p>
                                </div>

                                {/* Publishing Frequency */}
                                <div className="aca-form-group" style={{ marginBottom: '20px' }}>
                                    <label className="aca-label" htmlFor="publish-frequency">Publishing Frequency</label>
                                    <select 
                                        id="publish-frequency"
                                        className="aca-input" 
                                        value={currentSettings.fullAutoPublishFrequency || 'daily'} 
                                        onChange={(e) => handleSettingChange('fullAutoPublishFrequency', e.target.value)}
                                        style={{ marginTop: '5px' }}
                                    >
                                        <option value="hourly">Every hour - Publish posts throughout the day</option>
                                        <option value="daily">Daily - Publish once per day</option>
                                        <option value="weekly">Weekly - Publish once per week</option>
                                    </select>
                                    <p className="aca-page-description" style={{ marginTop: '5px', margin: '5px 0 0 0' }}>
                                        How often should created drafts be published automatically?
                                    </p>
                                </div>

                                {/* Auto-Publish Checkbox */}
                                <div className="aca-form-group" style={{ marginBottom: 0 }}>
                                    <label htmlFor="auto-publish" style={{ display: 'flex', alignItems: 'flex-start', cursor: 'pointer', gap: '12px' }}>
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
                </div>
            </div>
            ) : (
                <UpgradePrompt 
                    title="Advanced Automation Modes"
                    description="Unlock Semi-Automatic and Full-Automatic modes to automate your content creation workflow"
                    features={[
                        "Semi-Automatic: Automated idea generation with manual publishing",
                        "Full-Automatic: Complete automation from idea to published post",
                        "Advanced scheduling and frequency controls",
                        "Auto-publish with customizable timing"
                    ]}
                />
            )}
            </CollapsibleSection>

            {/* 3. Integrations - External Services */}
            <CollapsibleSection
                id="integrations"
                title="Integrations & Services"
                description="Connect to external services and configure how content is generated and optimized"
                icon={<Shield style={{ width: '18px', height: '18px', color: 'white' }} />}
                defaultOpen={false}
            >
                
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
                        isConfigured={isImageSourceConfigured}
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
                                        name="image-source-provider" 
                                        value={provider} 
                                        checked={currentSettings.imageSourceProvider === provider} 
                                        onChange={(e) => handleSettingChange('imageSourceProvider', e.target.value as ImageSourceProvider)} 
                                        style={{ display: 'none' }}
                                    />
                                    {provider === 'ai' ? 'AI Generated' : provider}
                                </label>
                           ))}
                        </div>

                        {/* Provider-specific settings */}
                        {currentSettings.imageSourceProvider === 'ai' && (
                            <div className="aca-fade-in">
                                <div className="aca-form-group">
                                    <label htmlFor="ai-image-style" className="aca-label">AI Image Style</label>
                                    <select 
                                        id="ai-image-style" 
                                        value={currentSettings.aiImageStyle} 
                                        onChange={(e) => handleSettingChange('aiImageStyle', e.target.value as AiImageStyle)} 
                                        className="aca-select"
                                        style={{ maxWidth: '200px' }}
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
                                        placeholder="Enter your Google Cloud Project ID" 
                                        value={currentSettings.googleCloudProjectId || ''} 
                                        onChange={e => handleSettingChange('googleCloudProjectId', e.target.value)} 
                                        className="aca-input"
                                    />
                                    <p className="aca-page-description" style={{ marginTop: '8px', fontSize: '13px' }}>
                                        Required for AI image generation using Google's Imagen API
                                    </p>
                                </div>
                                
                                <div className="aca-form-group">
                                    <label htmlFor="google-cloud-location" className="aca-label">Google Cloud Location</label>
                                    <select 
                                        id="google-cloud-location" 
                                        value={currentSettings.googleCloudLocation || 'us-central1'} 
                                        onChange={(e) => handleSettingChange('googleCloudLocation', e.target.value)} 
                                        className="aca-select"
                                        style={{ maxWidth: '200px' }}
                                    >
                                        <option value="us-central1">us-central1</option>
                                        <option value="us-east1">us-east1</option>
                                        <option value="us-west1">us-west1</option>
                                        <option value="europe-west1">europe-west1</option>
                                        <option value="asia-southeast1">asia-southeast1</option>
                                    </select>
                                    <p className="aca-page-description" style={{ marginTop: '8px', fontSize: '13px' }}>
                                        Choose the Google Cloud region closest to your users
                                    </p>
                                </div>
                                
                                <a 
                                    href="https://cloud.google.com/vertex-ai/generative-ai/docs/image/overview" 
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
                                    → Learn how to set up Google Cloud Vertex AI for Imagen
                                </a>
                            </div>
                        )}
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
                                <a 
                                    href="https://www.pexels.com/api/" 
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
                                    → Get your Pexels API key
                                </a>
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
                                <a 
                                    href="https://unsplash.com/developers" 
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
                                    → Get your Unsplash Access key
                                </a>
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
                                <a 
                                    href="https://pixabay.com/api/docs/" 
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
                                    → Get your Pixabay API key
                                </a>
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
                                <span className="aca-spinner"></span>
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
                                            const isPremium = plugin.pro || plugin.premium;

                                            return (
                                                <div key={plugin.plugin} style={{ 
                                                    padding: '16px', 
                                                    backgroundColor: colors.bg, 
                                                    borderRadius: '8px', 
                                                    border: `1px solid ${colors.border}`,
                                                    position: 'relative'
                                                }}>
                                                    <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: '8px' }}>
                                                        <div style={{ display: 'flex', alignItems: 'center', gap: '8px' }}>
                                                            <span style={{ fontSize: '20px' }}>{getPluginIcon(plugin.plugin)}</span>
                                                            <div>
                                                                <div style={{ display: 'flex', alignItems: 'center', gap: '8px', flexWrap: 'wrap' }}>
                                                                    <strong style={{ color: colors.text, fontSize: '15px' }}>{plugin.name}</strong>
                                                                    <span style={{ 
                                                                        color: '#6b7280', 
                                                                        fontSize: '13px',
                                                                        backgroundColor: 'rgba(255,255,255,0.7)',
                                                                        padding: '2px 6px',
                                                                        borderRadius: '4px'
                                                                    }}>
                                                                        v{plugin.version}
                                                                    </span>
                                                                    {isPremium && (
                                                                        <span style={{ 
                                                                            backgroundColor: '#7c3aed', 
                                                                            color: 'white', 
                                                                            padding: '2px 6px', 
                                                                            borderRadius: '4px', 
                                                                            fontSize: '11px',
                                                                            fontWeight: '600',
                                                                            textTransform: 'uppercase'
                                                                        }}>
                                                                            {plugin.pro ? 'PRO' : 'PREMIUM'}
                                                                        </span>
                                                                    )}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <span style={{ 
                                                            backgroundColor: '#22c55e', 
                                                            color: 'white', 
                                                            padding: '4px 8px', 
                                                            borderRadius: '12px', 
                                                            fontSize: '12px',
                                                            fontWeight: '600',
                                                            display: 'flex',
                                                            alignItems: 'center',
                                                            gap: '4px'
                                                        }}>
                                                            <span style={{ fontSize: '10px' }}>●</span>
                                                            ACTIVE
                                                        </span>
                                                    </div>
                                                    
                                                    <div style={{ marginBottom: '12px' }}>
                                                        <p style={{ margin: '0 0 8px 0', fontSize: '13px', color: colors.text, lineHeight: '1.4' }}>
                                                            Automatic integration includes: SEO titles, meta descriptions, focus keywords, 
                                                            {isPremium && ' advanced features,'} social media tags, and schema markup.
                                                        </p>
                                                    </div>

                                                    <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(120px, 1fr))', gap: '8px', fontSize: '12px' }}>
                                                        <div style={{ display: 'flex', alignItems: 'center', gap: '4px' }}>
                                                            <span style={{ color: '#22c55e' }}>✓</span>
                                                            <span>Meta Fields</span>
                                                        </div>
                                                        <div style={{ display: 'flex', alignItems: 'center', gap: '4px' }}>
                                                            <span style={{ color: '#22c55e' }}>✓</span>
                                                            <span>Social Media</span>
                                                        </div>
                                                        <div style={{ display: 'flex', alignItems: 'center', gap: '4px' }}>
                                                            <span style={{ color: '#22c55e' }}>✓</span>
                                                            <span>Schema Markup</span>
                                                        </div>
                                                        {isPremium && (
                                                            <div style={{ display: 'flex', alignItems: 'center', gap: '4px' }}>
                                                                <span style={{ color: '#7c3aed' }}>★</span>
                                                                <span>Premium Features</span>
                                                            </div>
                                                        )}
                                                    </div>
                                                </div>
                                            );
                                        })}
                                    </div>
                                </div>

                                <div style={{ 
                                    padding: '12px 16px', 
                                    backgroundColor: '#f9fafb', 
                                    borderRadius: '6px', 
                                    marginBottom: '16px',
                                    border: '1px solid #e5e7eb'
                                }}>
                                    <h4 style={{ margin: '0 0 8px 0', fontSize: '14px', fontWeight: '600', color: '#374151' }}>
                                        📊 Integration Features
                                    </h4>
                                    <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(200px, 1fr))', gap: '8px', fontSize: '13px', color: '#6b7280' }}>
                                        <div>• Automatic SEO title optimization</div>
                                        <div>• Meta description generation</div>
                                        <div>• Focus keyword assignment</div>
                                        <div>• OpenGraph social media tags</div>
                                        <div>• Twitter Card integration</div>
                                        <div>• Schema markup (Article/BlogPosting)</div>
                                        <div>• Primary category assignment</div>
                                        <div>• Canonical URL management</div>
                                    </div>
                                </div>

                                <button 
                                    onClick={handleAutoDetectSeo} 
                                    disabled={isDetectingSeo} 
                                    className="aca-button secondary"
                                    style={{ width: '100%', justifyContent: 'center' }}
                                >
                                    {isDetectingSeo && <span className="aca-spinner"></span>}
                                    {isDetectingSeo ? "Re-detecting SEO plugins..." : "🔄 Refresh Detection"}
                                </button>
                            </div>
                        ) : (
                            <div>
                                <div style={{ 
                                    padding: '20px', 
                                    backgroundColor: '#fef3c7', 
                                    borderRadius: '8px', 
                                    marginBottom: '20px',
                                    border: '1px solid #f59e0b',
                                    textAlign: 'center'
                                }}>
                                    <div style={{ fontSize: '48px', marginBottom: '12px' }}>⚠️</div>
                                    <h3 style={{ margin: '0 0 8px 0', color: '#92400e', fontSize: '16px' }}>
                                        No SEO Plugins Detected
                                    </h3>
                                    <p style={{ margin: '0 0 16px 0', color: '#92400e', fontSize: '14px', lineHeight: '1.4' }}>
                                        Install one of the supported SEO plugins to enable automatic SEO data integration 
                                        for your AI-generated content.
                                    </p>
                                </div>

                                <div style={{ marginBottom: '20px' }}>
                                    <h4 style={{ margin: '0 0 12px 0', fontSize: '16px', fontWeight: '600', color: '#374151' }}>
                                        🔧 Supported SEO Plugins
                                    </h4>
                                    <div style={{ display: 'grid', gap: '12px' }}>
                                        {[
                                            { 
                                                name: 'RankMath SEO', 
                                                icon: '🏆', 
                                                description: 'Advanced SEO plugin with comprehensive features and Pro version support',
                                                link: 'https://wordpress.org/plugins/seo-by-rank-math/',
                                                color: { bg: '#fef3c7', border: '#f59e0b', text: '#92400e' }
                                            },
                                            { 
                                                name: 'Yoast SEO', 
                                                icon: '🟢', 
                                                description: 'Popular SEO plugin with Premium features and readability analysis',
                                                link: 'https://wordpress.org/plugins/wordpress-seo/',
                                                color: { bg: '#dcfce7', border: '#22c55e', text: '#166534' }
                                            },
                                            { 
                                                name: 'All in One SEO (AIOSEO)', 
                                                icon: '🔵', 
                                                description: 'Comprehensive SEO solution with Pro features and social media integration',
                                                link: 'https://wordpress.org/plugins/all-in-one-seo-pack/',
                                                color: { bg: '#dbeafe', border: '#3b82f6', text: '#1e40af' }
                                            }
                                        ].map((plugin, index) => (
                                            <div key={index} style={{ 
                                                padding: '16px', 
                                                backgroundColor: plugin.color.bg, 
                                                borderRadius: '8px', 
                                                border: `1px solid ${plugin.color.border}`
                                            }}>
                                                <div style={{ display: 'flex', alignItems: 'flex-start', gap: '12px' }}>
                                                    <span style={{ fontSize: '24px', flexShrink: 0 }}>{plugin.icon}</span>
                                                    <div style={{ flex: 1 }}>
                                                        <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', marginBottom: '6px' }}>
                                                            <strong style={{ color: plugin.color.text, fontSize: '15px' }}>
                                                                {plugin.name}
                                                            </strong>
                                                            <a 
                                                                href={plugin.link} 
                                                                target="_blank" 
                                                                rel="noopener noreferrer"
                                                                style={{ 
                                                                    color: plugin.color.text, 
                                                                    textDecoration: 'none',
                                                                    fontSize: '12px',
                                                                    fontWeight: '500',
                                                                    padding: '4px 8px',
                                                                    backgroundColor: 'rgba(255,255,255,0.7)',
                                                                    borderRadius: '4px'
                                                                }}
                                                            >
                                                                Install →
                                                            </a>
                                                        </div>
                                                        <p style={{ margin: '0', fontSize: '13px', color: plugin.color.text, lineHeight: '1.4' }}>
                                                            {plugin.description}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                </div>

                                <button 
                                    onClick={handleAutoDetectSeo} 
                                    disabled={isDetectingSeo} 
                                    className="aca-button primary"
                                    style={{ width: '100%', justifyContent: 'center' }}
                                >
                                    {isDetectingSeo && <span className="aca-spinner"></span>}
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
                            isConfigured={!!currentSettings.searchConsoleUser}
                        >
                        {/* Dependencies Status */}
                        <div className="aca-form-group">
                            <div id="aca-dependencies-status">
                                {/* This will be populated by PHP */}
                            </div>
                        </div>
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
                                    <Google />
                                </div>
                                <div>
                                    <h4 className="aca-stat-title">Connection Status</h4>
                                    {gscAuthStatus?.authenticated ? (
                                        <p className="aca-stat-count" style={{ color: '#00a32a' }}>
                                            Connected as {gscAuthStatus.user_email}
                                        </p>
                                    ) : (
                                        <p className="aca-stat-count">
                                            Use search data to generate strategic content ideas
                                        </p>
                                    )}
                                </div>
                            </div>
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
                                    {isConnecting && <span className="aca-spinner"></span>}
                                    {isConnecting ? 'Connecting...' : 'Connect'}
                                </button>
                            )}
                        </div>
                    </IntegrationCard>
                    ) : (
                        <UpgradePrompt
                            title="Google Search Console Integration"
                            description="Connect your GSC account to generate content ideas based on your search performance data and improve SEO targeting."
                            features={[
                                "Data-driven content ideas from your search queries",
                                "Target keywords you're already ranking for",
                                "Identify content gaps and opportunities",
                                "Improve content relevance and SEO performance"
                            ]}
                        />
                    )}
                </div>
            </CollapsibleSection>
            
            {/* 4. Content Analysis Settings - AI Optimization */}
            <CollapsibleSection
                id="content"
                title="Content Analysis Settings"
                description="Configure how often the AI should analyze your content to update the style guide"
                icon={<SettingsIcon style={{ width: '18px', height: '18px', color: 'white' }} />}
                defaultOpen={false}
            >
                
                <div className="aca-form-group">
                    <label className="aca-label" htmlFor="analyze-frequency">Content Analysis Frequency</label>
                    <select 
                        id="analyze-frequency"
                        className="aca-input" 
                        value={currentSettings.analyzeContentFrequency || 'manual'} 
                        onChange={(e) => handleSettingChange('analyzeContentFrequency', e.target.value)}
                        style={{ marginTop: '5px' }}
                    >
                        <option value="manual">Manual - Only when you click the analyze button</option>
                        <option value="daily">Daily - Analyze content automatically every day</option>
                        <option value="weekly">Weekly - Analyze content automatically every week</option>
                        <option value="monthly">Monthly - Analyze content automatically every month</option>
                    </select>
                    <p className="aca-page-description" style={{ marginTop: '5px', margin: '5px 0 0 0' }}>
                        How often should the AI automatically analyze your site content to update the style guide? Manual mode gives you full control.
                    </p>
                </div>
            </CollapsibleSection>
            
            {/* 5. Debug Panel for Automation Testing - For Developers */}
            <CollapsibleSection
                id="advanced"
                title="Automation Debug Panel"
                description="For developers and advanced users - test automation functionality and debug issues"
                icon={<SettingsIcon style={{ width: '18px', height: '18px', color: 'white' }} />}
                defaultOpen={false}
            >
                <div className="aca-alert info" style={{ marginBottom: '20px' }}>
                    <p style={{ margin: 0, fontSize: '14px' }}>
                        <strong>🛠️ For Developers & Advanced Users:</strong> This panel is designed for testing and debugging automation features. 
                        Use these tools to manually trigger automation tasks, check cron job status, and troubleshoot issues. 
                        Regular users typically don't need to use this panel.
                    </p>
                </div>
                <p className="aca-page-description">
                    Test automation functionality and check cron status. Click the buttons below to manually trigger automation tasks or check their status.
                </p>
                
                <div style={{ display: 'flex', gap: '10px', marginBottom: '20px' }}>
                    <button 
                        className="aca-action-button"
                        onClick={() => {
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
                                if (onShowToast) {
                                    onShowToast('Debug info logged to console', 'info');
                                } else {
                                    alert('Debug info logged to console');
                                }
                            });
                        }}
                    >
                        Check Automation Status
                    </button>
                    
                    <button 
                        className="aca-action-button"
                        onClick={() => {
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
                                if (onShowToast) {
                                    onShowToast(data.message || 'Semi-auto cron triggered', 'success');
                                } else {
                                    alert(data.message || 'Semi-auto cron triggered');
                                }
                            });
                        }}
                    >
                        Test Semi-Auto Cron
                    </button>
                    
                    <button 
                        className="aca-action-button"
                        onClick={() => {
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
                                if (onShowToast) {
                                    onShowToast(data.message || 'Full-auto cron triggered', 'success');
                                } else {
                                    alert(data.message || 'Full-auto cron triggered');
                                }
                            });
                        }}
                    >
                        Test Full-Auto Cron
                    </button>
                                </div>
            </CollapsibleSection>

            {/* Save Button */}
            <div style={{ 
                display: 'flex', 
                justifyContent: 'space-between', 
                alignItems: 'center',
                paddingTop: '25px',
                borderTop: '1px solid #f0f0f1'
            }}>
                {isDirty && (
                    <div className="aca-alert warning" style={{ 
                        display: 'flex',
                        alignItems: 'center',
                        fontSize: '13px', 
                        fontWeight: '500',
                        gap: '8px',
                        padding: '8px 12px',
                        margin: 0
                    }}>
                        <div style={{ 
                            width: '8px', 
                            height: '8px', 
                            borderRadius: '50%', 
                            background: '#dba617' 
                        }}></div>
                        You have unsaved changes
                    </div>
                )}
                <div style={{ marginLeft: 'auto' }}>
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
        </div>
    );
};