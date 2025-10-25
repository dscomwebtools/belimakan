<?php

namespace App\Http\Controllers;

use App\Models\Sosmed;
use Illuminate\Http\Request;

class LinkController extends Controller
{
    public function index()
    {
        $sosmed = Sosmed::where('is_link', true)->get();

        return view('client.link.index', [
            'profile_name' => 'Nama Kamu',
            'profile_picture' => asset('assets/images/profile.webp'),
            'bio' => 'Temukan aku di berbagai platform berikut',
            'sosmed' => $sosmed,
        ]);
    }
}
