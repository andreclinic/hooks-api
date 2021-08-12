<?php



/*****************************************************************************************************/
/*************************** API Whatsapp envio de imagem com Jet Apintment **************************/
/*****************************************************************************************************/


// Pegando dados para o enviar para API do WhatsApp

function pegando_imagem_api($data){

	// Pegar telefone do cliente
	$telefone = valida_telefone($data['user_phone']);
	
	// Pegar Texto da imagem
	$texto_imagem_api = jet_engine()->listings->data->get_option('config-sistema::texto-wa-imagem');
	
	// Pegar URL da imagem
	$url_imagem_api = jet_engine()->listings->data->get_option('config-sistema::mensagem-wa-imagem');

	
	// Nome do sender de envio
	$sender = jet_engine()->listings->data->get_option('config-sistema::nome-do-sender-wa');
	
	// URL da API ou Sistema Intermediário
	$urlApi_img_wa = jet_engine()->listings->data->get_option( 'config-sistema::url-wa-api_imagem');
	

	// Função para enviar os dados para API
	enviar_mensagem_whatsapp_imagem($telefone, $sender, $urlApi_img_wa, $url_imagem_api, $texto_imagem_api);
	
}



// Executa o Envio da Imagem para API
function enviar_mensagem_whatsapp_imagem($telefone, $sender, $urlApi_img_wa, $url_imagem_api, $texto_imagem_api){
   
  $dominio = str_replace(array('http://','https://'), '', get_site_url());

    $data1 = array(
        'sender' => $sender, 
        'idc' => $dominio,  
        'number' => '55' . $telefone, 
        'file' => $url_imagem_api,
        'caption'=>$texto_imagem_api,
    );

    // use key 'http' even if you send the request to https://...
    $options = array(
        'http' => array(
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data1),
            'timeout' => 20,
        ),
    );
    // Montando a requisição
    $context = stream_context_create($options);
   
	// Enviando a requisição
    $result = file_get_contents($urlApi_img_wa, false, $context);

    $resultado = json_decode($result);

    $status_resultado = $resultado->status;

    if($status_resultado == 1){
        $r = array('status' => 'sucesso');
    }else{
        $r = array('status'=> 'Houve um erro ao tentar enviar sua mensagem');
    }

    return $r;
    
}



/************************** Hooks dos formulários do Jet Engine **************************/

add_action('jet-engine-booking/jet-engine-booking/disparo_whatsapp', 'pegando_imagem_api');
add_action( 'jet-engine-booking/disparo_whatsapp', 'pegando_imagem_api');
add_action( 'jet-engine-booking/disparo_whatsapp03', 'pegando_imagem_api');

/***********************************************FIM***************************************************/




/*****************************************************************************************************/
/***************************** API Whatsapp agendamento com Jet Apintment ****************************/
/*****************************************************************************************************/

 
// Pegando dados para o enviar para API do WhatsApp

function pegando_dados_api($data){
	
	// Pega o nome do cliente
	$nome_cliente =  $data['user_name'];
	
	// Pega o nome do tratamento (consulta)
	$tratamento = get_post($data['service_id']);
	if(empty($tratamento)){
		$tratamento = get_post($data['services']);
	}
	
	// Pegar nome do Profissional
	$profissional = get_post($data['provider_id']);
	if(empty($profissional)){
		$profissional = get_post($data['teams_id']);
	}
	
	// Pegar e-mail do cliente
	$email_cliente =  $data['user_email'];
	
	// Pegar telefone do cliente
	$telefone = $data['user_phone'];
	
	// Pegar numero do whatsapp do comercial (Options Page do Jet Engine)
	$telComercial = jet_engine()->listings->data->get_option( 'config-sistema::whatsapp-comercial'); 
	
	// Pegar mensagem para envio de notificação para o cliente (Options Page do Jet Engine)
	$msg_cliente =  jet_engine()->listings->data->get_option('config-sistema::msg_wa_cliente');
	
	// Pegar mensagem para envio de notificação para o comercial (Options Page do Jet Engine)
	$msg_comercial = jet_engine()->listings->data->get_option('config-sistema::msg_wa_comercial');
	
	// Pegando data do agendamento
	$data_agendamento = data_agendamento($data);
	
	// Pegando horário inicial do agendamento
	$horario_inicial = horario_inicial($data);
	
	// Pegando horário final do agendamento
	$horario_final = horario_final($data);
	
	// Gerando mensagem de despedida randomica
	$despedida = array("Muito obrigado, agradecemos a preferencia!","Ficamos a disposição, abraços!","Pode contar com a gente, agradecemos a preferencia!");
	
	// Gerando mensagem de despedida randomica
	$apimsgdespedida = $despedida[array_rand($despedida)];
	
	// Variavel para pular linha do texto
	$plinha = "\r\n";
	
	//Mensagem de envio para o cliente
	$msgCliente = "*AGENDAMENTO*".$plinha.$plinha."Obrigado *".$nome_cliente."*".$plinha.$plinha.$msg_cliente.$plinha.$plinha."Tratamento: *".$tratamento->post_title."*.".$plinha.$plinha."Data: *".$data_agendamento."* Início as *".$horario_inicial."* e finaliza as *".$horario_final."* .".$plinha.$plinha."*Profissional:* *".$profissional->post_title."*".$plinha.$plinha.$apimsgdespedida;
	
	//Mensagem de envio para o comercial
	$msgComercial = "*AGENDAMENTO*".$plinha.$plinha.$msg_comercial.$plinha.$plinha."Nome: "."*".$nome_cliente."*".$plinha.$plinha."E-mail: *".$email_cliente."*".$plinha.$plinha."Telefone: *".$telefone."*".$plinha.$plinha."Tratamento: *".$tratamento->post_title."*".$plinha.$plinha."Profissional: *".$profissional->post_title."*".$plinha.$plinha."Data: *".$data_agendamento."* Início as *".$horario_inicial."* e finaliza as *".$horario_final."*.";
	
	// Nome do sender de envio
	$sender = jet_engine()->listings->data->get_option('config-sistema::nome-do-sender-wa');
	
	// URL da API ou Sistema Intermediário
	$urlApi = jet_engine()->listings->data->get_option( 'config-sistema::url-wa-api');
	
	// Função para enviar os dados para API
	preparando_envio_mensagem_api($telefone, $telComercial, $msgCliente, $msgComercial, $sender, $urlApi);
	
}

/************************** Hooks dos formulários do Jet Engine **************************/

add_action('jet-engine-booking/jet-engine-booking/disparo_whatsapp', 'pegando_dados_api');
add_action( 'jet-engine-booking/disparo_whatsapp', 'pegando_dados_api');
add_action( 'jet-engine-booking/disparo_whatsapp03', 'pegando_dados_api');


	
/************************** Preparando dados para envio da Mensagem para API **************************/

	function preparando_envio_mensagem_api($telefone, $telComercial, $msgCliente, $msgComercial, $sender, $urlApi){

    $telefoneCliente = valida_telefone($telefone);
    $telComercial = valida_telefone($telComercial);
    $telefones = array('telComercial' => $telComercial, 'telCliente' => $telefone);
    $msgCompleta = $msgCliente;


    foreach ($telefones as $value):
        $telefoneArray = $value;
        if($telefoneArray == $telComercial){
            
            $msgCompleta = $msgComercial;
            $telefone = $telComercial;

            $r = enviar_mensagem_whatsapp_texto_api($sender,$telefone,$msgCompleta, $urlApi);


        
            if($r['status'] == "sucesso"){
                //echo "Mensagem Enviada com sucesso";
            }else{
                //echo "Houve um erro";
            }

        }else{
            $msgCompleta = $msgCliente;
            $telefone = $telefoneCliente;
 
            $r = enviar_mensagem_whatsapp_texto_api($sender,$telefone,$msgCompleta, $urlApi);

            if($r['status'] == "sucesso"){
                //echo "Mensagem Enviada com sucesso";
            }else{
                //echo "Houve um erro";
            }
        }
 
    
    endforeach;

}


/************************** Executa o Envio da Mensagem para API **************************/

function enviar_mensagem_whatsapp_texto_api($sender,$telefone,$msgCompleta, $urlApi){
    
    
	$dominio = str_replace(array('http://','https://'), '', get_site_url());

    $data1 = array('sender'=> $sender, 'idc' => $dominio,  'number' => '+55' . $telefone, 'message' => $msgCompleta);
    // use key 'http' even if you send the request to https://...
    $options = array(
        'http' => array(
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data1),
            'timeout' => 10,
        ),
    );
    // Montando a requisição
    $context = stream_context_create($options);
    // Enviando a requisição
    $result = file_get_contents($urlApi, false, $context);

    $resultado = json_decode($result);

    $status_resultado = $resultado->status;

    if($status_resultado == 1){
        $r = array('status' => 'sucesso');
    }else{
        $r = array('status'=> 'Houve um erro ao tentar enviar sua mensagem');
    }

    return $r;
    
}


/************************** Função validar validar o telefone **************************/

function valida_telefone($telefone)
{
    
    $telefone = preg_replace("/\D/", "", $telefone); 
    $ddd = substr($telefone, 0, 2); 

    if ($ddd === "11" || $ddd === "12" || $ddd === "13" || $ddd === "14" || $ddd === "15" || $ddd === "16" || $ddd === "17" || $ddd === "18" || $telefone1 === "19" || $telefone1 === "21" || $telefone1 === "22" || $telefone1 === "24") {
        $telefone = $telefone;
    } else {
        $telefone = substr($telefone, -8); 
        $telefone = $ddd . $telefone; 
        $telefone = $telefone;
    }

    return $telefone;
}


/************************** Pegar data deagendamento do Jet Apointemnt **************************/

function data_agendamento($data){

		// Tratando as datas No banco do jet apointiment
		global $wpdb;
		$id_agendamento = $data['appointment_id_list'][0];
		$result = $wpdb->get_results ( "SELECT * FROM wp_jet_appointments  WHERE ID = $id_agendamento ",ARRAY_A );
		$data_dia = date('d/m/Y', $result[0]['date']);
		$data_hi = date('H:i', $result[0]['slot']);
		$data_hs = date('H:i', $result[0]['slot_end']);
	
	return $data_dia;
}


/************************** Pegar data horário Inicial do Jet Apointemnt **************************/

function horario_inicial($data){

		// Tratando as datas No banco do jet apointiment
		global $wpdb;
		$id_agendamento = $data['appointment_id_list'][0];
		$result = $wpdb->get_results ( "SELECT * FROM wp_jet_appointments  WHERE ID = $id_agendamento ",ARRAY_A );
		$data_dia = date('d/m/Y', $result[0]['date']);
		$data_hi = date('H:i', $result[0]['slot']);
		$data_hs = date('H:i', $result[0]['slot_end']);
	
	return $data_hi;
}

/************************** Pegar data horário final do Jet Apointemnt **************************/

function horario_final($data){

		// Tratando as datas No banco do jet apointiment
		global $wpdb;
		$id_agendamento = $data['appointment_id_list'][0];
		$result = $wpdb->get_results ( "SELECT * FROM wp_jet_appointments  WHERE ID = $id_agendamento ",ARRAY_A );
		$data_dia = date('d/m/Y', $result[0]['date']);
		$data_hi = date('H:i', $result[0]['slot']);
		$data_hs = date('H:i', $result[0]['slot_end']);
	
	return $data_hs;
}

/************************************************FIM**************************************************/
