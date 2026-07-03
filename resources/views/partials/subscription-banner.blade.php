{{--
    Subscription status banner.

    Only rendered when billing is actually enforced (config billing.enforce),
    so local development and non-billing accounts never see it. Shows a trial
    countdown or a cancelled/grace-period notice; paying customers see nothing.
--}}
@auth
    @if (config('billing.enforce') && auth()->user()->isAdmin())
        @php($state = auth()->user()->billingState())

        @if ($state === 'trialing')
            @php($daysLeft = auth()->user()->proTrialDaysLeft())
            <div class="w-full" style="background: #e8f0fe; color: #1a3a6b;">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2.5 flex flex-wrap items-center justify-between gap-2 text-sm">
                    <span>
                        <i class="fas fa-gift mr-2"></i>
                        {{ trans_choice('{0}Your free trial ends today.|{1}Free trial: :count day left.|[2,*]Free trial: :count days left.', $daysLeft, ['count' => $daysLeft]) }}
                    </span>
                    <a href="{{ route('billing.index') }}" class="font-semibold underline hover:opacity-80">{{ __('Manage subscription') }}</a>
                </div>
            </div>
        @elseif ($state === 'grace')
            <div class="w-full" style="background: #fff4e5; color: #7a4f00;">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2.5 flex flex-wrap items-center justify-between gap-2 text-sm">
                    <span>
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        {{ __('Your subscription is cancelled and will end soon.') }}
                    </span>
                    <a href="{{ route('billing.index') }}" class="font-semibold underline hover:opacity-80">{{ __('Resume subscription') }}</a>
                </div>
            </div>
        @endif
    @endif
@endauth
