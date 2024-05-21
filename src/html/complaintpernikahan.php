<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/db.php';

class ScheduleGA {
    private $population;
    private $populationSize = 50;
    private $generations = 100;
    private $fitnessScores = [];
    private $mutationRate = 0.01;
    private $crossoverRate = 0.7;
    private $elitismCount = 2;
    private $usedIdentifiers = [];

    public function __construct($data) {
        $this->populationSize = max(2, count($data));
        $this->initializePopulation($data);
    }

    private function initializePopulation($data) {
        $this->normalizeDateTime($data);
        $this->population = [];
        if (count($data) > 0) {
            for ($i = 0; $i < $this->populationSize; $i++) {
                shuffle($data);
                $this->population[] = $data;
            }
        }
    }

    public function run() {
        if (empty($this->population)) {
            return [];
        }

        for ($gen = 0; $gen < $this->generations; $gen++) {
            $this->calculateFitness();
            $newPopulation = $this->selectWithElitism();
            $this->crossover($newPopulation);
            $this->mutation();
        }
        return $this->population[0] ?? [];
    }

    private function calculateFitness() {
        $this->fitnessScores = array_map(function($chromosome) {
            $score = 0;
            foreach ($chromosome as $meeting) {
                $timeDiff = strtotime($meeting['deadline']) - strtotime($meeting['waktu']);
                if ($timeDiff >= 0) {
                    $score += 1 + ($timeDiff / (24 * 3600));
                } else {
                    $score -= 1;
                }
            }
            return $score;
        }, $this->population);
    }

    private function selectWithElitism() {
        array_multisort($this->fitnessScores, SORT_DESC, $this->population);
        return array_slice($this->population, 0, $this->elitismCount);
    }

    private function optimizeTime($individual) {
        foreach ($individual as &$meeting) {
            $currentDate = strtotime($meeting['waktu']);
            $startOfWork = strtotime(date('Y-m-d 08:00:00', $currentDate));
            $endOfWork = strtotime(date('Y-m-d 17:00:00', $currentDate));

            if ($currentDate < $startOfWork) {
                $meeting['waktu'] = date('Y-m-d H:i:s', $startOfWork);
            } elseif ($currentDate > $endOfWork) {
                $meeting['waktu'] = date('Y-m-d H:i:s', $startOfWork);  // Next day's start
            }
        }
        return $individual;
    }

    private function crossover(&$newPopulation) {
        while (count($newPopulation) < $this->populationSize) {
            $parent1 = $this->population[mt_rand(0, $this->populationSize - 1)];
            $parent2 = $this->population[mt_rand(0, $this->populationSize - 1)];
            if (mt_rand() / mt_getrandmax() < $this->crossoverRate) {
                $cutPoint = mt_rand(0, count($parent1) - 1);
                $child1 = array_merge(array_slice($parent1, 0, $cutPoint), array_slice($parent2, $cutPoint));
                $child2 = array_merge(array_slice($parent2, 0, $cutPoint), array_slice($parent1, $cutPoint));
                if ($this->isUnique($child1) && $this->isUnique($child2)) {
                    $newPopulation[] = $this->optimizeTime($child1);
                    $newPopulation[] = $this->optimizeTime($child2);
                }
            }
        }
        $this->population = $newPopulation;
    }

    private function isUnique($individual) {
        foreach ($individual as $meeting) {
            $identifier = $meeting['nama'].$meeting['kabkota'].$meeting['tempat'].$meeting['tanggalinput'].$meeting['deadline'].$meeting['waktu'];
            if (in_array($identifier, $this->usedIdentifiers)) {
                return false;  // Duplicate found
            }
            $this->usedIdentifiers[] = $identifier;
        }
        return true;
    }

    private function mutation() {
        foreach ($this->population as &$individual) {
            if (mt_rand() / mt_getrandmax() < $this->mutationRate) {
                $mutatePoint = mt_rand(0, count($individual) - 1);
                $currentDate = strtotime($individual[$mutatePoint]['waktu']);
                $inputDate = strtotime($individual[$mutatePoint]['tanggalinput']);
                $newDate = max($currentDate - mt_rand(0, 3) * 24 * 3600, $inputDate);
                $individual[$mutatePoint]['waktu'] = $this->adjustToOfficeHours(date('Y-m-d H:i:s', $newDate));
            }
        }
    }

    private function adjustToOfficeHours($datetime) {
        $time = strtotime($datetime);
        $startOfWork = strtotime(date('Y-m-d 08:00:00', $time));
        $endOfWork = strtotime(date('Y-m-d 17:00:00', $time));
        if ($time < $startOfWork) {
            return date('Y-m-d H:i:s', $startOfWork);
        }
        if ($time > $endOfWork) {
            return date('Y-m-d H:i:s', strtotime('+1 day', $startOfWork));
        }
        return date('Y-m-d H:i:s', $time);
    }

    private function normalizeDateTime(&$data) {
        foreach ($data as &$meeting) {
            $meeting['waktu'] = date('Y-m-d H:i:s', strtotime($meeting['waktu']));
        }
    }
}

// Fetch data and manage schedule with session
$sql = "SELECT nama, kabkota, tempat, waktu, deadline, tanggalinput FROM bapedda ORDER BY nama, kabkota, tempat";
$result = $conn->query($sql);
$meetingData = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $meetingData[] = $row;
    }
}

$currentDataHash = md5(json_encode($meetingData));
if (!isset($_SESSION['lastDataHash']) || $_SESSION['lastDataHash'] !== $currentDataHash) {
    $ga = new ScheduleGA($meetingData);
    $schedule = $ga->run();
    $_SESSION['schedule'] = $schedule;
    $_SESSION['lastDataHash'] = $currentDataHash;
} else {
    $schedule = $_SESSION['schedule'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />

    <title>SB Admin 2 - Tables</title>

    <!-- Custom fonts for this template -->

    <style>
    .modal {
        display: none;
        position: fixed;
        z-index: 1050;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.5);
    }

    .modal-img {
        width: 200%;
        /* Makes the image responsive */
        height: 200px;
        /* Fixed height */
        object-fit: cover;
        /* Ensures the image covers the area without distorting aspect ratio */
    }

    figure {
        margin: 10px;
        /* Provides some spacing around the image */
    }

    figcaption {
        text-align: center;
        /* Centers the caption text */
        margin-top: 5px;
        /* Spacing between image and caption */
    }

    .modal-content {
        background-color: #fefefe;
        margin: 5% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
        /* Responsive width */
        box-shadow: 0 4px 6px rgba(0, 0, 0, .1);
        animation-name: modalopen;
        animation-duration: 0.3s;
    }

    @keyframes modalopen {
        from {
            opacity: 0
        }

        to {
            opacity: 1
        }
    }

    .close {
        color: #aaaaaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: #000;
        text-decoration: none;
        cursor: pointer;
    }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>

<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
                <div class="sidebar-brand-text mx-3">BAPEDDA</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0" />

            <!-- Divider -->
            <hr class="sidebar-divider" />

            <!-- Heading -->
            <div class="sidebar-heading">Addons</div>

            <li class="nav-item active">
                <a class="nav-link" href="data.php">
                    <i class="fas fa-database"></i>
                    <span>Data</span>
                </a>
            </li>

            <li class="nav-item active">
                <a class="nav-link" href="progress.php">
                    <i class="fas fa-database"></i>
                    <span>Proses</span>
                </a>
            </li>

            <li class="nav-item active">
                <a class="nav-link" href="jadwal.php">
                    <i class="fas fa-database"></i>
                    <span>Jadwal</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block" />

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small"
                                            placeholder="Search for..." aria-label="Search"
                                            aria-describedby="basic-addon2" />
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>

                    </ul>
                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    <!-- Page Heading -->
                    <h1 class="h3 mb-2 text-gray-800">Jadwal Pertemuan</h1>

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                Jadwal Pertemuan
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>No:</th>
                                            <th>Nama:</th>
                                            <th>Kabupaten/kota:</th>
                                            <th>Tempat:</th>
                                            <th>Tanggal penginputan:</th>
                                            <th>Waktu:</th>
                                            <th>Deadline:</th>
                                            <!-- <th>File:</th> -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td><?= htmlspecialchars($meeting['nama']); ?></td>
                                            <td><?= htmlspecialchars($meeting['kabkota']); ?></td>
                                            <td><?= htmlspecialchars($meeting['tempat']); ?></td>
                                            <td><?= htmlspecialchars($meeting['tanggalinput']); ?></td>
                                            <td><?= htmlspecialchars($meeting['waktu']); ?></td>
                                            <td><?= htmlspecialchars($meeting['deadline']); ?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="7">No meetings scheduled.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <a class="scroll-to-top rounded" href="#page-top">
                <i class="fas fa-angle-up"></i>
            </a>

            <!-- <script src="../assets/js/info.js"></script> -->

            <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

</body>

</html>