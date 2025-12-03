import { useState, useEffect } from 'react';
import { getBookings, getBookingStatistics } from '../../services/api';
import BookingsTable from '../../components/admin/BookingsTable';
import './AdminDashboard.css';

function AdminDashboard() {
    const [stats, setStats] = useState(null);
    const [bookings, setBookings] = useState([]);
    const [loading, setLoading] = useState(true);
    const [filter, setFilter] = useState({
        payment_status: '',
        search: '',
    });

    useEffect(() => {
        fetchData();
    }, [filter]);

    const fetchData = async () => {
        try {
            const [statsRes, bookingsRes] = await Promise.all([
                getBookingStatistics(),
                getBookings(filter),
            ]);
            setStats(statsRes.data);
            setBookings(bookingsRes.data.data);
        } catch (error) {
            console.error('Error fetching dashboard data:', error);
        } finally {
            setLoading(false);
        }
    };

    const handleFilterChange = (e) => {
        const { name, value } = e.target;
        setFilter(prev => ({ ...prev, [name]: value }));
    };

    if (loading) {
        return (
            <div className="loading-container">
                <div className="spinner"></div>
                <p>Loading dashboard...</p>
            </div>
        );
    }

    return (
        <div className="admin-dashboard">
            <div className="dashboard-header">
                <h1>🎵 LiveNetStudios Admin</h1>
                <p>Manage bookings and payments</p>
            </div>

            {/* Statistics Cards */}
            {stats && (
                <div className="stats-grid">
                    <div className="stat-card total">
                        <div className="stat-icon">📊</div>
                        <div className="stat-info">
                            <h3>{stats.total_bookings}</h3>
                            <p>Total Bookings</p>
                        </div>
                    </div>

                    <div className="stat-card pending">
                        <div className="stat-icon">⏳</div>
                        <div className="stat-info">
                            <h3>{stats.pending_payment}</h3>
                            <p>Pending Payment</p>
                        </div>
                    </div>

                    <div className="stat-card paid">
                        <div className="stat-icon">✅</div>
                        <div className="stat-info">
                            <h3>{stats.paid}</h3>
                            <p>Paid</p>
                        </div>
                    </div>

                    <div className="stat-card overdue">
                        <div className="stat-icon">⚠️</div>
                        <div className="stat-info">
                            <h3>{stats.overdue}</h3>
                            <p>Overdue</p>
                        </div>
                    </div>

                    <div className="stat-card revenue">
                        <div className="stat-icon">💰</div>
                        <div className="stat-info">
                            <h3>R{parseFloat(stats.total_revenue || 0).toFixed(2)}</h3>
                            <p>Total Revenue</p>
                        </div>
                    </div>
                </div>
            )}

            {/* Filters */}
            <div className="filters-section">
                <h2>Filter Bookings</h2>
                <div className="filters-row">
                    <div className="filter-group">
                        <label>Payment Status</label>
                        <select
                            name="payment_status"
                            className="form-control"
                            value={filter.payment_status}
                            onChange={handleFilterChange}
                        >
                            <option value="">All Statuses</option>
                            <option value="pending">Pending</option>
                            <option value="paid">Paid</option>
                            <option value="overdue">Overdue</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>

                    <div className="filter-group">
                        <label>Search</label>
                        <input
                            type="text"
                            name="search"
                            className="form-control"
                            placeholder="Search by reference, name, or email..."
                            value={filter.search}
                            onChange={handleFilterChange}
                        />
                    </div>
                </div>
            </div>

            {/* Bookings Table */}
            <div className="bookings-section">
                <h2>Recent Bookings</h2>
                <BookingsTable bookings={bookings} onUpdate={fetchData} />
            </div>
        </div>
    );
}

export default AdminDashboard;
