import React from 'react';

const Tooltip = ({ text, children }) => {
    return (
        <div className="group relative flex">
            {children}
            <span className="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block w-auto p-2 min-w-max rounded-md shadow-md text-white bg-gray-900 text-xs font-bold transition-all duration-100 scale-0 group-hover:scale-100 origin-bottom">
                {text}
            </span>
        </div>
    );
};

export default Tooltip;
