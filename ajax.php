<?php
define('AJAX_SCRIPT', true);
require_once('../../config.php');

require_login();
require_sesskey();

$action = required_param('action', PARAM_ALPHA);

header('Content-Type: application/json');

// --- Helper Function to Calculate Time Spent ---
function calculate_time_spent($userid, $courseid) {
    global $DB;
    
    // Get all log events for this user in this course, ordered by time
    $logs = $DB->get_records_sql("
        SELECT id, timecreated 
        FROM {logstore_standard_log} 
        WHERE userid = ? AND courseid = ? 
        ORDER BY timecreated ASC", 
        [$userid, $courseid]
    );

    if (empty($logs)) {
        return 0; // No time spent
    }

    $total_seconds = 0;
    $last_time = 0;
    $session_timeout = 900; // 15 minutes (900 seconds) of inactivity closes the session

    foreach ($logs as $log) {
        if ($last_time == 0) {
            $last_time = $log->timecreated;
            continue;
        }

        $diff = $log->timecreated - $last_time;

        // If the gap between clicks is less than 15 mins, add it to total time
        if ($diff < $session_timeout) {
            $total_seconds += $diff;
        }
        
        $last_time = $log->timecreated;
    }

    // Add a baseline of 1 minute (60s) for single-click sessions so they don't show 0
    if ($total_seconds == 0 && count($logs) > 0) {
        $total_seconds = 60; 
    }

    return $total_seconds;
}

// --- Helper Function to Format Seconds to Text ---
function format_time_string($seconds) {
    if ($seconds == 0) return '0 min';
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds / 60) % 60);
    $secs = $seconds % 60;
    
    $str = '';
    if ($hours > 0) $str .= "{$hours} hr ";
    if ($minutes > 0) $str .= "{$minutes} min ";
    if ($secs > 0) $str .= "{$secs} sec";
    
    return trim($str);
}


switch ($action) {

    // ADMIN: Get all courses to populate the dropdown
    case 'getallcourses':
        if (!has_capability('block/logincounter:viewalltimes', context_system::instance())) {
            echo json_encode(['error' => 'No permission']);
            die();
        }
        
        // Exclude site frontpage (id=1)
        $courses = $DB->get_records_sql("SELECT id, fullname FROM {course} WHERE id != 1 ORDER BY fullname ASC");
        
        $result = [];
        foreach ($courses as $c) {
            $result[] = ['id' => $c->id, 'text' => $c->fullname];
        }
        echo json_encode($result);
        break;

    // ADMIN: Get all users enrolled in a specific course and their time
    case 'getcoursereport':
        if (!has_capability('block/logincounter:viewalltimes', context_system::instance())) {
            echo json_encode(['error' => 'No permission']);
            die();
        }
        
        $courseid = required_param('courseid', PARAM_INT);
        
        // Find all users enrolled in this course
        $sql = "SELECT u.id, u.firstname, u.lastname 
                FROM {user} u
                JOIN {user_enrolments} ue ON ue.userid = u.id
                JOIN {enrol} e ON e.id = ue.enrolid
                WHERE e.courseid = ? AND u.deleted = 0";
        $users = $DB->get_records_sql($sql, [$courseid]);
        
        $result = [];
        foreach ($users as $u) {
            $seconds = calculate_time_spent($u->id, $courseid);
            $result[] = [
                'user' => fullname($u),
                'time' => format_time_string($seconds)
            ];
        }
        echo json_encode($result);
        break;

    // USER: Get their enrolled courses and time spent on each
    case 'getmyreport':
        $userid = required_param('userid', PARAM_INT);
        
        // Ensure user is only querying themselves
        if ($userid != $USER->id) {
            echo json_encode(['error' => 'No permission']);
            die();
        }

        // Get courses the user is enrolled in
        $sql = "SELECT c.id, c.fullname 
                FROM {course} c
                JOIN {enrol} e ON e.courseid = c.id
                JOIN {user_enrolments} ue ON ue.enrolid = e.id
                WHERE ue.userid = ?";
        $courses = $DB->get_records_sql($sql, [$userid]);

        $result = [];
        foreach ($courses as $c) {
            $seconds = calculate_time_spent($userid, $c->id);
            $result[] = [
                'course' => $c->fullname,
                'time' => format_time_string($seconds)
            ];
        }
        echo json_encode($result);
        break;
}