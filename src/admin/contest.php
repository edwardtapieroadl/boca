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
// Last modified 21/jul/2012 by cassio@ime.usp.br
require 'header.php';

$contest = $_SESSION["usertable"]["contestnumber"];

if (($ct = DBContestInfo($contest)) == null)
    ForceLoad("$loc/index.php");
$localsite = $ct["contestlocalsite"];
$mainsite = $ct["contestmainsite"];
if ($localsite == $mainsite) $main = true; else $main = false;

if ($main) {
    if (isset($_POST["SubmitDC"]) && $_POST["SubmitDC"] == "Delete ALL clars") {
        if ($_POST["confirmation"] == "confirm") {
            DBSiteDeleteAllClars($_SESSION["usertable"]["contestnumber"], -1,
                $_SESSION["usertable"]["usernumber"], $_SESSION["usertable"]["usersitenumber"]);
        }
        ForceLoad("contest.php");
    }
    if (isset($_POST["SubmitDR"]) && $_POST["SubmitDR"] == "Delete ALL runs") {
        if ($_POST["confirmation"] == "confirm") {
            DBSiteDeleteAllRuns($_SESSION["usertable"]["contestnumber"], -1,
                $_SESSION["usertable"]["usernumber"], $_SESSION["usertable"]["usersitenumber"]);
        }
        ForceLoad("contest.php");
    }
    if (isset($_POST["SubmitDT"]) && $_POST["SubmitDT"] == "Delete ALL tasks") {
        if ($_POST["confirmation"] == "confirm") {
            DBSiteDeleteAllTasks($_SESSION["usertable"]["contestnumber"], -1,
                $_SESSION["usertable"]["usernumber"], $_SESSION["usertable"]["usersitenumber"]);
        }
        ForceLoad("contest.php");
    }
    if (isset($_POST["SubmitDB"]) && $_POST["SubmitDB"] == "Delete ALL bkps") {
        if ($_POST["confirmation"] == "confirm") {
            DBSiteDeleteAllBkps($_SESSION["usertable"]["contestnumber"], -1,
                $_SESSION["usertable"]["usernumber"], $_SESSION["usertable"]["usersitenumber"]);
        }
        ForceLoad("contest.php");
    }
}

if (isset($_POST["Submit3"]) && isset($_POST["penalty"]) && is_numeric($_POST["penalty"]) &&
    isset($_POST["maxfilesize"]) && isset($_POST["mainsite"]) && isset($_POST["name"]) &&
    $_POST["name"] != "" && isset($_POST["lastmileanswer"]) && is_numeric($_POST["lastmileanswer"]) &&
    is_numeric($_POST["mainsite"]) && isset($_POST["lastmilescore"]) && is_numeric($_POST["lastmilescore"]) &&
    isset($_POST["duration"]) && is_numeric($_POST["duration"]) && isset($_POST['localsite']) &&
    isset($_POST["startdateh"]) && $_POST["startdateh"] >= 0 && $_POST["startdateh"] <= 23 &&
    isset($_POST["startdatemin"]) && $_POST["startdatemin"] >= 0 && $_POST["startdatemin"] <= 59 &&
    isset($_POST["startdated"]) && isset($_POST["startdatem"]) && isset($_POST["startdatey"]) &&
    checkdate($_POST["startdatem"], $_POST["startdated"], $_POST["startdatey"])) {
    if ($_POST["confirmation"] == "confirm") {
        $param['number'] = $contest;
        if ($_POST["Submit3"] == "Become Main Site") {
            $param['mainsite'] = $ct["contestlocalsite"];
        } else {
            $at = false;
            if (!is_numeric($_POST['localsite']) || $_POST['localsite'] <= 0) $_POST['localsite'] = -1;
            if ($_POST["Submit3"] == "Update Contest and All Sites") $at = true;
            $t = mktime($_POST["startdateh"], $_POST["startdatemin"], 0,
                $_POST["startdatem"], $_POST["startdated"], $_POST["startdatey"]);
            $param['localsite'] = $_POST['localsite'];
            $param['name'] = $_POST["name"];
            $param['startdate'] = $t;
            $param['duration'] = $_POST["duration"] * 60;
            $param['lastmileanswer'] = $_POST["lastmileanswer"] * 60;
            $param['lastmilescore'] = $_POST["lastmilescore"] * 60;
            $param['penalty'] = $_POST["penalty"] * 60;
            $param['maxfilesize'] = $_POST["maxfilesize"] * 1000;
            $param['active'] = 0;
            $param['mainsite'] = $_POST["mainsite"];
            $param['mainsiteurl'] = $_POST["mainsiteurl"];
            $param['unlockkey'] = $_POST["unlockkey"];

            if (isset($_FILES["keyfile"]) && $_FILES["keyfile"]["name"] != "") {
                $type = myhtmlspecialchars($_FILES["keyfile"]["type"]);
                $size = myhtmlspecialchars($_FILES["keyfile"]["size"]);
                $name = myhtmlspecialchars($_FILES["keyfile"]["name"]);
                $temp = myhtmlspecialchars($_FILES["keyfile"]["tmp_name"]);
                if (!is_uploaded_file($temp)) {
                    IntrusionNotify("file upload problem.");
                    ForceLoad("../index.php");
                }
                if (($ar = file($temp)) === false) {
                    IntrusionNotify("Unable to open the uploaded file.");
                    ForceLoad("user.php");
                }
                $dd = 0;
                foreach ($ar as $val => $key) {
                    $key = trim($key);
                    if ($key == '') {
                        unset($ar[$val]);
                        continue;
                    }
                    if (substr($key, 10, 5) != '#####') {
                        MSGError('Invalid key in the file -- not importing any keys');
                        $dd = 0;
                        break;
                    }
                    if (isset($param['unlockkey']) && $param['unlockkey'] != '') {
                        $pass = decryptData(substr($key, 15), $param['unlockkey'], 'includekeys');
                        if (substr($pass, 0, 5) != '#####') {
                            MSGError('Invalid key in the file -- not importing any keys');
                            $dd = 0;
                            break;
                        }
                    }
                    $ar[$val] = $key;
                    $dd++;
                }
                if ($dd > 0) {
                    $param['keys'] = implode(',', $ar);
                    MSGError(count($ar) . ' keys are being imported from the file');
                    DBClearProblemTmp($_SESSION["usertable"]["contestnumber"]);
                }
            }
            $param['atualizasites'] = $at;
        }
        DBUpdateContest($param);
        if (strlen($param['unlockkey']) > 1) {
            DBClearProblemTmp($_SESSION["usertable"]["contestnumber"]);
            DBGetFullProblemData($_SESSION["usertable"]["contestnumber"], true);
        }
    }
    if (($ct = DBContestInfo($contest)) == null)
        ForceLoad("$loc/index.php");
    if ($ct["contestlocalsite"] != $localsite || $mainsite != $ct["contestmainsite"])
        ForceLoad("$loc/index.php");
    ForceLoad("contest.php");
}
?>

<div style="width:70%; margin: 5% auto">

    <div class="alert alert-primary" role="alert">
        Your PHP config. allows at
        most: <?php echo ini_get('post_max_size') . 'B(max. post) and ' . ini_get('upload_max_filesize') . 'B(max. filesize)'; ?>
    </div>
    <div class="alert alert-success" role="alert">
        <?php echo ini_get('session.gc_maxlifetime') . 's of session expiration and ' . ini_get('session.cookie_lifetime') . ' as cookie lifetime (0 means unlimited)'; ?>
    </div>
    <div class="row">
        <br>

        <form class="row g-3 needs-validation" name="form1" enctype="multipart/form-data" method="post"
              action="contest.php" novalidate>
            <input type=hidden name="confirmation" value="noconfirm"/>
            <script language="javascript">
                function conf() {
                    if (confirm("Confirm?")) {
                        document.form1.confirmation.value = 'confirm';
                    }
                }

                function conf2() {
                    if (confirm("This will restart all start/stop related information in all the sites.\n\
If you have a contest running, the result is unpredictable. Are you really sure?")) {
                        document.form1.confirmation.value = 'confirm';
                    }
                }

                function conf3() {
                    if (confirm("This will make your site become the main site, that is, this site will\n\
play an active position in the contest regarding the information\n\
flow. ARE YOU SURE?")) {
                        document.form1.confirmation.value = 'confirm';
                    }
                }
            </script>
            <div class="col-md-4">
                <label for="validationCustom01" class="form-label">Contest number</label>
                <input type="number" class="form-control" id="validationCustom01" value="<?php echo $contest; ?>"
                       disabled>
            </div>
            <div class="col-md-4">
                <label for="validationCustom02" class="form-label">Name</label>
                <input class="form-control" id="validationCustom02" type="text" <?php if (!$main) echo "readonly"; ?>
                       name="name" value="<?php echo $ct["contestname"]; ?>" size="50" maxlength="50"/>
            </div>
            <div class="col-md-4">
                <div class="row">
                    <label for="validationCustomUsername" class="form-label">Start Date</label>

                    <div class="col-md-2">
                        <input class="form-control" placeholder="hh" type="text" <?php if (!$main) echo "readonly"; ?>
                               name="startdateh" value="<?php echo date("H", $ct["conteststartdate"]); ?>" size="2"
                               maxlength="2"/>
                    </div>
                    :
                    <div class="col-md-2">
                        <input class="form-control" placeholder="mm" type="text" <?php if (!$main) echo "readonly"; ?>
                               name="startdatemin" value="<?php echo date("i", $ct["conteststartdate"]); ?>" size="2"
                               maxlength="2"/>
                    </div>
                    <div class="col-md-2">
                        <input class="form-control" placeholder="dd" type="text" <?php if (!$main) echo "readonly"; ?>
                               name="startdated" value="<?php echo date("d", $ct["conteststartdate"]); ?>" size="2"
                               maxlength="2"/>

                    </div>
                    /
                    <div class="col-md-2">
                        <input class="form-control" placeholder="mm" type="text" <?php if (!$main) echo "readonly"; ?>
                               name="startdatem" value="<?php echo date("m", $ct["conteststartdate"]); ?>" size="2"
                               maxlength="2"/>

                    </div>
                    /
                    <div class="col-md-3">
                        <input class="form-control" placeholder="yyyy" type="text" <?php if (!$main) echo "readonly"; ?>
                               name="startdatey" value="<?php echo date("Y", $ct["conteststartdate"]); ?>" size="4"
                               maxlength="4"/>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <label for="validationCustom03" class="form-label">Duration (in minutes)</label>
                <input class="form-control" id="validationCustom03" type="text"
                       name="duration" <?php if (!$main) echo "readonly"; ?>
                       value="<?php echo $ct["contestduration"] / 60; ?>" size="20" maxlength="20"/>
            </div>
            <div class="col-md-3">
                <label for="validationCustom04" class="form-label">Stop answering (in minutes)</label>
                <input class="form-control" id="validationCustom04" type="text"
                       name="lastmileanswer" <?php if (!$main) echo "readonly"; ?>
                       value="<?php echo $ct["contestlastmileanswer"] / 60; ?>" size="20" maxlength="20"/>
            </div>
            <div class="col-md-3">
                <label for="validationCustom05" class="form-label">Stop scoreboard (in minutes)</label>
                <input class="form-control" id="validationCustom05" type="text"
                       name="lastmilescore" <?php if (!$main) echo "readonly"; ?>
                       value="<?php echo $ct["contestlastmilescore"] / 60; ?>" size="20" maxlength="20"/>
            </div>
            <div class="col-md-3">
                <label for="validationCustom06" class="form-label">Penalty (in minutes)</label>
                <input class="form-control" id="validationCustom06" type="text"
                       name="penalty" <?php if (!$main) echo "readonly"; ?>
                       value="<?php echo $ct["contestpenalty"] / 60; ?>" size="20" maxlength="20"/>
            </div>
            <div class="col-md-4">
                <label for="validationCustom07" class="form-label">Max file size allowed for teams (in KB)</label>
                <input class="form-control" id="validationCustom07" type="text"
                       name="maxfilesize" <?php if (!$main) echo "readonly"; ?>
                       value="<?php echo $ct["contestmaxfilesize"] / 1000; ?>" size="20" maxlength="20"/>
            </div>
            <div class="col-md-4">
                <label for="validationCustom08" class="form-label">Main site URL (IP/bocafolder)</label>
                <input class="form-control" id="validationCustom08" type="text" name="mainsiteurl"
                       value="<?php echo $ct["contestmainsiteurl"]; ?>" size="40"
                       maxlength="200"/>
            </div>
            <?php
            $exd = explode(' ', $ct["contestmainsiteurl"]);
            if (count($exd) >= 4 && is_numeric($exd[3]) && $exd[3] > 0)
                echo "<tr><td width=\"35%\" align=right>Last update from mainsite:</td><td width=\"65%\">" . dateconv($exd[3]) . "</td></tr>\n";
            ?>
            <div class="col-md-4">
                <label for="validationCustom09" class="form-label">Unlock password (only use it within a <b>secure
                        network</b>)</label>
                <input class="form-control" id="validationCustom09" type="password" name="unlockkey" value="" size="40"
                       maxlength="200"/>
                <?php if (strlen($ct["contestunlockkey"]) > 1) echo "<b><= has been set</b>"; ?>
            </div>
            <div class="col-md-4">
                <label for="validationCustom10" class="form-label">Keys (only use it within a <b>secure
                        network</b>)</label>
                <input class="form-control" id="validationCustom10" type="file" name="keyfile" size="40">
                <?php if (strlen($ct["contestkeys"]) > 32) echo "<b><= has been set</b>"; ?>

            </div>
            <div class="col-md-4">
                <label for="validationCustom11" class="form-label">Contest main site number</label>
                <input class="form-control" id="validationCustom11" type="text"
                       name="mainsite" <?php if (!$main) echo "readonly"; ?>
                       value="<?php echo $ct["contestmainsite"]; ?>" size="4" maxlength="4"/>
            </div>
            <div class="col-md-4">
                <label for="validationCustom12" class="form-label">Contest local site number</label>
                <input class="form-control" id="validationCustom12" type="text"
                       name="localsite" <?php if (!$main) echo "readonly"; ?>
                       value="<?php echo $ct["contestlocalsite"]; ?>" size="4" maxlength="4"/>
            </div>

            <div style="margin-top:7%;" class="col-12 text-center">
                <?php if ($main) { ?>
                    <button class="btn btn-outline-dark" type="submit" name="Submit3" value="Update" onClick="conf()">
                        Update
                    </button>
                    <button class="btn btn-outline-dark" type="submit" name="Submit3"
                            value="Update Contest and All Sites"
                            onClick="conf2()">
                        Update All
                    </button>
                    <button class="btn btn-outline-dark" type="reset" name="Submit4" value="Clear">Clear</button>
                    <br><br>
                    <button class="btn btn-dark" type="submit" name="SubmitDC" value="Delete ALL clars"
                            onClick="conf2()">Delete ALL clars
                    </button>
                    <button class="btn btn-dark" type="submit" name="SubmitDR" value="Delete ALL runs"
                            onClick="conf2()">Delete ALL runs
                    </button>
                    <button class="btn btn-dark" type="submit" name="SubmitDT" value="Delete ALL tasks"
                            onClick="conf2()">Delete ALL tasks
                    </button>
                    <button class="btn btn-dark" type="submit" name="SubmitDB" value="Delete ALL bkps"
                            onClick="conf2()">Delete ALL bkps
                    </button>
                <?php } else { ?>
                    <button class="btn btn-outline-dark" type="submit" name="Submit3" value="Update" onClick="conf()">
                        Update
                    </button>
                    <button class="btn btn-outline-dark" type="submit" name="Submit3" value="Become Main Site"
                            onClick="conf3()">Become Main Site
                    </button>
                <?php } ?>
            </div>
        </form>
    </div>
</div>
</body>
</html>
