<?php
    include_once "../config/Database.php";

    function doActivity($target_user_id, $action) {
        try {
            $db = (new Database())->getConnection();
            //echo $target_user_id;
            /*Esse "if" é caso seja promovido usuário à admin ou atualização de dados do usuário;
            * se for login ou logout, não passa por aqui*/
            if ($target_user_id) {
                $stmt = ($action == "Promoveu usuário à admin") ?
                    $db->prepare("UPDATE users SET role = 'admin' WHERE id = :target_user_id") : 
                    $db->prepare("UPDATE users WHERE id = :target_user_id");

                $stmt->bindParam(":target_user_id", $target_user_id);
                $stmt->execute();
            }
        } catch (Exception $e) {
            error_log("Erro ao executar tarefa: " . $e->getMessage());
        }        
    }

    /**
     * Função genérica para registrar logs de atividade
     * 
     * @param int $user_id ID do usuário que executou a ação
     * @param string $action Descrição da ação
     * @param int|null $target_user_id (opcional) ID do usuário afetado pela ação
     * @param string|null $details (opcional) Informações adicionais
     */
    function logActivity($user_id, $action, $target_user_id = null, $details = null) {
        try {
            $db = (new Database())->getConnection();

            $stmt = $db->prepare("
                INSERT INTO activity_logs (admin_id, action, target_user_id, details)
                VALUES (:admin_id, :action, :target_user_id, :details)
            ");
            $stmt->bindParam(":admin_id", $user_id);
            $stmt->bindParam(":action", $action);
            $stmt->bindParam(":target_user_id", $target_user_id);
            $stmt->bindParam(":details", $details);

            $stmt->execute();

        } catch (Exception $e) {
            error_log("Erro ao registrar log: " . $e->getMessage());
        }
    }
?>