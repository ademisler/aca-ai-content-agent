import React, { useState } from 'react';
import { useSettings } from './SettingsProvider';
import { 
  Google, 
  CheckCircle, 
  Zap, 
  Image, 
  Shield 
} from '../Icons';

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
            <CheckCircle size={14} />
            Configured
          </div>
        )}
      </div>
    </div>
    {children}
  </div>
);

const GeminiIntegration: React.FC = () => {
  const { settings, updateSetting } = useSettings();
  const [showApiKey, setShowApiKey] = useState(false);

  return (
    <IntegrationCard
      title="Google AI (Gemini)"
      icon={<Google size={20} />}
      isConfigured={!!settings.geminiApiKey}
    >
      <div>
        <label className="aca-label">
          API Key
        </label>
        <div style={{ position: 'relative' }}>
          <input
            type={showApiKey ? 'text' : 'password'}
            value={settings.geminiApiKey}
            onChange={(e) => updateSetting('geminiApiKey', e.target.value)}
            placeholder="Enter your Google AI API key"
            className="aca-input"
            style={{ paddingRight: '100px' }}
          />
          <button
            type="button"
            onClick={() => setShowApiKey(!showApiKey)}
            className="aca-button secondary"
            style={{
              position: 'absolute',
              right: '8px',
              top: '50%',
              transform: 'translateY(-50%)',
              padding: '4px 8px',
              fontSize: '12px',
              minHeight: 'auto'
            }}
          >
            {showApiKey ? 'Hide' : 'Show'}
          </button>
        </div>
        <p className="aca-page-description">
          Required for AI content generation. Get your free API key from{' '}
          <a href="https://makersuite.google.com/app/apikey" target="_blank" rel="noopener noreferrer">
            Google AI Studio
          </a>
        </p>
      </div>
    </IntegrationCard>
  );
};

const GoogleSearchConsoleIntegration: React.FC = () => {
  const { settings, updateSetting } = useSettings();
  const [showClientSecret, setShowClientSecret] = useState(false);

  return (
    <IntegrationCard
      title="Google Search Console"
      icon={<Google size={20} />}
      isConfigured={!!(settings.gscClientId && settings.gscClientSecret)}
    >
      <div style={{ display: 'grid', gap: '16px' }}>
        <div>
          <label className="aca-label">
            Client ID
          </label>
          <input
            type="text"
            value={settings.gscClientId}
            onChange={(e) => updateSetting('gscClientId', e.target.value)}
            placeholder="Enter Google Cloud Client ID"
            className="aca-input"
          />
        </div>
        
        <div>
          <label className="aca-label">
            Client Secret
          </label>
          <div style={{ position: 'relative' }}>
            <input
              type={showClientSecret ? 'text' : 'password'}
              value={settings.gscClientSecret}
              onChange={(e) => updateSetting('gscClientSecret', e.target.value)}
              placeholder="Enter Google Cloud Client Secret"
              className="aca-input"
              style={{ paddingRight: '100px' }}
            />
            <button
              type="button"
              onClick={() => setShowClientSecret(!showClientSecret)}
              className="aca-button secondary"
              style={{
                position: 'absolute',
                right: '8px',
                top: '50%',
                transform: 'translateY(-50%)',
                padding: '4px 8px',
                fontSize: '12px',
                minHeight: 'auto'
              }}
            >
              {showClientSecret ? 'Hide' : 'Show'}
            </button>
          </div>
        </div>

        <p className="aca-page-description">
          Connect to Google Search Console to get performance data for better content ideas.{' '}
          <a href={`${window.acaData?.admin_url || ''}/admin.php?page=ai-content-agent&tab=gsc-setup`} target="_blank">
            Setup Guide
          </a>
        </p>
      </div>
    </IntegrationCard>
  );
};

const ImageGenerationIntegration: React.FC = () => {
  const { settings, updateSetting } = useSettings();
  const [showGoogleKeys, setShowGoogleKeys] = useState(false);
  const [showStockKeys, setShowStockKeys] = useState(false);

  return (
    <IntegrationCard
      title="Image Generation & Stock Photos"
      icon={<Image size={20} />}
      isConfigured={
        settings.imageSourceProvider === 'google-ai' 
          ? !!(settings.googleCloudProjectId)
          : !!(
              (settings.imageSourceProvider === 'pexels' && settings.pexelsApiKey) ||
              (settings.imageSourceProvider === 'unsplash' && settings.unsplashApiKey) ||
              (settings.imageSourceProvider === 'pixabay' && settings.pixabayApiKey)
            )
      }
    >
      <div style={{ display: 'grid', gap: '16px' }}>
        <div>
          <label className="aca-label">
            Image Source Provider
          </label>
          <select
            value={settings.imageSourceProvider}
            onChange={(e) => updateSetting('imageSourceProvider', e.target.value as any)}
            className="aca-input"
          >
            <option value="pexels">Pexels (Free Stock Photos)</option>
            <option value="unsplash">Unsplash (Free Stock Photos)</option>
            <option value="pixabay">Pixabay (Free Stock Photos)</option>
            <option value="google-ai">Google AI - Imagen 3.0 (AI Generated)</option>
          </select>
        </div>

        {settings.imageSourceProvider === 'google-ai' && (
          <>
            <div>
              <label className="aca-label">
                Google Cloud Project ID
              </label>
              <input
                type="text"
                value={settings.googleCloudProjectId}
                onChange={(e) => updateSetting('googleCloudProjectId', e.target.value)}
                placeholder="Enter your Google Cloud Project ID"
                className="aca-input"
              />
            </div>
            
            <div>
              <label className="aca-label">
                Google Cloud Location
              </label>
              <select
                value={settings.googleCloudLocation}
                onChange={(e) => updateSetting('googleCloudLocation', e.target.value)}
                className="aca-input"
              >
                <option value="us-central1">us-central1</option>
                <option value="us-east1">us-east1</option>
                <option value="us-west1">us-west1</option>
                <option value="europe-west1">europe-west1</option>
                <option value="asia-east1">asia-east1</option>
              </select>
            </div>

            <div>
              <label className="aca-label">
                AI Image Style
              </label>
              <select
                value={settings.aiImageStyle}
                onChange={(e) => updateSetting('aiImageStyle', e.target.value as any)}
                className="aca-input"
              >
                <option value="photorealistic">Photorealistic</option>
                <option value="artistic">Artistic</option>
                <option value="illustration">Illustration</option>
                <option value="sketch">Sketch</option>
              </select>
            </div>
          </>
        )}

        {settings.imageSourceProvider !== 'google-ai' && (
          <div style={{ display: 'grid', gap: '16px' }}>
            {settings.imageSourceProvider === 'pexels' && (
              <div>
                <label className="aca-label">
                  Pexels API Key
                </label>
                <div style={{ position: 'relative' }}>
                  <input
                    type={showStockKeys ? 'text' : 'password'}
                    value={settings.pexelsApiKey}
                    onChange={(e) => updateSetting('pexelsApiKey', e.target.value)}
                    placeholder="Enter your Pexels API key"
                    className="aca-input"
                    style={{ paddingRight: '100px' }}
                  />
                  <button
                    type="button"
                    onClick={() => setShowStockKeys(!showStockKeys)}
                    className="aca-button secondary"
                    style={{
                      position: 'absolute',
                      right: '8px',
                      top: '50%',
                      transform: 'translateY(-50%)',
                      padding: '4px 8px',
                      fontSize: '12px',
                      minHeight: 'auto'
                    }}
                  >
                    {showStockKeys ? 'Hide' : 'Show'}
                  </button>
                </div>
              </div>
            )}

            {settings.imageSourceProvider === 'unsplash' && (
              <div>
                <label className="aca-label">
                  Unsplash Access Key
                </label>
                <div style={{ position: 'relative' }}>
                  <input
                    type={showStockKeys ? 'text' : 'password'}
                    value={settings.unsplashApiKey}
                    onChange={(e) => updateSetting('unsplashApiKey', e.target.value)}
                    placeholder="Enter your Unsplash access key"
                    className="aca-input"
                    style={{ paddingRight: '100px' }}
                  />
                  <button
                    type="button"
                    onClick={() => setShowStockKeys(!showStockKeys)}
                    className="aca-button secondary"
                    style={{
                      position: 'absolute',
                      right: '8px',
                      top: '50%',
                      transform: 'translateY(-50%)',
                      padding: '4px 8px',
                      fontSize: '12px',
                      minHeight: 'auto'
                    }}
                  >
                    {showStockKeys ? 'Hide' : 'Show'}
                  </button>
                </div>
              </div>
            )}

            {settings.imageSourceProvider === 'pixabay' && (
              <div>
                <label className="aca-label">
                  Pixabay API Key
                </label>
                <div style={{ position: 'relative' }}>
                  <input
                    type={showStockKeys ? 'text' : 'password'}
                    value={settings.pixabayApiKey}
                    onChange={(e) => updateSetting('pixabayApiKey', e.target.value)}
                    placeholder="Enter your Pixabay API key"
                    className="aca-input"
                    style={{ paddingRight: '100px' }}
                  />
                  <button
                    type="button"
                    onClick={() => setShowStockKeys(!showStockKeys)}
                    className="aca-button secondary"
                    style={{
                      position: 'absolute',
                      right: '8px',
                      top: '50%',
                      transform: 'translateY(-50%)',
                      padding: '4px 8px',
                      fontSize: '12px',
                      minHeight: 'auto'
                    }}
                  >
                    {showStockKeys ? 'Hide' : 'Show'}
                  </button>
                </div>
              </div>
            )}
          </div>
        )}

        <p className="aca-page-description">
          {settings.imageSourceProvider === 'google-ai' 
            ? 'AI-generated images using Google\'s Imagen 3.0 model. Requires Google Cloud setup.'
            : `Get high-quality stock photos from ${settings.imageSourceProvider}. API key required for usage.`
          }
        </p>
      </div>
    </IntegrationCard>
  );
};

const SEOIntegration: React.FC = () => {
  const { settings, updateSetting } = useSettings();

  return (
    <IntegrationCard
      title="SEO Plugin Integration"
      icon={<Zap size={20} />}
      isConfigured={settings.seoPlugin !== 'none'}
    >
      <div>
        <label className="aca-label">
          SEO Plugin
        </label>
        <select
          value={settings.seoPlugin}
          onChange={(e) => updateSetting('seoPlugin', e.target.value as any)}
          className="aca-input"
        >
          <option value="none">None</option>
          <option value="rankmath">RankMath</option>
          <option value="yoast">Yoast SEO</option>
          <option value="aioseo">All in One SEO</option>
        </select>
        <p className="aca-page-description">
          Automatically set SEO titles, meta descriptions, and focus keywords when publishing content.
        </p>
      </div>
    </IntegrationCard>
  );
};

export const IntegrationsSection: React.FC = () => {
  return (
    <div className="aca-card">
      <div className="aca-card-header">
        <h2 className="aca-card-title">
          <Shield size={20} />
          API Integrations
        </h2>
        <p className="aca-page-description">
          Configure external services to enhance your content creation workflow.
        </p>
      </div>

      <div style={{ display: 'grid', gap: '24px' }}>
        <GeminiIntegration />
        <GoogleSearchConsoleIntegration />
        <ImageGenerationIntegration />
        <SEOIntegration />
      </div>
    </div>
  );
};