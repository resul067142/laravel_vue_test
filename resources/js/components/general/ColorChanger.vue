<template>
    <div>
        <input type="radio" value="red" v-model="selectedColor">
        <label for="">Red</label>
        <input type="radio" value="blue" v-model="selectedColor">
        <label for="">Blue</label>
    </div>
</template>
<script>
export default {
    props: {
        color: {
            type: String,
            required: false,
        }
    },
    data() {
        return {
            selectedColor: 'red',
        }
    },
    watch: {
        selectedColor(value) {
            console.log(value);
            this.$emit('update:color', value)
        }
    }
}
</script>