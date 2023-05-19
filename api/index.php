<?php

// Definir as configurações do banco de dados
$host = 'localhost';
$username = 'root';
$password = '0000';
$dbname = 'apirest';

// Criar a conexão com o banco de dados
$conn = new mysqli($host, $username, $password, $dbname);

// Verificar a conexão
if ($conn->connect_error) {
    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
}

// Definir o cabeçalho da resposta como JSON
header("Content-Type: application/json");

// Verificar o método da requisição
$method = $_SERVER["REQUEST_METHOD"];

// Rota /clubes
if ($_SERVER["REQUEST_URI"] === "/clubes") {
    // Rota GET /clubes
    if ($method === "GET") {
        $sql = "SELECT * FROM clubes";
        $result = $conn->query($sql);
        
        $clubes = array();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $clubes[] = array(
                    "id" => $row["id"],
                    "clube" => $row["clube"],
                    "saldo_disponivel" => $row["saldo_disponivel"]
                );
            }
        }
        
        echo json_encode($clubes);
    }
    // Rota POST /clubes
    elseif ($method === "POST") {
        $data = json_decode(file_get_contents("php://input"), true);
        
        $clube = $data["clube"];
        $saldo_disponivel = $data["saldo_disponivel"];
        
        $sql = "INSERT INTO clubes (clube, saldo_disponivel) VALUES ('$clube', '$saldo_disponivel')";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(array("message" => "Clube cadastrado com sucesso"));
        } else {
            echo json_encode(array("message" => "Erro ao cadastrar o clube: " . $conn->error));
        }
    }
}

// Rota /consumo
elseif ($_SERVER["REQUEST_URI"] === "/consumo") {
    // Rota POST /consumo
    if ($method === "POST") {
        $data = json_decode(file_get_contents("php://input"), true);
        
        $clube_id = $data["clube_id"];
        $recurso_id = $data["recurso_id"];
        $valor_consumo = $data["valor_consumo"];
        
        // Verificar o saldo do clube
        $sql = "SELECT saldo_disponivel FROM clubes WHERE id = $clube_id";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $saldo_disponivel = $row["saldo_disponivel"];
            
            // Verificar se o saldo é suficiente para o consumo
            if ($saldo_disponivel >= $valor_consumo) {
                // Atualizar o saldo do clube
                $novo_saldo = $saldo_disponivel - $valor_consumo;
                $sql = "UPDATE clubes SET saldo_disponivel = $novo_saldo WHERE id = $clube_id";
                $conn->query($sql);
                
                // Atualizar o saldo do recurso
                $sql = "SELECT saldo_disponivel FROM recursos WHERE id = $recurso_id";
                $result = $conn->query($sql);
                
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $saldo_recurso = $row["saldo_disponivel"];
                    
                    $novo_saldo_recurso = $saldo_recurso - $valor_consumo;
                    $sql = "UPDATE recursos SET saldo_disponivel = $novo_saldo_recurso WHERE id = $recurso_id";
                    $conn->query($sql);
                    
                    echo json_encode(array(
                        "message" => "Recurso consumido com sucesso",
                        "clube" => $clube_id,
                        "saldo_anterior" => $saldo_disponivel,
                        "saldo_atual" => $novo_saldo
                    ));
                } else {
                    echo json_encode(array("message" => "Recurso não encontrado"));
                }
            } else {
                echo json_encode(array("message" => "O saldo disponível do clube é insuficiente"));
            }
        } else {
            echo json_encode(array("message" => "Clube não encontrado"));
        }
    }
}

// Fechar a conexão com o banco de dados
$conn->close();
