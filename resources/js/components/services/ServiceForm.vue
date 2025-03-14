<template>
  <div>
    <div class="flex justify-between items-center">
      <h1 class="text-2xl font-semibold text-gray-900">{{ isEditing ? 'Edit Service' : 'Add New Service' }}</h1>
      <router-link 
        to="/services" 
        class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
      >
        Back to Services
      </router-link>
    </div>

    <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-md">
      <div v-if="loading" class="px-4 py-5 sm:p-6 text-center">
        <div class="spinner"></div>
        <p class="mt-2 text-gray-500">Loading...</p>
      </div>

      <div v-else-if="error" class="px-4 py-5 sm:p-6 text-center text-red-500">
        {{ error }}
      </div>

      <form v-else @submit.prevent="saveService" class="px-4 py-5 sm:p-6">
        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
          <!-- Name -->
          <div class="sm:col-span-4">
            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
            <div class="mt-1">
              <input 
                type="text" 
                id="name" 
                v-model="form.name" 
                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                required
              />
            </div>
            <p v-if="errors.name" class="mt-2 text-sm text-red-600">{{ errors.name[0] }}</p>
          </div>

          <!-- Price -->
          <div class="sm:col-span-2">
            <label for="price" class="block text-sm font-medium text-gray-700">Price (R$)</label>
            <div class="mt-1">
              <input 
                type="number" 
                id="price" 
                v-model="form.price" 
                step="0.01" 
                min="0" 
                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                required
              />
            </div>
            <p v-if="errors.price" class="mt-2 text-sm text-red-600">{{ errors.price[0] }}</p>
          </div>

          <!-- Duration -->
          <div class="sm:col-span-3">
            <label for="duration_minutes" class="block text-sm font-medium text-gray-700">Duration (minutes)</label>
            <div class="mt-1">
              <input 
                type="number" 
                id="duration_minutes" 
                v-model="form.duration_minutes" 
                min="1" 
                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                required
              />
            </div>
            <p v-if="errors.duration_minutes" class="mt-2 text-sm text-red-600">{{ errors.duration_minutes[0] }}</p>
          </div>

          <!-- Active -->
          <div class="sm:col-span-3">
            <div class="flex items-start mt-6">
              <div class="flex items-center h-5">
                <input 
                  id="active" 
                  type="checkbox" 
                  v-model="form.active" 
                  class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                />
              </div>
              <div class="ml-3 text-sm">
                <label for="active" class="font-medium text-gray-700">Active</label>
                <p class="text-gray-500">Inactive services won't be available for new appointments</p>
              </div>
            </div>
            <p v-if="errors.active" class="mt-2 text-sm text-red-600">{{ errors.active[0] }}</p>
          </div>

          <!-- Description -->
          <div class="sm:col-span-6">
            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
            <div class="mt-1">
              <textarea 
                id="description" 
                v-model="form.description" 
                rows="3" 
                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
              ></textarea>
            </div>
            <p v-if="errors.description" class="mt-2 text-sm text-red-600">{{ errors.description[0] }}</p>
          </div>
        </div>

        <div class="mt-6 flex justify-end">
          <router-link 
            to="/services" 
            class="mr-3 inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            Cancel
          </router-link>
          <button 
            type="submit" 
            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            :disabled="saving"
          >
            {{ saving ? 'Saving...' : 'Save' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ServiceForm',
  props: {
    id: {
      type: [String, Number],
      required: false
    }
  },
  data() {
    return {
      form: {
        name: '',
        description: '',
        price: 0,
        duration_minutes: 30,
        active: true
      },
      errors: {},
      loading: false,
      saving: false,
      error: null
    }
  },
  computed: {
    isEditing() {
      return !!this.id;
    }
  },
  mounted() {
    if (this.isEditing) {
      this.fetchService();
    }
  },
  methods: {
    async fetchService() {
      this.loading = true;
      this.error = null;
      
      try {
        // Em uma aplicação real, você buscaria esses dados da sua API
        // Por enquanto, usaremos dados de exemplo
        setTimeout(() => {
          // Simular busca de dados do serviço
          const services = [
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
          
          const service = services.find(s => s.id === parseInt(this.id));
          
          if (service) {
            this.form = { ...service };
          } else {
            this.error = 'Service not found';
          }
          
          this.loading = false;
        }, 500);
      } catch (err) {
        this.error = 'Failed to load service data. Please try again.';
        this.loading = false;
      }
    },
    async saveService() {
      this.saving = true;
      this.errors = {};
      
      try {
        // Em uma aplicação real, você chamaria sua API para salvar o serviço
        // Por enquanto, apenas simularemos isso
        setTimeout(() => {
          this.saving = false;
          this.$router.push('/services');
        }, 500);
      } catch (err) {
        this.saving = false;
        
        if (err.response && err.response.status === 422) {
          this.errors = err.response.data.errors;
        } else {
          this.error = 'Failed to save service. Please try again.';
        }
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