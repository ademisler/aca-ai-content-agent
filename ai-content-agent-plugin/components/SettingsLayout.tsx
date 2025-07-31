import React from 'react';

interface SettingsLayoutProps {
    title: string;
    description: string;
    icon: React.ReactNode;
    children: React.ReactNode;
    actions?: React.ReactNode;
}

export const SettingsLayout: React.FC<SettingsLayoutProps> = ({ 
    title, 
    description, 
    icon, 
    children, 
    actions 
}) => {
    return (
        <div className="aca-page">
            <div className="aca-page-header" style={{
                background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                color: 'white',
                padding: '32px',
                borderRadius: '12px',
                marginBottom: '32px',
                position: 'relative',
                overflow: 'hidden'
            }}>
                <div style={{ position: 'relative', zIndex: 2 }}>
                    <div style={{ display: 'flex', alignItems: 'center', gap: '16px', marginBottom: '12px' }}>
                        <div style={{
                            width: '48px',
                            height: '48px',
                            background: 'rgba(255,255,255,0.2)',
                            borderRadius: '12px',
                            display: 'flex',
                            alignItems: 'center',
                            justifyContent: 'center'
                        }}>
                            {icon}
                        </div>
                        <div>
                            <h1 style={{ fontSize: '28px', fontWeight: '700', margin: 0 }}>
                                {title}
                            </h1>
                        </div>
                    </div>
                    <p style={{ 
                        fontSize: '14px', 
                        opacity: 0.85,
                        margin: 0,
                        maxWidth: '600px',
                        lineHeight: '1.5'
                    }}>
                        {description}
                    </p>
                </div>
            </div>
            
            <div className="aca-settings-content">
                {children}
            </div>
            
            {actions && (
                <div className="aca-settings-actions" style={{ 
                    marginTop: '32px',
                    padding: '24px',
                    background: '#f8fafc',
                    borderRadius: '12px',
                    borderTop: '1px solid #e2e8f0'
                }}>
                    {actions}
                </div>
            )}
        </div>
    );
};