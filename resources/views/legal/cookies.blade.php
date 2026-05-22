@php($title = __('Cookie Policy'))
<x-legal-layout :title="$title">
    <h1>{{ __('Cookie Policy') }}</h1>
    <p class="lead">{{ __('Pro Contact only uses cookies strictly necessary for the operation of the Service. No advertising, no tracking, no analytics cookies.') }}</p>

    <h2>{{ __('1. What is a cookie?') }}</h2>
    <p>{{ __('A cookie is a small file stored by your browser when you visit a website. It allows the site to remember information between visits (such as the fact that you are logged in).') }}</p>

    <h2>{{ __('2. Cookies we use') }}</h2>
    <table>
        <thead><tr><th>{{ __('Cookie') }}</th><th>{{ __('Purpose') }}</th><th>{{ __('Duration') }}</th></tr></thead>
        <tbody>
            <tr>
                <td><code>laravel_session</code></td>
                <td>{{ __('Keeps you logged in during your session.') }}</td>
                <td>{{ __('Session (deleted when you close the browser)') }}</td>
            </tr>
            <tr>
                <td><code>XSRF-TOKEN</code></td>
                <td>{{ __('Protects against cross-site request forgery (CSRF) attacks.') }}</td>
                <td>{{ __('Session') }}</td>
            </tr>
            <tr>
                <td><code>portal_td</code></td>
                <td>{{ __('Remembers your trusted device on the client portal, to avoid re-entering the verification code at every visit.') }}</td>
                <td>{{ __('60 days') }}</td>
            </tr>
            <tr>
                <td><code>locale</code> ({{ __('session') }})</td>
                <td>{{ __('Remembers the language you chose (EN / FR).') }}</td>
                <td>{{ __('Session') }}</td>
            </tr>
        </tbody>
    </table>

    <h2>{{ __('3. Consent') }}</h2>
    <p>{{ __('All cookies listed above are "strictly necessary" within the meaning of the ePrivacy Directive: they are required for the Service to work or are explicitly requested by you (login, language choice, trusted device). No prior consent is therefore required by law.') }}</p>

    <h2>{{ __('4. Third-party cookies') }}</h2>
    <p>{{ __('None. Pro Contact does not embed advertising, tracking, or third-party analytics scripts. Authentication via Google or Apple (optional) follows the redirect flow and does not place cookies on our domain.') }}</p>

    <h2>{{ __('5. How to manage cookies') }}</h2>
    <p>{{ __('You can delete the cookies stored by your browser at any time from its settings. Deleting them will log you out and may require you to re-verify your identity on the client portal.') }}</p>

    <h2>{{ __('6. Contact') }}</h2>
    <p>{{ __('For any question, write to :email.', ['email' => 'privacy@procontact.app']) }}</p>

    <p class="lead" style="margin-top: 2rem;">{{ __('Last updated: May 2026.') }}</p>
</x-legal-layout>
