<?php
session_start();

// admin credentials
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', 'admin');

// database config
$db_host = "localhost";
$db_user = "limotakh_loan";
$db_pass = "YK5wnCeBYaZUaC9upf9T";
$db_name = "limotakh_loan";
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
$conn->set_charset("utf8mb4");

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin.php');
    exit;
}

// Handle login POST
if (isset($_POST['login'])) {
    $u = $_POST['username'] ?? '';
    $p = $_POST['password'] ?? '';
    if ($u === ADMIN_USER && $p === ADMIN_PASS) {
        $_SESSION['is_admin'] = true;
        header('Location: admin.php');
        exit;
    } else {
        $error = "نام کاربری یا رمز عبور اشتباه است!";
    }
}

// Handle delete request
if (isset($_GET['delete']) && $_SESSION['is_admin'] ?? false) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM loan_requests WHERE id = $id LIMIT 1");
    header('Location: admin.php');
    exit;
}

// Check if logged in
$logged_in = $_SESSION['is_admin'] ?? false;
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>پنل مدیریت درخواست وام</title>
  <link rel="stylesheet" href="https://cdn.fontcdn.ir/Font/Persian/Vazirmatn/Vazirmatn.css">
  <style>
    body { font-family: Vazir, Tahoma, sans-serif; background: #f7f8fd; }
    .login-box {
      background: #fff; max-width:340px; margin:60px auto 0; border-radius:17px;
      box-shadow:0 8px 32px 0 rgba(52,80,209,0.13); padding:36px 24px;
      text-align:right;
    }
    .btn-delete {
    background: #f00;
    color: #fff;
    border: none;
    border-radius: 6px;
    padding: 6px 15px;
    cursor: pointer;
    font-size: 14px;
    font-family: 'Vazir';
}
button {
    font-family: 'Vazir';
}
button.logout-btn {
    font-family: 'Vazir';
}
    .login-box h2 { font-size:23px; margin-bottom:23px; color:#184aaa; }
    .login-box input { font-family: inherit; font-size:15px; border-radius:8px; border:1px solid #dbe5fa; margin-bottom:15px; width:93%; padding:11px 10px; }
    .login-box button { width:100%; background:#3974ff; color:#fff; font-size:17px; border-radius:8px; padding:13px 0; border:none; cursor:pointer; font-weight:600; }
    .login-box .error { color: #e22; margin-bottom:12px; text-align: right; font-size:15px; }

    .admin-panel { max-width:900px; margin:40px auto 0; background:#fff; border-radius:15px; box-shadow:0 8px 32px 0 rgba(52,80,209,0.09); padding:35px 22px 34px 22px;}
    .admin-panel h1 { color:#1d356c; font-size:23px; margin-bottom:25px; }
    .requests-table { border-collapse: collapse; width: 100%; margin-top:25px;}
    .requests-table th, .requests-table td { border:1px solid #e4eaf2; padding: 8px 11px; text-align:center; font-size:14px;}
    .requests-table th { background:#f5f8fe; color:#3557ad; }
    .requests-table tr:hover { background:#f0f5ff;}
    .btn-delete { background:#f00; color:#fff; border:none; border-radius:6px; padding:6px 15px; cursor:pointer; font-size:14px;}
    .top-bar { display:flex; align-items:center; justify-content:space-between; margin-bottom:24px;}
    .logout-btn { background: none; border:none; color: #3974ff; font-weight:700; font-size:16px; cursor:pointer;}
    .welcome { color:#184aaa; font-size:17px; }

    
  </style>
</head>
<body>

<?php if (!$logged_in): ?>
  <form class="login-box" method="post" autocomplete="off">
    <h2>ورود ادمین</h2>
    <?php if (isset($error)): ?><div class="error"><?= $error ?></div><?php endif; ?>
    <input type="text" name="username" placeholder="نام کاربری" required>
    <input type="password" name="password" placeholder="رمز عبور" required>
    <div style="font-size:13px; color:#888; margin-bottom:13px;">یوزر و پسورد: <b>admin</b></div>
    <button type="submit" name="login">ورود</button>
  </form>
<?php else: ?>
  <div class="admin-panel">
    <div class="top-bar">
      <span class="welcome">محمدعلی احمدپور خوش آمدید!</span>
      <form method="get" style="display:inline;">
        <button type="submit" name="logout" value="1" class="logout-btn">خروج</button>
      </form>
    </div>
    <h1>لیست درخواست‌های وام</h1>
    <table class="requests-table">
      <thead>
        <tr>
          <th>کد</th>
          <th>نام</th>
          <th>موبایل</th>
          <th>مبلغ وام</th>
          <th>تعداد ماه</th>
          <th>کل بازپرداخت</th>
          <th>پیش‌پرداخت</th>
          <th>قسط ماهانه</th>
          <th>حداقل چک</th>
          <th>مبلغ هر چک</th>
          <th>تاریخ</th>
          <th>حذف</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $q = $conn->query("SELECT * FROM loan_requests ORDER BY id DESC");
        if ($q->num_rows > 0): foreach ($q as $row): ?>
          <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['mobile']) ?></td>
            <td><?= number_format($row['loan_amount']) ?></td>
            <td><?= $row['months'] ?></td>
            <td><?= number_format($row['total_repay']) ?></td>
            <td><?= number_format($row['prepay']) ?></td>
            <td><?= number_format($row['installment_value']) ?></td>
            <td><?= htmlspecialchars($row['min_cheque']) ?></td>
            <td><?= htmlspecialchars($row['each_cheque']) ?></td>
            <td><?= $row['created_at'] ?></td>
            <td>
              <form method="get" style="margin:0;">
                <input type="hidden" name="delete" value="<?= $row['id'] ?>">
                <button type="submit" class="btn-delete" onclick="return confirm('آیا مطمئن هستید؟')">حذف</button>
              </form>
            </td>
          </tr>
        <?php endforeach; else: ?>
          <tr><td colspan="12">هیچ درخواستی ثبت نشده است.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

</body>
</html>
