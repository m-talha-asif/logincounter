<?php
class block_logincounter extends block_base {
    
    public function init() {
        $this->title = get_string('pluginname', 'block_logincounter');
    }

    public function get_content() {
        global $USER, $DB, $PAGE, $CFG;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = '';
        $this->content->footer = '';

        if (!isloggedin() || isguestuser()) {
            return $this->content;
        }

        // Count total login events for this user
        $logincount = $DB->count_records('logstore_standard_log', [
            'userid' => $USER->id, 
            'eventname' => '\core\event\user_loggedin'
        ]);

        // Format the last login timestamp
        $lastlogin = '-';
        if (!empty($USER->lastlogin)) {
            $lastlogin = userdate($USER->lastlogin);
        } elseif (!empty($USER->currentlogin)) {
            $lastlogin = userdate($USER->currentlogin);
        }

        // Build the original user details table
        $html = '<div class="table-responsive mb-3">';
        $html .= '<table class="table table-bordered table-striped" style="background-color: #fff;">';
        $html .= '<thead style="background-color: #f8f9fa;"><tr><th colspan="2">My Login Details</th></tr></thead>';
        $html .= '<tbody>';
        $html .= '<tr><td>User ID</td><td>' . $USER->id . '</td></tr>';
        $html .= '<tr><td>Username</td><td>' . $USER->username . '</td></tr>';
        $html .= '<tr><td>Name</td><td>' . fullname($USER) . '</td></tr>';
        $html .= '<tr><td>Login Count</td><td>' . $logincount . '</td></tr>';
        $html .= '<tr><td>Last Login</td><td>' . $lastlogin . '</td></tr>';
        $html .= '</tbody></table>';
        $html .= '</div>';

        // Check if the user is an Admin/Manager
        $canviewall = has_capability('block/logincounter:viewalltimes', context_system::instance());

        $html .= '<hr><div class="time-tracker-block">';

        if ($canviewall) {
            // --- ADMIN VIEW ---
            $html .= '<h5>Admin Time Tracker</h5>';
            $html .= '<div class="form-group">';
            $html .= '<label>' . get_string('selectcourse', 'block_logincounter') . '</label>';
            $html .= '<select id="lc-admin-course-select" class="form-control custom-select">';
            $html .= '<option value="">' . get_string('fetching', 'block_logincounter') . '</option>';
            $html .= '</select>';
            $html .= '</div>';
            
            // Container for Admin Results
            $html .= '<div id="lc-admin-results" class="mt-3 table-responsive" style="display:none;">';
            $html .= '<table class="table table-sm table-bordered table-striped" style="background-color: #fff;">';
            $html .= '<thead class="thead-light"><tr><th>Student</th><th>' . get_string('timespent', 'block_logincounter') . '</th></tr></thead>';
            $html .= '<tbody id="lc-admin-tbody"></tbody>';
            $html .= '</table></div>';

        } else {
            // --- USER VIEW ---
            $html .= '<h5>My Course Time</h5>';
            $html .= '<div id="lc-user-results" class="table-responsive">';
            $html .= '<p id="lc-user-loading">' . get_string('fetching', 'block_logincounter') . '</p>';
            $html .= '<table class="table table-sm table-bordered table-striped" id="lc-user-table" style="display:none; background-color: #fff;">';
            $html .= '<thead class="thead-light"><tr><th>Course</th><th>' . get_string('timespent', 'block_logincounter') . '</th></tr></thead>';
            $html .= '<tbody id="lc-user-tbody"></tbody>';
            $html .= '</table></div>';
        }

        $html .= '</div>';

        $this->content->text = $html;
        
        $jsargs = [
            'ajaxurl' => $CFG->wwwroot . '/blocks/logincounter/ajax.php',
            'sesskey' => sesskey(),
            'isadmin' => $canviewall,
            'userid'  => $USER->id
        ];

        $PAGE->requires->js_call_amd('block_logincounter/main', 'init', [$jsargs]);

        return $this->content;
    }
}