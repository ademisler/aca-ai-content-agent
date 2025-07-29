import React from 'react';
import { Shield, Zap } from './Icons';

interface UpgradePromptProps {
    title: string;
    description: string;
    features: string[];
    gumroadUrl?: string;
}

export const UpgradePrompt: React.FC<UpgradePromptProps> = ({ 
    title, 
    description, 
    features, 
    gumroadUrl = "https://gumroad.com/l/your-product-link" 
}) => {
    return (
        <div className="aca-card" style={{ 
            margin: 0, 
            border: '2px solid #f0ad4e',
            background: 'linear-gradient(135deg, #fff9e6 0%, #ffeaa7 100%)',
            position: 'relative',
            overflow: 'hidden'
        }}>
            {/* Pro Badge */}
            <div style={{
                position: 'absolute',
                top: '15px',
                right: '15px',
                background: '#f0ad4e',
                color: '#ffffff',
                padding: '4px 12px',
                borderRadius: '12px',
                fontSize: '11px',
                fontWeight: '600',
                textTransform: 'uppercase',
                letterSpacing: '0.5px',
                display: 'flex',
                alignItems: 'center',
                gap: '4px'
            }}>
                <Shield style={{ width: '12px', height: '12px' }} />
                Pro Feature
            </div>

            <div className="aca-card-header">
                <h3 className="aca-card-title" style={{ 
                    display: 'flex', 
                    alignItems: 'center', 
                    gap: '8px',
                    color: '#d68910'
                }}>
                    <Zap style={{ width: '20px', height: '20px' }} />
                    {title}
                </h3>
            </div>

            <div style={{ padding: '0 20px 20px' }}>
                <p style={{ 
                    margin: '0 0 15px 0', 
                    color: '#8b6914',
                    fontSize: '14px',
                    lineHeight: '1.5'
                }}>
                    {description}
                </p>

                <div style={{ marginBottom: '20px' }}>
                    <h4 style={{ 
                        margin: '0 0 10px 0', 
                        fontSize: '13px', 
                        fontWeight: '600',
                        color: '#8b6914'
                    }}>
                        🚀 Pro Features Include:
                    </h4>
                    <ul style={{ 
                        margin: 0, 
                        paddingLeft: '20px',
                        color: '#8b6914'
                    }}>
                        {features.map((feature, index) => (
                            <li key={index} style={{ 
                                marginBottom: '5px',
                                fontSize: '13px'
                            }}>
                                {feature}
                            </li>
                        ))}
                    </ul>
                </div>

                <div style={{ 
                    display: 'flex', 
                    gap: '10px', 
                    flexWrap: 'wrap',
                    alignItems: 'center'
                }}>
                    <a
                        href={gumroadUrl}
                        target="_blank"
                        rel="noopener noreferrer"
                        className="aca-button"
                        style={{
                            background: '#f0ad4e',
                            borderColor: '#f0ad4e',
                            color: '#ffffff',
                            fontWeight: '600',
                            textDecoration: 'none',
                            display: 'inline-flex',
                            alignItems: 'center',
                            gap: '6px'
                        }}
                    >
                        <Shield style={{ width: '16px', height: '16px' }} />
                        Upgrade to Pro
                    </a>
                    
                    <span style={{ 
                        fontSize: '12px', 
                        color: '#8b6914',
                        fontStyle: 'italic'
                    }}>
                        Already have a license? Activate it in the License section below.
                    </span>
                </div>
            </div>
        </div>
    );
};