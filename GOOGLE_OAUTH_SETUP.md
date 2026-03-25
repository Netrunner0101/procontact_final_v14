# Google OAuth Setup Guide

## Quick Setup Steps:

### 1. Go to Google Cloud Console
Visit: https://console.cloud.google.com/

### 2. Create or Select a Project
- Click "Select a project" → "New Project"
- Name: "Laravel Multi-Business Agenda"
- Click "Create"

### 3. Enable Google+ API
- Go to "APIs & Services" → "Library"
- Search for "Google+ API"
- Click "Enable"

### 4. Create OAuth 2.0 Credentials
- Go to "APIs & Services" → "Credentials"
- Click "Create Credentials" → "OAuth 2.0 Client IDs"
- Choose "Web application"
- Name: "Laravel Social Auth"

### 5. Configure Authorized Redirect URIs
Add these URLs:
```
http://127.0.0.1:8000/auth/google/callback
http://localhost:8000/auth/google/callback
```

### 6. Copy Your Credentials
After creating, you'll get:
- Client ID (looks like: 123456789-abcdefg.apps.googleusercontent.com)
- Client Secret (looks like: GOCSPX-abcdefghijklmnop)

### 7. Update Your .env File
Add these lines to your .env file:
```env
GOOGLE_CLIENT_ID=your_client_id_here
GOOGLE_CLIENT_SECRET=your_client_secret_here
```

### 8. Clear Config Cache
Run: `php artisan config:clear`

## Alternative: Test with Demo Credentials

For testing purposes, you can use these demo credentials (limited functionality):

```env
GOOGLE_CLIENT_ID=123456789-demo.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=GOCSPX-demo-secret-key
```

**Note:** Demo credentials won't work for actual OAuth, but will prevent the "missing client_id" error.

## Troubleshooting

### Error: "invalid_request"
- Make sure GOOGLE_CLIENT_ID is set in .env
- Run `php artisan config:clear`
- Restart the server

### Error: "redirect_uri_mismatch"
- Check that your redirect URI in Google Console matches exactly
- Make sure to include both localhost and 127.0.0.1 variants

### Error: "access_denied"
- Check that Google+ API is enabled
- Verify your OAuth consent screen is configured

## Production Setup

For production, you'll need to:
1. Verify your domain in Google Console
2. Set up OAuth consent screen
3. Add production redirect URIs
4. Update APP_URL in .env to your production domain
