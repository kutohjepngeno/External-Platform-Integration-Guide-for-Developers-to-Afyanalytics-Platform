# Afyanalytics Platform Integration

This service integrates with the Afyanalytics Health Platform using a secure two‑step handshake.

## Features

- Initiates handshake (`/initiate-handshake`) and retrieves a short‑lived handshake token.
- Completes handshake (`/complete-handshake`) within the 15‑minute expiry window.
- Automatic retry if handshake token expires.
- Full logging of handshake token, expiry time, and success/failure.
- Secure token storage (in‑memory for demo; replace with DB/Redis in production).

## Setup

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/afyanalytics-integration.git
   cd afyanalytics-integration
