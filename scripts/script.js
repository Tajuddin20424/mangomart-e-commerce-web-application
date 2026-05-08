/**
 * MangoMart - script.js
 * Optimized & Fixed by Md. Tajuddin
 */

let cart = [];
let isLoggedIn = false;

// ১. টোস্ট মেসেজ ফাংশন
function showToast(msg) {
    const toast = document.getElementById('toast');
    toast.innerText = msg;
    toast.style.display = 'block';
    setTimeout(() => toast.style.display = 'none', 2500);
}

// ২. কার্ট আপডেট ইউআই
function updateCartUI() {
    const cartItemsDiv = document.getElementById('cartItems');
    const cartTotalSpan = document.getElementById('cartTotal');
    const cartCountSpan = document.getElementById('cartCount');
    let total = 0;

    if (cart.length === 0) {
        cartItemsDiv.innerHTML = '<p style="text-align:center; color:#9B7B5C;">Your cart is empty</p>';
        cartTotalSpan.innerText = 'Total: BDT 0';
        cartCountSpan.innerText = '0';
        return;
    }

    let html = '';
    cart.forEach((item, idx) => {
        total += item.price;
        html += `<div class="cart-item">
            <div><strong>${item.name}</strong><br>BDT ${item.price}/kg</div>
            <button style="background:#C0392B; color:white; border:none; padding:4px 12px; border-radius:20px; cursor:pointer;" onclick="removeFromCart(${idx})">Remove</button>
        </div>`;
    });
    cartItemsDiv.innerHTML = html;
    cartTotalSpan.innerText = `Total: BDT ${total}`;
    cartCountSpan.innerText = cart.length;
}

// ৩. কার্ট থেকে রিমুভ
window.removeFromCart = function (idx) {
    cart.splice(idx, 1);
    updateCartUI();
    showToast("Item removed from cart");
};

// ৪. কার্টে অ্যাড করা
function addToCart(name, price) {
    if (!isLoggedIn) {
        showToast("⚠️ Please login first to add items to cart!");
        document.getElementById('loginModal').style.display = 'flex';
        return;
    }
    cart.push({ name, price: parseInt(price) });
    updateCartUI();
    showToast(`✅ ${name} added to cart!`);
}

// ৫. লগইন সিস্টেম
const loginModal = document.getElementById('loginModal');
const openLoginBtn = document.getElementById('openLoginBtn');
const closeModal = document.getElementById('closeModal');
const doLoginBtn = document.getElementById('doLoginBtn');

if(openLoginBtn) openLoginBtn.onclick = () => loginModal.style.display = 'flex';
if(closeModal) closeModal.onclick = () => loginModal.style.display = 'none';

window.onclick = (e) => { 
    if (e.target === loginModal) loginModal.style.display = 'none'; 
};

doLoginBtn.onclick = () => {
    const email = document.getElementById('loginEmail').value;
    const pwd = document.getElementById('loginPassword').value;
    if (email.trim() !== "" && pwd.trim() !== "") {
        isLoggedIn = true;
        showToast(`🎉 Welcome! You are now logged in.`);
        loginModal.style.display = 'none';
        openLoginBtn.innerText = "👤 Logged In";
        openLoginBtn.style.background = "#E67E22";
        openLoginBtn.style.color = "white";
    } else {
        showToast("❌ Please enter email and password");
    }
};

// ৬. কার্ট সাইডবার কন্ট্রোল
const cartSidebar = document.getElementById('cartSidebar');
const openCartBtn = document.getElementById('openCartBtn');
const closeCart = document.getElementById('closeCart');

openCartBtn.onclick = () => {
    if (!isLoggedIn) {
        showToast("⚠️ Please login first!");
        loginModal.style.display = 'flex';
        return;
    }
    cartSidebar.classList.add('open');
};
if(closeCart) closeCart.onclick = () => cartSidebar.classList.remove('open');

// ৭. চেকআউট
document.getElementById('checkoutBtn').onclick = () => {
    if (cart.length === 0) {
        showToast("🛒 Your cart is empty!");
        return;
    }
    showToast("🎉 Order placed successfully!");
    cart = [];
    updateCartUI();
    cartSidebar.classList.remove('open');
};

// ৮. মোবাইল মেনু টগল
const mobileMenu = document.getElementById('mobile-menu');
const navList = document.getElementById('nav-list');

mobileMenu.addEventListener('click', () => {
    navList.classList.toggle('active');
    const icon = mobileMenu.querySelector('i');
    if (navList.classList.contains('active')) {
        icon.classList.replace('fa-bars', 'fa-times');
    } else {
        icon.classList.replace('fa-times', 'fa-bars');
    }
});

// ৯. ন্যাভিগেশন এবং স্ক্রল হাইলাইট (Fix for Contact)
document.addEventListener('DOMContentLoaded', () => {
    const sections = document.querySelectorAll("section, footer, div[id]");
    const navLinks = document.querySelectorAll(".nav-links li a");

    // স্ক্রল হাইলাইট লজিক
    window.addEventListener("scroll", () => {
        let current = "";
        
        sections.forEach((section) => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.clientHeight;
            if (pageYOffset >= sectionTop - 120) {
                current = section.getAttribute("id");
            }
        });

        // পেজের একদম নিচে পৌঁছালে Contact হাইলাইট হবে
        if ((window.innerHeight + window.pageYOffset) >= document.body.offsetHeight - 5) {
            current = "contact";
        }

        navLinks.forEach((link) => {
            link.classList.remove("active");
            if (link.getAttribute("href").includes(current)) {
                link.classList.add("active");
            }
        });
    });

    // স্মুথ স্ক্রলিং এবং মোবাইল মেনু ক্লোজ
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            if (this.hash !== "") {
                e.preventDefault();
                const target = document.querySelector(this.hash);
                if(target) {
                    window.scrollTo({
                        top: target.offsetTop - 80,
                        behavior: 'smooth'
                    });
                }
                // মোবাইল মেনু বন্ধ করা
                if (navList.classList.contains('active')) {
                    navList.classList.remove('active');
                    mobileMenu.querySelector('i').classList.replace('fa-times', 'fa-bars');
                }
            }
        });
    });
});

// ১০. প্রোডাক্ট অ্যাড টু কার্ট ইভেন্ট
document.querySelectorAll('.add-to-cart').forEach(btn => {
    btn.addEventListener('click', (e) => {
        e.stopPropagation();
        const card = btn.closest('.variety-card');
        const name = card.querySelector('h3').innerText;
        const priceText = card.querySelector('strong').innerText;
        const price = parseInt(priceText.replace('BDT', '').replace('/kg', ''));
        addToCart(name, price);
    });
});

// ১১. অন্যান্য বাটন লজিক
document.getElementById('shopNowScrollBtn').onclick = () => document.getElementById('shop').scrollIntoView({ behavior: 'smooth' });
document.getElementById('watchFarmBtn').onclick = () => window.open('https://www.youtube.com/watch?v=MyH34_Gvn-Y', '_blank');