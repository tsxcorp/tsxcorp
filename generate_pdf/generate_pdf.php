<?php
// Chỉ định đường dẫn đến thư mục Dompdf trong dự án của bạn
require_once 'dompdf/autoload.inc.php';

use Dompdf\Dompdf;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['name']) && isset($data['company']) && isset($data['qrCodeUrl'])) {
        // Lấy dữ liệu từ yêu cầu POST
        $name = htmlspecialchars($data['name']);
        $company = htmlspecialchars($data['company']);
        $qrCodeUrl = htmlspecialchars($data['qrCodeUrl']);

        // Tạo nội dung HTML cho PDF
        $htmlContent = "
            <html>
                <head>
                    <style>
                        .badge {
                            width: 148mm;
                            height: 210mm;
                            padding: 20px;
                            box-sizing: border-box;
                            font-family: Arial, sans-serif;
                            display: flex;
                            flex-direction: column;
                            justify-content: center;
                            align-items: center;
                            text-align: center;
                        }
                        .name {
                            font-size: 38px;
                            font-weight: bold;
                            margin-bottom: 20px;
                            text-transform: uppercase;
                        }
                        .company {
                            font-size: 16px;
                            margin-bottom: 30px;
                            text-transform: uppercase;
                        }
                        .qr-code {
                            width: 150px;
                            height: 150px;
                        }
                    </style>
                </head>
                <body>
                    <div class='badge'>
                        <div class='name'>$name</div>
                        <div class='company'>$company</div>
                        <img src='$qrCodeUrl' class='qr-code' alt='QR Code'>
                    </div>
                </body>
            </html>
        ";

        // Tạo file PDF từ HTML
        $dompdf = new Dompdf();
        $dompdf->loadHtml($htmlContent);
        $dompdf->setPaper('A5', 'portrait');
        $dompdf->render();

        // Lấy nội dung PDF và mã hóa thành Base64
        $pdfOutput = $dompdf->output();
        $base64 = base64_encode($pdfOutput);

        // Trả về Base64 trong response
        header('Content-Type: application/json');
        echo json_encode(['base64' => $base64]);
    } else {
        echo json_encode(['error' => 'Invalid input data']);
    }
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
?>
