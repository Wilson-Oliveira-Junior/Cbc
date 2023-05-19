<?php

// Importar o arquivo de configuração do banco de dados
require_once '../config/database.php';

// Definir rota para listar todos os recursos
$app->get('/recursos', function () use ($app, $db) {
    $query = "SELECT * FROM Recurso";
    $result = mysqli_query($db, $query);
    
    if (!$result) {
        $app->response()->setStatus(500);
        $app->response()->setBody('Erro ao listar recursos');
        return;
    }
    
    $recursos = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $recursos[] = $row;
    }
    
    $app->response()->setStatus(200);
    $app->response()->setBody(json_encode($recursos));
});

// Definir rota para consumir recursos
$app->post('/consumir-recurso', function () use ($app, $db) {
    $request = json_decode($app->request()->getBody());
    
    if (!isset($request->clube_id) || !isset($request->recurso_id) || !isset($request->valor_consumo)) {
        $app->response()->setStatus(400);
        $app->response()->setBody('Dados de consumo incompletos');
        return;
    }
    
    $clube_id = mysqli_real_escape_string($db, $request->clube_id);
    $recurso_id = mysqli_real_escape_string($db, $request->recurso_id);
    $valor_consumo = mysqli_real_escape_string($db, $request->valor_consumo);
    
    // Verificar saldo disponível do clube
    $saldo_query = "SELECT saldo_disponivel FROM Clube WHERE id = $clube_id";
    $saldo_result = mysqli_query($db, $saldo_query);
    
    if (!$saldo_result) {
        $app->response()->setStatus(500);
        $app->response()->setBody('Erro ao verificar saldo do clube');
        return;
    }
    
    $saldo_row = mysqli_fetch_assoc($saldo_result);
    $saldo_disponivel = $saldo_row['saldo_disponivel'];
    
    if ($saldo_disponivel < $valor_consumo) {
        $app->response()->setStatus(400);
        $app->response()->setBody('O saldo disponível do clube é insuficiente');
        return;
    }
    
    // Atualizar saldo do clube
    $novo_saldo = $saldo_disponivel - $valor_consumo;
    $update_query = "UPDATE Clube SET saldo_disponivel = $novo_saldo WHERE id = $clube_id";
    $update_result = mysqli_query($db, $update_query);
    
    if (!$update_result) {
        $app->response()->setStatus(500);
        $app->response()->setBody('Erro ao atualizar saldo do clube');
        return;
    }
    
    // Atualizar saldo do recurso
    $recurso_query = "SELECT saldo_disponivel FROM Recurso WHERE id = $recurso_id";
    $recurso_result = mysqli_query($db, $recurso_query);
    
    if (!$recurso_result) {
        $app->response()->setStatus(500);
        $app->response()->setBody('Erro ao verificar saldo do recurso');
        return;
    }
    
    $recurso_row = mysqli_fetch_assoc($recurso_result);
    $recurso_saldo_disponivel = $recurso_row['saldo_disponivel'];
    
    $novo_saldo_recurso = $recurso_saldo_disponivel - $valor_consumo;
    $update_recurso_query = "UPDATE Recurso SET saldo_disponivel = $novo_saldo_recurso WHERE id = $recurso_id";
    $update_recurso_result = mysqli_query($db, $update_recurso_query);
    
    if (!$update_recurso_result) {
        $app->response()->setStatus(500);
        $app->response()->setBody('Erro ao atualizar saldo do recurso');
        return;
    }
    
    $app->response()->setStatus(200);
    $app->response()->setBody(json_encode([
        'clube' => $clube_id,
        'saldo_anterior' => $saldo_disponivel,
        'saldo_atual' => $novo_saldo
    ]));
});

?>
