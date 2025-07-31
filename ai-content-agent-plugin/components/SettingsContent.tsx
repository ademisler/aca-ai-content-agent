import React, { useState, useEffect } from 'react';
import type { AppSettings } from '../types';
import { Target, Search, Google } from './Icons';
import { SettingsLayout } from './SettingsLayout';
import { UpgradePrompt } from './UpgradePrompt';

interface SettingsContentProps {
    settings: AppSettings;
    onSaveSettings: (settings: AppSettings) => void;
    onShowToast: (message: string, type: 'success' | 'error' | 'warning' | 'info') => void;
    isProActive?: boolean;
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

export const SettingsContent: React.FC<SettingsContentProps> = ({ 
    settings, 
    onSaveSettings, 
    onShowToast,
    isProActive 
}) => {
    const [currentSettings, setCurrentSettings] = useState<AppSettings>(settings);
    const [isDirty, setIsDirty] = useState(false);
    const [isSaving, setIsSaving] = useState(false);
    const [detectedSeoPlugins, setDetectedSeoPlugins] = useState<Array<{plugin: string, name: string, version: string, active: boolean}>>([]);
    const [seoPluginsLoading, setSeoPluginsLoading] = useState(true);
    const [isDetectingSeo, setIsDetectingSeo] = useState(false);
    const [gscAuthStatus, setGscAuthStatus] = useState<any>(null);
    const [isConnecting, setIsConnecting] = useState(false);

    const handleSettingChange = (key: keyof AppSettings, value: any) => {
        const updatedSettings = { ...currentSettings, [key]: value };
        setCurrentSettings(updatedSettings);
        setIsDirty(true);
    };

    const handleSave = async () => {
        if (!isDirty) return;
        
        setIsSaving(true);
        try {
            await onSaveSettings(currentSettings);
            setIsDirty(false);
            onShowToast('Content settings saved successfully!', 'success');
        } catch (error) {
            onShowToast('Failed to save content settings', 'error');
        } finally {
            setIsSaving(false);
        }
    };

    // Load SEO plugins and GSC status on component mount
    useEffect(() => {
        const loadSeoPlugins = async () => {
            if (!window.acaData) return;
            
            try {
                const response = await fetch(window.acaData.api_url + 'seo/plugins', {
                    headers: { 'X-WP-Nonce': window.acaData.nonce }
                });
                const data = await response.json();
                console.log('ACA: SEO plugins data:', data);
                if (data.success) {
                    setDetectedSeoPlugins(data.plugins || data.detected_plugins || []);
                } else {
                    // Try legacy format
                    setDetectedSeoPlugins(data.detected_plugins || []);
                }
            } catch (error) {
                console.error('Failed to load SEO plugins:', error);
            } finally {
                setSeoPluginsLoading(false);
            }
        };

        const loadGscStatus = async () => {
            if (!window.acaData) return;
            
            try {
                const response = await fetch(window.acaData.api_url + 'gsc/status', {
                    headers: { 'X-WP-Nonce': window.acaData.nonce }
                });
                const data = await response.json();
                setGscAuthStatus(data);
            } catch (error) {
                console.error('Failed to load GSC status:', error);
            }
        };

        loadSeoPlugins();
        loadGscStatus();
    }, []);

    const handleAutoDetectSeo = async () => {
        if (!window.acaData) return;
        
        setIsDetectingSeo(true);
        try {
            const response = await fetch(window.acaData.api_url + 'seo/plugins', {
                method: 'POST',
                headers: { 'X-WP-Nonce': window.acaData.nonce }
            });
            const data = await response.json();
            console.log('ACA: SEO plugins refresh data:', data);
            if (data.success) {
                setDetectedSeoPlugins(data.plugins || data.detected_plugins || []);
                onShowToast('SEO plugins detection completed!', 'success');
            } else {
                // Try legacy format
                setDetectedSeoPlugins(data.detected_plugins || []);
                if (data.detected_plugins && data.detected_plugins.length > 0) {
                    onShowToast('SEO plugins detection completed!', 'success');
                } else {
                    onShowToast('Failed to detect SEO plugins', 'error');
                }
            }
        } catch (error) {
            console.error('SEO plugin detection error:', error);
            onShowToast('Failed to detect SEO plugins', 'error');
        } finally {
            setIsDetectingSeo(false);
        }
    };

    const handleGSCConnect = async () => {
        if (!window.acaData || !currentSettings.gscClientId || !currentSettings.gscClientSecret) return;
        
        setIsConnecting(true);
        try {
            const response = await fetch(window.acaData.api_url + 'gsc/connect', {
                method: 'POST',
                headers: { 
                    'X-WP-Nonce': window.acaData.nonce,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    client_id: currentSettings.gscClientId,
                    client_secret: currentSettings.gscClientSecret
                })
            });
            const data = await response.json();
            if (data.success && data.auth_url) {
                window.open(data.auth_url, '_blank');
                onShowToast('Opening Google authorization window...', 'info');
            } else {
                onShowToast(data.message || 'Failed to connect to Google Search Console', 'error');
            }
        } catch (error) {
            console.error('GSC connection error:', error);
            onShowToast('Failed to connect to Google Search Console', 'error');
        } finally {
            setIsConnecting(false);
        }
    };

    const handleGSCDisconnect = async () => {
        if (!window.acaData) return;
        
        setIsConnecting(true);
        try {
            const response = await fetch(window.acaData.api_url + 'gsc/disconnect', {
                method: 'POST',
                headers: { 'X-WP-Nonce': window.acaData.nonce }
            });
            const data = await response.json();
            if (data.success) {
                setGscAuthStatus({ authenticated: false });
                onShowToast('Disconnected from Google Search Console', 'success');
            } else {
                onShowToast('Failed to disconnect from Google Search Console', 'error');
            }
        } catch (error) {
            console.error('GSC disconnection error:', error);
            onShowToast('Failed to disconnect from Google Search Console', 'error');
        } finally {
            setIsConnecting(false);
        }
    };

    const saveButton = isDirty ? (
        <button
            onClick={handleSave}
            disabled={isSaving}
            className="aca-button aca-button-primary"
            style={{ minWidth: '120px' }}
        >
            {isSaving ? 'Saving...' : 'Save Changes'}
        </button>
    ) : null;

    return (
        <SettingsLayout
            title="Content & SEO"
            description="Configure content analysis and SEO optimization settings"
            icon={<Target style={{ width: '24px', height: '24px', color: 'white' }} />}
            actions={saveButton}
        >
            {/* Content Analysis Settings */}
            <div className="aca-card" style={{ margin: '0 0 24px 0' }}>
                <div className="aca-card-header">
                    <h3 className="aca-card-title" style={{ display: 'flex', alignItems: 'center', gap: '12px', margin: '0 0 16px 0' }}>
                        <div style={{
                            width: '40px',
                            height: '40px',
                            background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                            borderRadius: '10px',
                            display: 'flex',
                            alignItems: 'center',
                            justifyContent: 'center'
                        }}>
                            <Search style={{ width: '20px', height: '20px', color: 'white' }} />
                        </div>
                        Content Analysis
                    </h3>
                </div>
                <div className="aca-form-group">
                    <label className="aca-label" htmlFor="analyze-frequency">Analysis Frequency</label>
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
            </div>

            {/* SEO Integration */}
            <div className="aca-card" style={{ margin: '0 0 24px 0' }}>
                <div className="aca-card-header">
                    <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '16px' }}>
                        <h3 className="aca-card-title" style={{ display: 'flex', alignItems: 'center', gap: '12px', margin: 0 }}>
                            <div style={{
                                width: '40px',
                                height: '40px',
                                background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                                borderRadius: '10px',
                                display: 'flex',
                                alignItems: 'center',
                                justifyContent: 'center'
                            }}>
                                <Target style={{ width: '20px', height: '20px', color: 'white' }} />
                            </div>
                            SEO Integration
                        </h3>
                        {detectedSeoPlugins.length > 0 && (
                            <div className="aca-alert success" style={{ 
                                display: 'flex', 
                                alignItems: 'center', 
                                fontSize: '12px', 
                                fontWeight: '600',
                                gap: '6px',
                                padding: '4px 8px',
                                margin: 0
                            }}>
                                <div style={{ 
                                    width: '8px', 
                                    height: '8px', 
                                    borderRadius: '50%', 
                                    background: '#22c55e' 
                                }}></div>
                                {detectedSeoPlugins.length} Plugin{detectedSeoPlugins.length > 1 ? 's' : ''} Detected
                            </div>
                        )}
                    </div>
                </div>
                
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
                                            case 'rank_math': return '🏆';
                                            case 'yoast': return '🟢';
                                            case 'aioseo': return '🔵';
                                            default: return '🔧';
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
                                            padding: '16px', 
                                            backgroundColor: colors.bg, 
                                            borderRadius: '8px', 
                                            border: `1px solid ${colors.border}`,
                                            position: 'relative'
                                        }}>
                                            <div style={{ display: 'flex', alignItems: 'center', gap: '12px' }}>
                                                <span style={{ fontSize: '24px' }}>{getPluginIcon(plugin.plugin)}</span>
                                                <div style={{ flex: 1 }}>
                                                    <div style={{ display: 'flex', alignItems: 'center', gap: '8px', marginBottom: '4px' }}>
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
                                                    </div>
                                                    <p style={{ margin: '0', fontSize: '13px', color: colors.text, lineHeight: '1.4' }}>
                                                        Automatic integration includes: SEO titles, meta descriptions, focus keywords, 
                                                        social media tags, and schema markup.
                                                    </p>
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
                                        </div>
                                    );
                                })}
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
                            <h4 style={{ margin: '0 0 8px 0', color: '#92400e', fontSize: '16px' }}>
                                No SEO Plugins Detected
                            </h4>
                            <p style={{ margin: '0 0 16px 0', color: '#92400e', fontSize: '14px', lineHeight: '1.4' }}>
                                Install one of the supported SEO plugins to enable automatic SEO data integration 
                                for your AI-generated content.
                            </p>
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
            </div>

            {/* Google Search Console - Pro Feature */}
            {isProActive ? (
                <div className="aca-card" style={{ margin: '0 0 24px 0' }}>
                    <div className="aca-card-header">
                        <h3 className="aca-card-title" style={{ display: 'flex', alignItems: 'center', gap: '12px', margin: '0 0 16px 0' }}>
                            <div style={{
                                width: '40px',
                                height: '40px',
                                background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                                borderRadius: '10px',
                                display: 'flex',
                                alignItems: 'center',
                                justifyContent: 'center'
                            }}>
                                <Google style={{ width: '20px', height: '20px', color: 'white' }} />
                            </div>
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
                        </h3>
                    </div>
                    
                    <div className="aca-form-group">
                        <label className="aca-label">Google Search Console Setup</label>
                        <p className="aca-page-description" style={{ marginBottom: '15px' }}>
                            To connect with Google Search Console, you need to create OAuth2 credentials in your Google Cloud Console.
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
                                />
                            </div>
                        </div>
                    </div>
                    
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
                                {isConnecting ? 'Disconnecting...' : 'Disconnect'}
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
                </div>
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
        </SettingsLayout>
    );
};