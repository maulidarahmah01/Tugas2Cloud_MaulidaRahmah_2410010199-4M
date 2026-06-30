<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Bakuliner Kalsel - Kuliner & Budaya Kalimantan Selatan 🍜</title>
  <meta
    name="description"
    content="Temukan kuliner khas, pakaian adat, dan souvenir Kalimantan Selatan di Bakuliner Kalsel!" />
  <link
    href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css"
    rel="stylesheet" />
  <style>
    * {
      box-sizing: border-box;
    }

    /* Custom Color Palette */
    :root {
      --dark-green: #043915;
      --medium-green: #4c763b;
      --light-green: #b0ce88;
      --light-yellow: #fffd8f;
    }

    .gradient-hero {
      background: linear-gradient(135deg, #043915 0%, #4c763b 100%);
    }

    .bg-dark-green {
      background-color: #043915;
    }

    .bg-medium-green {
      background-color: #4c763b;
    }

    .bg-light-green {
      background-color: #b0ce88;
    }

    .bg-light-yellow {
      background-color: #fffd8f;
    }

    .text-dark-green {
      color: #043915;
    }

    .text-medium-green {
      color: #4c763b;
    }

    .text-light-green {
      color: #b0ce88;
    }

    .border-medium-green {
      border-color: #4c763b;
    }

    .card-hover {
      transition: all 0.3s ease;
      border: 2px solid transparent;
    }

    .card-hover:hover {
      transform: translateY(-8px);
      box-shadow: 0 20px 40px rgba(4, 57, 21, 0.2);
      border-color: #4c763b;
    }

    .category-badge {
      display: inline-block;
      padding: 8px 20px;
      border-radius: 25px;
      font-size: 0.875rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      border: 2px solid #4c763b;
      background: white;
      color: #4c763b;
    }

    .category-badge:hover {
      transform: scale(1.05);
      background: #b0ce88;
      color: #043915;
    }

    .category-badge.active {
      background: linear-gradient(135deg, #4c763b 0%, #043915 100%);
      color: white;
      border-color: #043915;
    }

    .loading-spinner {
      border: 4px solid #f3f4f6;
      border-top: 4px solid #4c763b;
      border-radius: 50%;
      width: 50px;
      height: 50px;
      animation: spin 1s linear infinite;
      margin: 40px auto;
    }

    @keyframes spin {
      0% {
        transform: rotate(0deg);
      }

      100% {
        transform: rotate(360deg);
      }
    }

    .hero-pattern {
      background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23B0CE88' fill-opacity='0.15'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }

    .rating-stars {
      color: #fffd8f;
      text-shadow: 0 0 3px rgba(0, 0, 0, 0.3);
    }

    .search-input {
      border: 3px solid #4c763b;
    }

    .search-input:focus {
      outline: none;
      border-color: #043915;
      box-shadow: 0 0 0 4px rgba(76, 118, 59, 0.1);
    }

    .btn-primary {
      background: linear-gradient(135deg, #4c763b 0%, #043915 100%);
      color: white;
      transition: all 0.3s ease;
    }

    .btn-primary:hover {
      transform: scale(1.05);
      box-shadow: 0 10px 20px rgba(4, 57, 21, 0.3);
    }

    .stat-badge {
      background: linear-gradient(135deg, #fffd8f 0%, #b0ce88 100%);
      color: #043915;
      font-weight: bold;
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 0.75rem;
    }

    .reko-card {
      background: linear-gradient(135deg, #b0ce88 0%, #fffd8f 100%);
    }

    @media (max-width: 640px) {
      .hero-title {
        font-size: 2rem;
      }
    }
  </style>
  <script>
    let allData = [];
    let currentCategory = 0;
    let currentSearch = "";
    let categories = [];

    async function getCategories() {
      try {
        const res = await fetch("api.php?action=categories");
        const result = await res.json();
        categories = result.data || [];
        return categories;
      } catch (error) {
        console.error("Error fetching categories:", error);
        return [];
      }
    }

    async function getData() {
      try {
        const res = await fetch("api.php");
        const result = await res.json();
        allData = result.data || result;
        return allData;
      } catch (error) {
        console.error("Error fetching data:", error);
        return [];
      }
    }

    function filterData() {
      let filtered = allData;

      if (currentCategory > 0) {
        filtered = filtered.filter(
          (item) => item.kategori_id == currentCategory
        );
      }

      if (currentSearch) {
        filtered = filtered.filter(
          (item) =>
          item.nama.toLowerCase().includes(currentSearch.toLowerCase()) ||
          (item.lokasi &&
            item.lokasi
            .toLowerCase()
            .includes(currentSearch.toLowerCase())) ||
          (item.deskripsi &&
            item.deskripsi
            .toLowerCase()
            .includes(currentSearch.toLowerCase())) ||
          (item.nama_kategori &&
            item.nama_kategori
            .toLowerCase()
            .includes(currentSearch.toLowerCase()))
        );
      }

      return filtered;
    }

    function renderStars(rating) {
      const fullStars = Math.floor(rating);
      const hasHalfStar = rating % 1 >= 0.5;
      let stars = "";

      for (let i = 0; i < fullStars; i++) {
        stars += "⭐";
      }
      if (hasHalfStar) {
        stars += "⭐";
      }

      return stars;
    }

    function renderKuliner() {
      const filtered = filterData();
      const list = document.getElementById("kuliner-list");

      if (filtered.length === 0) {
        list.innerHTML = `
        <div class="col-span-full text-center py-16">
          <div class="text-7xl mb-4">🔍</div>
          <h3 class="text-2xl font-bold text-dark-green mb-2">Tidak Ada Hasil</h3>
          <p class="text-gray-600">Coba kata kunci atau kategori lain</p>
        </div>
      `;
        return;
      }

      list.innerHTML = filtered
        .map((item) => {
          const categoryIcons = {
            "Makanan Berat": "🍛",
            "Kue Tradisional": "🍰",
            "Pakaian Khas Daerah Kalsel": "👕",
            Souvenir: "🎁",
          };
          const icon = categoryIcons[item.nama_kategori] || "📦";

          return `
      <div class="card-hover bg-white rounded-2xl overflow-hidden shadow-lg">
        <div class="relative">
          <img src="${item.gambar}" alt="${item.nama}" 
               class="h-56 w-full object-cover"
               onerror="this.src='https://via.placeholder.com/400x300/4C763B/ffffff?text=${encodeURIComponent(
                 item.nama
               )}'">
          <div class="absolute top-4 right-4 bg-dark-green px-3 py-2 rounded-full shadow-lg">
            <span class="rating-stars font-bold text-sm">${renderStars(
              parseFloat(item.rating || 0)
            )} ${item.rating || 0}</span>
          </div>
          <div class="absolute top-4 left-4 bg-light-yellow text-dark-green px-3 py-2 rounded-full text-xs font-bold shadow-lg border-2 border-medium-green">
            ${icon} ${item.nama_kategori || "Lainnya"}
          </div>
        </div>
        <div class="p-5 bg-gradient-to-b from-white to-gray-50">
          <h3 class="text-xl font-bold text-dark-green mb-2 line-clamp-1">${
            item.nama
          }</h3>
          <p class="text-medium-green font-semibold mb-3 flex items-center gap-1">
            <span>📍</span> ${item.lokasi || "Kalimantan Selatan"}
          </p>
          <p class="text-gray-700 text-sm mb-3 line-clamp-2">${
            item.deskripsi || ""
          }</p>
          <div class="flex items-center justify-between mt-4 pt-3 border-t border-light-green">
            <span class="stat-badge">${item.nama_kategori || "Item"}</span>
            <span class="text-medium-green font-bold text-lg">${
              item.rating || 0
            }/5</span>
          </div>
        </div>
      </div>
    `;
        })
        .join("");

      document.getElementById("result-count").textContent = filtered.length;
    }

    async function renderRekomendasi() {
      const data = await getData();
      const top = data
        .filter((d) => parseFloat(d.rating || 0) >= 4.7)
        .slice(0, 4);
      const list = document.getElementById("rekomendasi-list");

      if (top.length === 0) {
        list.innerHTML =
          '<p class="text-center text-gray-500 col-span-full">Belum ada rekomendasi</p>';
        return;
      }

      list.innerHTML = top
        .map(
          (item) => `
      <div class="card-hover reko-card rounded-2xl overflow-hidden shadow-xl border-2 border-medium-green">
        <div class="relative">
          <img src="${item.gambar}" 
               class="h-44 w-full object-cover"
               onerror="this.src='https://via.placeholder.com/400x200/4C763B/ffffff?text=${encodeURIComponent(
                 item.nama
               )}'">
          <div class="absolute top-3 right-3 bg-dark-green px-3 py-1 rounded-full shadow-lg">
            <span class="rating-stars font-bold text-xs">${renderStars(
              parseFloat(item.rating || 0)
            )}</span>
          </div>
        </div>
        <div class="p-4">
          <h4 class="font-bold text-dark-green text-lg mb-2 line-clamp-1">${
            item.nama
          }</h4>
          <p class="text-sm text-medium-green mb-3 font-medium">📍 ${
            item.lokasi || "Kalsel"
          }</p>
          <div class="flex items-center justify-between pt-2 border-t-2 border-medium-green">
            <span class="text-xs font-semibold text-dark-green bg-white px-2 py-1 rounded">${
              item.nama_kategori || "Item"
            }</span>
            <span class="text-dark-green font-bold text-xl">${
              item.rating || 0
            }</span>
          </div>
        </div>
      </div>
    `
        )
        .join("");
    }

    function setCategory(categoryId) {
      currentCategory = categoryId;

      document.querySelectorAll(".category-badge").forEach((badge) => {
        badge.classList.remove("active");
      });

      event.target.classList.add("active");

      renderKuliner();
    }

    function handleSearch(event) {
      currentSearch = event.target.value.trim();
      renderKuliner();
    }

    async function init() {
      document.getElementById("kuliner-list").innerHTML =
        '<div class="loading-spinner col-span-full"></div>';

      const cats = await getCategories();
      await getData();
      await renderRekomendasi();
      renderKuliner();

      const categoryList = document.getElementById("category-list");
      const categoryIcons = {
        Makanan: "🍛",
        Kue: "🍰",
        Pakaian: "👕",
        Souvenir: "🎁",
      };

      const categoryHTML = cats
        .map(
          (cat) => `
      <button onclick="setCategory(${cat.id})" 
              class="category-badge">
        ${categoryIcons[cat.nama_kategori] || "📦"} ${cat.nama_kategori}
      </button>
    `
        )
        .join("");

      categoryList.innerHTML = `
      <button onclick="setCategory(0)" 
              class="category-badge active">
        🌟 Semua Kategori
      </button>
      ${categoryHTML}
    `;
    }

    window.onload = init;
  </script>
</head>

<body
  class="bg-gradient-to-b from-dark-green via-dark-green to-[#2A4D2E] text-gray-100">
  <!-- Navbar -->
  <nav
    class="bg-white shadow-lg sticky top-0 z-50 border-b-4 border-medium-green">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">
        <div class="flex items-center gap-3">
          <span class="text-4xl">🌴</span>
          <div>
            <h1 class="text-2xl font-bold text-dark-green">
              Bakuliner Kalsel
            </h1>
            <p class="text-xs text-medium-green font-medium">
              Kalimantan Selatan
            </p>
          </div>
        </div>
        <div class="flex items-center gap-4">
          <a
            href="#rekomendasi"
            class="hidden sm:block text-medium-green hover:text-dark-green font-semibold transition">
            ⭐ Rekomendasi
          </a>
          <a
            href="#katalog"
            class="hidden sm:block text-medium-green hover:text-dark-green font-semibold transition">
            📦 Katalog
          </a>
          <a
            href="admin.php"
            class="btn-primary px-5 py-2 rounded-full font-semibold shadow-lg">
            🔐 Admin
          </a>
        </div>
      </div>
    </div>
  </nav>

  <!-- Hero Section -->
  <section class="gradient-hero hero-pattern text-white py-24">
    <div class="max-w-7xl mx-auto px-4 text-center">
      <div class="text-8xl mb-6 animate-bounce">🍽️</div>
      <h1
        class="hero-title text-5xl md:text-6xl font-extrabold mb-4 drop-shadow-lg text-dark-green">
        Budaya & Kuliner Kalimantan Selatan
      </h1>
      <p class="text-xl md:text-2xl text-light-green mb-10 font-medium">
        Dari makanan khas, kue tradisional, hingga pakaian adat & souvenir
        asli Kalsel
      </p>
      <div class="max-w-2xl mx-auto">
        <div class="relative">
          <input
            type="text"
            onkeyup="handleSearch(event)"
            placeholder="🔍 Cari kuliner, pakaian, atau souvenir..."
            class="search-input w-full px-6 py-5 rounded-full text-gray-800 text-lg shadow-2xl bg-white" />
          <div class="absolute right-4 top-1/2 transform -translate-y-1/2">
            <span class="text-3xl">🔍</span>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Categories -->
  <section class="max-w-7xl mx-auto px-4 -mt-10">
    <div
      class="bg-white rounded-3xl shadow-2xl p-6 border-4 border-medium-green">
      <h3 class="text-center text-dark-green font-bold text-lg mb-4">
        📂 Pilih Kategori
      </h3>
      <div class="flex flex-wrap gap-3 justify-center" id="category-list">
        <!-- Categories will be loaded here -->
      </div>
    </div>
  </section>

  <!-- Rekomendasi -->
  <section id="rekomendasi" class="max-w-7xl mx-auto px-4 py-20">
    <div class="text-center mb-12">
      <div
        class="inline-block bg-light-yellow text-dark-green px-6 py-2 rounded-full font-bold text-sm mb-4 border-2 border-medium-green">
        ⭐ PILIHAN TERBAIK
      </div>
      <h2 class="text-5xl font-extrabold text-dark-green mb-4">
        Rekomendasi Unggulan
      </h2>
      <p class="text-gray-600 text-lg">
        Produk dengan rating tertinggi dari Kalimantan Selatan
      </p>
    </div>
    <div
      id="rekomendasi-list"
      class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <!-- Recommendations will be loaded here -->
    </div>
  </section>

  <!-- Katalog Lengkap -->
  <section
    id="katalog"
    class="max-w-7xl mx-auto px-4 py-20 bg-gradient-to-b from-white to-light-green">
    <div class="text-center mb-12">
      <div
        class="inline-block bg-medium-green text-white px-6 py-2 rounded-full font-bold text-sm mb-4">
        📦 KATALOG LENGKAP
      </div>
      <h2 class="text-5xl font-extrabold text-dark-green mb-4">
        Jelajahi Semua Produk
      </h2>
      <p class="text-gray-700 text-lg">
        Menampilkan
        <span id="result-count" class="font-bold text-medium-green text-2xl">0</span>
        item pilihan
      </p>
    </div>
    <div
      id="kuliner-list"
      class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
      <!-- All items will be loaded here -->
    </div>
  </section>

  <!-- Footer -->
  <footer
    class="bg-dark-green text-white py-12 border-t-8 border-light-yellow">
    <div class="max-w-7xl mx-auto px-4">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
        <div>
          <div class="flex items-center gap-3 mb-4">
            <span class="text-4xl">🌴</span>
            <div>
              <span class="text-2xl font-bold">Bakuliner Kalsel</span>
              <p class="text-sm text-light-green">Kalimantan Selatan</p>
            </div>
          </div>
          <p class="text-light-green leading-relaxed">
            Platform digital untuk mempromosikan kuliner, budaya, dan produk
            lokal Kalimantan Selatan kepada dunia.
          </p>
        </div>
        <div>
          <h3 class="text-xl font-bold mb-4 text-light-yellow">
            📂 Kategori
          </h3>
          <ul class="space-y-2 text-light-green">
            <li class="hover:text-light-yellow transition cursor-pointer">
              🍛 Makanan Berat
            </li>
            <li class="hover:text-light-yellow transition cursor-pointer">
              🍰 Kue Tradisional
            </li>
            <li class="hover:text-light-yellow transition cursor-pointer">
              👕 Pakaian Khas Daerah
            </li>
            <li class="hover:text-light-yellow transition cursor-pointer">
              🎁 Souvenir & Oleh-oleh
            </li>
          </ul>
        </div>
        <div>
          <h3 class="text-xl font-bold mb-4 text-light-yellow">📞 Kontak</h3>
          <div class="space-y-3 text-light-green">
            <p class="flex items-center gap-2">
              <span>📧</span> info@bakulinerkalsel.id
            </p>
            <p class="flex items-center gap-2">
              <span>📱</span> +62 812-3456-7890
            </p>
            <p class="flex items-center gap-2">
              <span>📍</span> Banjarmasin, Kalimantan Selatan
            </p>
          </div>
        </div>
      </div>
      <div class="border-t-2 border-medium-green pt-8 text-center">
        <p class="text-light-yellow font-semibold text-lg mb-2">
          🌟 Bangga Produk Lokal Kalimantan Selatan 🌟
        </p>
        <p class="text-light-green text-sm">
          &copy; 2025 Bakuliner Kalsel. Dibuat dengan ❤️ untuk Borneo
        </p>
      </div>
    </div>
  </footer>
</body>

</html>
