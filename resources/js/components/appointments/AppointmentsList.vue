<template>
  <div>
    <div class="flex justify-between items-center">
      <h1 class="text-2xl font-semibold text-gray-900">Appointments</h1>
      <router-link 
        to="/appointments/create" 
        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
      >
        Schedule Appointment
      </router-link>
    </div>

    <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-md">
      <div v-if="loading" class="px-4 py-5 sm:p-6 text-center">
        <div class="spinner"></div>
        <p class="mt-2 text-gray-500">Loading appointments...</p>
      </div>

      <div v-else-if="error" class="px-4 py-5 sm:p-6 text-center text-red-500">
        {{ error }}
      </div>

      <ul v-else-if="appointments.length > 0" class="divide-y divide-gray-200">
        <li v-for="appointment in appointments" :key="appointment.id">
          <div class="px-4 py-4 sm:px-6">
            <div class="flex items-center justify-between">
              <p class="text-sm font-medium text-indigo-600 truncate">
                {{ appointment.pet.name }} - {{ appointment.service.name }}
              </p>
              <div class="ml-2 flex-shrink-0 flex">
                <p :class="statusClasses(appointment.status)">
                  {{ appointment.status }}
                </p>
              </div>
            </div>
            <div class="mt-2 sm:flex sm:justify-between">
              <div class="sm:flex">
                <p class="flex items-center text-sm text-gray-500">
                  <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                  </svg>
                  {{ formatDate(appointment.appointment_datetime) }}
                </p>
              </div>
              <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                <router-link 
                  :to="`/appointments/${appointment.id}/edit`" 
                  class="text-indigo-600 hover:text-indigo-900 mr-4"
                >
                  Edit
                </router-link>
                <button 
                  @click="confirmDelete(appointment)" 
                  class="text-red-600 hover:text-red-900"
                >
                  Cancel
                </button>
              </div>
            </div>
            <div v-if="appointment.notes" class="mt-2 text-sm text-gray-500">
              {{ appointment.notes }}
            </div>
          </div>
        </li>
      </ul>

      <div v-else class="px-4 py-5 sm:p-6 text-center text-gray-500">
        No appointments found. <router-link to="/appointments/create" class="text-indigo-600 hover:text-indigo-900">Schedule your first appointment</router-link>
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
                  Cancel Appointment
                </h3>
                <div class="mt-2">
                  <p class="text-sm text-gray-500">
                    Are you sure you want to cancel the appointment for {{ appointmentToDelete?.pet?.name }} on {{ formatDate(appointmentToDelete?.appointment_datetime) }}? This action cannot be undone.
                  </p>
                </div>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button 
              @click="deleteAppointment" 
              type="button" 
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm"
            >
              Cancel Appointment
            </button>
            <button 
              @click="cancelDelete" 
              type="button" 
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
            >
              Go Back
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AppointmentsList',
  data() {
    return {
      appointments: [],
      loading: true,
      error: null,
      showDeleteModal: false,
      appointmentToDelete: null
    }
  },
  mounted() {
    this.fetchAppointments();
  },
  methods: {
    async fetchAppointments() {
      this.loading = true;
      this.error = null;
      
      try {
        // Em uma aplicação real, você buscaria esses dados da sua API
        // Por enquanto, usaremos dados de exemplo
        setTimeout(() => {
          this.appointments = [
            {
              id: 1,
              pet: { id: 1, name: 'Max' },
              service: { id: 1, name: 'Basic Grooming' },
              appointment_datetime: '2025-03-20T10:00:00',
              status: 'scheduled',
              notes: 'Please trim his nails too'
            },
            {
              id: 2,
              pet: { id: 2, name: 'Bella' },
              service: { id: 3, name: 'Nail Trimming' },
              appointment_datetime: '2025-03-15T14:30:00',
              status: 'completed',
              notes: null
            },
            {
              id: 3,
              pet: { id: 3, name: 'Charlie' },
              service: { id: 2, name: 'Full Grooming' },
              appointment_datetime: '2025-03-25T09:15:00',
              status: 'scheduled',
              notes: 'He gets nervous around other dogs'
            },
            {
              id: 4,
              pet: { id: 1, name: 'Max' },
              service: { id: 4, name: 'Teeth Cleaning' },
              appointment_datetime: '2025-03-18T11:30:00',
              status: 'cancelled',
              notes: null
            }
          ];
          this.loading = false;
        }, 500);
      } catch (err) {
        this.error = 'Failed to load appointments. Please try again.';
        this.loading = false;
      }
    },
    formatDate(dateString) {
      if (!dateString) return '';
      
      const options = { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      };
      return new Date(dateString).toLocaleDateString(undefined, options);
    },
    statusClasses(status) {
      const classes = 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full ';
      
      switch(status) {
        case 'scheduled':
          return classes + 'bg-yellow-100 text-yellow-800';
        case 'completed':
          return classes + 'bg-green-100 text-green-800';
        case 'cancelled':
          return classes + 'bg-red-100 text-red-800';
        case 'no-show':
          return classes + 'bg-gray-100 text-gray-800';
        default:
          return classes + 'bg-gray-100 text-gray-800';
      }
    },
    confirmDelete(appointment) {
      this.appointmentToDelete = appointment;
      this.showDeleteModal = true;
    },
    cancelDelete() {
      this.appointmentToDelete = null;
      this.showDeleteModal = false;
    },
    async deleteAppointment() {
      if (!this.appointmentToDelete) return;
      
      try {
        // Em uma aplicação real, você chamaria sua API para cancelar o agendamento
        // Por enquanto, apenas simularemos isso
        const index = this.appointments.findIndex(a => a.id === this.appointmentToDelete.id);
        if (index !== -1) {
          this.appointments[index].status = 'cancelled';
        }
        
        this.showDeleteModal = false;
        this.appointmentToDelete = null;
      } catch (err) {
        this.error = 'Failed to cancel appointment. Please try again.';
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