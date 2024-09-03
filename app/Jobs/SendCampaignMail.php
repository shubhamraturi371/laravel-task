<?php

namespace App\Jobs;

use App\Mail\GenericMail;
use Exception;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendCampaignMail implements ShouldQueue
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    protected $subject;
    protected $name;
    protected $email;
    /**
     * Create a new job instance.
     */
    public function __construct( $subject, $name, $email)
    {
        $this->subject = $subject;
        $this->name = $name;
        $this->email = $email;
    }

    /**
     * Execute the job.
     * @throws Exception
     */
    public function handle()
    {
        $data = [
            'subject' => $this->subject,
            'template' => 'emails.campaign',
            'name' => $this->name,
        ];
        try {
         Mail::to($this->email)->send(new GenericMail((object) $data, []));
        } catch (\Exception $exception) {
            Log::error('Email sending failed: ' . $exception->getMessage());
            throw new Exception($exception);
        }
    }
}
