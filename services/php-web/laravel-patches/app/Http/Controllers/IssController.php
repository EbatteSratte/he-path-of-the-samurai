<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IssController extends Controller
{
    private function base(): string { return getenv('RUST_BASE') ?: 'http://rust_iss:3000'; }

    private function getJson(string $url): array {
        $raw = @file_get_contents($url);
        return $raw ? (json_decode($raw, true) ?: []) : [];
    }

    public function index()
    {
        $b = $this->base();
        $iss = $this->getJson($b.'/last');
        return view('iss', ['iss' => $iss]);
    }
}
