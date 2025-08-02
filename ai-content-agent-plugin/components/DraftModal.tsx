
import React, { useState, useEffect } from 'react';
import type { Draft, AppSettings } from '../types';
import { X, Spinner, CheckCircle, Info } from './Icons';

interface DraftModalProps {
    draft: Draft;
    onClose: () => void;
    onSave: (id: number, updates: Partial<Draft>) => void;
    settings: AppSettings;
}

type SeoStatus = 'idle' | 'sending' | 'analyzed';

interface SeoSuggestion {
    text: string;
    type: 'good' | 'improvement';
}
interface SeoAnalysis {
    score: number;
    suggestions: SeoSuggestion[];
}

export const DraftModal: React.FC<DraftModalProps> = ({ draft, onClose, onSave, settings }) => {
    const { seoPlugin } = settings;
    const [seoStatus, setSeoStatus] = useState<SeoStatus>('idle');
    const [seoAnalysis, setSeoAnalysis] = useState<SeoAnalysis | null>(null);
    const [isSaving, setIsSaving] = useState(false);

    const [editedTitle, setEditedTitle] = useState(draft.title);
    const [editedContent, setEditedContent] = useState(draft.content);
    const [editedMetaTitle, setEditedMetaTitle] = useState(draft.metaTitle);
    const [editedMetaDescription, setEditedMetaDescription] = useState(draft.metaDescription);
    const [editedFocusKeywords, setEditedFocusKeywords] = useState<string[]>([]);
    
    const originalFocusKeywords = Array.from({ length: 5 }, (_, i) => draft.focusKeywords?.[i] || '');
    const isDirty = draft.title !== editedTitle ||
                    draft.content !== editedContent ||
                    draft.metaTitle !== editedMetaTitle ||
                    draft.metaDescription !== editedMetaDescription ||
                    JSON.stringify(originalFocusKeywords) !== JSON.stringify(editedFocusKeywords);

    useEffect(() => {
        setEditedTitle(draft.title);
        setEditedContent(draft.content);
        setEditedMetaTitle(draft.metaTitle);
        setEditedMetaDescription(draft.metaDescription);
        setEditedFocusKeywords(Array.from({ length: 5 }, (_, i) => draft.focusKeywords?.[i] || ''));
        setSeoStatus('idle');
        setSeoAnalysis(null);
    }, [draft]);

    const handleSendToSeoPlugin = () => {
        if (seoStatus !== 'idle') return;
        setSeoStatus('sending');
        setTimeout(() => {
            setSeoAnalysis({
                score: Math.floor(Math.random() * (95 - 75 + 1) + 75), // Random score between 75-95
                suggestions: [
                    { text: 'Anahtar kelime ilk paragrafta geçiyor.', type: 'good' },
                    { text: 'Meta açıklama uzunluğu ideal.', type: 'good' },
                    { text: 'Yüksek otoriteye sahip bir siteye harici bir bağlantı eklemeyi düşünün.', type: 'improvement' }
                ]
            });
            setSeoStatus('analyzed');
        }, 1500);
    };

    const handleKeywordChange = (index: number, value: string) => {
        const newKeywords = [...editedFocusKeywords];
        newKeywords[index] = value;
        setEditedFocusKeywords(newKeywords);
    };

    const handleSave = () => {
        if (!isDirty) return;
        setIsSaving(true);
        const updates: Partial<Draft> = {
            title: editedTitle,
            content: editedContent,
            metaTitle: editedMetaTitle,
            metaDescription: editedMetaDescription,
            focusKeywords: editedFocusKeywords,
        };
        // Simulate save latency for better UX
        setTimeout(() => {
            onSave(draft.id, updates);
            setIsSaving(false);
        }, 700);
    };

    const seoPluginName = seoPlugin === 'rank_math' ? 'Rank Math' : 'Yoast SEO';

    return (
        <div className="fixed inset-0 bg-black bg-opacity-80 flex items-center justify-center z-40 p-0 sm:p-4" onClick={onClose}>
            <div
                className="bg-slate-800 sm:rounded-lg shadow-2xl w-full max-w-5xl h-full sm:h-[90vh] flex flex-col overflow-hidden border border-slate-700"
                onClick={(e) => e.stopPropagation()}
            >
                <header className="flex-shrink-0 p-4 border-b border-slate-700 flex justify-between items-center bg-slate-800/80 backdrop-blur-sm">
                     <input
                        type="text"
                        value={editedTitle}
                        onChange={(e) => setEditedTitle(e.target.value)}
                        className="text-xl font-bold text-white bg-transparent border-0 focus:ring-0 w-full p-0"
                        placeholder="Article Title"
                    />
                    <button onClick={onClose} className="text-slate-400 hover:text-white p-1 rounded-full hover:bg-slate-600 flex-shrink-0 ml-4">
                        <X className="h-6 w-6" />
                    </button>
                </header>

                <div className="flex-grow overflow-y-auto p-4 pt-6 sm:p-8 space-y-8">
                    <div>
                        <h3 className="text-lg font-semibold mb-3 text-sky-400">Featured Image</h3>
                        <img src={draft.featuredImage} alt={draft.title} className="w-full h-auto max-h-96 object-cover rounded-md shadow-lg border border-slate-700" />
                    </div>

                    <div className="max-w-prose mx-auto">
                        <label htmlFor="content-editor" className="text-lg font-semibold mb-3 text-sky-400 block">Content (Markdown)</label>
                        <textarea
                            id="content-editor"
                            value={editedContent}
                            onChange={(e) => setEditedContent(e.target.value)}
                            className="w-full h-96 bg-slate-900/50 border border-slate-600 rounded-md p-4 text-slate-300 text-base leading-relaxed focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            placeholder="Write your article content here..."
                        />
                    </div>
                    
                    <div className="max-w-prose mx-auto pt-6 border-t border-slate-700">
                        <div className="bg-slate-900 p-6 rounded-lg border border-slate-700/80 shadow-inner">
                            <h3 className="text-lg font-semibold text-green-400 mb-4">SEO Data</h3>
                            <div className="space-y-4">
                                <div>
                                    <label htmlFor="meta-title-editor" className="block text-sm font-medium text-slate-400 mb-1">Meta Title</label>
                                    <input
                                        id="meta-title-editor"
                                        type="text"
                                        value={editedMetaTitle}
                                        onChange={(e) => setEditedMetaTitle(e.target.value)}
                                        className="w-full bg-slate-700 border border-slate-600 rounded-md p-2 text-sm text-slate-200 focus:ring-2 focus:ring-blue-500"
                                        placeholder="SEO Meta Title"
                                    />
                                </div>
                                <div>
                                    <label htmlFor="meta-desc-editor" className="block text-sm font-medium text-slate-400 mb-1">Meta Description</label>
                                    <textarea
                                        id="meta-desc-editor"
                                        value={editedMetaDescription}
                                        onChange={(e) => setEditedMetaDescription(e.target.value)}
                                        rows={3}
                                        className="w-full bg-slate-700 border border-slate-600 rounded-md p-2 text-sm text-slate-300 leading-relaxed focus:ring-2 focus:ring-blue-500"
                                        placeholder="SEO Meta Description"
                                    />
                                </div>
                                <div>
                                    <label htmlFor="focus-keywords" className="block text-sm font-medium text-slate-400 mb-2">Focus Keywords</label>
                                    <div id="focus-keywords" className="space-y-2">
                                        {editedFocusKeywords.map((keyword, index) => (
                                            <input
                                                key={index}
                                                type="text"
                                                value={keyword}
                                                onChange={(e) => handleKeywordChange(index, e.target.value)}
                                                className="w-full bg-slate-700 border border-slate-600 rounded-md p-2 text-sm text-slate-200 focus:ring-2 focus:ring-blue-500"
                                                placeholder={`Focus Keyword ${index + 1}`}
                                            />
                                        ))}
                                    </div>
                                </div>
                            </div>

                            {seoAnalysis && seoStatus === 'analyzed' && (
                                <div className="mt-6 pt-6 border-t border-slate-700/60 animate-fade-in-fast" style={{'--animation-duration': '500ms'} as React.CSSProperties}>
                                    <h4 className="font-semibold text-slate-200 mb-3">SEO Analysis Results</h4>
                                    <div className="flex items-center gap-4 bg-slate-800/50 p-3 rounded-md">
                                        <div className={`flex items-baseline justify-center h-16 w-16 rounded-full border-4 ${seoAnalysis.score > 80 ? 'border-green-500' : 'border-yellow-500'}`}>
                                            <span className="text-2xl font-bold text-white">{seoAnalysis.score}</span>
                                            <span className="text-sm text-slate-400 -ml-1">/100</span>
                                        </div>
                                        <div className="flex-1">
                                            <p className={`font-bold text-lg ${seoAnalysis.score > 80 ? 'text-green-400' : 'text-yellow-400'}`}>İyi</p>
                                            <p className="text-sm text-slate-400">Yazınız iyi optimize edilmiş.</p>
                                        </div>
                                    </div>
                                    <ul className="mt-4 space-y-2 text-sm">
                                        {seoAnalysis.suggestions.map((tip, index) => (
                                            <li key={index} className="flex items-start gap-2.5">
                                                {tip.type === 'good' ? (
                                                    <CheckCircle className="h-4 w-4 mt-1 text-green-500 flex-shrink-0" />
                                                ) : (
                                                    <Info className="h-4 w-4 mt-1 text-blue-400 flex-shrink-0" />
                                                )}
                                                <span className="text-slate-300">{tip.text}</span>
                                            </li>
                                        ))}
                                    </ul>
                                </div>
                            )}

                            {seoPlugin !== 'none' && (
                                <div className="mt-6 text-right">
                                    <button 
                                        onClick={handleSendToSeoPlugin} 
                                        disabled={seoStatus !== 'idle'}
                                        className="bg-blue-600 text-white font-bold py-2 px-5 rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center disabled:bg-slate-600 disabled:cursor-not-allowed min-w-[160px]"
                                    >
                                        {seoStatus === 'sending' && <Spinner className="mr-2 h-5 w-5" />}
                                        {seoStatus === 'idle' && `${seoPluginName} ile Analiz Et`}
                                        {seoStatus === 'sending' && 'Analiz ediliyor...'}
                                        {seoStatus === 'analyzed' && <><CheckCircle className="mr-2 h-5 w-5" /> Analiz Edildi!</>}
                                    </button>
                                </div>
                            )}
                        </div>
                    </div>
                </div>

                <footer className="flex-shrink-0 p-3 border-t border-slate-700 flex justify-end items-center bg-slate-800/80 space-x-3">
                    <button
                        onClick={onClose}
                        className="bg-slate-600 text-white px-4 py-2 rounded-md text-sm font-semibold hover:bg-slate-700 transition-colors"
                    >
                        Kapat
                    </button>
                    <button
                        onClick={handleSave}
                        disabled={!isDirty || isSaving}
                        className="bg-blue-600 text-white font-bold px-5 py-2 rounded-md hover:bg-blue-700 transition-colors flex items-center justify-center disabled:bg-slate-500 disabled:cursor-not-allowed min-w-[100px]"
                    >
                        {isSaving ? <Spinner className="mr-2 h-5 w-5"/> : null}
                        {isSaving ? 'Kaydediliyor...' : 'Kaydet'}
                    </button>
                </footer>
            </div>
        </div>
    );
};
