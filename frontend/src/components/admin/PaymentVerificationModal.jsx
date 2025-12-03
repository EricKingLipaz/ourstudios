import { useState } from 'react';
import { verifyPayment } from '../../services/api';
import './PaymentVerificationModal.css';

function PaymentVerificationModal({ booking, onClose }) {
    const [notes, setNotes] = useState('');
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);

    const handleVerify = async () => {
        setLoading(true);
        setError(null);

        try {
            await verifyPayment(booking.payment.id, notes);
            alert('Payment verified successfully! ✓');
            onClose();
        } catch (err) {
            setError(err.response?.data?.message || 'Failed to verify payment');
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="modal-overlay" onClick={onClose}>
            <div className="modal-content" onClick={(e) => e.stopPropagation()}>
                <div className="modal-header">
                    <h2>💳 Verify Payment</h2>
                    <button className="close-btn" onClick={onClose}>✕</button>
                </div>

                <div className="modal-body">
                    {error && <div className="alert alert-danger">{error}</div>}

                    <div className="booking-info">
                        <h3>Booking Details</h3>
                        <div className="info-grid">
                            <div className="info-item">
                                <span className="label">Reference:</span>
                                <span className="value">{booking.booking_reference}</span>
                            </div>
                            <div className="info-item">
                                <span className="label">Customer:</span>
                                <span className="value">{booking.customer_name}</span>
                            </div>
                            <div className="info-item">
                                <span className="label">Email:</span>
                                <span className="value">{booking.customer_email}</span>
                            </div>
                            <div className="info-item">
                                <span className="label">Service:</span>
                                <span className="value">{booking.service?.name}</span>
                            </div>
                            <div className="info-item">
                                <span className="label">Amount:</span>
                                <span className="value amount">R{parseFloat(booking.total_amount).toFixed(2)}</span>
                            </div>
                            <div className="info-item">
                                <span className="label">Payment Method:</span>
                                <span className="value">{booking.payment_method?.name}</span>
                            </div>
                        </div>
                    </div>

                    {booking.payment?.proof_of_payment_path && (
                        <div className="proof-section">
                            <h3>Proof of Payment</h3>
                            <img
                                src={`http://localhost:8000/storage/${booking.payment.proof_of_payment_path}`}
                                alt="Proof of Payment"
                                className="proof-image"
                            />
                        </div>
                    )}

                    {booking.payment?.cash_send_reference && (
                        <div className="cash-send-ref">
                            <h3>Cash Send Reference</h3>
                            <p className="reference">{booking.payment.cash_send_reference}</p>
                        </div>
                    )}

                    <div className="notes-section">
                        <h3>Admin Notes (Optional)</h3>
                        <textarea
                            className="form-control"
                            value={notes}
                            onChange={(e) => setNotes(e.target.value)}
                            placeholder="Add any notes about this payment verification..."
                            rows="3"
                        />
                    </div>
                </div>

                <div className="modal-footer">
                    <button className="btn btn-outline" onClick={onClose} disabled={loading}>
                        Cancel
                    </button>
                    <button className="btn btn-success" onClick={handleVerify} disabled={loading}>
                        {loading ? (
                            <>
                                <div className="spinner"></div> Verifying...
                            </>
                        ) : (
                            <>✓ Verify Payment</>
                        )}
                    </button>
                </div>
            </div>
        </div>
    );
}

export default PaymentVerificationModal;
