@extends('layouts.admin')

@section('title', 'Settings — Admin')

@section('content')
    <h1 class="admin-page-title mb-4">Settings</h1>

    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PATCH')

        <div class="row g-4">
            <div class="col-lg-6">
                <div class="admin-panel rounded-4 p-3">
                    <h2 class="admin-panel-title mb-3">Brand</h2>

                    <div class="mb-3">
                        <label for="brand_name" class="form-label">Brand Name <span class="text-danger">*</span></label>
                        <input type="text" name="brand_name" id="brand_name" class="form-control yc-input" value="{{ old('brand_name', $settings['brand_name']) }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="tagline" class="form-label">Tagline</label>
                        <input type="text" name="tagline" id="tagline" class="form-control yc-input" value="{{ old('tagline', $settings['tagline']) }}">
                    </div>

                    <div class="mb-3">
                        <label for="hero_title" class="form-label">Hero Title</label>
                        <input type="text" name="hero_title" id="hero_title" class="form-control yc-input" value="{{ old('hero_title', $settings['hero_title']) }}">
                    </div>

                    <div class="mb-3">
                        <label for="hero_description" class="form-label">Hero Description</label>
                        <textarea name="hero_description" id="hero_description" rows="3" class="form-control yc-input">{{ old('hero_description', $settings['hero_description']) }}</textarea>
                    </div>

                    <div class="mb-3 mb-lg-0">
                        <label for="about_text" class="form-label">About Text</label>
                        <textarea name="about_text" id="about_text" rows="5" class="form-control yc-input">{{ old('about_text', $settings['about_text']) }}</textarea>
                    </div>
                </div>

                <div class="admin-panel rounded-4 p-3 mt-4">
                    <h2 class="admin-panel-title mb-3">Our Story</h2>

                    <div class="mb-3">
                        <label for="story_title" class="form-label">Story Title</label>
                        <input type="text" name="story_title" id="story_title" class="form-control yc-input" value="{{ old('story_title', $settings['story_title']) }}" placeholder="Our Story">
                    </div>

                    <div class="mb-3">
                        <label for="story_image" class="form-label">Story Image</label>
                        @if ($settings['story_image'])
                            <div class="mb-2">
                                <img src="{{ asset('storage/'.$settings['story_image']) }}" alt="Current story image" class="rounded-3 shadow-sm" style="max-height: 140px;">
                            </div>
                            <div class="form-check mb-2">
                                <input type="checkbox" name="remove_story_image" id="remove_story_image" value="1" class="form-check-input" @checked(old('remove_story_image'))>
                                <label for="remove_story_image" class="form-check-label text-danger">Remove current image</label>
                            </div>
                        @endif
                        <input type="file" name="story_image" id="story_image" class="form-control yc-input" accept="image/jpeg,image/png,image/webp">
                        <div class="form-text">JPG, PNG or WebP up to 2MB.</div>
                        @error('story_image')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="admin-panel rounded-4 p-3 mb-4">
                    <h2 class="admin-panel-title mb-3">Contact & Social</h2>

                    <div class="mb-3">
                        <label for="whatsapp_number" class="form-label">WhatsApp Number</label>
                        <input type="text" name="whatsapp_number" id="whatsapp_number" class="form-control yc-input" value="{{ old('whatsapp_number', $settings['whatsapp_number']) }}" placeholder="+91 98765 43210">
                        <div class="form-text">Shown on the WhatsApp buttons. Country code optional for Indian numbers.</div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control yc-input" value="{{ old('email', $settings['email']) }}">
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" name="phone" id="phone" class="form-control yc-input" value="{{ old('phone', $settings['phone']) }}">
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <input type="text" name="address" id="address" class="form-control yc-input" value="{{ old('address', $settings['address']) }}">
                    </div>

                    <div class="mb-3 mb-lg-0">
                        <label for="instagram_url" class="form-label">Instagram URL</label>
                        <input type="url" name="instagram_url" id="instagram_url" class="form-control yc-input" value="{{ old('instagram_url', $settings['instagram_url']) }}" placeholder="https://instagram.com/yazhcreations">
                    </div>
                </div>

                <button type="submit" class="btn btn-yc btn-lg w-100">Save Settings</button>
            </div>
        </div>
    </form>
@endsection