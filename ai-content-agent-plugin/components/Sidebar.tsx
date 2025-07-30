
import React from 'react';
import type { View } from '../types';
import { Lightbulb, BookOpen, Settings, LayoutDashboard, FileText, Send, Calendar, Sparkles } from './Icons';

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
    badge?: string;
}> = ({ icon, label, view, currentView, onClick, badge }) => {
    const isActive = currentView === view;
    return (
        <button
            onClick={onClick}
            className={`aca-nav-item ${isActive ? 'active' : ''}`}
            style={{
                position: 'relative',
                display: 'flex',
                alignItems: 'center',
                gap: '12px',
                padding: '12px 16px',
                margin: '2px 0',
                borderRadius: '8px',
                border: 'none',
                background: isActive 
                    ? 'linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%)' 
                    : 'transparent',
                color: isActive ? 'white' : '#64748b',
                fontSize: '14px',
                fontWeight: isActive ? '600' : '500',
                cursor: 'pointer',
                transition: 'all 0.2s ease',
                width: '100%',
                textAlign: 'left',
                boxShadow: isActive ? '0 4px 12px rgba(59, 130, 246, 0.3)' : 'none'
            }}
            onMouseEnter={(e) => {
                if (!isActive) {
                    e.currentTarget.style.background = '#f1f5f9';
                    e.currentTarget.style.color = '#1e293b';
                }
            }}
            onMouseLeave={(e) => {
                if (!isActive) {
                    e.currentTarget.style.background = 'transparent';
                    e.currentTarget.style.color = '#64748b';
                }
            }}
        >
            <span style={{ 
                display: 'flex', 
                alignItems: 'center', 
                justifyContent: 'center',
                width: '20px',
                height: '20px'
            }}>
                {icon}
            </span>
            <span style={{ flex: 1 }}>{label}</span>
            {badge && (
                <span style={{
                    background: 'linear-gradient(135deg, #f59e0b, #d97706)',
                    color: 'white',
                    fontSize: '10px',
                    fontWeight: '700',
                    padding: '2px 6px',
                    borderRadius: '4px',
                    textTransform: 'uppercase',
                    letterSpacing: '0.5px'
                }}>
                    {badge}
                </span>
            )}
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
            <div className="aca-sidebar-header" style={{
                background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                color: 'white',
                padding: '20px',
                borderRadius: '0 0 12px 12px',
                marginBottom: '20px',
                position: 'relative',
                overflow: 'hidden'
            }}>
                <div style={{ position: 'relative', zIndex: 2 }}>
                    <div style={{ display: 'flex', alignItems: 'center', gap: '12px', marginBottom: '8px' }}>
                        <div style={{
                            width: '40px',
                            height: '40px',
                            background: 'rgba(255,255,255,0.2)',
                            borderRadius: '10px',
                            display: 'flex',
                            alignItems: 'center',
                            justifyContent: 'center',
                            backdropFilter: 'blur(10px)'
                        }}>
                            <span style={{ fontSize: '20px' }}>🤖</span>
                        </div>
                        <div>
                            <h1 style={{ 
                                fontSize: '18px', 
                                fontWeight: '700', 
                                margin: 0,
                                textShadow: '0 2px 4px rgba(0,0,0,0.1)'
                            }}>
                                AI Content Agent
                            </h1>
                            <div style={{ fontSize: '11px', opacity: 0.8, fontWeight: '500' }}>
                                Powered by Gemini AI
                            </div>
                        </div>
                    </div>
                    <a 
                        href="https://ademisler.com/en" 
                        target="_blank" 
                        rel="noopener noreferrer" 
                        style={{
                            color: 'rgba(255,255,255,0.8)',
                            textDecoration: 'none',
                            fontSize: '12px',
                            display: 'flex',
                            alignItems: 'center',
                            gap: '4px',
                            transition: 'color 0.2s'
                        }}
                        onMouseEnter={(e) => e.currentTarget.style.color = 'white'}
                        onMouseLeave={(e) => e.currentTarget.style.color = 'rgba(255,255,255,0.8)'}
                    >
                        by Adem Isler ↗
                    </a>
                </div>
                {/* Decorative elements */}
                <div style={{
                    position: 'absolute',
                    top: '-20px',
                    right: '-20px',
                    width: '60px',
                    height: '60px',
                    background: 'rgba(255,255,255,0.1)',
                    borderRadius: '50%',
                    zIndex: 1
                }}></div>
            </div>
            
            <nav className="aca-sidebar-nav" style={{ padding: '0 16px' }}>
                {/* Main Navigation */}
                <div style={{ marginBottom: '24px' }}>
                    <div style={{ 
                        fontSize: '11px', 
                        fontWeight: '600', 
                        color: '#94a3b8', 
                        textTransform: 'uppercase', 
                        letterSpacing: '0.5px',
                        marginBottom: '8px',
                        paddingLeft: '16px'
                    }}>
                        Main
                    </div>
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
                </div>

                {/* Content Creation */}
                <div style={{ marginBottom: '24px' }}>
                    <div style={{ 
                        fontSize: '11px', 
                        fontWeight: '600', 
                        color: '#94a3b8', 
                        textTransform: 'uppercase', 
                        letterSpacing: '0.5px',
                        marginBottom: '8px',
                        paddingLeft: '16px'
                    }}>
                        Content Creation
                    </div>
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
                </div>

                {/* Pro Features */}
                <div style={{ marginBottom: '24px' }}>
                    <div style={{ 
                        fontSize: '11px', 
                        fontWeight: '600', 
                        color: '#94a3b8', 
                        textTransform: 'uppercase', 
                        letterSpacing: '0.5px',
                        marginBottom: '8px',
                        paddingLeft: '16px'
                    }}>
                        Pro Features
                    </div>
                    <NavItem 
                        icon={<Sparkles />} 
                        label="Content Freshness" 
                        badge="PRO"
                        view="content-freshness" 
                        currentView={currentView} 
                        onClick={() => handleNavigation('content-freshness')} 
                    />
                </div>
            </nav>
            
            <div style={{ 
                paddingTop: '20px', 
                borderTop: '1px solid #e2e8f0', 
                marginTop: 'auto',
                padding: '20px 16px 0 16px'
            }}>
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
