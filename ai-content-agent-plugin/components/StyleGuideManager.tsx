
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
            <div className="aca-page-header">
                <h1 className="aca-page-title">Style Guide</h1>
                <p className="aca-page-description">Define your brand's unique voice and writing style. The AI analyzes your content to keep this guide current and relevant.</p>
            </div>
            
            {/* Analysis Status Card */}
            <div className="aca-card">
                <div className="aca-card-header">
                    <h2 className="aca-card-title">
                        <RefreshCw style={{ width: '20px', height: '20px', marginRight: '8px', fill: '#0073aa' }} />
                        Content Analysis
                    </h2>
                </div>
                <p style={{ color: '#646970', marginBottom: '25px', fontSize: '13px' }}>
                    The AI periodically scans your published content to understand your writing style and automatically updates your guide.
                </p>
                
                <div className="aca-stat-item">
                    <div className="aca-stat-info">
                        <div className="aca-stat-icon">
                            <CheckCircle style={{ fill: styleGuide?.lastAnalyzed ? '#00a32a' : '#a7aaad' }} />
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
                        style={{ 
                            display: 'flex', 
                            alignItems: 'center', 
                            minWidth: '160px',
                            justifyContent: 'center'
                        }}
                    >
                        {isLoading && <Spinner style={{ marginRight: '8px', width: '16px', height: '16px' }} />}
                        {isLoading ? 'Analyzing...' : 'Analyze Content'}
                    </button>
                </div>
            </div>

            {/* Style Guide Editor */}
            <div className="aca-card">
                <div className="aca-card-header">
                    <h2 className="aca-card-title">
                        <BookOpen style={{ width: '20px', height: '20px', marginRight: '8px', fill: '#0073aa' }} />
                        Your Style Guide
                    </h2>
                </div>
                <p style={{ color: '#646970', marginBottom: '30px', fontSize: '13px' }}>
                    Review and customize the AI-generated style guide below. These settings will influence all future content generation.
                </p>
                
                {editableGuide ? (
                    <div style={{ display: 'flex', flexDirection: 'column', gap: '30px' }}>
                        {/* Tone Selection */}
                        <div className="aca-form-group">
                            <label className="aca-label" style={{ marginBottom: '15px' }}>
                                Writing Tone & Voice
                            </label>
                            <div style={{ 
                                display: 'grid', 
                                gridTemplateColumns: 'repeat(auto-fit, minmax(120px, 1fr))', 
                                gap: '12px',
                                padding: '20px',
                                background: '#f6f7f7',
                                borderRadius: '4px',
                                border: '1px solid #ccd0d4'
                            }}>
                                {PREDEFINED_TONES.map(tone => (
                                    <button 
                                        key={tone} 
                                        onClick={() => handleToneToggle(tone)}
                                        style={{
                                            padding: '12px 16px',
                                            fontSize: '13px',
                                            fontWeight: '500',
                                            borderRadius: '4px',
                                            cursor: 'pointer',
                                            transition: 'all 0.2s ease',
                                            border: '1px solid',
                                            background: selectedTones.has(tone) ? '#0073aa' : '#ffffff',
                                            color: selectedTones.has(tone) ? '#ffffff' : '#646970',
                                            borderColor: selectedTones.has(tone) ? '#0073aa' : '#ccd0d4',
                                            textAlign: 'center'
                                        }}
                                        onMouseEnter={(e) => {
                                            if (!selectedTones.has(tone)) {
                                                e.currentTarget.style.background = '#f0f0f1';
                                                e.currentTarget.style.borderColor = '#8c8f94';
                                            }
                                        }}
                                        onMouseLeave={(e) => {
                                            if (!selectedTones.has(tone)) {
                                                e.currentTarget.style.background = '#ffffff';
                                                e.currentTarget.style.borderColor = '#ccd0d4';
                                            }
                                        }}
                                    >
                                        {tone}
                                    </button>
                                ))}
                            </div>
                        </div>

                        {/* Sentence Structure Slider */}
                        <div className="aca-form-group">
                            <label className="aca-label" style={{ marginBottom: '15px' }}>
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
                                    style={{ 
                                        width: '100%', 
                                        height: '8px', 
                                        background: '#ddd', 
                                        borderRadius: '4px', 
                                        appearance: 'none', 
                                        cursor: 'pointer',
                                        outline: 'none'
                                    }}
                                />
                                <div style={{ 
                                    display: 'flex', 
                                    justifyContent: 'space-between', 
                                    fontSize: '11px', 
                                    color: '#646970', 
                                    marginTop: '12px',
                                    fontWeight: '500'
                                }}>
                                    <span>Short & Simple</span>
                                    <span>Balanced Mix</span>
                                    <span>Long & Complex</span>
                                </div>
                                <div style={{ 
                                    fontSize: '13px', 
                                    textAlign: 'center', 
                                    color: '#0073aa', 
                                    marginTop: '15px', 
                                    padding: '12px', 
                                    background: '#f0f6fc', 
                                    borderRadius: '4px',
                                    border: '1px solid #c3c4c7',
                                    fontWeight: '500'
                                }}>
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
                    <div style={{ 
                        display: 'flex',
                        flexDirection: 'column',
                        justifyContent: 'center',
                        alignItems: 'center',
                        minHeight: '200px',
                        textAlign: 'center',
                        border: '2px dashed #ccd0d4',
                        borderRadius: '4px',
                        background: '#f9f9f9',
                        padding: '40px 20px'
                    }}>
                        <BookOpen style={{ width: '48px', height: '48px', marginBottom: '15px', fill: '#a7aaad' }} />
                        <h4 style={{ margin: '0 0 8px 0', fontSize: '16px', fontWeight: '500', color: '#23282d' }}>
                            No Style Guide Yet
                        </h4>
                        <p style={{ margin: 0, fontSize: '13px', color: '#646970' }}>
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
                            <div style={{ 
                                display: 'flex',
                                alignItems: 'center',
                                fontSize: '13px', 
                                color: '#dba617',
                                fontWeight: '500',
                                gap: '8px'
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
                            style={{ 
                                display: 'flex', 
                                alignItems: 'center', 
                                minWidth: '140px',
                                justifyContent: 'center',
                                marginLeft: 'auto'
                            }}
                        >
                            {isSaving && <Spinner style={{ marginRight: '8px', width: '16px', height: '16px' }} />}
                            {isSaving ? 'Saving...' : 'Save Changes'}
                        </button>
                    </div>
                )}
            </div>
        </div>
    );
};
