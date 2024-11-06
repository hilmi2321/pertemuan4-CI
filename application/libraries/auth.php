<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auth {
    var $CI = NULL;

    function __construct()
    {
        // Get CI's object
        $this->CI =& get_instance();
    }

    // Login validation
    function do_login($username, $password)
    {
        // Check the database for matching username and password
        $this->CI->db->from('user');
        $this->CI->db->where('user_username', $username);
        $this->CI->db->where('user_password', MD5($password)); // Consider using a more secure hash
        $result = $this->CI->db->get();

        if ($result->num_rows() == 0) {
            // Username and password do not match
            return false;
        } else {
            // Retrieve user information from the database
            $userdata = $result->row();
            $session_data = array(
                'user_id' => $userdata->user_id,
                'nama' => $userdata->user_nama,
                'alamat' => $userdata->user_alamat,
                'kota' => $userdata->user_kota,
                'kodepos' => $userdata->user_kodepos,
                'telepon' => $userdata->user_telepon,
                'email' => $userdata->user_email,
                'username' => $userdata->user_username,
                'role' => $userdata->user_role,
                'level' => $userdata->user_level
            );

            // Create session
            $this->CI->session->set_userdata($session_data);
            return true;
        }
    }

    // Check if the user is logged in
    function is_logged_in()
    {
        return !empty($this->CI->session->userdata('user_id'));
    }

    // Restrict access to authenticated users
    function restrict()
    {
        if (!$this->is_logged_in()) {
            redirect('home');
        }
    }

    // Check access to a menu
    function cek_menu($idmenu)
    {
        $this->CI->load->model('usermodel');
        $status_user = $this->CI->session->userdata('level');
        $allowed_level = $this->CI->usermodel->get_array_menu($idmenu);

        if (!in_array($status_user, $allowed_level)) {
            die("Maaf, Anda tidak berhak untuk mengakses halaman ini.");
        }
    }

    // Logout function
    function do_logout()
    {
        $this->CI->session->sess_destroy();
    }
}