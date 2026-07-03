@extends('layouts.app')

@section('title', __('Subscription - Pro Contact'))

@section('content')
@php
    $subscription = $user->subscription('default');
    $onTrial = $subscription && $subscription->onTrial();
    $onGracePeriod = $subscription && $subscription->onGracePeriod();
    $active = $user->subscribed('default');
@endphp
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ __('Subscription') }}</h1>
            <p class="text-gray-600">{{ __('Manage your ProContact Pro subscription.') }}</p>
        </div>

        @if (session('success'))
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">{{ session('error') }}</div>
        @endif
        @if (request('checkout') === 'success')
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">{{ __('Thank you! Your subscription is now active.') }}</div>
        @elseif (request('checkout') === 'cancelled')
            <div class="mb-6 p-4 bg-yellow-100 border border-yellow-400 text-yellow-800 rounded-lg">{{ __('Checkout cancelled. You can subscribe whenever you are ready.') }}</div>
        @endif

        <div class="card">
            <div class="card-header">
                <h2 class="text-xl font-semibold text-gray-900">{{ __('ProContact Pro') }} — €19 / {{ __('month') }}</h2>
            </div>
            <div class="card-body space-y-6">

                {{-- Current status --}}
                <div>
                    <span class="text-sm font-semibold uppercase tracking-wide text-gray-500">{{ __('Status') }}</span>
                    <div class="mt-1">
                        @if ($onGracePeriod)
                            <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800">{{ __('Cancelled') }}</span>
                            <p class="mt-2 text-gray-600">{{ __('Your access continues until :date.', ['date' => optional($subscription->ends_at)->translatedFormat('d F Y')]) }}</p>
                        @elseif ($onTrial)
                            <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 text-blue-800">{{ __('Free trial') }}</span>
                            <p class="mt-2 text-gray-600">{{ __('Your free trial ends on :date, then €19/month.', ['date' => optional($subscription->trial_ends_at)->translatedFormat('d F Y')]) }}</p>
                        @elseif ($active)
                            <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800">{{ __('Active') }}</span>
                            <p class="mt-2 text-gray-600">{{ __('Billed €19 every month. Cancel anytime.') }}</p>
                        @else
                            <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold bg-gray-100 text-gray-700">{{ __('No active subscription') }}</span>
                        @endif
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex flex-wrap gap-3 pt-2">
                    @if (! $priceConfigured)
                        <p class="text-red-600">{{ __('Billing is not configured yet. Please set the Stripe price in your environment.') }}</p>
                    @elseif ($onGracePeriod)
                        <form method="POST" action="{{ route('billing.resume') }}">
                            @csrf
                            <button type="submit" class="btn btn-primary">{{ __('Resume subscription') }}</button>
                        </form>
                        <a href="{{ route('billing.portal') }}" class="btn btn-secondary">{{ __('Manage billing') }}</a>
                    @elseif ($active)
                        <a href="{{ route('billing.portal') }}" class="btn btn-primary">{{ __('Manage billing') }}</a>
                        <p class="text-sm text-gray-500 w-full">{{ __('Update your card, download invoices or cancel from the secure Stripe portal.') }}</p>
                    @else
                        <a href="{{ route('billing.checkout') }}" class="btn btn-primary">{{ __('Start your :days-day free trial', ['days' => $trialDays]) }}</a>
                        <p class="text-sm text-gray-500 w-full">{{ __('No commitment — cancel anytime.') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
