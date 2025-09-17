<?php

namespace App\Export;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class HtmlExport implements FromView
{
    private $data;
    private $page;

    public function __construct($data, $page)
    {
        $this->data = $data;
        $this->page = $page;
    }

    public function view(): View
    {
        return view($this->page, [
            'data' => $this->data
        ]);
    }
}
