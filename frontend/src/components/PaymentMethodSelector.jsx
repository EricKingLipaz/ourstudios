import './PaymentMethodSelector.css';

function PaymentMethodSelector({ paymentMethods, selectedId, onSelect }) {
    const getIcon = (code) => {
        switch (code) {
            case 'bank_transfer':
                return '🏦';
            case 'eft':
                return '💳';
            case 'cash':
                return '💵';
            case 'cash_send':
                return '📱';
            default:
                return '💰';
        }
    };

    return (
        <div className="payment-method-selector">
            {paymentMethods.map(method => (
                <div
                    key={method.id}
                    className={`payment-card ${selectedId === method.id.toString() ? 'selected' : ''}`}
                    onClick={() => onSelect(method.id.toString())}
                >
                    <input
                        type="radio"
                        name="payment_method"
                        value={method.id}
                        checked={selectedId === method.id.toString()}
                        onChange={() => onSelect(method.id.toString())}
                    />
                    <div className="payment-icon">{getIcon(method.code)}</div>
                    <div className="payment-info">
                        <h4>{method.name}</h4>
                        <p>{method.description}</p>
                    </div>
                    <div className="selected-indicator">✓</div>
                </div>
            ))}
        </div>
    );
}

export default PaymentMethodSelector;
