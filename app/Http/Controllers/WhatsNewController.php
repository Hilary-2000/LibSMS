<?php

namespace App\Http\Controllers;

use App\Services\WhatsNew;

class WhatsNewController extends Controller
{
    /** Called when the user closes the "What's new" window: do not open it by itself again. */
    function seen()
    {
        WhatsNew::markSeen();
        return response()->json(["ok" => true]);
    }
}
