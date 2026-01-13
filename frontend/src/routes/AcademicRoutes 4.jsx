import React from 'react';
import { Route, Routes } from 'react-router-dom';

const AcademicRoutes = () => {
    return (
        <Routes>
            <Route path="grades" element={<div>Grades Management</div>} />
            <Route path="curriculum" element={<div>Curriculum Management</div>} />
        </Routes>
    );
};

export default AcademicRoutes;
