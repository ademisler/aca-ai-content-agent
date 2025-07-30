
import React, { memo } from 'react';
import type { View, ActivityLog } from '../types';
import { Lightbulb, BookOpen, FileText, Send, CheckCircle } from './Icons';
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
            className="aca-action-button"
            aria-label={isLoading ? loadingTitle : title}
        >
            <div className="aca-action-icon">
                {icon}
            </div>
            <h3 className="aca-action-title">
                {isLoading ? (loadingTitle || 'Loading...') : title}
                {isLoading && <span className="aca-spinner" style={{ marginLeft: '8px' }}></span>}
            </h3>
            <p className="aca-action-description">{description}</p>
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
    <div className="aca-stat-item">
        <div className="aca-stat-info">
            <div className="aca-stat-icon">{icon}</div>
            <div>
                <h4 className="aca-stat-title">{title}</h4>
                <p className="aca-stat-count">{count} {description}</p>
            </div>
        </div>
        <button 
            onClick={() => onNavigate(view)} 
            className="aca-button"
            aria-label={`View ${title.toLowerCase()}`}
        >
            View
        </button>
    </div>
));

export const Dashboard: React.FC<DashboardProps> = ({ 
    stats, 
    lastAnalyzed, 
    activityLogs, 
    onNavigate, 
    onGenerateIdeas, 
    onUpdateStyleGuide, 
    isLoadingIdeas, 
    isLoadingStyle 
}) => {
    const isStyleGuideReady = !!lastAnalyzed;

    return (
        <div className="aca-fade-in">
            <div className="aca-page-header">
                <h1 className="aca-page-title">Dashboard</h1>
                <p className="aca-page-description">Welcome back! Here's a quick overview of your content pipeline.</p>
            </div>

            {!isStyleGuideReady && (
                <div className="aca-alert info">
                    <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', flexWrap: 'wrap', gap: '15px' }}>
                        <div style={{ display: 'flex', alignItems: 'center' }}>
                            <Lightbulb style={{ width: '24px', height: '24px', marginRight: '12px', flexShrink: 0 }} />
                            <div>
                                <h4 style={{ margin: '0 0 5px 0', fontWeight: '600', color: '#0073aa' }}>Get Started with AI Content Agent (ACA)</h4>
                                <p style={{ margin: 0, fontSize: '13px' }}>Create your Style Guide first to enable all features and generate on-brand content.</p>
                            </div>
                        </div>
                        <button 
                            onClick={() => onNavigate('style-guide')} 
                            className="aca-button large"
                            style={{ flexShrink: 0 }}
                        >
                            Create Style Guide
                        </button>
                    </div>
                </div>
            )}

            <div className="aca-grid aca-grid-2" style={{ marginBottom: '30px' }}>
                <div className="aca-card">
                    <div className="aca-card-header">
                        <h2 className="aca-card-title">Quick Actions</h2>
                    </div>
                    <div className="aca-grid aca-grid-2">
                        <ActionButton
                            icon={<Lightbulb />}
                            title="Generate Ideas"
                            description="Create fresh content ideas based on your style guide"
                            onClick={onGenerateIdeas}
                            disabled={!isStyleGuideReady}
                            isLoading={isLoadingIdeas}
                            loadingTitle="Generating Ideas..."
                        />
                        <ActionButton
                            icon={<BookOpen />}
                            title="Update Style Guide"
                            description="Analyze your site content to refresh your style guide"
                            onClick={onUpdateStyleGuide}
                            isLoading={isLoadingStyle}
                            loadingTitle="Analyzing Style..."
                        />
                    </div>
                </div>

                <div className="aca-card">
                    <div className="aca-card-header">
                        <h2 className="aca-card-title">Content Pipeline</h2>
                    </div>
                    <div style={{ display: 'flex', flexDirection: 'column', gap: '10px' }}>
                        <PipelineItem
                            icon={<Lightbulb />}
                            title="Ideas"
                            count={stats.ideas}
                            description="content ideas"
                            view="ideas"
                            onNavigate={onNavigate}
                        />
                        <PipelineItem
                            icon={<FileText />}
                            title="Drafts"
                            count={stats.drafts}
                            description="draft posts"
                            view="drafts"
                            onNavigate={onNavigate}
                        />
                        <PipelineItem
                            icon={<Send />}
                            title="Published"
                            count={stats.published}
                            description="published posts"
                            view="published"
                            onNavigate={onNavigate}
                        />
                    </div>
                </div>
            </div>

            {isStyleGuideReady && (
                <div className="aca-card">
                    <div className="aca-card-header">
                        <h2 className="aca-card-title">
                            <CheckCircle style={{ width: '20px', height: '20px', marginRight: '8px', fill: '#00a32a' }} />
                            Style Guide Active
                        </h2>
                    </div>
                    <p style={{ margin: '0 0 15px 0', color: '#646970' }}>
                        Last analyzed: {new Date(lastAnalyzed).toLocaleDateString()} at {new Date(lastAnalyzed).toLocaleTimeString()}
                    </p>
                    <div style={{ display: 'flex', gap: '10px', flexWrap: 'wrap' }}>
                        <button 
                            onClick={() => onNavigate('style-guide')} 
                            className="aca-button secondary"
                        >
                            View Style Guide
                        </button>
                        <button 
                            onClick={onUpdateStyleGuide} 
                            className="aca-button secondary"
                            disabled={isLoadingStyle}
                        >
                            {isLoadingStyle ? 'Updating...' : 'Refresh Analysis'}
                        </button>
                    </div>
                </div>
            )}

            <div className="aca-card">
                <div className="aca-card-header">
                    <h2 className="aca-card-title">Recent Activity</h2>
                </div>
                <ActivityLogList logs={activityLogs.slice(0, 10)} />
            </div>
        </div>
    );
};
