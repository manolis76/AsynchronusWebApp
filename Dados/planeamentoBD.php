<?php

session_start();
require_once '../Dados/configBD.php';

if (isset($_SESSION['utilizador']) && $_REQUEST) {
    $funcao = $_GET["funcao"];
    $dataHoraInicioUnix = date("Y-m-d H:i:s", $_GET['start']);
    $dataHoraInicio = $_GET['dataInicio'];
    $dataHoraFimUnix = date("Y-m-d H:i:s", $_GET['end']);
    $dataHoraFim = $_GET['dataFim'];
    $idPlaneamento = $_GET['idPlaneamento'];
    $idTarefa = $_GET['idTarefa'];

    if ($funcao != "") {
        switch ($funcao) {
            case 0:
                obtemItemsPlaneamento(NULL, $dataHoraInicioUnix, $dataHoraFimUnix);
                break;
            case 1:
                obtemItemsPlaneamento($idPlaneamento, NULL, NULL);
                break;
            case 2:
                menusGestaoEvento();
                break;
            case 3:
                eliminaItemPlaneamento($idPlaneamento);
                break;
            case 4:
                editaItemPlaneamento($dataHoraInicio, $dataHoraFim, $idPlaneamento);
                break;
            case 5:
                insereItemPlaneamento($idTarefa, $dataHoraInicio, $dataHoraFim);
                break;
        }
    }
}

function insereItemPlaneamento($idTarefa, $dataHoraInicio, $dataHoraFim) {

    $utilizador = $_SESSION['utilizador'];
    $db = iniciaBD();
// Save the entry into the database
    $sql = "INSERT INTO PlaneamentoAtividade (Utilizador, idTarefa, DataHoraInicio, DataHoraFim) VALUES (?, ?, ?, ?)";
    try {
        $stmt = $db->prepare($sql);
        $stmt->execute(array($utilizador, $idTarefa, $dataHoraInicio, $dataHoraFim));
        $stmt->closeCursor();
    } catch (PDOException $e) {
        echo ("Erro BD: " . $e->getMessage() . "\n");
        echo ("Código erro BD: " . $e->getCode() . "\n");
        exit;
    }
    echo ("Evento criado com sucesso!");
}

function editaItemPlaneamento($dataHoraInicio, $dataHoraFim, $idPlaneamento) {
    $db = iniciaBD();
    $sql = "UPDATE PlaneamentoAtividade SET DataHoraInicio=?,DataHoraFim=? WHERE idPlaneamentoAtividade=?";
    try {
        $stmt = $db->prepare($sql);
        $stmt->execute(array($dataHoraInicio, $dataHoraFim, $idPlaneamento));
        $stmt->closeCursor();
    } catch (PDOException $e) {
        echo ("Erro BD: " . $e->getMessage() . "\n");
        echo ("Código erro BD: " . $e->getCode() . "\n");
        exit;
    }
    echo ("Evento actualizado com sucesso!");
}

function eliminaItemPlaneamento($idPlaneamento) {
    $db = iniciaBD();
    $sql = "DELETE FROM PlaneamentoAtividade WHERE idPlaneamentoAtividade=?";
    try {
        $stmt = $db->prepare($sql);
        $stmt->execute(array($idPlaneamento));
        $stmt->closeCursor();
    } catch (PDOException $e) {
        echo ("Erro BD: " . $e->getMessage() . "\n");
        echo ("Código erro BD: " . $e->getCode() . "\n");
        exit;
    }
    echo ("Evento eliminado com sucesso!");
}

function obtemItemsPlaneamento($idPlaneamento, $dataHoraInicio, $dataHoraFim) {
    $db = iniciaBD();
    $utilizador = $_SESSION['utilizador'];
    $eventos = array();
    if ($idPlaneamento == NULL) {
        $sql = "SELECT P.idPlaneamentoAtividade, P.DataHoraInicio, DATE_FORMAT(P.DataHoraInicio, '%Y-%m-%dT%H:%i') AS startDate,
        P.DataHoraFim, DATE_FORMAT(P.DataHoraFim, '%Y-%m-%dT%H:%i') AS endDate, T.NomeTarefa, PT.PesoPrioridade 
        FROM PlaneamentoAtividade AS P, Tarefa AS T, PrioridadeTarefa AS PT 
        WHERE P.DataHoraInicio>=? AND P.DataHoraInicio <=? AND Utilizador=? AND P.idTarefa = T.idTarefa AND T.idPrioridadeTarefa = PT.idPrioridadeTarefa ORDER BY idPlaneamentoAtividade";
        try {
// Loop through returned results and store as an array
            $stmt = $db->prepare($sql);
            $stmt->execute(array($dataHoraInicio, $dataHoraFim, $utilizador));
            $resultado = $stmt->fetchAll();
            foreach ($resultado as $row) {
                $linhaEvento['id'] = $row['idPlaneamentoAtividade'];
                $linhaEvento['title'] = $row['NomeTarefa'];
                $linhaEvento['start'] = $row['startDate'];
                $linhaEvento['end'] = $row['endDate'];
                $linhaEvento['allDay'] = false;
                switch ($row['PesoPrioridade']) {
                    case 1:
                        $linhaEvento['color'] = "forestgreen";
                        break;
                    case 2:
                        $linhaEvento['color'] = "goldenrod";
                        break;
                    case 3:
                        $linhaEvento['color'] = "orangered";
                        break;
                    case 4:
                        $linhaEvento['color'] = "dodgerblue";
                        break;
                }
                $eventos[] = $linhaEvento;
            }
        } catch (PDOException $e) {
            echo ("Erro BD: " . $e->getMessage() . "\n");
            echo ("Código erro BD: " . $e->getCode() . "\n");
            exit;
        }
        echo json_encode($eventos);
    } else {
        $sql = "SELECT P.idPlaneamentoAtividade, P.DataHoraInicio, P.DataHoraFim, T.NomeTarefa 
        FROM PlaneamentoAtividade AS P, Tarefa AS T 
        WHERE P.idPlaneamentoAtividade=? AND P.idTarefa = T.idTarefa";
        try {
// Loop through returned results and store as an array
            $stmt = $db->prepare($sql);
            $stmt->execute(array($idPlaneamento));
            $resultado = $stmt->fetchAll();
            foreach ($resultado as $row) {
                $linhaEvento['id'] = $row['idPlaneamentoAtividade'];
                $linhaEvento['title'] = $row['NomeTarefa'];
                $linhaEvento['start'] = $row['DataHoraInicio'];
                $linhaEvento['end'] = $row['DataHoraFim'];
                $linhaEvento['allDay'] = false;
                $eventos[] = $linhaEvento;
            }
        } catch (PDOException $e) {
            echo ("Erro BD: " . $e->getMessage() . "\n");
            echo ("Código erro BD: " . $e->getCode() . "\n");
            exit;
        }
        output(NULL, $eventos, 1);
    }
}

function menusGestaoEvento() {
    $listaTarefas = obtemTarefasParaEventos(0);
    output(NULL, $listaTarefas, 0);
}

function obtemTarefasParaEventos($output) {

    $db = iniciaBD();
    $sql = "SELECT idTarefa, NomeTarefa FROM Tarefa ORDER BY NomeTarefa ASC";
// Loop through returned results and store as an array
    try {
        foreach ($db->query($sql) as $row) {
            $enviar[] = array(
                'idTarefa' => $row['idTarefa'],
                'nomeTarefa' => $row['NomeTarefa']
            );
        }
    } catch (PDOException $e) {
        echo ("Erro BD: " . $e->getMessage() . "\n");
        echo ("Código erro BD: " . $e->getCode() . "\n");
        exit;
    }
    if ($output == 1) {
        output($id, $enviar, 0);
    } else {
        return $enviar;
    }
}

function output($id, $dados, $tipoOutput) {
    switch ($tipoOutput) {
        case 0:
            echo ("<div id=\"menuCriaEvento\" hidden=\"hidden\"><u>Criar Evento</u>");
            echo("<p><select id=\"listaTarefasEvento\"><option selected=\"selected\" value=\"escolha\">Escolha uma tarefa</option>");
            foreach ($dados as $entry) {
                echo ("<option value=\"" . $entry['idTarefa'] . "\">" . $entry['nomeTarefa'] . "</option>");
            }
            echo("</select></p>Data Inicio: <input id=\"inicioEvento\" type=\"text\" disabled=\"disabled\">");
            echo ("Data Fim: <input id=\"fimEvento\" type=\"text\" disabled=\"disabled\"><input type=\"button\" class=\"botaoMenuCriaEvento\" value=\"Criar\">");
            echo ("<input type=\"reset\" class=\"botaoMenuCancelaEvento\" value=\"Cancelar\"></div>");
            echo ("<div id=\"menuApagaEvento\" hidden=\"hidden\"></div>");
            echo ("<div id=\"resultadoAlteracoesEvento\" hidden=\"hidden\"></div>");
            break;
        case 1:
            echo("<u>Eliminar Evento</u><p></p>Tarefa: <input id=\"tarefaEventoEdicao\" type=\"text\" disabled=\"disabled\" value=\"" . $dados[0]['title'] . "\">");
            echo("Data Inicio: <input id=\"inicioEventoEdicao\" type=\"text\" disabled=\"disabled\" value=\"" . $dados[0]['start'] . "\">");
            echo("Data Fim: <input id=\"fimEventoEdicao\" type=\"text\" disabled=\"disabled\" value=\"" . $dados[0]['end'] . "\">");
            echo ("<input type=\"button\" class=\"botaoMenuEdicaoApagaEvento\" value=\"Apagar\">");
            echo ("<input type=\"reset\" class=\"botaoMenuEdicaoSairEvento\" value=\"Sair\">");
            break;
    }
}

function iniciaBD() {
// Open a database connection
    try {
        $db = new PDO(DB_INFO, DB_USER, DB_PASS);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo ("Erro BD: " . $e->getMessage() . "\n");
        echo ("Código Erro BD: " . $e->getCode() . "\n");
    }
    return $db;
}

?>