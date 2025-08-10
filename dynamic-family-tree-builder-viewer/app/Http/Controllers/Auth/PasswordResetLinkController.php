<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Inertia\Inertia;
use Inertia\Response;

class PasswordResetLinkController extends Controller
{
    public function create(): Response
    {
        return Inertia::render("Auth/ForgotPassword", [
            "status" => session("status"),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            "email" => "required|email",
        ]);

        $status = Password::sendResetLink(
            $request->only("email")
        );

        return $status == Password::RESET_LINK_SENT
                    ? back()->with("status", __($status))
                    : back()->withInput($request->only("email"))
                            ->withErrors(["email" => __($status)]);
    }
}
