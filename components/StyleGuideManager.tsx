
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
        <div className="space-y-8 max-w-4xl mx-auto">
            <header>
                <h2 className="text-3xl font-bold text-white border-b border-slate-700 pb-3 mb-6">Brand Style Guide</h2>
                <p className="text-slate-400 -mt-2">Define your brand's unique voice. The AI automatically scans your site and keeps this guide updated.</p>
            </header>
            
            <div className="bg-slate-800 rounded-lg border border-slate-700/80">
                <div className="p-6">
                    <h3 className="text-xl font-semibold mb-1 text-white">Analysis Status</h3>
                    <p className="text-slate-400 mb-4 text-sm">The agent periodically scans your site content to learn your style. You can also trigger a manual scan.</p>
                    <div className="bg-slate-900/50 p-4 rounded-lg flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div className="text-center sm:text-left">
                            <p className="text-sm text-slate-400">Last Scanned</p>
                            <p className="font-semibold text-white">
                                {styleGuide?.lastAnalyzed ? new Date(styleGuide.lastAnalyzed).toLocaleString() : 'Never'}
                            </p>
                        </div>
                        <button
                            onClick={onAnalyze}
                            disabled={isLoading}
                            className="w-full sm:w-auto bg-blue-600 text-white font-bold py-2.5 px-4 rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center disabled:bg-slate-600 disabled:cursor-not-allowed"
                        >
                            {isLoading ? <><Spinner className="mr-2 h-5 w-5" /> Scanning...</> : 'Scan Site Content Now'}
                        </button>
                    </div>
                </div>
            </div>

            <div className="bg-slate-800 rounded-lg border border-slate-700/80">
                <div className="p-6">
                    <h3 className="text-xl font-semibold mb-1 text-white">Review & Refine Your Guide</h3>
                    <p className="text-slate-400 mb-6 text-sm">The AI-generated guide is shown below. You can edit the fields at any time to fine-tune the AI's output.</p>
                    {editableGuide ? (
                        <div className="space-y-6">
                            <div>
                                <label className="block text-sm font-medium text-slate-300 mb-2">Tone</label>
                                <div className="flex flex-wrap gap-2">
                                    {PREDEFINED_TONES.map(tone => (
                                        <button key={tone} onClick={() => handleToneToggle(tone)} className={`px-3 py-1.5 text-sm font-semibold rounded-full cursor-pointer transition-colors ${selectedTones.has(tone) ? 'bg-blue-600 text-white' : 'bg-slate-700 hover:bg-slate-600 text-slate-300'}`}>
                                            {tone}
                                        </button>
                                    ))}
                                </div>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-slate-300 mb-2">Sentence Structure</label>
                                <div className="px-2">
                                     <input
                                        type="range"
                                        min="0"
                                        max="100"
                                        step="25"
                                        value={sentenceSliderValue}
                                        onChange={handleSliderChange}
                                        className="w-full h-2 bg-slate-700 rounded-lg appearance-none cursor-pointer"
                                    />
                                    <div className="flex justify-between text-xs text-slate-400 mt-2">
                                        <span>Short & Simple</span>
                                        <span>Balanced</span>
                                        <span>Long & Complex</span>
                                    </div>
                                    <p className="text-sm text-center text-blue-300 mt-2 p-2 bg-slate-900/50 rounded-md">{sentenceStructureMap[sentenceSliderValue as keyof typeof sentenceStructureMap]}</p>
                                </div>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-slate-300 mb-1">Paragraph Length</label>
                                <input type="text" value={editableGuide.paragraphLength} onChange={(e) => handleFieldChange('paragraphLength', e.target.value)} className="w-full bg-slate-700 border border-slate-600 rounded-md p-2 text-sm focus:ring-2 focus:ring-blue-500"/>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-slate-300 mb-1">Formatting Style</label>
                                <input type="text" value={editableGuide.formattingStyle} onChange={(e) => handleFieldChange('formattingStyle', e.target.value)} className="w-full bg-slate-700 border border-slate-600 rounded-md p-2 text-sm focus:ring-2 focus:ring-blue-500"/>
                            </div>
                        </div>
                    ) : (
                        <div className="flex flex-col items-center justify-center h-full text-slate-500 border-2 border-dashed border-slate-700 rounded-md py-10">
                            <BookOpen className="h-10 w-10 mb-2" />
                            <p>Your guide will appear here after the first scan.</p>
                        </div>
                    )}
                </div>
                <div className="px-6 py-4 bg-slate-800/50 border-t border-slate-700/80 flex justify-end items-center">
                    <button
                        onClick={handleSave}
                        disabled={!isDirty || isSaving}
                        className="bg-blue-600 text-white font-bold py-2 px-5 rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center disabled:bg-slate-600 disabled:cursor-not-allowed"
                    >
                        {isSaving ? <Spinner className="mr-2 h-5 w-5" /> : null}
                        {isSaving ? 'Saving...' : 'Save Changes'}
                    </button>
                </div>
            </div>
        </div>
    );
};
