<template>
  <div class="flex flex-col md:flex-row h-screen bg-gray-50">
    <!-- Client List -->
    <div
      ref="scrollContainer"
      class="flex-1 overflow-y-auto p-4 bg-white shadow-md border-r"
      @scroll="handleScroll"
    >
      <h1 class="text-xl font-bold text-gray-800 mb-4">Client List</h1>
      <div
        v-for="client in clients"
        :key="client.id"
        class="p-4 mb-2 bg-gray-100 rounded-lg hover:bg-blue-50 transition"
      >
        <p class="text-lg font-medium text-gray-700">{{ client.name }}</p>
      </div>
      <div v-if="loading" class="text-center py-4 text-blue-600">Loading...</div>
    </div>

    <!-- Alphabet Bar -->
    <div class="md:w-16 w-full md:flex flex-wrap md:flex-col items-center justify-center bg-gray-100 shadow-inner">
      <div
        v-for="letter in alphabet"
        :key="letter"
        @click="scrollToLetter(letter)"
        class="cursor-pointer text-sm font-semibold py-2 w-full md:w-auto text-center rounded-md hover:bg-blue-200 hover:text-blue-700 transition"
        :class="{'bg-blue-500 text-white': currentLetter === letter}"
      >
        {{ letter }}
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from "vue";
import axios from "axios";

export default {
  setup() {
    const clients = ref([]);
    const alphabet = ["A", "B", "C", "D", "E", "F", "G", "H"];
    const currentLetter = ref("A");
    const loading = ref(false);
    const scrollContainer = ref(null);

    const fetchClients = async (letter, reset = false) => {
      if (loading.value) return;
      loading.value = true;

      try {
        const response = await axios.get("/clients", {
          params: { letter },
        });

        if (reset) {
          clients.value = response.data.data; // Reset clients when jumping to a new letter
        } else {
          clients.value.push(...response.data.data); // Append new clients when scrolling
        }
      } catch (error) {
        console.error("Error fetching clients:", error);
      } finally {
        loading.value = false;
      }
    };

    const handleScroll = () => {
      const container = scrollContainer.value;
      if (
        container.scrollTop + container.clientHeight >=
        container.scrollHeight - 10
      ) {
        const nextLetter = alphabet[alphabet.indexOf(currentLetter.value) + 1];
        if (nextLetter) {
          currentLetter.value = nextLetter;
          fetchClients(nextLetter);
        }
      }
    };

    const scrollToLetter = (letter) => {
      currentLetter.value = letter;
      fetchClients(letter, true);
    };

    onMounted(() => {
      fetchClients("A");
    });

    return {
      clients,
      alphabet,
      currentLetter,
      loading,
      scrollContainer,
      fetchClients,
      handleScroll,
      scrollToLetter,
    };
  },
};
</script>

<style scoped>
/* Custom scrollbar styling */
.flex-1::-webkit-scrollbar {
  width: 8px;
}
.flex-1::-webkit-scrollbar-thumb {
  background-color: #cbd5e0;
  border-radius: 4px;
}
.flex-1::-webkit-scrollbar-thumb:hover {
  background-color: #94a3b8;
}

/* Mobile-friendly adjustments */
@media (max-width: 768px) {
  .md\\:w-16 {
    width: 100% !important;
  }
  .md\\:flex-col {
    flex-direction: row !important;
    flex-wrap: wrap;
  }
}
</style>
