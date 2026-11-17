<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

/**
 * Book details by ISBN from the Google Books API (the same source the Acquisitions form uses).
 * Results are cached for 30 days so re-importing a file does not repeat the requests.
 */
class GoogleBooks
{
    /** @return array<string,string>|null  title, author, publisher, published, category, description, cover, pages, language, isbn_10, isbn_13 */
    public static function lookup(string $isbn): ?array
    {
        $isbn = preg_replace('/[^0-9Xx]/', '', $isbn);
        if ($isbn === "") {
            return null;
        }
        $cached = Cache::get("google_books:".$isbn);
        if ($cached !== null) {
            return $cached ?: null;
        }

        $ch = curl_init("https://www.googleapis.com/books/v1/volumes?q=isbn:".urlencode($isbn));
        curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_CONNECTTIMEOUT => 3, CURLOPT_TIMEOUT => 6]);
        $body = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($body === false || $status !== 200) {
            return null; // network problem: do not cache, try again next time
        }

        $data = json_decode($body, true);
        $info = $data["items"][0]["volumeInfo"] ?? null;
        if (!$info) {
            Cache::put("google_books:".$isbn, [], now()->addDays(30)); // not found
            return null;
        }
        $ids = [];
        foreach ($info["industryIdentifiers"] ?? [] as $id) {
            $ids[$id["type"] ?? ""] = $id["identifier"] ?? "";
        }
        $result = [
            "title" => (string) ($info["title"] ?? ""),
            "author" => implode(", ", $info["authors"] ?? []),
            "publisher" => (string) ($info["publisher"] ?? ""),
            "published" => isset($info["publishedDate"]) ? substr((string) $info["publishedDate"], 0, 4) : "",
            "category" => implode(", ", $info["categories"] ?? []),
            "description" => (string) ($info["description"] ?? ""),
            "cover" => (string) ($info["imageLinks"]["smallThumbnail"] ?? ""),
            "pages" => isset($info["pageCount"]) ? (string) $info["pageCount"] : "",
            "language" => (string) ($info["language"] ?? ""),
            "isbn_10" => (string) ($ids["ISBN_10"] ?? ""),
            "isbn_13" => (string) ($ids["ISBN_13"] ?? ""),
        ];
        Cache::put("google_books:".$isbn, $result, now()->addDays(30));
        return $result;
    }
}
