
const DashboardSkeleton = () => {
    return (
        <div className="space-y-8 animate-in fade-in duration-500">
            {/* Greeting Skeleton */}
            <div className="space-y-2">
                <SkeletonLoader type="rect" className="h-8 w-1/3" />
                <SkeletonLoader type="text" count={1} className="w-1/4" />
            </div>

            {/* Stats Grid Skeleton */}
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                {[...Array(4)].map((_, i) => (
                    <div key={i} className="rounded-2xl border border-gray-100 dark:border-gray-800 p-6 flex items-center gap-4 bg-white dark:bg-gray-900 shadow-sm">
                        <SkeletonLoader type="circle" className="w-12 h-12" />
                        <div className="flex-1 space-y-2">
                            <SkeletonLoader type="text" count={1} className="w-1/2" />
                            <SkeletonLoader type="rect" className="h-8 w-3/4" />
                        </div>
                    </div>
                ))}
            </div>

            {/* Quick Actions & Widgets Skeleton */}
            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div className="lg:col-span-2 space-y-6">
                    {/* Quick Actions Grid */}
                    <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                        {[...Array(4)].map((_, i) => (
                            <div key={i} className="h-32 rounded-2xl border border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900 flex flex-col items-center justify-center gap-3">
                                <SkeletonLoader type="circle" className="w-10 h-10" />
                                <SkeletonLoader type="text" count={1} className="w-20" />
                            </div>
                        ))}
                    </div>
                    {/* Table Skeleton */}
                    <div className="rounded-2xl border border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900 p-6 space-y-4">
                        <SkeletonLoader type="rect" className="h-6 w-1/4" />
                        <div className="space-y-3">
                            {[...Array(5)].map((_, i) => (
                                <div key={i} className="flex gap-4">
                                    <SkeletonLoader type="rect" className="h-10 w-full" />
                                </div>
                            ))}
                        </div>
                    </div>
                </div>

                {/* Sidebar Widget Skeleton */}
                <div className="rounded-2xl border border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900 p-6 h-full">
                    <SkeletonLoader type="rect" className="h-6 w-1/2 mb-4" />
                    <div className="space-y-4">
                        {[...Array(3)].map((_, i) => (
                            <div key={i} className="flex gap-3">
                                <SkeletonLoader type="rect" className="w-10 h-10 rounded-lg" />
                                <div className="flex-1 space-y-2">
                                    <SkeletonLoader type="text" count={2} />
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </div>
    );
};

export default DashboardSkeleton;
