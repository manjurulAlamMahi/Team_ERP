<?php

namespace App\Http\Controllers;

use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;

abstract class Controller
{
    public function uploadImage($image, $oldImage = null, $folder = 'uploads', $width = 150, $height = 150, $customName = 'image')
    {
        if ($image && $image->isValid()) {
            // Delete old image if exists
            if ($oldImage && File::exists(public_path($oldImage))) {
                File::delete(public_path($oldImage));
            }

            // Generate new image name with custom name + timestamp
            $extension = $image->getClientOriginalExtension();
            $image_name = $customName . '-' . time() . '.' . $extension;
            $image_path = $folder . '/' . $image_name;

            // Resize and save the image
            Image::make($image)->resize($width, $height)->save(public_path($image_path));

            return $image_path; // Return new image path
        }

        return $oldImage; // Return old image if no new image is uploaded
    }
}
