
import React from 'react';
import type { Draft } from '../types';
import { Send } from './Icons';

interface PublishedListProps {
    posts: Draft[];
    onSelectPost: (post: Draft) => void;
}

export const PublishedList: React.FC<PublishedListProps> = ({ posts, onSelectPost }) => {
    return (
        <div className="space-y-6">
            <header>
                <h2 className="text-3xl font-bold text-white border-b border-slate-700 pb-3 mb-6">Published Posts</h2>
                <p className="text-slate-400 -mt-2">Here are your live articles. Congratulations!</p>
            </header>

            <div className="bg-slate-800 p-4 sm:p-6 rounded-lg border border-slate-700/80">
                {posts.length > 0 ? (
                    <div className="space-y-3">
                        {posts.map(post => (
                            <div key={post.id} className="bg-slate-900/50 p-4 rounded-lg border border-slate-700 flex flex-col sm:flex-row gap-4 justify-between items-center hover:bg-slate-700/50 transition-colors">
                                <div className="flex-grow text-center sm:text-left cursor-pointer" onClick={() => onSelectPost(post)}>
                                    <h3 className="text-lg font-semibold text-green-400">{post.title}</h3>
                                    <p className="text-sm text-slate-400">
                                        Published: {post.publishedAt ? new Date(post.publishedAt).toLocaleString() : 'N/A'}
                                    </p>
                                </div>
                                <button
                                    onClick={() => onSelectPost(post)}
                                    className="bg-slate-600 text-white px-4 py-2 rounded-md text-sm font-semibold hover:bg-slate-700 transition-colors flex-shrink-0"
                                >
                                    View Post
                                </button>
                            </div>
                        ))}
                    </div>
                ) : (
                    <div className="text-center py-16 text-slate-500 border-2 border-dashed border-slate-700 rounded-md">
                        <Send className="mx-auto h-12 w-12 text-slate-600" />
                        <p className="mt-4 text-lg font-medium">No posts have been published yet.</p>
                        <p>Create drafts and publish them to see them here.</p>
                    </div>
                )}
            </div>
        </div>
    );
};