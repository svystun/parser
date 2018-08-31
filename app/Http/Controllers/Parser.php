<?php namespace App\Http\Controllers;

use App\Services\ParseJsonService;

/**
 * Class Parser
 * @package App\Http\Controllers
 */
class Parser extends Controller
{
    public function index(ParseJsonService $parseJsonService)
    {
        try {
            $parseJsonService->loadJsonFile('result.json');
            $parseJsonService->storeRootDomains();
            $parseJsonService->storeRelatedDomains();
            return [
                'status' => 'ok',
                'message' => 'Successfully parsed'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

}
