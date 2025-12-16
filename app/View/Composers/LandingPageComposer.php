<?php

namespace App\View\Composers;

use Illuminate\View\View;
use App\View\Composers\Concerns\HasLanguageData;

class LandingPageComposer
{
    use HasLanguageData;

    public function compose(View $view): void
    {
        $data = $this->getLanguageData();

        $user = auth()->user();
        $isAdmin = $user && method_exists($user, 'isAdmin') && $user->isAdmin();

        $data['user'] = $user;
        $data['isAdmin'] = $isAdmin;

        $view->with($data);
    }
}
