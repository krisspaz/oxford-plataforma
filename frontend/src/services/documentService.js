import { api } from './api';

export const documentService = {
    // Get documents for an entity
    getByEntity: (entityType, entityId) =>
        api.get(`/documents?entityType=${entityType}&entityId=${entityId}`),

    // Upload a document
    upload: (file, entityType, entityId, documentType = 'OTRO') => {
        const formData = new FormData();
        formData.append('file', file);
        formData.append('entityType', entityType);
        formData.append('entityId', entityId);
        formData.append('documentType', documentType);

        return fetch(`${import.meta.env.VITE_API_URL}/documents/upload`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('token')}`
            },
            body: formData
        }).then(res => res.json());
    },

    // Delete a document
    delete: (id) => api.delete(`/documents/${id}`),

    // Get photo URL for an entity
    getPhoto: (entityType, entityId) =>
        api.get(`/documents/photo/${entityType}/${entityId}`),

    // Upload photo specifically
    uploadPhoto: (file, entityType, entityId) =>
        documentService.upload(file, entityType, entityId, 'FOTO'),
};
