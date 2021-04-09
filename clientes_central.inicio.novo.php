<?
    //clientes/views/index/central.inicio.novo.php
	require_once 'modules/financeiro/controllers/financeiro.php';
	require_once 'modules/pessoas/controllers/pessoas.php';
	require_once 'modules/clientes/controllers/clientes.php';
	require_once 'modules/promocoes/controllers/promocoes.php';
	require_once 'modules/vendas/controllers/vendas.php';
	
	if ($_GET['idPessoa'] == '') {
		die();
	}
	

	//VER TODOS OS DÉBITOS DO CLIENTE

//[bloco listar ou carregar-inicio]
	$receberArr = $Financeiro->cobrancas_receber_listar([        
        'idPessoa' => $_GET['idPessoa'],
        // fiquei na duvida de como fazer com essa instrução ==>>> $dtPeriodoCobranca = $recParams['dtPeriodo'] = '00-00-0000,'.date("d-m-Y", mktime(0, 0, 0, date("m"), date("d") - 5, date("Y")));
        $dtPeriodoCobranca = 'dtPeriodo' => '00-00-0000,'.date("d-m-Y", mktime(0, 0, 0, date("m"), date("d") - 5, date("Y"))),
        'tpPeriodo' => 'dtVencimentoCobrancaReceber',
        'statusCobranca' => 'Aberto';
        'statusTransitoNotIn' => '("Expedido","Transito")',
        'naoAgruparPessoa' => true
    ]);
	
	$serasaFields = $Pessoas->serasa_carregar([
        'idPessoa' => $_GET['idPessoa']
    ]);
	
	
	$serasaArr = $Pessoas->serasa_listar([
       'params' => [
           'limit' => '0,4'
       ]
    ]);

	$receberArr = $Financeiro->cobrancas_receber_listar([
        'idPessoa' => $_GET['idPessoa'],
        'dtPeriodo' => '00-00-0000,99-99-9999',
        'tpPeriodo' => 'dtVencimentoCobrancaReceber',
        'statusTransitoNotIn' => '("Expedido","Transito")',
        'statusCobranca' => 'Aberto',
        'naoAgruparPessoa' => true,
    ]);
	
    $pesFields = $Pessoas->carregar([
        'idPessoa' => $_GET['idPessoa']
    ]);

    //$Pessoas->enderecos_carregar($cliParams);
	//$enderecosFields = $Pessoas->fields['Pessoas_Enderecos'];
    $enderecosFields = $Pessoas->enderecos_carregar([

    ]);

    $checkinFields = $Clientes->contatos_listar([
        'idPessoa' => $_GET['idPessoa']
        'idContatoTipo' => '8,10,11'
    ]);
 
    $serasaFields = $Pessoas->serasa_carregar([
        'idPessoa' => $_GET['idPessoa']
    ]);
    
    $cliFields = $Clientes->carregar([
        'idPessoa' => $_GET['idPessoa'],
	    'hasColors' => true
    ]);
			
	$relParams = $Clientes->condicoescomerciais_relacionamentos_carregar([
        'Clientes_idCliente' => $cliFields['idCliente']
    ]);

    $cobrancasTiposArr = $Financeiro->cobrancas_tipos_listar([
        'idPessoa' => $_GET['idPessoa']	
    ]);
	

    $planosArr = $Financeiro->planosdepagamentos_listar([
        $funParams['idPessoa' = $_GET['idPessoa'],	
        $funParams['isAberto'] = 'Não'
    ]);
//[bloco listar ou carregar-fim]

//[bloco if, foreach-inicio]
	//PROCESSAR O CONTAS A RECEBER
	if (is_array($receberArr)) {
		foreach ($receberArr as $cobrancaReceberFields) {			
			$dias = $fieldsProcessing->calcularDias($cobrancaReceberFields['dtVencimentoCobrancaReceber'], date("d/m/Y"));	
			if (($dias >= $parametros['Financeiro_qtdDiasToleranciaJuros']) && ($cobrancaReceberFields['hasCobrancaJuros'] == 'Sim') && ($cobrancaReceberFields['statusCobranca'] == 'Aberto')){
				$vrMulta = ($parametros['vrMulta'] / 100) * $cobrancaReceberFields['vrBruto'];
				$vrJuros = ((($parametros['vrTaxaJuros']* $cobrancaReceberFields['vrBruto']) / 100) / 30 ) * ($dias - 1);
				$vrAtualizado = $cobrancaReceberFields['vrBruto'] + $vrMulta + $vrJuros;
			}
			$vrTotalCobrancas += $cobrancaReceberFields['vrBruto'];
			$vrTotalJuros += $vrJuros;
			$vrTotalAtualizado += $vrAtualizado;
		}
	}	
	
	//PROCESSAR O CONTAS A RECEBER
	if (is_array($receberArr)) {
		foreach ($receberArr as $cobrancaReceberFields) {
			$dias = $fieldsProcessing->calcularDias($cobrancaReceberFields['dtVencimentoCobrancaReceber'], date("d/m/Y"));	
			if (($dias >= $parametros['Financeiro_qtdDiasToleranciaJuros']) && ($cobrancaReceberFields['hasCobrancaJuros'] == 'Sim') && ($cobrancaReceberFields['statusCobranca'] == 'Aberto')){
				$vrMulta = ($parametros['vrMulta'] / 100) * $cobrancaReceberFields['vrBruto'];
				$vrJuros = ((($parametros['vrTaxaJuros']* $cobrancaReceberFields['vrBruto']) / 100) / 30 ) * ($dias - 1);
				$vrAtualizado = $cobrancaReceberFields['vrBruto'] + $vrMulta + $vrJuros;
				
			}
			else {
				$vrAtualizado = $cobrancaReceberFields['vrBruto'] + $vrMulta + $vrJuros;			
			}	
			
			if ($dias < 0) {
				//CALCULAR QUANTO JÁ ESTÁ COMPROMETIDO POR MÊS, EM MÉDIA
				$mes = substr($cobrancaReceberFields['dtVencimentoCobrancaReceberOD'], 3, 7);
				if ($qtdComprometidos < 3) {
					$comprometido[$mes] += $vrAtualizado;
				}
				$qtdComprometidos++;
				
			}
			
			$vrBrutoTotal += $cobrancaReceberFields['vrBruto'];
			
			
			
			$vrTotalCobrancasTotais += $vrAtualizado;
		}
	}
	  
		
	if ($pesFields['nrCelular1'] != '') {
		$numeros[$pesFields['nrCelular1']]['tipo'] = 'nrCelular1';
		$numeros[$pesFields['nrCelular1']]['icone'] = 'mobile';
	}
	
	if ($pesFields['nrCelular2'] != '') {
		$numeros[$pesFields['nrCelular2']]['tipo'] = 'nrCelular2';
		$numeros[$pesFields['nrCelular2']]['icone'] = 'mobile';
	}
	
	if ($pesFields['nrTelefone1'] != '') {
		$numeros[$pesFields['nrTelefone1']]['tipo'] = 'nrTelefone1';
		$numeros[$pesFields['nrTelefone1']]['icone'] = 'phone';
	}
	
	if ($pesFields['nrTelefone2'] != '') {
		$numeros[$pesFields['nrTelefone2']]['tipo'] = 'nrTelefone2';
		$numeros[$pesFields['nrTelefone2']]['icone'] = 'phone';
	}	
		
	$contatosArr = $Pessoas->contatos_listar([
        'Pessoas_id' => $pesFields['idPessoa'],
	    'Pessoas_Contatos_tpContato' => 'Celular'
    ]);

	if (is_array($contatosArr)) {
		foreach ($contatosArr as $contato) {
			$numeros[$contato['Pessoas_Contatos_nrContato']]['tipo'] = 'Contato';
			$numeros[$contato['Pessoas_Contatos_nrContato']]['nome'] = $contato['Pessoas_Contatos_dsContato'];
			$numeros[$contato['Pessoas_Contatos_nrContato']]['icone'] = 'mobile';
		}
	}	
	
	
	//VERIFICAR SE A PESSOA POSSUI ALGUM CHECKIN FEITO
	
	
	
	if ($_GET['idPessoa'] > 1) {
		//CARREGAR A ÚLTIMA VENDA
		$vendParams['idPessoa'] = $_GET['idPessoa'];
		$vendParams['statusFaturamento'] = 'Faturado';
		$vendParams['params']['fields'][] = ['sql' => 'idVenda', 'nmField' => 'idVenda'];        
		$vendParams['params']['fields'][] = ['sql' => 'vrVenda', 'nmField' => 'vrVenda'];
		$vendParams['params']['fields'][] = ['sql' => 'nmPessoaRCA', 'nmField' => 'nmPessoaRCA'];
		$vendParams['params']['fields'][] = ['sql' => 'DATE_FORMAT(dtFaturamento, "%d/%m/%Y")', 'nmField' => 'dtFaturamentoOD'];
		
        $Vendas->carregar($vendParams);
		$vendasFields = $Vendas->fields['Vendas'];


		//CARREGAR A ÚLTIMA CONSULTA SERASA
		
		
	}
	
	
	//--------------------------------------------
	if ($_GET['idPessoa'] > 1) {
		$devolucoesArr = $Vendas->cargas_sincronizacao_listar([
            'dtSincronizacao' => date("d-m-Y", mktime(0, 0, 0, date("m")-8, date("d") , date("Y"))).','.date("d-m-Y", mktime(0, 0, 0, date("m"), date("d") , date("Y"))),
            'idPessoa' => $_GET['idPessoa'],
            'tpAcao' => 'DevolucaoTotal'
        ]);

		if(is_array($devolucoesArr)){
			foreach($devolucoesArr as $devolucoes){
				$idsSincronizacao .= $devolucoes['idSincronizacao'].',';
				//die();
			}

		}
		
		//CARREGAR O LIMITE DE CRÉDITO		
		$limFields = $Clientes->limitesdecredito_carregar([
            'idPessoa' => $_GET['idPessoa']
        ]);
	}
	
	
	//SE TIVER ATRASOS, VERIFICAR SE ELE FOI COBRADO OU QUANDO FOI COBRADO
	if ($vrTotalCobrancas > 0) {		
		$contFields = $Clientes->contatos_carregar([
            'idPessoa' => $_GET['idPessoa'],
            'idContatoTipo' => '1,7,10,12,13,14'  
        ]);			
	}
		
	
		
	if (is_array($cobrancasTiposArr)) {
		foreach ($cobrancasTiposArr as $cobranca) {
			$cobrancas .= $cobranca['cdCobrancaTipo'] . ", ";
		}
	}
	$cobrancas = substr($cobrancas, 0, -2);
	
	
	
	
	
	if (is_array($planosArr)) {
		foreach ($planosArr as $plano) {
			$planos .= $plano['Financeiro_PlanosDePagamentos_nome'] . "<br>";
		}
	}
	
	//$planos = substr($planos, 0, -2);
	
	if($cliFields['isBloqueado'] == 'Sim'){
		$isClasseRed = 'bg-color-red txt-color-white';
	
	}
	
	
	//VERIFICA SE EXISTE UMA PROMOÇÃO PARA LISTAR O SALDO DO CLIENTE
	if ($_GET['idPessoa'] > 1) {
		
		$promocoesFields = $Promocoes->carregar([
            'idPessoa' => $_GET['idPessoa']
        ]);
		
	
		//VERIFICA SE A PESSOA ESTÁ BLOQUEADA EM DEFINITIVO
		if ($cliFields['isBloqueadoDefinitivo'] == 'Sim') {
			$generate->message("warning", "Bloqueado em definitivo");
		}
	
	}
	
	//TESTAR SE A CONDIÇÃO COMERCIAL DELE É VÁLIDA, EXCETO SE ELE GOZAR DE UMA LIBERAÇÃO
	/*
		1. INSCRIÇÃO ESTADUAL
	*/
	if ($cliFields['hasCondicaoEspecial'] == 'Não') {
		if ($relParams['hasInscricaoEstadualValida'] == 'Sim') {
			if (($pesFields['nrIE'] == '') || ($pesFields['nrIE'] == 'ISENTO') || ($pesFields['tpPessoa'] == 'F')) {
			
				

				$generate->message("danger", "Este cliente não está elegível para a tabela de preços atual. Falta inscrição estadual ou é Pessoa Física.");
				
				
				$arr['Clientes_CondicoesComerciais_id'] = 1;
				$arr['Clientes_idCliente'] = $cliFields['idCliente'];
				$arr['Configuracoes_Filiais_idFilial'] = $_SESSION['idFilial'];

				$_POST['fields']['Clientes_CondicoesComerciais_Relacionamentos'] = $arr;
				$Clientes->condicoescomerciais_relacionamentos_atualizar();
								
				$relParams = $Clientes->condicoescomerciais_relacionamentos_carregar([
                    'Clientes_idCliente' => $cliFields['idCliente']
                ]);
				
			}
		}
	}
	

    if ($limFields['Clientes_LimitesDeCredito_valor'] == 0) {
        $limFields['vrSaldo'] = 0;
        $vrPcntMeta = 100;
    }
    else {
        $vrPcntMeta = ($limFields['vrTotalConsumido'] / $limFields['Clientes_LimitesDeCredito_valor']) * 100;
    }
    $vrPcntMeta = round($vrPcntMeta);
    
    if ($vrPcntMeta > 50) {
        $color = 'orange';
    }
    if ($vrPcntMeta > 75) {
        $color = 'red';
    }

//[bloco if, foreach-fim]
 
?>

<!-- [bloco html-inicio]-->		
    <div class="row">
        <div class="col col-md-2">
            <a href="assistentep:financeiro/creditosdeclientes/estornar&idPessoa=<?=$_GET['idPessoa']; ?>" class="btn btn-xs btn-labeled btn-default  fill"> Estornar crédito</a>		
        </div>
        <div class="col col-md-2">
            <a href="assistentep:financeiro/creditosdeclientes/lancar&idPessoa=<?=$_GET['idPessoa']; ?>" class="btn btn-xs btn-labeled btn-default  fill"> Lançar crédito</a>		
        </div>
        <div class="col col-md-2">
            <button onclick="printUrl('pessoas/index/ficha/<?=$_GET['idPessoa']; ?>&noHeader=true&rand=<?=rand(0,1000); ?>', '<?=$_GET['idPessoa'];?>')" class="btn btn-xs btn-labeled btn-default  fill"> Ficha</button>		
        </div>
        <div class="col col-md-2">
            <a href="assistente:vendas/index/central&idPessoa=<?=$_GET['idPessoa']; ?>&dtPeriodo-ignorar=Sim" class="btn btn-xs btn-labeled btn-default  fill"> Pedidos</a>		
        </div>	
        <div class="col col-md-2">
            <a href="assistentep:financeiro/creditosdeclientes/ver/&idPessoa=<?=$_GET['idPessoa']; ?>" class="btn btn-xs btn-labeled btn-default  fill"> Nota de crédito</a> 		
        </div>
    </div>		
    <br><br>
    <div class="row">        
        <div class="col col-md-3">
            <? $html->well_open('Situação Financeira'); ?>
            <a href="assistente:financeiro/cobrancas_receber/central&idPessoa=<?=$_GET['idPessoa']; ?>&statusCobranca=Aberto&tpPeriodo=dtVencimentoCobrancaReceber&dtPeriodo=<?=$dtPeriodoCobranca; ?>" class="txt-color-darken">
                <?
                    if ($vrTotalCobrancasTotais == 0) {
                        $vrPcntMeta = 0;
                    }
                    else {
                        $vrPcntMeta = ($vrTotalAtualizado / $vrTotalCobrancasTotais) * 100;
                    }
                    $vrPcntMeta = round($vrPcntMeta);
                ?>
                <div class="row">                    
                    <div class="col col-md-12">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <td>Opção:</td>
                                    <td align="right">Valor:</td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Vencido:</td>
                                    <td align="right"><?=number_format($vrTotalCobrancas, 2, ',', '.') ;?></td>
                                </tr>
                                <tr>
                                    <td>A Vencer:</td>
                                    <td align="right"><?=number_format($vrBrutoTotal - $vrTotalCobrancas, 2, ',', '.') ;?></td>
                                </tr>
                                <tr>
                                    <td><strong>Total:</strong></td>
                                    <td align="right"><strong><?=number_format($vrBrutoTotal, 2, ',', '.') ;?></strong></td>
                                </tr>
                                <tr>
                                    <td>Créditos:</td>
                                    <td align="right"><?=number_format($cliFields['vrSaldo'], 2, ',', '.') ;?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>	
                </div>                
            </a>
            <? $html->well_close(); ?>

            <!--[BLOCO 02-inicio]-->
                <?
                    if ($parametros['hasAprovacaoLimiteCredito'] == 'Sim') {
                        ?>
                        <? $html->well_open(); ?>
                        <header>Limite de Compras:<?
                            if ($consultaVencida) {
                                ?>
                                    <a href="assistente:clientes/limitesdecredito/criar&idPessoa=<?=$_GET['idPessoa']; ?>" class="btn btn-danger btn-xs pull-right btn-grande">Alterar</a>
                                    <a href="assistente:pessoas/serasa/index&idPessoa=<?=$_GET['idPessoa']; ?>" class="btn btn-default btn-xs pull-right" style=" margin-right:10px">Consultas</a>  
                                <?
                            }
                            else {
                                ?>
                                    <a href="assistente:clientes/limitesdecredito/criar&idPessoa=<?=$_GET['idPessoa']; ?>" class="btn btn-primary btn-xs pull-right btn-grande">Alterar</a>
                                    <a href="assistente:pessoas/serasa/index&idPessoa=<?=$_GET['idPessoa']; ?>" class="btn btn-default btn-xs pull-right" style=" margin-right:10px">Consultas</a>
                                <?
                            }
                        ?>
                        </header>
                        <a href="assistentep:clientes/limitesdecredito/criar&idPessoa=<?=$_GET['idPessoa']; ?>">
                            <div class="row">
                                <div class="col col-md-12">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <td class="txt-color-darken">Opção:</td>
                                                <td align="right">Valor:</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="txt-color-darken">Limite:</td>
                                                <td align="right" class="txt-color-darken"><?=number_format($limFields['Clientes_LimitesDeCredito_valor'], 2, ',', '.') ;?></td>
                                            </tr>
                                            <tr>
                                                <td class="txt-color-darken">Utilizado:</td>
                                                <td align="right" class="txt-color-darken"><?=number_format($limFields['vrTotalConsumido'], 2, ',', '.') ;?></td>
                                            </tr>
                                            <tr>
                                                <td class="txt-color-darken"><strong>Disponível:</strong></td>
                                                <td align="right" class="txt-color-darken"><strong><?=number_format($limFields['vrSaldo'], 2, ',', '.') ;?></strong></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </a>
                        <? $html->well_close(); ?>
                        <?
                            if (is_array($serasaArr)) {
                                ?>
                                <? $html->well_open('Consultas de Crédito:'); ?>	
                                <div class="row">
                                <?
                                    foreach ($serasaArr as $serasa) {
                                        ?>
                                            <a href="#" onclick="window.open('/clientes/<?=$_SESSION['gerencia']['alias']; ?>/mgerencia/arquivos/serasa/<?=$serasa['dsArquivo']; ?>')"><div class="col col-md-3 col-xs-3" style="text-align:center"><?=$serasa['qtdOcorrencias'];?><br><?=$serasa['dtCriacaoOD']; ?></div></a>
                                        <?
                                    }
                                ?>
                                </div>
                                <? $html->well_close(); ?>
                                <?
                            }
                        ?>
                        <?
                    }
                ?>
            <!--[BLOCO 02-fim]-->

            <? $html->well_open('Comprometido (3 meses)'); ?>	
            <?
            if (is_array($comprometido)) {
                ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <td>Mês Venc</td>
                            <td align="right">Valor</td>
                        </tr>
                    </thead>
                <?
                        if (is_array($comprometido)) {
                            foreach ($comprometido as $mes => $valor) {
                                $total += $valor;
                                ?>
                                <tr>
                                    <td width="100%"><?=$mes; ?></td>
                                    <td align="right"><?=number_format($valor, 2, ',', '.'); ?></td>
                                </tr>
                                <?
                            }
                        }
                    ?>
                    <tfoot>
                        <tr>
                            <td><strong>Média</strong></td>
                            <td align="right"><strong><?=number_format($total / count($comprometido), 2, ',', '.'); ?></strong></td>
                        </tr>
                    </tfoot>
                </table>
                <?
            }
            else {
                ?>
                <div class="col col-md-12 texto14 txt-color-green text-center" style="margin-top:10px; margin-bottom:20px;">
                    Nada comprometido <i class="fa fa-thumbs-up"></i>
                </div>
                <?
            }
            ?>
            <? $html->well_close(); ?>
            
            <?
                
                if ($parametros['clientes_hasCheckins'] == 'Sim') {
                    if (is_array($checkinFields)) {
                        ?>
                        <? $html->well_open(); ?>
                        <header>Checkins <a onclick="loadPureURL('clientes/contatos/&idContatoTipoNotIn=8,10,11&idPessoa=<?=$_GET['idPessoa']; ?>', '#aba_principal')" class="btn btn-default btn-xs pull-right btn-grande">Visualizar</a></header>		
                        <div id="areaCarousel">

                            <div id="areaCarouselCheckins"></div>
                            <div id="areaSetaEsquerda"><a onclick="anteriorCarousel()"><i class="fa fa-arrow-left"></i></a></div>
                            <div id="areaSetaDireita"><a onclick="proximoCarousel()"><i class="fa fa-arrow-right"></i></a></div>
                        </div>
                        <style>
                            #areaSetaDireita {
                                position: absolute;
                                right: 0;
                                font-size: 20px;
                                margin-top: -65px;
                                background-color:#FFF;
                                padding:4px;
                                border-top-left-radius: 5px;
                                border-bottom-left-radius: 5px;
                                box-shadow: -5px -2px 19px 0px rgba(0,0,0,0.4);
                            }
                            #areaSetaEsquerda {
                                position: absolute;
                                left: 0;
                                font-size: 20px;
                                margin-top: -65px;
                                display:none;
                                padding:4px;
                                background-color:#FFF;
                                border-top-right-radius: 5px;
                                border-bottom-right-radius: 5px;
                                box-shadow: 5px -2px 19px 0px rgba(0,0,0,0.4);

                            }
                        </style>
                        <? $html->well_close(); ?>
                        <?
                    }
                }
            ?>          
            
        </div>
        <div class=" col col-md-3" id="">
            <? $html->well_open('Histórico de Atrasos'); ?>
            <div id="areaAtrasos"></div>
            <? $html->well_close(); ?>
        </div>
        <div class="col col-md-3">
            <? $html->well_open('Dados Cadastrais'); ?>
            <?
                if ($pesFields['dsArquivoImagem'] != '') {
                    ?>
                    <img src="/clientes/<?=$_SESSION['gerencia']['alias'];?>/mgerencia/arquivos/clientes/fotos/<?=$pesFields['dsArquivoImagem'];?>" style="margin-left:15%;" width="70%"/><br><br>
                    <hr>
                    <?
                }
            ?>
            <span class="texto14 bold" style="text-transform:uppercase">
                <?=$pesFields['nmPessoa'];?></span><br>
            <span class="texto12 bold"><?=$pesFields['nmApelido'];?></span><br><br>
           
            <?            
                if ($parametros['pessoas_hasCadastroCrediario'] == 'Sim') {
                    if (($cliFields['vrRendaConfirmada'] + $cliFields['vrRendaEstipulada']) > 0) {
                        ?>
                        <strong>Trabalho e Renda:</strong><br>
                        <?=$cliFields['dsCargo']; ?> - R$ <?=number_format($cliFields['vrRendaConfirmada'] + $cliFields['vrRendaEstipulada'], 2, ',', '.'); ?><br>
                        <?
                            if ($parametros['vendas_vrLimiteRendaVenda'] > 0) {
                                ?>
                                <strong class="txt-color-red">Valor Máximo da Parcela: R$ <?=number_format(($cliFields['vrRendaConfirmada'] + $cliFields['vrRendaEstipulada']) * ($parametros['vendas_vrLimiteRendaVenda'] / 100), 2, ',', '.'); ?> (<?=$parametros['vendas_vrLimiteRendaVenda']; ?>%)<br></strong>
                                <?						
                            }
                        ?>

                        <br>
                        <?
                    }
                    else {
                        ?>
                        <strong class="txt-color-red texto14">Cliente sem renda informada!</strong><br><br>
                        <?
                    }
                                        
                    if ($cliFields['tpResidência'] == '') {
                        ?>
                        <strong class="txt-color-red texto14">Dados de residência incompletos!</strong><br><br>
                        <?
                    }
                    if ($cliFields['hasComprovanteEndereco'] != 'Sim') {
                        ?>
                        <strong class="txt-color-red texto14">Sem comprovante de endereço!</strong><br><br>
                        <?
                    }                    
                }
            
                if (($pesFields['tpPessoa'] == 'F') && ($pesFields['idPessoa'] > 1)) {
                    if ($pesFields['nmApelido'] == '') {
                        $nm = explode(" ", $pesFields['nmPessoa']);
                        $pesFields['nmApelido'] = $nm[0];
                    }
                ?>
                <script>
                
                    $(".nmPessoaContato").val('<?=$pesFields['nmApelido']; ?>');
                    
                    <?
                        if ($pesFields['nmApelido'] != '') {
                            ?>
                                $(".nmPessoaContato").removeClass('txt-color-white');
                                $(".nmPessoaContato").removeClass('bg-color-redLight');
                            <?
                        }
                        else {
                            ?>
                                $(".nmPessoaContato").addClass('txt-color-white');
                                $(".nmPessoaContato").addClass('bg-color-redLight');
                            <?
                        }
                    ?>
                    
                </script>
                <?
            }
            else {
                ?>
                    <script>
                        $(".nmPessoaContato").addClass('txt-color-white');
                        $(".nmPessoaContato").addClass('bg-color-redLight');
                    </script>
                <?
            }
           
                if ($pesFields['tpPessoa'] == 'F') {
                    ?>
                        <strong>CPF - RG:</strong><br>
                        <?=$fieldsProcessing->mask($pesFields['nrCpf'], '###.###.###-##');?> - <?=$pesFields['nrRG']; ?>
                    <?
                    
                    if ($pesFields['nrRG'] == '') {
                        ?>
                            <strong class="txt-color-red">Cliente sem RG!</strong><br>
                        <?
                    }
                    ?>
                        <br><br>
                    <?
                }
                else {
                    ?>				
                        <strong>CNPJ - IE:</strong><br>
                        <?=$fieldsProcessing->mask($pesFields['nrCnpj'], '##.###.###/####-##');?> - <?=$pesFields['nrIE']; ?><br><br>
                    <?
                }
            ?>
                <a href="assistentep:clientes/index/parcela.liberar/<?=$pesFields['idPessoa'];?>"> 
                    <strong>Valor da parcela liberado:</strong><br>
                    <?=$cliFields['hasLivreParcela']; ?>
                </a><br><br>
            <?
                if (is_array($numeros)) {
                    ?>
                        <strong>Telefones:</strong><br>
                        <div>
                            <?
                                foreach ($numeros as $numero => $num) {
                                    /*//<a class="btn btn-default btn-xs fill" style="margin-bottom:10px" href="assistente:clientes/chamadas/ligar&idPessoa=<?=$pesFields['idPessoa']; ?>&tpTelefone=<?=$num['tipo']; ?>"><i class="fa fa-<?=$num['icone']; ?>" aria-hidden="true"></i> <?=$numero; ?></a>				*/
                                    ?>							
                                        <i class="fa fa-<?=$num['icone']; ?>" aria-hidden="true"></i> <?=$numero; ?> <? if ($num['nome'] != '') { echo " (".$num['nome']. ")"; } ?><br>
                                    <?									
                                }
                            ?>
                        </div><br>
                    <?
                }
                
                if ($enderecosFields['dsEnderecoCompleto'] != '') {
                    ?>
                    
                    <strong>Endereço:</strong><br>
                    <?=$enderecosFields['dsEnderecoCompleto']; ?><br><br>
                    <?
                }
                if ($pesFields['dsEmail'] != '') {
                    ?>
                    <strong>E-mail:</strong><br>
                    <?=$pesFields['dsEmail']; ?><br><br>
                    <?
                }
                if ($cliFields['nmPai'] != '') {
                    ?>
                    <strong>Nome do Pai:</strong><br>
                    <?=$cliFields['nmPai']; ?><br><br>
                    <?
                }
                else {
                    if ($parametros['pessoas_hasCadastroCrediario'] == 'Sim') {
                        ?>
                        <strong class="txt-color-red">Nome do pai não informado!</strong><br>
                        <?
                    }
                }
                if ($cliFields['nmMae'] != '') {
                    ?>
                    <strong>Nome da Mãe:</strong><br>
                    <?=$cliFields['nmMae']; ?><br><br>
                    <?
                }
                else {
                    if ($parametros['pessoas_hasCadastroCrediario'] == 'Sim') {
                        ?>
                        <strong class="txt-color-red">Nome da mãe não informado!</strong><br>
                        <?
                    }
                }
                
            ?>
            <? $html->well_close(); ?>    
            
        </div>
        <div class="col col-md-3">
            <? $html->well_open('Outras Informações'); ?>
            <style>
                .itensInformacoes div {
                    min-height:45px;
                    margin-bottom:5px;
                }
            </style>
            <div class="row itensInformacoes">
                <?
                    if ($vrTotalCobrancas > 0) {
                        if (is_array($contFields)) {
                            ?>
                                <div class="col col-md-12 col-xs-12" >
                                    <strong>Última Cobrança:</strong><br>				
                                    <span class="italic">"<?=$contFields['dsContato']; ?>"</span><br>
                                    <?=$contFields['dtCriacaoOD']; ?> por <?=$contFields['nmPessoaAutor']; ?>
                                </div>
                            <?
                        }
                        else {
                            ?>
                                <div class="col col-md-12 col-xs-12" >
                                    <strong>Última Cobrança:</strong><br>				
                                    <span class="italic txt-color-red">Este cliente não possui cobrança registrada!!</span>
                                </div>
                            <?
                        }
                    }
                ?>                
                <div class="col col-md-12 col-xs-12" >
                    <strong>Última Venda:</strong><br>				
                    R$ <?=number_format($vendasFields['vrVenda'], 2, ',', '.'); ?> em: <?=$vendasFields['dtFaturamentoOD']; ?> por <?=$vendasFields['nmPessoaRCA']; ?> <a href="assistente:vendas/index/vernoprint/<?=$vendasFields['idVenda']; ?>" class="btn btn-xs btn-default pull-right" style="margin-top:-3px"><i class="fa fa-search"></i></a>
                </div>
                <?
                    if(count($devolucoesArr) > 0){
                        ?>
                            <div class="col col-md-6 col-xs-6">
                                <a class="btn btn-danger btn-xs fill" href="assistente:vendas/relatorios/devolucoes&idSincronizacaoIN=<?=$idsSincronizacao; ?>">Retornos:<br><?=count($devolucoesArr);?></a>
                            </div>
                        <?
                    }
                ?>
                <?
                    $diasSerasa = $fieldsProcessing->calcularDias($serasaFields['dtCriacaoOD'], date('d/m/Y'));
                    if ($diasSerasa > $parametros['pessoas_prazovalidadeconsultacredito']) {
                        $colorConsulta = 'txt-color-orange';
                    }
                ?>
                <div class="col col-md-6 col-xs-6 <?=$colorConsulta; ?>">
                    <strong>Data Cons. Crédito:</strong><br>
                    <a  class="<?=$colorConsulta; ?>" href="assistente:pessoas/serasa/listar&idPessoa=<?=$_GET['idPessoa']; ?>"><?=$serasaFields['dtCriacaoOD']; ?></a>
                </div>
                
                <div class="col col-md-6 col-xs-6 ">
                    <strong>Tabela:</strong><br>
                    <?=$relParams['Clientes_CondicoesComerciais_descricao'] ;?>
                </div>
            
                
                <?
                    if ($parametros['hasAprovacaoLimiteCredito'] == 'Sim') {
                        ?>
                            <div class="col col-md-12 col-xs-12">
                                <strong>Cobranças:</strong><br>
                                <?=$cobrancas; ?>
                            </div>
                            <div class="col col-md-12 col-xs-12">
                                <strong>Planos de Pagamento:</strong><br>
                                <?=$planos; ?>
                            </div>
                        <?
                    }
                ?>
                <?
                    if (($cliFields['dtBloqueioOD'] != '') && ($cliFields['dtBloqueioOD'] != '00/00/0000')) {
                        ?>
                            <div class="col col-md-12 col-xs-12">
                                <strong>Motivo do último bloqueio:</strong><br>
                                <?=$cliFields['dsMotivoBloqueio']; ?> em: <?=$cliFields['dtBloqueioOD']; ?>
                            </div>
                        <?
                    }
                ?>

                <div style="text-align:center">
                    <?
                        if (is_array($promocoesFields)) {
                            $generate->button('a', 'warning', 'Cupons: '.number_format($promocoesFields['vrSaldo'] / $promocoesFields['Promocoes_vrCriterio'], 2, ',', '.'), 'clock-o', 'assistente:promocoes/vendas/&Pessoas_idPessoa='.$_GET['idPessoa'],  '', 'xs', '');
                        }              
                    ?>
                    <script>
                        $("#areaBtnInformacoes").html('<?=$generate->button('a', 'default', 'Alterar Tab.', '', 'assistente:vendas/index/criar.pedido.alterartabela&idPessoa='.$_GET['idPessoa'], '',   'xs', ''); ?> <?=$generate->button('a', 'default', 'Imprimir Ficha', '', '', 'printUrl(\\\'pessoas/index/ficha/'.$_GET['idPessoa'].'&noHeader=1\\\', '.$_GET['idPessoa'].');',   'xs', ''); ?> <?=$generate->button('a', 'default', 'Detalhes', '', 'assistente:clientes/contatos/criar.cliente&idContatoTipoNotIn=8,10&idPessoa='.$_GET['idPessoa'],  '', 'xs', ''); ?>');
                    </script>
                </div>
                            
                <? $html->well_close(); ?>
            </div>
        </div>            
    </div> 
<!-- [bloco html-inicio]-->   
    <script>

        <?
            if ($cliFields['isBloqueadoDefinitivo'] == 'Sim') {
                ?>
                $("#areaPessoa input[type=text]").addClass('bg-color-red');
                $("#areaPessoa input[type=text]").addClass('txt-color-white');
                <?
            }
        
        ?>
            statusCliente = 'Aberto';
            $("#iniciarPedido<?=$_GET['rand'];?>").css('display', '');
        <?
                        
            /*
            if ($cliFields['isBloqueado'] == 'Sim') {
                ?>
                statusCliente = 'Aberto';
                $("#iniciarPedido").css('display', 'none');
                <?
            }
            else {
                ?>
                statusCliente = 'Aberto';
                $("#iniciarPedido").css('display', '');
                <?
            }
            */
            
        ?>
        
        function carregarPagar(idPessoa) {        
            var url = "financeiro/cobrancas_receber/lista&hasntPesquisa=true&dtPeriodo-ignorar=Sim&statusCobranca=Aberto&idPessoa="+idPessoa;
            console.log(url );
            navegarAssistente(url);
            /*$.get(url, function(data) {
                $("#areaReceber").html(data);
            });*/
        }
        function carregarUltimaVenda(idVenda) {
            var url = "vendas/index/listar&hasntPesquisa=true&dtPeriodo-ignorar=Sim&idVenda="+idVenda;
            console.log(url );
            navegarAssistente(url);
            /*$.get(url, function(data) {
                $("#areaReceber").html(data);
            });*/
        }
        
    </script>
    <script>
        var indiceCarousel = 0;
        function proximoCarousel() {
            indiceCarousel++;
            carregarCarouselCheckins();
        }
        function anteriorCarousel() {
            indiceCarousel--;
            carregarCarouselCheckins();
        }
        
        function carregarCarouselCheckins() {
            var url = defaultDOM + '/pure/clientes/index/central.checkins&idPessoa=<?=$_GET['idPessoa']; ?>&l='+indiceCarousel;
            inserirLoad("#areaCarouselCheckins");
            $.get(url, function(data) {
                $("#areaCarouselCheckins").html(data);
            });
        }
        
        function carregarAtrasos() {
            var url = 'clientes/index/contasreceber&idPessoa=<?=$_GET['idPessoa']; ?>';
            ewiki_load(url, '#areaAtrasos');
        }
        carregarAtrasos();
        carregarCarouselCheckins();
        
    </script>

    <script>                                                      
        <?
            if ($cliFields['isBloqueadoDefinitivo'] == 'Sim') {
                ?>
                    $("#areaPessoa input[type=text]").addClass('bg-color-red');
                    $("#areaPessoa input[type=text]").addClass('txt-color-white');
                <?
            }
        ?>        
    </script>