<?php
function get_all_subscription_plans($type = null, $white_label_id = null) {
    $db = get_db_connection();
    $sql = "SELECT * FROM subscription_plans WHERE 1=1";
    $params = [];

    if ($type) {
        $sql .= " AND type = :type";
        $params[':type'] = $type;
    }

    if ($white_label_id === 'GLOBAL') {
        // Explicitly fetch global plans (NULL)
        $sql .= " AND white_label_id IS NULL";
    } elseif ($white_label_id) {
        // Fetch specific WL plans
        $sql .= " AND white_label_id = :wl_id";
        $params[':wl_id'] = $white_label_id;
    }

    $sql .= " ORDER BY created_at DESC";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll();
}

function get_subscription_plan_by_id($id) {
    $db = get_db_connection();

    // Fetch Plan
    $sql = "SELECT * FROM subscription_plans WHERE id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $id);
    $stmt->execute();
    $plan = $stmt->fetch();

    if (!$plan) return null;

    // Fetch Linked Services
    $sql = "SELECT service_id FROM subscription_plan_services WHERE plan_id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $id);
    $stmt->execute();
    $plan['services'] = $stmt->fetchAll(PDO::FETCH_COLUMN);

    return $plan;
}

function create_subscription_plan($data) {
    $db = get_db_connection();

    try {
        $db->beginTransaction();

        // 1. Insert Plan
        $sql = "INSERT INTO subscription_plans (id, white_label_id, name, type, price, gst_rate, description, footer_description, status)
                VALUES (:id, :white_label_id, :name, :type, :price, :gst_rate, :description, :footer_description, :status)";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $data['id']);
        $stmt->bindValue(':white_label_id', $data['white_label_id'] ?? null);
        $stmt->bindValue(':name', $data['name']);
        $stmt->bindValue(':type', $data['type'] ?? 'PARTNER');
        $stmt->bindValue(':price', $data['price']);
        $stmt->bindValue(':gst_rate', $data['gst_rate']);
        $stmt->bindValue(':description', $data['description']);
        $stmt->bindValue(':footer_description', $data['footer_description']);
        $stmt->bindValue(':status', $data['status']);
        $stmt->execute();

        // 2. Insert Services
        if (!empty($data['services'])) {
            $sql = "INSERT INTO subscription_plan_services (plan_id, service_id) VALUES (:plan_id, :service_id)";
            $stmt = $db->prepare($sql);

            foreach ($data['services'] as $service_id) {
                $stmt->bindValue(':plan_id', $data['id']);
                $stmt->bindValue(':service_id', $service_id);
                $stmt->execute();
            }
        }

        $db->commit();
        return true;
    } catch (Exception $e) {
        $db->rollBack();
        error_log($e->getMessage());
        return false;
    }
}

function update_subscription_plan($data) {
    $db = get_db_connection();

    try {
        $db->beginTransaction();

        // 1. Update Plan
        $sql = "UPDATE subscription_plans SET
                name = :name,
                type = :type,
                price = :price,
                gst_rate = :gst_rate,
                description = :description,
                footer_description = :footer_description,
                status = :status
                WHERE id = :id";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $data['id']);
        $stmt->bindValue(':name', $data['name']);
        $stmt->bindValue(':type', $data['type'] ?? 'PARTNER');
        $stmt->bindValue(':price', $data['price']);
        $stmt->bindValue(':gst_rate', $data['gst_rate']);
        $stmt->bindValue(':description', $data['description']);
        $stmt->bindValue(':footer_description', $data['footer_description']);
        $stmt->bindValue(':status', $data['status']);
        $stmt->execute();

        // 2. Update Services (Delete all and re-insert)
        $db->exec("DELETE FROM subscription_plan_services WHERE plan_id = '" . $data['id'] . "'");

        if (!empty($data['services'])) {
            $sql = "INSERT INTO subscription_plan_services (plan_id, service_id) VALUES (:plan_id, :service_id)";
            $stmt = $db->prepare($sql);

            foreach ($data['services'] as $service_id) {
                $stmt->bindValue(':plan_id', $data['id']);
                $stmt->bindValue(':service_id', $service_id);
                $stmt->execute();
            }
        }

        $db->commit();
        return true;
    } catch (Exception $e) {
        $db->rollBack();
        error_log($e->getMessage());
        return false;
    }
}

function delete_subscription_plan($id) {
    $db = get_db_connection();

    try {
        $db->beginTransaction();

        // Delete linked services
        $db->exec("DELETE FROM subscription_plan_services WHERE plan_id = '$id'");

        // Delete plan
        $sql = "DELETE FROM subscription_plans WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();

        $db->commit();
        return true;
    } catch (Exception $e) {
        $db->rollBack();
        return false;
    }
}
