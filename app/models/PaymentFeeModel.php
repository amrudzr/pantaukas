<?php

class PaymentFeeModel
{
    private $db;

    public function __construct()
    {
        $this->db = getDbConnection();
    }

    public function create($data)
    {
        $stmt = $this->db->prepare("INSERT INTO payment_fee 
            (id_type_fee, id_member, payment_date, nominal, status, notes) 
            VALUES (?, ?, ?, ?, ?, ?)");

        $stmt->bind_param(
            "iisiss",
            $data['id_type_fee'],
            $data['id_member'],
            $data['payment_date'],
            $data['nominal'],
            $data['status'],
            $data['notes']
        );

        return $stmt->execute() ? $this->db->insert_id : false;
    }

    public function update($id, $data)
    {
        $stmt = $this->db->prepare("UPDATE payment_fee SET
            nominal = ?,
            status = ?,
            notes = ?,
            payment_date = ?,
            updated_at = NOW()
            WHERE id = ?");

        $stmt->bind_param(
            "isssi",
            $data['nominal'],
            $data['status'],
            $data['notes'],
            $data['payment_date'],
            $id
        );

        return $stmt->execute();
    }

    public function updateStatus($id, $status, $paymentDate)
    {
        $stmt = $this->db->prepare("UPDATE payment_fee SET
            status = ?,
            payment_date = ?,
            updated_at = NOW()
            WHERE id = ?");

        $stmt->bind_param("ssi", $status, $paymentDate, $id);
        return $stmt->execute();
    }

    public function bulkUpdateStatus($memberIds, $feeTypeId, $month, $year, $status, $paymentDate)
    {
        $userId = $_SESSION['user_id']; // Ambil user ID dari session

        $this->db->begin_transaction();
        try {
            foreach ($memberIds as $memberId) {
                // 1. Cek existing payment
                $stmt = $this->db->prepare("SELECT id FROM payment_fee 
                                      WHERE id_type_fee = ? 
                                      AND id_member = ? 
                                      AND MONTH(payment_date) = ?
                                      AND YEAR(payment_date) = ?");
                $stmt->bind_param("iiii", $feeTypeId, $memberId, $month, $year);
                $stmt->execute();
                $existing = $stmt->get_result()->fetch_assoc();

                if ($existing) {
                    // Update existing record
                    $updateStmt = $this->db->prepare("UPDATE payment_fee SET
                    status = ?,
                    payment_date = ?,
                    nominal = ?,
                    updated_at = NOW()
                    WHERE id = ?");

                    $feeType = $this->getFeeType($feeTypeId);
                    $updateStmt->bind_param(
                        "ssdi",
                        $status,
                        $paymentDate,
                        $feeType['nominal'],
                        $existing['id']
                    );
                    $updateStmt->execute();
                } else {
                    // Create new record
                    $feeType = $this->getFeeType($feeTypeId);
                    $insertStmt = $this->db->prepare("INSERT INTO payment_fee 
                    (id_type_fee, id_member, id_user, payment_date, nominal, status) 
                    VALUES (?, ?, ?, ?, ?, ?)");

                    $insertStmt->bind_param(
                        "iiisds",
                        $feeTypeId,
                        $memberId,
                        $userId, // Tambahkan user ID
                        $paymentDate,
                        $feeType['nominal'],
                        $status
                    );
                    $insertStmt->execute();
                }
            }
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Database Error: " . $e->getMessage() . "\n" . $this->db->error);
            return false;
        }
    }

    public function getFeeDetails($feeTypeId, $month, $year)
    {
        $userId = $_SESSION['user_id'];

        // Dapatkan detail jenis iuran
        $feeType = $this->getFeeType($feeTypeId);
        if (!$feeType) return null;

        // Dapatkan daftar anggota
        $members = $this->getMembers($userId);

        // Dapatkan pembayaran untuk periode ini
        $payments = $this->getPaymentsForPeriod($feeTypeId, $month, $year);

        // Gabungkan data
        $result = [];
        foreach ($members as $member) {
            $payment = $this->findPaymentForMember($payments, $member['id']);

            $result[] = [
                'member_id' => $member['id'],
                'member_name' => $member['name'],
                'member_phone' => $member['phone'],
                'member_status' => $member['status'],
                'fee_type_id' => $feeTypeId,
                'fee_type_name' => $feeType['name'],
                'target_nominal' => $feeType['nominal'],
                'payment_id' => $payment ? $payment['id'] : null,
                'payment_nominal' => $payment ? $payment['nominal'] : 0,
                'payment_status' => $payment ? $payment['status'] : 'unpaid',
                'payment_notes' => $payment ? $payment['notes'] : null,
                'payment_date' => $payment ? $payment['payment_date'] : null
            ];
        }

        return [
            'fee_type' => $feeType,
            'members' => $result,
            'month' => $month,
            'year' => $year
        ];
    }

    private function getFeeType($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM type_fee WHERE id = ? AND deleted_at IS NULL");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    private function getMembers($userId)
    {
        $stmt = $this->db->prepare("SELECT id, name, phone, status FROM member 
                                   WHERE id_user = ? AND deleted_at IS NULL AND status = 'active'");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    private function getPaymentsForPeriod($feeTypeId, $month, $year)
    {
        $stmt = $this->db->prepare("
            SELECT p.id, p.id_member, p.nominal, p.status, p.notes, p.payment_date 
            FROM payment_fee p
            WHERE p.id_type_fee = ?
            AND MONTH(p.payment_date) = ?
            AND YEAR(p.payment_date) = ?
            AND p.deleted_at IS NULL
        ");
        $stmt->bind_param("iii", $feeTypeId, $month, $year);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    private function findPaymentForMember($payments, $memberId)
    {
        foreach ($payments as $payment) {
            if ($payment['id_member'] == $memberId) {
                return $payment;
            }
        }
        return null;
    }

    public function getSummary($feeTypeId, $month, $year)
    {
        $userId = $_SESSION['user_id'];

        // Hitung total anggota aktif
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM member 
                                   WHERE id_user = ? AND deleted_at IS NULL AND status = 'active'");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $totalMembers = $stmt->get_result()->fetch_assoc()['total'];

        // Dapatkan detail jenis iuran
        $feeType = $this->getFeeType($feeTypeId);
        $targetNominal = $feeType ? $feeType['nominal'] : 0;
        $totalTarget = $totalMembers * $targetNominal;

        // Hitung pembayaran
        $stmt = $this->db->prepare("
            SELECT 
                SUM(CASE WHEN status = 'paid' THEN nominal ELSE 0 END) as total_paid,
                SUM(CASE WHEN status = 'unpaid' THEN 1 ELSE 0 END) as total_unpaid,
                SUM(CASE WHEN status = 'debt' THEN 1 ELSE 0 END) as total_debt
            FROM payment_fee
            WHERE id_type_fee = ?
            AND MONTH(payment_date) = ?
            AND YEAR(payment_date) = ?
            AND deleted_at IS NULL
        ");
        $stmt->bind_param("iii", $feeTypeId, $month, $year);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        return [
            'total_members' => $totalMembers,
            'target_nominal' => $targetNominal,
            'total_target' => $totalTarget,
            'total_paid' => $result['total_paid'] ?? 0,
            'total_unpaid' => $result['total_unpaid'] ?? $totalMembers,
            'total_debt' => $result['total_debt'] ?? 0
        ];
    }
}
