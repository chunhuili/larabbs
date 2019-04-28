<?php
/**
 * Created by PhpStorm.
 * User: lichunhui
 * Date: 2018/12/24
 * Time: 下午3:18
 */

namespace App\Observers;


use App\Models\Link;
use Illuminate\Support\Facades\Cache;

class LinkObserver
{
    public function saved(Link $link)
    {
        Cache::forget($link->cache_key);
    }
}