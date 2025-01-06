// Keranjang belanja sebagai array
let cart = JSON.parse(localStorage.getItem("cart")) || [];

// Fungsi untuk memperbarui jumlah item di ikon keranjang
function updateCartCount() {
  const cartCount = document.getElementById("cart-count");
  cartCount.textContent = cart.length;
}

// Fungsi untuk menampilkan item di modal keranjang
function displayCartItems() {
  const cartItemsList = document.getElementById("cart-items-list");
  const cartItemsCount = document.getElementById("cart-items-count");

  // Kosongkan daftar item
  cartItemsList.innerHTML = "";

  cart.forEach((item, index) => {
    const itemElement = document.createElement("div");
    itemElement.classList.add("cart-item");
    itemElement.innerHTML = `
      <div>
        <img src="${item.image}" alt="${item.name}" style="width: 50px; height: 50px;" />
        <p>${item.name} - Rp ${item.price}</p>
        <p>Quantity: ${item.quantity}</p>
        <button onclick="removeFromCart(${index})">Remove</button>
      </div>
    `;
    cartItemsList.appendChild(itemElement);
  });

  // Perbarui jumlah item
  cartItemsCount.textContent = cart.length;
}

// Fungsi untuk menambahkan produk ke keranjang
function addToCart(event) {
  const productElement = event.target.closest(".product");
  const productName = productElement.querySelector("h3").textContent;
  const productPrice = productElement
    .querySelector(".price")
    .textContent.replace("Rp ", "")
    .replace(".", "");
  const productImage = productElement.querySelector("img").src;

  // Cari produk dalam keranjang
  const existingItem = cart.find((item) => item.name === productName);

  if (existingItem) {
    existingItem.quantity += 1; // Tambahkan kuantitas jika produk sudah ada
  } else {
    cart.push({
      name: productName,
      price: parseInt(productPrice),
      image: productImage,
      quantity: 1,
    });
  }

  // Simpan ke localStorage
  localStorage.setItem("cart", JSON.stringify(cart));

  // Perbarui tampilan
  updateCartCount();
  displayCartItems();

  // Tampilkan notifikasi setelah menambahkan produk ke keranjang
  showNotification();

  // Tampilkan alert saat produk ditambahkan ke keranjang
  alert("Product Successfully Added to Cart!");
}

// Fungsi untuk menghapus item dari keranjang
function removeFromCart(index) {
  cart.splice(index, 1);

  // Simpan perubahan ke localStorage
  localStorage.setItem("cart", JSON.stringify(cart));

  // Perbarui tampilan
  updateCartCount();
  displayCartItems();
}

// Fungsi untuk membuka modal
function openModal() {
  document.getElementById("cart-modal").style.display = "block";
  displayCartItems();
}

// Fungsi untuk menutup modal
function closeModal() {
  document.getElementById("cart-modal").style.display = "none";
}

// Pasang event listener ke tombol "Cart"
document.getElementById("cart-button").addEventListener("click", openModal);

// Pasang event listener ke semua tombol "Add to Cart"
document.querySelectorAll(".add-to-cart").forEach((button) => {
  button.addEventListener("click", addToCart); // Tambahkan produk ke keranjang dan tampilkan notifikasi
});

// Perbarui jumlah item saat halaman dimuat
document.addEventListener("DOMContentLoaded", updateCartCount);

// Ambil elemen yang dibutuhkan untuk notifikasi
const notification = document.getElementById("notification");

// Fungsi untuk menampilkan notifikasi
function showNotification() {
  // Tambahkan kelas .show untuk menampilkan notifikasi
  notification.classList.add("show");

  // Hapus kelas .show setelah 3 detik untuk menyembunyikan notifikasi
  setTimeout(() => {
    notification.classList.remove("show");
  }, 3000); // Menyembunyikan notifikasi setelah 3 detik
}
