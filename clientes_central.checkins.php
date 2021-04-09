<?
    //clientes/views/index/central.checkins.php
	require_once 'modules/funcionarios/controllers/funcionarios.php';
	
	$l = $_GET['l'];
	
	$cheParams['Pessoas_idPessoa'] = $_GET['idPessoa'];
	$cheParams['params']['limit'] = $l.',1';
	$Funcionarios->checkins_carregar($cheParams);
	$checkinsFields = $Funcionarios->fields['Funcionarios_Checkins'];
	
	if (is_array($checkinsFields)) {
?>	

<!--[BLOCO 01-inicio]-->
    <div class="row">
        <div class="col col-md-12 col-xs-12">
            <strong><?=$checkinsFields['nmApelido']; ?> em <?=$checkinsFields['Funcionarios_Checkins_dataOD']; ?></strong><br>
            <?=$checkinsFields['Funcionarios_Checkins_descricao']; ?>
        </div>
    </div>
    <div class="row linha_imagens">
        <?
            if ($checkinsFields['Funcionarios_Checkins_dsArquivo'] != '') {
                ?>
                    <div class="col col-md-4 col-xs-4"><img width="100%" onclick="window.open($(this).attr('src'))" src="/clientes/<?=$_SESSION['gerencia']['alias']; ?>/mgerencia/rotas/<?=$checkinsFields['Funcionarios_Checkins_dsArquivo']; ?>"></div>
                <?
            }
            if ($checkinsFields['Funcionarios_Checkins_dsArquivo2'] != '') {
                ?>
                    <div class="col col-md-4 col-xs-4"><img width="100%" onclick="window.open($(this).attr('src'))"  src="/clientes/<?=$_SESSION['gerencia']['alias']; ?>/mgerencia/rotas/<?=$checkinsFields['Funcionarios_Checkins_dsArquivo2']; ?>"></div>
                <?
            }
            if ($checkinsFields['Funcionarios_Checkins_dsArquivo3'] != '') {
                ?>
                    <div class="col col-md-4 col-xs-4"><img width="100%" onclick="window.open($(this).attr('src'))"  src="/clientes/<?=$_SESSION['gerencia']['alias']; ?>/mgerencia/rotas/<?=$checkinsFields['Funcionarios_Checkins_dsArquivo3']; ?>"></div>
                <?
            }
        ?>
    </div>
<!--[BLOCO 01-fim]-->

    <?
        if ($l == 0) {
            ?>
                <script>
                    $("#areaSetaEsquerda").css('display','none');
                </script>
            <?
        }
        else {
            ?>
                <script>
                    $("#areaSetaEsquerda").css('display','block');
                </script>
            <?
        }
        
        $cheParams['Pessoas_idPessoa'] = $_GET['idPessoa'];
        $cheParams['params']['limit'] = ($l + 1).',1';
        $Funcionarios->checkins_carregar($cheParams);
        $checkinsFields = $Funcionarios->fields['Funcionarios_Checkins'];
        if (is_array($checkinsFields)) {
            ?>
                <script>
                    $("#areaSetaDireita").css('display','block');
                </script>
            <?
        }
        else {
            ?>
                <script>
                    $("#areaSetaDireita").css('display','none');
                </script>
            <?
        }
    }
    else {
        ?>
            <script>
                $("#areaSetaEsquerda").css('display','block');
                $("#areaSetaDireita").css('display','none');
            </script>
        <?
    }
?>