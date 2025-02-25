<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sorteio da Rifa</title>
    <link rel="stylesheet" href="css/sorteio.css">
</head>

<body>
    <div class="top-bg"></div>
    <div class="container">
        <header class="header">
            <h1>Sorteio da Rifa</h1>
            <p>Clique no botão para sortear o número vencedor</p>
        </header>

        <div class="draw-card">
            <div class="number-display" id="numberDisplay">0</div>
            <button class="draw-button" id="drawButton">Sortear Número</button>
        </div>

        <div class="winner-card" id="winnerCard">
            <h2 style="color: #008374; margin-bottom: 15px;">Ganhador!</h2>
            <div class="winner-info">
                <strong>Nome:</strong> <span id="winnerName">-</span>
            </div>
            <div class="winner-info">
                <strong>Telefone:</strong> <span id="winnerPhone">-</span>
            </div>
            <div class="winner-info">
                <strong>Número:</strong> <span id="winnerNumber">-</span>
            </div>
            <div class="winner-info">
                <strong>Vendido por:</strong> <span id="winnerSeller">-</span>
            </div>
        </div>
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
        const numberDisplay = document.getElementById('numberDisplay');
        const drawButton = document.getElementById('drawButton');
        const winnerCard = document.getElementById('winnerCard');

        let raffleData = [];

        // Função para buscar as rifas disponíveis do banco de dados
        async function fetchRaffleData() {
            try {
                const response = await fetch('get_rifas.php');
                raffleData = await response.json();

                if (raffleData.length === 0) {
                    alert("Nenhuma rifa disponível para sorteio.");
                    drawButton.disabled = true;
                }
            } catch (error) {
                console.error("Erro ao buscar rifas:", error);
            }
        }

        // Carregar rifas assim que a página abrir
        fetchRaffleData();

        let isDrawing = false;

        drawButton.addEventListener('click', () => {
            if (isDrawing || raffleData.length === 0) return;

            isDrawing = true;
            drawButton.disabled = true;
            winnerCard.classList.remove('visible');

            let counter = 0;
            const duration = 3000; // 3 segundos
            const interval = 50; // Atualização a cada 50ms
            const totalSteps = duration / interval;

            const animation = setInterval(() => {
                counter++;
                const randomNum = Math.floor(Math.random() * 100);
                numberDisplay.textContent = randomNum;
                numberDisplay.style.animation = 'numberAnimation 0.1s ease';

                if (counter >= totalSteps) {
                    clearInterval(animation);
                    showWinner();
                }

                setTimeout(() => {
                    numberDisplay.style.animation = '';
                }, 100);
            }, interval);
        });

        function shuffleArray(array) {
            for (let i = array.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [array[i], array[j]] = [array[j], array[i]]; // Troca os elementos
            }
        }

        function showWinner() {
            if (raffleData.length === 0) {
                alert("Nenhuma rifa disponível para sorteio.");
                return;
            }

            // Primeiro, embaralha a lista de rifas para garantir uma escolha mais justa
            shuffleArray(raffleData);

            // Seleciona o primeiro da lista embaralhada
            const winner = raffleData[0];

            // Atualiza os dados na tela
            numberDisplay.textContent = winner.number;
            document.getElementById('winnerName').textContent = winner.name;
            document.getElementById('winnerPhone').textContent = winner.phone;
            document.getElementById('winnerNumber').textContent = winner.number;
            document.getElementById('winnerSeller').textContent = winner.seller;

            // Exibir o cartão do ganhador
            setTimeout(() => {
                winnerCard.classList.add('visible');
                isDrawing = false;
                drawButton.disabled = false;
            }, 500);
        }

    </script>
</body>

</html>