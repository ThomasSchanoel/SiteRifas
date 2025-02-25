<?php
include("lib/conexao.php");

// Consulta rifas com status = 1 (vendidas)
$sql = "SELECT * FROM rifas WHERE status = 1";
$query = $mysqli->query($sql) or die(json_encode(["error" => $mysqli->error]));

$rifas = [];
while ($row = $query->fetch_assoc()) {
    $rifas[] = [
        "number" => $row["id"],
        "name" => $row["nome_comprador"],
        "phone" => $row["telefone_comprador"],
        "seller"=> $row["nome_vendedor"],
        "payment"=> $row["pagamento"]
    ];
}

// Retorna os dados em formato JSON
echo json_encode($rifas);
?>