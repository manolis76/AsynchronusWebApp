<?php

session_start();
require_once '../Dados/configBD.php';
require_once '../Utilitarios/Graficos/libchart.php';

if (isset($_SESSION['utilizador']) && $_REQUEST) {
    $funcao = $_GET["funcao"];
    $dataHoraInicioUnix = date("Y-m-d H:i:s", $_GET['start']);
    $dataHoraInicio = $_GET['dataInicio'];
    $dataHoraFimUnix = date("Y-m-d H:i:s", $_GET['end']);
    $dataHoraFim = $_GET['dataFim'];
    $tipoGrafico = $_GET['tipoGrafico'];

    if ($funcao != "") {
        switch ($funcao) {
            case 0:
                obtemItemsRegisto($dataHoraInicioUnix, $dataHoraFimUnix);
                break;
            case 1:
                output(NULL, NULL, 0);
                break;
            case 2:
                grafico($dataHoraInicio, $dataHoraFim, $tipoGrafico);
                break;
        }
    }
}

function grafico($dataHoraInicio, $dataHoraFim, $tipoGrafico) {

    $dadosTratar = obtemItemsGrafico($dataHoraInicio, $dataHoraFim);
    $tarefas = obtemTarefasGrafico();

    if ($dadosTratar != NULL) {
        $dadosGrafico = trataDadosGrafico($tarefas, $dadosTratar);
    }

    if ($tipoGrafico == 2) {
        $dadosTratar = obtemItemsGraficoPlaneados($dataHoraInicio, $dataHoraFim);
    }

    if ($dadosTratar != NULL) {
        $dadosGraficoPlaneado = trataDadosGrafico($tarefas, $dadosTratar);
    }


    switch ($tipoGrafico) {
        case 0:
            uasort($dadosGrafico, 'ordenaDadosGraficoPorHoras');
            desenhaGraficoBarras($dadosGrafico, $dataHoraInicio, $dataHoraFim);
            break;
        case 1:
            uasort($dadosGrafico, 'ordenaDadosGraficoPorHoras');
            desenhaGraficoQueijo($dadosGrafico, $dataHoraInicio, $dataHoraFim);
            break;
        case 2:
            desenhaGraficoDesvio($dadosGrafico, $dadosGraficoPlaneado, $tarefas, $dataHoraInicio, $dataHoraFim);
            break;
    }
}

function trataDadosGrafico($tarefas, $dadosTratar) {

    foreach ($tarefas as $compara) {
        $temp = 0;
        foreach ($dadosTratar as $linha) {
            if ($linha['idTarefa'] == $compara['idTarefa']) {
                $temp+=$linha['totalTempo'];
                $corPesoTarefa = $linha['color'];
            }
        }
        $acumulaHoras['idTarefa'] = $compara['idTarefa'];
        $acumulaHoras['nomeTarefa'] = $compara['nomeTarefa'];
        $acumulaHoras['acumulaHoras'] = floatval($temp);
        $acumulaHoras['corPesoTarefa'] = $corPesoTarefa;
        $dadosGrafico[] = $acumulaHoras;
    }
    return $dadosGrafico;
}

function ordenaDadosGraficoPorHoras($a, $b) {
    if ($a['acumulaHoras'] == $b['acumulaHoras']) {
        return 0;
    }
    return ($b['acumulaHoras'] > $a['acumulaHoras']) ? -1 : 1;
}

function desenhaGraficoBarras($dadosGrafico, $dataHoraInicio, $dataHoraFim) {

    if ($dadosGrafico != NULL) {
        $chart = new VerticalBarChart(770, 380);

        $dataSet = new XYDataSet();

        foreach ($dadosGrafico as $preGraf) {
            if ($preGraf['acumulaHoras'] != 0) {
                $dataSet->addPoint(new Point($preGraf['nomeTarefa'], $preGraf['acumulaHoras']));
            }
        }

        $chart->setDataSet($dataSet);
        $chart->setTitle("Tarefas (horas) - " . $dataHoraInicio . " a " . $dataHoraFim);
        $chart->render("../Graficos/graficoGerado.png");
        echo ("<h1>Gráfico</h1><br><img src=\"../Graficos/graficoGerado.png\">");
    } else {
        echo ("<h1>Não existem dados para o intervalo de tempo em questão</h1>");
    }
}

function desenhaGraficoDesvio($dadosGrafico, $dadosGraficoPlaneado, $tarefas, $dataHoraInicio, $dataHoraFim) {
    $num = 0;
    $tarefasZeroHoras1 = 0;
    $tarefasZeroHoras2 = 0;
    if ($dadosGrafico != NULL || $dadosGraficoPlaneado != NULL) {

        $chart = new VerticalBarChart(770, 380);
        $serie1 = new XYDataSet();
        $serie2 = new XYDataSet();
        $dataSet = new XYSeriesDataSet();

        foreach ($tarefas as $compara) {

            if ($compara['idTarefa'] == $dadosGrafico[$num]['idTarefa']) {
                $grafico1['idTarefa'] = $dadosGrafico[$num]['idTarefa'];
                $grafico1['nomeTarefa'] = $dadosGrafico[$num]['nomeTarefa'];
                $grafico1['acumulaHoras'] = $dadosGrafico[$num]['acumulaHoras'];
                $dadosGrafico1[] = $grafico1;
                ($dadosGrafico[$num]['acumulaHoras'] == 0) ? $tarefasZeroHoras1 = 1 : $tarefasZeroHoras1 = 0;
            } else {
                $grafico1['idTarefa'] = $compara['idTarefa'];
                $grafico1['nomeTarefa'] = $compara['nomeTarefa'];
                $grafico1['acumulaHoras'] = 0;
                $dadosGrafico1[] = $grafico1;
                $tarefasZeroHoras1 = 1;
            }

            if ($compara['idTarefa'] == $dadosGraficoPlaneado[$num]['idTarefa']) {
                $grafico2['idTarefa'] = $dadosGraficoPlaneado[$num]['idTarefa'];
                $grafico2['nomeTarefa'] = $dadosGraficoPlaneado[$num]['nomeTarefa'];
                $grafico2['acumulaHoras'] = $dadosGraficoPlaneado[$num]['acumulaHoras'];
                $dadosGrafico2[] = $grafico2;
                ($dadosGrafico2[$num]['acumulaHoras'] == 0) ? $tarefasZeroHoras2 = 1 : $tarefasZeroHoras2 = 0;
            } else {
                $grafico2['idTarefa'] = $compara['idTarefa'];
                $grafico2['nomeTarefa'] = $compara['nomeTarefa'];
                $grafico2['acumulaHoras'] = 0;
                $dadosGrafico2[] = $grafico2;
                $tarefasZeroHoras2 = 1;
            }

            if ($tarefasZeroHoras1 == 1 && $tarefasZeroHoras2 == 1) {
                $temp1 = array_pop($dadosGrafico1);
                $temp1 = array_pop($dadosGrafico2);
            }
            $num++;
        }

        foreach ($dadosGrafico1 as $preGraf) {
            $serie1->addPoint(new Point($preGraf['nomeTarefa'], $preGraf['acumulaHoras']));
        }

        foreach ($dadosGrafico2 as $preGraf) {
            $serie2->addPoint(new Point($preGraf['nomeTarefa'], $preGraf['acumulaHoras']));
        }

        $dataSet->addSerie("Executado", $serie1);
        $dataSet->addSerie("Planeado", $serie2);

        $chart->setDataSet($dataSet);
        $chart->setTitle("Tarefas Executadas vs Planeadas (horas) - " . $dataHoraInicio . " a " . $dataHoraFim);
        $chart->render("../Graficos/graficoGerado.png");
        echo ("<h1>Gráfico</h1><br><img src=\"../Graficos/graficoGerado.png\">");
    } else {
        echo ("<h1>Não existem dados para o intervalo de tempo em questão</h1>");
    }
}

function desenhaGraficoQueijo($dadosGrafico, $dataHoraInicio, $dataHoraFim) {

    if ($dadosGrafico != NULL) {
        $chart = new PieChart(770, 380);

        $dataSet = new XYDataSet();

        foreach ($dadosGrafico as $preGraf) {
            if ($preGraf['acumulaHoras'] != 0) {
                $dataSet->addPoint(new Point($preGraf['nomeTarefa'] . "(" . $preGraf['acumulaHoras'] . ")", $preGraf['acumulaHoras']));
            }
        }

        $chart->setDataSet($dataSet);
        $chart->setTitle("Tarefas (%) - " . $dataHoraInicio . " a " . $dataHoraFim);
        $chart->render("../Graficos/graficoGerado.png");
        echo ("<h1>Gráfico</h1><br><img src=\"../Graficos/graficoGerado.png\">");
    } else {
        echo ("<h1>Não existem dados para o intervalo de tempo em questão</h1>");
    }
}

function obtemTarefasGrafico() {

    $db = iniciaBD();
    $sql = "SELECT idTarefa, NomeTarefa FROM Tarefa ORDER BY idTarefa ASC";
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
    return $enviar;
}

function obtemItemsGrafico($dataHoraInicio, $dataHoraFim) {
    $db = iniciaBD();
    $utilizador = $_SESSION['utilizador'];
    $eventos = array();

    $dataHoraFimTratada = $dataHoraFim . " 23:59:59";

    $sql = "SELECT R.idRegistoAtividade, R.DataHoraInicio, R.DataHoraFim, T.NomeTarefa, T.idTarefa, PT.PesoPrioridade, R.NaoPlaneada
            FROM RegistoAtividade AS R, Tarefa AS T, PrioridadeTarefa AS PT
            WHERE R.DataHoraInicio>=? AND R.DataHoraInicio <=? AND R.Utilizador=? AND R.idTarefa = T.idTarefa AND T.idPrioridadeTarefa = PT.idPrioridadeTarefa ORDER BY T.idTarefa";
    try {
// Loop through returned results and store as an array
        $stmt = $db->prepare($sql);
        $stmt->execute(array($dataHoraInicio, $dataHoraFimTratada, $utilizador));
        $resultado = $stmt->fetchAll();
        foreach ($resultado as $row) {
            $linhaEvento['id'] = $row['idRegistoAtividade'];
            $linhaEvento['title'] = $row['NomeTarefa'];
            $linhaEvento['idTarefa'] = $row['idTarefa'];
            $linhaEvento['start'] = $row['DataHoraInicio'];
            $linhaEvento['end'] = $row['DataHoraFim'];

            $dataInicioUx = strtotime($row['DataHoraInicio']);
            $dataFimUx = strtotime($row['DataHoraFim']);
            $diferencaHoras = round((($dataFimUx - $dataInicioUx) / 3600), 2);
            $linhaEvento['totalTempo'] = $diferencaHoras;

            if ($row['NaoPlaneada']) {
                $linhaEvento['color'] = "plum";
            } else {
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
            }
            $eventos[] = $linhaEvento;
        }
    } catch (PDOException $e) {
        echo ("Erro BD: " . $e->getMessage() . "\n");
        echo ("Código erro BD: " . $e->getCode() . "\n");
        exit;
    }
    return $eventos;
}

function obtemItemsGraficoPlaneados($dataHoraInicio, $dataHoraFim) {
    $db = iniciaBD();
    $utilizador = $_SESSION['utilizador'];
    $eventos = array();

    $dataHoraFimTratada = $dataHoraFim . " 23:59:59";

    $sql = "SELECT R.idPlaneamentoAtividade, R.DataHoraInicio, R.DataHoraFim, T.NomeTarefa, T.idTarefa, PT.PesoPrioridade
            FROM PlaneamentoAtividade AS R, Tarefa AS T, PrioridadeTarefa AS PT
            WHERE R.DataHoraInicio>=? AND R.DataHoraInicio <=? AND R.Utilizador=? AND R.idTarefa = T.idTarefa AND T.idPrioridadeTarefa = PT.idPrioridadeTarefa ORDER BY T.idTarefa";
    try {
// Loop through returned results and store as an array
        $stmt = $db->prepare($sql);
        $stmt->execute(array($dataHoraInicio, $dataHoraFimTratada, $utilizador));
        $resultado = $stmt->fetchAll();
        foreach ($resultado as $row) {
            $linhaEvento['id'] = $row['idPlaneamentoAtividade'];
            $linhaEvento['title'] = $row['NomeTarefa'];
            $linhaEvento['idTarefa'] = $row['idTarefa'];
            $linhaEvento['start'] = $row['DataHoraInicio'];
            $linhaEvento['end'] = $row['DataHoraFim'];

            $dataInicioUx = strtotime($row['DataHoraInicio']);
            $dataFimUx = strtotime($row['DataHoraFim']);
            $diferencaHoras = round((($dataFimUx - $dataInicioUx) / 3600), 2);
            $linhaEvento['totalTempo'] = $diferencaHoras;

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
    return $eventos;
}

function obtemItemsRegisto($dataHoraInicio, $dataHoraFim) {
    $db = iniciaBD();
    $utilizador = $_SESSION['utilizador'];
    $eventos = array();
    $sql = "SELECT R.idRegistoAtividade, R.DataHoraInicio, DATE_FORMAT(R.DataHoraInicio, '%Y-%m-%dT%H:%i') AS startDate,
            R.DataHoraFim, DATE_FORMAT(R.DataHoraFim, '%Y-%m-%dT%H:%i') AS endDate, T.NomeTarefa, PT.PesoPrioridade, R.NaoPlaneada, R.Notas
            FROM RegistoAtividade AS R, Tarefa AS T, PrioridadeTarefa AS PT
            WHERE R.DataHoraInicio>=? AND R.DataHoraInicio <=? AND R.Utilizador=? AND R.idTarefa = T.idTarefa AND T.idPrioridadeTarefa = PT.idPrioridadeTarefa ORDER BY idRegistoAtividade";
    try {
// Loop through returned results and store as an array
        $stmt = $db->prepare($sql);
        $stmt->execute(array($dataHoraInicio, $dataHoraFim, $utilizador));
        $resultado = $stmt->fetchAll();
        foreach ($resultado as $row) {
            $linhaEvento['id'] = $row['idRegistoAtividade'];
            $linhaEvento['title'] = $row['NomeTarefa'];
            $linhaEvento['start'] = $row['startDate'];
            $linhaEvento['end'] = $row['endDate'];
            $linhaEvento['allDay'] = false;
            if ($row['NaoPlaneada']) {
                $linhaEvento['color'] = "plum";
            } else {
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
            }
            if (!$row['NaoPlaneada']) {
                $linhaEvento['descricao'] = "Tarefa: " . $row['NomeTarefa'] . "<br>Início: " . substr($row['startDate'], 0, 10) . " " . substr($row['startDate'], 11, 15) . "<br>Conclusão: " . substr($row['endDate'], 0, 10) . " " . substr($row['endDate'], 11, 15) . $row['Notas'];
                $eventos[] = $linhaEvento;
            } else {
                $linhaEvento['descricao'] = "Tarefa Não Planeada: " . $row['NomeTarefa'] . "<br>Início: " . substr($row['startDate'], 0, 10) . " " . substr($row['startDate'], 11, 15) . "<br>Conclusão: " . substr($row['endDate'], 0, 10) . " " . substr($row['endDate'], 11, 15) .  $row['Notas'];
                $eventos[] = $linhaEvento;                
            }
        }
    } catch (PDOException $e) {
        echo ("Erro BD: " . $e->getMessage() . "\n");
        echo ("Código erro BD: " . $e->getCode() . "\n");
        exit;
    }
    echo json_encode($eventos);
}

function output($id, $dados, $tipoOutput) {
    switch ($tipoOutput) {
        case 0:
            echo ("<ul>");
            echo ("<li><a style=\"color:plum\">Peso 5</a></li>");
            echo("<li><a style=\"color:dodgerblue\">Peso 4</a></li>");
            echo("<li><a style=\"color:orangered\">Peso 3</a></li>");
            echo("<li><a style=\"color:goldenrod\">Peso 2</a></li>");
            echo("<li><a style=\"color:forestgreen\">Peso 1</a></li>");
            echo("</ul>");
            break;
    }
}

function iniciaBD() {
// Open a database connection
    try {
        $db = new PDO(DB_INFO, DB_USER, DB_PASS);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo("Erro BD: " . $e->getMessage() . "\n");
        echo("Código Erro BD: " . $e->getCode() . "\n");
    }
    return $db;
}

?>