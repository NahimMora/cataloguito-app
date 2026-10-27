<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TemplateController extends Controller
{
    public function index()
    {
        return view('templates.index', ['styles' => config('cataloguito.styles')]);
    }

    public function show(Request $request, string $style)
    {
        $template = config('cataloguito.styles.'.$style);
        abort_unless($template, 404);

        $selectedColor = (string) $request->query('color', $template['accent']);
        if (! array_key_exists($selectedColor, $template['palettes'])) {
            $selectedColor = $template['accent'];
        }

        return response()->view('templates.show', compact('template', 'style', 'selectedColor'))->header('X-Robots-Tag', 'noindex, follow');
    }
}
