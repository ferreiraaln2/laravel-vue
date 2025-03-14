<template>
  <div>
    <h1 class="text-2xl font-semibold text-gray-900">Dashboard</h1>
    
    <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
      <!-- Pets card -->
      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
              <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Total Pets</dt>
                <dd class="flex items-baseline">
                  <div class="text-2xl font-semibold text-gray-900">{{ petCount }}</div>
                </dd>
              </dl>
            </div>
          </div>
        </div>
        <div class="bg-gray-50 px-4 py-4 sm:px-6">
          <div class="text-sm">
            <router-link to="/pets" class="font-medium text-indigo-600 hover:text-indigo-500">
              View all pets
              <span class="sr-only">Total Pets</span>
            </router-link>
          </div>
        </div>
      </div>

      <!-- Services card -->
      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
              <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Available Services</dt>
                <dd class="flex items-baseline">
                  <div class="text-2xl font-semibold text-gray-900">{{ serviceCount }}</div>
                </dd>
              </dl>
            </div>
          </div>
        </div>
        <div class="bg-gray-50 px-4 py-4 sm:px-6">
          <div class="text-sm">
            <router-link to="/services" class="font-medium text-indigo-600 hover:text-indigo-500">
              View all services
              <span class="sr-only">Available Services</span>
            </router-link>
          </div>
        </div>
      </div>

      <!-- Appointments card -->
      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
              <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Upcoming Appointments</dt>
                <dd class="flex items-baseline">
                  <div class="text-2xl font-semibold text-gray-900">{{ appointmentCount }}</div>
                </dd>
              </dl>
            </div>
          </div>
        </div>
        <div class="bg-gray-50 px-4 py-4 sm:px-6">
          <div class="text-sm">
            <router-link to="/appointments" class="font-medium text-indigo-600 hover:text-indigo-500">
              View all appointments
              <span class="sr-only">Upcoming Appointments</span>
            </router-link>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent appointments -->
    <h2 class="mt-8 text-lg font-medium text-gray-900">Recent Appointments</h2>
    <div class="mt-2 bg-white shadow overflow-hidden sm:rounded-md">
      <ul v-if="recentAppointments.length > 0" class="divide-y divide-gray-200">
        <li v-for="appointment in recentAppointments" :key="appointment.id">
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
                  {{ formatDate(appointment.appointment_datetime) }}
                </p>
              </div>
            </div>
          </div>
        </li>
      </ul>
      <div v-else class="px-4 py-5 sm:p-6 text-center text-gray-500">
        No recent appointments found
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Dashboard',
  data() {
    return {
      petCount: 0,
      serviceCount: 0,
      appointmentCount: 0,
      recentAppointments: []
    }
  },
  mounted() {
    this.fetchDashboardData();
  },
  methods: {
    fetchDashboardData() {
      // In a real application, you would fetch this data from your API
      // For now, we'll use placeholder data
      this.petCount = 5;
      this.serviceCount = 8;
      this.appointmentCount = 3;
      
      // Placeholder data for recent appointments
      this.recentAppointments = [
        {
          id: 1,
          pet: { name: 'Max' },
          service: { name: 'Grooming' },
          status: 'scheduled',
          appointment_datetime: '2025-03-20T10:00:00'
        },
        {
          id: 2,
          pet: { name: 'Bella' },
          service: { name: 'Vaccination' },
          status: 'completed',
          appointment_datetime: '2025-03-15T14:30:00'
        },
        {
          id: 3,
          pet: { name: 'Charlie' },
          service: { name: 'Check-up' },
          status: 'cancelled',
          appointment_datetime: '2025-03-18T09:15:00'
        }
      ];
    },
    formatDate(dateString) {
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
    }
  }
}
</script> 