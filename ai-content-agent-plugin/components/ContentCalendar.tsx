
import React, { useState } from 'react';
import { ChevronLeft, ChevronRight, Calendar as CalendarIcon, Clock, Edit, Eye } from './Icons';
import { Draft } from '../types';

interface ContentCalendarProps {
    drafts: Draft[];
    publishedPosts: Draft[];
    onScheduleDraft: (draftId: number, scheduledDate: string) => void;
    onSelectPost: (post: Draft) => void;
    onPublishDraft?: (draftId: number) => void; // Optional for immediate publishing
    onUpdatePostDate?: (postId: number, newDate: string, shouldConvertToDraft?: boolean) => void; // For moving published posts
}

export const ContentCalendar: React.FC<ContentCalendarProps> = ({ drafts, publishedPosts, onScheduleDraft, onSelectPost, onPublishDraft, onUpdatePostDate }) => {
    const [currentDate, setCurrentDate] = useState(new Date());
    const [dragOverDate, setDragOverDate] = useState<Date | null>(null);
    const [draggedDraft, setDraggedDraft] = useState<Draft | null>(null);
    const [expandedDates, setExpandedDates] = useState<Set<string>>(new Set());

    // Separate drafts into scheduled and unscheduled
    const unscheduledDrafts = drafts.filter(draft => !draft.scheduledFor);
    const scheduledDrafts = drafts.filter(draft => draft.scheduledFor);

    // Function to open WordPress edit page
    const openWordPressEditor = (postId: number) => {
        // Check if WordPress localized data is available
        if (!window.acaData) {
            console.error('ACA Error: window.acaData is not defined');
            alert('WordPress data not available. Please refresh the page.');
            return;
        }
        
        const editUrl = `${window.acaData.admin_url}post.php?post=${postId}&action=edit`;
        window.open(editUrl, '_blank');
    };

    // Get posts for a specific date
    const getPostsForDate = (date: Date) => {
        const dateStr = date.toISOString().split('T')[0];
        
        const scheduled = scheduledDrafts.filter(draft => {
            if (!draft.scheduledFor) return false;
            const scheduledDate = new Date(draft.scheduledFor);
            return scheduledDate.toISOString().split('T')[0] === dateStr;
        });

        const published = publishedPosts.filter(post => {
            if (!post.publishedAt) return false;
            const publishedDate = new Date(post.publishedAt);
            return publishedDate.toISOString().split('T')[0] === dateStr;
        });

        return { scheduled, published };
    };

    // Toggle expanded state for a date
    const toggleDateExpansion = (date: Date) => {
        const dateStr = date.toISOString().split('T')[0];
        const newExpanded = new Set(expandedDates);
        if (newExpanded.has(dateStr)) {
            newExpanded.delete(dateStr);
        } else {
            newExpanded.add(dateStr);
        }
        setExpandedDates(newExpanded);
    };

    const handleDragStart = (e: React.DragEvent<HTMLDivElement>, draft: Draft) => {
        e.dataTransfer.setData('text/plain', draft.id.toString());
        setDraggedDraft(draft);
    };

    const handleDragOver = (e: React.DragEvent<HTMLDivElement>, date: Date) => {
        e.preventDefault();
        setDragOverDate(date);
    };

    const handleDrop = (e: React.DragEvent<HTMLDivElement>, date: Date) => {
        e.preventDefault();
        const postId = e.dataTransfer.getData('text/plain');
        if (postId && draggedDraft) {
            const today = new Date();
            today.setHours(0, 0, 0, 0); // Reset time to compare dates only
            
            const dropDate = new Date(date);
            dropDate.setHours(0, 0, 0, 0);
            
            const isPublishedPost = draggedDraft.status === 'published';
            
            if (isPublishedPost) {
                // Handle published post movement
                if (dropDate > today) {
                    // Moving to future date - convert to draft
                    const confirmConvert = window.confirm(
                        `You're moving a published post to a future date (${date.toLocaleDateString()}). ` +
                        `This will convert it to a scheduled draft. Do you want to continue?`
                    );
                    
                    if (confirmConvert && onUpdatePostDate) {
                        onUpdatePostDate(parseInt(postId), date.toISOString(), true);
                    }
                } else {
                    // Moving to past/today - just update publish date
                    const confirmMove = window.confirm(
                        `You're changing the publish date of this post to ${date.toLocaleDateString()}. ` +
                        `Do you want to continue?`
                    );
                    
                    if (confirmMove && onUpdatePostDate) {
                        onUpdatePostDate(parseInt(postId), date.toISOString(), false);
                    }
                }
            } else {
                // Handle draft movement (existing logic)
                if (dropDate < today) {
                    // Show confirmation dialog
                    const confirmPublish = window.confirm(
                        `You're scheduling this draft for a past date (${date.toLocaleDateString()}). ` +
                        `This will publish the post immediately. Do you want to continue?`
                    );
                    
                    if (confirmPublish) {
                        // Instead of scheduling, we'll call publish directly
                        if (onPublishDraft) {
                            onPublishDraft(parseInt(postId));
                        } else {
                            // Fallback: schedule for the past date (will be published by WordPress)
                            onScheduleDraft(parseInt(postId), date.toISOString());
                        }
                    }
                } else {
                    // Normal scheduling for future dates
                    onScheduleDraft(parseInt(postId), date.toISOString());
                }
            }
        }
        setDragOverDate(null);
        setDraggedDraft(null);
    };

    const handleDragLeave = (e: React.DragEvent<HTMLDivElement>) => {
        e.preventDefault();
        if (!e.currentTarget.contains(e.relatedTarget as Node)) {
            setDragOverDate(null);
        }
    };

    const startOfMonth = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
    const endOfMonth = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);
    const startDayOfWeek = startOfMonth.getDay(); // 0=Sun, 1=Mon, ...
    
    const daysInMonth = [];
    // Add blank days for the start of the week
    for (let i = 0; i < startDayOfWeek; i++) {
        daysInMonth.push(null);
    }
    // Add days of the month
    for (let day = 1; day <= endOfMonth.getDate(); day++) {
        daysInMonth.push(new Date(currentDate.getFullYear(), currentDate.getMonth(), day));
    }

    const nextMonth = () => setCurrentDate(new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 1));
    const prevMonth = () => setCurrentDate(new Date(currentDate.getFullYear(), currentDate.getMonth() - 1, 1));

    // Render a single post item
    const renderPostItem = (post: Draft, type: 'scheduled' | 'published', isCompact: boolean = false) => {
        const isScheduled = type === 'scheduled';
        const style = isScheduled ? {
            background: '#fff3cd',
            border: '1px solid #ffc107',
            color: '#856404',
        } : {
            background: '#d4edda',
            border: '1px solid #28a745',
            color: '#155724',
        };

        return (
            <div 
                key={`${type}-${post.id}`}
                draggable={true}
                onDragStart={(e) => handleDragStart(e, post)}
                onClick={() => openWordPressEditor(post.id)}
                className="aca-action-button"
                style={{
                    ...style,
                    padding: isCompact ? '3px 5px' : '5px 7px',
                    fontSize: isCompact ? '9px' : '10px',
                    overflow: 'hidden',
                    textOverflow: 'ellipsis',
                    whiteSpace: 'nowrap' as const,
                    cursor: 'pointer',
                    display: 'flex',
                    alignItems: 'center',
                    gap: '3px',
                    fontWeight: '500',
                    margin: 0,
                    borderRadius: '3px',
                    minHeight: isCompact ? '18px' : '20px',
                    transition: 'all 0.2s ease'
                }}
                title={`${isScheduled ? 'Scheduled' : 'Published'}: ${post.title || `${isScheduled ? 'Draft' : 'Post'} ${post.id}`} (Click to edit, drag to reschedule)`}
            >
                {isScheduled ? (
                    <Clock style={{ width: isCompact ? '8px' : '9px', height: isCompact ? '8px' : '9px', flexShrink: 0 }} />
                ) : (
                    <Eye style={{ width: isCompact ? '8px' : '9px', height: isCompact ? '8px' : '9px', flexShrink: 0 }} />
                )}
                <span style={{ flex: 1, overflow: 'hidden', textOverflow: 'ellipsis' }}>
                    {post.title || `${isScheduled ? 'Draft' : 'Post'} ${post.id}`}
                </span>
                <Edit style={{ width: isCompact ? '6px' : '7px', height: isCompact ? '6px' : '7px', flexShrink: 0 }} />
            </div>
        );
    };

    return (
        <div className="aca-fade-in">
            {/* Modern Calendar Header */}
            <div style={{
                background: 'linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%)',
                borderRadius: '12px',
                padding: '30px',
                marginBottom: '30px',
                color: 'white',
                position: 'relative',
                overflow: 'hidden'
            }}>
                <div style={{ position: 'relative', zIndex: 2 }}>
                    <div style={{ display: 'flex', alignItems: 'center', gap: '12px', marginBottom: '12px' }}>
                        <div style={{
                            width: '48px',
                            height: '48px',
                            background: 'rgba(255,255,255,0.2)',
                            borderRadius: '12px',
                            display: 'flex',
                            alignItems: 'center',
                            justifyContent: 'center',
                            backdropFilter: 'blur(10px)'
                        }}>
                            <CalendarIcon style={{ width: '24px', height: '24px' }} />
                        </div>
                        <div>
                            <h1 style={{ 
                                fontSize: '28px', 
                                fontWeight: '700', 
                                margin: 0,
                                textShadow: '0 2px 4px rgba(0,0,0,0.1)'
                            }}>
                                Content Calendar
                            </h1>
                            <div style={{ fontSize: '16px', opacity: 0.9, marginTop: '4px' }}>
                                Schedule and organize your content timeline
                            </div>
                        </div>
                    </div>
                    <p style={{ 
                        fontSize: '14px', 
                        opacity: 0.85,
                        margin: 0,
                        maxWidth: '600px',
                        lineHeight: '1.5'
                    }}>
                        Schedule your drafts by dragging them onto calendar dates. View scheduled and published content timeline.
                    </p>
                    
                    {/* Stats */}
                    <div style={{ display: 'flex', gap: '20px', marginTop: '20px', flexWrap: 'wrap' }}>
                        <div style={{ display: 'flex', alignItems: 'center', gap: '6px' }}>
                            <div style={{ width: '8px', height: '8px', backgroundColor: '#fbbf24', borderRadius: '50%' }}></div>
                            <span style={{ fontSize: '14px', opacity: 0.9 }}>{scheduledDrafts.length} Scheduled</span>
                        </div>
                        <div style={{ display: 'flex', alignItems: 'center', gap: '6px' }}>
                            <div style={{ width: '8px', height: '8px', backgroundColor: '#4ade80', borderRadius: '50%' }}></div>
                            <span style={{ fontSize: '14px', opacity: 0.9 }}>{publishedPosts.length} Published</span>
                        </div>
                        <div style={{ display: 'flex', alignItems: 'center', gap: '6px' }}>
                            <div style={{ width: '8px', height: '8px', backgroundColor: '#60a5fa', borderRadius: '50%' }}></div>
                            <span style={{ fontSize: '14px', opacity: 0.9 }}>{unscheduledDrafts.length} Unscheduled</span>
                        </div>
                    </div>
                </div>
                {/* Decorative elements */}
                <div style={{
                    position: 'absolute',
                    top: '-30px',
                    right: '-30px',
                    width: '120px',
                    height: '120px',
                    background: 'rgba(255,255,255,0.1)',
                    borderRadius: '50%',
                    zIndex: 1
                }}></div>
                <div style={{
                    position: 'absolute',
                    bottom: '-20px',
                    left: '-20px',
                    width: '80px',
                    height: '80px',
                    background: 'rgba(255,255,255,0.05)',
                    borderRadius: '50%',
                    zIndex: 1
                }}></div>
            </div>

            <div style={{ display: 'flex', flexDirection: 'column', gap: '20px' }}>
                {/* Calendar */}
                <div className="aca-card">
                    <div className="aca-card-header">
                        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                            <h2 className="aca-card-title">
                                <CalendarIcon className="aca-nav-item-icon" />
                                {currentDate.toLocaleDateString('en-US', { month: 'long', year: 'numeric' })}
                            </h2>
                            <div style={{ display: 'flex', gap: '8px' }}>
                                <button onClick={prevMonth} className="aca-action-button">
                                    <ChevronLeft style={{ width: '20px', height: '20px' }} />
                                </button>
                                <button onClick={nextMonth} className="aca-action-button">
                                    <ChevronRight style={{ width: '20px', height: '20px' }} />
                                </button>
                            </div>
                        </div>
                    </div>
                    <div className="aca-card-content">
                        {/* Calendar Grid */}
                        <div style={{ 
                            display: 'grid', 
                            gridTemplateColumns: 'repeat(7, 1fr)', 
                            gap: '1px', 
                            backgroundColor: '#e0e0e0',
                            border: '1px solid #e0e0e0'
                        }}>
                            {/* Day headers */}
                            {['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'].map(day => (
                                <div key={day} style={{
                                    backgroundColor: '#f8f9fa',
                                    padding: '12px 8px',
                                    textAlign: 'center' as const,
                                    fontWeight: '600',
                                    fontSize: '14px',
                                    color: '#495057'
                                }}>
                                    {day}
                                </div>
                            ))}
                            
                            {/* Calendar days */}
                            {daysInMonth.map((date, index) => {
                                if (!date) {
                                    return <div key={index} style={{ backgroundColor: '#fff', minHeight: '120px' }} />;
                                }
                                
                                const { scheduled, published } = getPostsForDate(date);
                                const totalPosts = scheduled.length + published.length;
                                const isToday = date.toDateString() === new Date().toDateString();
                                const isDragOver = dragOverDate && date.getTime() === dragOverDate.getTime();
                                const dateStr = date.toISOString().split('T')[0];
                                const isExpanded = expandedDates.has(dateStr);
                                
                                // Smart display logic
                                const maxVisiblePosts = 3;
                                const shouldShowExpander = totalPosts > maxVisiblePosts;
                                const visibleScheduled = isExpanded ? scheduled : scheduled.slice(0, Math.min(maxVisiblePosts, scheduled.length));
                                const remainingSlots = isExpanded ? published.length : Math.max(0, maxVisiblePosts - scheduled.length);
                                const visiblePublished = published.slice(0, remainingSlots);
                                const hiddenCount = totalPosts - visibleScheduled.length - visiblePublished.length;
                                
                                return (
                                    <div
                                        key={date.getTime()}
                                        onDragOver={(e) => handleDragOver(e, date)}
                                        onDrop={(e) => handleDrop(e, date)}
                                        onDragLeave={handleDragLeave}
                                        style={{
                                            backgroundColor: isDragOver ? '#e3f2fd' : '#fff',
                                            minHeight: '120px',
                                            padding: '8px',
                                            position: 'relative',
                                            border: isToday ? '2px solid #2196f3' : 'none',
                                            cursor: 'pointer',
                                            overflow: 'visible'
                                        }}
                                    >
                                        {/* Date number */}
                                        <div style={{
                                            position: 'absolute',
                                            top: '8px',
                                            left: '8px',
                                            fontWeight: isToday ? '700' : '600',
                                            fontSize: '16px',
                                            color: isToday ? '#2196f3' : '#333',
                                            zIndex: 1
                                        }}>
                                            {date.getDate()}
                                        </div>
                                        
                                        {/* Post count indicator */}
                                        {totalPosts > 0 && (
                                            <div style={{
                                                position: 'absolute',
                                                top: '8px',
                                                right: '8px',
                                                background: totalPosts > 3 ? '#ff9800' : '#4caf50',
                                                color: 'white',
                                                borderRadius: '50%',
                                                width: '18px',
                                                height: '18px',
                                                display: 'flex',
                                                alignItems: 'center',
                                                justifyContent: 'center',
                                                fontSize: '9px',
                                                fontWeight: 'bold',
                                                zIndex: 1
                                            }}>
                                                {totalPosts}
                                            </div>
                                        )}
                                        
                                        {/* Content area */}
                                        <div style={{ 
                                            display: 'flex', 
                                            flexDirection: 'column', 
                                            gap: '2px', 
                                            marginTop: '28px',
                                            flex: 1
                                        }}>
                                            {/* Visible scheduled drafts */}
                                            {visibleScheduled.map(draft => 
                                                renderPostItem(draft, 'scheduled', !isExpanded && totalPosts > 2)
                                            )}
                                            
                                            {/* Visible published posts */}
                                            {visiblePublished.map(post => 
                                                renderPostItem(post, 'published', !isExpanded && totalPosts > 2)
                                            )}
                                            
                                            {/* Expand/Collapse button */}
                                            {shouldShowExpander && (
                                                <button
                                                    onClick={(e) => {
                                                        e.stopPropagation();
                                                        toggleDateExpansion(date);
                                                    }}
                                                    style={{
                                                        background: '#f0f0f0',
                                                        border: '1px solid #ccc',
                                                        color: '#666',
                                                        padding: '3px 6px',
                                                        fontSize: '9px',
                                                        borderRadius: '2px',
                                                        cursor: 'pointer',
                                                        textAlign: 'center' as const,
                                                        fontWeight: '500',
                                                        transition: 'all 0.2s ease'
                                                    }}
                                                    onMouseOver={(e) => {
                                                        e.currentTarget.style.background = '#e0e0e0';
                                                    }}
                                                    onMouseOut={(e) => {
                                                        e.currentTarget.style.background = '#f0f0f0';
                                                    }}
                                                >
                                                    {isExpanded ? 'Show Less' : `+${hiddenCount} More`}
                                                </button>
                                            )}
                                        </div>
                                    </div>
                                );
                            })}
                        </div>
                    </div>
                </div>

                {/* Unscheduled Drafts Sidebar */}
                <div className="aca-card">
                    <div className="aca-card-header">
                        <h3 className="aca-card-title">Unscheduled Drafts</h3>
                        <p style={{ margin: 0, fontSize: '14px', color: '#666' }}>
                            Drag these drafts onto calendar dates to schedule them
                        </p>
                    </div>
                    <div className="aca-card-content">
                        {unscheduledDrafts.length === 0 ? (
                            <p style={{ textAlign: 'center', color: '#666', margin: '20px 0' }}>
                                No unscheduled drafts available
                            </p>
                        ) : (
                            <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(250px, 1fr))', gap: '12px' }}>
                                {unscheduledDrafts.map(draft => (
                                    <div
                                        key={draft.id}
                                        draggable
                                        onDragStart={(e) => handleDragStart(e, draft)}
                                        className="aca-action-button"
                                        style={{
                                            background: '#f8f9fa',
                                            border: '1px solid #dee2e6',
                                            color: '#495057',
                                            padding: '12px',
                                            cursor: 'grab',
                                            borderRadius: '6px',
                                            transition: 'all 0.2s ease'
                                        }}
                                        onMouseDown={(e) => e.currentTarget.style.cursor = 'grabbing'}
                                        onMouseUp={(e) => e.currentTarget.style.cursor = 'grab'}
                                        title={`Drag to schedule: ${draft.title}`}
                                    >
                                        <div style={{ fontWeight: '600', marginBottom: '4px' }}>
                                            {draft.title || `Draft ${draft.id}`}
                                        </div>
                                        <div style={{ fontSize: '12px', color: '#6c757d' }}>
                                            Created: {new Date(draft.createdAt).toLocaleDateString()}
                                        </div>
                                    </div>
                                ))}
                            </div>
                        )}
                    </div>
                </div>

                {/* Instructions */}
                <div className="aca-card">
                    <div className="aca-card-header">
                        <h3 className="aca-card-title">How to Use</h3>
                    </div>
                    <div className="aca-card-content">
                        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(300px, 1fr))', gap: '20px' }}>
                            <div>
                                <h4 className="aca-label">📅 Scheduling Drafts</h4>
                                <p style={{ margin: 0, fontSize: '14px', color: '#666' }}>
                                    Drag any unscheduled draft from the sidebar and drop it onto a calendar date to schedule it for publication.
                                </p>
                            </div>
                            
                            <div>
                                <h4 className="aca-label">🔄 Rescheduling</h4>
                                <p style={{ margin: 0, fontSize: '14px', color: '#666' }}>
                                    Scheduled drafts (yellow) can be dragged to different dates to reschedule them.
                                </p>
                            </div>
                            
                            <div>
                                <h4 className="aca-label">✏️ Editing Content</h4>
                                <p style={{ margin: 0, fontSize: '14px', color: '#666' }}>
                                    Click on any scheduled draft (yellow) or published post (green) to open it in WordPress editor.
                                </p>
                            </div>
                            
                            <div>
                                <h4 className="aca-label">📊 Smart Display</h4>
                                <p style={{ margin: 0, fontSize: '14px', color: '#666' }}>
                                    Days with many posts show first 3 items. Click "+X More" to expand and see all content.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};
