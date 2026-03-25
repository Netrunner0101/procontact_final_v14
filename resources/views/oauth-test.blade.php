<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>OAuth Test - Pro Contact</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full bg-white rounded-lg shadow-md p-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900">OAuth Configuration Test</h1>
                <p class="text-gray-600 mt-2">Testing Google OAuth Setup</p>
            </div>

            <div class="space-y-4">
                <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <h3 class="font-semibold text-blue-900">Configuration Status:</h3>
                    <ul class="mt-2 text-sm text-blue-800">
                        <li>✅ Laravel Socialite: Installed</li>
                        <li>✅ Google Client ID: {{ config('services.google.client_id') ? 'Set' : 'Missing' }}</li>
                        <li>✅ Google Client Secret: {{ config('services.google.client_secret') ? 'Set' : 'Missing' }}</li>
                        <li>✅ Redirect URL: {{ config('services.google.redirect') }}</li>
                    </ul>
                </div>

                @if(config('services.google.client_id') === '123456789-demo.apps.googleusercontent.com')
                    <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <h3 class="font-semibold text-yellow-900">⚠️ Demo Credentials Active</h3>
                        <p class="mt-2 text-sm text-yellow-800">
                            You're using demo credentials. To enable actual Google OAuth:
                        </p>
                        <ol class="mt-2 text-sm text-yellow-800 list-decimal list-inside">
                            <li>Go to <a href="https://console.cloud.google.com/" class="underline" target="_blank">Google Cloud Console</a></li>
                            <li>Create OAuth 2.0 credentials</li>
                            <li>Update your .env file with real credentials</li>
                            <li>Add redirect URI: <code class="bg-yellow-100 px-1 rounded">{{ config('services.google.redirect') }}</code></li>
                        </ol>
                    </div>
                @endif

                <div class="space-y-3">
                    <a href="{{ route('auth.google') }}" class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition duration-200">
                        <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        Test Google OAuth
                    </a>

                    <a href="{{ route('login') }}" class="w-full flex items-center justify-center px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition duration-200">
                        Back to Login
                    </a>
                </div>
            </div>

            <div class="mt-6 text-xs text-gray-500 text-center">
                <p>For detailed setup instructions, see GOOGLE_OAUTH_SETUP.md</p>
            </div>
        </div>
    </div>
</body>
</html>
