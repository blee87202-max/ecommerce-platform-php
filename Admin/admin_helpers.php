<?php

if (!function_exists('stmt_get_one')) {
    function stmt_get_one($stmt)
    {
        $result = $stmt->get_result();
        if ($result) {
            $row = $result->fetch_assoc();
            $result->free();
            return $row;
        }

        if ($stmt->field_count > 0) {
            $meta = $stmt->result_metadata();
            $fields = array();
            $data = array();
            $row = array();

            while ($field = $meta->fetch_field()) {
                $fields[] = &$row[$field->name];
            }

            call_user_func_array(array($stmt, 'bind_result'), $fields);

            if ($stmt->fetch()) {
                foreach ($row as $key => $val) {
                    $data[$key] = $val;
                }
                $stmt->free_result();
                return $data;
            }
            $stmt->free_result();
        }
        return null;
    }
}

if (!function_exists('stmt_get_all')) {
    function stmt_get_all($stmt)
    {
        $result = $stmt->get_result();
        if ($result) {
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            return $rows;
        }

        if ($stmt->field_count > 0) {
            $meta = $stmt->result_metadata();
            $fields = array();
            $data = array();
            $rows = array();

            while ($field = $meta->fetch_field()) {
                $fields[] = &$data[$field->name];
            }

            call_user_func_array(array($stmt, 'bind_result'), $fields);

            while ($stmt->fetch()) {
                $row = array();
                foreach ($data as $key => $val) {
                    $row[$key] = $val;
                }
                $rows[] = $row;
            }
            $stmt->free_result();
            return $rows;
        }
        return array();
    }
}

if (!function_exists('stmt_execute_simple')) {
    function stmt_execute_simple($conn, $sql, $types, $params)
    {
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            return false;
        }
        call_user_func_array(array($stmt, 'bind_param'), array_merge(array($types), $params));
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }
}

if (!function_exists('val')) {
    function val($arr, $key, $default = 0)
    {
        return isset($arr[$key]) ? $arr[$key] : $default;
    }
}

if (!function_exists('esc')) {
    function esc($s)
    {
        return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
    }
}