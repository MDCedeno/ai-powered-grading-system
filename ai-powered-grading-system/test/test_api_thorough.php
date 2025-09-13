<?php
// Thorough testing script for all backend API endpoints

function sendRequest($method, $url, $data = null)
{
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

    if ($data) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    }

    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    return ['code' => $httpcode, 'response' => $response];
}

$baseUrl = 'http://localhost:3000/ai-powered-grading-system/backend/routes/api.php';

// SuperAdmin tests
echo "Testing SuperAdmin endpoints...\n";
$res = sendRequest('GET', $baseUrl . '/api/superadmin/users');
echo "GET /api/superadmin/users: HTTP {$res['code']}\n";

$res = sendRequest('POST', $baseUrl . '/api/superadmin/users/deactivate', ['user_id' => 1]);
echo "POST /api/superadmin/users/deactivate: HTTP {$res['code']}\n";

$res = sendRequest('GET', $baseUrl . '/api/superadmin/logs');
echo "GET /api/superadmin/logs: HTTP {$res['code']}\n";

// Admin tests
echo "Testing Admin endpoints...\n";
$res = sendRequest('GET', $baseUrl . '/api/admin/students');
echo "GET /api/admin/students: HTTP {$res['code']}\n";

$res = sendRequest('POST', $baseUrl . '/api/admin/students', ['user_id' => 1, 'program' => 'CS', 'year' => 3]);
echo "POST /api/admin/students: HTTP {$res['code']}\n";

$res = sendRequest('PUT', $baseUrl . '/api/admin/students/1', ['program' => 'IT', 'year' => 4]);
echo "PUT /api/admin/students/1: HTTP {$res['code']}\n";

$res = sendRequest('DELETE', $baseUrl . '/api/admin/students/1');
echo "DELETE /api/admin/students/1: HTTP {$res['code']}\n";

// Professor tests
echo "Testing Professor endpoints...\n";
$res = sendRequest('GET', $baseUrl . '/api/professor/students');
echo "GET /api/professor/students: HTTP {$res['code']}\n";

$res = sendRequest('POST', $baseUrl . '/api/professor/grades', [
    'student_id' => 1,
    'course_id' => 1,
    'midterm_quizzes' => 10,
    'midterm_exam' => 20,
    'midterm_grade' => 30,
    'final_quizzes' => 10,
    'final_exam' => 20,
    'final_grade' => 30,
    'gpa' => 3.5
]);
echo "POST /api/professor/grades: HTTP {$res['code']}\n";

// Student tests - Note: These require login, so they will fail without session
echo "Testing Student endpoints...\n";
echo "Note: Student endpoints require login session, testing without login will fail.\n";
$res = sendRequest('GET', $baseUrl . '/api/student/grades');
echo "GET /api/student/grades: HTTP {$res['code']} - Expected failure without login\n";

$res = sendRequest('GET', $baseUrl . '/api/student/courses');
echo "GET /api/student/courses: HTTP {$res['code']} - Expected failure without login\n";

// Auth tests
echo "Testing Auth endpoints...\n";
$res = sendRequest('POST', $baseUrl . '/api/auth/login', ['email' => 'john.doe@plmun.edu.ph', 'password' => 'password123']);
echo "POST /api/auth/login (superadmin): HTTP {$res['code']}\n";

$res = sendRequest('POST', $baseUrl . '/api/auth/login', ['email' => 'charlie.brown@plmun.edu.ph', 'password' => 'password123']);
echo "POST /api/auth/login (student): HTTP {$res['code']}\n";

$res = sendRequest('POST', $baseUrl . '/api/auth/register', ['name' => 'Test User', 'email' => 'test2@example.com', 'password' => 'password', 'role' => 'student']);
echo "POST /api/auth/register: HTTP {$res['code']}\n";

$res = sendRequest('POST', $baseUrl . '/api/auth/logout');
echo "POST /api/auth/logout: HTTP {$res['code']}\n";

echo "Thorough API testing completed.\n";
