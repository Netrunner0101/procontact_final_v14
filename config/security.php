<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Idle session timeout
    |--------------------------------------------------------------------------
    |
    | After SESSION_IDLE_MINUTES of inactivity, the front-end shows a modal
    | with a SESSION_IDLE_GRACE_SECONDS countdown. If the user does not
    | confirm "stay signed in" before the countdown ends, the next request
    | is rejected by the EnforceIdleTimeout middleware which logs them out.
    |
    | The server enforces idle_minutes + grace_seconds (i.e. the modal grace
    | period is part of the allowed window). Client-side enforcement is for
    | UX only; the server check is the security boundary.
    |
    */

    'idle_minutes' => (int) env('SESSION_IDLE_MINUTES', 10),
    'idle_grace_seconds' => (int) env('SESSION_IDLE_GRACE_SECONDS', 60),

];
