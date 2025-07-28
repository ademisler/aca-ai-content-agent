
import React, { useState, useEffect } from 'react';
import type { StyleGuide } from '../types';
import { Spinner, BookOpen } from './Icons';

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
                <p className="aca-page-description">Define your brand's unique voice. The AI automatically scans your site and keeps this guide updated.</p>
            </div>
            
            <div className="aca-card">
                <div className="aca-card-header">
                    <h2 className="aca-card-title">Analysis Status</h2>
                </div>
                <p style={{ color: '#646970', marginBottom: '20px', fontSize: '13px' }}>The agent periodically scans your site content to learn your style. You can also trigger a manual scan.</p>
                <div style={{ background: '#f6f7f7', padding: '20px', borderRadius: '4px', display: 'flex', alignItems: 'center', justifyContent: 'space-between', gap: '20px', flexWrap: 'wrap' }}>
                    <div>
                        <p style={{ fontSize: '13px', color: '#646970', margin: '0 0 5px 0' }}>Last Scanned</p>
                        <p style={{ fontWeight: '600', color: '#23282d', margin: 0 }}>
                            {styleGuide?.lastAnalyzed ? new Date(styleGuide.lastAnalyzed).toLocaleString() : 'Never'}
                        </p>
                    </div>
                    <button
                        onClick={onAnalyze}
                        disabled={isLoading}
                        className="aca-button large"
                        style={{ display: 'flex', alignItems: 'center', minWidth: '160px' }}
                    >
                        {isLoading && <Spinner style={{ marginRight: '8px', width: '16px', height: '16px' }} />}
                        {isLoading ? 'Scanning...' : 'Scan Site Content Now'}
                    </button>
                </div>
            </div>

            <div className="aca-card">
                <div className="aca-card-header">
                    <h2 className="aca-card-title">Review & Refine Your Guide</h2>
                </div>
                <p style={{ color: '#646970', marginBottom: '25px', fontSize: '13px' }}>The AI-generated guide is shown below. You can edit the fields at any time to fine-tune the AI's output.</p>
                {editableGuide ? (
                    <div style={{ display: 'flex', flexDirection: 'column', gap: '25px' }}>
                        <div className="aca-form-group">
                            <label className="aca-label">Tone</label>
                            <div style={{ display: 'flex', flexWrap: 'wrap', gap: '8px' }}>
                                {PREDEFINED_TONES.map(tone => (
                                    <button 
                                        key={tone} 
                                        onClick={() => handleToneToggle(tone)}
                                        style={{
                                            padding: '6px 12px',
                                            fontSize: '12px',
                                            fontWeight: '600',
                                            borderRadius: '20px',
                                            cursor: 'pointer',
                                            transition: 'all 0.2s ease',
                                            border: '1px solid',
                                            background: selectedTones.has(tone) ? '#0073aa' : '#ffffff',
                                            color: selectedTones.has(tone) ? '#ffffff' : '#646970',
                                            borderColor: selectedTones.has(tone) ? '#0073aa' : '#ccd0d4'
                                        }}
                                        onMouseEnter={(e) => {
                                            if (!selectedTones.has(tone)) {
                                                e.currentTarget.style.background = '#f6f7f7';
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
                        <div className="aca-form-group">
                            <label className="aca-label">Sentence Structure</label>
                            <div style={{ padding: '0 8px' }}>
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
                                        background: '#f6f7f7', 
                                        borderRadius: '4px', 
                                        appearance: 'none', 
                                        cursor: 'pointer',
                                        outline: 'none'
                                    }}
                                />
                                <div style={{ display: 'flex', justifyContent: 'space-between', fontSize: '11px', color: '#646970', marginTop: '8px' }}>
                                    <span>Short & Simple</span>
                                    <span>Balanced</span>
                                    <span>Long & Complex</span>
                                </div>
                                <p style={{ 
                                    fontSize: '13px', 
                                    textAlign: 'center', 
                                    color: '#0073aa', 
                                    marginTop: '10px', 
                                    padding: '10px', 
                                    background: '#f0f6fc', 
                                    borderRadius: '4px',
                                    margin: '10px 0 0 0'
                                }}>
                                    {sentenceStructureMap[sentenceSliderValue as keyof typeof sentenceStructureMap]}
                                </p>
                            </div>
                        </div>
                        <div className="aca-form-group">
                            <label className="aca-label">Paragraph Length</label>
                            <input 
                                type="text" 
                                value={editableGuide.paragraphLength} 
                                onChange={(e) => handleFieldChange('paragraphLength', e.target.value)} 
                                className="aca-input"
                            />
                        </div>
                        <div className="aca-form-group">
                            <label className="aca-label">Formatting Style</label>
                            <input 
                                type="text" 
                                value={editableGuide.formattingStyle} 
                                onChange={(e) => handleFieldChange('formattingStyle', e.target.value)} 
                                className="aca-input"
                            />
                        </div>
                    </div>
                ) : (
                    <div style={{ 
                        display: 'flex', 
                        flexDirection: 'column', 
                        alignItems: 'center', 
                        justifyContent: 'center', 
                        height: '200px', 
                        color: '#646970', 
                        border: '2px dashed #ccd0d4', 
                        borderRadius: '4px', 
                        padding: '40px',
                        textAlign: 'center'
                    }}>
                        <BookOpen style={{ width: '40px', height: '40px', marginBottom: '10px', fill: '#a7aaad' }} />
                        <p style={{ margin: 0, fontSize: '14px' }}>Your guide will appear here after the first scan.</p>
                    </div>
                )}
                <div style={{ 
                    marginTop: '25px', 
                    paddingTop: '20px', 
                    borderTop: '1px solid #f0f0f1', 
                    display: 'flex', 
                    justifyContent: 'flex-end' 
                }}>
                    <button
                        onClick={handleSave}
                        disabled={!isDirty || isSaving}
                        className="aca-button large"
                        style={{ display: 'flex', alignItems: 'center', minWidth: '140px' }}
                    >
                        {isSaving && <Spinner style={{ marginRight: '8px', width: '16px', height: '16px' }} />}
                        {isSaving ? 'Saving...' : 'Save Changes'}
                    </button>
                </div>
            </div>
        </div>
    );
};
