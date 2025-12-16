<?php

namespace App\Jobs;

use App\Models\Page;
use App\Models\Product;
use App\Services\PageGenerationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class GeneratePageJob implements ShouldQueue
{
    use Queueable;

    public $tries = 3;
    public $timeout = 300; // 5 minutes

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Page $page,
        public Product $product,
        public array $pageData,
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

            // Generate complete page with template using AI
            $result = $pageGenerationService->generateCompletePage(
                $this->product,
                $this->pageData,
                $this->language,
                $this->apiKey
            );

            if (!$result['success']) {
                throw new \Exception($result['message'] ?? 'Failed to generate page content');
            }

            // Update page with generated content
            $this->page->update([
                'generation_status' => 'completed',
                'template_id' => $result['template']['template_id'] ?? null,
                'content' => $result['content'],
                'seo_title' => $this->pageData['seo_title'] ?? $this->product->name,
                'seo_description' => $this->pageData['seo_description'] ?? $this->product->description ?? '',
                'seo_keywords' => $this->pageData['seo_keywords'] ?? '',
                'form_type' => $this->pageData['form_type'],
                'form_fields' => $this->pageData['form_fields'],
                'ai_version' => 'v3',
                'generation_error' => null,
            ]);
        } catch (\Exception $e) {
            Log::error('Page generation failed', [
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
