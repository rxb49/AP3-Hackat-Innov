<?php

namespace App\Utils;


use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;

class EmailHelpers
{
    public static function sendEmail($to, $subject, $view, $data, $attachmentPath = null)
    {
        Mail::send($view, $data, function ($message) use ($to, $subject, $attachmentPath) {
            $message->to($to)
                    ->subject($subject);

            // Si un fichier est fourni, ajoutez-le en pièce jointe
            if ($attachmentPath) {
                $message->attach($attachmentPath);
            }
        });
    }
}
