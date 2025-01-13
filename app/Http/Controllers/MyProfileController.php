<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class MyProfileController extends Controller
{
    public function index()
    {

    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'cropped_photo' => 'required',
        ]);

        $user = Auth::user();
        $base64Image = $request->input('cropped_photo');

        // Decode base64 string
        list($type, $data) = explode(';', $base64Image);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);

        // Create image instance
        $img = Image::make($data);

        // Crop and resize
        $img->fit(200, 200);

        // Generate filename
        $filename = time() . '.png';
        $path = 'uploads/profiles/' . $filename;

        // Save the image
        $img->save(storage_path('app/public/' . $path));

        // Delete old photo
        if ($user->photo) {
            Storage::disk('public')->delete('uploads/profiles/' . $user->photo);
        }

        $user->photo = $filename;
        $user->save();

        return redirect()->route('myprofile.index');
    }
}
