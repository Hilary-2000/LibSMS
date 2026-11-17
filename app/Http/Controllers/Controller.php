<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Cache;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /** Notification data for the top bar (provided by the "notifications" middleware). */
    protected function layoutData(\Illuminate\Http\Request $request, array $data = []): array
    {
        return array_merge([
            "notifications" => $request->input("notifications") != null ? $request->input("notifications") : [],
            "notification_count" => $request->input("notification_count"),
        ], $data);
    }

    /**
     * Does this URL answer with a 2xx/3xx status?
     *
     * Used to decide whether to show a book cover / profile picture or a placeholder. The answer is
     * cached (7 days when the link works, 1 hour when it does not) because the check is a network
     * request: without the cache the catalogue made one request per book on every page view.
     */
    function isLinkValid($url) {
        $url = trim((string) $url);
        if ($url === "" || !preg_match('#^https?://#i', $url) || str_ends_with($url, "/sims/")) {
            return false; // empty, local path or "no picture" - nothing to check
        }

        $key = "link_ok:".sha1($url);
        $cached = Cache::get($key);
        if ($cached !== null) {
            return (bool) $cached;
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true); // No body needed, only header
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow redirects
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3); // Timeout after 3 seconds
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the result as a string
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Don't verify SSL certificate
        curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $ok = ($http_code >= 200 && $http_code < 400);
        Cache::put($key, $ok ? 1 : 0, $ok ? now()->addDays(7) : now()->addHour());
        return $ok;
    }
}
