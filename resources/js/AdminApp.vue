<template>
  <div class="admin-dashboard">
    <header class="dashboard-header">
      <h1>Dashboard Overview</h1>
      <p v-if="loading" class="loading-text"></p>
    </header>

    <section class="cards" v-if="!loading">
      <div class="card fade-in">
        <h2>Total Plugins</h2>
        <p>{{ stats.total }}</p>
      </div>
      <div class="card fade-in delay-1">
        <h2>Active Plugins</h2>
        <p>{{ stats.active }}</p>
      </div>
      <div class="card fade-in delay-2">
        <h2>Inactive Plugins</h2>
        <p>{{ stats.inactive }}</p>
      </div>
    </section>

    <div v-if="loading" class="loader"></div>

    <CreditVue />
  </div>
</template>

<script>
import CreditVue from "./CreditVue.vue";

export default {
  name: "AdminApp",
  components: { CreditVue },
  data() {
    return {
      stats: { total: 0, active: 0, inactive: 0 },
      loading: true
    };
  },
  mounted() {
    fetch(SuitePressSettings.restUrl, {
      method: "GET",
      headers: {
        "X-WP-Nonce": SuitePressSettings.nonce,
        "Content-Type": "application/json"
      },
      credentials: "same-origin"
    })
        .then((res) => res.json())
        .then((data) => {
          this.stats = data;
          this.loading = false;
        })
        .catch((err) => {
          console.error("Error loading stats:", err);
          this.loading = false;
        });
  }
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');

.admin-dashboard {
  font-family: 'Inter', sans-serif;
  background: #f3f4f6;
  padding: 40px 60px;
  min-height: 100vh;
  box-sizing: border-box;
}

.dashboard-header {
  text-align: left;
  margin-bottom: 30px;
}
.dashboard-header h1 {
  font-size: 1.5rem;
  font-weight: bolder;
  margin: 0;
  color: #000000;
}
.loading-text {
  color: #6b7280;
  margin-top: 8px;
  font-size: 0.95rem;
}
.cards {
  display: flex;
  gap: 24px;
  flex-wrap: wrap;
  justify-content: space-between;
}

.card {
  background: #ffffff;
  border-radius: 10px;
  padding: .5rem 1.5rem;
  flex: 1 1 30%;
  box-shadow: 0 6px 12px rgba(0, 0, 0, 0.06);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  border: none;
}
.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
}
.card h2 {
  font-size: 1.1rem;
  color: #374151;
  margin-bottom: 10px;
}
.card p {
  font-size: 2rem;
  font-weight: bold;
  color: #111827;
  padding: .5rem 0;
  margin: 0;
}

/* Loading spinner */
.loader {
  width: 80px;
  height: 80px;
  margin: 40px auto;
  border: 10px solid #d1d5db;
  border-top-color: #008080;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

/* Animations */
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-15px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
.fade-in {
  animation: fadeIn 0.6s ease-out forwards;
}
.delay-1 {
  animation-delay: 0.2s;
}
.delay-2 {
  animation-delay: 0.4s;
}
</style>
