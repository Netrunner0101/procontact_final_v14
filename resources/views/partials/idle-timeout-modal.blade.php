@auth
    <script>
        window.IDLE_TIMEOUT_CONFIG = {
            idleSeconds: {{ (int) config('security.idle_minutes', 10) * 60 }},
            graceSeconds: {{ (int) config('security.idle_grace_seconds', 60) }},
        };
    </script>

    <div id="idle-timeout-modal"
         class="hidden fixed inset-0 z-[1000] items-center justify-center bg-black/50 px-4"
         role="dialog" aria-modal="true" aria-labelledby="idle-timeout-title">
        <div class="bg-white rounded-xl shadow-2xl max-w-sm w-full p-6">
            <h2 id="idle-timeout-title" class="text-lg font-semibold mb-2" style="color: #1b1c1a;">
                {{ __('Are you still there?') }}
            </h2>
            <p class="text-sm mb-4" style="color: #44483e;">
                {!! __('You will be signed out in <strong><span id="idle-timeout-countdown">:seconds</span></strong> seconds for your security.', ['seconds' => (int) config('security.idle_grace_seconds', 60)]) !!}
            </p>
            <div class="flex gap-2 justify-end">
                <button type="button" id="idle-timeout-logout"
                        class="px-4 py-2 text-sm font-medium rounded-md border"
                        style="border-color: rgba(197,200,185,0.4); color: #44483e; background: #ffffff;">
                    {{ __('Sign out') }}
                </button>
                <button type="button" id="idle-timeout-stay"
                        class="px-4 py-2 text-sm font-medium rounded-md text-white"
                        style="background: linear-gradient(135deg, #843728, #c4816e);">
                    {{ __('Stay signed in') }}
                </button>
            </div>
        </div>
    </div>
@endauth
