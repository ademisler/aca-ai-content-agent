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
                background: 'linear-gradient(135deg, #f39c12 0%, #e67e22 100%)',
                color: 'white',
                padding: '4px 12px',
                borderRadius: '12px',
                fontSize: '12px',
                fontWeight: 'bold',
                boxShadow: '0 2px 4px rgba(0,0,0,0.1)'
            }}>
                <Shield style={{ width: '12px', height: '12px', marginRight: '4px' }} />
                PRO
            </div>

            <div className="aca-card-header">
                <h2 className="aca-card-title" style={{ marginTop: '0', color: '#d68910' }}>
                    <Zap className="aca-nav-item-icon" style={{ color: '#f39c12' }} />
                    {title}
                </h2>
            </div>

            <p style={{ color: '#8b6914', marginBottom: '20px', fontSize: '14px' }}>
                {description}
            </p>

            <div style={{ marginBottom: '20px' }}>
                <h4 style={{ color: '#d68910', marginBottom: '10px', fontSize: '14px' }}>
                    🚀 Unlock Pro Features:
                </h4>
                <ul style={{ 
                    listStyle: 'none', 
                    padding: 0, 
                    margin: 0,
                    color: '#8b6914'
                }}>
                    {features.map((feature, index) => (
                        <li key={index} style={{ 
                            marginBottom: '8px', 
                            paddingLeft: '20px',
                            position: 'relative',
                            fontSize: '13px'
                        }}>
                            <span style={{
                                position: 'absolute',
                                left: '0',
                                color: '#27ae60',
                                fontWeight: 'bold'
                            }}>✓</span>
                            {feature}
                        </li>
                    ))}
                </ul>
            </div>

            <div style={{ textAlign: 'center' }}>
                <a 
                    href={gumroadUrl}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="aca-button aca-button-primary"
                    style={{
                        background: 'linear-gradient(135deg, #f39c12 0%, #e67e22 100%)',
                        border: 'none',
                        color: 'white',
                        padding: '12px 24px',
                        borderRadius: '6px',
                        textDecoration: 'none',
                        fontWeight: 'bold',
                        display: 'inline-block',
                        boxShadow: '0 3px 6px rgba(0,0,0,0.1)',
                        transition: 'all 0.3s ease'
                    }}
                    onMouseOver={(e) => {
                        e.currentTarget.style.transform = 'translateY(-2px)';
                        e.currentTarget.style.boxShadow = '0 5px 10px rgba(0,0,0,0.2)';
                    }}
                    onMouseOut={(e) => {
                        e.currentTarget.style.transform = 'translateY(0)';
                        e.currentTarget.style.boxShadow = '0 3px 6px rgba(0,0,0,0.1)';
                    }}
                >
                    <Shield style={{ width: '16px', height: '16px', marginRight: '8px' }} />
                    Upgrade to Pro
                </a>
            </div>

            <div style={{ 
                textAlign: 'center', 
                marginTop: '15px',
                fontSize: '12px',
                color: '#a67c00'
            }}>
                💡 One-time purchase • Lifetime updates • 30-day money-back guarantee
            </div>
        </div>
    );
};