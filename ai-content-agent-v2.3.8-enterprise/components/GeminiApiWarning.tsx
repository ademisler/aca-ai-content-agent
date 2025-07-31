import React from 'react';
import { AlertTriangle, Settings } from './Icons';

interface GeminiApiWarningProps {
    onNavigateToSettings: () => void;
}

export const GeminiApiWarning: React.FC<GeminiApiWarningProps> = ({ onNavigateToSettings }) => {
    return (
        <div 
            className="aca-gemini-warning"
            style={{
                backgroundColor: '#fff3cd',
                border: '1px solid #ffeaa7',
                borderRadius: '4px',
                padding: '12px 16px',
                margin: '0 0 20px 0',
                display: 'flex',
                alignItems: 'center',
                gap: '12px',
                boxShadow: '0 2px 4px rgba(0,0,0,0.1)'
            }}
        >
            <AlertTriangle 
                style={{ 
                    width: '20px', 
                    height: '20px', 
                    color: '#856404',
                    flexShrink: 0 
                }} 
            />
            <div style={{ flex: 1 }}>
                <div style={{ 
                    fontWeight: '600', 
                    color: '#856404', 
                    marginBottom: '4px',
                    fontSize: '14px'
                }}>
                    Gemini API Key Required
                </div>
                <div style={{ 
                    color: '#856404', 
                    fontSize: '13px',
                    lineHeight: '1.4'
                }}>
                    AI Content Agent (ACA) requires a Google Gemini API key to function. 
                    Please configure your API key in Settings to enable content generation, 
                    idea creation, and all AI-powered features.
                </div>
            </div>
            <button
                onClick={onNavigateToSettings}
                style={{
                    backgroundColor: '#856404',
                    color: 'white',
                    border: 'none',
                    borderRadius: '4px',
                    padding: '8px 12px',
                    fontSize: '12px',
                    fontWeight: '500',
                    cursor: 'pointer',
                    display: 'flex',
                    alignItems: 'center',
                    gap: '6px',
                    flexShrink: 0,
                    transition: 'background-color 0.2s'
                }}
                onMouseOver={(e) => e.currentTarget.style.backgroundColor = '#6c5ce7'}
                onMouseOut={(e) => e.currentTarget.style.backgroundColor = '#856404'}
            >
                <Settings style={{ width: '14px', height: '14px' }} />
                Go to Settings
            </button>
        </div>
    );
};