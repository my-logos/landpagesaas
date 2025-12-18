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

        // Get product image codes (dynamic Blade code, not static URLs)
        $imageCodes = $this->getProductImageCodes($product);
        $hasImages = !empty($imageCodes);

        if ($isArabic) {
            return $this->buildArabicPrompt($productName, $productDescription, $productPrice, $productCurrency, $pageTitle, $additionalDescription, $seoDescription, $imageCodes, $hasImages);
        }

        return $this->buildEnglishPrompt($productName, $productDescription, $productPrice, $productCurrency, $pageTitle, $additionalDescription, $seoDescription, $imageCodes, $hasImages);
    }

    /**
     * Get product image code examples (dynamic Blade code, not static URLs)
     * 
     * This method generates dynamic Blade template code snippets that the AI can use
     * to access product images. Instead of providing static image URLs (which would break),
     * we provide dynamic code that will work at runtime.
     * 
     * The generated code uses Laravel's asset() helper with dynamic image paths
     * from the product model, ensuring images load correctly regardless of storage location.
     * 
     * @param Product $product The product to get images from
     * @return array Array of Blade code strings for accessing each product image
     */
    protected function getProductImageCodes(Product $product): array
    {
        $images = $product->images ?? [];
        if (empty($images) || !is_array($images)) {
            return [];
        }

        // Generate dynamic Blade code examples for each image
        // These code snippets will be included in AI prompt so AI knows how to access images
        $codes = [];
        foreach ($images as $index => $image) {
            if (!empty($image)) {
                // Generate Blade code that accesses image by array index
                // ltrim() removes leading slash if present (handles both /path and path formats)
                // Null coalescing operator ensures no errors if image doesn't exist
                $codes[] = "{{ asset('' . ltrim(\\\$page->product->images[{$index}] ?? '', '/')) }}";
            }
        }

        return $codes;
    }

    /**
     * Build Arabic prompt
     */
    protected function buildArabicPrompt(string $productName, string $productDescription, float $price, string $currency, string $title, string $additionalDesc, string $seoDesc, array $imageCodes, bool $hasImages): string
    {
        $imagesSection = '';
        if ($hasImages && !empty($imageCodes)) {
            $imageExamples = implode("\n", array_map(function ($code, $index) {
                return "  - الصورة " . ($index + 1) . ": {$code}";
            }, $imageCodes, array_keys($imageCodes)));

            $imagesSection = "
Product Images (must use dynamic Blade code, not static URLs):

Examples of Blade code to access images:
{$imageExamples}

Or you can use a loop to display all images:
@if(\$page->product && \$page->product->images)
    @foreach(\$page->product->images as \$image)
        @if(\$image)
            <img src=\"{{ asset('' . ltrim(\$image, '/')) }}\" alt=\"{{ \$page->product->name }}\">
        @endif
    @endforeach
@endif

⚠️ Very Important: Use dynamic Blade code like the examples above, NOT static URLs!
";
        }

        $partialsInfo = "
Required sections to include using Blade includes (mandatory):

1. At the beginning of <head> right after <meta charset> and <meta viewport>:
   @include('landing-templates.partials.head')
   
   Note: If you already have a complete <head>, use only the partials mentioned below.

2. At the beginning of <body> (right after opening <body> tag):
   @include('landing-templates.partials.page-features')

3. In the Order Form Section:
   @include('landing-templates.partials.order-form')

⚠️ Critical Rules:
- Use @include('landing-templates.partials.head') in <head> if you don't have a complete head
- Use @include('landing-templates.partials.page-features') at the start of <body>
- Use @include('landing-templates.partials.order-form') in the order form section
- Don't forget to add data attributes in <body>: data-google-analytics-id=\"{{ \$page->google_analytics_id ?? '' }}\" data-facebook-pixel=\"{{ \$page->facebook_pixel ?? '' }}\" data-tiktok-pixel=\"{{ \$page->tiktok_pixel ?? '' }}\" data-snapchat-pixel=\"{{ \$page->snapchat_pixel ?? '' }}\"
";

        // Build Arabic landing page generation prompt
        // This prompt instructs AI to generate complete HTML page in Arabic (RTL layout)
        return "Create a professional landing page in Arabic (RTL) for the following product:

Product Information:
- Product Name: {$productName}
- Description: {$productDescription}
- Price: {$price} {$currency}
- Page Title: {$title}
" . (!empty($additionalDesc) ? "- Additional Description: {$additionalDesc}\n" : "") . "
{$imagesSection}
Design Requirements (mandatory):
1. Use Tajawal font from Google Fonts (must be included in <head>)
2. Design must be responsive and work on all screen sizes
3. Use dir=\"rtl\" in <html> tag
4. Use lang=\"ar\" in <html> tag
{$partialsInfo}
Create a complete and valid HTML page (from <!DOCTYPE html> to </html>) with a professional and modern design.";
    }

    /**
     * Build English prompt
     */
    protected function buildEnglishPrompt(string $productName, string $productDescription, float $price, string $currency, string $title, string $additionalDesc, string $seoDesc, array $imageCodes, bool $hasImages): string
    {
        $imagesSection = '';
        if ($hasImages && !empty($imageCodes)) {
            $imageExamples = implode("\n", array_map(function ($code, $index) {
                return "  - Image " . ($index + 1) . ": {$code}";
            }, $imageCodes, array_keys($imageCodes)));

            $imagesSection = "
Product Images (must use dynamic Blade code, not static URLs):

Examples of Blade code to access images:
{$imageExamples}

Or you can use a loop to display all images:
@if(\$page->product && \$page->product->images)
    @foreach(\$page->product->images as \$image)
        @if(\$image)
            <img src=\"{{ asset('' . ltrim(\$image, '/')) }}\" alt=\"{{ \$page->product->name }}\">
        @endif
    @endforeach
@endif

⚠️ Very Important: Use dynamic Blade code like the examples above, NOT static URLs!
";
        }

        $partialsInfo = "
Required sections to include using Blade includes (mandatory):

1. At the beginning of <head> right after <meta charset> and <meta viewport>:
   @include('landing-templates.partials.head')
   
   Note: If you already have a complete <head>, use only the partials mentioned below.

2. At the beginning of <body> (right after opening <body> tag):
   @include('landing-templates.partials.page-features')

3. In the Order Form Section:
   @include('landing-templates.partials.order-form')

⚠️ Critical Rules:
- Use @include('landing-templates.partials.head') in <head> if you don't have a complete head
- Use @include('landing-templates.partials.page-features') at the start of <body>
- Use @include('landing-templates.partials.order-form') in the order form section
- Don't forget to add data attributes in <body>: data-google-analytics-id=\"{{ \$page->google_analytics_id ?? '' }}\" data-facebook-pixel=\"{{ \$page->facebook_pixel ?? '' }}\" data-tiktok-pixel=\"{{ \$page->tiktok_pixel ?? '' }}\" data-snapchat-pixel=\"{{ \$page->snapchat_pixel ?? '' }}\"
";

        return "Create a professional landing page in English (LTR) for the following product:

Product Information:
- Product Name: {$productName}
- Description: {$productDescription}
- Price: {$price} {$currency}
- Page Title: {$title}
" . (!empty($additionalDesc) ? "- Additional Description: {$additionalDesc}\n" : "") . "
{$imagesSection}
Design Requirements (mandatory):
1. Use Tajawal font from Google Fonts (must be included in <head>)
2. Design must be responsive and work on all screen sizes
3. Use dir=\"ltr\" in <html> tag
4. Use lang=\"en\" in <html> tag
{$partialsInfo}
Create a complete and valid HTML page (from <!DOCTYPE html> to </html>) with a professional and modern design.";
    }

    /**
     * Generate static content (description, features, FAQs) using AI
     */
    public function generateStaticContent(Product $product, string $language = 'ar', ?string $apiKey = null): array
    {
        if (!$this->isAiAvailable($apiKey)) {
            return $this->errorResponse('AI service is not available');
        }

        try {
            $isArabic = $language === 'ar';
            $productName = $product->name ?? '';
            $productDescription = $product->description ?? '';

            // Build prompt for content generation
            $prompt = $this->buildContentPrompt($productName, $productDescription, $isArabic);

            // Generate content using AI
            $aiResponse = $apiKey
                ? $this->aiService->generateWithGeminiKey($apiKey, $prompt)
                : $this->aiService->generateContent($prompt);

            if (!$aiResponse) {
                Log::error('Failed to generate static content from AI', [
                    'product_id' => $product->id,
                    'language' => $language
                ]);
                return $this->errorResponse('Failed to generate content');
            }

            // Parse AI response
            $contentData = $this->parseContentResponse($aiResponse, $isArabic);

            return [
                'success' => true,
                'content' => $contentData
            ];
        } catch (\Exception $e) {
            Log::error('Error generating static content: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Build prompt for content generation (description, features, FAQs)
     */
    protected function buildContentPrompt(string $productName, string $productDescription, bool $isArabic): string
    {
        if ($isArabic) {
            // Arabic prompt for AI content generation
            // Note: The prompt text is in Arabic because the AI needs to understand
            // the language requirements, but the actual product data can be in any language
            return "You are a professional marketing content writer. Create professional marketing content in Arabic for the following product:

Product Name: {$productName}
Product Description: {$productDescription}

Required (return JSON only without any additional text):
{
    \"description\": \"Enhanced product description (3-5 attractive and convincing sentences)\",
    \"features\": [
        {
            \"title\": \"Feature title\",
            \"description\": \"Feature description\",
            \"icon\": \"fa-solid fa-icon-name\"
        }
    ],
    \"faqs\": [
        {
            \"question\": \"Question\",
            \"answer\": \"Answer\"
        }
    ]
}

Requirements:
- Create 3-6 main features with appropriate Font Awesome icons (fa-solid fa-*)
- Preferred: 6 features (3 per row)
- Create 3-5 frequently asked questions with answers
- Use proper Arabic language
- Content must be attractive and persuasive
- Return valid JSON only without any additional text or explanation";
        }

        return "You are a professional marketing content writer. Create professional marketing content in English for the following product:

Product Name: {$productName}
Product Description: {$productDescription}

Required (return JSON only without any additional text):
{
    \"description\": \"Enhanced product description (3-5 attractive and convincing sentences)\",
    \"features\": [
        {
            \"title\": \"Feature title\",
            \"description\": \"Feature description\",
            \"icon\": \"fa-solid fa-icon-name\"
        }
    ],
    \"faqs\": [
        {
            \"question\": \"Question\",
            \"answer\": \"Answer\"
        }
    ]
}

Requirements:
- Create 3-6 main features with appropriate Font Awesome icons (fa-solid fa-*) - preferably 6 features (3 per row)
- Create 3-5 frequently asked questions with their answers
- Use proper English
- Content must be attractive and convincing
- Return valid JSON only without any additional text or explanation";
    }

    /**
     * Parse AI response to extract JSON content
     * 
     * AI responses may come wrapped in markdown code blocks (```json ... ```)
     * or may contain additional text. This method:
     * 1. Removes markdown code block markers
     * 2. Extracts JSON object using regex pattern matching
     * 3. Parses JSON and validates structure
     * 4. Limits arrays to prevent UI overflow (6 features max, 5 FAQs max)
     * 5. Returns fallback content if parsing fails
     * 
     * @param string $aiResponse Raw response from AI service
     * @param bool $isArabic Whether the response is in Arabic
     * @return array Parsed content with 'description', 'features', 'faqs', 'language' keys
     */
    protected function parseContentResponse(string $aiResponse, bool $isArabic): array
    {
        // Clean response - remove markdown code block markers
        // AI may wrap JSON in ```json ... ``` or just ``` ... ```
        $cleaned = preg_replace('/```json\s*/', '', $aiResponse);
        $cleaned = preg_replace('/```\s*/', '', $cleaned);
        $cleaned = trim($cleaned);

        // Extract JSON object using regex (matches first { ... } block)
        // The 's' modifier allows . to match newlines (multiline JSON)
        if (preg_match('/\{.*\}/s', $cleaned, $matches)) {
            $jsonString = $matches[0];
        } else {
            // If no match, assume entire cleaned string is JSON
            $jsonString = $cleaned;
        }

        // Parse JSON string to PHP array
        $parsed = json_decode($jsonString, true);

        // If JSON parsing failed, return fallback content
        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::warning('Failed to parse AI response as JSON', [
                'error' => json_last_error_msg(),
                'response' => substr($cleaned, 0, 500) // Log first 500 chars for debugging
            ]);
            return $this->getFallbackContent($isArabic);
        }

        // Ensure all required fields exist and limit array sizes
        // Limit features to 6 (3 per row, max 2 rows for better UI layout)
        // Limit FAQs to 5 (reasonable number for landing page)
        $result = [
            'description' => $parsed['description'] ?? '',
            'features' => array_slice($parsed['features'] ?? [], 0, 6), // Max 6 features
            'faqs' => array_slice($parsed['faqs'] ?? [], 0, 5), // Max 5 FAQs
            'language' => $isArabic ? 'ar' : 'en'
        ];

        // Clean and validate features array
        // Ensures each feature has required fields with default values if missing
        $result['features'] = array_map(function ($feature) {
            return [
                'title' => $feature['title'] ?? '',
                'description' => $feature['description'] ?? '',
                'icon' => $feature['icon'] ?? 'fa-solid fa-star' // Default icon if not provided
            ];
        }, $result['features']);

        // Clean and validate FAQs array
        // Ensures each FAQ has required fields (question and answer)
        $result['faqs'] = array_map(function ($faq) {
            return [
                'question' => $faq['question'] ?? '',
                'answer' => $faq['answer'] ?? ''
            ];
        }, $result['faqs']);

        return $result;
    }

    /**
     * Get fallback content if AI parsing fails
     */
    protected function getFallbackContent(bool $isArabic): array
    {
        if ($isArabic) {
            return [
                'description' => '',
                'features' => [
                    ['title' => 'High Quality', 'description' => 'High quality and guaranteed product', 'icon' => 'fa-solid fa-star'],
                    ['title' => 'Great Price', 'description' => 'Best prices in the market', 'icon' => 'fa-solid fa-tag'],
                    ['title' => 'Fast Delivery', 'description' => 'Fastest delivery time', 'icon' => 'fa-solid fa-truck']
                ],
                'faqs' => [
                    ['question' => 'What is the delivery time?', 'answer' => 'Delivery time ranges from 3-7 business days'],
                    ['question' => 'Can the product be replaced?', 'answer' => 'Yes, the product can be replaced within 7 days of receipt']
                ],
                'language' => 'ar'
            ];
        }

        return [
            'description' => '',
            'features' => [
                ['title' => 'High Quality', 'description' => 'High quality and guaranteed product', 'icon' => 'fa-solid fa-star'],
                ['title' => 'Great Price', 'description' => 'Best prices in the market', 'icon' => 'fa-solid fa-tag'],
                ['title' => 'Fast Delivery', 'description' => 'Fastest delivery time', 'icon' => 'fa-solid fa-truck']
            ],
            'faqs' => [
                ['question' => 'What is the delivery time?', 'answer' => 'Delivery time ranges from 3-7 business days'],
                ['question' => 'Can I exchange the product?', 'answer' => 'Yes, you can exchange the product within 7 days of receipt']
            ],
            'language' => 'en'
        ];
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
