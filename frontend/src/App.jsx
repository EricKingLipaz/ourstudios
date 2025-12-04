import { useState, useEffect } from 'react';
import { BrowserRouter as Router, Routes, Route, Link, Navigate } from 'react-router-dom';
import BookingForm from './components/BookingForm';
import AdminDashboard from './pages/admin/AdminDashboard';
import AdminLogin from './pages/admin/AdminLogin';
import './App.css';

function App() {
    const [adminUser, setAdminUser] = useState(null);

    // Check if user is already logged in
    useEffect(() => {
        const storedUser = localStorage.getItem('admin_user');
        if (storedUser) {
            setAdminUser(JSON.parse(storedUser));
        }
    }, []);

    const handleLogin = (user) => {
        setAdminUser(user);
    };

    const handleLogout = () => {
        localStorage.removeItem('admin_user');
        setAdminUser(null);
    };

    // Protected Route wrapper
    const ProtectedRoute = ({ children }) => {
        if (!adminUser) {
            return <Navigate to="/admin/login" replace />;
        }
        return children;
    };

    return (
        <Router>
            <div className="app">
                {/* Navigation */}
                <nav className="navbar">
                    <div className="container">
                        <div className="nav-content">
                            <Link to="/" className="logo">
                                <span className="logo-icon">🎵</span>
                                <span className="logo-text">LiveNetStudios</span>
                            </Link>
                            <div className="nav-links">
                                <Link to="/" className="nav-link">Book Now</Link>
                                {adminUser && (
                                    <button onClick={handleLogout} className="nav-link logout-btn">
                                        Logout
                                    </button>
                                )}
                            </div>
                        </div>
                    </div>
                </nav>

                {/* Main Content */}
                <main className="main-content">
                    <Routes>
                        <Route path="/" element={<BookingForm />} />
                        <Route path="/admin/login" element={<AdminLogin onLogin={handleLogin} />} />
                        <Route
                            path="/admin"
                            element={
                                <ProtectedRoute>
                                    <AdminDashboard onLogout={handleLogout} adminUser={adminUser} />
                                </ProtectedRoute>
                            }
                        />
                    </Routes>
                </main>

                {/* Footer */}
                <footer className="footer">
                    <div className="container">
                        <p>&copy; {new Date().getFullYear()} LiveNetStudios. All rights reserved.</p>
                        <p>Professional Recording & Production</p>
                    </div>
                </footer>
            </div>
        </Router>
    );
}

export default App;

