
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

const sourceStyles: { [key in IdeaSource]: string } = {
    'ai': 'bg-green-800/50 text-green-300 border-green-700/60',
    'search-console': 'bg-blue-800/50 text-blue-300 border-blue-700/60',
    'similar': 'bg-purple-800/50 text-purple-300 border-purple-700/60',
    'manual': 'bg-slate-600/50 text-slate-300 border-slate-500/60'
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


    return (
        <div className="bg-slate-800 p-4 rounded-lg border border-slate-700 flex flex-col justify-between items-start gap-4 transition-shadow hover:shadow-lg">
            <div className="flex-grow w-full">
                {isEditing ? (
                    <input
                        ref={inputRef}
                        type="text"
                        value={title}
                        onChange={(e) => setTitle(e.target.value)}
                        onBlur={handleSave}
                        onKeyDown={handleKeyDown}
                        className="flex-grow bg-slate-700 border border-slate-600 rounded-md p-2 text-sm focus:ring-2 focus:ring-blue-500 w-full"
                    />
                ) : (
                    <p 
                    onClick={() => setIsEditing(true)} 
                    className="text-slate-300 font-medium flex-grow cursor-pointer p-2 rounded-md hover:bg-slate-700/50"
                    title="Click to edit"
                    >
                        {idea.title}
                    </p>
                )}
            </div>
            <div className="w-full flex justify-between items-center">
                 <div className={`text-xs font-medium px-2 py-0.5 rounded-full border ${sourceStyles[idea.source]}`}>
                    {sourceNames[idea.source]}
                </div>
                <div className="flex items-center space-x-2 flex-shrink-0">
                    <button
                        onClick={() => onGenerateSimilar(idea)}
                        disabled={isGeneratingSimilar || isLoading}
                        className="bg-purple-600/50 text-purple-200 px-3 py-1.5 rounded-md text-sm font-semibold hover:bg-purple-600/80 hover:text-white transition-colors disabled:bg-slate-600 disabled:cursor-not-allowed flex items-center"
                        title="Generate similar ideas"
                    >
                        {isGeneratingSimilar ? <Spinner className="h-4 w-4" /> : <Sparkles className="h-4 w-4" />}
                    </button>
                    <button
                        onClick={() => onCreateDraft(idea)}
                        disabled={isLoading || isGeneratingSimilar}
                        className="bg-green-600 text-white px-3 py-1.5 rounded-md text-sm font-semibold hover:bg-green-700 transition-colors disabled:bg-slate-600 disabled:cursor-not-allowed flex items-center"
                    >
                        {isLoading ? <Spinner className="h-4 w-4" /> : "Create Draft"}
                    </button>
                     <div className="relative group">
                        <button onClick={() => onArchive(idea.id)} className="text-slate-500 p-1.5 rounded-md hover:bg-red-900/50 hover:text-red-400 transition-colors">
                            <Trash className="h-4 w-4" />
                        </button>
                        <div className="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 bg-slate-900 text-white text-xs rounded-md opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
                            Archive Idea
                        </div>
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
        <div className="space-y-6">
            <header className="flex flex-col sm:flex-row justify-between items-start gap-4">
                <div>
                    <h2 className="text-3xl font-bold text-white border-b border-slate-700 pb-3 mb-2">Idea Board</h2>
                    <p className="text-slate-400">Generate new topics, edit their titles, and turn your favorites into drafts.</p>
                </div>
                <button
                    onClick={onGenerate}
                    disabled={isLoading}
                    className="w-full sm:w-auto bg-blue-600 text-white font-bold py-2.5 px-5 rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center disabled:bg-slate-600 disabled:cursor-not-allowed flex-shrink-0"
                >
                    {isLoading ? <><Spinner className="mr-2 h-5 w-5" /> Generating...</> : 'Generate New Ideas'}
                </button>
            </header>

            <div className="bg-slate-800 p-4 sm:p-6 rounded-lg border border-slate-700/80">
                <form onSubmit={handleAddIdeaSubmit} className="flex gap-2 mb-6 pb-6 border-b border-slate-700">
                    <input
                        type="text"
                        value={newIdeaTitle}
                        onChange={(e) => setNewIdeaTitle(e.target.value)}
                        placeholder="Or add your own idea manually..."
                        className="flex-grow bg-slate-700 border border-slate-600 rounded-md p-2.5 text-sm focus:ring-2 focus:ring-blue-500 placeholder-slate-400"
                    />
                    <button
                        type="submit"
                        disabled={!newIdeaTitle.trim()}
                        className="bg-green-600 text-white font-bold py-2.5 px-4 rounded-lg hover:bg-green-700 transition-colors flex items-center justify-center disabled:bg-slate-600 disabled:cursor-not-allowed"
                    >
                        <PlusCircle className="h-5 w-5 mr-2" /> Add Idea
                    </button>
                </form>

                {ideas.length > 0 ? (
                    <div className="space-y-3">
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
                    <div className="text-center py-16 text-slate-500 border-2 border-dashed border-slate-700 rounded-md">
                        <Lightbulb className="mx-auto h-12 w-12 text-slate-600" />
                        <p className="mt-4 text-lg font-medium">Your Idea Board is Empty</p>
                        <p>Click "Generate New Ideas" or add one manually to get started!</p>
                    </div>
                )}
            </div>
        </div>
    );
};
