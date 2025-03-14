<template>
  <div>
    <div class="flex justify-between items-center">
      <h1 class="text-2xl font-semibold text-gray-900">Services</h1>
      <router-link 
        to="/services/create" 
        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
      >
        Add Service
      </router-link>
    </div>

    <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-md">
      <div v-if="loading" class="px-4 py-5 sm:p-6 text-center">
        <div class="spinner"></div>
        <p class="mt-2 text-gray-500">Loading services...</p>
      </div>

      <div v-else-if="error" class="px-4 py-5 sm:p-6 text-center text-red-500">
        {{ error }}
      </div>

      <ul v-else-if="services.length > 0" class="divide-y divide-gray-200">
        <li v-for="service in services" :key="service.id">
          <div class="px-4 py-4 sm:px-6">
            <div class="flex items-center justify-between">
              <p class="text-sm font-medium text-indigo-600 truncate">
                {{ service.name }}
              </p>
              <div class="ml-2 flex-shrink-0 flex">
                <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
                   :class="service.active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'">
                  {{ service.active ? 'Active' : 'Inactive' }}
                </p>
              </div>
            </div>
            <div class="mt-2 sm:flex sm:justify-between">
              <div class="sm:flex">
                <p class="flex items-center text-sm text-gray-500">
                  <span class="font-medium text-gray-900">R$ {{ formatPrice(service.price) }}</span>
                  <span class="ml-2">• {{ service.duration_minutes }} min</span>
                </p>
              </div>
              <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                <router-link 
                  :to="`/services/${service.id}/edit`" 
                  class="text-indigo-600 hover:text-indigo-900 mr-4"
                >
                  Edit
                </router-link>
                <button 
                  @click="confirmDelete(service)" 
                  class="text-red-600 hover:text-red-900"
                >
                  Delete
                </button>
              </div>
            </div>
            <div v-if="service.description" class="mt-2 text-sm text-gray-500">
              {{ service.description }}
            </div>
          </div>
        </li>
      </ul>

      <div v-else class="px-4 py-5 sm:p-6 text-center text-gray-500">
        No services found. <router-link to="/services/create" class="text-indigo-600 hover:text-indigo-900">Add your first service</router-link>
      </div>
    </div>

    <!-- Delete confirmation modal -->
    <div v-if="showDeleteModal" class="fixed z-10 inset-0 overflow-y-auto">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
          <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
              </div>
              <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                  Delete Service
                </h3>
                <div class="mt-2">
                  <p class="text-sm text-gray-500">
                    Are you sure you want to delete {{ serviceToDelete?.name }}? This action cannot be undone.
                  </p>
                </div>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button 
              @click="deleteService" 
              type="button" 
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm"
            >
              Delete
            </button>
            <button 
              @click="cancelDelete" 
              type="button" 
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
            >
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ServicesList',
  data() {
    return {
      services: [],
      loading: true,
      error: null,
      showDeleteModal: false,
      serviceToDelete: null
    }
  },
  mounted() {
    this.fetchServices();
  },
  methods: {
    async fetchServices() {
      this.loading = true;
      this.error = null;
      
      try {
        // Em uma aplicação real, você buscaria esses dados da sua API
        // Por enquanto, usaremos dados de exemplo
        setTimeout(() => {
          this.services = [
            {
              id: 1,
              name: 'Basic Grooming',
              description: 'Basic grooming service including bath, dry, and brush',
              price: 50.00,
              duration_minutes: 60,
              active: true
            },
            {
              id: 2,
              name: 'Full Grooming',
              description: 'Complete grooming service including bath, dry, brush, nail trim, and ear cleaning',
              price: 80.00,
              duration_minutes: 90,
              active: true
            },
            {
              id: 3,
              name: 'Nail Trimming',
              description: 'Nail trimming service for dogs and cats',
              price: 20.00,
              duration_minutes: 15,
              active: true
            },
            {
              id: 4,
              name: 'Teeth Cleaning',
              description: 'Professional teeth cleaning service',
              price: 45.00,
              duration_minutes: 30,
              active: false
            }
          ];
          this.loading = false;
        }, 500);
      } catch (err) {
        this.error = 'Failed to load services. Please try again.';
        this.loading = false;
      }
    },
    formatPrice(price) {
      return price.toFixed(2).replace('.', ',');
    },
    confirmDelete(service) {
      this.serviceToDelete = service;
      this.showDeleteModal = true;
    },
    cancelDelete() {
      this.serviceToDelete = null;
      this.showDeleteModal = false;
    },
    async deleteService() {
      if (!this.serviceToDelete) return;
      
      try {
        // Em uma aplicação real, você chamaria sua API para excluir o serviço
        // Por enquanto, apenas simularemos isso
        this.services = this.services.filter(service => service.id !== this.serviceToDelete.id);
        this.showDeleteModal = false;
        this.serviceToDelete = null;
      } catch (err) {
        this.error = 'Failed to delete service. Please try again.';
      }
    }
  }
}
</script>

<style scoped>
.spinner {
  border: 4px solid rgba(0, 0, 0, 0.1);
  width: 36px;
  height: 36px;
  border-radius: 50%;
  border-left-color: #3f51b5;
  animation: spin 1s linear infinite;
  margin: 0 auto;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}
</style> 