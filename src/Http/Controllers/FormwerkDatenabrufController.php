<?php

declare(strict_types=1);

namespace Hwkdo\IntranetAppFormwerk\Http\Controllers;

use Hwkdo\IntranetAppFormwerk\Services\FormwerkDatenabrufService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class FormwerkDatenabrufController extends Controller
{
    public function __construct(
        private readonly FormwerkDatenabrufService $service,
    ) {}

    public function gewerke(Request $request): JsonResponse
    {
        return response()->json(
            $this->service->gewerke($request->query('key'))
        );
    }

    public function rechtsformen(Request $request): JsonResponse
    {
        return response()->json(
            $this->service->rechtsformen($request->query('key'))
        );
    }

    public function eintragungsvoraussetzung(Request $request): JsonResponse
    {
        return response()->json(
            $this->service->eintragungsvoraussetzung($request->query('key'))
        );
    }

    public function betrieb(Request $request, string $nr): Response
    {
        $data = $this->service->betrieb($nr, $request->query('key'));

        $html = view('intranet-app-formwerk::datenabrufe.betrieb', $data)->render();

        return response($html)->withHeaders([
            'Access-Control-Allow-Origin' => '*',
            'Content-Type' => 'text/html',
        ]);
    }
}
