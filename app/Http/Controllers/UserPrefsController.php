<?php

namespace App\Http\Controllers;

use App\Services\UserPrefs;
use Illuminate\Http\Request;

class UserPrefsController extends Controller
{
    /** The Light / Dark / System switch saves the choice to the user's account. */
    function theme(Request $request)
    {
        $mode = (string) $request->input("mode");
        if (!in_array($mode, UserPrefs::THEMES, true)) {
            return response()->json(["ok" => false, "error" => "Unknown theme"], 422);
        }
        UserPrefs::set("theme", $mode);
        session()->put("theme_mode", $mode);
        return response()->json(["ok" => true, "mode" => $mode]);
    }
}
