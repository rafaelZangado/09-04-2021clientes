<!--[BLOCO 01-inici]-->
    <?
        // clientes/views/index/aniversariantes.pesquisar.php
        if ($_GET['mes'] == '') {
            $_GET['mes'] = date("m");
        }
        if ($_GET['ano'] == '') {
            $_GET['ano'] = date("Y");
        }
        
        $clientesArr = $Clientes->listar([
            'mesAniversario' => $_GET['mes']
        ]);
        
        
        if (is_array($clientesArr)) {
            foreach ($clientesArr as $cliente) {
                $data = $cliente['dtAniversarioOD'];
                if ($cliente['tpPessoa'] == 'F') {
                    $valores[$data] .= "<a href='assistentep:pessoas/index/criar.cliente.experimental/".$cliente['idPessoa']."'>".$cliente['nmApelido'] . "</a><br>";
                }
                else {
                    $valores[$data] .= "<a href='assistentep:pessoas/index/criar.cliente.pj/".$cliente['idPessoa']."'>".$cliente['nmApelido'] . "</a><br>";
                }
                
            }
        }
        
        $funParams['mes'] = $_GET['mes'];
        $ParentesArr = $Clientes->parentes_listar($funParams);
            
        if (is_array($ParentesArr)) {
            foreach ($ParentesArr as $parente) {
                $data = $parente['dtAniversarioOD'];
                $valores[$data] .= "<a href='assistentep:pessoas/index/criar.cliente.experimental/".$parente['Pessoas_idPessoa']."'>".$parente['Clientes_Parentes_nome'] . "</a><br>";
            }
        }
    ?>
<!--[BLOCO 01-fim]-->

<!--[BLOCO 02-inici]-->
    <style>
        /* calendar */
        table.calendar{ 
            border-left:1px solid #999; 
        }
        tr.calendar-row	{  }
        .calendar td {
            border:1px solid #eaeaea !important;
        }
        div.calendar-value {
            font-size:14px;
            text-align:right;
            
        }
        td.calendar-day	{ 
            min-height:90px; 
            font-size:11px; 
            position:relative;  
            height:80px; 
            vertical-align:top !important;  
            } 
        * html div.calendar-day { 
            height:80px; 
        }

        td.calendar-day:hover { 
            background:#eceff5; 
        }
        td.calendar-day-np	{ 
            background:#eee; 
            min-height:80px; 
            height:80px 

        } 
        
        * html div.calendar-day-np { 
            height:80px; 
        }
        
        div.day-number	{ 
            color:#c3c3c3; 
            font-weight:bold; 
            float:right; 
            width:100%; 
            text-align:left; 
        }
        /* shared */
        td.calendar-day, td.calendar-day-np { 
            width:120px;
            padding:5px; 
            border-bottom:1px 
            solid #999; 
            border-right:1px solid #999; 
        }

    </style>
<!--[BLOCO 02-fim]-->

<!--[BLOCO 03-inici]-->
    <?
    /* draws a calendar */
    function draw_calendar($month,$year, $valores){

        /* draw table */
        $calendar = '<table cellpadding="0" cellspacing="0" class="calendar display table table-bordered" width="100%">';

        /* table headings */
        $headings = ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'];
        $months = [
            'Jan' => 'Janeiro',
            'Feb' => 'Fevereiro',
            'Mar' => 'Março',
            'May' => 'Maio',
            'Jun' => 'Junho',
            'Jul' => 'Julho',
            'Aug' => 'Agosto',
            'Sep' => 'Setembro',
            'Oct' => 'Outubro',
            'Nov' => 'Novembro',
            'Dec' => 'Dezembro',
        ];
        
        $calendar.= '<thead><tr><td colspan="7" align="center">'.$months[date("M", mktime(0, 0, 0, $month, 1, $year))].'/'.$year.'</td></tr><tr class="calendar-row"><td class="calendar-day-head">'.implode('</td><td class="calendar-day-head">',$headings).'</td></tr></thead>';

        /* days and weeks vars now ... */
        $running_day = date('w',mktime(0,0,0,$month,1,$year));
        $days_in_month = date('t',mktime(0,0,0,$month,1,$year));
        $days_in_this_week = 1;
        $day_counter = 0;
        $dates_array = array();

        /* row for week one */
        $calendar.= '<tr class="calendar-row">';

        /* print "blank" days until the first of the current week */
        for ($x = 0; $x < $running_day; $x++) {
            $calendar.= '<td class="calendar-day-np"> </td>';
            $days_in_this_week++;
        }

        /* keep going with days.... */
        for ($list_day = 1; $list_day <= $days_in_month; $list_day++) {
            $calendar.= '<td class="calendar-day">';
                /* add in the day number */			
            $checked = '';
            $list_day = str_pad($list_day, 2, '0', STR_PAD_LEFT);
            $dia = $list_day . "/".$month."/".$year;
            $timestamp = $year . "-". $month . "-" . $list_day . " 00:00:00";
        
            if (is_array($diasUteis[$dia])) {
                $checked = 'checked';
            }
            
            $calendar .= '<div class="day-number"><label>'.$list_day.' </label></div>';
            $calendar .= '<div class="calendar-value">'.$valores[$dia].'</div>';
            /** QUERY THE DATABASE FOR AN ENTRY FOR THIS DAY !!  IF MATCHES FOUND, PRINT THEM !! **/
            $calendar.= str_repeat('<p> </p>',2);
            
            $calendar.= '</td>';
            if($running_day == 6){ 
                $calendar.= '</tr>';
                if(($day_counter+1) != $days_in_month) {
                    $calendar.= '<tr class="calendar-row">';
                }
                $running_day = -1;
                $days_in_this_week = 0;
            }
            $days_in_this_week++; $running_day++; $day_counter++;
        };

        /* finish the rest of the days in the week */
        if($days_in_this_week < 8) {
            for($x = 1; $x <= (8 - $days_in_this_week); $x++) {
                $calendar.= '<td class="calendar-day-np"> </td>';
            }
        }

        /* final row */
        $calendar.= '</tr>';
        /* end the table */
        $calendar.= '</table>';
        
        /* all done, return result */
        return $calendar;
    }

    /* sample usages */

    ?>
<!--[BLOCO 03-fim]-->

<!--[BLOCO 04-inici]-->

    <div class="row">
        <div class="col col-md-12">
            <?
                echo draw_calendar($_GET['mes'],$_GET['ano'], $valores);
            ?>
        </div>
    </div>

    <script>	
        function carregarCr(dtPeriodoInicio, dtPeriodoFim) {
            let url = 'relatorios/panorama/central.contas_receber&idFilial=<?=$_GET['idFilial']; ?>&isCartaoDeCredito=<?=$_GET['isCartaoDeCredito']; ?>&tpPeriodo=<?=$campoVencimento; ?>&statusCobranca=<?=$_GET['statusCobranca']; ?>&dtPeriodo='+dtPeriodoInicio+','+dtPeriodoFim;
            console.log(url);
            ewiki_load(url, '#areaDetalhesCR');
        }
    </script>
<!--[BLOCO 04-fim]-->
