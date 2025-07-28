
import React, { useState, useEffect } from 'react';
import type { AppSettings, AutomationMode, ImageSourceProvider, AiImageStyle, SeoPlugin } from '../types';
import { Spinner, Google, CheckCircle, Settings as SettingsIcon, Zap, Image, Shield } from './Icons';

interface SettingsProps {
    settings: AppSettings;
    onSaveSettings: (settings: AppSettings) => void;
}

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
                padding: '20px',
                border: '2px solid',
                borderColor: isChecked ? '#0073aa' : '#ccd0d4',
                background: isChecked ? '#f0f6fc' : '#ffffff',
                boxShadow: isChecked ? '0 2px 4px rgba(0, 0, 0, 0.1)' : 'none',
                transition: 'all 0.2s ease',
                cursor: 'pointer'
            }}
            onMouseEnter={(e) => {
                if (!isChecked) {
                    e.currentTarget.style.borderColor = '#8c8f94';
                }
            }}
            onMouseLeave={(e) => {
                if (!isChecked) {
                    e.currentTarget.style.borderColor = '#ccd0d4';
                }
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
                <div style={{ fontSize: '13px' }}>
                    <h4 style={{ fontWeight: '600', color: '#23282d', margin: '0 0 8px 0', fontSize: '16px' }}>
                        {title}
                    </h4>
                    <p style={{ color: '#646970', margin: 0, lineHeight: '1.4' }}>
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
                <h3 style={{ 
                    fontWeight: '600', 
                    color: '#23282d', 
                    margin: 0,
                    display: 'flex',
                    alignItems: 'center',
                    gap: '8px',
                    fontSize: '16px'
                }}>
                    {icon}
                    {title}
                </h3>
                {isConfigured && (
                    <div style={{ 
                        display: 'flex', 
                        alignItems: 'center', 
                        fontSize: '12px', 
                        color: '#00a32a',
                        fontWeight: '600',
                        gap: '6px'
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

export const Settings: React.FC<SettingsProps> = ({ settings, onSaveSettings }) => {
    const [currentSettings, setCurrentSettings] = useState<AppSettings>(settings);
    const [isSaving, setIsSaving] = useState(false);
    const [isConnecting, setIsConnecting] = useState(false);
    const [isDetectingSeo, setIsDetectingSeo] = useState(false);

    useEffect(() => {
        setCurrentSettings(settings);
    }, [settings]);

    const isDirty = JSON.stringify(currentSettings) !== JSON.stringify(settings);

    const handleSettingChange = (field: keyof AppSettings, value: any) => {
        setCurrentSettings(prev => ({ ...prev, [field]: value }));
    };
    
    const handleModeChange = (mode: AutomationMode) => {
        handleSettingChange('mode', mode);
        if (mode !== 'full-automatic') {
            handleSettingChange('autoPublish', false);
        }
    };

    const handleAutoDetectSeo = () => {
        setIsDetectingSeo(true);
        setTimeout(() => {
            const detectedPlugin = Math.random() > 0.5 ? 'rank_math' : 'yoast';
            handleSettingChange('seoPlugin', detectedPlugin);
            setIsDetectingSeo(false);
        }, 1500);
    };

    const handleGSCConnect = () => {
        setIsConnecting(true);
        setTimeout(() => {
            handleSettingChange('searchConsoleUser', { email: 'example.user@gmail.com' });
            setIsConnecting(false);
        }, 2000);
    };

    const handleSave = () => {
        setIsSaving(true);
        setTimeout(() => {
            onSaveSettings(currentSettings);
            setIsSaving(false);
        }, 700);
    };
    
    const isImageSourceConfigured = currentSettings.imageSourceProvider === 'ai' ||
        (currentSettings.imageSourceProvider === 'pexels' && !!currentSettings.pexelsApiKey) ||
        (currentSettings.imageSourceProvider === 'unsplash' && !!currentSettings.unsplashApiKey) ||
        (currentSettings.imageSourceProvider === 'pixabay' && !!currentSettings.pixabayApiKey);

    return (
        <div className="aca-fade-in">
            <div className="aca-page-header">
                <h1 className="aca-page-title">Settings</h1>
                <p className="aca-page-description">
                    Configure automation modes, integrations, and content generation preferences to customize your AI Content Agent experience.
                </p>
            </div>

            {/* Automation Mode */}
            <div className="aca-card">
                <div className="aca-card-header">
                    <h2 className="aca-card-title">
                        <Zap style={{ width: '20px', height: '20px', marginRight: '8px', fill: '#0073aa' }} />
                        Automation Mode
                    </h2>
                </div>
                <p style={{ color: '#646970', marginBottom: '30px', fontSize: '13px' }}>
                    Choose how you want the AI Content Agent to operate. You can change this at any time.
                </p>
                
                <div style={{ display: 'flex', flexDirection: 'column', gap: '15px' }}>
                    <RadioCard 
                        id="manual" 
                        title="Manual Mode" 
                        description="You are in full control. Manually generate ideas and create drafts one by one." 
                        currentSelection={currentSettings.mode} 
                        onChange={handleModeChange} 
                    />
                    <RadioCard 
                        id="semi-automatic" 
                        title="Semi-Automatic Mode" 
                        description="The AI automatically generates new ideas periodically. You choose which ideas to turn into drafts." 
                        currentSelection={currentSettings.mode} 
                        onChange={handleModeChange} 
                    />
                    
                    {/* Full Automatic with Auto-Publish Option */}
                    <div className="aca-card" style={{
                        margin: 0,
                        padding: '20px',
                        border: '2px solid',
                        borderColor: currentSettings.mode === 'full-automatic' ? '#0073aa' : '#ccd0d4',
                        background: currentSettings.mode === 'full-automatic' ? '#f0f6fc' : '#ffffff',
                        boxShadow: currentSettings.mode === 'full-automatic' ? '0 2px 4px rgba(0, 0, 0, 0.1)' : 'none',
                        transition: 'all 0.2s ease'
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
                            <div style={{ fontSize: '13px' }}>
                                <h4 style={{ fontWeight: '600', color: '#23282d', margin: '0 0 8px 0', fontSize: '16px' }}>
                                    Full-Automatic Mode (Set & Forget)
                                </h4>
                                <p style={{ color: '#646970', margin: 0, lineHeight: '1.4' }}>
                                    The AI handles everything: generates ideas, picks the best ones, and creates drafts automatically.
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
                                        <span style={{ fontWeight: '500', color: '#23282d', fontSize: '14px' }}>
                                            Enable Auto-Publish
                                        </span>
                                        <p style={{ fontSize: '13px', color: '#646970', marginTop: '5px', margin: '5px 0 0 0' }}>
                                            When enabled, the AI will not only create drafts but also publish them automatically.
                                        </p>
                                    </div>
                                </label>
                            </div>
                        )}
                    </div>
                </div>
            </div>
            
            {/* Integrations */}
            <div className="aca-card">
                <div className="aca-card-header">
                    <h2 className="aca-card-title">
                        <Shield style={{ width: '20px', height: '20px', marginRight: '8px', fill: '#0073aa' }} />
                        Integrations & Services
                    </h2>
                </div>
                <p style={{ color: '#646970', marginBottom: '30px', fontSize: '13px' }}>
                    Connect to external services and configure how content is generated and optimized.
                </p>
                
                <div style={{ display: 'flex', flexDirection: 'column', gap: '25px' }}>
                    {/* Google AI Integration */}
                    <IntegrationCard 
                        title="Google AI (Gemini)" 
                        icon={<Google style={{ width: '18px', height: '18px', fill: '#0073aa' }} />}
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
                                style={{ 
                                    fontSize: '12px', 
                                    color: '#0073aa', 
                                    textDecoration: 'none', 
                                    marginTop: '8px', 
                                    display: 'block' 
                                }}
                                onMouseEnter={(e) => {
                                    e.currentTarget.style.textDecoration = 'underline';
                                }}
                                onMouseLeave={(e) => {
                                    e.currentTarget.style.textDecoration = 'none';
                                }}
                            >
                                → Get your Google AI API key
                            </a>
                        </div>
                    </IntegrationCard>

                    {/* Image Source Integration */}
                    <IntegrationCard 
                        title="Featured Image Source" 
                        icon={<Image style={{ width: '18px', height: '18px', fill: '#0073aa' }} />}
                        isConfigured={isImageSourceConfigured}
                    >
                        <p style={{ fontSize: '13px', color: '#646970', marginBottom: '20px', margin: '0 0 20px 0' }}>
                            Select where to get featured images. For stock photo sites, an API key is required.
                        </p>
                        
                        <div className="aca-grid aca-grid-2" style={{ gap: '12px', marginBottom: '25px' }}>
                           {(['ai', 'pexels', 'unsplash', 'pixabay'] as ImageSourceProvider[]).map(provider => (
                                <label 
                                    key={provider} 
                                    className="aca-button secondary"
                                    style={{
                                        textTransform: 'capitalize',
                                        padding: '12px 16px',
                                        fontSize: '13px',
                                        fontWeight: '500',
                                        cursor: 'pointer',
                                        transition: 'all 0.2s ease',
                                        background: currentSettings.imageSourceProvider === provider ? '#0073aa' : '#ffffff',
                                        color: currentSettings.imageSourceProvider === provider ? '#ffffff' : '#646970',
                                        border: '1px solid',
                                        borderColor: currentSettings.imageSourceProvider === provider ? '#0073aa' : '#ccd0d4',
                                        textAlign: 'center',
                                        margin: 0
                                    }}
                                    onMouseEnter={(e) => {
                                        if (currentSettings.imageSourceProvider !== provider) {
                                            e.currentTarget.style.background = '#f6f7f7';
                                            e.currentTarget.style.borderColor = '#8c8f94';
                                        }
                                    }}
                                    onMouseLeave={(e) => {
                                        if (currentSettings.imageSourceProvider !== provider) {
                                            e.currentTarget.style.background = '#ffffff';
                                            e.currentTarget.style.borderColor = '#ccd0d4';
                                        }
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
                            <div className="aca-form-group" style={{ animation: 'aca-fade-in 0.3s ease-out' }}>
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
                        )}
                        {currentSettings.imageSourceProvider === 'pexels' && (
                            <div className="aca-form-group" style={{ animation: 'aca-fade-in 0.3s ease-out' }}>
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
                                    style={{ 
                                        fontSize: '12px', 
                                        color: '#0073aa', 
                                        textDecoration: 'none', 
                                        marginTop: '8px', 
                                        display: 'block' 
                                    }}
                                    onMouseEnter={(e) => {
                                        e.currentTarget.style.textDecoration = 'underline';
                                    }}
                                    onMouseLeave={(e) => {
                                        e.currentTarget.style.textDecoration = 'none';
                                    }}
                                >
                                    → Get your Pexels API key
                                </a>
                            </div>
                        )}
                        {currentSettings.imageSourceProvider === 'unsplash' && (
                            <div className="aca-form-group" style={{ animation: 'aca-fade-in 0.3s ease-out' }}>
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
                                    style={{ 
                                        fontSize: '12px', 
                                        color: '#0073aa', 
                                        textDecoration: 'none', 
                                        marginTop: '8px', 
                                        display: 'block' 
                                    }}
                                    onMouseEnter={(e) => {
                                        e.currentTarget.style.textDecoration = 'underline';
                                    }}
                                    onMouseLeave={(e) => {
                                        e.currentTarget.style.textDecoration = 'none';
                                    }}
                                >
                                    → Get your Unsplash Access key
                                </a>
                            </div>
                        )}
                         {currentSettings.imageSourceProvider === 'pixabay' && (
                            <div className="aca-form-group" style={{ animation: 'aca-fade-in 0.3s ease-out' }}>
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
                                    style={{ 
                                        fontSize: '12px', 
                                        color: '#0073aa', 
                                        textDecoration: 'none', 
                                        marginTop: '8px', 
                                        display: 'block' 
                                    }}
                                    onMouseEnter={(e) => {
                                        e.currentTarget.style.textDecoration = 'underline';
                                    }}
                                    onMouseLeave={(e) => {
                                        e.currentTarget.style.textDecoration = 'none';
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
                        icon={<SettingsIcon style={{ width: '18px', height: '18px', fill: '#0073aa' }} />}
                        isConfigured={currentSettings.seoPlugin !== 'none'}
                    >
                        <div style={{ display: 'flex', alignItems: 'center', gap: '15px', flexWrap: 'wrap' }}>
                            <div className="aca-form-group" style={{ flexGrow: 1, marginBottom: 0 }}>
                                <label className="aca-label">SEO Plugin</label>
                                <select 
                                    value={currentSettings.seoPlugin} 
                                    onChange={(e) => handleSettingChange('seoPlugin', e.target.value as SeoPlugin)} 
                                    className="aca-select"
                                    style={{ maxWidth: '200px' }}
                                >
                                    <option value="none">None</option>
                                    <option value="rank_math">Rank Math</option>
                                    <option value="yoast">Yoast SEO</option>
                                </select>
                            </div>
                            <button 
                                onClick={handleAutoDetectSeo} 
                                disabled={isDetectingSeo} 
                                className="aca-button secondary"
                                style={{ 
                                    display: 'flex', 
                                    alignItems: 'center',
                                    gap: '8px',
                                    flexShrink: 0 
                                }}
                            >
                                {isDetectingSeo && <Spinner style={{ width: '14px', height: '14px' }} />}
                                {isDetectingSeo ? "Detecting..." : "Auto-Detect"}
                            </button>
                        </div>
                    </IntegrationCard>

                    {/* Google Search Console */}
                    <IntegrationCard 
                        title="Google Search Console" 
                        icon={<Google style={{ width: '18px', height: '18px', fill: '#0073aa' }} />}
                        isConfigured={!!currentSettings.searchConsoleUser}
                    >
                        <div className="aca-stat-item" style={{ margin: 0 }}>
                            <div className="aca-stat-info">
                                <div className="aca-stat-icon">
                                    <Google style={{ fill: '#23282d' }} />
                                </div>
                                <div>
                                    <h4 className="aca-stat-title">Connection Status</h4>
                                    {currentSettings.searchConsoleUser ? (
                                        <p className="aca-stat-count" style={{ color: '#00a32a' }}>
                                            Connected as {currentSettings.searchConsoleUser.email}
                                        </p>
                                    ) : (
                                        <p className="aca-stat-count">
                                            Use search data to generate strategic content ideas
                                        </p>
                                    )}
                                </div>
                            </div>
                            {currentSettings.searchConsoleUser ? (
                                <button 
                                    onClick={() => handleSettingChange('searchConsoleUser', null)} 
                                    className="aca-button"
                                    style={{ 
                                        fontSize: '13px',
                                        flexShrink: 0,
                                        background: '#d63638',
                                        borderColor: '#d63638'
                                    }}
                                    onMouseEnter={(e) => {
                                        e.currentTarget.style.background = '#b32d2e';
                                        e.currentTarget.style.borderColor = '#b32d2e';
                                    }}
                                    onMouseLeave={(e) => {
                                        e.currentTarget.style.background = '#d63638';
                                        e.currentTarget.style.borderColor = '#d63638';
                                    }}
                                >
                                    Disconnect
                                </button>
                            ) : (
                                <button 
                                    onClick={handleGSCConnect} 
                                    disabled={isConnecting} 
                                    className="aca-button"
                                    style={{ 
                                        fontSize: '13px',
                                        flexShrink: 0,
                                        display: 'flex',
                                        alignItems: 'center',
                                        gap: '8px',
                                        background: '#00a32a',
                                        borderColor: '#00a32a'
                                    }}
                                    onMouseEnter={(e) => {
                                        if (!isConnecting) {
                                            e.currentTarget.style.background = '#008a20';
                                            e.currentTarget.style.borderColor = '#008a20';
                                        }
                                    }}
                                    onMouseLeave={(e) => {
                                        if (!isConnecting) {
                                            e.currentTarget.style.background = '#00a32a';
                                            e.currentTarget.style.borderColor = '#00a32a';
                                        }
                                    }}
                                >
                                    {isConnecting && <Spinner style={{ width: '16px', height: '16px' }} />}
                                    {isConnecting ? 'Connecting...' : 'Connect'}
                                </button>
                            )}
                        </div>
                    </IntegrationCard>
                </div>
            </div>

            {/* Save Settings */}
            <div style={{ 
                display: 'flex', 
                justifyContent: 'space-between', 
                alignItems: 'center',
                paddingTop: '25px',
                borderTop: '1px solid #f0f0f1'
            }}>
                {isDirty && (
                    <div style={{ 
                        display: 'flex',
                        alignItems: 'center',
                        fontSize: '13px', 
                        color: '#dba617',
                        fontWeight: '500',
                        gap: '8px'
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
                        style={{ 
                            display: 'flex', 
                            alignItems: 'center', 
                            minWidth: '140px',
                            justifyContent: 'center'
                        }}
                    >
                        {isSaving && <Spinner style={{ marginRight: '8px', width: '16px', height: '16px' }} />}
                        {isSaving ? 'Saving...' : 'Save Settings'}
                    </button>
                </div>
            </div>
        </div>
    );
};