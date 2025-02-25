<?php
include "lib/conexao.php";

$sql = "SELECT * FROM rifas WHERE status = 1";
$query = $mysqli->query($sql) or die($mysqli->error);

$totalCalouros = [0, 0, 0, 0, 0, 0, 0, 0];
$totalVeteranos = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
$total = array("total" => 0, "pix" => 0, "dinheiro" => 0);

while ($rifa = $query->fetch_assoc()) {
    switch ($rifa['nome_vendedor']) {
        case $veteranos[0]: $totalVeteranos[0]++; break;
        case $veteranos[1]: $totalVeteranos[1]++; break;
        case $veteranos[2]: $totalVeteranos[2]++; break;
        case $veteranos[3]: $totalVeteranos[3]++; break;
        case $veteranos[4]: $totalVeteranos[4]++; break;
        case $veteranos[5]: $totalVeteranos[5]++; break;
        case $veteranos[6]: $totalVeteranos[6]++; break;
        case $veteranos[7]: $totalVeteranos[7]++; break;
        case $veteranos[8]: $totalVeteranos[8]++; break;
        case $veteranos[9]: $totalVeteranos[9]++; break;
        case $veteranos[10]: $totalVeteranos[10]++; break;
        case $veteranos[11]: $totalVeteranos[11]++; break;
        case $calouros[0]: $totalCalouros[0]++; break;
        case $calouros[1]: $totalCalouros[1]++; break;
        case $calouros[2]: $totalCalouros[2]++; break;
        case $calouros[3]: $totalCalouros[3]++; break;
        case $calouros[4]: $totalCalouros[4]++; break;
        case $calouros[5]: $totalCalouros[5]++; break;
        case $calouros[6]: $totalCalouros[6]++; break;
    }

    switch ($rifa['pagamento']) {
        case 'Pix': $total['pix']++; break;  
        case 'Dinheiro': $total['dinheiro']++; break;    
    }
}

for ($i = 0; $i <= 11; $i++) {
    $totalVeteranos[12] += $totalVeteranos[$i];
}
for ($i = 0; $i <= 6; $i++) {
    $totalCalouros[7] += $totalCalouros[$i];
}

$calourosPercent = [14.28, 14.28, 14.28, 14.28, 14.28, 14.28, 14.28];
if ($totalCalouros[7] > 0) {
    $calourosPercent[0] = ($totalCalouros[0] / $totalCalouros[7]) * 100;
    $calourosPercent[1] = ($totalCalouros[1] / $totalCalouros[7]) * 100;
    $calourosPercent[2] = ($totalCalouros[2] / $totalCalouros[7]) * 100;
    $calourosPercent[3] = ($totalCalouros[3] / $totalCalouros[7]) * 100;
    $calourosPercent[4] = ($totalCalouros[4] / $totalCalouros[7]) * 100;
    $calourosPercent[5] = ($totalCalouros[5] / $totalCalouros[7]) * 100;
    $calourosPercent[6] = ($totalCalouros[6] / $totalCalouros[7]) * 100;
}

$veteranosPercent = [8.3, 8.3, 8.3, 8.3, 8.3, 8.3, 8.3, 8.3, 8.3, 8.3, 8.3, 8.3];
if ($totalVeteranos[12] > 0) {
    $veteranosPercent[0] = ($totalVeteranos[0] / $totalVeteranos[12]) * 100;
    $veteranosPercent[1] = ($totalVeteranos[1] / $totalVeteranos[12]) * 100;
    $veteranosPercent[2] = ($totalVeteranos[2] / $totalVeteranos[12]) * 100;
    $veteranosPercent[3] = ($totalVeteranos[3] / $totalVeteranos[12]) * 100;
    $veteranosPercent[4] = ($totalVeteranos[4] / $totalVeteranos[12]) * 100;
    $veteranosPercent[5] = ($totalVeteranos[5] / $totalVeteranos[12]) * 100;
    $veteranosPercent[6] = ($totalVeteranos[6] / $totalVeteranos[12]) * 100;
    $veteranosPercent[7] = ($totalVeteranos[7] / $totalVeteranos[12]) * 100;
    $veteranosPercent[8] = ($totalVeteranos[8] / $totalVeteranos[12]) * 100;
    $veteranosPercent[9] = ($totalVeteranos[9] / $totalVeteranos[12]) * 100;
    $veteranosPercent[10] = ($totalVeteranos[10] / $totalVeteranos[12]) * 100;
    $veteranosPercent[11] = ($totalVeteranos[11] / $totalVeteranos[12]) * 100;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rifas</title>
    <link rel="stylesheet" href="css/index.css">
</head>

<body>
    <div class="top-bg"></div>
    <div class="container">
        <header class="header">
            <h1>Bem vindo</h1>
            <p>Rifas UCP 2025</p>
        </header>

        <div class="card">
            <h2>Rifas</h2>
            <div class="expense-item">
                <span>Total Rifas</span>
                <span><?php echo $totalCalouros[7] + $totalVeteranos[12]; ?></span>
            </div>
            <div class="expense-item">
                <span>Total Dinheiro</span>
                <span>R$ <?php echo $total['dinheiro'] * $preco; ?></span>
            </div>
            <div class="expense-item">
                <span>Total Pix</span>
                <span>R$ <?php echo $total['pix'] * $preco; ?></span>
            </div>
        </div>

        <div class="card">
            <h2>Calouros</h2>
            <div class="chart-container">
                <canvas id="calouros"></canvas>
            </div>
        </div>

        <div class="card">
            <h2>Vendas Calouros</h2>
            <?php
            for ($i = 0; $i < count($calouros); $i++) {
                echo "<div class=\"expense-item\">";
                echo "<span>{$calouros[$i]}</span>";
                echo "<span>{$totalCalouros[$i]}</span>";
                echo "</div>";
            }
            ?>

            <div class="expense-item">  
                <span>Total</span>
                <span><?php echo $totalCalouros[7]; ?></span>
            </div>
        </div>

        <div class="card">
            <h2>Veteranos</h2>
            <div class="chart-container">
                <canvas id="veteranos"></canvas>
            </div>
        </div>

        <div class="card">
            <h2>Vendas Veteranos</h2>
            <?php
            for ($i = 0; $i < count($veteranos); $i++) {
                echo "<div class=\"expense-item\">";
                echo "<span>{$veteranos[$i]}</span>";
                echo "<span>{$totalVeteranos[$i]}</span>";
                echo "</div>";
            }
            ?>

<div class="expense-item">  
                <span>Total</span>
                <span><?php echo $totalVeteranos[12]; ?></span>
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

        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
        <script>
            // Change for the first chart (calouros)
            const ctxCalouros = document.getElementById('calouros').getContext('2d');
            new Chart(ctxCalouros, {
                type: 'pie',
                data: {
                    labels: [
                        '<?php echo $calouros[0]; ?>',
                        '<?php echo $calouros[1]; ?>',
                        '<?php echo $calouros[2]; ?>',
                        '<?php echo $calouros[3]; ?>',
                        '<?php echo $calouros[4]; ?>',
                        '<?php echo $calouros[5]; ?>',
                        '<?php echo $calouros[6]; ?>'
                    ],
                    datasets: [{
                        data: [
                            <?php echo $calourosPercent[0]; ?>,
                            <?php echo $calourosPercent[1]; ?>,
                            <?php echo $calourosPercent[2]; ?>,
                            <?php echo $calourosPercent[3]; ?>,
                            <?php echo $calourosPercent[4]; ?>,
                            <?php echo $calourosPercent[5]; ?>,
                            <?php echo $calourosPercent[6]; ?>
                        ],
                        backgroundColor: [
                            '#004F4F', '#007272', '#00B3B3',
                            '#66CCCC', '#33A1A1', '#B3FFFF',
                            '#48D1CC'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            display: true,
                            labels: {
                                usePointStyle: true,
                                pointStyle: 'circle',
                                boxWidth: 8,
                                padding: 8,
                                font: {
                                    size: 12
                                },
                                generateLabels: function (chart) {
                                    const data = chart.data;
                                    if (data.labels.length && data.datasets.length) {
                                        return data.labels.map((label, i) => {
                                            const meta = chart.getDatasetMeta(0);
                                            const style = meta.controller.getStyle(i);

                                            let shortLabel = label;
                                            if (label.length > 12) {
                                                shortLabel = label.substring(0, 10) + '...';
                                            }

                                            return {
                                                text: shortLabel,
                                                fillStyle: style.backgroundColor,
                                                strokeStyle: style.backgroundColor,
                                                lineWidth: 0,
                                                hidden: isNaN(data.datasets[0].data[i]) || meta.data[i].hidden,
                                                index: i,
                                                pointStyle: 'circle'
                                            };
                                        });
                                    }
                                    return [];
                                }
                            },
                            onClick: function (e, legendItem, legend) {
                                const index = legendItem.index;
                                const ci = legend.chart;

                                if (ci.isDatasetVisible(0)) {
                                    ci.getDatasetMeta(0).data[index].hidden = !ci.getDatasetMeta(0).data[index].hidden;
                                    ci.update();
                                }
                            },
                            maxWidth: 240,
                            maxHeight: 100
                        },
                        tooltip: {
                            callbacks: {
                                label: function (tooltipItem) {
                                    let label = tooltipItem.chart.data.labels[tooltipItem.dataIndex];
                                    let value = tooltipItem.raw;
                                    return `${label}: ${value.toFixed(2)}%`;
                                }
                            }
                        }
                    },
                    layout: {
                        padding: {
                            bottom: 30
                        }
                    }
                }
            });

            // Change for the second chart (veteranos)
            const ctxVeteranos = document.getElementById('veteranos').getContext('2d');
            new Chart(ctxVeteranos, {
                type: 'pie',
                data: {
                    labels: [
                        '<?php echo $veteranos[0]; ?>',
                        '<?php echo $veteranos[1]; ?>',
                        '<?php echo $veteranos[2]; ?>',
                        '<?php echo $veteranos[3]; ?>',
                        '<?php echo $veteranos[4]; ?>',
                        '<?php echo $veteranos[5]; ?>',
                        '<?php echo $veteranos[6]; ?>',
                        '<?php echo $veteranos[7]; ?>',
                        '<?php echo $veteranos[8]; ?>',
                        '<?php echo $veteranos[9]; ?>',
                        '<?php echo $veteranos[10]; ?>',
                        '<?php echo $veteranos[11]; ?>'
                    ],
                    datasets: [{
                        data: [
                            <?php echo $veteranosPercent[0]; ?>,
                            <?php echo $veteranosPercent[1]; ?>,
                            <?php echo $veteranosPercent[2]; ?>,
                            <?php echo $veteranosPercent[3]; ?>,
                            <?php echo $veteranosPercent[4]; ?>,
                            <?php echo $veteranosPercent[5]; ?>,
                            <?php echo $veteranosPercent[6]; ?>,
                            <?php echo $veteranosPercent[7]; ?>,
                            <?php echo $veteranosPercent[8]; ?>,
                            <?php echo $veteranosPercent[9]; ?>,
                            <?php echo $veteranosPercent[10]; ?>,
                            <?php echo $veteranosPercent[11]; ?>
                        ],
                        backgroundColor: [
                            '#004F4F', '#007272', '#009999',
                            '#00B3B3', '#00CCCC', '#00E6E6',
                            '#2F6F6F', '#ffcbdb', '#66CCCC', '#99E6E6',
                            '#33A1A1', '#B3FFFF'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            display: true,
                            labels: {
                                usePointStyle: true,
                                pointStyle: 'circle',
                                boxWidth: 8,
                                padding: 8,
                                font: {
                                    size: 12
                                },
                                generateLabels: function (chart) {
                                    const data = chart.data;
                                    if (data.labels.length && data.datasets.length) {
                                        return data.labels.map((label, i) => {
                                            const meta = chart.getDatasetMeta(0);
                                            const style = meta.controller.getStyle(i);

                                            let shortLabel = label;
                                            if (label.length > 12) {
                                                shortLabel = label.substring(0, 10) + '...';
                                            }

                                            return {
                                                text: shortLabel,
                                                fillStyle: style.backgroundColor,
                                                strokeStyle: style.backgroundColor,
                                                lineWidth: 0,
                                                hidden: isNaN(data.datasets[0].data[i]) || meta.data[i].hidden,
                                                index: i,
                                                pointStyle: 'circle'
                                            };
                                        });
                                    }
                                    return [];
                                }
                            },
                            onClick: function (e, legendItem, legend) {
                                const index = legendItem.index;
                                const ci = legend.chart;

                                if (ci.isDatasetVisible(0)) {
                                    ci.getDatasetMeta(0).data[index].hidden = !ci.getDatasetMeta(0).data[index].hidden;
                                    ci.update();
                                }
                            },
                            maxWidth: 240,
                            maxHeight: 100
                        },
                        tooltip: {
                            callbacks: {
                                label: function (tooltipItem) {
                                    let label = tooltipItem.chart.data.labels[tooltipItem.dataIndex];
                                    let value = tooltipItem.raw;
                                    return `${label}: ${value.toFixed(2)}%`;
                                }
                            }
                        }
                    },
                    layout: {
                        padding: {
                            bottom: 30
                        }
                    }
                }
            });
        </script>

</body>

</html>