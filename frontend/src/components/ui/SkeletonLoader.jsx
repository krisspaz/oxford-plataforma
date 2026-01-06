import React from 'react';

const SkeletonLoader = ({ lines = 3, className = "" }) => {
    return (
        <div className={`animate-pulse space-y-3 ${className}`}>
            {[...Array(lines)].map((_, i) => (
                <div key={i} className="h-4 bg-gray-200 dark:bg-gray-700 rounded w-full"></div>
            ))}
        </div>
    );
};

export default SkeletonLoader;
