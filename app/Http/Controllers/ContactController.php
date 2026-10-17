<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Contracts\View\View;

class ContactController extends Controller
{
    public function index(): View
    {
        return view('contact.index', [
            'brandName' => Setting::get('brand_name', 'Yazh Creations'),
            'email' => Setting::get('email', ''),
            'phone' => Setting::get('phone', ''),
            'whatsappNumber' => Setting::get('whatsapp_number', ''),
            'instagramUrl' => Setting::get('instagram_url', '#'),
            'address' => Setting::get('address', ''),
        ]);
    }
}
