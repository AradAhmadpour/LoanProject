<?php
// database config
$db_host = "localhost";
$db_user = "limotakh_loan";
$db_pass = "YK5wnCeBYaZUaC9upf9T";
$db_name = "limotakh_loan";

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
$conn->set_charset("utf8mb4");

// اگر درخواست ajax هست و متد POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_loan'])) {
    $name = $_POST['name'] ?? '';
    $mobile = $_POST['mobile'] ?? '';
    $loan_amount = intval($_POST['loan_amount'] ?? 0);
    $months = intval($_POST['months'] ?? 0);
    $total_repay = intval($_POST['total_repay'] ?? 0);
    $prepay = intval($_POST['prepay'] ?? 0);
    $installment_value = intval($_POST['installment_value'] ?? 0);
    $min_cheque = $_POST['min_cheque'] ?? '';
    $each_cheque = $_POST['each_cheque'] ?? '';

    // ذخیره در دیتابیس
    $stmt = $conn->prepare("INSERT INTO loan_requests
      (name, mobile, loan_amount, months, total_repay, prepay, installment_value, min_cheque, each_cheque)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiiiisss", $name, $mobile, $loan_amount, $months, $total_repay, $prepay, $installment_value, $min_cheque, $each_cheque);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'fail', 'error' => $stmt->error]);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
  <head>
    <meta charset="UTF-8" />
    <title>خرید اقساطی PB360</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <link rel="stylesheet" href="style.css" />
  </head>
  <body>
    <!-- Header -->
    <header class="main-header">
      <div class="container">
        <div class="header-content">
          <div class="logo-box">
            <img src="./Files/Logo.png" alt="لوگو" class="logo-img" />
          </div>
          <nav class="main-menu">
            <a href="#calculator" class="menu-link">محاسبه‌گر</a>
            <a href="#conditions" class="menu-link">شرایط اقساطی</a>
            <a href="#account-importance" class="menu-link">اهمیت گردش حساب</a>
            <a href="#faq" class="menu-link">سوالات متداول</a>
          </nav>
        </div>
      </div>
    </header>

    <!-- Hero Section -->
    <div class="section-bg">
      <div class="container">
        <section class="hero-box">
          <div class="hero-info">
            <div class="hero-title">خرید اقساطی از پی بی ۳۶۰</div>
            <ul>
              <li>تا سقف ۵۰ میلیون تومان اعتبار خرید اقساطی دارم</li>
              <li>۲۵ درصد ارزش کل کالا را پیش پرداخت می‌دهم</li>
              <li>چک صیادی و دیجیتال مورد پذیرش است</li>
              <li>اقساط می‌تواند تا ۱۲ ماه شناور باشد</li>
              <li>تنها ۳.۵ درصد سود پرداخت می‌کنم</li>
              <li>بدون نیاز به ضامن و با چک ضمانت</li>
            </ul>
          </div>
          <div class="hero-img-box">
            <img src="./Files/banner.png" alt="بنر" class="hero-img" />
          </div>
        </section>
      </div>
    </div>

    <!-- Calculator Section -->
    <div id="calculator" class="calc-section">
      <div class="container">
        <h2 class="section-title">محاسبه‌گر وام خرید کالا</h2>
        <div class="calc-box">
          <form id="loanForm" class="loan-calc-form" autocomplete="off">
            <div class="form-group sliders-row">
              <div>
                <label for="installments">تعداد اقساط</label>
                <input
                  type="range"
                  id="installments"
                  min="2"
                  max="12"
                  step="1"
                  value="7"
                />
                <div class="slider-value">
                  <span id="installmentsLabel">۸</span> ماه
                </div>
              </div>
              <div>
                <label for="loanAmount">مبلغ درخواست وام</label>
                <input
                  type="range"
                  id="loanAmount"
                  min="10000000"
                  max="50000000"
                  step="1000000"
                  value="30000000"
                />
                <div class="slider-value">
                  <span id="loanAmountLabel">۳۰</span> میلیون تومان
                </div>
              </div>
            </div>

            <div class="calc-fields">
              <div>
                <div class="field-label">کل مبلغ بازپرداخت</div>
                <div id="totalRepay" class="field-value">—</div>
              </div>
              <div>
                <div class="field-label">مبلغ هر قسط</div>
                <div id="installmentValue" class="field-value">—</div>
              </div>
              <div>
                <div class="field-label">پیش‌پرداخت (۲۵٪)</div>
                <div id="prepayValue" class="field-value">—</div>
              </div>
              <div>
                <div class="field-label">حداقل چک مورد قبول</div>
                <div id="minCheque" class="field-value">۳ چک</div>
              </div>
              <div class="col-span-2">
                <div class="field-label">مبلغ هر چک</div>
                <div id="chequeValue" class="field-value">—</div>
              </div>
            </div>

            <button type="button" class="btn-main">درخواست وام</button>
          </form>
        </div>
      </div>
    </div>

    <!-- شرایط اقساطی -->
    <div id="conditions" class="white-section">
      <div class="container">
        <h2 class="section-title">شرایط اقساطی خرید کالا</h2>
        <div class="features-row">
          <div class="feature-card">
            <img src="./Files/s4.svg" alt="نیاز به چک ضامن نیست" />
            <div class="feature-title">نیاز به چک ضامن نیست</div>
          </div>
          <div class="feature-card">
            <img
              src="./Files/s3.svg"
              alt="فروش اقساط از حداقل ۳ ماه تا حداکثر ۱۲ ماه"
            />
            <div class="feature-title">
              فروش اقساط از حداقل ۳ ماه تا حداکثر ۱۲ ماه
            </div>
          </div>
          <div class="feature-card">
            <img
              src="./Files/s2.svg"
              alt="نیاز به ضامن و یا گواهی کسر از حقوق نمی‌باشد."
            />
            <div class="feature-title">
              نیاز به ضامن و یا گواهی کسر از حقوق نمی‌باشد.
            </div>
          </div>
          <div class="feature-card">
            <img
              src="./Files/s1.svg"
              alt="دارا بودن حساب جاری و دسته چک صیادی"
            />
            <div class="feature-title">دارا بودن حساب جاری و دسته چک صیادی</div>
          </div>
        </div>
        <div class="feature-desc">
          شما از این پس می‌توانید از شرایط فروش اقساط انواع کالا بدون ضامن و
          تحویل سریع استفاده کنید. خریداران می‌توانند با پرداخت حداقل ۲۵ درصد
          (٪۲۵) قیمت محصول به صورت پیش پرداخت و پرداخت الباقی به صورت اقساط ۳ تا
          ۱۲ ماهه اقدام به خرید نمایند. هیچ سند و یا چکی بابت ضمانت از شما
          دریافت نمی‌گردد و حتی شما با چک شخصی و بدون نیاز به چک کارمندی
          می‌توانید از تسهیلات خرید اقساطی فروشگاه بهره‌مند گردید.
          <br /><br />
          تایید شرایط خرید اقساطی به این صورت است که پس از استعلام و بررسی
          مدارک، نتیجه به اطلاع شما خواهد رسید و در صورت مثبت بودن استعلام شما
          می‌توانید خرید خود را انجام داده و کالای خود را تحویل بگیرید و وجه آن
          را به صورت اقساط پرداخت نمایید. انتخاب کالا مناسب بستگی مستقیم به
          بودجه افراد، سن، جنسیت و نحوه استفاده از کالا دارد. در نتیجه مشتریانی
          زیادی در انتخاب کالای مورد نیاز خود دچار شک و تردید می‌شوند. برای کسب
          اطلاعات بیشتر و مشاوره با همکاران ما در بخش فروش اقساط تماس حاصل
          فرمایید.
        </div>
      </div>
    </div>

    <!-- اهمیت گردش حساب -->
    <div id="account-importance" class="section-bg">
      <div class="container">
        <div class="account-row">
          <div class="account-img">
            <img src="./Files/account-importance.svg" alt="گردش حساب" />
          </div>
          <div class="account-info">
            <h2 class="section-title ahmiatgardesh-title">اهمیت گردش حساب</h2>
            <div class="account-desc">
              سیستم اعتباری ممکن است صلاحیت مشتری را تایید نماید ولی با توجه به
              حجم مانده و گردش حساب، مبلغ پیش پرداخت را افزایش دهد.
              <br />
              معیار تایید اعتبار خریداران از سوی پی‌بی۳۶۰:
              <br />
              به طور معمول معیار سیستم اعتباری عدم وجود چک برگشتی، گردش حساب
              مناسب و میانگین مانده حساب می‌باشد.
              <br />
              ذکر این نکته ضروریست که نیازی نیست حساب معرفی شده با حسابی که چک
              صیادی به نام شما صادر می‌شود یکسان باشد. گردش و مانده حساب در سایر
              حساب‌های شخص نیز مورد قبول است.
            </div>
          </div>
        </div>
        <div class="account-warning">
          به این علت که ممکن است مراحل تایید حساب شما ۲۴ الی ۴۸ ساعت به طول
          بینجامد، قیمت لحظه‌ای برای شما در نظر گرفته خواهد شد.
        </div>
      </div>
    </div>

    <!-- سوالات متداول -->
    <div id="faq" class="white-section">
      <div class="container">
        <h2 class="section-title">سوالات متداول</h2>
        <div class="faq-list">
          <div class="faq-item">
            <div class="faq-q">
              آیا امکان پیش خرید لپ تاپ ها (موجودی انبار چین) به صورت اقساطی
              وجود دارد؟
            </div>
            <div class="faq-a">
              بله، امکان پیش خرید لپ تاپ‌ها با شرایط اقساطی وجود دارد.
            </div>
          </div>
          <div class="faq-item">
            <div class="faq-q">
              آیا محدودیتی از بابت خرید چند محصول و یا محدودیت مبلغ فاکتور وجود
              دارد؟
            </div>
            <div class="faq-a">
              خیر، محدودیتی در تعداد محصول یا مبلغ فاکتور وجود ندارد.
            </div>
          </div>
          <div class="faq-item">
            <div class="faq-q">
              آیا امکان خرید اقساطی برای مشتریان خارج از شهر تهران نیز وجود دارد
              ؟
            </div>
            <div class="faq-a">
              بله، ساکنین سراسر کشور می‌توانند به صورت اقساطی خرید کنند.
            </div>
          </div>
          <div class="faq-item">
            <div class="faq-q">
              پس از تایید اولیه در مرحله ی ارسال چک آیا نیاز است به صورت حضوری
              به آدرس فروشگاه مراجعه کنیم ؟
            </div>
            <div class="faq-a">
              در بیشتر مواقع نیازی به مراجعه حضوری نیست و روند ارسال چک‌ها
              هماهنگ می‌شود.
            </div>
          </div>
          <div class="faq-item">
            <div class="faq-q">
              پس از نوشتن چک ها در هنگام ثبت چک در سامانه صیاد چک‌ها را به چه
              نامی ثبت کنیم ؟
            </div>
            <div class="faq-a">
              چک‌ها باید به نام فروشگاه یا شرکت صادرکننده ثبت شوند.
            </div>
          </div>
          <div class="faq-item">
            <div class="faq-q">
              آیا امکان دارد فرد ثبت نام کننده در سایت با صاحب آورنده چک یکی
              نباشد ؟
            </div>
            <div class="faq-a">
              ترجیحاً اطلاعات فرد ثبت‌نام‌کننده و صاحب چک باید یکسان باشد.
            </div>
          </div>
        </div>
        <div class="faq-bottom">
          <div class="faq-contact">
            سوالی داری؟
            <span class="faq-phone">۰۲۱-۶۷۳۹۶۰۰۰ داخلی (۱۰۱)</span>
          </div>
          <a href="#calculator" class="btn-main">تکمیل فرم و شروع خرید</a>
        </div>
      </div>
    </div>
    <div class="loan-modal-bg">
      <form class="loan-modal" id="loanModalForm" autocomplete="off">
        <label for="userName">نام شما</label>
        <input type="text" id="userName" required />
        <label for="userMobile">شماره موبایل</label>
        <input
          type="tel"
          id="userMobile"
          required
          placeholder="مثلاً ۰۹۹۳۵۱۵۱۱۸۷"
          pattern="^09\d{9}$"
          maxlength="11"
        />
        <div class="error-message" id="loanModalError"></div>
        <div class="modal-actions">
          <button type="submit">ثبت درخواست</button>
          <button type="button" data-close>انصراف</button>
        </div>
      </form>
    </div>

    <script src="main.js"></script>
  </body>
  <!-- Footer -->
<footer class="footer">
  <div class="container">
    <p>پروژه دانشگاه آزاد اسلامی واحد رشت | استاد راهنما: دکتر اعظم عندلیب | دانشجو: محمدعلی احمدپور دلچه</p>
  </div>
</footer>
</html>