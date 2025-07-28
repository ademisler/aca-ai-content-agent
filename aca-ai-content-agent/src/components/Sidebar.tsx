
import React from 'react';
import type { View } from '../types';
import { Lightbulb, BookOpen, Settings, LayoutDashboard, FileText, Send, Calendar } from './Icons';

interface SidebarProps {
    currentView: View;
    setView: (view: View) => void;
    isOpen: boolean;
    closeSidebar: () => void;
}

const NavItem: React.FC<{
    icon: React.ReactNode;
    label: string;
    view: View;
    currentView: View;
    onClick: () => void;
}> = ({ icon, label, view, currentView, onClick }) => {
    const isActive = currentView === view;
    return (
        <button
            onClick={onClick}
            className={`flex items-center w-full px-3 py-2.5 text-sm font-medium rounded-md transition-colors duration-200 group ${
                isActive
                    ? 'bg-blue-600 text-white'
                    : 'text-slate-400 hover:bg-slate-700 hover:text-slate-100'
            }`}
        >
            <span className={`mr-3 ${isActive ? 'text-white' : 'text-slate-500 group-hover:text-slate-300'}`}>{icon}</span>
            {label}
        </button>
    );
};

export const Sidebar: React.FC<SidebarProps> = ({ currentView, setView, isOpen, closeSidebar }) => {
    
    const handleNavigation = (view: View) => {
        setView(view);
        closeSidebar();
    }

    return (
        <aside className={`fixed sm:relative inset-y-0 left-0 z-20 w-60 bg-slate-800 p-4 flex flex-col justify-between border-r border-slate-700/50 transform transition-transform duration-300 ease-in-out ${isOpen ? 'translate-x-0' : '-translate-x-full'} sm:translate-x-0`}>
            <div>
                <div className="mb-8 px-2">
                    <h1 className="text-xl font-bold text-white tracking-wider">AI Content Agent</h1>
                    <a href="https://ademisler.com" target="_blank" rel="noopener noreferrer" className="text-xs text-slate-400 hover:text-blue-400 transition-colors">by Adem Isler</a>
                </div>
                <nav className="space-y-1.5">
                    <NavItem icon={<LayoutDashboard className="h-5 w-5" />} label="Dashboard" view="dashboard" currentView={currentView} onClick={() => handleNavigation('dashboard')} />
                    <NavItem icon={<BookOpen className="h-5 w-5" />} label="Style Guide" view="style-guide" currentView={currentView} onClick={() => handleNavigation('style-guide')} />
                    <NavItem icon={<Lightbulb className="h-5 w-5" />} label="Idea Board" view="ideas" currentView={currentView} onClick={() => handleNavigation('ideas')} />
                    <NavItem icon={<FileText className="h-5 w-5" />} label="Drafts" view="drafts" currentView={currentView} onClick={() => handleNavigation('drafts')} />
                     <NavItem icon={<Calendar className="h-5 w-5" />} label="Calendar" view="calendar" currentView={currentView} onClick={() => handleNavigation('calendar')} />
                    <NavItem icon={<Send className="h-5 w-5" />} label="Published" view="published" currentView={currentView} onClick={() => handleNavigation('published')} />
                </nav>
            </div>
            <div className="pt-4 border-t border-slate-700/50">
                 <NavItem icon={<Settings className="h-5 w-5" />} label="Settings" view="settings" currentView={currentView} onClick={() => handleNavigation('settings')} />
            </div>
        </aside>
    );
};
