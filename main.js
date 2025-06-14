// ==================
// Accordion FAQ
// ==================
document.querySelectorAll('.faq-item').forEach(function (item) {
  item.addEventListener('click', function () {
    document.querySelectorAll('.faq-item').forEach(function (i) {
      if (i !== item) i.classList.remove('open');
    });
    item.classList.toggle('open');
  });
});

// ==================
// Menu Highlight on Scroll
// ==================
const menuLinks = document.querySelectorAll('.menu-link');
window.addEventListener('scroll', function () {
  let scrollY = window.scrollY;
  let ids = ['calculator', 'conditions', 'account-importance', 'faq'];
  let found = false;
  ids.forEach(function (id, idx) {
    let el = document.getElementById(id);
    if (el && scrollY + 120 >= el.offsetTop) {
      menuLinks.forEach(link => link.classList.remove('active'));
      menuLinks[idx].classList.add('active');
      found = true;
    }
  });
  if (!found) menuLinks.forEach(link => link.classList.remove('active'));
});

// ==================
// Number to Toman & Persian
// ==================
function toToman(n) {
  if (isNaN(n)) return "—";
  return n.toLocaleString('fa-IR') + " تومان";
}
function toFaNum(str) {
  if (typeof str === "number") str = str.toString();
  return str.replace(/[0-9]/g, (d) => "۰۱۲۳۴۵۶۷۸۹"[d]);
}

// ==================
// Interest & Cheque Logic
// ==================
function getInterest(months) {
  if (months <= 3) return 0.04;
  if (months <= 5) return 0.035;
  if (months <= 9) return 0.032;
  return 0.03;
}
function getMinCheque(months) {
  if (months <= 6) return 3;
  if (months <= 9) return 4;
  return 6;
}

// ==================
// Calculator Logic
// ==================
function updateCalculator() {
  const amount = parseInt(document.getElementById('loanAmount').value);
  const months = parseInt(document.getElementById('installments').value);

  // مقدار نمایش عددی
  document.getElementById('loanAmountLabel').innerText = toFaNum((amount / 1000000));
  document.getElementById('installmentsLabel').innerText = toFaNum(months);

  // محاسبه اقساط و سود و پیش‌پرداخت
  const prePay = Math.round(amount * 0.25);
  const loanUsed = amount - prePay;
  const monthlyRate = getInterest(months);
  const totalInterest = Math.round(loanUsed * monthlyRate * months);
  const totalRepay = loanUsed + totalInterest;
  const installmentValue = Math.ceil(totalRepay / months);
  const minCheque = getMinCheque(months);
  const chequeValue = Math.ceil(totalRepay / minCheque);

  // مقداردهی و ذخیره data-value برای ارسال به سرور
  document.getElementById('prepayValue').innerText = toToman(prePay);
  document.getElementById('prepayValue').setAttribute('data-value', prePay);

  document.getElementById('totalRepay').innerText = toToman(totalRepay + prePay);
  document.getElementById('totalRepay').setAttribute('data-value', totalRepay + prePay);

  document.getElementById('installmentValue').innerText = toToman(installmentValue);
  document.getElementById('installmentValue').setAttribute('data-value', installmentValue);

  document.getElementById('minCheque').innerText = toFaNum(minCheque + ' چک');
  document.getElementById('chequeValue').innerText = toFaNum(minCheque) + " چک × " + toToman(chequeValue);
  document.getElementById('chequeValue').setAttribute('data-value', chequeValue);
}
document.getElementById('loanAmount').addEventListener('input', updateCalculator);
document.getElementById('installments').addEventListener('input', updateCalculator);

// مقداردهی اولیه
updateCalculator();

// ==================
// پاپ‌آپ و ذخیره در دیتابیس
// ==================
document.querySelector('.loan-calc-form .btn-main').addEventListener('click', function (e) {
  e.preventDefault();
  document.querySelector('.loan-modal-bg').classList.add('active');
});

// بستن پاپ‌آپ
document.querySelector('.loan-modal [data-close]').addEventListener('click', function () {
  document.querySelector('.loan-modal-bg').classList.remove('active');
});

// ثبت نهایی درخواست و ارسال AJAX
document.getElementById('loanModalForm').addEventListener('submit', function (e) {
  e.preventDefault();
  let name = document.getElementById('userName').value.trim();
  let mobile = document.getElementById('userMobile').value.trim();
  let error = "";

  if (!name) error = "لطفاً نام را وارد کنید.";
  else if (!/^09\d{9}$/.test(mobile)) error = "شماره موبایل صحیح نیست. (مثلاً: ۰۹۹۳۵۱۵۱۱۸۷)";

  document.getElementById('loanModalError').innerText = error;
  if (error) return;

  // اطلاعات وام (از فیلدهای محاسبه‌گر و data-value)
  let loan_amount = parseInt(document.getElementById('loanAmount').value);
  let months = parseInt(document.getElementById('installments').value);
  let total_repay = parseInt(document.getElementById('totalRepay').getAttribute('data-value'));
  let prepay = parseInt(document.getElementById('prepayValue').getAttribute('data-value'));
  let installment_value = parseInt(document.getElementById('installmentValue').getAttribute('data-value'));
  let min_cheque = document.getElementById('minCheque').innerText;
  let each_cheque = document.getElementById('chequeValue').innerText;

  // ارسال به سرور
  let formData = new FormData();
  formData.append('save_loan', 1);
  formData.append('name', name);
  formData.append('mobile', mobile);
  formData.append('loan_amount', loan_amount);
  formData.append('months', months);
  formData.append('total_repay', total_repay);
  formData.append('prepay', prepay);
  formData.append('installment_value', installment_value);
  formData.append('min_cheque', min_cheque);
  formData.append('each_cheque', each_cheque);

  fetch('index.php', {
    method: 'POST',
    body: formData
  })
    .then(res => res.json())
    .then(data => {
      if (data.status === 'success') {
        alert('درخواست شما با موفقیت ثبت شد.کارشناسان پس از بررسی درخواست شما با شما ارتباط خواهند گرفت.');
        document.querySelector('.loan-modal-bg').classList.remove('active');
        document.getElementById('loanModalForm').reset();
      } else {
        alert('مشکلی رخ داد! دوباره امتحان کنید.');
      }
    })
    .catch(() => alert('مشکل ارتباط با سرور!'));
});




// باز کردن پاپ‌آپ با کلیک
document.getElementById('admin-login-link').addEventListener('click', function (e) {
  e.preventDefault();
  document.getElementById('adminModalBg').classList.add('active');
  document.getElementById('adminUser').focus();
});

// بستن با دکمه انصراف
document.getElementById('adminLoginCancel').addEventListener('click', function (e) {
  e.preventDefault();
  document.getElementById('adminModalBg').classList.remove('active');
  document.getElementById('adminLoginError').innerText = "";
  document.getElementById('adminLoginForm').reset();
});

// بستن با کلیک روی بک‌گراند (اختیاری)
document.getElementById('adminModalBg').addEventListener('click', function (e) {
  if (e.target === this) {
    this.classList.remove('active');
    document.getElementById('adminLoginError').innerText = "";
    document.getElementById('adminLoginForm').reset();
  }
});


