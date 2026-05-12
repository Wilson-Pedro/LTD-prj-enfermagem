<?php
include('../../protect.php');

include('../../db/conexao.php');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$total = 0;

$id_paciente_status = isset($_GET['id_paciente_status']) ? $_GET['id_paciente_status'] : '';
$busca = isset($_GET['busca']) ? $_GET['busca'] : '';

$where = "WHERE 1=1";

if(!empty($id_paciente_status)) {
    $where .= " AND pa.id_paciente_status = " . (int)$id_paciente_status;
}

$por_pagina = 10;
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;

if ($pagina < 1) {
    $pagina = 1;
}

$offset = ($pagina - 1) * $por_pagina;

// if (!empty($busca)) {
//     $where = "WHERE pa.nome LIKE '%" . mysqli_real_escape_string($mysqli, $busca) . "%' AND pa.id_paciente_status = " . $id_paciente_status;
// }

?>
<!DOCTYPE html>

<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listagem de Prontuários | Estácio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
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
            box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.2);
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
            /* grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); */
            gap: 25px;
        }

        .card {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0px 6px 14px rgba(0, 0, 0, 0.2);
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

        .barra-busca {
            width: 30%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1em;
        }


        .barra-busca:focus {
            outline: none;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        button {
            background: none;
            border: none;
            cursor: pointer;
        }

        .nome-user {
            margin-top: 1.5%;
        }

        .nome-user strong {
            color: gray;
        }

        form {
            display: flex;
            align-items: start;
            flex-direction: column;
        }

        form div {
            width: 100%;
        }

        form label {
            font-size: 120%;
            font-weight: bold;
            color: #333;
        }

        .icons {
            text-align: center;
            font-size: 130%;
            cursor: pointer;
            color: red;
        }
    </style>

</head>

<body>
    <header>
        <a href="../../goToHome.php" class="voltar" title="Voltar">
            <i class="bi bi-arrow-left-circle-fill"></i>
        </a>
        <a href="../../goToHome.php" title="logo estácio">
            <img src="../../img/estacio-logo.png" alt="Logo Estácio">
        </a>
    </header>

    <main>
        <h1>Lista de Prontuários</h1>
        <form method="GET">
            <input type="hidden" name="pagina" value="1">
            <div>
                <label for="">Buscar Por Nome: </label>
                <input type="text" name="busca" placeholder="Pesquisar..." class="barra-busca" value="<?php echo htmlspecialchars($busca); ?>">
                <button type="submit">
                    <i class="bi bi-search"></i>
                </button>
            </div>

            <div>

                <label for="">Paciente Status</label>
                <select
                    id="select_paciente_status"
                    name="id_paciente_status"
                    class="form-select form-select-lg mb-3"
                    aria-label=".form-select-lg example">

                    <option value="" <?php echo empty($id_paciente_status) ? 'selected' : ''; ?>>
                        Todos
                    </option>

                    <?php
                    $sql_paciente_status = "SELECT ps.id, ps.status
                                            FROM tbl_paciente_status ps";

                    $stmt_paciente_status = $mysqli->prepare($sql_paciente_status);
                    $stmt_paciente_status->execute();
                    $stmt_paciente_status_result = $stmt_paciente_status->get_result();

                    while ($paciente_status = $stmt_paciente_status_result->fetch_assoc()) {
                    ?>
                        <option 
                            value='<?php echo htmlspecialchars($paciente_status['id']) ?>'
                            <?php echo ($id_paciente_status == $paciente_status['id']) ? 'selected' : '' ?>
                        >
                            <?php echo htmlspecialchars($paciente_status['status']) ?>
                        </option>
                    <?php
                    }
                    ?>
                </select>
            </div>
        </form> <br>



        <table class="table table-sm table-striped table-hover table-responsive">
            <thead class="table-dark">
                <tr>
                    <th>Nome</th>
                    <th>CPF</th>
                    <th>Nascimento</th>
                    <th>Nome da Mãe</th>
                    <th>Status</th>
                    <th>Atendimento</th>
                    <th>Realizado por</th>
                    <th>Evolução</th>
                    <th>Editar</th>
                    <th>Ver Evoluções</th>
                </tr>
            </thead>
            <tbody style="font-size: 90%;">
                <?php
                try {

                    $sql = "SELECT pr.id, pr.numero_prontuario, pa.nome, pa.cpf, pa.data_nascimento, pa.nome_mae, pr.data_atendimento, 
                                us.nome AS nome_user, pr.id_paciente, ps.status  
                                FROM tbl_prontuario pr 
                                JOIN tbl_paciente pa ON pa.id = pr.id_paciente 
                                JOIN tbl_users us ON us.id = pr.id_user
                                JOIN tbl_paciente_status ps ON ps.id = pa.id_paciente_status
                                $where
                                ORDER BY pr.registro DESC
                                LIMIT $por_pagina  OFFSET $offset";

                    $sql_total = "SELECT COUNT(*) as total FROM tbl_paciente pa $where";
                    $result_total = mysqli_query($mysqli, $sql_total);
                    $total = mysqli_fetch_assoc($result_total)['total'];
                    $result = mysqli_query($mysqli, $sql);
                    //$result = $mysqli->query($sql);

                    if ($result->num_rows === 0) {
                        echo "<p style='text-align:center; color:gray;'>Nenhum prontuário cadastrado.</p>";
                    } else {

                        while ($row = $result->fetch_assoc()) {
                            $id = htmlspecialchars($row['id']);
                            $numero = htmlspecialchars($row['numero_prontuario']);
                            $nome = htmlspecialchars($row['nome']);
                            $cpf = htmlspecialchars($row['cpf']);
                            $data_nascimento = htmlspecialchars(date('d/m/Y', strtotime($row['data_nascimento'])));
                            $nome_mae = htmlspecialchars($row['nome_mae']);
                            $status = htmlspecialchars($row['status']);
                            $data_atendimento = htmlspecialchars(date('d/m/Y', strtotime($row['data_atendimento'])));
                            $nome_user = htmlspecialchars($row['nome_user']);
                            $id_paciente = htmlspecialchars($row['id_paciente']);
                ?>

                            <tr>
                                <td><?php echo $nome ?></td>
                                <td><?php echo $cpf ?></td>
                                <td><?php echo $data_nascimento ?></td>
                                <td><?php echo $nome_mae ?></td>
                                <td><?php echo $status ?></td>
                                <td><?php echo $data_atendimento ?></td>
                                <td><?php echo $nome_user ?></td>
                                <td class="icons">
                                    <a href="../cadastro/cadastrar-evolucao.php?id=<?php echo $id_paciente ?>" class="card-link">
                                        <i class="bi bi-clipboard2-plus-fill"></i>
                                    </a>
                                </td>
                                <td class="icons">
                                    <a href="../edit/formularioEdit.php?id=<?php echo $id ?>" class="card-link">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                </td>
                                <td class="icons">
                                    <a href="evolucao.php?id=<?php echo $id_paciente ?>" class="card-link">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                <?php
                        }
                    }
                } catch (Exception $e) {
                    echo "<p style='color:grey; text-align:center;'>Nehnhum formulário encontrada.</p>";
                    error_log("Error ao listar prontuários " . $e->getMessage());
                }
                ?>
            </tbody>
        </table>

        <?php
        $total_paginas = ceil($total / $por_pagina);
        ?>
        </div>
        <nav>
            <ul class="pagination justify-content-center">

                <?php
                $range = 2;

                if ($pagina > 1) {
                ?>
                    <li class="page-item">
                        <a class="page-link"href="?pagina=<?php echo $pagina - 1; ?>&busca=<?php echo urlencode($busca); ?>&id_paciente_status=<?php echo urlencode($id_paciente_status); ?>">
                            «
                        </a>
                    </li>
                <?php
                }

                // PRIMEIRA PAGINA
                if ($pagina > ($range + 1)) {
                ?>
                    <li class="page-item">
                        <a class="page-link" href="?pagina=1&busca=<?php echo urlencode($busca); ?>&id_paciente_status=<?php echo urlencode($id_paciente_status); ?>">
                            1
                        </a>
                    </li>

                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                <?php
                }

                // PÁGINAS AO REDOR DA ATUAL
                for ($i = max(1, $pagina - $range); $i <= min($total_paginas, $pagina + $range); $i++) {
                ?>
                    <li class="page-item <?php echo ($i == $pagina) ? 'active' : ''; ?>">
                        <a class="page-link" href="?pagina=<?php echo $i; ?>&busca=<?php echo urlencode($busca); ?>&id_paciente_status=<?php echo urlencode($id_paciente_status); ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php
                }

                // ULTIMA PAGINA
                if ($pagina < ($total_paginas - $range)) {
                ?>
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>

                    <li class="page-item">
                        <a class="page-link" href="?pagina=<?php echo $total_paginas; ?>&busca=<?php echo urlencode($busca); ?>&id_paciente_status=<?php echo urlencode($id_paciente_status); ?>">
                            <?php echo $total_paginas; ?>
                        </a>
                    </li>
                <?php
                }

                // PROXIMO
                if ($pagina < $total_paginas) {
                ?>
                    <li class="page-item">
                        <a class="page-link" href="?pagina=<?php echo $pagina + 1; ?>&busca=<?php echo urlencode($busca); ?>&id_paciente_status=<?php echo urlencode($id_paciente_status); ?>">
                            »
                        </a>
                    </li>
                <?php
                }
                ?>

            </ul>
        </nav>

    </main>

    <!-- Rodapé -->
    <footer>
        © 2025 Estácio - Sistema de Prontuários
    </footer>

    <script>
        document.getElementById("select_paciente_status")
        .addEventListener("change", function() {
            this.form.submit();
        });
    </script>
</body>

</html>