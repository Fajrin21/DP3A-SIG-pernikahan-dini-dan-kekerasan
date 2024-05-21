  <?php

  function rc4($key, $encrypted_str)
  {
    // Inisialisasi array s
    $s = array();
    for ($i = 0; $i < 256; $i++) {
      $s[$i] = $i;
    }

    // Inisialisasi variabel dan array lainnya
    $j = 0;
    $key_length = strlen($key);
    $str_length = strlen($encrypted_str);
    $res = '';

    // Key-Scheduling Algorithm (KSA)
    for ($i = 0; $i < 256; $i++) {
      $j = ($j + $s[$i] + ord($key[$i % $key_length])) % 256;
      $temp = $s[$i];
      $s[$i] = $s[$j];
      $s[$j] = $temp;
    }

    // Pseudo-Random Generation Algorithm (PRGA) dan Dekripsi
    $i = $j = 0;
    for ($y = 0; $y < $str_length / 2; $y++) {
      $i = ($i + 1) % 256;
      $j = ($j + $s[$i]) % 256;
      $temp = $s[$i];
      $s[$i] = $s[$j];
      $s[$j] = $temp;

      // Mendapatkan byte enkripsi dari ciphertext dalam format heksadesimal
      $hex = substr($encrypted_str, $y * 2, 2); //Baris ini mengambil dua karakter dari ciphertext dalam format heksadesimal pada setiap iterasi loop.

      // Mendekripsi byte
      $res .= chr(hexdec($hex) ^ $s[($s[$i] + $s[$j]) % 256]); //Baris ini mendekripsi byte yang diambil dari ciphertext.
    }

    return $res;
  }

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



  // Kunci untuk enkripsi
  $key = "kuncisaya";

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


  if (isset($_GET['delete']) && isset($_GET['id'])) {
    // Get the ID from the URL
    $id = $_GET['id'];

    // Prepare a SQL statement to delete the record with the given ID
    $deleteQuery = "DELETE FROM pengaduan WHERE id = $id";

    // Execute the delete query
    if ($conn->query($deleteQuery) === TRUE) {
      // Deletion successful, redirect to the same page without 'delete' parameter
      header("Location: " . $_SERVER['PHP_SELF']);
      exit();
    } else {
      // Handle the error if deletion fails
      echo "Error deleting data: " . $conn->error;
    }
  }

  if (isset($_GET['fetch_data']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $fetchQuery = "SELECT * FROM pengaduan WHERE id = $id";
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

  if (isset($_POST['change_status']) && isset($_POST['id']) && isset($_POST['status'])) {
    $id = $_POST['id'];
    $status = $_POST['status'];

    // Update hanya status pada record dengan ID yang diberikan
    $updateStatusQuery = "UPDATE pengaduan SET proses = '$status' WHERE id = $id";

    if ($conn->query($updateStatusQuery) === TRUE) {
    } else {
    }
    exit(); // Stop further execution
  }


  $sql = "SELECT * FROM pengaduan";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
    // Output data dari setiap baris
    while ($row = $result->fetch_assoc()) {
      // Dekripsi data
      $nama_decrypted = rc4($key, $row['nama']);
      $email_decrypted = rc4($key, $row['email']);
      $message_decrypted = rc4($key, $row['message']);
      $bukti_decrypted = rc4($key, $row['bukti']);
    }
  }

  $recordsPerPage = 5; // Adjust as needed
  $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
  $offset = ($page - 1) * $recordsPerPage;

  // Fetch total number of records
  $totalQuery = "SELECT COUNT(*) AS total FROM pengaduan";
  $totalResult = $conn->query($totalQuery);
  $totalRecords = $totalResult->fetch_assoc()['total'];

  // Calculate total pages
  $totalPages = ceil($totalRecords / $recordsPerPage);

  // Fetch data for the current page
  $query = "SELECT * FROM pengaduan LIMIT $offset, $recordsPerPage";
  $result = $conn->query($query);

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the form was submitted
    $id = isset($_POST['id']) ? $_POST['id'] : null;

    // Validate other fields as needed
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];
    $tanggal_pelaporan = $_POST['tanggal_pelaporan'];
    $nomorhp = $_POST['nomorhp'];


    if ($id) {
      $updateQuery = "UPDATE pengaduan SET nama = '$nama', email = '$email', subject = '$subject', message = '$message', tanggal_pelaporan = '$tanggal_pelaporan', nomorhp = '$nomorhp' WHERE id = $id";
      if ($conn->query($updateQuery) === TRUE) {
        echo "Record updated successfully";
      } else {
        echo "Error updating record: " . $conn->error;
      }
    } else {
      $insertQuery = "INSERT INTO pengaduan (nama, email, subject, message, tanggal_pelaporan) VALUES ('$nama', '$email', '$subject', '$message', '$tanggal_pelaporan')";
      if ($conn->query($insertQuery) === TRUE) {
        echo "New record created successfully";
      } else {
        echo "Error: " . $insertQuery . "<br>" . $conn->error;
      }
    }
  }
  ?>

  <!DOCTYPE html>
  <html lang="en">

  <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>DP3A ADMIN DASHBOARD</title>
      <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png" />
      <link rel="stylesheet" href="../assets/css/styles.min.css" />
      <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
      <script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
      <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
      <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
          integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
      <script>
      function submitForm() {
          // Fetch form data
          var formData = $('#updateForm').serialize();

          // Send an AJAX request to update data
          $.ajax({
              type: 'POST',
              url: window.location.href,
              data: formData,
              success: function(response) {
                  try {
                      var data = JSON.parse(response);
                      if (data.status === 'success') {
                          // If update is successful, reload the page
                          location.reload();
                      } else {
                          // If there is an error, display the error message
                          alert('Error updating data: ' + data.message);
                          location.reload();
                      }
                  } catch (error) {
                      console.log(error);
                  } finally {
                      // Regardless of success or error, hide the modal
                      $('#updateModal').modal('hide');
                      location.reload();
                  }
              },
              error: function(error) {
                  console.log(error);
                  // In case of an error, hide the modal
                  $('#updateModal').modal('hide');
                  location.reload();
              }
          });
      }

      function deleteData(id) {
          // Display a confirmation alert before proceeding with deletion
          var isConfirmed = confirm("Are you sure you want to delete this record?");
          if (isConfirmed) {
              // Redirect to the same page with the 'delete' parameter in the URL
              window.location.href = '?delete=true&id=' + id;
          }
      }

      function updateData(id) {
          $('#updateModal').modal('show');

          // Fetch existing data using AJAX and populate the form fields
          $.ajax({
              type: 'GET',
              url: window.location.href, // Use the current URL (the same file) to fetch data
              data: {
                  fetch_data: true,
                  id: id
              },
              success: function(response) {
                  try {
                      var data = JSON.parse(response);
                      $('#user_id').val(data.id);
                      $('#nama').val(data.nama);
                      $('#email').val(data.email);
                      $('#subject').val(data.subject);
                      $('#message').val(data.message);
                      $('#tanggal_pelaporan').val(data.tanggal_pelaporan);
                      $('#proses').val(data.proses);
                  } catch (error) {
                      console.log(error);
                  }
              },
              error: function(error) {
                  console.log(error);
              }
          });
      }

      function changeStatus(id, status) {
          // Send AJAX request to change status
          $.ajax({
              type: 'POST',
              url: window.location.href,
              data: {
                  change_status: true,
                  id: id,
                  status: status
              },
              success: function(response) {
                  // Jika status berhasil diperbarui, tampilkan pesan sukses atau lakukan tindakan lain yang sesuai
                  location.reload();
              },
              error: function(error) {
                  console.log(error);
              }
          });
      }
      </script>
  </head>

  <body>

      <!-- Body Wrapper -->
      <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
          data-sidebar-position="fixed" data-header-position="fixed">
          <!-- Sidebar Start -->
          <aside class="left-sidebar">
              <!-- Sidebar scroll-->
              <div>
                  <div class="brand-logo d-flex align-items-center justify-content-between">
                      <a href="./index.php" class="text-nowrap logo-img">
                          <img src="../assets/images/logos/logoasli.png" alt="" style="margin: 10px 30px" width="150px">
                      </a>
                      <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
                          <i class="ti ti-x fs-8"></i>
                      </div>
                  </div>
                  <!-- Sidebar navigation-->
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
                  <!-- End Sidebar navigation -->
              </div>
              <!-- End Sidebar scroll-->
          </aside>
          <!-- Sidebar End -->
          <!-- Main wrapper -->
          <div class="body-wrapper">
              <div class="container-fluid">
                  <br>
                  <!-- Row 1 -->
                  <div class="col-lg-12 d-flex align-items-stretch">
                      <div class="card w-300">
                          <div class="card-body p-4">
                              <h5 class="card-title fw-semibold mb-4">Data Complaint</h5>
                              <div class="table-responsive">
                                  <table class="table text-nowrap mb-0 align-middle">
                                      <thead class="text-dark fs-4">
                                          <tr>
                                              <th class="border-bottom-0">
                                                  <h6 class="fw-semibold mb-0">Unique Code</h6>
                                              </th>
                                              <th class="border-bottom-0">
                                                  <h6 class="fw-semibold mb-0">Nama</h6>
                                              </th>
                                              <th class="border-bottom-0">
                                                  <h6 class="fw-semibold mb-0">Email</h6>
                                              </th>
                                              <th class="border-bottom-0">
                                                  <h6 class="fw-semibold mb-0">Subject</h6>
                                              </th>
                                              <th class="border-bottom-0">
                                                  <h6 class="fw-semibold mb-0">Message</h6>
                                              </th>
                                              <th class="border-bottom-0">
                                                  <h6 class="fw-semibold mb-0">Nomor Hp</h6>
                                              </th>
                                              <th class="border-bottom-0">
                                                  <h6 class="fw-semibold mb-0">Tanggal Pelaporan</h6>
                                              </th>
                                              <th class="border-bottom-0">
                                                  <h6 class="fw-semibold mb-0">NIK</h6>
                                              </th>
                                              <th class="border-bottom-0">
                                                  <h6 class="fw-semibold mb-0">Bukti</h6>
                                              </th>
                                              <th class="border-bottom-0">
                                                  <h6 class="fw-semibold mb-0">Status</h6>
                                              </th>
                                              <th class="border-bottom-0">
                                                  <h6 class="fw-semibold mb-0">Tombol Aksi</h6>
                                              </th>
                                          </tr>
                                      </thead>
                                      <tbody>
                                          <?php
                      // Waktu awal
                      $start_time = microtime(true);

                      while ($row = $result->fetch_assoc()) {
                        // Dekripsi data
                        $uniqueCode = rc4($key, $row['nama']);

                        $start_decrypt_time = microtime(true);

                        $nama_decrypted = rc4($key, $row['nama']);
                        $end_decrypt_name_time = microtime(true);

                        $email_decrypted = rc4($key, $row['email']);
                        $end_decrypt_email_time = microtime(true);

                        $message_decrypted = rc4($key, $row['message']);
                        $end_decrypt_message_time = microtime(true);

                        // Waktu dekripsi untuk nama
                        $execution_time_decrypt_name = ($end_decrypt_name_time - $start_decrypt_time) * 1000;

                        // Waktu dekripsi untuk email
                        $execution_time_decrypt_email = ($end_decrypt_email_time - $end_decrypt_name_time) * 1000;

                        // Waktu dekripsi untuk pesan (message)
                        $execution_time_decrypt_message = ($end_decrypt_message_time - $end_decrypt_email_time) * 1000;

                        $ktp_decrypted = rc4($key, $row['ktp']);

                        $subject = $row['subject'];
                        $nomorhp_decrypted = rc4($key, $row['nomorhp']);

                        $kodeunik = $row['kode_unik'];

                        $fileDirectory = 'file/';
                        $bukti = $fileDirectory . $row['bukti'];
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
                        echo "<td class='border-bottom-0'><h6 class='fw-semibold mb-0'>" . $nama_decrypted . "</h6></td>";
                        echo "<td class='border-bottom-0'><h6 class='fw-semibold mb-0'>" . $email_decrypted . "</h6></td>";
                        echo "<td class='border-bottom-0'><h6 class='fw-semibold mb-0'>" . $subject . "</h6></td>";
                        echo "<td class='border-bottom-0'><h6 class='fw-semibold mb-0'>" . $message_decrypted . "</h6></td>";
                        echo "<td class='border-bottom-0'><h6 class='fw-semibold mb-0'>" . $nomorhp_decrypted . "</h6></td>";
                        echo "<td class='border-bottom-0'><h6 class='fw-semibold mb-0'>" . date('Y-m-d', strtotime($row['tanggal_pelaporan'])) . "</h6></td>";
                        echo "<td class='border-bottom-0'><h6 class='fw-semibold mb-0'>" . $ktp_decrypted . "</h6></td>";
                        echo "<td class='border-bottom-0'><img src='" . $bukti . "' style='width:100px;height:100px;'></td>";
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
                        echo "<td class='border-bottom-0'>
              <button class='btn btn-sm btn-primary' onclick='updateData(" . $row['id'] . ")'>Update</button>
              <button class='btn btn-sm btn-danger' onclick='deleteData(" . $row['id'] . ")'>Delete</button>
              <button class='btn btn-sm btn-warning' onclick='changeStatus(" . $row['id'] . ", \"Sementara di Proses\")'>Sementara di Proses</button>
              <button class='btn btn-sm btn-success' onclick='changeStatus(" . $row['id'] . ", \"Sudah di Proses\")'>Sudah di Proses</button>
                          </td>";
                        echo "</tr>";
                        echo "</tr>";
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
          <div class="modal" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel"
              aria-hidden="true">
              <div class="modal-dialog" role="document">
                  <div class="modal-content">
                      <div class="modal-header">
                          <h5 class="modal-title" id="updateModalLabel">Update Data</h5>
                          <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                          </button>
                      </div>
                      <div class="modal-body">

                          <!-- Update Form -->
                          <form id="updateForm" action="" method="post">
                              <div class="mb-3">
                                  <input type="hidden" class="form-control" id="user_id" name="id">
                              </div>
                              <div class="mb-3">
                                  <label for="nama" class="form-label">nama</label>
                                  <input type="text" class="form-control" id="nama" name="nama">
                              </div>
                              <div class="mb-3">
                                  <label for="email" class="form-label">email</label>
                                  <input type="text" class="form-control" id="email" name="email">
                              </div>

                              <div class="mb-3">
                                  <label for="subject" class="form-label">subject</label>
                                  <input type="text" class="form-control" id="subject" name="subject">
                              </div>

                              <div class="mb-3">
                                  <label for="message" class="form-label">message</label>
                                  <input type="text" class="form-control" id="message" name="message">
                              </div>

                              <div class="mb-3">
                                  <label for="tanggal_pelaporan" class="form-label">tanggal pelaporan</label>
                                  <input type="date" class="form-control" id="tanggal_pelaporan"
                                      name="tanggal_pelaporan">
                              </div>

                              <div class="mb-3">
                                  <label for="proses" class="form-label">Proses</label>
                                  <input type="text" class="form-control" id="proses" name="proses">
                              </div>

                              <div class="mb-3">
                                  <label for="tanggal_input" class="form-label">Tanggal Input</label>
                                  <input type="text" class="form-control" id="tanggal_input" name="tanggal_input"
                                      value="<?php echo date('Y-m-d'); ?>" disabled>
                              </div>
                          </form>
                          <div class="modal-footer">
                              <!-- Update button triggers form submission using JavaScript -->
                              <button type="button" class="btn btn-primary" onclick="submitForm()">Update</button>
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                          </div>
                      </div>
                  </div>
              </div>
              <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
              <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.min.js"></script>
              <script src="../assets/js/sidebarmenu.js"></script>
              <script src="../assets/js/app.min.js"></script>
              <script src="../assets/libs/apexcharts/dist/apexcharts.min.js"></script>
              <script src="../assets/libs/simplebar/dist/simplebar.js"></script>
              <script src="../assets/js/dashboard.js"></script>
  </body>

  </html>