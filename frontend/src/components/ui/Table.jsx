/**
 * Table Component
 * Reusable table with loading, empty, and sortable states
 */
import { cn } from '../../utils/cn';
import { ChevronUp, ChevronDown, Loader2, Inbox } from 'lucide-react';

export const Table = ({
    columns = [],
    data = [],
    loading = false,
    emptyTitle = 'Sin datos',
    emptyMessage = 'No hay elementos para mostrar.',
    onRowClick,
    sortColumn,
    sortDirection,
    onSort,
    className,
}) => {
    // Loading State
    if (loading) {
        return (
            <div className="flex flex-col items-center justify-center py-16">
                <Loader2 className="animate-spin text-blue-600 mb-3" size={32} />
                <p className="text-gray-500 dark:text-gray-400">Cargando datos...</p>
            </div>
        );
    }

    // Empty State
    if (data.length === 0) {
        return (
            <div className="flex flex-col items-center justify-center py-16 text-center">
                <div className="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-4">
                    <Inbox className="text-gray-400" size={32} />
                </div>
                <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-1">
                    {emptyTitle}
                </h3>
                <p className="text-gray-500 dark:text-gray-400 max-w-sm">
                    {emptyMessage}
                </p>
            </div>
        );
    }

    const handleSort = (column) => {
        if (!column.sortable || !onSort) return;
        onSort(column.key);
    };

    return (
        <div className={cn('overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700', className)}>
            <table className="w-full border-collapse">
                <thead>
                    <tr className="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-700">
                        {columns.map((col) => (
                            <th
                                key={col.key}
                                onClick={() => handleSort(col)}
                                className={cn(
                                    'px-4 py-3 text-left text-sm font-semibold',
                                    'text-gray-600 dark:text-gray-300',
                                    col.sortable && onSort && 'cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors',
                                    col.align === 'center' && 'text-center',
                                    col.align === 'right' && 'text-right'
                                )}
                                style={{ width: col.width }}
                            >
                                <div className={cn(
                                    'flex items-center gap-2',
                                    col.align === 'center' && 'justify-center',
                                    col.align === 'right' && 'justify-end'
                                )}>
                                    {col.header}
                                    {col.sortable && sortColumn === col.key && (
                                        sortDirection === 'asc'
                                            ? <ChevronUp size={14} className="text-blue-600" />
                                            : <ChevronDown size={14} className="text-blue-600" />
                                    )}
                                </div>
                            </th>
                        ))}
                    </tr>
                </thead>
                <tbody className="divide-y divide-gray-100 dark:divide-gray-700">
                    {data.map((row, rowIdx) => (
                        <tr
                            key={row.id || rowIdx}
                            onClick={() => onRowClick?.(row)}
                            className={cn(
                                'bg-white dark:bg-gray-800',
                                'hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors',
                                onRowClick && 'cursor-pointer'
                            )}
                        >
                            {columns.map((col) => (
                                <td
                                    key={col.key}
                                    className={cn(
                                        'px-4 py-3 text-sm text-gray-700 dark:text-gray-200',
                                        col.align === 'center' && 'text-center',
                                        col.align === 'right' && 'text-right'
                                    )}
                                >
                                    {col.render
                                        ? col.render(row[col.key], row, rowIdx)
                                        : row[col.key] ?? '-'}
                                </td>
                            ))}
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
};

export default Table;
