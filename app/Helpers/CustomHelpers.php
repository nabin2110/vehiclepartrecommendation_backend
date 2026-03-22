<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CustomHelpers
{
    public static function saveFile($request, $image_path, $image_file_name)
    {
        if ($request->hasFile('image_file')) {
            $image_file = $request->file('image_file');
            $image_file_name = uniqid() . '.' . $image_file->getClientOriginalName();
            $image_file->storeAs($image_path, $image_file_name, 'public');
        }
        return $image_file_name;
    }
    public static function slugGenerator($title, $model)
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        while ($model->where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . time();
        }
        return $slug;
    }
    public static function deleteWithSingleImage($data, $file_path, $file_name)
    {
        if ($data) {
            if ($file_name && Storage::disk('public')->exists($file_path . $file_name)) {
                Storage::disk('public')->delete($file_path . $file_name);
            }
            return $data->delete();
        }
    }
    public static function updateWithUnlinkImage($request, $image_path, $data)
    {
        $image_file_name = $data;
        if ($request->hasFile('image_file')) {
            $image = $request->file('image_file');
            $image_file_name = uniqid() . '.' . $image->getClientOriginalExtension();
            $image->storeAs($image_path, $image_file_name, 'public');
            if ($data && Storage::disk('public')->exists($image_path . $data)) {
                Storage::disk('public')->delete($image_path . $data);
            }
        }
        return $image_file_name;
    }
    public static function slugGeneratorForUpdate($id, $model, $slug)
    {
        $originalSlug = $slug;
        while ($model->where([['slug', $slug], ['id', '!=', $id]])->exists()) {
            $slug = $originalSlug . '-' . time();
        }
        return $slug;
    }
}
