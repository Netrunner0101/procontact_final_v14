@php($title = __('Terms of Service'))
<x-legal-layout :title="$title">
    <h1>{{ __('Terms of Service') }}</h1>
    <p class="lead">{{ __('General Terms and Conditions of Use of the Pro Contact service.') }}</p>

    <h2>{{ __('1. Object') }}</h2>
    <p>{{ __('These terms govern your use of the Pro Contact CRM (the "Service"), operated by Bouncy Boring ESHL Tech SRL (Belgium). By creating an account, you accept these terms in full.') }}</p>

    <h2>{{ __('2. Account') }}</h2>
    <ul>
        <li>{{ __('You must be at least 16 years old to create an account.') }}</li>
        <li>{{ __('You are responsible for the accuracy of the information you provide.') }}</li>
        <li>{{ __('You are responsible for keeping your password confidential.') }}</li>
        <li>{{ __('One account per professional. Account sharing is not allowed.') }}</li>
    </ul>

    <h2>{{ __('3. Acceptable use') }}</h2>
    <p>{{ __('You agree not to use the Service to:') }}</p>
    <ul>
        <li>{{ __('Violate any applicable law or third-party rights.') }}</li>
        <li>{{ __('Send unsolicited communications (spam) to your contacts.') }}</li>
        <li>{{ __('Attempt to access data of other users.') }}</li>
        <li>{{ __('Attempt to disrupt the operation of the Service.') }}</li>
    </ul>

    <h2>{{ __('4. Your data') }}</h2>
    <p>{{ __('You retain ownership of all contact, appointment, and note data you create. We process this data on your behalf as a data processor when you act as a controller for your own clients. See the Privacy Policy for details.') }}</p>

    <h2>{{ __('5. Availability and limitations') }}</h2>
    <p>{{ __('The Service is provided "as is". We strive for high availability but do not guarantee uninterrupted service. Scheduled maintenance will be announced in advance when possible.') }}</p>

    <h2>{{ __('6. Termination') }}</h2>
    <p>{{ __('You can delete your account at any time from your profile. We may suspend or close an account that violates these terms after notice, except in cases of serious breach.') }}</p>

    <h2>{{ __('7. Liability') }}</h2>
    <p>{{ __('To the maximum extent permitted by law, our liability is limited to the amount you paid for the Service in the 12 months preceding the event giving rise to the claim.') }}</p>

    <h2>{{ __('8. Modifications') }}</h2>
    <p>{{ __('We may update these terms. Material changes will be notified by email at least 30 days before they take effect.') }}</p>

    <h2>{{ __('9. Governing law') }}</h2>
    <p>{{ __('These terms are governed by Belgian law. Any dispute falls under the exclusive jurisdiction of the courts of Brussels, subject to mandatory consumer protection rules.') }}</p>

    <h2>{{ __('10. Contact') }}</h2>
    <p>{{ __('For any question, write to :email.', ['email' => 'contact@procontact.app']) }}</p>

    <p class="lead" style="margin-top: 2rem;">{{ __('Last updated: May 2026.') }}</p>
</x-legal-layout>
