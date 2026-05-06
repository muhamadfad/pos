<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\KasproAlert;

class SidebarComposer
{
    public function compose(View $view)
    {
        $unreadCount = 0;
        if (Auth::check()) {
            $unreadCount = KasproAlert::where('user_id', Auth::id())
                ->where('is_read', false)
                ->count();
        }
        
        $view->with('unreadAlertsCount', $unreadCount);
    }
}