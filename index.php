<?php


include 'config.php';
include 'auth.php';

$result   = mysqli_query($conn, "SELECT id, name, description, price, image, category, rating FROM products ORDER BY id DESC");
$products = mysqli_fetch_all($result, MYSQLI_ASSOC);
$discount = get_discount_percent();

$title="Menu";
include 'navbar.php';
?>

<script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>

<section class="bg-coffee-soft">
  <div class="max-w-6xl mx-auto px-4 py-16 md:py-24 grid md:grid-cols-2 gap-8 items-center">
    <div class="text-8xl md:text-9xl text-center select-none">☕</div>
    <div>
      <h1 class="text-4xl font-semibold leading-tight">Waktunya menemukan<br>kopi favoritmu</h1>
      <p class="text-stone-500 mt-4 max-w-md">Pesan kopi, minuman non-kafein, dan roti bakar dari Kopipiro. Masuk atau daftar untuk diskon 10% di setiap pesanan.</p>
      <a href="#menu" class="inline-block mt-6 border border-coffee-dark px-6 py-2 text-sm hover:bg-coffee-dark hover:text-white">Lihat menu</a>
    </div>
  </div>
</section>

<main id="menu" class="max-w-6xl mx-auto px-4 py-12">
<div id="app">
  <h2 class="text-2xl font-semibold text-center mb-6">Menu Kami</h2>

  <p v-if="discount === 0" class="text-center text-sm bg-coffee-soft py-2 rounded mb-6">
    Harga normal. <a href="login.php" class="underline">Masuk</a> atau <a href="register.php" class="underline">daftar</a> untuk diskon 10%.
  </p>
  <p v-else class="text-center text-sm bg-green-50 text-green-700 py-2 rounded mb-6">Diskon {{ discount }}% aktif untuk akunmu.</p>

    <div class="flex flex-wrap gap-3 justify-between items-center mb-8">
        <div class="flex gap-2">
        <button v-for="c in categories" :key="c.key" @click="category = c.key"
            :class="category === c.key ? 'bg-coffee text-white' : 'border border-stone-300'"
            class="px-4 py-1.5 text-sm rounded">{{ c.label }}</button>
        </div>
        <input v-model="search" placeholder="Cari produk..." class="border border-stone-300 rounded px-3 py-1.5 text-sm">
    </div>

    <p v-if="filtered.length === 0" class="text-center text-stone-500 py-12">Produk tidak ditemukan.</p>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
    <div v-for="p in filtered" :key="p.id" class="group">
      <div class="bg-stone-100 aspect-square flex items-center justify-center overflow-hidden">
        <img v-if="p.image" :src="'uploads/' + p.image" :alt="p.name" class="w-full h-full object-cover">
        <span v-else class="text-5xl">☕</span>
      </div>
      <div class="mt-3 text-sm">
        <div class="text-amber-500 text-xs">{{ stars(p.rating) }} <span class="text-stone-400">{{ p.rating }}</span></div>
        <h3 class="font-medium mt-1">{{ p.name }}</h3>
        <p class="text-stone-500 text-xs mt-1 line-clamp-2">{{ p.description }}</p>
        <div class="mt-2">
          <span v-if="discount" class="text-stone-400 line-through text-xs mr-1">{{ rupiah(p.price) }}</span>
          <span class="font-semibold">{{ rupiah(finalPrice(p)) }}</span>
        </div>
        <button @click="add(p)" class="mt-3 w-full border border-coffee-dark py-1.5 hover:bg-coffee-dark hover:text-white">Tambah</button>
      </div>
    </div>
  </div>
<div v-if="toast" class="fixed bottom-6 right-6 bg-coffee-dark text-white text-sm px-4 py-2 rounded shadow">{{ toast }}</div>
</div>
</main>

<script>
Vue.createApp({
  data() {
    return {
      products:<?= json_encode($products)?>,
      discount:<?=(int)$discount?>,
      category:'all',
      search:'',
      search:'',
      toast:'',
      categories:[
        {key:'all',label:'Semua'},
        {key: 'kopi',label:'Kopi'},
        {key: 'non-kopi', label: 'Non Kopi' }, 
        {key: 'makanan', label: 'Makanan' }
      ]
}
  },

  computed:{
    filtered(){
      return this.products.filter(p=>
        (this.category==='all'||p.category===this.category)&&
        p.name.toLowerCase().includes(this.search.toLowerCase())
      );
    }
  },

  methods:{
    rupiah(n){
      return 'Rp '+Number(n).toLocaleString('id-ID');
    },
    finalPrice(p){
      return p.price*(100-this.discount)/100;
    },
    stars(r){
      const n=Math.round(r);
      return '★'.repeat(n) + '☆'.repeat(5 - n);
      
    },
    add(p){
      const cart=JSON.parse(localStorage.getItem('cart')||'{}');
      cart[p.id]=(cart[p.id]||0)+1;
      localStorage.setItem('cart',JSON.stringify(cart));
      updateCartCount();
      this.toast=p.name+' masuk keranjang';
      setTimeout(()=>this.toast='',1500);
    }
  }
}).mount('#app');
</script>
</body>
</html>