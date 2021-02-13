<?php


namespace App\Repositories;


use Spatie\MediaLibrary\Models\Media;

class MainPageImageRepository
{

    /**
     * it handle updating many images
     *
     * @param $model
     * @param $advertisement_media
     * @param array $request_images
     */
    public function updateMedia($model, $advertisement_media, array $request_images)
    {
        $images = [];
        foreach ($advertisement_media as $image) {
            $images[] = $image->file_name;
        }
        $tmp =[];
        foreach ($advertisement_media as $advertisement_image) {
            if (!in_array($advertisement_image->file_name, $request_images)) {
                //delete it
                $tmp[]= $advertisement_image->file_name;
//                dd($advertisement_image->file_name);
                Media::where('id', $advertisement_image->id)->delete();
            }
        }
        foreach ($request_images as $request_image) {
            if (!in_array($request_image, $images)) {
                $model->addMedia(storage_path('tmp/uploads/' . $request_image))->toMediaCollection('images');
            }
        }
    }
}
