
import React from 'react';
import type { Draft } from '../types';
import { Send } from './Icons';

interface PublishedListProps {
    posts: Draft[];
    onSelectPost: (post: Draft) => void;
}

export const PublishedList: React.FC<PublishedListProps> = ({ posts, onSelectPost }) => {
    return (
        <div className="aca-fade-in">
            <div className="aca-page-header">
                <h1 className="aca-page-title">Published Posts</h1>
                <p className="aca-page-description">Here are your live articles. Congratulations!</p>
            </div>

            <div className="aca-card">
                {posts.length > 0 ? (
                    <div style={{ display: 'flex', flexDirection: 'column', gap: '15px' }}>
                        {posts.map(post => (
                            <div 
                                key={post.id} 
                                style={{ 
                                    background: '#f6f7f7', 
                                    padding: '20px', 
                                    borderRadius: '4px', 
                                    border: '1px solid #ccd0d4', 
                                    display: 'flex', 
                                    flexDirection: 'column', 
                                    gap: '15px', 
                                    alignItems: 'center', 
                                    transition: 'background 0.2s ease',
                                    cursor: 'pointer'
                                }}
                                onMouseEnter={(e) => {
                                    e.currentTarget.style.background = '#f0f0f1';
                                }}
                                onMouseLeave={(e) => {
                                    e.currentTarget.style.background = '#f6f7f7';
                                }}
                            >
                                <div 
                                    style={{ 
                                        flexGrow: 1, 
                                        textAlign: 'center', 
                                        cursor: 'pointer',
                                        width: '100%'
                                    }} 
                                    onClick={() => onSelectPost(post)}
                                >
                                    <h3 style={{ 
                                        fontSize: '18px', 
                                        fontWeight: '600', 
                                        color: '#00a32a', 
                                        margin: '0 0 8px 0' 
                                    }}>
                                        {post.title}
                                    </h3>
                                    <p style={{ 
                                        fontSize: '13px', 
                                        color: '#646970', 
                                        margin: 0 
                                    }}>
                                        Published: {post.publishedAt ? new Date(post.publishedAt).toLocaleString() : 'N/A'}
                                    </p>
                                </div>
                                <button
                                    onClick={() => onSelectPost(post)}
                                    className="aca-button secondary"
                                    style={{ 
                                        fontSize: '13px',
                                        padding: '8px 16px',
                                        flexShrink: 0
                                    }}
                                >
                                    View Post
                                </button>
                            </div>
                        ))}
                    </div>
                ) : (
                    <div style={{ 
                        textAlign: 'center', 
                        padding: '60px 20px', 
                        color: '#646970', 
                        border: '2px dashed #ccd0d4', 
                        borderRadius: '4px' 
                    }}>
                        <Send style={{ margin: '0 auto 15px auto', width: '48px', height: '48px', fill: '#a7aaad' }} />
                        <p style={{ margin: '0 0 5px 0', fontSize: '16px', fontWeight: '500', color: '#23282d' }}>No posts have been published yet.</p>
                        <p style={{ margin: 0, fontSize: '13px' }}>Create drafts and publish them to see them here.</p>
                    </div>
                )}
            </div>
        </div>
    );
};