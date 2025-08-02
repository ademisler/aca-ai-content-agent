import React, { useState, useEffect } from 'react';
import type { AppSettings, AutomationMode } from '../types';
import { Zap, Shield } from './Icons';
import { SettingsLayout } from './SettingsLayout';
import { UpgradePrompt } from './UpgradePrompt';
import { licenseApi } from '../services/wordpressApi';

interface SettingsAutomationProps {
    settings: AppSettings;
    onSaveSettings: (settings: AppSettings) => void;
    onShowToast: (message: string, type: 'success' | 'error' | 'warning' | 'info') => void;
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

export const SettingsAutomation: React.FC<SettingsAutomationProps> = ({ 
    settings, 
    onSaveSettings, 
    onShowToast
}) => {
    const [currentSettings, setCurrentSettings] = useState<AppSettings>(settings);
    const [licenseStatus, setLicenseStatus] = useState<{
        status: string, 
        is_active: boolean, 
        verified_at?: string
    }>({status: 'inactive', is_active: false});
    const [isLoadingLicenseStatus, setIsLoadingLicenseStatus] = useState(true);
    const [isDirty, setIsDirty] = useState(false);
    const [isSaving, setIsSaving] = useState(false);

    // Load license status on component mount
    useEffect(() => {
        const loadLicenseStatus = async () => {
            try {
                const data = await licenseApi.getStatus();
                setLicenseStatus({
                    status: data.status || 'inactive',
                    is_active: data.is_active || false,
                    verified_at: data.verified_at || undefined
                });
            } catch (error) {
                console.error('Failed to load license status:', error);
                // Set default inactive state on error
                setLicenseStatus({
                    status: 'inactive',
                    is_active: false,
                    verified_at: undefined
                });
            } finally {
                setIsLoadingLicenseStatus(false);
            }
        };
        
        loadLicenseStatus();
    }, []);
    
    // Sync license status with current settings
    useEffect(() => {
        if (licenseStatus && licenseStatus.is_active !== undefined) {
            // setIsProActive(licenseStatus.is_active); // This line was removed
        }
    }, [licenseStatus]);

    const isProActive = () => {
        // Keep consistent with Settings.tsx logic
        return currentSettings.is_pro || licenseStatus.is_active;
    };

    const handleModeChange = (mode: AutomationMode) => {
        const updatedSettings = { ...currentSettings, mode };
        setCurrentSettings(updatedSettings);
        setIsDirty(true);
    };

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
            onShowToast('Automation settings saved successfully!', 'success');
        } catch (error) {
            onShowToast('Failed to save automation settings', 'error');
        } finally {
            setIsSaving(false);
        }
    };

    if (isLoadingLicenseStatus) {
        return (
            <SettingsLayout
                title="Automation Mode"
                description="Configure how AI Content Agent creates and publishes content automatically"
                icon={<Zap style={{ width: '24px', height: '24px', color: 'white' }} />}
            >
                <div style={{ padding: '40px', textAlign: 'center', color: '#666' }}>
                    Loading license status...
                </div>
            </SettingsLayout>
        );
    }

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
            title="Automation Mode"
            description="Configure how AI Content Agent creates and publishes content automatically"
            icon={<Zap style={{ width: '24px', height: '24px', color: 'white' }} />}
            actions={saveButton}
        >
            {isProActive() ? (
                <div>
                    <p className="aca-page-description" style={{ marginBottom: '20px' }}>
                        Choose how you want the AI Content Agent (ACA) to operate. You can change this at any time.
                    </p>
                
                    <div style={{ display: 'flex', flexDirection: 'column', gap: '15px' }}>
                        <RadioCard 
                            id="manual" 
                            title="Manual Mode" 
                            description="You are in full control. Manually generate ideas and create drafts one by one." 
                            currentSelection={currentSettings.mode} 
                            onChange={handleModeChange} 
                        />
                        
                        <div className="aca-card" style={{
                            margin: 0,
                            border: '2px solid',
                            borderColor: currentSettings.mode === 'semi-automatic' ? '#0073aa' : '#ccd0d4',
                            background: currentSettings.mode === 'semi-automatic' ? '#f0f6fc' : '#ffffff',
                            boxShadow: currentSettings.mode === 'semi-automatic' ? '0 2px 4px rgba(0, 0, 0, 0.1)' : 'none'
                        }}>
                            <label htmlFor="semi-automatic" style={{ display: 'flex', alignItems: 'flex-start', cursor: 'pointer', gap: '12px' }}>
                                <input 
                                    type="radio" 
                                    id="semi-automatic" 
                                    name="automation-mode" 
                                    checked={currentSettings.mode === 'semi-automatic'} 
                                    onChange={() => handleModeChange('semi-automatic')} 
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
                                        Semi-Automatic Mode
                                    </h4>
                                    <p className="aca-page-description" style={{ margin: 0 }}>
                                        The AI automatically generates new ideas periodically. You choose which ideas to turn into drafts.
                                    </p>
                                </div>
                            </label>
                            
                            {currentSettings.mode === 'semi-automatic' && (
                                <div className="aca-form-group" style={{ 
                                    paddingLeft: '30px', 
                                    paddingTop: '20px', 
                                    marginTop: '20px', 
                                    borderTop: '1px solid #e0e0e0',
                                    marginBottom: 0
                                }}>
                                    <label className="aca-label" htmlFor="semi-auto-frequency">Idea Generation Frequency</label>
                                    <select 
                                        id="semi-auto-frequency"
                                        className="aca-input" 
                                        value={currentSettings.semiAutoIdeaFrequency || 'weekly'} 
                                        onChange={(e) => handleSettingChange('semiAutoIdeaFrequency', e.target.value)}
                                        style={{ marginTop: '5px' }}
                                    >
                                        <option value="daily">Daily - Generate new ideas every day</option>
                                        <option value="weekly">Weekly - Generate new ideas every week</option>
                                        <option value="monthly">Monthly - Generate new ideas every month</option>
                                    </select>
                                    <p className="aca-page-description" style={{ marginTop: '5px', margin: '5px 0 0 0' }}>
                                        How often should the AI automatically generate new content ideas?
                                    </p>
                                </div>
                            )}
                        </div>
                        
                        <div className="aca-card" style={{
                            margin: 0,
                            border: '2px solid',
                            borderColor: currentSettings.mode === 'full-automatic' ? '#0073aa' : '#ccd0d4',
                            background: currentSettings.mode === 'full-automatic' ? '#f0f6fc' : '#ffffff',
                            boxShadow: currentSettings.mode === 'full-automatic' ? '0 2px 4px rgba(0, 0, 0, 0.1)' : 'none'
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
                                <div>
                                    <h4 className="aca-card-title" style={{ marginBottom: '8px' }}>
                                        Full-Automatic Mode (Set & Forget)
                                    </h4>
                                    <p className="aca-page-description" style={{ margin: 0 }}>
                                        The AI handles everything: generates ideas, picks the best ones, and creates drafts automatically.
                                    </p>
                                </div>
                            </label>
                            
                            {currentSettings.mode === 'full-automatic' && (
                                <div style={{ 
                                    paddingLeft: '30px', 
                                    paddingTop: '20px', 
                                    marginTop: '20px', 
                                    borderTop: '1px solid #e0e0e0',
                                    marginBottom: 0
                                }}>
                                    <div className="aca-form-group" style={{ marginBottom: '20px' }}>
                                        <label className="aca-label" htmlFor="daily-post-count">Daily Post Count</label>
                                        <select 
                                            id="daily-post-count"
                                            className="aca-input" 
                                            value={currentSettings.fullAutoDailyPostCount || 1} 
                                            onChange={(e) => handleSettingChange('fullAutoDailyPostCount', parseInt(e.target.value))}
                                            style={{ marginTop: '5px' }}
                                        >
                                            <option value={1}>1 post per day</option>
                                            <option value={2}>2 posts per day</option>
                                            <option value={3}>3 posts per day</option>
                                            <option value={5}>5 posts per day</option>
                                        </select>
                                        <p className="aca-page-description" style={{ marginTop: '5px', margin: '5px 0 0 0' }}>
                                            How many posts should be created daily in full-automatic mode?
                                        </p>
                                    </div>

                                    <div className="aca-form-group" style={{ marginBottom: '20px' }}>
                                        <label className="aca-label" htmlFor="publish-frequency">Publishing Frequency</label>
                                        <select 
                                            id="publish-frequency"
                                            className="aca-input" 
                                            value={currentSettings.fullAutoPublishFrequency || 'daily'} 
                                            onChange={(e) => handleSettingChange('fullAutoPublishFrequency', e.target.value)}
                                            style={{ marginTop: '5px' }}
                                        >
                                            <option value="hourly">Every hour - Publish posts throughout the day</option>
                                            <option value="daily">Daily - Publish once per day</option>
                                            <option value="weekly">Weekly - Publish once per week</option>
                                        </select>
                                        <p className="aca-page-description" style={{ marginTop: '5px', margin: '5px 0 0 0' }}>
                                            How often should created drafts be published automatically?
                                        </p>
                                    </div>

                                    <div className="aca-form-group" style={{ marginBottom: 0 }}>
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
                                                <span className="aca-label">Enable Auto-Publish</span>
                                                <p className="aca-page-description" style={{ marginTop: '5px', margin: '5px 0 0 0' }}>
                                                    When enabled, the AI will automatically publish posts according to the frequency settings above.
                                                </p>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            ) : (
                <UpgradePrompt 
                    title="Automation Features Require Pro License"
                    description="Unlock powerful automation modes including semi-automatic and full-automatic content generation."
                    features={[
                        "Semi-Automatic Mode - AI generates ideas automatically",
                        "Full-Automatic Mode - Complete hands-off content creation",
                        "Flexible scheduling options",
                        "Auto-publish capabilities"
                    ]}
                />
            )}
        </SettingsLayout>
    );
};