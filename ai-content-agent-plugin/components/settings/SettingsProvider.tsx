import React, { createContext, useContext, useState, useEffect, ReactNode } from 'react';
import type { AppSettings, AutomationMode, ImageSourceProvider, AiImageStyle, SeoPlugin } from '../../types';

interface SettingsContextType {
  settings: AppSettings;
  updateSetting: <K extends keyof AppSettings>(key: K, value: AppSettings[K]) => void;
  saveSettings: () => void;
  isSaving: boolean;
  hasChanges: boolean;
  resetToDefaults: () => void;
}

const SettingsContext = createContext<SettingsContextType | null>(null);

interface SettingsProviderProps {
  children: ReactNode;
  initialSettings: AppSettings;
  onSaveSettings: (settings: AppSettings) => void;
}

export const SettingsProvider: React.FC<SettingsProviderProps> = ({
  children,
  initialSettings,
  onSaveSettings,
}) => {
  const [settings, setSettings] = useState<AppSettings>(initialSettings);
  const [originalSettings, setOriginalSettings] = useState<AppSettings>(initialSettings);
  const [isSaving, setIsSaving] = useState(false);

  useEffect(() => {
    setSettings(initialSettings);
    setOriginalSettings(initialSettings);
  }, [initialSettings]);

  const updateSetting = <K extends keyof AppSettings>(key: K, value: AppSettings[K]) => {
    setSettings(prev => ({
      ...prev,
      [key]: value,
    }));
  };

  const saveSettings = async () => {
    setIsSaving(true);
    try {
      await onSaveSettings(settings);
      setOriginalSettings(settings);
    } catch (error) {
      console.error('Failed to save settings:', error);
    } finally {
      setIsSaving(false);
    }
  };

  const hasChanges = JSON.stringify(settings) !== JSON.stringify(originalSettings);

  const resetToDefaults = () => {
    const defaultSettings: AppSettings = {
      mode: 'manual',
      autoPublish: false,
      searchConsoleUser: null,
      gscClientId: '',
      gscClientSecret: '',
      imageSourceProvider: 'pexels',
      aiImageStyle: 'photorealistic',
      googleCloudProjectId: '',
      googleCloudLocation: 'us-central1',
      pexelsApiKey: '',
      unsplashApiKey: '',
      pixabayApiKey: '',
      seoPlugin: 'none',
      geminiApiKey: '',
      semiAutoIdeaFrequency: 'weekly',
      fullAutoPostCount: 1,
      fullAutoFrequency: 'daily',
      contentAnalysisFrequency: 'weekly',
    };
    setSettings(defaultSettings);
  };

  const contextValue: SettingsContextType = {
    settings,
    updateSetting,
    saveSettings,
    isSaving,
    hasChanges,
    resetToDefaults,
  };

  return (
    <SettingsContext.Provider value={contextValue}>
      {children}
    </SettingsContext.Provider>
  );
};

export const useSettings = () => {
  const context = useContext(SettingsContext);
  if (!context) {
    throw new Error('useSettings must be used within a SettingsProvider');
  }
  return context;
};