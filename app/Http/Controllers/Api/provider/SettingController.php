<?php

namespace App\Http\Controllers\Api\provider;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\SocialLink;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function social_links()
    {
        $social_links = SocialLink::get();
        return ApiResponse::sendResponse(200 , 'social links retrieved successfully' , $social_links);

    }
}
