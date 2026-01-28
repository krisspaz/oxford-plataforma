/**
 * EmptyState Component
 * Displays empty states with optional action button
 */
import { Inbox } from 'lucide-react';
import { Button } from './index.jsx';
import { cn } from '../../utils/cn';

export const EmptyState = ({
    icon: Icon = Inbox,
    title = 'Sin datos',
    description = 'No hay elementos para mostrar.',
    action,
    actionLabel,
    actionIcon,
    className,
}) => {
    return (
        <div className={cn('flex flex-col items-center justify-center py-16 px-4 text-center', className)}>
            {/* Icon */}
            <div className="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-4">
                <Icon className="text-gray-400 dark:text-gray-500" size={32} />
            </div>

            {/* Title */}
            <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                {title}
            </h3>

            {/* Description */}
            <p className="text-gray-500 dark:text-gray-400 mb-6 max-w-md">
                {description}
            </p>

            {/* Action Button */}
            {action && actionLabel && (
                <Button onClick={action} icon={actionIcon}>
                    {actionLabel}
                </Button>
            )}
        </div>
    );
};

export default EmptyState;
