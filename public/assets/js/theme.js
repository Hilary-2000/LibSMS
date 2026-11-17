// Light / Dark / System colour theme (same behaviour as the college LMS switch).
// The choice is saved in this browser (localStorage "ladybird-library-theme") and, when logged in, on the
// person's account (POST /Preferences/Theme) so it follows them to any computer; "system" follows the
// device setting live. partials/theme_init applies it before the page paints; this file handles the
// buttons ([data-theme-set]), the confirmation toast, and tells charts to recolour ("library:themechange").
(function () {
    var STORAGE_KEY = "ladybird-library-theme";
    var root = document.documentElement;
    var media = window.matchMedia ? window.matchMedia("(prefers-color-scheme: dark)") : null;

    function storedMode() {
        try { return localStorage.getItem(STORAGE_KEY) || "light"; } catch (e) { return "light"; }
    }
    function effective(mode) {
        if (mode === "system") return media && media.matches ? "dark" : "light";
        return mode === "dark" ? "dark" : "light";
    }
    function iconFor(mode) {
        return mode === "light" ? "bx-sun" : mode === "dark" ? "bx-moon" : "bx-desktop";
    }
    function labelFor(mode) {
        return mode === "light" ? "Light mode activated" : mode === "dark" ? "Dark mode activated" : "Switched to system theme";
    }

    function apply(mode) {
        var theme = effective(mode);
        var changed = root.getAttribute("data-bs-theme") !== theme;
        root.setAttribute("data-bs-theme", theme);
        document.querySelectorAll("[data-theme-set]").forEach(function (b) {
            b.setAttribute("aria-pressed", b.getAttribute("data-theme-set") === mode ? "true" : "false");
        });
        if (changed) {
            document.dispatchEvent(new CustomEvent("library:themechange", { detail: { theme: theme, mode: mode } }));
        }
    }

    var toastTimer;
    function toast(mode) {
        var el = document.getElementById("themeToast");
        if (!el) {
            el = document.createElement("div");
            el.id = "themeToast";
            el.className = "theme-toast";
            el.setAttribute("role", "status");
            el.setAttribute("aria-live", "polite");
            document.body.appendChild(el);
        }
        el.innerHTML = '<i class="bx ' + iconFor(mode) + '"></i>' + labelFor(mode);
        clearTimeout(toastTimer);
        requestAnimationFrame(function () { el.classList.add("show"); });
        toastTimer = setTimeout(function () { el.classList.remove("show"); }, 2200);
    }

    function setMode(mode) {
        try { localStorage.setItem(STORAGE_KEY, mode); } catch (e) {}
        apply(mode);
        toast(mode);
        // logged in: remember it on the person's account so it follows them to other computers
        var save = window.LIBRARY_THEME_SAVE;
        if (save && window.fetch) {
            fetch(save.url, {
                method: "POST",
                credentials: "same-origin",
                headers: { "Content-Type": "application/x-www-form-urlencoded", "Accept": "application/json", "X-CSRF-TOKEN": save.token },
                body: "mode=" + encodeURIComponent(mode)
            }).catch(function () {});
        }
    }

    apply(storedMode());
    if (media) {
        var onChange = function () { if (storedMode() === "system") apply("system"); };
        media.addEventListener ? media.addEventListener("change", onChange) : media.addListener(onChange);
    }
    document.addEventListener("click", function (e) {
        var btn = e.target.closest ? e.target.closest("[data-theme-set]") : null;
        if (!btn) return;
        e.preventDefault();
        setMode(btn.getAttribute("data-theme-set"));
    });
    // another tab changed the theme
    window.addEventListener("storage", function (e) { if (e.key === STORAGE_KEY) apply(storedMode()); });

    window.LibraryTheme = { current: function () { return root.getAttribute("data-bs-theme"); }, set: setMode };
})();
