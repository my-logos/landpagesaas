<?php

namespace App\Jobs;

use App\Models\Page;
use App\Models\Product;
use App\Services\PageGenerationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class GenerateClassicPageContentJob implements ShouldQueue
{
    use Queueable;

    public $tries = 3;
    public $timeout = 120; // 2 minutes

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Page $page,
        public Product $product,
        public string $language,
        public ?string $apiKey = null
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(PageGenerationService $pageGenerationService): void
    {
        try {
            // Update status to processing
            $this->page->update([
                'generation_status' => 'processing',
                'generation_error' => null,
            ]);

            // Generate static content (description, features, FAQs)
            $result = $pageGenerationService->generateStaticContent(
                $this->product,
                $this->language,
                $this->apiKey
            );

            if (!$result['success']) {
                throw new \Exception($result['message'] ?? 'Failed to generate content');
            }

            // Get existing content and merge with AI-generated content
            $existingContent = $this->page->content;
            $existingData = is_string($existingContent) ? json_decode($existingContent, true) : ($existingContent ?? []);

            // Merge AI content with existing content
            $mergedContent = array_merge($existingData, $result['content']);
            $mergedContent['language'] = $this->language;

            // Update page with generated content (use JSON_UNESCAPED_UNICODE to preserve Arabic text)
            $this->page->update([
                'generation_status' => 'completed',
                'content' => json_encode($mergedContent, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'generation_error' => null,
            ]);
        } catch (\Exception $e) {
            Log::error('Classic page content generation failed', [
                'page_id' => $this->page->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->page->update([
                'generation_status' => 'failed',
                'generation_error' => $e->getMessage(),
            ]);

            throw $e; // Re-throw to mark job as failed
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        $this->page->update([
            'generation_status' => 'failed',
            'generation_error' => $exception->getMessage(),
        ]);
    }
}
