<template>
  <div class="h-screen flex flex-col bg-gray-50">
    <!-- Header -->
    <div class="bg-blue-600 text-white text-center py-4 font-bold text-lg shadow-md">
      Phone Directory
    </div>

    <!-- Content Wrapper -->
    <div class="flex flex-1 overflow-hidden">
      <!-- Client List -->
      <div ref="scrollContainer" class="flex-1 overflow-y-auto px-4 pt-4 bg-white">
        <div v-for="client in clients" :key="client.id" class="py-2 border-b">
          <p class="text-lg font-medium text-gray-800">{{ client.name }}</p>
        </div>
        <div v-if="loading" class="text-center py-4 text-blue-600">
          <svg
            class="animate-spin h-5 w-5 text-blue-500 mx-auto"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
          >
            <circle
              class="opacity-25"
              cx="12"
              cy="12"
              r="10"
              stroke="currentColor"
              stroke-width="4"
            ></circle>
            <path
              class="opacity-75"
              fill="currentColor"
              d="M4 12a8 8 0 018-8v8H4z"
            ></path>
          </svg>
        </div>
      </div>

      <!-- Alphabet Sidebar -->
      <div class="w-12 bg-gray-100 flex flex-col items-center justify-center shadow-inner">
        <div
          v-for="letter in alphabet"
          :key="letter"
          @click="scrollToLetter(letter)"
          class="cursor-pointer text-xs font-semibold py-1 px-2 text-center rounded-full mb-1 hover:bg-blue-500 hover:text-white transition"
          :class="{'bg-blue-600 text-white': currentLetter === letter}"
        >
          {{ letter }}
        </div>
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
    const alphabet = ["A", "B", "C", "D", "E", "F", "G", "H" , "I" , "J"];
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
/* Custom scrollbar for client list */
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
@media (max-width: 768px) {
  .w-12 {
    width: 10%;
  }
  .text-xs {
    font-size: 0.75rem;
  }
}

</style>
