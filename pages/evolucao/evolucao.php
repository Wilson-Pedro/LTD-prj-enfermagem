<?php
include('../../protect.php');

include('../../db/conexao.php');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$id_paciente = $_GET['id'] ?? '';
$id_paciente = trim($id_paciente);

?>
<!DOCTYPE html>

<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evoluções</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="shortcut icon" href="../../img/favicon.ico" type="image/x-icon">
<style>
    body {
        margin: 0;
        font-family: "Segoe UI", Arial, sans-serif;
        background: #f5f7fb;
        color: #333;
    }

    header {
        background: #004B93;
        color: #fff;
        padding: 15px 30px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0px 2px 8px rgba(0,0,0,0.2);
    }

    header img {
        height: 45px;
    }

    .back-btn {
        color: #fff;
        font-size: 1.8rem;
        text-decoration: none;
    }

    main {
        max-width: 1100px;
        margin: 40px auto;
        padding: 0 20px;
    }

    h1 {
        text-align: center;
        color: #004B93;
        font-size: 28px;
        margin-bottom: 30px;
    }

    .card-list {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 25px;
    }

    .card {
        background: #fff;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0px 4px 10px rgba(0,0,0,0.1);
        transition: all 0.3s ease;

    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0px 6px 14px rgba(0,0,0,0.2);
    }

    .card h2 {
        color: #004B93;
        font-size: 20px;
        margin-top: 0;
    }

    .info {
        margin-top: 10px;
        line-height: 1.6;
    }

    .info span {
        display: block;
        font-size: 15px;
        color: #444;
    }

    .info strong {
        color: #0099DA;
    }

    .info button {
        margin-top: 2%;
    }

    footer {
        text-align: center;
        padding: 20px;
        color: #777;
        font-size: 14px;
    }

    .voltar {
        color: #fff;
        font-size: 1.8rem;
        text-decoration: none;
    }

    .card-link {
        text-decoration: none;
    }

    .divBtn {
        margin: 3%;
        width: 100%;
        display: flex;
        justify-content: end;
    }
</style>

</head>
<body>
    <header>
        <a href="prontuarios.php?id=<?php echo $id_paciente ?>" class="voltar" title="Voltar">
            <i class="bi bi-arrow-left-circle-fill"></i>
        </a>
        <a href="../../goToHome.php" title="logo estácio">
             <img src="../../img/estacio-logo.png" alt="Logo Estácio">
        </a>
    </header>

    <main>
        <h1>Evoluções</h1>

            <div class="divBtn">
                <a href="../cadastro/cadastrar-evolucao.php?id=<?php echo $id_paciente ?>">
                    <button type="button" class="btn btn-success">Fazer Evolução</button>
                </a>
            </div>

            <?php
                $stmt_nome_paciente = $mysqli->prepare("SELECT pa.nome FROM tbl_paciente pa WHERE pa.id = ?;");
                $stmt_nome_paciente->bind_param("i", $id_paciente);
                $stmt_nome_paciente->execute();
                $result = $stmt_nome_paciente->get_result();

                $nome_paciente = null;

                if($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $nome_paciente = htmlspecialchars($row['nome']);
                } else {
                    $nome_paciente = htmlspecialchars("");
                }

            ?>

            <div class="container-flex">
                <h4>Nome do paciente: <?php echo $nome_paciente ?></h4>
            </div> <br>

            <table class="table">
            <?php
                try {

                    $sql = "SELECT ev.id as id_evolucao, ev.data_atendimento, ev.pressao, ev.glicemia, ev.pulso, 
                                ev.frequencia_cardiaca, ev.frequencia_respiratoria, ev.temperatura_axilar, ev.observacao, us.nome AS nome_user 
                            FROM tbl_evolucao ev 
                            JOIN tbl_paciente pa ON pa.id = ev.id_paciente 
                            JOIN tbl_users us ON us.id = ev.id_user 
                            WHERE pa.id = $id_paciente
                            ORDER BY ev.id DESC;";

                    $result = $mysqli->query($sql);


                    if($result->num_rows === 0) {
                        echo "<p style='text-align:center; color:gray;'>Nenhuma evolução cadastrada.</p>";
                    } else {

                ?>

                        <thead class="table-dark">
                            <tr>
                            <th scope="col" style="text-align:center">Data da evolução</th>
                            <th scope="col" style="text-align:center">Responsável</th>
                            <th scope="col" style="text-align:center">Pressão Arterial</th>
                            <th scope="col" style="text-align:center">Glicemia</th>
                            <th scope="col" style="text-align:center">Pulso</th>
                            <th scope="col" style="text-align:center">F.Cardíaca</th>
                            <th scope="col" style="text-align:center">F.Respiratória </th>
                            <th scope="col" style="text-align:center">Tempertura Axilar</th>
                            <th scope="col" style="text-align:center">Observação</th>
                            </tr>
                        </thead>

            <?php

                    while($row = $result->fetch_assoc()) {
                        $data_atendimento = htmlspecialchars(date('d/m/Y', strtotime($row['data_atendimento'])));
                        $pressao = htmlspecialchars($row['pressao']);
                        $glicemia = htmlspecialchars($row['glicemia']);
                        $pulso = htmlspecialchars($row['pulso']);
                        $frequencia_cardiaca = htmlspecialchars($row['frequencia_cardiaca']);
                        $frequencia_respiratoria = htmlspecialchars($row['frequencia_respiratoria']);
                        $temperatura_axilar = htmlspecialchars($row['temperatura_axilar']);
                        $observacao = htmlspecialchars($row['observacao']);
                        $nome_user = htmlspecialchars($row['nome_user']);
            ?>
                        <tbody>
                            <tr>
                            <td  style="width:10%;text-align:center"><?php echo $data_atendimento ?></td>
                            <td  style="width:12%;text-align:center"><?php echo $nome_user ?></td>
                            <td  style="width:20%;text-align:center"><?php echo $pressao ?>/80 mmHg</td>
                            <td  style="width:10%;text-align:center"><?php echo $glicemia ?></td>
                            <td  style="width:10%;text-align:center"><?php echo $pulso ?>xbpm</td>
                            <td  style="width:10%;text-align:center"><?php echo $frequencia_cardiaca ?>bpm</td>
                            <td  style="width:10%;text-align:center"><?php echo $frequencia_respiratoria ?>ipm</td>
                            <td  style="width:10%;text-align:center"><?php echo $temperatura_axilar ?>°C</td>
                            <td><?php echo $observacao ?></td>
                            </tr>
                        </tbody>

            <?php 
                        }
                    }
                } catch(Exception $e) {
                    echo "<p style='color:grey; text-align:center;'>Nehnhuma evolução encontrada.</p>";
                    error_log("Error ao listar prontuários " . $e->getMessage());
                }
            ?>
            </table>

    </main>

    <!-- Rodapé -->
    <footer>
        © 2025 Estácio - Sistema de Prontuários
    </footer>


</body>
</html>
