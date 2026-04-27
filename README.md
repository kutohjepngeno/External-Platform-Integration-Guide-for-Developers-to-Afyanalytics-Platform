# Afyanalytics Integration – PHP Implementation

This service integrates with Afyanalytics Health Platform using a two‑step handshake (initiate → complete) and handles the 15‑minute token expiry.

## Features

- Initiates handshake with platform credentials  
- Completes handshake within expiry window  
- Automatic retry if handshake token expires  
- Logs handshake token, expiry time, success/failure  
- Simple token storage (PHP session – replace with DB/Redis in production)  

## Requirements

- PHP 7.4+ with cURL and JSON extensions  
- Web server (Apache/Nginx) or PHP built‑in server  

## Setup

1. **Clone the repository**  
   ```bash
   git clone https://github.com/kutohjepngeno/External-Platform-Integration-Guide-for-Developers-to-Afyanalytics-Platform.git
   cd afyanalytics-php
