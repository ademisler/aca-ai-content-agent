
import React, { useState } from 'react';
import { ChevronLeft, ChevronRight, Calendar as CalendarIcon, Clock, Edit, Eye } from './Icons';
import { Draft } from '../types';

interface ContentCalendarProps {
    drafts: Draft[];
    publishedPosts: Draft[];
    onScheduleDraft: (draftId: number, scheduledDate: string) => void;
    onSelectPost: (post: Draft) => void;
}

export const ContentCalendar: React.FC<ContentCalendarProps> = ({ drafts, publishedPosts, onScheduleDraft, onSelectPost }) => {
    const [currentDate, setCurrentDate] = useState(new Date());
    const [dragOverDate, setDragOverDate] = useState<Date | null>(null);
    const [draggedDraft, setDraggedDraft] = useState<Draft | null>(null);

    // Separate drafts into scheduled and unscheduled
    const unscheduledDrafts = drafts.filter(draft => !draft.scheduledFor);
    const scheduledDrafts = drafts.filter(draft => draft.scheduledFor);

    // Function to open WordPress edit page
    const openWordPressEditor = (postId: number) => {
        const adminUrl = (window as any).aca_object?.admin_url || '/wp-admin/';
        window.open(`${adminUrl}post.php?post=${postId}&action=edit`, '_blank');
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
        const draftId = e.dataTransfer.getData('text/plain');
        if (draftId && draggedDraft) {
            onScheduleDraft(parseInt(draftId), date.toISOString());
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

    return (
        <div className="aca-fade-in">
            <div className="aca-page-header">
                <h1 className="aca-page-title">Content Calendar</h1>
                <p className="aca-page-description">
                    Schedule your drafts by dragging them onto calendar dates. View scheduled and published content timeline.
                </p>
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
                                const isToday = date.toDateString() === new Date().toDateString();
                                const isDragOver = dragOverDate && date.getTime() === dragOverDate.getTime();
                                
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
                                            overflow: 'hidden'
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
                                        
                                        {/* Post count indicator for days with many posts */}
                                        {(scheduled.length + published.length) > 3 && (
                                            <div style={{
                                                position: 'absolute',
                                                top: '8px',
                                                right: '8px',
                                                background: '#ff9800',
                                                color: 'white',
                                                borderRadius: '50%',
                                                width: '20px',
                                                height: '20px',
                                                display: 'flex',
                                                alignItems: 'center',
                                                justifyContent: 'center',
                                                fontSize: '10px',
                                                fontWeight: 'bold',
                                                zIndex: 1
                                            }}>
                                                {scheduled.length + published.length}
                                            </div>
                                        )}
                                        
                                        {/* Content - Scrollable for many items */}
                                        <div style={{ 
                                            display: 'flex', 
                                            flexDirection: 'column', 
                                            gap: '3px', 
                                            marginTop: '28px',
                                            flex: 1,
                                            maxHeight: '85px',
                                            overflowY: 'auto',
                                            overflowX: 'hidden',
                                            paddingRight: '2px'
                                        }}>
                                            {/* Scheduled Drafts */}
                                            {scheduled.map((draft, index) => (
                                                <div 
                                                    key={`scheduled-${draft.id}`}
                                                    draggable
                                                    onDragStart={(e) => handleDragStart(e, draft)}
                                                    onClick={() => openWordPressEditor(draft.id)}
                                                    className="aca-action-button"
                                                    style={{
                                                        background: '#fff3cd',
                                                        border: '1px solid #ffc107',
                                                        color: '#856404',
                                                        padding: '4px 6px',
                                                        fontSize: '10px',
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
                                                        minHeight: '22px',
                                                        opacity: index > 2 ? 0.8 : 1
                                                    }}
                                                    title={`Scheduled: ${draft.title || `Draft ${draft.id}`} (Click to edit, drag to reschedule)`}
                                                >
                                                    <Clock style={{ width: '10px', height: '10px', flexShrink: 0 }} />
                                                    <span style={{ flex: 1, overflow: 'hidden', textOverflow: 'ellipsis' }}>
                                                        {draft.title || `Draft ${draft.id}`}
                                                    </span>
                                                    <Edit style={{ width: '8px', height: '8px', flexShrink: 0 }} />
                                                </div>
                                            ))}
                                            
                                            {/* Published Posts */}
                                            {published.map((post, index) => (
                                                <div 
                                                    key={`published-${post.id}`}
                                                    onClick={() => openWordPressEditor(post.id)}
                                                    className="aca-action-button"
                                                    style={{
                                                        background: '#d4edda',
                                                        border: '1px solid #28a745',
                                                        color: '#155724',
                                                        padding: '4px 6px',
                                                        fontSize: '10px',
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
                                                        minHeight: '22px',
                                                        opacity: index > 2 ? 0.8 : 1
                                                    }}
                                                    title={`Published: ${post.title || `Post ${post.id}`} (Click to edit)`}
                                                >
                                                    <Eye style={{ width: '10px', height: '10px', flexShrink: 0 }} />
                                                    <span style={{ flex: 1, overflow: 'hidden', textOverflow: 'ellipsis' }}>
                                                        {post.title || `Post ${post.id}`}
                                                    </span>
                                                    <Edit style={{ width: '8px', height: '8px', flexShrink: 0 }} />
                                                </div>
                                            ))}
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
                                <h4 className="aca-label">📊 Visual Indicators</h4>
                                <p style={{ margin: 0, fontSize: '14px', color: '#666' }}>
                                    Yellow = Scheduled drafts, Green = Published posts, Blue border = Today's date.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};
