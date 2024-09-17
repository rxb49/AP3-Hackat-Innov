<?php

namespace App\Utils;


use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;

class EmailHelpers
{
    public static function sendEmail($to, $subject, $template, $data = [])
    {
        $body = View::make($template, $data)->render();

        Mail::send([], [], function ($message) use ($to, $subject, $body) {
            $message->to($to)
                ->subject($subject)
                ->html($body);
        });
    }
}
