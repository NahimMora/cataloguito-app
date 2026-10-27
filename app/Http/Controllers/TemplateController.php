<?php

namespace App\Http\Controllers;

class TemplateController extends Controller
{
    public function index()
    {
        return view('templates.index', ['styles' => config('cataloguito.styles')]);
    }

    public function show(string $style)
    {
        $template = config('cataloguito.styles.'.$style);
        abort_unless($template, 404);

        return response()->view('templates.show', compact('template', 'style'))->header('X-Robots-Tag', 'noindex, follow');
    }
}
