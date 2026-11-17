{{-- Apply the Light / Dark / System theme before the page paints, so dark mode never flashes light.
     Logged in: the person's own saved choice (App\Services\UserPrefs) wins and is copied to this browser.
     Not logged in (login page): this browser's last choice. theme.js handles the switch buttons. --}}
@php
    $accountTheme = session("user_id") ? session("theme_mode") : null;
@endphp
<script>
    (function () {
        var KEY = "ladybird-library-theme", account = @json($accountTheme), mode = "light";
        try { mode = account || localStorage.getItem(KEY) || "light"; if (account) localStorage.setItem(KEY, account); } catch (e) { mode = account || "light"; }
        window.LIBRARY_THEME_SAVE = @json(session("user_id") ? ["url" => "/Preferences/Theme", "token" => csrf_token()] : null);
        if (mode === "system") mode = window.matchMedia && window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
        document.documentElement.setAttribute("data-bs-theme", mode === "dark" ? "dark" : "light");
    })();
</script>
