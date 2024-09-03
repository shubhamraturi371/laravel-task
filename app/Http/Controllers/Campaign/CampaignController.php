<?php

namespace App\Http\Controllers\Campaign;

use App\Http\Controllers\Controller;
use App\Http\Requests\Campaign\StoreCampaignRequest;
use App\Jobs\NotifyAdminCampaignMail;
use App\Jobs\SendCampaignMail;
use Illuminate\Bus\Batch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class CampaignController extends Controller
{
    /**
     * @throws \Exception
     * @throws Throwable
     */

    public function store(StoreCampaignRequest $storeCampaignRequest)
    {
        $file = $storeCampaignRequest->file('file');
        $fileContents = file($file->getPathname());

        $capName = $storeCampaignRequest->input('campaignName');
        $chunkSize = 100; // Set your desired chunk size here

        // Remove the header row and chunk the remaining lines
        $lines = array_slice($fileContents, 1);
        $chunks = array_chunk($lines, $chunkSize);

        foreach ($chunks as $chunk) {
            $jobs = [];

            foreach ($chunk as $line) {
                $data = str_getcsv($line);
                $jobs[] = new SendCampaignMail($capName, $data[0], $data[1]);
            }
            Bus::batch($jobs)
                ->name($capName." job")
                ->then(function (Batch $batch) use ($capName) {
                    Log::info('Batch processing complete, dispatching admin notification.');
                    NotifyAdminCampaignMail::dispatch($capName);
                })
                ->catch(function (Batch $batch, Throwable $e) {
                    Log::error('Batch job failed: ' . $e->getMessage());
                })
                ->dispatch();
        }

        return response()->json(['message' => 'Jobs dispatched successfully']);
    }

    public function index()
    {
        return DB::table('job_batches')->get();

    }
}
