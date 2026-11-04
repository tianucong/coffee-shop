<?php

require_once 'config.php';
require_once 'auth.php';


if (in_array(current_role(), ['owner', 'cashier'])) { 
    header("Location: index.php"); 
    exit; 
    }

$result= mysqli_query($conn, "SELECT id, name, price, image FROM products");
$products = mysqli_fetch_all($result, MYSQLI_ASSOC);
$discount = get_discount_percent();

$title = "Checkout";
include 'navbar.php';
?>
<script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>

<div id="app" class="max-w-3xl mx-auto px-4 py-10">
  <h1 class="text-2xl font-semibold mb-6">Checkout</h1>

  <div v-if="items.length === 0" class="text-center py-16 text-stone-500">
    Keranjang masih kosong. <a href="index.php" class="underline">Pilih menu</a>
  </div>

  <div v-else>
 
    <div v-for="i in items" :key="i.id" class="flex items-center gap-4 py-4 border-b border-stone-200">
      <div class="w-16 h-16 bg-stone-100 flex items-center justify-center overflow-hidden shrink-0">
        <img v-if="i.image" :src="i.image" class="w-full h-full object-cover">
        <span v-else>☕</span>
      </div>
      <div class="flex-1">
        <div class="font-medium">{{ i.name }}</div>
        <div class="text-sm text-stone-500">{{ rupiah(i.price) }}</div>
      </div>
      <div class="flex items-center gap-2">
        <button @click="change(i.id, -1)" class="w-7 h-7 border border-stone-300 rounded">−</button>
        <span class="w-6 text-center">{{ i.qty }}</span>
        <button @click="change(i.id, 1)" class="w-7 h-7 border border-stone-300 rounded">+</button>
      </div>
      <div class="w-28 text-right font-medium">{{ rupiah(i.price * i.qty) }}</div>
    </div>

    <div class="mt-6 ml-auto max-w-xs space-y-2 text-sm">
      <div class="flex justify-between"><span>Subtotal</span><span>{{ rupiah(subtotal) }}</span></div>
      <div class="flex justify-between text-green-700" v-if="discount">
        <span>Diskon member {{ discount }}%</span><span>− {{ rupiah(discountAmount) }}</span>
      </div>
      <div v-else class="text-xs text-stone-500">Tanpa diskon. <a href="login.php" class="underline">Masuk</a> untuk diskon 10%.</div>
      <div class="flex justify-between text-lg font-semibold border-t pt-2"><span>Total</span><span>{{ rupiah(total) }}</span></div>
    </div>

    <div class="mt-8">
      <label class="text-sm">Nama pemesan</label>
      <input v-model="customerName" :disabled="loggedIn" placeholder="Nama kamu" class="w-full border border-stone-300 rounded px-3 py-2 mt-1 disabled:bg-stone-100">
    </div>
    <p v-if="error" class="text-red-600 text-sm mt-3">{{ error }}</p>
    <button @click="submit" :disabled="loading" class="mt-6 w-full bg-coffee text-white py-3 rounded hover:opacity-90 disabled:opacity-50">
      {{ loading ? 'Memproses...' : 'Pesan sekarang' }}
    </button>
  </div>
</div>

<script>
Vue.createApp({
  data() {
    return {
      products: <?= json_encode($products, JSON_HEX_TAG) ?>,
      discount: <?= (int)$discount ?>,
      loggedIn: <?= is_logged() ? 'true' : 'false' ?>,
      customerName: <?= json_encode(is_logged() ? $_SESSION['name'] : '', JSON_HEX_TAG) ?>,
      cart: JSON.parse(localStorage.getItem('cart') || '{}'),
      error: '', loading: false
    }
  },
  computed: {
    items() {
      return this.products
        .filter(p => this.cart[p.id] > 0)
        .map(p => ({ ...p, qty: this.cart[p.id] }));
    },
    subtotal(){ 
        return this.items.reduce((s, i) => s + i.price * i.qty, 0); 
    },
    discountAmount() { 
        return Math.round(this.subtotal * this.discount / 100); 
    },
    total(){ 
        return this.subtotal - this.discountAmount; 
    }
  },
  methods: {
    rupiah(n){ 
        return 'Rp ' + Number(n).toLocaleString('id-ID'); 
    },
    change(id, delta) {
      this.cart[id] = (this.cart[id] || 0) + delta;
      if (this.cart[id] <= 0) delete this.cart[id];
      localStorage.setItem('cart', JSON.stringify(this.cart));
      updateCartCount();
    },
    async submit() {
      this.error = '';
      if (!this.customerName.trim()) { this.error = 'Isi nama pemesan dulu.'; return; }
      this.loading = true;
      const res = await fetch('order_process.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          customer_name: this.customerName,
          items: this.items.map(i => ({ id: i.id, qty: i.qty }))
        })
      });
      const data = await res.json();
      this.loading = false;
      if (data.ok) {
        localStorage.removeItem('cart');
        window.location = 'order_success.php?id=' + data.order_id;
      } else {
        this.error = data.msg;
      }
    }
  }
}).mount('#app');
</script>
<?php ?>
</body>
</html>
