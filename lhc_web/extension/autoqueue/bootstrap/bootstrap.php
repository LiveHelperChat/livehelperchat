<?php

class erLhcoreClassExtensionAutoqueue {

    public function __construct() {

    }

    //Cadastrar os event listeners aqui
    public function run() {

        $dispatcher = erLhcoreClassChatEventDispatcher::getInstance();

        $dispatcher->listen('statistic.valid_tabs', array($this, 'statisticValidTabs')); //abas válidas da estatística
        $dispatcher->listen('chat.accept', array($this, 'chatAccept')); //ao aceitar um chat
        $dispatcher->listen('user.login_after_success_authenticate', array($this, 'userLoginAfterAuth')); //assim que o usuário é autenticado, após o login
        $dispatcher->listen('chat.chat_transfered', array($this, 'chatTransfered')); //chat acaba de ser transferido
        $dispatcher->listen('chat.chat_transfer_accepted', array($this, 'chatTransferAccepted')); //após aceitar a transferencia
        //$dispatcher->listen('chat.syncadmininterface', array($this, 'syncAdminInterface')); //após sincronização (padrão - 6s)
        $dispatcher->listen('chat.workflow.autoassign', array($this, 'chatWorkflowAutoassign')); //atribuir um usuário antes do fluxo normal da fila
        //erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.dashboardwidgets',array('supported_widgets' => & $supportedWidgets)); novos widgets
    }

    /*Função que interfere no workflow normal de atribuição da fila, para atribuir automaticamente para o atendente anterior, caso
     *exista um atendimento para o mesmo CPF na última hora*/
    public function chatWorkflowAutoassign($params) {
        $chat = $params['chat'];
        $time1h = time() - 3600; //De uma hora atrás para cá

        if ($chat->tslasign != 0) {
            return false; 
        }

        //abaixo retornará false para o workflow se não achar um usuário, fazendo com que atribua pelo método do chat
        $user_id = self::getUserLastChat($chat->id, $time1h, $chat->additional_data, $params['is_online']); //verificar se o usuário foi atendido na última hora e por quem
        if ($user_id && $user_id > 0) {
            return array( 'status' => erLhcoreClassChatEventDispatcher::STOP_WORKFLOW, 'user_id' => $user_id);
        } else {
            return false;
        }
    }

    /*Função ao transferir um chat. Está sendo utilizada para não penalizar um atendente que transfere o chat para outro dar a continuidade.
     *Válido apenas para as últimas 6 horas. Após isto o atendente perde a vez se o transferir. */
    public function chatTransfered($params) {
        $chat = $params['chat'];
        $time1h = time() - (6 * 3600); //De 6 horas atrás para cá

        $transfer = erLhcoreClassTransfer::getTransferByChat($chat->id); //recuperar dados da transferencia

        $user_id = self::getUserLastChat($chat->id, $time1h, $chat->additional_data); //verificar se o usuário foi atendido nas últimas 6 horas e por quem

        if ($user_id && $transfer['transfer_to_user_id'] == $user_id) {
            erLhcoreClassUserDep::updateLastAcceptedByUser($transfer['transfer_user_id'], time() - (24 * 3600)); //coloca o atendente que transferiu para o início da fila
        }
    }

    /* Função para o momento de aceite de uma transferência. Sempre coloca o usuário que recebeu a transferência para o final da fila. */
    public function chatTransferAccepted($params) {
        $chat = $params['chat'];

        erLhcoreClassChat::updateActiveChats($chat->transfer_uid); //atualiza a quantidade de chats ativos de quem transferiu
        erLhcoreClassChat::updateActiveChats($chat->user_id); //atualiza a quantidade de chats ativos de quem recebeu
        if ($chat->status != erLhcoreClassModelChat::STATUS_OPERATORS_CHAT) { //apenas se não for chat de operadores
            erLhcoreClassUserDep::updateLastAcceptedByUser($chat->user_id, time()); //O atendente que aceitou a transferência sempre vai para o final da fila
        }
    }

    //Verificar os chats transferidos acima do limite de tempo e retransferir. Pendente do método para retransferência
    /*public function syncAdminInterface($params) {

        $transferchatsUser = $params['lists']['transfer_chats']['list'];

        if (!empty($transferchatsUser)) {
            $time = time();
            foreach ($transferchatsUser as & $transf) {
                $dept = erLhcoreClassModelDepartament::fetch($transf['dep_id']);
                $diff = $time - $transf['time'];
                if ($time - $transf['time'] > $dept->max_timeout_seconds && $time - $transf['tslasign'] > $dept->max_timeout_seconds) {
                    //erLhcoreClassTransfer::handleTransferredChatOpen()
                    $chat = erLhcoreClassChat::getSession()->load('erLhcoreClassModelChat', $transf['id']);

                    $chat->user_id = $transf['user_id'];
                    $chat->status = erLhcoreClassModelChat::STATUS_PENDING_CHAT;
                    $chat->updateThis();

                    erLhcoreClassChat::updateActiveChats($transf['transfer_uid']); //atualiza a quantidade de chats ativos
                    //erLhcoreClassUserDep::updateLastAcceptedByUser($transf['user_id']);

                    erLhcoreClassChatWorkflow::autoAssign($chat, $dept);


                }
            }
        }

    }*/

    //função para atribuir automaticamente o operador, sobrepõe funcionalidade standard. Deve retornar um user_id
    /*public function chatWorkflowAutoassign($params) {
        $error = false;

        if ($error === true) {
            return false;
        }
    }*/

    //função para adicionar a aba 'fila' às abas válidas de estatística
    public function statisticValidTabs($params) {
        $params['valid_tabs'][] = 'fila';
        $params['valid_tabs'][] = 'fila_redir';
    }

    //função para zerar o contador assim que o operador aceitar o chat, e não quando o sistema atribuir
    public function chatAccept($params) {
        $user_id = $params['user']->getUserID();
        erLhcoreClassChat::updateActiveChats($user_id); //atualiza a quantidade de chats ativos
        erLhcoreClassUserDep::updateLastAcceptedByUser($user_id, time()); //coloca o atendente para o final da fila
    }

    //função para zerar o contador assim que o operador loga no sistema pela p  rimeira vez no dia
    //só é valido se existe pausa entre os turnos da noite (antes das 00:00) e da manhã
    public function userLoginAfterAuth($params) {
        $userid = $params['current_user']->getUserID();

        try {

            $db = ezcDbInstance::get();

            $sql = "SELECT last_accepted FROM lh_userdep WHERE user_id = :user_id ORDER BY last_accepted DESC LIMIT 1";

            $stmt = $db->prepare($sql);
            $stmt->bindValue(':user_id', $userid, PDO::PARAM_INT);

            $stmt->execute();

            $last_accepted = $stmt->fetchColumn();

			$today = new DateTime();
			$today->setTime(0, 0);

			$today_timest = $today->getTimestamp(); //recupera a timestamp de 00:00 do dia atual

            if ($last_accepted < $today_timest) {
                erLhcoreClassUserDep::updateLastAcceptedByUser($userid, time()); //coloca o atendente para o final da fila
            }

            erLhcoreClassChat::updateActiveChats($userid); //atualiza a quantidade de chats ativos

        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }
    }

    /*Verifica o último chat para o CPF dentro do período de tempo, excluindo o chat atual */
    public static function getUserLastChat($chat_id, $user_msg_time, $additional_data, $isOnlineUser = null) {

        $cpf = explode("},{", $additional_data)[0] . "%"; //feito desta forma para facilitar o LIKE - performance

        try {

            $db = ezcDbInstance::get();

            if ($isOnlineUser === null) {
                $isOnlineUser = (int)erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data['online_timeout'];
            }

            $sql = "SELECT c.user_id FROM lh_chat c INNER JOIN lh_userdep u ON c.user_id = u.user_id WHERE c.last_user_msg_time > :time1h AND c.additional_data LIKE :cpf AND c.id != :chat_id AND u.ro = 0 AND u.hide_online = 0 AND u.last_activity > :last_activity ORDER BY last_user_msg_time DESC LIMIT 1";

            $stmt = $db->prepare($sql);
            $stmt->bindValue(':cpf', $cpf, PDO::PARAM_STR);
            $stmt->bindValue(':time1h', $user_msg_time, PDO::PARAM_INT);
            $stmt->bindValue(':chat_id', $chat_id, PDO::PARAM_INT);
            $stmt->bindValue(':last_activity',(time()-$isOnlineUser),PDO::PARAM_INT);

            $stmt->execute();

            return $stmt->fetchColumn();

        } catch (Exception $e) {
            //TODO exception logic
        }
    }

}
