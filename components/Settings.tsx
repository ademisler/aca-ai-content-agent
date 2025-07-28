
import React, { useState, useEffect } from 'react';
import type { AppSettings, AutomationMode, ImageSourceProvider, AiImageStyle, SeoPlugin } from '../types';
import { Spinner, Google, CheckCircle } from './Icons';

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
            style={{
                display: 'block',
                padding: '20px',
                borderRadius: '4px',
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
            <div style={{ display: 'flex', alignItems: 'flex-start' }}>
                <input
                    type="radio"
                    id={id}
                    name="automation-mode"
                    checked={isChecked}
                    onChange={() => onChange(id)}
                    style={{
                        marginTop: '2px',
                        width: '16px',
                        height: '16px',
                        accentColor: '#0073aa'
                    }}
                />
                <div style={{ marginLeft: '12px', fontSize: '13px' }}>
                    <h4 style={{ fontWeight: '600', color: '#23282d', margin: '0 0 5px 0' }}>{title}</h4>
                    <p style={{ color: '#646970', margin: 0 }}>{description}</p>
                </div>
            </div>
        </label>
    );
};

const IntegrationCard: React.FC<{ title: string; children: React.ReactNode; isConfigured: boolean; }> = ({ title, children, isConfigured }) => (
    <div style={{ 
        background: '#f6f7f7', 
        padding: '20px', 
        borderRadius: '4px', 
        border: '1px solid #ccd0d4' 
    }}>
        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '12px' }}>
            <h4 style={{ fontWeight: '600', color: '#23282d', margin: 0 }}>{title}</h4>
            {isConfigured && (
                <div style={{ display: 'flex', alignItems: 'center', fontSize: '11px', color: '#00a32a' }}>
                    <CheckCircle style={{ width: '14px', height: '14px', marginRight: '4px' }} />
                    Configured
                </div>
            )}
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
                <p className="aca-page-description">Configure automation, integrations, and other plugin settings.</p>
            </div>

            <div className="aca-card">
                <div className="aca-card-header">
                    <h2 className="aca-card-title">Automation Mode</h2>
                </div>
                <p style={{ color: '#646970', marginBottom: '25px', fontSize: '13px' }}>Choose how you want the AI Content Agent to operate.</p>
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
                    <div style={{
                        padding: '20px',
                        borderRadius: '4px',
                        border: '2px solid',
                        borderColor: currentSettings.mode === 'full-automatic' ? '#0073aa' : '#ccd0d4',
                        background: currentSettings.mode === 'full-automatic' ? '#f0f6fc' : '#ffffff',
                        boxShadow: currentSettings.mode === 'full-automatic' ? '0 2px 4px rgba(0, 0, 0, 0.1)' : 'none',
                        transition: 'all 0.2s ease'
                    }}>
                        <label htmlFor="full-automatic-radio" style={{ display: 'flex', alignItems: 'flex-start', cursor: 'pointer' }}>
                            <input 
                                type="radio" 
                                id="full-automatic-radio" 
                                name="automation-mode" 
                                checked={currentSettings.mode === 'full-automatic'} 
                                onChange={() => handleModeChange('full-automatic')} 
                                style={{
                                    marginTop: '2px',
                                    width: '16px',
                                    height: '16px',
                                    accentColor: '#0073aa'
                                }}
                            />
                            <div style={{ marginLeft: '12px', fontSize: '13px' }}>
                                <h4 style={{ fontWeight: '600', color: '#23282d', margin: '0 0 5px 0' }}>Full-Automatic Mode (Set & Forget)</h4>
                                <p style={{ color: '#646970', margin: 0 }}>The AI handles everything: generates ideas, picks the best ones, and creates drafts automatically.</p>
                            </div>
                        </label>
                        {currentSettings.mode === 'full-automatic' && (
                            <div style={{ 
                                paddingLeft: '28px', 
                                paddingTop: '15px', 
                                marginTop: '15px', 
                                borderTop: '1px solid #f0f0f1' 
                            }}>
                                <label htmlFor="auto-publish" style={{ display: 'flex', alignItems: 'center', cursor: 'pointer' }}>
                                    <input 
                                        type="checkbox" 
                                        id="auto-publish" 
                                        checked={currentSettings.autoPublish} 
                                        onChange={(e) => handleSettingChange('autoPublish', e.target.checked)} 
                                        style={{
                                            width: '16px',
                                            height: '16px',
                                            accentColor: '#0073aa'
                                        }}
                                    />
                                    <span style={{ marginLeft: '12px', fontWeight: '500', color: '#23282d', fontSize: '13px' }}>Enable Auto-Publish</span>
                                </label>
                                <p style={{ paddingLeft: '28px', fontSize: '13px', color: '#646970', marginTop: '5px', margin: '5px 0 0 28px' }}>When enabled, the AI will not only create drafts but also publish them automatically.</p>
                            </div>
                        )}
                    </div>
                </div>
            </div>
            
            <div className="aca-card">
                <div className="aca-card-header">
                    <h2 className="aca-card-title">Integrations & Content Generation</h2>
                </div>
                <p style={{ color: '#646970', marginBottom: '25px', fontSize: '13px' }}>Connect to external services and configure how content is generated.</p>
                <div style={{ display: 'flex', flexDirection: 'column', gap: '25px' }}>
                    <IntegrationCard title="Google AI (Gemini)" isConfigured={!!currentSettings.geminiApiKey}>
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
                                    fontSize: '11px', 
                                    color: '#0073aa', 
                                    textDecoration: 'none', 
                                    marginTop: '6px', 
                                    display: 'block' 
                                }}
                                onMouseEnter={(e) => {
                                    e.currentTarget.style.textDecoration = 'underline';
                                }}
                                onMouseLeave={(e) => {
                                    e.currentTarget.style.textDecoration = 'none';
                                }}
                            >
                                How to get a Google AI API key?
                            </a>
                        </div>
                    </IntegrationCard>

                    <div style={{ 
                        background: '#f6f7f7', 
                        padding: '20px', 
                        borderRadius: '4px', 
                        border: '1px solid #ccd0d4' 
                    }}>
                        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '12px' }}>
                            <h4 style={{ fontWeight: '600', color: '#23282d', margin: 0 }}>Featured Image Source</h4>
                            {isImageSourceConfigured && (
                                <div style={{ display: 'flex', alignItems: 'center', fontSize: '11px', color: '#00a32a' }}>
                                    <CheckCircle style={{ width: '14px', height: '14px', marginRight: '4px' }} />
                                    Configured
                                </div>
                            )}
                        </div>
                        <p style={{ fontSize: '13px', color: '#646970', marginBottom: '15px', margin: '0 0 15px 0' }}>Select where to get featured images. For stock photo sites, an API key is required.</p>
                        
                        <div style={{ display: 'flex', flexWrap: 'wrap', gap: '8px' }}>
                           {(['ai', 'pexels', 'unsplash', 'pixabay'] as ImageSourceProvider[]).map(provider => (
                                <label 
                                    key={provider} 
                                    style={{
                                        textTransform: 'capitalize',
                                        padding: '6px 12px',
                                        fontSize: '12px',
                                        fontWeight: '600',
                                        borderRadius: '4px',
                                        cursor: 'pointer',
                                        transition: 'all 0.2s ease',
                                        background: currentSettings.imageSourceProvider === provider ? '#0073aa' : '#ffffff',
                                        color: currentSettings.imageSourceProvider === provider ? '#ffffff' : '#646970',
                                        border: '1px solid',
                                        borderColor: currentSettings.imageSourceProvider === provider ? '#0073aa' : '#ccd0d4'
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
                                    {provider}
                                </label>
                           ))}
                        </div>

                        <div style={{ 
                            marginTop: '15px', 
                            paddingTop: '15px', 
                            borderTop: '1px solid #f0f0f1', 
                            display: 'flex', 
                            flexDirection: 'column', 
                            gap: '10px' 
                        }}>
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
                                            fontSize: '11px', 
                                            color: '#0073aa', 
                                            textDecoration: 'none', 
                                            marginTop: '6px', 
                                            display: 'block' 
                                        }}
                                        onMouseEnter={(e) => {
                                            e.currentTarget.style.textDecoration = 'underline';
                                        }}
                                        onMouseLeave={(e) => {
                                            e.currentTarget.style.textDecoration = 'none';
                                        }}
                                    >
                                        How to get a Pexels API key?
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
                                            fontSize: '11px', 
                                            color: '#0073aa', 
                                            textDecoration: 'none', 
                                            marginTop: '6px', 
                                            display: 'block' 
                                        }}
                                        onMouseEnter={(e) => {
                                            e.currentTarget.style.textDecoration = 'underline';
                                        }}
                                        onMouseLeave={(e) => {
                                            e.currentTarget.style.textDecoration = 'none';
                                        }}
                                    >
                                        How to get an Unsplash Access key?
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
                                            fontSize: '11px', 
                                            color: '#0073aa', 
                                            textDecoration: 'none', 
                                            marginTop: '6px', 
                                            display: 'block' 
                                        }}
                                        onMouseEnter={(e) => {
                                            e.currentTarget.style.textDecoration = 'underline';
                                        }}
                                        onMouseLeave={(e) => {
                                            e.currentTarget.style.textDecoration = 'none';
                                        }}
                                    >
                                        How to get a Pixabay API key?
                                    </a>
                                </div>
                            )}
                        </div>
                    </div>

                    <IntegrationCard title="SEO Integration" isConfigured={currentSettings.seoPlugin !== 'none'}>
                        <div style={{ display: 'flex', flexDirection: 'column', gap: '15px' }}>
                            <div style={{ display: 'flex', alignItems: 'center', gap: '15px', flexWrap: 'wrap' }}>
                                <select 
                                    value={currentSettings.seoPlugin} 
                                    onChange={(e) => handleSettingChange('seoPlugin', e.target.value as SeoPlugin)} 
                                    className="aca-select"
                                    style={{ flexGrow: 1, maxWidth: '200px' }}
                                >
                                    <option value="none">None</option>
                                    <option value="rank_math">Rank Math</option>
                                    <option value="yoast">Yoast SEO</option>
                                </select>
                                <button 
                                    onClick={handleAutoDetectSeo} 
                                    disabled={isDetectingSeo} 
                                    className="aca-button secondary"
                                    style={{ 
                                        display: 'flex', 
                                        alignItems: 'center', 
                                        flexShrink: 0 
                                    }}
                                >
                                    {isDetectingSeo && <Spinner style={{ marginRight: '8px', width: '14px', height: '14px' }} />}
                                    {isDetectingSeo ? "Detecting..." : "Auto-Detect"}
                                </button>
                            </div>
                        </div>
                    </IntegrationCard>

                    <IntegrationCard title="Google Search Console" isConfigured={!!currentSettings.searchConsoleUser}>
                        <div style={{ display: 'flex', flexDirection: 'column', gap: '15px' }}>
                            <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', flexWrap: 'wrap', gap: '15px' }}>
                                <div style={{ display: 'flex', alignItems: 'center' }}>
                                    <Google style={{ width: '28px', height: '28px', marginRight: '15px', fill: '#23282d' }} />
                                    <div>
                                        <h4 style={{ fontWeight: '600', color: '#23282d', margin: '0 0 3px 0' }}>Status</h4>
                                        {currentSettings.searchConsoleUser ? (
                                            <p style={{ fontSize: '13px', color: '#00a32a', margin: 0 }}>Connected as {currentSettings.searchConsoleUser.email}</p>
                                        ) : (
                                            <p style={{ fontSize: '13px', color: '#646970', margin: 0 }}>Use search data to generate strategic ideas.</p>
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
                                        {isConnecting && <Spinner style={{ width: '16px', height: '16px', marginRight: '8px' }} />}
                                        {isConnecting ? 'Connecting...' : 'Connect'}
                                    </button>
                                )}
                            </div>
                        </div>
                    </IntegrationCard>
                </div>
            </div>

            <div style={{ display: 'flex', justifyContent: 'flex-end' }}>
                <button
                    onClick={handleSave}
                    disabled={!isDirty || isSaving}
                    className="aca-button large"
                    style={{ 
                        display: 'flex', 
                        alignItems: 'center', 
                        minWidth: '140px' 
                    }}
                >
                    {isSaving && <Spinner style={{ marginRight: '8px', width: '16px', height: '16px' }} />}
                    {isSaving ? 'Saving...' : 'Save Changes'}
                </button>
            </div>
        </div>
    );
};