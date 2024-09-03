<?php

namespace App\Mail;

use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;

class GenericMail extends Mailable
{
    // Declare the typed property
    public object $eventData;
    public array $mailAttachments;
// Constructor to initialize the property

    /**
     * @param object $eventData
     * @param array $mailAttachments
     */
    public function __construct(object $eventData, array $mailAttachments = [])
    {
        $this->eventData = $eventData;
        $this->mailAttachments = $mailAttachments;
    }

    /**
     * @return GenericMail
     */
    public function build(): GenericMail
    {
        return $this->subject($this->eventData->subject)
            ->markdown($this->eventData->template, ['data' => $this->eventData]);
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        // using attachment from raw data for now
        $attachments = [];

        foreach ($this->mailAttachments as $mailAttachment) {
            $attachments[] = Attachment::fromData(fn() => $mailAttachment['data'], $mailAttachment['name'])
                ->withMime($mailAttachment['mime_type']);
        }
        return $attachments;
    }
}
