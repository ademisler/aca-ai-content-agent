
import React, { useState } from 'react';
import type { Draft } from '../types';
import { ChevronLeft, ChevronRight, FileText, Send } from './Icons';

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
            style={{
                background: '#e3f2fd',
                border: '1px solid #2196f3',
                color: '#0d47a1',
                padding: '6px',
                borderRadius: '4px',
                fontSize: '11px',
                overflow: 'hidden',
                textOverflow: 'ellipsis',
                whiteSpace: 'nowrap',
                cursor: 'grab',
                display: 'flex',
                alignItems: 'center',
                gap: '6px'
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
                <p className="aca-page-description">Schedule your drafts and view published content on the calendar.</p>
            </div>

            <div style={{ display: 'flex', flexDirection: 'column', gap: '20px' }}>
                <div className="aca-card" style={{ flexGrow: 1, display: 'flex', flexDirection: 'column' }}>
                    <div className="aca-card-header" style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                        <h2 className="aca-card-title">{currentDate.toLocaleString('default', { month: 'long', year: 'numeric' })}</h2>
                        <div style={{ display: 'flex', alignItems: 'center', gap: '8px' }}>
                            <button 
                                onClick={prevMonth} 
                                className="aca-button secondary"
                                style={{ padding: '8px', display: 'flex', alignItems: 'center' }}
                            >
                                <ChevronLeft style={{ width: '16px', height: '16px' }} />
                            </button>
                            <button 
                                onClick={nextMonth} 
                                className="aca-button secondary"
                                style={{ padding: '8px', display: 'flex', alignItems: 'center' }}
                            >
                                <ChevronRight style={{ width: '16px', height: '16px' }} />
                            </button>
                        </div>
                    </div>

                    <div style={{ 
                        display: 'grid', 
                        gridTemplateColumns: 'repeat(7, 1fr)', 
                        flexGrow: 1, 
                        border: '1px solid #ccd0d4',
                        borderRadius: '4px',
                        overflow: 'hidden'
                    }}>
                        {['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'].map((day, index) => (
                            <div 
                                key={day} 
                                style={{ 
                                    textAlign: 'center', 
                                    fontWeight: '600', 
                                    fontSize: '13px', 
                                    padding: '10px', 
                                    borderBottom: '1px solid #ccd0d4',
                                    borderRight: index < 6 ? '1px solid #ccd0d4' : 'none',
                                    color: '#646970', 
                                    background: '#f6f7f7' 
                                }}
                            >
                                {day}
                            </div>
                        ))}
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
                                        transition: 'background-color 0.2s ease',
                                        background: day && dragOverDate === day.toISOString() ? '#f0f6fc' : '#ffffff'
                                    }}
                                >
                                    {day && (
                                        <span style={{
                                            position: 'absolute',
                                            top: '6px',
                                            right: '6px',
                                            fontSize: '11px',
                                            fontWeight: '600',
                                            background: isToday ? '#0073aa' : 'transparent',
                                            color: isToday ? '#ffffff' : '#646970',
                                            borderRadius: isToday ? '50%' : '0',
                                            width: isToday ? '20px' : 'auto',
                                            height: isToday ? '20px' : 'auto',
                                            display: 'flex',
                                            alignItems: 'center',
                                            justifyContent: 'center'
                                        }}>
                                            {day.getDate()}
                                        </span>
                                    )}
                                    <div style={{ display: 'flex', flexDirection: 'column', gap: '4px', marginTop: '24px' }}>
                                        {scheduledDrafts.map(draft => <DraggableDraft key={draft.id} draft={draft} />)}
                                        {postsToday.map(post => (
                                            <div 
                                                key={post.id} 
                                                onClick={() => onSelectPost(post)} 
                                                style={{
                                                    background: '#e6f7e6',
                                                    border: '1px solid #28a745',
                                                    color: '#0a5d0a',
                                                    padding: '6px',
                                                    borderRadius: '4px',
                                                    fontSize: '11px',
                                                    overflow: 'hidden',
                                                    textOverflow: 'ellipsis',
                                                    whiteSpace: 'nowrap',
                                                    cursor: 'pointer',
                                                    display: 'flex',
                                                    alignItems: 'center',
                                                    gap: '6px',
                                                    transition: 'background 0.2s ease'
                                                }}
                                                onMouseEnter={(e) => {
                                                    e.currentTarget.style.background = '#d4edda';
                                                }}
                                                onMouseLeave={(e) => {
                                                    e.currentTarget.style.background = '#e6f7e6';
                                                }}
                                            >
                                                <Send style={{ width: '12px', height: '12px', flexShrink: 0 }} />
                                                <span style={{ flexGrow: 1, overflow: 'hidden', textOverflow: 'ellipsis' }}>{post.title}</span>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                </div>

                <div className="aca-card" style={{ maxWidth: '350px' }}>
                    <div className="aca-card-header">
                        <h2 className="aca-card-title">Unscheduled Drafts</h2>
                    </div>
                    <div style={{ 
                        background: '#f6f7f7', 
                        padding: '15px', 
                        borderRadius: '4px', 
                        border: '1px solid #ccd0d4', 
                        height: '300px', 
                        overflowY: 'auto' 
                    }}>
                        {unscheduledDrafts.length > 0 ? (
                            <div style={{ display: 'flex', flexDirection: 'column', gap: '8px' }}>
                                {unscheduledDrafts.map(draft => <DraggableDraft key={draft.id} draft={draft} />)}
                            </div>
                        ) : (
                            <div style={{ 
                                display: 'flex', 
                                flexDirection: 'column', 
                                alignItems: 'center', 
                                justifyContent: 'center', 
                                height: '100%', 
                                color: '#646970', 
                                textAlign: 'center' 
                            }}>
                                <FileText style={{ width: '40px', height: '40px', marginBottom: '10px', fill: '#a7aaad' }} />
                                <p style={{ fontSize: '13px', margin: 0 }}>No unscheduled drafts.</p>
                            </div>
                        )}
                    </div>
                    <div style={{ 
                        marginTop: '15px', 
                        fontSize: '13px', 
                        color: '#646970', 
                        padding: '15px', 
                        background: '#f0f6fc', 
                        borderRadius: '4px', 
                        border: '1px solid #ccd0d4' 
                    }}>
                        <p style={{ fontWeight: '600', color: '#23282d', margin: '0 0 5px 0' }}>How to use:</p>
                        <p style={{ margin: 0 }}>Drag and drop an unscheduled draft onto a date in the calendar to schedule it.</p>
                    </div>
                </div>
            </div>
        </div>
    );
};
