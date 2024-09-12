import React, { useState } from 'react';
import axios from 'axios';

const App = () => {
  const [email, setEmail] = useState('');
  const [otp, setOtp] = useState('');
  const [subject, setSubject] = useState('');
  const [message, setMessage] = useState('');
  const [status, setStatus] = useState('');
  const [step, setStep] = useState('email'); // 'email', 'otp', or 'compose'

  const sendOTP = async (e) => {
    e.preventDefault();
    setStatus('Sending OTP...');
    try {
      const response = await axios.post('http://localhost/gmail-sender/api/send_email.php', {
        action: 'send_otp',
        email
      });
      setStatus(response.data.message);
      setStep('otp');
    } catch (error) {
      setStatus('Error sending OTP: ' + (error.response?.data?.error || error.message));
    }
  };

  const verifyOTP = async (e) => {
    e.preventDefault();
    setStatus('Verifying OTP...');
    try {
      const response = await axios.post('http://localhost/gmail-sender/api/send_email.php', {
        action: 'verify_otp',
        otp
      });
      setStatus(response.data.message);
      setStep('compose');
    } catch (error) {
      setStatus('Error verifying OTP: ' + (error.response?.data?.error || error.message));
    }
  };

  const sendEmail = async (e) => {
    e.preventDefault();
    setStatus('Sending email...');
    try {
      const response = await axios.post('http://localhost/gmail-sender/api/send_email.php', {
        to: email,
        subject,
        message
      });
      setStatus(response.data.message);
      setSubject('');
      setMessage('');
      setStep('email');
    } catch (error) {
      setStatus('Error sending email: ' + (error.response?.data?.error || error.message));
    }
  };

  return (
    <div className="email-sender">
      <h1>Gmail Sender</h1>
      {step === 'email' && (
        <form onSubmit={sendOTP}>
          <div>
            <label htmlFor="email">Email:</label>
            <input
              type="email"
              id="email"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              required
            />
          </div>
          <button type="submit">Send OTP</button>
        </form>
      )}
      {step === 'otp' && (
        <form onSubmit={verifyOTP}>
          <div>
            <label htmlFor="otp">Enter OTP:</label>
            <input
              type="text"
              id="otp"
              value={otp}
              onChange={(e) => setOtp(e.target.value)}
              required
            />
          </div>
          <button type="submit">Verify OTP</button>
        </form>
      )}
      {step === 'compose' && (
        <form onSubmit={sendEmail}>
          <div>
            <label htmlFor="subject">Subject:</label>
            <input
              type="text"
              id="subject"
              value={subject}
              onChange={(e) => setSubject(e.target.value)}
              required
            />
          </div>
          <div>
            <label htmlFor="message">Message:</label>
            <textarea
              id="message"
              value={message}
              onChange={(e) => setMessage(e.target.value)}
              required
            />
          </div>
          <button type="submit">Send Email</button>
        </form>
      )}
      {status && <p className="status">{status}</p>}
    </div>
  );
};

export default App;