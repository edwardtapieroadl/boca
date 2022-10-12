<?php
////////////////////////////////////////////////////////////////////////////////
//BOCA Online Contest Administrator
//    Copyright (C) 2003-2012 by BOCA Development Team (bocasystem@gmail.com)
//
//    This program is free software: you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation, either version 3 of the License, or
//    (at your option) any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//    You should have received a copy of the GNU General Public License
//    along with this program.  If not, see <http://www.gnu.org/licenses/>.
////////////////////////////////////////////////////////////////////////////////
// Last modified 19/oct/2017 by cassio@ime.usp.br

ob_start();
header ("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header ("Cache-Control: no-cache, must-revalidate");
header ("Pragma: no-cache");
header ("Content-Type: text/html; charset=utf-8");
session_start();
if(!isset($_POST['noflush']))
    ob_end_flush();
//$loc = $_SESSION['loc'];
//$locr = $_SESSION['locr'];
$loc = $locr = "..";
$runphp = "run.php";
$runeditphp = "runedit.php";

require_once("$locr/globals.php");
require_once("$locr/db.php");

if(!isset($_POST['noflush'])) {
    require_once("$locr/version.php");
    echo "<html><head><title>Admin's Page</title>\n";
    echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">\n";
    echo "<link rel='stylesheet' href=\"./style.css\">";
    echo "<script src=\"https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js\"
            integrity=\"sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3\"
	    crossorigin=\"anonymous\"></script>";
    echo "<link href=\"https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css\" rel=\"stylesheet\"
          integrity=\"sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT\" crossorigin=\"anonymous\">";
    echo "<script src=\"https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.min.js\"
            integrity=\"sha384-7VPbUDkoPSGFnVtYi0QogXtr74QeVeeIs99Qfg5YCF+TidwNdjvaKZX19NZ/e6oz\"
            crossorigin=\"anonymous\"></script>";
    echo "<script src=\"https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js\"
            integrity=\"sha384-u1OknCvxWvY5kfmNBILK2hRnQC3Pr17a+RTT6rIHI7NnikvbZlHgTPOOmMi466C8\"
            crossorigin=\"anonymous\"></script>";
    echo "<script src=\"https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js\"
            integrity=\"sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa\"
            crossorigin=\"anonymous\"></script>";
}

if(!ValidSession()) {
    InvalidSession("admin/index.php");
    ForceLoad("$loc/index.php");
}
if($_SESSION["usertable"]["usertype"] != "admin") {
    IntrusionNotify("admin/index.php");
    ForceLoad("$loc/index.php");
}

if ((isset($_GET["Submit1"]) && $_GET["Submit1"] == "Transfer") ||
    (isset($_GET["Submit3"]) && $_GET["Submit3"] == "Transfer scores")) {
    echo "<meta http-equiv=\"refresh\" content=\"60\" />";
}

if(!isset($_POST['noflush'])) {
    echo "</head>
    <body>
    <nav class=\"navbar navbar-expand-sm bg-transparent py-4\">
        <div class=\"container-fluid\">
            <div class=\"collapse navbar-collapse\" id=\"navbarScroll\">
                <ul class=\"navbar-nav me-auto my-2 my-lg-0 navbar-nav-scroll\" style=\"--bs-scroll-height: 100px;\">
                    <li class=\"nav-item\">
                        <a class=\"nav-link active\" aria-current=\"page\" href=\"index.php\">Home</a>
                    </li>
                    <li class=\"nav-item\">
                        <a class=\"nav-link active\" aria-current=\"page\" href=\"contest.php\">Contest</a>
                    </li>
                    <li class=\"nav-item\">
                        <a class=\"nav-link active\" aria-current=\"page\" href=\"run.php\">Runs</a>
                    </li>
                    <li class=\"nav-item dropdown\">
                        <a class=\"nav-link dropdown-toggle\" href=\"#\" role=\"button\" data-bs-toggle=\"dropdown\"
                           aria-expanded=\"false\">
                               More...
                        </a>
                        <ul class=\"dropdown-menu\">
                            <li><a class=\"dropdown-item dropdown\" href=\"score.php\">Score</a></li>
                            <li><a class=\"dropdown-item\" href=\"clar.php\">Clarifications</a></li>
                            <li><a class=\"dropdown-item\" href=\"user.php\">Users</a></li>
                            <li><a class=\"dropdown-item\" href=\"problem.php\">Problems</a></li>
                            <li><a class=\"dropdown-item\" href=\"language.php\">Languages</a></li>
                            <li><a class=\"dropdown-item\" href=\"answer.php\">Answers</a></li>
                            <li><a class=\"dropdown-item\" href=\"misc.php\">Misc</a></li>
                            <li><a class=\"dropdown-item\" href=\"task.php\">Tasks</a></li>
                            <li><a class=\"dropdown-item\" href=\"site.php\">Site</a></li>
                            <li><a class=\"dropdown-item\" href=\"log.php\">Logs</a></li>
                            <li><a class=\"dropdown-item\" href=\"report.php\">Reports</a></li>
                            <li><a class=\"dropdown-item\" href=\"files.php\">Backups</a></li>
                        </ul>
                    </li>
                </ul>
                <ul class=\"navbar-nav me-auto my-2 my-lg-0 navbar-nav-scroll \" style=\"--bs-scroll-height: 100px;\">
                    <li class=\"nav-item \">
                        <a class=\"navbar-brand navbar-center\" href=\"#\">B o c a</a>
                    </li>
                </ul>
                <ul class=\"navbar-nav mr-auto\">
                    <li class=\"nav-item dropdown\">
                        <a class=\"nav-link dropdown-toggle\" href=\"index.html\" role=\"button\" data-bs-toggle=\"dropdown\"
                           aria-expanded=\"false\">
                               Additional Options
    <svg width=\"1em\" height=\"1em\" viewBox=\"0 0 16 16\" class=\"bi bi-wrench\" fill=\"currentColor\"
                                 xmlns=\"http://www.w3.org/2000/svg\">
                                <path fill-rule=\"evenodd\"
                                      d=\"M.102 2.223A3.004 3.004 0 0 0 3.78 5.897l6.341 6.252A3.003 3.003 0 0 0 13 16a3 3 0 1 0-.851-5.878L5.897 3.781A3.004 3.004 0 0 0 2.223.1l2.141 2.142L4 4l-1.757.364L.102 2.223zm13.37 9.019L13 11l-.471.242-.529.026-.287.445-.445.287-.026.529L11 13l.242.471.026.529.445.287.287.445.529.026L13 15l.471-.242.529-.026.287-.445.445-.287.026-.529L15 13l-.242-.471-.026-.529-.445-.287-.287-.445-.529-.026z\"/>
                            </svg>
                        </a>
                        <ul class=\"dropdown-menu\" aria-labelledby=\"navbarDropdown2\">
                            <li><a class=\"dropdown-item\" href=\"option.php\">My Account</a></li>
                            <li><a class=\"dropdown-item\" href=\"$loc/index.php\">Logout</a></li>
                        </ul>
                    </li>
                </ul>
    
            </div>
        </div>
    </nav>";
}

?>
