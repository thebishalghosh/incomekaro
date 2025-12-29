<?php
function get_all_subscription_plans() {
    $db = get_db_connection();
    $sql = "SELECT * FROM subscription_plans ORDER BY price ASC";
    $stmt = $db->query($sql);
    return $stmt->fetchAll();
}

function get_subscription_plan_by_id($id) {
    $db = get_db_connection();
    $sql = "SELECT * FROM subscription_plans WHERE id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $id);
    $stmt->execute();
    $plan = $stmt->fetch();

    if ($plan) {
        // Fetch linked services
        $sql = "SELECT service_id FROM subscription_plan_services WHERE plan_id = :plan_id";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':plan_id', $id);
        $stmt->execute();
        $plan['services'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    return $plan;
}

function create_subscription_plan($data) {
    $db = get_db_connection();

    try {
        $db->beginTransaction();

        $sql = "INSERT INTO subscription_plans (id, name, price, gst_rate, description, footer_description, status)
                VALUES (:id, :name, :price, :gst_rate, :description, :footer_description, :status)";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $data['id']);
        $stmt->bindValue(':name', $data['name']);
        $stmt->bindValue(':price', $data['price']);
        $stmt->bindValue(':gst_rate', $data['gst_rate']);
        $stmt->bindValue(':description', $data['description']);
        $stmt->bindValue(':footer_description', $data['footer_description']);
        $stmt->bindValue(':status', $data['status']);
        $stmt->execute();

        // Insert Services
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

        $sql = "UPDATE subscription_plans SET
                name = :name, price = :price, gst_rate = :gst_rate,
                description = :description, footer_description = :footer_description, status = :status
                WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $data['id']);
        $stmt->bindValue(':name', $data['name']);
        $stmt->bindValue(':price', $data['price']);
        $stmt->bindValue(':gst_rate', $data['gst_rate']);
        $stmt->bindValue(':description', $data['description']);
        $stmt->bindValue(':footer_description', $data['footer_description']);
        $stmt->bindValue(':status', $data['status']);
        $stmt->execute();

        // Update Services (Delete all and re-insert)
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
    // Services are deleted via CASCADE
    $sql = "DELETE FROM subscription_plans WHERE id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $id);
    return $stmt->execute();
}
