<?php

namespace App\View\Composers;

use Illuminate\View\View;
use App\View\Composers\Concerns\HasLanguageData;

class GuestLayoutComposer
{
    use HasLanguageData;

    public function compose(View $view): void
    {
        $data = $this->getLanguageData();
        $view->with($data);
    }
}
