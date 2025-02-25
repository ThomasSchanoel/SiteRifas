<?php
include("lib/conexao.php");
require 'lib/mail.php'; // Arquivo onde está sua função enviarEmail
require 'lib/fpdf/fpdf.php';

$sql = "SELECT id FROM rifas WHERE status = 0";
$query = $mysqli->query($sql) or die($mysqli->error);

if (count($_POST) > 0) {
    $erro = false;
    $nome = $_POST['nome'];
    $telefone = $_POST['telefone'];
    $vendedor = $_POST['vendedor'];
    $rifas = $_POST['rifas'];
    $pagamento = $_POST['pagamento'];

    if (!validarTelefone($telefone)) {
        $erro = "Telefone inválido!";
    }

    if (!$erro) {
        $telefone = formatar_telefone($telefone);
        
        // Start transaction
        $mysqli->begin_transaction();
        
        $all_available = true;
        $unavailable_rifas = [];
        
        // First, check if ALL rifas are available
        foreach ($rifas as $r) {
            $check_sql = "SELECT status FROM rifas WHERE id = $r AND status = 0 FOR UPDATE";
            $check_result = $mysqli->query($check_sql);
            
            if ($check_result->num_rows == 0) {
                // Rifa is no longer available
                $unavailable_rifas[] = $r;
                $all_available = false;
            }
        }
        
        // Only proceed with updates if ALL rifas are available
        if ($all_available) {
            $update_success = true;
            
            foreach ($rifas as $r) {
                $sql_code = "UPDATE rifas SET nome_vendedor = '$vendedor', 
                            nome_comprador = '$nome', 
                            telefone_comprador = '$telefone', 
                            pagamento = '$pagamento', 
                            status = 1 
                            WHERE id = $r";
                
                $update_result = $mysqli->query($sql_code);
                if (!$update_result) {
                    $update_success = false;
                    break;
                }
            }
            
            if ($update_success) {
                // All updates succeeded, commit the transaction
                $mysqli->commit();
                
                // Continue with PDF generation and email
                $arquivoPDF = "rifas_lista.pdf";
                gerarPDF($arquivoPDF, $mysqli);
                
                // Enviando o e-mail com o PDF
                $destinatario = "rifasucp@gmail.com"; // Altere para o e-mail desejado
                if (count($rifas) > 1) {
                    $assunto = "Rifas #" . implode(', #', $rifas) . " Cadastradas";
                } else {
                    $assunto = "Rifa #" . implode(', #', $rifas) . " Cadastrada";
                }
                $mensagem = "<p>Uma nova rifa foi cadastrada no sistema.</p><p>Nome do comprador: <strong>$nome</strong></p><p>Telefone: <strong>$telefone</strong></p><p>Vendedor: <strong>$vendedor</strong></p><p>Forma de pagamento: <strong>$pagamento</strong></p>";
                $anexos = [$arquivoPDF];
                enviarEmail($destinatario, $assunto, $mensagem, "Administrador", $anexos);
                
                echo "<script>alert('Rifa cadastrada com sucesso!'); window.location.href='$url_index';</script>";
            } else {
                // Update failed, rollback changes
                $mysqli->rollback();
                $erro = "Erro ao cadastrar as rifas.";
            }
        } else {
            // Not all rifas are available, rollback and show error
            $mysqli->rollback();
            if (count($unavailable_rifas) > 1) {
                $erro = "As rifas #" . implode(', #', $unavailable_rifas) . " já não estão mais disponíveis. Por favor, atualize a página e escolha outras rifas.";
            } else {
                $erro = "A rifa #" . implode(', #', $unavailable_rifas) . " já não está mais disponível. Por favor, atualize a página e escolha outra rifa.";
            }
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Cadastrar Nova Rifa</title>
    <link rel="stylesheet" href="css/cadastrar_rifa.css">
</head>

<body>
    <div class="card">
        <h1 class="title">Cadastrar Nova<br>Rifa</h1>

        <?php if (isset($erro) && $erro): ?>
            <div class="error"><?php echo $erro; ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-group">
                <label for="nome">Nome Comprador</label>
                <input type="text" id="nome" name="nome" placeholder="Digite o nome" required autocomplete="name">
            </div>

            <div class="form-group">
                <label for="telefone">Telefone</label>
                <input type="tel" id="telefone" name="telefone" placeholder="12 123451234" required autocomplete="tel"
                    pattern="[0-9() -]*">
            </div>

            <div class="form-group">
                <label for="vendedor">Quem está vendendo</label>
                <select id="vendedor" name="vendedor" required>
                    <option value="">-- Selecione --</option>
                    <?php
                    $vendedores = array_merge($calouros, $veteranos);
                    foreach ($vendedores as $vendedor) {
                        echo "<option value=\"$vendedor\">$vendedor</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label>Rifas</label>
                <div class="raffle-list">
                    <?php while ($row = $query->fetch_assoc()) { ?>
                        <div class="raffle-item">
                            <input type="checkbox" id="rifa-<?php echo $row['id']; ?>" name="rifas[]"
                                value="<?php echo $row['id']; ?>">
                            <label for="rifa-<?php echo $row['id']; ?>">Rifa: <?php echo $row['id']; ?></label>
                        </div>
                    <?php } ?>
                </div>
            </div>

            <div class="selected-count">Quantidade: Nenhuma</div>
            <div class="sold-tickets">Rifas Selecionadas: Nenhuma</div>

            <div class="form-group">
                <label for="pagamento">Pagamento</label>
                <select id="pagamento" name="pagamento" required>
                    <option value="">-- Selecione --</option>
                    <option value="Pix">Pix</option>
                    <option value="Dinheiro">Dinheiro</option>
                </select>
            </div>

            <form action="" method="POST" id="formCadastrar" onsubmit="bloquearBotao()">
                <button type="submit" class="submit-btn" id="btnCadastrar">Cadastrar</button>
            </form>
            <a href= <?php echo "{$url}index.php"?> class="back-link">Voltar para Home</a>
        </form>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const checkboxes = document.querySelectorAll('input[name="rifas[]"]');
            const selectedCount = document.querySelector('.selected-count');
            const soldNumbers = document.querySelector('.sold-tickets');

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', () => {
                    const checkedBoxes = document.querySelectorAll('input[name="rifas[]"]:checked');
                    const count = checkedBoxes.length;
                    const selectedNumbers = Array.from(checkedBoxes).map(box => box.value).join(', ');

                    selectedCount.textContent = `Quantidade: ${count === 0 ? 'Nenhuma' : count}`;
                    soldNumbers.textContent = `Rifas Selecionadas: ${count === 0 ? 'Nenhuma' : selectedNumbers}`;
                });
            });

            // Prevent form submission if no rifas are selected
            document.querySelector('form').addEventListener('submit', function (e) {
                const checkedBoxes = document.querySelectorAll('input[name="rifas[]"]:checked');
                if (checkedBoxes.length === 0) {
                    e.preventDefault();
                    alert('Por favor, selecione pelo menos uma rifa.');
                } else {
                    bloquearBotao(); // Chama a função para bloquear o botão
                }
            });
        });

        function bloquearBotao() {
            let botao = document.getElementById("btnCadastrar");

            botao.disabled = true;  // Desativa o botão
            botao.style.backgroundColor = "#a0a0a0"; // Muda para cor cinza
            botao.style.cursor = "not-allowed"; // Altera o cursor para indicar indisponível

            // Reabilita o botão após 3 segundos
            setTimeout(() => {
                botao.disabled = false; // Reabilita o botão
                botao.style.backgroundColor = ""; // Restaura a cor original
                botao.style.cursor = ""; // Restaura o cursor original
            }, 6000); // Tempo em milissegundos
        }
    </script>
</body>

</html>