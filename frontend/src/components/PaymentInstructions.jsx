import { useState } from 'react';
import './PaymentInstructions.css';

function PaymentInstructions({ paymentMethod, bankDetails, bookingReference }) {
    const [copied, setCopied] = useState(false);

    if (!paymentMethod) return null;

    const copyToClipboard = (text) => {
        navigator.clipboard.writeText(text);
        setCopied(true);
        setTimeout(() => setCopied(false), 2000);
    };

    const renderBankTransferInstructions = () => (
        <div className="payment-instructions bank-transfer">
            <h3>💳 Bank Transfer / EFT Instructions</h3>
            <p>Please transfer the amount to the following account:</p>
            <div className="bank-details-grid">
                <div className="bank-detail-item">
                    <span className="label">Account Name:</span>
                    <span className="value">{bankDetails.account_holder_name}</span>
                </div>
                <div className="bank-detail-item">
                    <span className="label">Bank:</span>
                    <span className="value">{bankDetails.bank_name}</span>
                </div>
                <div className="bank-detail-item">
                    <span className="label">Account Number:</span>
                    <span className="value">
                        {bankDetails.account_number}
                        <button
                            className="copy-btn"
                            onClick={() => copyToClipboard(bankDetails.account_number)}
                            title="Copy to clipboard"
                        >
                            {copied ? '✓' : '📋'}
                        </button>
                    </span>
                </div>
                <div className="bank-detail-item">
                    <span className="label">Branch Code:</span>
                    <span className="value">{bankDetails.branch_code}</span>
                </div>
                <div className="bank-detail-item important">
                    <span className="label">Reference:</span>
                    <span className="value">
                        {bookingReference}
                        <button
                            className="copy-btn"
                            onClick={() => copyToClipboard(bookingReference)}
                            title="Copy to clipboard"
                        >
                            {copied ? '✓' : '📋'}
                        </button>
                    </span>
                </div>
            </div>
            <div className="important-note">
                <p><strong>⚠️ Important:</strong> Please email your Proof of Payment to <a href="mailto:diamondlipaz@gmail.com">diamondlipaz@gmail.com</a></p>
                <p>Include your booking reference <strong>{bookingReference}</strong> in the email.</p>
            </div>
        </div>
    );

    const renderCashSendInstructions = () => (
        <div className="payment-instructions cash-send">
            <h3>📱 Cash Send Instructions</h3>
            <p>Send cash using any of these services:</p>
            <ul className="cash-send-options">
                <li>🏦 FNB eWallet</li>
                <li>🏦 Absa CashSend</li>
                <li>🏦 Standard Bank Instant Money</li>
                <li>🏦 Nedbank Send-iMali</li>
            </ul>
            <div className="bank-details-grid">
                <div className="bank-detail-item important">
                    <span className="label">Mobile Number:</span>
                    <span className="value">
                        {bankDetails.cash_send_mobile}
                        <button
                            className="copy-btn"
                            onClick={() => copyToClipboard(bankDetails.cash_send_mobile)}
                            title="Copy to clipboard"
                        >
                            {copied ? '✓' : '📋'}
                        </button>
                    </span>
                </div>
                <div className="bank-detail-item important">
                    <span className="label">Reference:</span>
                    <span className="value">{bookingReference}</span>
                </div>
            </div>
            <div className="important-note">
                <p><strong>⚠️ Important:</strong> After sending, please email the withdrawal PIN/reference to <a href="mailto:diamondlipaz@gmail.com">diamondlipaz@gmail.com</a></p>
            </div>
        </div>
    );

    const renderCashInstructions = () => (
        <div className="payment-instructions cash">
            <h3>💵 Cash Payment Instructions</h3>
            <p>Please bring cash to the studio on the day of your booking.</p>
            <div className="important-note">
                <p><strong>⚠️ Important:</strong> Payment must be made before the session begins.</p>
                <p>Studio Address: <em>(Please contact us for address details)</em></p>
            </div>
        </div>
    );

    return (
        <div className="payment-instructions-container">
            {(paymentMethod.code === 'bank_transfer' || paymentMethod.code === 'eft') &&
                renderBankTransferInstructions()}
            {paymentMethod.code === 'cash_send' && renderCashSendInstructions()}
            {paymentMethod.code === 'cash' && renderCashInstructions()}
        </div>
    );
}

export default PaymentInstructions;
