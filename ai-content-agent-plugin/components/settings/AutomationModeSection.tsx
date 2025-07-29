import React from 'react';
import type { AutomationMode } from '../../types';
import { useSettings } from './SettingsProvider';

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

const AutomationFrequencySection: React.FC<{
  mode: AutomationMode;
}> = ({ mode }) => {
  const { settings, updateSetting } = useSettings();

  if (mode === 'manual') return null;

  return (
    <div style={{ marginTop: '24px' }}>
      {mode === 'semi-auto' && (
        <div>
          <label className="aca-label">
            Idea Generation Frequency
          </label>
          <select
            value={settings.semiAutoIdeaFrequency}
            onChange={(e) => updateSetting('semiAutoIdeaFrequency', e.target.value as 'daily' | 'weekly' | 'monthly')}
            className="aca-input"
          >
            <option value="daily">Daily</option>
            <option value="weekly">Weekly</option>
            <option value="monthly">Monthly</option>
          </select>
        </div>
      )}

      {mode === 'full-auto' && (
        <>
          <div style={{ marginBottom: '16px' }}>
            <label className="aca-label">
              Posts per Batch
            </label>
            <input
              type="number"
              min="1"
              max="10"
              value={settings.fullAutoPostCount}
              onChange={(e) => updateSetting('fullAutoPostCount', parseInt(e.target.value))}
              className="aca-input"
            />
          </div>
          <div>
            <label className="aca-label">
              Publishing Frequency
            </label>
            <select
              value={settings.fullAutoFrequency}
              onChange={(e) => updateSetting('fullAutoFrequency', e.target.value as 'daily' | 'weekly' | 'monthly')}
              className="aca-input"
            >
              <option value="daily">Daily</option>
              <option value="weekly">Weekly</option>
              <option value="monthly">Monthly</option>
            </select>
          </div>
        </>
      )}

      <div style={{ marginTop: '16px' }}>
        <label className="aca-label">
          Content Analysis Frequency
        </label>
        <select
          value={settings.contentAnalysisFrequency}
          onChange={(e) => updateSetting('contentAnalysisFrequency', e.target.value as 'daily' | 'weekly' | 'monthly')}
          className="aca-input"
        >
          <option value="daily">Daily</option>
          <option value="weekly">Weekly</option>
          <option value="monthly">Monthly</option>
        </select>
      </div>

      <div style={{ marginTop: '16px' }}>
        <label style={{ display: 'flex', alignItems: 'center', gap: '8px', cursor: 'pointer' }}>
          <input
            type="checkbox"
            checked={settings.autoPublish}
            onChange={(e) => updateSetting('autoPublish', e.target.checked)}
            style={{ accentColor: '#0073aa' }}
          />
          <span className="aca-label" style={{ margin: 0 }}>
            Auto-publish generated content
          </span>
        </label>
        <p className="aca-page-description" style={{ marginTop: '4px', marginLeft: '26px' }}>
          When enabled, generated posts will be automatically published. Otherwise, they'll be saved as drafts.
        </p>
      </div>
    </div>
  );
};

export const AutomationModeSection: React.FC = () => {
  const { settings, updateSetting } = useSettings();

  return (
    <div className="aca-card">
      <div className="aca-card-header">
        <h2 className="aca-card-title">Automation Mode</h2>
        <p className="aca-page-description">
          Choose how you want the AI Content Agent to work for you.
        </p>
      </div>

      <div style={{ display: 'grid', gap: '16px' }}>
        <RadioCard
          id="manual"
          title="Manual Mode"
          description="You control everything. Generate ideas and create content when you want."
          currentSelection={settings.mode}
          onChange={(mode) => updateSetting('mode', mode)}
        />
        
        <RadioCard
          id="semi-auto"
          title="Semi-Automatic Mode"
          description="AI generates content ideas automatically based on your schedule. You review and create content from these ideas."
          currentSelection={settings.mode}
          onChange={(mode) => updateSetting('mode', mode)}
        />
        
        <RadioCard
          id="full-auto"
          title="Full Automatic Mode"
          description="AI handles everything - generates ideas, creates content, and can even publish automatically based on your preferences."
          currentSelection={settings.mode}
          onChange={(mode) => updateSetting('mode', mode)}
        />
      </div>

      <AutomationFrequencySection mode={settings.mode} />
    </div>
  );
};