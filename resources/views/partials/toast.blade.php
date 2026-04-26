@php
    $toastSuccess = session('success');
    $toastError = session('error');
    $toastWarning = session('warning');
    $toastInfo = session('info');
    $toastValidation = $errors->any() ? $errors->first() : null;
@endphp

@if ($toastSuccess || $toastError || $toastWarning || $toastInfo || $toastValidation)
    <div id="toast-stack"
         class="fixed top-4 right-4 z-[9999] flex flex-col gap-2 pointer-events-none"
         style="max-width: 22rem;">
        @if ($toastSuccess)
            <div class="toast pointer-events-auto" data-toast-type="success" role="status" aria-live="polite">
                <i class="fas fa-check-circle toast-icon"></i>
                <span class="toast-message">{{ $toastSuccess }}</span>
                <button type="button" class="toast-close" aria-label="{{ __('Close') }}">&times;</button>
            </div>
        @endif

        @if ($toastError)
            <div class="toast pointer-events-auto" data-toast-type="error" role="alert" aria-live="assertive">
                <i class="fas fa-exclamation-circle toast-icon"></i>
                <span class="toast-message">{{ $toastError }}</span>
                <button type="button" class="toast-close" aria-label="{{ __('Close') }}">&times;</button>
            </div>
        @endif

        @if ($toastValidation && !$toastError)
            <div class="toast pointer-events-auto" data-toast-type="error" role="alert" aria-live="assertive">
                <i class="fas fa-exclamation-circle toast-icon"></i>
                <span class="toast-message">{{ $toastValidation }}</span>
                <button type="button" class="toast-close" aria-label="{{ __('Close') }}">&times;</button>
            </div>
        @endif

        @if ($toastWarning)
            <div class="toast pointer-events-auto" data-toast-type="warning" role="status" aria-live="polite">
                <i class="fas fa-triangle-exclamation toast-icon"></i>
                <span class="toast-message">{{ $toastWarning }}</span>
                <button type="button" class="toast-close" aria-label="{{ __('Close') }}">&times;</button>
            </div>
        @endif

        @if ($toastInfo)
            <div class="toast pointer-events-auto" data-toast-type="info" role="status" aria-live="polite">
                <i class="fas fa-circle-info toast-icon"></i>
                <span class="toast-message">{{ $toastInfo }}</span>
                <button type="button" class="toast-close" aria-label="{{ __('Close') }}">&times;</button>
            </div>
        @endif
    </div>

    <style>
        #toast-stack .toast {
            display: flex;
            align-items: flex-start;
            gap: 0.625rem;
            padding: 0.75rem 0.875rem;
            border-radius: 0.625rem;
            font-size: 0.8125rem;
            font-weight: 500;
            line-height: 1.3;
            box-shadow: 0 8px 24px rgba(15, 17, 21, 0.12), 0 2px 6px rgba(15, 17, 21, 0.06);
            backdrop-filter: blur(8px);
            border: 1px solid transparent;
            transform: translateX(120%);
            opacity: 0;
            transition: transform 320ms cubic-bezier(0.22, 1, 0.36, 1), opacity 320ms ease;
            min-width: 16rem;
        }
        #toast-stack .toast.toast-show {
            transform: translateX(0);
            opacity: 1;
        }
        #toast-stack .toast.toast-hide {
            transform: translateX(120%);
            opacity: 0;
        }
        #toast-stack .toast-icon {
            flex-shrink: 0;
            margin-top: 1px;
            font-size: 0.95rem;
        }
        #toast-stack .toast-message {
            flex: 1;
            word-break: break-word;
        }
        #toast-stack .toast-close {
            flex-shrink: 0;
            background: transparent;
            border: 0;
            color: inherit;
            opacity: 0.55;
            cursor: pointer;
            font-size: 1.1rem;
            line-height: 1;
            padding: 0 0.125rem;
            transition: opacity 150ms ease;
        }
        #toast-stack .toast-close:hover { opacity: 1; }

        #toast-stack .toast[data-toast-type="success"] {
            background: #ecfdf3;
            border-color: #b6efc8;
            color: #03543f;
        }
        #toast-stack .toast[data-toast-type="error"] {
            background: #fef2f2;
            border-color: #fbc6c2;
            color: #7f1d1d;
        }
        #toast-stack .toast[data-toast-type="warning"] {
            background: #fffaeb;
            border-color: #fde6a8;
            color: #7a4b00;
        }
        #toast-stack .toast[data-toast-type="info"] {
            background: #eff6ff;
            border-color: #c7dbff;
            color: #1e3a8a;
        }

        @media (max-width: 480px) {
            #toast-stack {
                left: 1rem;
                right: 1rem;
                max-width: none !important;
            }
        }
    </style>

    <script>
        (function () {
            const stack = document.getElementById('toast-stack');
            if (!stack) return;

            const AUTO_DISMISS_MS = 4500;

            const dismiss = (toast) => {
                if (!toast || toast.classList.contains('toast-hide')) return;
                toast.classList.remove('toast-show');
                toast.classList.add('toast-hide');
                toast.addEventListener('transitionend', () => toast.remove(), { once: true });
            };

            stack.querySelectorAll('.toast').forEach((toast, index) => {
                requestAnimationFrame(() => {
                    setTimeout(() => toast.classList.add('toast-show'), index * 80);
                });

                const closeBtn = toast.querySelector('.toast-close');
                if (closeBtn) closeBtn.addEventListener('click', () => dismiss(toast));

                let timer = setTimeout(() => dismiss(toast), AUTO_DISMISS_MS + index * 80);
                toast.addEventListener('mouseenter', () => clearTimeout(timer));
                toast.addEventListener('mouseleave', () => {
                    timer = setTimeout(() => dismiss(toast), 1500);
                });
            });
        })();
    </script>
@endif
