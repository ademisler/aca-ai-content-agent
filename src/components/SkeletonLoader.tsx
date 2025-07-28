import React from 'react';

interface SkeletonProps {
    className?: string;
    width?: string | number;
    height?: string | number;
    rounded?: boolean;
}

export const Skeleton: React.FC<SkeletonProps> = ({ 
    className = '', 
    width, 
    height, 
    rounded = false 
}) => {
    const style: React.CSSProperties = {};
    if (width) style.width = typeof width === 'number' ? `${width}px` : width;
    if (height) style.height = typeof height === 'number' ? `${height}px` : height;

    return (
        <div
            className={`animate-pulse bg-slate-700 ${rounded ? 'rounded-full' : 'rounded'} ${className}`}
            style={style}
        />
    );
};

export const SkeletonText: React.FC<{ lines?: number; className?: string }> = ({ 
    lines = 1, 
    className = '' 
}) => (
    <div className={`space-y-2 ${className}`}>
        {Array.from({ length: lines }).map((_, index) => (
            <Skeleton
                key={index}
                height={16}
                width={index === lines - 1 ? '75%' : '100%'}
            />
        ))}
    </div>
);

export const SkeletonCard: React.FC<{ className?: string }> = ({ className = '' }) => (
    <div className={`bg-slate-800 border border-slate-700 rounded-lg p-6 ${className}`}>
        <div className="flex items-center mb-4">
            <Skeleton width={40} height={40} rounded className="mr-3" />
            <div className="flex-1">
                <Skeleton height={20} width="60%" className="mb-2" />
                <Skeleton height={16} width="40%" />
            </div>
        </div>
        <SkeletonText lines={3} />
    </div>
);

export const SkeletonButton: React.FC<{ className?: string }> = ({ className = '' }) => (
    <Skeleton 
        height={40} 
        width={120} 
        className={`rounded-lg ${className}`} 
    />
);

export const SkeletonStats: React.FC<{ className?: string }> = ({ className = '' }) => (
    <div className={`bg-slate-800 border border-slate-700 rounded-lg p-6 ${className}`}>
        <div className="flex items-center justify-between mb-4">
            <div className="flex items-center">
                <Skeleton width={40} height={40} rounded className="mr-3" />
                <div>
                    <Skeleton height={20} width={80} className="mb-1" />
                    <Skeleton height={14} width={60} />
                </div>
            </div>
            <SkeletonButton />
        </div>
    </div>
);

export const SkeletonDashboard: React.FC = () => (
    <div className="space-y-8">
        {/* Header */}
        <div>
            <Skeleton height={36} width={200} className="mb-4" />
            <Skeleton height={20} width={300} />
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {/* Left Column */}
            <div className="lg:col-span-2 space-y-8">
                {/* Quick Actions */}
                <div>
                    <Skeleton height={24} width={150} className="mb-4" />
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <SkeletonCard />
                        <SkeletonCard />
                    </div>
                </div>

                {/* Content Pipeline */}
                <div>
                    <Skeleton height={24} width={180} className="mb-4" />
                    <div className="bg-slate-800 border border-slate-700 rounded-lg p-5 space-y-4">
                        <SkeletonStats />
                        <SkeletonStats />
                        <SkeletonStats />
                    </div>
                </div>
            </div>

            {/* Right Column */}
            <div className="lg:col-span-1 space-y-8">
                {/* Status */}
                <div>
                    <Skeleton height={24} width={80} className="mb-4" />
                    <SkeletonCard />
                </div>

                {/* Recent Activity */}
                <div>
                    <Skeleton height={24} width={140} className="mb-4" />
                    <div className="bg-slate-800 border border-slate-700 rounded-lg p-5 space-y-3">
                        {Array.from({ length: 5 }).map((_, index) => (
                            <div key={index} className="flex items-center">
                                <Skeleton width={32} height={32} rounded className="mr-3" />
                                <div className="flex-1">
                                    <Skeleton height={16} width="80%" className="mb-1" />
                                    <Skeleton height={12} width="50%" />
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </div>
    </div>
);

export const SkeletonIdeaBoard: React.FC = () => (
    <div className="space-y-6">
        <div className="flex justify-between items-center">
            <Skeleton height={36} width={180} />
            <SkeletonButton />
        </div>
        
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {Array.from({ length: 6 }).map((_, index) => (
                <SkeletonCard key={index} />
            ))}
        </div>
    </div>
);

export const SkeletonSettings: React.FC = () => (
    <div className="space-y-8">
        <Skeleton height={36} width={120} />
        
        <div className="space-y-6">
            {/* Automation Mode */}
            <div>
                <Skeleton height={20} width={150} className="mb-4" />
                <div className="space-y-3">
                    {Array.from({ length: 3 }).map((_, index) => (
                        <div key={index} className="bg-slate-800 border border-slate-700 rounded-lg p-5">
                            <div className="flex items-start">
                                <Skeleton width={16} height={16} className="mt-1 mr-3" />
                                <div className="flex-1">
                                    <Skeleton height={18} width="40%" className="mb-2" />
                                    <Skeleton height={14} width="80%" />
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
            </div>

            {/* Integrations */}
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {Array.from({ length: 4 }).map((_, index) => (
                    <SkeletonCard key={index} />
                ))}
            </div>
        </div>
    </div>
);

export default Skeleton;