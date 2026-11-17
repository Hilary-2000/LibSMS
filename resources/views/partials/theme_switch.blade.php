{{-- Light / Dark / System switch (top bar and login page). theme.js saves the choice and applies it. --}}
<div class="theme-switch" role="group" aria-label="Colour theme">
    <button type="button" data-theme-set="light" title="Light" aria-label="Light theme" aria-pressed="false"><i class="bx bx-sun"></i></button>
    <button type="button" data-theme-set="dark" title="Dark" aria-label="Dark theme" aria-pressed="false"><i class="bx bx-moon"></i></button>
    <button type="button" data-theme-set="system" title="Same as device" aria-label="Same as device" aria-pressed="false"><i class="bx bx-desktop"></i></button>
</div>
@once
    <style>
        .theme-switch { display: inline-flex; gap: 2px; padding: 2px; border-radius: 9px; border: 1px solid var(--bs-border-color); background: transparent; }
        .theme-switch button { display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 28px; border: 0; border-radius: 7px; background: transparent; color: var(--bs-secondary-color); font-size: 16px; line-height: 1; padding: 0; transition: background-color .15s, color .15s; }
        .theme-switch button:hover { color: var(--bs-emphasis-color); }
        .theme-switch button:focus-visible { outline: 2px solid var(--bs-primary); outline-offset: 1px; }
        .theme-switch button[aria-pressed="true"] { background: var(--bs-tertiary-bg); color: var(--bs-emphasis-color); }
        .theme-toast { position: fixed; left: 50%; bottom: 24px; z-index: 2000; display: flex; align-items: center; gap: 10px; padding: 10px 16px; border-radius: 12px; font-size: 13px; font-weight: 600; background: var(--bs-secondary-bg); color: var(--bs-emphasis-color); border: 1px solid var(--bs-border-color); box-shadow: 0 10px 30px rgba(0,0,0,.18); transform: translate(-50%, 12px); opacity: 0; transition: transform .3s, opacity .3s; pointer-events: none; }
        .theme-toast.show { transform: translate(-50%, 0); opacity: 1; }
        .theme-toast i { font-size: 17px; color: var(--bs-primary); }
    </style>
    <script src="/assets/js/theme.js?v=2" defer></script>
@endonce
