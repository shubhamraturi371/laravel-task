<template>
   <form class="form" method="post" enctype="multipart/form-data">
    <div class="form">
        <input
            type="text"
            v-model="campaignName"
            class="form-input"
            placeholder="Enter campaign name"
        />
        <input
            type="file"
            @change="handleFileUpload"
            accept=".csv"
            placeholder="Import CSV file"
        />
        <button
            type="submit"
            class="btn btn-primary"
            @click="handleSubmit"
            :disabled="!isValid"
        >
            Submit
        </button>
        <p v-if="errorMessage" class="error-message">{{ errorMessage }}</p>
    </div>
   </form>
    <div class="py-12">
<ShowCampaign :isSubmitForm="triggerFunction"></ShowCampaign>
    </div>
</template>
<script setup>
import { ref } from 'vue';
import ShowCampaign from "~components/ShowCampaign.vue";

// References to form fields and validation states
const campaignName = ref('');
const isValid = ref(false);
const errorMessage = ref('');
const selectedFile = ref(null);
const triggerFunction = ref(false);

const token = localStorage;
console.log(token.getItem('auth_token'))
// Regular expression to validate email format
const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

// Handle file upload
const handleFileUpload = (event) => {
    const file = event.target.files[0];

    // Validate file type
    if (file && file.type === 'text/csv') {
        parseCSV(file);
        selectedFile.value = file;
    } else {
        errorMessage.value = 'Please upload a valid CSV file.';
        isValid.value = false;
    }
};

// Parse CSV file and validate
const parseCSV = (file) => {
    const reader = new FileReader();

    reader.onload = (event) => {
        const text = event.target.result;
        const rows = text.split('\n').map((row) => row.split(','));

        // Validate columns
        if (rows[0] && rows[0].length === 2 && rows[0][0].trim() === 'name' && rows[0][1].trim() === 'email') {
            // Validate email addresses in subsequent rows
            const isEmailsValid = rows.slice(1).every((row) => {

                if (row && row.length === 2) {
                    const email = row[1]?.trim();
                    console.log(`Email being validated: "${email}"`);
                    console.log(`Length of email: ${email.length}`);
                    return emailRegex.test(email);
                }
                return true;
            });

            if (isEmailsValid) {
                isValid.value = true;
                errorMessage.value = '';
            } else {
                isValid.value = false;
                errorMessage.value = 'Invalid email format found in CSV file.';
            }
        } else {
            isValid.value = false;
            errorMessage.value = 'Invalid CSV format. The CSV must contain exactly two columns: "name" and "email".';
        }
    };

    reader.onerror = () => {
        errorMessage.value = 'Error reading file.';
        isValid.value = false;
    };

    reader.readAsText(file);
};


// Handle form submission
const handleSubmit = async (event) => {
    event.preventDefault();

    if (!isValid.value) {
        alert('Please fix the errors before submitting.');
        return;
    }

    const formData = new FormData();
    formData.append('campaignName', campaignName.value);
    formData.append('file', selectedFile.value);
    try {
        const response = await axios.post('/upload-campaign', formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
                'Authorization': `Bearer ${token}`
            },
        });

        alert('Form submitted successfully!');
        console.log(response.data);
        // Optionally reset form
        campaignName.value = '';
        selectedFile.value = null;
        isValid.value = false;
        triggerFunction.value = true;
    } catch (error) {
        console.error('Error submitting form:', error);
        errorMessage.value = 'There was an error submitting the form. Please try again later.';
    }
};
</script>


<style scoped>
.form-input {
    width: 100%;
    padding: 8px;
    margin-bottom: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.btn-primary {
    padding: 10px 15px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.btn-primary:disabled {
    background-color: #ccc;
    cursor: not-allowed;
}

.error-message {
    color: red;
    margin-top: 10px;
}
</style>
