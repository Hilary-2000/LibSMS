{{--
    "What's new" window. Content: config/whats_new.php. Opened from the top-bar link, and opens by itself
    once per user after each release (App\Services\WhatsNew); closing it records that it was seen.
--}}
@php
    $whatsNew = config('whats_new');
    $whatsNewUnseen = \App\Services\WhatsNew::unseen();
@endphp
<div class="modal fade" id="whatsNewModal" tabindex="-1" aria-labelledby="whatsNewTitle" aria-hidden="true" data-auto-open="{{ $whatsNewUnseen ? '1' : '0' }}">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title d-flex align-items-center gap-2" id="whatsNewTitle"><i class="bx bx-gift text-primary font-size-22"></i> {{ $whatsNew['title'] }}</h5>
                    <div class="small text-muted mt-1">{{ $whatsNew['intro'] }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @foreach ($whatsNew['sections'] as $section)
                    <div class="whats-new-section {{ !$loop->last ? 'mb-4' : '' }}">
                        <h6 class="d-flex align-items-center gap-2 mb-2"><i class="bx {{ $section['icon'] }} text-primary font-size-18"></i> {{ $section['title'] }}</h6>
                        <ul class="list-unstyled mb-0">
                            @foreach ($section['items'] as $item)
                                <li class="d-flex gap-2 py-1">
                                    <i class="bx bx-check-circle text-success mt-1 flex-shrink-0"></i>
                                    <span>
                                        {!! $item['text'] !!}
                                        @if (!empty($item['link']))
                                            <a href="{{ $item['link'] }}" class="ms-1 text-nowrap">Open <i class="bx bx-right-arrow-alt"></i></a>
                                        @endif
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
            <div class="modal-footer justify-content-between">
                <span class="small text-muted">You can open this again any time from <b>What's new</b> in the top bar.</span>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Got it</button>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        var el = document.getElementById("whatsNewModal");
        if (!el || !window.bootstrap) return;
        // in the page body, outside the top bar, so it layers correctly
        document.body.appendChild(el);
        var marked = false;
        el.addEventListener("hidden.bs.modal", function () {
            if (marked || el.getAttribute("data-auto-open") !== "1") return;
            marked = true;
            el.setAttribute("data-auto-open", "0");
            document.querySelectorAll("[data-whats-new-dot]").forEach(function (d) { d.remove(); });
            var token = document.querySelector('meta[name="csrf-token"]');
            fetch("/Whats-new/Seen", {
                method: "POST",
                headers: { "X-CSRF-TOKEN": token ? token.content : "{{ csrf_token() }}", "Accept": "application/json" },
                credentials: "same-origin"
            }).catch(function () {});
        });
        if (el.getAttribute("data-auto-open") === "1") {
            bootstrap.Modal.getOrCreateInstance(el).show();
        }
    });
</script>
