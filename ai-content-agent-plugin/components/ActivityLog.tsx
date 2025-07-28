
import React from 'react';
import type { ActivityLog, IconName } from '../types';
import * as Icons from './Icons';

interface ActivityLogListProps {
    logs: ActivityLog[];
}

const IconMap: { [key in IconName]: React.FC<{ className?: string; style?: React.CSSProperties }> } = {
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
    Archive: Icons.Archive,
    Edit: Icons.Edit,
};

const ActivityLogItem: React.FC<{ log: ActivityLog }> = ({ log }) => {
    const IconComponent = IconMap[log.icon] || Icons.Info;
    const timestamp = new Date(log.timestamp);
    const timeString = timestamp.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

    return (
        <div className="aca-list-item" style={{ padding: '12px 0', borderBottom: '1px solid #f0f0f1' }}>
            <div style={{ display: 'flex', alignItems: 'flex-start', gap: '12px' }}>
                <div style={{ 
                    width: '32px', 
                    height: '32px', 
                    backgroundColor: '#f6f7f7',
                    borderRadius: '50%',
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                    flexShrink: 0
                }}>
                    <IconComponent style={{ width: '16px', height: '16px', fill: '#0073aa' }} />
                </div>
                <div style={{ flex: 1, minWidth: 0 }}>
                    <p style={{ margin: '0 0 4px 0', fontSize: '13px', color: '#23282d', lineHeight: '1.4' }}>
                        {log.details}
                    </p>
                    <p style={{ margin: 0, fontSize: '12px', color: '#646970' }}>
                        {timeString}
                    </p>
                </div>
            </div>
        </div>
    );
};

export const ActivityLogList: React.FC<ActivityLogListProps> = ({ logs }) => {
    if (logs.length === 0) {
        return (
            <div style={{ 
                textAlign: 'center', 
                padding: '40px 20px', 
                color: '#646970',
                fontSize: '13px'
            }}>
                <Icons.Info style={{ 
                    width: '24px', 
                    height: '24px', 
                    margin: '0 auto 10px auto', 
                    display: 'block',
                    fill: '#a7aaad'
                }} />
                No activity yet. Start by creating your style guide or generating ideas!
            </div>
        );
    }

    // Group logs by date
    const today = new Date();
    const yesterday = new Date();
    yesterday.setDate(yesterday.getDate() - 1);

    const todayStr = today.toDateString();
    const yesterdayStr = yesterday.toDateString();

    const groupedLogs = logs.reduce((acc, log) => {
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

    return (
        <div style={{ maxHeight: '400px', overflowY: 'auto' }}>
            {Object.entries(groupedLogs).map(([dateKey, dateLogs]) => (
                <div key={dateKey} style={{ marginBottom: '20px' }}>
                    <h4 style={{ 
                        margin: '0 0 10px 0',
                        fontSize: '12px',
                        fontWeight: '600',
                        color: '#646970',
                        textTransform: 'uppercase',
                        letterSpacing: '0.5px'
                    }}>
                        {dateKey}
                    </h4>
                    <div>
                        {dateLogs.map((log) => (
                            <ActivityLogItem key={log.id} log={log} />
                        ))}
                    </div>
                </div>
            ))}
        </div>
    );
};
