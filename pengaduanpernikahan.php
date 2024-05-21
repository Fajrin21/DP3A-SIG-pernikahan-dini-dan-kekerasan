<?php

function generateUniqueCode() {
  return substr(md5(uniqid(mt_rand(), true)), 0, 8);
}
    function rc4($key, $str)
    {
      //inisialisasi array 3
      $s = array();
      for ($i = 0; $i < 256; $i++) {
        $s[$i] = $i;
      }
    
      //Key-Scheduling Algorithm
      $j = 0;
      for ($i = 0; $i < 256; $i++) { //loop yang akan dieksekusi sebanyak 256 kali
        $j = ($j + $s[$i] + ord($key[$i % strlen($key)])) % 256; // langkah untuk mengacak array S
        $temp = $s[$i]; //  Ini adalah langkah untuk menukar nilai antara elemen array S pada indeks $i dengan nilai pada indeks $j.
        $s[$i] = $s[$j];
        $s[$j] = $temp;
      }
    
      //Pseudo-Random Generation Algorithm (PRGA) untuk melakukan enkripsi atau dekripsi data.
      $i = $j = 0;
      $res = '';
      for ($y = 0; $y < strlen($str); $y++) {
        $i = ($i + 1) % 256;
        $j = ($j + $s[$i]) % 256;
        $temp = $s[$i];
        $s[$i] = $s[$j];
        $s[$j] = $temp;
        // Menggunakan fungsi sprintf() untuk menghasilkan format heksadesimal
        $res .= sprintf("%02X", ord($str[$y]) ^ $s[($s[$i] + $s[$j]) % 256]);
      }
      return $res;
    }
    
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "datapernikahananak";
    
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    $key = "kuncisaya";


    if (isset($_GET['fetch_data']) && isset($_GET['id'])) {
      $id = $_GET['id'];
      $fetchQuery = "SELECT * FROM pengaduanpernikahan WHERE id = $id";
      $fetchResult = $conn->query($fetchQuery);
  
      if ($fetchResult->num_rows > 0) {
        $rowData = $fetchResult->fetch_assoc();
        echo json_encode($rowData);
        exit();
      } else {
        echo json_encode(['error' => 'Data not found']);
        exit();
      }
    }

    $sql = "SELECT * FROM pengaduanpernikahan";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
    // Output data dari setiap baris
    while ($row = $result->fetch_assoc()) {
      // // Dekripsi data
      // $nama_decrypted = rc4($key, $row['nama']);
      // $email_decrypted = rc4($key, $row['email']);
      // $message_decrypted = rc4($key, $row['message']);
      // $bukti_decrypted = rc4($key, $row['bukti']);
    }
  }

  $recordsPerPage = 5; // Adjust as needed
  $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
  $offset = ($page - 1) * $recordsPerPage;

   // Fetch total number of records
   $totalQuery = "SELECT COUNT(*) AS total FROM pengaduanpernikahan";
   $totalResult = $conn->query($totalQuery);
   $totalRecords = $totalResult->fetch_assoc()['total'];
 
   // Calculate total pages
   $totalPages = ceil($totalRecords / $recordsPerPage);
 
   // Fetch data for the current page
   $query = "SELECT * FROM pengaduanpernikahan LIMIT $offset, $recordsPerPage";
   $result = $conn->query($query);

   

    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      // Ambil nilai dari formulir
      $id = isset($_POST['id']) ? $_POST['id'] : null;
    
      $nama_pengadu = $_POST['nama_pengadu'];
      $nama_anak = $_POST['nama_anak'];
      $tanggal_menikah = $_POST['tanggal_menikah'];
      $tempat_menikah = $_POST['tempat_menikah'];
      $nomor_pengadu = $_POST['nomor_pengadu'];
      $tanggal_pelaporan =  date('Y-m-d');
      $status = isset($_POST['proses']) ? $_POST['proses'] : "Belum di Proses";
      $uniqueCode = generateUniqueCode(); // Menghasilkan kode unik secara otomatis


      // Hitung waktu enkripsi untuk nama dan email
    // Hitung waktu enkripsi untuk nama dan email

    $start_time_nama = microtime(true);
    $nama_encrypted = $conn->real_escape_string(rc4($key, $nama_pengadu));
    $end_time_nama = microtime(true);
    $execution_time_nama = ($end_time_nama - $start_time_nama) * 1000; 
    
    $start_time_anak = microtime(true);
    $anak_encrypted = $conn->real_escape_string(rc4($key, $nama_anak));
    $end_time_anak = microtime(true);
    $execution_time_anak = ($end_time_anak - $start_time_anak) * 1000; 
    
    $start_time_menikah = microtime(true);
    $tanggalmenikah_encrypted = $conn->real_escape_string(rc4($key, $tanggal_menikah));
    $end_time_menikah = microtime(true);
    $execution_time_menikah = ($end_time_menikah - $start_time_menikah) * 1000; 
    
      $start_time_tempat = microtime(true);
      $tempatmenikah_encrypted = $conn->real_escape_string(rc4($key, $tempat_menikah));
      $end_time_tempat = microtime(true);
      $execution_time_tempat = ($end_time_tempat - $start_time_tempat) * 1000; // Waktu eksekusi dalam 
    
      $nomorpengadu_encrypted = $conn->real_escape_string(rc4($key, $nomor_pengadu));
    
      if ($id) {
        $updateQuery = "UPDATE pengaduanpernikahan SET nama_pengadu = '$namapengadu', nama_anak = '$nama_anak', tanggal_menikah = '$tanggal_menikah',tempat_menikah = '$tempat_menikah',proses = '$status', nomor_pengadu = '$nomor_pengadu', tanggal_pelaporan = '$tanggal_pelaporan', kode_unik = '$uniqueCode' WHERE id = $id";
        if ($conn->query($updateQuery) === TRUE) {
        } else {
        }
      } else {
        $insertQuery = "INSERT INTO pengaduanpernikahan (nama_pengadu, nama_anak, tanggal_menikah, tempat_menikah, nomor_pengadu, tanggal_pelaporan, proses, kode_unik) VALUES ('$nama_encrypted', '$anak_encrypted', '$tanggalmenikah_encrypted', '$tempatmenikah_encrypted', '$nomorpengadu_encrypted',  '$tanggal_pelaporan', '$status', '$uniqueCode ')";
        if ($conn->query($insertQuery) === TRUE) {
          $alertMessage = "Kode Unik Anda: $uniqueCode . silahkan disimpan untuk pengecekan status";
          echo "<script>alert('$alertMessage'); window.location.href = 'index.php';</script>";
    exit();
        } else {
        }
      }
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>TUGAS AKHIR</title>
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet" />
    <link rel="stylesheet" href="assets/css/style.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Jost:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet" />

    <!-- Vendor CSS Files -->
    <link href="assets/vendor/aos/aos.css" rel="stylesheet" />
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet" />
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet" />
    <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet" />
    <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet" />
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet" />

    <!-- Template Main CSS File -->
    <link href="assets/css/style.css" rel="stylesheet" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <style>
        #map {
            width: 800px;
            height: 500px;
        }

        .info {
            padding: 6px 8px;
            font: 14px/16px Arial, Helvetica, sans-serif;
            background: white;
            background: rgba(255, 255, 255, 0.8);
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            border-radius: 5px;
        }

        .info h4 {
            margin: 0 0 5px;
            color: #777;
        }

        .legend {
            text-align: left;
            line-height: 18px;
            color: #555;
        }

        .legend i {
            width: 18px;
            height: 18px;
            float: left;
            margin-right: 8px;
            opacity: 0.7;
        }
    </style>
</head>

<body>
    <header id="header" class="fixed-top" style="background-color: rgba(40, 58, 90, 0.9)">
        <div class="container d-flex align-items-center">
            <h1 class="logo me-auto"><a href="index.php">DP3A</a></h1>

            <nav id="navbar" class="navbar">
                <ul>
                    <li><a class="nav-link scrollto" href="index.php">Home</a></li>
                    <li class="dropdown scrollto">
                        <a href="#" style="cursor: hand"><span>Profil</span> <i class="bi bi-chevron-down"></i></a>
                        <ul>
                            <li><a href="informasidp3a.html">Informasi Kelembagaan</a></li>
                            <li><a href="daftardp3a.html">Daftar Dinas P3A</a></li>
                            <li><a href="#">Daftar Program Unggulan</a></li>
                        </ul>
                    </li>
                    <li class="dropdown active"><a href="#" style="color: white; cursor: hand;"><span>Pendataan</span> <i class="bi bi-chevron-down"></i></a>
                        <ul>
                            <li><a href="datapernikahan.php">Data Pernikahan</a></li>
                            <li><a href="datakekerasan.php">Data Kekerasan</a></li>
                        </ul>
                    </li>
                    <!-- <li><a class="nav-link scrollto" href="berita.html">Berita</a></li> -->
                    <li class="dropdown active"><a href="#" style="color: white; cursor: hand;"><span>Pengaduan</span> <i class="bi bi-chevron-down"></i></a>
            <ul>
              <li><a href="pengaduanpernikahan.php">Pengaduan Pernikahan Dini</a></li>
              <li><a href="pengaduankekerasan.php">Pengaduan Kekerasan</a></li>
            </ul>
          </li>                </ul>
                <i class="bi bi-list mobile-nav-toggle"></i>
            </nav>
        </div>
    </header>
    <br>
    <br><br>


    <section id="contact" class="contact">
    <div class="container" data-aos="fade-up">

      <div class="section-title">
        <h2>Pengaduan Pernikahan Dini</h2>
        <p>Berikut ini adalah formulir pengaduan yang kami sediakan untuk memberikan wadah kepada masyarakat dalam melaporkan pernikahan dini yang terjadi di daerah sendiri.</p><br>
        <p>#BersuaraUntukPerubahan</p>
      </div>

      <div class="row">

        <div class="col-lg-7 mt-5 mt-lg-0 d-flex align-items-stretch mx-auto" style="display: flex; justify-content: center; align-items: center;">
          <form action="" method="post" class="form-label" enctype="multipart/form-data">
            <div class="row">
              <div class="form-group col-md-6">
                <label for="nama" class="form-text">Nama Pengadu</label>
                <input type="text" name="nama_pengadu" class="form-control" id="nama" required>
              </div>
              <div class="form-group col-md-6">
                <label for="nama_anak" class="form-text">Nama Anak Yang Menikah</label>
                <input type="text" class="form-control" name="nama_anak" id="nama_anak" required>
              </div>
            </div>
            <br>
            <div class="mb-3">
              <label for="tanggal_menikah" class="form-text">Tanggal Pernikahan</label>
              <input type="date" class="form-control" id="tanggal_menikah" name="tanggal_menikah" required>
            </div>
            <div class="mb-3">
              <label for="tempat_menikah" class="form-text">Tempat Menikah</label>
              <input type="text" class="form-control" id="tempat_menikah" name="tempat_menikah" required>
            </div>
            <div class="mb-3">
              <label for="nomor_pengadu" class="form-text">Nomor HP Pengadu</label>
              <input type="text" class="form-control" id="nomor_pengadu" name="nomor_pengadu" required>
            </div>
            <div class="mb-3">
    <label for="tanggal_pelaporan" class="form-text">Tanggal Pelaporan</label>
    <input type="date" class="form-control" id="tanggal_pelaporan" name="tanggal_pelaporan" required>
</div>

<script>
    // Mendapatkan tanggal hari ini
    var today = new Date();

    // Mendapatkan tahun, bulan, dan tanggal
    var year = today.getFullYear();
    var month = String(today.getMonth() + 1).padStart(2, '0'); // Ditambah 1 karena Januari dimulai dari 0
    var day = String(today.getDate()).padStart(2, '0');

    // Format tanggal untuk input tanggal HTML: YYYY-MM-DD
    var formattedDate = year + '-' + month + '-' + day;

    // Mengatur nilai input tanggal ke tanggal hari ini
    document.getElementById('tanggal_pelaporan').value = formattedDate;
</script>
            <br>
            <div class="text-center"><button type="submit"  class="btn btn-primary" onclick="submitForm()">Send Message</button></div>
            <script>
              function submitForm() {
                var uniqueCode = generateUniqueCode();
  if (confirm("Kode unik anda untuk pelaporan ini adalah: " + uniqueCode + "")) {
    document.getElementById("myForm").submit();
  } else {
    return false;
  }
}
  </script>
            </form>
        </div>
      </div>
    </div>
  </section><!-- End Contact Section -->

  <div class="body-wrapper">
    <div class="container-fluid">
        <br>
        <!-- Row 1 -->
        <div class="row justify-content-center"> <!-- Menggunakan kelas justify-content-center untuk memposisikan konten ke tengah -->
            <div class="col-lg-6 d-flex align-items-stretch">
            <div class="card w-100">
                <div class="card-body p-4">
                    <h5 class="card-title fw-semibold mb-4">Data Pengaduan Pernikahan</h5>
                    <div class="table-responsive mx-auto"> <!-- Tambahkan mx-auto di sini -->
                        <table class="table table-bordered text-nowrap mb-0 align-middle">
                            <thead class="text-dark fs-4">
                                <tr>
                                    <th class="border-bottom-0">
                                        <h6 class="fw-semibold mb-0">Kode Unik</h6>
                                    </th>
                                    <th class="border-bottom-0">
                                        <h6 class="fw-semibold mb-0">Status</h6>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                while ($row = $result->fetch_assoc()) {

                                    $kodeunik = $row['kode_unik'];
                                    $statusColor = '';
                                    switch ($row['proses']) {
                                        case 'Sudah di Proses':
                                            $statusColor = 'green'; // Green color for 'Sudah di Proses'
                                            break;
                                        case 'Sementara di Proses':
                                            $statusColor = 'yellow'; // Yellow color for 'Sementara di Proses'
                                            break;
                                        default:
                                            $statusColor = 'white'; // White color for other cases
                                    }

                                    echo "<tr>";
                                    echo "<td class='border-bottom-0'><h6 class='fw-semibold mb-0'>" . $kodeunik . "</h6></td>";
                                    echo "<td class='border-bottom-0'>";
                                    switch ($row['proses']) {
                                        case 'Sudah di Proses':
                                            echo "<h6 class='fw-semibold mb-0' style='color: green'>" . $row['proses'] . "</h6>";
                                            break;
                                        case 'Sementara di Proses':
                                            echo "<h6 class='fw-semibold mb-0' style='color: orange'>" . $row['proses'] . "</h6>";
                                            break;
                                        default:
                                            echo "<h6 class='fw-semibold mb-0' style='color: black'>" . $row['proses'] . "</h6>";
                                    }
                                    echo "</td>";
                                }
                                echo '<ul class="pagination">';
                                for ($i = 1; $i <= $totalPages; $i++) {
                                    echo '<li class="page-item ' . ($page == $i ? 'active' : '') . '"><a class="page-link" href="?page=' . $i . '">' . $i . '</a></li>';
                                }
                                echo '</ul>';
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
<footer id="footer">
    <div class="footer-top">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6 footer-contact">
                    <h3>DINAS PEMBERDAYAAN PEREMPUAN DAN PERLINDUNGAN ANAK PROVINSI SULAWESI TENGAH</h3>
                    <p>
                        Jl. Mangunsarkoro No.31<br />
                        Besusu Timur <br />
                        Kec. Palu Timur Kota Palu<br />
                        Sulawesi Tengah 94111<br /><br />
                        <strong>Phone:</strong> +62 822 3122 8860<br />
                        <strong>Email:</strong> info@example.com<br />
                    </p>
                </div>

                <div class="col">
                    <div class="col-lg-3 col-md-6 footer-links">
                        <h4>Ikuti Kami</h4>
                        <div class="social-links mt-3">
                            <a href="#" class="facebook"><i class="bx bxl-facebook"></i></a>
                            <a href="https://www.instagram.com/dp3asulteng_official/" class="instagram"><i class="bx bxl-instagram"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container footer-bottom clearfix">
        <div class="copyright">
            &copy; Copyright <strong><span>Dinas P3A Sulawesi Tengah</span></strong>.
        </div>
    </div>
</footer>

<div id="preloader"></div>
<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<!-- Vendor JS Files -->
<script src="assets/vendor/aos/aos.js"></script>
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
<script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
<script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
<script src="assets/vendor/waypoints/noframework.waypoints.js"></script>
<script src="assets/vendor/php-email-form/validate.js"></script>

<!-- Template Main JS File -->
<script src="assets/js/main.js"></script>
<script src="assets/js/ajaxleaflet.js"></script>

</html>