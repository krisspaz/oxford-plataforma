import api from './api';

const teacherRatingService = {
    getAll: async () => {
        const response = await api.get('/teacher_ratings');
        return response.member || response['hydra:member'] || response;
    },

    getByTeacher: async (teacherId) => {
        const response = await api.get(`/teacher_ratings?teacher=${teacherId}`);
        return response.member || response['hydra:member'] || response;
    },

    create: async (data) => {
        const response = await api.post('/teacher_ratings', data);
        return response;
    },
};

export default teacherRatingService;
