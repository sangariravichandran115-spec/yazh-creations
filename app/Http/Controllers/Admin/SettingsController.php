<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\ImageService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    private const FIELDS = [
        'brand_name',
        'tagline',
        'whatsapp_number',
        'instagram_url',
        'email',
        'phone',
        'address',
        'about_text',
        'hero_title',
        'hero_description',
        'story_title',
    ];

    public function index(): View
    {
        $settings = [];

        foreach (self::FIELDS as $key) {
            $settings[$key] = Setting::get($key);
        }

        $settings['story_image'] = Setting::get('story_image');

        return view('admin.settings.index', [
            'settings' => $settings,
        ]);
    }

    public function update(Request $request, ImageService $images): RedirectResponse
    {
        $fields = $request->validate([
            'brand_name' => ['required', 'string', 'max:255'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'whatsapp_number' => ['nullable', 'string', 'max:20'],
            'instagram_url' => ['nullable', 'url', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'about_text' => ['nullable', 'string'],
            'hero_title' => ['nullable', 'string', 'max:255'],
            'hero_description' => ['nullable', 'string', 'max:500'],
            'story_title' => ['nullable', 'string', 'max:255'],
            'story_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'remove_story_image' => ['sometimes', 'boolean'],
        ]);

        if ($request->boolean('remove_story_image')) {
            $images->deleteStoredImage(Setting::get('story_image'));
            unset($fields['story_image']);
            Setting::set('story_image', null);
        } elseif ($request->hasFile('story_image')) {
            $images->deleteStoredImage(Setting::get('story_image'));
            $fields['story_image'] = $request->file('story_image')->store('settings', 'public');
        }

        foreach ($fields as $key => $value) {
            Setting::set($key, $value);
        }

        return redirect()
            ->route('admin.settings.index')
            ->with('success', 'Settings updated successfully.');
    }
}
