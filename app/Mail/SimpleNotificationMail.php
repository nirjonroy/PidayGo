<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SimpleNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $subjectLine;
    public string $content;
    public ?string $fromAddress;
    public ?string $fromName;

    public function __construct(string $subjectLine, string $content, ?string $fromAddress = null, ?string $fromName = null)
    {
        $this->subjectLine = $subjectLine;
        $this->content = $content;
        $this->fromAddress = $fromAddress;
        $this->fromName = $fromName;
    }

    public function build()
    {
        if ($this->fromAddress) {
            $this->from($this->fromAddress, $this->fromName);
        }

        return $this->subject($this->subjectLine)
            ->view('emails.simple-notification', [
                'content' => $this->content,
            ]);
    }
}
