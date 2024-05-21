<?php

session_start();

// Fungsi untuk logout
function logout()
{
  // Hapus semua data sesi
  $_SESSION = array();

  // Hapus cookie sesi jika ada
  if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
      session_name(),
      '',
      time() - 42000,
      $params["path"],
      $params["domain"],
      $params["secure"],
      $params["httponly"]
    );
  }

  // Hancurkan sesi
  session_destroy();

  // Redirect ke halaman login atau halaman lain yang sesuai
  header("Location:  ../../../form login/login.php");
  exit();
}

// Periksa apakah tombol logout ditekan
if (isset($_POST['logout'])) {
  logout();
}

// Lakukan koneksi ke database (gunakan informasi koneksi Anda)
$servername = "localhost";
$username = "root";
  $password = "";
$dbname = "datapernikahananak";

$conn = new mysqli($servername, $username, $password, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Cek apakah formulir dikirim (melalui metode POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Ambil nilai dari formulir
  $nikIstri = $_POST['nik_istri'];
  $nikSuami = $_POST['nik_suami'];
  $namaSuami = $_POST['nama_suami'];
  $namaIstri = $_POST['nama_istri'];
  $ttlSuami = $_POST['ttl_suami'];
  $ttlIstri = $_POST['ttl_istri'];
  $tanggalNikah = $_POST['tanggal_nikah'];
  $usiaSuami = $_POST['usia_suami'];
  $usiaIstri = $_POST['usia_istri'];
  $pendidikanSuami = $_POST['pendidikanterakhir_suami'];
  $pendidikanIstri = $_POST['pendidikanterakhir_istri'];
  $alamatPernikahan = $_POST['alamat_nikah'];
  $saksiNikah = isset($_POST['saksi_nikah']) ? $_POST['saksi_nikah'] : '';
  $faktorPernikahan = isset($_POST['faktor_pernikahan']) ? $_POST['faktor_pernikahan'] : '';
  $tanggalInput = date('Y-m-d'); // Tanggal penginputan
  $kabkota_kua = $_POST['kabkota_kua'];


  // Query SQL untuk menyimpan data ke database
  $query = "INSERT INTO datapernikahan (nik_istri, nik_suami, nama_suami, nama_istri, ttl_suami, ttl_istri, tanggal_nikah, usia_suami, usia_istri, pendidikanterakhir_suami, pendidikanterakhir_istri, alamat_nikah, saksi_nikah, faktor_pernikahan, tanggal_penginputan, kabkota_kua) VALUES ('$nikIstri', '$nikSuami', '$namaSuami', '$namaIstri', '$ttlSuami', '$ttlIstri', '$tanggalNikah', '$usiaSuami', '$usiaIstri', '$pendidikanSuami', '$pendidikanIstri', '$alamatPernikahan', '$saksiNikah', '$faktorPernikahan', '$tanggalInput', '$kabkota_kua')";


  $query_check_suaminik = "SELECT * FROM datapernikahan WHERE nik_suami = '$nikSuami'";
  $query_check_istrinik = "SELECT * FROM datapernikahan WHERE nik_istri = '$nikIstri'";
$result_check_suaminik = $conn->query($query_check_suaminik);
$result_check_istrinik = $conn->query($query_check_istrinik);


// Memeriksa apakah NIK suami atau NIK istri sudah ada di database
if ($result_check_suaminik->num_rows > 0) {
    $alertMessage = "NIK suami sudah terdaftar dalam database.";
    echo "<script>alert('$alertMessage'); window.location.href = 'datapernikahan.php';</script>";
} elseif($result_check_istrinik->num_rows > 0) {
  $alertMessage = " NIK istri sudah terdaftar dalam database.";
  echo "<script>alert('$alertMessage'); window.location.href = 'datapernikahan.php';</script>";
}else {
    // Query SQL untuk menyimpan data ke database
    $query = "INSERT INTO datapernikahan (nik_istri, nik_suami, nama_suami, nama_istri, ttl_suami, ttl_istri, tanggal_nikah, usia_suami, usia_istri, pendidikanterakhir_suami, pendidikanterakhir_istri, alamat_nikah, saksi_nikah, faktor_pernikahan, tanggal_penginputan, kabkota_kua) VALUES ('$nikIstri', '$nikSuami', '$namaSuami', '$namaIstri', '$ttlSuami', '$ttlIstri', '$tanggalNikah', '$usiaSuami', '$usiaIstri', '$pendidikanSuami', '$pendidikanIstri', '$alamatPernikahan', '$saksiNikah', '$faktorPernikahan', '$tanggalInput', '$kabkota_kua')";

    // Eksekusi query untuk menyimpan data
    if ($conn->query($query) === TRUE) {
        echo "Data berhasil disimpan";
        header("Location: ./datapernikahan.php");
        exit();
    } else {
        echo "Error: " . $query . "<br>" . $conn->error;
    }
}
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin</title>
    <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png" />
    <link rel="stylesheet" href="../assets/css/styles.min.css" />
</head>

<body>
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">
        <aside class="left-sidebar">
            <div>
                <div class="brand-logo d-flex align-items-center justify-content-between">
                    <a href="./index.php" class="text-nowrap logo-img">
                        <h1>DP3A</h1>
                    </a>
                    <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
                        <i class="ti ti-x fs-8"></i>
                    </div>
                </div>

                <aside class="left-sidebar">
                    <div>
                        <div class="brand-logo d-flex align-items-center justify-content-between">
                            <a href="./index.php" class="text-nowrap logo-img">
                                <img src="../assets/images/logos/logoasli.png" alt="" style="margin: 10px 30px"
                                    width="150px">
                            </a>
                            <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
                                <i class="ti ti-x fs-8"></i>
                            </div>
                        </div>
                        <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
                            <ul id="sidebarnav">
                                <li class="nav-small-cap">
                                    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                                    <span class="hide-menu">Dashboard</span>
                                </li>
                                <li class="sidebar-item">
                                    <a class="sidebar-link" href="./index.php" aria-expanded="false">
                                        <span>
                                            <i class="ti ti-layout-dashboard"></i>
                                        </span>
                                        <span class="hide-menu">Data Pernikahan</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a class="sidebar-link" href="./datapernikahan.php" aria-expanded="false">
                                        <span>
                                            <i class="ti ti-file-description"></i>
                                        </span>
                                        <span class="hide-menu">Forms Pernikahan</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a class="sidebar-link" href="./indexkekerasan.php" aria-expanded="false">
                                        <span>
                                            <i class="ti ti-layout-dashboard"></i>
                                        </span>
                                        <span class="hide-menu">Data Kekerasan</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a class="sidebar-link" href="./datakekerasan.php" aria-expanded="false">
                                        <span>
                                            <i class="ti ti-file-description"></i>
                                        </span>
                                        <span class="hide-menu">Forms Kekerasan</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a class="sidebar-link" href="./complaint.php" aria-expanded="false">
                                        <span>
                                            <i class="ti ti-layout-dashboard"></i>
                                        </span>
                                        <span class="hide-menu">Complaint Kekerasan</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a class="sidebar-link" href="./complaintpernikahan.php" aria-expanded="false">
                                        <span>
                                            <i class="ti ti-layout-dashboard"></i>
                                        </span>
                                        <span class="hide-menu">Complaint Pernikahan</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <form method="post" action="">
                                        <input class='btn btn-danger' type="submit" name="logout" value="Logout">
                                    </form>
                                </li>
                            </ul>
                        </nav>
                    </div>
            </div>
        </aside>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-4">Forms</h5>
                <div class="card">
                    <div class="card-body">
                        <form method="post" action="">

                            <div class="body-wrapper">

                                <label for="kabkota_kua" class="form-label">Kabupaten</label>
                                <div class="dropdown">
                                    <button class="btn btn-secondary dropdown-toggle" type="button"
                                        id="dropdownMenuButton5" data-bs-toggle="dropdown" aria-expanded="false">
                                        <?php echo isset($_POST['kabkota_kua']) ? $_POST['kabkota_kua'] : 'Kabupaten'; ?>
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton5">
                                        <li><a class="dropdown-item" onclick="updateDropdown5('KOTA PALU')">Kota
                                                Palu</a></li>
                                        <li><a class="dropdown-item" onclick="updateDropdown5('SIGI')">Kabupaten
                                                Sigi</a></li>
                                        <li><a class="dropdown-item" onclick="updateDropdown5('DONGGALA')">Kabupaten
                                                Donggala</a></li>
                                        <li><a class="dropdown-item"
                                                onclick="updateDropdown5('PARIGI MOUTONG')">Kabupaten Parigi Moutong</a>
                                        </li>
                                        <li><a class="dropdown-item" onclick="updateDropdown5('POSO')">Kabupaten
                                                Poso</a></li>
                                        <li><a class="dropdown-item" onclick="updateDropdown5('TOJO UNA-UNA')">Kabupaten
                                                Tojo Una-Una</a></li>
                                        <li><a class="dropdown-item" onclick="updateDropdown5('MOROWALI')">Kabupaten
                                                Morowali</a></li>
                                        <li><a class="dropdown-item" onclick="updateDropdown5('BANGGAI')">Kabupaten
                                                Banggai</a></li>
                                        <li><a class="dropdown-item" onclick="updateDropdown5('BANGGAI LAUT')">Kabupaten
                                                Banggai Laut</a></li>
                                        <li><a class="dropdown-item"
                                                onclick="updateDropdown5('BANGGAI KEPULAUAN')">Kabupaten Banggai
                                                Kepulauan</a></li>
                                        <li><a class="dropdown-item" onclick="updateDropdown5('TOLITOLI')">Kabupaten
                                                Toli-Toli</a></li>
                                        <li><a class="dropdown-item" onclick="updateDropdown5('BUOL')">Kabupaten
                                                Buol</a></li>
                                        <li><a class="dropdown-item"
                                                onclick="updateDropdown5('MOROWALI UTARA')">Kabupaten Morowali Utara</a>
                                        </li>
                                    </ul>
                                    <input type="hidden" name="kabkota_kua" id="kabkota_kua"
                                        value="<?php echo isset($_POST['kabkota_kua']) ? $_POST['kabkota_kua'] : ''; ?>"
                                        required>
                                </div>
                                <script>
                                function updateDropdown5(value) {
                                    document.querySelector("#dropdownMenuButton5").innerText = value;
                                    document.querySelector("#kabkota_kua").value = value;
                                }
                                </script>
                                <br>
                                <div class="mb-3">
                                    <label for="nik_istri" class="form-label">NIK Istri</label>
                                    <input type="text" class="form-control" id="nik_istri" name="nik_istri" required>
                                </div>
                                <div class="mb-3">
                                    <label for="nik_suami" class="form-label">NIK Suami</label>
                                    <input type="text" class="form-control" id="nik_suami" name="nik_suami" required>
                                </div>

                                <div class="mb-3">
                                    <label for="nama_suami" class="form-label">Nama Suami</label>
                                    <input type="text" class="form-control" id="nama_suami" name="nama_suami" required>
                                </div>

                                <div class="mb-3">
                                    <label for="nama_istri" class="form-label">Nama Istri</label>
                                    <input type="text" class="form-control" id="nama_istri" name="nama_istri" required>
                                </div>

                                <div class="mb-3">
                                    <label for="ttl_suami" class="form-label">Tanggal Lahir Suami</label>
                                    <input type="date" class="form-control" id="ttl_suami" name="ttl_suami"
                                        onchange="hitungUsia('ttl_suami', 'usia_suami')" required>
                                </div>

                                <div class="mb-3">
                                    <label for="usia_suami" class="form-label">Usia Suami</label>
                                    <input type="text" class="form-control" id="usia_suami" name="usia_suami" readonly>
                                </div>

                                <div class="mb-3">
                                    <label for="ttl_istri" class="form-label">Tanggal Lahir Istri</label>
                                    <input type="date" class="form-control" id="ttl_istri" name="ttl_istri"
                                        onchange="hitungUsia('ttl_istri', 'usia_istri')" required>
                                </div>

                                <div class="mb-3">
                                    <label for="usia_istri" class="form-label">Usia Istri</label>
                                    <input type="text" class="form-control" id="usia_istri" name="usia_istri" readonly>
                                </div>

                                <script>
                                function hitungUsia(tanggalLahirId, usiaId) {
                                    var tanggalLahir = document.getElementById(tanggalLahirId).value;
                                    var dob = new Date(tanggalLahir);
                                    var today = new Date();
                                    var age = Math.floor((today - dob) / (365.25 * 24 * 60 * 60 * 1000));
                                    document.getElementById(usiaId).value = age + " tahun";
                                }
                                </script>

                                <div class="mb-3">
                                    <label for="tanggal_nikah" class="form-label">Tanggal Nikah</label>
                                    <input type="date" class="form-control" id="tanggal_nikah" name="tanggal_nikah"
                                        required>
                                </div>

                                <div class="mb-3">
                                    <label for="pendidikanterakhir_suami" class="form-label">Pendidikan Terakhir
                                        Suami</label>
                                    <div class="dropdown">
                                        <button class="btn btn-secondary dropdown-toggle" type="button"
                                            id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                            <?php echo isset($_POST['pendidikanterakhir_suami']) ? $_POST['pendidikanterakhir_suami'] : 'Pendidikan Terakhir'; ?>
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <li><a class="dropdown-item" onclick="updateDropdown4('SD')">SD</a></li>
                                            <li><a class="dropdown-item"
                                                    onclick="updateDropdown4('SLTP/SMP')">SLTP/SMP</a></li>
                                            <li><a class="dropdown-item"
                                                    onclick="updateDropdown4('SLTA/SMA')">SLTA/SMA</a></li>
                                        </ul>
                                        <input type="hidden" name="pendidikanterakhir_suami"
                                            id="pendidikanterakhir_suami"
                                            value="<?php echo isset($_POST['pendidikanterakhir_suami']) ? $_POST['pendidikanterakhir_suami'] : ''; ?>"
                                            required>
                                    </div>
                                </div>
                                <script>
                                function updateDropdown4(value) {
                                    document.querySelector("#dropdownMenuButton").innerText = value;
                                    document.querySelector("#pendidikanterakhir_suami").value = value;
                                }
                                </script>

                                <div class="mb-3">
                                    <label for="pendidikanterakhir_istri" class="form-label">Pendidikan Terakhir
                                        Istri</label>
                                    <div class="dropdown">
                                        <button class="btn btn-secondary dropdown-toggle" type="button"
                                            id="dropdownMenuButton3" data-bs-toggle="dropdown" aria-expanded="false">
                                            <?php echo isset($_POST['pendidikanterakhir_istri']) ? $_POST['pendidikanterakhir_istri'] : 'Pendidikan Terakhir'; ?>
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton3">
                                            <li><a class="dropdown-item" onclick="updateDropdown3('SD')">SD</a></li>
                                            <li><a class="dropdown-item"
                                                    onclick="updateDropdown3('SLTP/SMP')">SLTP/SMP</a></li>
                                            <li><a class="dropdown-item"
                                                    onclick="updateDropdown3('SLTA/SMA')">SLTA/SMA</a></li>
                                        </ul>
                                        <input type="hidden" name="pendidikanterakhir_istri"
                                            id="pendidikanterakhir_istri"
                                            value="<?php echo isset($_POST['pendidikanterakhir_istri']) ? $_POST['pendidikanterakhir_istri'] : ''; ?>"
                                            required>
                                    </div>
                                </div>
                                <script>
                                function updateDropdown3(value) {
                                    document.querySelector("#dropdownMenuButton3").innerText = value;
                                    document.querySelector("#pendidikanterakhir_istri").value = value;
                                }
                                </script>

                                <div class="mb-3">
                                    <label for="alamat_nikah" class="form-label">Alamat Pernikahan</label>
                                    <input type="text" class="form-control" id="alamat_nikah" name="alamat_nikah"
                                        required>
                                </div>

                                <div class="mb-3">
                                    <label for="saksi_nikah" class="form-label">Saksi</label>
                                    <div class="dropdown">
                                        <button class="btn btn-secondary dropdown-toggle" type="button"
                                            id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                            <?php echo isset($_POST['saksi_nikah']) ? $_POST['saksi_nikah'] : 'Pilih Saksi Pernikahan'; ?>
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                            <li><a class="dropdown-item" onclick="updateDropdown('Aparat Desa')">Aparat
                                                    Desa</a></li>
                                            <li><a class="dropdown-item"
                                                    onclick="updateDropdown('Lembaga Adat')">Lembaga Adat</a></li>
                                            <li><a class="dropdown-item" onclick="updateDropdown('Imam Masjid')">Imam
                                                    Masjid</a></li>
                                        </ul>
                                        <input type="hidden" name="saksi_nikah" id="saksi_nikah"
                                            value="<?php echo isset($_POST['saksi_nikah']) ? $_POST['saksi_nikah'] : ''; ?>"
                                            required>
                                    </div>
                                </div>

                                <script>
                                function updateDropdown(value) {
                                    document.querySelector("#dropdownMenuButton1").innerText = value;
                                    document.querySelector("#saksi_nikah").value = value;
                                }
                                </script>

                                <div class="mb-3">
                                    <label for="faktor_pernikahan" class="form-label">Faktor</label>
                                    <div class="dropdown">
                                        <button class="btn btn-secondary dropdown-toggle" type="button"
                                            id="dropdownMenuButton2" data-bs-toggle="dropdown" aria-expanded="false"
                                            name="faktor_pernikahan">
                                            <?php echo isset($_POST['faktor_pernikahan']) ? $_POST['faktor_pernikahan'] : 'Pilih Faktor Pernikahan'; ?>
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton2">
                                            <li><a class="dropdown-item"
                                                    onclick="updateDropdown2('Pergaulan Bebas')">Pergaulan
                                                    Bebas</a></li>
                                            <li><a class="dropdown-item"
                                                    onclick="updateDropdown2('Perjodohan')">Perjodohan</a></li>
                                            <li><a class="dropdown-item" onclick="updateDropdown2('Budaya')">Budaya</a>
                                            </li>
                                            <li><a class="dropdown-item"
                                                    onclick="updateDropdown2('Ekonomi')">Ekonomi</a></li>
                                            <li><a class="dropdown-item"
                                                    onclick="updateDropdown2('Pendidikan')">Pendidikan</a></li>
                                        </ul>
                                        <input type="hidden" name="faktor_pernikahan" id="faktor_pernikahan"
                                            value="<?php echo isset($_POST['faktor_pernikahan']) ? $_POST['faktor_pernikahan'] : ''; ?>"
                                            required>
                                    </div>
                                </div>

                                <script>
                                function updateDropdown2(value) {
                                    document.querySelector("#dropdownMenuButton2").innerText = value;
                                    document.querySelector("#faktor_pernikahan").value = value;
                                }
                                </script>

                                <div class="mb-3">
                                    <label for="tanggal_input" class="form-label">Tanggal Input</label>
                                    <input type="text" class="form-control" id="tanggal_input" name="tanggal_input"
                                        value="<?php echo date('Y-m-d'); ?>" disabled>
                                </div>
                                <button type="submit" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
    <script src="../assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/sidebarmenu.js"></script>
    <script src="../assets/js/app.min.js"></script>
    <script src="../assets/libs/simplebar/dist/simplebar.js"></script>
</body>

</html>