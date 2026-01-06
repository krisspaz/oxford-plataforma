import React from 'react';
import { Archive } from 'lucide-react';

const EmptyState = ({ title, description, action }) => {
    return (
        <div className="text-center py-12">
            <div className="flex justify-center mb-4">
                <div className="p-4 bg-gray-100 dark:bg-gray-800 rounded-full">
                    <Archive className="h-8 w-8 text-gray-400" />
                </div>
            </div>
            <h3 className="text-lg font-medium text-gray-900 dark:text-white">{title}</h3>
            <p className="mt-1 text-sm text-gray-500 max-w-sm mx-auto">{description}</p>
            {action && <div className="mt-6">{action}</div>}
        </div>
    );
};

export default EmptyState;
