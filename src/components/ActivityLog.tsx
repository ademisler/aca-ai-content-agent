
import React from 'react';
import type { ActivityLog, IconName } from '../types';
import * as Icons from './Icons';

interface ActivityLogListProps {
    logs: ActivityLog[];
}

const IconMap: { [key in IconName]: React.FC<{ className?: string }> } = {
    BookOpen: Icons.BookOpen,
    Lightbulb: Icons.Lightbulb,
    FileText: Icons.FileText,
    Send: Icons.Send,
    Settings: Icons.Settings,
    Trash: Icons.Trash,
    Pencil: Icons.Pencil,
    Calendar: Icons.Calendar,
    Sparkles: Icons.Sparkles,
    PlusCircle: Icons.PlusCircle,
};

const groupLogsByDate = (logs: ActivityLog[]): { [key: string]: ActivityLog[] } => {
    const today = new Date();
    const yesterday = new Date();
    yesterday.setDate(yesterday.getDate() - 1);

    const todayStr = today.toDateString();
    const yesterdayStr = yesterday.toDateString();

    return logs.reduce((acc, log) => {
        const logDate = new Date(log.timestamp);
        let key: string;

        if (logDate.toDateString() === todayStr) {
            key = 'Today';
        } else if (logDate.toDateString() === yesterdayStr) {
            key = 'Yesterday';
        } else {
            key = logDate.toLocaleDateString(undefined, {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
            });
        }

        if (!acc[key]) {
            acc[key] = [];
        }
        acc[key].push(log);
        return acc;
    }, {} as { [key: string]: ActivityLog[] });
};

export const ActivityLogList: React.FC<ActivityLogListProps> = ({ logs }) => {
    if (logs.length === 0) {
        return (
            <div className="flex flex-col items-center justify-center h-full text-slate-500">
                <Icons.FileText className="h-10 w-10 mb-2" />
                <p>No activity yet.</p>
                <p className="text-sm">Start working to see your logs here.</p>
            </div>
        );
    }
    
    const groupedLogs = groupLogsByDate(logs);

    return (
        <div className="space-y-6">
            {Object.entries(groupedLogs).map(([date, logsForDate]) => (
                <div key={date}>
                    <h4 className="text-sm font-semibold text-slate-400 mb-3 sticky top-0 bg-slate-800 py-1">
                        {date}
                    </h4>
                    <ul className="space-y-4">
                        {logsForDate.map(log => {
                            const IconComponent = IconMap[log.icon];
                            return (
                                <li key={log.id} className="flex items-start space-x-3">
                                    <div className="bg-slate-700/80 rounded-full p-1.5 mt-0.5">
                                        {IconComponent && <IconComponent className="h-4 w-4 text-slate-300" />}
                                    </div>
                                    <div className="flex-1">
                                        <p className="text-sm text-slate-300 leading-snug">{log.details}</p>
                                        <p className="text-xs text-slate-500 mt-0.5">
                                            {new Date(log.timestamp).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true })}
                                        </p>
                                    </div>
                                </li>
                            );
                        })}
                    </ul>
                </div>
            ))}
        </div>
    );
};
