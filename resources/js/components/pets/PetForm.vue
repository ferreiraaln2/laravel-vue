<template>
  <div>
    <div class="flex justify-between items-center">
      <h1 class="text-2xl font-semibold text-gray-900">{{ isEditing ? 'Edit Pet' : 'Add New Pet' }}</h1>
      <router-link 
        to="/pets" 
        class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
      >
        Back to Pets
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

      <form v-else @submit.prevent="savePet" class="px-4 py-5 sm:p-6">
        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
          <!-- Name -->
          <div class="sm:col-span-3">
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

          <!-- Species -->
          <div class="sm:col-span-3">
            <label for="species" class="block text-sm font-medium text-gray-700">Species</label>
            <div class="mt-1">
              <input 
                type="text" 
                id="species" 
                v-model="form.species" 
                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                required
              />
            </div>
            <p v-if="errors.species" class="mt-2 text-sm text-red-600">{{ errors.species[0] }}</p>
          </div>

          <!-- Breed -->
          <div class="sm:col-span-3">
            <label for="breed" class="block text-sm font-medium text-gray-700">Breed</label>
            <div class="mt-1">
              <input 
                type="text" 
                id="breed" 
                v-model="form.breed" 
                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
              />
            </div>
            <p v-if="errors.breed" class="mt-2 text-sm text-red-600">{{ errors.breed[0] }}</p>
          </div>

          <!-- Gender -->
          <div class="sm:col-span-3">
            <label for="gender" class="block text-sm font-medium text-gray-700">Gender</label>
            <div class="mt-1">
              <select 
                id="gender" 
                v-model="form.gender" 
                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                required
              >
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="unknown">Unknown</option>
              </select>
            </div>
            <p v-if="errors.gender" class="mt-2 text-sm text-red-600">{{ errors.gender[0] }}</p>
          </div>

          <!-- Birth Date -->
          <div class="sm:col-span-3">
            <label for="birth_date" class="block text-sm font-medium text-gray-700">Birth Date</label>
            <div class="mt-1">
              <input 
                type="date" 
                id="birth_date" 
                v-model="form.birth_date" 
                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
              />
            </div>
            <p v-if="errors.birth_date" class="mt-2 text-sm text-red-600">{{ errors.birth_date[0] }}</p>
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
            to="/pets" 
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
  name: 'PetForm',
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
        species: '',
        breed: '',
        gender: 'unknown',
        birth_date: '',
        description: ''
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
      this.fetchPet();
    }
  },
  methods: {
    async fetchPet() {
      this.loading = true;
      this.error = null;
      
      try {
        // In a real application, you would fetch this data from your API
        // For now, we'll use placeholder data
        setTimeout(() => {
          // Simulate fetching pet data
          const pets = [
            {
              id: 1,
              name: 'Max',
              species: 'Dog',
              breed: 'Golden Retriever',
              gender: 'male',
              birth_date: '2020-05-15',
              description: 'Friendly and energetic'
            },
            {
              id: 2,
              name: 'Bella',
              species: 'Cat',
              breed: 'Siamese',
              gender: 'female',
              birth_date: '2019-10-20',
              description: 'Quiet and independent'
            },
            {
              id: 3,
              name: 'Charlie',
              species: 'Dog',
              breed: 'Beagle',
              gender: 'male',
              birth_date: '2021-02-10',
              description: 'Playful and curious'
            }
          ];
          
          const pet = pets.find(p => p.id === parseInt(this.id));
          
          if (pet) {
            this.form = { ...pet };
          } else {
            this.error = 'Pet not found';
          }
          
          this.loading = false;
        }, 500);
      } catch (err) {
        this.error = 'Failed to load pet data. Please try again.';
        this.loading = false;
      }
    },
    async savePet() {
      this.saving = true;
      this.errors = {};
      
      try {
        // In a real application, you would call your API to save the pet
        // For now, we'll just simulate it
        setTimeout(() => {
          this.saving = false;
          this.$router.push('/pets');
        }, 500);
      } catch (err) {
        this.saving = false;
        
        if (err.response && err.response.status === 422) {
          this.errors = err.response.data.errors;
        } else {
          this.error = 'Failed to save pet. Please try again.';
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