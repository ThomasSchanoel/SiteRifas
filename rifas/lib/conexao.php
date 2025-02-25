<?php
//UPDATE rifas SET nome_vendedor=NULL, nome_comprador=NULL, telefone_comprador=NULL, pagamento=NULL, status=0 WHERE status = 1

$host = "venda_rifas.mysql.dbaas.com.br";
$db = "venda_rifas";
$user = "venda_rifas";
$pass = "Akbdkh1605#";

/*
$host = "localhost";
$db = "controlegastos";
$user = "root";
$pass = "";
*/

$mysqli = new mysqli($host, $user, $pass, $db);
if($mysqli->connect_errno){
    die("Falha na conexão com o banco de dados.");
}
$mysqli->set_charset("utf8mb4");

$calouros = array("Miguel","Helena","Bianca","Giovanna","Kamili","Matheus Calouro", "Duda");
$veteranos = array("Ryan","Carol","Vitor","Vitória","Matheus Veterano",
 "Camily", "Aguida","Bia linda","Briza","Izadora","Júlia","Nicoly");

$preco = 10;

$url = "https://tschanoel.com.br/rifas/";
//https://tschanoel.com.br/rifas/
$url_index = "{$url}index.php";

function validarTelefone($telefone) {
    // Remove todos os caracteres que não são números
    $numeroLimpo = preg_replace('/\D/', '', $telefone);

    // Verifica se tem exatamente 11 dígitos
    if (strlen($numeroLimpo) === 11) {
        return true; // Telefone válido
    } else {
        return false; // Telefone inválido
    }
}
function formatar_telefone($telefone) {
    $numeroLimpo = preg_replace('/\D/', '', $telefone);
    $ddd = substr($numeroLimpo, 0, 2);
    $parte1 = substr($numeroLimpo, 2, 5);
    $parte2 = substr($numeroLimpo, 7);
    return "($ddd) $parte1-$parte2";
}

