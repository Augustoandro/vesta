<?php
// Init
error_reporting(NULL);
ob_start();
session_start();

$TAB = 'USER';
include($_SERVER['DOCUMENT_ROOT']."/inc/main.php");

// Header
include($_SERVER['DOCUMENT_ROOT'].'/templates/header.html');

// Panel
top_panel($user,$TAB);

// Are you admin?
if ($_SESSION['user'] == 'admin') {

    // Check user argument?
    if (empty($_GET['user'])) {
        header("Location: /list/user/");
        exit;
    }

    // Check user
    $v_username = escapeshellarg($_GET['user']);
    exec (VESTA_CMD."v_list_user ".$v_username." json", $output, $return_var);
    if ($return_var != 0) {
        $error = implode('<br>', $output);
        if (empty($error)) $error = 'Error: vesta did not return any output.';
        $_SESSION['error_msg'] = $error;
    } else {
        $data = json_decode(implode('', $output), true);
        unset($output);
        $v_username = $_GET['user'];
        $v_password = "••••••••";
        $v_email = $data[$v_username]['CONTACT'];
        $v_package = $data[$v_username]['PACKAGE'];
        $v_fname = $data[$v_username]['FNAME'];
        $v_lname = $data[$v_username]['LNAME'];
        $v_shell = $data[$v_username]['SHELL'];
        $v_ns = $data[$v_username]['NS'];
        $nameservers = explode(", ", $v_ns);
        $v_ns1 = $nameservers[0];
        $v_ns2 = $nameservers[1];
        $v_ns3 = $nameservers[2];
        $v_ns4 = $nameservers[3];
        $v_suspended = $data[$v_username]['SUSPENDED'];
        if ( $v_suspended == 'yes' ) {
            $v_status =  'suspended';
        } else {
            $v_status =  'active';
        }
        $v_time = $data[$v_username]['TIME'];
        $v_date = $data[$v_username]['DATE'];

        exec (VESTA_CMD."v_list_user_packages json", $output, $return_var);
        $packages = json_decode(implode('', $output), true);
        unset($output);

        exec (VESTA_CMD."v_list_sys_shells json", $output, $return_var);
        $shells = json_decode(implode('', $output), true);
        unset($output);
    }

    // Action
    if (!empty($_POST['save'])) {
        $v_username = escapeshellarg($_POST['v_username']);

        // Change password
        if (($v_password != $_POST['v_password']) && (empty($_SESSION['error_msg']))) {
            $v_password = escapeshellarg($_POST['v_password']);
            exec (VESTA_CMD."v_change_user_password ".$v_username." ".$v_password, $output, $return_var);
            if ($return_var != 0) {
                $error = implode('<br>', $output);
                if (empty($error)) $error = 'Error: vesta did not return any output.';
                $_SESSION['error_msg'] = $error;
            }
            $v_password = "••••••••";
            unset($output);
        }

        // Change package
        if (($v_package != $_POST['v_package']) && (empty($_SESSION['error_msg']))) {
            $v_package = escapeshellarg($_POST['v_package']);
            exec (VESTA_CMD."v_change_user_package ".$v_username." ".$v_package, $output, $return_var);
            if ($return_var != 0) {
                $error = implode('<br>', $output);
                if (empty($error)) $error = 'Error: vesta did not return any output.';
                $_SESSION['error_msg'] = $error;
            }
            unset($output);
        }

        // Change shell
        if (($v_shell != $_POST['v_shell']) && (empty($_SESSION['error_msg']))) {
            $v_shell = escapeshellarg($_POST['v_shell']);
            exec (VESTA_CMD."v_change_user_shell ".$v_username." ".$v_shell, $output, $return_var);
            if ($return_var != 0) {
                $error = implode('<br>', $output);
                if (empty($error)) $error = 'Error: vesta did not return any output.';
                $_SESSION['error_msg'] = $error;
            }
            unset($output);
        }

        // Change contact email
        if (($v_email != $_POST['v_email']) && (empty($_SESSION['error_msg']))) {
            $v_email = escapeshellarg($_POST['v_email']);
            exec (VESTA_CMD."v_change_user_contact ".$v_username." ".$v_email, $output, $return_var);
            if ($return_var != 0) {
                $error = implode('<br>', $output);
                if (empty($error)) $error = 'Error: vesta did not return any output.';
                $_SESSION['error_msg'] = $error;
            }
            unset($output);
        }

        // Change Name
        if (($v_fname != $_POST['v_fname']) || ($v_lname != $_POST['v_lname']) && (empty($_SESSION['error_msg']))) {
            $v_fname = escapeshellarg($_POST['v_fname']);
            $v_lname = escapeshellarg($_POST['v_lname']);
            exec (VESTA_CMD."v_change_user_name ".$v_username." ".$v_fname." ".$v_lname, $output, $return_var);
            if ($return_var != 0) {
                $error = implode('<br>', $output);
                if (empty($error)) $error = 'Error: vesta did not return any output.';
                $_SESSION['error_msg'] = $error;
            }
            unset($output);
        }

        // Change NameServers
        if (($v_ns1 != $_POST['v_ns1']) || ($v_ns2 != $_POST['v_ns2']) || ($v_ns3 != $_POST['v_ns3']) || ($v_ns4 != $_POST['v_ns4']) && (empty($_SESSION['error_msg']))) {
            $v_ns1 = escapeshellarg($_POST['v_ns1']);
            $v_ns2 = escapeshellarg($_POST['v_ns2']);
            $v_ns3 = escapeshellarg($_POST['v_ns3']);
            $v_ns4 = escapeshellarg($_POST['v_ns4']);
            $ns_cmd = VESTA_CMD."v_change_user_ns ".$v_username." ".$v_ns1." ".$v_ns2;
            if (!empty($_POST['v_ns3'])) $ns_cmd = $ns_cmd." ".$v_ns3;
            if (!empty($_POST['v_ns4'])) $ns_cmd = $ns_cmd." ".$v_ns4;
            exec ($ns_cmd, $output, $return_var);
            if ($return_var != 0) {
                $error = implode('<br>', $output);
                if (empty($error)) $error = 'Error: vesta did not return any output.';
                $_SESSION['error_msg'] = $error;
            }
            unset($output);
        }

        if (empty($_SESSION['error_msg'])) {
            $_SESSION['ok_msg'] = "OK: changes has been saved.";
        }
    }
    include($_SERVER['DOCUMENT_ROOT'].'/templates/admin/menu_edit_user.html');
    include($_SERVER['DOCUMENT_ROOT'].'/templates/admin/edit_user.html');
    unset($_SESSION['error_msg']);
    unset($_SESSION['ok_msg']);
} else {
    // Check user argument?
    if (empty($_GET['user'])) {
        header("Location: /list/user/");
        exit;
    }

    // Check user
    $v_username = escapeshellarg($_GET['user']);
    exec (VESTA_CMD."v_list_user ".$v_username." json", $output, $return_var);
    if ($return_var != 0) {
        $error = implode('<br>', $output);
        if (empty($error)) $error = 'Error: vesta did not return any output.';
        $_SESSION['error_msg'] = $error;
    } else {
        $data = json_decode(implode('', $output), true);
        unset($output);
        $v_username = $_GET['user'];
        $v_password = "••••••••";
        $v_email = $data[$v_username]['CONTACT'];
        $v_fname = $data[$v_username]['FNAME'];
        $v_lname = $data[$v_username]['LNAME'];
        $v_ns = $data[$v_username]['NS'];
        $nameservers = explode(", ", $v_ns);
        $v_ns1 = $nameservers[0];
        $v_ns2 = $nameservers[1];
        $v_ns3 = $nameservers[2];
        $v_ns4 = $nameservers[3];
        $v_suspended = $data[$v_username]['SUSPENDED'];
        if ( $v_suspended == 'yes' ) {
            $v_status =  'suspended';
        } else {
            $v_status =  'active';
        }
        $v_time = $data[$v_username]['TIME'];
        $v_date = $data[$v_username]['DATE'];

    }

    // Action
    if (!empty($_POST['save'])) {
        $v_username = escapeshellarg($_POST['v_username']);

        // Change password
        if (($v_password != $_POST['v_password']) && (empty($_SESSION['error_msg']))) {
            $v_password = escapeshellarg($_POST['v_password']);
            exec (VESTA_CMD."v_change_user_password ".$v_username." ".$v_password, $output, $return_var);
            if ($return_var != 0) {
                $error = implode('<br>', $output);
                if (empty($error)) $error = 'Error: vesta did not return any output.';
                $_SESSION['error_msg'] = $error;
            }
            $v_password = "••••••••";
            unset($output);
        }

        // Change contact email
        if (($v_email != $_POST['v_email']) && (empty($_SESSION['error_msg']))) {
            $v_email = escapeshellarg($_POST['v_email']);
            exec (VESTA_CMD."v_change_user_contact ".$v_username." ".$v_email, $output, $return_var);
            if ($return_var != 0) {
                $error = implode('<br>', $output);
                if (empty($error)) $error = 'Error: vesta did not return any output.';
                $_SESSION['error_msg'] = $error;
            }
            unset($output);
        }

        // Change Name
        if (($v_fname != $_POST['v_fname']) || ($v_lname != $_POST['v_lname']) && (empty($_SESSION['error_msg']))) {
            $v_fname = escapeshellarg($_POST['v_fname']);
            $v_lname = escapeshellarg($_POST['v_lname']);
            exec (VESTA_CMD."v_change_user_name ".$v_username." ".$v_fname." ".$v_lname, $output, $return_var);
            if ($return_var != 0) {
                $error = implode('<br>', $output);
                if (empty($error)) $error = 'Error: vesta did not return any output.';
                $_SESSION['error_msg'] = $error;
            }
            unset($output);
        }

        // Change NameServers
        if (($v_ns1 != $_POST['v_ns1']) || ($v_ns2 != $_POST['v_ns2']) || ($v_ns3 != $_POST['v_ns3']) || ($v_ns4 != $_POST['v_ns4']) && (empty($_SESSION['error_msg']))) {
            $v_ns1 = escapeshellarg($_POST['v_ns1']);
            $v_ns2 = escapeshellarg($_POST['v_ns2']);
            $v_ns3 = escapeshellarg($_POST['v_ns3']);
            $v_ns4 = escapeshellarg($_POST['v_ns4']);
            $ns_cmd = VESTA_CMD."v_change_user_ns ".$v_username." ".$v_ns1." ".$v_ns2;
            if (!empty($_POST['v_ns3'])) $ns_cmd = $ns_cmd." ".$v_ns3;
            if (!empty($_POST['v_ns4'])) $ns_cmd = $ns_cmd." ".$v_ns4;
            exec ($ns_cmd, $output, $return_var);
            if ($return_var != 0) {
                $error = implode('<br>', $output);
                if (empty($error)) $error = 'Error: vesta did not return any output.';
                $_SESSION['error_msg'] = $error;
            }
            unset($output);
        }

        if (empty($_SESSION['error_msg'])) {
            $_SESSION['ok_msg'] = "OK: changes has been saved.";
        }
    }
    include($_SERVER['DOCUMENT_ROOT'].'/templates/user/menu_edit_user.html');
    include($_SERVER['DOCUMENT_ROOT'].'/templates/user/edit_user.html');
    unset($_SESSION['error_msg']);
    unset($_SESSION['ok_msg']);
}

// Footer
include($_SERVER['DOCUMENT_ROOT'].'/templates/footer.html');
