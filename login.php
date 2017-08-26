<html>
<head>
<title>เข้าสู่ระบบ - ระบบเช็คยอดเงินธนาคาร</title>
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>
<link href="https://fonts.googleapis.com/css?family=Kanit" rel="stylesheet">
</head>
<body background="./images/bg.png">
  <font face="Kanit">
  <div class="container">
  <div class="row">
    <div class="col">
    </div>
    <div class="col-6"><br><br>
      <table>
        <tr>
          <th><center><img src="./images/bbl.png" width="50%" height="30%"></center></th>
          <th><center><img src="./images/kbank.png" width="50%" height="30%"></center></th>
          <th><center><img src="./images/ktb.png" width="50%" height="30%"></center></th>
          <th><center><img src="./images/scb.png" width="50%" height="30%"></center></th>
        </tr>
      </table><br>
      <center><h3>ระบบเช็คยอดเงินธนาคาร</h3></center><br>
      <form name="loginform" method="post" action="checklogin.php">
        <div class="form-group">
        <label for="InputUsername">ชื่อผู้ใช้</label>
        <input type="text" class="form-control" id="txtUsername" name="txtUsername" aria-describedby="emailHelp" placeholder="กรุณากรอกชื่อผู้ใช้" required>
      </div>
      <div class="form-group">
        <label for="InputPassword1">รหัสผ่าน</label>
        <input type="password" class="form-control" id="txtPassword" name="txtPassword" placeholder="กรุณากรอกรหัสผ่าน" required>
      </div>
        <br>
        <center><button type="submit" class="btn btn-primary">เข้าสู่ระบบ</button></center>
      </form>
    </div>
    <div class="col">
    </div>
  </div>

</body>
</font>
</html>
