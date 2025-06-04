<?php
    // Cấu hình CORS để frontend Angular gọi được
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: *");
    header("Access-Control-Allow-Methods: POST");
    header('Content-Type: application/json');

    // Xử lý preflight
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit();
    }

    require_once "db_module.php";
    $link = null;
    taoKetNoi($link);

    // Khởi tạo response mặc định
    $response = ["success" => false, "message" => ""];

    try {
        // Kiểm tra dữ liệu truyền lên
        if (!isset($_POST['cards'])) {
            throw new Exception("Thiếu dữ liệu từ vựng!");
        }

        $cards = json_decode($_POST['cards'], true);
        if (!is_array($cards) || count($cards) === 0) {
            throw new Exception("Dữ liệu không hợp lệ!");
        }

        // Gán trực tiếp, đã kiểm tra ở phía frontend
        $title = mysqli_real_escape_string($link, $_POST['title']);
        $description = mysqli_real_escape_string($link, $_POST['description']);

        // Thêm danh sách từ vựng
        $sqlList = "INSERT INTO vocab_lists (title, description) VALUES ('$title', '$description')";
        if (!chayTruyVanKhongTraVeDL($link, $sqlList)) {
            throw new Exception("Lỗi khi tạo danh sách: " . mysqli_error($link));
        }
        $listId = mysqli_insert_id($link);

        // Thêm từng từ vựng
        foreach ($cards as $card) {
            $term = mysqli_real_escape_string($link, $card['term']);
            $definition = mysqli_real_escape_string($link, $card['definition']);

            $sql = "INSERT INTO words (vocab_list_id, term, definition) VALUES ($listId, '$term', '$definition')";
            if (!chayTruyVanKhongTraVeDL($link, $sql)) {
                throw new Exception("Lỗi khi thêm từ vựng: " . mysqli_error($link));
            }
        }

        $response['success'] = true;
        $response['message'] = "Đã lưu thành công!";
    } 
    catch (Exception $e) {
        $response['message'] = $e->getMessage();
    } 
    finally {
        if ($link) {
            giaiPhongBoNho($link);
        }
    }

    // Trả JSON về Angular
    echo json_encode($response);
?>
