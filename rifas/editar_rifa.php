<?php
include("lib/conexao.php");
require 'lib/mail.php'; // Arquivo onde está sua função enviarEmail
require 'lib/fpdf/fpdf.php';

$id = intval($_GET['id']);

$sql = "SELECT * FROM rifas WHERE id = '$id'";
$query = $mysqli->query($sql) or die($mysqli->error);
$row = $query->fetch_assoc();
$nomeA = $row['nome_comprador'];
$telefoneA = $row['telefone_comprador'];
$vendedorA = $row['nome_vendedor'];
$pagamentoA = $row['pagamento'];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $erro = false;

    if (isset($_POST['editar'])) {
        $nome = $_POST['nome'];
        $telefone = $_POST['telefone'];
        $vendedor = $_POST['vendedor'];
        $pagamento = $_POST['pagamento'];

        if (!validarTelefone($telefone)) {
            $erro = "Telefone inválido!";
        }

        if (!$erro) {
            $telefone = formatar_telefone($telefone);

            $sql_code = "UPDATE rifas SET nome_vendedor = '$vendedor', nome_comprador = '$nome', telefone_comprador = '$telefone', pagamento = '$pagamento', status = 1 WHERE id = $id";
            $deu_certo = $mysqli->query($sql_code) or die($mysqli->error);

            if ($deu_certo) {
                // Criando o PDF com a lista de rifas
                $arquivoPDF = "rifas_lista.pdf";
                gerarPDF($arquivoPDF, $mysqli);

                // Enviando o e-mail com o PDF
                $destinatario = "rifasucp@gmail.com"; // Altere para o e-mail desejado
                $assunto = "Rifa #" . $id . " Atualizada";

                $mensagem = "<p>Uma rifa foi atualizada no sistema.</p>
                <p>Nome do comprador anterior: <strong>$nomeA</strong></p>
                <p>Telefone anterior: <strong>$telefoneA</strong></p>
                <p>Vendedor anterior: <strong>$vendedorA</strong></p>
                <p>Forma de pagamento anterior: <strong>$pagamentoA</strong></p>
                <p>---------------------------------------</p>
                <p>Nome do comprador atualizada: <strong>$nome</strong></p>
                <p>Telefone atualizado: <strong>$telefone</strong></p>
                <p>Vendedor atualizado: <strong>$vendedor</strong></p>
                <p>Forma de pagamento atualizado: <strong>$pagamento</strong></p>";
                $anexos = [$arquivoPDF];

                enviarEmail($destinatario, $assunto, $mensagem, "Administrador", $anexos);

                echo "<script>alert('Rifa atualizada com sucesso!'); window.location.href='$url_index';</script>";
            } else {
                $erro = "Erro ao atualizar a rifa.";
            }
        }
    } elseif (isset($_POST['liberar'])) {
        // Lógica para liberar a rifa
        $sql_liberar = "UPDATE rifas SET nome_vendedor = NULL, nome_comprador = NULL, telefone_comprador = NULL, pagamento = NULL, status = 0 WHERE id = $id";
        $deu_certo = $mysqli->query($sql_liberar) or die($mysqli->error);

        if ($deu_certo) {
            // Criando o PDF com a lista de rifas
            $arquivoPDF = "rifas_lista.pdf";
            gerarPDF($arquivoPDF, $mysqli);

            // Enviando o e-mail com o PDF
            $destinatario = "rifasucp@gmail.com"; // Altere para o e-mail desejado
            $assunto = "Rifa #" . $id . " Liberada";

            $mensagem = "<p>Uma rifa foi liberada do sistema.</p>
            <p>Nome do comprador: <strong>$nomeA</strong></p>
            <p>Telefone: <strong>$telefoneA</strong></p>
            <p>Vendedor: <strong>$vendedorA</strong></p>
            <p>Forma de pagamento: <strong>$pagamentoA</strong></p>";
            $anexos = [$arquivoPDF];

            enviarEmail($destinatario, $assunto, $mensagem, "Administrador", $anexos);

            echo "<script>alert('Rifa liberada com sucesso!'); window.location.href='$url_index';</script>";
        } else {
            $erro = "Erro ao liberar a rifa.";
        }
    }

}

// Função para gerar PDF da lista de rifas
function gerarPDF($arquivo, $mysqli)
{
    date_default_timezone_set('America/Sao_Paulo');
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Rifas Atualizadas - ' . date("d/m/Y H:i"), 0, 1, 'C');
    $pdf->Ln(10);

    // Cabeçalho da tabela
    $pdf->Cell(10, 10, 'ID', 1);
    $pdf->Cell(65, 10, 'Comprador', 1);
    $pdf->Cell(40, 10, 'Telefone', 1);
    $pdf->Cell(50, 10, 'Vendedor', 1);
    $pdf->Cell(25, 10, 'Pagamento', 1);
    $pdf->Ln();

    // Buscando rifas no banco de dados
    $sql = "SELECT * FROM rifas ORDER BY id ASC";
    $result = $mysqli->query($sql);

    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(10, 10, $row['id'], 1);
        $pdf->Cell(65, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', (string) $row['nome_comprador']), 1);
        $pdf->Cell(40, 10, (string) $row['telefone_comprador'], 1);
        $pdf->Cell(50, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', (string) $row['nome_vendedor']), 1);
        $pdf->Cell(25, 10, $row['pagamento'], 1);
        $pdf->Ln();
    }
    // Salvando o arquivo PDF
    $pdf->Output('F', $arquivo);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, maximum-scale=1.0, user-scalable=no">
    <title>Editar Rifa</title>
    <link rel="stylesheet" href="css/cadastrar_rifa.css">
</head>

<body>
    <div class="card">
        <h1 class="title">Editar Rifa #<?php echo $id; ?></h1>

        <?php if (isset($erro) && $erro): ?>
            <div class="error"><?php echo $erro; ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-group">
                <label for="nome">Nome Comprador</label>
                <input value="<?php echo $row['nome_comprador']; ?>" type="text" id="nome" name="nome"
                    placeholder="Digite o nome" required autocomplete="name">
            </div>

            <div class="form-group">
                <label for="telefone">Telefone</label>
                <input value="<?php echo $row['telefone_comprador']; ?>" type="tel" id="telefone" name="telefone"
                    placeholder="12 123451234" required autocomplete="tel" pattern="[0-9() -]*">
            </div>

            <div class="form-group">
                <label for="vendedor">Quem está vendendo</label>
                <select id="vendedor" name="vendedor" required>
                    <option value="">-- Selecione --</option>
                    <?php
                    $vendedores = array_merge($calouros, $veteranos);
                    $vendedorSelecionado = $row['nome_vendedor'];

                    foreach ($vendedores as $vendedor) {
                        $selected = ($vendedor == $vendedorSelecionado) ? 'selected' : '';
                        echo "<option value=\"$vendedor\" $selected>$vendedor</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="pagamento">Pagamento</label>
                <select id="pagamento" name="pagamento" required>
                    <option value="">-- Selecione --</option>
                    <?php
                    $formasPagamento = ["Pix", "Dinheiro"];
                    $pagamentoSelecionado = $row['pagamento'];

                    foreach ($formasPagamento as $forma) {
                        $selected = ($forma == $pagamentoSelecionado) ? 'selected' : '';
                        echo "<option value=\"$forma\" $selected>$forma</option>";
                    }
                    ?>
                </select>
            </div>

            <button type="submit" name="editar" class="submit-btn">Confirmar</button>
            <button type="submit" name="liberar" class="submit-btn liberar-btn">Liberar Rifa</button>
            <a href= <?php echo "{$url}index.php"?> class="back-link">Voltar para Home</a>

        </form>
    </div>
    <script>
        function bloquearBotoesAposEnvio() {
            setTimeout(() => {
                const botoes = document.querySelectorAll("button[type='submit']");

                botoes.forEach(botao => {
                    botao.disabled = true; // Desativa o botão
                    botao.style.backgroundColor = "#ccc"; // Muda a cor para cinza
                    botao.style.cursor = "not-allowed"; // Altera o cursor
                });
            }, 100); // Aguarda um pequeno tempo para garantir que a ação ocorra antes do bloqueio
        }

        function confirmarLiberacao(event) {
            if (!confirm("Tem certeza que deseja liberar esta rifa?")) {
                event.preventDefault(); // Cancela o envio do formulário se o usuário clicar em "Cancelar"
            } else {
                bloquearBotoesAposEnvio(); // Se confirmado, bloqueia os botões após o envio
            }
        }

        document.addEventListener("DOMContentLoaded", () => {
            const formulario = document.querySelector("form");
            const btnLiberar = document.querySelector("button[name='liberar']");

            if (formulario) {
                formulario.addEventListener("submit", bloquearBotoesAposEnvio);
            }

            if (btnLiberar) {
                btnLiberar.addEventListener("click", confirmarLiberacao);
            }
        });
    </script>
</body>
</html>