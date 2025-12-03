import { useState } from 'react';
import PaymentVerificationModal from './PaymentVerificationModal';
import './BookingsTable.css';

function BookingsTable({ bookings, onUpdate }) {
    const [selectedBooking, setSelectedBooking] = useState(null);
    const [showModal, setShowModal] = useState(false);

    const getStatusBadge = (status) => {
        const badges = {
            pending: 'badge-pending',
            paid: 'badge-paid',
            overdue: 'badge-overdue',
            cancelled: 'badge-cancelled',
        };
        return badges[status] || 'badge-pending';
    };

    const formatDate = (dateString) => {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-ZA', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        });
    };

    const handleVerifyClick = (booking) => {
        setSelectedBooking(booking);
        setShowModal(true);
    };

    const handleModalClose = () => {
        setShowModal(false);
        setSelectedBooking(null);
        onUpdate();
    };

    if (!bookings || bookings.length === 0) {
        return (
            <div className="no-bookings">
                <p>📭 No bookings found</p>
            </div>
        );
    }

    return (
        <>
            <div className="table-container">
                <table className="table bookings-table">
                    <thead>
                        <tr>
                            <th>Reference</th>
                            <th>Customer</th>
                            <th>Service</th>
                            <th>Amount</th>
                            <th>Payment Method</th>
                            <th>Payment Status</th>
                            <th>Booking Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {bookings.map(booking => (
                            <tr key={booking.id}>
                                <td className="reference-cell">
                                    <strong>{booking.booking_reference}</strong>
                                </td>
                                <td>
                                    <div className="customer-info">
                                        <div>{booking.customer_name}</div>
                                        <div className="email">{booking.customer_email}</div>
                                    </div>
                                </td>
                                <td>{booking.service?.name}</td>
                                <td className="amount-cell">
                                    R{parseFloat(booking.total_amount).toFixed(2)}
                                </td>
                                <td>{booking.payment_method?.name}</td>
                                <td>
                                    <span className={`badge ${getStatusBadge(booking.payment_status)}`}>
                                        {booking.payment_status}
                                    </span>
                                </td>
                                <td className="date-cell">
                                    {formatDate(booking.booking_start)}
                                </td>
                                <td>
                                    {booking.payment_status === 'pending' && (
                                        <button
                                            className="btn-action verify"
                                            onClick={() => handleVerifyClick(booking)}
                                            title="Verify Payment"
                                        >
                                            ✓ Verify
                                        </button>
                                    )}
                                    {booking.payment_status === 'paid' && (
                                        <span className="verified-text">✓ Verified</span>
                                    )}
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>

            {showModal && selectedBooking && (
                <PaymentVerificationModal
                    booking={selectedBooking}
                    onClose={handleModalClose}
                />
            )}
        </>
    );
}

export default BookingsTable;
