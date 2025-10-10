<?php
    include_once "../config/Database.php";

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