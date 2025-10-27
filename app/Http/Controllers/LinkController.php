<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Sosmed;
use Illuminate\Http\Request;

class LinkController extends Controller
{
    public function index()
    {
        $sosmed = Sosmed::where('is_link', true)->get();

        $setting = Setting::where('section_name', 'link')->first();

        return view('client.link.index', [
            'profile_name' => $setting->title,
            'profile_picture' => asset('assets/images/profile.webp'),
            'bio' => $setting->description,
            'sosmed' => $sosmed,
        ]);
    }
}
