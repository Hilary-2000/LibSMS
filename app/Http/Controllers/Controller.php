<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    function isLinkValid($url) {
        // Initialize cURL session
        $ch = curl_init($url);
        
        // Set cURL options
        curl_setopt($ch, CURLOPT_NOBODY, true); // No body needed, only header
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow redirects
        curl_setopt($ch, CURLOPT_TIMEOUT, 3); // Timeout after 3 seconds
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the result as a string
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Don't verify SSL certificate
        
        // Execute cURL session
        curl_exec($ch);
        
        // Get the HTTP response code
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        // Close cURL session
        curl_close($ch);
        
        // Return true if the HTTP code is 200, indicating success
        return ($http_code >= 200 && $http_code < 400);
    }
}
