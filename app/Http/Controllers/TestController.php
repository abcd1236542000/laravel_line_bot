<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Test\FamiportService;

class TestController extends Controller
{
    protected $famiportService;
    public function __construct(
        FamiportService $famiportService
    ) {
        $this->famiportService = $famiportService;
	}

    public function famiport(Request $request, $ec_order)
    {
        try {
            $data = $this->famiportService->getOrderInfo($ec_order);
            return $this->responseMaker($data, 200);
        } catch (Exception $e) {
            return $this->responseMaker(null, 403, $e->getMessage());
        }
     
    }
}