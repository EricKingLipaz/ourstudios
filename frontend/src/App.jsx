import { BrowserRouter as Router, Routes, Route, Link } from 'react-router-dom';
import BookingForm from './components/BookingForm';
import AdminDashboard from './pages/admin/AdminDashboard';
import './App.css';

function App() {
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
                                <Link to="/admin" className="nav-link admin-link">Admin</Link>
                            </div>
                        </div>
                    </div>
                </nav>

                {/* Main Content */}
                <main className="main-content">
                    <Routes>
                        <Route path="/" element={<BookingForm />} />
                        <Route path="/admin" element={<AdminDashboard />} />
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
