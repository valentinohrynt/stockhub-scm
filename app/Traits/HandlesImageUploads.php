<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

trait HandlesImageUploads
{
    public function handleImageUpload(Request $request, Model $model = null, string $disk = 's3', string $directory = 'product_images'): ?string
    {
        if (!$request->hasFile('image_path')) {
            return $model->image_path ?? null;
        }

        if ($model && $model->image_path) {
            Storage::disk($disk)->delete($model->image_path);
        }

        return $request->file('image_path')->store($directory, $disk);
    }
}