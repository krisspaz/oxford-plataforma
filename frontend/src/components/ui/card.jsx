
export const Card = ({ className = '', children, ...props }) => {
    return (
        <div
            className={`bg-white dark:bg-gray-800 rounded-2xl shadow-lg ${className}`}
            {...props}
        >
            {children}
        </div>
    );
};

export const CardHeader = ({ className = '', children, ...props }) => {
    return (
        <div className={`flex flex-col space-y-1.5 p-6 pb-2 ${className}`} {...props}>
            {children}
        </div>
    );
};

export const CardTitle = ({ className = '', children, ...props }) => {
    return (
        <h3 className={`font-semibold leading-none tracking-tight text-gray-900 dark:text-white ${className}`} {...props}>
            {children}
        </h3>
    );
};

export const CardContent = ({ className = '', children, ...props }) => {
    return (
        <div className={`p-6 pt-0 ${className}`} {...props}>
            {children}
        </div>
    );
};
