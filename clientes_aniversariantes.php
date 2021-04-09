<h1>Clientes - Aniversariantes</h1>
<?	
	require '../mtos/controllers/utils/fastsearch.php';
	require_once 'modules/configuracoes/views/parametros/request.php';
	require_once 'modules/configuracoes/controllers/configuracoes.php';


	
	$randID = rand(0, 1000);
	
	if ($_GET['mes'] == '') {
		$_GET['mes'] = date("m");
	}
	if ($_GET['ano'] == '') {
		$_GET['ano'] = date("Y");
	}
	
	$filParams['hasAcessoDados'] = true;
	$Configuracoes->filiais_listar($filParams);
	$filiaisArr  = $Configuracoes->fieldsarr['Configuracoes_Filiais'];
	
	$fastsearch = new fastsearch([
		'url' => 'clientes/index/aniversariantes.pesquisar',
		'autoload' => 'true'
    ]);
	
	
	$months = [
		'01' => 'Janeiro',
		'02' => 'Fevereiro',
		'03' => 'Março',
		'04' => 'Abril',
		'05' => 'Maio',
		'06' => 'Junho',
		'07' => 'Julho',
		'08' => 'Agosto',
		'09' => 'Setembro',
		'10' => 'Outubro',
		'11' => 'Novembro',
		'12' => 'Dezembro',
    ];
	
	

	$fastsearch->fastsearch_add([
		'label' => 'Mês',
		'name' => 'mes',
		'size' => 1,
		'size-m' => 6,
		'type' => 'select',
		'defaultValue' => $_GET['mes'],
		'array' => $months
    ]);

	$fastsearch->fastsearch_add([
		'label' => 'Ano',
		'name' => 'ano',
		'size' => 1,
		'size-m' => 6,
		'type' => '',
		'defaultValue' => $_GET['ano'],
		
    ]);
	

	$fastsearch->fastsearch_render();	
	
?>
<? $fastsearch->fastsearch_render_container(); ?>