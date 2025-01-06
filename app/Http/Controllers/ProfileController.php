<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'cropped_photo' => 'required', // Ensure the cropped photo is included
        ]);

        $user = Auth::user();
        $base64Image = $request->input('cropped_photo');

        // Decode base64 string and create image resource
        list($type, $data) = explode(';', $base64Image);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);
        $sourceImage = imagecreatefromstring($data);

        if ($sourceImage === false) {
            return redirect()->back()->withErrors(['photo' => 'Invalid image data.']);
        }

        // Create a blank true color image
        $imageWidth = imagesx($sourceImage);
        $imageHeight = imagesy($sourceImage);
        $destImage = imagecreatetruecolor(200, 200);

        // Calculate the cropping coordinates and dimensions
        $cropWidth = min($imageWidth, $imageHeight);
        $cropHeight = $cropWidth;
        $cropX = ($imageWidth - $cropWidth) / 2;
        $cropY = ($imageHeight - $cropHeight) / 2;

        // Crop and resize the image
        imagecopyresampled(
            $destImage,
            $sourceImage,
            0,
            0,
            $cropX,
            $cropY,
            200,
            200,
            $cropWidth,
            $cropHeight
        );

        // Define the file name and path
        $filename = time() . '.png'; // You can change to '.jpg' if needed
        $path = 'uploads/profiles/' . $filename;

        // Save the image to the public storage
        imagepng($destImage, public_path('storage/' . $path)); // For PNG format
        // imagejpeg($destImage, public_path('storage/' . $path)); // For JPG format

        // Free up memory
        imagedestroy($sourceImage);
        imagedestroy($destImage);

        // Delete old photo if it exists
        if ($user->photo) {
            @unlink(public_path('storage/uploads/profiles/' . $user->photo));
        }

        $user->photo = $filename;
        $user->save();

        return redirect()->route('profile.show')->with('status', 'Profile photo updated successfully.');
    }
}
