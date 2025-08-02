
import React from 'react';
import type { Draft } from '../types';
import { Send, Eye, CheckCircle, ExternalLink } from './Icons';

interface PublishedListProps {
    posts: Draft[];
    onSelectPost: (post: Draft) => void;
    onNavigateToDrafts?: () => void;
}

const PublishedPostCard: React.FC<{
    post: Draft;
    onSelectPost: (post: Draft) => void;
}> = ({ post, onSelectPost }) => {
    return (
        <div className="aca-card" style={{ margin: 0, position: 'relative' }}>
            {/* Post Header */}
            <div style={{ marginBottom: '15px', paddingRight: '100px' }}>
                <h3 className="aca-card-title" style={{ 
                    color: '#00a32a', 
                    marginBottom: '8px'
                }}>
                    {post.title}
                </h3>
                <div className="aca-page-description" style={{ 
                    display: 'flex', 
                    alignItems: 'center', 
                    gap: '15px', 
                    flexWrap: 'wrap'
                }}>
                    <span>
                        Published: {post.publishedAt ? new Date(post.publishedAt).toLocaleDateString() : 'N/A'}
                    </span>
                    {post.publishedAt && (
                        <span>
                            {new Date(post.publishedAt).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
                        </span>
                    )}
                </div>
                
                {/* Status Badge */}
                <div style={{ 
                    position: 'absolute',
                    top: '16px',
                    right: '16px',
                    fontSize: '11px', 
                    fontWeight: '600', 
                    padding: '4px 8px', 
                    borderRadius: '6px', 
                    background: 'linear-gradient(135deg, #10b981, #059669)',
                    color: 'white',
                    display: 'flex',
                    alignItems: 'center',
                    gap: '4px',
                    boxShadow: '0 2px 4px rgba(16, 185, 129, 0.2)'
                }}>
                    <CheckCircle style={{ width: '12px', height: '12px' }} />
                    Published
                </div>
            </div>

            {/* Actions */}
            <div className="aca-list-item" style={{ padding: '15px 0 0 0', margin: 0, justifyContent: 'flex-end' }}>
                <button
                    onClick={() => onSelectPost(post)}
                    className="aca-button secondary"
                    style={{ 
                        background: '#ffffff',
                        borderColor: '#0073aa',
                        color: '#0073aa',
                        boxShadow: '0 1px 3px rgba(0,0,0,0.1)'
                    }}
                >
                    <Eye className="aca-nav-item-icon" style={{ color: '#0073aa', strokeWidth: '1.5' }} />
                    View Content
                </button>
                
                {/* If we had post URLs, we could add a "View Live" button */}
                {/* <button
                    className="aca-button"
                    style={{ 
                        background: '#0073aa',
                        borderColor: '#0073aa'
                    }}
                >
                    <ExternalLink className="aca-nav-item-icon" />
                    View Live
                </button> */}
            </div>
        </div>
    );
};

export const PublishedList: React.FC<PublishedListProps> = ({ posts, onSelectPost, onNavigateToDrafts }) => {
    // Sort posts by publication date (newest first)
    const sortedPosts = [...posts].sort((a, b) => {
        if (!a.publishedAt) return 1;
        if (!b.publishedAt) return -1;
        return new Date(b.publishedAt).getTime() - new Date(a.publishedAt).getTime();
    });

    return (
        <div className="aca-fade-in">
            {/* Modern Published Posts Header */}
            <div style={{
                background: 'linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%)',
                borderRadius: '12px',
                padding: '30px',
                marginBottom: '30px',
                color: 'white',
                position: 'relative',
                overflow: 'hidden'
            }}>
                <div style={{ position: 'relative', zIndex: 2 }}>
                    <div style={{ display: 'flex', alignItems: 'center', gap: '12px', marginBottom: '12px' }}>
                        <div style={{
                            width: '48px',
                            height: '48px',
                            background: 'rgba(255,255,255,0.2)',
                            borderRadius: '12px',
                            display: 'flex',
                            alignItems: 'center',
                            justifyContent: 'center',
                            backdropFilter: 'blur(10px)'
                        }}>
                            <Send style={{ width: '24px', height: '24px' }} />
                        </div>
                        <div>
                                                         <h1 style={{ 
                                fontSize: '28px', 
                                fontWeight: '700', 
                                margin: 0,
                                textShadow: '0 2px 4px rgba(0,0,0,0.1)',
                                color: 'white'
                            }}>
                                Published Posts
                            </h1>
                            <div style={{ fontSize: '16px', opacity: 0.9, marginTop: '4px' }}>
                                Your successful content publications
                            </div>
                        </div>
                    </div>
                    <p style={{ 
                        fontSize: '14px', 
                        opacity: 0.85,
                        margin: 0,
                        maxWidth: '600px',
                        lineHeight: '1.5'
                    }}>
                        Congratulations! Here are your successfully published articles. Click "View Content" to see the full post.
                    </p>
                    
                    {/* Stats */}
                    <div style={{ display: 'flex', gap: '20px', marginTop: '20px', flexWrap: 'wrap' }}>
                        <div style={{ display: 'flex', alignItems: 'center', gap: '6px' }}>
                            <div style={{ width: '8px', height: '8px', backgroundColor: '#4ade80', borderRadius: '50%' }}></div>
                            <span style={{ fontSize: '14px', opacity: 0.9 }}>{posts.length} Published Articles</span>
                        </div>
                        <div style={{ display: 'flex', alignItems: 'center', gap: '6px' }}>
                            <div style={{ width: '8px', height: '8px', backgroundColor: '#60a5fa', borderRadius: '50%' }}></div>
                            <span style={{ fontSize: '14px', opacity: 0.9 }}>AI-Generated Content</span>
                        </div>
                        <div style={{ display: 'flex', alignItems: 'center', gap: '6px' }}>
                            <div style={{ width: '8px', height: '8px', backgroundColor: '#fbbf24', borderRadius: '50%' }}></div>
                            <span style={{ fontSize: '14px', opacity: 0.9 }}>SEO Optimized</span>
                        </div>
                    </div>
                </div>
                {/* Decorative elements */}
                <div style={{
                    position: 'absolute',
                    top: '-30px',
                    right: '-30px',
                    width: '120px',
                    height: '120px',
                    background: 'rgba(255,255,255,0.1)',
                    borderRadius: '50%',
                    zIndex: 1
                }}></div>
                <div style={{
                    position: 'absolute',
                    bottom: '-20px',
                    left: '-20px',
                    width: '80px',
                    height: '80px',
                    background: 'rgba(255,255,255,0.05)',
                    borderRadius: '50%',
                    zIndex: 1
                }}></div>
            </div>

            {posts.length > 0 ? (
                <div className="aca-card">
                    <div className="aca-card-header">
                        <h2 className="aca-card-title">
                            <Send className="aca-nav-item-icon" />
                            Your Published Content ({posts.length})
                        </h2>
                    </div>
                    <div className="aca-grid aca-grid-2">
                        {sortedPosts.map(post => (
                            <PublishedPostCard
                                key={post.id}
                                post={post}
                                onSelectPost={onSelectPost}
                            />
                        ))}
                    </div>
                </div>
            ) : (
                <div className="aca-card">
                    <div style={{ textAlign: 'center', padding: '60px 20px', color: '#646970' }}>
                        <Send style={{ margin: '0 auto 20px auto', width: '48px', height: '48px', fill: '#a7aaad' }} />
                        <h3 className="aca-card-title">No Published Posts Yet</h3>
                        <p className="aca-page-description" style={{ maxWidth: '400px', marginLeft: 'auto', marginRight: 'auto' }}>
                            Create drafts and publish them to see your success stories here.
                        </p>
                        <div style={{ display: 'flex', gap: '10px', justifyContent: 'center', flexWrap: 'wrap' }}>
                            <button 
                                onClick={onNavigateToDrafts || (() => window.location.hash = '#drafts')} 
                                className="aca-button large"
                            >
                                <Send style={{ width: '16px', height: '16px' }} />
                                Go to Drafts
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};