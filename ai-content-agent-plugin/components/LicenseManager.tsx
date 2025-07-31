import React, { useState, useEffect } from 'react';

interface LicenseStatus {
    status: string;
    license_key: string;
    license_key_masked: string;
    is_active: boolean;
    data: any;
    features: {
        [key: string]: boolean;
    };
    migration_notice: {
        message: string;
        timestamp: string;
        dismissed: boolean;
    } | false;
}

const LicenseManager: React.FC = () => {
    const [licenseStatus, setLicenseStatus] = useState<LicenseStatus | null>(null);
    const [licenseKey, setLicenseKey] = useState('');
    const [loading, setLoading] = useState(false);
    const [message, setMessage] = useState('');
    const [showMigrationNotice, setShowMigrationNotice] = useState(false);

    useEffect(() => {
        fetchLicenseStatus();
    }, []);

    const fetchLicenseStatus = async () => {
        try {
            const response = await fetch('/wp-json/aca/v1/license/status', {
                method: 'GET',
                headers: {
                    'X-WP-Nonce': (window as any).acaData.nonce,
                    'Content-Type': 'application/json'
                }
            });

            const data = await response.json();
            
            if (data.success) {
                setLicenseStatus(data.data);
                
                // Show migration notice if exists
                if (data.data.migration_notice && !data.data.migration_notice.dismissed) {
                    setShowMigrationNotice(true);
                }
            }
        } catch (error) {
            console.error('Failed to fetch license status:', error);
        }
    };

    const activateLicense = async () => {
        if (!licenseKey.trim()) {
            setMessage('Please enter a license key');
            return;
        }

        setLoading(true);
        setMessage('');

        try {
            const response = await fetch('/wp-json/aca/v1/license/activate', {
                method: 'POST',
                headers: {
                    'X-WP-Nonce': (window as any).acaData.nonce,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    license_key: licenseKey
                })
            });

            const data = await response.json();

            if (data.success) {
                setMessage('License activated successfully!');
                setLicenseKey('');
                setShowMigrationNotice(false);
                await fetchLicenseStatus();
            } else {
                setMessage(data.data || 'License activation failed');
            }
        } catch (error) {
            setMessage('Connection error. Please try again.');
        } finally {
            setLoading(false);
        }
    };

    const deactivateLicense = async () => {
        if (!confirm('Are you sure you want to deactivate your license?')) {
            return;
        }

        setLoading(true);
        setMessage('');

        try {
            const response = await fetch('/wp-json/aca/v1/license/deactivate', {
                method: 'POST',
                headers: {
                    'X-WP-Nonce': (window as any).acaData.nonce,
                    'Content-Type': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                setMessage('License deactivated successfully');
                await fetchLicenseStatus();
            } else {
                setMessage(data.data || 'License deactivation failed');
            }
        } catch (error) {
            setMessage('Connection error. Please try again.');
        } finally {
            setLoading(false);
        }
    };

    const dismissMigrationNotice = async () => {
        try {
            await fetch('/wp-json/aca/v1/license/dismiss-migration-notice', {
                method: 'POST',
                headers: {
                    'X-WP-Nonce': (window as any).acaData.nonce,
                    'Content-Type': 'application/json'
                }
            });
            setShowMigrationNotice(false);
        } catch (error) {
            console.error('Failed to dismiss notice:', error);
        }
    };

    if (!licenseStatus) {
        return <div className="loading">Loading license information...</div>;
    }

    return (
        <div className="license-manager">
            {/* Migration Notice */}
            {showMigrationNotice && licenseStatus.migration_notice && (
                <div className="notice notice-warning is-dismissible">
                    <p>
                        <strong>Important:</strong> {licenseStatus.migration_notice.message}
                    </p>
                    <button 
                        type="button" 
                        className="notice-dismiss"
                        onClick={dismissMigrationNotice}
                    >
                        <span className="screen-reader-text">Dismiss this notice.</span>
                    </button>
                </div>
            )}

            <div className="license-status-card">
                <h3>License Status</h3>
                
                <div className="license-info">
                    <div className={`status-badge ${licenseStatus.is_active ? 'active' : 'inactive'}`}>
                        {licenseStatus.is_active ? '✓ Active' : '✗ Inactive'}
                    </div>
                    
                    {licenseStatus.license_key_masked && (
                        <div className="license-key-display">
                            <strong>License Key:</strong> {licenseStatus.license_key_masked}
                        </div>
                    )}
                </div>

                {!licenseStatus.is_active ? (
                    <div className="license-activation">
                        <h4>Activate Pro License</h4>
                        <p>Enter your Pro license key to access premium features:</p>
                        
                        <div className="activation-form">
                            <input
                                type="text"
                                value={licenseKey}
                                onChange={(e) => setLicenseKey(e.target.value)}
                                placeholder="Enter your license key"
                                className="license-key-input"
                                disabled={loading}
                            />
                            <button
                                onClick={activateLicense}
                                disabled={loading || !licenseKey.trim()}
                                className="button button-primary"
                            >
                                {loading ? 'Activating...' : 'Activate License'}
                            </button>
                        </div>
                    </div>
                ) : (
                    <div className="license-management">
                        <h4>License Management</h4>
                        <button
                            onClick={deactivateLicense}
                            disabled={loading}
                            className="button button-secondary"
                        >
                            {loading ? 'Deactivating...' : 'Deactivate License'}
                        </button>
                    </div>
                )}

                {message && (
                    <div className={`message ${message.includes('successfully') ? 'success' : 'error'}`}>
                        {message}
                    </div>
                )}
            </div>

            {/* Feature Availability */}
            <div className="features-card">
                <h3>Feature Availability</h3>
                <div className="features-grid">
                    {Object.entries(licenseStatus.features).map(([feature, available]) => (
                        <div key={feature} className={`feature-item ${available ? 'available' : 'unavailable'}`}>
                            <span className="feature-icon">
                                {available ? '✓' : '✗'}
                            </span>
                            <span className="feature-name">
                                {feature.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}
                            </span>
                        </div>
                    ))}
                </div>
            </div>

            <style jsx>{`
                .license-manager {
                    max-width: 800px;
                    margin: 20px 0;
                }

                .notice {
                    background: #fff3cd;
                    border: 1px solid #ffeaa7;
                    border-radius: 4px;
                    padding: 12px;
                    margin-bottom: 20px;
                    position: relative;
                }

                .notice-dismiss {
                    position: absolute;
                    top: 8px;
                    right: 8px;
                    background: none;
                    border: none;
                    cursor: pointer;
                    font-size: 16px;
                }

                .license-status-card, .features-card {
                    background: #fff;
                    border: 1px solid #ddd;
                    border-radius: 8px;
                    padding: 20px;
                    margin-bottom: 20px;
                    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                }

                .license-info {
                    display: flex;
                    align-items: center;
                    gap: 20px;
                    margin-bottom: 20px;
                }

                .status-badge {
                    padding: 6px 12px;
                    border-radius: 4px;
                    font-weight: bold;
                    font-size: 14px;
                }

                .status-badge.active {
                    background: #d4edda;
                    color: #155724;
                    border: 1px solid #c3e6cb;
                }

                .status-badge.inactive {
                    background: #f8d7da;
                    color: #721c24;
                    border: 1px solid #f5c6cb;
                }

                .activation-form {
                    display: flex;
                    gap: 10px;
                    align-items: center;
                    margin-top: 10px;
                }

                .license-key-input {
                    flex: 1;
                    padding: 8px 12px;
                    border: 1px solid #ddd;
                    border-radius: 4px;
                    font-family: monospace;
                }

                .features-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                    gap: 10px;
                    margin-top: 15px;
                }

                .feature-item {
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    padding: 8px 12px;
                    border-radius: 4px;
                    font-size: 14px;
                }

                .feature-item.available {
                    background: #d4edda;
                    color: #155724;
                }

                .feature-item.unavailable {
                    background: #f8f9fa;
                    color: #6c757d;
                }

                .message {
                    margin-top: 15px;
                    padding: 10px;
                    border-radius: 4px;
                    font-weight: bold;
                }

                .message.success {
                    background: #d4edda;
                    color: #155724;
                    border: 1px solid #c3e6cb;
                }

                .message.error {
                    background: #f8d7da;
                    color: #721c24;
                    border: 1px solid #f5c6cb;
                }

                .loading {
                    text-align: center;
                    padding: 40px;
                    color: #666;
                }
            `}</style>
        </div>
    );
};

export default LicenseManager;