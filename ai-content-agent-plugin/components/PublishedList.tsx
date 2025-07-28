
import React from 'react';
import type { Draft } from '../types';
import { Send, Eye, CheckCircle, ExternalLink } from './Icons';

interface PublishedListProps {
    posts: Draft[];
    onSelectPost: (post: Draft) => void;
}

const PublishedPostCard: React.FC<{
    post: Draft;
    onSelectPost: (post: Draft) => void;
}> = ({ post, onSelectPost }) => {
    return (
        <div className="aca-card" style={{ margin: 0 }}>
            {/* Post Header */}
            <div style={{ 
                display: 'flex', 
                justifyContent: 'space-between', 
                alignItems: 'flex-start',
                marginBottom: '15px',
                gap: '15px'
            }}>
                <div style={{ flexGrow: 1 }}>
                    <h3 style={{ 
                        fontSize: '18px', 
                        fontWeight: '600', 
                        color: '#00a32a', 
                        margin: '0 0 8px 0',
                        lineHeight: '1.3'
                    }}>
                        {post.title}
                    </h3>
                    <div style={{ 
                        display: 'flex', 
                        alignItems: 'center', 
                        gap: '15px', 
                        fontSize: '13px', 
                        color: '#646970',
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
                </div>
                
                {/* Status Badge */}
                <div style={{ 
                    fontSize: '11px', 
                    fontWeight: '600', 
                    padding: '6px 12px', 
                    borderRadius: '4px', 
                    border: '1px solid',
                    background: '#e6f7e6',
                    color: '#0a5d0a',
                    borderColor: '#28a745',
                    flexShrink: 0,
                    display: 'flex',
                    alignItems: 'center',
                    gap: '6px'
                }}>
                    <CheckCircle style={{ width: '12px', height: '12px' }} />
                    Published
                </div>
            </div>

            {/* Actions */}
            <div style={{ 
                display: 'flex', 
                alignItems: 'center', 
                gap: '10px',
                paddingTop: '15px',
                borderTop: '1px solid #f0f0f1',
                flexWrap: 'wrap',
                justifyContent: 'flex-end'
            }}>
                <button
                    onClick={() => onSelectPost(post)}
                    className="aca-button secondary"
                    style={{ 
                        fontSize: '13px',
                        padding: '8px 16px',
                        display: 'flex',
                        alignItems: 'center',
                        gap: '8px'
                    }}
                >
                    <Eye style={{ width: '14px', height: '14px' }} />
                    View Content
                </button>
                
                {/* If we had post URLs, we could add a "View Live" button */}
                {/* <button
                    className="aca-button"
                    style={{ 
                        fontSize: '13px',
                        padding: '8px 16px',
                        display: 'flex',
                        alignItems: 'center',
                        gap: '8px',
                        background: '#0073aa',
                        borderColor: '#0073aa'
                    }}
                >
                    <ExternalLink style={{ width: '14px', height: '14px' }} />
                    View Live
                </button> */}
            </div>
        </div>
    );
};

export const PublishedList: React.FC<PublishedListProps> = ({ posts, onSelectPost }) => {
    // Sort posts by publication date (newest first)
    const sortedPosts = [...posts].sort((a, b) => {
        if (!a.publishedAt) return 1;
        if (!b.publishedAt) return -1;
        return new Date(b.publishedAt).getTime() - new Date(a.publishedAt).getTime();
    });

    return (
        <div className="aca-fade-in">
            <div className="aca-page-header">
                <h1 className="aca-page-title">Published Posts</h1>
                <p className="aca-page-description">
                    Congratulations! Here are your successfully published articles. Click "View Content" to see the full post.
                </p>
            </div>

            {posts.length > 0 ? (
                <div className="aca-card">
                    <div className="aca-card-header">
                        <h2 className="aca-card-title">
                            <Send style={{ width: '20px', height: '20px', marginRight: '8px', fill: '#0073aa' }} />
                            Your Published Content ({posts.length})
                        </h2>
                    </div>
                    <div className="aca-grid aca-grid-2" style={{ gap: '20px' }}>
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
                    <div style={{ 
                        textAlign: 'center', 
                        padding: '60px 20px', 
                        color: '#646970'
                    }}>
                        <Send style={{ margin: '0 auto 20px auto', width: '48px', height: '48px', fill: '#a7aaad' }} />
                        <h3 style={{ margin: '0 0 10px 0', fontSize: '18px', fontWeight: '500', color: '#23282d' }}>
                            No Published Posts Yet
                        </h3>
                        <p style={{ margin: '0 0 25px 0', fontSize: '14px', maxWidth: '400px', marginLeft: 'auto', marginRight: 'auto' }}>
                            Create drafts and publish them to see your success stories here.
                        </p>
                        <div style={{ display: 'flex', gap: '10px', justifyContent: 'center', flexWrap: 'wrap' }}>
                            <button 
                                onClick={() => window.location.hash = '#drafts'} 
                                className="aca-button large"
                                style={{ 
                                    display: 'inline-flex', 
                                    alignItems: 'center',
                                    gap: '8px'
                                }}
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