// ===== EBOOK STORE - MAIN SCRIPT =====
let selectedAddressId = null;
// ================= ADDRESS SYSTEM =================

// INIT
document.addEventListener("DOMContentLoaded", () => {

    showStep(1);
    loadCheckoutCart();
    if(document.getElementById("addr-line1")){
        renderSavedAddresses();
    }
    const saved = window.savedAddresses || [];

    if (saved.length > 0) {
        document.getElementById('saved-address-container').style.display = 'block';
        document.getElementById('new-address-form').style.display = 'none';
    } else {
        document.getElementById('new-address-form').style.display = 'block';
    }

});
function showEmptyCheckoutState() {

    const container = document.getElementById("checkout-items");

    container.innerHTML = `
        <div style="text-align:center; padding:40px;">
            <h5>Your cart is empty</h5>
            <p style="color:#aaa; margin-top:10px;">
                You will be redirected shortly.
            </p>
            <p style="margin-top:10px;">
                Redirecting in <strong id="empty-timer">5</strong> seconds...
            </p>
        </div>
    `;

    // hide payment section
    document.getElementById('section-personal').style.display = "none";
    document.getElementById('section-address').style.display = "none";
    document.getElementById('section-payment').style.display = "none";

    startEmptyRedirect();
}
function startEmptyRedirect() {
    let seconds = 5;

    const el = document.getElementById("empty-timer");

    const interval = setInterval(() => {
        seconds--;

        if (el) el.innerText = seconds;

        if (seconds <= 0) {
            clearInterval(interval);
            window.location.href = "/ebook";
        }
    }, 1000);
}
function startAutoRedirect() {
    let seconds = 12;

    const timerEl = document.getElementById("redirect-timer");

    const interval = setInterval(() => {
        seconds--;

        if (timerEl) {
            timerEl.innerText = seconds;
        }

        if (seconds <= 0) {
            clearInterval(interval);
            window.location.href = "/";
        }

    }, 1000);
}
// RENDER SAVED
function renderSavedAddresses() {

    const container = document.getElementById("saved-address-container");
    const list = document.getElementById("saved-address-list");

    if (!window.savedAddresses || window.savedAddresses.length === 0) {
        container.style.display = "none";
        return;
    }

    container.style.display = "block";
    list.innerHTML = "";

    window.savedAddresses.forEach((addr) => {

        const div = document.createElement("div");
        div.className = "address-card p-3 rounded";
        div.style.cursor = "pointer";
        div.style.border = "1px solid #444";
        div.style.transition = "0.2s";

        div.innerHTML = `
            <div><strong>${addr.type.toUpperCase()}</strong></div>
            <div>${addr.line1}</div>
            <div>${addr.line2 ?? ''}</div>
            <div>${addr.city}, ${addr.state} - ${addr.pincode}</div>
        `;

        div.onclick = () => selectAddress(addr.id);

        list.appendChild(div);
    });

    // ✅ SELECT FIRST ADDRESS (ONLY HIGHLIGHT, NO FILL)
    selectAddress(window.savedAddresses[0].id);

    // ✅ CLEAR FORM ALWAYS (IMPORTANT)
    clearAddress();
}
function selectAddress(id) {

    selectedAddressId = id;

    document.querySelectorAll(".address-card").forEach(el => {
        el.style.border = "1px solid #444";
    });

    const index = window.savedAddresses.findIndex(a => a.id === id);
    const selectedCard = document.querySelectorAll(".address-card")[index];

    if (selectedCard) {
        selectedCard.style.border = "2px solid #00c853";
    }
}

// CLEAR
function clearAddress(){
    document.getElementById('addr-line1').value = "";
    document.getElementById('addr-line2').value = "";
    document.getElementById('city').value = "";
    document.getElementById('state').value = "";
    document.getElementById('pincode').value = "";
}
function toggleAddressForm() {

    const form = document.getElementById('new-address-form');
    const btn = document.querySelector('.address-action-bar button');

    const isHidden = window.getComputedStyle(form).display === "none";

    if (isHidden) {
        form.style.display = 'block';
        btn.innerText = "Cancel";
    } else {
        form.style.display = 'none';
        btn.innerText = "+ Add New Address";
    }
}
// SAVE / UPDATE
function saveAddress(){
let type = document.getElementById("address-type").value;

if(!type){
    showToast("Select address type", "error");
    return;
}
    let data = {
        line1: document.getElementById('addr-line1').value,
        line2: document.getElementById('addr-line2').value,
        city: document.getElementById('city').value,
        state: document.getElementById('state').value,
        pincode: document.getElementById('pincode').value,
        type: type,
        country: document.getElementById('country').value
    };

    if(!data.line1 || !data.city || !data.state || data.pincode.length !== 6){
        showToast("Fill address properly", "error");
        return;
    }

    fetch(BASE_URL + "/save-address", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
        },
        body: new URLSearchParams(data) // ✅ IMPORTANT FIX
    })
    .then(res => res.text())
.then(data => {
    console.log("RESPONSE:", data);
    try {
        let json = JSON.parse(data);

	// update global
	window.savedAddresses = json.addresses;

	// re-render
	renderSavedAddresses();

	// auto select last added
	selectAddress(json.addresses[json.addresses.length - 1].id);
	toggleAddressForm()

	showToast("Address saved");
    } catch(e){
        console.error("NOT JSON:", data);
    }
});
}
// VALIDATION BEFORE ORDER
function validateCheckout(){

    if(!selectedAddressId){
    showToast("Please select address", "error");
    return false;
}

    return true;
}

let currentStep = 1;

// SHOW STEP
function showStep(step){

    document.querySelectorAll('.checkout-card').forEach(el => {
        el.classList.remove('active');
    });

    if(step === 1){
        document.getElementById('section-personal').classList.add('active');
    }
    if(step === 2){
        document.getElementById('section-address').classList.add('active');
    }
    if(step === 3){
        document.getElementById('section-payment').classList.add('active');
    }

    currentStep = step;
}

function goToAddress(btn){
if (btn) setButtonLoading(btn, "Loading...");
    let valid = true;

    valid &= validateField('first-name','err-first-name', v => v.trim().length >= 2);
    valid &= validateField('last-name','err-last-name', v => v.trim().length >= 2);
    valid &= validateField('email','err-email', v => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v));
    valid &= validateField('phone','err-phone', v => v.replace(/\D/g,'').length >= 10);

    if(!valid){
        showToast("Fill personal details properly","error");
        return;
    }
    
    setTimeout(() => {

        if (btn) resetButton(btn);

        document.getElementById('step-address').classList.add('active');
        document.getElementById('div-2').classList.add('done');

        showStep(2);

    }, 400);

    // mark step UI
   // document.getElementById('step-address').classList.add('active');
   // document.getElementById('div-2').classList.add('done');

   // showStep(2);
}

function goToPayment(btn){
if (btn) setButtonLoading(btn, "Loading...");
    if(!selectedAddressId){
        showToast("Please select address", "error");
        return;
    }
setTimeout(() => {

        if (btn) resetButton(btn);
    document.getElementById('step-payment').classList.add('active');
    document.getElementById('div-2').classList.add('done');

    showStep(3);
    }, 400);
}
function validateField(id, errId, condition) {
    const el = document.getElementById(id);
    const err = document.getElementById(errId);
    if (!el || !err) return true;
    if (!condition(el.value)) {
      el.classList.add('error');
      err.classList.add('show');
      return false;
    }
    el.classList.remove('error');
    err.classList.remove('show');
    return true;
  }

  //function validateForm() {
 //   let ok = true;
  //  ok = validateField('first-name','err-first-name', v => v.trim().length >= 2) && ok;
  //  ok = validateField('last-name','err-last-name', v => v.trim().length >= 2) && ok;
  //  ok = validateField('email','err-email', v => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)) && ok;
   // ok = validateField('phone','err-phone', v => v.replace(/\D/g,'').length >= 10) && ok;
  //  ok = validateField('addr-line1','err-addr-line1', v => v.trim().length >= 5) && ok;
  //  ok = validateField('city','err-city', v => v.trim().length >= 2) && ok;
  //  ok = validateField('state','err-state', v => v !== '') && ok;
  //  ok = validateField('pincode','err-pincode', v => /^\d{6}$/.test(v.trim())) && ok;

    // Payment validation
   // if (selectedPayment === 'upi') {
    //  ok = validateField('upi-id','err-upi', v => /^[\w\.\-]+@[\w]+$/.test(v.trim())) && ok;
   // } else if (selectedPayment === 'card') {
   //   ok = validateField('card-number','err-card-number', v => v.replace(/\D/g,'').length === 16) && ok;
  //    ok = validateField('card-name','err-card-name', v => v.trim().length >= 3) && ok;
  //    ok = validateField('card-expiry','err-card-expiry', v => /^\d{2}\s\/\s\d{2}$/.test(v.trim())) && ok;
   //   ok = validateField('card-cvv','err-card-cvv', v => /^\d{3,4}$/.test(v.trim())) && ok;
  //  }
   // if (!ok) {
    //  const firstErr = document.querySelector('.co-input.error');
    //  if (firstErr) firstErr.scrollIntoView({ behavior: 'smooth', block: 'center' });
   // }
    //return ok;
  //}

  // Place Order
function startRazorpayPayment(btn) {
    if (btn && btn.disabled) return; // ✅ prevent double click

    if (btn) setButtonLoading(btn, "Processing...");

    if (!selectedAddressId) {
    	if (btn) resetButton(btn);
        showToast("Please select address", "error");
        return;
    }

    let total = document.getElementById('co-total').innerText.replace('₹','');

    fetch(BASE_URL + "/create-order", {
        method: "POST",
        credentials: "include",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            amount: total,
            address_id: selectedAddressId
        })
    })
    .then(res => res.json())
    .then(data => {

        
        var options = {
            key: RAZORPAY_KEY,
            amount: data.amount,
            currency: "INR",
            name: "BookVault",
            description: "Ebook Purchase",
            order_id: data.order_id,
modal: {
        ondismiss: function () {
            if (btn) resetButton(btn);
            showToast("Payment cancelled", "error");
        }
    },
            handler: function (response) {

               
                fetch(BASE_URL + "/verify-payment", {
                    method: "POST",
                    credentials: "include",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
    razorpay_order_id: response.razorpay_order_id,
    razorpay_payment_id: response.razorpay_payment_id,
    razorpay_signature: response.razorpay_signature,
    address_id: selectedAddressId,
    amount: document.getElementById('co-total').innerText.replace('₹',''),
    cart: JSON.parse(localStorage.getItem('ebookCart'))
})
                })
                .then(res => res.json())
                .then(res => {
console.log("VERIFY RESPONSE:", res);
                    if (res.success) {
			document.getElementById('success-order-id').innerText = "#BV-" + res.order_id;
                        showToast("Payment Successful ");
                        document.getElementById('success-overlay').classList.add('show');
                        startAutoRedirect();

                    } else {
                        showToast("Payment verification failed", "error");
                    }
                    if (btn) resetButton(btn);
                })
                .catch(() => {
                    if (btn) resetButton(btn);
                    showToast("Something went wrong", "error");
                });
            },

            theme: {
                color: "#00c853"
            }
        };

        var rzp = new Razorpay(options);

// ✅ ADD THIS BLOCK HERE
rzp.on('payment.failed', function (response) {

    fetch(BASE_URL + "/verify-payment", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            razorpay_order_id: response.error.metadata.order_id,
            razorpay_payment_id: null,
            razorpay_signature: null,
            address_id: selectedAddressId,
        })
    });
 if (btn) resetButton(btn);
    showToast("Payment Failed ❌", "error");
});

rzp.open();

    })
    .catch(() => {
        if (btn) resetButton(btn);
        showToast("Unable to initiate payment", "error");
    });
}
function loadCheckoutCart() {

    fetch(BASE_URL + "/cart/data")
    .then(res => res.json())
    .then(data => {
        const cart = data.cart;

    if (Object.keys(cart).length === 0) {

    showEmptyCheckoutState();

    return;
}
        const container = document.getElementById("checkout-items");
        const subtotalEl = document.getElementById("co-subtotal");
        const taxEl = document.getElementById("co-tax");
        let totalEl = document.getElementById('co-total');

if(!totalEl){
    showToast("Cart not loaded", "error");
    return;
}


        if(!container) return;

        container.innerHTML = "";

        let subtotal = 0;

        Object.keys(cart).forEach(id => {

            let item = cart[id];

            subtotal += item.price * item.quantity;

            container.innerHTML += `
                <div class="order-item">
                    <div class="order-item-cover">
                        <img src="${item.image}" style="width:100%;height:100%;object-fit:cover">
                    </div>

                    <div>
                        <div class="order-item-name">${item.name}</div>
                        <div class="order-item-author">${item.description ?? ''}</div>
                    </div>

                    <div class="order-item-price">
                        ₹${item.price}
                    </div>
                </div>
            `;
        });

        let tax = subtotal * 0.18;
        let total = subtotal + tax;

        subtotalEl.innerText = "₹" + subtotal;
        taxEl.innerText = "₹" + tax;
        totalEl.innerText = "₹" + total;
    });
}

