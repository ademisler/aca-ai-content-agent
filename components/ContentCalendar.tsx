
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
            className="bg-sky-900/50 border border-sky-700/60 text-sky-200 p-1.5 rounded-md text-xs truncate cursor-grab active:cursor-grabbing flex items-center gap-2"
        >
            <FileText className="h-3 w-3 flex-shrink-0" />
            <span className="flex-grow truncate">{draft.title}</span>
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
        <div className="flex flex-col lg:flex-row gap-8 h-full">
            <div className="flex-grow flex flex-col">
                <header className="flex justify-between items-center mb-6">
                    <h2 className="text-3xl font-bold text-white">{currentDate.toLocaleString('default', { month: 'long', year: 'numeric' })}</h2>
                    <div className="flex items-center gap-2">
                        <button onClick={prevMonth} className="p-2 rounded-md bg-slate-700 hover:bg-slate-600"><ChevronLeft className="h-5 w-5"/></button>
                        <button onClick={nextMonth} className="p-2 rounded-md bg-slate-700 hover:bg-slate-600"><ChevronRight className="h-5 w-5"/></button>
                    </div>
                </header>

                <div className="grid grid-cols-7 flex-grow border-t border-l border-slate-700">
                    {['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'].map(day => (
                        <div key={day} className="text-center font-bold text-sm py-2 border-b border-r border-slate-700 text-slate-400 bg-slate-800/50">{day}</div>
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
                                className={`relative p-2 min-h-[8rem] border-b border-r border-slate-700 flex flex-col gap-1 overflow-y-auto transition-colors duration-200 ${day && dragOverDate === day.toISOString() ? 'bg-slate-700/50' : ''}`}
                            >
                                {day && (
                                    <span className={`absolute top-1.5 right-1.5 text-xs font-semibold ${isToday ? 'bg-blue-600 text-white rounded-full h-6 w-6 flex items-center justify-center' : 'text-slate-500'}`}>
                                        {day.getDate()}
                                    </span>
                                )}
                                <div className="space-y-1.5 mt-6">
                                    {scheduledDrafts.map(draft => <DraggableDraft key={draft.id} draft={draft} />)}
                                    {postsToday.map(post => (
                                         <div key={post.id} onClick={() => onSelectPost(post)} className="bg-green-900/50 border border-green-700/60 text-green-200 p-1.5 rounded-md text-xs truncate cursor-pointer flex items-center gap-2">
                                             <Send className="h-3 w-3 flex-shrink-0" />
                                             <span className="flex-grow truncate">{post.title}</span>
                                         </div>
                                    ))}
                                </div>
                            </div>
                        );
                    })}
                </div>
            </div>

            <aside className="lg:w-72 flex-shrink-0">
                <h3 className="text-xl font-bold text-white mb-4">Unscheduled Drafts</h3>
                <div className="bg-slate-800 p-4 rounded-lg border border-slate-700/80 h-96 overflow-y-auto">
                    {unscheduledDrafts.length > 0 ? (
                        <div className="space-y-2">
                            {unscheduledDrafts.map(draft => <DraggableDraft key={draft.id} draft={draft} />)}
                        </div>
                    ) : (
                        <div className="flex flex-col items-center justify-center h-full text-slate-500 text-center">
                            <FileText className="h-10 w-10 mb-2" />
                            <p className="text-sm">No unscheduled drafts.</p>
                        </div>
                    )}
                </div>
                 <div className="mt-6 text-sm text-slate-400 p-4 bg-slate-900/50 rounded-lg border border-slate-700">
                    <p className="font-bold text-slate-300 mb-1">How to use:</p>
                    <p>Drag and drop an unscheduled draft onto a date in the calendar to schedule it.</p>
                </div>
            </aside>
        </div>
    );
};
