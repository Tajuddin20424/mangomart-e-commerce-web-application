
let cart = [];
let isLoggedIn = false;

// 1. Toast message function.
function showToast(msg) {
    const toast = document.getElementById('toast');
    toast.innerText = msg;
    toast.style.display = 'block';
    setTimeout(() => toast.style.display = 'none', 2500);
}

// 2. Cart update UI
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

// 3. Remove from cart
window.removeFromCart = function (idx) {
    cart.splice(idx, 1);
    updateCartUI();
    showToast("Item removed from cart");
};

// 4. Add to cart
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

// 5. Login and Register Modal Logic (The Solution)
const loginModal = document.getElementById('loginModal');
const registerModal = document.getElementById('registerModal');

// Button Select
const openLoginBtn = document.getElementById('openLoginBtn');
const openRegisterBtn = document.getElementById('openRegisterBtn');

// Close Button Select
const closeModal = document.getElementById('closeModal');
const closeRegisterModal = document.getElementById('closeRegisterModal');

// Open Function
if(openLoginBtn) openLoginBtn.onclick = () => loginModal.style.display = 'flex';
if(openRegisterBtn) openRegisterBtn.onclick = () => registerModal.style.display = 'flex';

// Close Function (Cross Button Fix)
if(closeModal) closeModal.onclick = () => loginModal.style.display = 'none';
if(closeRegisterModal) closeRegisterModal.onclick = () => registerModal.style.display = 'none';

// The modal will close if clicked outside
window.onclick = (e) => { 
    if (e.target === loginModal) loginModal.style.display = 'none'; 
    if (e.target === registerModal) registerModal.style.display = 'none'; 
};

// Login button action
document.getElementById('doLoginBtn').onclick = () => {
    const email = document.getElementById('loginEmail').value;
    const pwd = document.getElementById('loginPassword').value;
    if (email.trim() !== "" && pwd.trim() !== "") {
        isLoggedIn = true;
        showToast(`🎉 Welcome! You are now logged in.`);
        loginModal.style.display = 'none';
        openLoginBtn.innerHTML = '<i class="fas fa-user"></i> Logged In';
        openLoginBtn.style.background = "#27AE60";
    } else {
        showToast("❌ Please enter email and password");
    }
};

// Register button action (Register Fix)
document.getElementById('doRegisterBtn').onclick = () => {
    const name = document.getElementById('regName').value;
    const email = document.getElementById('regEmail').value;
    const pwd = document.getElementById('regPassword').value;

    if (name.trim() !== "" && email.trim() !== "" && pwd.trim() !== "") {
        showToast(`✅ Registration Successful for ${name}!`);
        registerModal.style.display = 'none';
        // Automatic login (optional)
        isLoggedIn = true;
        openLoginBtn.innerHTML = '<i class="fas fa-user"></i> Logged In';
    } else {
        showToast("❌ Please fill all the fields");
    }
};

// 6. Cart sidebar control
const cartSidebar = document.getElementById('cartSidebar');
const openCartBtn = document.getElementById('openCartBtn');
const closeCart = document.getElementById('closeCart');

if(openCartBtn) {
    openCartBtn.onclick = () => {
        if (!isLoggedIn) {
            showToast("⚠️ Please login first!");
            loginModal.style.display = 'flex';
            return;
        }
        cartSidebar.classList.add('open');
    };
}
if(closeCart) closeCart.onclick = () => cartSidebar.classList.remove('open');

// 7. Checkout
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

// 8. Mobile menu toggle
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

// 9. Navigation and Scroll Highlight
document.addEventListener('DOMContentLoaded', () => {
    const sections = document.querySelectorAll("section, footer, div[id]");
    const navLinks = document.querySelectorAll(".nav-links li a");

    window.addEventListener("scroll", () => {
        let current = "";
        sections.forEach((section) => {
            const sectionTop = section.offsetTop;
            if (pageYOffset >= sectionTop - 120) {
                current = section.getAttribute("id");
            }
        });
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
                if (navList.classList.contains('active')) {
                    navList.classList.remove('active');
                    mobileMenu.querySelector('i').classList.replace('fa-times', 'fa-bars');
                }
            }
        });
    });
});

// 10. Product add to cart event
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

// 11. Other buttons
document.getElementById('shopNowScrollBtn').onclick = () => document.getElementById('shop').scrollIntoView({ behavior: 'smooth' });


// new

