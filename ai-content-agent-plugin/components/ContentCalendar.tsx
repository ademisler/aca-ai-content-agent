
import React, { useState } from 'react';
import type { Draft } from '../types';
import { ChevronLeft, ChevronRight, FileText, Send, Calendar as CalendarIcon, Info } from './Icons';

interface ContentCalendarProps {
    drafts: Draft[];
    publishedPosts: Draft[];
    onScheduleDraft: (id: number, date: string) => void;
    onSelectPost: (post: Draft) => void;
}

const DraggableDraft: React.FC<{ draft: Draft }> = ({ draft }) => {
    const handleDragStart = (e: React.DragEvent<HTMLDivElement>) => {
        e.dataTransfer.setData('application/json', JSON.stringify({ draftId: draft.id, type: 'draft' }));
        e.dataTransfer.effectAllowed = 'move';
    };

    return (
        <div
            draggable
            onDragStart={handleDragStart}
            className="aca-action-button"
            style={{
                background: '#e3f2fd',
                border: '1px solid #2196f3',
                color: '#0d47a1',
                padding: '8px 10px',
                fontSize: '12px',
                overflow: 'hidden',
                textOverflow: 'ellipsis',
                whiteSpace: 'nowrap' as const,
                cursor: 'grab',
                display: 'flex',
                alignItems: 'center',
                gap: '6px',
                fontWeight: '500',
                margin: 0
            }}
            onMouseDown={(e) => {
                e.currentTarget.style.cursor = 'grabbing';
            }}
            onMouseUp={(e) => {
                e.currentTarget.style.cursor = 'grab';
            }}
        >
            <FileText style={{ width: '12px', height: '12px', flexShrink: 0 }} />
            <span style={{ flexGrow: 1, overflow: 'hidden', textOverflow: 'ellipsis' }}>{draft.title}</span>
        </div>
    );
};

export const ContentCalendar: React.FC<ContentCalendarProps> = ({ drafts, publishedPosts, onScheduleDraft, onSelectPost }) => {
    const [currentDate, setCurrentDate] = useState(new Date());
    const [dragOverDate, setDragOverDate] = useState<string | null>(null);

    const unscheduledDrafts = drafts.filter(d => !d.scheduledFor);

    const handleDrop = (e: React.DragEvent<HTMLDivElement>, date: Date) => {
        e.preventDefault();
        const data = e.dataTransfer.getData('application/json');
        if (data) {
            const { draftId } = JSON.parse(data);
            if (draftId) {
                onScheduleDraft(draftId, date.toISOString());
            }
        }
        setDragOverDate(null);
    };
    
    const handleDragOver = (e: React.DragEvent<HTMLDivElement>) => {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
    };

    const handleDragEnter = (e: React.DragEvent<HTMLDivElement>, date: Date) => {
        e.preventDefault();
        setDragOverDate(date.toISOString());
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
                    Schedule your drafts by dragging them onto calendar dates and view your published content timeline.
                </p>
            </div>

            <div style={{ display: 'flex', flexDirection: 'column', gap: '20px' }}>
                {/* Calendar */}
                <div className="aca-card">
                    <div className="aca-card-header">
                        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                            <h2 className="aca-card-title">
                                <CalendarIcon className="aca-nav-item-icon" />
                                {currentDate.toLocaleString('default', { month: 'long', year: 'numeric' })}
                            </h2>
                            <div style={{ display: 'flex', alignItems: 'center', gap: '8px' }}>
                                <button 
                                    onClick={prevMonth} 
                                    className="aca-button secondary"
                                    style={{ padding: '8px', minWidth: 'auto' }}
                                    title="Previous month"
                                >
                                    <ChevronLeft style={{ width: '16px', height: '16px' }} />
                                </button>
                                <button 
                                    onClick={nextMonth} 
                                    className="aca-button secondary"
                                    style={{ padding: '8px', minWidth: 'auto' }}
                                    title="Next month"
                                >
                                    <ChevronRight style={{ width: '16px', height: '16px' }} />
                                </button>
                            </div>
                        </div>
                    </div>

                    <div style={{ 
                        display: 'grid', 
                        gridTemplateColumns: 'repeat(7, 1fr)', 
                        border: '1px solid #ccd0d4',
                        borderRadius: '4px',
                        overflow: 'hidden',
                        background: '#ffffff'
                    }}>
                        {/* Day Headers */}
                        {['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'].map((day, index) => (
                            <div 
                                key={day} 
                                className="aca-card-header"
                                style={{ 
                                    textAlign: 'center' as const, 
                                    fontWeight: '600', 
                                    fontSize: '13px', 
                                    padding: '12px 8px', 
                                    borderBottom: '1px solid #ccd0d4',
                                    borderRight: index < 6 ? '1px solid #ccd0d4' : 'none',
                                    color: '#646970', 
                                    background: '#f6f7f7',
                                    margin: 0
                                }}
                            >
                                {day}
                            </div>
                        ))}
                        
                        {/* Calendar Days */}
                        {daysInMonth.map((day, index) => {
                            const isToday = day && day.toDateString() === new Date().toDateString();
                            const scheduledDrafts = day ? drafts.filter(d => d.scheduledFor && new Date(d.scheduledFor).toDateString() === day.toDateString()) : [];
                            const postsToday = day ? publishedPosts.filter(p => p.publishedAt && new Date(p.publishedAt).toDateString() === day.toDateString()) : [];

                            return (
                                <div
                                    key={index}
                                    onDrop={(e) => day && handleDrop(e, day)}
                                    onDragOver={handleDragOver}
                                    onDragEnter={(e) => day && handleDragEnter(e, day)}
                                    onDragLeave={handleDragLeave}
                                    style={{
                                        position: 'relative',
                                        padding: '8px',
                                        minHeight: '120px',
                                        borderBottom: Math.floor(index / 7) < Math.floor((daysInMonth.length - 1) / 7) ? '1px solid #ccd0d4' : 'none',
                                        borderRight: (index % 7) < 6 ? '1px solid #ccd0d4' : 'none',
                                        display: 'flex',
                                        flexDirection: 'column',
                                        gap: '4px',
                                        overflowY: 'auto',
                                        background: day ? (dragOverDate === day.toISOString() ? '#f0f6fc' : '#ffffff') : '#f9f9f9'
                                    }}
                                >
                                    {day && (
                                        <>
                                            {/* Day Number */}
                                            <div style={{
                                                position: 'absolute',
                                                top: '6px',
                                                right: '6px',
                                                fontSize: '12px',
                                                fontWeight: '600',
                                                background: isToday ? '#0073aa' : 'transparent',
                                                color: isToday ? '#ffffff' : '#646970',
                                                borderRadius: isToday ? '50%' : '0',
                                                width: isToday ? '22px' : 'auto',
                                                height: isToday ? '22px' : 'auto',
                                                display: 'flex',
                                                alignItems: 'center',
                                                justifyContent: 'center',
                                                zIndex: 1
                                            }}>
                                                {day.getDate()}
                                            </div>
                                            
                                            {/* Content */}
                                            <div style={{ 
                                                display: 'flex', 
                                                flexDirection: 'column', 
                                                gap: '4px', 
                                                marginTop: '28px',
                                                flex: 1
                                            }}>
                                                {scheduledDrafts.map(draft => (
                                                    <div 
                                                        key={draft.id} 
                                                        onClick={() => onSelectPost(draft)} 
                                                        className="aca-action-button"
                                                        style={{
                                                            background: '#fff3cd',
                                                            border: '1px solid #ffc107',
                                                            color: '#856404',
                                                            padding: '8px 10px',
                                                            fontSize: '12px',
                                                            overflow: 'hidden',
                                                            textOverflow: 'ellipsis',
                                                            whiteSpace: 'nowrap' as const,
                                                            cursor: 'pointer',
                                                            display: 'flex',
                                                            alignItems: 'center',
                                                            gap: '6px',
                                                            fontWeight: '500',
                                                            margin: 0
                                                        }}
                                                        title={`Scheduled: ${draft.title}`}
                                                    >
                                                        <FileText style={{ width: '12px', height: '12px', flexShrink: 0 }} />
                                                        <span style={{ flexGrow: 1, overflow: 'hidden', textOverflow: 'ellipsis' }}>
                                                            {draft.title}
                                                        </span>
                                                    </div>
                                                ))}
                                                {postsToday.map(post => (
                                                    <div 
                                                        key={post.id} 
                                                        onClick={() => onSelectPost(post)} 
                                                        className="aca-action-button"
                                                        style={{
                                                            background: '#e6f7e6',
                                                            border: '1px solid #28a745',
                                                            color: '#0a5d0a',
                                                            padding: '8px 10px',
                                                            fontSize: '12px',
                                                            overflow: 'hidden',
                                                            textOverflow: 'ellipsis',
                                                            whiteSpace: 'nowrap' as const,
                                                            cursor: 'pointer',
                                                            display: 'flex',
                                                            alignItems: 'center',
                                                            gap: '6px',
                                                            fontWeight: '500',
                                                            margin: 0
                                                        }}
                                                        title={`Published: ${post.title}`}
                                                    >
                                                        <Send style={{ width: '12px', height: '12px', flexShrink: 0 }} />
                                                        <span style={{ flexGrow: 1, overflow: 'hidden', textOverflow: 'ellipsis' }}>
                                                            {post.title}
                                                        </span>
                                                    </div>
                                                ))}
                                            </div>
                                        </>
                                    )}
                                </div>
                            );
                        })}
                    </div>
                </div>

                {/* Side panels */}
                <div className="aca-grid aca-grid-2">
                    {/* Unscheduled Drafts */}
                    <div className="aca-card">
                        <div className="aca-card-header">
                            <h2 className="aca-card-title">
                                <FileText className="aca-nav-item-icon" />
                                Unscheduled Drafts
                            </h2>
                        </div>
                        
                        <div className="aca-card" style={{ 
                            background: '#f6f7f7', 
                            border: '1px solid #ccd0d4', 
                            minHeight: '200px',
                            maxHeight: '300px',
                            overflowY: 'auto',
                            margin: 0
                        }}>
                            {unscheduledDrafts.length > 0 ? (
                                <div style={{ display: 'flex', flexDirection: 'column', gap: '8px' }}>
                                    {unscheduledDrafts.map(draft => (
                                        <DraggableDraft key={draft.id} draft={draft} />
                                    ))}
                                </div>
                            ) : (
                                <div style={{ 
                                    display: 'flex', 
                                    flexDirection: 'column', 
                                    alignItems: 'center', 
                                    justifyContent: 'center', 
                                    height: '100%', 
                                    color: '#646970', 
                                    textAlign: 'center' as const,
                                    padding: '20px'
                                }}>
                                    <FileText style={{ width: '40px', height: '40px', marginBottom: '15px', fill: '#a7aaad' }} />
                                    <h4 className="aca-card-title">No Unscheduled Drafts</h4>
                                    <p className="aca-page-description" style={{ margin: 0 }}>
                                        All your drafts are scheduled or you haven't created any yet.
                                    </p>
                                </div>
                            )}
                        </div>
                    </div>

                    {/* Instructions */}
                    <div className="aca-card">
                        <div className="aca-card-header">
                            <h2 className="aca-card-title">
                                <Info className="aca-nav-item-icon" />
                                How to Use
                            </h2>
                        </div>
                        
                        <div className="aca-page-description">
                            <div style={{ marginBottom: '20px' }}>
                                <h4 className="aca-label">Scheduling Drafts</h4>
                                <p style={{ margin: 0 }}>
                                    Drag any unscheduled draft from the sidebar and drop it onto a calendar date to schedule it for publication on that day.
                                </p>
                            </div>
                            
                            <div style={{ marginBottom: '20px' }}>
                                <h4 className="aca-label">Scheduled Drafts</h4>
                                <p style={{ margin: 0 }}>
                                    Yellow items show scheduled drafts. Click on them to view and edit the content. Scheduled posts will be automatically published by WordPress.
                                </p>
                            </div>
                            
                            <div style={{ marginBottom: '20px' }}>
                                <h4 className="aca-label">Published Posts</h4>
                                <p style={{ margin: 0 }}>
                                    Green items show published posts. Click on them to view the full content and manage them.
                                </p>
                            </div>
                            
                            <div>
                                <h4 className="aca-label">Navigation</h4>
                                <p style={{ margin: 0 }}>
                                    Use the arrow buttons to navigate between months and see your complete content timeline.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};
