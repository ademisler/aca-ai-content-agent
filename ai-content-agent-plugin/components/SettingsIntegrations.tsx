import React, { useState } from 'react';
import type { AppSettings, ImageSourceProvider, AiImageStyle } from '../types';
import { Globe, Google, Image } from './Icons';
import { SettingsLayout } from './SettingsLayout';

interface SettingsIntegrationsProps {
    settings: AppSettings;
    onSaveSettings: (settings: AppSettings) => void;
    onShowToast: (message: string, type: 'success' | 'error' | 'warning' | 'info') => void;
    isProActive?: boolean;
}

const IntegrationCard: React.FC<{ 
    title: string; 
    icon: React.ReactNode;
    children: React.ReactNode; 
    isConfigured: boolean; 
}> = ({ title, icon, children, isConfigured }) => (
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
                        {icon}
                    </div>
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
                        <div style={{ 
                            width: '8px', 
                            height: '8px', 
                            borderRadius: '50%', 
                            background: '#22c55e' 
                        }}></div>
                        Configured
                    </div>
                )}
            </div>
        </div>
        {children}
    </div>
);

export const SettingsIntegrations: React.FC<SettingsIntegrationsProps> = ({ 
    settings, 
    onSaveSettings, 
    onShowToast 
}) => {
    const [currentSettings, setCurrentSettings] = useState<AppSettings>(settings);
    const [isDirty, setIsDirty] = useState(false);
    const [isSaving, setIsSaving] = useState(false);

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
            onShowToast('Integration settings saved successfully!', 'success');
        } catch (error) {
            onShowToast('Failed to save integration settings', 'error');
        } finally {
            setIsSaving(false);
        }
    };

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
            title="Integrations & Services"
            description="Connect to external services and configure how content is generated"
            icon={<Globe style={{ width: '24px', height: '24px', color: 'white' }} />}
            actions={saveButton}
        >
            {/* Google AI Integration */}
            <IntegrationCard 
                title="Google AI (Gemini)" 
                icon={<Google style={{ width: '20px', height: '20px', color: 'white' }} />}
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
                icon={<Image style={{ width: '20px', height: '20px', color: 'white' }} />}
                isConfigured={isImageSourceConfigured()}
            >
                <p className="aca-page-description" style={{ marginBottom: '20px' }}>
                    Select where to get featured images. For stock photo sites, an API key is required.
                </p>
                
                <div style={{ 
                    display: 'grid', 
                    gridTemplateColumns: 'repeat(auto-fit, minmax(120px, 1fr))', 
                    gap: '12px', 
                    marginBottom: '25px' 
                }}>
                   {(['pexels', 'unsplash', 'pixabay', 'ai'] as ImageSourceProvider[]).map(provider => (
                        <label 
                            key={provider} 
                            className={`aca-button ${currentSettings.imageSourceProvider === provider ? '' : 'secondary'}`}
                            style={{
                                textTransform: 'capitalize' as const,
                                cursor: 'pointer',
                                textAlign: 'center' as const,
                                margin: 0,
                                padding: '12px 8px',
                                fontSize: '14px'
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
                                className="aca-input"
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
                                className="aca-input"
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
        </SettingsLayout>
    );
};