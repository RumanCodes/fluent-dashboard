<template>
  <section class="welcome-banner">
    <h2> Welcome back!</h2>
    <p>Here’s what’s happening with your website today. </p>
  </section>
</template>

<script setup>

</script>

<style>

.welcome-banner {
  background: linear-gradient(135deg, #0000001c, #0000001c);
  color: #000000;
  padding: .5rem 1.5rem;
  border-radius: 10px 0 0 10px;
  box-shadow: 0 1px 1px #0000001c;
  animation: slideDown-1af2cd46 0.6s ease-out;
  margin-top: 2rem;
}
.welcome-banner h2 {
  font-size: 1.5rem;
  margin-bottom: 8px;
}
.welcome-banner p {
  font-size: 1rem;
  opacity: 0.95;
}

</style>
