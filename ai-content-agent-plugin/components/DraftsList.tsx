
import React from 'react';
import type { Draft } from '../types';
import { FileText, Send, Spinner, Calendar, Eye } from './Icons';

interface DraftsListProps {
    drafts: Draft[];
    onSelectDraft: (draft: Draft) => void;
    onPublish: (id: number) => void;
    publishingId: number | null;
}

const DraftCard: React.FC<{
    draft: Draft;
    onSelectDraft: (draft: Draft) => void;
    onPublish: (id: number) => void;
    isPublishing: boolean;
}> = ({ draft, onSelectDraft, onPublish, isPublishing }) => {
    const isScheduled = !!draft.scheduledFor;
    
    return (
        <div className="aca-card" style={{ 
            margin: 0,
            opacity: isPublishing ? 0.7 : 1,
            transform: isPublishing ? 'scale(0.98)' : 'scale(1)',
            transition: 'all 0.3s ease',
            cursor: isPublishing ? 'not-allowed' : 'default'
        }}>
            {/* Draft Header */}
            <div style={{ 
                display: 'flex', 
                justifyContent: 'space-between', 
                alignItems: 'flex-start',
                marginBottom: '15px',
                gap: '15px'
            }}>
                <div style={{ flexGrow: 1 }}>
                    <h3 style={{ 
                        fontSize: '18px', 
                        fontWeight: '600', 
                        color: '#23282d', 
                        margin: '0 0 8px 0',
                        lineHeight: '1.3'
                    }}>
                        {draft.title}
                    </h3>
                    <div style={{ 
                        display: 'flex', 
                        alignItems: 'center', 
                        gap: '15px', 
                        fontSize: '13px', 
                        color: '#646970',
                        flexWrap: 'wrap'
                    }}>
                        <span>Created: {new Date(draft.createdAt).toLocaleDateString()}</span>
                        {isScheduled && (
                            <div style={{ 
                                display: 'flex', 
                                alignItems: 'center', 
                                gap: '6px',
                                color: '#0073aa',
                                fontWeight: '500'
                            }}>
                                <Calendar style={{ width: '14px', height: '14px' }} />
                                Scheduled: {new Date(draft.scheduledFor!).toLocaleDateString()}
                            </div>
                        )}
                    </div>
                </div>
                
                {/* Status Badge */}
                <div style={{ 
                    fontSize: '11px', 
                    fontWeight: '600', 
                    padding: '6px 12px', 
                    borderRadius: '4px', 
                    border: '1px solid',
                    background: isScheduled ? '#e3f2fd' : '#f6f7f7',
                    color: isScheduled ? '#0d47a1' : '#646970',
                    borderColor: isScheduled ? '#2196f3' : '#8c8f94',
                    flexShrink: 0
                }}>
                    {isScheduled ? 'Scheduled' : 'Draft'}
                </div>
            </div>

            {/* Actions */}
            <div style={{ 
                display: 'flex', 
                alignItems: 'center', 
                gap: '10px',
                paddingTop: '15px',
                borderTop: '1px solid #f0f0f1',
                flexWrap: 'wrap',
                justifyContent: 'flex-end'
            }}>
                <button
                    onClick={() => onSelectDraft(draft)}
                    disabled={isPublishing}
                    className="aca-button secondary"
                    style={{ 
                        fontSize: '13px',
                        padding: '8px 16px',
                        display: 'flex',
                        alignItems: 'center',
                        gap: '8px'
                    }}
                >
                    <Eye style={{ width: '14px', height: '14px' }} />
                    View Draft
                </button>
                
                <button
                    onClick={() => onPublish(draft.id)}
                    disabled={isPublishing}
                    className="aca-button"
                    style={{ 
                        display: 'flex', 
                        alignItems: 'center', 
                        gap: '8px',
                        fontSize: '13px',
                        padding: '8px 16px',
                        background: isPublishing ? '#f6f7f7' : '#00a32a',
                        borderColor: isPublishing ? '#ccd0d4' : '#00a32a',
                        color: isPublishing ? '#a7aaad' : '#ffffff',
                        cursor: isPublishing ? 'wait' : 'pointer',
                        minWidth: '120px',
                        justifyContent: 'center'
                    }}
                >
                    {isPublishing ? (
                        <>
                            <Spinner style={{ width: '16px', height: '16px' }} />
                            Publishing...
                        </>
                    ) : (
                        <>
                            <Send style={{ width: '14px', height: '14px' }} />
                            Publish Now
                        </>
                    )}
                </button>
            </div>
        </div>
    );
};

export const DraftsList: React.FC<DraftsListProps> = ({ drafts, onSelectDraft, onPublish, publishingId }) => {
    const scheduledDrafts = drafts.filter(draft => draft.scheduledFor);
    const unscheduledDrafts = drafts.filter(draft => !draft.scheduledFor);

    return (
        <div className="aca-fade-in">
            <div className="aca-page-header">
                <h1 className="aca-page-title">Content Drafts</h1>
                <p className="aca-page-description">
                    Review your AI-generated content drafts and publish them when ready. Click "View Draft" to edit before publishing.
                </p>
            </div>

            {drafts.length > 0 ? (
                <div style={{ display: 'flex', flexDirection: 'column', gap: '30px' }}>
                    {/* Scheduled Drafts */}
                    {scheduledDrafts.length > 0 && (
                        <div className="aca-card">
                            <div className="aca-card-header">
                                <h2 className="aca-card-title">
                                    <Calendar style={{ width: '20px', height: '20px', marginRight: '8px', fill: '#0073aa' }} />
                                    Scheduled Drafts ({scheduledDrafts.length})
                                </h2>
                            </div>
                            <div className="aca-grid aca-grid-2" style={{ gap: '20px' }}>
                                {scheduledDrafts.map(draft => (
                                    <DraftCard
                                        key={draft.id}
                                        draft={draft}
                                        onSelectDraft={onSelectDraft}
                                        onPublish={onPublish}
                                        isPublishing={publishingId === draft.id}
                                    />
                                ))}
                            </div>
                        </div>
                    )}

                    {/* Unscheduled Drafts */}
                    {unscheduledDrafts.length > 0 && (
                        <div className="aca-card">
                            <div className="aca-card-header">
                                <h2 className="aca-card-title">
                                    <FileText style={{ width: '20px', height: '20px', marginRight: '8px', fill: '#0073aa' }} />
                                    Ready to Publish ({unscheduledDrafts.length})
                                </h2>
                            </div>
                            <div className="aca-grid aca-grid-2" style={{ gap: '20px' }}>
                                {unscheduledDrafts.map(draft => (
                                    <DraftCard
                                        key={draft.id}
                                        draft={draft}
                                        onSelectDraft={onSelectDraft}
                                        onPublish={onPublish}
                                        isPublishing={publishingId === draft.id}
                                    />
                                ))}
                            </div>
                        </div>
                    )}
                </div>
            ) : (
                <div className="aca-card">
                    <div style={{ 
                        textAlign: 'center', 
                        padding: '60px 20px', 
                        color: '#646970'
                    }}>
                        <FileText style={{ margin: '0 auto 20px auto', width: '48px', height: '48px', fill: '#a7aaad' }} />
                        <h3 style={{ margin: '0 0 10px 0', fontSize: '18px', fontWeight: '500', color: '#23282d' }}>
                            No Drafts Created Yet
                        </h3>
                        <p style={{ margin: '0 0 25px 0', fontSize: '14px', maxWidth: '400px', marginLeft: 'auto', marginRight: 'auto' }}>
                            Create content ideas first, then turn them into drafts to see them here.
                        </p>
                        <button 
                            onClick={() => window.location.hash = '#ideas'} 
                            className="aca-button large"
                            style={{ 
                                display: 'inline-flex', 
                                alignItems: 'center',
                                gap: '8px'
                            }}
                        >
                            <FileText style={{ width: '16px', height: '16px' }} />
                            Go to Idea Board
                        </button>
                    </div>
                </div>
            )}
        </div>
    );
};
