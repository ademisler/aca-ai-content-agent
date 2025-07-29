
import React, { useState, useEffect, useRef } from 'react';
import type { ContentIdea, IdeaSource } from '../types';
import { Spinner, Lightbulb, Trash, Sparkles, PlusCircle, Edit } from './Icons';

interface IdeaBoardProps {
    ideas: ContentIdea[];
    onGenerate: () => void;
    onCreateDraft: (idea: ContentIdea) => void;
    onArchive: (id: number) => void;
    onDeleteIdea?: (id: number) => void;
    onRestoreIdea?: (id: number) => void;
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
        <div className={`aca-card ${isLoading ? 'loading' : ''}`} style={{ margin: 0, minHeight: '140px' }}>
            {/* Idea Title */}
            <div style={{ flexGrow: 1, marginBottom: '15px' }}>
                {isEditing ? (
                    <input
                        ref={inputRef}
                        type="text"
                        value={title}
                        onChange={(e) => setTitle(e.target.value)}
                        onBlur={handleSave}
                        onKeyDown={handleKeyDown}
                        className="aca-input"
                        style={{ fontSize: '16px', fontWeight: '500' }}
                    />
                ) : (
                    <div 
                        onClick={() => setIsEditing(true)} 
                        className="aca-action-button"
                        style={{ 
                            padding: '8px 12px', 
                            margin: '-8px -12px',
                            border: '1px solid transparent',
                            display: 'flex',
                            alignItems: 'center',
                            gap: '8px',
                            lineHeight: '1.4',
                            fontSize: '16px',
                            fontWeight: '500'
                        }}
                        title="Click to edit title"
                    >
                        <Edit style={{ width: '14px', height: '14px', color: '#0073aa' }} />
                        {idea.title}
                    </div>
                )}
            </div>

            {/* Meta Info and Actions */}
            <div className="aca-list-item" style={{ padding: '15px 0 0 0', margin: 0 }}>
                {/* Source Tag */}
                <div style={{ 
                    fontSize: '11px', 
                    fontWeight: '600', 
                    padding: '6px 12px', 
                    borderRadius: '4px', 
                    border: '1px solid',
                    background: sourceStyle.background,
                    color: sourceStyle.color,
                    borderColor: sourceStyle.borderColor,
                    flexShrink: 0
                }}>
                    {sourceNames[idea.source]}
                </div>

                {/* Action Buttons */}
                <div style={{ display: 'flex', alignItems: 'center', gap: '8px', flexShrink: 0 }}>
                    <button
                        onClick={() => onGenerateSimilar(idea)}
                        disabled={isGeneratingSimilar || isLoading}
                        className="aca-button secondary"
                        style={{ fontSize: '12px', padding: '6px 12px' }}
                        title="Generate similar ideas"
                    >
                        {isGeneratingSimilar ? (
                            <span className="aca-spinner" style={{ width: '14px', height: '14px' }}></span>
                        ) : (
                            <Sparkles style={{ width: '14px', height: '14px' }} />
                        )}
                        Similar
                    </button>
                    
                    <button
                        onClick={() => onCreateDraft(idea)}
                        disabled={isLoading || isGeneratingSimilar}
                        className="aca-button"
                        style={{ 
                            fontSize: '12px',
                            padding: '6px 16px',
                            background: isLoading ? '#f6f7f7' : '#00a32a',
                            borderColor: isLoading ? '#ccd0d4' : '#00a32a',
                            color: isLoading ? '#a7aaad' : '#ffffff',
                            cursor: isLoading ? 'wait' : 'pointer',
                            minWidth: '120px',
                            position: 'relative',
                            overflow: 'hidden'
                        }}
                        title={isLoading ? 'AI is generating your draft...' : 'Create draft from this idea'}
                    >
                        {isLoading ? (
                            <div style={{ display: 'flex', alignItems: 'center', gap: '6px' }}>
                                <span className="aca-spinner" style={{ width: '14px', height: '14px' }}></span>
                                <span style={{ fontSize: '11px' }}>Creating...</span>
                            </div>
                        ) : (
                            <div style={{ display: 'flex', alignItems: 'center', gap: '6px' }}>
                                <Sparkles style={{ width: '14px', height: '14px' }} />
                                Create Draft
                            </div>
                        )}
                        
                        {/* Progress indicator overlay */}
                        {isLoading && (
                            <div style={{
                                position: 'absolute',
                                bottom: 0,
                                left: 0,
                                width: '100%',
                                height: '2px',
                                background: 'rgba(0, 163, 42, 0.3)',
                                overflow: 'hidden'
                            }}>
                                <div style={{
                                    width: '30%',
                                    height: '100%',
                                    background: '#00a32a',
                                    animation: 'aca-progress-slide 2s infinite linear'
                                }} />
                            </div>
                        )}
                    </button>
                    
                    <button 
                        onClick={() => onArchive(idea.id)} 
                        className="aca-button secondary"
                        style={{ fontSize: '12px', padding: '6px', minWidth: 'auto', color: '#646970' }}
                        title="Archive idea"
                    >
                        <Trash style={{ width: '14px', height: '14px' }} />
                    </button>
                </div>
            </div>
        </div>
    );
};

export const IdeaBoard: React.FC<IdeaBoardProps> = ({ 
    ideas, 
    onGenerate, 
    onCreateDraft, 
    onArchive, 
    onDeleteIdea,
    onRestoreIdea,
    onUpdateTitle, 
    onGenerateSimilar, 
    onAddIdea, 
    isLoading, 
    isLoadingDraft 
}) => {
    const [newIdeaTitle, setNewIdeaTitle] = useState('');

    const handleAddIdeaSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        if (newIdeaTitle.trim()) {
            onAddIdea(newIdeaTitle);
            setNewIdeaTitle('');
        }
    };

    const activeIdeas = ideas.filter(idea => idea.status === 'active');
    const archivedIdeas = ideas.filter(idea => idea.status === 'archived');

    return (
        <div className="aca-fade-in">
            <div className="aca-page-header">
                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', flexWrap: 'wrap', gap: '20px' }}>
                    <div>
                        <h1 className="aca-page-title">Idea Board</h1>
                        <p className="aca-page-description">
                            Generate fresh content ideas and turn your favorites into drafts. Click any title to edit it.
                        </p>
                    </div>
                    <button
                        onClick={onGenerate}
                        disabled={isLoading}
                        className="aca-button large"
                    >
                        {isLoading && <span className="aca-spinner"></span>}
                        <Lightbulb className="aca-nav-item-icon" />
                        {isLoading ? 'Generating...' : 'Generate Ideas'}
                    </button>
                </div>
            </div>

            {/* Add New Idea */}
            <div className="aca-card">
                <div className="aca-card-header">
                    <h2 className="aca-card-title">
                        <PlusCircle className="aca-nav-item-icon" />
                        Add Your Own Idea
                    </h2>
                </div>
                <form onSubmit={handleAddIdeaSubmit} style={{ display: 'flex', gap: '12px', alignItems: 'flex-end' }}>
                    <div className="aca-form-group" style={{ flexGrow: 1, marginBottom: 0 }}>
                        <label className="aca-label" htmlFor="new-idea-input">Idea Title</label>
                        <input
                            id="new-idea-input"
                            type="text"
                            value={newIdeaTitle}
                            onChange={(e) => setNewIdeaTitle(e.target.value)}
                            placeholder="Enter your content idea..."
                            className="aca-input"
                        />
                    </div>
                    <button
                        type="submit"
                        disabled={!newIdeaTitle.trim()}
                        className="aca-button"
                        style={{ 
                            background: '#00a32a',
                            borderColor: '#00a32a',
                            flexShrink: 0,
                            padding: '8px 16px'
                        }}
                    >
                        <PlusCircle style={{ width: '16px', height: '16px' }} />
                        Add Idea
                    </button>
                </form>
            </div>

            {/* Active Ideas */}
            {activeIdeas.length > 0 ? (
                <div className="aca-card">
                    <div className="aca-card-header">
                        <h2 className="aca-card-title">
                            <Lightbulb className="aca-nav-item-icon" />
                            Active Ideas ({activeIdeas.length})
                        </h2>
                    </div>
                    <div className="aca-grid aca-grid-2">
                        {activeIdeas.map(idea => (
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
                </div>
            ) : (
                <div className="aca-card">
                    <div style={{ textAlign: 'center', padding: '60px 20px', color: '#646970' }}>
                        <Lightbulb style={{ margin: '0 auto 20px auto', width: '48px', height: '48px', fill: '#a7aaad' }} />
                        <h3 className="aca-card-title">No Active Ideas Yet</h3>
                        <p className="aca-page-description" style={{ maxWidth: '400px', marginLeft: 'auto', marginRight: 'auto' }}>
                            Get started by generating AI-powered content ideas or adding your own manually.
                        </p>
                        <div style={{ display: 'flex', gap: '10px', justifyContent: 'center', flexWrap: 'wrap' }}>
                            <button
                                onClick={onGenerate}
                                disabled={isLoading}
                                className="aca-button large"
                            >
                                {isLoading ? (
                                    <span className="aca-spinner"></span>
                                ) : (
                                    <Lightbulb style={{ width: '16px', height: '16px' }} />
                                )}
                                {isLoading ? 'Generating...' : 'Generate Ideas'}
                            </button>
                        </div>
                    </div>
                </div>
            )}

            {/* Archived Ideas */}
            {archivedIdeas.length > 0 && (
                <div className="aca-card">
                    <div className="aca-card-header">
                        <h2 className="aca-card-title">
                            Archived Ideas ({archivedIdeas.length})
                        </h2>
                        <p className="aca-page-description" style={{ margin: 0, fontSize: '14px' }}>
                            Manage your archived ideas - restore them or delete permanently
                        </p>
                    </div>
                    <div className="aca-list">
                        {archivedIdeas.map((idea, index) => (
                            <div key={idea.id} className="aca-list-item">
                                <span style={{ flexGrow: 1, marginRight: '15px' }}>{idea.title}</span>
                                <div style={{ 
                                    fontSize: '11px', 
                                    fontWeight: '600', 
                                    padding: '4px 8px', 
                                    borderRadius: '4px', 
                                    background: sourceStyles[idea.source].background,
                                    color: sourceStyles[idea.source].color,
                                    border: `1px solid ${sourceStyles[idea.source].borderColor}`,
                                    flexShrink: 0,
                                    marginRight: '10px'
                                }}>
                                    {sourceNames[idea.source]}
                                </div>
                                
                                {/* Action buttons */}
                                <div style={{ display: 'flex', gap: '8px', alignItems: 'center' }}>
                                    {onRestoreIdea && (
                                        <button
                                            onClick={() => onRestoreIdea(idea.id)}
                                            className="aca-button secondary"
                                            style={{ 
                                                fontSize: '11px', 
                                                padding: '4px 8px', 
                                                minWidth: 'auto',
                                                background: '#e6f7e6',
                                                borderColor: '#28a745',
                                                color: '#0a5d0a'
                                            }}
                                            title="Restore to active ideas"
                                        >
                                            <Edit style={{ width: '12px', height: '12px', marginRight: '4px' }} />
                                            Restore
                                        </button>
                                    )}
                                    
                                    {onDeleteIdea && (
                                        <button
                                            onClick={() => {
                                                if (window.confirm(`Are you sure you want to permanently delete "${idea.title}"? This action cannot be undone.`)) {
                                                    onDeleteIdea(idea.id);
                                                }
                                            }}
                                            className="aca-button secondary"
                                            style={{ 
                                                fontSize: '11px', 
                                                padding: '4px 8px', 
                                                minWidth: 'auto',
                                                background: '#ffeaea',
                                                borderColor: '#dc3545',
                                                color: '#721c24'
                                            }}
                                            title="Delete permanently"
                                        >
                                            <Trash style={{ width: '12px', height: '12px' }} />
                                        </button>
                                    )}
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            )}
        </div>
    );
};
