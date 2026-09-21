<?php

require_once dirname(__DIR__) . '/middleware/AuthMiddleware.php';
AuthMiddleware::allow([1, 2, 3, 4]);

require_once dirname(__DIR__) . '/config/database.php';

header('Content-Type: application/json');

$db = (new Database())->connect();

$stmt = $db->query("
SELECT
COUNT(*) total_members,
SUM(is_present) present_members,
ROUND(
(SUM(is_present)/COUNT(*))*100,
2
) attendance_rate
FROM meeting_attendance
");

echo json_encode(
$stmt->fetch()
);