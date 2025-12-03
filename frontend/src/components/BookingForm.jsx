import { useState, useEffect } from 'react';
import { getServices, getPaymentMethods, getBankDetails, createBooking } from '../services/api';
import PaymentMethodSelector from './PaymentMethodSelector';
import PaymentInstructions from './PaymentInstructions';
import './BookingForm.css';

function BookingForm() {
    const [services, setServices] = useState([]);
    const [paymentMethods, setPaymentMethods] = useState([]);
    const [bankDetails, setBankDetails] = useState(null);
    const [loading, setLoading] = useState(false);
    const [success, setSuccess] = useState(false);
    const [error, setError] = useState(null);
    const [bookingData, setBookingData] = useState(null);

    const [formData, setFormData] = useState({
        service_id: '',
        payment_method_id: '',
        customer_name: '',
        customer_email: '',
        customer_phone: '',
        booking_start: '',
        booking_end: '',
        notes: '',
    });

    useEffect(() => {
        fetchInitialData();
    }, []);

    const fetchInitialData = async () => {
        try {
            const [servicesRes, methodsRes, bankRes] = await Promise.all([
                getServices(),
                getPaymentMethods(),
                getBankDetails(),
            ]);
            setServices(servicesRes.data);
            setPaymentMethods(methodsRes.data);
            setBankDetails(bankRes.data);
        } catch (err) {
            setError('Failed to load form data. Please refresh the page.');
        }
    };

    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormData(prev => ({ ...prev, [name]: value }));
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        setError(null);

        try {
            const response = await createBooking(formData);
            setBookingData(response.data.booking);
            setSuccess(true);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        } catch (err) {
            setError(err.response?.data?.message || 'Failed to create booking. Please try again.');
        } finally {
            setLoading(false);
        }
    };

    const selectedService = services.find(s => s.id === parseInt(formData.service_id));
    const selectedPaymentMethod = paymentMethods.find(m => m.id === parseInt(formData.payment_method_id));

    if (success && bookingData) {
        return (
            <div className="booking-success">
                <div className="success-icon">✅</div>
                <h2>Booking Confirmed!</h2>
                <div className="success-details">
                    <p><strong>Booking Reference:</strong> {bookingData.booking_reference}</p>
                    <p><strong>Service:</strong> {bookingData.service.name}</p>
                    <p><strong>Amount:</strong> R{parseFloat(bookingData.total_amount).toFixed(2)}</p>
                </div>

                {selectedPaymentMethod && (
                    <PaymentInstructions
                        paymentMethod={selectedPaymentMethod}
                        bankDetails={bankDetails}
                        bookingReference={bookingData.booking_reference}
                    />
                )}

                <div className="success-message">
                    <p>📧 A confirmation email has been sent to <strong>{bookingData.customer_email}</strong></p>
                    <p>and to <strong>diamondlipaz@gmail.com</strong></p>
                </div>

                <button className="btn btn-primary" onClick={() => window.location.reload()}>
                    Make Another Booking
                </button>
            </div>
        );
    }

    return (
        <div className="booking-form-container">
            <div className="form-header">
                <h1 className="gradient-text">🎵 Book Your Studio Session</h1>
                <p>LiveNetStudios - Professional Recording & Production</p>
            </div>

            {error && <div className="alert alert-danger">{error}</div>}

            <form onSubmit={handleSubmit} className="booking-form">
                {/* Customer Information */}
                <div className="form-section">
                    <h3>👤 Your Information</h3>
                    <div className="form-row">
                        <div className="form-group">
                            <label className="form-label">Full Name *</label>
                            <input
                                type="text"
                                name="customer_name"
                                className="form-control"
                                value={formData.customer_name}
                                onChange={handleChange}
                                required
                                placeholder="Enter your full name"
                            />
                        </div>
                    </div>

                    <div className="form-row">
                        <div className="form-group">
                            <label className="form-label">Email Address *</label>
                            <input
                                type="email"
                                name="customer_email"
                                className="form-control"
                                value={formData.customer_email}
                                onChange={handleChange}
                                required
                                placeholder="your.email@example.com"
                            />
                        </div>
                        <div className="form-group">
                            <label className="form-label">Phone Number *</label>
                            <input
                                type="tel"
                                name="customer_phone"
                                className="form-control"
                                value={formData.customer_phone}
                                onChange={handleChange}
                                required
                                placeholder="+27 XX XXX XXXX"
                            />
                        </div>
                    </div>
                </div>

                {/* Service Selection */}
                <div className="form-section">
                    <h3>🎙️ Select Service</h3>
                    <div className="form-group">
                        <label className="form-label">Service Type *</label>
                        <select
                            name="service_id"
                            className="form-control"
                            value={formData.service_id}
                            onChange={handleChange}
                            required
                        >
                            <option value="">Choose a service...</option>
                            {services.map(service => (
                                <option key={service.id} value={service.id}>
                                    {service.name} - R{parseFloat(service.base_price).toFixed(2)} ({service.duration_value} {service.duration_unit})
                                </option>
                            ))}
                        </select>
                    </div>

                    {selectedService && (
                        <div className="service-info">
                            <p>{selectedService.description}</p>
                            <p><strong>Price:</strong> R{parseFloat(selectedService.base_price).toFixed(2)}</p>
                        </div>
                    )}
                </div>

                {/* Booking Dates */}
                <div className="form-section">
                    <h3>📅 Booking Period</h3>
                    <div className="form-row">
                        <div className="form-group">
                            <label className="form-label">Start Date & Time *</label>
                            <input
                                type="datetime-local"
                                name="booking_start"
                                className="form-control"
                                value={formData.booking_start}
                                onChange={handleChange}
                                required
                            />
                        </div>
                        <div className="form-group">
                            <label className="form-label">End Date & Time *</label>
                            <input
                                type="datetime-local"
                                name="booking_end"
                                className="form-control"
                                value={formData.booking_end}
                                onChange={handleChange}
                                required
                            />
                        </div>
                    </div>
                </div>

                {/* Payment Method */}
                <div className="form-section">
                    <h3>💳 Payment Method</h3>
                    <PaymentMethodSelector
                        paymentMethods={paymentMethods}
                        selectedId={formData.payment_method_id}
                        onSelect={(id) => setFormData(prev => ({ ...prev, payment_method_id: id }))}
                    />
                </div>

                {/* Additional Notes */}
                <div className="form-section">
                    <h3>📝 Additional Notes (Optional)</h3>
                    <div className="form-group">
                        <textarea
                            name="notes"
                            className="form-control"
                            value={formData.notes}
                            onChange={handleChange}
                            placeholder="Any special requirements or notes for your booking..."
                            rows="4"
                        />
                    </div>
                </div>

                <button type="submit" className="btn btn-primary btn-large" disabled={loading}>
                    {loading ? (
                        <>
                            <div className="spinner"></div> Processing...
                        </>
                    ) : (
                        <>✨ Confirm Booking</>
                    )}
                </button>
            </form>
        </div>
    );
}

export default BookingForm;
