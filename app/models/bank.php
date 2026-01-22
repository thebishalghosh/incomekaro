<?php
function get_all_banks($page = 1, $limit = 10, $search = '') {
    $db = get_db_connection();
    $offset = ($page - 1) * $limit;

    $sql = "SELECT * FROM banks WHERE 1=1";
    $params = [];

    if (!empty($search)) {
        $sql .= " AND name LIKE :search";
        $params[':search'] = "%$search%";
    }

    $sql .= " ORDER BY name ASC LIMIT :limit OFFSET :offset";

    $stmt = $db->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function get_total_banks_count($search = '') {
    $db = get_db_connection();
    $sql = "SELECT COUNT(*) FROM banks WHERE 1=1";
    $params = [];

    if (!empty($search)) {
        $sql .= " AND name LIKE :search";
        $params[':search'] = "%$search%";
    }

    $stmt = $db->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    return $stmt->fetchColumn();
}

function get_banks_by_pincode($pincode) {
    $db = get_db_connection();
    $sql = "SELECT b.name
            FROM banks b
            JOIN bank_pincodes bp ON b.id = bp.bank_id
            WHERE bp.pincode = :pincode";
    $stmt = $db->prepare($sql);
    $stmt->execute(['pincode' => $pincode]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function import_banks_from_csv($file_path) {
    $db = get_db_connection();

    // Enable line ending detection for Mac/Legacy CSVs
    ini_set('auto_detect_line_endings', true);

    if (($handle = fopen($file_path, "r")) !== FALSE) {
        try {
            $db->beginTransaction();

            // 1. Detect delimiter
            $first_line = fgets($handle);
            rewind($handle);
            $delimiter = ',';
            if ($first_line && substr_count($first_line, ';') > substr_count($first_line, ',')) {
                $delimiter = ';';
            }

            // 2. Skip Header
            $first_row = fgetcsv($handle, 0, $delimiter);
            if ($first_row) {
                // Remove BOM
                $first_cell = $first_row[0];
                if (substr($first_cell, 0, 3) == pack('CCC', 0xef, 0xbb, 0xbf)) {
                    $first_cell = substr($first_cell, 3);
                }

                $first_cell_lower = strtolower(trim($first_cell));
                $header_keywords = ['pin', 'code', 'zip', 'bank', 'name'];
                $is_header = false;

                foreach ($header_keywords as $keyword) {
                    if (strpos($first_cell_lower, $keyword) !== false) {
                        $is_header = true;
                        break;
                    }
                }

                if (!$is_header && isset($first_row[1])) {
                     $second_cell_lower = strtolower(trim($first_row[1]));
                     foreach ($header_keywords as $keyword) {
                        if (strpos($second_cell_lower, $keyword) !== false) {
                            $is_header = true;
                            break;
                        }
                    }
                }

                if (!$is_header) {
                    rewind($handle);
                }
            }

            // 3. Prepare Statements & Cache
            $bank_stmt = $db->prepare("INSERT IGNORE INTO banks (id, name) VALUES (:id, :name)");

            // Load existing banks to minimize DB hits
            $bank_cache = $db->query("SELECT name, id FROM banks")->fetchAll(PDO::FETCH_KEY_PAIR);

            $batch_size = 500;
            $pincode_buffer = [];
            $pincode_params = [];

            $row_count = 0;

            while (($data = fgetcsv($handle, 0, $delimiter)) !== FALSE) {
                $row_count++;
                if (count($data) < 2) continue;

                // Assume Col 1 = Pincode, Col 2 = Bank Name
                $pincode = preg_replace('/[^a-zA-Z0-9]/', '', trim($data[0]));
                $bank_name = mb_convert_encoding(trim($data[1]), 'UTF-8', 'UTF-8');
                $bank_name = preg_replace('/[^\p{L}\p{N}\s\-\.\(\)]/u', '', $bank_name);

                if (empty($bank_name) || empty($pincode)) continue;

                // Handle Bank (Single Insert + Cache)
                if (!isset($bank_cache[$bank_name])) {
                    $bank_id = uniqid('bnk-');
                    $bank_stmt->execute(['id' => $bank_id, 'name' => $bank_name]);
                    $bank_cache[$bank_name] = $bank_id;
                } else {
                    $bank_id = $bank_cache[$bank_name];
                }

                // Buffer Pincode for Batch Insert
                $pincode_buffer[] = "(?, ?, ?)";
                $pincode_params[] = uniqid('bp-');
                $pincode_params[] = $bank_id;
                $pincode_params[] = $pincode;

                // Flush Buffer if full
                if (count($pincode_buffer) >= $batch_size) {
                    flush_pincode_buffer($db, $pincode_buffer, $pincode_params);
                    $pincode_buffer = [];
                    $pincode_params = [];
                }
            }

            // Flush remaining buffer
            if (!empty($pincode_buffer)) {
                flush_pincode_buffer($db, $pincode_buffer, $pincode_params);
            }

            $db->commit();
            fclose($handle);
            return true;

        } catch (Exception $e) {
            $db->rollBack();
            fclose($handle);
            error_log("Bank Import Error: " . $e->getMessage());
            return false;
        }
    }
    return false;
}

function flush_pincode_buffer($db, $placeholders, $params) {
    $sql = "INSERT INTO bank_pincodes (id, bank_id, pincode) VALUES " . implode(', ', $placeholders);
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
}

function clear_bank_data() {
    $db = get_db_connection();
    try {
        $db->beginTransaction();
        $db->exec("DELETE FROM bank_pincodes");
        $db->exec("DELETE FROM banks");
        $db->commit();
        return true;
    } catch (Exception $e) {
        $db->rollBack();
        return false;
    }
}
