import { lazy } from 'react';
import { Route } from 'react-router-dom';

const Login = lazy(() => import('../pages/Login'));

export const authRouteElements = (
    <Route path="/login" element={<Login />} />
);
