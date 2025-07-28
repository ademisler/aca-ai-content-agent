
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
        <label htmlFor={id} className={`block p-5 rounded-lg border-2 transition-all cursor-pointer ${isChecked ? 'bg-blue-900/30 border-blue-500 shadow-md' : 'bg-slate-800 border-slate-700 hover:border-slate-600'}`}>
            <div className="flex items-start">
                <input
                    type="radio"
                    id={id}
                    name="automation-mode"
                    checked={isChecked}
                    onChange={() => onChange(id)}
                    className="mt-1 h-4 w-4 text-blue-600 bg-slate-700 border-slate-500 focus:ring-blue-500"
                />
                <div className="ml-3 text-sm">
                    <h4 className="font-semibold text-white">{title}</h4>
                    <p className="text-slate-400 mt-1">{description}</p>
                </div>
            </div>
        </label>
    );
};

const IntegrationCard: React.FC<{ title: string; children: React.ReactNode; isConfigured: boolean; }> = ({ title, children, isConfigured }) => (
    <div className="bg-slate-900/50 p-4 sm:p-5 rounded-lg border border-slate-700">
        <div className="flex justify-between items-center mb-3">
            <h4 className="font-semibold text-slate-200">{title}</h4>
            {isConfigured && <div className="flex items-center text-xs text-green-400"><CheckCircle className="h-4 w-4 mr-1"/> Configured</div>}
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
        <div className="space-y-8 max-w-4xl mx-auto">
            <header>
                <h2 className="text-3xl font-bold text-white border-b border-slate-700 pb-3 mb-6">Settings</h2>
                <p className="text-slate-400 -mt-2">Configure automation, integrations, and other plugin settings.</p>
            </header>

            <div className="bg-slate-800 rounded-lg border border-slate-700/80">
                <div className="p-6">
                    <h3 className="text-xl font-semibold text-white">Automation Mode</h3>
                    <p className="text-slate-400 mt-1 mb-6">Choose how you want the AI Content Agent to operate.</p>
                    <div className="space-y-4">
                        <RadioCard id="manual" title="Manual Mode" description="You are in full control. Manually generate ideas and create drafts one by one." currentSelection={currentSettings.mode} onChange={handleModeChange} />
                        <RadioCard id="semi-automatic" title="Semi-Automatic Mode" description="The AI automatically generates new ideas periodically. You choose which ideas to turn into drafts." currentSelection={currentSettings.mode} onChange={handleModeChange} />
                        <div className={`p-5 rounded-lg border-2 transition-all ${currentSettings.mode === 'full-automatic' ? 'border-blue-500 bg-blue-900/30 shadow-md' : 'border-slate-700 bg-slate-800 hover:border-slate-600'}`}>
                            <label htmlFor="full-automatic-radio" className="flex items-start cursor-pointer">
                                <input type="radio" id="full-automatic-radio" name="automation-mode" checked={currentSettings.mode === 'full-automatic'} onChange={() => handleModeChange('full-automatic')} className="mt-1 h-4 w-4 text-blue-600 bg-slate-700 border-slate-500 focus:ring-blue-500" />
                                <div className="ml-3 text-sm">
                                    <h4 className="font-semibold text-white">Full-Automatic Mode (Set & Forget)</h4>
                                    <p className="text-slate-400 mt-1">The AI handles everything: generates ideas, picks the best ones, and creates drafts automatically.</p>
                                </div>
                            </label>
                            {currentSettings.mode === 'full-automatic' && (
                                <div className="pl-8 pt-4 mt-4 border-t border-slate-700/60">
                                    <label htmlFor="auto-publish" className="flex items-center cursor-pointer">
                                        <input type="checkbox" id="auto-publish" checked={currentSettings.autoPublish} onChange={(e) => handleSettingChange('autoPublish', e.target.checked)} className="h-5 w-5 rounded bg-slate-700 border-slate-500 text-blue-500 focus:ring-blue-500" />
                                        <span className="ml-3 font-medium text-slate-200">Enable Auto-Publish</span>
                                    </label>
                                    <p className="pl-8 text-sm text-slate-400 mt-1">When enabled, the AI will not only create drafts but also publish them automatically.</p>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
            
            <div className="bg-slate-800 p-6 rounded-lg border border-slate-700/80">
                <h3 className="text-xl font-semibold mb-2 text-white">Integrations & Content Generation</h3>
                <p className="text-slate-400 mb-6 text-sm">Connect to external services and configure how content is generated.</p>
                <div className="space-y-6">
                    <div className="bg-slate-900/50 p-4 sm:p-5 rounded-lg border border-slate-700">
                        <div className="flex justify-between items-center mb-3">
                            <h4 className="font-semibold text-slate-200">Featured Image Source</h4>
                            {isImageSourceConfigured && <div className="flex items-center text-xs text-green-400"><CheckCircle className="h-4 w-4 mr-1"/> Configured</div>}
                        </div>
                        <p className="text-sm text-slate-400 mb-4">Select where to get featured images. For stock photo sites, an API key is required.</p>
                        
                        <div className="flex flex-wrap gap-2">
                           {(['ai', 'pexels', 'unsplash', 'pixabay'] as ImageSourceProvider[]).map(provider => (
                                <label key={provider} className={`capitalize px-3 py-1.5 text-sm font-semibold rounded-md cursor-pointer transition-colors ${currentSettings.imageSourceProvider === provider ? 'bg-blue-600 text-white' : 'bg-slate-700 hover:bg-slate-600 text-slate-300'}`}>
                                    <input type="radio" name="image-source-provider" value={provider} checked={currentSettings.imageSourceProvider === provider} onChange={(e) => handleSettingChange('imageSourceProvider', e.target.value as ImageSourceProvider)} className="sr-only"/>
                                    {provider}
                                </label>
                           ))}
                        </div>

                        <div className="mt-4 pt-4 border-t border-slate-700/60 space-y-2">
                            {currentSettings.imageSourceProvider === 'ai' && (
                                <div className="animate-fade-in-fast" style={{'--animation-duration': '300ms'} as React.CSSProperties}>
                                    <label htmlFor="ai-image-style" className="block text-sm font-medium text-slate-300 mb-1">AI Image Style</label>
                                    <select id="ai-image-style" value={currentSettings.aiImageStyle} onChange={(e) => handleSettingChange('aiImageStyle', e.target.value as AiImageStyle)} className="w-full max-w-xs bg-slate-700 border border-slate-600 rounded-md p-2 text-sm focus:ring-2 focus:ring-blue-500">
                                        <option value="photorealistic">Photorealistic</option>
                                        <option value="digital_art">Digital Art</option>
                                    </select>
                                </div>
                            )}
                            {currentSettings.imageSourceProvider === 'pexels' && (
                                <div className="animate-fade-in-fast" style={{'--animation-duration': '300ms'} as React.CSSProperties}>
                                    <label htmlFor="pexels-api-key" className="text-sm font-medium text-slate-300">Pexels API Key</label>
                                    <input id="pexels-api-key" type="password" placeholder="Enter Pexels API Key" value={currentSettings.pexelsApiKey} onChange={e => handleSettingChange('pexelsApiKey', e.target.value)} className="mt-1 w-full bg-slate-700 border border-slate-600 rounded-md p-2 text-sm focus:ring-2 focus:ring-blue-500"/>
                                    <a href="https://www.pexels.com/api/" target="_blank" rel="noopener noreferrer" className="text-xs text-blue-400 hover:underline mt-1.5 block">How to get a Pexels API key?</a>
                                </div>
                            )}
                            {currentSettings.imageSourceProvider === 'unsplash' && (
                                <div className="animate-fade-in-fast" style={{'--animation-duration': '300ms'} as React.CSSProperties}>
                                    <label htmlFor="unsplash-api-key" className="text-sm font-medium text-slate-300">Unsplash Access Key</label>
                                    <input id="unsplash-api-key" type="password" placeholder="Enter Unsplash Access Key" value={currentSettings.unsplashApiKey} onChange={e => handleSettingChange('unsplashApiKey', e.target.value)} className="mt-1 w-full bg-slate-700 border border-slate-600 rounded-md p-2 text-sm focus:ring-2 focus:ring-blue-500"/>
                                    <a href="https://unsplash.com/developers" target="_blank" rel="noopener noreferrer" className="text-xs text-blue-400 hover:underline mt-1.5 block">How to get an Unsplash Access key?</a>
                                </div>
                            )}
                             {currentSettings.imageSourceProvider === 'pixabay' && (
                                <div className="animate-fade-in-fast" style={{'--animation-duration': '300ms'} as React.CSSProperties}>
                                    <label htmlFor="pixabay-api-key" className="text-sm font-medium text-slate-300">Pixabay API Key</label>
                                    <input id="pixabay-api-key" type="password" placeholder="Enter Pixabay API Key" value={currentSettings.pixabayApiKey} onChange={e => handleSettingChange('pixabayApiKey', e.target.value)} className="mt-1 w-full bg-slate-700 border border-slate-600 rounded-md p-2 text-sm focus:ring-2 focus:ring-blue-500"/>
                                    <a href="https://pixabay.com/api/docs/" target="_blank" rel="noopener noreferrer" className="text-xs text-blue-400 hover:underline mt-1.5 block">How to get a Pixabay API key?</a>
                                </div>
                            )}
                        </div>
                    </div>

                    <IntegrationCard title="SEO Integration" isConfigured={currentSettings.seoPlugin !== 'none'}>
                        <div className="flex flex-col sm:flex-row sm:items-center gap-4">
                            <select value={currentSettings.seoPlugin} onChange={(e) => handleSettingChange('seoPlugin', e.target.value as SeoPlugin)} className="w-full flex-grow max-w-xs bg-slate-700 border border-slate-600 rounded-md p-2 text-sm focus:ring-2 focus:ring-blue-500">
                                <option value="none">None</option>
                                <option value="rank_math">Rank Math</option>
                                <option value="yoast">Yoast SEO</option>
                            </select>
                            <button onClick={handleAutoDetectSeo} disabled={isDetectingSeo} className="w-full sm:w-auto bg-slate-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-slate-700 transition-colors flex items-center justify-center disabled:bg-slate-500 disabled:cursor-wait flex-shrink-0">
                                {isDetectingSeo && <Spinner className="mr-2 h-4 w-4"/>}
                                {isDetectingSeo ? "Detecting..." : "Auto-Detect"}
                            </button>
                        </div>
                    </IntegrationCard>

                    <IntegrationCard title="Google Search Console" isConfigured={!!currentSettings.searchConsoleUser}>
                         <div className="flex flex-col sm:flex-row items-center justify-between gap-4">
                            <div className="flex items-center">
                                <Google className="h-7 w-7 mr-4 text-white"/>
                                <div>
                                    <h4 className="font-semibold text-white">Status</h4>
                                    {currentSettings.searchConsoleUser ? <p className="text-sm text-green-400">Connected as {currentSettings.searchConsoleUser.email}</p> : <p className="text-sm text-slate-400">Use search data to generate strategic ideas.</p>}
                                </div>
                            </div>
                            {currentSettings.searchConsoleUser ? (
                                <button onClick={() => handleSettingChange('searchConsoleUser', null)} className="w-full sm:w-auto font-bold py-2 px-5 rounded-lg transition-colors text-sm flex-shrink-0 bg-red-900/70 text-red-300 hover:bg-red-900">Disconnect</button>
                            ) : (
                                <button onClick={handleGSCConnect} disabled={isConnecting} className="w-full sm:w-auto font-bold py-2 px-5 rounded-lg transition-colors text-sm flex-shrink-0 bg-green-600 text-white hover:bg-green-700 disabled:bg-slate-600 disabled:cursor-wait flex items-center justify-center">
                                    {isConnecting && <Spinner className="h-5 w-5 mr-2" />}
                                    {isConnecting ? 'Connecting...' : 'Connect'}
                                </button>
                            )}
                        </div>
                    </IntegrationCard>
                </div>
            </div>

            <div className="flex justify-end">
                <button
                    onClick={handleSave}
                    disabled={!isDirty || isSaving}
                    className="bg-blue-600 text-white font-bold py-2 px-5 rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center disabled:bg-slate-600 disabled:cursor-not-allowed min-w-[140px]"
                >
                    {isSaving ? <Spinner className="mr-2 h-5 w-5" /> : null}
                    {isSaving ? 'Saving...' : 'Save Changes'}
                </button>
            </div>
        </div>
    );
};
