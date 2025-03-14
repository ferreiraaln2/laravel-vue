<template>
  <div>
    <div class="flex justify-between items-center">
      <h1 class="text-2xl font-semibold text-gray-900">{{ isEditing ? 'Edit Appointment' : 'Schedule Appointment' }}</h1>
      <router-link 
        to="/appointments" 
        class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
      >
        Back to Appointments
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

      <form v-else @submit.prevent="saveAppointment" class="px-4 py-5 sm:p-6">
        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
          <!-- Pet -->
          <div class="sm:col-span-3">
            <label for="pet_id" class="block text-sm font-medium text-gray-700">Pet</label>
            <div class="mt-1">
              <select 
                id="pet_id" 
                v-model="form.pet_id" 
                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                required
              >
                <option value="" disabled>Select a pet</option>
                <option v-for="pet in pets" :key="pet.id" :value="pet.id">
                  {{ pet.name }} ({{ pet.species }})
                </option>
              </select>
            </div>
            <p v-if="errors.pet_id" class="mt-2 text-sm text-red-600">{{ errors.pet_id[0] }}</p>
          </div>

          <!-- Service -->
          <div class="sm:col-span-3">
            <label for="service_id" class="block text-sm font-medium text-gray-700">Service</label>
            <div class="mt-1">
              <select 
                id="service_id" 
                v-model="form.service_id" 
                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                required
              >
                <option value="" disabled>Select a service</option>
                <option v-for="service in activeServices" :key="service.id" :value="service.id">
                  {{ service.name }} - R$ {{ formatPrice(service.price) }} ({{ service.duration_minutes }} min)
                </option>
              </select>
            </div>
            <p v-if="errors.service_id" class="mt-2 text-sm text-red-600">{{ errors.service_id[0] }}</p>
          </div>

          <!-- Date -->
          <div class="sm:col-span-3">
            <label for="appointment_date" class="block text-sm font-medium text-gray-700">Date</label>
            <div class="mt-1">
              <input 
                type="date" 
                id="appointment_date" 
                v-model="form.appointment_date" 
                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                required
              />
            </div>
            <p v-if="errors.appointment_datetime" class="mt-2 text-sm text-red-600">{{ errors.appointment_datetime[0] }}</p>
          </div>

          <!-- Time -->
          <div class="sm:col-span-3">
            <label for="appointment_time" class="block text-sm font-medium text-gray-700">Time</label>
            <div class="mt-1">
              <input 
                type="time" 
                id="appointment_time" 
                v-model="form.appointment_time" 
                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                required
              />
            </div>
          </div>

          <!-- Status (only for editing) -->
          <div v-if="isEditing" class="sm:col-span-3">
            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
            <div class="mt-1">
              <select 
                id="status" 
                v-model="form.status" 
                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                required
              >
                <option value="scheduled">Scheduled</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
                <option value="no-show">No-show</option>
              </select>
            </div>
            <p v-if="errors.status" class="mt-2 text-sm text-red-600">{{ errors.status[0] }}</p>
          </div>

          <!-- Notes -->
          <div class="sm:col-span-6">
            <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
            <div class="mt-1">
              <textarea 
                id="notes" 
                v-model="form.notes" 
                rows="3" 
                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
              ></textarea>
            </div>
            <p v-if="errors.notes" class="mt-2 text-sm text-red-600">{{ errors.notes[0] }}</p>
          </div>
        </div>

        <div class="mt-6 flex justify-end">
          <router-link 
            to="/appointments" 
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
  name: 'AppointmentForm',
  props: {
    id: {
      type: [String, Number],
      required: false
    }
  },
  data() {
    return {
      form: {
        pet_id: '',
        service_id: '',
        appointment_date: '',
        appointment_time: '',
        status: 'scheduled',
        notes: ''
      },
      pets: [],
      services: [],
      errors: {},
      loading: false,
      saving: false,
      error: null
    }
  },
  computed: {
    isEditing() {
      return !!this.id;
    },
    activeServices() {
      return this.services.filter(service => service.active);
    },
    appointmentDatetime() {
      if (!this.form.appointment_date || !this.form.appointment_time) {
        return null;
      }
      
      return `${this.form.appointment_date}T${this.form.appointment_time}:00`;
    }
  },
  mounted() {
    this.fetchPets();
    this.fetchServices();
    
    if (this.isEditing) {
      this.fetchAppointment();
    }
  },
  methods: {
    formatPrice(price) {
      return price.toFixed(2).replace('.', ',');
    },
    async fetchPets() {
      try {
        // Em uma aplicação real, você buscaria esses dados da sua API
        // Por enquanto, usaremos dados de exemplo
        this.pets = [
          {
            id: 1,
            name: 'Max',
            species: 'Dog',
            breed: 'Golden Retriever',
            gender: 'male'
          },
          {
            id: 2,
            name: 'Bella',
            species: 'Cat',
            breed: 'Siamese',
            gender: 'female'
          },
          {
            id: 3,
            name: 'Charlie',
            species: 'Dog',
            breed: 'Beagle',
            gender: 'male'
          }
        ];
      } catch (err) {
        this.error = 'Failed to load pets. Please try again.';
      }
    },
    async fetchServices() {
      try {
        // Em uma aplicação real, você buscaria esses dados da sua API
        // Por enquanto, usaremos dados de exemplo
        this.services = [
          {
            id: 1,
            name: 'Basic Grooming',
            price: 50.00,
            duration_minutes: 60,
            active: true
          },
          {
            id: 2,
            name: 'Full Grooming',
            price: 80.00,
            duration_minutes: 90,
            active: true
          },
          {
            id: 3,
            name: 'Nail Trimming',
            price: 20.00,
            duration_minutes: 15,
            active: true
          },
          {
            id: 4,
            name: 'Teeth Cleaning',
            price: 45.00,
            duration_minutes: 30,
            active: false
          }
        ];
      } catch (err) {
        this.error = 'Failed to load services. Please try again.';
      }
    },
    async fetchAppointment() {
      this.loading = true;
      this.error = null;
      
      try {
        // Em uma aplicação real, você buscaria esses dados da sua API
        // Por enquanto, usaremos dados de exemplo
        setTimeout(() => {
          // Simular busca de dados do agendamento
          const appointments = [
            {
              id: 1,
              pet_id: 1,
              service_id: 1,
              appointment_datetime: '2025-03-20T10:00:00',
              status: 'scheduled',
              notes: 'Please trim his nails too'
            },
            {
              id: 2,
              pet_id: 2,
              service_id: 3,
              appointment_datetime: '2025-03-15T14:30:00',
              status: 'completed',
              notes: null
            },
            {
              id: 3,
              pet_id: 3,
              service_id: 2,
              appointment_datetime: '2025-03-25T09:15:00',
              status: 'scheduled',
              notes: 'He gets nervous around other dogs'
            },
            {
              id: 4,
              pet_id: 1,
              service_id: 4,
              appointment_datetime: '2025-03-18T11:30:00',
              status: 'cancelled',
              notes: null
            }
          ];
          
          const appointment = appointments.find(a => a.id === parseInt(this.id));
          
          if (appointment) {
            const datetime = new Date(appointment.appointment_datetime);
            const date = datetime.toISOString().split('T')[0];
            const time = datetime.toTimeString().slice(0, 5);
            
            this.form = {
              pet_id: appointment.pet_id,
              service_id: appointment.service_id,
              appointment_date: date,
              appointment_time: time,
              status: appointment.status,
              notes: appointment.notes || ''
            };
          } else {
            this.error = 'Appointment not found';
          }
          
          this.loading = false;
        }, 500);
      } catch (err) {
        this.error = 'Failed to load appointment data. Please try again.';
        this.loading = false;
      }
    },
    async saveAppointment() {
      this.saving = true;
      this.errors = {};
      
      if (!this.appointmentDatetime) {
        this.errors.appointment_datetime = ['Please select both date and time'];
        this.saving = false;
        return;
      }
      
      try {
        // Em uma aplicação real, você chamaria sua API para salvar o agendamento
        // Por enquanto, apenas simularemos isso
        setTimeout(() => {
          this.saving = false;
          this.$router.push('/appointments');
        }, 500);
      } catch (err) {
        this.saving = false;
        
        if (err.response && err.response.status === 422) {
          this.errors = err.response.data.errors;
        } else {
          this.error = 'Failed to save appointment. Please try again.';
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