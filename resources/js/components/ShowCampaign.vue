<template>
<div class="container">
    <table class="table table-hover">
        <thead>
        <tr>
            <th>Name</th>
            <th>Total Jobs</th>
            <th>Fail Jobs</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="camp in data">
            <td>{{camp.name}}</td>
            <td>{{camp.total_jobs}}</td>
            <td>{{camp.pending_jobs}}</td>
        </tr>
        </tbody>
    </table>
</div>
</template>
<script setup>
import {onMounted, ref, watch} from "vue";
const data = ref({});
const props = defineProps({
    isSubmitForm: Boolean
});
onMounted(async () => {
    await loadCamp();
});
watch(()=>props.isSubmitForm,()=>{
    loadCamp();
})
async function loadCamp() {
    const response = await axios.get('/batch', {
        headers: {
            'Content-Type': 'multipart/form-data',
        },
    });
    data.value = response.data;
}
</script>

<style scoped>

</style>
