<style>
.kyp-otp-box{
 padding:14px;
 border:1px solid #cbd8e8;
 border-radius:14px;
 background:#f8fbff
}
.kyp-otp-row{
 display:flex;
 gap:9px;
 align-items:center
}
.kyp-otp-row+.kyp-otp-row{
 margin-top:9px
}
.kyp-otp-row input{
 flex:1
}
.kyp-otp-status{
 margin-top:8px;
 font-size:12px;
 font-weight:750;
 color:#a16207
}
.kyp-otp-status.ok{color:#087443}
.kyp-otp-status.bad{color:#b42318}

@media(max-width:600px){
 .kyp-otp-row{
  align-items:stretch;
  flex-direction:column
 }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {

 document.querySelectorAll('.kyp-otp-box').forEach(function (box) {

  const email = box.querySelector('.kyp-otp-email');
  const send = box.querySelector('.kyp-send-otp');
  const area = box.querySelector('.kyp-code-area');
  const code = box.querySelector('.kyp-otp-code');
  const verify = box.querySelector('.kyp-verify-otp');
  const status = box.querySelector('.kyp-otp-status');

  let verifiedEmail = null;

  function message(text, type) {
   status.textContent = text;
   status.classList.remove('ok','bad');

   if (type) {
    status.classList.add(type);
   }
  }

  async function post(url, payload) {

   const response = await fetch(url, {
    method: 'POST',
    headers: {
     'Content-Type': 'application/json',
     'Accept': 'application/json',
     'X-CSRF-TOKEN':
      document.querySelector('meta[name="csrf-token"]')
       ?.getAttribute('content') ||
      document.querySelector('input[name="_token"]')
       ?.value
    },
    credentials: 'same-origin',
    body: JSON.stringify(payload)
   });

   const data = await response.json().catch(function () {
    return {};
   });

   if (!response.ok) {
    throw new Error(
     data.message ||
     Object.values(data.errors || {}).flat()[0] ||
     'Request failed.'
    );
   }

   return data;
  }

  send.addEventListener('click', async function () {

   if (!email.checkValidity()) {
    email.reportValidity();
    return;
   }

   send.disabled = true;
   message('OTP भेजा जा रहा है...');

   try {

    const data = await post(
     box.dataset.send,
     {
      email: email.value,
      purpose: box.dataset.purpose
     }
    );

    verifiedEmail = null;
    area.hidden = false;
    code.value = '';
    code.focus();

    message(
     data.message || 'OTP भेज दिया गया है।'
    );

   } catch (error) {

    message(error.message, 'bad');

   } finally {

    send.disabled = false;
   }
  });

  verify.addEventListener('click', async function () {

   if (!/^[0-9]{6}$/.test(code.value)) {
    message('6-digit OTP दर्ज करें।', 'bad');
    return;
   }

   verify.disabled = true;
   message('OTP verify किया जा रहा है...');

   try {

    const data = await post(
     box.dataset.verify,
     {
      email: email.value,
      purpose: box.dataset.purpose,
      otp: code.value
     }
    );

    verifiedEmail =
     email.value.trim().toLowerCase();

    email.readOnly = true;
    send.disabled = true;
    area.hidden = true;

    message(
     data.message || 'Email verified.',
     'ok'
    );

   } catch (error) {

    message(error.message, 'bad');

   } finally {

    verify.disabled = false;
   }
  });

  email.addEventListener('input', function () {

   if (
    verifiedEmail &&
    email.value.trim().toLowerCase() !== verifiedEmail
   ) {
    verifiedEmail = null;
    email.readOnly = false;
    send.disabled = false;
    message(
     'Email बदलने पर नया OTP verification आवश्यक है।',
     'bad'
    );
   }
  });

 });

});
</script>
