import React from 'react';
import { Link, useLocation } from 'react-router-dom';
import { ChevronRight } from 'lucide-react';

const Breadcrumbs = () => {
    const location = useLocation();
    const paths = location.pathname.split('/').filter(x => x);

    return (
        <nav className="flex text-sm text-gray-500 mb-4" aria-label="Breadcrumb">
            <ol className="inline-flex items-center space-x-1 md:space-x-3">
                <li className="inline-flex items-center">
                    <Link to="/dashboard" className="hover:text-indigo-600">Home</Link>
                </li>
                {paths.map((path, index) => {
                    const to = `/${paths.slice(0, index + 1).join('/')}`;
                    return (
                        <li key={to} className="inline-flex items-center">
                            <ChevronRight size={16} />
                            <Link to={to} className="capitalize hover:text-indigo-600 ml-1">{path}</Link>
                        </li>
                    );
                })}
            </ol>
        </nav>
    );
};

export default Breadcrumbs;
