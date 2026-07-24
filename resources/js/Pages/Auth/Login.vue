<template>
  <div class="max-w-md mx-auto mt-12">
    <h1 class="text-2xl font-bold">Login</h1>
    <form @submit.prevent="submit">
      <input v-model="form.email" placeholder="Email or phone" class="border p-2 w-full mb-2" />
      <input v-model="form.password" type="password" placeholder="Password" class="border p-2 w-full mb-2" />
      <button class="bg-teal-600 text-white px-4 py-2">Login</button>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Inertia } from '@inertiajs/inertia'
const form = ref({ email:'', password:'' })
function submit(){ Inertia.post('/login', form.value) }
</script>
