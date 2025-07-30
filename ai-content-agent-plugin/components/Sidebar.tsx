
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
            className={`aca-nav-item ${isActive ? 'active' : ''}`}
        >
            <span className="aca-nav-item-icon">{icon}</span>
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
        <aside className={`aca-sidebar ${isOpen ? 'open' : ''}`}>
            <div className="aca-sidebar-header">
                <h1 className="aca-sidebar-title">AI Content Agent</h1>
                <a 
                    href="https://ademisler.com/en" 
                    target="_blank" 
                    rel="noopener noreferrer" 
                    className="aca-sidebar-subtitle"
                >
                    by Adem Isler
                </a>
            </div>
            
            <nav className="aca-sidebar-nav">
                <NavItem 
                    icon={<LayoutDashboard />} 
                    label="Dashboard" 
                    view="dashboard" 
                    currentView={currentView} 
                    onClick={() => handleNavigation('dashboard')} 
                />
                <NavItem 
                    icon={<BookOpen />} 
                    label="Style Guide" 
                    view="style-guide" 
                    currentView={currentView} 
                    onClick={() => handleNavigation('style-guide')} 
                />
                <NavItem 
                    icon={<Lightbulb />} 
                    label="Idea Board" 
                    view="ideas" 
                    currentView={currentView} 
                    onClick={() => handleNavigation('ideas')} 
                />
                <NavItem 
                    icon={<FileText />} 
                    label="Drafts" 
                    view="drafts" 
                    currentView={currentView} 
                    onClick={() => handleNavigation('drafts')} 
                />
                <NavItem 
                    icon={<Calendar />} 
                    label="Calendar" 
                    view="calendar" 
                    currentView={currentView} 
                    onClick={() => handleNavigation('calendar')} 
                />
                <NavItem 
                    icon={<Send />} 
                    label="Published" 
                    view="published" 
                    currentView={currentView} 
                    onClick={() => handleNavigation('published')} 
                />
            </nav>
            
            <div style={{ paddingTop: '20px', borderTop: '1px solid #32373c', marginTop: 'auto' }}>
                <NavItem 
                    icon={<Settings />} 
                    label="Settings" 
                    view="settings" 
                    currentView={currentView} 
                    onClick={() => handleNavigation('settings')} 
                />
            </div>
        </aside>
    );
};
