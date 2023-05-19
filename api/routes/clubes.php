<?php

// Importar o arquivo de configuração do banco de dados
require_once 'config/database.php';

// Definir rota para listar todos os clubes
$app->get('/clubes', function () use ($app, $db) {
    $query = "SELECT * FROM Clube";
    $result = mysqli_query($db, $query);
    
    if (!$result) {
        $app->response()->setStatus(500);
        $app->response()->setBody('Erro ao listar clubes');
        return;
    }
    
    $clubes = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $clubes[] = $row;
    }
    
    $app->response()->setStatus(200);
    $app->response()->setBody(json_encode($clubes));
});

// Definir rota para cadastrar um clube
$app->post('/clubes', function () use ($app, $db) {
    $request = json_decode($app->request()->getBody());
    
    if (!isset($request->clube) || !isset($request->saldo_disponivel)) {
        $app->response()->setStatus(400);
        $app->response()->setBody('Dados do clube incompletos');
        return;
    }
    
    $clube = mysqli_real_escape_string($db, $request->clube);
    $saldo_disponivel = mysqli_real_escape_string($db, $request->saldo_disponivel);
    
    $query = "INSERT INTO Clube (clube, saldo_disponivel) VALUES ('$clube', '$saldo_disponivel')";
    $result = mysqli_query($db, $query);
    
    if (!$result) {
        $app->response()->setStatus(500);
        $app->response()->setBody('Erro ao cadastrar clube');
        return;
    }
    
    $app->response()->setStatus(200);
    $app->response()->setBody('Clube cadastrado com sucesso');
});

?>
