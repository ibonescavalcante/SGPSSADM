<?php

namespace App\controllers;


use App\Models\Naturalidade;


class NaturalidadeController  extends Controller
{
  public function buscarCidadesPorEstado()
  {
    if (!isset($_GET['estado_id'])) {
      http_response_code(400);
      echo json_encode(["erro" => "ID do estado não informado"]);
      exit;
    }

    $estadoID = intval($_GET['estado_id']);

    try {
      $cidades = Naturalidade::buscarNaturalidade($estadoID);
      echo json_encode($cidades);
    } catch (\Exception $e) {
      http_response_code(500);
      echo json_encode(["erro" => "Erro ao buscar cidades"]);
    }
  }
}
