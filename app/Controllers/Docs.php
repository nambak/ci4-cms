<?php

namespace App\Controllers;

class Docs extends BaseController
{
    public function api(): string
    {
        return view('docs/api');
    }
}
