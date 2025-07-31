import React, { useState } from 'react';
import type { AppSettings } from '../types';
import { Image } from './Icons';
import { SettingsLayout } from './SettingsLayout';

interface SettingsContentProps {
    settings: AppSettings;
    onSaveSettings: (settings: AppSettings) => void;
    onShowToast: (message: string, type: 'success' | 'error' | 'warning' | 'info') => void;
}

export const SettingsContent: React.FC<SettingsContentProps> = ({ 
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
            onShowToast('Content settings saved successfully!', 'success');
        } catch (error) {
            onShowToast('Failed to save content settings', 'error');
        } finally {
            setIsSaving(false);
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
            icon={<Image style={{ width: '24px', height: '24px', color: 'white' }} />}
            actions={saveButton}
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
        </SettingsLayout>
    );
};