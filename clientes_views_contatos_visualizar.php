
<!--[BLOCO 01-inicio]-->
    <?
        //clientes/views/contatos/visualizar.php
        if (!$noLoading) { 
            require_once 'modules/veiculos/controllers/veiculos.php';
            require_once 'modules/pessoas/controllers/pessoas.php';
        
            function calcDistancia($lat1, $long1, $lat2, $long2){
            
                $d2r = 0.017453292519943295769236;

                $dlong = ($long2 - $long1) * $d2r;
                $dlat = ($lat2 - $lat1) * $d2r;

                $temp_sin = sin($dlat/2.0);
                $temp_cos = cos($lat1 * $d2r);
                $temp_sin2 = sin($dlong/2.0);

                $a = ($temp_sin * $temp_sin) + ($temp_cos * $temp_cos) * ($temp_sin2 * $temp_sin2);
                $c = 2.0 * atan2(sqrt($a), sqrt(1.0 - $a));

                return 6368.1 * $c;
            }

            $Clientes->contatos_carregar($_GET);
            $contatosFields = $Clientes->fields['Clientes_Contatos'];
            
            $dsCoordenadas = $contatosFields['dsCoordenadas'];
            $tpMarker = 'Destino';
            
            $veiculo['coord'] = $dsCoordenadas;
            $veiculo['dsPosition'] = '('.$dsCoordenadas.')';
            $veiculo['tpMarker'] = $tpMarker;
            $veiculo['nmPessoa'] = $contatosFields['nmPessoa'];
            $veiculo['dsArquivo'] = $contatosFields['dsArquivo'];
            $sincronizacao[$contatosFields['nmPessoa']] = $veiculo;
                
            $flag = 1;

            if ($_GET['nrIntervalo'] == '') {
                $_GET['nrIntervalo'] = 0;
            }
        }        
    ?>
<!--[BLOCO 01-fim]-->
<style type="text/css">
    #map_canvas {
        height: 100%;
        min-height: 500px;
    }
</style>

<!--[BLOCO 02-inicio]-->
    <script>

        var stops = [];
        var directionsDisplay;
        var directionsService;
        var dtPeriodo = '<?=$_GET['dtPeriodo']; ?>';
        var nrIntervalo = <?=$_GET['nrIntervalo']; ?>;
        var minuto = 0;
        var markersArray = new Object;
        var timeoutCaminhoes = 0;
        var indexMarker = 1;
        var randomColor = '';
        
        
        //[BLOCO 02.1-inicio]
            function calcularRota(directionsService, directionsDisplay) {
                var batches = [];
                var itemsPerBatch = 5; // google API max = 10 - 1 start, 1 stop, and 8 waypoints
                var itemsCounter = 0;
                var wayptsExist = stops.length > 0;		
            
                while (wayptsExist) {
                //alert(wayptsExist);
                    var subBatch = [];
                        var subitemsCounter = 0;			
                //alert(itemsCounter);
                        for (var j = itemsCounter; j < stops.length; j++) {
                        console.log("Ignicao: "+stops[j].Geometry.Ignicao);
                            subitemsCounter++;
                            subBatch.push({
                        location: new window.google.maps.LatLng(stops[j].Geometry.Latitude, stops[j].Geometry.Longitude),
                                stopover: true,
                    
                            });
                            if (subitemsCounter == itemsPerBatch)
                                break;
                        }
                        itemsCounter += subitemsCounter;

                        batches.push(subBatch);
                        wayptsExist = itemsCounter < stops.length;
                //alert(stops.length);
                        // If it runs again there are still points. Minus 1 before continuing to
                        // start up with end of previous tour leg
                        itemsCounter--;
                }

                // now we should have a 2 dimensional array with a list of a list of waypoints
                var combinedResults;
                var unsortedResults = [{}]; // to hold the counter and the results themselves as they come back, to later sort
                var directionsResultsReturned = 0;
            
                console.log(unsortedResults);		
                
                for (var k = 0; k < batches.length; k++) {
            
                    var lastIndex = batches[k].length - 1;
                    var start = batches[k][0].location;
                    var end = batches[k][lastIndex].location;		    

                    // trim first and last entry from array
                    var waypts = [];
                    waypts = batches[k];
                    waypts.splice(0, 1);
                    waypts.splice(waypts.length - 1, 1);

                    var request = {
                    origin: start,
                        destination: end,
                        waypoints: waypts,
                        travelMode: window.google.maps.TravelMode.DRIVING
                    };
            
                    (function (kk) {			
                        console.log("Aqui");
                        directionsService.route(request, function (result, status) {
                            console.log("Dentro");
                            //alert(status);
                            //if (status == window.google.maps.DirectionsStatus.OK) {
                            if (true) {
                                console.log("OK");
                                var unsortedResult = { order: kk, result: result };
                                unsortedResults.push(unsortedResult);
                                directionsResultsReturned++;
                                //console.log(directionsResultsReturned + " - " + batches.length);
                                //alert(directionsResultsReturned);
                                if (directionsResultsReturned == batches.length) {
                                    console.log("TAMANHO");
                                    // sort the returned values into their correct order							
                                    unsortedResults.sort(function (a, b) { return parseFloat(a.order) - parseFloat(b.order); });
                                    var count = 0;							
                                        for (var key in unsortedResults) {
                                            if (unsortedResults[key].result != null) {
                                                if (unsortedResults.hasOwnProperty(key)) {                                
                                                    if (count == 0) // first results. new up the combinedResults object
                                                    combinedResults = unsortedResults[key].result;
                                                    else {
                                                        // only building up legs, overview_path, and bounds in my consolidated object. This is not a complete
                                                        // directionResults object, but enough to draw a path on the map, which is all I need
                                                        combinedResults.routes[0].legs = combinedResults.routes[0].legs.concat(unsortedResults[key].result.routes[0].legs);
                                                        combinedResults.routes[0].overview_path = combinedResults.routes[0].overview_path.concat(unsortedResults[key].result.routes[0].overview_path);

                                                        combinedResults.routes[0].bounds = combinedResults.routes[0].bounds.extend(unsortedResults[key].result.routes[0].bounds.getNorthEast());
                                                        combinedResults.routes[0].bounds = combinedResults.routes[0].bounds.extend(unsortedResults[key].result.routes[0].bounds.getSouthWest());
                                                    }
                                                    count++;
                                                }
                                            }
                                        }
                                        directionsDisplay.setDirections(combinedResults);
                                        var legs = combinedResults.routes[0].legs;
                                        
                                        console.log("Foi");
                                        for (var i=0; i < legs.length;i++){
                                            var hasIgnicao = stops[i].Geometry.Ignicao;
                                            var isUltimo = stops[i].Geometry.isUltimo;
                                            var dtRastreamento = stops[i].Geometry.dtRastreamento;
                                            var markerletter = "A".charCodeAt(0);
                                            markerletter += i;
                                            markerletter = String.fromCharCode(markerletter);
                                            console.log("Foi");
                                            createMarker(directionsDisplay.getMap(),legs[i].start_location,"marker"+i,"some text for marker "+i+"<br>"+legs[i].start_address,markerletter, hasIgnicao, '', dtRastreamento);
                                        }
                                        var i=legs.length;
                                        var markerletter = "A".charCodeAt(0);
                                        markerletter += i;
                                        markerletter = String.fromCharCode(markerletter);
                                        createMarker(directionsDisplay.getMap(),legs[legs.length-1].end_location,"marker"+i,"some text for the "+i+"marker<br>"+legs[legs.length-1].end_address,markerletter, hasIgnicao, 'Sim', dtRastreamento);
                                }
                            }
                        });
                    })(k);
                }
            }
        //[BLOCO 02.1-fim]

        //[BLOCO 02.2-inicio]
            function createMarker(map, latlng, label, html, color, hasIgnicao, isUltimo, dtRastreamento) {
                var truck = '';
                if (isUltimo == 'Sim') {
                    truck = '_actual';
                }
                //alert(isUltimo);
            
                if (hasIgnicao != 'Sim') {
                    truck = '_igoff';
                }
                else {
                    var bkg = 'FF0000';
                }
            
                
                var contentString = '<b>'+label+'</b><br>'+html;
                //alert("Marker");
                var marker = new google.maps.Marker({
                    position: latlng,
                    map: map,
                    icon: '<?=$config['platform']; ?>/img/maps/truck'+truck+'.png',
                    //icon: 'http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld='+color+'|'+bkg+'|000000', // getMarkerImage(color),
                    title: label,
                    zIndex: Math.round(latlng.lat()*-100000)<<5
                });
                marker.myname = label;
                txt = dtRastreamento + "<br>Ignição: "+hasIgnicao;

                //google.maps.event.addListener(marker, 'click', function() {
                //  infowindow.setContent(txt); 
                //  infowindow.open(map,marker);
                //  });
                            
                var infowindow = new google.maps.InfoWindow({
                        content: criarMessage(txt, marker.position, marker.indexMarker)
                });
                google.maps.event.addListener(marker, 'click', function() {
                    infowindow.open(marker.get('map'), marker);
                });


                return marker;
            }
        //[BLOCO 02.2-fim]  

        var hora = 0;
        
        function atualizarSlider() {
            hora = minuto / 60;
            hora = number_format(hora, 2,'.', '');
            $("#horas").ionRangeSlider("update", {
                from: hora
                });
        }

        
        function loadScriptGoogle() {
            var script = document.createElement("script");
            script.type = "text/javascript";
            script.src = "http://maps.google.com/maps/api/js?sensor=false&callback=initialize&key=AIzaSyCQ-0xHUEO9A36tpua2nPRN_ileH12KBSw";
            document.body.appendChild(script);
        }
        
        //[BLOCO 02.3-inicio]
            function initialize() {        
                var mapOptions = {
                    zoom: 12,
                    center: new google.maps.LatLng(-2.536,-44.234),
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                };

                var map = new google.maps.Map(document.getElementById('map_canvas'),mapOptions);
                directionsDisplay = new google.maps.DirectionsRenderer({suppressMarkers: true});
                directionsService = new google.maps.DirectionsService();
            
                <?
                
                    if (is_array($sincronizacao)) {
                        ?>                    
                            randomColor = Math.floor(Math.random()*16777215).toString(16);
                            <?
                            foreach ($sincronizacao as $idSincronizacao => $pontos) {
                                extract($pontos);                        
                                if ($dsPosition != '(,)') {
                                    ?>
                                        var txt = '<strong><?=$nmPessoa; ?></strong><br>';                             
                                        txt += '<?=$nmPessoa; ?><br>';
                                        txt += '<?=$dtEvento; ?><br>';                            
                                        var aux = new google.maps.LatLng<?=$dsPosition; ?>;
                                        placeMarker('<?=$nmPessoa; ?>', aux, map, indexMarker, txt, 10, '', 1, randomColor, '<?=$tpMarker; ?>');                                                           
                                    <?
                                }
                            }
                        
                    }
                
                ?>
                directionsDisplay.setMap(map);
                $("#map_canvas").height(400);
                
                /*
                if (nrIntervalo > 0) {
                    if (timeoutCaminhoes > 0) {
                        clearInterval(timeoutCaminhoes);
                    }
                    /timeoutCaminhoes = window.setInterval('requisitarPosicaoPeriodo()', 4000);
                }
                else {

                    if (timeoutCaminhoes > 0) {
                        clearInterval(timeoutCaminhoes);
                    }
                    //timeoutCaminhoes = window.setInterval('requisitarPosicao()', 10000);
                }
                */
            } // Final de initialize()
        //[BLOCO 02.3-fim]


        var markers = new Object();
        // Função para criar marcação
        //[BLOCO 02.4-inicio]
            function placeMarker(id, position, map, index, txt, radiuso, fillColor, indexMarkerDef, randomColor, tpMarker) {
                markersArray[indexMarker] = [];        
                // Definir Ícone customizado
                var iconBase = 'http://ewiki.titanapps.com.br/mgerencia_testes/modules/veiculos/imagens/';
                var myLatLng = position;
                //alert(tpMarker);
                if (tpMarker == 'Abastecimento') {
                    markers[id] = new google.maps.Marker({
                        id: id,
                        position: position,
                        map: map,
                        icon: '<?=$config['platform']; ?>/img/maps/abastecimento.png'
                    });            
                }
                
                if (tpMarker == 'Destino') {
                    markers[id] = new google.maps.Marker({
                        id: id,
                        position: position,
                        map: map,
                        icon: '<?=$config['platform']; ?>/img/maps/entrega.png'
                    });            
                }

                if (tpMarker == 'Devolucao') {
                    markers[id] = new google.maps.Marker({
                        id: id,
                        position: position,
                        map: map,
                        icon: '<?=$config['platform']; ?>/img/maps/devolucao.png'
                    });            
                }

                if (tpMarker == 'Veiculo') {
                    markers[id] = new google.maps.Marker({
                        id: 'vei-'+id,
                        position: position,
                        map: map,
                        icon: iconBase + 'caminhao.png'
                    });
                }
                
                var marker = markers[id];
                
                markersArray[indexMarker] = marker;
                indexMarker++;
                map.panTo(position);
                console.log(position);
                        
                var infowindow = new google.maps.InfoWindow({
                    content: criarMessage(txt, marker.position, marker.indexMarker)
                });
                
                google.maps.event.addListener(marker, 'click', function() {
                    infowindow.open(marker.get('map'), marker);
                });
                /*
                    google.maps.event.addListener(marker, 'click', function() {
                        deleteMarker(marker.indexMarker);
                        position = requisitarPosicao();
                        placeMarker(id, position, map, index, txt, radiuso, fillColor);
                    });
                */

                google.maps.event.addListener(marker, 'dragend', function() {
                    infowindow.setContent(criarMessage(marker.position, marker.indexMarker));	
                });
                
                google.maps.event.addListener(marker, 'rightclick', function() {
                    deleteMarker(marker.indexMarker);	
                });             
            }
        //[BLOCO 02.4-fim]


        // Função para criar a mensagem
        function criarMessage(txt, position, index) {
            var message = txt + '<br>';
            //var message =  message + '<button onclick="navegarAssistente(\'clientes/index/definirPosicao&position='+position+'\')">Salvar Ponto</button>';
            return 	message;
        }
        
        // Função para deletar marcação
        function deleteMarker(i) {
            if (markersArray[i]) {
                markersArray[i].setMap(null);
            }
        return false;
        }
        
        // Função para colocar descrição
        function attachSecretMessage(marker, number) {    
            var infowindow = new google.maps.InfoWindow({
                content: message[number],
                size: new google.maps.Size(50,50)
            });
            google.maps.event.addListener(marker, 'click', function() {
                infowindow.open(map,marker);
            });
        }	
        
        var index = 0;
        function requisitarPosicao() {
            if (index == 7) { index = 0; }
            var url = defaultDOM + '/pure/veiculos/rastreamentos/requestPosition&index='+index;		
            console.log(url);
            index++;
            if (index == 7) { 
                index = 0; 
            }

            $.getJSON(url, function (data) {
                console.log("Buscando Posicoes");
                $.each(data, function(caminhao, dados) {            
                    $.each(dados, function(key, posicao) {	            
                        if (key == 'coord') {
                            var latlng = posicao.split(",");
                            var position = new google.maps.LatLng(latlng[0], latlng[1]);
                            if (markers[caminhao] != undefined) {
                                markers[caminhao].setPosition(position);
                            }                    
                        }
                    });
                });
            });
        }
        
        function requisitarPosicaoPeriodo() {
            if (index == 7) { 
                index = 0; 
            }

            var url = defaultDOM + '/pure/veiculos/rastreamentos/requestPosition&dtPeriodo='+dtPeriodo+'&minuto='+minuto+'&nrIntervalo='+nrIntervalo+'&index='+index;		
            console.log(url);
            index++;

            if (index == 7) { 
                index = 0; 
            }

            minuto = minuto + nrIntervalo;
            atualizarSlider();        
            $.getJSON(url, function (data) {
                $(".placas").removeClass('txt-color-green');
                $(".placas").addClass('txt-color-red');
                $.each(data, function(caminhao, dados) {                
                    if ((dados != null)) {
                        console.log(dados);
                        $.each(dados, function(key, posicao) {                    
                            if (key == 'coord') {
                                var latlng = posicao.split(",");
                                var position = new google.maps.LatLng(latlng[0], latlng[1]);
                                if (markers[caminhao] != undefined) {
                                    $("#statusPlaca-"+caminhao).removeClass('txt-color-red');
                                    $("#statusPlaca-"+caminhao).addClass('txt-color-green');
                                    markers[caminhao].setPosition(position);
                                }                        
                            }
                        });
                    }    
                });
            });    
        }
        
        //google.maps.event.addDomListener(window, 'load', initialize);
        loadScriptGoogle(); 
        $("#windowvendas .programTitle").remove();
        
    </script>
<!--[BLOCO 02-fim]-->   


<!--[BLOCO 03-inicio]-->
    <div class="row">
        <div class="col col-md-5">    
            <?
                if ($_GET['idContato'] > 0) {    
                    $Clientes->contatos_carregar($_GET);
                    $contatosFields = $Clientes->fields['Clientes_Contatos'];
                    ?>
                        <table class="table" width="30%">
                                <tr>	
                                    <?
                                        $dsArquivo= $contatosFields['dsArquivo'];
                                    ?>
                                    <td><img  src="<?=$config['DefaultDOM'];?>/../clientes/<?=$_SESSION['gerencia']['alias']; ?>/mgerencia/relacionamentos/<?=$dsArquivo;?>"></td>                           
                                </tr>	
                        </table>
                    <?
                }
            ?>

            <script>
                var popbackground="" //specify backcolor or background image for pop window
                var windowtitle="Image Window"  //pop window title
                function detectexist(obj){
                    return (typeof obj !="undefined")
                }

                function jkpopimage(imgpath, popwidth, popheight, title){
                    windowtitle= title;
                    function getpos(){
                        leftpos=(detectexist(window.screenLeft))? screenLeft+document.body.clientWidth/2-popwidth/2 : detectexist(window.screenX)? screenX+innerWidth/2-popwidth/2 : 0
                        toppos=(detectexist(window.screenTop))? screenTop+document.body.clientHeight/2-popheight/2 : detectexist(window.screenY)? screenY+innerHeight/2-popheight/2 : 0
                        if (window.opera){
                            leftpos-=screenLeft
                            toppos-=screenTop
                        }
                    }

                    getpos()
                    var winattributes='width='+popwidth+',height='+popheight+',resizable=yes,left='+leftpos+',top='+toppos
                    var bodyattribute=(popbackground.indexOf(".")!=-1)? 'background="'+popbackground+'"' : 'bgcolor="'+popbackground+'"'
                    if (typeof jkpopwin=="undefined" || jkpopwin.closed)
                        jkpopwin=window.open("","",winattributes)
                    else{
                        //getpos() //uncomment these 2 lines if you wish subsequent popups to be centered too
                        //jkpopwin.moveTo(leftpos, toppos)
                        jkpopwin.resizeTo(popwidth, popheight+30)
                    }
                    jkpopwin.document.open()
                    jkpopwin.document.write('<html><title>'+windowtitle+'</title><body '+bodyattribute+'><img src="'+imgpath+'" style="margin-bottom: 0.5em"><br /></body></html>')
                    jkpopwin.document.close()
                    jkpopwin.focus()
                }
            </script>    
        </div>
        <div id="map_canvas" class="col col-md-7"></div>    
    </div>

<!--[BLOCO 03-fim]--> 


<!--[BLOCO 04-inicio]-->
    <script>
        function intervalAtualizacao() {
            if (windows['vendas']['action'] == 'maps') {
                refreshWindow();
            }
        }
        function pararIntervalo() {
            clearInterval(nrIntervalAtualizacao);
        }
        if (nrIntervalAtualizacao != undefined) {
            clearInterval(nrIntervalAtualizacao);
        }
        else {
            var nrIntervalAtualizacao = 0;
        }
        nrIntervalAtualizacao = window.setInterval('intervalAtualizacao()', 60000);
    </script>
<!--[BLOCO 04-fim]-->


