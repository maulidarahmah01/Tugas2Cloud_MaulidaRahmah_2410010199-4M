<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

include 'koneksi.php';

$method = $_SERVER['REQUEST_METHOD'];

// Handle preflight requests
if ($method === 'OPTIONS') {
    http_response_code(200);
    exit();
}

switch ($method) {
    case 'GET':
        // Cek apakah request untuk kategori
        if (isset($_GET['action']) && $_GET['action'] === 'categories') {
            $query = "SELECT * FROM kategori ORDER BY nama_kategori ASC";
            $result = mysqli_query($conn, $query);
            $categories = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $categories[] = $row;
            }
            echo json_encode([
                "success" => true,
                "data" => $categories
            ]);
            break;
        }
        
        // Ambil data kuliner dengan JOIN ke tabel kategori
        $search = isset($_GET['search']) ? mysqli_real_escape_string($conn, trim($_GET['search'])) : '';
        $kategori_id = isset($_GET['kategori_id']) ? intval($_GET['kategori_id']) : 0;
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 100;
        $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
        
        $query = "SELECT k.*, kat.nama_kategori 
                  FROM kuliner k 
                  LEFT JOIN kategori kat ON k.kategori_id = kat.id 
                  WHERE 1=1";
        
        if ($search) {
            $query .= " AND (k.nama LIKE '%$search%' OR k.lokasi LIKE '%$search%' OR k.deskripsi LIKE '%$search%' OR kat.nama_kategori LIKE '%$search%')";
        }
        
        if ($kategori_id > 0) {
            $query .= " AND k.kategori_id = $kategori_id";
        }
        
        $query .= " ORDER BY k.id DESC LIMIT $limit OFFSET $offset";
        
        $result = mysqli_query($conn, $query);
        
        if (!$result) {
            echo json_encode(["success" => false, "error" => "Query gagal: " . mysqli_error($conn)]);
            break;
        }
        
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        
        // Hitung total data untuk pagination
        $countQuery = "SELECT COUNT(*) as total FROM kuliner k 
                       LEFT JOIN kategori kat ON k.kategori_id = kat.id 
                       WHERE 1=1";
        if ($search) {
            $countQuery .= " AND (k.nama LIKE '%$search%' OR k.lokasi LIKE '%$search%' OR k.deskripsi LIKE '%$search%' OR kat.nama_kategori LIKE '%$search%')";
        }
        if ($kategori_id > 0) {
            $countQuery .= " AND k.kategori_id = $kategori_id";
        }
        
        $countResult = mysqli_query($conn, $countQuery);
        $total = mysqli_fetch_assoc($countResult)['total'];
        
        echo json_encode([
            "success" => true,
            "data" => $data,
            "total" => intval($total),
            "limit" => $limit,
            "offset" => $offset
        ]);
        break;

    case 'POST':
        // Tambah data kuliner baru
        $input = json_decode(file_get_contents("php://input"), true);
        
        if (!$input || !isset($input['nama']) || !isset($input['kategori_id'])) {
            echo json_encode(["success" => false, "error" => "Data tidak lengkap"]);
            break;
        }
        
        $nama = mysqli_real_escape_string($conn, trim($input['nama']));
        $lokasi = mysqli_real_escape_string($conn, trim($input['lokasi']));
        $kategori_id = intval($input['kategori_id']);
        $deskripsi = mysqli_real_escape_string($conn, trim($input['deskripsi']));
        $gambar = mysqli_real_escape_string($conn, trim($input['gambar']));
        $rating = max(0, min(5, floatval($input['rating']))); // Validasi rating 0-5

        $sql = "INSERT INTO kuliner (nama, lokasi, kategori_id, deskripsi, gambar, rating)
                VALUES ('$nama', '$lokasi', $kategori_id, '$deskripsi', '$gambar', $rating)";
        
        if (mysqli_query($conn, $sql)) {
            echo json_encode([
                "success" => true,
                "message" => "Data berhasil ditambahkan",
                "id" => mysqli_insert_id($conn)
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "error" => "Gagal menambahkan data: " . mysqli_error($conn)
            ]);
        }
        break;

    case 'PUT':
        // Update data kuliner
        $input = json_decode(file_get_contents("php://input"), true);
        
        if (!$input || !isset($input['id'])) {
            echo json_encode(["success" => false, "error" => "ID tidak ditemukan"]);
            break;
        }
        
        $id = intval($input['id']);
        $nama = mysqli_real_escape_string($conn, trim($input['nama']));
        $lokasi = mysqli_real_escape_string($conn, trim($input['lokasi']));
        $kategori_id = intval($input['kategori_id']);
        $deskripsi = mysqli_real_escape_string($conn, trim($input['deskripsi']));
        $gambar = mysqli_real_escape_string($conn, trim($input['gambar']));
        $rating = max(0, min(5, floatval($input['rating']))); // Validasi rating 0-5

        $sql = "UPDATE kuliner SET 
                nama='$nama', lokasi='$lokasi', kategori_id=$kategori_id,
                deskripsi='$deskripsi', gambar='$gambar', rating=$rating
                WHERE id=$id";
        
        if (mysqli_query($conn, $sql)) {
            echo json_encode([
                "success" => true,
                "message" => "Data berhasil diperbarui"
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "error" => "Gagal memperbarui data: " . mysqli_error($conn)
            ]);
        }
        break;

    case 'DELETE':
        // Hapus data
        parse_str(file_get_contents("php://input"), $input);
        
        if (!isset($input['id'])) {
            echo json_encode(["success" => false, "error" => "ID tidak ditemukan"]);
            break;
        }
        
        $id = intval($input['id']);
        $sql = "DELETE FROM kuliner WHERE id=$id";
        
        if (mysqli_query($conn, $sql)) {
            echo json_encode([
                "success" => true,
                "message" => "Data berhasil dihapus"
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "error" => "Gagal menghapus data: " . mysqli_error($conn)
            ]);
        }
        break;

    default:
        echo json_encode([
            "success" => false,
            "error" => "Metode HTTP tidak didukung"
        ]);
        break;
}

mysqli_close($conn);
?>
