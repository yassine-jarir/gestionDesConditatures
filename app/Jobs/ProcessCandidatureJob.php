<?php

namespace App\Jobs;

use App\Models\Candidature;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessCandidatureJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;
    protected $offreIds;
    protected $cvPath;

    /**
     * Create a new job instance.
     */
    public function __construct($userId, array $offreIds, $cvPath)
    {
        $this->userId = $userId;
        $this->offreIds = $offreIds;
        $this->cvPath = $cvPath;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Enregistrer plusieurs candidatures en boucle
        foreach ($this->offreIds as $offreId) {
            Candidature::create([
                'user_id' => $this->userId,
                'offre_id' => $offreId,
                'cv_path' => $this->cvPath,
            ]);
        }
    }
}
