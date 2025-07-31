import React, { useState, useEffect } from 'react';
import type { AppSettings } from '../types';
import { Shield, CheckCircle, Spinner } from './Icons';
import { SettingsLayout } from './SettingsLayout';
import { licenseApi } from '../services/wordpressApi';

interface SettingsLicenseProps {
    settings: AppSettings;
    onSaveSettings: (settings: AppSettings) => void;
    onShowToast: (message: string, type: 'success' | 'error' | 'warning' | 'info') => void;
}

export const SettingsLicense: React.FC<SettingsLicenseProps> = ({ 
    settings, 
    onSaveSettings, 
    onShowToast 
}) => {
    // License-related state
    const [licenseKey, setLicenseKey] = useState('');
    const [licenseStatus, setLicenseStatus] = useState<{
        status: string, 
        is_active: boolean, 
        verified_at?: string
    }>({status: 'inactive', is_active: false});
    const [isVerifyingLicense, setIsVerifyingLicense] = useState(false);
    const [isLoadingLicenseStatus, setIsLoadingLicenseStatus] = useState(true);
    const [isDirty, setIsDirty] = useState(false);

    // Load license status on component mount
    useEffect(() => {
        const loadLicenseStatus = async () => {
            try {
                const status = await licenseApi.getStatus();
                setLicenseStatus({
                    status: status.status || 'inactive',
                    is_active: status.is_active || false,
                    verified_at: status.verified_at
                });
            } catch (error) {
                console.error('Failed to load license status:', error);
                // Keep default inactive status
            } finally {
                setIsLoadingLicenseStatus(false);
            }
        };
        
        loadLicenseStatus();
    }, []);

    const handleLicenseVerification = async () => {
        if (!licenseKey.trim()) {
            onShowToast('Please enter a license key', 'warning');
            return;
        }

        setIsVerifyingLicense(true);
        try {
            const result = await licenseApi.verify(licenseKey);
            
            if (result.success) {
                setLicenseStatus({
                    status: 'active',
                    is_active: true,
                    verified_at: new Date().toISOString()
                });
                setLicenseKey('');
                onShowToast('License verified successfully! Pro features are now active.', 'success');
                
                // Update settings to reflect Pro status
                const updatedSettings = { ...settings, is_pro: true };
                onSaveSettings(updatedSettings);
            } else {
                onShowToast(result.message || 'License verification failed', 'error');
            }
        } catch (error) {
            console.error('License verification error:', error);
            onShowToast('Failed to verify license. Please try again.', 'error');
        } finally {
            setIsVerifyingLicense(false);
        }
    };

    const handleLicenseDeactivation = async () => {
        if (!confirm('Are you sure you want to deactivate your Pro license? This will disable all Pro features.')) {
            return;
        }

        setIsVerifyingLicense(true);
        try {
            const result = await licenseApi.deactivate();
            
            if (result.success) {
                setLicenseStatus({
                    status: 'inactive',
                    is_active: false
                });
                onShowToast('License deactivated successfully. You can now use it on another site.', 'success');
                
                // Update settings to reflect free status
                const updatedSettings = { ...settings, is_pro: false };
                onSaveSettings(updatedSettings);
            } else {
                onShowToast(result.message || 'License deactivation failed', 'error');
            }
        } catch (error) {
            console.error('License deactivation error:', error);
            onShowToast('Failed to deactivate license. Please try again.', 'error');
        } finally {
            setIsVerifyingLicense(false);
        }
    };

    if (isLoadingLicenseStatus) {
        return (
            <SettingsLayout
                title="Pro License"
                description="Unlock advanced features and automation capabilities"
                icon={<Shield style={{ width: '24px', height: '24px', color: 'white' }} />}
            >
                <div style={{ padding: '40px', textAlign: 'center', color: '#666' }}>
                    <Spinner style={{ width: '24px', height: '24px', marginBottom: '16px' }} />
                    <div>Loading license status...</div>
                </div>
            </SettingsLayout>
        );
    }

    return (
        <SettingsLayout
            title="Pro License"
            description="Unlock advanced features and automation capabilities"
            icon={<Shield style={{ width: '24px', height: '24px', color: 'white' }} />}
        >
            <div className="aca-stat-item" style={{ margin: '0 0 20px 0' }}>
                <div className="aca-stat-info">
                    <div className="aca-stat-icon">
                        {licenseStatus.is_active ? (
                            <CheckCircle style={{ color: '#27ae60', width: '20px', height: '20px' }} />
                        ) : (
                            <Shield style={{ color: '#e74c3c', width: '20px', height: '20px' }} />
                        )}
                    </div>
                    <div>
                        <div className="aca-stat-number">
                            {licenseStatus.is_active ? 'Pro Active' : 'Free Version'}
                        </div>
                        <div className="aca-stat-label">
                            {licenseStatus.is_active 
                                ? `Verified ${licenseStatus.verified_at ? new Date(licenseStatus.verified_at).toLocaleDateString() : ''}`
                                : 'Upgrade to unlock Pro features'
                            }
                        </div>
                    </div>
                </div>
            </div>

            {!licenseStatus.is_active && (
                <div className="aca-form-group">
                    <label className="aca-label" htmlFor="license-key">License Key</label>
                    <div style={{ display: 'flex', gap: '10px' }}>
                        <input
                            id="license-key"
                            type="text"
                            className="aca-input"
                            value={licenseKey}
                            onChange={(e) => setLicenseKey(e.target.value)}
                            placeholder="Enter your Pro license key"
                            disabled={isVerifyingLicense}
                        />
                        <button
                            onClick={handleLicenseVerification}
                            disabled={isVerifyingLicense || !licenseKey.trim()}
                            className="aca-button aca-button-primary"
                            style={{ minWidth: '120px' }}
                        >
                            {isVerifyingLicense ? (
                                <>
                                    <Spinner className="aca-spinner" />
                                    Verifying...
                                </>
                            ) : (
                                'Verify License'
                            )}
                        </button>
                    </div>
                    <p className="aca-page-description" style={{ marginTop: '10px' }}>
                        Don't have a Pro license? <a href="https://ademisler.gumroad.com/l/ai-content-agent-pro" target="_blank" rel="noopener noreferrer" style={{ color: '#0073aa' }}>Purchase here</a>
                    </p>
                </div>
            )}

            {licenseStatus.is_active && (
                <>
                    <div className="aca-alert aca-alert-success" style={{ margin: '20px 0' }}>
                        <CheckCircle style={{ width: '16px', height: '16px', marginRight: '8px' }} />
                        Pro license is active! You now have access to all premium features.
                    </div>
                    <div style={{ marginTop: '15px' }}>
                        <button
                            onClick={handleLicenseDeactivation}
                            disabled={isVerifyingLicense}
                            className="aca-button aca-button-secondary"
                            style={{ 
                                minWidth: '140px',
                                backgroundColor: '#dc3545',
                                borderColor: '#dc3545',
                                color: '#ffffff'
                            }}
                        >
                            {isVerifyingLicense ? (
                                <>
                                    <Spinner className="aca-spinner" />
                                    Deactivating...
                                </>
                            ) : (
                                'Deactivate License'
                            )}
                        </button>
                        <p className="aca-page-description" style={{ marginTop: '8px', fontSize: '12px' }}>
                            This will disable all Pro features and allow you to use the license on another site.
                        </p>
                    </div>
                </>
            )}
        </SettingsLayout>
    );
};