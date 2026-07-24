<template>
  <div class="p-4 max-w-3xl mx-auto">
    <h2 class="text-xl font-semibold">Create Prescription</h2>
    <form @submit.prevent="submit">
      <label>Patient Name</label>
      <input v-model="form.patient_name" class="border p-2 w-full" />

      <label class="mt-2">Chief Complaints</label>
      <textarea v-model="form.chief_complaints" class="border p-2 w-full"></textarea>

      <label class="mt-2">Medicine</label>
      <!-- medicine picker stub -->
      <input v-model="medicineQuery" @input="searchMedicines" placeholder="Search medicines" class="border p-2 w-full" />
      <ul v-if="results.length" class="border bg-white">
        <li v-for="m in results" :key="m.id" @click="selectMedicine(m)">{{ m.name }} ({{m.form}})</li>
      </ul>

      <button class="mt-4 bg-teal-600 text-white px-4 py-2">Save</button>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue'
const form = ref({ patient_name:'', chief_complaints:'', on_examination:'', advice:'', items:[] })
const medicineQuery = ref('')
const results = ref([])
function searchMedicines(){ /* call API /api/medicines?search= */ }
function selectMedicine(m){ form.value.items.push({medicine_id:m.id, name:m.name}) }
function submit(){ /* POST to /prescriptions */ }
</script>