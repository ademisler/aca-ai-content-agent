
import React, { useState, useEffect } from 'react';
import type { StyleGuide } from '../types';
import { Spinner, BookOpen, CheckCircle, RefreshCw } from './Icons';

interface StyleGuideManagerProps {
    styleGuide: StyleGuide | null;
    onAnalyze: () => void;
    onSaveStyleGuide: (styleGuide: StyleGuide) => void;
    isLoading: boolean;
}

const PREDEFINED_TONES = ['Friendly', 'Conversational', 'Formal', 'Professional', 'Technical', 'Informative', 'Witty', 'Humorous'];

const sentenceStructureMap: { [key: number]: string } = {
    0: 'Primarily short and direct sentences',
    25: 'Mostly short sentences with some variation',
    50: 'Mix of short, punchy sentences and longer, more descriptive ones',
    75: 'Mostly longer sentences with some short ones for impact',
    100: 'Primarily complex sentences with multiple clauses',
};

const getClosestSliderValue = (description: string) => {
    if (!description) return 50;
    const entry = Object.entries(sentenceStructureMap).find(([, value]) => value === description);
    return entry ? parseInt(entry[0], 10) : 50;
};

export const StyleGuideManager: React.FC<StyleGuideManagerProps> = ({ styleGuide, onAnalyze, onSaveStyleGuide, isLoading }) => {
    const [editableGuide, setEditableGuide] = useState<StyleGuide | null>(styleGuide);
    const [isDirty, setIsDirty] = useState(false);
    const [isSaving, setIsSaving] = useState(false);
    const [selectedTones, setSelectedTones] = useState<Set<string>>(new Set());
    const [sentenceSliderValue, setSentenceSliderValue] = useState(50);

    useEffect(() => {
        setEditableGuide(styleGuide);
        if (styleGuide) {
            const tones = styleGuide.tone ? styleGuide.tone.split(',').map(t => t.trim()).filter(Boolean) : [];
            setSelectedTones(new Set(tones));
            setSentenceSliderValue(getClosestSliderValue(styleGuide.sentenceStructure));
        }
        setIsDirty(false);
    }, [styleGuide]);
    
    const markDirty = () => {
        if (!isDirty) {
            setIsDirty(true);
        }
    };

    const handleToneToggle = (tone: string) => {
        const newTones = new Set(selectedTones);
        if (newTones.has(tone)) {
            newTones.delete(tone);
        } else {
            newTones.add(tone);
        }
        setSelectedTones(newTones);
        if (editableGuide) {
            setEditableGuide({ ...editableGuide, tone: Array.from(newTones).join(', ') });
        }
        markDirty();
    };

    const handleSliderChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const value = parseInt(e.target.value, 10);
        setSentenceSliderValue(value);
        if (editableGuide) {
            const description = sentenceStructureMap[value as keyof typeof sentenceStructureMap] || sentenceStructureMap[50];
            setEditableGuide({ ...editableGuide, sentenceStructure: description });
        }
        markDirty();
    };

    const handleFieldChange = (field: keyof Omit<StyleGuide, 'tone' | 'sentenceStructure'>, value: string) => {
        if (editableGuide) {
            setEditableGuide({ ...editableGuide, [field]: value });
            markDirty();
        }
    };
    
    const handleSave = () => {
        if (editableGuide && isDirty) {
            setIsSaving(true);
            setTimeout(() => { // Simulate save latency for UX
                onSaveStyleGuide(editableGuide);
                setIsSaving(false);
                setIsDirty(false);
            }, 700);
        }
    };
    
    return (
        <div className="aca-fade-in">
            {/* Modern Style Guide Header */}
            <div style={{
                background: 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)',
                borderRadius: '12px',
                padding: '30px',
                marginBottom: '30px',
                color: 'white',
                position: 'relative',
                overflow: 'hidden'
            }}>
                <div style={{ position: 'relative', zIndex: 2 }}>
                    <div style={{ display: 'flex', alignItems: 'center', gap: '12px', marginBottom: '12px' }}>
                        <div style={{
                            width: '48px',
                            height: '48px',
                            background: 'rgba(255,255,255,0.2)',
                            borderRadius: '12px',
                            display: 'flex',
                            alignItems: 'center',
                            justifyContent: 'center',
                            backdropFilter: 'blur(10px)'
                        }}>
                            <BookOpen style={{ width: '24px', height: '24px' }} />
                        </div>
                        <div>
                            <h1 style={{ 
                                fontSize: '28px', 
                                fontWeight: '700', 
                                margin: 0,
                                textShadow: '0 2px 4px rgba(0,0,0,0.1)',
                                color: 'white'
                            }}>
                                Style Guide
                            </h1>
                            <div style={{ fontSize: '16px', opacity: 0.9, marginTop: '4px' }}>
                                Define your brand's unique voice
                            </div>
                        </div>
                    </div>
                    <p style={{ 
                        fontSize: '14px', 
                        opacity: 0.85,
                        margin: 0,
                        maxWidth: '600px',
                        lineHeight: '1.5'
                    }}>
                        Define your brand's unique voice and writing style. The AI analyzes your content to keep this guide current and relevant.
                    </p>
                    
                    {/* Stats */}
                    <div style={{ display: 'flex', gap: '20px', marginTop: '20px', flexWrap: 'wrap' }}>
                        <div style={{ display: 'flex', alignItems: 'center', gap: '6px' }}>
                            <div style={{ width: '8px', height: '8px', backgroundColor: '#fbbf24', borderRadius: '50%' }}></div>
                            <span style={{ fontSize: '14px', opacity: 0.9 }}>AI-Powered Analysis</span>
                        </div>
                        <div style={{ display: 'flex', alignItems: 'center', gap: '6px' }}>
                            <div style={{ width: '8px', height: '8px', backgroundColor: '#4ade80', borderRadius: '50%' }}></div>
                            <span style={{ fontSize: '14px', opacity: 0.9 }}>Brand Voice Definition</span>
                        </div>
                        <div style={{ display: 'flex', alignItems: 'center', gap: '6px' }}>
                            <div style={{ width: '8px', height: '8px', backgroundColor: '#60a5fa', borderRadius: '50%' }}></div>
                            <span style={{ fontSize: '14px', opacity: 0.9 }}>Auto-Updated</span>
                        </div>
                    </div>
                </div>
                {/* Decorative elements */}
                <div style={{
                    position: 'absolute',
                    top: '-30px',
                    right: '-30px',
                    width: '120px',
                    height: '120px',
                    background: 'rgba(255,255,255,0.1)',
                    borderRadius: '50%',
                    zIndex: 1
                }}></div>
                <div style={{
                    position: 'absolute',
                    bottom: '-20px',
                    left: '-20px',
                    width: '80px',
                    height: '80px',
                    background: 'rgba(255,255,255,0.05)',
                    borderRadius: '50%',
                    zIndex: 1
                }}></div>
            </div>
            
            {/* Analysis Status Card */}
            <div className="aca-card">
                <div className="aca-card-header">
                    <h2 className="aca-card-title">
                        <RefreshCw className="aca-nav-item-icon" />
                        Content Analysis
                    </h2>
                </div>
                <p className="aca-page-description">
                    The AI periodically scans your published content to understand your writing style and automatically updates your guide.
                </p>
                
                <div className="aca-stat-item">
                    <div className="aca-stat-info">
                        <div className="aca-stat-icon">
                            <CheckCircle />
                        </div>
                        <div>
                            <h4 className="aca-stat-title">Analysis Status</h4>
                            <p className="aca-stat-count">
                                {styleGuide?.lastAnalyzed 
                                    ? `Last updated: ${new Date(styleGuide.lastAnalyzed).toLocaleDateString()} at ${new Date(styleGuide.lastAnalyzed).toLocaleTimeString()}`
                                    : 'Never analyzed'
                                }
                            </p>
                        </div>
                    </div>
                    <button
                        onClick={onAnalyze}
                        disabled={isLoading}
                        className="aca-button large"
                    >
                        {isLoading && <span className="aca-spinner"></span>}
                        {isLoading ? 'Analyzing...' : 'Analyze Content'}
                    </button>
                </div>
            </div>

            {/* Style Guide Editor */}
            <div className="aca-card">
                <div className="aca-card-header">
                    <h2 className="aca-card-title">
                        <BookOpen className="aca-nav-item-icon" />
                        Your Style Guide
                    </h2>
                </div>
                <p className="aca-page-description">
                    Review and customize the AI-generated style guide below. These settings will influence all future content generation.
                </p>
                
                {editableGuide ? (
                    <div className="aca-grid" style={{ gap: '30px' }}>
                        {/* Tone Selection */}
                        <div className="aca-form-group">
                            <label className="aca-label">
                                Writing Tone & Voice
                            </label>
                            <div className="aca-grid aca-grid-3" style={{ padding: '20px', background: '#f6f7f7', borderRadius: '4px', border: '1px solid #ccd0d4' }}>
                                {PREDEFINED_TONES.map(tone => (
                                    <button 
                                        key={tone} 
                                        onClick={() => handleToneToggle(tone)}
                                        className={`aca-button ${selectedTones.has(tone) ? '' : 'secondary'}`}
                                        style={{ textAlign: 'center' as const }}
                                    >
                                        {tone}
                                    </button>
                                ))}
                            </div>
                        </div>

                        {/* Sentence Structure Slider */}
                        <div className="aca-form-group">
                            <label className="aca-label">
                                Sentence Structure Preference
                            </label>
                            <div style={{ 
                                padding: '20px',
                                background: '#f6f7f7',
                                borderRadius: '4px',
                                border: '1px solid #ccd0d4'
                            }}>
                                <input
                                    type="range"
                                    min="0"
                                    max="100"
                                    step="25"
                                    value={sentenceSliderValue}
                                    onChange={handleSliderChange}
                                    className="aca-input"
                                    style={{ 
                                        width: '100%', 
                                        height: '8px', 
                                        background: '#ddd', 
                                        borderRadius: '4px', 
                                        appearance: 'none' as const, 
                                        cursor: 'pointer',
                                        outline: 'none'
                                    }}
                                />
                                <div className="aca-grid aca-grid-3" style={{ fontSize: '11px', color: '#646970', marginTop: '12px', fontWeight: '500', textAlign: 'center' as const }}>
                                    <span>Short & Simple</span>
                                    <span>Balanced Mix</span>
                                    <span>Long & Complex</span>
                                </div>
                                <div className="aca-alert info" style={{ marginTop: '15px', textAlign: 'center' as const, fontWeight: '500' }}>
                                    {sentenceStructureMap[sentenceSliderValue as keyof typeof sentenceStructureMap]}
                                </div>
                            </div>
                        </div>

                        {/* Form Fields Grid */}
                        <div className="aca-grid aca-grid-2">
                            <div className="aca-form-group">
                                <label className="aca-label">Paragraph Length Style</label>
                                <input 
                                    type="text" 
                                    value={editableGuide.paragraphLength} 
                                    onChange={(e) => handleFieldChange('paragraphLength', e.target.value)} 
                                    className="aca-input"
                                    placeholder="e.g., Short paragraphs, 2-3 sentences"
                                />
                            </div>
                            <div className="aca-form-group">
                                <label className="aca-label">Formatting Preferences</label>
                                <input 
                                    type="text" 
                                    value={editableGuide.formattingStyle} 
                                    onChange={(e) => handleFieldChange('formattingStyle', e.target.value)} 
                                    className="aca-input"
                                    placeholder="e.g., Use bullet points, bold headings"
                                />
                            </div>
                        </div>
                    </div>
                ) : (
                    <div className="aca-card" style={{ 
                        textAlign: 'center' as const,
                        border: '2px dashed #ccd0d4',
                        background: '#f9f9f9',
                        padding: '40px 20px'
                    }}>
                        <BookOpen style={{ width: '48px', height: '48px', marginBottom: '15px', fill: '#a7aaad' }} />
                        <h4 className="aca-card-title">No Style Guide Yet</h4>
                        <p className="aca-page-description">
                            Run your first content analysis to generate your personalized style guide.
                        </p>
                    </div>
                )}
                
                {/* Save Actions */}
                {editableGuide && (
                    <div style={{ 
                        marginTop: '30px', 
                        paddingTop: '20px', 
                        borderTop: '1px solid #f0f0f1', 
                        display: 'flex', 
                        justifyContent: 'space-between',
                        alignItems: 'center',
                        gap: '15px'
                    }}>
                        {isDirty && (
                            <div className="aca-alert warning" style={{ 
                                display: 'flex',
                                alignItems: 'center',
                                fontSize: '13px', 
                                fontWeight: '500',
                                gap: '8px',
                                padding: '8px 12px',
                                margin: 0
                            }}>
                                <div style={{ 
                                    width: '8px', 
                                    height: '8px', 
                                    borderRadius: '50%', 
                                    background: '#dba617' 
                                }}></div>
                                Unsaved changes
                            </div>
                        )}
                        <button
                            onClick={handleSave}
                            disabled={!isDirty || isSaving}
                            className="aca-button large"
                            style={{ marginLeft: 'auto' }}
                        >
                            {isSaving && <span className="aca-spinner"></span>}
                            {isSaving ? 'Saving...' : 'Save Changes'}
                        </button>
                    </div>
                )}
            </div>
        </div>
    );
};
