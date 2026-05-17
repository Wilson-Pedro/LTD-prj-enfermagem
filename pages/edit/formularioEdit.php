<?php
    include('../../protect.php');
    include('../../db/conexao.php');
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $mysqli->set_charset("utf8");


    $id = $_GET['id'] ?? '';
    $id = trim($id);

    if(isset($_POST['atualizar'])) {

        try {
        
            //PEGAR ID DO ENDERECO E ID DO PACIENTE
            $stmt_id_endereco = $mysqli->prepare("
                SELECT pr.id_paciente AS paciente_id, pa.id_endereco AS endereco_id 
                FROM tbl_prontuario pr 
                JOIN tbl_paciente pa ON pr.id_paciente = pa.id 
                WHERE pr.id = ?;
            ");
            $stmt_id_endereco->bind_param("i", $id);
            $stmt_id_endereco->execute();
            $result = $stmt_id_endereco->get_result();
            $id_endereco = null;
            $id_paciente = null;

            if($row = $result->fetch_assoc()) {
                $id_endereco = $row['endereco_id'];
                $id_paciente = $row['paciente_id'];
            }

            //DADOS PARA TBL_PACIENTE
            $rg_update = $_POST['rg'];
            $cpf_update = $_POST['cpf'];
            $ssp_update = $_POST['ssp'];
            $nome_update = $_POST['nome'];
            $telefone_update = $_POST['telefone'];
            $nome_mae_update = $_POST['nome_mae'];
            $cartao_sus_update = $_POST['cartao_sus'];
            $nome_mae_consta_update = empty($_POST['nome_mae_consta'])  ? 1 : 0;

            $data_nascimento_update = $_POST['data_nascimento'];

            $id_paciente_status  = $_POST['paciente_status'];

            //DADOS PARA TBL_ENDERECO
            $cep_update = $_POST['cep'];
            $rua_update = $_POST['rua'];
            $bairro_update = $_POST['bairro'];
            $cidade_update = $_POST['cidade'];
            $complemento_update = $_POST['complemento'];


            $stmt_endereco_update = $mysqli->prepare("UPDATE tbl_endereco SET cep = ?, rua = ?, bairro = ?, cidade = ?, complemento = ? 
            WHERE id = ?");
            $stmt_endereco_update->bind_param("sssssi", $cep_update, $rua_update, $bairro_update, $cidade_update, $complemento_update, $id_endereco);
            $stmt_endereco_update->execute();
            $stmt_endereco_update->close();

            $sql_update_paciente = "UPDATE tbl_paciente SET nome = ?,data_nascimento = ?,
                        nome_mae = ?,mae_nao_consta = ?, cpf = ?,
                        rg = ?,ssp = ?,
                        telefone = ?, cartao_sus = ?, id_paciente_status = ?
                        WHERE id = ?";

            $stmt_paciente_update = $mysqli->prepare($sql_update_paciente);
            $stmt_paciente_update->bind_param("sssisssssii", $nome_update, $data_nascimento_update, $nome_mae_update, $nome_mae_consta_update,
                                                            $cpf_update, $rg_update, $ssp_update, $telefone_update, $cartao_sus_update, $id_paciente_status, $id_paciente);

            $stmt_paciente_update->execute();
            $stmt_paciente_update->close();

        } catch(Exception $e) {
            echo "Error ao atualizar prontuário" . $e->getMessage();
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../../img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../css/formulario.css">
    <title>Editar Formulário</title>
    <style>
        body {
            background: #146c8f;
            background: linear-gradient(180deg,rgba(20, 108, 143, 1) 0%, rgba(59, 157, 196, 1) 100%);  
        }

        .form-group select {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 100%;
        }
    </style>
</head>
<body>

    <!-- Botão de Voltar -->
    <div class="back-btn">
        <a href="../evolucao/prontuarios.php"><i class="bi bi-arrow-left-circle-fill" id="iconeVoltar"></i></a>
    </div>

    <form method="post" class="container">
        <h2>Editar Formulário</h2>

        <?php 
            try {
                $stmt_sql = $mysqli->prepare("SELECT pr.id AS prontuarioId, pr.numero_prontuario, pr.data_atendimento, pa.nome, pa.cpf, pa.data_nascimento, 
                pa.rg, pa.ssp, pa.nome_mae, pa.mae_nao_consta, pa.id_paciente_status, ps.status,
                pa.telefone, pa.cartao_sus, 
                en.id AS enderecoId, en.cep, en.rua, en.bairro, en.cidade, en.complemento, us.nome AS nome_user
                FROM tbl_prontuario pr 
                JOIN tbl_paciente pa ON pa.id = pr.id_paciente 
                JOIN tbl_endereco en ON en.id = pa.id_endereco 
                JOIN tbl_users us ON us.id = pr.id_user
                JOIN tbl_paciente_status ps ON ps.id = pa.id_paciente_status 
                WHERE pr.id = ?
                LIMIT 1;");
                
                $stmt_sql->bind_param("i", $id);
                $stmt_sql->execute();
                $result = $stmt_sql->get_result();

                if($result->num_rows == 0) {
                    echo "<p style='text-align:center; color:gray;'>Prontuário não encontrado.</p>";
                } else {
                    while($row = $result->fetch_assoc()) {
                            $prontuarioId = htmlspecialchars($row['prontuarioId']);
                            $enderecoId = htmlspecialchars($row['enderecoId']);
                            $numero_prontuario = htmlspecialchars($row['numero_prontuario']);
                            $data_atendimento = htmlspecialchars($row['data_atendimento']);
                            $nome = htmlspecialchars($row['nome']);
                            $cpf = htmlspecialchars($row['cpf']);
                            $data_nascimento = htmlspecialchars($row['data_nascimento']);
                            $rg = htmlspecialchars($row['rg']);
                            $ssp = htmlspecialchars($row['ssp']);
                            $nome_mae = htmlspecialchars($row['nome_mae']);
                            $mae_nao_consta = htmlspecialchars($row['mae_nao_consta']);
                            $telefone = htmlspecialchars($row['telefone']);
                            $cartao_sus = htmlspecialchars($row['cartao_sus']);
                            $cep = htmlspecialchars($row['cep']);
                            $rua = htmlspecialchars($row['rua']);
                            $bairro = htmlspecialchars($row['bairro']);
                            $cidade = htmlspecialchars($row['cidade']);
                            $complemento = htmlspecialchars($row['complemento']);
                            $nome_user = htmlspecialchars($row['nome_user']);
                            $status = htmlspecialchars($row['status']);
        ?>

        <div class="form-group">
            <label>Tipo paciente</label>
            <select id="paciente_status " name="paciente_status"  class="form-group">
                <option value="<?php echo $row['id_paciente_status']; ?>" selected> <?php echo $row['status'] ?> </option>
                <?php
                    $id_paciente_status = $row['id_paciente_status'];
                    $sql_paciente_status = "SELECT * FROM tbl_paciente_status WHERE id !=  $id_paciente_status";
                    $result_paciente_status = mysqli_query($mysqli, $sql_paciente_status);
                    while ($rowPs = mysqli_fetch_assoc($result_paciente_status)) {
                ?>
                        <option value='<?php echo $rowPs['id'] ?>'> <?php echo $rowPs['status'] ?> </option>
                <?php
                    }
                ?>
            </select>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="prontuario">Número do Prontuário</label>
                <input type="number" id="numero_prontuario" value="<?php echo $numero_prontuario ?>" name="numero_prontuario" placeholder="Nº Prontuário" disabled>
            </div>
            <div class="form-group">
                <label for="atendimento">Data do Atendimento</label>
                <input type="date" id="data_atendimento" value="<?php echo date("Y-m-d", strtotime($data_atendimento)) ?>" name="data_atendimento" placeholder="Dia / Mês / Ano" disabled>
            </div>
        </div>

        <div class="form-group">
            <label for="nome">Nome do Paciente</label>
            <input type="text" id="nome" value="<?php echo $nome ?>" name="nome" placeholder="Nome" required>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="cpf">CPF</label>
                <input type="text" id="cpf" value="<?php echo $cpf ?>" name="cpf" placeholder="CPF" required>
            </div>
            <div class="form-group">
                <label for="nascimento">Data de Nascimento</label>
                <input type="date" id="nascimento" value="<?php echo date('Y-m-d', strtotime($data_nascimento)) ?>" name="data_nascimento" placeholder="Dia / Mês / Ano" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="rg">RG</label>
                <input type="text" id="rg" value="<?php echo $rg ?>" name="rg" placeholder="RG" required>
            </div>
            <div class="form-group">
                <label for="ssp">SSP</label>
                <input type="text" id="ssp" value="<?php echo $ssp ?>" name="ssp" placeholder="SSP" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="nome-mae">Nome da Mãe</label>
                <input type="text" id="nome-mae" value="<?php echo $nome_mae ?>" name="nome_mae" placeholder="Nome da Mãe">
            </div>
            <div class="form-group">
                <label for="telefone">Telefone</label>
                <input type="text" id="telefone" value="<?php echo $telefone ?>" name="telefone" placeholder="Telefone" required>
            </div>
        </div>

        <div class="form-row">
            <div class="checkbox-group">
                <input type="checkbox" id="nao-consta-mae" name="nome_mae_consta"  <?php echo ($mae_nao_consta == 1) ? 'checked': ""; ?>>
                <label for="nao-consta-mae">Não Consta</label>
            </div>
        </div>
        <br>
        <div class="form-group">
            <label for="sus">Cartão do SUS</label>
            <input type="text" id="sus" value="<?php echo $cartao_sus ?>" name="cartao_sus" placeholder="SUS">
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="cep">CEP</label>
                <input type="text" id="cep" value="<?php echo $cep ?>" name="cep" placeholder="cep" required>
            </div>
            <div class="form-group">
                <label for="rua">Rua</label>
                <input type="text" id="rua" value="<?php echo $rua ?>" name="rua" placeholder="Rua" required>
            </div>
        </div>

        <div class="form-group">
            <div class="form-group">
                <label for="rua">Bairro</label>
                <input type="text" id="bairro" value="<?php echo $bairro ?>" name="bairro" placeholder="bairro" required>
            </div>
            
            <div class="form-group">
                <label for="cidade">Cidade</label>
                <input type="text" id="cidade" value="<?php echo $cidade ?>" name="cidade" placeholder="Cidade" required>
            </div>
        </div>

        <div class="form-group">
            <div class="form-group">
                <label for="cidade">Complemento</label>
                <textarea rows="5" cols="30" id="complemento" name="complemento" placeholder="complemento"><?php echo htmlspecialchars($complemento) ?></textarea>
            </div>
        </div>

        <div class="form-group">
            
            <div class="form-group">
                <label for="nome_user">Realizado por:</label>
                <input type="text" id="nome-user" value="<?php echo $nome_user ?>" name="nome_user" placeholder="<?php echo $nome_user ?>" disabled>
            </div>
        </div>

        <?php
                    }
                }
            } catch(Exception $e) {
                echo "<p style='color:red; text-align:center;'>Erro ao buscar formulário.</p>" . $e;
                error_log("Error ao bsucar formulário. " . $e->getMessage());
            }
        ?>

        <div class="submit-btn">
            <button type="submit" name="atualizar">Atualizar</button>
        </div>
    </form>

</body>
</html>