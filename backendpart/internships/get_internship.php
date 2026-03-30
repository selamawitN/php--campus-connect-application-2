<?php

require_once '../config/db.php';

header('Content-Type: application/json');

// Allow CORS for frontend access
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

$database = new Database();
$db = $database->getConnection();

try {
    // Get all internships, ordered by deadline (soonest first)
    $stmt = $db->prepare("SELECT * FROM internships ORDER BY deadline ASC");
    $stmt->execute();
    $internships = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format data for frontend
    $formatted_internships = [];
    foreach ($internships as $internship) {
        // Determine badge type based on stipend
        $stipend_type = 'unpaid';
        if (!empty($internship['stipend']) && $internship['stipend'] > 0) {
            $stipend_type = 'paid';
        }
        
        // Determine work type
        $work_type = $internship['work_type'] ?? 'on-site';
        
        $formatted_internships[] = [
            'id' => $internship['id'],
            'title' => $internship['title'],
            'company' => $internship['company'],
            'description' => $internship['description'],
            'location' => $internship['location'],
            'stipend' => $internship['stipend'],
            'stipend_type' => $stipend_type,
            'duration' => $internship['duration'],
            'deadline' => $internship['deadline'],
            'requirements' => $internship['requirements'],
            'year_requirement' => $internship['year_requirement'],
            'work_type' => $work_type,
            'created_at' => $internship['created_at']
        ];
    }
    
    echo json_encode([
        'status' => 'success',
        'data' => $formatted_internships,
        'count' => count($formatted_internships)
    ]);
    
} catch(PDOException $e) {
    echo json_encode([
        'status' => 'error', 
        'message' => 'Failed to retrieve internships',
        'error' => $e->getMessage()
    ]);
}
?>
