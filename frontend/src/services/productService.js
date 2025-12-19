import { api } from './api';

export const productService = {
    // Get all products
    getAll: (type = null) =>
        api.get(`/products${type ? `?type=${type}` : ''}`),

    // Get product types
    getTypes: () => api.get('/products/types'),

    // Create product
    create: (data) => api.post('/products', data),

    // Update product
    update: (id, data) => api.put(`/products/${id}`, data),
};
export default productService;
