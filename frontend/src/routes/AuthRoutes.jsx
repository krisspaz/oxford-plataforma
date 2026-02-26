import { lazy } from 'react';

// eslint-disable-next-line unused-imports/no-unused-vars
const Login = lazy(() => import('../pages/Login'));

export const authRouteElements = (
    <Route path="/login" element={<Login />} />
);
