<template>
  <form @submit.prevent="handleSubmit" class="space-y-4">
    <!-- Name Field -->
    <div>
      <label for="name" class="block text-sm font-medium text-gray-700">Name *</label>
      <input
        id="name"
        v-model="form.name"
        type="text"
        required
        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
        placeholder="Enter full name"
      />
    </div>

    <!-- Relation Field -->
    <div>
      <label for="relation" class="block text-sm font-medium text-gray-700">Relation *</label>
      <select
        id="relation"
        v-model="form.relation"
        required
        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
      >
        <option value="">Select relation</option>
        <option value="Self">Self</option>
        <option value="Spouse">Spouse</option>
        <option value="Father">Father</option>
        <option value="Mother">Mother</option>
        <option value="Son">Son</option>
        <option value="Daughter">Daughter</option>
        <option value="Brother">Brother</option>
        <option value="Sister">Sister</option>
        <option value="Grandfather">Grandfather</option>
        <option value="Grandmother">Grandmother</option>
        <option value="Grandson">Grandson</option>
        <option value="Granddaughter">Granddaughter</option>
        <option value="Uncle">Uncle</option>
        <option value="Aunt">Aunt</option>
        <option value="Nephew">Nephew</option>
        <option value="Niece">Niece</option>
        <option value="Cousin">Cousin</option>
        <option value="Step-father">Step-father</option>
        <option value="Step-mother">Step-mother</option>
        <option value="Step-son">Step-son</option>
        <option value="Step-daughter">Step-daughter</option>
        <option value="Step-brother">Step-brother</option>
        <option value="Step-sister">Step-sister</option>
        <option value="Father-in-law">Father-in-law</option>
        <option value="Mother-in-law">Mother-in-law</option>
        <option value="Son-in-law">Son-in-law</option>
        <option value="Daughter-in-law">Daughter-in-law</option>
        <option value="Brother-in-law">Brother-in-law</option>
        <option value="Sister-in-law">Sister-in-law</option>
        <option value="Other">Other</option>
      </select>
    </div>

    <!-- Profile Picture Upload -->
    <div>
      <label for="profile_pic" class="block text-sm font-medium text-gray-700">Profile Picture</label>
      <div class="mt-1 flex items-center space-x-4">
        <div v-if="profilePicPreview || (node?.data?.profilePic)" class="relative">
          <img
            :src="profilePicPreview || node?.data?.profilePic"
            alt="Profile preview"
            class="w-16 h-16 rounded-lg object-cover border border-gray-300"
          />
          <button
            v-if="profilePicPreview"
            @click="removeProfilePic"
            type="button"
            class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full hover:bg-red-600 flex items-center justify-center text-xs"
          >
            ×
          </button>
        </div>
        <div class="flex-1">
          <input
            id="profile_pic"
            ref="fileInput"
            type="file"
            accept="image/*"
            @change="handleFileChange"
            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
          />
        </div>
      </div>
    </div>

    <!-- Date of Birth -->
    <div>
      <label for="dob" class="block text-sm font-medium text-gray-700">Date of Birth</label>
      <input
        id="dob"
        v-model="form.dob"
        type="date"
        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
      />
    </div>

    <!-- Alive Status -->
    <div class="flex items-center">
      <input
        id="is_alive"
        v-model="form.is_alive"
        type="checkbox"
        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
      />
      <label for="is_alive" class="ml-2 block text-sm text-gray-900">Person is alive</label>
    </div>

    <!-- Date of Death (only if not alive) -->
    <div v-if="!form.is_alive">
      <label for="dod" class="block text-sm font-medium text-gray-700">Date of Death</label>
      <input
        id="dod"
        v-model="form.dod"
        type="date"
        :min="form.dob"
        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
      />
    </div>

    <!-- Additional Biodata -->
    <div>
      <label for="biodata" class="block text-sm font-medium text-gray-700">Additional Information</label>
      <textarea
        id="biodata"
        v-model="form.biodata"
        rows="3"
        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
        placeholder="Enter any additional information about this person..."
      ></textarea>
    </div>

    <!-- Form Actions -->
    <div class="flex justify-end space-x-3 pt-4">
      <button
        type="button"
        @click="$emit('close')"
        class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
      >
        Cancel
      </button>
      <button
        type="submit"
        :disabled="isSubmitting"
        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
      >
        <span v-if="isSubmitting">Saving...</span>
        <span v-else>{{ isNew ? 'Create' : 'Update' }}</span>
      </button>
    </div>
  </form>
</template>

<script setup>
import { ref, reactive, onMounted, watch } from 'vue'

const props = defineProps({
  node: {
    type: Object,
    default: null
  },
  isNew: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['create', 'update', 'close'])

const fileInput = ref(null)
const isSubmitting = ref(false)
const profilePicPreview = ref(null)

const form = reactive({
  name: '',
  relation: '',
  profile_pic: null,
  dob: '',
  is_alive: true,
  dod: '',
  biodata: ''
})

// Initialize form with node data if editing
onMounted(() => {
  if (props.node && !props.isNew) {
    form.name = props.node.data.name || ''
    form.relation = props.node.data.relation || ''
    form.dob = props.node.data.dob || ''
    form.is_alive = props.node.data.isAlive !== undefined ? props.node.data.isAlive : true
    form.dod = props.node.data.dod || ''
    form.biodata = props.node.data.biodata || ''
  }
})

// Watch for alive status changes
watch(() => form.is_alive, (newValue) => {
  if (newValue) {
    form.dod = ''
  }
})

const handleFileChange = (event) => {
  const file = event.target.files[0]
  if (file) {
    form.profile_pic = file
    const reader = new FileReader()
    reader.onload = (e) => {
      profilePicPreview.value = e.target.result
    }
    reader.readAsDataURL(file)
  }
}

const removeProfilePic = () => {
  form.profile_pic = null
  profilePicPreview.value = null
  if (fileInput.value) {
    fileInput.value.value = ''
  }
}

const handleSubmit = async () => {
  isSubmitting.value = true
  
  try {
    const formData = new FormData()
    
    // Add form fields
    Object.keys(form).forEach(key => {
      if (form[key] !== null && form[key] !== '') {
        formData.append(key, form[key])
      }
    })

    if (props.isNew) {
      emit('create', Object.fromEntries(formData))
    } else {
      emit('update', Object.fromEntries(formData))
    }
  } catch (error) {
    console.error('Form submission error:', error)
  } finally {
    isSubmitting.value = false
  }
}
</script>