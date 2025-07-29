import React from 'react';
import type { AppSettings } from '../types';
import { SettingsProvider } from './settings/SettingsProvider';
import { AutomationModeSection } from './settings/AutomationModeSection';
import { IntegrationsSection } from './settings/IntegrationsSection';
import { useSettings } from './settings/SettingsProvider';
import { Spinner, Settings as SettingsIcon } from './Icons';

interface SettingsProps {
  settings: AppSettings;
  onSaveSettings: (settings: AppSettings) => void;
}

const SettingsHeader: React.FC = () => {
  const { hasChanges, isSaving, saveSettings, resetToDefaults } = useSettings();

  return (
    <div className="aca-page-header">
      <div>
        <h1 className="aca-page-title">
          <SettingsIcon size={24} />
          Settings
        </h1>
        <p className="aca-page-description">
          Configure your AI Content Agent to work exactly how you want it.
        </p>
      </div>
      
      <div style={{ display: 'flex', gap: '12px', alignItems: 'center' }}>
        {hasChanges && (
          <div className="aca-alert warning" style={{ 
            margin: 0, 
            padding: '8px 12px',
            fontSize: '14px',
          }}>
            You have unsaved changes
          </div>
        )}
        
        <button
          type="button"
          onClick={resetToDefaults}
          className="aca-button secondary"
          disabled={isSaving}
        >
          Reset to Defaults
        </button>
        
        <button
          type="button"
          onClick={saveSettings}
          className="aca-button primary"
          disabled={!hasChanges || isSaving}
          style={{
            display: 'flex',
            alignItems: 'center',
            gap: '8px'
          }}
        >
          {isSaving && <Spinner size={16} />}
          {isSaving ? 'Saving...' : 'Save Settings'}
        </button>
      </div>
    </div>
  );
};

const SettingsContent: React.FC = () => {
  return (
    <div className="aca-container">
      <SettingsHeader />
      
      <div style={{ display: 'grid', gap: '32px' }}>
        <AutomationModeSection />
        <IntegrationsSection />
      </div>
    </div>
  );
};

export const Settings: React.FC<SettingsProps> = ({ settings, onSaveSettings }) => {
  return (
    <SettingsProvider initialSettings={settings} onSaveSettings={onSaveSettings}>
      <SettingsContent />
    </SettingsProvider>
  );
};