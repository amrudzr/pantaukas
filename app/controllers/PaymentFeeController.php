<?php
require_once APP_PATH . 'models/PaymentFeeModel.php';
require_once APP_PATH . 'models/MemberModel.php';
require_once APP_PATH . 'models/TypeFeeModel.php';

class PaymentFeeController
{
    private $model;
    private $memberModel;
    private $typeFeeModel;

    public function __construct()
    {
        $this->model = new PaymentFeeModel();
        $this->memberModel = new MemberModel();
        $this->typeFeeModel = new TypeFeeModel();
    }

    /** GET /fee/types/{id}/details */
    public function details($feeTypeId)
    {
        $selectedMonth = $_GET['bulan'] ?? date('m');
        $selectedYear = $_GET['tahun'] ?? date('Y');

        // Dapatkan data detail iuran
        $details = $this->model->getFeeDetails($feeTypeId, $selectedMonth, $selectedYear);
        if (!$details) {
            $_SESSION['flash'] = ['danger', 'Jenis iuran tidak ditemukan.'];
            redirect('/fee/types');
        }

        // Dapatkan ringkasan
        $summary = $this->model->getSummary($feeTypeId, $selectedMonth, $selectedYear);

        view('type_fee/payment_fee/details', [
            'details' => $details,
            'summary' => $summary,
            'selectedMonth' => $selectedMonth,
            'selectedYear' => $selectedYear,
            'pageTitle' => 'Detail Iuran: ' . $details['fee_type']['name'],
            'breadcrumbs' => [
                ['label' => 'Jenis Iuran', 'url' => '/fee/types'],
                ['label' => 'Detail Iuran', 'url' => "/fee/types/{$feeTypeId}/details"]
            ],
        ]);
    }

    /** POST /fee/types/{id}/details/update */
    public function updatePayment($feeTypeId)
    {
        try {
            // $paymentId = $_POST['payment_id'] ?? null;
            // $memberId = $_POST['member_id'];
            // $feeTypeId = $_POST['fee_type_id'];
            // $nominal = (int)$_POST['nominal'];
            // $notes = trim($_POST['notes'] ?? '');
            // $month = $_POST['month'];
            // $year = $_POST['year'];
            // $paymentDate = date('Y-m-d H:i:s');

            $paymentId = $_POST['payment_id'] ?? null;
            $memberId = $_POST['member_id'];
            $nominal = (int)$_POST['nominal'];
            $notes = trim($_POST['notes'] ?? '');
            $month = $_POST['month'];
            $year = $_POST['year'];
            $paymentDate = date('Y-m-d H:i:s');

            // Dapatkan target nominal dari jenis iuran
            $feeType = $this->typeFeeModel->find($feeTypeId);
            $targetNominal = $feeType['nominal'];

            // Tentukan status berdasarkan nominal yang dibayarkan
            $status = 'paid';
            if ($nominal <= 0) {
                $status = 'unpaid';
            } elseif ($nominal < $targetNominal) {
                $status = 'debt';
            }

            if ($paymentId) {
                // Update pembayaran yang sudah ada
                $this->model->update($paymentId, [
                    'nominal' => $nominal,
                    'status' => $status,
                    'notes' => $notes,
                    'payment_date' => $paymentDate
                ]);
            } else {
                // Buat pembayaran baru
                $this->model->create([
                    'id_fee_type' => $feeTypeId,
                    'id_member' => $memberId,
                    'nominal' => $nominal,
                    'status' => $status,
                    'notes' => $notes,
                    'payment_date' => $paymentDate
                ]);
            }

            $_SESSION['flash'] = ['success', 'Pembayaran berhasil diperbarui.'];
        } catch (Exception $e) {
            $_SESSION['flash'] = ['danger', $e->getMessage()];
        }

        redirect("/fee/types/{$feeTypeId}/details?bulan={$month}&tahun={$year}");
    }

    /** POST /fee/types/{id}/details/bulk-update */
    // public function bulkUpdatePayments($feeTypeId)
    // {
    //     // Debugging - tampilkan di console browser
    //     echo '<script>';
    //     echo 'console.log(' . json_encode([
    //         'post_data' => $_POST,
    //         'fee_type_id' => $feeTypeId
    //     ]) . ')';
    //     echo '</script>';
    //     try {
    //         // Log semua input POST
    //         error_log("[BulkUpdate] Received POST data: " . print_r($_POST, true));

    //         $memberIds = isset($_POST['member_ids']) ? explode(',', $_POST['member_ids']) : [];
    //         $month = $_POST['month'] ?? null;
    //         $year = $_POST['year'] ?? null;
    //         $paymentDate = date('Y-m-d H:i:s');

    //         error_log("[BulkUpdate] Parsed params - feeTypeId: $feeTypeId, month: $month, year: $year, memberIds: " . implode(',', $memberIds));

    //         // Validasi input
    //         if (empty($feeTypeId)) {
    //             throw new Exception('Jenis iuran tidak valid');
    //         }

    //         if (empty($memberIds)) {
    //             throw new Exception('Tidak ada anggota terpilih');
    //         }

    //         if (empty($month) || empty($year)) {
    //             throw new Exception('Bulan atau tahun tidak valid');
    //         }

    //         // Panggil model dengan logging
    //         error_log("[BulkUpdate] Calling model->bulkUpdateStatus()");
    //         $success = $this->model->bulkUpdateStatus(
    //             $memberIds,
    //             $feeTypeId,
    //             $month,
    //             $year,
    //             'paid',
    //             $paymentDate
    //         );

    //         if (!$success) {
    //             throw new Exception('Gagal memperbarui status pembayaran di database');
    //         }

    //         $_SESSION['flash'] = ['success', 'Status pembayaran berhasil diperbarui untuk anggota terpilih.'];
    //         error_log("[BulkUpdate] Successfully updated payments");
    //     } catch (Exception $e) {
    //         error_log("[BulkUpdate] ERROR: " . $e->getMessage());
    //         $_SESSION['flash'] = ['danger', $e->getMessage()];
    //     }

    //     redirect("/fee/types/{$feeTypeId}/details?bulan={$month}&tahun={$year}");
    // }
    public function bulkUpdatePayments($feeTypeId)
    {
        try {
            error_log("=== BULK UPDATE START ===");
            error_log("POST Data: " . print_r($_POST, true));

            $memberIds = explode(',', $_POST['member_ids']);
            error_log("Member IDs: " . print_r($memberIds, true));

            $result = $this->model->bulkUpdateStatus(
                $memberIds,
                $feeTypeId,
                $_POST['month'],
                $_POST['year'],
                'paid',
                date('Y-m-d H:i:s')
            );

            error_log("Update Result: " . ($result ? 'SUCCESS' : 'FAILED'));
            $_SESSION['flash'] = ['success', 'Update berhasil!'];
        } catch (Exception $e) {
            error_log("ERROR: " . $e->getMessage());
            $_SESSION['flash'] = ['danger', $e->getMessage()];
        }

        redirect("/fee/types/{$feeTypeId}/details?bulan={$_POST['month']}&tahun={$_POST['year']}");
    }
}
