import React from 'react';
import { Route, Routes } from 'react-router-dom';

const FinancialRoutes = () => {
    return (
        <Routes>
            <Route path="payments" element={<div>Payments Management</div>} />
            <Route path="invoices" element={<div>Invoices Management</div>} />
        </Routes>
    );
};

export default FinancialRoutes;
