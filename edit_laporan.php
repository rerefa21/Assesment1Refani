<?php
session_start();
include 'koneksi.php';

$id = $_GET['id'];
$data = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM laporan WHERE id='$id'"));

if(isset($_POST['update'])) {
    $lokasi = $_POST['lokasi'];
    $waktu = $_POST['waktu_laporan'];
    $jumlah = $_POST['jumlah_kendaraan'];
    $deskripsi = $_POST['deskripsi'];

    if($jumlah >= 1 && $jumlah <= 5) {
        $status = "Ringan";
    } elseif($jumlah >= 6 && $jumlah <= 15) {
        $status = "Sedang";
    } else {
        $status = "Berat";
    }
    
    $foto = $_FILES['foto_bukti']['name'];
    if($foto != '') {
        unlink('uploads/' . $data['foto_bukti']);

        move_uploaded_file($_FILES['foto_bukti']['tmp_name'], 'uploads/' . $foto);
    } else {
        $foto = $data['foto_bukti'];
    }

    mysqli_query($koneksi, "UPDATE laporan SET
        lokasi='$lokasi',
        waktu_laporan='$waktu',
        jumlah_kendaraan='$jumlah',
        status_pelanggaran='$status',
        deskripsi='$deskripsi',
        foto_bukti='$foto'
        WHERE id='$id'");

    header("Location: laporan.php");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Laporan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container mt-5">
    <h2>Edit Laporan</h2>

    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="lokasi" value="<?php echo $data['lokasi']; ?>" class="form-control mb-3">

        <input type="datetime-local" name="waktu_laporan" class="form-control mb-3">

        <input type="number" name="jumlah_kendaraan" value="<?php echo $data['jumlah_kendaraan']; ?>" class="form-control mb-3">

        <textarea name="deskripsi" class="form-control mb-3"><?php echo $data['deskripsi']; ?></textarea>

        <input type="file" name="foto_bukti" class="form-control mb-3">

        <button type="submit" name="update" class="btn btn-success">Update</button>
    </form>
</div>

<?php include 'footer.php'; ?>

</body>
</html>