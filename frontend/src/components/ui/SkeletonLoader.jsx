
const SkeletonLoader = ({ type = 'text', count = 1, className = "" }) => {
    const baseClass = "animate-pulse bg-gray-200 dark:bg-gray-700 rounded";

    if (type === 'circle') {
        return <div className={`${baseClass} rounded-full ${className}`}></div>;
    }

    if (type === 'rect') {
        return <div className={`${baseClass} ${className}`}></div>;
    }

    // Default text lines
    return (
        <div className={`space-y-3 ${className}`}>
            {[...Array(count)].map((_, i) => (
                <div key={i} className={`${baseClass} h-4 w-full`}></div>
            ))}
        </div>
    );
};

export default SkeletonLoader;
