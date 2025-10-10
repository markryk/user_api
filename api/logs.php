<?php
    include_once "../config/Database.php";
    include_once "auth.php";

    header("Content-Type: application/json; charset=UTF-8");

    $admin = authenticate(true); // só admins podem ver logs

    // Parâmetros de filtro
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $offset = ($page - 1) * $limit;
    $action = $_GET['action'] ?? '';
    $user = $_GET['user'] ?? '';

    // Conexão
    $db = (new Database())->getConnection();

    $query = "
        SELECT 
            l.id, 
            u.name AS admin_name, 
            l.action, 
            t.name AS target_name, 
            l.details, 
            l.created_at
        FROM activity_logs l
        JOIN users u ON u.id = l.admin_id
        LEFT JOIN users t ON t.id = l.target_user_id
        WHERE 1=1
    ";

    if (!empty($action)) {
        $query .= " AND l.action LIKE :action";
    }
    if (!empty($user)) {
        $query .= " AND (u.name LIKE :user OR t.name LIKE :user)";
    }

    $query .= " ORDER BY l.created_at DESC LIMIT :limit OFFSET :offset";

    $stmt = $db->prepare($query);

    if (!empty($action)) {
        $actionFilter = "%{$action}%";
        $stmt->bindParam(":action", $actionFilter);
    }
    if (!empty($user)) {
        $userFilter = "%{$user}%";
        $stmt->bindParam(":user", $userFilter);
    }

    $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
    $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
    $stmt->execute();

    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($logs);
?>