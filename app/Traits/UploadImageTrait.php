<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait UploadImageTrait
{
    protected function uploadImage($request, $fields, $model)
    {
        $imagePaths = [];

        foreach ($fields as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $fileName = Str::random(20) . '.' . $file->getClientOriginalExtension();

                $file->storeAs($model, $fileName, 'public');
                $imagePath = 'storage/' . $model . '/' . $fileName;

                $imagePaths[$field] = $imagePath;
            }
        }

        return $imagePaths;
    }

    protected function deleteImage($path){
        if(file_exists($path)){
            unlink($path);
        }
    }

    protected function uploadFileManager($model,$request, $fields){


    }
}
