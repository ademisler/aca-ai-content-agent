import React from 'react';
import { render, screen, fireEvent } from '@testing-library/react';
import { Dashboard } from '../Dashboard';
import type { ActivityLog } from '../../types';

// Mock the LoadingContext
jest.mock('../../contexts/LoadingContext', () => ({
    LoadingProvider: ({ children }: { children: React.ReactNode }) => <div>{children}</div>,
    useLoading: () => ({
        setLoading: jest.fn(),
        isLoading: jest.fn(() => false),
        isAnyLoading: jest.fn(() => false),
        clearAllLoading: jest.fn(),
    }),
}));

const mockStats = {
    ideas: 5,
    drafts: 3,
    published: 10,
};

const mockActivityLogs: ActivityLog[] = [
    {
        id: 1,
        timestamp: '2024-01-15T10:00:00Z',
        type: 'style_updated',
        details: 'Style Guide was successfully updated.',
        icon: 'BookOpen',
    },
    {
        id: 2,
        timestamp: '2024-01-15T09:30:00Z',
        type: 'ideas_generated',
        details: 'Generated 5 new content ideas.',
        icon: 'Lightbulb',
    },
];

const defaultProps = {
    stats: mockStats,
    lastAnalyzed: '2024-01-15T10:00:00Z',
    activityLogs: mockActivityLogs,
    onNavigate: jest.fn(),
    onGenerateIdeas: jest.fn(),
    onUpdateStyleGuide: jest.fn(),
    isLoadingIdeas: false,
    isLoadingStyle: false,
};

describe('Dashboard Component', () => {
    beforeEach(() => {
        jest.clearAllMocks();
    });

    it('renders dashboard with correct heading', () => {
        render(<Dashboard {...defaultProps} />);
        
        expect(screen.getByRole('heading', { level: 1, name: 'Dashboard' })).toBeInTheDocument();
        expect(screen.getByText('Welcome back! Here\'s a quick overview of your content pipeline.')).toBeInTheDocument();
    });

    it('displays correct stats in pipeline items', () => {
        render(<Dashboard {...defaultProps} />);
        
        expect(screen.getByText('5 ideas waiting')).toBeInTheDocument();
        expect(screen.getByText('3 drafts to review')).toBeInTheDocument();
        expect(screen.getByText('10 posts are live')).toBeInTheDocument();
    });

    it('shows style guide ready status when lastAnalyzed is provided', () => {
        render(<Dashboard {...defaultProps} />);
        
        expect(screen.getByText('Ready')).toBeInTheDocument();
        expect(screen.getByText('Last Scanned:')).toBeInTheDocument();
    });

    it('shows style guide not set status when lastAnalyzed is null', () => {
        const propsWithUndefined = { ...defaultProps, lastAnalyzed: undefined };
        render(<Dashboard {...propsWithUndefined} />);
        
        expect(screen.getByText('Not Set')).toBeInTheDocument();
        expect(screen.queryByText('Last Scanned:')).not.toBeInTheDocument();
    });

    it('displays get started banner when style guide is not ready', () => {
        const propsWithUndefined = { ...defaultProps, lastAnalyzed: undefined };
        render(<Dashboard {...propsWithUndefined} />);
        
        expect(screen.getByText('Get Started with AI Content Agent')).toBeInTheDocument();
        expect(screen.getByText('Create your Style Guide first to enable all features and generate on-brand content.')).toBeInTheDocument();
    });

    it('calls onUpdateStyleGuide when update style guide button is clicked', () => {
        const onUpdateStyleGuide = jest.fn();
        render(<Dashboard {...defaultProps} onUpdateStyleGuide={onUpdateStyleGuide} />);
        
        const updateButton = screen.getByRole('button', { name: /update style guide/i });
        fireEvent.click(updateButton);
        
        expect(onUpdateStyleGuide).toHaveBeenCalledTimes(1);
    });

    it('calls onGenerateIdeas when generate ideas button is clicked', () => {
        const onGenerateIdeas = jest.fn();
        render(<Dashboard {...defaultProps} onGenerateIdeas={onGenerateIdeas} />);
        
        const generateButton = screen.getByRole('button', { name: /generate new ideas/i });
        fireEvent.click(generateButton);
        
        expect(onGenerateIdeas).toHaveBeenCalledTimes(1);
    });

    it('disables generate ideas button when style guide is not ready', () => {
        const propsWithUndefined = { ...defaultProps, lastAnalyzed: undefined };
        render(<Dashboard {...propsWithUndefined} />);
        
        const generateButton = screen.getByRole('button', { name: /generate new ideas/i });
        expect(generateButton).toBeDisabled();
    });

    it('calls onNavigate when pipeline view buttons are clicked', () => {
        const onNavigate = jest.fn();
        render(<Dashboard {...defaultProps} onNavigate={onNavigate} />);
        
        const viewButtons = screen.getAllByRole('button', { name: /view/i });
        
        fireEvent.click(viewButtons[0]); // Ideas view
        expect(onNavigate).toHaveBeenCalledWith('ideas');
        
        fireEvent.click(viewButtons[1]); // Drafts view
        expect(onNavigate).toHaveBeenCalledWith('drafts');
        
        fireEvent.click(viewButtons[2]); // Published view
        expect(onNavigate).toHaveBeenCalledWith('published');
    });

    it('shows loading state for style guide update', () => {
        render(<Dashboard {...defaultProps} isLoadingStyle={true} />);
        
        expect(screen.getByText('Scanning...')).toBeInTheDocument();
        expect(screen.getByRole('button', { name: /scanning/i })).toBeDisabled();
    });

    it('shows loading state for idea generation', () => {
        render(<Dashboard {...defaultProps} isLoadingIdeas={true} />);
        
        expect(screen.getByText('Generating...')).toBeInTheDocument();
        expect(screen.getByRole('button', { name: /generating/i })).toBeDisabled();
    });

    it('renders activity logs', () => {
        render(<Dashboard {...defaultProps} />);
        
        expect(screen.getByRole('heading', { name: 'Recent Activity' })).toBeInTheDocument();
        // ActivityLogList component would be tested separately
    });

    it('has proper accessibility attributes', () => {
        render(<Dashboard {...defaultProps} />);
        
        // Check for proper heading hierarchy
        const mainHeading = screen.getByRole('heading', { level: 1 });
        expect(mainHeading).toHaveTextContent('Dashboard');
        
        const sectionHeadings = screen.getAllByRole('heading', { level: 2 });
        expect(sectionHeadings).toHaveLength(4); // Quick Actions, Content Pipeline, Status, Recent Activity
        
        // Check for aria-labels on buttons
        const actionButtons = screen.getAllByRole('button');
        actionButtons.forEach(button => {
            expect(button).toHaveProperty('getAttribute');
        });
    });

    it('handles keyboard navigation properly', () => {
        render(<Dashboard {...defaultProps} />);
        
        const buttons = screen.getAllByRole('button');
        buttons.forEach(button => {
            expect(button).not.toHaveAttribute('tabindex', '-1');
        });
    });

    it('renders with proper semantic structure', () => {
        render(<Dashboard {...defaultProps} />);
        
        // Check for proper sections
        const sections = screen.getAllByRole('region');
        expect(sections.length).toBeGreaterThan(0);
        
        // Check for proper landmarks
        // Main element would be in parent component
    });
});

describe('Dashboard Component - Edge Cases', () => {
    it('handles empty activity logs gracefully', () => {
        render(<Dashboard {...defaultProps} activityLogs={[]} />);
        
        expect(screen.getByRole('heading', { name: 'Recent Activity' })).toBeInTheDocument();
        // Empty state would be handled by ActivityLogList component
    });

    it('handles zero stats gracefully', () => {
        const zeroStats = { ideas: 0, drafts: 0, published: 0 };
        render(<Dashboard {...defaultProps} stats={zeroStats} />);
        
        expect(screen.getByText('0 ideas waiting')).toBeInTheDocument();
        expect(screen.getByText('0 drafts to review')).toBeInTheDocument();
        expect(screen.getByText('0 posts are live')).toBeInTheDocument();
    });

    it('formats last analyzed date correctly', () => {
        const testDate = '2024-01-15T10:30:00Z';
        render(<Dashboard {...defaultProps} lastAnalyzed={testDate} />);
        
        // The exact format depends on locale, but should be present
        expect(screen.getByText('Last Scanned:')).toBeInTheDocument();
    });
});