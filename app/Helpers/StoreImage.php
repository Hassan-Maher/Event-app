<?php

namespace App\Helpers;


class StoreImage
{

    static function upload($image , $folder)
    {
    
        $path = $image->store($folder, 'public'); 
        return 'storage/' . $path; 
    }
    
}