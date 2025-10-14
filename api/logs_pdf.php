<?php
    // logs_pdf.php
    require "../vendor/autoload.php";
    use Dompdf\Dompdf;
    include_once "../config/Database.php";
    include_once "auth.php";

    header("Content-Type: application/json; charset=UTF-8");
    $admin = authenticate(true); // só admins
    //$admin = (object)['mark' => 'mark@email.com']; // mock

    // Recebe filtros opcionais via GET
    $action = $_GET['action'] ?? '';
    $from = $_GET['from'] ?? ''; // YYYY-MM-DD
    $to = $_GET['to'] ?? '';     // YYYY-MM-DD

    $db = (new Database())->getConnection();
    $query = "
    SELECT l.id, u.name AS admin_name, l.action, t.name AS target_name, l.details, l.created_at
    FROM activity_logs l
    JOIN users u ON u.id = l.admin_id
    LEFT JOIN users t ON t.id = l.target_user_id
    WHERE 1=1
    ";
    $params = [];
    if ($action) {
        $query .= " AND l.action LIKE :action";
        $params[':action'] = "%{$action}%";
    }
    if ($from) {
        $query .= " AND l.created_at >= :from";
        $params[':from'] = $from . " 00:00:00";
    }
    if ($to) {
        $query .= " AND l.created_at <= :to";
        $params[':to'] = $to . " 23:59:59";
    }
    $query .= " ORDER BY l.created_at DESC LIMIT 1000"; // limite para não sobrecarregar

    $stmt = $db->prepare($query);
    foreach ($params as $k=>$v) $stmt->bindValue($k, $v);
    $stmt->execute();
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Monta HTML do relatório
    $html = '<html><head><meta charset="utf-8"><style>
    body{font-family: DejaVu Sans, sans-serif;font-size:12px}
    table{width:100%;border-collapse:collapse}
    th,td{border:1px solid #ddd;padding:6px}
    th{background:#f5f5f5}
    </style></head><body>';
    $html .= "<h2>Relatório de Logs</h2>";
    $html .= "<p>Gerado por: {$admin->email} em ".date('Y-m-d H:i:s')."</p>";
    $html .= "<table><thead><tr><th>ID</th><th>Admin</th><th>Ação</th><th>Alvo</th><th>Detalhes</th><th>Data</th></tr></thead><tbody>";
    foreach ($logs as $l) {
        $html .= "<tr>";
        $html .= "<td>{$l['id']}</td>";
        $html .= "<td>".htmlspecialchars((string) $l['admin_name'])."</td>";
        $html .= "<td>".htmlspecialchars((string) $l['action'])."</td>";
        $html .= "<td>".htmlspecialchars((string) $l['target_name'])."</td>";
        $html .= "<td>".htmlspecialchars((string) $l['details'])."</td>";
        $html .= "<td>{$l['created_at']}</td>";
        $html .= "</tr>";
    }
    $html .= "</tbody></table></body></html>";

    // Gerar PDF com Dompdf
    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $filename = 'logs_'.date('Ymd_His').'.pdf';

    // Enviar PDF para download
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="'.$filename.'"');
    echo $dompdf->output();
    exit;
?>