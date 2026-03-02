<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\FooterLink;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FooterLinkController extends Controller
{
    private array $sections = [
        'marketplace' => 'Marketplace',
        'resources' => 'Resources',
        'community' => 'Community',
    ];

    public function index(): View
    {
        return view('admin.footer-links.index', [
            'links' => FooterLink::orderBy('section')->orderBy('sort_order')->get(),
            'sections' => $this->sections,
        ]);
    }

    public function create(): View
    {
        return view('admin.footer-links.form', [
            'link' => new FooterLink(),
            'sections' => $this->sections,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatePayload($request);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['is_active'] = $request->boolean('is_active');

        $link = FooterLink::create($validated);
        ActivityLog::record('footer.link.created', $request->user('admin'), $link);

        return redirect()->route('admin.footer-links.index')->with('status', 'Footer link created.');
    }

    public function edit(FooterLink $footerLink): View
    {
        return view('admin.footer-links.form', [
            'link' => $footerLink,
            'sections' => $this->sections,
        ]);
    }

    public function update(Request $request, FooterLink $footerLink): RedirectResponse
    {
        $validated = $this->validatePayload($request);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['is_active'] = $request->boolean('is_active');

        $footerLink->update($validated);
        ActivityLog::record('footer.link.updated', $request->user('admin'), $footerLink);

        return redirect()->route('admin.footer-links.index')->with('status', 'Footer link updated.');
    }

    public function toggle(Request $request, FooterLink $footerLink): RedirectResponse
    {
        $footerLink->update(['is_active' => !$footerLink->is_active]);
        ActivityLog::record('footer.link.toggled', $request->user('admin'), $footerLink);

        return back()->with('status', 'Footer link status updated.');
    }

    public function destroy(Request $request, FooterLink $footerLink): RedirectResponse
    {
        $footerLink->delete();
        ActivityLog::record('footer.link.deleted', $request->user('admin'), $footerLink);

        return back()->with('status', 'Footer link deleted.');
    }

    private function validatePayload(Request $request): array
    {
        return $request->validate([
            'section' => ['required', Rule::in(array_keys($this->sections))],
            'label' => ['required', 'string', 'max:120'],
            'url' => ['required', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }
}
