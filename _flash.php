<?php
function flash($key, $val = null) {
    if (!isset($_SESSION)) session_start();
    if ($val === null) {
        if (!empty($_SESSION['flash'][$key])) {
            $msg = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            return $msg;
        }
        return null;
    } else {
        $_SESSION['flash'][$key] = $val;
    }
}
?>
