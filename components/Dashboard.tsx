
import React, { memo } from 'react';
import type { View, ActivityLog } from '../types';
import { Lightbulb, BookOpen, FileText, Send, Spinner, CheckCircle } from './Icons';
import { ActivityLogList } from './ActivityLog';

interface DashboardProps {
    stats: {
        ideas: number;
        drafts: number;
        published: number;
    };
    lastAnalyzed?: string | undefined;
    activityLogs: ActivityLog[];
    onNavigate: (view: View) => void;
    onGenerateIdeas: () => void;
    onUpdateStyleGuide: () => void;
    isLoadingIdeas: boolean;
    isLoadingStyle: boolean;
}

const ActionButton: React.FC<{
    icon: React.ReactNode;
    title: string;
    description: string;
    onClick: () => void;
    disabled?: boolean;
    isLoading?: boolean;
    loadingTitle?: string;
}> = memo(({ icon, title, description, onClick, disabled, isLoading, loadingTitle }) => {
    return (
        <button
            onClick={onClick}
            disabled={disabled || isLoading}
            aria-label={isLoading ? loadingTitle : title}
            aria-describedby={`action-desc-${title.replace(/\s+/g, '-').toLowerCase()}`}
            className="bg-slate-800 p-6 rounded-lg border border-slate-700/80 shadow-md flex flex-col items-start text-left w-full hover:bg-slate-700/50 hover:border-slate-600 transition-all disabled:opacity-50 disabled:cursor-not-allowed transform hover:-translate-y-1 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-950"
        >
            <div className="bg-blue-600 p-3 rounded-lg mb-4">
                {icon}
            </div>
            <h3 className="text-lg font-semibold text-white mb-1 flex items-center">
                <span>{isLoading ? loadingTitle : title}</span>
                {isLoading && <Spinner className="h-5 w-5 ml-2" />}
            </h3>
            <p id={`action-desc-${title.replace(/\s+/g, '-').toLowerCase()}`} className="text-slate-400 text-sm">{description}</p>
        </button>
    );
});

const PipelineItem: React.FC<{
    icon: React.ReactNode;
    title: string;
    count: number;
    description: string;
    view: View;
    onNavigate: (view: View) => void;
}> = memo(({ icon, title, count, description, view, onNavigate }) => (
    <div className="flex items-center justify-between p-4 bg-slate-900/50 rounded-lg border border-slate-700/60">
        <div className="flex items-center gap-4">
            {icon}
            <div>
                <p className="font-semibold text-white">{title}</p>
                <p className="text-sm text-slate-400">{count} {description}</p>
            </div>
        </div>
        <button 
            onClick={() => onNavigate(view)} 
            aria-label={`View ${title.toLowerCase()}`}
            className="bg-slate-600 text-white px-4 py-1.5 rounded-md text-sm font-semibold hover:bg-slate-700 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-950"
        >
            View
        </button>
    </div>
));


export const Dashboard: React.FC<DashboardProps> = ({ stats, lastAnalyzed, activityLogs, onNavigate, onGenerateIdeas, onUpdateStyleGuide, isLoadingIdeas, isLoadingStyle }) => {
    const isStyleGuideReady = !!lastAnalyzed;

    return (
        <div className="space-y-8 animate-fade-in-fast">
            <header>
                <h1 className="text-3xl font-bold text-white border-b border-slate-700 pb-3 mb-6">Dashboard</h1>
                <p className="text-slate-400 -mt-2">Welcome back! Here's a quick overview of your content pipeline.</p>
            </header>

            {!isStyleGuideReady && (
                 <div className="bg-blue-900/20 border border-blue-700/50 text-blue-300 px-6 py-5 rounded-lg flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div className="flex items-center">
                        <Lightbulb className="h-8 w-8 text-blue-400 flex-shrink-0 mr-4" />
                        <div>
                            <h4 className="font-bold text-white">Get Started with AI Content Agent</h4>
                            <p className="text-sm">Create your Style Guide first to enable all features and generate on-brand content.</p>
                        </div>
                    </div>
                    <button onClick={() => onNavigate('style-guide')} className="bg-blue-600 text-white font-bold py-2 px-5 rounded-lg hover:bg-blue-700 transition-colors flex-shrink-0 w-full sm:w-auto">
                        Create Style Guide
                    </button>
                </div>
            )}
            
            <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {/* Left Column */}
                <div className="lg:col-span-2 space-y-8">
                    <section aria-labelledby="quick-actions-heading">
                        <h2 id="quick-actions-heading" className="text-xl font-semibold text-white mb-4">Quick Actions</h2>
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <ActionButton
                                icon={<BookOpen className="h-6 w-6 text-white" />}
                                title={isStyleGuideReady ? 'Update Style Guide' : 'Learn My Style'}
                                description="Analyze your content to define or refine your brand's voice."
                                onClick={onUpdateStyleGuide}
                                isLoading={isLoadingStyle}
                                loadingTitle="Scanning..."
                            />
                            <ActionButton
                                icon={<Lightbulb className="h-6 w-6 text-white" />}
                                title="Generate New Ideas"
                                description="Create a fresh batch of content ideas based on your style."
                                onClick={onGenerateIdeas}
                                disabled={!isStyleGuideReady}
                                isLoading={isLoadingIdeas}
                                loadingTitle="Generating..."
                            />
                        </div>
                    </section>
                    
                    <section aria-labelledby="content-pipeline-heading">
                        <h2 id="content-pipeline-heading" className="text-xl font-semibold text-white mb-4">Content Pipeline</h2>
                        <div className="bg-slate-800 p-4 sm:p-5 rounded-lg border border-slate-700/80 space-y-4">
                           <PipelineItem
                                icon={<div className="p-3 rounded-lg bg-yellow-900/30"><Lightbulb className="h-6 w-6 text-yellow-400" /></div>}
                                title="Pending Ideas"
                                count={stats.ideas}
                                description="ideas waiting"
                                view="ideas"
                                onNavigate={onNavigate}
                           />
                           <PipelineItem
                                icon={<div className="p-3 rounded-lg bg-sky-900/30"><FileText className="h-6 w-6 text-sky-400" /></div>}
                                title="Drafts"
                                count={stats.drafts}
                                description="drafts to review"
                                view="drafts"
                                onNavigate={onNavigate}
                           />
                           <PipelineItem
                                icon={<div className="p-3 rounded-lg bg-green-900/30"><Send className="h-6 w-6 text-green-400" /></div>}
                                title="Published Posts"
                                count={stats.published}
                                description="posts are live"
                                view="published"
                                onNavigate={onNavigate}
                           />
                        </div>
                    </section>
                </div>

                {/* Right Column */}
                <div className="lg:col-span-1 space-y-8">
                    <section aria-labelledby="status-heading">
                         <h2 id="status-heading" className="text-xl font-semibold text-white mb-4">Status</h2>
                         <div className="bg-slate-800 p-5 rounded-lg border border-slate-700/80">
                            <div className="flex items-center gap-4">
                                <div className="p-3 rounded-full bg-slate-700">
                                    <BookOpen className="h-6 w-6 text-purple-400" />
                                </div>
                                <div>
                                    <p className="font-semibold text-white">Style Guide</p>
                                    {isStyleGuideReady ? (
                                        <p className="text-sm text-green-400 flex items-center gap-1.5"><CheckCircle className="h-4 w-4" /> Ready</p>
                                    ) : (
                                        <p className="text-sm text-yellow-400">Not Set</p>
                                    )}
                                </div>
                            </div>
                            {lastAnalyzed && (
                                <div className="mt-4 pt-4 border-t border-slate-700/60">
                                    <p className="text-xs text-slate-400">Last Scanned:</p>
                                    <p className="text-sm font-medium text-slate-300">{new Date(lastAnalyzed).toLocaleString()}</p>
                                </div>
                            )}
                         </div>
                    </section>
                    
                    <section aria-labelledby="recent-activity-heading">
                        <h2 id="recent-activity-heading" className="text-xl font-semibold text-white mb-4">Recent Activity</h2>
                        <div className="bg-slate-800 p-4 sm:p-5 rounded-lg border border-slate-700/80 h-96 overflow-y-auto">
                           <ActivityLogList logs={activityLogs} />
                        </div>
                    </section>
                </div>
            </div>
        </div>
    );
};
