<?php

namespace App\Services;

use App\Models\Product;
use App\Models\LandingPageTemplate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PageGenerationService
{
    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Generate complete landing page with template and save it
     */
    public function generateCompletePage(Product $product, array $pageData, string $language = 'ar', ?string $apiKey = null): array
    {
        if (!$this->isAiAvailable($apiKey)) {
            return $this->errorResponse('AI service is not available');
        }

        // Build prompt if not provided, or use provided prompt
        $prompt = $pageData['prompt'] ?? $this->buildPrompt($product, $pageData, $language);

        if (empty($prompt)) {
            return $this->errorResponse('Prompt is required');
        }

        try {
            $htmlContent = $this->generateHtmlContent($prompt, $apiKey);

            if (!$htmlContent) {
                Log::error('Failed to generate page content from AI', [
                    'product_id' => $product->id,
                    'language' => $language
                ]);
                return $this->errorResponse('Failed to generate page content');
            }

            $cleanedHtml = $this->cleanGeneratedHTML($htmlContent);
            $templateData = $this->saveTemplate($product, $cleanedHtml, $language);

            return [
                'success' => true,
                'content' => $cleanedHtml,
                'template' => $templateData
            ];
        } catch (\Exception $e) {
            Log::error('Error generating complete page: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Check if AI is available (either service or provided API key)
     */
    protected function isAiAvailable(?string $apiKey = null): bool
    {
        return $this->aiService->isAvailable() || !empty($apiKey);
    }

    /**
     * Generate HTML content using AI
     */
    protected function generateHtmlContent(string $prompt, ?string $apiKey = null): ?string
    {
        return $apiKey
            ? $this->aiService->generateWithGeminiKey($apiKey, $prompt)
            : $this->aiService->generateContent($prompt);
    }

    /**
     * Clean generated HTML - remove markdown code blocks
     */
    protected function cleanGeneratedHTML(string $html): string
    {
        return trim(preg_replace('/```(?:html)?\s*/', '', $html));
    }

    /**
     * Save template file and create database record
     */
    protected function saveTemplate(Product $product, string $htmlContent, string $language): array
    {
        try {
            $templateSlug = $this->generateTemplateSlug($product->name, $language);
            $templateFileName = $templateSlug . '.blade.php';
            $templatePath = resource_path("views/landing-templates/{$templateFileName}");

            $this->ensureTemplateDirectoryExists();
            file_put_contents($templatePath, $htmlContent);

            $template = $this->createTemplateRecord($product, $templateSlug, $templateFileName, $language);

            return [
                'template_id' => $template->id,
                'template_file' => $templateFileName,
                'template_path' => $templatePath
            ];
        } catch (\Exception $e) {
            Log::error('Error saving template: ' . $e->getMessage());
            return [
                'template_id' => null,
                'template_file' => null,
                'template_path' => null
            ];
        }
    }

    /**
     * Generate unique template slug
     */
    protected function generateTemplateSlug(string $productName, string $language): string
    {
        return Str::slug($productName . '-' . $language . '-' . time());
    }

    /**
     * Ensure template directory exists
     */
    protected function ensureTemplateDirectoryExists(): void
    {
        $templateDir = resource_path('views/landing-templates');
        if (!is_dir($templateDir)) {
            mkdir($templateDir, 0755, true);
        }
    }

    /**
     * Create or update template database record
     */
    protected function createTemplateRecord(Product $product, string $slug, string $fileName, string $language): LandingPageTemplate
    {
        return LandingPageTemplate::updateOrCreate(
            ['slug' => $slug],
            [
                'name' => "{$product->name} - " . strtoupper($language),
                'description' => "AI Generated Template for {$product->name}",
                'template_file' => $fileName,
                'is_enabled' => true,
                'sort_order' => 0,
                'category' => 'ai-generated',
            ]
        );
    }

    /**
     * Build prompt for AI page generation
     */
    public function buildPrompt(Product $product, array $pageData, string $language = 'ar'): string
    {
        $isArabic = $language === 'ar';

        $productName = $product->name ?? '';
        $productDescription = $product->description ?? '';
        $productPrice = $product->price_cents ? ($product->price_cents / 100) : 0;
        $productCurrency = $product->currency ?? 'EGP';

        $pageTitle = $pageData['title'] ?? $productName;
        $additionalDescription = $pageData['additional_description'] ?? '';
        $seoDescription = $pageData['seo_description'] ?? $productDescription;

        if ($isArabic) {
            return $this->buildArabicPrompt($productName, $productDescription, $productPrice, $productCurrency, $pageTitle, $additionalDescription, $seoDescription);
        }

        return $this->buildEnglishPrompt($productName, $productDescription, $productPrice, $productCurrency, $pageTitle, $additionalDescription, $seoDescription);
    }

    /**
     * Build Arabic prompt
     */
    protected function buildArabicPrompt(string $productName, string $productDescription, float $price, string $currency, string $title, string $additionalDesc, string $seoDesc): string
    {
        return "أنشئ صفحة هبوط احترافية باللغة العربية للمنتج التالي:

اسم المنتج: {$productName}
الوصف: {$productDescription}
السعر: {$price} {$currency}
العنوان: {$title}
" . (!empty($additionalDesc) ? "وصف إضافي: {$additionalDesc}\n" : "") . "
أنشئ صفحة HTML كاملة وجذابة مع تصميم احترافي وعصري.";
    }

    /**
     * Build English prompt
     */
    protected function buildEnglishPrompt(string $productName, string $productDescription, float $price, string $currency, string $title, string $additionalDesc, string $seoDesc): string
    {
        return "Create a professional landing page in English for the following product:

Product Name: {$productName}
Description: {$productDescription}
Price: {$price} {$currency}
Title: {$title}
" . (!empty($additionalDesc) ? "Additional Description: {$additionalDesc}\n" : "") . "
Create a complete and attractive HTML page with a professional and modern design.";
    }

    /**
     * Return standardized error response
     */
    protected function errorResponse(string $message): array
    {
        return [
            'success' => false,
            'message' => $message
        ];
    }
}
