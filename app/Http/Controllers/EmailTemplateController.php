<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EmailTemplateController extends Controller
{
    /**
     * Display a listing of email templates.
     */
    public function index()
    {
        $templates = EmailTemplate::latest()->get();
        return view('admin.email-templates.index', compact('templates'));
    }

    /**
     * Show the form for creating a new email template.
     */
    public function create()
    {
        return view('admin.email-templates.create');
    }

    /**
     * Store a newly created email template in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'slug' => 'required|unique:email_templates,slug|regex:/^[a-z0-9\-]+$/i',
            'subject' => 'required|max:255',
            'message' => 'required',
            'is_active' => 'nullable|boolean',
        ]);

        $validated = $this->prepareTemplatePayload($validated, $request);

        EmailTemplate::create($validated);

        return redirect()->route('admin.email-templates.index')->with('success', 'Email template created successfully!');
    }

    /**
     * Show the form for editing the specified email template.
     */
    public function edit(EmailTemplate $emailTemplate)
    {
        return view('admin.email-templates.edit', compact('emailTemplate'));
    }

    /**
     * Update the specified email template in storage.
     */
    public function update(Request $request, EmailTemplate $emailTemplate)
    {
        $validated = $request->validate([
            'slug' => 'required|unique:email_templates,slug,' . $emailTemplate->id . '|regex:/^[a-z0-9\-]+$/i',
            'subject' => 'required|max:255',
            'message' => 'required',
            'is_active' => 'nullable|boolean',
        ]);

        $validated = $this->prepareTemplatePayload($validated, $request, $emailTemplate);

        $emailTemplate->update($validated);

        return redirect()->route('admin.email-templates.index')->with('success', 'Email template updated successfully!');
    }

    /**
     * Remove the specified email template from storage.
     */
    public function destroy(EmailTemplate $emailTemplate)
    {
        $emailTemplate->delete();

        return redirect()->route('admin.email-templates.index')->with('success', 'Email template deleted successfully!');
    }

    /**
     * Toggle active status
     */
    public function toggleActive(EmailTemplate $emailTemplate)
    {
        $emailTemplate->update(['is_active' => !$emailTemplate->is_active]);

        return back()->with('success', 'Email template status updated!');
    }

    private function prepareTemplatePayload(array $validated, Request $request, ?EmailTemplate $emailTemplate = null): array
    {
        $validated['is_active'] = $request->has('is_active');
        $validated['title'] = $emailTemplate?->title ?: Str::headline(str_replace('-', ' ', $validated['slug']));
        $validated['short_text'] = $emailTemplate?->short_text ?: '';

        return $validated;
    }
}
