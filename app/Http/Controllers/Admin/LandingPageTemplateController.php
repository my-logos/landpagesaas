<?php

namespace App\Http\Controllers\Admin;

use App\Models\LandingPageTemplate;
use App\Http\Controllers\Concerns\HandlesFileUploads;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LandingPageTemplateController extends BaseAdminController
{
    use HandlesFileUploads;

    public function index(Request $request)
    {
        $templates = LandingPageTemplate::orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.landing-page-templates.index', $this->getViewData(compact('templates')));
    }

    public function create(Request $request)
    {
        return view('admin.landing-page-templates.create', $this->getViewData());
    }

    public function store(Request $request)
    {
        $validated = $this->validateTemplate($request);

        $data = $this->prepareTemplateData($request, $validated);
        $data['preview_image'] = $this->handlePreviewImageUpload($request);
        $data['template_file'] = $this->handleTemplateFileUpload($request, $data['slug'] ?? '');

        LandingPageTemplate::create($data);

        return $this->redirectWithSuccess(
            'admin.landing-page-templates.index',
            'messages.template_created',
            'Template created successfully'
        );
    }

    public function edit(Request $request, $id)
    {
        $template = LandingPageTemplate::findOrFail($id);
        return view('admin.landing-page-templates.edit', $this->getViewData(compact('template')));
    }

    public function update(Request $request, $id)
    {
        $template = LandingPageTemplate::findOrFail($id);
        $validated = $this->validateTemplate($request, $id);

        $data = $this->prepareTemplateData($request, $validated);

        // Handle preview image upload
        if ($request->hasFile('preview_image')) {
            $this->deleteOldPreviewImage($template);
            $data['preview_image'] = $this->handlePreviewImageUpload($request);
        }

        // Handle template file upload
        if ($request->hasFile('template_file')) {
            $this->deleteOldTemplateFile($template);
            $data['template_file'] = $this->handleTemplateFileUpload($request, $data['slug'] ?? $template->slug);
        }

        $template->update($data);

        return $this->redirectWithSuccess(
            'admin.landing-page-templates.index',
            'messages.template_updated',
            'Template updated successfully'
        );
    }

    public function destroy($id)
    {
        $template = LandingPageTemplate::findOrFail($id);

        if ($template->pages()->count() > 0) {
            return $this->redirectWithError(
                'admin.landing-page-templates.index',
                'messages.template_in_use',
                'Cannot delete template that is in use'
            );
        }

        $this->deleteOldPreviewImage($template);
        $this->deleteOldTemplateFile($template);

        $template->delete();

        return $this->redirectWithSuccess(
            'admin.landing-page-templates.index',
            'messages.template_deleted',
            'Template deleted successfully'
        );
    }

    /**
     * Validate template data
     */
    private function validateTemplate(Request $request, ?int $id = null): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:landing_page_templates,slug' . ($id ? ",{$id}" : ''),
            'description' => 'nullable|string',
            'preview_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'template_file' => 'nullable|file|mimes:blade.php,php|max:5120',
            'is_enabled' => 'boolean',
            'category' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer',
        ];

        return $request->validate($rules);
    }

    /**
     * Prepare template data from request
     */
    private function prepareTemplateData(Request $request, array $validated): array
    {
        $data = $request->only([
            'name',
            'description',
            'is_enabled',
            'category',
            'sort_order'
        ]);

        // Generate slug if not provided
        $data['slug'] = $validated['slug']
            ? Str::slug($validated['slug'])
            : Str::slug($request->name);

        return $data;
    }

    /**
     * Handle preview image upload
     */
    private function handlePreviewImageUpload(Request $request): ?string
    {
        return $this->uploadImage(
            $request,
            'preview_image',
            'templates/previews',
            'public',
            2048
        );
    }

    /**
     * Handle template file upload
     */
    private function handleTemplateFileUpload(Request $request, string $slug): ?string
    {
        if (!$request->hasFile('template_file')) {
            return null;
        }

        if (empty($slug)) {
            return null;
        }

        $templatePath = resource_path('views/landing-templates');
        return $this->moveFileToDirectory($request, 'template_file', $templatePath, Str::slug($slug));
    }

    /**
     * Delete old preview image
     */
    private function deleteOldPreviewImage(LandingPageTemplate $template): void
    {
        if ($template->preview_image) {
            $this->deleteFile('templates/previews/' . $template->preview_image, 'public');
        }
    }

    /**
     * Delete old template file
     */
    private function deleteOldTemplateFile(LandingPageTemplate $template): void
    {
        if ($template->template_file) {
            $filePath = resource_path('views/landing-templates/' . $template->template_file);
            $this->deleteFileFromDirectory($filePath);
        }
    }
}
