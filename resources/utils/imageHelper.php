<?php

use Illuminate\Support\Facades\Log;

function handleProductImage($image, $productCode)
{
    try {
        if ($image) {
            $extension = $image->getClientOriginalExtension();
            $imageGD = imagecreatefromstring(file_get_contents($image->getPathname()));

            if (imageistruecolor($imageGD)) {
                $imageName = $productCode . '.webp';

                if (function_exists('imagewebp')) {
                    $directory = storage_path('app/public/product_img/');
                    if (!file_exists($directory)) {
                        mkdir($directory, 0777, true);
                    }

                    imagewebp($imageGD, $directory . $imageName, 40);
                } else {
                    $imageName = $productCode . '.' . $extension;
                    $image->storeAs('public/product_img', $imageName);
                }
            } else {
                $imageName = $productCode . '.' . $extension;
                $image->storeAs('public/product_img', $imageName);
            }

            imagedestroy($imageGD);

            return $imageName;
        }
    } catch (\Exception $e) {
        Log::error($e->getMessage());
        return null;
    }

    return null;
}
