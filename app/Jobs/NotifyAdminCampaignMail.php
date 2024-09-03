<?php

namespace App\Jobs;

use App\Mail\GenericMail;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotifyAdminCampaignMail implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    protected $subject;
    /**
     * Create a new job instance.
     */
    public function __construct( $subject)
    {
        $this->subject = $subject;
    }

    /**
     * Execute the job.
     * @throws Exception
     */
    public function handle(): void
    {
        $data = [
            'subject' => $this->subject,
            'template' => 'emails.notify_admin_campaign',
        ];
        try {

         Mail::to('admin@gmail.com')->send(new GenericMail((object) $data, []));
        } catch (\Exception $exception) {
            Log::error('Email sending failed: ' . $exception->getMessage());
            throw new Exception($exception);
        }
    }
}
