import axios from 'axios';

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api';

const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});

// Services
export const getServices = () => api.get('/services');
export const getAllServices = () => api.get('/admin/services');
export const createService = (data) => api.post('/admin/services', data);
export const updateService = (id, data) => api.put(`/admin/services/${id}`, data);
export const deleteService = (id) => api.delete(`/admin/services/${id}`);

// Payment Methods
export const getPaymentMethods = () => api.get('/payment-methods');

// Bank Details
export const getBankDetails = () => api.get('/bank-details');
export const updateBankDetails = (id, data) => api.put(`/admin/bank-details/${id}`, data);

// Bookings
export const createBooking = (data) => api.post('/bookings', data);
export const getBooking = (id) => api.get(`/bookings/${id}`);
export const getBookings = (params) => api.get('/admin/bookings', { params });
export const updateBooking = (id, data) => api.put(`/admin/bookings/${id}`, data);
export const cancelBooking = (id) => api.delete(`/admin/bookings/${id}`);
export const getBookingStatistics = () => api.get('/admin/bookings/statistics');

// Payments
export const uploadProofOfPayment = (formData) => {
  return api.post('/payments/upload-proof', formData, {
    headers: { 'Content-Type': 'multipart/form-data' },
  });
};
export const getPayments = (params) => api.get('/admin/payments', { params });
export const verifyPayment = (id, notes) => api.put(`/admin/payments/${id}/verify`, { admin_notes: notes });
export const rejectPayment = (id, notes) => api.put(`/admin/payments/${id}/reject`, { admin_notes: notes });
export const exportPayments = (params) => {
  return api.get('/admin/payments/export', { 
    params,
    responseType: 'blob' 
  });
};

export default api;
