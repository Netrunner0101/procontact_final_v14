<x-legal-layout :title="__('Invalid link')">
    <h1>{{ __('Invalid or expired link') }}</h1>
    <p class="lead">{{ __('This consent link does not match any record. It may have already been used, revoked, or never existed.') }}</p>
    <p>{{ __('If you believe this is an error, please contact the professional who invited you, or write to :email.', ['email' => 'privacy@procontact.app']) }}</p>
</x-legal-layout>
