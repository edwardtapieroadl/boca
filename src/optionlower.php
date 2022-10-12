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
// Last modified 05/aug/2012 by cassio@ime.usp.br

//optionlower.php: parte de baixo da tela de option.php, que eh igual para
//			todos os usuarios
require_once("globals.php");

if (!ValidSession()) { // || $_SESSION["usertable"]["usertype"] == 'team') {
    InvalidSession("optionlower.php");
    ForceLoad("index.php");
}
$loc = $_SESSION['loc'];

if (isset($_GET["username"]) && isset($_GET["userfullname"]) && isset($_GET["userdesc"]) &&
    isset($_GET["passwordo"]) && isset($_GET["passwordn"])) {
    $username = myhtmlspecialchars($_GET["username"]);
    $userfullname = myhtmlspecialchars($_GET["userfullname"]);
    $userdesc = myhtmlspecialchars($_GET["userdesc"]);
    $passwordo = $_GET["passwordo"];
    $passwordn = $_GET["passwordn"];
    DBUserUpdate($_SESSION["usertable"]["contestnumber"],
        $_SESSION["usertable"]["usersitenumber"],
        $_SESSION["usertable"]["usernumber"],
        $_SESSION["usertable"]["username"], // $username, but users should not change their names
        $userfullname,
        $userdesc,
        $passwordo,
        $passwordn);
    ForceLoad("option.php");
}

$a = DBUserInfo($_SESSION["usertable"]["contestnumber"],
    $_SESSION["usertable"]["usersitenumber"],
    $_SESSION["usertable"]["usernumber"]);

?>

<script language="JavaScript" src="<?php echo $loc; ?>/sha256.js" xmlns="http://www.w3.org/1999/html"></script>
<script language="JavaScript" src="<?php echo $loc; ?>/hex.js"></script>
<script language="JavaScript">
    function computeHASH() {
        var username, userdesc, userfull, passHASHo, passHASHn;
        if (document.form1.passwordn1.value != document.form1.passwordn2.value) return;
        if (document.form1.passwordn1.value == document.form1.passwordo.value) return;
        username = document.form1.username.value;
        userdesc = document.form1.userdesc.value;
        userfull = document.form1.userfull.value;

        passHASHo = js_myhash(js_myhash(document.form1.passwordo.value) + '<?php echo session_id(); ?>');
        passHASHn = bighexsoma(js_myhash(document.form1.passwordn2.value), js_myhash(document.form1.passwordo.value));
        document.form1.passwordo.value = '                                                         ';
        document.form1.passwordn1.value = '                                                         ';
        document.form1.passwordn2.value = '                                                         ';
        document.location = 'option.php?username=' + username + '&userdesc=' + userdesc + '&userfullname=' + userfull + '&passwordo=' + passHASHo + '&passwordn=' + passHASHn;
    }
</script>

<div class="main-content">
    <div class="container mt-7">

        <div class="row">
            <div class="col-xl-8 m-auto order-xl-1">
                <div class="card  shadow">
                    <div class="card-header bg-secondary border-0">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <br>
                                <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                                    <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                                    <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/>
                                </svg>
                                <br>
                                <br>
                                <h3 class="mb-0 user">My account</h3>
                            </div>

                        </div>
                    </div>

                    <div class="card-body">
                        <form>
                            <h6 class="heading-small text-muted mb-4">User information</h6>
                            <div class="pl-lg-4">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group focused">
                                            <label class="form-control-label" for="input-username">Username</label>
                                            <input id="input-username"
                                                   class="form-control form-control-alternative" placeholder="Username"
                                                   type="text" readonly name="username"
                                                   value="<?php echo $a["username"]; ?>" size="20"
                                                   maxlength="20"/>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group focused">
                                            <label class="form-control-label" for="input-first-name">User Full
                                                Name</label>
                                            <input id="input-first-name"
                                                   class="form-control form-control-alternative"
                                                   placeholder="First name"
                                                   type="text" readonly name="userfull"
                                                   value="<?php echo $a["userfullname"]; ?>"
                                                   size="50" maxlength="50"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr class="my-4">

                            <h6 class="heading-small text-muted mb-4">Security</h6>
                            <div class="pl-lg-4">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group focused">
                                            <label class="form-control-label" for="old-password">Old Password</label>
                                            <input id="old-password" class="form-control form-control-alternative"
                                                   type="password" name="passwordo" size="20" maxlength="200"/>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group focused">
                                            <label class="form-control-label" for="new-password">New Password</label>
                                            <input id="new-password" class="form-control form-control-alternative"
                                                   type="password" name="passwordn1" size="20" maxlength="200"/>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group focused">
                                            <label class="form-control-label" for="re-password">Retype New
                                                Password</label>
                                            <input id="new-password" class="form-control form-control-alternative"
                                                   type="password" name="passwordn2" size="20" maxlength="200"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr class="my-4">
                            <!-- Description -->
                            <h6 class="heading-small text-muted mb-4">About me</h6>
                            <div class="pl-lg-4">
                                <div class="form-group focused">
                                    <label for="about">User Description</label>

                                    <textarea id="about" type="text" name="userdesc"
                                              value="<?php echo $a["userdesc"]; ?>"
                                              size="50"
                                              rows="4" class="form-control form-control-alternative"
                                              maxlength="250"></textarea>

                                </div>
                            </div>
                            <br>
                            <div class="container pull-right">
                                    <button class="btn btn-lg btn-primary " type="submit" name="Submit"
                                            value="Send">Send
                                    </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
