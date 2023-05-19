<?php
// Configurações do banco de dados
$host = 'localhost';
$username = 'root';
$password = '0000';
$dbname = 'apirest';

// Criar conexão
$db = mysqli_connect($host, $username, $password, $dbname);

// Verificar se ocorreu um erro na conexão
if (mysqli_connect_errno()) {
    echo 'Erro na conexão com o banco de dados: ' . mysqli_connect_error();
    exit;
}
