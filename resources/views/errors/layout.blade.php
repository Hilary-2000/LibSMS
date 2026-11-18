{{--
    Shared layout for the error pages (resources/views/errors/{code}.blade.php), styled after the
    Skote template's pages-404 / pages-500 / pages-maintenance.

    Laravel renders these pages even when something deep in the app has failed, so this file is
    deliberately self-contained: no shared partials, no database, absolute asset paths (an error can
    happen at any URL depth), and every session lookup wrapped so a missing session never causes a
    second error.

    Sections a page can fill:
      code                    the full status code, read out to screen readers, e.g. "404"
      code_left / code_right  digits either side of the animated icon, e.g. "4" and "4"
      icon                    boxicons class for the middle digit, e.g. "bx-buoy bx-spin"
      title, message          heading and one or two sentences of plain-language explanation
      tips                    optional <li> items: what the person can try
      image                   optional illustration path (defaults to Skote's error image)
--}}
@php
    try {
        $signedIn = session()->has("user_id") && session("user_id") !== null;
    } catch (\Throwable $e) {
        $signedIn = false;
    }
    $home = $signedIn ? "/Dashboard" : "/";
    $homeLabel = $signedIn ? "Back to Dashboard" : "Go to Login";
    $homeIcon = $signedIn ? "bx-home-circle" : "bx-log-in";
@endphp
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="robots" content="noindex">
        <title>@yield('title') | Ladybird Library System</title>
        {{-- same theme choice as the rest of the app, read from this browser only (no session needed) --}}
        <script>
            (function () {
                var mode = "light";
                try { mode = localStorage.getItem("ladybird-library-theme") || "light"; } catch (e) {}
                if (mode === "system") mode = window.matchMedia && window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
                document.documentElement.setAttribute("data-bs-theme", mode === "dark" ? "dark" : "light");
            })();
        </script>
        <link rel="icon" type="image/png" href="/images/ladybird-favicon.png?v=4">
        <link rel="shortcut icon" href="/favicon.ico?v=4">
        <link href="/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
        <link href="/assets/css/app.min.css" rel="stylesheet" type="text/css" />
        <style>
            body { min-height: 100vh; background: var(--bs-body-bg); }
            .error-wrap { min-height: 100vh; display: flex; align-items: center; padding: 40px 0; }
            .error-brand img { height: 44px; }
            .error-brand .on-dark { display: none; }
            [data-bs-theme="dark"] .error-brand .on-light { display: none; }
            [data-bs-theme="dark"] .error-brand .on-dark { display: inline; }
            .error-code { font-size: clamp(64px, 14vw, 112px); line-height: 1; font-weight: 600; letter-spacing: 2px; color: var(--bs-emphasis-color); }
            .error-code i { font-size: .82em; vertical-align: -0.06em; margin: 0 2px; }
            .error-title { letter-spacing: .06em; }
            .error-message { max-width: 520px; margin-left: auto; margin-right: auto; color: var(--bs-secondary-color); font-size: 15px; }
            .error-tips { max-width: 460px; margin: 0 auto; text-align: left; }
            .error-tips li { display: flex; gap: 10px; align-items: flex-start; padding: 6px 0; color: var(--bs-body-color); }
            .error-tips li i { color: var(--bs-primary); font-size: 18px; flex-shrink: 0; margin-top: 1px; }
            .error-image { max-width: 440px; width: 100%; margin: 0 auto; display: block; }
            .error-ref { font-size: 12px; color: var(--bs-secondary-color); }
            @media (prefers-reduced-motion: reduce) { .bx-spin, .bx-tada, .bx-flashing, .bx-burst { animation: none !important; } }
        </style>
    </head>

    <body>
        <div class="error-wrap">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xl-7 col-lg-8 col-md-10">
                        <div class="text-center">
                            <a href="{{ $home }}" class="error-brand d-inline-block mb-4" aria-label="Ladybird Library home">
                                <img class="on-light" src="/images/brand/ladybird-logo-on-light.png" alt="Ladybird">
                                <img class="on-dark" src="/images/brand/ladybird-logo-on-dark.png" alt="Ladybird">
                            </a>

                            <h1 class="error-code mb-3" aria-label="Error @yield('code')">
                                <span aria-hidden="true">@yield('code_left')<i class="bx @yield('icon') text-primary"></i>@yield('code_right')</span>
                            </h1>
                            <h4 class="error-title text-uppercase mb-3">@yield('title')</h4>
                            <p class="error-message mb-4">@yield('message')</p>

                            @hasSection('tips')
                                <ul class="list-unstyled error-tips mb-4">
                                    @yield('tips')
                                </ul>
                            @endif

                            <div class="d-flex flex-wrap justify-content-center gap-2 mb-4">
                                @hasSection('actions')
                                    @yield('actions')
                                @else
                                    <a class="btn btn-primary waves-effect waves-light" href="{{ $home }}"><i class="bx {{ $homeIcon }} me-1"></i> {{ $homeLabel }}</a>
                                    <button type="button" class="btn btn-outline-secondary" onclick="history.length > 1 ? history.back() : location.assign('{{ $home }}')"><i class="bx bx-arrow-back me-1"></i> Go back</button>
                                @endif
                            </div>
                        </div>

                        <img src="@yield('image', '/assets/images/error-img.png')" alt="" class="error-image img-fluid">

                        <p class="error-ref text-center mt-4 mb-0">
                            @yield('reference')
                            &copy; {{ date('Y') }} Ladybird Library System
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
