<?php
include("lib/conexao.php");

$sql = "SELECT * FROM rifas WHERE status = 1";
$rifas = $mysqli->query($sql) or die($mysqli->error);

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rifas Recentes</title>
    <link rel="stylesheet" href="css/rifas.css">
</head>

<body>
    <div class="top-bg"></div>
    <div class="container">
        <header class="header">
            <h1>Rifas Recentes</h1>
            <p>Últimas rifas vendidas</p>
        </header>

        <div class="search-container">
            <input type="text" class="search-input" id="searchInput" placeholder="Pesquisar rifas..." onkeyup="searchRifas()">
        </div>

        <?php while ($rifa = $rifas->fetch_assoc()) { ?>
            <div class="raffle-card">
                <div class="raffle-header">
                    <span class="raffle-number"><a href=<?php echo "{$url}editar_rifa.php?id="; echo $rifa['id']; ?>>Rifa #<?php echo $rifa['id']; ?></a></span>
                </div>
                <div class="raffle-info">
                    <strong>Comprador:</strong> <?php echo $rifa['nome_comprador']; ?>
                </div>
                <div class="raffle-info">
                    <strong>Telefone:</strong> <?php echo $rifa['telefone_comprador']; ?>
                </div>
                <div class="raffle-info">
                    <strong>Pagamento:</strong> <?php echo $rifa['pagamento']; ?>
                </div>
                <div class="raffle-seller">
                    Vendido por: <?php echo $rifa['nome_vendedor']; ?>
                </div>
            </div>
        <?php } ?>
    </div>

    <nav class="navigation">
            <a href=<?php echo "{$url}index.php"?> class="nav-item active"><img src="icones/home.png"
                    alt="Home" width="35"></a>
            <a href=<?php echo "{$url}cadastrar_rifa.php"?> class="nav-item"><img src="icones/rifa.png"
                    alt="Cadastrar" width="40"></a>
            <a href=<?php echo "{$url}rifas.php"?> class="nav-item"><img src="icones/total.png" alt="Rifas"
                    width="35"></a>
            <a href=<?php echo "{$url}sorteio.php"?> class="nav-item"><img src="icones/sorteio.png"
                    alt="Sorteio" width="35"></a>
        </nav>

    <script>
        function searchRifas() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            const cards = document.getElementsByClassName('raffle-card');

            for (let card of cards) {
                const text = card.textContent.toLowerCase();
                if (text.includes(filter)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            }
        }
    </script>
</body>

</html>