<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use App\Http\Controllers\Controller;
use App\Models\Budget;
use App\Models\Transaction;
use App\Traits\HomeTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class HomeApiController extends AppBaseController
{

    use HomeTrait;

    public function detailDaily(): JsonResponse
    {
        $disponibleHoy = $this->getDisponibleHoy();
        $mensajeDiario = $this->getDaylyCoatch();

        $data = [
            'disponible_hoy' => $disponibleHoy,
            'gastado_hoy' => $mensajeDiario['total_gastado_hoy'],
//            'mensaje_diario' => $mensajeDiario['respuesta_ai'],
            'estado_alerta' => $mensajeDiario['estado_alerta'],
        ];

        return $this->sendResponse($data, 'Disponible hoy calculado correctamente.');

    }
}
