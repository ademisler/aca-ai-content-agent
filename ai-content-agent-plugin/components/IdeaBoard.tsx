
import React, { useState, useEffect, useRef } from 'react';
import type { ContentIdea, IdeaSource } from '../types';
import { Spinner, Lightbulb, Trash, Sparkles, PlusCircle } from './Icons';

interface IdeaBoardProps {
    ideas: ContentIdea[];
    onGenerate: () => void;
    onCreateDraft: (idea: ContentIdea) => void;
    onArchive: (id: number) => void;
    onUpdateTitle: (id: number, newTitle: string) => void;
    onGenerateSimilar: (idea: ContentIdea) => void;
    onAddIdea: (title: string) => void;
    isLoading: boolean;
    isLoadingDraft: { [key: string]: boolean };
}

const sourceStyles: { [key in IdeaSource]: { background: string; color: string; borderColor: string } } = {
    'ai': { background: '#e6f7e6', color: '#0a5d0a', borderColor: '#28a745' },
    'search-console': { background: '#e3f2fd', color: '#0d47a1', borderColor: '#2196f3' },
    'similar': { background: '#f3e5f5', color: '#4a148c', borderColor: '#9c27b0' },
    'manual': { background: '#f6f7f7', color: '#646970', borderColor: '#8c8f94' }
};

const sourceNames: { [key in IdeaSource]: string } = {
    'ai': 'AI Generated',
    'search-console': 'Search Console',
    'similar': 'Similar Idea',
    'manual': 'Manual Entry'
};

const IdeaCard: React.FC<{
    idea: ContentIdea;
    onCreateDraft: (idea: ContentIdea) => void;
    onArchive: (id: number) => void;
    onUpdateTitle: (id: number, newTitle: string) => void;
    onGenerateSimilar: (idea: ContentIdea) => void;
    isLoading: boolean;
    isGeneratingSimilar: boolean;
}> = ({ idea, onCreateDraft, onArchive, onUpdateTitle, onGenerateSimilar, isLoading, isGeneratingSimilar }) => {
    const [isEditing, setIsEditing] = useState(false);
    const [title, setTitle] = useState(idea.title);
    const inputRef = useRef<HTMLInputElement>(null);

    useEffect(() => {
        if (isEditing) {
            inputRef.current?.focus();
            inputRef.current?.select();
        }
    }, [isEditing]);
    
    const handleSave = () => {
        if (title.trim() && title.trim() !== idea.title) {
            onUpdateTitle(idea.id, title.trim());
        } else {
            setTitle(idea.title); // Reset if empty or unchanged
        }
        setIsEditing(false);
    };

    const handleKeyDown = (e: React.KeyboardEvent<HTMLInputElement>) => {
        if (e.key === 'Enter') {
            handleSave();
        } else if (e.key === 'Escape') {
            setTitle(idea.title);
            setIsEditing(false);
        }
    };

    const sourceStyle = sourceStyles[idea.source];

    return (
        <div style={{ 
            background: '#ffffff', 
            padding: '20px', 
            borderRadius: '4px', 
            border: '1px solid #ccd0d4', 
            display: 'flex', 
            flexDirection: 'column', 
            justifyContent: 'space-between', 
            alignItems: 'flex-start', 
            gap: '15px', 
            transition: 'box-shadow 0.2s ease',
            boxShadow: '0 1px 1px rgba(0, 0, 0, 0.04)'
        }}
        onMouseEnter={(e) => {
            e.currentTarget.style.boxShadow = '0 2px 4px rgba(0, 0, 0, 0.1)';
        }}
        onMouseLeave={(e) => {
            e.currentTarget.style.boxShadow = '0 1px 1px rgba(0, 0, 0, 0.04)';
        }}
        >
            <div style={{ flexGrow: 1, width: '100%' }}>
                {isEditing ? (
                    <input
                        ref={inputRef}
                        type="text"
                        value={title}
                        onChange={(e) => setTitle(e.target.value)}
                        onBlur={handleSave}
                        onKeyDown={handleKeyDown}
                        className="aca-input"
                        style={{ width: '100%' }}
                    />
                ) : (
                    <p 
                        onClick={() => setIsEditing(true)} 
                        style={{ 
                            color: '#23282d', 
                            fontWeight: '500', 
                            flexGrow: 1, 
                            cursor: 'pointer', 
                            padding: '8px', 
                            borderRadius: '4px', 
                            margin: 0,
                            transition: 'background 0.2s ease'
                        }}
                        title="Click to edit"
                        onMouseEnter={(e) => {
                            e.currentTarget.style.background = '#f6f7f7';
                        }}
                        onMouseLeave={(e) => {
                            e.currentTarget.style.background = 'transparent';
                        }}
                    >
                        {idea.title}
                    </p>
                )}
            </div>
            <div style={{ width: '100%', display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                <div style={{ 
                    fontSize: '11px', 
                    fontWeight: '600', 
                    padding: '4px 8px', 
                    borderRadius: '20px', 
                    border: '1px solid',
                    background: sourceStyle.background,
                    color: sourceStyle.color,
                    borderColor: sourceStyle.borderColor
                }}>
                    {sourceNames[idea.source]}
                </div>
                <div style={{ display: 'flex', alignItems: 'center', gap: '8px', flexShrink: 0 }}>
                    <button
                        onClick={() => onGenerateSimilar(idea)}
                        disabled={isGeneratingSimilar || isLoading}
                        style={{
                            background: isGeneratingSimilar ? '#f6f7f7' : '#f3e5f5',
                            color: isGeneratingSimilar ? '#a7aaad' : '#4a148c',
                            padding: '6px 12px',
                            borderRadius: '4px',
                            fontSize: '12px',
                            fontWeight: '600',
                            border: '1px solid',
                            borderColor: isGeneratingSimilar ? '#ccd0d4' : '#9c27b0',
                            cursor: isGeneratingSimilar ? 'not-allowed' : 'pointer',
                            transition: 'all 0.2s ease',
                            display: 'flex',
                            alignItems: 'center'
                        }}
                        title="Generate similar ideas"
                        onMouseEnter={(e) => {
                            if (!isGeneratingSimilar && !isLoading) {
                                e.currentTarget.style.background = '#e1bee7';
                            }
                        }}
                        onMouseLeave={(e) => {
                            if (!isGeneratingSimilar && !isLoading) {
                                e.currentTarget.style.background = '#f3e5f5';
                            }
                        }}
                    >
                        {isGeneratingSimilar ? <Spinner style={{ width: '14px', height: '14px' }} /> : <Sparkles style={{ width: '14px', height: '14px' }} />}
                    </button>
                    <button
                        onClick={() => onCreateDraft(idea)}
                        disabled={isLoading || isGeneratingSimilar}
                        className="aca-button"
                        style={{ 
                            fontSize: '12px',
                            padding: '6px 12px',
                            display: 'flex',
                            alignItems: 'center',
                            background: '#00a32a',
                            borderColor: '#00a32a'
                        }}
                        onMouseEnter={(e) => {
                            if (!isLoading && !isGeneratingSimilar) {
                                e.currentTarget.style.background = '#008a20';
                                e.currentTarget.style.borderColor = '#008a20';
                            }
                        }}
                        onMouseLeave={(e) => {
                            if (!isLoading && !isGeneratingSimilar) {
                                e.currentTarget.style.background = '#00a32a';
                                e.currentTarget.style.borderColor = '#00a32a';
                            }
                        }}
                    >
                        {isLoading ? <Spinner style={{ width: '14px', height: '14px' }} /> : "Create Draft"}
                    </button>
                    <div style={{ position: 'relative', display: 'inline-block' }}>
                        <button 
                            onClick={() => onArchive(idea.id)} 
                            style={{
                                color: '#646970',
                                padding: '6px',
                                borderRadius: '4px',
                                border: 'none',
                                background: 'transparent',
                                cursor: 'pointer',
                                transition: 'all 0.2s ease'
                            }}
                            onMouseEnter={(e) => {
                                e.currentTarget.style.background = '#fcf0f1';
                                e.currentTarget.style.color = '#d63638';
                            }}
                            onMouseLeave={(e) => {
                                e.currentTarget.style.background = 'transparent';
                                e.currentTarget.style.color = '#646970';
                            }}
                        >
                            <Trash style={{ width: '14px', height: '14px' }} />
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
};

export const IdeaBoard: React.FC<IdeaBoardProps> = ({ ideas, onGenerate, onCreateDraft, onArchive, onUpdateTitle, onGenerateSimilar, onAddIdea, isLoading, isLoadingDraft }) => {
    const [newIdeaTitle, setNewIdeaTitle] = useState('');

    const handleAddIdeaSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        if (newIdeaTitle.trim()) {
            onAddIdea(newIdeaTitle);
            setNewIdeaTitle('');
        }
    };

    return (
        <div className="aca-fade-in">
            <div className="aca-page-header" style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', flexWrap: 'wrap', gap: '20px' }}>
                <div>
                    <h1 className="aca-page-title">Idea Board</h1>
                    <p className="aca-page-description">Generate new topics, edit their titles, and turn your favorites into drafts.</p>
                </div>
                <button
                    onClick={onGenerate}
                    disabled={isLoading}
                    className="aca-button large"
                    style={{ 
                        display: 'flex', 
                        alignItems: 'center', 
                        flexShrink: 0,
                        minWidth: '180px'
                    }}
                >
                    {isLoading && <Spinner style={{ marginRight: '8px', width: '16px', height: '16px' }} />}
                    {isLoading ? 'Generating...' : 'Generate New Ideas'}
                </button>
            </div>

            <div className="aca-card">
                <form onSubmit={handleAddIdeaSubmit} style={{ display: 'flex', gap: '10px', marginBottom: '25px', paddingBottom: '25px', borderBottom: '1px solid #f0f0f1' }}>
                    <input
                        type="text"
                        value={newIdeaTitle}
                        onChange={(e) => setNewIdeaTitle(e.target.value)}
                        placeholder="Or add your own idea manually..."
                        className="aca-input"
                        style={{ flexGrow: 1 }}
                    />
                    <button
                        type="submit"
                        disabled={!newIdeaTitle.trim()}
                        className="aca-button"
                        style={{ 
                            display: 'flex', 
                            alignItems: 'center',
                            background: '#00a32a',
                            borderColor: '#00a32a',
                            flexShrink: 0
                        }}
                        onMouseEnter={(e) => {
                            if (newIdeaTitle.trim()) {
                                e.currentTarget.style.background = '#008a20';
                                e.currentTarget.style.borderColor = '#008a20';
                            }
                        }}
                        onMouseLeave={(e) => {
                            if (newIdeaTitle.trim()) {
                                e.currentTarget.style.background = '#00a32a';
                                e.currentTarget.style.borderColor = '#00a32a';
                            }
                        }}
                    >
                        <PlusCircle style={{ width: '16px', height: '16px', marginRight: '8px' }} /> Add Idea
                    </button>
                </form>

                {ideas.length > 0 ? (
                    <div style={{ display: 'flex', flexDirection: 'column', gap: '15px' }}>
                        {ideas.map(idea => (
                            <IdeaCard
                                key={idea.id}
                                idea={idea}
                                onCreateDraft={onCreateDraft}
                                onArchive={onArchive}
                                onUpdateTitle={onUpdateTitle}
                                onGenerateSimilar={onGenerateSimilar}
                                isLoading={isLoadingDraft[`draft-${idea.id}`] || false}
                                isGeneratingSimilar={isLoadingDraft[`similar-${idea.id}`] || false}
                            />
                        ))}
                    </div>
                ) : (
                    <div style={{ 
                        textAlign: 'center', 
                        padding: '60px 20px', 
                        color: '#646970', 
                        border: '2px dashed #ccd0d4', 
                        borderRadius: '4px' 
                    }}>
                        <Lightbulb style={{ margin: '0 auto 15px auto', width: '48px', height: '48px', fill: '#a7aaad' }} />
                        <p style={{ margin: '0 0 5px 0', fontSize: '16px', fontWeight: '500', color: '#23282d' }}>Your Idea Board is Empty</p>
                        <p style={{ margin: 0, fontSize: '13px' }}>Click "Generate New Ideas" or add one manually to get started!</p>
                    </div>
                )}
            </div>
        </div>
    );
};
